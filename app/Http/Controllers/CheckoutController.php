<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\PaymentMethod;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\UserAddress;
use App\Mail\OrderConfirmation;
use App\Mail\ReceiptEmail;
use App\Services\ReceiptGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function view(Request $request)
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->user_id)->with('items.variant.product')->first();

        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Get selected items from cart, or use all if not specified
        $selectedItems = $request->input('selected_items', []);
        
        // Store selected items in session for use in placeOrder
        if (!empty($selectedItems)) {
            session(['selected_items' => $selectedItems]);
        } else {
            // If no selected items specified, use all items
            $selectedItems = $cart->items->pluck('cart_item_id')->toArray();
            session(['selected_items' => $selectedItems]);
        }

        $addresses = UserAddress::where('user_id', $user->user_id)->get();
        $methods = PaymentMethod::active()->get();
        return view('checkout.index', compact('cart', 'addresses', 'methods', 'selectedItems'));
    }

    public function placeOrder(Request $request)
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->user_id)->with('items.variant.product')->first();

        // Get the payment method to check if it's online payment
        $paymentMethod = PaymentMethod::find($request->method_id);
        
        // Get selected items - first from session, then from request
        $selectedItems = session('selected_items', $request->input('selected_items', []));
        
        if (empty($selectedItems)) {
            return back()->with('error', 'Please select at least one product to checkout.');
        }
        
        // Calculate total only for selected items
        $selectedCartItems = $cart->items()->whereIn('cart_item_id', $selectedItems)->get();
        $totalAmount = $selectedCartItems->sum(function($item) {
            return $item->subtotal();
        });
        
        // Validation rules
        $rules = [
            'shipping_address' => 'required|string|max:255',
            'method_id' => 'required|exists:payment_methods,method_id',
            'selected_items' => 'required|array|min:1',
        ];
        
        // Add validation based on payment method
        if ($paymentMethod && $paymentMethod->method_name === 'Online Payment') {
            $rules['payment_amount'] = 'required|numeric|min:' . $totalAmount . '|max:999999.99';
        } else {
            // COD doesn't need payment amount validation
            $rules['payment_amount'] = 'nullable|numeric';
        }
        
        $messages = [];
        if ($paymentMethod && $paymentMethod->method_name === 'Online Payment') {
            $messages['payment_amount.required'] = 'Please enter the payment amount for online payment.';
            $messages['payment_amount.numeric'] = 'Payment amount must be a valid number.';
            $messages['payment_amount.min'] = 'Payment amount must be at least Php ' . number_format($totalAmount, 2) . ' (order total).';
        }
        
        $validated = $request->validate($rules, $messages);

        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $user->user_id,
                'method_id' => $request->method_id,
                'order_status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'total_amount' => $totalAmount,
            ]);

            // Create order items only for selected cart items
            foreach ($selectedCartItems as $item) {
                // Verify stock before creating order item
                if ($item->quantity > $item->variant->stock) {
                    throw new \Exception($item->variant->product->product_name . ' (' . $item->variant->color . '/' . $item->variant->size . ') only has ' . $item->variant->stock . ' in stock.');
                }
                
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'variant_id' => $item->variant_id,
                    'oi_quantity' => $item->quantity,
                    'oi_price' => $item->variant->product->price,
                ]);
                $item->variant->decrement('stock', $item->quantity);
            }

            // Create payment record - set status based on payment method
            $paymentStatus = ($paymentMethod && $paymentMethod->method_name === 'Online Payment') ? 'paid' : 'pending';
            
            Payment::create([
                'order_id' => $order->order_id,
                'payment_status' => $paymentStatus,
                'transaction_ref' => $request->payment_amount ?? null,
                'amount' => $totalAmount,
            ]);

            // Generate PDF receipt ONLY for Online Payment (COD waits until delivery)
            $pdfPath = null;
            if ($paymentMethod && $paymentMethod->method_name === 'Online Payment') {
                try {
                    $pdfPath = ReceiptGenerator::generateReceipt($order);
                    Log::info('✅ PDF receipt generated for Online Payment', ['order_id' => $order->order_id, 'path' => $pdfPath]);
                } catch (\Exception $e) {
                    Log::error('❌ Failed to generate PDF receipt', [
                        'order_id' => $order->order_id,
                        'error' => $e->getMessage()
                    ]);
                }
                
                // Send receipt email with PDF attachment - this is wrapped separately so checkout doesn't fail
                try {
                    Mail::to($user->email)->send(new ReceiptEmail($order, $pdfPath));
                    Log::info('✅ Receipt email with PDF sent for Online Payment', ['order_id' => $order->order_id, 'pdf_path' => $pdfPath]);
                } catch (\Exception $mailError) {
                    Log::error('⚠️ Failed to send receipt email (order still placed)', [
                        'order_id' => $order->order_id,
                        'pdf_path' => $pdfPath,
                        'error' => $mailError->getMessage()
                    ]);
                    // Continue - email failure should not prevent order placement
                }
            } else {
                Log::info('ℹ️ PDF receipt skipped for COD (will be generated on delivery)', ['order_id' => $order->order_id]);
            }

            // Delete only selected items from cart
            CartItem::whereIn('cart_item_id', $selectedItems)->delete();

            DB::commit();
            
            // Clear selected items from session
            session()->forget('selected_items');
            
            // Send order confirmation email
            Mail::to($user->email)->send(new OrderConfirmation($order));
            
            // Prepare success message based on payment method
            $successMessage = 'Order placed successfully! A confirmation email has been sent.';
            if ($paymentMethod && $paymentMethod->method_name === 'Online Payment') {
                $successMessage .= ' Your receipt (PDF) has also been sent to your email.';
            } else {
                $successMessage .= ' Your receipt (PDF) will be sent when the order is delivered.';
            }
            
            return redirect()->route('orders.confirmation', $order->order_id)->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage() ?: 'Something went wrong during checkout. Please try again.');
        }
    }

    public function confirmation($order_id)
    {
        $order = Order::where('order_id', $order_id)
            ->where('user_id', Auth::user()->user_id)
            ->with(['items.variant.product', 'paymentMethod'])
            ->firstOrFail();

        return view('orders.confirmation', compact('order'));
    }
}
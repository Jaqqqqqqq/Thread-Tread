<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\PaymentMethod;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\UserAddress;
use App\Mail\OrderConfirmation;
use App\Services\ReceiptGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function view()
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->user_id)->with('items.variant.product')->first();

        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $addresses = UserAddress::where('user_id', $user->user_id)->get();
        $methods = PaymentMethod::active()->get();
        return view('checkout.index', compact('cart', 'addresses', 'methods'));
    }

    public function placeOrder(Request $request)
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->user_id)->with('items.variant.product')->first();

        // Get the payment method to check if it's online payment
        $paymentMethod = PaymentMethod::find($request->method_id);
        
        // Validation rules
        $rules = [
            'shipping_address' => 'required|string|max:255',
            'method_id' => 'required|exists:payment_methods,method_id',
        ];
        
        // Add payment_ref validation for online payment
        if ($paymentMethod && $paymentMethod->method_name === 'Online Payment') {
            $rules['payment_ref'] = 'required|string|min:3|max:100';
        } else {
            $rules['payment_ref'] = 'nullable|string|max:100';
        }
        
        $validated = $request->validate($rules, [
            'payment_ref.required' => 'Please provide your payment reference for online payment.',
            'payment_ref.min' => 'Payment reference must be at least 3 characters.',
            'payment_ref.max' => 'Payment reference cannot exceed 100 characters.',
        ]);

        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Verify stock before placing order
        foreach ($cart->items as $item) {
            if ($item->quantity > $item->variant->stock) {
                return back()->with('error', $item->variant->product->product_name . ' (' . $item->variant->color . '/' . $item->variant->size . ') only has ' . $item->variant->stock . ' in stock.');
            }
        }

        DB::beginTransaction();
        try {
            $totalAmount = $cart->calculateTotal();
            
            $order = Order::create([
                'user_id' => $user->user_id,
                'method_id' => $request->method_id,
                'order_status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'total_amount' => $totalAmount,
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'variant_id' => $item->variant_id,
                    'oi_quantity' => $item->quantity,
                    'oi_price' => $item->variant->product->price,
                ]);
                $item->variant->decrement('stock', $item->quantity);
            }

            // Create payment record
            Payment::create([
                'order_id' => $order->order_id,
                'payment_status' => 'pending', // Will be marked 'paid' when confirmed
                'transaction_ref' => $request->payment_ref ?? null,
                'amount' => $totalAmount,
            ]);

            // Generate PDF receipt immediately after order creation
            try {
                ReceiptGenerator::generateReceipt($order);
                Log::info('✅ PDF receipt generated successfully', ['order_id' => $order->order_id]);
            } catch (\Exception $e) {
                Log::error('❌ Failed to generate PDF receipt', [
                    'order_id' => $order->order_id,
                    'error' => $e->getMessage()
                ]);
                // Continue even if PDF generation fails - order should still complete
            }

            $cart->items()->delete();

            DB::commit();
            
            // Send order confirmation email
            Mail::to($user->email)->send(new OrderConfirmation($order));
            
            return redirect()->route('orders.confirmation', $order->order_id)->with('success', 'Order placed successfully! A confirmation email has been sent.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Something went wrong during checkout. Please try again.');
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
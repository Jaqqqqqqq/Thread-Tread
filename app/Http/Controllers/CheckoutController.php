<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\PaymentMethod;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $request->validate([
            'shipping_address' => 'required|string|max:255',
            'method_id' => 'required|exists:payment_methods,method_id',
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
            $order = Order::create([
                'user_id' => $user->user_id,
                'method_id' => $request->method_id,
                'order_status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'total_amount' => $cart->calculateTotal(),
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

            $cart->items()->delete();

            DB::commit();
            return redirect()->route('orders.confirmation', $order->order_id)->with('success', 'Order placed successfully!');
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
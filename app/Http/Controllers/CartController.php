<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Get or create cart for the authenticated user.
     */
    private function getCart()
    {
        return Cart::firstOrCreate(['user_id' => Auth::user()->user_id]);
    }

    /**
     * Show the cart page.
     */
    public function index()
    {
        $cart = $this->getCart();
        $cart->load('items.variant.product');
        return view('cart.index', compact('cart'));
    }

    /**
     * Add item to cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|integer|exists:product_variants,variant_id',
            'quantity' => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);

        if ($variant->stock < 1) {
            return redirect()->back()->with('error', 'This variant is out of stock.');
        }

        $cart = $this->getCart();

        // Check if this variant is already in the cart
        $existingItem = CartItem::where('cart_id', $cart->cart_id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($existingItem) {
            $newQty = $existingItem->quantity + $request->quantity;
            if ($newQty > $variant->stock) {
                return redirect()->back()->with('error', 'Cannot add more. You already have ' . $existingItem->quantity . ' in your cart and only ' . $variant->stock . ' are available.');
            }
            $existingItem->update(['quantity' => $newQty]);
        } else {
            if ($request->quantity > $variant->stock) {
                return redirect()->back()->with('error', 'Not enough stock. Only ' . $variant->stock . ' available.');
            }
            CartItem::create([
                'cart_id' => $cart->cart_id,
                'variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Item added to cart!');
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, $cart_item_id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = CartItem::findOrFail($cart_item_id);
        $cart = $this->getCart();

        // Verify the item belongs to user's cart
        if ($item->cart_id !== $cart->cart_id) {
            return redirect()->route('cart.index')->with('error', 'Unauthorized action.');
        }

        // Check stock
        if ($request->quantity > $item->variant->stock) {
            return redirect()->route('cart.index')->with('error', 'Only ' . $item->variant->stock . ' available for this variant.');
        }

        $item->update(['quantity' => $request->quantity]);
        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    /**
     * Remove item from cart.
     */
    public function remove($cart_item_id)
    {
        $item = CartItem::findOrFail($cart_item_id);
        $cart = $this->getCart();

        if ($item->cart_id !== $cart->cart_id) {
            return redirect()->route('cart.index')->with('error', 'Unauthorized action.');
        }

        $item->delete();
        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }
}
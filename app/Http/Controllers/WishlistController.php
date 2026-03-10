<?php

use App\Models\Wishlist;
use App\Models\WishlistItem;
use App\Models\Product;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = auth()->user()->wishlist()->with('items.product')->firstOrCreate(['user_id'=> auth()->id()]);
        return view('wishlist.index', compact('wishlist'));
    }

    public function add(Request $request)
    {
        $wishlist = auth()->user()->wishlist()->firstOrCreate(['user_id'=> auth()->id()]);
        $product = Product::findOrFail($request->product_id);

        if (!$wishlist->hasProduct($product->product_id)) {
            $wishlist->items()->create(['product_id' => $product->product_id]);
        }

        return back()->with('success', 'Added to wishlist.');
    }

    public function remove($itemId)
    {
        WishlistItem::findOrFail($itemId)->delete();
        return back();
    }
}

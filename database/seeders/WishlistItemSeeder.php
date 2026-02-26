<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WishlistItem;
use App\Models\Wishlist;
use App\Models\Product;

class WishlistItemSeeder extends Seeder
{
    public function run(): void
    {
        $wishlists = Wishlist::all();
        $products  = Product::all();

        // Wishlist 1 — save 3 products
        if ($wishlists->get(0) && $products->count() >= 3) {
            WishlistItem::create([
                'wishlist_id' => $wishlists->get(0)->wishlist_id,
                'product_id'  => $products->get(0)->product_id,
            ]);
            WishlistItem::create([
                'wishlist_id' => $wishlists->get(0)->wishlist_id,
                'product_id'  => $products->get(2)->product_id,
            ]);
            WishlistItem::create([
                'wishlist_id' => $wishlists->get(0)->wishlist_id,
                'product_id'  => $products->get(4)->product_id,
            ]);
        }

        // Wishlist 2 — save 2 products
        if ($wishlists->get(1) && $products->count() >= 5) {
            WishlistItem::create([
                'wishlist_id' => $wishlists->get(1)->wishlist_id,
                'product_id'  => $products->get(1)->product_id,
            ]);
            WishlistItem::create([
                'wishlist_id' => $wishlists->get(1)->wishlist_id,
                'product_id'  => $products->get(3)->product_id,
            ]);
        }
    }
}
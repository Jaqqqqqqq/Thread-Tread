<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CartItem;
use App\Models\Cart;
use App\Models\ProductVariant;

class CartItemSeeder extends Seeder
{
    public function run(): void
    {
        // Add items to the first two customers' carts only.
        // Simulates active shopping sessions.

        $carts    = Cart::all();
        $variants = ProductVariant::availableStock()->get();

        // Cart 1 — 3 items
        if ($carts->get(0) && $variants->count() >= 3) {
            CartItem::create([
                'cart_id'    => $carts->get(0)->cart_id,
                'variant_id' => $variants->get(0)->variant_id,
                'quantity'   => 2,
            ]);
            CartItem::create([
                'cart_id'    => $carts->get(0)->cart_id,
                'variant_id' => $variants->get(1)->variant_id,
                'quantity'   => 1,
            ]);
            CartItem::create([
                'cart_id'    => $carts->get(0)->cart_id,
                'variant_id' => $variants->get(2)->variant_id,
                'quantity'   => 3,
            ]);
        }

        // Cart 2 — 2 items
        if ($carts->get(1) && $variants->count() >= 5) {
            CartItem::create([
                'cart_id'    => $carts->get(1)->cart_id,
                'variant_id' => $variants->get(3)->variant_id,
                'quantity'   => 1,
            ]);
            CartItem::create([
                'cart_id'    => $carts->get(1)->cart_id,
                'variant_id' => $variants->get(4)->variant_id,
                'quantity'   => 2,
            ]);
        }
    }
}
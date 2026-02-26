<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\Product;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $orders   = Order::all();
        $variants = ProductVariant::with('product')->get();

        // Helper to find a variant by product name + size + color
        $findVariant = function (string $productName, string $size, string $color)
            use ($variants): ?ProductVariant {
            return $variants->first(function ($v) use ($productName, $size, $color) {
                return $v->product->product_name === $productName
                    && $v->size  === $size
                    && $v->color === $color;
            });
        };

        // Order 1 (Juan - completed) — 2 items
        $v1 = $findVariant('Nike Dri-FIT Training Tee', 'M', 'Black');
        $v2 = $findVariant('Adidas Essentials Tee', 'L', 'White');

        if ($v1) OrderItem::create([
            'order_id'    => $orders->get(0)->order_id,
            'variant_id'  => $v1->variant_id,
            'oi_quantity' => 2,
            'oi_price'    => 899.00, // Snapshot price at purchase
        ]);

        if ($v2) OrderItem::create([
            'order_id'    => $orders->get(0)->order_id,
            'variant_id'  => $v2->variant_id,
            'oi_quantity' => 1,
            'oi_price'    => 799.00,
        ]);

        // Order 2 (Juan - pending) — 1 item
        $v3 = $findVariant('Nike Flex Stride Shorts', 'M', 'Black');
        if ($v3) OrderItem::create([
            'order_id'    => $orders->get(1)->order_id,
            'variant_id'  => $v3->variant_id,
            'oi_quantity' => 1,
            'oi_price'    => 899.00,
        ]);

        // Order 3 (Maria - shipped) — 1 item
        $v4 = $findVariant('Nike Windrunner Jacket', 'S', 'Black');
        if ($v4) OrderItem::create([
            'order_id'    => $orders->get(2)->order_id,
            'variant_id'  => $v4->variant_id,
            'oi_quantity' => 1,
            'oi_price'    => 2499.00,
        ]);

        // Order 4 (Maria - cancelled) — 1 item
        $v5 = $findVariant('Penshoppe Graphic Tee', 'S', 'White');
        if ($v5) OrderItem::create([
            'order_id'    => $orders->get(3)->order_id,
            'variant_id'  => $v5->variant_id,
            'oi_quantity' => 1,
            'oi_price'    => 399.00,
        ]);

        // Order 5 (Jose - processing) — 2 items
        $v6 = $findVariant('Bench Classic Polo', 'L', 'White');
        $v7 = $findVariant('Penshoppe Slim Fit Jeans', 'M', 'Black');

        if ($v6) OrderItem::create([
            'order_id'    => $orders->get(4)->order_id,
            'variant_id'  => $v6->variant_id,
            'oi_quantity' => 1,
            'oi_price'    => 599.00,
        ]);

        if ($v7) OrderItem::create([
            'order_id'    => $orders->get(4)->order_id,
            'variant_id'  => $v7->variant_id,
            'oi_quantity' => 1,
            'oi_price'    => 999.00,
        ]);
    }
}
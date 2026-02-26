<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductVariant;
use App\Models\Product;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        // Each product gets multiple variants (size + color combos).
        // UNIQUE INDEX on (product_id, size, color) is respected —
        // no duplicate combinations are created.

        $products = Product::all();

        foreach ($products as $product) {
            $variants = [
                ['size' => 'S',  'color' => 'Black', 'stock' => 20],
                ['size' => 'M',  'color' => 'Black', 'stock' => 25],
                ['size' => 'L',  'color' => 'Black', 'stock' => 15],
                ['size' => 'XL', 'color' => 'Black', 'stock' => 10],
                ['size' => 'S',  'color' => 'White', 'stock' => 18],
                ['size' => 'M',  'color' => 'White', 'stock' => 22],
                ['size' => 'L',  'color' => 'White', 'stock' => 12],
                ['size' => 'M',  'color' => 'Navy',  'stock' => 0 ], // Out of stock variant
            ];

            foreach ($variants as $variant) {
                ProductVariant::create([
                    'product_id' => $product->product_id,
                    'size'       => $variant['size'],
                    'color'      => $variant['color'],
                    'stock'      => $variant['stock'],
                ]);
            }
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch IDs dynamically — avoids hardcoding and handles
        // any insertion order differences.
        $tshirts   = Category::where('category_name', 'T-Shirts')->first()->category_id;
        $polos     = Category::where('category_name', 'Polo Shirts')->first()->category_id;
        $jackets   = Category::where('category_name', 'Jackets')->first()->category_id;
        $jeans     = Category::where('category_name', 'Jeans')->first()->category_id;
        $shorts    = Category::where('category_name', 'Shorts')->first()->category_id;
        $dresses   = Category::where('category_name', 'Dresses')->first()->category_id;

        $nike      = Brand::where('brand_name', 'Nike')->first()->brand_id;
        $adidas    = Brand::where('brand_name', 'Adidas')->first()->brand_id;
        $penshoppe = Brand::where('brand_name', 'Penshoppe')->first()->brand_id;
        $bench     = Brand::where('brand_name', 'Bench')->first()->brand_id;

        $products = [
            // T-Shirts
            [
                'category_id'  => $tshirts,
                'brand_id'     => $nike,
                'product_name' => 'Nike Dri-FIT Training Tee',
                'prod_desc'    => 'Lightweight, sweat-wicking training t-shirt.',
                'price'        => 899.00,
                'prod_image'   => 'products/nike-dri-fit-tee.jpg',
            ],
            [
                'category_id'  => $tshirts,
                'brand_id'     => $adidas,
                'product_name' => 'Adidas Essentials Tee',
                'prod_desc'    => 'Classic cotton tee with Adidas logo.',
                'price'        => 799.00,
                'prod_image'   => 'products/adidas-essentials-tee.jpg',
            ],
            [
                'category_id'  => $tshirts,
                'brand_id'     => $penshoppe,
                'product_name' => 'Penshoppe Graphic Tee',
                'prod_desc'    => 'Trendy graphic print tee for everyday casual wear.',
                'price'        => 399.00,
                'prod_image'   => 'products/penshoppe-graphic-tee.jpg',
            ],
            // Polo Shirts
            [
                'category_id'  => $polos,
                'brand_id'     => $bench,
                'product_name' => 'Bench Classic Polo',
                'prod_desc'    => 'Smart casual polo shirt with embroidered logo.',
                'price'        => 599.00,
                'prod_image'   => 'products/bench-classic-polo.jpg',
            ],
            [
                'category_id'  => $polos,
                'brand_id'     => $nike,
                'product_name' => 'Nike Victory Polo',
                'prod_desc'    => 'Performance polo with moisture-wicking fabric.',
                'price'        => 1299.00,
                'prod_image'   => 'products/nike-victory-polo.jpg',
            ],
            // Jackets
            [
                'category_id'  => $jackets,
                'brand_id'     => $adidas,
                'product_name' => 'Adidas Tiro Track Jacket',
                'prod_desc'    => 'Slim-fit track jacket with contrast stripes.',
                'price'        => 1999.00,
                'prod_image'   => 'products/adidas-tiro-jacket.jpg',
            ],
            [
                'category_id'  => $jackets,
                'brand_id'     => $nike,
                'product_name' => 'Nike Windrunner Jacket',
                'prod_desc'    => 'Lightweight wind-resistant running jacket.',
                'price'        => 2499.00,
                'prod_image'   => 'products/nike-windrunner-jacket.jpg',
            ],
            // Jeans
            [
                'category_id'  => $jeans,
                'brand_id'     => $penshoppe,
                'product_name' => 'Penshoppe Slim Fit Jeans',
                'prod_desc'    => 'Modern slim-fit denim jeans in mid-wash.',
                'price'        => 999.00,
                'prod_image'   => 'products/penshoppe-slim-jeans.jpg',
            ],
            // Shorts
            [
                'category_id'  => $shorts,
                'brand_id'     => $nike,
                'product_name' => 'Nike Flex Stride Shorts',
                'prod_desc'    => '5-inch running shorts with liner.',
                'price'        => 899.00,
                'prod_image'   => 'products/nike-flex-shorts.jpg',
            ],
            // Dresses (no brand — brand_id = null)
            [
                'category_id'  => $dresses,
                'brand_id'     => null,
                'product_name' => 'Floral Wrap Dress',
                'prod_desc'    => 'Elegant floral wrap dress for casual outings.',
                'price'        => 749.00,
                'prod_image'   => 'products/floral-wrap-dress.jpg',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
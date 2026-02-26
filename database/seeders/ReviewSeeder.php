<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // UNIQUE INDEX on (user_id, product_id) — one review per user per product.
        $juan  = User::where('email', 'juan@example.com')->first();
        $maria = User::where('email', 'maria@example.com')->first();
        $jose  = User::where('email', 'jose@example.com')->first();

        $nikeTee      = Product::where('product_name', 'Nike Dri-FIT Training Tee')->first();
        $adidasTee    = Product::where('product_name', 'Nike Dri-FIT Training Tee')->first();
        $windrunner   = Product::where('product_name', 'Nike Windrunner Jacket')->first();
        $penshoppe    = Product::where('product_name', 'Penshoppe Graphic Tee')->first();
        $benchPolo    = Product::where('product_name', 'Bench Classic Polo')->first();

        $reviews = [
            [
                'user_id'    => $juan->user_id,
                'product_id' => $nikeTee->product_id,
                'rating'     => 5,
                'comment'    => 'Great quality tee! Very comfortable for workouts.',
            ],
            [
                'user_id'    => $juan->user_id,
                'product_id' => $windrunner->product_id,
                'rating'     => 4,
                'comment'    => 'Lightweight and wind-resistant. Good buy!',
            ],
            [
                'user_id'    => $maria->user_id,
                'product_id' => $windrunner->product_id,
                'rating'     => 5,
                'comment'    => 'Absolutely love this jacket. Fits perfectly!',
            ],
            [
                'user_id'    => $maria->user_id,
                'product_id' => $penshoppe->product_id,
                'rating'     => 3,
                'comment'    => 'Decent quality for the price.',
            ],
            [
                'user_id'    => $jose->user_id,
                'product_id' => $benchPolo->product_id,
                'rating'     => 4,
                'comment'    => 'Nice polo, great for casual office wear.',
            ],
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }
    }
}
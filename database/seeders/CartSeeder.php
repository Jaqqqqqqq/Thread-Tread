<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\User;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        // Create one cart per customer user (UNIQUE INDEX on user_id).
        // Admin does not need a cart.
        $customers = User::customers()->get();

        foreach ($customers as $customer) {
            Cart::create(['user_id' => $customer->user_id]);
        }
    }
}
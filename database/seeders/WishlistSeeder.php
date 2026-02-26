<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Wishlist;
use App\Models\User;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        // Create one wishlist per customer (UNIQUE INDEX on user_id).
        $customers = User::customers()->get();

        foreach ($customers as $customer) {
            Wishlist::create(['user_id' => $customer->user_id]);
        }
    }
}
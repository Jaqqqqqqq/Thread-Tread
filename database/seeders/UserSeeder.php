<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // =====================================================
        // ADMIN USER
        // Create one admin account for backend access.
        // Password is hashed using bcrypt via Hash::make().
        // =====================================================
        User::create([
            'fname'    => 'Admin',
            'lname'    => 'User',
            'email'    => 'admin@clothingstore.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'is_active'=> true,
            'phone'    => '09171234567',
        ]);

        // =====================================================
        // CUSTOMER USERS
        // Create sample customers for testing user features:
        // cart, checkout, wishlist, reviews, order history.
        // =====================================================
        $customers = [
            [
                'fname'    => 'Juan',
                'lname'    => 'Dela Cruz',
                'email'    => 'juan@example.com',
                'password' => Hash::make('password'),
                'role'     => 'customer',
                'is_active'=> true,
                'phone'    => '09181234567',
            ],
            [
                'fname'    => 'Maria',
                'lname'    => 'Santos',
                'email'    => 'maria@example.com',
                'password' => Hash::make('password'),
                'role'     => 'customer',
                'is_active'=> true,
                'phone'    => '09191234567',
            ],
            [
                'fname'    => 'Jose',
                'lname'    => 'Reyes',
                'email'    => 'jose@example.com',
                'password' => Hash::make('password'),
                'role'     => 'customer',
                'is_active'=> true,
                'phone'    => '09201234567',
            ],
            [
                'fname'    => 'Ana',
                'lname'    => 'Garcia',
                'email'    => 'ana@example.com',
                'password' => Hash::make('password'),
                'role'     => 'customer',
                'is_active'=> true,
                'phone'    => '09211234567',
            ],
            [
                'fname'    => 'Carlos',
                'lname'    => 'Mendoza',
                'email'    => 'carlos@example.com',
                'password' => Hash::make('password'),
                'role'     => 'customer',
                'is_active'=> false, // Inactive user for testing admin controls
                'phone'    => '09221234567',
            ],
        ];

        foreach ($customers as $customer) {
            User::create($customer);
        }
    }
}
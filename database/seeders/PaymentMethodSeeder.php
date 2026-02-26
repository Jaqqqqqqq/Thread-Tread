<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['method_name' => 'Cash on Delivery', 'is_active' => true],
            ['method_name' => 'GCash',             'is_active' => true],
            ['method_name' => 'Credit Card',       'is_active' => true],
            ['method_name' => 'Debit Card',        'is_active' => true],
            ['method_name' => 'PayMaya',           'is_active' => false], // Inactive for testing
        ];

        foreach ($methods as $method) {
            PaymentMethod::create($method);
        }
    }
}
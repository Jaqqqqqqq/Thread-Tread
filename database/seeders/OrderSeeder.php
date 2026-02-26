<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\PaymentMethod;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $juan  = User::where('email', 'juan@example.com')->first();
        $maria = User::where('email', 'maria@example.com')->first();
        $jose  = User::where('email', 'jose@example.com')->first();

        $cod        = PaymentMethod::where('method_name', 'Cash on Delivery')->first();
        $gcash      = PaymentMethod::where('method_name', 'GCash')->first();
        $creditCard = PaymentMethod::where('method_name', 'Credit Card')->first();

        $orders = [
            // Juan — completed order
            [
                'user_id'          => $juan->user_id,
                'method_id'        => $gcash->method_id,
                'order_status'     => 'completed',
                'shipping_address' => '123 Rizal St, Manila, Metro Manila 1000, Philippines',
                'total_amount'     => 1798.00,
            ],
            // Juan — pending order
            [
                'user_id'          => $juan->user_id,
                'method_id'        => $cod->method_id,
                'order_status'     => 'pending',
                'shipping_address' => '123 Rizal St, Manila, Metro Manila 1000, Philippines',
                'total_amount'     => 899.00,
            ],
            // Maria — shipped order
            [
                'user_id'          => $maria->user_id,
                'method_id'        => $creditCard->method_id,
                'order_status'     => 'shipped',
                'shipping_address' => '456 Mabini Ave, Quezon City, Metro Manila 1100, Philippines',
                'total_amount'     => 2499.00,
            ],
            // Maria — cancelled order
            [
                'user_id'          => $maria->user_id,
                'method_id'        => $gcash->method_id,
                'order_status'     => 'cancelled',
                'shipping_address' => '456 Mabini Ave, Quezon City, Metro Manila 1100, Philippines',
                'total_amount'     => 399.00,
            ],
            // Jose — processing order
            [
                'user_id'          => $jose->user_id,
                'method_id'        => $cod->method_id,
                'order_status'     => 'processing',
                'shipping_address' => '789 Bonifacio Blvd, Cebu City, Cebu 6000, Philippines',
                'total_amount'     => 1598.00,
            ],
        ];

        foreach ($orders as $order) {
            Order::create($order);
        }
    }
}
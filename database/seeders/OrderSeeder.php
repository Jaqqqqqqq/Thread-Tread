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
            // Juan — completed order (January)
            [
                'user_id'          => $juan->user_id,
                'method_id'        => $gcash->method_id,
                'order_status'     => 'completed',
                'shipping_address' => '123 Rizal St, Manila, Metro Manila 1000, Philippines',
                'total_amount'     => 1798.00,
                'created_at'       => now()->setMonth(1)->setDay(10),
            ],
            // Juan — pending order (February)
            [
                'user_id'          => $juan->user_id,
                'method_id'        => $cod->method_id,
                'order_status'     => 'pending',
                'shipping_address' => '123 Rizal St, Manila, Metro Manila 1000, Philippines',
                'total_amount'     => 899.00,
                'created_at'       => now()->setMonth(2)->setDay(14),
            ],
            // Maria — shipped order (March)
            [
                'user_id'          => $maria->user_id,
                'method_id'        => $creditCard->method_id,
                'order_status'     => 'shipped',
                'shipping_address' => '456 Mabini Ave, Quezon City, Metro Manila 1100, Philippines',
                'total_amount'     => 2499.00,
                'created_at'       => now()->setMonth(3)->setDay(15),
            ],
            // Maria — cancelled order (May)
            [
                'user_id'          => $maria->user_id,
                'method_id'        => $gcash->method_id,
                'order_status'     => 'cancelled',
                'shipping_address' => '456 Mabini Ave, Quezon City, Metro Manila 1100, Philippines',
                'total_amount'     => 399.00,
                'created_at'       => now()->setMonth(5)->setDay(20),
            ],
            // Jose — processing order (June)
            [
                'user_id'          => $jose->user_id,
                'method_id'        => $cod->method_id,
                'order_status'     => 'processing',
                'shipping_address' => '789 Bonifacio Blvd, Cebu City, Cebu 6000, Philippines',
                'total_amount'     => 1598.00,
                'created_at'       => now()->setMonth(6)->setDay(5),
            ],
        ];

        foreach ($orders as $order) {
            Order::create($order);
        }
    }
}
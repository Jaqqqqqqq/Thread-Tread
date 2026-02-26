<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Order;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // UNIQUE INDEX on payments.order_id — one payment per order.
        // payment_status mirrors the order's payment state.
        $orders = Order::all();

        $paymentData = [
            // Order 1 (completed) — paid
            [
                'order_id'        => $orders->get(0)->order_id,
                'payment_status'  => 'paid',
                'transaction_ref' => 'GCASH-TXN-001',
                'amount'          => 1798.00,
                'paid_at'         => now()->subDays(5),
            ],
            // Order 2 (pending COD) — pending
            [
                'order_id'        => $orders->get(1)->order_id,
                'payment_status'  => 'pending',
                'transaction_ref' => null,
                'amount'          => 899.00,
                'paid_at'         => null,
            ],
            // Order 3 (shipped) — paid by credit card
            [
                'order_id'        => $orders->get(2)->order_id,
                'payment_status'  => 'paid',
                'transaction_ref' => 'CC-TXN-001',
                'amount'          => 2499.00,
                'paid_at'         => now()->subDays(2),
            ],
            // Order 4 (cancelled) — refunded
            [
                'order_id'        => $orders->get(3)->order_id,
                'payment_status'  => 'refunded',
                'transaction_ref' => 'GCASH-TXN-002',
                'amount'          => 399.00,
                'paid_at'         => now()->subDays(3),
            ],
            // Order 5 (processing) — pending COD
            [
                'order_id'        => $orders->get(4)->order_id,
                'payment_status'  => 'pending',
                'transaction_ref' => null,
                'amount'          => 1598.00,
                'paid_at'         => null,
            ],
        ];

        foreach ($paymentData as $payment) {
            Payment::create($payment);
        }
    }
}
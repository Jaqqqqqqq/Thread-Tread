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
            // Order 1 (completed, January) — paid
            [
                'order_id'        => $orders->get(0)->order_id,
                'payment_status'  => 'paid',
                'transaction_ref' => 'GCASH-TXN-001',
                'amount'          => 1798.00,
                'paid_at'         => now()->setMonth(1)->setDay(10),
            ],
            // Order 2 (pending COD, February) — pending
            [
                'order_id'        => $orders->get(1)->order_id,
                'payment_status'  => 'pending',
                'transaction_ref' => null,
                'amount'          => 899.00,
                'paid_at'         => null,
            ],
            // Order 3 (shipped, March) — paid by credit card
            [
                'order_id'        => $orders->get(2)->order_id,
                'payment_status'  => 'paid',
                'transaction_ref' => 'CC-TXN-001',
                'amount'          => 2499.00,
                'paid_at'         => now()->setMonth(3)->setDay(15),
            ],
            // Order 4 (cancelled, May) — refunded
            [
                'order_id'        => $orders->get(3)->order_id,
                'payment_status'  => 'refunded',
                'transaction_ref' => 'GCASH-TXN-002',
                'amount'          => 399.00,
                'paid_at'         => now()->setMonth(5)->setDay(20),
            ],
            // Order 5 (processing, June) — pending COD
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
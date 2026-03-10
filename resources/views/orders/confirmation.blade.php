@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px; max-width: 700px; margin: 0 auto;">

    <div style="background: #fff; border-radius: 8px; padding: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-align: center;">
        <div style="font-size: 56px; margin-bottom: 16px;">✅</div>
        <h2 style="margin: 0 0 8px 0; color: #2e7d32; font-size: 1.5rem;">Order Placed Successfully!</h2>
        <p style="color: #666; font-size: 14px; margin: 0 0 28px 0;">Thank you for your purchase. Your order number is <strong>#{{ $order->order_id }}</strong>.</p>

        <!-- Order Details -->
        <div style="text-align: left; background: #f9f9f9; border-radius: 6px; padding: 24px; margin-bottom: 24px;">
            <h4 style="margin: 0 0 16px 0; color: #333; font-size: 14px;">Order Details</h4>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; font-size: 13px;">
                <div>
                    <span style="color: #888;">Order ID:</span>
                    <span style="color: #333; font-weight: 600; margin-left: 4px;">#{{ $order->order_id }}</span>
                </div>
                <div>
                    <span style="color: #888;">Status:</span>
                    <span style="background: #fff3e0; color: #e65100; padding: 2px 8px; border-radius: 3px; font-size: 12px; font-weight: 600; margin-left: 4px;">{{ ucfirst($order->order_status) }}</span>
                </div>
                <div>
                    <span style="color: #888;">Payment:</span>
                    <span style="color: #333; font-weight: 600; margin-left: 4px;">{{ $order->paymentMethod->method_name ?? '—' }}</span>
                </div>
            </div>

            <div style="margin-bottom: 16px; font-size: 13px;">
                <span style="color: #888;">Shipping to:</span>
                <span style="color: #333; margin-left: 4px;">{{ $order->shipping_address }}</span>
            </div>

            <div style="border-top: 1px solid #ddd; padding-top: 12px;">
                @foreach($order->items as $item)
                    <div style="display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; {{ !$loop->last ? 'border-bottom: 1px solid #eee;' : '' }}">
                        <div>
                            <span style="color: #333; font-weight: 500;">{{ $item->variant->product->product_name ?? 'N/A' }}</span>
                            <span style="color: #888;"> ({{ $item->variant->color ?? '—' }} / {{ $item->variant->size ?? '—' }}) &times; {{ $item->oi_quantity }}</span>
                        </div>
                        <span style="color: #333; font-weight: 600;">&#8369;{{ number_format($item->subtotal(), 2) }}</span>
                    </div>
                @endforeach
            </div>

            <div style="display: flex; justify-content: space-between; border-top: 2px solid #ddd; padding-top: 12px; margin-top: 8px;">
                <span style="font-size: 15px; font-weight: 700; color: #333;">Total</span>
                <span style="font-size: 1.2rem; font-weight: 700; color: #2e7d32;">&#8369;{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <div style="display: flex; gap: 12px; justify-content: center;">
            <a href="{{ route('orders.mine') }}" style="padding: 12px 28px; background: #1976d2; color: #fff; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 14px;">View My Orders</a>
            <a href="{{ route('home') }}" style="padding: 12px 28px; background: #ddd; color: #333; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 14px;">Continue Shopping</a>
        </div>
    </div>
</div>
@endsection

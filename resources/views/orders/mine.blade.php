@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px; max-width: 900px; margin: 0 auto;">

    @if (session('success'))
        <div style="background: #c8e6c9; padding: 12px 16px; border-radius: 4px; color: #2e7d32; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="margin: 0; color: #333; font-size: 1.5rem;">📦 My Orders</h2>
        <a href="{{ route('home') }}" style="color: #1976d2; text-decoration: none; font-size: 14px;">&larr; Back to Shop</a>
    </div>

    @if($orders->count() > 0)
        @foreach($orders as $order)
            <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 16px; overflow: hidden;">
                <!-- Order Header -->
                <div style="padding: 16px 20px; background: #f9f9f9; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <span style="font-weight: 700; color: #333; font-size: 14px;">Order #{{ $order->order_id }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        @php
                            $statusColors = [
                                'pending' => ['bg' => '#fff3e0', 'text' => '#e65100'],
                                'processing' => ['bg' => '#e3f2fd', 'text' => '#1565c0'],
                                'shipped' => ['bg' => '#e8f5e9', 'text' => '#2e7d32'],
                                'completed' => ['bg' => '#c8e6c9', 'text' => '#1b5e20'],
                                'cancelled' => ['bg' => '#ffcdd2', 'text' => '#c62828'],
                            ];
                            $sc = $statusColors[$order->order_status] ?? ['bg' => '#f5f5f5', 'text' => '#666'];
                        @endphp
                        <span style="background: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">{{ ucfirst($order->order_status) }}</span>
                        <span style="font-weight: 700; color: #2e7d32; font-size: 14px;">&#8369;{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>

                <!-- Order Items -->
                <div style="padding: 16px 20px;">
                    @foreach($order->items as $item)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; {{ !$loop->last ? 'border-bottom: 1px solid #f5f5f5;' : '' }}">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                @if($item->variant && $item->variant->product)
                                    <img src="{{ $item->variant->product->prod_image ? asset($item->variant->product->prod_image) : 'https://via.placeholder.com/40x40?text=N/A' }}" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #eee;">
                                    <div>
                                        <p style="margin: 0; font-size: 13px; font-weight: 600; color: #333;">{{ $item->variant->product->product_name }}</p>
                                        <p style="margin: 2px 0 0 0; font-size: 12px; color: #888;">{{ $item->variant->color ?? '—' }} / {{ $item->variant->size ?? '—' }}</p>
                                    </div>
                                @else
                                    <span style="color: #999; font-size: 13px;">Product unavailable</span>
                                @endif
                            </div>
                            <div style="text-align: right;">
                                <span style="font-size: 13px; color: #666;">{{ $item->oi_quantity }} &times; &#8369;{{ number_format($item->oi_price, 2) }}</span>
                                <span style="font-weight: 600; color: #333; margin-left: 12px;">&#8369;{{ number_format($item->subtotal(), 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Footer -->
                <div style="padding: 12px 20px; background: #fafafa; border-top: 1px solid #eee; font-size: 12px; color: #888; display: flex; justify-content: space-between;">
                    <span>{{ $order->paymentMethod->method_name ?? '—' }} &bull; {{ $order->shipping_address }}</span>
                </div>
            </div>
        @endforeach

        <div style="margin-top: 16px;">
            {{ $orders->links() }}
        </div>
    @else
        <div style="background: #fff; border-radius: 8px; padding: 48px 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-align: center;">
            <p style="font-size: 48px; margin: 0 0 16px 0;">📦</p>
            <h3 style="color: #333; margin: 0 0 8px 0;">No orders yet</h3>
            <p style="color: #666; margin: 0 0 24px 0; font-size: 14px;">Start shopping and your orders will appear here.</p>
            <a href="{{ route('home') }}" style="padding: 12px 32px; background: #1976d2; color: #fff; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 14px;">Browse Products</a>
        </div>
    @endif
</div>
@endsection
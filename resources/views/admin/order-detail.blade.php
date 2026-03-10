@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px; max-width: 900px; margin: 0 auto;">

    @if (session('success'))
        <div style="background: #c8e6c9; padding: 12px 16px; border-radius: 4px; color: #2e7d32; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('admin.orders') }}" style="color: #1976d2; text-decoration: none; font-size: 14px; display: inline-block; margin-bottom: 20px;">&larr; Back to Orders</a>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="margin: 0; color: #333; font-size: 1.5rem;">Order #{{ $order->order_id }}</h2>
        @php
            $statusColors = [
                'pending'    => ['bg' => '#fff3e0', 'text' => '#e65100'],
                'processing' => ['bg' => '#e3f2fd', 'text' => '#1565c0'],
                'shipped'    => ['bg' => '#e8eaf6', 'text' => '#283593'],
                'completed'  => ['bg' => '#c8e6c9', 'text' => '#1b5e20'],
                'cancelled'  => ['bg' => '#ffcdd2', 'text' => '#c62828'],
            ];
            $sc = $statusColors[$order->order_status] ?? ['bg' => '#f5f5f5', 'text' => '#666'];
        @endphp
        <span style="background: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; padding: 6px 16px; border-radius: 4px; font-size: 14px; font-weight: 700;">{{ ucfirst($order->order_status) }}</span>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
        <!-- Customer Info -->
        <div style="background: #fff; border-radius: 8px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h4 style="margin: 0 0 16px 0; color: #333; font-size: 14px; border-bottom: 1px solid #eee; padding-bottom: 10px;">👤 Customer Information</h4>
            <div style="font-size: 13px; color: #555; line-height: 2;">
                <div><strong>Name:</strong> {{ $order->user->fname ?? '' }} {{ $order->user->lname ?? '' }}</div>
                <div><strong>Email:</strong> {{ $order->user->email ?? '—' }}</div>
                <div><strong>Phone:</strong> {{ $order->user->phone ?? '—' }}</div>
            </div>
        </div>

        <!-- Order Info -->
        <div style="background: #fff; border-radius: 8px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h4 style="margin: 0 0 16px 0; color: #333; font-size: 14px; border-bottom: 1px solid #eee; padding-bottom: 10px;">📋 Order Information</h4>
            <div style="font-size: 13px; color: #555; line-height: 2;">
                <div><strong>Payment Method:</strong> {{ $order->paymentMethod->method_name ?? '—' }}</div>
                <div><strong>Shipping Address:</strong> {{ $order->shipping_address }}</div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 24px;">
        <h4 style="margin: 0; padding: 16px 20px; color: #333; font-size: 14px; border-bottom: 1px solid #eee; background: #fafafa;">🛍️ Order Items</h4>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f9f9f9; border-bottom: 1px solid #ddd;">
                    <th style="padding: 10px 16px; text-align: left; font-weight: 600; font-size: 13px; color: #333;">Product</th>
                    <th style="padding: 10px 16px; text-align: left; font-weight: 600; font-size: 13px; color: #333;">Variant</th>
                    <th style="padding: 10px 16px; text-align: center; font-weight: 600; font-size: 13px; color: #333;">Qty</th>
                    <th style="padding: 10px 16px; text-align: right; font-weight: 600; font-size: 13px; color: #333;">Price</th>
                    <th style="padding: 10px 16px; text-align: right; font-weight: 600; font-size: 13px; color: #333;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px 16px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                @if($item->variant && $item->variant->product)
                                    <img src="{{ $item->variant->product->prod_image ? asset($item->variant->product->prod_image) : 'https://via.placeholder.com/40x40?text=N/A' }}" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #eee;">
                                    <span style="font-size: 13px; font-weight: 600; color: #333;">{{ $item->variant->product->product_name }}</span>
                                @else
                                    <span style="color: #999; font-size: 13px;">Product unavailable</span>
                                @endif
                            </div>
                        </td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #555;">
                            {{ $item->variant->color ?? '—' }} / {{ $item->variant->size ?? '—' }}
                        </td>
                        <td style="padding: 12px 16px; text-align: center; font-size: 13px; color: #333;">{{ $item->oi_quantity }}</td>
                        <td style="padding: 12px 16px; text-align: right; font-size: 13px; color: #333;">&#8369;{{ number_format($item->oi_price, 2) }}</td>
                        <td style="padding: 12px 16px; text-align: right; font-size: 13px; font-weight: 600; color: #333;">&#8369;{{ number_format($item->subtotal(), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f9f9f9; border-top: 2px solid #ddd;">
                    <td colspan="4" style="padding: 14px 16px; text-align: right; font-weight: 700; font-size: 15px; color: #333;">Total</td>
                    <td style="padding: 14px 16px; text-align: right; font-weight: 700; font-size: 1.1rem; color: #2e7d32;">&#8369;{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Order Status Flow (Shopee-style) -->
    <div style="background: #fff; border-radius: 8px; padding: 28px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <h4 style="margin: 0 0 20px 0; color: #333; font-size: 14px;">📊 Order Status</h4>

        <!-- Status Progress Bar -->
        @php
            $steps = ['pending', 'processing', 'shipped', 'completed'];
            $currentIdx = array_search($order->order_status, $steps);
            $isCancelled = $order->order_status === 'cancelled';
        @endphp

        @if(!$isCancelled)
            <div style="display: flex; align-items: center; margin-bottom: 28px;">
                @foreach($steps as $idx => $step)
                    @php
                        $isActive = $currentIdx !== false && $idx <= $currentIdx;
                        $dotColor = $isActive ? '#2e7d32' : '#ddd';
                        $lineColor = ($currentIdx !== false && $idx < $currentIdx) ? '#2e7d32' : '#ddd';
                        $labels = ['pending' => '🕐 Pending', 'processing' => '✅ Confirmed', 'shipped' => '🚚 Shipped', 'completed' => '📦 Completed'];
                    @endphp
                    <div style="display: flex; flex-direction: column; align-items: center; flex: 0;">
                        <div style="width: 28px; height: 28px; border-radius: 50%; background: {{ $dotColor }}; display: flex; align-items: center; justify-content: center;">
                            @if($isActive)
                                <span style="color: #fff; font-size: 14px; font-weight: 700;">✓</span>
                            @endif
                        </div>
                        <span style="font-size: 11px; color: {{ $isActive ? '#333' : '#999' }}; margin-top: 6px; white-space: nowrap; font-weight: {{ $isActive ? '600' : 'normal' }};">{{ $labels[$step] }}</span>
                    </div>
                    @if(!$loop->last)
                        <div style="flex: 1; height: 3px; background: {{ $lineColor }}; margin: 0 8px; margin-bottom: 22px;"></div>
                    @endif
                @endforeach
            </div>
        @else
            <div style="background: #ffcdd2; padding: 14px 16px; border-radius: 4px; color: #c62828; margin-bottom: 20px; font-weight: 600;">
                ❌ This order has been cancelled.
            </div>
        @endif

        <!-- Update Status Buttons -->
        @if($order->order_status !== 'completed' && $order->order_status !== 'cancelled')
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                @if($order->order_status === 'pending')
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order->order_id) }}" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="order_status" value="processing">
                        <button type="submit" style="padding: 10px 24px; background: #1976d2; color: #fff; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: 600;" onclick="return confirm('Confirm this order?')">✅ Confirm Order</button>
                    </form>
                @elseif($order->order_status === 'processing')
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order->order_id) }}" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="order_status" value="shipped">
                        <button type="submit" style="padding: 10px 24px; background: #283593; color: #fff; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: 600;" onclick="return confirm('Mark as shipped?')">🚚 Mark as Shipped</button>
                    </form>
                @elseif($order->order_status === 'shipped')
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order->order_id) }}" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="order_status" value="completed">
                        <button type="submit" style="padding: 10px 24px; background: #2e7d32; color: #fff; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: 600;" onclick="return confirm('Mark as completed?')">📦 Mark as Completed</button>
                    </form>
                @endif

                @if(in_array($order->order_status, ['pending', 'processing']))
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order->order_id) }}" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="order_status" value="cancelled">
                        <button type="submit" style="padding: 10px 24px; background: #d32f2f; color: #fff; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: 600;" onclick="return confirm('Cancel this order? Stock will be restored.')">❌ Cancel Order</button>
                    </form>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

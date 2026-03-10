@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; background: #f5f5f5;">
    <div style="padding: 48px 32px;">
        <div style="background: #fff; border-radius: 8px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h2 style="margin: 0; color: #333;">📦 Order Management</h2>
                <span style="font-size: 14px; color: #666;">{{ $orders->count() }} total {{ Str::plural('order', $orders->count()) }}</span>
            </div>

            @if (session('success'))
                <div style="background: #c8e6c9; padding: 12px 16px; border-radius: 4px; color: #2e7d32; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            @if ($orders->count() > 0)
                <div style="overflow-x: auto;">
                    <table id="ordersTable" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f5f5f5; border-bottom: 2px solid #ddd;">
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Order ID</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Customer</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Total</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Payment</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Status</th>
                                <th style="padding: 12px; text-align: center; font-weight: 600; color: #333;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
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
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td style="padding: 12px; font-weight: 600; color: #333;">#{{ $order->order_id }}</td>
                                    <td style="padding: 12px; color: #333;">
                                        @if($order->user)
                                            {{ $order->user->fname }} {{ $order->user->lname }}
                                        @else
                                            <span style="color: #999;">Deleted User</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px; font-weight: 600; color: #2e7d32;">&#8369;{{ number_format($order->total_amount, 2) }}</td>
                                    <td style="padding: 12px; color: #555; font-size: 13px;">{{ $order->paymentMethod->method_name ?? '—' }}</td>
                                    <td style="padding: 12px;">
                                        <span style="background: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">{{ ucfirst($order->order_status) }}</span>
                                    </td>
                                    <td style="padding: 12px; text-align: center;">
                                        <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                            <a href="{{ route('admin.orders.show', $order->order_id) }}" style="padding: 5px 12px; background: #1976d2; color: #fff; border: none; border-radius: 3px; text-decoration: none; font-size: 12px; font-weight: 500;">View</a>

                                            @if($order->order_status !== 'completed' && $order->order_status !== 'cancelled')
                                                <form method="POST" action="{{ route('admin.orders.updateStatus', $order->order_id) }}" style="display: inline;">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="order_status" onchange="this.form.submit()" style="padding: 4px 8px; border: 1px solid #ddd; border-radius: 3px; font-size: 12px; cursor: pointer; background: #fff;">
                                                        <option value="" disabled selected>Update...</option>
                                                        @if($order->order_status === 'pending')
                                                            <option value="processing">✅ Confirm</option>
                                                            <option value="cancelled">❌ Cancel</option>
                                                        @elseif($order->order_status === 'processing')
                                                            <option value="shipped">🚚 Ship</option>
                                                            <option value="cancelled">❌ Cancel</option>
                                                        @elseif($order->order_status === 'shipped')
                                                            <option value="completed">✅ Complete</option>
                                                        @endif
                                                    </select>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="background: #f0f0f0; padding: 20px; border-radius: 4px; text-align: center; color: #666;">
                    No orders found.
                </div>
            @endif
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#ordersTable').DataTable({
            pageLength: 15,
            order: [[0, 'desc']],
            language: {
                search: "Search orders:"
            }
        });
    });
</script>
@endsection

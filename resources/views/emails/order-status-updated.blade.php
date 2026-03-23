<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 30px;
        }
        .status-update {
            background: linear-gradient(135deg, #f0f4ff 0%, #f5f0ff 100%);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-right: 10px;
        }
        .status-old {
            background-color: #e0e0e0;
            color: #666;
        }
        .status-arrow {
            display: inline-block;
            margin: 0 10px;
            color: #999;
            font-weight: bold;
        }
        .status-new {
            background-color: #667eea;
            color: white;
        }
        .message {
            font-size: 1.1rem;
            color: #333;
            margin: 15px 0;
            font-weight: 500;
        }
        h2 {
            color: #333;
            font-size: 18px;
            margin-top: 25px;
            margin-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        .order-summary {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }
        .summary-item.total {
            border-top: 2px solid #e0e0e0;
            padding-top: 10px;
            font-weight: 700;
            font-size: 1.1rem;
            color: #667eea;
        }
        .items-list {
            margin: 15px 0;
        }
        .item {
            border-bottom: 1px solid #f0f0f0;
            padding: 12px 0;
            font-size: 0.95rem;
        }
        .item:last-child {
            border-bottom: none;
        }
        .item-name {
            font-weight: 600;
            color: #333;
        }
        .item-details {
            color: #999;
            font-size: 0.85rem;
        }
        .item-price {
            color: #667eea;
            font-weight: 500;
        }
        .shipping-info {
            background-color: #fffbf0;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            border-left: 4px solid #ffa500;
        }
        .shipping-info p {
            margin: 5px 0;
            font-size: 0.95rem;
            color: #333;
        }
        .label {
            font-weight: 600;
            color: #333;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            font-size: 13px;
            color: #999;
        }
        .cta-button {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 12px 30px;
            border-radius: 4px;
            text-decoration: none;
            margin: 20px 0;
            font-weight: 600;
        }
        .cta-button:hover {
            background-color: #5568d3;
        }
        .note {
            background-color: #e8f4f8;
            padding: 15px;
            border-radius: 4px;
            border-left: 4px solid #0288d1;
            margin: 20px 0;
            font-size: 0.9rem;
            color: #0288d1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 Order Status Update</h1>
            <p style="margin: 10px 0; opacity: 0.9;">Order #{{ $order->order_id }}</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello {{ $customerName }},
            </div>

            <div class="status-update">
                <div class="message">{{ $statusMessage }}</div>
                
                <div style="margin-top: 15px;">
                    <span class="status-badge status-old">{{ ucfirst($oldStatus) }}</span>
                    <span class="status-arrow">→</span>
                    <span class="status-badge status-new">{{ ucfirst($newStatus) }}</span>
                </div>
            </div>

            <h2>Order Summary</h2>
            <div class="order-summary">
                <div class="summary-item">
                    <span>Order Number:</span>
                    <span>#{{ $order->order_id }}</span>
                </div>
                <div class="summary-item">
                    <span>Order Total:</span>
                    <span>₱{{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="summary-item">
                    <span>Payment Method:</span>
                    <span>{{ $order->paymentMethod->method_name ?? 'Not specified' }}</span>
                </div>
            </div>

            <h2>Items Ordered</h2>
            <div class="items-list">
                @foreach($order->items as $item)
                <div class="item">
                    <div class="item-name">{{ $item->variant->product->product_name }}</div>
                    <div class="item-details">
                        Color: {{ $item->variant->color ?? 'N/A' }} | Size: {{ $item->variant->size ?? 'N/A' }} | Qty: {{ $item->oi_quantity }}
                    </div>
                    <div class="item-price">₱{{ number_format($item->oi_price * $item->oi_quantity, 2) }}</div>
                </div>
                @endforeach
            </div>

            <h2>Shipping Address</h2>
            <div class="shipping-info">
                <p class="label">{{ $customerName }}</p>
                <p>{{ $order->shipping_address }}</p>
            </div>

            @if($newStatus === 'shipped')
            <div class="note">
                <strong>📦 Your package is on the way!</strong> Track your shipment using the tracking number provided. You'll receive it in a separate email.
            </div>
            @elseif($newStatus === 'delivered')
            <div class="note">
                <strong>✓ Your order has arrived!</strong> We hope you're happy with your purchase. Please let us know if you have any questions or need help with your order.
            </div>
            @endif

            <div style="text-align: center;">
                <a href="{{ url('/orders/' . $order->order_id) }}" class="cta-button">View Order Details</a>
            </div>

            @if($hasPdfAttached)
            <div class="note">
                <strong>✓ Receipt Attached:</strong> Your order receipt is attached to this email in PDF format for your records.
            </div>
            @endif
        </div>

        <div class="footer">
            <p>Thank you for shopping with us! If you have any questions, please contact our customer support.</p>
            <p style="margin-top: 15px; border-top: 1px solid #ddd; padding-top: 15px;">
                © {{ date('Y') }} Thread & Trend. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>

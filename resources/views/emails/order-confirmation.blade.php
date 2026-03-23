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
        .order-number {
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #667eea;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .order-number p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        .order-number .number {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        h2 {
            color: #333;
            font-size: 18px;
            margin-top: 25px;
            margin-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        .greeting {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table thead {
            background-color: #f9f9f9;
        }
        .items-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        .items-table tr:last-child td {
            border-bottom: none;
        }
        .product-name {
            font-weight: 500;
            color: #333;
        }
        .product-variant {
            font-size: 13px;
            color: #999;
        }
        .qty {
            text-align: center;
            color: #666;
        }
        .price {
            text-align: right;
            color: #333;
            font-weight: 500;
        }
        .summary {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .summary-row.total {
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
            border-top: 2px solid #e0e0e0;
            padding-top: 10px;
            margin-top: 10px;
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
            font-size: 14px;
            color: #333;
        }
        .shipping-label {
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
            font-size: 14px;
            color: #0288d1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ Order Confirmed!</h1>
            <p style="margin: 10px 0; opacity: 0.9;">Thank you for your purchase</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello {{ $customerName }},<br>
                We've received your order and it's being processed. Here's a summary of your purchase.
            </div>

            <div class="order-number">
                <p>Order Number</p>
                <div class="number">#{{ $order->order_id }}</div>
                <p>Placed on {{ $orderDate }}</p>
            </div>

            <h2>Order Details</h2>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 45%;">Product</th>
                        <th style="width: 15%; text-align: center;">Quantity</th>
                        <th style="width: 20%; text-align: right;">Price</th>
                        <th style="width: 20%; text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div class="product-name">{{ $item->variant->product->product_name }}</div>
                            <div class="product-variant">
                                {{ $item->variant->color }} / {{ $item->variant->size }}
                            </div>
                        </td>
                        <td class="qty">{{ $item->oi_quantity }}</td>
                        <td class="price">₱{{ number_format($item->oi_price, 2) }}</td>
                        <td class="price">₱{{ number_format($item->oi_price * $item->oi_quantity, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>₱{{ $orderTotal }}</span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>Free</span>
                </div>
                <div class="summary-row total">
                    <span>Total Amount:</span>
                    <span>₱{{ $orderTotal }}</span>
                </div>
            </div>

            <h2>Shipping Address</h2>
            <div class="shipping-info">
                <p class="shipping-label">{{ $customerName }}</p>
                <p>{{ $order->shipping_address }}</p>
            </div>

            <h2>Payment Method</h2>
            <div class="shipping-info" style="border-left-color: #667eea; background-color: #f0f4ff;">
                <p>{{ $order->paymentMethod->method_name ?? 'Not specified' }}</p>
            </div>

            <div class="note">
                <strong>What happens next?</strong> We'll prepare your order for shipment and send you a tracking number via email. You can also check your order status anytime in your account.
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/orders/' . $order->order_id) }}" class="cta-button">Track Your Order</a>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for shopping with us! If you have any questions, please don't hesitate to contact our customer support.</p>
            <p style="margin-top: 15px; border-top: 1px solid #ddd; padding-top: 15px;">
                © {{ date('Y') }} Thread & Trend. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 30px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
        }
        
        .company-info h1 {
            font-size: 28px;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .company-info p {
            color: #999;
            font-size: 13px;
        }
        
        .receipt-title {
            text-align: right;
        }
        
        .receipt-title h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 5px;
        }
        
        .receipt-number {
            font-size: 14px;
            color: #667eea;
            font-weight: bold;
        }
        
        .order-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        
        .info-box {
            flex: 1;
        }
        
        .info-box h3 {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        
        .info-box p {
            font-size: 14px;
            line-height: 1.8;
            color: #333;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table thead {
            background-color: #f5f5f5;
        }
        
        .items-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #ddd;
        }
        
        .items-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }
        
        .items-table tr:last-child td {
            border-bottom: none;
        }
        
        .text-right {
            text-align: right;
        }
        
        .amount {
            font-weight: 600;
            color: #333;
        }
        
        .summary {
            margin-left: auto;
            width: 300px;
            margin-bottom: 40px;
            margin-top: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }
        
        .summary-row.total {
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            padding: 15px 0;
            font-size: 16px;
            font-weight: bold;
            color: #667eea;
        }
        
        .summary-label {
            color: #666;
        }
        
        .summary-value {
            text-align: right;
            font-weight: 600;
            color: #333;
        }
        
        .total-value {
            color: #667eea;
        }
        
        .notes {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .notes h4 {
            font-size: 13px;
            color: #333;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .notes p {
            font-size: 12px;
            color: #666;
        }
        
        .footer {
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            margin-top: 40px;
        }
        
        .footer p {
            font-size: 12px;
            color: #999;
            margin: 5px 0;
        }
        
        .footer-divider {
            display: inline-block;
            width: 100%;
            height: 1px;
            background-color: #ddd;
            margin: 15px 0;
        }
        
        .payment-info {
            background-color: #e8f4f8;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        
        .payment-info strong {
            color: #0288d1;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>Thread & Trend</h1>
                <p>Premium Clothing Store</p>
                <p>Email: support@threadtrend.com</p>
                <p>Phone: (555) 123-4567</p>
            </div>
            <div class="receipt-title">
                <h2>ORDER RECEIPT</h2>
                <div class="receipt-number">Order #{{ $order->order_id }}</div>
            </div>
        </div>

        <!-- Order Info -->
        <div class="order-info">
            <div class="info-box">
                <h3>Bill To</h3>
                <p>
                    {{ $customerName }}<br>
                    {{ $customerEmail }}<br>
                </p>
            </div>
            
            <div class="info-box">
                <h3>Ship To</h3>
                <p>{{ $order->shipping_address }}</p>
            </div>
            
            <div class="info-box">
                <h3>Order Details</h3>
                <p>
                    <strong>Date:</strong> {{ $orderDate }}<br>
                    <strong>Status:</strong> {{ ucfirst($order->order_status) }}<br>
                    <strong>Payment:</strong> {{ $order->paymentMethod->method_name ?? 'N/A' }}<br>
                </p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Variant</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->variant->product->product_name }}</td>
                    <td>{{ $item->variant->color ?? 'N/A' }} / {{ $item->variant->size ?? 'N/A' }}</td>
                    <td class="text-right">{{ $item->oi_quantity }}</td>
                    <td class="text-right amount">Php {{ number_format($item->oi_price, 2) }}</td>
                    <td class="text-right amount">Php {{ number_format($item->oi_price * $item->oi_quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-row">
                <span class="summary-label">Subtotal:</span>
                <span class="summary-value">Php {{ number_format($order->total_amount, 2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Shipping:</span>
                <span class="summary-value">Free</span>
            </div>
            <div class="summary-row total">
                <span class="summary-label">Total Amount:</span>
                <span class="summary-value total-value">Php {{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="payment-info">
            <strong>Payment Status:</strong> 
            @if($order->payment && $order->payment->payment_status === 'paid')
                <span style="color: #2e7d32; font-weight: bold;">PAID</span><br>
                Thank you! Your payment has been received.
            @else
                <span style="color: #e65100; font-weight: bold;">PENDING</span><br>
                Thank you for your purchase! Your order will be processed shortly.
            @endif
        </div>

        <!-- Notes -->
        <div class="notes">
            <h4>Important Information</h4>
            <p>
                &bull; Please keep this receipt for your records.<br>
                &bull; You will receive a shipping notification once your order is dispatched.<br>
                &bull; For any questions or concerns, please contact our customer support.<br>
                &bull; Returns and exchanges must be made within 30 days of delivery.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>&copy; {{ date('Y') }} Thread & Trend. All rights reserved.</p>
            <p style="margin-top: 20px; color: #bbb; font-size: 11px;">This is an automated receipt. No signature is required.</p>
        </div>
    </div>
</body>
</html>

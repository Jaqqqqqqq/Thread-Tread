<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #6366f1; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9fafb; }
        .panel { background: white; padding: 15px; margin: 15px 0; border: 1px solid #e5e7eb; }
        .table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .table th { background: #f3f4f6; padding: 10px; text-align: left; border: 1px solid #e5e7eb; }
        .table td { padding: 10px; border: 1px solid #e5e7eb; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Receipt</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $customerName }},</p>
            <p>Your order receipt is attached below. Your payment has been received and processed successfully.</p>
            
            <div class="panel">
                <h3>Order #{{ $order->order_id }}</h3>
                <p><strong>Order Date:</strong> {{ $orderDate }}</p>
                <p><strong>Payment Status:</strong> <span style="color: #2e7d32; font-weight: bold;">PAID ✓</span></p>
                <p><strong>Order Total:</strong> ₱{{ $orderTotal }}</p>
            </div>
            
            <h3>Order Items</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Variant</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->variant->product->product_name }}</td>
                        <td>{{ $item->variant->color }} / {{ $item->variant->size }}</td>
                        <td>{{ $item->oi_quantity }}</td>
                        <td>₱{{ number_format($item->oi_price, 2) }}</td>
                        <td>₱{{ number_format($item->oi_quantity * $item->oi_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="panel">
                <h4>Shipping Address</h4>
                <p>{{ $order->shipping_address }}</p>
            </div>
            
            <p>Thank you for your order! If you have any questions, please contact us.</p>
        </div>
        
        <div class="footer">
            <p>{{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>

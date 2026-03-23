@extends('layouts.app')

@section('content')
<div style="padding: 40px 32px; max-width: 600px; margin: 0 auto;">
    <h1 style="margin: 0 0 8px 0; color: #333; font-size: 2rem;">Update Order Status</h1>
    <p style="color: #666; margin: 0 0 32px 0;">Order #{{ $order->order_id }}</p>

    @if ($errors->any())
    <div style="background-color: #ffebee; border-left: 4px solid #c62828; padding: 16px; border-radius: 4px; margin-bottom: 20px;">
        <h4 style="color: #c62828; margin: 0 0 8px 0;">Please fix the following errors:</h4>
        <ul style="margin: 0; padding-left: 20px; color: #b71c1c;">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 30px; margin-bottom: 20px;">
        
        <!-- Current Order Info -->
        <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
            <h3 style="margin: 0 0 16px 0; color: #333; font-size: 1.1rem;">Order Information</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 0.85rem; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Customer</label>
                    <p style="margin: 0; color: #333; font-weight: 500;">{{ $order->user->fname }} {{ $order->user->lname }}</p>
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Email</label>
                    <p style="margin: 0; color: #333; font-weight: 500;">{{ $order->user->email }}</p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 0.85rem; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Total Amount</label>
                    <p style="margin: 0; color: #667eea; font-weight: 600; font-size: 1.2rem;">₱{{ number_format($order->total_amount, 2) }}</p>
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Current Status</label>
                    <p style="margin: 0; color: #333; font-weight: 500;">
                        <span style="display: inline-block; padding: 4px 12px; background-color: #e8f5e9; color: #2e7d32; border-radius: 20px; font-size: 0.85rem;">
                            {{ ucfirst($order->order_status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Status Update Form -->
        <form method="POST" action="{{ route('orders.update-status', ['order' => $order->order_id]) }}">
            @csrf

            <h3 style="margin: 0 0 16px 0; color: #333; font-size: 1.1rem;">Update Status</h3>

            <div style="margin-bottom: 20px;">
                <label for="order_status" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">New Status</label>
                <select name="order_status" id="order_status" style="width: 100%; padding: 12px 16px; border: 1px solid #ddd; border-radius: 8px; font-size: 0.95rem;">
                    <option value="">-- Select New Status --</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ $order->order_status === $status ? 'disabled' : '' }}>
                            @switch($status)
                                @case('pending')
                                    ⏳ Pending
                                    @break
                                @case('confirmed')
                                    ✓ Confirmed
                                    @break
                                @case('processing')
                                    🔄 Processing
                                    @break
                                @case('shipped')
                                    📦 Shipped
                                    @break
                                @case('delivered')
                                    ✓ Delivered
                                    @break
                                @case('cancelled')
                                    ✗ Cancelled
                                    @break
                            @endswitch
                        </option>
                    @endforeach
                </select>
                @error('order_status')
                <span style="color: #c62828; font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="background-color: #f0f4ff; padding: 16px; border-radius: 8px; border-left: 4px solid #667eea; margin-bottom: 24px;">
                <strong style="color: #667eea;">📧 Email Notification</strong>
                <p style="margin: 8px 0 0 0; color: #555; font-size: 0.9rem;">
                    An automated email with the order receipt (PDF) will be sent to the customer at <strong>{{ $order->user->email }}</strong> to notify them of the status update.
                </p>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" style="flex: 1; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.95rem;">
                    Update Status & Send Email
                </button>
                <a href="{{ back()->getTargetUrl() }}" style="flex: 1; padding: 12px 24px; background: #f0f0f0; color: #333; border: 1px solid #ddd; border-radius: 8px; font-weight: 600; cursor: pointer; text-align: center; text-decoration: none; font-size: 0.95rem;">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Status Legend -->
    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 30px;">
        <h3 style="margin: 0 0 16px 0; color: #333; font-size: 1.1rem;">Status Guide</h3>
        
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; gap: 12px; align-items: flex-start;">
                <span style="font-size: 1.2rem;">⏳</span>
                <div>
                    <strong style="color: #333;">Pending</strong>
                    <p style="margin: 4px 0 0 0; color: #666; font-size: 0.9rem;">Order received, awaiting confirmation</p>
                </div>
            </div>

            <div style="display: flex; gap: 12px; align-items: flex-start;">
                <span style="font-size: 1.2rem;">✓</span>
                <div>
                    <strong style="color: #333;">Confirmed</strong>
                    <p style="margin: 4px 0 0 0; color: #666; font-size: 0.9rem;">Order has been confirmed by admin</p>
                </div>
            </div>

            <div style="display: flex; gap: 12px; align-items: flex-start;">
                <span style="font-size: 1.2rem;">🔄</span>
                <div>
                    <strong style="color: #333;">Processing</strong>
                    <p style="margin: 4px 0 0 0; color: #666; font-size: 0.9rem;">Order is being picked and packed</p>
                </div>
            </div>

            <div style="display: flex; gap: 12px; align-items: flex-start;">
                <span style="font-size: 1.2rem;">📦</span>
                <div>
                    <strong style="color: #333;">Shipped</strong>
                    <p style="margin: 4px 0 0 0; color: #666; font-size: 0.9rem;">Order is on the way to customer</p>
                </div>
            </div>

            <div style="display: flex; gap: 12px; align-items: flex-start;">
                <span style="font-size: 1.2rem;">✓</span>
                <div>
                    <strong style="color: #333;">Delivered</strong>
                    <p style="margin: 4px 0 0 0; color: #666; font-size: 0.9rem;">Order has been delivered successfully</p>
                </div>
            </div>

            <div style="display: flex; gap: 12px; align-items: flex-start;">
                <span style="font-size: 1.2rem;">✗</span>
                <div>
                    <strong style="color: #333;">Cancelled</strong>
                    <p style="margin: 4px 0 0 0; color: #666; font-size: 0.9rem;">Order has been cancelled</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

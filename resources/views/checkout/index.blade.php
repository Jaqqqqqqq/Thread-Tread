@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px; max-width: 800px; margin: 0 auto;">

    @if (session('error'))
        <div style="background: #ffcdd2; padding: 12px 16px; border-radius: 4px; color: #c62828; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <a href="{{ route('cart.index') }}" style="color: #1976d2; text-decoration: none; font-size: 14px; display: inline-block; margin-bottom: 20px;">&larr; Back to Cart</a>

    <h2 style="margin: 0 0 24px 0; color: #333; font-size: 1.5rem;">Checkout</h2>

    <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 24px;">
        <!-- Left: Order Form -->
        <div style="background: #fff; border-radius: 8px; padding: 28px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <form method="POST" action="{{ route('checkout.place') }}">
                @csrf

                <!-- Shipping Address -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #333; font-size: 14px;">Shipping Address</label>
                    @if($addresses->count() > 0)
                        <select name="shipping_address" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box; background: #fff;">
                            @foreach($addresses as $addr)
                                <option value="{{ $addr->formatted_address }}" {{ $addr->is_default ? 'selected' : '' }}>
                                    {{ $addr->formatted_address }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" name="shipping_address" required placeholder="Enter your full shipping address" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                        <p style="color: #888; font-size: 12px; margin: 6px 0 0 0;">You haven't saved any addresses yet. Type your address above.</p>
                    @endif
                    @error('shipping_address')
                        <span style="color: #c62828; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #333; font-size: 14px;">Payment Method</label>
                    @if($methods->count() > 0)
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            @foreach($methods as $m)
                                <label style="display: flex; align-items: center; gap: 10px; padding: 12px 14px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; transition: border-color 0.2s;">
                                    <input type="radio" name="method_id" value="{{ $m->method_id }}" {{ $loop->first ? 'checked' : '' }} style="margin: 0;">
                                    <span style="font-size: 14px; color: #333;">{{ $m->method_name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p style="color: #888; font-size: 13px;">No payment methods available.</p>
                    @endif
                    @error('method_id')
                        <span style="color: #c62828; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" style="width: 100%; padding: 14px; background: #2e7d32; color: #fff; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: 600;" onclick="return confirm('Place this order?')">Place Order</button>
            </form>
        </div>

        <!-- Right: Order Summary -->
        <div>
            <div style="background: #fff; border-radius: 8px; padding: 28px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h3 style="margin: 0 0 16px 0; color: #333; font-size: 1.1rem;">Order Summary</h3>

                <div style="border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom: 12px;">
                    @foreach($cart->items as $item)
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; padding: 8px 0; {{ !$loop->last ? 'border-bottom: 1px solid #f5f5f5;' : '' }}">
                            <div style="flex: 1;">
                                <p style="margin: 0; font-size: 13px; font-weight: 600; color: #333;">{{ $item->variant->product->product_name ?? 'N/A' }}</p>
                                <p style="margin: 2px 0 0 0; font-size: 12px; color: #888;">{{ $item->variant->color ?? '—' }} / {{ $item->variant->size ?? '—' }} &times; {{ $item->quantity }}</p>
                            </div>
                            <span style="font-size: 13px; font-weight: 600; color: #333; white-space: nowrap;">&#8369;{{ number_format($item->subtotal(), 2) }}</span>
                        </div>
                    @endforeach
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 15px; font-weight: 700; color: #333;">Total</span>
                    <span style="font-size: 1.3rem; font-weight: 700; color: #2e7d32;">&#8369;{{ number_format($cart->calculateTotal(), 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

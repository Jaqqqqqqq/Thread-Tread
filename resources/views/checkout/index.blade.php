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
                                    <input type="radio" name="method_id" value="{{ $m->method_id }}" class="payment-method-radio" {{ $loop->first ? 'checked' : '' }} style="margin: 0;">
                                    <span style="font-size: 14px; color: #333;">{{ $m->method_name }}</span>
                                </label>
                                
                                @if($m->method_name === 'Online Payment')
                                    <div id="onlinePaymentInput" style="display: {{ $loop->first ? 'block' : 'none' }}; margin: -4px 0 12px 28px; background: #f9f9f9; padding: 12px; border-radius: 4px; border: 1px solid #e0e0e0;">
                                        <label for="payment_ref" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px; color: #333;">Payment Reference/Transaction ID</label>
                                        <input type="text" id="payment_ref" name="payment_ref" placeholder="Enter payment reference (e.g., GCash/Credit Card ref)" value="{{ old('payment_ref') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                                        <small style="color: #888; display: block; margin-top: 6px;">Please provide your payment reference to confirm the transaction.</small>
                                        @error('payment_ref')
                                            <span style="color: #c62828; font-size: 12px; display: block; margin-top: 4px;">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p style="color: #888; font-size: 13px;">No payment methods available.</p>
                    @endif
                    @error('method_id')
                        <span style="color: #c62828; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="button" id="placeOrderBtn" style="width: 100%; padding: 14px; background: #2e7d32; color: #fff; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: 600;">Place Order</button>
                <!-- Custom Confirmation Modal -->
                <div id="orderConfirmModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:1000; align-items:center; justify-content:center;">
                    <div style="background:#fff; border-radius:8px; max-width:350px; margin:auto; padding:32px 24px; box-shadow:0 4px 24px rgba(0,0,0,0.12); text-align:center;">
                        <h4 style="margin:0 0 16px 0; color:#2e7d32; font-size:1.1rem;">Confirm Order</h4>
                        <p style="color:#333; font-size:15px; margin-bottom:24px;">Are you sure you want to place this order?</p>
                        <div style="display:flex; gap:12px; justify-content:center;">
                            <button id="confirmOrderYes" style="padding:8px 20px; background:#2e7d32; color:#fff; border:none; border-radius:4px; font-weight:600; cursor:pointer;">Yes, Place Order</button>
                            <button id="confirmOrderNo" style="padding:8px 20px; background:#eee; color:#333; border:none; border-radius:4px; font-weight:600; cursor:pointer;">Cancel</button>
                        </div>
                    </div>
                </div>
            @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const placeOrderBtn = document.getElementById('placeOrderBtn');
                    const modal = document.getElementById('orderConfirmModal');
                    const yesBtn = document.getElementById('confirmOrderYes');
                    const noBtn = document.getElementById('confirmOrderNo');
                    const form = placeOrderBtn.closest('form');
                    
                    // Payment method toggle functionality
                    const paymentRadios = document.querySelectorAll('.payment-method-radio');
                    const onlinePaymentInput = document.getElementById('onlinePaymentInput');
                    const paymentRefInput = document.getElementById('payment_ref');
                    
                    // Function to toggle online payment input visibility
                    function togglePaymentInput() {
                        const selectedMethod = document.querySelector('.payment-method-radio:checked');
                        const selectedLabel = selectedMethod.closest('label').textContent.trim();
                        
                        if (selectedLabel.includes('Online Payment')) {
                            onlinePaymentInput.style.display = 'block';
                            paymentRefInput.setAttribute('required', 'required');
                        } else {
                            onlinePaymentInput.style.display = 'none';
                            paymentRefInput.removeAttribute('required');
                            paymentRefInput.value = '';
                        }
                    }
                    
                    // Add event listeners to all payment method radios
                    paymentRadios.forEach(radio => {
                        radio.addEventListener('change', togglePaymentInput);
                    });
                    
                    // Validate payment reference before order submission
                    placeOrderBtn.addEventListener('click', function(e) {
                        const selectedMethod = document.querySelector('.payment-method-radio:checked');
                        const selectedLabel = selectedMethod.closest('label').textContent.trim();
                        
                        if (selectedLabel.includes('Online Payment') && !paymentRefInput.value.trim()) {
                            e.preventDefault();
                            paymentRefInput.style.borderColor = '#d32f2f';
                            alert('Please enter your payment reference for online payment.');
                            return;
                        }
                        
                        modal.style.display = 'flex';
                    });
                    
                    yesBtn.addEventListener('click', function() {
                        modal.style.display = 'none';
                        form.submit();
                    });
                    noBtn.addEventListener('click', function() {
                        modal.style.display = 'none';
                    });
                    // Optional: close modal on outside click
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) modal.style.display = 'none';
                    });
                });
            </script>
            @endpush
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

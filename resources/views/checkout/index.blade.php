@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px; max-width: 800px; margin: 0 auto;">

    @if (session('error'))
        <div style="background: #ffcdd2; padding: 12px 16px; border-radius: 4px; color: #c62828; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    @php
        // Calculate total for selected items only
        $selectedTotal = 0;
        foreach($cart->items as $item) {
            if(in_array($item->cart_item_id, $selectedItems)) {
                $selectedTotal += $item->subtotal();
            }
        }
    @endphp

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
                                        <label for="payment_amount" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px; color: #333;">Payment Amount</label>
                                        <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                            <span style="font-size: 13px; color: #666; width: 40px;">₱</span>
                                            <input type="number" id="payment_amount" name="payment_amount" placeholder="0.00" step="0.01" value="{{ old('payment_amount') }}" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                                        </div>
                                        <small style="color: #888; display: block;">Order Total: <strong>₱{{ number_format($selectedTotal, 2) }}</strong></small>
                                        @error('payment_amount')
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
                
                <!-- Hidden inputs for selected items -->
                @foreach($cart->items as $item)
                    @if(in_array($item->cart_item_id, $selectedItems))
                        <input type="hidden" name="selected_items[]" value="{{ $item->cart_item_id }}">
                    @endif
                @endforeach
                
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
                    const paymentAmountInput = document.getElementById('payment_amount');
                    
                    // Function to toggle online payment input visibility
                    function togglePaymentInput() {
                        const selectedMethod = document.querySelector('.payment-method-radio:checked');
                        const selectedLabel = selectedMethod.closest('label').textContent.trim();
                        
                        if (selectedLabel.includes('Online Payment')) {
                            onlinePaymentInput.style.display = 'block';
                            paymentAmountInput.setAttribute('required', 'required');
                        } else {
                            onlinePaymentInput.style.display = 'none';
                            paymentAmountInput.removeAttribute('required');
                            paymentAmountInput.value = '';
                        }
                    }
                    
                    // Add event listeners to all payment method radios
                    paymentRadios.forEach(radio => {
                        radio.addEventListener('change', togglePaymentInput);
                    });
                    
                    // Show custom notification instead of alert
                    function showNotification(message, type = 'error') {
                        const notification = document.createElement('div');
                        notification.style.cssText = 'position:fixed;top:20px;right:20px;background:' + (type === 'error' ? '#d32f2f' : '#2e7d32') + ';color:#fff;padding:16px 24px;border-radius:4px;font-size:14px;z-index:10000;box-shadow:0 4px 12px rgba(0,0,0,0.15);';
                        notification.textContent = message;
                        document.body.appendChild(notification);
                        
                        setTimeout(() => {
                            notification.remove();
                        }, 4000);
                    }
                    
                    // Validate and place order
                    placeOrderBtn.addEventListener('click', function(e) {
                        // Check if hidden selected_items exist
                        const selectedItemsInputs = document.querySelectorAll('input[name="selected_items[]"]');
                        if (selectedItemsInputs.length === 0) {
                            e.preventDefault();
                            showNotification('Please select at least one product to checkout.', 'error');
                            return;
                        }
                        
                        const selectedMethod = document.querySelector('.payment-method-radio:checked');
                        const selectedLabel = selectedMethod.closest('label').textContent.trim();
                        
                        if (selectedLabel.includes('Online Payment')) {
                            const paymentAmount = parseFloat(paymentAmountInput.value);
                            const selectedTotal = parseFloat(document.getElementById('selectedTotal').textContent.replace(/[₱,]/g, ''));
                            
                            if (!paymentAmountInput.value.trim()) {
                                e.preventDefault();
                                paymentAmountInput.style.borderColor = '#d32f2f';
                                showNotification('Please enter the payment amount.', 'error');
                                return;
                            }
                            
                            if (isNaN(paymentAmount) || paymentAmount <= 0) {
                                e.preventDefault();
                                paymentAmountInput.style.borderColor = '#d32f2f';
                                showNotification('Payment amount must be a valid number greater than 0.', 'error');
                                return;
                            }
                            
                            if (paymentAmount < selectedTotal) {
                                e.preventDefault();
                                paymentAmountInput.style.borderColor = '#d32f2f';
                                showNotification('Payment amount must be at least ₱' + selectedTotal.toFixed(2) + ' (order total).', 'error');
                                return;
                            }
                            
                            paymentAmountInput.style.borderColor = '';
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
                <p style="font-size: 12px; color: #888; margin: 0 0 16px 0;">Selected products</p>

                <div style="border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom: 12px;">
                    @foreach($cart->items as $item)
                        @if(in_array($item->cart_item_id, $selectedItems))
                            <div style="display: flex; align-items: flex-start; gap: 12px; padding: 8px 0; border-bottom: 1px solid #f5f5f5;">
                                <div style="flex: 1;">
                                    <p style="margin: 0; font-size: 13px; font-weight: 600; color: #333;">{{ $item->variant->product->product_name ?? 'N/A' }}</p>
                                    <p style="margin: 2px 0 0 0; font-size: 12px; color: #888;">{{ $item->variant->color ?? '—' }} / {{ $item->variant->size ?? '—' }} &times; {{ $item->quantity }}</p>
                                </div>
                                <span style="font-size: 13px; font-weight: 600; color: #333; white-space: nowrap;">&#8369;{{ number_format($item->subtotal(), 2) }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 15px; font-weight: 700; color: #333;">Total</span>
                    <span id="selectedTotal" style="font-size: 1.3rem; font-weight: 700; color: #2e7d32;">&#8369;{{ number_format($selectedTotal, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

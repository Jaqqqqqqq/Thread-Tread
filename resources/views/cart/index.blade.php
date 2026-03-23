@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px; max-width: 1000px; margin: 0 auto;">

    @if (session('success'))
        <div style="background: #c8e6c9; padding: 12px 16px; border-radius: 4px; color: #2e7d32; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div style="background: #ffcdd2; padding: 12px 16px; border-radius: 4px; color: #c62828; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="margin: 0; color: #333; font-size: 1.5rem;">🛒 Shopping Cart</h2>
        <a href="{{ route('home') }}" style="color: #1976d2; text-decoration: none; font-size: 14px;">&larr; Continue Shopping</a>
    </div>

    @if($cart && $cart->items->count() > 0)
        <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f5f5f5; border-bottom: 2px solid #ddd;">
                        <th style="padding: 14px 16px; text-align: center; font-weight: 600; color: #333; font-size: 13px; width: 40px;">
                            <input type="checkbox" id="selectAll" style="cursor: pointer;">
                        </th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 600; color: #333; font-size: 13px;">Product</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 600; color: #333; font-size: 13px;">Variant</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 600; color: #333; font-size: 13px;">Price</th>
                        <th style="padding: 14px 16px; text-align: center; font-weight: 600; color: #333; font-size: 13px;">Quantity</th>
                        <th style="padding: 14px 16px; text-align: right; font-weight: 600; color: #333; font-size: 13px;">Subtotal</th>
                        <th style="padding: 14px 16px; text-align: center; font-weight: 600; color: #333; font-size: 13px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart->items as $item)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 14px 16px; text-align: center;">
                                <input type="checkbox" class="item-checkbox" value="{{ $item->cart_item_id }}" data-price="{{ $item->subtotal() }}" style="cursor: pointer;" checked>
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    @if($item->variant && $item->variant->product)
                                        <img src="{{ $item->variant->product->prod_image ? asset($item->variant->product->prod_image) : 'https://via.placeholder.com/50x50?text=N/A' }}" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #eee;">
                                        <div>
                                            <a href="{{ route('products.show', $item->variant->product->product_id) }}" style="color: #333; text-decoration: none; font-weight: 600; font-size: 14px;">{{ $item->variant->product->product_name }}</a>
                                        </div>
                                    @else
                                        <span style="color: #999;">Product unavailable</span>
                                    @endif
                                </div>
                            </td>
                            <td style="padding: 14px 16px; font-size: 13px; color: #555;">
                                {{ $item->variant->color ?? '—' }} / {{ $item->variant->size ?? '—' }}
                            </td>
                            <td style="padding: 14px 16px; font-size: 14px; color: #333;">
                                &#8369;{{ number_format($item->variant->product->price ?? 0, 2) }}
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <form method="POST" action="{{ route('cart.update', $item->cart_item_id) }}" style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->variant->stock }}" style="width: 60px; padding: 6px 8px; border: 1px solid #ddd; border-radius: 4px; text-align: center; font-size: 13px;">
                                    <button type="submit" style="padding: 6px 10px; background: #1976d2; color: #fff; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">Update</button>
                                </form>
                            </td>
                            <td style="padding: 14px 16px; text-align: right; font-weight: 600; font-size: 14px; color: #2e7d32;">
                                &#8369;{{ number_format($item->subtotal(), 2) }}
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <form method="POST" action="{{ route('cart.remove', $item->cart_item_id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="removeBtn" style="padding: 6px 12px; background: #d32f2f; color: #fff; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">Remove</button>
                                        <!-- Custom Remove Confirmation Modal -->
                                        <div id="removeConfirmModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:1000; align-items:center; justify-content:center;">
                                            <div style="background:#fff; border-radius:8px; max-width:350px; margin:auto; padding:32px 24px; box-shadow:0 4px 24px rgba(0,0,0,0.12); text-align:center;">
                                                <h4 style="margin:0 0 16px 0; color:#d32f2f; font-size:1.1rem;">Remove Item</h4>
                                                <p style="color:#333; font-size:15px; margin-bottom:24px;">Are you sure you want to remove this item from your cart?</p>
                                                <div style="display:flex; gap:12px; justify-content:center;">
                                                    <button id="confirmRemoveYes" style="padding:8px 20px; background:#d32f2f; color:#fff; border:none; border-radius:4px; font-weight:600; cursor:pointer;">Yes, Remove</button>
                                                    <button id="confirmRemoveNo" style="padding:8px 20px; background:#eee; color:#333; border:none; border-radius:4px; font-weight:600; cursor:pointer;">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                @push('scripts')
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        let formToSubmit = null;
                                        const removeBtns = document.querySelectorAll('.removeBtn');
                                        const modal = document.getElementById('removeConfirmModal');
                                        const yesBtn = document.getElementById('confirmRemoveYes');
                                        const noBtn = document.getElementById('confirmRemoveNo');

                                        removeBtns.forEach(function(btn) {
                                            btn.addEventListener('click', function(e) {
                                                formToSubmit = btn.closest('form');
                                                modal.style.display = 'flex';
                                            });
                                        });
                                        yesBtn.addEventListener('click', function() {
                                            modal.style.display = 'none';
                                            if (formToSubmit) formToSubmit.submit();
                                        });
                                        noBtn.addEventListener('click', function() {
                                            modal.style.display = 'none';
                                            formToSubmit = null;
                                        });
                                        // Optional: close modal on outside click
                                        modal.addEventListener('click', function(e) {
                                            if (e.target === modal) {
                                                modal.style.display = 'none';
                                                formToSubmit = null;
                                            }
                                        });
                                    });
                                </script>
                                @endpush
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Cart Total & Checkout -->
            <form id="checkoutForm" method="POST" action="{{ route('checkout.view') }}" style="padding: 20px 16px; background: #f9f9f9; border-top: 2px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
                @csrf
                <div>
                    <span id="selectedCount" style="font-size: 14px; color: #666;">{{ $cart->itemCount() }} {{ Str::plural('item', $cart->itemCount()) }} in cart</span>
                </div>
                <div style="display: flex; align-items: center; gap: 24px;">
                    <div style="text-align: right;">
                        <span style="font-size: 13px; color: #666;">Selected Total:</span>
                        <span id="selectedTotal" style="font-size: 1.4rem; font-weight: 700; color: #2e7d32; margin-left: 8px;">&#8369;{{ number_format($cart->calculateTotal(), 2) }}</span>
                    </div>
                    <button type="submit" id="checkoutBtn" style="padding: 12px 32px; background: #2e7d32; color: #fff; border: none; border-radius: 4px; font-size: 15px; cursor: pointer; font-weight: 600;">Proceed to Checkout</button>
                </div>
            </form>
        </div>
    @else
        <div style="background: #fff; border-radius: 8px; padding: 48px 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-align: center;">
            <p style="font-size: 48px; margin: 0 0 16px 0;">🛒</p>
            <h3 style="color: #333; margin: 0 0 8px 0;">Your cart is empty</h3>
            <p style="color: #666; margin: 0 0 24px 0; font-size: 14px;">Browse our products and add items to your cart.</p>
            <a href="{{ route('home') }}" style="padding: 12px 32px; background: #1976d2; color: #fff; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 14px;">Browse Products</a>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const checkoutForm = document.getElementById('checkoutForm');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const selectedTotal = document.getElementById('selectedTotal');
        const selectedCount = document.getElementById('selectedCount');
        
        // Function to update total and count
        function updateTotal() {
            let total = 0;
            let count = 0;
            itemCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    total += parseFloat(checkbox.getAttribute('data-price'));
                    count += 1;
                }
            });
            selectedTotal.textContent = '₱' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            selectedCount.textContent = count + ' ' + (count === 1 ? 'item' : 'items') + ' selected';
            
            // Disable checkout button if no items selected
            checkoutBtn.disabled = count === 0;
            checkoutBtn.style.opacity = count === 0 ? '0.5' : '1';
            checkoutBtn.style.cursor = count === 0 ? 'not-allowed' : 'pointer';
        }
        
        // Select All functionality
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateTotal();
        });
        
        // Individual checkbox listeners
        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Update select all checkbox state
                const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(itemCheckboxes).some(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
                
                updateTotal();
            });
        });
        
        // Form submission - add selected items as hidden inputs
        checkoutForm.addEventListener('submit', function(e) {
            const selectedItems = Array.from(itemCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            if (selectedItems.length === 0) {
                e.preventDefault();
                alert('Please select at least one item to checkout.');
                return;
            }
            
            // Add hidden inputs for selected items
            selectedItems.forEach(itemId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_items[]';
                input.value = itemId;
                checkoutForm.appendChild(input);
            });
        });
        
        // Initialize
        updateTotal();
    });
</script>
@endpush


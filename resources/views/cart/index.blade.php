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
                                    <button type="submit" style="padding: 6px 12px; background: #d32f2f; color: #fff; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;" onclick="return confirm('Remove this item?')">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Cart Total & Checkout -->
            <div style="padding: 20px 16px; background: #f9f9f9; border-top: 2px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-size: 14px; color: #666;">{{ $cart->itemCount() }} {{ Str::plural('item', $cart->itemCount()) }} in cart</span>
                </div>
                <div style="display: flex; align-items: center; gap: 24px;">
                    <div style="text-align: right;">
                        <span style="font-size: 13px; color: #666;">Total:</span>
                        <span style="font-size: 1.4rem; font-weight: 700; color: #2e7d32; margin-left: 8px;">&#8369;{{ number_format($cart->calculateTotal(), 2) }}</span>
                    </div>
                    <a href="{{ route('checkout.view') }}" style="padding: 12px 32px; background: #2e7d32; color: #fff; border: none; border-radius: 4px; font-size: 15px; cursor: pointer; font-weight: 600; text-decoration: none;">Proceed to Checkout</a>
                </div>
            </div>
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
@endsection

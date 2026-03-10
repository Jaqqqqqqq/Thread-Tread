@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px;">
    <h1 style="margin: 0 0 8px 0; color: #333;">Welcome to Thread & Trend!</h1>
    <p style="color: #666; margin: 0 0 32px 0;">Your one-stop shop for trendy clothing.</p>

    <h2 id="products" style="margin-top: 0; font-size: 1.3rem; font-weight: 500; color: #333;">Products</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 32px; margin-top: 24px;">
        @foreach($products ?? [] as $product)
        <a href="{{ route('products.show', $product->product_id) }}" style="text-decoration: none; color: inherit;">
        <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 18px; text-align: center; transition: box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.12)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)'">
            <img src="{{ $product->prod_image ? asset($product->prod_image) : 'https://via.placeholder.com/180x180?text=No+Image' }}" alt="{{ $product->product_name }}" style="width: 180px; height: 180px; object-fit: cover; border-radius: 6px; margin-bottom: 12px;">
            <div style="font-size: 1.1rem; font-weight: 500; margin-bottom: 6px;">{{ $product->product_name }}</div>
            <div style="color: #444; font-size: 1rem; margin-bottom: 10px;">&#8369;{{ number_format($product->price, 2) }}</div>
        </div>
        </a>
        @endforeach
    </div>
</div>
@endsection

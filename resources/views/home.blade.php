@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px;">
    <h1 style="margin: 0 0 8px 0; color: #333;">Welcome to Thread & Trend!</h1>
    <p style="color: #666; margin: 0 0 32px 0;">Your one-stop shop for trendy clothing.</p>

    <!-- Search Bar -->
    <div style="margin-bottom: 32px; background: #f9f9f9; padding: 20px; border-radius: 8px;">
        <h3 style="margin: 0 0 16px 0; font-size: 1rem; color: #333;">Search Products</h3>
        <form method="GET" action="{{ route('home') }}" style="display: flex; gap: 12px; align-items: flex-end;">
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 6px; font-size: 0.9rem; color: #666;">Product Name</label>
                <input type="text" name="q" placeholder="Search by product name..." value="{{ request('q') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.95rem;">
            </div>
            <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 500;">Search</button>
            <a href="{{ route('home') }}" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-weight: 500;">Clear</a>
        </form>
        @if(request('q'))
            <p style="margin: 12px 0 0 0; font-size: 0.9rem; color: #666;">Results for: <strong>{{ request('q') }}</strong> ({{ method_exists($products, 'total') ? $products->total() : 0 }} found)</p>
        @endif
    </div>

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

    <!-- Pagination -->
    <div style="margin-top: 40px; display: flex; justify-content: center;">
        @if(method_exists($products, 'links'))
            {{ $products->appends(['q' => request('q')])->links('pagination::tailwind') }}
        @endif
    </div>
</div>
@endsection

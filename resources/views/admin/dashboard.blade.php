@extends('layouts.minimal')

@section('content')
<header style="display: flex; justify-content: space-between; align-items: center; padding: 18px 32px; background: #222; color: #fff;">
    <div style="display: flex; align-items: center; gap: 32px;">
        <h2 style="font-size: 1.7rem; font-weight: 600; letter-spacing: 1px; margin: 0;">Admin Dashboard</h2>
        <nav>
            <a href="{{ route('admin.dashboard') }}" style="color: #fff; text-decoration: none; font-size: 1.1rem; font-weight: 500; margin-left: 12px;">Products</a>
        </nav>
    </div>
    <div style="position: relative;">
        <button id="profileBtn" style="background: none; border: none; color: #fff; display: flex; align-items: center; cursor: pointer;">
            <span style="margin-right: 8px;">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 8-4 8-4s8 0 8 4"/></svg>
            </span>
            Admin
            <span style="margin-left: 8px;">▼</span>
        </button>
        <div id="profileDropdown" style="display: none; position: absolute; right: 0; top: 40px; background: #fff; color: #222; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); min-width: 120px;">
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" style="width: 100%; background: none; border: none; padding: 10px 18px; text-align: left; cursor: pointer; color: #222; font-size: 16px;">Log out</button>
            </form>
        </div>
    </div>
</header>
<div style="padding: 48px 32px;">
    <h2 id="products">Products</h2>
    <a href="{{ route('admin.products.create') }}" style="display: inline-block; margin-bottom: 18px; background: #222; color: #fff; border-radius: 4px; padding: 8px 16px; text-decoration: none;">Insert Product</a>
    @if(empty($products) || count($products) === 0)
        <div style="margin-bottom: 32px; color: #888; font-size: 1.1rem;">No products found.</div>
    @else
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 32px;">
        <thead>
            <tr style="background: #f5f5f5;">
                <th>Product ID</th>
                <th>Category ID</th>
                <th>Brand ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Image</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->product_id }}</td>
                <td>{{ $product->category_id }}</td>
                <td>{{ $product->brand_id }}</td>
                <td>{{ $product->product_name }}</td>
                <td>{{ $product->prod_desc }}</td>
                <td>₱{{ number_format($product->price, 2) }}</td>
                <td><img src="{{ $product->prod_image ?? 'https://via.placeholder.com/60x60?text=No+Image' }}" alt="{{ $product->product_name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;"></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    <h3>Add Product</h3>
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" style="margin-bottom: 40px;">
        @csrf
        <input type="text" name="product_name" placeholder="Product Name" required style="margin-right: 8px;">
        <input type="text" name="prod_desc" placeholder="Description" required style="margin-right: 8px;">
        <input type="number" name="price" placeholder="Price" required style="margin-right: 8px;">
        <input type="number" name="category_id" placeholder="Category ID" required style="margin-right: 8px;">
        <input type="number" name="brand_id" placeholder="Brand ID" required style="margin-right: 8px;">
        <input type="file" name="prod_image" accept="image/*" style="margin-right: 8px;">
        <button type="submit" style="background: #222; color: #fff; border: none; border-radius: 4px; padding: 8px 16px; cursor: pointer;">Add Product</button>
    </form>
    <h2>Product Variants</h2>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 32px;">
        <thead>
            <tr style="background: #f5f5f5;">
                <th>Variant ID</th>
                <th>Product ID</th>
                <th>Size</th>
                <th>Color</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($variants ?? [] as $variant)
            <tr>
                <td>{{ $variant->variant_id }}</td>
                <td>{{ $variant->product_id }}</td>
                <td>{{ $variant->size }}</td>
                <td>{{ $variant->color }}</td>
                <td>{{ $variant->stock }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <h3>Add Variant</h3>
    <form method="POST" action="{{ route('admin.variants.store') }}" style="margin-bottom: 40px;">
        @csrf
        <input type="number" name="product_id" placeholder="Product ID" required style="margin-right: 8px;">
        <input type="text" name="size" placeholder="Size" required style="margin-right: 8px;">
        <input type="text" name="color" placeholder="Color" required style="margin-right: 8px;">
        <input type="number" name="stock" placeholder="Stock" required style="margin-right: 8px;">
        <button type="submit" style="background: #222; color: #fff; border: none; border-radius: 4px; padding: 8px 16px; cursor: pointer;">Add Variant</button>
    </form>
</div>
<script>
    const btn = document.getElementById('profileBtn');
    const dropdown = document.getElementById('profileDropdown');
    if(btn && dropdown) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });
        document.addEventListener('click', function() {
            dropdown.style.display = 'none';
        });
    }
</script>
@endsection

@extends('layouts.minimal')

@section('content')
<header style="display: flex; justify-content: space-between; align-items: center; padding: 18px 32px; background: #222; color: #fff;">
    <h2 style="font-size: 1.7rem; font-weight: 600; letter-spacing: 1px; margin: 0;">Insert Product</h2>
    <a href="{{ route('admin.dashboard') }}" style="color: #fff; text-decoration: none; font-size: 1.1rem; font-weight: 500;">Back to Products</a>
</header>
<div style="padding: 48px 32px; max-width: 600px; margin: 0 auto;">
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom: 18px;">
            <label>Product Name</label>
            <input type="text" name="product_name" required style="width: 100%; padding: 8px;">
        </div>
        <div style="margin-bottom: 18px;">
            <label>Description</label>
            <textarea name="prod_desc" required style="width: 100%; padding: 8px;"></textarea>
        </div>
        <div style="margin-bottom: 18px;">
            <label>Price</label>
            <input type="number" name="price" required style="width: 100%; padding: 8px;">
        </div>
        <div style="margin-bottom: 18px;">
            <label>Category ID</label>
            <input type="number" name="category_id" required style="width: 100%; padding: 8px;">
        </div>
        <div style="margin-bottom: 18px;">
            <label>Brand ID</label>
            <input type="number" name="brand_id" required style="width: 100%; padding: 8px;">
        </div>
        <div style="margin-bottom: 18px;">
            <label>Product Image</label>
            <input type="file" name="prod_image" accept="image/*" style="width: 100%;">
        </div>
        <button type="submit" style="background: #222; color: #fff; border: none; border-radius: 4px; padding: 10px 20px; cursor: pointer;">Add Product</button>
    </form>
</div>
@endsection

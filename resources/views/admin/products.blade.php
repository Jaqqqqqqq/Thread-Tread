@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3 style="margin: 0;">Product List</h3>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.products.import') }}" style="background: #1976d2; color: #fff; padding: 10px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;">📤 Import Excel</a>
            <a href="{{ route('admin.products.trashed') }}" style="background: #b71c1c; color: #fff; padding: 10px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;">🗑️ Trash</a>
            <a href="{{ route('admin.products.create') }}" style="background: #222; color: #fff; padding: 10px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;">+ Add Product</a>
        </div>
    </div>

    @if (session('success'))
        <div style="background: #c8e6c9; padding: 12px 16px; border-radius: 4px; color: #2e7d32; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if($products->isEmpty())
        <div style="background: #e3f2fd; padding: 12px 16px; border-radius: 4px; color: #1976d2;">No products found.</div>
    @else
        <div style="overflow-x: auto; background: #fff; border-radius: 4px; border: 1px solid #ddd;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f5f5f5; border-bottom: 1px solid #ddd;">
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600;">ID</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Image</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Name</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Brand</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Category</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Price</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px 16px;">{{ $product->product_id }}</td>
                            <td style="padding: 12px 16px;">
                                @if($product->prod_image)
                                    <img src="{{ asset($product->prod_image) }}" alt="{{ $product->product_name }}" style="height: 40px; width: 40px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <span style="color: #999; font-size: 12px;">No image</span>
                                @endif
                            </td>
                            <td style="padding: 12px 16px;">{{ $product->product_name }}</td>
                            <td style="padding: 12px 16px;">{{ $product->brand->name ?? '—' }}</td>
                            <td style="padding: 12px 16px;">{{ $product->category->name ?? '—' }}</td>
                            <td style="padding: 12px 16px;">₱{{ number_format($product->price, 2) }}</td>
                            <td style="padding: 12px 16px;">
                                <div style="display: flex; gap: 8px;">
                                    <a href="{{ route('admin.products.edit', $product->product_id) }}" style="background: #0052cc; color: #fff; padding: 6px 14px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: 500;">Edit</a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product->product_id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to move this product to trash?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #d32f2f; color: #fff; padding: 6px 14px; border-radius: 4px; border: none; font-size: 13px; font-weight: 500; cursor: pointer;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
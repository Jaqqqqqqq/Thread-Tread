@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3 style="margin: 0;">🗑️ Trashed Products</h3>
        <a href="{{ route('admin.products') }}" style="background: #222; color: #fff; padding: 10px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;">← Back to Products</a>
    </div>

    @if (session('success'))
        <div style="background: #c8e6c9; padding: 12px 16px; border-radius: 4px; color: #2e7d32; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if($products->isEmpty())
        <div style="background: #e3f2fd; padding: 12px 16px; border-radius: 4px; color: #1976d2;">
            Trash is empty. No deleted products found.
        </div>
    @else
        <div style="background: #fff3e0; padding: 12px 16px; border-radius: 4px; color: #e65100; margin-bottom: 20px;">
            ⚠️ These products have been soft-deleted. You can restore them to make them active again.
        </div>

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
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Deleted At</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr style="border-bottom: 1px solid #ddd; background: #fafafa;">
                            <td style="padding: 12px 16px; color: #999;">{{ $product->product_id }}</td>
                            <td style="padding: 12px 16px;">
                                @if($product->prod_image)
                                    <img src="{{ asset($product->prod_image) }}" alt="{{ $product->product_name }}" style="height: 40px; width: 40px; object-fit: cover; border-radius: 4px; opacity: 0.5;">
                                @else
                                    <span style="color: #999; font-size: 12px;">No image</span>
                                @endif
                            </td>
                            <td style="padding: 12px 16px; color: #999;">{{ $product->product_name }}</td>
                            <td style="padding: 12px 16px; color: #999;">{{ $product->brand->name ?? '—' }}</td>
                            <td style="padding: 12px 16px; color: #999;">{{ $product->category->name ?? '—' }}</td>
                            <td style="padding: 12px 16px; color: #999;">₱{{ number_format($product->price, 2) }}</td>
                            <td style="padding: 12px 16px; color: #999; font-size: 13px;">{{ $product->deleted_at->format('M d, Y H:i') }}</td>
                            <td style="padding: 12px 16px;">
                                <form method="POST" action="{{ route('admin.products.restore', $product->product_id) }}" style="display: inline;" onsubmit="return confirm('Restore this product?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" style="background: #2e7d32; color: #fff; padding: 6px 14px; border-radius: 4px; border: none; font-size: 13px; font-weight: 500; cursor: pointer;">♻️ Restore</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
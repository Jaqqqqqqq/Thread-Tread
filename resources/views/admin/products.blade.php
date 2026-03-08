@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3 style="margin: 0;">Product List</h3>
        <a href="{{ route('admin.products.create') }}" style="background: #222; color: #fff; padding: 10px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;">Add Product</a>
    </div>
	@if($products->isEmpty())
		<div style="background: #e3f2fd; padding: 12px 16px; border-radius: 4px; color: #1976d2;">No products found.</div>
	@else
		<div style="overflow-x: auto; background: #fff; border-radius: 4px; border: 1px solid #ddd;">
			<table style="width: 100%; border-collapse: collapse;">
				<thead>
					<tr style="background: #f5f5f5; border-bottom: 1px solid #ddd;">
						<th style="padding: 12px 16px; text-align: left; font-weight: 600;">ID</th>
						<th style="padding: 12px 16px; text-align: left; font-weight: 600;">Name</th>
						<th style="padding: 12px 16px; text-align: left; font-weight: 600;">Brand</th>
						<th style="padding: 12px 16px; text-align: left; font-weight: 600;">Category</th>
						<th style="padding: 12px 16px; text-align: left; font-weight: 600;">Price</th>
					</tr>
				</thead>
				<tbody>
					@foreach($products as $product)
						<tr style="border-bottom: 1px solid #ddd;">
							<td style="padding: 12px 16px;">{{ $product->product_id }}</td>
							<td style="padding: 12px 16px;">{{ $product->product_name }}</td>
							<td style="padding: 12px 16px;">{{ $product->brand->name ?? '-' }}</td>
							<td style="padding: 12px 16px;">{{ $product->category->name ?? '-' }}</td>
							<td style="padding: 12px 16px;">${{ $product->price }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	@endif
</div>
@endsection

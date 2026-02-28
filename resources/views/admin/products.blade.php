@extends('layouts.app')

@section('content')
<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h2>Products</h2>
		<a href="{{ route('admin.products.create') }}" class="btn btn-primary">Insert Product</a>
	</div>
	@if($products->isEmpty())
		<div class="alert alert-info">No products found.</div>
	@else
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Brand</th>
					<th>Category</th>
					<th>Price</th>
				</tr>
			</thead>
			<tbody>
				@foreach($products as $product)
					<tr>
						<td>{{ $product->id }}</td>
						<td>{{ $product->name }}</td>
						<td>{{ $product->brand->name ?? '-' }}</td>
						<td>{{ $product->category->name ?? '-' }}</td>
						<td>{{ $product->price }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	@endif
</div>
@endsection

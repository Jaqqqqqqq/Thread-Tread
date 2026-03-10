@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Browse Products</h2>
    <form class="row" method="GET">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="col">
        <select name="category" class="col">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->category_id }}"{{ request('category')==$cat->category_id ? ' selected' : '' }}>{{ $cat->category_name }}</option>
            @endforeach
        </select>
        <select name="brand" class="col">
            <option value="">All Brands</option>
            @foreach($brands as $b)
                <option value="{{ $b->brand_id }}"{{ request('brand')==$b->brand_id ? ' selected' : '' }}>{{ $b->brand_name }}</option>
            @endforeach
        </select>
        <select name="sort" class="col">
            <option value="">Sort</option>
            <option value="price_asc"{{ request('sort')=='price_asc'?' selected':'' }}>Price Low-High</option>
            <option value="price_desc"{{ request('sort')=='price_desc'?' selected':'' }}>Price High-Low</option>
        </select>
        <button type="submit" class="btn btn-primary col">Go</button>
    </form>
    <hr>
    <div class="row">
    @foreach ($results as $product)
        <div class="col-md-3 mb-4">
            <div class="card">
                <img src="{{ asset('storage/'.$product->prod_image) }}" style="height:200px;object-fit:cover;" class="card-img-top">
                <div class="card-body">
                    <h5 class="card-title">{{ $product->product_name }}</h5>
                    <p>{{ $product->category->category_name??'' }} | {{ $product->brand->brand_name??'' }}</p>
                    <p>Price: ₱{{ number_format($product->price,2) }}</p>
                    <p>Stock: {{ $product->totalStock() }}</p>
                    <a href="{{ route('product.show', $product->product_id) }}" class="btn btn-outline-primary btn-sm">Details</a>
                </div>
            </div>
        </div>
    @endforeach
    </div>
    {{ $results->links() }}
</div>
@endsection
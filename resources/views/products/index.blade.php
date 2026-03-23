@extends('layouts.app')

@section('content')
<style>
    .products-container {
        padding: 40px 0;
    }
    
    .page-header {
        margin-bottom: 40px;
    }
    
    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 10px;
    }
    
    .page-header p {
        color: #666;
        font-size: 1.05rem;
    }
    
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 28px;
        margin-bottom: 40px;
    }
    
    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    }
    
    .product-image {
        width: 100%;
        height: 240px;
        object-fit: cover;
        background: #f5f5f5;
        display: block;
    }
    
    .product-info {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    
    .product-category {
        font-size: 0.8rem;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    
    .product-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 8px;
        line-height: 1.4;
    }
    
    .product-brand {
        font-size: 0.9rem;
        color: #667eea;
        font-weight: 500;
        margin-bottom: 12px;
    }
    
    .product-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 8px;
    }
    
    .product-stock {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 16px;
        flex-grow: 1;
    }
    
    .stock-badge {
        display: inline-block;
        padding: 4px 12px;
        background-color: #e8f5e9;
        color: #2e7d32;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .stock-badge.low {
        background-color: #fff3e0;
        color: #e65100;
    }
    
    .stock-badge.out {
        background-color: #ffebee;
        color: #c62828;
    }
    
    .product-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn-details {
        flex: 1;
        padding: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    
    .btn-details:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        text-decoration: none;
        color: white;
    }
    
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 40px;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    
    .empty-state h3 {
        font-size: 1.5rem;
        color: #666;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #999;
        margin-bottom: 20px;
    }
</style>

<div class="container products-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>🛍️ All Products</h1>
        <p>Browse our complete collection of premium clothing items</p>
    </div>

    <!-- Products Grid -->
    @if($results->count() > 0)
        <div class="products-grid">
            @foreach ($results as $product)
                <div class="product-card">
                    <img 
                        src="{{ asset($product->prod_image) }}" 
                        alt="{{ $product->product_name }}"
                        class="product-image"
                        onerror="this.src='https://via.placeholder.com/250x240?text=No+Image'"
                    >
                    
                    <div class="product-info">
                        <div class="product-category">
                            {{ $product->category->category_name ?? 'Uncategorized' }}
                        </div>
                        
                        <h3 class="product-name">{{ $product->product_name }}</h3>
                        
                        @if($product->brand)
                            <div class="product-brand">{{ $product->brand->brand_name }}</div>
                        @endif
                        
                        <div class="product-price">₱{{ number_format($product->price, 2) }}</div>
                        
                        <div class="product-stock">
                            @php
                                $stock = $product->totalStock();
                                if ($stock > 20) {
                                    $badgeClass = '';
                                    $label = 'In Stock';
                                } elseif ($stock > 0) {
                                    $badgeClass = 'low';
                                    $label = 'Limited Stock';
                                } else {
                                    $badgeClass = 'out';
                                    $label = 'Out of Stock';
                                }
                            @endphp
                            <span class="stock-badge {{ $badgeClass }}">{{ $label }} ({{ $stock }})</span>
                        </div>
                        
                        <div class="product-actions">
                            <a href="{{ route('products.show', $product->product_id) }}" class="btn-details">
                                View Details →
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $results->links() }}
        </div>
    @else
        <div class="empty-state">
            <h3>😕 No Products Available</h3>
            <p>Check back soon for new items</p>
        </div>
    @endif
</div>
@endsection
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'brand', 'variants'])
            ->withStockInfo()
            ->withRatingInfo();

        if ($request->filled('category')) {
            $products->inCategory($request->category);
        }

        if ($request->filled('brand')) {
            $products->byBrand($request->brand);
        }

        if ($request->filled('min_price')) {
            $products->where('price', '>=', (float)$request->min_price);
        }
        if ($request->filled('max_price')) {
            $products->where('price', '<=', (float)$request->max_price);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $products->where('product_name', 'like', "%$search%");
        }

        if ($request->sort == 'price_asc') {
            $products->orderBy('price', 'asc');
        } elseif ($request->sort == 'price_desc') {
            $products->orderBy('price', 'desc');
        } else {
            $products->orderBy('product_name');
        }

        $categories = Category::all();
        $brands = Brand::active()->get();
        $results = $products->paginate(12);

        return view('products.index', compact('results', 'categories', 'brands'));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'brand', 'variants', 'reviews.user', 'images'])
            ->withStockInfo()
            ->withRatingInfo()
            ->findOrFail($id);

        return view('products.show', compact('product'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Get latest (or featured) products.
        $products = Product::with(['category', 'brand', 'variants'])->withStockInfo()->paginate(8);
        return view('home', compact('products'));
    }
}   

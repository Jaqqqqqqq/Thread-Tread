<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->input('q');

        if ($searchQuery) {
            // Laravel Scout full-text search with pagination
            try {
                // Get search results from Scout
                $scoutResults = Product::search($searchQuery)->get();
                
                if ($scoutResults->count() > 0) {
                    // Extract product IDs from Scout results
                    $ids = $scoutResults->pluck('product_id')->toArray();
                    
                    // Fetch full products with relationships in same order as Scout results
                    $products = Product::whereIn('product_id', $ids)
                        ->with(['category', 'brand', 'variants'])
                        ->withStockInfo()
                        ->orderByRaw('FIELD(product_id, ' . implode(',', $ids) . ')')
                        ->paginate(8);
                } else {
                    // No results from Scout - show empty search
                    $products = Product::with(['category', 'brand', 'variants'])
                        ->withStockInfo()
                        ->where('product_id', '=', null)  // Force empty result
                        ->paginate(8);
                }
            } catch (\Exception $e) {
                // If Scout fails, fallback to LIKE search
                $products = Product::with(['category', 'brand', 'variants'])
                    ->withStockInfo()
                    ->where('product_name', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('prod_desc', 'LIKE', "%{$searchQuery}%")
                    ->paginate(8);
            }
        } else {
            // No search query - show all products
            $products = Product::with(['category', 'brand', 'variants'])
                ->withStockInfo()
                ->paginate(8);
        }

        return view('home', compact('products'));
    }
}   

<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
        public function dashboard()
        {
            $products = Product::all();
            $variants = ProductVariant::all();
            return view('admin.dashboard', compact('products', 'variants'));
        }

        public function products()
        {
            $products = Product::all();
            return view('admin.products', compact('products'));
        }

        public function storeVariant(Request $request)
        {
            $request->validate([
                'product_id' => 'required|integer',
                'size' => 'required',
                'color' => 'required',
                'stock' => 'required|integer',
            ]);
            ProductVariant::create([
                'product_id' => $request->product_id,
                'size' => $request->size,
                'color' => $request->color,
                'stock' => $request->stock,
            ]);
            return redirect()->route('admin.dashboard');
        }
    }

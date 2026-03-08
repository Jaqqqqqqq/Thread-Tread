<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Brand;
use App\Models\Category;
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

        public function create()
        {
            $brands = Brand::all();
            $categories = Category::all();
            return view('admin.create-product', compact('brands', 'categories'));
        }

        public function store(Request $request)
        {
            $validated = $request->validate([
                'product_name' => 'required|string|max:255',
                'prod_desc' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'brand_id' => 'required|integer|exists:brands,brand_id',
                'category_id' => 'required|integer|exists:categories,category_id',
                'prod_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'variants' => 'nullable|array',
                'variants.*.color' => 'nullable|string|max:255',
                'variants.*.size' => 'nullable|string|max:255',
                'variants.*.stock' => 'nullable|integer|min:0',
                'variants.*.color_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Handle image upload
            if ($request->hasFile('prod_image')) {
                $image = $request->file('prod_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/products'), $imageName);
                $validated['prod_image'] = 'images/products/' . $imageName;
            }

            // Create the product (exclude variants from mass assign)
            $productData = collect($validated)->except('variants')->filter()->all();
            $product = Product::create($productData);

            // Handle product variants
            if ($request->has('variants') && is_array($request->variants)) {
                foreach ($request->variants as $index => $variant) {
                    // Skip if no color or size provided
                    if (empty($variant['color']) && empty($variant['size'])) {
                        continue;
                    }

                    $variantData = [
                        'product_id' => $product->product_id,
                        'color' => $variant['color'] ?? null,
                        'size' => $variant['size'] ?? null,
                        'stock' => $variant['stock'] ?? 0,
                    ];

                    // Handle variant color image
                    if ($request->hasFile("variants.$index.color_image")) {
                        $colorImage = $request->file("variants.$index.color_image");
                        $colorImageName = time() . '_' . $index . '_' . $colorImage->getClientOriginalExtension();
                        $colorImage->move(public_path('images/products/variants'), $colorImageName);
                        $variantData['color_image'] = 'images/products/variants/' . $colorImageName;
                    }

                    ProductVariant::create($variantData);
                }
            }

            return redirect()->route('admin.products')->with('success', 'Product created successfully!');
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

        // Brands CRUD
        public function brands()
        {
            $brands = Brand::all();
            return view('admin.brands', compact('brands'));
        }

        public function brandCreate()
        {
            return view('admin.create-brand');
        }

        public function brandStore(Request $request)
        {
            $validated = $request->validate([
                'brand_name' => 'required|string|max:255|unique:brands,brand_name',
                'description' => 'nullable|string',
                'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'is_active' => 'nullable|boolean',
            ]);

            // Handle logo upload
            if ($request->hasFile('brand_logo')) {
                $logo = $request->file('brand_logo');
                $logoName = time() . '.' . $logo->getClientOriginalExtension();
                $logo->move(public_path('images/brands'), $logoName);
                $validated['brand_logo'] = 'images/brands/' . $logoName;
            }

            $validated['is_active'] = $request->has('is_active') ? 1 : 0;

            Brand::create($validated);
            return redirect()->route('admin.brands')->with('success', 'Brand created successfully!');
        }

        public function brandEdit($brand_id)
        {
            $brand = Brand::findOrFail($brand_id);
            return view('admin.edit-brand', compact('brand'));
        }

        public function brandUpdate(Request $request, $brand_id)
        {
            $brand = Brand::findOrFail($brand_id);
            $validated = $request->validate([
                'brand_name' => 'required|string|max:255|unique:brands,brand_name,' . $brand_id . ',brand_id',
                'description' => 'nullable|string',
                'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'is_active' => 'nullable|boolean',
            ]);

            // Handle logo upload
            if ($request->hasFile('brand_logo')) {
                // Delete old logo if exists
                if ($brand->brand_logo && file_exists(public_path($brand->brand_logo))) {
                    unlink(public_path($brand->brand_logo));
                }
                
                $logo = $request->file('brand_logo');
                $logoName = time() . '.' . $logo->getClientOriginalExtension();
                $logo->move(public_path('images/brands'), $logoName);
                $validated['brand_logo'] = 'images/brands/' . $logoName;
            }

            $validated['is_active'] = $request->has('is_active') ? 1 : 0;

            $brand->update($validated);
            return redirect()->route('admin.brands')->with('success', 'Brand updated successfully!');
        }

        public function brandDestroy($brand_id)
        {
            Brand::findOrFail($brand_id)->delete();
            return redirect()->route('admin.brands')->with('success', 'Brand deleted successfully!');
        }

        // Categories CRUD
        public function categories()
        {
            $categories = Category::all();
            return view('admin.categories', compact('categories'));
        }

        public function categoryCreate()
        {
            return view('admin.create-category');
        }

        public function categoryStore(Request $request)
        {
            $validated = $request->validate([
                'category_name' => 'required|string|max:255|unique:categories,category_name',
                'description' => 'nullable|string',
            ]);

            Category::create($validated);
            return redirect()->route('admin.categories')->with('success', 'Category created successfully!');
        }

        public function categoryEdit($category_id)
        {
            $category = Category::findOrFail($category_id);
            return view('admin.edit-category', compact('category'));
        }

        public function categoryUpdate(Request $request, $category_id)
        {
            $category = Category::findOrFail($category_id);
            $validated = $request->validate([
                'category_name' => 'required|string|max:255|unique:categories,category_name,' . $category_id . ',category_id',
                'description' => 'nullable|string',
            ]);

            $category->update($validated);
            return redirect()->route('admin.categories')->with('success', 'Category updated successfully!');
        }

        public function categoryDestroy($category_id)
        {
            Category::findOrFail($category_id)->delete();
            return redirect()->route('admin.categories')->with('success', 'Category deleted successfully!');
        }

        // Users: list, update role, update status
        public function users()
        {
            $users = \App\Models\User::orderBy('created_at', 'desc')->get();
            return view('admin.users', compact('users'));
        }

        public function userUpdate(Request $request, $user_id)
        {
            $user = \App\Models\User::findOrFail($user_id);
            $validated = $request->validate([
                'role' => 'required|in:admin,customer',
                'is_active' => 'required|in:0,1',
            ]);
            $user->role = $validated['role'];
            $user->is_active = (int) $validated['is_active'] === 1;
            $user->save();
            return redirect()->route('admin.users')->with('success', 'User updated successfully.');
        }
    }

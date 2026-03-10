<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
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
                'prod_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'product_images' => 'nullable|array',
                'product_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'variants' => 'nullable|array',
                'variants.*.color' => 'nullable|string|max:255',
                'variants.*.size' => 'nullable|string|max:255',
                'variants.*.stock' => 'nullable|integer|min:0',
                'variants.*.color_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            // Handle main product image upload
            if ($request->hasFile('prod_image')) {
                $image = $request->file('prod_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/products'), $imageName);
                $validated['prod_image'] = 'images/products/' . $imageName;
            }

            // Create the product (exclude variants and product_images from mass assign)
            $productData = collect($validated)->except(['variants', 'product_images'])->filter()->all();
            $product = Product::create($productData);

            // Handle multiple product images
            if ($request->hasFile('product_images')) {
                $sortOrder = 0;
                foreach ($request->file('product_images') as $image) {
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images/products/gallery'), $imageName);
                    
                    ProductImage::create([
                        'product_id' => $product->product_id,
                        'image_path' => 'images/products/gallery/' . $imageName,
                        'sort_order' => $sortOrder,
                    ]);
                    
                    $sortOrder++;
                    
                    // If no main image was provided, use the first gallery image
                    if (!$request->hasFile('prod_image') && $sortOrder === 1) {
                        $product->update([
                            'prod_image' => 'images/products/gallery/' . $imageName
                        ]);
                    }
                }
            }

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
                'product_id' => 'required|integer|exists:products,product_id',
                'size' => 'required|string|max:255',
                'color' => 'required|string|max:255',
                'stock' => 'required|integer|min:0',
            ]);

            ProductVariant::create([
                'product_id' => $request->product_id,
                'size' => $request->size,
                'color' => $request->color,
                'stock' => $request->stock,
            ]);

            return redirect()->route('admin.products.edit', $request->product_id)->with('success', 'Variant added successfully!');
        }

                // ===== Product Edit & Update =====

        public function productEdit($product_id)
        {
            // Find the product or show 404 if not found
            $product = Product::findOrFail($product_id);

            // Get all brands and categories for the dropdown selects
            $brands = Brand::all();
            $categories = Category::all();

            // Load the product's existing variants so we can display them
            $variants = $product->variants;
            
            // Load the product's existing images
            $images = $product->images;

            return view('admin.edit-product', compact('product', 'brands', 'categories', 'variants', 'images'));
        }

        public function productUpdate(Request $request, $product_id)
        {
            // Find the product or show 404 if not found
            $product = Product::findOrFail($product_id);

            // Validate the incoming data
            $validated = $request->validate([
                'product_name' => 'required|string|max:255',
                'prod_desc'    => 'nullable|string',
                'price'        => 'required|numeric|min:0',
                'brand_id'     => 'required|integer|exists:brands,brand_id',
                'category_id'  => 'required|integer|exists:categories,category_id',
                'prod_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'product_images' => 'nullable|array',
                'product_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'delete_images' => 'nullable|array',
                'delete_images.*' => 'integer',
            ]);

            // Handle main image upload (only if a new image was selected)
            if ($request->hasFile('prod_image')) {
                // Delete the old image file if it exists
                if ($product->prod_image && file_exists(public_path($product->prod_image))) {
                    unlink(public_path($product->prod_image));
                }

                $image = $request->file('prod_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/products'), $imageName);
                $validated['prod_image'] = 'images/products/' . $imageName;
            }

            // Handle deleting existing product images
            if ($request->has('delete_images') && is_array($request->delete_images)) {
                foreach ($request->delete_images as $imageId) {
                    $productImage = ProductImage::find($imageId);
                    if ($productImage && $productImage->product_id == $product_id) {
                        // Delete the file if it exists
                        if (file_exists(public_path($productImage->image_path))) {
                            unlink(public_path($productImage->image_path));
                        }
                        $productImage->delete();
                    }
                }
            }

            // Handle adding new product images
            if ($request->hasFile('product_images')) {
                $maxSortOrder = $product->images->max('sort_order') ?? -1;
                $sortOrder = $maxSortOrder + 1;
                
                foreach ($request->file('product_images') as $image) {
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images/products/gallery'), $imageName);
                    
                    ProductImage::create([
                        'product_id' => $product->product_id,
                        'image_path' => 'images/products/gallery/' . $imageName,
                        'sort_order' => $sortOrder,
                    ]);
                    
                    $sortOrder++;
                }
            }

            // Update the product with validated data
            $product->update($validated);

            return redirect()->route('admin.products')->with('success', 'Product updated successfully!');
        }

        // ===== Product Delete, Trash & Restore =====

        public function productDestroy($product_id)
        {
            // findOrFail only searches non-trashed products by default
            $product = Product::findOrFail($product_id);

            // This does a SOFT delete — sets 'deleted_at' timestamp
            // The product is NOT removed from the database
            $product->delete();

            return redirect()->route('admin.products')->with('success', 'Product moved to trash.');
        }

        public function productsTrashed()
        {
            // onlyTrashed() returns ONLY soft-deleted products
            // (products where deleted_at IS NOT NULL)
            $products = Product::onlyTrashed()->with('brand', 'category')->get();

            return view('admin.products-trashed', compact('products'));
        }

        public function productRestore($product_id)
        {
            // withTrashed() searches ALL products including soft-deleted ones
            // Without it, findOrFail would skip trashed products and return 404
            $product = Product::withTrashed()->findOrFail($product_id);

            // restore() sets 'deleted_at' back to NULL
            // The product becomes visible again in normal queries
            $product->restore();

            return redirect()->route('admin.products.trashed')->with('success', 'Product restored successfully!');
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
                'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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
                'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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

        // ===== Product Import =====

        public function productsImportForm()
        {
            return view('admin.import-products');
        }

        public function productsImportStore(Request $request)
        {
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv',
            ]);

            try {
                Excel::import(new ProductsImport(), $request->file('excel_file'));
                return redirect()->route('admin.products')->with('success', 'Products imported successfully!');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error importing products: ' . $e->getMessage());
            }
        }

        // ===== Product Variant Edit, Update, Delete =====

        public function variantEdit($variant_id)
        {
            $variant = ProductVariant::findOrFail($variant_id);
            $product = Product::findOrFail($variant->product_id);
            
            return view('admin.edit-variant', compact('variant', 'product'));
        }

        public function variantUpdate(Request $request, $variant_id)
        {
            $variant = ProductVariant::findOrFail($variant_id);

            $validated = $request->validate([
                'size' => 'required|string|max:255',
                'color' => 'required|string|max:255',
                'stock' => 'required|integer|min:0',
                'color_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            // Handle color image upload
            if ($request->hasFile('color_image')) {
                // Delete old color image if it exists
                if ($variant->color_image && file_exists(public_path($variant->color_image))) {
                    unlink(public_path($variant->color_image));
                }

                $image = $request->file('color_image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/products/variants'), $imageName);
                $validated['color_image'] = 'images/products/variants/' . $imageName;
            }

            $variant->update($validated);

            return redirect()->route('admin.products.edit', $variant->product_id)->with('success', 'Variant updated successfully!');
        }

        public function variantDestroy($variant_id)
        {
            $variant = ProductVariant::findOrFail($variant_id);
            $product_id = $variant->product_id;

            // Delete color image if it exists
            if ($variant->color_image && file_exists(public_path($variant->color_image))) {
                unlink(public_path($variant->color_image));
            }

            $variant->delete();

            return redirect()->route('admin.products.edit', $product_id)->with('success', 'Variant deleted successfully!');
        }

        // ===== Admin Order Management =====

        public function orders()
        {
            $orders = Order::with(['user', 'paymentMethod'])
                ->orderBy('order_id', 'desc')
                ->get();
            return view('admin.orders', compact('orders'));
        }

        public function orderShow($order_id)
        {
            $order = Order::with(['user', 'items.variant.product', 'paymentMethod', 'payment'])
                ->findOrFail($order_id);
            return view('admin.order-detail', compact('order'));
        }

        public function orderUpdateStatus(Request $request, $order_id)
        {
            $order = Order::findOrFail($order_id);

            $request->validate([
                'order_status' => 'required|in:pending,processing,shipped,completed,cancelled',
            ]);

            $oldStatus = $order->order_status;
            $newStatus = $request->order_status;

            // If cancelling, restore stock
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                $order->load('items.variant');
                foreach ($order->items as $item) {
                    if ($item->variant) {
                        $item->variant->increment('stock', $item->oi_quantity);
                    }
                }
            }

            $order->update(['order_status' => $newStatus]);

            return redirect()->back()->with('success', 'Order #' . $order->order_id . ' status updated to ' . ucfirst($newStatus) . '.');
        }
    }


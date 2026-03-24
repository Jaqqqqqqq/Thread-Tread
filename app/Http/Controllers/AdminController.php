<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Imports\ProductsImport;
use App\Charts\YearlySalesChart;
use App\Charts\SalesRangeChart;
use App\Charts\ProductPieChart;
use App\Mail\OrderStatusUpdated;
use App\Services\ReceiptGenerator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
                'prod_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,avif|max:2048',
                'product_images' => 'nullable|array',
                'product_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
                'variants' => 'nullable|array',
                'variants.*.color' => 'nullable|string|max:255',
                'variants.*.size' => 'nullable|string|max:255',
                'variants.*.stock' => 'nullable|integer|min:0',
                'variants.*.color_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            ]);

            if ($request->hasFile('prod_image')) {
                $image = $request->file('prod_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $directory = public_path('images/products');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                $image->move($directory, $imageName);
                $validated['prod_image'] = 'images/products/' . $imageName;
            }

            $productData = collect($validated)->except(['variants', 'product_images'])->filter()->all();
            $product = Product::create($productData);

            if ($request->hasFile('product_images')) {
                $sortOrder = 0;
                $galleryDir = public_path('images/products/gallery');
                if (!is_dir($galleryDir)) {
                    mkdir($galleryDir, 0755, true);
                }
                
                foreach ($request->file('product_images') as $image) {
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move($galleryDir, $imageName);
                    
                    ProductImage::create([
                        'product_id' => $product->product_id,
                        'image_path' => 'images/products/gallery/' . $imageName,
                        'sort_order' => $sortOrder,
                    ]);
                    
                    $sortOrder++;
                    
                    if (!$request->hasFile('prod_image') && $sortOrder === 1) {
                        $product->update([
                            'prod_image' => 'images/products/gallery/' . $imageName
                        ]);
                    }
                }
            }

            if ($request->has('variants') && is_array($request->variants)) {
                foreach ($request->variants as $index => $variant) {
                    if (empty($variant['color']) && empty($variant['size'])) {
                        continue;
                    }

                    $variantData = [
                        'product_id' => $product->product_id,
                        'color' => $variant['color'] ?? null,
                        'size' => $variant['size'] ?? null,
                        'stock' => $variant['stock'] ?? 0,
                    ];

                    if ($request->hasFile("variants.$index.color_image")) {
                        $colorImage = $request->file("variants.$index.color_image");
                        $colorImageName = time() . '_' . $index . '.' . $colorImage->getClientOriginalExtension();
                        $variantDir = public_path('images/products/variants');
                        if (!is_dir($variantDir)) {
                            mkdir($variantDir, 0755, true);
                        }
                        $colorImage->move($variantDir, $colorImageName);
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

        public function productEdit($product_id)
        {
            $product = Product::findOrFail($product_id);

            $brands = Brand::all();
            $categories = Category::all();

            $variants = $product->variants;
            
            $images = $product->images;

            return view('admin.edit-product', compact('product', 'brands', 'categories', 'variants', 'images'));
        }

        public function productUpdate(Request $request, $product_id)
        {
            $product = Product::findOrFail($product_id);

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

            if ($request->hasFile('prod_image')) {
                if ($product->prod_image && file_exists(public_path($product->prod_image))) {
                    unlink(public_path($product->prod_image));
                }

                $image = $request->file('prod_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/products'), $imageName);
                $validated['prod_image'] = 'images/products/' . $imageName;
            }

            if ($request->has('delete_images') && is_array($request->delete_images)) {
                foreach ($request->delete_images as $imageId) {
                    $productImage = ProductImage::find($imageId);
                    if ($productImage && $productImage->product_id == $product_id) {
                        if (file_exists(public_path($productImage->image_path))) {
                            unlink(public_path($productImage->image_path));
                        }
                        $productImage->delete();
                    }
                }
            }

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

            $product->update($validated);

            return redirect()->route('admin.products')->with('success', 'Product updated successfully!');
        }

        public function productDestroy($product_id)
        {
            $product = Product::findOrFail($product_id);

            $product->delete();

            return redirect()->route('admin.products')->with('success', 'Product moved to trash.');
        }

        public function productsTrashed()
        {
            $products = Product::onlyTrashed()->with('brand', 'category')->get();

            return view('admin.products-trashed', compact('products'));
        }

        public function productRestore($product_id)
        {
            $product = Product::withTrashed()->findOrFail($product_id);

            $product->restore();

            return redirect()->route('admin.products.trashed')->with('success', 'Product restored successfully!');
        }

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

            if ($request->hasFile('brand_logo')) {
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

            if ($request->hasFile('color_image')) {
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

            if ($variant->color_image && file_exists(public_path($variant->color_image))) {
                unlink(public_path($variant->color_image));
            }

            $variant->delete();

            return redirect()->route('admin.products.edit', $product_id)->with('success', 'Variant deleted successfully!');
        }

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
            $order = Order::with(['items.variant.product', 'user', 'paymentMethod'])->findOrFail($order_id);

            $request->validate([
                'order_status' => 'required|in:pending,processing,shipped,completed,cancelled',
            ]);

            $oldStatus = $order->order_status;
            $newStatus = $request->order_status;

            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                $order->load('items.variant');
                foreach ($order->items as $item) {
                    if ($item->variant) {
                        $item->variant->increment('stock', $item->oi_quantity);
                    }
                }
            }

            $order->update(['order_status' => $newStatus]);

            try {
                ReceiptGenerator::generateReceipt($order);

                $order = $order->fresh();
                $order->load('user', 'items.variant.product', 'paymentMethod');

                Mail::to($order->user->email)->send(new OrderStatusUpdated($order, $oldStatus, $newStatus));

                return redirect()->back()->with('success', 'Order #' . $order->order_id . ' status updated to ' . ucfirst($newStatus) . ' and email with PDF receipt sent to customer.');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error updating order status and sending email', [
                    'order_id' => $order_id,
                    'error' => $e->getMessage()
                ]);
                
                return redirect()->back()->with('error', 'Status updated but failed to send email: ' . $e->getMessage());
            }
        }

        public function salesCharts(Request $request)
        {
            $yearlySalesData = DB::table('orders')
                ->whereYear('created_at', date('Y'))
                ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->all();

            $yearlySalesChart = new YearlySalesChart();
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $chartMonths = [];
            $chartData = [];
            for ($i = 1; $i <= 12; $i++) {
                $chartMonths[] = $months[$i - 1];
                $chartData[] = isset($yearlySalesData[$i]) ? (float)$yearlySalesData[$i] : 0;
            }
            $yearlySalesChart->labels($chartMonths);
            $yearlySalesChart->dataset('Monthly Sales 2026', 'bar', $chartData)
                ->backgroundColor('rgba(54, 162, 235, 0.5)');
            $yearlySalesChart->options([
                'responsive' => true,
                'legend' => ['display' => true],
                'tooltips' => ['enabled' => true],
                'aspectRatio' => 1.5,
                'scales' => [
                    'yAxes' => [['display' => true]],
                    'xAxes' => [['gridLines' => ['display' => false], 'display' => true]],
                ],
            ]);

            $endDate = $request->input('end_date', date('Y-m-d'));
            $startDate = $request->input('start_date', date('Y-m-d', strtotime('-30 days')));
            
            $rangeData = DB::table('orders')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date')
                ->all();

            $salesRangeChart = new SalesRangeChart();
            $salesRangeChart->labels(array_keys($rangeData));
            $salesRangeChart->dataset('Sales by Date Range', 'bar', array_values(array_map('floatval', $rangeData)))
                ->backgroundColor('rgba(255, 159, 64, 0.5)');
            $salesRangeChart->options([
                'responsive' => true,
                'legend' => ['display' => true],
                'tooltips' => ['enabled' => true],
                'aspectRatio' => 1.5,
                'scales' => [
                    'yAxes' => [['display' => true]],
                    'xAxes' => [['gridLines' => ['display' => false], 'display' => true]],
                ],
            ]);

            $productSalesData = DB::table('order_items as oi')
                ->join('product_variants as pv', 'oi.variant_id', '=', 'pv.variant_id')
                ->join('products as p', 'pv.product_id', '=', 'p.product_id')
                ->selectRaw('p.product_name, SUM(oi.oi_quantity * oi.oi_price) as total')
                ->groupBy('p.product_id', 'p.product_name')
                ->orderByDesc('total')
                ->limit(8)
                ->pluck('total', 'product_name')
                ->all();

            $productPieChart = new ProductPieChart();
            $productPieChart->labels(array_keys($productSalesData));
            $productPieChart->dataset(
                'Sales by Product',
                'pie',
                array_values(array_map('floatval', $productSalesData))
            )->backgroundColor([
                'rgba(255, 99, 132, 0.5)',
                'rgba(54, 162, 235, 0.5)',
                'rgba(255, 206, 86, 0.5)',
                'rgba(75, 192, 192, 0.5)',
                'rgba(153, 102, 255, 0.5)',
                'rgba(255, 159, 64, 0.5)',
                'rgba(199, 199, 199, 0.5)',
                'rgba(83, 102, 255, 0.5)',
            ]);
            $productPieChart->options([
                'responsive' => true,
                'legend' => ['display' => true],
                'tooltips' => ['enabled' => true],
                'aspectRatio' => 1.25,
            ]);

            return view('admin.sales-charts', compact('yearlySalesChart', 'salesRangeChart', 'productPieChart'));
        }
    }


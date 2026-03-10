
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Verified;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Models\User;
use Illuminate\Http\Request;

Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->role === 'admin'
            ? redirect()->route('admin.products')
            : redirect()->route('home');
    }
    return redirect()->route('login');
});

Route::get('/home', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }
    $products = \App\Models\Product::with('brand', 'category')->take(24)->get();
    return view('home', compact('products'));
})->name('home')->middleware('auth');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
// Password reset route for forgot password feature
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');

// Product routes (customer)
Route::middleware('auth')->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product_id}', [ProductController::class, 'show'])->name('products.show');
});

// Review routes (customer)
Route::middleware('auth')->group(function () {
    Route::post('/products/{product_id}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review_id}', [ReviewController::class, 'update'])->name('reviews.update');
});

// Cart routes
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{cart_item_id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart_item_id}', [CartController::class, 'remove'])->name('cart.remove');
});

// Checkout routes
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'view'])->name('checkout.view');
    Route::post('/checkout/place', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
    Route::get('/orders/{order_id}/confirmation', [CheckoutController::class, 'confirmation'])->name('orders.confirmation');
});

// Orders routes (customer)
Route::middleware('auth')->group(function () {
    Route::get('/orders', [OrderController::class, 'myOrders'])->name('orders.mine');
});

// User profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', function () {
        return redirect()->route('admin.products');
    });
    Route::get('/admin/dashboard', function () {
        return redirect()->route('admin.products');
    })->name('admin.dashboard');
    
        // Products routes
    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/admin/products/create', [AdminController::class, 'create'])->name('admin.products.create');
    Route::get('/admin/products/trashed', [AdminController::class, 'productsTrashed'])->name('admin.products.trashed');
    Route::get('/admin/products/import', [AdminController::class, 'productsImportForm'])->name('admin.products.import');
    Route::post('/admin/products/import', [AdminController::class, 'productsImportStore'])->name('admin.products.import.store');
    Route::post('/admin/products/store', [AdminController::class, 'store'])->name('admin.products.store');
    Route::post('/admin/variants/store', [AdminController::class, 'storeVariant'])->name('admin.variants.store');
    Route::get('/admin/products/{product_id}/edit', [AdminController::class, 'productEdit'])->name('admin.products.edit');
    Route::put('/admin/products/{product_id}/update', [AdminController::class, 'productUpdate'])->name('admin.products.update');
    Route::delete('/admin/products/{product_id}', [AdminController::class, 'productDestroy'])->name('admin.products.destroy');
    Route::put('/admin/products/{product_id}/restore', [AdminController::class, 'productRestore'])->name('admin.products.restore');

    // Variants routes
    Route::get('/admin/variants/{variant_id}/edit', [AdminController::class, 'variantEdit'])->name('admin.variants.edit');
    Route::put('/admin/variants/{variant_id}/update', [AdminController::class, 'variantUpdate'])->name('admin.variants.update');
    Route::delete('/admin/variants/{variant_id}', [AdminController::class, 'variantDestroy'])->name('admin.variants.destroy');

    // Brands routes
    Route::get('/admin/brands', [AdminController::class, 'brands'])->name('admin.brands');
    Route::get('/admin/brands/create', [AdminController::class, 'brandCreate'])->name('admin.brands.create');
    Route::post('/admin/brands/store', [AdminController::class, 'brandStore'])->name('admin.brands.store');
    Route::get('/admin/brands/{brand_id}/edit', [AdminController::class, 'brandEdit'])->name('admin.brands.edit');
    Route::put('/admin/brands/{brand_id}/update', [AdminController::class, 'brandUpdate'])->name('admin.brands.update');
    Route::delete('/admin/brands/{brand_id}', [AdminController::class, 'brandDestroy'])->name('admin.brands.destroy');
    
    // Categories routes
    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/categories/create', [AdminController::class, 'categoryCreate'])->name('admin.categories.create');
    Route::post('/admin/categories/store', [AdminController::class, 'categoryStore'])->name('admin.categories.store');
    Route::get('/admin/categories/{category_id}/edit', [AdminController::class, 'categoryEdit'])->name('admin.categories.edit');
    Route::put('/admin/categories/{category_id}/update', [AdminController::class, 'categoryUpdate'])->name('admin.categories.update');
    Route::delete('/admin/categories/{category_id}', [AdminController::class, 'categoryDestroy'])->name('admin.categories.destroy');
    
    // Users (list, update role, update status)
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::put('/admin/users/{user_id}', [AdminController::class, 'userUpdate'])->name('admin.users.update');

    // Orders (admin)
    Route::get('/admin/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/admin/orders/{order_id}', [AdminController::class, 'orderShow'])->name('admin.orders.show');
    Route::put('/admin/orders/{order_id}/status', [AdminController::class, 'orderUpdateStatus'])->name('admin.orders.updateStatus');

    // Reviews (admin)
    Route::get('/admin/reviews', [ReviewController::class, 'adminIndex'])->name('admin.reviews');
    Route::delete('/admin/reviews/{review_id}', [ReviewController::class, 'destroy'])->name('admin.reviews.destroy');
});

// Email verification routes
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

// Verification link works without login: user clicks link in Mailtrap, we verify and redirect to login
Route::get('/email/verify/{id}/{hash}', function (Request $request) {
    $user = User::find($request->route('id'));
    if (! $user) {
        abort(404, 'User not found.');
    }
    if (! hash_equals(sha1($user->getEmailForVerification()), (string) $request->route('hash'))) {
        abort(403, 'Invalid verification link.');
    }
    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }
    return redirect()->route('login')->with('success', 'Your email has been verified. You can now log in.');
})->middleware('signed')->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Verified;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\AdminController;
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

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return redirect()->route('admin.products');
    })->name('admin.dashboard');
    
    // Products routes
    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/admin/products/create', [AdminController::class, 'create'])->name('admin.products.create');
    Route::post('/admin/products/store', [AdminController::class, 'store'])->name('admin.products.store');
    Route::post('/admin/variants/store', [AdminController::class, 'storeVariant'])->name('admin.variants.store');
    
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

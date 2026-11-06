<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

Route::controller(FrontendController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/shop', 'shop')->name('shop.index');
    Route::get('/shop/{slug}', 'product')->name('shop.show');
    Route::get('/3d-printing', 'services')->name('services');
    Route::get('/drones', 'drones')->name('drones');
    Route::get('/about', 'about')->name('about');
    Route::get('/contact', 'contact')->name('contact');
});

// Quote / contact form submissions (saved to Admin → Enquiries).
Route::post('/enquiries', [EnquiryController::class, 'store'])->middleware('throttle:5,1')->name('enquiries.store');

// Customer accounts.
Route::controller(AccountController::class)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', 'login')->name('login');
        Route::post('/login', 'authenticate')->middleware('throttle:10,1')->name('login.store');
        Route::get('/register', 'register')->name('register');
        Route::post('/register', 'storeRegistration')->middleware('throttle:10,1')->name('register.store');
        Route::get('/forgot-password', 'forgotPassword')->name('password.request');
        Route::get('/reset-password/{token?}', 'resetPassword')->name('password.reset');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', 'logout')->name('logout');
        Route::get('/account', 'dashboard')->name('account');
        Route::get('/account/orders/{order}', 'order')->name('account.orders.show');
        Route::put('/account', 'updateDetails')->name('account.update');
        Route::put('/account/address', 'updateAddress')->name('account.address');
        Route::put('/account/password', 'updatePassword')->name('account.password');
    });
});

// Shopping bag and checkout.
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->middleware('throttle:60,1')->name('cart.store');
Route::patch('/cart/{product}', [CartController::class, 'update'])->whereNumber('product')->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'destroy'])->whereNumber('product')->name('cart.destroy');
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');
});

// Admin panel.
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [Admin\AuthController::class, 'create'])->name('login');
    Route::post('login', [Admin\AuthController::class, 'store'])->middleware('throttle:10,1')->name('login.store');

    Route::middleware(['auth', 'can:access-admin'])->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'destroy'])->name('logout');
        Route::get('/', Admin\DashboardController::class)->name('dashboard');
        Route::resource('orders', Admin\OrderController::class)->only(['index', 'show', 'update']);
        Route::resource('enquiries', Admin\EnquiryController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::resource('products', Admin\ProductController::class)->except('show');
        Route::resource('projects', Admin\ProjectController::class)->except('show');
        Route::get('customers', Admin\CustomerController::class)->name('customers.index');
        Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
        Route::get('profile', [Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [Admin\ProfileController::class, 'update'])->name('profile.update');
    });
});

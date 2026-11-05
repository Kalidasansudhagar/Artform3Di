<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
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

// Account screens (frontend preview — no authentication yet).
Route::controller(AccountController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::get('/register', 'register')->name('register');
    Route::get('/forgot-password', 'forgotPassword')->name('password.request');
    Route::get('/reset-password/{token?}', 'resetPassword')->name('password.reset');
    Route::get('/account', 'dashboard')->name('account');
});

// Admin panel (frontend preview — add auth + admin middleware in the backend phase).
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
});

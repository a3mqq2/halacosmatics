<?php

use App\Http\Controllers\Marketer\CartController;
use App\Http\Controllers\Marketer\CheckoutController;
use App\Http\Controllers\Marketer\DashboardController;
use App\Http\Controllers\Marketer\ProductController;
use App\Http\Controllers\Marketer\ProfileController;
use App\Http\Controllers\Marketer\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:marketer', 'marketer.active'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/products',  [ProductController::class,  'index'])->name('products');

    Route::get('/cart',                      [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add',                 [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{cartItem}',         [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}',        [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart',                   [CartController::class, 'clear'])->name('cart.clear');

    Route::get('/checkout',  [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/orders',        [\App\Http\Controllers\Marketer\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Marketer\OrderController::class, 'show'])->name('orders.show');

    Route::get('/reports',                  [ReportController::class, 'index'])->name('reports');

    Route::get('/profile',                  [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/password',        [ProfileController::class, 'updatePassword'])->name('profile.password');
});

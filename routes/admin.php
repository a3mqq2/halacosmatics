<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VaultController;
use App\Http\Controllers\VaultTransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MarketerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)
        ->except(['show'])
        ->middleware('permission:users');

    Route::middleware('permission:marketers.view')->group(function () {
        Route::resource('marketers', MarketerController::class)->only(['index', 'show']);
    });
    Route::middleware('permission:marketers.manage')->group(function () {
        Route::resource('marketers', MarketerController::class)->except(['index', 'show']);
        Route::patch('marketers/{marketer}/toggle',  [MarketerController::class, 'toggle'])->name('marketers.toggle');
        Route::patch('marketers/{marketer}/approve', [MarketerController::class, 'approve'])->name('marketers.approve');
        Route::patch('marketers/{marketer}/reject',  [MarketerController::class, 'reject'])->name('marketers.reject');
    });
    Route::middleware('permission:marketers.finance')->group(function () {
        Route::post('marketers/{marketer}/transactions', [MarketerController::class, 'storeTransaction'])->name('marketers.transactions.store');
    });

    Route::middleware('permission:products.view')->group(function () {
        Route::resource('products', ProductController::class)->only(['index', 'show']);
    });
    Route::middleware('permission:products.edit')->group(function () {
        Route::resource('products', ProductController::class)->except(['index', 'show']);
        Route::patch('products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');
    });
    Route::middleware('permission:products.stock')->group(function () {
        Route::post('products/{product}/add',      [ProductController::class, 'addQuantity'])->name('products.add-quantity');
        Route::post('products/{product}/subtract', [ProductController::class, 'subtractQuantity'])->name('products.subtract-quantity');
    });

    Route::get('orders',         [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::middleware('permission:orders.approve')->group(function () {
        Route::patch('orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
        Route::patch('orders/{order}/reject',  [OrderController::class, 'reject'])->name('orders.reject');
    });
    Route::middleware('permission:orders.deliver')->group(function () {
        Route::post('orders/{order}/dispatch',      [OrderController::class, 'dispatch'])->name('orders.dispatch');
        Route::post('orders/{order}/deliver',       [OrderController::class, 'deliver'])->name('orders.deliver');
        Route::post('orders/{order}/fail-delivery', [OrderController::class, 'failDelivery'])->name('orders.fail-delivery');
    });
    Route::middleware('permission:orders.returned')->group(function () {
        Route::post('orders/{order}/accept-return', [OrderController::class, 'acceptReturn'])->name('orders.accept-return');
    });

    Route::middleware('permission:agents')->group(function () {
        Route::resource('agents', AgentController::class);
        Route::patch('agents/{agent}/toggle',      [AgentController::class, 'toggle'])->name('agents.toggle');
        Route::post('agents/{agent}/transactions', [AgentController::class, 'storeTransaction'])->name('agents.transactions.store');
    });

    Route::middleware('permission:vaults')->group(function () {
        Route::resource('vaults', VaultController::class)->except(['show', 'destroy']);
        Route::get('vaults/{vault}/transactions',  [VaultTransactionController::class, 'index'])->name('vaults.transactions.index');
        Route::post('vaults/{vault}/transactions', [VaultTransactionController::class, 'store'])->name('vaults.transactions.store');
    });

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index')->middleware('permission:reports');

    Route::middleware('permission:settings')->group(function () {
        Route::get('settings',                [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings',                [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/musafir',       [SettingController::class, 'musafirSave'])->name('settings.musafir');
        Route::post('settings/musafir/login', [SettingController::class, 'musafirLogin'])->name('settings.musafir.login');
    });
});

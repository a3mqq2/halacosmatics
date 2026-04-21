<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MarketerController;
use App\Http\Controllers\Webhook\MosafirWebhookController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', 'dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register/marketer', [MarketerController::class, 'register'])->name('marketers.register');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('webhook/mosafir', [MosafirWebhookController::class, 'handle'])->name('webhook.mosafir');

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FarmerDashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

// Publik
Route::get('/', [OrderController::class, 'index'])->name('catalog.index');
Route::post('/webhooks/midtrans', [PaymentWebhookController::class, 'handle'])->name('webhooks.midtrans');

// Khusus yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
});

// Wajib login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/checkout', [OrderController::class, 'store'])
        ->middleware('throttle:checkout')
        ->name('checkout.store');
    Route::get('/orders/{order:order_number}', [OrderController::class, 'show'])->name('orders.show');
});

// Khusus Petani & Admin (RBAC)
Route::middleware(['auth', 'role:farmer,admin'])->group(function () {
    Route::get('/farmer/dashboard', [FarmerDashboardController::class, 'index'])->name('farmer.dashboard');
    Route::patch('/farmer/orders/{order:order_number}/harvest', [FarmerDashboardController::class, 'markHarvested'])
        ->name('farmer.orders.harvest');
});
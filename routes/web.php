<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Publik
Route::get('/', [OrderController::class, 'index'])->name('catalog.index');
Route::post('/webhooks/midtrans', [PaymentWebhookController::class, 'handle'])->name('webhooks.midtrans');

// Khusus yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Wajib login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
    Route::get('/orders/{order:order_number}', [OrderController::class, 'show'])->name('orders.show');
});

Route::middleware(['auth', 'role:farmer,admin'])->group(function () {
    Route::get('/farmer/dashboard', function (Request $request) {
        $user = $request->user();
        return "Halo {$user->name} ({$user->role}) — Akses Dashboard Petani Terbuka!";
    })->name('farmer.dashboard');
});
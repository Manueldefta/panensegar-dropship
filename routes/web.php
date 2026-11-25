<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentWebhookController;

Route::get('/', [OrderController::class, 'index'])->name('catalog.index');
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
Route::get('/orders/{order:order_number}', [OrderController::class, 'show'])->name('orders.show');
Route::post('/webhooks/midtrans', [PaymentWebhookController::class, 'handle'])->name('webhooks.midtrans');
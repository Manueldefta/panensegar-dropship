<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\CheckoutService;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(CheckoutService $checkoutService)
    {
        $deliveryDate = $checkoutService->getDeliveryDate();
        $products = Product::where('is_available', true)->get();
        $bookedMap = $checkoutService->getBookedQuantities($deliveryDate);

        return view('catalog', compact('products', 'deliveryDate', 'bookedMap'));
    }

    public function store(CheckoutRequest $request, CheckoutService $checkoutService)
    {
        $order = $checkoutService->createOrder(
            user: $request->user(),
            requestedItems: $request->validated('items'),
            shippingAddress: $request->validated('shipping_address')
        );

        return redirect()->route('orders.show', $order->order_number);
    }

    public function show(Request $request, Order $order, MidtransService $midtransService)
    {
        // Anti-IDOR: hanya pemilik pesanan atau admin yang boleh melihat struk
        $user = $request->user();
        if ($order->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Anda tidak berhak mengakses pesanan ini.');
        }

        $order->load(['items.product', 'user']);

        $paymentError = null;
        if ($order->status === 'pending' && !$order->snap_token) {
            try {
                $midtransService->getOrCreateSnapToken($order);
            } catch (\Throwable $e) {
                Log::error('Gagal membuat Midtrans Snap Token', [
                    'order_number' => $order->order_number,
                    'message' => $e->getMessage(),
                ]);

                $paymentError = 'Layanan pembayaran sedang mengalami gangguan sementara. Silakan muat ulang halaman beberapa saat lagi.';
            }
        }

        return view('order-success', compact('order', 'paymentError'));
    }
}
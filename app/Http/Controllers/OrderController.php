<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CheckoutService;

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
        // TODO: Ganti dengan auth()->user() setelah fitur Login selesai dibuat
        $customer = User::where('role', 'customer')->firstOrFail();

        $order = $checkoutService->createOrder(
            user: $customer,
            requestedItems: $request->validated('items'),
            shippingAddress: $request->validated('shipping_address')
        );

        return redirect()->route('orders.show', $order->order_number);
    }

    public function show(Order $order)
    {
        $order->load('items.product');

        return view('order-success', compact('order'));
    }
}
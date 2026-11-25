<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class FarmerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $selectedDate = $request->query('date', now('Asia/Jakarta')->addDay()->toDateString());

        // 1. Rekap total sayur yang harus dipanen (hanya pesanan lunas)
        $harvestSummary = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereDate('orders.delivery_date', $selectedDate)
            ->whereIn('orders.status', ['paid', 'harvested'])
            ->when($user->isFarmer(), fn ($q) => $q->where('products.farmer_id', $user->id))
            ->groupBy('products.id', 'products.name', 'order_items.unit_snapshot')
            ->selectRaw('
                products.id as product_id,
                products.name as product_name,
                order_items.unit_snapshot as unit,
                SUM(order_items.quantity) as total_qty,
                SUM(order_items.quantity * order_items.base_price_snapshot) as farmer_payout
            ')
            ->orderBy('products.name')
            ->get();

        // 2. Daftar pesanan lunas untuk tanggal kirim terpilih
        $orders = Order::with(['user', 'items.product'])
            ->whereDate('delivery_date', $selectedDate)
            ->whereIn('status', ['paid', 'harvested', 'delivered'])
            ->when($user->isFarmer(), function ($q) use ($user) {
                $q->whereHas('items.product', fn ($sub) => $sub->where('farmer_id', $user->id));
            })
            ->latest('paid_at')
            ->get();

        $totalPayout = $harvestSummary->sum('farmer_payout');

        return view('farmer.dashboard', compact('harvestSummary', 'orders', 'selectedDate', 'totalPayout'));
    }

    public function markHarvested(Request $request, Order $order)
    {
        $user = $request->user();

        if ($user->isFarmer()) {
            $ownsItem = $order->items()->whereHas('product', fn ($q) => $q->where('farmer_id', $user->id))->exists();
            if (!$ownsItem) {
                abort(403, 'Pesanan ini tidak berisi produk dari kebun Anda.');
            }
        }

        if ($order->status !== 'paid') {
            return back()->with('error', 'Hanya pesanan berstatus PAID yang bisa ditandai sudah dipanen.');
        }

        $order->update(['status' => 'harvested']);

        return back()->with('success', "Pesanan {$order->order_number} berhasil ditandai sudah dipanen.");
    }
}
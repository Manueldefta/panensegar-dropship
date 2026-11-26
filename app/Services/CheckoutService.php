<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    private const MAX_PENDING_ORDERS = 2;

    /**
     * Aturan PO H-1:
     * Pesan sebelum jam 20.00 WIB -> dikirim besok.
     * Pesan jam 20.00 WIB ke atas -> dikirim lusa.
     */
    public function getDeliveryDate(): Carbon
    {
        $now = now('Asia/Jakarta');

        return $now->hour < 20
            ? $now->copy()->addDay()->startOfDay()
            : $now->copy()->addDays(2)->startOfDay();
    }

    /**
     * Hitung total sayur yang sudah dipesan untuk tanggal kirim tertentu.
     */
    public function getBookedQuantities(Carbon $deliveryDate): array
    {
        return OrderItem::selectRaw('product_id, SUM(quantity) as total_booked')
            ->whereHas('order', function ($query) use ($deliveryDate) {
                $query->whereDate('delivery_date', $deliveryDate->toDateString())
                    ->whereIn('status', ['pending', 'paid', 'harvested', 'delivered']);
            })
            ->groupBy('product_id')
            ->pluck('total_booked', 'product_id')
            ->toArray();
    }

    /**
     * Proses checkout aman dengan Database Transaction, Row Lock, dan Anti-Denial of Inventory.
     */
    public function createOrder(User $user, array $requestedItems, string $shippingAddress): Order
    {
        // 1. Saring hanya produk yang dipesan (qty > 0)
        $filteredItems = collect($requestedItems)
            ->map(fn ($qty) => (int) $qty)
            ->filter(fn ($qty) => $qty > 0);

        if ($filteredItems->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Pilih minimal 1 sayuran sebelum melakukan checkout.',
            ]);
        }

        $deliveryDate = $this->getDeliveryDate();
        $productIds = $filteredItems->keys()->sort()->values()->all();

        return DB::transaction(function () use ($user, $filteredItems, $productIds, $shippingAddress, $deliveryDate) {
            // 2. Kunci baris user agar request paralel dari akun yang sama tidak bisa menembus batas pending
            User::whereKey($user->id)->lockForUpdate()->first();

            $activePendingCount = Order::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count();

            if ($activePendingCount >= self::MAX_PENDING_ORDERS) {
                throw ValidationException::withMessages([
                    'items' => "Kamu masih memiliki {$activePendingCount} pesanan yang belum dibayar. Selesaikan pembayaran terlebih dahulu sebelum membuat pesanan baru.",
                ]);
            }

            // 3. Kunci baris produk secara berurutan di level SQL (anti race-condition & deadlock)
            $products = Product::whereIn('id', $productIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $bookedMap = $this->getBookedQuantities($deliveryDate);
            $subtotal = 0;
            $preparedItems = [];

            foreach ($filteredItems as $productId => $qty) {
                $product = $products->get($productId);

                if (!$product || !$product->is_available) {
                    throw ValidationException::withMessages([
                        'items' => 'Salah satu sayuran yang kamu pilih sedang tidak tersedia.',
                    ]);
                }

                // 4. Cek sisa kuota panen harian
                $alreadyBooked = (int) ($bookedMap[$product->id] ?? 0);
                $remaining = $product->daily_quota - $alreadyBooked;

                if ($qty > $remaining) {
                    throw ValidationException::withMessages([
                        'items' => "Stok panen {$product->name} hanya tersisa {$remaining} ({$product->unit}).",
                    ]);
                }

                // 5. Server-side calculation & Price Snapshot
                $lineSubtotal = $product->selling_price * $qty;
                $subtotal += $lineSubtotal;

                $preparedItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_snapshot' => $product->unit,
                    'base_price_snapshot' => $product->base_price,
                    'selling_price_snapshot' => $product->selling_price,
                    'subtotal' => $lineSubtotal,
                ];
            }

            // Ongkir flat Rp5.000, gratis ongkir minimal belanja Rp50.000
            $shippingFee = $subtotal >= 50000 ? 0 : 5000;
            $totalAmount = $subtotal + $shippingFee;

            // 6. Simpan Order utama
            $order = Order::create([
                'order_number' => 'SYR-' . now('Asia/Jakarta')->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'user_id' => $user->id,
                'delivery_date' => $deliveryDate->toDateString(),
                'shipping_address' => $shippingAddress,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            // 7. Simpan rincian item beserta snapshot harganya
            foreach ($preparedItems as $itemData) {
                $order->items()->create($itemData);
            }

            return $order;
        });
    }
}
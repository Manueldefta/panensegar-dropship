<?php

namespace App\Console\Commands;

use App\Models\OrderItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateHarvestSummary extends Command
{
    protected $signature = 'harvest:summary {--date= : Tanggal kirim format YYYY-MM-DD (default besok)}';
    protected $description = 'Membuat rekap daftar panen harian untuk petani setelah jam cut-off PO';

    public function handle(): int
    {
        $deliveryDate = $this->option('date') ?: now('Asia/Jakarta')->addDay()->toDateString();

        $summary = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereDate('orders.delivery_date', $deliveryDate)
            ->whereIn('orders.status', ['paid', 'harvested'])
            ->groupBy('products.id', 'products.name', 'order_items.unit_snapshot')
            ->selectRaw('
                products.name as product_name,
                order_items.unit_snapshot as unit,
                SUM(order_items.quantity) as total_qty,
                SUM(order_items.quantity * order_items.base_price_snapshot) as farmer_payout
            ')
            ->orderBy('products.name')
            ->get();

        if ($summary->isEmpty()) {
            $this->warn("Tidak ada pesanan lunas untuk jadwal kirim {$deliveryDate}.");
            return self::SUCCESS;
        }

        $lines = ["=== REKAP PANEN PANENSEGAR ({$deliveryDate}) ==="];
        foreach ($summary as $item) {
            $lines[] = "- {$item->product_name}: {$item->total_qty} x {$item->unit} (Setoran: Rp " . number_format($item->farmer_payout, 0, ',', '.') . ')';
        }
        $lines[] = 'Total Setoran Petani: Rp ' . number_format($summary->sum('farmer_payout'), 0, ',', '.');

        $message = implode(PHP_EOL, $lines);

        Log::info($message);
        $this->line($message);

        return self::SUCCESS;
    }
}
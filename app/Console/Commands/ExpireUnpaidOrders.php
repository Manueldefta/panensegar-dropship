<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class ExpireUnpaidOrders extends Command
{
    protected $signature = 'orders:expire-unpaid {--minutes=120 : Batas waktu bayar dalam menit}';
    protected $description = 'Mengubah pesanan pending yang melewati batas bayar menjadi expired agar kuota panen kembali tersedia';

    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');
        $cutoff = now()->subMinutes($minutes);

        $count = Order::where('status', 'pending')
            ->where('created_at', '<=', $cutoff)
            ->update(['status' => 'expired']);

        $this->info("Selesai: {$count} pesanan pending kedaluwarsa diubah menjadi expired.");

        return self::SUCCESS;
    }
}
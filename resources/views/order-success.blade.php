<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan {{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="max-w-xl mx-auto p-4 sm:p-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between border-b pb-4">
                <div>
                    <p class="text-xs text-gray-500">Nomor Pesanan</p>
                    <h1 class="text-lg font-bold text-gray-900">{{ $order->order_number }}</h1>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 uppercase">
                    {{ $order->status }}
                </span>
            </div>

            <div class="py-4 text-sm space-y-1 border-b">
                <p><span class="text-gray-500">Jadwal Antar:</span> <strong>{{ $order->delivery_date->format('d M Y') }}</strong></p>
                <p><span class="text-gray-500">Alamat:</span> {{ $order->shipping_address }}</p>
            </div>

            <div class="py-4 border-b space-y-2 text-sm">
                @foreach ($order->items as $item)
                    <div class="flex justify-between">
                        <span>{{ $item->product->name }} ({{ $item->quantity }} × {{ $item->unit_snapshot }})</span>
                        <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <div class="py-4 space-y-1.5 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Ongkir</span>
                    <span>{{ $order->shipping_fee === 0 ? 'Gratis' : 'Rp ' . number_format($order->shipping_fee, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t">
                    <span>Total Bayar</span>
                    <span class="text-green-700">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="mt-4 space-y-2">
                <button disabled class="w-full bg-gray-300 text-gray-600 font-semibold py-2.5 rounded-xl cursor-not-allowed">
                    Bayar via QRIS (Tahap Payment Gateway Berikutnya)
                </button>
                <a href="{{ route('catalog.index') }}" class="block text-center text-sm text-green-700 hover:underline pt-2">
                    ← Kembali ke Katalog
                </a>
            </div>
        </div>
    </div>
</body>
</html>
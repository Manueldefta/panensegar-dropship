<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan {{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script
        src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('services.midtrans.client_key') }}"
    ></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="max-w-xl mx-auto p-4 sm:p-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between border-b pb-4">
                <div>
                    <p class="text-xs text-gray-500">Nomor Pesanan</p>
                    <h1 class="text-lg font-bold text-gray-900">{{ $order->order_number }}</h1>
                </div>
                @php
                    $badgeColor = match($order->status) {
                        'paid', 'harvested', 'delivered' => 'bg-green-100 text-green-800',
                        'pending' => 'bg-amber-100 text-amber-800',
                        default => 'bg-red-100 text-red-800',
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase {{ $badgeColor }}">
                    {{ $order->status }}
                </span>
            </div>

            @if (!empty($paymentError))
                <div class="mt-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-xs">
                    {{ $paymentError }}
                </div>
            @endif

            <div class="py-4 text-sm space-y-1 border-b">
                <p><span class="text-gray-500">Jadwal Antar:</span> <strong>{{ $order->delivery_date->format('d M Y') }}</strong></p>
                <p><span class="text-gray-500">Alamat:</span> {{ $order->shipping_address }}</p>
                @if ($order->paid_at)
                    <p><span class="text-gray-500">Dibayar pada:</span> {{ $order->paid_at->format('d M Y H:i') }}</p>
                @endif
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
                @if ($order->status === 'pending' && $order->snap_token)
                    <button
                        id="pay-button"
                        type="button"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl shadow transition"
                    >
                        Bayar Sekarang (QRIS / E-Wallet / VA)
                    </button>
                @elseif ($order->status === 'paid')
                    <div class="p-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm text-center font-medium">
                        Pembayaran Lunas! Pesananmu masuk daftar panen.
                    </div>
                @endif

                <a href="{{ route('catalog.index') }}" class="block text-center text-sm text-green-700 hover:underline pt-2">
                    ← Kembali ke Katalog
                </a>
            </div>
        </div>
    </div>

    @if ($order->status === 'pending' && $order->snap_token)
        <script>
            document.getElementById('pay-button').addEventListener('click', function () {
                window.snap.pay('{{ $order->snap_token }}', {
                    onSuccess: function () {
                        window.location.reload();
                    },
                    onPending: function () {
                        alert('Menunggu pembayaran diselesaikan.');
                    },
                    onError: function () {
                        alert('Pembayaran gagal, silakan coba lagi.');
                    }
                });
            });
        </script>
    @endif
</body>
</html>
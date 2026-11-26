<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan {{ $order->order_number }} — PanenSegar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <script
        src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('services.midtrans.client_key') }}"
    ></script>
</head>
<body class="bg-stone-50 text-stone-800 antialiased min-h-screen selection:bg-orange-100 selection:text-orange-900">

    {{-- Navbar Ringkas --}}
    <nav class="sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-stone-200/80">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('catalog.index') }}" class="flex items-center gap-2.5 group">
                <span class="relative w-9 h-9 rounded-xl bg-emerald-900 text-white font-extrabold flex items-center justify-center text-sm shadow-sm">
                    PS
                    <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-orange-500 ring-2 ring-white"></span>
                </span>
                <div class="leading-tight">
                    <span class="block font-extrabold tracking-tight text-emerald-950 text-base">PanenSegar</span>
                    <span class="block text-[11px] font-medium text-stone-500">Detail Pesanan</span>
                </div>
            </a>

            <div class="flex items-center gap-4 text-xs sm:text-sm font-semibold">
                <a href="{{ route('orders.mine') }}" class="text-emerald-800 hover:text-orange-600 transition">
                    Pesanan Saya
                </a>
                <a href="{{ route('catalog.index') }}" class="text-stone-600 hover:text-emerald-900 transition">
                    ← Katalog
                </a>
            </div>
        </div>
    </nav>

    @php
        $badgeStyle = match($order->status) {
            'paid' => 'bg-emerald-500/20 text-emerald-200 border-emerald-400/30',
            'harvested', 'delivered' => 'bg-sky-500/20 text-sky-200 border-sky-400/30',
            'pending' => 'bg-orange-500/25 text-orange-200 border-orange-400/40',
            default => 'bg-rose-500/25 text-rose-200 border-rose-400/40',
        };

        $statusLabel = match($order->status) {
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Lunas • Siap Panen',
            'harvested' => 'Sudah Dipanen',
            'delivered' => 'Terkirim',
            'expired' => 'Kedaluwarsa',
            'need_refund' => 'Perlu Refund',
            default => strtoupper($order->status),
        };

        $stepLevel = match($order->status) {
            'pending' => 1,
            'paid' => 2,
            'harvested', 'delivered' => 3,
            default => 0,
        };
    @endphp

    <main class="max-w-3xl mx-auto px-4 sm:px-6 py-8 sm:py-10">
        <div class="bg-white rounded-3xl border border-stone-200/80 shadow-sm overflow-hidden">

            {{-- Header Struk Deep Emerald --}}
            <div class="relative overflow-hidden bg-emerald-900 text-white p-6 sm:p-8">
                <div class="pointer-events-none absolute -top-20 -right-20 w-56 h-56 rounded-full bg-orange-500/20 blur-3xl"></div>

                <div class="relative flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-200/80">Nomor Pesanan</p>
                        <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight mt-1 tabular-nums">
                            {{ $order->order_number }}
                        </h1>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold border {{ $badgeStyle }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        {{ $statusLabel }}
                    </span>
                </div>

                {{-- Progress Stepper (tampil jika order aktif) --}}
                @if ($stepLevel > 0)
                    <div class="relative mt-6 pt-5 border-t border-white/15 grid grid-cols-3 gap-2 text-xs">
                        <div class="flex items-center gap-2 {{ $stepLevel >= 1 ? 'text-white font-semibold' : 'text-emerald-200/50' }}">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold shrink-0 {{ $stepLevel >= 2 ? 'bg-emerald-500 text-white' : 'bg-orange-500 text-white' }}">
                                {{ $stepLevel >= 2 ? '✓' : '1' }}
                            </span>
                            <span class="truncate">Checkout</span>
                        </div>
                        <div class="flex items-center gap-2 {{ $stepLevel >= 2 ? 'text-white font-semibold' : 'text-emerald-200/50' }}">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold shrink-0 {{ $stepLevel >= 3 ? 'bg-emerald-500 text-white' : ($stepLevel === 2 ? 'bg-orange-500 text-white' : 'bg-white/15 text-emerald-100') }}">
                                {{ $stepLevel >= 3 ? '✓' : '2' }}
                            </span>
                            <span class="truncate">Lunas</span>
                        </div>
                        <div class="flex items-center gap-2 {{ $stepLevel >= 3 ? 'text-white font-semibold' : 'text-emerald-200/50' }}">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold shrink-0 {{ $stepLevel >= 3 ? 'bg-emerald-500 text-white' : 'bg-white/15 text-emerald-100' }}">
                                {{ $stepLevel >= 3 ? '✓' : '3' }}
                            </span>
                            <span class="truncate">Dipanen</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Isi Struk --}}
            <div class="p-6 sm:p-8 space-y-6">
                @if (!empty($paymentError))
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm">
                        <p class="font-bold mb-0.5">Kendala Koneksi Pembayaran</p>
                        <p>{{ $paymentError }}</p>
                    </div>
                @endif

                {{-- Info Pengiriman --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-stone-50 rounded-2xl border border-stone-200/70 p-4 sm:p-5 text-sm">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Jadwal Pengiriman</p>
                        <p class="font-bold text-stone-900 mt-1">{{ $order->delivery_date->format('d M Y') }}</p>
                        @if ($order->paid_at)
                            <p class="text-xs text-emerald-700 font-medium mt-1 tabular-nums">
                                Dibayar: {{ $order->paid_at->format('d M Y, H:i') }} WIB
                            </p>
                        @endif
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Alamat Pengiriman</p>
                        <p class="text-stone-700 mt-1 leading-relaxed">{{ $order->shipping_address }}</p>
                    </div>
                </div>

                {{-- Rincian Sayuran --}}
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-stone-500 mb-3">Rincian Sayuran</h2>
                    <div class="divide-y divide-stone-100 border-y border-stone-100">
                        @foreach ($order->items as $item)
                            <div class="py-3 flex items-center justify-between gap-4 text-sm">
                                <div>
                                    <p class="font-semibold text-stone-900">{{ $item->product->name }}</p>
                                    <p class="text-xs text-stone-500 tabular-nums">
                                        {{ $item->quantity }} × {{$item->unit_snapshot }}
                                    </p>
                                </div>
                                <span class="font-bold text-stone-800 tabular-nums shrink-0">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Ringkasan Pembayaran --}}
                <div class="space-y-2 text-sm tabular-nums">
                    <div class="flex justify-between text-stone-600">
                        <span>Subtotal</span>
                        <span class="font-semibold text-stone-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-stone-600">
                        <span>Ongkos Kirim</span>
                        @if ($order->shipping_fee === 0)
                            <span class="font-bold text-emerald-700">Gratis</span>
                        @else
                            <span class="font-semibold text-stone-900">Rp {{ number_format($order->shipping_fee, 0, ',', '.') }}</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-baseline pt-3 mt-2 border-t border-stone-200/80">
                        <span class="font-extrabold text-stone-900 text-base">Total Bayar</span>
                        <span class="text-2xl font-extrabold text-emerald-900">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Aksi / Banner Status --}}
                <div class="pt-2 space-y-3">
                    @if ($order->status === 'pending' &&$order->snap_token)
                        <button
                            id="pay-button"
                            type="button"
                            class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3.5 px-5 rounded-xl shadow-sm transition active:scale-[0.99] flex items-center justify-center gap-2"
                        >
                            <span>Bayar Sekarang (QRIS / E-Wallet / VA)</span>
                        </button>
                        <p class="text-[11px] text-center text-stone-500">
                            Selesaikan pembayaran dalam 2 jam agar kuota panen tidak hangus otomatis.
                        </p>
                    @elseif ($order->status === 'paid')
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 text-sm text-center font-semibold">
                            Pembayaran Lunas! Pesananmu sudah masuk daftar panen petani.
                        </div>
                    @elseif ($order->status === 'harvested')
                        <div class="p-4 rounded-2xl bg-sky-50 border border-sky-200/80 text-sky-900 text-sm text-center font-semibold">
                            Sayuranmu sudah dipanen dan sedang disiapkan untuk pengiriman.
                        </div>
                    @elseif (in_array($order->status, ['expired', 'cancelled', 'need_refund'], true))
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-900 text-sm text-center font-semibold">
                            Status pesanan: {{ $statusLabel }}. Silakan buat pesanan baru jika masih membutuhkan.
                        </div>
                    @endif

                    <a href="{{ route('catalog.index') }}" class="block text-center text-sm font-semibold text-emerald-800 hover:text-orange-600 pt-1 transition">
                        ← Kembali ke Katalog Panen
                    </a>
                </div>
            </div>
        </div>
    </main>

    @if ($order->status === 'pending' &&$order->snap_token)
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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya — PanenSegar</title>
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
</head>
<body class="bg-stone-50 text-stone-800 antialiased min-h-screen selection:bg-orange-100 selection:text-orange-900">

    {{-- Navbar Sticky --}}
    <nav class="sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-stone-200/80">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('catalog.index') }}" class="flex items-center gap-2.5">
                <span class="relative w-9 h-9 rounded-xl bg-emerald-900 text-white font-extrabold flex items-center justify-center text-sm shadow-sm">
                    PS
                    <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-orange-500 ring-2 ring-white"></span>
                </span>
                <div class="leading-tight">
                    <span class="block font-extrabold tracking-tight text-emerald-950 text-base">PanenSegar</span>
                    <span class="block text-[11px] font-medium text-stone-500">Riwayat Pesanan</span>
                </div>
            </a>

            <div class="flex items-center gap-3 text-xs sm:text-sm">
                <a href="{{ route('catalog.index') }}" class="font-semibold text-stone-600 hover:text-emerald-900 transition">
                    ← Katalog
                </a>
                @if (auth()->user()->isFarmer() || auth()->user()->isAdmin())
                    <a href="{{ route('farmer.dashboard') }}" class="px-3 py-1.5 rounded-lg bg-emerald-900 text-white font-semibold text-xs hover:bg-emerald-800 transition">
                        Dashboard Petani
                    </a>
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="font-medium text-stone-500 hover:text-orange-600 transition">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    @php
        $tabs = [
            '' => 'Semua',
            'pending' => 'Menunggu Bayar',
            'paid' => 'Lunas',
            'harvested' => 'Dipanen',
            'expired' => 'Kedaluwarsa',
        ];
    @endphp

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-8 space-y-6">

        {{-- Header Banner --}}
        <section class="relative overflow-hidden bg-emerald-900 text-white rounded-3xl shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div class="pointer-events-none absolute -top-20 -right-20 w-56 h-56 rounded-full bg-orange-500/20 blur-3xl"></div>

            <div class="relative space-y-1.5">
                <span class="text-xs font-bold uppercase tracking-wider text-orange-300">Akun {{ auth()->user()->name }}</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Pesanan Saya</h1>
                <p class="text-xs sm:text-sm text-emerald-100/80">
                    Pantau status panen harian atau lanjutkan pembayaran pesanan yang masih tertunda.
                </p>
            </div>

            <a href="{{ route('catalog.index') }}" class="relative inline-flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs sm:text-sm px-5 py-3 rounded-xl shadow-sm transition shrink-0">
                + Pesan Sayur Lagi
            </a>
        </section>

        {{-- Pengingat Pesanan Pending --}}
        @if ($pendingCount > 0)
            <div class="rounded-2xl bg-orange-50 border border-orange-200/80 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-sm">
                <div class="flex items-start gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500 mt-1.5 shrink-0 animate-pulse"></span>
                    <p class="text-orange-950 text-xs sm:text-sm leading-relaxed">
                        Kamu memiliki <strong>{{ $pendingCount }} pesanan menunggu pembayaran</strong>. Selesaikan dalam 2 jam agar kuota panen tidak hangus otomatis.
                    </p>
                </div>
                @if ($status !== 'pending')
                    <a href="{{ route('orders.mine', ['status' => 'pending']) }}" class="text-xs font-bold text-orange-700 hover:text-orange-900 underline shrink-0">
                        Lihat yang belum dibayar
                    </a>
                @endif
            </div>
        @endif

        {{-- Tab Filter Status --}}
        <div class="flex flex-wrap items-center gap-2">
            @foreach ($tabs as $key => $label)
                @php
                    $isActive = ($status ?? '') === $key;
                @endphp
                <a
                    href="{{ $key === '' ? route('orders.mine') : route('orders.mine', ['status' => $key]) }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold border transition {{ $isActive ? 'bg-emerald-900 text-white border-emerald-900 shadow-sm' : 'bg-white text-stone-600 border-stone-200 hover:border-emerald-300 hover:text-emerald-900' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Daftar Kartu Pesanan --}}
        <section class="space-y-4">
            @forelse ($orders as $order)
                @php
                    $badgeClass = match($order->status) {
                        'paid' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                        'harvested', 'delivered' => 'bg-sky-50 text-sky-800 border-sky-200',
                        'pending' => 'bg-orange-50 text-orange-700 border-orange-200',
                        default => 'bg-rose-50 text-rose-700 border-rose-200',
                    };

                    $statusText = match($order->status) {
                        'pending' => 'Menunggu Pembayaran',
                        'paid' => 'Lunas • Siap Panen',
                        'harvested' => 'Sudah Dipanen',
                        'delivered' => 'Terkirim',
                        'expired' => 'Kedaluwarsa',
                        'need_refund' => 'Perlu Refund',
                        default => strtoupper($order->status),
                    };
                @endphp

                <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-5 sm:p-6 space-y-4 hover:border-stone-300 transition">
                    <div class="flex flex-wrap items-start justify-between gap-3 border-b border-stone-100 pb-4">
                        <div>
                            <p class="text-[11px] font-semibold text-stone-400 tabular-nums">
                                Dipesan {{ $order->created_at->format('d M Y, H:i') }} WIB • Kirim {{ $order->delivery_date->format('d M Y') }}
                            </p>
                            <h2 class="font-extrabold text-stone-900 text-base sm:text-lg tabular-nums mt-0.5">
                                {{ $order->order_number }}
                            </h2>
                        </div>

                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $badgeClass }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            {{ $statusText }}
                        </span>
                    </div>

                    {{-- Ringkasan Item --}}
                    <div class="flex flex-wrap gap-2">
                        @foreach ($order->items as $item)
                            <span class="text-xs font-medium px-2.5 py-1 rounded-lg bg-stone-50 border border-stone-200/70 text-stone-700">
                                {{ $item->product->name }} <strong class="text-emerald-900">×{{ $item->quantity }}</strong>
                            </span>
                        @endforeach
                    </div>

                    {{-- Footer Kartu: Total & Tombol Aksi --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-stone-100">
                        <div class="tabular-nums">
                            <span class="text-xs text-stone-500">Total Tagihan</span>
                            <p class="text-lg font-extrabold text-emerald-900">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($order->status === 'pending')
                                <a
                                    href="{{ route('orders.show', $order->order_number) }}"
                                    class="w-full sm:w-auto text-center bg-orange-600 hover:bg-orange-700 text-white text-xs sm:text-sm font-bold px-5 py-2.5 rounded-xl shadow-sm transition"
                                >
                                    Bayar Sekarang
                                </a>
                            @else
                                <a
                                    href="{{ route('orders.show', $order->order_number) }}"
                                    class="w-full sm:w-auto text-center bg-stone-100 hover:bg-emerald-900 hover:text-white text-stone-700 text-xs sm:text-sm font-bold px-4 py-2.5 rounded-xl transition"
                                >
                                    Lihat Struk →
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl border border-stone-200/80 p-10 text-center space-y-4">
                    <p class="text-sm font-semibold text-stone-700">
                        {{ $status ? 'Belum ada pesanan dengan status ini.' : 'Kamu belum pernah membuat pesanan PO.' }}
                    </p>
                    <a href="{{ route('catalog.index') }}" class="inline-flex items-center bg-orange-600 hover:bg-orange-700 text-white text-xs sm:text-sm font-bold px-5 py-2.5 rounded-xl shadow-sm transition">
                        Mulai Belanja Sayur Segar
                    </a>
                </div>
            @endforelse
        </section>

        @if ($orders->hasPages())
            <div class="pt-2">
                {{ $orders->links() }}
            </div>
        @endif
    </main>
</body>
</html>
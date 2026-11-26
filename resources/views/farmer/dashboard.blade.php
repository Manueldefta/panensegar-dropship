<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Panen Petani — PanenSegar</title>
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
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('catalog.index') }}" class="flex items-center gap-2.5">
                <span class="relative w-9 h-9 rounded-xl bg-emerald-900 text-white font-extrabold flex items-center justify-center text-sm shadow-sm">
                    PS
                    <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-orange-500 ring-2 ring-white"></span>
                </span>
                <div class="leading-tight">
                    <span class="block font-extrabold tracking-tight text-emerald-950 text-base">PanenSegar</span>
                    <span class="block text-[11px] font-medium text-stone-500">Panel Operasional Kebun</span>
                </div>
            </a>

            <div class="flex items-center gap-3 text-xs sm:text-sm">
                <a href="{{ route('catalog.index') }}" class="font-semibold text-stone-600 hover:text-emerald-900 transition">
                    ← Katalog
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg border border-stone-200 text-stone-600 hover:text-rose-600 hover:border-rose-200 font-semibold transition">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    @php
        $totalUnits = $harvestSummary->sum('total_qty');
        $pendingHarvestCount = $orders->where('status', 'paid')->count();
    @endphp

    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-8 space-y-6">

        {{-- Header Banner + Filter Tanggal --}}
        <section class="relative overflow-hidden bg-emerald-900 text-white rounded-3xl shadow-sm p-6 sm:p-8">
            <div class="pointer-events-none absolute -top-24 -right-24 w-64 h-64 rounded-full bg-orange-500/20 blur-3xl"></div>

            <div class="relative flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-orange-200 text-xs font-semibold">
                        <span>{{ auth()->user()->name }}</span>
                        <span class="opacity-50">•</span>
                        <span class="uppercase">{{ auth()->user()->role }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Daftar Panen Harian</h1>
                    <p class="text-xs sm:text-sm text-emerald-100/80">
                        Hanya menghitung pesanan berstatus lunas agar tidak ada hasil panen terbuang.
                    </p>
                </div>

                <form method="GET" action="{{ route('farmer.dashboard') }}" class="bg-white/10 backdrop-blur-sm border border-white/15 rounded-2xl p-3 flex flex-wrap items-end gap-2.5">
                    <div>
                        <label for="date" class="block text-[10px] font-bold uppercase tracking-wider text-emerald-100/80 mb-1">
                            Tanggal Pengiriman
                        </label>
                        <input
                            id="date"
                            type="date"
                            name="date"
                            value="{{ $selectedDate }}"
                            class="bg-white text-stone-900 rounded-xl px-3 py-2 text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-orange-400"
                        >
                    </div>
                    <button
                        type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white text-xs sm:text-sm font-bold px-4 py-2 rounded-xl shadow-sm transition"
                    >
                        Lihat Rekap
                    </button>
                </form>
            </div>
        </section>

        {{-- Flash Message --}}
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        {{-- 3 KPI Summary Cards --}}
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-4 tabular-nums">
            <div class="bg-white rounded-2xl border border-stone-200/80 p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-stone-400">Estimasi Setoran Petani</p>
                <p class="text-2xl font-extrabold text-emerald-900 mt-2">
                    Rp {{ number_format($totalPayout, 0, ',', '.') }}
                </p>
                <p class="text-xs text-stone-500 mt-1">Berdasarkan harga dasar panen</p>
            </div>

            <div class="bg-white rounded-2xl border border-stone-200/80 p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-stone-400">Total Komoditas Panen</p>
                <p class="text-2xl font-extrabold text-stone-900 mt-2">
                    {{ $totalUnits }} <span class="text-sm font-semibold text-stone-500">paket / ikat</span>
                </p>
                <p class="text-xs text-stone-500 mt-1">Dari {{ $harvestSummary->count() }} jenis sayuran</p>
            </div>

            <div class="bg-white rounded-2xl border border-stone-200/80 p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-stone-400">Status Pesanan ({{ $selectedDate }})</p>
                <p class="text-2xl font-extrabold {{ $pendingHarvestCount > 0 ? 'text-orange-600' : 'text-emerald-700' }} mt-2">
                    {{ $pendingHarvestCount }} <span class="text-sm font-semibold text-stone-500">perlu dipanen</span>
                </p>
                <p class="text-xs text-stone-500 mt-1">Total {{ $orders->count() }} pesanan lunas masuk</p>
            </div>
        </section>

        {{-- Tabel Ringkasan Total Panen --}}
        <section class="bg-white rounded-2xl border border-stone-200/80 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-stone-100 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="font-extrabold text-stone-900 text-base">Ringkasan Total Panen</h2>
                    <p class="text-xs text-stone-500">Akumulasi kuantitas per sayuran untuk pengiriman {{ $selectedDate }}</p>
                </div>
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                    Hanya Pesanan Lunas
                </span>
            </div>

            @if ($harvestSummary->isEmpty())
                <div class="p-8 text-center text-sm text-stone-500">
                    Belum ada pesanan lunas untuk tanggal kirim <strong>{{ $selectedDate }}</strong>.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left tabular-nums">
                        <thead class="bg-stone-50 text-[11px] font-bold text-stone-500 uppercase tracking-wider border-b border-stone-200/70">
                            <tr>
                                <th class="px-5 sm:px-6 py-3.5">Sayuran</th>
                                <th class="px-5 sm:px-6 py-3.5">Total Panen</th>
                                <th class="px-5 sm:px-6 py-3.5 text-right">Setoran Petani (Harga Dasar)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach ($harvestSummary as $row)
                                <tr class="hover:bg-stone-50/70 transition">
                                    <td class="px-5 sm:px-6 py-3.5 font-bold text-stone-900">{{ $row->product_name }}</td>
                                    <td class="px-5 sm:px-6 py-3.5">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-900 font-bold text-xs">
                                            {{ $row->total_qty }} × {{ $row->unit }}
                                        </span>
                                    </td>
                                    <td class="px-5 sm:px-6 py-3.5 text-right font-bold text-stone-800">
                                        Rp {{ number_format($row->farmer_payout, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-stone-50/80 border-t border-stone-200/80">
                            <tr>
                                <td colspan="2" class="px-5 sm:px-6 py-3.5 font-bold text-stone-700 text-xs uppercase tracking-wider">
                                    Total Estimasi Setoran
                                </td>
                                <td class="px-5 sm:px-6 py-3.5 text-right font-extrabold text-emerald-900 text-base">
                                    Rp {{ number_format($totalPayout, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </section>

        {{-- Daftar Rincian Pesanan --}}
        <section class="space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="font-extrabold text-stone-900 text-base">
                    Rincian Pesanan Siap Panen <span class="text-stone-400 font-semibold">({{ $orders->count() }})</span>
                </h2>
            </div>

            @forelse ($orders as $order)
                <div class="bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:border-stone-300 transition">
                    <div class="space-y-1.5 text-sm">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-extrabold text-stone-900 tabular-nums">{{ $order->order_number }}</span>
                            @if ($order->status === 'harvested')
                                <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full uppercase bg-sky-50 text-sky-800 border border-sky-200">
                                    Sudah Dipanen
                                </span>
                            @elseif ($order->status === 'delivered')
                                <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full uppercase bg-stone-100 text-stone-700 border border-stone-200">
                                    Terkirim
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full uppercase bg-orange-50 text-orange-700 border border-orange-200">
                                    Siap Panen
                                </span>
                            @endif
                        </div>

                        <p class="text-xs text-stone-600">
                            <strong class="text-stone-800">{{ $order->user->name }}</strong> — {{ $order->shipping_address }}
                        </p>

                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @foreach ($order->items as $item)
                                <span class="text-[11px] font-medium px-2 py-0.5 rounded-md bg-stone-100 text-stone-700">
                                    {{ $item->product->name }} <strong>×{{ $item->quantity }}</strong>
                                </span>
                            @endforeach
                        </div>
                    </div>

                    @if ($order->status === 'paid')
                        <form action="{{ route('farmer.orders.harvest', $order->order_number) }}" method="POST" class="shrink-0">
                            @csrf
                            @method('PATCH')
                            <button
                                type="submit"
                                class="w-full sm:w-auto bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition active:scale-[0.98]"
                            >
                                Tandai Sudah Dipanen
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="bg-white p-6 rounded-2xl border border-stone-200/80 text-center text-sm text-stone-500">
                    Tidak ada pesanan lunas pada tanggal ini.
                </div>
            @endforelse
        </section>
    </main>
</body>
</html>
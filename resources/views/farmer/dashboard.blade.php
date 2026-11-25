<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Panen Petani — PanenSegar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="max-w-3xl mx-auto p-4 sm:p-6 space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b pb-4">
            <div>
                <h1 class="text-2xl font-bold text-green-700">Daftar Panen Harian</h1>
                <p class="text-sm text-gray-500">
                    Masuk sebagai <strong>{{ auth()->user()->name }}</strong> ({{ strtoupper(auth()->user()->role) }})
                </p>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route('catalog.index') }}" class="text-green-700 hover:underline">← Katalog</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-red-600 hover:underline">Keluar</button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="p-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form method="GET" action="{{ route('farmer.dashboard') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Pengiriman</label>
                <input type="date" name="date" value="{{ $selectedDate }}"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                Lihat Rekap
            </button>
        </form>

        <section class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-900">Ringkasan Total Panen (Hanya Pesanan Lunas)</h2>
                <span class="text-sm font-semibold text-green-700">
                    Estimasi Setoran: Rp {{ number_format($totalPayout, 0, ',', '.') }}
                </span>
            </div>

            @if ($harvestSummary->isEmpty())
                <p class="text-sm text-gray-500">Belum ada pesanan lunas untuk tanggal kirim {{ $selectedDate }}.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase border-b">
                            <tr>
                                <th class="py-2">Sayuran</th>
                                <th class="py-2">Total Panen</th>
                                <th class="py-2 text-right">Setoran Petani (Harga Dasar)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($harvestSummary as $row)
                                <tr>
                                    <td class="py-2.5 font-medium text-gray-900">{{ $row->product_name }}</td>
                                    <td class="py-2.5 font-semibold text-green-700">{{ $row->total_qty }} × {{ $row->unit }}</td>
                                    <td class="py-2.5 text-right">Rp {{ number_format($row->farmer_payout, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="space-y-3">
            <h2 class="font-bold text-gray-900">Rincian Pesanan Siap Panen ({{ $orders->count() }})</h2>
            @forelse ($orders as $order)
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-wrap items-center justify-between gap-4">
                    <div class="space-y-1 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-900">{{ $order->order_number }}</span>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full uppercase {{ $order->status === 'harvested' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $order->status }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600">Pembeli: {{ $order->user->name }} — {{ $order->shipping_address }}</p>
                        <p class="text-xs text-gray-500">
                            @foreach ($order->items as $item)
                                {{ $item->product->name }} ({{ $item->quantity }}){{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </p>
                    </div>

                    @if ($order->status === 'paid')
                        <form action="{{ route('farmer.orders.harvest', $order->order_number) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3.5 py-2 rounded-lg transition">
                                Tandai Sudah Dipanen
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="bg-white p-4 rounded-xl border border-gray-100 text-sm text-gray-500">
                    Tidak ada pesanan lunas pada tanggal ini.
                </div>
            @endforelse
        </section>
    </div>
</body>
</html>
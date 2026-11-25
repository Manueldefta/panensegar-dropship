<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dropship Sayur Segar — PO Harian</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="max-w-xl mx-auto p-4 sm:p-6">
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-green-700">PanenSegar PO Harian</h1>
            <p class="text-sm text-gray-600 mt-1">
                Pengiriman terjadwal:
                <span class="font-semibold text-green-700">{{ $deliveryDate->translatedFormat('l, d M Y') }}</span>
                (Cut-off harian pukul 20.00 WIB)
            </p>
        </header>

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="space-y-3">
                @foreach ($products as $product)
                    @php
                        $remaining = max(0, $product->daily_quota - ($bookedMap[$product->id] ?? 0));
                    @endphp
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between gap-4">
                        <div>
                            <h2 class="font-semibold text-gray-900">{{ $product->name }}</h2>
                            <p class="text-sm text-green-700 font-medium">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }} / {{ $product->unit }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">Sisa kuota panen: {{ $remaining }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <input
                                type="number"
                                name="items[{{ $product->id }}]"
                                value="{{ old('items.' . $product->id, 0) }}"
                                min="0"
                                max="{{ $remaining }}"
                                {{ $remaining === 0 ? 'disabled' : '' }}
                                class="w-20 text-center border border-gray-300 rounded-lg py-1.5 px-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                            >
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">
                    Alamat Pengiriman
                </label>
                <textarea
                    id="shipping_address"
                    name="shipping_address"
                    rows="3"
                    required
                    placeholder="Contoh: Jl. Merdeka No. 10, RT 02/RW 03, patokan dekat masjid..."
                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                >{{ old('shipping_address') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Ongkir Rp 5.000 (Gratis ongkir untuk belanja minimal Rp 50.000).</p>
            </div>

            <button
                type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl shadow transition"
            >
                Buat Pesanan PO
            </button>
        </form>
    </div>
</body>
</html>
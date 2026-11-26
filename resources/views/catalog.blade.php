<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PanenSegar — Pre-Order Sayur Segar Harian</title>
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
<body class="bg-stone-50 text-stone-800 antialiased selection:bg-orange-100 selection:text-orange-900">

    {{-- Navbar Sticky --}}
    <nav class="sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-stone-200/80">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('catalog.index') }}" class="flex items-center gap-2.5 group">
                <span class="relative w-9 h-9 rounded-xl bg-emerald-900 text-white font-extrabold flex items-center justify-center text-sm shadow-sm group-hover:bg-emerald-800 transition">
                    PS
                    <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-orange-500 ring-2 ring-white"></span>
                </span>
                <div class="leading-tight">
                    <span class="block font-extrabold tracking-tight text-emerald-950 text-base">PanenSegar</span>
                    <span class="block text-[11px] font-medium text-stone-500">PO Sayur Harian</span>
                </div>
            </a>

            <div class="hidden md:flex items-center gap-7 text-sm font-medium text-stone-600">
                <a href="#cara-kerja" class="hover:text-emerald-800 transition">Cara Kerja</a>
                <a href="#katalog" class="hover:text-emerald-800 transition">Katalog Panen</a>
            </div>

            <div class="flex items-center gap-3 text-sm">
                @auth
                    <div class="hidden sm:flex items-center gap-2">
                        <span class="font-semibold text-stone-800">{{ auth()->user()->name }}</span>
                        <span class="px-2 py-0.5 text-[10px] font-bold tracking-wide rounded-full bg-emerald-50 text-emerald-900 border border-emerald-200 uppercase">
                            {{ auth()->user()->role }}
                        </span>
                    </div>
                    @if (auth()->user()->isFarmer() || auth()->user()->isAdmin())
                        <a href="{{ route('farmer.dashboard') }}" class="px-3 py-1.5 rounded-lg bg-emerald-900 text-white font-semibold text-xs hover:bg-emerald-800 transition">
                            Dashboard Petani
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-medium text-stone-500 hover:text-orange-600 transition">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-stone-700 hover:text-emerald-900 transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="bg-orange-600 hover:bg-orange-700 text-white font-semibold px-4 py-2 rounded-xl text-xs sm:text-sm shadow-sm transition">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-10 space-y-12">

        {{-- Hero Section (Deep Emerald + Harvest Orange Accent) --}}
        <section class="relative overflow-hidden bg-emerald-900 text-white rounded-3xl shadow-md p-6 sm:p-10">
            {{-- Dekorasi cahaya lembut di sudut --}}
            <div class="pointer-events-none absolute -top-24 -right-24 w-72 h-72 rounded-full bg-orange-500/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-24 w-72 h-72 rounded-full bg-emerald-500/20 blur-3xl"></div>

            <div class="relative grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7 space-y-5">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-orange-200 text-xs font-semibold backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-orange-400 animate-pulse"></span>
                        Pengiriman Berikutnya: {{ $deliveryDate->translatedFormat('l, d M Y') }}
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-[42px] font-extrabold tracking-tight text-white leading-[1.15]">
                        Sayur segar dipanen subuh, <span class="text-orange-300">langsung dari kebun</span> ke dapurmu.
                    </h1>

                    <p class="text-sm sm:text-base text-emerald-100/85 leading-relaxed max-w-xl">
                        Sistem Pre-Order harian tanpa stok menginap. Petani hanya memanen tepat sesuai pesananmu sehingga kualitas sayur selalu segar dan minim limbah.
                    </p>

                    <div class="flex flex-wrap items-center gap-3 pt-1">
                        <a href="#katalog" class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-bold px-5 py-3 rounded-xl text-sm shadow-sm transition">
                            Mulai Pilih Sayur
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                        <a href="#cara-kerja" class="inline-flex items-center font-semibold text-emerald-50 hover:text-white px-4 py-3 rounded-xl text-sm border border-white/20 hover:bg-white/10 transition">
                            Lihat Cara Kerja
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="bg-white text-stone-800 rounded-2xl shadow-lg p-5 sm:p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-stone-500">Info PO Hari Ini</span>
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 bg-emerald-50 border border-emerald-200/70 px-2.5 py-0.5 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                PO Buka
                            </span>
                        </div>
                        <dl class="space-y-3 text-sm tabular-nums">
                            <div class="flex items-center justify-between">
                                <dt class="text-stone-500">Batas Pesan (Cut-off)</dt>
                                <dd class="font-bold text-stone-900">20.00 WIB</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-stone-500">Jadwal Antar</dt>
                                <dd class="font-bold text-stone-900">{{ $deliveryDate->translatedFormat('d M Y') }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-stone-500">Ongkos Kirim</dt>
                                <dd class="font-bold text-stone-900">Rp 5.000</dd>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-stone-100">
                                <dt class="text-stone-700 font-semibold">Promo Gratis Ongkir</dt>
                                <dd class="font-extrabold text-orange-600 bg-orange-50 px-2.5 py-0.5 rounded-lg">Min. Rp 50.000</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </section>

        {{-- Strip Cara Kerja --}}
        <section id="cara-kerja" class="scroll-mt-24">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl border border-stone-200/80 p-5 flex items-start gap-4 hover:border-orange-200 transition">
                    <span class="w-9 h-9 rounded-xl bg-orange-50 border border-orange-200/80 text-orange-700 font-extrabold text-sm flex items-center justify-center shrink-0">01</span>
                    <div>
                        <h2 class="font-bold text-stone-900 text-sm">Pesan Sebelum 20.00 WIB</h2>
                        <p class="text-xs text-stone-500 mt-1 leading-relaxed">Pilih sayur sesuai kuota panen harian dan selesaikan pembayaran via QRIS atau transfer.</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-stone-200/80 p-5 flex items-start gap-4 hover:border-orange-200 transition">
                    <span class="w-9 h-9 rounded-xl bg-orange-50 border border-orange-200/80 text-orange-700 font-extrabold text-sm flex items-center justify-center shrink-0">02</span>
                    <div>
                        <h2 class="font-bold text-stone-900 text-sm">Petani Panen Subuh</h2>
                        <p class="text-xs text-stone-500 mt-1 leading-relaxed">Rekap pesanan lunas otomatis dikirim ke petani untuk dipanen segar esok subuh.</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-stone-200/80 p-5 flex items-start gap-4 hover:border-orange-200 transition">
                    <span class="w-9 h-9 rounded-xl bg-orange-50 border border-orange-200/80 text-orange-700 font-extrabold text-sm flex items-center justify-center shrink-0">03</span>
                    <div>
                        <h2 class="font-bold text-stone-900 text-sm">Diantar Langsung ke Rumah</h2>
                        <p class="text-xs text-stone-500 mt-1 leading-relaxed">Pesanan dikemas rapi dan diantar ke alamatmu pada {{ $deliveryDate->translatedFormat('l, d M Y') }}.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Katalog & Panel Checkout --}}
        <section id="katalog" class="scroll-mt-24 space-y-5">
            <div class="flex flex-wrap items-end justify-between gap-2">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600">Panen Terbatas</span>
                    <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-emerald-950">Katalog Panen Hari Ini</h2>
                    <p class="text-sm text-stone-500 mt-0.5">Atur jumlah pesananmu—ringkasan harga akan terhitung otomatis di samping.</p>
                </div>
                <span class="text-xs font-semibold text-emerald-900 bg-emerald-50 border border-emerald-200/70 px-3 py-1 rounded-full">
                    {{ $products->count() }} produk tersedia
                </span>
            </div>

            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    <p class="font-semibold mb-1">Periksa kembali pesananmu:</p>
                    <ul class="list-disc pl-5 space-y-0.5 text-xs sm:text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('checkout.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                @csrf

                {{-- Grid Produk --}}
                <div class="lg:col-span-7 xl:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse ($products as $product)
                        @php
                            $remaining = max(0, $product->daily_quota - ($bookedMap[$product->id] ?? 0));
                            $initialQty = (int) old('items.' . $product->id, 0);
                        @endphp
                        <div
                            data-product-card
                            class="bg-white rounded-2xl border border-stone-200/80 p-5 flex flex-col justify-between gap-5 transition-all hover:border-stone-300 hover:shadow-sm {{ $remaining === 0 ? 'opacity-65' : '' }}"
                        >
                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 rounded-full {{ $remaining === 0 ? 'bg-stone-100 text-stone-500' : ($remaining <= 5 ? 'bg-orange-50 text-orange-700 border border-orange-200/80' : 'bg-emerald-50 text-emerald-800 border border-emerald-200/60') }}">
                                        @if ($remaining === 0)
                                            Kuota Habis
                                        @elseif ($remaining <= 5)
                                            Sisa sedikit: {{ $remaining }}
                                        @else
                                            Sisa kuota: {{ $remaining }}
                                        @endif
                                    </span>
                                    <span class="text-xs font-medium text-stone-400">per {{ $product->unit }}</span>
                                </div>

                                <div>
                                    <h3 class="font-bold text-stone-900 text-base leading-snug">{{ $product->name }}</h3>
                                    <p class="mt-1 text-lg font-extrabold text-emerald-800 tabular-nums">
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                        <span class="text-xs font-medium text-stone-500">/ {{ $product->unit }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t border-stone-100">
                                <span class="text-xs font-medium text-stone-500">Jumlah</span>
                                <div class="inline-flex items-center rounded-xl border border-stone-200 bg-stone-50 p-1">
                                    <button
                                        type="button"
                                        data-stepper="minus"
                                        {{ $remaining === 0 ? 'disabled' : '' }}
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-stone-700 font-bold hover:bg-white hover:shadow-sm disabled:opacity-40 disabled:cursor-not-allowed transition"
                                        aria-label="Kurangi {{ $product->name }}"
                                    >
                                        −
                                    </button>
                                    <input
                                        type="number"
                                        name="items[{{ $product->id }}]"
                                        value="{{ $initialQty }}"
                                        min="0"
                                        max="{{ $remaining }}"
                                        data-qty-input
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->selling_price }}"
                                        data-unit="{{ $product->unit }}"
                                        {{ $remaining === 0 ? 'disabled' : '' }}
                                        class="w-12 text-center bg-transparent text-sm font-bold text-stone-900 tabular-nums focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                    >
                                    <button
                                        type="button"
                                        data-stepper="plus"
                                        {{ $remaining === 0 ? 'disabled' : '' }}
                                        class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-800 text-white font-bold hover:bg-emerald-900 disabled:opacity-40 disabled:cursor-not-allowed transition"
                                        aria-label="Tambah {{ $product->name }}"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="sm:col-span-2 bg-white rounded-2xl border border-stone-200/80 p-8 text-center text-sm text-stone-500">
                            Belum ada produk sayur yang tersedia untuk jadwal PO hari ini.
                        </div>
                    @endforelse
                </div>

                {{-- Panel Checkout Sticky --}}
                <aside class="lg:col-span-5 xl:col-span-4 lg:sticky lg:top-24 bg-white rounded-2xl border border-stone-200/80 shadow-sm overflow-hidden">
                    <div class="bg-emerald-900 px-5 sm:px-6 py-4 flex items-center justify-between">
                        <h3 class="font-extrabold text-white text-base">Ringkasan Pesanan</h3>
                        <span id="summary-count" class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-orange-500 text-white tabular-nums">
                            0 item
                        </span>
                    </div>

                    <div class="p-5 sm:p-6 space-y-5">
                        {{-- Daftar Item Terpilih --}}
                        <div id="summary-items" class="space-y-2 text-sm">
                            <p class="text-xs text-stone-400 italic">Belum ada sayur yang dipilih.</p>
                        </div>

                        {{-- Indikator Gratis Ongkir --}}
                        <div id="free-shipping-box" class="rounded-xl bg-orange-50/70 border border-orange-200/70 p-3 space-y-2 transition-colors">
                            <div class="flex items-center justify-between text-xs">
                                <span id="free-shipping-text" class="font-semibold text-orange-900">
                                    Belanja min. Rp 50.000 untuk gratis ongkir
                                </span>
                            </div>
                            <div class="w-full h-2 bg-orange-200/70 rounded-full overflow-hidden">
                                <div id="free-shipping-bar" class="h-full bg-orange-500 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                        </div>

                        {{-- Rincian Harga --}}
                        <div class="space-y-2 pt-1 text-sm tabular-nums border-t border-stone-100">
                            <div class="flex justify-between text-stone-600 pt-2">
                                <span>Subtotal</span>
                                <span id="summary-subtotal" class="font-semibold text-stone-900">Rp 0</span>
                            </div>
                            <div class="flex justify-between text-stone-600">
                                <span>Ongkos Kirim</span>
                                <span id="summary-shipping" class="font-semibold text-stone-900">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-baseline pt-2 border-t border-stone-100">
                                <span class="font-bold text-stone-900">Estimasi Total</span>
                                <span id="summary-total" class="text-xl font-extrabold text-emerald-900">Rp 0</span>
                            </div>
                        </div>

                        {{-- Alamat Pengiriman --}}
                        <div class="pt-1">
                            <label for="shipping_address" class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1.5">
                                Alamat Pengiriman
                            </label>
                            <textarea
                                id="shipping_address"
                                name="shipping_address"
                                rows="3"
                                required
                                placeholder="Contoh: Jl. Merdeka No. 10, RT 02/RW 03, patokan dekat masjid..."
                                class="w-full border border-stone-300 rounded-xl p-3 text-sm text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-orange-500/25 focus:border-orange-500 transition"
                            >{{ old('shipping_address', auth()->user()?->address) }}</textarea>
                        </div>

                        <div class="space-y-2">
                            <button
                                type="submit"
                                class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3.5 rounded-xl shadow-sm transition active:scale-[0.99]"
                            >
                                Buat Pesanan PO
                            </button>
                            @guest
                                <p class="text-[11px] text-center text-stone-500">
                                    Belum masuk? Kamu akan diarahkan ke halaman login terlebih dahulu.
                                </p>
                            @endguest
                        </div>
                    </div>
                </aside>
            </form>
        </section>
    </main>

    <footer class="mt-16 border-t border-stone-200/80 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-stone-500">
            <p class="font-semibold text-emerald-950">PanenSegar — Dropship Sayur Segar Harian</p>
            <p>Cut-off PO setiap pukul 20.00 WIB • Pengiriman segar keesokan harinya</p>
        </div>
    </footer>

    <script>
        (function () {
            const FREE_SHIPPING_MIN = 50000;
            const SHIPPING_FEE = 5000;
            const rupiah = (n) => 'Rp ' + new Intl.NumberFormat('id-ID').format(n);

            const inputs = Array.from(document.querySelectorAll('[data-qty-input]'));
            const summaryItems = document.getElementById('summary-items');
            const summaryCount = document.getElementById('summary-count');
            const summarySubtotal = document.getElementById('summary-subtotal');
            const summaryShipping = document.getElementById('summary-shipping');
            const summaryTotal = document.getElementById('summary-total');
            const freeText = document.getElementById('free-shipping-text');
            const freeBar = document.getElementById('free-shipping-bar');
            const freeBox = document.getElementById('free-shipping-box');

            function updateSummary() {
                let subtotal = 0;
                let totalQty = 0;
                const rows = [];

                inputs.forEach((input) => {
                    const max = parseInt(input.max || '0', 10);
                    let qty = parseInt(input.value || '0', 10);
                    if (isNaN(qty) || qty < 0) qty = 0;
                    if (qty > max) qty = max;
                    input.value = qty;

                    const card = input.closest('[data-product-card]');
                    if (card) {
                        card.classList.toggle('border-emerald-700', qty > 0);
                        card.classList.toggle('ring-2', qty > 0);
                        card.classList.toggle('ring-emerald-700/15', qty > 0);
                    }

                    if (qty > 0) {
                        const price = parseInt(input.dataset.price, 10);
                        const lineTotal = qty * price;
                        subtotal += lineTotal;
                        totalQty += qty;
                        rows.push(`
                            <div class="flex items-center justify-between gap-2 text-xs">
                                <span class="text-stone-700 truncate">${input.dataset.name} <strong class="text-emerald-900">×${qty}</strong></span>
                                <span class="font-semibold text-stone-800 tabular-nums shrink-0">${rupiah(lineTotal)}</span>
                            </div>
                        `);
                    }
                });

                const shipping = subtotal === 0 ? 0 : (subtotal >= FREE_SHIPPING_MIN ? 0 : SHIPPING_FEE);
                const total = subtotal + shipping;

                summaryCount.textContent = `${totalQty} item`;
                summaryItems.innerHTML = rows.length
                    ? rows.join('')
                    : '<p class="text-xs text-stone-400 italic">Belum ada sayur yang dipilih.</p>';

                summarySubtotal.textContent = rupiah(subtotal);
                if (subtotal > 0 && shipping === 0) {
                    summaryShipping.textContent = 'Gratis';
                    summaryShipping.className = 'font-bold text-emerald-700';
                } else {
                    summaryShipping.textContent = rupiah(shipping);
                    summaryShipping.className = 'font-semibold text-stone-900';
                }
                summaryTotal.textContent = rupiah(total);

                const pct = Math.min(100, Math.round((subtotal / FREE_SHIPPING_MIN) * 100));
                freeBar.style.width = `${pct}%`;

                if (subtotal >= FREE_SHIPPING_MIN) {
                    freeText.textContent = 'Yeay! Pesananmu dapat Gratis Ongkir.';
                    freeText.className = 'font-semibold text-emerald-800';
                    freeBox.className = 'rounded-xl bg-emerald-50 border border-emerald-200/80 p-3 space-y-2 transition-colors';
                    freeBar.className = 'h-full bg-emerald-600 rounded-full transition-all duration-300';
                } else if (subtotal > 0) {
                    freeText.textContent = `Tambah ${rupiah(FREE_SHIPPING_MIN - subtotal)} lagi untuk Gratis Ongkir`;
                    freeText.className = 'font-semibold text-orange-900';
                    freeBox.className = 'rounded-xl bg-orange-50/70 border border-orange-200/70 p-3 space-y-2 transition-colors';
                    freeBar.className = 'h-full bg-orange-500 rounded-full transition-all duration-300';
                } else {
                    freeText.textContent = 'Belanja min. Rp 50.000 untuk gratis ongkir';
                    freeText.className = 'font-semibold text-orange-900';
                    freeBox.className = 'rounded-xl bg-orange-50/70 border border-orange-200/70 p-3 space-y-2 transition-colors';
                    freeBar.className = 'h-full bg-orange-500 rounded-full transition-all duration-300';
                }
            }

            document.querySelectorAll('[data-stepper]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const input = btn.parentElement.querySelector('[data-qty-input]');
                    if (!input || input.disabled) return;
                    const current = parseInt(input.value || '0', 10);
                    const max = parseInt(input.max || '0', 10);
                    const next = btn.dataset.stepper === 'plus'
                        ? Math.min(max, current + 1)
                        : Math.max(0, current - 1);
                    input.value = next;
                    updateSummary();
                });
            });

            inputs.forEach((input) => input.addEventListener('input', updateSummary));
            updateSummary();
        })();
    </script>
</body>
</html>
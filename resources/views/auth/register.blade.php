<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — PanenSegar</title>
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
<body class="bg-stone-50 text-stone-800 antialiased min-h-screen flex items-center justify-center p-4 sm:p-6 selection:bg-orange-100 selection:text-orange-900">
    <div class="w-full max-w-4xl bg-white rounded-3xl border border-stone-200/80 shadow-sm overflow-hidden grid grid-cols-1 md:grid-cols-12">

        {{-- Panel Kiri: Brand Deep Emerald --}}
        <div class="relative overflow-hidden bg-emerald-900 text-white p-7 sm:p-9 md:col-span-5 flex flex-col justify-between gap-8">
            <div class="pointer-events-none absolute -top-20 -right-20 w-56 h-56 rounded-full bg-orange-500/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-20 -left-20 w-56 h-56 rounded-full bg-emerald-500/20 blur-3xl"></div>

            <div class="relative space-y-6">
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2.5">
                    <span class="relative w-9 h-9 rounded-xl bg-white/10 border border-white/15 text-white font-extrabold flex items-center justify-center text-sm">
                        PS
                        <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-orange-500 ring-2 ring-emerald-900"></span>
                    </span>
                    <div class="leading-tight">
                        <span class="block font-extrabold tracking-tight text-white text-base">PanenSegar</span>
                        <span class="block text-[11px] font-medium text-emerald-200/80">PO Sayur Harian</span>
                    </div>
                </a>

                <div class="space-y-2.5">
                    <h1 class="text-2xl font-extrabold tracking-tight leading-snug">
                        Mulai belanja sayur <span class="text-orange-300">langsung dari petani</span>.
                    </h1>
                    <p class="text-xs sm:text-sm text-emerald-100/80 leading-relaxed">
                        Simpan alamat pengirimanmu sekali saja agar checkout harian lebih cepat dan praktis.
                    </p>
                </div>

                <ul class="space-y-2.5 text-xs text-emerald-100/90 pt-2">
                    <li class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-orange-500/20 text-orange-300 flex items-center justify-center font-bold text-[10px]">✓</span>
                        Tanpa stok menginap, panen sesuai pesanan
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-orange-500/20 text-orange-300 flex items-center justify-center font-bold text-[10px]">✓</span>
                        Gratis ongkir belanja minimal Rp 50.000
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-orange-500/20 text-orange-300 flex items-center justify-center font-bold text-[10px]">✓</span>
                        Pembayaran cepat & aman via QRIS / VA
                    </li>
                </ul>
            </div>

            <div class="relative pt-4 border-t border-white/15 text-xs text-emerald-100/70">
                Akun baru otomatis terdaftar sebagai <strong class="text-white">Pembeli</strong>.
            </div>
        </div>

        {{-- Panel Kanan: Form Register --}}
        <div class="p-7 sm:p-9 md:col-span-7">
            <div class="mb-5">
                <span class="text-xs font-bold uppercase tracking-wider text-orange-600">Akun Baru</span>
                <h2 class="text-2xl font-extrabold tracking-tight text-emerald-950 mt-0.5">Daftar Akun Pembeli</h2>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-3.5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">
                            Nama Lengkap
                        </label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            placeholder="Nama lengkapmu"
                            class="w-full border border-stone-300 rounded-xl px-3.5 py-2.5 text-sm text-stone-900 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-orange-500/25 focus:border-orange-500 transition"
                        >
                    </div>
                    <div>
                        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">
                            Nomor WhatsApp
                        </label>
                        <input
                            id="phone"
                            type="text"
                            name="phone"
                            value="{{ old('phone') }}"
                            required
                            placeholder="0812..."
                            class="w-full border border-stone-300 rounded-xl px-3.5 py-2.5 text-sm text-stone-900 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-orange-500/25 focus:border-orange-500 transition"
                        >
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">
                        Email
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="nama@email.com"
                        class="w-full border border-stone-300 rounded-xl px-3.5 py-2.5 text-sm text-stone-900 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-orange-500/25 focus:border-orange-500 transition"
                    >
                </div>

                <div>
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">
                        Alamat Pengiriman Default
                    </label>
                    <textarea
                        id="address"
                        name="address"
                        rows="2"
                        required
                        placeholder="Jl. ..., RT/RW, patokan rumah..."
                        class="w-full border border-stone-300 rounded-xl px-3.5 py-2.5 text-sm text-stone-900 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-orange-500/25 focus:border-orange-500 transition"
                    >{{ old('address') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">
                            Password (min. 8)
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            placeholder="Min. 8 karakter"
                            class="w-full border border-stone-300 rounded-xl px-3.5 py-2.5 text-sm text-stone-900 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-orange-500/25 focus:border-orange-500 transition"
                        >
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">
                            Konfirmasi Password
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            placeholder="Ulangi password"
                            class="w-full border border-stone-300 rounded-xl px-3.5 py-2.5 text-sm text-stone-900 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-orange-500/25 focus:border-orange-500 transition"
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-xl shadow-sm transition active:scale-[0.99] mt-1"
                >
                    Daftar Sekarang
                </button>
            </form>

            <div class="mt-5 pt-4 border-t border-stone-100 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs sm:text-sm">
                <p class="text-stone-600">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-bold text-emerald-800 hover:text-orange-600 transition">Masuk</a>
                </p>
                <a href="{{ route('catalog.index') }}" class="text-stone-500 hover:text-emerald-900 font-medium transition">
                    ← Kembali ke Katalog
                </a>
            </div>
        </div>
    </div>
</body>
</html>
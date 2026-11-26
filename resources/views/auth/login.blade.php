<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — PanenSegar</title>
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
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2.5 group">
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
                        Selamat datang kembali di <span class="text-orange-300">PanenSegar</span>.
                    </h1>
                    <p class="text-xs sm:text-sm text-emerald-100/80 leading-relaxed">
                        Masuk untuk melanjutkan pesanan PO harianmu atau memantau daftar panen kebun.
                    </p>
                </div>
            </div>

            <div class="relative pt-4 border-t border-white/15 text-xs text-emerald-100/75 space-y-1">
                <p class="font-semibold text-orange-200">Cut-off harian pukul 20.00 WIB</p>
                <p>Dipanen subuh, diantar segar esok pagi.</p>
            </div>
        </div>

        {{-- Panel Kanan: Form Login --}}
        <div class="p-7 sm:p-9 md:col-span-7 flex flex-col justify-center">
            <div class="mb-6">
                <span class="text-xs font-bold uppercase tracking-wider text-orange-600">Akses Akun</span>
                <h2 class="text-2xl font-extrabold tracking-tight text-emerald-950 mt-0.5">Masuk ke Akunmu</h2>
                <p class="text-xs sm:text-sm text-stone-500 mt-1">Gunakan akun pembeli, petani, atau admin yang terdaftar.</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1.5">
                        Email
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="nama@email.com"
                        class="w-full border border-stone-300 rounded-xl px-3.5 py-2.5 text-sm text-stone-900 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-orange-500/25 focus:border-orange-500 transition"
                    >
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1.5">
                        Password
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        placeholder="Masukkan password"
                        class="w-full border border-stone-300 rounded-xl px-3.5 py-2.5 text-sm text-stone-900 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-orange-500/25 focus:border-orange-500 transition"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-xl shadow-sm transition active:scale-[0.99] mt-2"
                >
                    Masuk Sekarang
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-stone-100 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs sm:text-sm">
                <p class="text-stone-600">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-bold text-emerald-800 hover:text-orange-600 transition">Daftar di sini</a>
                </p>
                <a href="{{ route('catalog.index') }}" class="text-stone-500 hover:text-emerald-900 font-medium transition">
                    ← Kembali ke Katalog
                </a>
            </div>
        </div>
    </div>
</body>
</html>
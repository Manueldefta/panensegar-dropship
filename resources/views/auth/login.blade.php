<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — PanenSegar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h1 class="text-xl font-bold text-green-700 mb-1">Masuk ke PanenSegar</h1>
        <p class="text-xs text-gray-500 mb-5">Gunakan akun pembeli, petani, atau admin.</p>

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-xl transition">
                Masuk
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-4">
            Belum punya akun? <a href="{{ route('register') }}" class="text-green-700 font-medium hover:underline">Daftar di sini</a>
        </p>
        <p class="text-center text-xs mt-2">
            <a href="{{ route('catalog.index') }}" class="text-gray-500 hover:underline">← Kembali ke Katalog</a>
        </p>
    </div>
</body>
</html>
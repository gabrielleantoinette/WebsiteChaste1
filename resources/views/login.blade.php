<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | CHASTE</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-white font-sans">
    <!-- Navbar minimal -->
    @include('layouts.customer-nav')

    <!-- Konten login -->
    <main class="flex items-center justify-center min-h-screen px-4 md:px-0 bg-white">
        <div
            class="flex flex-col md:flex-row overflow-hidden rounded-[20px] shadow-lg border border-gray-200 max-w-5xl w-full h-[500px]">
            <!-- Gambar -->
            <div class="hidden md:block w-[400px] h-full">
                <img src="{{ asset('images/terpal-login.png') }}" alt="Terpal Gulungan"
                    class="object-cover w-full h-full">
            </div>

            <!-- Form -->
            <div class="w-full md:w-[700px] bg-white p-8 flex flex-col justify-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-3 text-center">MASUK</h2>
                @if (session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('login') }}" class="mt-3">
                    @csrf

                    <label class="block mb-2 text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" required
                        class="w-full mb-4 px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-teal-300 outline-none">

                    <label class="block mb-2 text-sm font-medium text-gray-700">Kata Sandi</label>
                    <input type="password" name="password" required
                        class="w-full mb-6 px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-teal-300 outline-none">

                    <button type="submit"
                        class="w-full bg-[#D9F2F2] hover:bg-teal-200 text-gray-800 font-semibold py-3 rounded-md transition">
                        Masuk
                    </button>
                </form>

                <div class="text-center text-sm mt-4">
                    Belum punya akun?
                    <a href="{{ url('/register') }}" class="text-red-500 font-medium">Daftar sekarang</a>
                </div>
            </div>
        </div>
    </main>

@include('layouts.footer')

</body>

</html>

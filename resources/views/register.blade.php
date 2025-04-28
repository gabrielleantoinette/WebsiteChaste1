<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | CHASTE</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-white font-sans">
    @include('layouts.customer-nav')

    <!-- Form Daftar -->
    <main class="flex items-center justify-center min-h-screen px-4 md:px-0 bg-white">
        <div
            class="flex flex-col md:flex-row overflow-hidden rounded-[20px] shadow-lg border border-gray-200 max-w-5xl w-full h-[540px]">
            <!-- Gambar -->
            <div class="hidden md:block w-[400px] h-full">
                <img src="{{ asset('images/terpal-login.png') }}" alt="Terpal Gulungan"
                    class="object-cover w-full h-full">
            </div>

            <!-- Form -->
            <div class="w-full md:w-[700px] bg-white p-8 flex flex-col justify-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">DAFTAR</h2>
                <form method="POST" action="{{ url('/register') }}">
                    @csrf

                    <label class="block mb-2 text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" required
                        class="w-full mb-4 px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-teal-300 outline-none">

                    <label class="block mb-2 text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="name" required
                        class="w-full mb-4 px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-teal-300 outline-none">

                    <label class="block mb-2 text-sm font-medium text-gray-700">Nomor Telepon</label>
                    <input type="tel" name="phone" placeholder="+62" required
                        class="w-full mb-4 px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-teal-300 outline-none">

                    <label class="block mb-2 text-sm font-medium text-gray-700">Kata Sandi</label>
                    <input type="password" name="password" required
                        class="w-full mb-6 px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-teal-300 outline-none">

                    <button type="submit"
                        class="w-full bg-[#D9F2F2] hover:bg-teal-200 text-gray-800 font-semibold py-3 rounded-md transition">
                        Daftar
                    </button>
                </form>

                <div class="text-center text-sm mt-4">
                    Sudah punya akun?
                    <a href="{{ url('/login') }}" class="text-red-500 font-medium">Masuk sekarang</a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    @include('layouts.footer')

</body>

</html>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Pembeli | CHASTE</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-white font-sans text-gray-800">
    {{-- Header --}}
    @include('layouts.customer-nav')
    <div class="px-[100px] h-screen">
        <div class="flex items-center justify-between mt-10">
            <h1 class="text-2xl font-bold">Selamat datang, {{ $customer->name }}</h1>
            <a href="{{ route('logout') }}" class="btn btn-error">Keluar</a>
        </div>
        <form class="mt-10 space-y-4">
            <div>
                <label for="name">Nama</label>
                <input type="text" name="name" id="name" class="w-full input input-primary"
                    value="{{ $customer->name }}">
            </div>
            <div>
                <label for="name">Email</label>
                <input type="text" name="email" id="name" class="w-full input input-rimary"
                    value="{{ $customer->email }}">
            </div>
            <div>
                <label for="name">No. Telepon</label>
                <input type="text" name="phone" id="name" class="w-full input input-rimary"
                    value="{{ $customer->phone }}">
            </div>
            <div>
                <label for="name">Kata Sandi</label>
                <input type="text" name="password" id="name" class="w-full input inpurimary"
                    value="{{ $customer->password }}">
            </div>
            <button type="submit" class="bg-teal-500 text-white px-4 py-2 rounded-md mt-5">Simpan</button>
        </form>
    </div>
    <!-- Footer -->
    <footer class="bg-[#D9F2F2] py-10 px-[100px] text-sm text-gray-700 mt-20">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Brand -->
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">CHASTE</h3>
                <p class="mb-4 max-w-xs">Kami membantu anda menyediakan terpal terbaik.</p>
                <div class="flex space-x-4 text-lg">
                    <a href="#">📷</a>
                    <a href="#">🐦</a>
                    <a href="#">📘</a>
                </div>
            </div>
            <!-- Informasi -->
            <div class="space-y-2">
                <h4 class="font-semibold text-gray-800 mb-2">Informasi</h4>
                <a href="#" class="block hover:text-black">Tentang</a>
                <a href="#" class="block hover:text-black">Produk</a>
            </div>
            <!-- Kontak -->
            <div class="space-y-2">
                <h4 class="font-semibold text-gray-800 mb-2">Kontak Kami</h4>
                <p>Telp: 089123231221</p>
                <p>E-mail: xyz@bca</p>
            </div>
        </div>
        <div class="text-center text-xs text-gray-500 mt-8">
            © 2025 Hak Cipta Dilindungi
        </div>
    </footer>
</body>

</html>

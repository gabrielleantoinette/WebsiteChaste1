<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk | CHASTE</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-white font-sans text-gray-800">
<!-- Header -->
<header class="flex items-center justify-between px-6 md:px-20 py-5 border-b border-gray-200">
    <div class="text-2xl font-bold tracking-wide">CHASTE</div>
    <nav class="hidden md:flex space-x-8 text-sm font-medium text-gray-600">
        <a href="{{ url('/') }}" class="hover:text-teal-500">Beranda</a>
        <a href="{{ url('/produk') }}" class="hover:text-teal-500">Produk</a>
        <a href="#" class="hover:text-teal-500">Kontak</a>
    </nav>
    <div class="space-x-4 text-xl text-gray-700">
        <a href="#">🛒</a>
        <a href="#">👤</a>
        <a href="#">☰</a>
    </div>
</header>

<!-- Detail Produk -->
<section class="px-6 md:px-20 py-16 bg-white">
    <div class="flex flex-col md:flex-row gap-10">
        <!-- Gambar Produk -->
        <div class="w-full md:w-1/2">
            <div class="relative rounded-xl border border-gray-200 overflow-hidden p-4">
                <img src="{{ asset('images/terpal-ayam.png') }}" alt="Terpal A5"
                     class="w-full h-[400px] object-cover rounded-lg">

                <!-- Tombol panah kiri-kanan dummy -->
                <button class="absolute left-4 top-1/2 -translate-y-1/2 bg-white p-2 rounded-full shadow text-xl">❮</button>
                <button class="absolute right-4 top-1/2 -translate-y-1/2 bg-white p-2 rounded-full shadow text-xl">❯</button>
            </div>

            <!-- Thumbnail kecil -->
            <div class="flex gap-4 mt-4">
                <img src="{{ asset('images/terpal-ayam.png') }}" class="w-16 h-16 object-cover rounded shadow-sm border">
                <img src="{{ asset('images/terpal-gajah.png') }}" class="w-16 h-16 object-cover rounded shadow-sm border">
                <img src="{{ asset('images/terpal-lumba.png') }}" class="w-16 h-16 object-cover rounded shadow-sm border">
            </div>
        </div>

        <!-- Info Produk -->
        <div class="w-full md:w-1/2 space-y-6">
            <div>
                <h2 class="text-2xl font-bold">Terpal A5 2x3</h2>
                <p class="text-xl text-teal-600 font-semibold mt-1">Rp 4.500,00</p>
                <p class="text-sm text-gray-600 mt-2">Terpal plastik kualitas bagus yang cocok untuk kebutuhan bertani</p>
            </div>

            <!-- Pilih Warna -->
            <div>
                <label class="block text-sm font-medium mb-1">Pilihan Warna</label>
                <select class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-teal-300">
                    <option>Pilih...</option>
                    <option>Biru</option>
                    <option>Oranye</option>
                </select>
            </div>

            <!-- Jumlah -->
            <div>
                <label class="block text-sm font-medium mb-1">Jumlah</label>
                <div class="flex items-center border border-gray-300 w-max rounded-md overflow-hidden">
                    <button type="button" class="px-3 py-2 text-lg hover:bg-gray-100">-</button>
                    <input type="number" value="1" min="1"
                           class="w-12 text-center border-l border-r border-gray-300 outline-none text-sm py-2">
                    <button type="button" class="px-3 py-2 text-lg hover:bg-gray-100">+</button>
                </div>
            </div>

            <!-- Tombol -->
            <button class="w-full bg-[#D9F2F2] hover:bg-teal-200 text-gray-800 font-semibold py-3 rounded-md transition">
                Tambah ke Keranjang
            </button>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-[#D9F2F2] py-10 px-6 md:px-20 text-sm text-gray-700 mt-20">
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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Pembeli | CHASTE</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-white font-sans text-gray-800">

<!-- Header -->
<header class="flex items-center justify-between px-6 md:px-20 py-5 border-b border-gray-200">
    <div class="text-2xl font-bold tracking-wide">CHASTE</div>
    <nav class="hidden md:flex space-x-8 text-sm font-medium text-gray-600">
        <a href="{{ url('/') }}" class="hover:text-teal-500">Beranda</a>
        <a href="{{ url('/produk') }}" class="text-black font-semibold">Produk</a>
        <a href="#" class="hover:text-teal-500">Kontak</a>
    </nav>
    <div class="space-x-4 text-xl text-gray-700">
        <a href="#">🛒</a>
        <a href="{{ url('/login') }}">👤</a>
        <a href="#">☰</a>
    </div>
</header>

<!-- Hero -->
<section class="bg-[#D9F2F2] py-16 px-6 md:px-20 rounded-[24px] mt-[24px]">
    <div class="flex flex-col md:flex-row items-center justify-between gap-12">
        <div class="max-w-xl space-y-8">
            <h1 class="text-5xl font-extrabold text-gray-900 leading-snug tracking-tight">
                Butuh terpal tapi ukuran tidak sesuai keinginan?
            </h1>
            <a href="#"
               class="inline-block bg-white text-gray-800 font-semibold px-6 py-3 rounded-md border border-gray-300 hover:bg-gray-100 transition">
                Custom Terpalmu Disini!
            </a>
        </div>
        <div class="w-100 h-100 rounded-full overflow-hidden border-4 border-white shadow-md">
            <img src="{{ asset('images/gulungan-terpal.png') }}" alt="Custom Terpal" class="w-full h-full object-cover">
        </div>
    </div>
</section>

<!-- Produk Section -->
<section class="px-6 md:px-20 py-16 bg-white">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Filter -->
        <aside class="w-full md:w-1/4 space-y-8">
            <div>
                <h3 class="text-lg font-semibold mb-2">Kategori</h3>
                <div class="space-y-2 text-sm">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="form-checkbox text-teal-500"> Plastik
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="form-checkbox text-teal-500"> Kain
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="form-checkbox text-teal-500"> Karet
                    </label>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-2">Ukuran</h3>
                <div class="space-y-2 text-sm">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="form-checkbox text-teal-500"> 2x3
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="form-checkbox text-teal-500"> 5x7
                    </label>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-2">Harga</h3>
                <div class="flex items-center gap-2 text-sm">
                    <input type="number" name="harga_min" placeholder="Min"
                        class="w-full border border-gray-300 rounded-md p-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    <span>-</span>
                    <input type="number" name="harga_max" placeholder="Max"
                        class="w-full border border-gray-300 rounded-md p-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                </div>
            </div>


            <div>
                <h3 class="text-lg font-semibold mb-2">Warna</h3>
                <div class="space-y-2 text-sm">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="form-checkbox text-teal-500"> Biru
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="form-checkbox text-teal-500"> Oranye
                    </label>
                </div>
            </div>
        </aside>

        <!-- Grid Produk -->
        <div class="w-full md:w-3/4">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500">Menampilkan 1–9 dari 52</p>
                <select class="border border-gray-300 rounded-md p-2 text-sm">
                    <option>Urutkan</option>
                </select>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                @for ($i = 0; $i < 9; $i++)
                <div class="relative bg-white border border-gray-200 rounded-[20px] overflow-hidden shadow-sm 
                    hover:shadow-lg hover:-translate-y-1 transition duration-200 transform group">

                    <!-- Gambar Produk -->
                    <div class="relative w-full h-56">
                        <img src="{{ asset('images/terpal-ayam.png') }}" alt="Produk"
                            class="w-full h-full object-cover rounded-t-[20px]">

                        <!-- Tombol Lihat Detail di tengah -->
                        <a href="{{ url('/produk/1') }}"
                        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 
                        bg-white text-teal-600 font-medium text-sm px-4 py-2 rounded-full 
                        shadow-md opacity-0 group-hover:opacity-100 transition duration-300 ease-in-out">
                            Lihat Detail
                        </a>

                    </div>

                    <!-- Info Produk -->
                    <div class="p-4 text-center">
                        <h3 class="text-sm font-semibold text-gray-800">Terpal Ayam Jago Cap A5</h3>
                        <p class="text-sm text-gray-600 mt-1">Rp 4.500,00</p>
                    </div>
                </div>



                @endfor
            </div>
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

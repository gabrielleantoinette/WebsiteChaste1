<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHASTE | Terpal</title>
    @vite('resources/css/app.css') <!-- pastikan Laravel Vite aktif -->
</head>
<body class="bg-white text-gray-900 font-sans">

<!-- Header -->
<header class="flex items-center justify-between px-6 md:px-20 py-5 border-b border-gray-200">
    <div class="text-2xl font-bold tracking-wide">CHASTE</div>
    <nav class="hidden md:flex space-x-8 text-sm font-medium text-gray-600">
        <a href="#" class="hover:text-teal-500 transition">Beranda</a>
        <a href="#" class="hover:text-teal-500 transition">Produk</a>
        <a href="#" class="hover:text-teal-500 transition">Kontak</a>
    </nav>
    <div class="space-x-4 text-xl text-gray-700">
        <a href="#" class="hover:text-teal-500">🛒</a>
        <a href="{{ route('login') }}" class="hover:text-teal-500">👤</a>
    </div>

</header>

<!-- Hero Section -->
<section class="bg-[#D9F2F2] py-16 px-6 md:px-20">
    <div class="flex flex-col md:flex-row items-center justify-between gap-12">
        <!-- Text -->
        <div class="max-w-xl space-y-8">
            <h1 class="text-5xl font-extrabold text-gray-900 leading-snug tracking-tight">
                Beli terpal anda <br> Sekarang!
            </h1>
            <div class="flex gap-12 text-gray-700">
                <div>
                    <p class="text-3xl font-bold">20+</p>
                    <p class="text-base">Tipe Terpal</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">100+</p>
                    <p class="text-base">Pelanggan</p>
                </div>
            </div>
            <div class="relative">
                <input type="text" placeholder="Apa yang anda cari?"
                    class="w-full py-4 px-5 pr-14 rounded-[16px] border border-gray-300 focus:outline-none focus:ring-2 focus:ring-teal-400 shadow-sm placeholder:text-gray-500">
                <button class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 text-lg">🔍</button>
            </div>
        </div>

        <!-- Image -->
        <div class="w-[320px] h-[320px] rounded-[32px] overflow-hidden border-4 border-white shadow-xl">
            <img src="{{ asset('images/gulungan-terpal.png') }}" alt="Gulungan Terpal" class="object-cover w-full h-full">
        </div>
    </div>
</section>

<!-- Produk Section -->
<section class="py-20 px-6 md:px-20 bg-white">
    <div class="mb-12 text-center">
        <h2 class="text-3xl font-bold text-gray-800">Penjualan Terpal Terbaik</h2>
        <p class="text-gray-600 mt-3">Langkah mudah membeli produk terpal favorit anda!</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-10">
        <!-- Contoh produk statis -->
        <div class="bg-white border border-gray-200 rounded-[20px] overflow-hidden shadow-sm hover:shadow-md transition">
            <img src="{{ asset('images/terpal-ayam.png') }}" alt="Terpal Ayam" class="w-full h-56 object-cover">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800">Terpal Ayam Jago Cap A5</h3>
                <p class="text-sm text-gray-600 mt-2">Rp 4.500,00</p>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-[20px] overflow-hidden shadow-sm hover:shadow-md transition">
            <img src="{{ asset('images/terpal-gajah.png') }}" alt="Terpal Gajah" class="w-full h-56 object-cover">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800">Terpal Gajah Surya A2</h3>
                <p class="text-sm text-gray-600 mt-2">Rp 2.600,00</p>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-[20px] overflow-hidden shadow-sm hover:shadow-md transition">
            <img src="{{ asset('images/terpal-lumba.png') }}" alt="Terpal Lumba" class="w-full h-56 object-cover">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800">Terpal Cap Lumba-lumba 5x7</h3>
                <p class="text-sm text-gray-600 mt-2">Rp 3.500,00</p>
            </div>
        </div>
    </div>
</section>
<!-- Tentang Kami -->
<section class="py-16 px-6 md:px-20 bg-white text-center">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Tentang kami</h2>
    <p class="text-gray-600 mb-12">Beli sekarang dan rasakan kualitasnya</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 max-w-5xl mx-auto">
        <!-- Fitur 1 -->
        <div class="flex flex-col items-center text-center space-y-4">
            <div class="text-3xl">🎯</div>
            <h3 class="font-semibold text-gray-800">Beragam Pilihan</h3>
            <p class="text-sm text-gray-600 max-w-xs">Menawarkan berbagai jenis, tipe, dan ukuran sesuai keinginan anda.</p>
        </div>

        <!-- Fitur 2 -->
        <div class="flex flex-col items-center text-center space-y-4">
            <div class="text-3xl">📦</div>
            <h3 class="font-semibold text-gray-800">Pengiriman Cepat</h3>
            <p class="text-sm text-gray-600 max-w-xs">3-hari atau kurang untuk pengiriman terpal anda.</p>
        </div>

        <!-- Fitur 3 -->
        <div class="flex flex-col items-center text-center space-y-4">
            <div class="text-3xl">💬</div>
            <h3 class="font-semibold text-gray-800">24/7 Bantuan</h3>
            <p class="text-sm text-gray-600 max-w-xs">Menjawab seluruh pertanyaan berkaitan dengan bisnis 24/7.</p>
        </div>
    </div>
</section>

<!-- Kategori -->
<section class="py-20 px-6 md:px-20 bg-[#D9F2F2] text-center">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Kategori</h2>
    <p class="text-gray-600 mb-10">Temukan apa yang anda cari</p>

    <div class="flex flex-col md:flex-row justify-center items-center gap-10">
        <!-- Kategori 1 -->
        <div class="flex flex-col items-center space-y-4">
            <div class="w-[180px] h-[240px] overflow-hidden rounded-[20px] shadow-md">
                <img src="{{ asset('images/terpal-ayam.png') }}" class="w-full h-full object-cover" alt="Terpal Plastik">
            </div>
            <p class="text-sm font-medium text-gray-700">Terpal Plastik</p>
        </div>

        <!-- Kategori 2 -->
        <div class="flex flex-col items-center space-y-4">
            <div class="w-[180px] h-[240px] overflow-hidden rounded-[20px] shadow-md">
                <img src="{{ asset('images/terpal-gajah.png') }}" class="w-full h-full object-cover" alt="Terpal Kain">
            </div>
            <p class="text-sm font-medium text-gray-700">Terpal Kain</p>
            <a href="#" class="mt-2 inline-flex items-center gap-2 px-5 py-2 bg-white border border-gray-300 text-sm rounded-full hover:bg-gray-100 transition">
                Telusuri →
            </a>
        </div>

        <!-- Kategori 3 -->
        <div class="flex flex-col items-center space-y-4">
            <div class="w-[180px] h-[240px] overflow-hidden rounded-[20px] shadow-md">
                <img src="{{ asset('images/terpal-lumba.png') }}" class="w-full h-full object-cover" alt="Terpal Karet">
            </div>
            <p class="text-sm font-medium text-gray-700">Terpal Karet</p>
        </div>
    </div>
</section>
<!-- Testimoni -->
<section class="py-20 px-6 md:px-20 bg-white">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 text-left mb-10 max-w-3xl leading-snug">
        Apa yang dikatakan pelanggan tentang <br class="hidden md:block">
        PT. Chaste Gemilang Mandiri?
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Testimoni 1 -->
        <div class="bg-[#D9F2F2] rounded-[16px] p-6 shadow-sm flex flex-col justify-between">
            <p class="text-sm text-gray-700 mb-6">
                Jorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc vulputate libero et velit interdum, ac aliquet odio mattis. 
                Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
            </p>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/avatar1.png') }}" alt="John Doe" class="w-10 h-10 rounded-full object-cover">
                    <div class="text-sm">
                        <p class="font-semibold text-gray-800">John Doe</p>
                        <p class="text-gray-500 text-xs">YouTuber</p>
                    </div>
                </div>
                <div class="flex items-center gap-1 text-sm text-gray-700">
                    <span class="text-xl">⭐</span> 4.5
                </div>
            </div>
        </div>

        <!-- Testimoni 2 -->
        <div class="bg-[#D9F2F2] rounded-[16px] p-6 shadow-sm flex flex-col justify-between">
            <p class="text-sm text-gray-700 mb-6">
                Jorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc vulputate libero et velit interdum, ac aliquet odio mattis. 
                Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
            </p>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/avatar2.png') }}" alt="John Doe" class="w-10 h-10 rounded-full object-cover">
                    <div class="text-sm">
                        <p class="font-semibold text-gray-800">John Doe</p>
                        <p class="text-gray-500 text-xs">YouTuber</p>
                    </div>
                </div>
                <div class="flex items-center gap-1 text-sm text-gray-700">
                    <span class="text-xl">⭐</span> 4.5
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Footer -->
<!-- Footer -->
<footer class="bg-[#D9F2F2] py-10 px-6 md:px-20 text-sm text-gray-700">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Brand -->
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">CHASTE</h3>
            <p class="mb-4 max-w-xs">Kami membantu anda menyediakan terpal terbaik.</p>
            <div class="flex space-x-4 text-lg">
                <a href="#" class="hover:text-black">Instagram</a>
                <a href="#" class="hover:text-black">Facebook</a>
                <a href="#" class="hover:text-black">Twitter</a>
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
            <p>E-mail: xyz@abc.a</p>
        </div>
    </div>

    <div class="text-center text-xs text-gray-500 mt-8">
        © {{ date('Y') }} Hak Cipta Dilindungi
    </div>
</footer>


</body>
</html>

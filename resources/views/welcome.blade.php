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
    @include('layouts.customer-nav')

    <div class="px-[100px]">
        <!-- Hero Section -->
        <section class="bg-[#D9F2F2] py-16 px-6 md:px-20 rounded-[24px] mt-[24px]">
  <div class="flex flex-col md:flex-row items-center justify-between gap-6">
    
    <!-- Text -->
    <div class="w-full md:w-3/5 space-y-8">
      <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-relaxed">
        Beli terpal anda sekarang!<br>
        Cari terpal yang <span class="font-extrabold text-teal-600">kuat, tahan air, dan siap pakai?</span> 
  Temukan berbagai tipe dan ukuran dengan harga terbaik di sini!
      </h1>

                    <div class="flex gap-12 text-gray-700">
                        <div>
                            <p class="text-3xl font-bold">20+</p>
                            <p class="text-base">Tipe Terpal</p>
                        </div>
                        <span>|</span>
                        <div>
                            <p class="text-3xl font-bold">100+</p>
                            <p class="text-base">Pelanggan</p>
                        </div>
                    </div>
                    <div class="relative">
                        <input type="text" placeholder="Apa yang anda cari?"
                            class="w-full py-5 px-5 pr-14 rounded-[16px] focus:outline-none placeholder:text-gray-500 bg-white">
                        <button
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 text-lg bg-[#D9F2F2] rounded-[16px] p-2 w-[48==px] h-[48px] flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Image -->
                <div class="w-150 h-150 rounded-full overflow-hidden border-4 border-white shadow-md">
                    <img src="{{ asset('images/gulungan-terpal.png') }}" alt="Custom Terpal"
                        class="w-full h-full object-cover">
                </div>
            </div>
        </section>

        <!-- Produk Section -->
        <section class="py-20 bg-white flex gap-[24px]">
            <div class="mb-12 max-w-[250px]">
                <h2 class="text-3xl font-bold text-gray-800">Penjualan<br>Terpal Terbaik</h2>
                <p class="text-gray-600 mt-3">Langkah mudah membeli produk terpal favorit anda!</p>
                <button class="bg-[#D9F2F2] h-[50px] text-gray-800 px-4 py-2 rounded-lg mt-4 flex items-center gap-2">
                    Lihat Lebih
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>

                </button>
            </div>

            <div class="grid grid-cols-3 gap-10 grow">
                <!-- Contoh produk statis -->
                <div class="min-w-[300px]">
                    <img src="{{ asset('images/terpal-ayam.png') }}" alt="Terpal Ayam"
                        class="w-full h-[436px] object-cover overflow-hidden rounded-xl">
                    <div class='pt-4'>
                        <h3 class="text-lg font-semibold text-gray-800">Terpal Ayam Jago Cap A5</h3>
                        <p class="text-gray-600 mt-2">Rp 4.500,00</p>
                    </div>
                </div>
                <div class="min-w-[300px]">
                    <img src="{{ asset('images/terpal-gajah.png') }}" alt="Terpal Gajah"
                        class="w-full h-[436px] object-cover overflow-hidden rounded-xl">
                    <div class='pt-4'>
                        <h3 class="text-lg font-semibold text-gray-800">Terpal Gajah Surya A2</h3>
                        <p class="text-gray-600 mt-2">Rp 2.600,00</p>
                    </div>
                </div>
                <div class="min-w-[300px]">
                    <img src="{{ asset('images/terpal-lumba.png') }}" alt="Terpal Lumba"
                        class="w-full h-[436px] object-cover overflow-hidden rounded-xl">
                    <div class='pt-4'>
                        <h3 class="text-lg font-semibold text-gray-800">Terpal Cap Lumba-lumba 5x7</h3>
                        <p class="text-gray-600 mt-2">Rp 3.500,00</p>
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
                    <p class="text-sm text-gray-600 max-w-xs">Menawarkan berbagai jenis, tipe, dan ukuran sesuai
                        keinginan
                        anda.</p>
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
                    <p class="text-sm text-gray-600 max-w-xs">Menjawab seluruh pertanyaan berkaitan dengan bisnis 24/7.
                    </p>
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
                        <img src="{{ asset('images/terpal-ayam.png') }}" class="w-full h-full object-cover"
                            alt="Terpal Plastik">
                    </div>
                    <p class="text-sm font-medium text-gray-700">Terpal Plastik</p>
                </div>

                <!-- Kategori 2 -->
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-[180px] h-[240px] overflow-hidden rounded-[20px] shadow-md">
                        <img src="{{ asset('images/terpal-gajah.png') }}" class="w-full h-full object-cover"
                            alt="Terpal Kain">
                    </div>
                    <p class="text-sm font-medium text-gray-700">Terpal Kain</p>
                    <a href="#"
                        class="mt-2 inline-flex items-center gap-2 px-5 py-2 bg-white border border-gray-300 text-sm rounded-full hover:bg-gray-100 transition">
                        Telusuri →
                    </a>
                </div>

                <!-- Kategori 3 -->
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-[180px] h-[240px] overflow-hidden rounded-[20px] shadow-md">
                        <img src="{{ asset('images/terpal-lumba.png') }}" class="w-full h-full object-cover"
                            alt="Terpal Karet">
                    </div>
                    <p class="text-sm font-medium text-gray-700">Terpal Karet</p>
                </div>
            </div>
        </section>
        <!-- Testimoni -->
        <section class="py-20 bg-white">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 text-left mb-10 max-w-3xl leading-snug">
                Apa yang dikatakan pelanggan tentang <br class="hidden md:block">
                PT. Chaste Gemilang Mandiri?
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Testimoni 1 -->
                <div class="bg-[#D9F2F2] rounded-[16px] p-6 shadow-sm flex flex-col justify-between">
                    <p class="text-sm text-gray-700 mb-6">
                        Jorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc vulputate libero et velit
                        interdum, ac
                        aliquet odio mattis.
                        Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('images/avatar1.jpeg') }}" alt="John Doe"
                                class="w-10 h-10 rounded-full object-cover">
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
                        Jorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc vulputate libero et velit
                        interdum, ac
                        aliquet odio mattis.
                        Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('images/avatar2.jpeg') }}" alt="John Doe"
                                class="w-10 h-10 rounded-full object-cover">
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
    </div>
    <!-- Footer -->
    <footer class="bg-[#D9F2F2] py-10 px-[100px] text-sm text-gray-700">
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

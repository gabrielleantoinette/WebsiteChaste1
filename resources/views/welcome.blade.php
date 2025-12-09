<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CHASTE | Terpal</title>
    @vite('resources/css/app.css') <!-- pastikan Laravel Vite aktif -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-white text-gray-900 font-sans">
    <!-- Header -->
    @include('layouts.customer-nav')

    <!-- Notifikasi Hutang -->
    @if(session('user'))
        @php
            $debtStatus = App\Http\Controllers\CustomerController::checkCustomerDebtStatus(session('user')['id']);
        @endphp
        
        @if($debtStatus['melebihiLimit'] || $debtStatus['adaHutangTerlambat'])
            <div class="px-4 sm:px-6 lg:px-12 xl:px-20 mt-4">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                @if($debtStatus['melebihiLimit'])
                                    Limit Hutang Terlampaui
                                @else
                                    Hutang Jatuh Tempo
                                @endif
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                @if($debtStatus['melebihiLimit'])
                                    <p>Total hutang Anda Rp {{ number_format($debtStatus['totalHutangAktif'], 0, ',', '.') }} telah melebihi limit Rp {{ number_format($debtStatus['limitHutang'], 0, ',', '.') }}.</p>
                                @else
                                    <p>Anda memiliki hutang jatuh tempo yang belum dilunasi.</p>
                                @endif
                                <p class="mt-1">Silakan lunasi hutang terlebih dahulu untuk dapat melakukan transaksi baru.</p>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('profile.hutang') }}" class="text-sm font-medium text-red-800 hover:text-red-900 underline">
                                    Lihat Detail Hutang →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <div class="px-4 sm:px-6 lg:px-12 xl:px-20">
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-teal-500 via-teal-600 to-teal-700 py-12 sm:py-16 px-6 sm:px-10 lg:px-16 rounded-[24px] mt-6 sm:mt-10 overflow-hidden shadow-2xl">
            <!-- Decorative elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
            
            <div class="relative flex flex-col lg:flex-row items-center justify-between gap-10 z-10">
                <!-- Text -->
                <div class="w-full lg:w-3/5 space-y-8">
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-snug tracking-tight drop-shadow-lg">
                        Beli terpal anda sekarang!<br>
                        Cari terpal yang <span class="text-teal-100">kuat, tahan air, dan siap pakai?</span><br>
                        <span class="text-lg sm:text-xl lg:text-2xl font-semibold text-teal-50">Temukan berbagai tipe dan ukuran dengan harga terbaik di sini!</span>
                    </h1>

                    <div class="flex flex-wrap items-center gap-6 sm:gap-12 text-white">
                        <div class="text-center sm:text-left">
                            <p class="text-3xl sm:text-4xl font-bold drop-shadow-md">20+</p>
                            <p class="text-sm sm:text-base text-teal-50">Tipe Terpal</p>
                        </div>
                        <span class="hidden sm:inline text-white/30">|</span>
                        <div class="text-center sm:text-left">
                            <p class="text-3xl sm:text-4xl font-bold drop-shadow-md">100+</p>
                            <p class="text-sm sm:text-base text-teal-50">Pelanggan</p>
                        </div>
                    </div>
                    
                    <div class="relative group">
                        <form action="{{ route('produk') }}" method="GET" class="flex">
                            <input type="text" 
                                   name="search"
                                   placeholder="Apa yang anda cari?"
                                   class="w-full py-4 sm:py-5 px-4 sm:px-5 pr-14 rounded-[16px] focus:outline-none placeholder:text-gray-500 bg-white shadow-xl border-2 border-white focus:border-teal-300 focus:ring-4 focus:ring-teal-200 transition-all duration-300">
                            <button type="submit"
                                class="absolute right-2 top-1/2 -translate-y-1/2 bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white rounded-[12px] p-2.5 w-12 h-12 flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Image -->
                <div class="w-full max-w-sm sm:max-w-md lg:max-w-lg xl:max-w-xl rounded-full overflow-hidden border-4 border-white/30 shadow-2xl transform hover:scale-105 transition-transform duration-300 z-10">
                    <img src="{{ asset('images/gulungan-terpal.png') }}" alt="Custom Terpal"
                        class="w-full h-full object-cover">
                </div>
            </div>
        </section>

        <!-- Produk Section -->
        <section class="py-16 sm:py-20 bg-white flex flex-col lg:flex-row gap-10 lg:gap-12">
            <div class="lg:max-w-xs">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">Penjualan<br>Terpal Terbaik</h2>
                <p class="text-gray-600 mt-3 text-sm sm:text-base">Langkah mudah membeli produk terpal favorit anda!</p>
                <a href="{{ route('produk') }}"
                    class="inline-flex bg-[#D9F2F2] min-h-[48px] text-gray-800 px-4 py-2 rounded-lg mt-4 items-center gap-2 text-sm sm:text-base">
                    Lihat Lebih
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                </a>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 flex-1">
                <!-- Contoh produk statis -->
                <div class="space-y-4">
                    <img src="{{ asset('images/terpal-ayam.png') }}" alt="Terpal Ayam"
                        class="w-full h-72 sm:h-[380px] object-cover overflow-hidden rounded-xl">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Terpal Ayam Jago Cap A5</h3>
                        <p class="text-gray-600 mt-2">Rp 4.500,00</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <img src="{{ asset('images/terpal-gajah.png') }}" alt="Terpal Gajah"
                        class="w-full h-72 sm:h-[380px] object-cover overflow-hidden rounded-xl">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Terpal Gajah Surya A2</h3>
                        <p class="text-gray-600 mt-2">Rp 2.600,00</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <img src="{{ asset('images/terpal-lumba.png') }}" alt="Terpal Lumba"
                        class="w-full h-72 sm:h-[380px] object-cover overflow-hidden rounded-xl">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Terpal Cap Lumba-lumba 5x7</h3>
                        <p class="text-gray-600 mt-2">Rp 3.500,00</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- Tentang Kami -->
        <section class="py-16 px-6 sm:px-10 lg:px-16 bg-white text-center">
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
        <section class="py-20 px-6 sm:px-10 lg:px-16 bg-[#D9F2F2] text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Kategori</h2>
            <p class="text-gray-600 mb-10">Temukan apa yang anda cari</p>

            <div class="flex flex-col md:flex-row justify-center items-center gap-10">
                <!-- Kategori 1 -->
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-40 sm:w-[180px] h-56 sm:h-[240px] overflow-hidden rounded-[20px] shadow-md">
                        <img src="{{ asset('images/terpal-ayam.png') }}" class="w-full h-full object-cover"
                            alt="Terpal Plastik">
                    </div>
                    <p class="text-sm font-medium text-gray-700">Terpal Plastik</p>
                </div>

                <!-- Kategori 2 -->
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-40 sm:w-[180px] h-56 sm:h-[240px] overflow-hidden rounded-[20px] shadow-md">
                        <img src="{{ asset('images/terpal-gajah.png') }}" class="w-full h-full object-cover"
                            alt="Terpal Kain">
                    </div>
                    <p class="text-sm font-medium text-gray-700">Terpal Kain</p>
                    <a href="{{ route('produk') }}"
                        class="mt-2 inline-flex items-center gap-2 px-5 py-2 bg-white border border-gray-300 text-sm rounded-full hover:bg-gray-100 transition">
                        Telusuri →
                    </a>
                </div>

                <!-- Kategori 3 -->
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-40 sm:w-[180px] h-56 sm:h-[240px] overflow-hidden rounded-[20px] shadow-md">
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
                @forelse($reviews->take(6) as $review)
                    <div class="bg-[#D9F2F2] rounded-[16px] p-6 shadow-sm flex flex-col justify-between">
                        <p class="text-sm text-gray-700 mb-6">
                            "{{ Str::limit($review->comment, 200) }}"
                        </p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                @if($review->user && $review->user->profile_picture)
                                    <img src="{{ asset('storage/photos/' . $review->user->profile_picture) }}" 
                                         alt="{{ $review->user->name }}"
                                         class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-teal-500 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <div class="text-sm">
                                    <p class="font-semibold text-gray-800">{{ $review->user->name ?? 'Pelanggan' }}</p>
                                    @if($review->product)
                                        <p class="text-gray-500 text-xs">{{ $review->product->name }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-1 text-sm text-gray-700">
                                <span class="text-xl">⭐</span> {{ number_format($review->rating, 1) }}
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Fallback jika belum ada review -->
                    <div class="bg-[#D9F2F2] rounded-[16px] p-6 shadow-sm flex flex-col justify-between">
                        <p class="text-sm text-gray-700 mb-6">
                            Belum ada testimoni dari pelanggan. Jadilah yang pertama memberikan review!
                        </p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-teal-500 flex items-center justify-center text-white font-semibold">
                                    ?
                                </div>
                                <div class="text-sm">
                                    <p class="font-semibold text-gray-800">Belum ada review</p>
                                    <p class="text-gray-500 text-xs">Menunggu review pertama</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 text-sm text-gray-700">
                                <span class="text-xl">⭐</span> 0.0
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
    <!-- Footer -->
    @include('layouts.footer')
</body>

</html>

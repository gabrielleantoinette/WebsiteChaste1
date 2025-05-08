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
    <div class="px-[100px]">
        <!-- Hero -->
        <section class="bg-[#D9F2F2] py-16 px-6 md:px-20 rounded-[24px] mt-[24px]">
            <div class="flex flex-col md:flex-row items-center justify-between gap-12">
                <div class="max-w-xl space-y-8">
                    <h1 class="text-5xl font-extrabold text-gray-900 leading-snug tracking-tight">
                        Butuh terpal tapi ukuran tidak sesuai keinginan?
                    </h1>
                    <a href="{{ route('custom.terpal') }}"
                        class="inline-block bg-white text-gray-800 font-semibold px-6 py-3 rounded-md border border-gray-300 hover:bg-gray-100 transition">
                        Custom Terpalmu Disini!
                    </a>
                </div>
                <div class="w-100 h-100 rounded-full overflow-hidden border-4 border-white shadow-md">
                    <img src="{{ asset('images/gulungan-terpal.png') }}" alt="Custom Terpal"
                        class="w-full h-full object-cover">
                </div>
            </div>
        </section>

        <!-- Produk Section -->
        <section class="py-16 bg-white">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Sidebar Filter -->
                <aside class="w-full md:w-1/4 space-y-8 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <form method="GET" action="{{ route('produk') }}" class="space-y-8">
                        {{-- Kategori --}}
                        <div>
                            <h3 class="text-lg font-bold text-teal-700 mb-4 border-b pb-2">Kategori</h3>
                            <div class="space-y-3 text-sm text-gray-700">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="kategori[]" value="plastik"
                                        {{ in_array('plastik', request('kategori', [])) ? 'checked' : '' }}
                                        class="form-checkbox text-teal-600 focus:ring-teal-400">
                                    <span>Plastik</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="kategori[]" value="kain"
                                        {{ in_array('kain', request('kategori', [])) ? 'checked' : '' }}
                                        class="form-checkbox text-teal-600 focus:ring-teal-400">
                                    <span>Kain</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="kategori[]" value="karet"
                                        {{ in_array('karet', request('kategori', [])) ? 'checked' : '' }}
                                        class="form-checkbox text-teal-600 focus:ring-teal-400">
                                    <span>Karet</span>
                                </label>
                            </div>
                        </div>
                    
                        {{-- Ukuran --}}
                        <div>
                            <h3 class="text-lg font-bold text-teal-700 mb-4 border-b pb-2">Ukuran</h3>
                            <div class="space-y-3 text-sm text-gray-700">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="ukuran[]" value="2x3"
                                        {{ in_array('2x3', request('ukuran', [])) ? 'checked' : '' }}
                                        class="form-checkbox text-teal-600 focus:ring-teal-400">
                                    <span>2x3</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="ukuran[]" value="5x7"
                                        {{ in_array('5x7', request('ukuran', [])) ? 'checked' : '' }}
                                        class="form-checkbox text-teal-600 focus:ring-teal-400">
                                    <span>5x7</span>
                                </label>
                            </div>
                        </div>
                    
                        {{-- Harga --}}
                        <div>
                            <h3 class="text-lg font-bold text-teal-700 mb-4 border-b pb-2">Harga</h3>
                            <div class="flex items-center gap-3 text-sm text-gray-700">
                                <input type="number" name="harga_min" placeholder="Min" value="{{ request('harga_min') }}"
                                    class="w-1/2 border border-gray-300 rounded-md p-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                                <span>-</span>
                                <input type="number" name="harga_max" placeholder="Max" value="{{ request('harga_max') }}"
                                    class="w-1/2 border border-gray-300 rounded-md p-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                            </div>
                        </div>
                    
                        {{-- Warna --}}
                        <div>
                            <h3 class="text-lg font-bold text-teal-700 mb-4 border-b pb-2">Warna</h3>
                            <div class="space-y-3 text-sm text-gray-700">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="warna[]" value="biru"
                                        {{ in_array('biru', request('warna', [])) ? 'checked' : '' }}
                                        class="form-checkbox text-teal-600 focus:ring-teal-400">
                                    <span>Biru</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="warna[]" value="oranye"
                                        {{ in_array('oranye', request('warna', [])) ? 'checked' : '' }}
                                        class="form-checkbox text-teal-600 focus:ring-teal-400">
                                    <span>Oranye</span>
                                </label>
                            </div>
                        </div>
                    
                        {{-- Tombol Filter --}}
                        <div>
                            <button type="submit"
                                class="w-full bg-teal-600 text-white font-semibold py-2 rounded hover:bg-teal-700 transition">
                                Terapkan Filter
                            </button>
                        </div>
                    </form>                    
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
                        @foreach ($products as $product)
                            <div>
                                <div
                                    class="relative bg-white border border-gray-200 rounded-[20px] overflow-hidden shadow-sm
                    hover:shadow-lg hover:-translate-y-1 transition duration-200 transform group">

                                    <!-- Gambar Produk -->
                                    <div class="relative w-full h-56">
                                        <img src="{{ asset('images/terpal-ayam.png') }}" alt="Produk"
                                            class="w-full h-full object-cover rounded-t-[20px]">

                                        <!-- Tombol Lihat Detail di tengah -->
                                        <a href="{{ url('/produk/' . $product->id) }}"
                                            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                        bg-white text-teal-600 font-medium text-sm px-4 py-2 rounded-full
                        shadow-md opacity-0 group-hover:opacity-100 transition duration-300 ease-in-out">
                                            Lihat Detail
                                        </a>

                                    </div>

                                    <!-- Info Produk -->
                                    <div class="p-4 text-center">
                                        <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
                                        <p class="text-xs text-gray-600">Ukuran: {{ $product->size }}</p>
                                        <p class="text-sm text-gray-600 mt-1">Rp
                                            {{ number_format($product->price) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

    </div>
    <!-- Footer -->
    @include('layouts.footer')
</body>

</html>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Produk Pembeli | CHASTE</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        
        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.1);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        }
        
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
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
                        Kustom Terpalmu Disini!
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
            <!-- Search Bar -->
            <div class="mb-8">
                <form method="GET" action="{{ route('produk') }}" class="max-w-3xl mx-auto">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-teal-500 transition-all duration-300 transform group-focus-within:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               placeholder="Cari terpal, ukuran, atau kategori..." 
                               value="{{ request('search') }}"
                               class="w-full pl-12 pr-16 py-4 text-lg border-2 border-gray-200 rounded-2xl focus:outline-none focus:border-teal-400 focus:ring-4 focus:ring-teal-100 transition-all duration-300 placeholder:text-gray-400 bg-white shadow-sm hover:shadow-lg group-hover:border-gray-300">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                            <button type="submit"
                                    class="bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white px-6 py-2 rounded-xl font-medium transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:ring-offset-2 shadow-md hover:shadow-lg">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Cari
                                </span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Quick Search Tags -->
                    @if(request('search'))
                        <div class="mt-4 flex items-center gap-2 flex-wrap animate-fade-in">
                            <span class="text-sm text-gray-500">Hasil pencarian:</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-teal-100 to-teal-200 text-teal-800 shadow-sm">
                                "{{ request('search') }}"
                                <a href="{{ route('produk') }}" class="ml-2 text-teal-600 hover:text-teal-800 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </span>
                        </div>
                    @else
                        <div class="mt-4 flex items-center gap-2 flex-wrap">
                            <span class="text-sm text-gray-500">Pencarian populer:</span>
                            <a href="{{ route('produk', ['search' => 'terpal plastik']) }}" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gradient-to-r hover:from-teal-100 hover:to-teal-200 hover:text-teal-800 transition-all duration-200 transform hover:scale-105">
                                Terpal Plastik
                            </a>
                            <a href="{{ route('produk', ['search' => 'terpal kain']) }}" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gradient-to-r hover:from-teal-100 hover:to-teal-200 hover:text-teal-800 transition-all duration-200 transform hover:scale-105">
                                Terpal Kain
                            </a>
                            <a href="{{ route('produk', ['search' => 'ukuran besar']) }}" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gradient-to-r hover:from-teal-100 hover:to-teal-200 hover:text-teal-800 transition-all duration-200 transform hover:scale-105">
                                Ukuran Besar
                            </a>
                        </div>
                    @endif
                </form>
            </div>

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
                                    <input type="checkbox" name="ukuran[]" value="3x4"
                                        {{ in_array('3x4', request('ukuran', [])) ? 'checked' : '' }}
                                        class="form-checkbox text-teal-600 focus:ring-teal-400">
                                    <span>3x4</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="ukuran[]" value="4x5"
                                        {{ in_array('4x5', request('ukuran', [])) ? 'checked' : '' }}
                                        class="form-checkbox text-teal-600 focus:ring-teal-400">
                                    <span>4x5</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="ukuran[]" value="4x6"
                                        {{ in_array('4x6', request('ukuran', [])) ? 'checked' : '' }}
                                        class="form-checkbox text-teal-600 focus:ring-teal-400">
                                    <span>4x6</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="ukuran[]" value="5x7"
                                        {{ in_array('5x7', request('ukuran', [])) ? 'checked' : '' }}
                                        class="form-checkbox text-teal-600 focus:ring-teal-400">
                                    <span>5x7</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="ukuran[]" value="6x8"
                                        {{ in_array('6x8', request('ukuran', [])) ? 'checked' : '' }}
                                        class="form-checkbox text-teal-600 focus:ring-teal-400">
                                    <span>6x8</span>
                                </label>
                            </div>
                        </div>

                        {{-- Harga --}}
                        <div>
                            <h3 class="text-lg font-bold text-teal-700 mb-4 border-b pb-2">Harga</h3>
                            <div class="flex items-center gap-3 text-sm text-gray-700">
                                <input type="number" name="harga_min" placeholder="Min"
                                    value="{{ request('harga_min') }}"
                                    class="w-1/2 border border-gray-300 rounded-md p-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                                <span>-</span>
                                <input type="number" name="harga_max" placeholder="Max"
                                    value="{{ request('harga_max') }}"
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
                        <div class="space-y-3">
                            <button type="submit"
                                class="w-full bg-teal-600 text-white font-semibold py-2 rounded hover:bg-teal-700 transition">
                                Terapkan Filter
                            </button>
                            <a href="{{ route('produk') }}" 
                               class="block w-full text-center bg-gray-100 text-gray-700 font-semibold py-2 rounded hover:bg-gray-200 transition">
                                Hapus Filter
                            </a>
                        </div>
                    </form>
                </aside>


                <!-- Grid Produk -->
                <div class="w-full md:w-3/4">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <p class="text-sm text-gray-500">
                                Menampilkan {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk
                            </p>
                            @if(request('search') || request('kategori') || request('ukuran') || request('harga_min') || request('harga_max') || request('warna'))
                                <span class="text-xs bg-teal-100 text-teal-800 px-2 py-1 rounded-full">
                                    Filter Aktif
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-600">Urutkan:</label>
                            <select name="sort" class="border border-gray-300 rounded-md p-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                                <option value="name_asc" {{ (request('sort') == 'name' && request('order') != 'desc') ? 'selected' : '' }}>Nama A-Z</option>
                                <option value="name_desc" {{ (request('sort') == 'name' && request('order') == 'desc') ? 'selected' : '' }}>Nama Z-A</option>
                                <option value="price_asc" {{ (request('sort') == 'price' && request('order') != 'desc') ? 'selected' : '' }}>Harga Terendah</option>
                                <option value="price_desc" {{ (request('sort') == 'price' && request('order') == 'desc') ? 'selected' : '' }}>Harga Tertinggi</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                        @foreach ($products as $product)
                            <div class="group">
                                <div class="relative bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 hover-lift">
                                    <!-- Gambar Produk -->
                                    <div class="relative w-full h-56">
                                        <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/logo-perusahaan.png') }}"
                                            alt="{{ $product->name }}"
                                            class="w-full h-full object-cover rounded-t-[20px]">

                                        <a href="{{ route('produk.detail', $product->id) }}"
                                            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                                                    bg-white text-teal-600 font-medium text-sm px-4 py-2 rounded-full
                                                    shadow-md opacity-0 group-hover:opacity-100 transition duration-300 ease-in-out">
                                            Lihat Detail
                                        </a>
                                    </div>

                                    <!-- Info Produk -->
                                    <div class="p-5 text-center">
                                        <h3 class="font-semibold text-gray-800 text-lg mb-2 group-hover:text-teal-600 transition-colors">{{ $product->name }}</h3>
                                        <p class="text-sm text-gray-600 mb-2">Ukuran: {{ $product->size }}</p>
                                        <p class="text-lg font-bold text-teal-600">Rp {{ number_format($product->price) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="mt-8 flex justify-center">
                            {{ $products->links() }}
                        </div>
                    @endif
                    
                    <!-- Empty State -->
                    @if($products->count() == 0)
                        <div class="text-center py-12">
                            <div class="text-gray-400 mb-4">
                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada produk ditemukan</h3>
                            <p class="text-gray-500 mb-4">
                                @if(request('search'))
                                    Tidak ada produk yang cocok dengan pencarian "{{ request('search') }}"
                                @else
                                    Coba ubah filter pencarian Anda
                                @endif
                            </p>
                            <a href="{{ route('produk') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700">
                                Lihat Semua Produk
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </section>

    </div>
    <!-- Footer -->
    @include('layouts.footer')
    
    <script>
        // Handle sorting
        document.addEventListener('DOMContentLoaded', function() {
            const sortSelect = document.querySelector('select[name="sort"]');
            if (sortSelect) {
                sortSelect.addEventListener('change', function() {
                    const form = document.createElement('form');
                    form.method = 'GET';
                    form.action = '{{ route("produk") }}';
                    
                    // Preserve existing query parameters
                    const urlParams = new URLSearchParams(window.location.search);
                    for (let [key, value] of urlParams) {
                        if (key !== 'sort' && key !== 'order') {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = key;
                            input.value = value;
                            form.appendChild(input);
                        }
                    }
                    
                    // Parse sort value (e.g., "name_asc" -> sort=name, order=asc)
                    const sortValue = this.value;
                    const [sortField, sortOrder] = sortValue.split('_');
                    
                    const sortInput = document.createElement('input');
                    sortInput.type = 'hidden';
                    sortInput.name = 'sort';
                    sortInput.value = sortField;
                    form.appendChild(sortInput);
                    
                    const orderInput = document.createElement('input');
                    orderInput.type = 'hidden';
                    orderInput.name = 'order';
                    orderInput.value = sortOrder;
                    form.appendChild(orderInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                });
            }
        });
    </script>
</body>

</html>

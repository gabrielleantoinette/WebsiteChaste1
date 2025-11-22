@extends('layouts.admin')

@section('content')
{{-- Header dengan Gradient Background --}}
<div class="relative overflow-hidden bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl mb-4 sm:mb-6 lg:mb-8">
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="relative px-4 sm:px-6 lg:px-8 py-4 sm:py-5 lg:py-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mb-1 sm:mb-2">📦 Daftar Produk</h1>
                <p class="text-sm sm:text-base text-teal-100">Kelola produk dan inventori Anda</p>
            </div>
            <div class="flex gap-3 w-full sm:w-auto">
                <a href="{{ url('/admin/products/create') }}" 
                   class="inline-flex items-center gap-2 px-4 sm:px-6 py-2 sm:py-3 bg-white/20 backdrop-blur-sm text-white text-sm sm:text-base font-medium rounded-lg hover:bg-white/30 transition-all duration-300 shadow-lg border border-white/20 w-full sm:w-auto justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                         stroke-width="2" stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Produk
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 lg:gap-6 mb-4 sm:mb-6 lg:mb-8">
    <div class="bg-white rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600">Total Produk</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">{{ $totalProducts ?? 0 }}</p>
            </div>
            <div class="p-2 sm:p-3 bg-teal-100 rounded-lg flex-shrink-0 ml-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600">Produk Aktif</p>
                <p class="text-xl sm:text-2xl font-bold text-emerald-600 truncate">{{ $activeProducts ?? 0 }}</p>
            </div>
            <div class="p-2 sm:p-3 bg-emerald-100 rounded-lg flex-shrink-0 ml-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600">Produk Non-Aktif</p>
                <p class="text-xl sm:text-2xl font-bold text-red-600 truncate">{{ $inactiveProducts ?? 0 }}</p>
            </div>
            <div class="p-2 sm:p-3 bg-red-100 rounded-lg flex-shrink-0 ml-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600">Total Kategori</p>
                <p class="text-xl sm:text-2xl font-bold text-blue-600 truncate">{{ $totalCategories ?? 0 }}</p>
            </div>
            <div class="p-2 sm:p-3 bg-blue-100 rounded-lg flex-shrink-0 ml-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- Search dan Filter dengan Card --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 sm:p-5 lg:p-6 mb-4 sm:mb-6 lg:mb-8">
    <form method="GET" action="" class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4 w-full lg:w-auto">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                </svg>
                <label for="status" class="text-xs sm:text-sm font-semibold text-gray-700">Filter Status:</label>
            </div>
            <select name="status" id="status" onchange="this.form.submit()" class="px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 bg-white shadow-sm w-full sm:w-auto text-sm">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
            </select>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full lg:w-auto">
            <div class="relative flex-1 sm:flex-none">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" placeholder="Cari nama produk..." value="{{ request('search') }}" class="pl-10 pr-4 py-2 w-full sm:w-64 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 shadow-sm text-sm" />
            </div>
            <button type="submit" class="px-4 sm:px-6 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-sm sm:text-base font-medium rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all duration-200 shadow-lg hover:shadow-xl w-full sm:w-auto">Cari</button>
            @if(request('search') || request('status'))
                <a href="{{ url('/admin/products') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm sm:text-base font-medium rounded-lg hover:bg-gray-200 transition-all duration-200 w-full sm:w-auto text-center">Reset</a>
            @endif
        </div>
    </form>
</div>

{{-- Grid Layout untuk Card Produk --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5 lg:gap-6">
    @forelse ($products as $product)
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden">
            {{-- Header Card dengan Gambar --}}
            <div class="relative">
                {{-- Gambar Produk --}}
                <div class="aspect-square bg-gray-100 overflow-hidden">
                    <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}"
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                </div>
                
                {{-- Status Badge --}}
                <div class="absolute top-3 right-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                        {{ $product->live ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $product->live ? 'Aktif' : 'Non-Aktif' }}
                    </span>
                </div>
            </div>

            {{-- Info Produk --}}
            <div class="p-4 sm:p-5 lg:p-6">
                <div class="space-y-2 sm:space-y-3">
                    <h3 class="font-semibold text-gray-900 text-base sm:text-lg line-clamp-2">{{ $product->name }}</h3>
                    <p class="text-xs sm:text-sm text-gray-600 line-clamp-2">{{ Str::limit($product->description, 80) }}</p>
                    
                    {{-- Harga --}}
                    <div class="flex items-center justify-between">
                        <span class="text-lg sm:text-xl font-bold text-teal-600 truncate">Rp {{ number_format($product->price) }}</span>
                        <span class="text-xs text-gray-500 flex-shrink-0 ml-2">ID: {{ $product->id }}</span>
                    </div>
                    
                    {{-- Kategori --}}
                    @if($product->category)
                        <div class="flex items-center gap-2">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <span class="text-xs sm:text-sm text-gray-600 truncate">{{ $product->category->name }}</span>
                        </div>
                    @endif
                </div>
                
                {{-- Action Buttons --}}
                <div class="mt-4 sm:mt-6">
                    <a href="{{ url('/admin/products/detail/' . $product->id) }}" 
                       class="w-full inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-xs sm:text-sm font-medium rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Detail
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 sm:p-8 lg:p-12 text-center">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Tidak ada produk</h3>
                <p class="text-sm sm:text-base text-gray-500 mb-4 sm:mb-6">Mulai dengan menambahkan produk baru</p>
                <a href="{{ url('/admin/products/create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-sm sm:text-base font-medium rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all duration-200 shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Produk
                </a>
            </div>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($products->hasPages())
    <div class="mt-4 sm:mt-6 lg:mt-8 flex justify-center">
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 p-2 sm:p-4">
            {{ $products->links() }}
        </div>
    </div>
@endif
@endsection
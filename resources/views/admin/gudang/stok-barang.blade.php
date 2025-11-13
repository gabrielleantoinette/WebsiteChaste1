@extends('layouts.admin')

@section('content')
<div class="py-6">
    {{-- Header dengan Gradient Background --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl mb-8">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">📦 Stok Barang Gudang</h1>
                    <p class="text-teal-100">Ringkasan stok produk, bahan baku, dan custom materials</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('gudang.dashboard') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm text-white font-medium rounded-lg hover:bg-white/30 transition-all duration-300 shadow-lg border border-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                             stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Kembali ke Dashboard
                    </a>
                    <a href="{{ route('gudang.laporan-stok') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm text-white font-medium rounded-lg hover:bg-white/30 transition-all duration-300 shadow-lg border border-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                             stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                        </svg>
                        Export Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan Stok --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                         stroke-width="2" stroke="currentColor" class="w-6 h-6 text-blue-600 dark:text-blue-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5m-16.5 0v-1.125c0-.621.504-1.125 1.125-1.125h13.5c.621 0 1.125.504 1.125 1.125V7.5m-16.5 0h16.5" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Produk</p>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $products->count() }}</p>
        </div>
        <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                         stroke-width="2" stroke="currentColor" class="w-6 h-6 text-purple-600 dark:text-purple-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h5.25c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Custom Material</p>
            <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $customMaterials->count() }}</p>
        </div>
        <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                         stroke-width="2" stroke="currentColor" class="w-6 h-6 text-orange-600 dark:text-orange-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15m-15 0a3 3 0 00-3 3v12a3 3 0 003 3h15a3 3 0 003-3V6a3 3 0 00-3-3M4.5 6h15M9 12h6m-6 3h6" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Bahan Baku</p>
            <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $rawMaterials->count() }}</p>
        </div>
        <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                         stroke-width="2" stroke="currentColor" class="w-6 h-6 text-green-600 dark:text-green-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Stok Tersedia</p>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                {{ number_format($products->sum(function($p) { return $p->variants->sum('stock'); }) + 
                   $customMaterials->sum(function($c) { return $c->variants->sum('stock'); }) + 
                   $rawMaterials->sum('stock'), 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                         stroke-width="2" stroke="currentColor" class="w-6 h-6 text-red-600 dark:text-red-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Stok Rendah (≤10)</p>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400">
                {{ $products->sum(function($p) { return $p->variants->where('stock', '<=', 10)->count(); }) + 
                   $customMaterials->sum(function($c) { return $c->variants->where('stock', '<=', 10)->count(); }) + 
                   $rawMaterials->where('stock', '<=', 10)->count() }}
            </p>
        </div>
    </div>

    {{-- Produk Regular --}}
    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg mb-8">
        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-6">Produk Regular</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto data-table text-sm border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-[#004d4d] text-gray-700 dark:text-[#ccf2f2]">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nama Produk</th>
                        <th class="px-4 py-3 text-left font-semibold">Kategori</th>
                        <th class="px-4 py-3 text-left font-semibold">Total Stok</th>
                        <th class="px-4 py-3 text-left font-semibold">Detail Stok per Warna</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr class="border-b border-gray-100 dark:border-gray-700 @if($loop->odd) bg-gray-50 dark:bg-[#2c2c2c] @endif hover:bg-teal-50 dark:hover:bg-[#003333] transition">
                            <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-200">{{ $product->name }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                <span class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded text-xs">
                                    {{ $product->category->name ?? 'Tanpa Kategori' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold text-lg">
                                <span class="{{ $product->variants->sum('stock') <= 10 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                    {{ number_format($product->variants->sum('stock'), 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="space-y-1">
                                    @forelse ($product->variants as $variant)
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $variant->color }}</span>
                                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $variant->stock <= 10 ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' }}">
                                                {{ $variant->stock }}
                                            </span>
                                        </div>
                                    @empty
                                        <span class="text-gray-500 dark:text-gray-400 text-xs">Tidak ada variant</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($product->variants->sum('stock') <= 10)
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                                        Stok Rendah
                                    </span>
                                @elseif($product->variants->sum('stock') <= 50)
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                                        Stok Menengah
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                        Stok Aman
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5m-16.5 0v-1.125c0-.621.504-1.125 1.125-1.125h13.5c.621 0 1.125.504 1.125 1.125V7.5m-16.5 0h16.5" />
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada produk regular</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Produk akan muncul di sini setelah ditambahkan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Bahan Baku --}}
    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg mb-8">
        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-6">Bahan Baku</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto data-table text-sm border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-[#004d4d] text-gray-700 dark:text-[#ccf2f2]">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nama Bahan Baku</th>
                        <th class="px-4 py-3 text-left font-semibold">Unit</th>
                        <th class="px-4 py-3 text-left font-semibold">Stok (m²)</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rawMaterials as $material)
                        <tr class="border-b border-gray-100 dark:border-gray-700 @if($loop->odd) bg-gray-50 dark:bg-[#2c2c2c] @endif hover:bg-orange-50 dark:hover:bg-[#332700] transition">
                            <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-200">{{ $material->name }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                <span class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded text-xs">
                                    {{ $material->unit ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold text-lg">
                                <span class="{{ $material->stock <= 10 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                    {{ number_format($material->stock, 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($material->stock <= 10)
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                                        Stok Rendah
                                    </span>
                                @elseif($material->stock <= 50)
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                                        Stok Menengah
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                        Stok Aman
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 21h16.5M4.5 3h15m-15 0a3 3 0 00-3 3v12a3 3 0 003 3h15a3 3 0 003-3V6a3 3 0 00-3-3M4.5 6h15M9 12h6m-6 3h6" />
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada bahan baku</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Bahan baku akan muncul di sini setelah ditambahkan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Custom Materials --}}
    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg">
        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-6">Custom Materials</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto data-table text-sm border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-[#004d4d] text-gray-700 dark:text-[#ccf2f2]">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nama Material</th>
                        <th class="px-4 py-3 text-left font-semibold">Total Stok</th>
                        <th class="px-4 py-3 text-left font-semibold">Detail Stok per Warna</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customMaterials as $material)
                        <tr class="border-b border-gray-100 dark:border-gray-700 @if($loop->odd) bg-gray-50 dark:bg-[#2c2c2c] @endif hover:bg-purple-50 dark:hover:bg-[#2d0033] transition">
                            <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-200">{{ $material->name }}</td>
                            <td class="px-4 py-3 font-bold text-lg">
                                <span class="{{ $material->variants->sum('stock') <= 10 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                    {{ number_format($material->variants->sum('stock'), 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="space-y-1">
                                    @forelse ($material->variants as $variant)
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $variant->color }}</span>
                                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $variant->stock <= 10 ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' }}">
                                                {{ $variant->stock }}
                                            </span>
                                        </div>
                                    @empty
                                        <span class="text-gray-500 dark:text-gray-400 text-xs">Tidak ada variant</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($material->variants->sum('stock') <= 10)
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                                        Stok Rendah
                                    </span>
                                @elseif($material->variants->sum('stock') <= 50)
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                                        Stok Menengah
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                        Stok Aman
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h5.25c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada custom materials</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Custom materials akan muncul di sini setelah ditambahkan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

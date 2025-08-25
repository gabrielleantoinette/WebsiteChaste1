@extends('layouts.admin')

@section('content')
<div class="py-6">
    {{-- Breadcrumb --}}
    <div class="mb-4">
        <a href="{{ route('gudang.dashboard') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
            ← Kembali ke Dashboard
        </a>
    </div>
    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Stok Barang Gudang</h1>
        <div class="flex space-x-3">
            <a href="{{ route('gudang.laporan-stok') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition">
                📊 Laporan Stok
            </a>
            <a href="{{ route('gudang.laporan-stok.pdf') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition">
                📄 Export PDF
            </a>
        </div>
    </div>

    {{-- Ringkasan Stok --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Total Produk</p>
            <p class="text-2xl font-bold text-blue-600">{{ $products->count() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Total Custom Material</p>
            <p class="text-2xl font-bold text-purple-600">{{ $customMaterials->count() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Total Stok Tersedia</p>
            <p class="text-2xl font-bold text-green-600">
                {{ $products->sum(function($p) { return $p->variants->sum('stock'); }) + 
                   $customMaterials->sum(function($c) { return $c->variants->sum('stock'); }) }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Stok Rendah (≤10)</p>
            <p class="text-2xl font-bold text-red-600">
                {{ $products->sum(function($p) { return $p->variants->where('stock', '<=', 10)->count(); }) + 
                   $customMaterials->sum(function($c) { return $c->variants->where('stock', '<=', 10)->count(); }) }}
            </p>
        </div>
    </div>

    {{-- Produk Regular --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-8">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Produk Regular</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
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
                        <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-blue-50 transition">
                            <td class="px-4 py-3 font-semibold">{{ $product->name }}</td>
                            <td class="px-4 py-3">{{ $product->category->name ?? 'Tanpa Kategori' }}</td>
                            <td class="px-4 py-3 font-bold text-lg">
                                <span class="{{ $product->variants->sum('stock') <= 10 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $product->variants->sum('stock') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="space-y-1">
                                    @forelse ($product->variants as $variant)
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="font-medium">{{ $variant->color }}</span>
                                            <span class="px-2 py-1 rounded {{ $variant->stock <= 10 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                                {{ $variant->stock }}
                                            </span>
                                        </div>
                                    @empty
                                        <span class="text-gray-500">Tidak ada variant</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($product->variants->sum('stock') <= 10)
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        Stok Rendah
                                    </span>
                                @elseif($product->variants->sum('stock') <= 50)
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                        Stok Menengah
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        Stok Aman
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-gray-500 py-4">Tidak ada produk regular.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Custom Materials --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Custom Materials</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nama Material</th>
                        <th class="px-4 py-3 text-left font-semibold">Total Stok</th>
                        <th class="px-4 py-3 text-left font-semibold">Detail Stok per Warna</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customMaterials as $material)
                        <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-purple-50 transition">
                            <td class="px-4 py-3 font-semibold">{{ $material->name }}</td>
                            <td class="px-4 py-3 font-bold text-lg">
                                <span class="{{ $material->variants->sum('stock') <= 10 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $material->variants->sum('stock') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="space-y-1">
                                    @forelse ($material->variants as $variant)
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="font-medium">{{ $variant->color }}</span>
                                            <span class="px-2 py-1 rounded {{ $variant->stock <= 10 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                                {{ $variant->stock }}
                                            </span>
                                        </div>
                                    @empty
                                        <span class="text-gray-500">Tidak ada variant</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($material->variants->sum('stock') <= 10)
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        Stok Rendah
                                    </span>
                                @elseif($material->variants->sum('stock') <= 50)
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                        Stok Menengah
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        Stok Aman
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-gray-500 py-4">Tidak ada custom materials.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

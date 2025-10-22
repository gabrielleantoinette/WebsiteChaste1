@extends('layouts.admin')

@section('content')
@php
use Illuminate\Support\Facades\Storage;
@endphp

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Daftar Produk</h1>
    <a href="{{ url('/admin/products/create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white font-medium rounded-md hover:bg-teal-700 transition shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
             stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Tambah Produk
    </a>
</div>

{{-- Grid Layout untuk Card Produk --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @foreach ($products as $product)
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
            {{-- Header Card dengan Gambar --}}
            <div class="p-4">
                {{-- Gambar Produk --}}
                <div class="aspect-square mb-4 bg-gray-100 rounded-lg overflow-hidden">
                    @if($product->image && Storage::disk('public')->exists($product->image))
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Info Produk --}}
                <div class="space-y-2">
                    <h3 class="font-semibold text-gray-800 text-lg">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($product->description, 80) }}</p>
                    
                    {{-- Harga dan Status --}}
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-teal-600">Rp {{ number_format($product->price) }}</span>
                        <span class="text-xs px-2 py-1 rounded-full {{ $product->live ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->live ? 'Tampil' : 'Tidak Tampil' }}
                        </span>
                    </div>
                    
                    <p class="text-sm text-gray-500">Ukuran: {{ $product->size }}</p>
                </div>
            </div>

            {{-- Footer Card dengan Actions --}}
            <div class="px-4 pb-4 space-y-3">
                {{-- Owner Settings --}}
                @if (Session::get('user')->role == 'owner')
                    <div class="space-y-2">
                        {{-- Harga Minimum --}}
                        <form method="POST" action="{{ url('/admin/products/detail/' . $product->id . '/min-price') }}" class="flex gap-2">
                            @csrf
                            <input type="number" name="min_price"
                                class="flex-1 px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-teal-300"
                                value="{{ $product->min_price }}"
                                placeholder="Harga Min">
                            <button type="submit" class="px-2 py-1 bg-teal-600 text-white text-xs rounded hover:bg-teal-700">
                                Update
                            </button>
                        </form>

                        {{-- Min Quantity --}}
                        <form method="POST" action="{{ url('/admin/products/detail/' . $product->id . '/min-buying-stock') }}" class="flex gap-2">
                            @csrf
                            <input type="number" name="min_buying_stock"
                                class="flex-1 px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-teal-300"
                                value="{{ $product->min_buying_stock ?? 1 }}"
                                placeholder="Min Qty">
                            <button type="submit" class="px-2 py-1 bg-teal-600 text-white text-xs rounded hover:bg-teal-700">
                                Update
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Action Button --}}
                <a href="{{ url('/admin/products/detail/' . $product->id) }}"
                    class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 border border-teal-600 text-teal-600 text-sm font-medium rounded-md hover:bg-teal-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    Detail & Edit
                </a>
            </div>
        </div>
    @endforeach
</div>

{{-- Empty State --}}
@if($products->isEmpty())
    <div class="text-center py-12">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-gray-400 mx-auto mb-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada produk</h3>
        <p class="text-gray-500 mb-4">Mulai dengan menambahkan produk pertama Anda.</p>
        <a href="{{ url('/admin/products/create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white font-medium rounded-md hover:bg-teal-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Produk
        </a>
    </div>
@endif

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection

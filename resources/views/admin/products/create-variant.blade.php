@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Breadcrumb --}}
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="hover:text-teal-600 transition">Dashboard</a>
            </li>
            <li class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mx-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
                <a href="{{ url('/admin/products') }}" class="hover:text-teal-600 transition">Kelola Produk</a>
            </li>
            <li class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mx-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
                <a href="{{ url('/admin/products/detail/' . $product->id) }}" class="hover:text-teal-600 transition">Edit: {{ $product->name }}</a>
            </li>
            <li class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mx-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
                <span class="text-gray-900 font-medium">Tambah Varian</span>
            </li>
        </ol>
    </nav>

    <h3 class="text-2xl font-bold text-gray-800 mb-6">Tambah Varian untuk {{ $product->name }}</h3>
    
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <form method="POST" class="space-y-4">
            @csrf
            
            {{-- Warna --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Warna Varian</label>
                <select name="color" class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300">
                    <option value="">Pilih Warna</option>
                    <option value="biru silver">Biru Silver</option>
                    <option value="biru polos">Biru Polos</option>
                    <option value="oranye silver">Oranye Silver</option>
                    <option value="oranye polos">Oranye Polos</option>
                    <option value="coklat polos">Coklat Polos</option>
                    <option value="coklat silver">Coklat Silver</option>
                    <option value="hijau silver">Hijau Silver</option>
                    <option value="hijau polos">Hijau Polos</option>
                    <option value="merah silver">Merah Silver</option>
                    <option value="merah polos">Merah Polos</option>
                </select>
            </div>

            {{-- Stok --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                <input type="number" name="stock" placeholder="Masukkan jumlah stok" min="0" 
                       class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300" required>
            </div>

            {{-- Tombol Submit --}}
            <div class="pt-4 flex gap-3">
                <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 rounded-md transition">
                    Tambah Varian
                </button>
                <a href="{{ url('/admin/products/detail/' . $product->id) }}" 
                   class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 rounded-md transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

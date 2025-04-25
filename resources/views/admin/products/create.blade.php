@extends('layouts.admin')

@section('content')
<div class="flex justify-center">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 max-w-xl w-full mt-6">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Tambah Produk Baru</h3>

        <form method="POST" class="space-y-4">
            @csrf

            {{-- Nama Produk --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                <input type="text" name="name" id="name" placeholder="Contoh: Terpal Gajah Surya"
                    class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" placeholder="Tulis deskripsi produk"
                    class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-teal-300"></textarea>
            </div>

            {{-- Gambar --}}
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">URL Gambar</label>
                <input type="text" name="image" id="image" placeholder="https://contoh.com/terpal.jpg"
                    class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            {{-- Harga --}}
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                <input type="number" name="price" id="price" placeholder="Contoh: 7500"
                    class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300" required>
            </div>

            {{-- Ukuran --}}
            <div>
                <label for="size" class="block text-sm font-medium text-gray-700 mb-1">Ukuran</label>
                <select name="size" id="size"
                    class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300" required>
                    <option selected disabled>Pilih Ukuran</option>
                    <option value="2x3">2x3</option>
                    <option value="3x4">3x4</option>
                    <option value="4x6">4x6</option>
                    <option value="6x8">6x8</option>
                </select>
            </div>

            {{-- Status Tampil --}}
            <div>
                <label for="live" class="block text-sm font-medium text-gray-700 mb-1">Status Tampil</label>
                <select name="live" id="live"
                    class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="1" selected>Tampil</option>
                    <option value="0">Tidak Tampil</option>
                </select>
            </div>

            {{-- Tombol Submit --}}
            <div class="pt-2">
                <button type="submit"
                    class="w-full bg-teal-600 text-white font-semibold py-2 rounded-md hover:bg-teal-700 transition">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

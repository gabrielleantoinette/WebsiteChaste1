@extends('layouts.admin')

@section('content')
<div class="flex justify-center px-4 sm:px-0">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 sm:p-5 lg:p-6 max-w-xl w-full mt-4 sm:mt-5 lg:mt-6">
        <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 text-center">Tambah Produk Baru</h3>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3 sm:space-y-4">
            @csrf

            {{-- Nama Produk --}}
            <div>
                <label for="name" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                <input type="text" name="name" id="name" placeholder="Contoh: Terpal Gajah Surya"
                    value="{{ old('name') }}"
                    class="w-full border border-teal-600 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300 @error('name') border-red-500 @enderror">
                @error('name')<p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="description" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" placeholder="Tulis deskripsi produk" rows="3"
                    class="w-full border border-teal-600 rounded-md px-3 sm:px-4 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-teal-300 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')<p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Gambar Produk --}}
            <div>
                <label for="image" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
                <input type="file" name="image" id="image" accept="image/*"
                    class="w-full text-xs sm:text-sm text-gray-600
                           file:py-1.5 sm:file:py-2 file:px-3 sm:file:px-4 file:rounded file:border-0
                           file:text-xs sm:file:text-sm file:font-semibold
                           file:bg-teal-100 file:text-teal-700
                           hover:file:bg-teal-200 focus:outline-none focus:ring-2 focus:ring-teal-300 @error('image') file:bg-red-100 file:text-red-700 @enderror">
                @error('image')<p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Harga --}}
            <div>
                <label for="price" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Harga</label>
                <input type="number" name="price" id="price" placeholder="Contoh: 7500"
                    value="{{ old('price') }}"
                    class="w-full border border-teal-600 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300 @error('price') border-red-500 @enderror" required>
                @error('price')<p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Ukuran --}}
            <div>
                <label for="size" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Ukuran</label>
                <select name="size" id="size"
                    class="w-full border border-teal-600 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300 @error('size') border-red-500 @enderror" required>
                    <option value="" disabled {{ old('size') ? '' : 'selected' }}>Pilih Ukuran</option>
                    <option value="2x3" {{ old('size')=='2x3'?'selected':'' }}>2x3</option>
                    <option value="3x4" {{ old('size')=='3x4'?'selected':'' }}>3x4</option>
                    <option value="4x6" {{ old('size')=='4x6'?'selected':'' }}>4x6</option>
                    <option value="6x8" {{ old('size')=='6x8'?'selected':'' }}>6x8</option>
                </select>
                @error('size')<p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Status Tampil --}}
            <div>
                <label for="live" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status Tampil</label>
                <select name="live" id="live"
                    class="w-full border border-teal-600 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300 @error('live') border-red-500 @enderror">
                    <option value="1" {{ old('live', '1')=='1'?'selected':'' }}>Tampil</option>
                    <option value="0" {{ old('live')=='0'?'selected':'' }}>Tidak Tampil</option>
                </select>
                @error('live')<p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Tombol Submit --}}
            <div class="pt-2">
                <button type="submit"
                    class="w-full bg-teal-600 text-white text-sm sm:text-base font-semibold py-2 sm:py-2.5 rounded-md hover:bg-teal-700 transition">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

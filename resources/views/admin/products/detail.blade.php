@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto">
  <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Produk: {{ $product->name }}</h2>

  <form method="POST" class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-4">
    @csrf

    {{-- Nama Produk --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
      <input type="text" name="name" class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300" value="{{ $product->name }}">
    </div>

    {{-- Deskripsi --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
      <textarea name="description" rows="3" class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm resize-none focus:ring-2 focus:ring-teal-300">{{ $product->description }}</textarea>
    </div>

    {{-- Gambar --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">URL Gambar</label>
      <input type="text" name="image" class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300" value="{{ $product->image }}">
    </div>

    {{-- Harga --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
      <input type="number" name="price" required class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300" value="{{ $product->price }}">
    </div>

    {{-- Ukuran --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran</label>
      <select name="size" required class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300">
        <option disabled>Pilih Ukuran</option>
        <option value="2x3" {{ $product->size == '2x3' ? 'selected' : '' }}>2x3</option>
        <option value="3x4" {{ $product->size == '3x4' ? 'selected' : '' }}>3x4</option>
        <option value="4x6" {{ $product->size == '4x6' ? 'selected' : '' }}>4x6</option>
        <option value="6x8" {{ $product->size == '6x8' ? 'selected' : '' }}>6x8</option>
      </select>
    </div>

    {{-- Tampilkan atau Tidak --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Status Tampilkan</label>
      <select name="live" class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300">
        <option value="1" {{ $product->live ? 'selected' : '' }}>Tampil</option>
        <option value="0" {{ !$product->live ? 'selected' : '' }}>Tidak Tampil</option>
      </select>
    </div>

    {{-- Tombol Update --}}
    <div class="pt-2">
      <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 rounded-md transition">
        Update Produk
      </button>
    </div>
  </form>

  {{-- Bagian Varian --}}
  <div class="mt-10 flex justify-between items-center">
    <h3 class="text-xl font-bold text-gray-800">Varian Produk</h3>
    <a href="{{ url('/admin/products/detail/' . $product->id . '/variants/create') }}"
       class="bg-teal-600 hover:bg-teal-700 text-white text-sm px-4 py-2 rounded-md transition">
      + Tambah Varian
    </a>
  </div>

  <div class="mt-4 overflow-x-auto">
    <table class="w-full table-auto border text-sm">
      <thead class="bg-gray-100 text-gray-700 font-semibold">
        <tr>
          <th class="px-4 py-2 text-left">Warna</th>
          <th class="px-4 py-2 text-left">Stok</th>
          <th class="px-4 py-2 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($product->variants as $variant)
          <tr class="border-t">
            <td class="px-4 py-2">{{ $variant->color }}</td>
            <td class="px-4 py-2">{{ $variant->stock }}</td>
            <td class="px-4 py-2 text-center">
              <a href="{{ url('/admin/products/variants/edit/' . $variant->id) }}"
                class="bg-white border border-teal-600 text-teal-600 hover:bg-teal-50 text-xs px-3 py-1 rounded-md transition">
                Edit
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection

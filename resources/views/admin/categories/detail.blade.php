@extends('layouts.admin')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 text-green-800 px-3 sm:px-4 py-2 sm:py-3 rounded mb-3 sm:mb-4">
            <span class="text-xs sm:text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-0">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Kategori {{ $category->name }}</h1>
    </div>

    <div class="flex justify-end mb-4 sm:mb-5">
        <a href="{{ route('admin.categories.add.product', $category->id) }}"
            class="bg-blue-500 text-white px-3 sm:px-4 py-2 text-xs sm:text-sm rounded hover:bg-blue-600 transition w-full sm:w-auto text-center">
            Tambah Produk
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5 lg:p-6 shadow-sm">
        {{-- Desktop Table View --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="table-auto data-table text-sm min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Nama Produk</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-900">{{ $product->name }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.categories.remove.product', $category->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="bg-red-500 text-white px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm rounded hover:bg-red-600 transition">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        {{-- Mobile Card View --}}
        <div class="lg:hidden divide-y divide-gray-200">
            @forelse ($products as $product)
                <div class="p-4 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-center">
                        <p class="text-sm font-medium text-gray-900 flex-1 min-w-0 truncate mr-3">{{ $product->name }}</p>
                        <form action="{{ route('admin.categories.remove.product', $category->id) }}" method="POST" class="flex-shrink-0">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="bg-red-500 text-white px-3 py-2 text-xs sm:text-sm rounded hover:bg-red-600 transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">
                    <p class="text-sm">Tidak ada produk dalam kategori ini</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

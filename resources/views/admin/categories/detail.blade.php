@extends('layouts.admin')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Kategori {{ $category->name }}</h1>
    </div>

    <div class="flex justify-end mb-5">
        <a href="{{ route('admin.categories.add.product', $category->id) }}"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Tambah Produk
        </a>
    </div>

    <div class="">
        <table class="table-auto data-table text-sm">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>
                            <form action="{{ route('admin.categories.remove.product', $category->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

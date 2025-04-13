@extends('layouts.admin')

@section('content')
    <h3 class="text-2xl font-bold mb-5">Product Detail: {{ $product->name }}</h3>
    <form method="POST" class="flex flex-col gap-4">
        @csrf
        <input type="text" name="name" placeholder="name" class="input input-primary w-full" value="{{ $product->name }}">
        <textarea name="description" placeholder="description" class="textarea textarea-primary w-full">{{ $product->description }}</textarea>
        <input type="text" name="image" placeholder="image url" class="input input-primary w-full"
            value="{{ $product->image }}">
        <select name="live" class="select select-primary w-full">
            <option value="1" {{ $product->live ? 'selected' : '' }}>Tampil</option>
            <option value="0" {{ !$product->live ? 'selected' : '' }}>Tidak Tampil</option>
        </select>
        <button class="btn btn-primary">Update</button>
    </form>

    <div class="mt-10 flex justify-between">
        <h3 class="text-2xl font-bold">Product Variants</h3>
        <a href="{{ url('/admin/products/detail/' . $product->id . '/variants/create') }}" class="btn btn-primary">
            Add Variant
        </a>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ukuran</th>
                <th>Warna</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($product->variants as $variant)
                <tr>
                    <td>{{ $variant->size }}</td>
                    <td>{{ $variant->color }}</td>
                    <td>{{ $variant->price }}</td>
                    <td>{{ $variant->stock }}</td>
                    <td>
                        <a href="{{ url('/admin/products/variants/edit/' . $variant->id) }}"
                            class="btn btn-sm btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

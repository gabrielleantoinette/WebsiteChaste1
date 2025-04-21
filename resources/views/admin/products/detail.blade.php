@extends('layouts.admin')

@section('content')
    <h3 class="text-2xl font-bold mb-5">Product Detail: {{ $product->name }}</h3>
    <form method="POST" class="flex flex-col gap-4">
        @csrf
        <input type="text" name="name" placeholder="name" class="input input-primary w-full" value="{{ $product->name }}">
        <textarea name="description" placeholder="description" class="textarea textarea-primary w-full">{{ $product->description }}</textarea>
        <input type="text" name="image" placeholder="image url" class="input input-primary w-full"
            value="{{ $product->image }}">
        <input type="number" name="price" placeholder="harga" class="input input-primary w-full" required
            value="{{ $product->price }}">
        <select name="size" class="select select-primary w-full" required>
            <option selected disabled>Pilih Ukuran</option>
            <option value="2x3" {{ $product->size == '2x3' ? 'selected' : '' }}>2x3</option>
            <option value="3x4" {{ $product->size == '3x4' ? 'selected' : '' }}>3x4</option>
            <option value="4x6" {{ $product->size == '4x6' ? 'selected' : '' }}>4x6</option>
            <option value="6x8" {{ $product->size == '6x8' ? 'selected' : '' }}>6x8</option>
        </select>
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
                <th>Warna</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($product->variants as $variant)
                <tr>
                    <td>{{ $variant->color }}</td>
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

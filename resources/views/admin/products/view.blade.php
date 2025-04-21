@extends('layouts.admin')

@section('content')
    <div class="flex justify-between mb-5">
        <h1 class="text-xl font-bold mb-5">Product List</h1>
        <a href="{{ url('/admin/products/create') }}" class="btn btn-primary">Create</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <td>ID</td>
                <td>Name</td>
                <td>Description</td>
                <td>Image</td>
                <td>Price</td>
                <td>Size</td>
                <td>Live</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->description }}</td>
                    <td>{{ $product->image }}</td>
                    <td>Rp {{ number_format($product->price) }}</td>
                    <td>{{ $product->size }}</td>
                    <td>{{ $product->live ? 'Tampil' : 'Tidak Tampil' }}</td>
                    <td>
                        <a href="{{ url('/admin/products/detail/' . $product->id) }}"
                            class="btn btn-sm btn-primary">Detail</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between w-100">
        <h3>Daftar Produk</h3>
        <a href="{{ url('/admin/products/create') }}">
            <button class="btn btn-primary">Add Product</button>
        </a>
    </div>
    <table class="table mt-3">
        <thead>
            <tr>
                <td>ID</td>
                <td>Name</td>
                <td>Description</td>
                <td>Stock</td>
                <td>Price</td>
                <td>Image</td>
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
                    <td>{{ $product->stock }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->image }}</td>
                    <td>{{ $product->live }}</td>
                    <td><a href="{{ url('/admin/products/detail/' . $product->id) }}">Detail</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

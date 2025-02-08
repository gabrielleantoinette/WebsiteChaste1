@extends('layouts.admin')

@section('content')
    <div class="py-4 px-5">
        <div class="d-flex justify-content-end pb-3">
            <a>
                <button class="btn btn-primary">Add Product</button>
            </a>
        </div>
        <table class="table">
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
    </div>
@endsection

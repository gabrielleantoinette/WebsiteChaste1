@extends('layouts.admin')

@section('content')
    <h3>Product Detail: {{ $product->name }}</h3>
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
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->description }}</td>
                <td>{{ $product->stock }}</td>
                <td>{{ $product->price }}</td>
                <td>{{ $product->image }}</td>
                <td>{{ $product->live ? 'live' : 'hidden' }}</td>
            </tr>
        </tbody>
    </table>
@endsection

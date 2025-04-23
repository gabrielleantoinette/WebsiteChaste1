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
                @if (Session::get('user')->role == 'owner')
                    <td>Harga Minimum</td>
                @endif
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
                    @if (Session::get('user')->role == 'owner')
                        <td>
                            <form method="POST" action="{{ url('/admin/products/detail/' . $product->id . '/min-price') }}">
                                @csrf
                                <input type="number" name="min_price" class="input input-primary input-sm w-min"
                                    value="{{ $product->min_price }}">
                                <button class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </td>
                    @endif
                    <td>
                        <a href="{{ url('/admin/products/detail/' . $product->id) }}"
                            class="btn btn-sm btn-primary">Detail</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

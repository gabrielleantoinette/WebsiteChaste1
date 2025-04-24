@extends('layouts.admin')

@section('content')
    <div class="flex justify-between mb-5">
        <h1 class="text-xl font-bold mb-5">Product List</h1>
        <a href="{{ url('/admin/products/create') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-teal-600 text-teal-600 font-medium rounded-md hover:bg-teal-50 transition shadow-sm">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
         stroke-width="2" stroke="currentColor" class="w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
    </svg>
    Create
</a>
    </div>

    <table class="table table-bordered data-table">
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
                            class="inline-flex items-center gap-2 px-3 py-1.5 border border-teal-600 text-teal-600 text-sm font-medium rounded-md hover:bg-teal-50 transition shadow-sm">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Detail
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-bold mb-5">Create Invoice - Pilih Produk</h1>
    <form method="POST" action="{{ url('/admin/invoices/create-product') }}">
        @csrf
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>Rp {{ number_format($product->price) }}</td>
                        <td>
                            <select name="variant_id[]" class="select select-primary w-min">
                                @foreach ($product->variants as $variant)
                                    <option value="{{ $variant->id }}">{{ $variant->color }}</option>
                                @endforeach
                            </select>
                            <input type="number" name="quantity[]" value="0" class="input input-primary w-min">
                            <input type="hidden" name="product_id[]" value="{{ $product->id }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Pilih</button>
    </form>
@endsection

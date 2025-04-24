@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-bold mb-5">Create Invoice - Konfirmasi</h1>

    <div class="mb-5">
        <div class="text-lg font-bold">Data Pelanggan</div>
        <div>
            <div>Nama : {{ $customer->name }}</div>
            <div>Email : {{ $customer->email }}</div>
            <div>No. HP : {{ $customer->phone }}</div>
            <div>Alamat : {{ $customer->address }}</div>
        </div>
    </div>

    <div class="mb-5">
        <div class="text-lg font-bold mb-5">Barang Yang Di Beli</div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Warna</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>Rp {{ number_format($product->price) }}</td>
                        <td>{{ $product->variant->color }}</td>
                        <td>{{ $product->quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        <div class="text-lg font-bold">Data Invoice</div>
        <form method="POST" action="{{ url('/admin/invoices/create-confirmation') }}">
            @csrf
            <div class="space-y-3">
                <div>
                    <p>Tanggal Jatuh Tempo Pembayaran</p>
                    <input type="date" name="due_date" value="{{ date('Y-m-d') }}" class="input input-primary w-full">
                </div>
                <div>
                    <p>Tanggal Penerimaan Barang</p>
                    <input type="date" name="receive_date" value="{{ date('Y-m-d') }}"
                        class="input input-primary w-full">
                </div>
                <div>
                    <p>Alamat Pengiriman</p>
                    <input type="text" name="address" value="{{ $customer->address }}"
                        class="input input-primary w-full">
                </div>
            </div>
            <button class="btn btn-primary mt-2">Buat Invoice</button>
        </form>
    </div>
@endsection

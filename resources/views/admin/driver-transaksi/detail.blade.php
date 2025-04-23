@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-bold mb-5">Invoice Detail</h1>
    <div class="mt-5">
        <div class="flex flex-row gap-2">
            <p class="font-bold">Invoice ID:</p>
            <p>{{ $invoice->id }}</p>
        </div>
        <div class="flex flex-row gap-2">
            <p class="font-bold">Invoice Code:</p>
            <p>{{ $invoice->code }}</p>
        </div>
        <div class="flex flex-row gap-2">
            <p class="font-bold">Tanggal Jatuh Tempo:</p>
            <p>{{ $invoice->due_date }}</p>
        </div>
        <div class="flex flex-row gap-2">
            <p class="font-bold">Tanggal Penerimaan Barang:</p>
            <p>{{ $invoice->receive_date }}</p>
        </div>
    </div>
    <div class="mt-5">
        <div class="text-lg font-bold">Customer</div>
        <div class="flex flex-row gap-2">
            <p class="font-bold">Name:</p>
            <p>{{ $invoice->customer->name }}</p>
        </div>
        <div class="flex flex-row gap-2">
            <p class="font-bold">Phone:</p>
            <p>{{ $invoice->customer->phone }}</p>
        </div>
        <div class="flex flex-row gap-2">
            <p class="font-bold">Email:</p>
            <p>{{ $invoice->customer->email }}</p>
        </div>
    </div>
    <div class="mt-5">
        <div class="text-lg font-bold">Staff Gudang Yang Menyiapkan Barang</div>
        @if ($invoice->gudang)
            <div class="flex flex-row gap-2">
                <p class="font-bold">Name:</p>
                <p>{{ $invoice->gudang->name }}</p>
            </div>
            <div class="flex flex-row gap-2">
                <p class="font-bold">Email:</p>
                <p>{{ $invoice->gudang->email }}</p>
            </div>
            <div class="flex flex-row gap-2">
                <p class="font-bold">Aktif:</p>
                <p>{{ $invoice->gudang->active ? 'Aktif' : 'Tidak Aktif' }}</p>
            </div>
        @else
            <div>
                <p>Belum ada staff gudang yang menyiapkan barang</p>
                <form action="{{ url('/admin/gudang-transaksi/assign-gudang/' . $invoice->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary">Siapkan Barang</button>
                </form>
            </div>
        @endif
    </div>
    <div class="mt-5">
        <div class="text-lg font-bold">Produk</div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Warna</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->details as $detail)
                    <tr>
                        <td>{{ $detail->id }}</td>
                        <td>{{ $detail->product->name }}</td>
                        <td>{{ $detail->price }}</td>
                        <td>{{ $detail->variant->color }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>{{ $detail->subtotal }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="text-lg font-bold">Total : Rp {{ number_format($invoice->grand_total) }}</div>
    </div>
@endsection

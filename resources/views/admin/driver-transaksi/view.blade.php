@extends('layouts.admin')

@section('content')
    <div class="flex justify-between mb-5">
        <h1 class="text-xl font-bold mb-5">Transaksi</h1>
        {{-- <a href="{{ url('/admin/customers/create') }}" class="btn btn-primary">Create</a> --}}
    </div>

    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <td>ID</td>
                <td>Kode</td>
                <td>Customer</td>
                <td>Driver</td>
                <td>Status</td>
                <td>Alamat</td>
                <td>Tanggal Diterima</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->id }}</td>
                    <td>{{ $invoice->code }}</td>
                    <td>{{ $invoice->customer->name }}</td>
                    <td>{{ $invoice->driver->name }}</td>
                    <td>{{ $invoice->status }}</td>
                    <td>{{ $invoice->address }}</td>
                    <td>{{ $invoice->receive_date }}</td>
                    <td>
                        <a href="{{ route('driver-transaksi.detail', $invoice->id) }}" class="btn btn-primary">
                            Detail
                        </a>                    
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

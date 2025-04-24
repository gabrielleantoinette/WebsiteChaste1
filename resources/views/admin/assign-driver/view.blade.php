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
                <td>Alamat Pengiriman</td>
                <td>Tanggal Diterima</td>
                <td>Status</td>
                <td>Driver</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->id }}</td>
                    <td>{{ $invoice->code }}</td>
                    <td>{{ $invoice->customer->name }}</td>
                    <td>{{ $invoice->address }}</td>
                    <td>{{ $invoice->receive_date }}</td>
                    <td>{{ $invoice->status }}</td>
                    <td>{{ $invoice->driver ? $invoice->driver->name : 'Belum ada driver' }}</td>
                    <td>
                        <form method="POST" action="{{ url('/admin/assign-driver/assign/' . $invoice->id) }}">
                            @csrf
                            <div class="flex gap-2">
                                <select name="driver_id" class="select select-primary">
                                    @foreach ($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-primary">Assign</button>
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

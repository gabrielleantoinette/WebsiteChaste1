@extends('layouts.admin')

@section('content')
    <div class="flex justify-between mb-5">
        <h1 class="text-xl font-bold mb-5">Invoices List</h1>
        @if (Session::get('user')->role !== 'owner')
        <a href="{{ url('/admin/invoices/create-customer') }}"
            class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white font-medium text-sm px-5 py-2.5 rounded-lg shadow-sm transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Transaksi Toko
        </a>
        @endif
    </div>

    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <td>ID</td>
                <td>Code</td>
                <td>Customer</td>
                <td>Admin</td>
                <td>Driver</td>
                <td>Gudang</td>
                <td>Keuangan</td>
                <td>Grand Total</td>
                <td>Alamat Pengiriman</td>
                <td>Status</td>
                <td>Jenis Pembelian</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->id }}</td>
                    <td>{{ $invoice->code }}</td>
                    <td>{{ $invoice->customer->name }}</td>
                    <td>{{ $invoice->employee->name }}</td>
                    <td>{{ $invoice->driver ? $invoice->driver->name : '-' }}</td>
                    <td>{{ $invoice->gudang ? $invoice->gudang->name : '-' }}</td>
                    <td>{{ $invoice->accountant ? $invoice->accountant->name : '-' }}</td>
                    <td>Rp {{ number_format($invoice->grand_total) }}</td>
                    <td>{{ $invoice->address }}</td>
                    <td>{{ $invoice->status }}</td>
                    <td>{{ $invoice->is_online ? 'Offline' : 'Online' }}</td>
                    <td>
                        <a href="{{ url('/admin/invoices/detail/' . $invoice->id) }}"  class="inline-flex items-center gap-2 px-3 py-1.5 border border-teal-600 text-teal-600 text-sm font-medium rounded-md hover:bg-teal-50 transition shadow-sm">
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

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
                    <td>{{ $invoice->receive_date }}</td>
                    <td>
                        <a href="{{ url('/admin/gudang-transaksi/detail/' . $invoice->id) }}"
                        class="inline-flex items-center gap-2 px-3 py-1.5 border border-teal-600 text-teal-600 text-sm font-medium rounded-md hover:bg-teal-50 transition shadow-sm">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>Detail</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

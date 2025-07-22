@extends('layouts.admin')

@section('content')
    <div class="flex justify-between mb-5">
        <h1 class="text-xl font-bold mb-5">Atur Kurir</h1>
    </div>

    <h2 class="text-lg font-semibold mb-3">Daftar Pengiriman Normal</h2>
    <table class="table table-bordered data-table mb-8">
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
            @foreach ($pengirimanNormal as $invoice)
                <tr>
                    <td>{{ $invoice->id }}</td>
                    <td>{{ $invoice->code }}</td>
                    <td>{{ $invoice->customer->name }}</td>
                    <td>{{ $invoice->address }}</td>
                    <td>{{ $invoice->receive_date }}</td>
                    <td>{{ $invoice->status }}</td>
                    <td>{{ $invoice->driver ? $invoice->driver->name : 'Belum ada driver' }}</td>
                    <td>
                        @if(!$invoice->driver)
                        <form method="POST" action="{{ url('/admin/assign-driver/assign/' . $invoice->id) }}">
                            @csrf
                            <div class="flex gap-2">
                            <select name="driver_id" class="w-full border border-teal-600 text-gray-800 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300 transition">
                                @foreach ($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                                <button class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 transition shadow-sm">
                                    Assign
                                </button>
                            </div>
                        </form>
                        @else
                            <button class="px-4 py-2 bg-gray-400 text-white rounded-md cursor-not-allowed" disabled>Sudah Diassign</button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2 class="text-lg font-semibold mb-3">Daftar Pengambilan Barang Retur</h2>
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
            @foreach ($pengambilanRetur as $invoice)
                <tr>
                    <td>{{ $invoice->id }}</td>
                    <td>{{ $invoice->code }}</td>
                    <td>{{ $invoice->customer->name }}</td>
                    <td>{{ $invoice->address }}</td>
                    <td>{{ $invoice->receive_date }}</td>
                    <td>{{ $invoice->status }}</td>
                    <td>{{ $invoice->driver ? $invoice->driver->name : 'Belum ada driver' }}</td>
                    <td>
                        @if(!$invoice->driver)
                        <form method="POST" action="{{ url('/admin/assign-driver/assign/' . $invoice->id) }}">
                            @csrf
                            <div class="flex gap-2">
                            <select name="driver_id" class="w-full border border-teal-600 text-gray-800 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300 transition">
                                @foreach ($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                                <button class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 transition shadow-sm">
                                    Assign
                                </button>
                            </div>
                        </form>
                        @else
                            <button class="px-4 py-2 bg-gray-400 text-white rounded-md cursor-not-allowed" disabled>Sudah Diassign</button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

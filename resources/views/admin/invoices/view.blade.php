@extends('layouts.admin')

@section('content')
    <div class="flex justify-between mb-5">
        <h1 class="text-xl font-bold mb-5">Daftar Penjualan</h1>
        @if (Session::get('user')->role !== 'owner')
        <div class="flex items-center gap-2">
            @if (Session::get('user')->role !== 'owner')
                <a href="{{ url('/admin/invoices/create-customer') }}"
                   class="bg-teal-600 hover:bg-teal-700 text-white font-medium px-4 py-2 rounded-md transition">
                    + Tambah Transaksi Toko
                </a>
            @endif
    
            <a href="{{ route('invoices.export.pdf') }}"
               class="bg-teal-600 hover:bg-teal-700 text-white font-medium px-4 py-2 rounded-md transition shadow-sm">
                📄 Export PDF
            </a>
        </div>

        @endif
        <div class="relative inline-block text-left">
            <button id="exportDropdownBtn"
                    class="inline-flex justify-center px-4 py-2 bg-teal-600 text-white font-medium rounded-md hover:bg-teal-700 transition shadow-sm">
                📄 Export Laporan
                <svg class="w-4 h-4 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06-.02L10 10.939l3.71-3.75a.75.75 0 111.08 1.04l-4.24 4.28a.75.75 0 01-1.08 0L5.23 8.27a.75.75 0 01-.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>
            <div id="exportDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-md shadow-md z-50">
                <a href="{{ route('laporan.penjualan.pdf') }}"
                class="block px-4 py-2 text-sm hover:bg-gray-100">📦 Laporan Penjualan</a>
                <a href="{{ route('laporan.retur.pdf') }}"
                class="block px-4 py-2 text-sm hover:bg-gray-100">↩️ Laporan Barang Retur</a>
                <a href="{{ route('laporan.ratarata.pdf') }}"
                class="block px-4 py-2 text-sm hover:bg-gray-100">📊 Laporan Rata-Rata Pesanan</a>
            </div>
        </div>
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
    <script>
        const dropdownBtn = document.getElementById('exportDropdownBtn');
        const dropdownMenu = document.getElementById('exportDropdown');
        dropdownBtn.addEventListener('click', () => dropdownMenu.classList.toggle('hidden'));
    </script>
@endsection

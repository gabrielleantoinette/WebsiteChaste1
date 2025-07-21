@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Transaksi Kurir</h1>
    </div>

    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto data-table text-sm border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-[#004d4d] text-gray-700 dark:text-[#ccf2f2]">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Driver</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Alamat</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal Diterima</th>
                        <th class="px-4 py-3 text-left font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoices as $invoice)
                        <tr class="border-b border-gray-100 dark:border-gray-700 @if($loop->odd) bg-gray-50 dark:bg-[#2c2c2c] @endif hover:bg-teal-50 dark:hover:bg-[#003333] transition">
                            <td class="px-4 py-3">{{ $invoice->id }}</td>
                            <td class="px-4 py-3 font-mono text-teal-700 dark:text-[#80C0CE] font-semibold">{{ $invoice->code }}</td>
                            <td class="px-4 py-3">{{ $invoice->customer->name }}</td>
                            <td class="px-4 py-3">{{ $invoice->driver->name }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-teal-100 dark:bg-[#004d4d] text-teal-700 dark:text-[#ccf2f2]">{{ ucfirst($invoice->status) }}</span>
                            </td>
                            <td class="px-4 py-3">{{ $invoice->address }}</td>
                            <td class="px-4 py-3">{{ $invoice->receive_date }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('driver-transaksi.detail', $invoice->id) }}" class="inline-block px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded shadow transition font-semibold">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

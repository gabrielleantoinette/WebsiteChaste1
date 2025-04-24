@extends('layouts.admin')

@section('content')
    <div class="py-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

        {{-- Ringkasan Statistik --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                <p class="text-sm text-gray-500 mb-1">Total Produk</p>
                <p class="text-2xl font-bold text-teal-600">{{ $totalProducts ?? 0 }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                <p class="text-sm text-gray-500 mb-1">Total Pembeli</p>
                <p class="text-2xl font-bold text-teal-600">{{ $totalCustomers ?? 0 }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                <p class="text-sm text-gray-500 mb-1">Total Pegawai</p>
                <p class="text-2xl font-bold text-teal-600">{{ $totalEmployees ?? 0 }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                <p class="text-sm text-gray-500 mb-1">Total Penjualan</p>
                <p class="text-2xl font-bold text-teal-600">Rp {{ number_format($totalSales ?? 0) }}</p>
            </div>
        </div>

        {{-- Transaksi Terbaru --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Transaksi Terbaru</h2>
            <table class="w-full text-sm text-gray-700">
                <thead class="bg-[#D9F2F2] text-gray-800 font-medium">
                    <tr>
                        <th class="px-4 py-2 text-left">Invoice</th>
                        <th class="px-4 py-2 text-left">Customer</th>
                        <th class="px-4 py-2 text-left">Total</th>
                        <th class="px-4 py-2 text-left">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentInvoices ?? [] as $invoice)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $invoice->code }}</td>
                            <td class="px-4 py-2">{{ $invoice->customer->name }}</td>
                            <td class="px-4 py-2">Rp {{ number_format($invoice->grand_total) }}</td>
                            <td class="px-4 py-2">{{ $invoice->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

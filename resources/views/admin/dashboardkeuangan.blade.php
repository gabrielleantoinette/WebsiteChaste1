@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <h1 class="text-3xl font-bold mb-8 text-gray-800">Dashboard Keuangan</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-xl shadow border border-gray-100 flex items-center gap-4">
            <div class="bg-teal-100 text-teal-600 rounded-full p-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M9 16h6"/></svg>
            </div>
            <div>
                <div class="text-xs text-gray-500 mb-1">Total Transaksi Hari Ini</div>
                <div class="text-2xl font-bold text-teal-700">{{ $totalTransaksi }}</div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow border border-gray-100 flex items-center gap-4">
            <div class="bg-green-100 text-green-600 rounded-full p-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 0C7.582 4 4 7.582 4 12s3.582 8 8 8 8-3.582 8-8-3.582-8-8-8z"/></svg>
            </div>
            <div>
                <div class="text-xs text-gray-500 mb-1">Total Pemasukan Hari Ini</div>
                <div class="text-2xl font-bold text-green-700">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow border border-gray-100 flex items-center gap-4">
            <div class="bg-red-100 text-red-600 rounded-full p-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>
            </div>
            <div>
                <div class="text-xs text-gray-500 mb-1">Total Pengeluaran Hari Ini</div>
                <div class="text-2xl font-bold text-red-700">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 mt-8">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Reminder Hutang Jatuh Tempo <span class="font-normal text-gray-500">(3 Hari ke Depan)</span></h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden shadow-sm mt-2">
                <thead class="bg-teal-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-teal-700">Nama Customer</th>
                        <th class="px-4 py-2 text-left font-semibold text-teal-700">Kode Invoice</th>
                        <th class="px-4 py-2 text-left font-semibold text-teal-700">Jatuh Tempo</th>
                        <th class="px-4 py-2 text-left font-semibold text-teal-700">Sisa Hutang</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hutangJatuhTempo as $hutang)
                        <tr class="hover:bg-teal-50 transition">
                            <td class="px-4 py-2 border-b">{{ $hutang->customer->name ?? '-' }}</td>
                            <td class="px-4 py-2 border-b">{{ $hutang->code }}</td>
                            <td class="px-4 py-2 border-b">{{ \Carbon\Carbon::parse($hutang->due_date)->format('d-m-Y') }}</td>
                            <td class="px-4 py-2 border-b font-bold text-red-600">Rp {{ number_format($hutang->grand_total - ($hutang->paid_amount ?? 0), 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">Tidak ada hutang yang jatuh tempo dalam 3 hari ke depan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 
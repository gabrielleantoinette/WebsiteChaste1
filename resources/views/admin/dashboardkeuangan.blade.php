@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto py-4 sm:py-6 lg:py-8 px-4 sm:px-6 lg:px-0">
    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-4 sm:mb-6 lg:mb-8 text-gray-800">Dashboard Keuangan</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 lg:gap-6 mb-6 sm:mb-8 lg:mb-10">
        <div class="bg-white p-4 sm:p-5 lg:p-6 rounded-xl shadow border border-gray-100 flex items-center gap-3 sm:gap-4">
            <div class="bg-teal-100 text-teal-600 rounded-full p-2 sm:p-3 flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M9 16h6"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs text-gray-500 mb-1">Total Transaksi Hari Ini</div>
                <div class="text-xl sm:text-2xl font-bold text-teal-700 truncate">{{ $totalTransaksi }}</div>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-5 lg:p-6 rounded-xl shadow border border-gray-100 flex items-center gap-3 sm:gap-4">
            <div class="bg-green-100 text-green-600 rounded-full p-2 sm:p-3 flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 0C7.582 4 4 7.582 4 12s3.582 8 8 8 8-3.582 8-8-3.582-8-8-8z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs text-gray-500 mb-1">Total Pemasukan Hari Ini</div>
                <div class="text-lg sm:text-xl lg:text-2xl font-bold text-green-700 truncate">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-5 lg:p-6 rounded-xl shadow border border-gray-100 flex items-center gap-3 sm:gap-4">
            <div class="bg-red-100 text-red-600 rounded-full p-2 sm:p-3 flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs text-gray-500 mb-1">Total Pengeluaran Hari Ini</div>
                <div class="text-lg sm:text-xl lg:text-2xl font-bold text-red-700 truncate">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-5 lg:p-6 rounded-xl shadow border border-gray-100 flex items-center gap-3 sm:gap-4">
            <div class="bg-orange-100 text-orange-600 rounded-full p-2 sm:p-3 flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs text-gray-500 mb-1">Butuh Konfirmasi</div>
                <div class="text-xl sm:text-2xl font-bold text-orange-700 truncate">{{ $totalButuhKonfirmasi }}</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4 sm:p-5 lg:p-6 mt-4 sm:mt-6 lg:mt-8 mb-4 sm:mb-6 lg:mb-8">
        <h2 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800">Pembayaran yang Butuh Dikonfirmasi</h2>
        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden shadow-sm mt-2">
                <thead class="bg-orange-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-orange-700">Kode Invoice</th>
                        <th class="px-4 py-2 text-left font-semibold text-orange-700">Nama Customer</th>
                        <th class="px-4 py-2 text-left font-semibold text-orange-700">Tanggal</th>
                        <th class="px-4 py-2 text-left font-semibold text-orange-700">Total</th>
                        <th class="px-4 py-2 text-left font-semibold text-orange-700">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembayaranButuhKonfirmasi as $invoice)
                        <tr class="hover:bg-orange-50 transition">
                            <td class="px-4 py-2 border-b font-mono text-teal-700 font-semibold">{{ $invoice->code }}</td>
                            <td class="px-4 py-2 border-b">{{ $invoice->customer->name ?? '-' }}</td>
                            <td class="px-4 py-2 border-b">{{ \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y H:i') }}</td>
                            <td class="px-4 py-2 border-b font-bold text-green-700">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 border-b">
                                <a href="{{ route('keuangan.detail', $invoice->id) }}" 
                                   class="inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-teal-600 text-white text-xs sm:text-sm font-semibold rounded-md shadow-sm hover:bg-teal-700 transition">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Tidak ada pembayaran yang butuh dikonfirmasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200 mt-2">
            @forelse($pembayaranButuhKonfirmasi as $invoice)
                <div class="p-3 sm:p-4 hover:bg-orange-50 transition">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-mono font-semibold text-teal-700 truncate">{{ $invoice->code }}</p>
                            <p class="text-sm text-gray-800 truncate">{{ $invoice->customer->name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y H:i') }}</p>
                        <p class="text-sm font-bold text-green-700">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</p>
                    </div>
                    <a href="{{ route('keuangan.detail', $invoice->id) }}" 
                       class="inline-flex items-center gap-2 px-3 py-2 bg-teal-600 text-white text-xs font-semibold rounded-md shadow-sm hover:bg-teal-700 transition w-full justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Detail
                    </a>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">Tidak ada pembayaran yang butuh dikonfirmasi.</div>
            @endforelse
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4 sm:p-5 lg:p-6 mt-4 sm:mt-6 lg:mt-8">
        <h2 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800">Reminder Hutang Jatuh Tempo <span class="font-normal text-sm text-gray-500">(3 Hari ke Depan)</span></h2>
        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
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
        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200 mt-2">
            @forelse($hutangJatuhTempo as $hutang)
                <div class="p-3 sm:p-4 hover:bg-teal-50 transition">
                    <div class="mb-2">
                        <p class="text-sm font-medium text-gray-800">{{ $hutang->customer->name ?? '-' }}</p>
                        <p class="text-xs text-gray-600 font-mono">{{ $hutang->code }}</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <p class="text-xs text-gray-600">Jatuh Tempo: {{ \Carbon\Carbon::parse($hutang->due_date)->format('d-m-Y') }}</p>
                        <p class="text-sm font-bold text-red-600">Rp {{ number_format($hutang->grand_total - ($hutang->paid_amount ?? 0), 0, ',', '.') }}</p>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">Tidak ada hutang yang jatuh tempo dalam 3 hari ke depan.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection 
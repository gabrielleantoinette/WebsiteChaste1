@extends('layouts.admin')

@section('content')
    {{-- Header dengan Gradient Background --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl mb-4 sm:mb-6 lg:mb-8">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-4 sm:px-6 lg:px-8 py-4 sm:py-5 lg:py-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mb-1 sm:mb-2">📦 Transaksi Gudang</h1>
                    <p class="text-sm sm:text-base text-teal-100">Manajemen transaksi dan pesanan yang perlu diproses</p>
                </div>
                <div class="flex gap-2 sm:gap-3 w-full sm:w-auto">
                    <a href="{{ route('gudang.dashboard') }}" 
                       class="inline-flex items-center gap-2 px-4 sm:px-6 py-2 sm:py-3 bg-white/20 backdrop-blur-sm text-white text-sm sm:text-base font-medium rounded-lg hover:bg-white/30 transition-all duration-300 shadow-lg border border-white/20 w-full sm:w-auto justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                             stroke-width="2" stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Transaksi Gudang --}}
    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg">
        <h2 class="text-base sm:text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3 sm:mb-4">Daftar Transaksi yang Perlu Diproses</h2>
        {{-- Desktop Table View --}}
        <div class="hidden lg:block overflow-x-auto">
            <table id="datatable-gudang" class="min-w-full table-auto data-table text-sm border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-[#004d4d] text-gray-700 dark:text-[#ccf2f2]">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal Diterima</th>
                        <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        <tr class="border-b border-gray-100 dark:border-gray-700 @if($loop->odd) bg-gray-50 dark:bg-[#2c2c2c] @endif hover:bg-teal-50 dark:hover:bg-[#003333] transition">
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $invoice->id }}</td>
                            <td class="px-4 py-3 font-mono text-teal-700 dark:text-[#80C0CE] font-semibold">{{ $invoice->code }}</td>
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $invoice->customer->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                @if($invoice->receive_date)
                                    {{ \Carbon\Carbon::parse($invoice->receive_date)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ url('/admin/gudang-transaksi/detail/' . $invoice->id) }}"
                                   class="inline-block px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white rounded-md text-xs font-semibold transition shadow-sm">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada transaksi yang perlu diproses</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Transaksi yang perlu diproses akan muncul di sini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Mobile Card View --}}
        <div class="lg:hidden divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($invoices as $invoice)
                <div class="p-4 hover:bg-teal-50 dark:hover:bg-[#003333] transition">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-mono font-semibold text-teal-700 dark:text-[#80C0CE] truncate">{{ $invoice->code }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">ID: {{ $invoice->id }}</p>
                        </div>
                    </div>
                    <div class="space-y-2 mb-3 text-xs sm:text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Customer:</span>
                            <span class="text-gray-900 dark:text-gray-200 font-medium truncate ml-2">{{ $invoice->customer->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Tanggal Diterima:</span>
                            <span class="text-gray-900 dark:text-gray-200">
                                @if($invoice->receive_date)
                                    {{ \Carbon\Carbon::parse($invoice->receive_date)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </div>
                    <a href="{{ url('/admin/gudang-transaksi/detail/' . $invoice->id) }}"
                       class="inline-block w-full text-center px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white rounded-md text-xs sm:text-sm font-semibold transition shadow-sm">
                        Detail
                    </a>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-base font-medium">Tidak ada transaksi yang perlu diproses</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Transaksi yang perlu diproses akan muncul di sini</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@push('scripts')
<script>
    $(document).ready(function() {
        $('#datatable-gudang').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Tidak ada data",
                zeroRecords: "Tidak ada transaksi yang perlu diproses",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }
            },
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            responsive: true,
            order: []
        });
    });
</script>
@endpush
@endsection

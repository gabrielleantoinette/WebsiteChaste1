@extends('layouts.admin')

@section('content')
    {{-- Header dengan Gradient Background --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl mb-4 sm:mb-6 lg:mb-8">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-4 sm:px-6 lg:px-8 py-4 sm:py-5 lg:py-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mb-1 sm:mb-2">🔄 Kelola Retur</h1>
                    <p class="text-sm sm:text-base text-teal-100">Manajemen permintaan retur dan pengembalian barang</p>
                </div>
                <div class="flex gap-3 w-full sm:w-auto">
                    <a href="{{ route('admin.retur.damaged-products') }}" 
                       class="inline-flex items-center gap-2 px-4 sm:px-6 py-2 sm:py-3 bg-white/20 backdrop-blur-sm text-white text-sm sm:text-base font-medium rounded-lg hover:bg-white/30 transition-all duration-300 shadow-lg border border-white/20 w-full sm:w-auto justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                             stroke-width="2" stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Barang Rusak
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan Statistik dengan Card Modern --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 lg:gap-6 mb-4 sm:mb-6 lg:mb-8">
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Total Retur</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 truncate">{{ $returs->count() }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Diajukan</p>
                    <p class="text-xl sm:text-2xl font-bold text-yellow-600 dark:text-yellow-400 truncate">{{ $returs->where('status', 'diajukan')->count() }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Diproses</p>
                    <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 truncate">{{ $returs->where('status', 'diproses')->count() }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Selesai</p>
                    <p class="text-xl sm:text-2xl font-bold text-green-600 dark:text-green-400 truncate">{{ $returs->where('status', 'selesai')->count() }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-green-100 dark:bg-green-900/30 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Daftar Retur --}}
    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg">
        <h2 class="text-base sm:text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3 sm:mb-4">Daftar Permintaan Retur</h2>
        {{-- Desktop Table View --}}
        <div class="hidden lg:block overflow-x-auto">
            <table id="retur-table" class="min-w-full table-auto data-table text-sm border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-[#004d4d] text-gray-700 dark:text-[#ccf2f2]">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">#</th>
                        <th class="px-4 py-3 text-left font-semibold">Nomor Invoice</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Alasan Retur</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returs as $retur)
                    <tr class="border-b border-gray-100 dark:border-gray-700 @if($loop->odd) bg-gray-50 dark:bg-[#2c2c2c] @endif hover:bg-teal-50 dark:hover:bg-[#003333] transition">
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-mono text-teal-700 dark:text-[#80C0CE] font-semibold">{{ $retur->invoice->code ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $retur->customer->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ Str::limit($retur->description, 50) }}</td>
                        <td class="px-4 py-3">
                            @if($retur->status == 'diajukan')
                                <span class="inline-block px-2 sm:px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300">
                                    Diajukan
                                </span>
                            @elseif($retur->status == 'diproses')
                                <span class="inline-block px-2 sm:px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                    Diproses
                                </span>
                            @elseif($retur->status == 'selesai')
                                <span class="inline-block px-2 sm:px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                    Selesai
                                </span>
                            @else
                                <span class="inline-block px-2 sm:px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    {{ ucfirst($retur->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.retur.detail', $retur->id) }}" 
                               class="inline-block px-3 sm:px-4 py-1.5 sm:py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-xs font-semibold transition shadow-sm">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 dark:text-gray-400 py-8">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <p class="text-base sm:text-lg font-medium">Tidak ada permintaan retur</p>
                                <p class="text-xs sm:text-sm text-gray-400 dark:text-gray-500 mt-1">Permintaan retur akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Mobile Card View --}}
        <div class="lg:hidden divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($returs as $retur)
                <div class="p-3 sm:p-4 hover:bg-teal-50 dark:hover:bg-[#003333] transition">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-mono font-semibold text-teal-700 dark:text-[#80C0CE] truncate">{{ $retur->invoice->code ?? '-' }}</p>
                            <p class="text-sm text-gray-800 dark:text-gray-200 truncate">{{ $retur->customer->name ?? '-' }}</p>
                        </div>
                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold ml-2 flex-shrink-0
                            @if($retur->status == 'diajukan') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300
                            @elseif($retur->status == 'diproses') bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300
                            @elseif($retur->status == 'selesai') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300
                            @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                            @endif">
                            {{ ucfirst($retur->status) }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Alasan Retur:</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 break-words">{{ Str::limit($retur->description, 100) }}</p>
                    </div>
                    <a href="{{ route('admin.retur.detail', $retur->id) }}" 
                       class="inline-block w-full text-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-xs sm:text-sm font-semibold transition shadow-sm mt-2">
                        Lihat Detail
                    </a>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <p class="text-base font-medium">Tidak ada permintaan retur</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Permintaan retur akan muncul di sini</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@push('scripts')
<script>
    $(function() {
        $('#retur-table').DataTable({
            order: [],
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Tidak ada data",
                zeroRecords: "Tidak ada permintaan retur.",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                },
            },
            responsive: true,
            pageLength: 10
        });
    });
</script>
@endpush
@endsection 
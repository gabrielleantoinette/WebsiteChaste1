@extends('layouts.admin')

@section('title', 'Laporan Transaksi Keuangan')

@section('content')
<div class="p-4 sm:p-5 lg:p-6">
    <h1 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-teal-600">Laporan Transaksi</h1>

    {{-- Filter Waktu --}}
    <div class="mb-4 sm:mb-6">
        <form method="GET" action="{{ route('keuangan.view') }}"
              class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    
            {{-- KIRI: Filter Waktu --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3">
                <label for="filter" class="text-xs sm:text-sm font-medium text-gray-700">Filter Waktu:</label>
                <select name="filter" id="filter"
                        onchange="this.form.submit()"
                        class="w-full sm:w-auto text-sm px-3 py-2 rounded-md border border-gray-300 focus:ring-teal-500 focus:border-teal-500 transition">
                    <option value="hari" {{ request('filter') == 'hari' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="minggu" {{ request('filter') == 'minggu' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="bulan" {{ request('filter') == 'bulan' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="tahun" {{ request('filter') == 'tahun' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </div>
    
            {{-- KANAN: Search + Export --}}
            <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                <input type="text" name="search"
                       placeholder="Cari kode transaksi"
                       value="{{ request('search') }}"
                       class="flex-1 sm:flex-none w-full sm:w-[180px] md:w-[250px] px-3 sm:px-4 py-2 rounded-md border border-gray-300 focus:ring-teal-500 focus:border-teal-500 text-sm" />
    
                <button type="submit"
                        class="px-3 sm:px-4 py-2 bg-teal-600 text-white text-sm rounded-md hover:bg-teal-700 transition shrink-0 w-full sm:w-auto">
                    Cari
                </button>
    
                @if(request('search'))
                    <a href="{{ route('keuangan.view', ['filter' => request('filter')]) }}"
                       class="px-3 py-2 bg-gray-200 text-sm rounded-md hover:bg-gray-300 transition text-gray-700 w-full sm:w-auto text-center">
                        ✕ Reset
                    </a>
                @endif
    
            </div>
        </form>
    </div>    
           
    
    {{-- Tabel Transaksi --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <h2 class="text-base sm:text-xl font-semibold px-4 sm:px-6 py-3 sm:py-4 bg-teal-50 text-teal-700 border-b">Daftar Transaksi</h2>
        {{-- Desktop Table View --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full table-auto text-sm border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">Tanggal</th>
                        <th class="px-6 py-3 text-left font-semibold">Kode Transaksi</th>
                        <th class="px-6 py-3 text-left font-semibold">Jumlah</th>
                        <th class="px-6 py-3 text-left font-semibold">Status</th>
                        <th class="px-6 py-3 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksi as $trx)
                        <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-teal-50 transition">
                            <td class="px-6 py-4">{{ $trx->receive_date ? \Carbon\Carbon::parse($trx->receive_date)->format('d M Y') : ($trx->created_at ? \Carbon\Carbon::parse($trx->created_at)->format('d M Y') : '-') }}</td>
                            <td class="px-6 py-4 font-mono text-teal-700 font-semibold">{{ $trx->code }}</td>
                            <td class="px-6 py-4 font-semibold">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                    @if(strtolower($trx->status) == 'dikemas') bg-teal-100 text-teal-700
                                    @elseif(strtolower($trx->status) == 'menunggu konfirmasi pembayaran') bg-yellow-100 text-yellow-700
                                    @elseif(strtolower($trx->status) == 'pembayaran ditolak') bg-red-100 text-red-700
                                    @elseif(strtolower($trx->status) == 'selesai') bg-green-100 text-green-700
                                    @else bg-gray-200 text-gray-700 @endif">
                                    {{ ucfirst($trx->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('keuangan.detail', $trx->id) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white text-sm font-semibold rounded-md shadow-sm hover:bg-teal-700 transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-500 py-4">Tidak ada transaksi ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Mobile Card View --}}
        <div class="lg:hidden divide-y divide-gray-100">
            @forelse ($transaksi as $trx)
                <div class="p-4 hover:bg-teal-50 transition">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-mono font-semibold text-teal-700 truncate">{{ $trx->code }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ $trx->receive_date ? \Carbon\Carbon::parse($trx->receive_date)->format('d M Y') : ($trx->created_at ? \Carbon\Carbon::parse($trx->created_at)->format('d M Y') : '-') }}</p>
                        </div>
                        <span class="inline-block px-2 sm:px-3 py-1 rounded-full text-xs font-semibold ml-2 flex-shrink-0
                            @if(strtolower($trx->status) == 'dikemas') bg-teal-100 text-teal-700
                            @elseif(strtolower($trx->status) == 'menunggu konfirmasi pembayaran') bg-yellow-100 text-yellow-700
                            @elseif(strtolower($trx->status) == 'pembayaran ditolak') bg-red-100 text-red-700
                            @elseif(strtolower($trx->status) == 'selesai') bg-green-100 text-green-700
                            @else bg-gray-200 text-gray-700 @endif">
                            {{ ucfirst($trx->status) }}
                        </span>
                    </div>
                    <div class="space-y-2 mb-3 text-xs sm:text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jumlah:</span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('keuangan.detail', $trx->id) }}"
                       class="inline-block w-full text-center px-4 py-2 bg-teal-600 text-white text-sm font-semibold rounded-md shadow-sm hover:bg-teal-700 transition">
                        Detail
                    </a>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">
                    <p class="text-sm">Tidak ada transaksi ditemukan.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

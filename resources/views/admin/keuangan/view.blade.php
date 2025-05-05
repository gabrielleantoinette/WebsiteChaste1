@extends('layouts.admin')

@section('title', 'Laporan Transaksi Keuangan')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6 text-teal-600">Laporan Transaksi</h1>

    {{-- Filter Waktu --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('keuangan.view') }}"
              class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    
            {{-- KIRI: Filter Waktu --}}
            <div class="flex items-center gap-2">
                <label for="filter" class="text-sm font-medium text-gray-700">Filter Waktu:</label>
                <select name="filter" id="filter"
                        onchange="this.form.submit()"
                        class="text-sm px-3 py-2 rounded-md border border-gray-300 focus:ring-teal-500 focus:border-teal-500 transition">
                    <option value="hari" {{ request('filter') == 'hari' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="minggu" {{ request('filter') == 'minggu' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="bulan" {{ request('filter') == 'bulan' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="tahun" {{ request('filter') == 'tahun' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </div>
    
            {{-- KANAN: Search + Export --}}
            <div class="flex flex-wrap items-center gap-2 justify-end">
                <input type="text" name="search"
                       placeholder="Cari kode transaksi"
                       value="{{ request('search') }}"
                       class="w-[180px] md:w-[250px] px-4 py-2 rounded-md border border-gray-300 focus:ring-teal-500 focus:border-teal-500 text-sm" />
    
                <button type="submit"
                        class="px-4 py-2 bg-teal-600 text-white text-sm rounded-md hover:bg-teal-700 transition shrink-0">
                    Cari
                </button>
    
                @if(request('search'))
                    <a href="{{ route('keuangan.view', ['filter' => request('filter')]) }}"
                       class="px-3 py-2 bg-gray-200 text-sm rounded-md hover:bg-gray-300 transition text-gray-700">
                        ✕
                    </a>
                @endif
    
                <a href="{{ route('keuangan.export.pdf', request()->only('filter', 'search')) }}"
                   class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 transition shrink-0">
                    📄 Export PDF
                </a>
            </div>
        </form>
    </div>    
           
    
    {{-- Tabel Transaksi --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <h2 class="text-xl font-semibold px-6 py-4 bg-teal-50 text-teal-700 border-b">Daftar Transaksi</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left">Tanggal</th>
                        <th class="px-6 py-3 text-left">Kode Transaksi</th>
                        <th class="px-6 py-3 text-left">Jumlah</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksi as $trx)
                        <tr>
                            <td class="px-6 py-4">
                                {{ $trx->created_at ? \Carbon\Carbon::parse($trx->created_at)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4">{{ $trx->code }}</td>
                            <td class="px-6 py-4">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ ucfirst($trx->status) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-500 py-4">Tidak ada transaksi ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

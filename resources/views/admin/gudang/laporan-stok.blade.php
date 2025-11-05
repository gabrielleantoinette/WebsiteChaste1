@extends('layouts.admin')

@section('content')
<div class="py-6">
    {{-- Header dengan Gradient Background --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl mb-8">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">📊 Laporan Stok {{ ucfirst($periode) }}</h1>
                    <p class="text-teal-100">Laporan lengkap pergerakan stok dalam periode yang dipilih</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('gudang.dashboard') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm text-white font-medium rounded-lg hover:bg-white/30 transition-all duration-300 shadow-lg border border-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                             stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Kembali ke Dashboard
                    </a>
                    <a href="{{ route('gudang.stok-barang') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm text-white font-medium rounded-lg hover:bg-white/30 transition-all duration-300 shadow-lg border border-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                             stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5m-16.5 0v-1.125c0-.621.504-1.125 1.125-1.125h13.5c.621 0 1.125.504 1.125 1.125V7.5m-16.5 0h16.5" />
                        </svg>
                        Stok Barang
                    </a>
                    <a href="{{ route('gudang.laporan-stok.pdf') }}?periode={{ $periode }}&tanggal={{ $tanggal }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm text-white font-medium rounded-lg hover:bg-white/30 transition-all duration-300 shadow-lg border border-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                             stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Export PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Periode --}}
    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg mb-8">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Filter Laporan</h2>
        <form method="GET" action="{{ route('gudang.laporan-stok') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Periode</label>
                <select name="periode" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-[#2c2c2c] dark:text-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400">
                    <option value="harian" {{ $periode == 'harian' ? 'selected' : '' }}>Harian</option>
                    <option value="mingguan" {{ $periode == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="tahunan" {{ $periode == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" 
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-[#2c2c2c] dark:text-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400">
            </div>
            <div>
                <button type="submit" 
                        class="px-6 py-2 bg-teal-500 hover:bg-teal-600 text-white font-medium rounded-lg transition shadow-sm">
                    Filter
                </button>
            </div>
        </form>
        
        <div class="mt-4 p-3 bg-teal-50 dark:bg-teal-900/20 rounded-lg">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                <strong class="text-teal-700 dark:text-teal-400">Periode:</strong> 
                <span class="text-gray-600 dark:text-gray-400">
                    {{ $periode == 'harian' ? date('d/m/Y', strtotime($startDate)) : 
                        ($periode == 'mingguan' ? date('d/m/Y', strtotime($startDate)) . ' s/d ' . date('d/m/Y', strtotime($endDate)) :
                        ($periode == 'bulanan' ? date('F Y', strtotime($startDate)) : date('Y', strtotime($startDate)))) }}
                </span>
            </p>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                         stroke-width="2" stroke="currentColor" class="w-6 h-6 text-blue-600 dark:text-blue-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5m-16.5 0v-1.125c0-.621.504-1.125 1.125-1.125h13.5c.621 0 1.125.504 1.125 1.125V7.5m-16.5 0h16.5" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Stok Saat Ini</p>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($stokSaatIni->sum('stok'), 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                         stroke-width="2" stroke="currentColor" class="w-6 h-6 text-green-600 dark:text-green-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6.75 6.75M12 4.5l6.75 6.75" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Stok Masuk</p>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($stokMasuk->sum(function($item) { return collect($item['items'])->sum('qty'); }), 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                         stroke-width="2" stroke="currentColor" class="w-6 h-6 text-red-600 dark:text-red-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6.75-6.75M12 19.5l-6.75-6.75" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Stok Keluar</p>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ number_format($stokKeluar->sum(function($item) { return collect($item['items'])->sum('qty'); }), 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                         stroke-width="2" stroke="currentColor" class="w-6 h-6 text-purple-600 dark:text-purple-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Sisa Stok</p>
            <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                {{ number_format($stokSaatIni->sum('stok') + $stokMasuk->sum(function($item) { return collect($item['items'])->sum('qty'); }) - $stokKeluar->sum(function($item) { return collect($item['items'])->sum('qty'); }), 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Stok Saat Ini --}}
    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg mb-8">
        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-6">Stok Saat Ini</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto data-table text-sm border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-[#004d4d] text-gray-700 dark:text-[#ccf2f2]">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nama Produk/Material</th>
                        <th class="px-4 py-3 text-left font-semibold">Ukuran</th>
                        <th class="px-4 py-3 text-left font-semibold">Kategori</th>
                        <th class="px-4 py-3 text-left font-semibold">Tipe</th>
                        <th class="px-4 py-3 text-left font-semibold">Total Stok</th>
                        <th class="px-4 py-3 text-left font-semibold">Detail Variant</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stokSaatIni as $item)
                        <tr class="border-b border-gray-100 dark:border-gray-700 @if($loop->odd) bg-gray-50 dark:bg-[#2c2c2c] @endif hover:bg-teal-50 dark:hover:bg-[#003333] transition">
                            <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-200">{{ $item['nama'] }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $item['ukuran'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                <span class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded text-xs">
                                    {{ $item['kategori'] ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold 
                                    {{ $item['tipe'] == 'Produk Regular' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' }}">
                                    {{ $item['tipe'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold text-lg">
                                <span class="{{ $item['stok'] <= 10 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                    {{ number_format($item['stok'], 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="space-y-1">
                                    @forelse ($item['variants'] ?? [] as $variant)
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $variant['warna'] ?? '-' }}</span>
                                            <span class="px-2 py-1 rounded text-xs font-semibold {{ ($variant['stok'] ?? 0) <= 10 ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' }}">
                                                {{ $variant['stok'] ?? 0 }}
                                            </span>
                                        </div>
                                    @empty
                                        <span class="text-gray-500 dark:text-gray-400 text-xs">Tidak ada variant</span>
                                    @endforelse
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5m-16.5 0v-1.125c0-.621.504-1.125 1.125-1.125h13.5c.621 0 1.125.504 1.125 1.125V7.5m-16.5 0h16.5" />
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada data stok</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Data stok akan muncul di sini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Stok Masuk --}}
    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg mb-8">
        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-6">Stok Masuk</h2>
        @if($stokMasuk->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto data-table text-sm border border-gray-200 dark:border-gray-700">
                    <thead class="bg-gray-100 dark:bg-[#004d4d] text-gray-700 dark:text-[#ccf2f2]">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Sumber</th>
                            <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                            <th class="px-4 py-3 text-left font-semibold">Tipe</th>
                            <th class="px-4 py-3 text-left font-semibold">Items</th>
                            <th class="px-4 py-3 text-left font-semibold">Ukuran</th>
                            <th class="px-4 py-3 text-left font-semibold">Total Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stokMasuk as $masuk)
                            <tr class="border-b border-gray-100 dark:border-gray-700 @if($loop->odd) bg-gray-50 dark:bg-[#2c2c2c] @endif hover:bg-green-50 dark:hover:bg-[#003300] transition">
                                <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-200">{{ $masuk['sumber'] }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($masuk['tanggal'])->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                        {{ $masuk['tipe'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        @foreach ($masuk['items'] as $item)
                                            <div class="text-xs">
                                                <div class="font-medium text-gray-800 dark:text-gray-200">{{ $item['nama'] }}</div>
                                                <div class="text-gray-600 dark:text-gray-400">{{ $item['keterangan'] ?? '-' }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        @foreach ($masuk['items'] as $item)
                                            <div class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $item['ukuran'] ?? '-' }}</div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-bold text-green-600 dark:text-green-400">
                                    {{ number_format(collect($masuk['items'])->sum('qty'), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19.5v-15m0 0l-6.75 6.75M12 4.5l6.75 6.75" />
                    </svg>
                    <p class="text-lg font-medium">Tidak ada stok masuk dalam periode ini</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Stok masuk akan muncul di sini</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Stok Keluar --}}
    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg">
        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-6">Stok Keluar</h2>
        @if($stokKeluar->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto data-table text-sm border border-gray-200 dark:border-gray-700">
                    <thead class="bg-gray-100 dark:bg-[#004d4d] text-gray-700 dark:text-[#ccf2f2]">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Sumber</th>
                            <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                            <th class="px-4 py-3 text-left font-semibold">Tipe</th>
                            <th class="px-4 py-3 text-left font-semibold">Items</th>
                            <th class="px-4 py-3 text-left font-semibold">Ukuran</th>
                            <th class="px-4 py-3 text-left font-semibold">Total Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stokKeluar as $keluar)
                            <tr class="border-b border-gray-100 dark:border-gray-700 @if($loop->odd) bg-gray-50 dark:bg-[#2c2c2c] @endif hover:bg-red-50 dark:hover:bg-[#330000] transition">
                                <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-200">{{ $keluar['sumber'] }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($keluar['tanggal'])->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                                        {{ $keluar['tipe'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        @foreach ($keluar['items'] as $item)
                                            <div class="text-xs">
                                                <div class="font-medium text-gray-800 dark:text-gray-200">{{ $item['nama'] }}</div>
                                                <div class="text-gray-600 dark:text-gray-400">{{ $item['keterangan'] ?? '-' }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        @foreach ($keluar['items'] as $item)
                                            <div class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $item['ukuran'] ?? '-' }}</div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-bold text-red-600 dark:text-red-400">
                                    {{ number_format(collect($keluar['items'])->sum('qty'), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m0 0l6.75-6.75M12 19.5l-6.75-6.75" />
                    </svg>
                    <p class="text-lg font-medium">Tidak ada stok keluar dalam periode ini</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Stok keluar akan muncul di sini</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

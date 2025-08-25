@extends('layouts.admin')

@section('content')
<div class="py-6">
    {{-- Breadcrumb --}}
    <div class="mb-4">
        <a href="{{ route('gudang.dashboard') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
            ← Kembali ke Dashboard
        </a>
    </div>
    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Laporan Stok {{ ucfirst($periode) }}</h1>
        <div class="flex space-x-3">
            <a href="{{ route('gudang.stok-barang') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition">
                📦 Stok Barang
            </a>
            <a href="{{ route('gudang.laporan-stok.pdf') }}?periode={{ $periode }}&tanggal={{ $tanggal }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition">
                📄 Export PDF
            </a>
        </div>
    </div>

    {{-- Filter Periode --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-8">
        <form method="GET" action="{{ route('gudang.laporan-stok') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                <select name="periode" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="harian" {{ $periode == 'harian' ? 'selected' : '' }}>Harian</option>
                    <option value="mingguan" {{ $periode == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="tahunan" {{ $periode == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition">
                    Filter
                </button>
            </div>
        </form>
        
        <div class="mt-4 text-sm text-gray-600">
            <strong>Periode:</strong> {{ $periode == 'harian' ? date('d/m/Y', strtotime($startDate)) : 
                ($periode == 'mingguan' ? date('d/m/Y', strtotime($startDate)) . ' s/d ' . date('d/m/Y', strtotime($endDate)) :
                ($periode == 'bulanan' ? date('F Y', strtotime($startDate)) : date('Y', strtotime($startDate)))) }}
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Total Stok Saat Ini</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stokSaatIni->sum('stok') }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Stok Masuk</p>
            <p class="text-2xl font-bold text-green-600">{{ $stokMasuk->sum(function($item) { return collect($item['items'])->sum('qty'); }) }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Stok Keluar</p>
            <p class="text-2xl font-bold text-red-600">{{ $stokKeluar->sum(function($item) { return collect($item['items'])->sum('qty'); }) }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Sisa Stok</p>
            <p class="text-2xl font-bold text-purple-600">
                {{ $stokSaatIni->sum('stok') + $stokMasuk->sum(function($item) { return collect($item['items'])->sum('qty'); }) - $stokKeluar->sum(function($item) { return collect($item['items'])->sum('qty'); }) }}
            </p>
        </div>
    </div>

    {{-- Stok Saat Ini --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-8">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Stok Saat Ini</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nama Produk/Material</th>
                        <th class="px-4 py-3 text-left font-semibold">Kategori</th>
                        <th class="px-4 py-3 text-left font-semibold">Tipe</th>
                        <th class="px-4 py-3 text-left font-semibold">Total Stok</th>
                        <th class="px-4 py-3 text-left font-semibold">Detail Variant</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stokSaatIni as $item)
                        <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-blue-50 transition">
                            <td class="px-4 py-3 font-semibold">{{ $item['nama'] }}</td>
                            <td class="px-4 py-3">{{ $item['kategori'] }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold 
                                    {{ $item['tipe'] == 'Produk Regular' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ $item['tipe'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold text-lg">
                                <span class="{{ $item['stok'] <= 10 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $item['stok'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="space-y-1">
                                    @forelse ($item['variants'] as $variant)
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="font-medium">{{ $variant['warna'] }}</span>
                                            <span class="px-2 py-1 rounded {{ $variant['stok'] <= 10 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                                {{ $variant['stok'] }}
                                            </span>
                                        </div>
                                    @empty
                                        <span class="text-gray-500">Tidak ada variant</span>
                                    @endforelse
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-gray-500 py-4">Tidak ada data stok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Stok Masuk --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-8">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Stok Masuk</h2>
        @if($stokMasuk->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm border border-gray-200">
                    <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Sumber</th>
                            <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                            <th class="px-4 py-3 text-left font-semibold">Tipe</th>
                            <th class="px-4 py-3 text-left font-semibold">Items</th>
                            <th class="px-4 py-3 text-left font-semibold">Total Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stokMasuk as $masuk)
                            <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-green-50 transition">
                                <td class="px-4 py-3 font-semibold">{{ $masuk['sumber'] }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($masuk['tanggal'])->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        {{ $masuk['tipe'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        @foreach ($masuk['items'] as $item)
                                            <div class="text-xs">
                                                <div class="font-medium">{{ $item['nama'] }}</div>
                                                <div class="text-gray-600">{{ $item['keterangan'] }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-bold text-green-600">
                                    {{ collect($masuk['items'])->sum('qty') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center text-gray-500 py-8">
                <p>Tidak ada stok masuk dalam periode ini.</p>
            </div>
        @endif
    </div>

    {{-- Stok Keluar --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Stok Keluar</h2>
        @if($stokKeluar->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm border border-gray-200">
                    <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Sumber</th>
                            <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                            <th class="px-4 py-3 text-left font-semibold">Tipe</th>
                            <th class="px-4 py-3 text-left font-semibold">Items</th>
                            <th class="px-4 py-3 text-left font-semibold">Total Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stokKeluar as $keluar)
                            <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-red-50 transition">
                                <td class="px-4 py-3 font-semibold">{{ $keluar['sumber'] }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($keluar['tanggal'])->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        {{ $keluar['tipe'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        @foreach ($keluar['items'] as $item)
                                            <div class="text-xs">
                                                <div class="font-medium">{{ $item['nama'] }}</div>
                                                <div class="text-gray-600">{{ $item['keterangan'] }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-bold text-red-600">
                                    {{ collect($keluar['items'])->sum('qty') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center text-gray-500 py-8">
                <p>Tidak ada stok keluar dalam periode ini.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('content')
<div class="py-6">
    {{-- Header dengan Gradient Background --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl mb-8">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">⚠️ Kelola Barang Rusak</h1>
                    <p class="text-teal-100">Manajemen barang rusak dari retur customer</p>
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
                    <a href="{{ route('gudang.laporan.retur.pdf') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm text-white font-medium rounded-lg hover:bg-white/30 transition-all duration-300 shadow-lg border border-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                             stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Download Laporan Retur
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <div class="flex items-center gap-2 text-green-800 dark:text-green-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <div class="flex items-center gap-2 text-red-800 dark:text-red-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Stats Cards --}}
    @php
        $totalRusak = $damagedProducts->where('status', 'rusak')->count();
        $totalDiperbaiki = $damagedProducts->where('status', 'diperbaiki')->count();
        $totalSelesai = $damagedProducts->where('status', 'selesai')->count();
        $totalBarang = $damagedProducts->count();
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                         stroke-width="2" stroke="currentColor" class="w-6 h-6 text-red-600 dark:text-red-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Barang Rusak</p>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $totalRusak }}</p>
        </div>
        <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                         stroke-width="2" stroke="currentColor" class="w-6 h-6 text-yellow-600 dark:text-yellow-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655-5.653a2.548 2.548 0 00-4.11.504L2.25 13.5m9.17 1.67l-4.655-5.653a2.548 2.548 0 00-4.11.504L2.25 13.5m0 0l3.744-3.5m0 0l3.744 3.5M2.25 13.5l3.744 3.5m0 0l3.744-3.5m-7.488 0l3.744-3.5" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Sedang Diperbaiki</p>
            <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $totalDiperbaiki }}</p>
        </div>
        <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                         stroke-width="2" stroke="currentColor" class="w-6 h-6 text-green-600 dark:text-green-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Sudah Diperbaiki</p>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $totalSelesai }}</p>
        </div>
        <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                         stroke-width="2" stroke="currentColor" class="w-6 h-6 text-blue-600 dark:text-blue-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5m-16.5 0v-1.125c0-.621.504-1.125 1.125-1.125h13.5c.621 0 1.125.504 1.125 1.125V7.5m-16.5 0h16.5" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Barang</p>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $totalBarang }}</p>
        </div>
    </div>

    {{-- Tabel Barang Rusak --}}
    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg">
        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-6">Daftar Barang Rusak</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto data-table text-sm border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-[#004d4d] text-gray-700 dark:text-[#ccf2f2]">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Produk</th>
                        <th class="px-4 py-3 text-left font-semibold">Variant</th>
                        <th class="px-4 py-3 text-left font-semibold">Quantity</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Invoice</th>
                        <th class="px-4 py-3 text-left font-semibold">Alasan Kerusakan</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal Diproses</th>
                        <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($damagedProducts as $item)
                        <tr class="border-b border-gray-100 dark:border-gray-700 @if($loop->odd) bg-gray-50 dark:bg-[#2c2c2c] @endif hover:bg-teal-50 dark:hover:bg-[#003333] transition">
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $item->id }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-200">{{ $item->product->name ?? 'Produk tidak ditemukan' }}</td>
                            <td class="px-4 py-3">
                                @if($item->variant)
                                    <span class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded text-xs">
                                        {{ $item->variant->color }}
                                    </span>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-medium">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $item->retur->invoice->customer->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($item->retur->invoice->code ?? null)
                                    <span class="font-mono text-teal-700 dark:text-[#80C0CE] font-semibold text-xs">
                                        {{ $item->retur->invoice->code }}
                                    </span>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-sm">{{ $item->damage_description ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                    @if($item->status === 'rusak') bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400
                                    @elseif($item->status === 'diperbaiki') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400
                                    @else bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                                    @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-sm">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                @if($item->status === 'rusak')
                                    <form action="{{ route('gudang.barang-rusak.perbaiki', $item->id) }}" method="POST" onsubmit="return confirm('Tandai barang ini sudah diperbaiki?');" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-xs font-semibold transition shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655-5.653a2.548 2.548 0 00-4.11.504L2.25 13.5m9.17 1.67l-4.655-5.653a2.548 2.548 0 00-4.11.504L2.25 13.5m0 0l3.744-3.5m0 0l3.744 3.5M2.25 13.5l3.744 3.5m0 0l3.744-3.5m-7.488 0l3.744-3.5" />
                                            </svg>
                                            Sudah Diperbaiki
                                        </button>
                                    </form>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-md text-xs font-semibold">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Selesai
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-lg font-medium">Belum ada barang rusak yang diproses</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Barang rusak dari retur akan muncul di sini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 
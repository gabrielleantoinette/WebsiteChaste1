@extends('layouts.admin')

@section('content')
    @php
        $reportRangeOptions = \App\Support\ReportDateRange::options();
    @endphp

    {{-- Header dengan Gradient Background --}}
    <div class="relative bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl mb-8 overflow-visible">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">💳 Kelola Transaksi</h1>
                    <p class="text-teal-100">Kelola dan monitor semua transaksi</p>
                </div>
                <div class="flex gap-3">
        {{-- Tombol Download dengan Dropdown --}}
        <div class="relative">
                        <button id="downloadBtn" class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm text-white font-medium rounded-lg hover:bg-white/30 transition-all duration-300 shadow-lg border border-white/20">
                📄 Unduh Laporan
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            
            {{-- Dropdown Menu --}}
                        <div id="downloadDropdown" class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden">
                <div class="py-3 px-4 space-y-4">
                    <div class="space-y-3 border-b border-gray-200 pb-4">
                        <h4 class="text-sm font-semibold text-gray-700">📊 Laporan Transaksi</h4>
                        <form method="GET" action="{{ route('owner.laporan.download') }}" class="space-y-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Rentang Waktu</label>
                                <select name="range" data-report-range data-description-target="transaksi-desc" data-field-prefix="transaksi"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                    @foreach($reportRangeOptions as $option)
                                        <option value="{{ $option['value'] }}" data-description="{{ $option['description'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                                <p id="transaksi-desc" class="text-[11px] text-gray-400 mt-1">{{ $reportRangeOptions[0]['description'] }}</p>
                            </div>
                            <div id="transaksi-date" class="hidden space-y-1">
                                <label class="block text-xs font-semibold text-gray-600">Tanggal</label>
                                <input type="date" name="date" disabled
                                       value="{{ request('date', now()->toDateString()) }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>
                            <div id="transaksi-month" class="hidden space-y-1">
                                <label class="block text-xs font-semibold text-gray-600">Bulan</label>
                                <input type="month" name="month" disabled
                                       value="{{ request('month', now()->format('Y-m')) }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>
                            <div id="transaksi-year" class="hidden space-y-1">
                                <label class="block text-xs font-semibold text-gray-600">Tahun</label>
                                <input type="number" name="year" min="2000" max="{{ now()->year }}" disabled
                                       value="{{ request('year', now()->year) }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                       placeholder="Contoh: {{ now()->year }}">
                            </div>
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-sm font-semibold rounded-lg hover:from-teal-600 hover:to-teal-700 transition">
                                Unduh PDF
                            </button>
                        </form>
                    </div>

                    <div class="space-y-3 border-b border-gray-200 pb-4">
                        <h4 class="text-sm font-semibold text-gray-700">💳 Laporan Payment Gateway</h4>
                        <form method="GET" action="{{ route('owner.laporan.payment-gateway') }}" class="space-y-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Rentang Waktu</label>
                                <select name="range" data-report-range data-description-target="payment-desc" data-field-prefix="payment"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                    @foreach($reportRangeOptions as $option)
                                        <option value="{{ $option['value'] }}" data-description="{{ $option['description'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                                <p id="payment-desc" class="text-[11px] text-gray-400 mt-1">{{ $reportRangeOptions[0]['description'] }}</p>
                            </div>
                            <div id="payment-date" class="hidden space-y-1">
                                <label class="block text-xs font-semibold text-gray-600">Tanggal</label>
                                <input type="date" name="date" disabled
                                       value="{{ request('date', now()->toDateString()) }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>
                            <div id="payment-month" class="hidden space-y-1">
                                <label class="block text-xs font-semibold text-gray-600">Bulan</label>
                                <input type="month" name="month" disabled
                                       value="{{ request('month', now()->format('Y-m')) }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>
                            <div id="payment-year" class="hidden space-y-1">
                                <label class="block text-xs font-semibold text-gray-600">Tahun</label>
                                <input type="number" name="year" min="2000" max="{{ now()->year }}" disabled
                                       value="{{ request('year', now()->year) }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                       placeholder="Contoh: {{ now()->year }}">
                            </div>
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-gradient-to-r from-sky-500 to-indigo-500 text-white text-sm font-semibold rounded-lg hover:from-sky-600 hover:to-indigo-600 transition">
                                Unduh PDF
                            </button>
                        </form>
                    </div>

                    <div class="space-y-3">
                        <h4 class="text-sm font-semibold text-gray-700">🤝 Laporan Negosiasi Harga</h4>
                        <form method="GET" action="{{ route('owner.laporan.negosiasi') }}" class="space-y-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Rentang Waktu</label>
                                <select name="range" data-report-range data-description-target="negosiasi-desc" data-field-prefix="negosiasi"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                    @foreach($reportRangeOptions as $option)
                                        <option value="{{ $option['value'] }}" data-description="{{ $option['description'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                                <p id="negosiasi-desc" class="text-[11px] text-gray-400 mt-1">{{ $reportRangeOptions[0]['description'] }}</p>
                            </div>
                            <div id="negosiasi-date" class="hidden space-y-1">
                                <label class="block text-xs font-semibold text-gray-600">Tanggal</label>
                                <input type="date" name="date" disabled
                                       value="{{ request('date', now()->toDateString()) }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>
                            <div id="negosiasi-month" class="hidden space-y-1">
                                <label class="block text-xs font-semibold text-gray-600">Bulan</label>
                                <input type="month" name="month" disabled
                                       value="{{ request('month', now()->format('Y-m')) }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>
                            <div id="negosiasi-year" class="hidden space-y-1">
                                <label class="block text-xs font-semibold text-gray-600">Tahun</label>
                                <input type="number" name="year" min="2000" max="{{ now()->year }}" disabled
                                       value="{{ request('year', now()->year) }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                       placeholder="Contoh: {{ now()->year }}">
                            </div>
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white text-sm font-semibold rounded-lg hover:from-orange-600 hover:to-red-600 transition">
                                Unduh PDF
                            </button>
                        </form>
                    </div>
                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalPayments ?? 0 }}</p>
                </div>
                <div class="p-3 bg-teal-100 rounded-lg">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Transaksi Lunas</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $paidPayments ?? 0 }}</p>
                </div>
                <div class="p-3 bg-emerald-100 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Belum Lunas</p>
                    <p class="text-2xl font-bold text-red-600">{{ $unpaidPayments ?? 0 }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
            </div>
        </div>
    </div>

        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Pembayaran</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalPaymentsAmount ?? 0) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Waktu & Search dengan Card --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-8">
        <form method="GET" action="{{ route('owner.transactions.index') }}" class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                    </svg>
                    <label for="filter" class="text-sm font-semibold text-gray-700">Filter Waktu:</label>
                </div>
                <select name="filter" id="filter" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 bg-white shadow-sm">
                    <option value="semua" {{ request('filter', $filter) == 'semua' ? 'selected' : '' }}>Semua</option>
                    <option value="hari" {{ request('filter', $filter) == 'hari' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="minggu" {{ request('filter', $filter) == 'minggu' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="bulan" {{ request('filter', $filter) == 'bulan' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="tahun" {{ request('filter', $filter) == 'tahun' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" placeholder="Cari kode/nama/ket" value="{{ request('search', $search) }}" class="pl-10 pr-4 py-2 w-64 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 shadow-sm" />
                </div>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white font-medium rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all duration-200 shadow-lg hover:shadow-xl">Cari</button>
                @if(request('search') || (request('filter') && request('filter') != 'semua'))
                    <a href="{{ route('owner.transactions.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-all duration-200">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel dengan Card Layout --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Data Transaksi
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Metode Pembayaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($payments as $p)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ ($payments->currentPage() - 1) * $payments->perPage() + $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-teal-600">{{ $p->hinvoice->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $p->hinvoice->customer->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $p->hinvoice->receive_date ? \Carbon\Carbon::parse($p->hinvoice->receive_date)->format('d M Y') : ($p->hinvoice->created_at ? \Carbon\Carbon::parse($p->hinvoice->created_at)->format('d M Y') : '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @switch($p->method)
                                    @case('bank_transfer')
                                        Transfer Bank
                                        @break
                                    @case('cash')
                                        Tunai
                                        @break
                                    @case('credit_card')
                                        Kartu Kredit
                                        @break
                                    @case('debit_card')
                                        Kartu Debit
                                        @break
                                    @case('e_wallet')
                                        E-Wallet
                                        @break
                                    @case('qris')
                                        QRIS
                                        @break
                                    @case('gopay')
                                        GoPay
                                        @break
                                    @case('shopeepay')
                                        ShopeePay
                                        @break
                                    @case('dana')
                                        DANA
                                        @break
                                    @case('ovo')
                                        OVO
                                        @break
                                    @case('linkaja')
                                        LinkAja
                                        @break
                                    @default
                                        {{ ucfirst(str_replace('_', ' ', $p->method)) }}
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-600">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $p->is_paid ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $p->is_paid ? 'Lunas' : 'Belum Lunas' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ url('/admin/invoices/detail/' . $p->hinvoice->id) }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-sm font-medium rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak ada data transaksi</h3>
                                    <p class="text-gray-500">Transaksi akan muncul di sini setelah ada pembayaran</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($payments->hasPages())
    <div class="mt-8 flex justify-center">
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 p-4">
            {{ $payments->links() }}
        </div>
    </div>
    @endif

<script>
        // Download dropdown functionality
        document.getElementById('downloadBtn').addEventListener('click', function() {
            const dropdown = document.getElementById('downloadDropdown');
            dropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('downloadDropdown');
            const button = document.getElementById('downloadBtn');
            
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
});

        document.querySelectorAll('[data-report-range]').forEach(function(select) {
            const descriptionId = select.getAttribute('data-description-target');
            const descriptionElement = descriptionId ? document.getElementById(descriptionId) : null;
            const prefix = select.getAttribute('data-field-prefix');

            const toggleRangeFields = () => {
                if (!prefix) return;
                const fieldMap = {
                    harian: document.getElementById(`${prefix}-date`),
                    bulanan: document.getElementById(`${prefix}-month`),
                    tahunan: document.getElementById(`${prefix}-year`),
                };

                Object.entries(fieldMap).forEach(([rangeKey, element]) => {
                    if (!element) {
                        return;
                    }
                    const inputs = element.querySelectorAll('input, select');
                    if (select.value === rangeKey) {
                        element.classList.remove('hidden');
                        inputs.forEach(input => input.disabled = false);
                    } else {
                        element.classList.add('hidden');
                        inputs.forEach(input => input.disabled = true);
                    }
                });
            };

            const updateDescription = () => {
                if (!descriptionElement) return;
                const selectedOption = select.options[select.selectedIndex];
                descriptionElement.textContent = selectedOption?.getAttribute('data-description') || '';
            };

            select.addEventListener('change', () => {
                toggleRangeFields();
                updateDescription();
            });

            toggleRangeFields();
            updateDescription();
        });
</script>
@endsection 
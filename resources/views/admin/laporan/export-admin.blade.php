@extends('layouts.admin')

@section('content')
    {{-- Header dengan Gradient Background --}}
    <div class="relative bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl mb-8 overflow-visible">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Export Laporan</h1>
                    <p class="text-teal-100">Unduh laporan dalam format PDF</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid Export Laporan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Export PDF Transaksi --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-teal-100 rounded-lg">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Export PDF Transaksi</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">Unduh semua transaksi dalam format PDF</p>
            <a href="{{ route('invoices.export.pdf') }}"
               class="block w-full px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-sm font-semibold rounded-lg hover:from-teal-600 hover:to-teal-700 transition text-center">
                Unduh PDF
            </a>
        </div>

        {{-- Laporan Penjualan --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Laporan Penjualan</h3>
            </div>
            <form method="GET" action="{{ route('laporan.penjualan.pdf') }}" class="space-y-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Rentang Waktu</label>
                    <select name="range" data-report-range data-description-target="penjualan-desc" data-field-prefix="penjualan"
                            class="report-range-select w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        @foreach($reportRangeOptions as $option)
                            <option value="{{ $option['value'] }}" data-description="{{ $option['description'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                    <p id="penjualan-desc" class="text-xs text-gray-500 mt-1">{{ $reportRangeOptions[0]['description'] }}</p>
                </div>
                <div id="penjualan-date" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="date" disabled
                           value="{{ request('date', now()->toDateString()) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div id="penjualan-month" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bulan</label>
                    <input type="month" name="month" disabled
                           value="{{ request('month', now()->format('Y-m')) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div id="penjualan-year" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun</label>
                    <input type="number" name="year" min="2000" max="{{ now()->year }}" disabled
                           value="{{ request('year', now()->year) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                           placeholder="Contoh: {{ now()->year }}">
                </div>
                <button type="submit"
                        class="w-full px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-sm font-semibold rounded-lg hover:from-teal-600 hover:to-teal-700 transition">
                    Unduh PDF
                </button>
            </form>
        </div>

        {{-- Laporan Pembeli --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Laporan Pembeli</h3>
            </div>
            <form method="GET" action="{{ route('laporan.customer.pdf') }}" class="space-y-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Rentang Waktu</label>
                    <select name="range" data-report-range data-description-target="customer-desc" data-field-prefix="customer"
                            class="report-range-select w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        @foreach($reportRangeOptions as $option)
                            <option value="{{ $option['value'] }}" data-description="{{ $option['description'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                    <p id="customer-desc" class="text-xs text-gray-500 mt-1">{{ $reportRangeOptions[0]['description'] }}</p>
                </div>
                <div id="customer-date" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="date" disabled
                           value="{{ request('date', now()->toDateString()) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div id="customer-month" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bulan</label>
                    <input type="month" name="month" disabled
                           value="{{ request('month', now()->format('Y-m')) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div id="customer-year" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun</label>
                    <input type="number" name="year" min="2000" max="{{ now()->year }}" disabled
                           value="{{ request('year', now()->year) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                           placeholder="Contoh: {{ now()->year }}">
                </div>
                <button type="submit"
                        class="w-full px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-sm font-semibold rounded-lg hover:from-teal-600 hover:to-teal-700 transition">
                    Unduh PDF
                </button>
            </form>
        </div>

        {{-- Laporan Retur --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Laporan Barang Retur</h3>
            </div>
            <form method="GET" action="{{ route('laporan.retur.pdf') }}" class="space-y-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Rentang Waktu</label>
                    <select name="range" data-report-range data-description-target="retur-desc" data-field-prefix="retur"
                            class="report-range-select w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        @foreach($reportRangeOptions as $option)
                            <option value="{{ $option['value'] }}" data-description="{{ $option['description'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                    <p id="retur-desc" class="text-xs text-gray-500 mt-1">{{ $reportRangeOptions[0]['description'] }}</p>
                </div>
                <div id="retur-date" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="date" disabled
                           value="{{ request('date', now()->toDateString()) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div id="retur-month" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bulan</label>
                    <input type="month" name="month" disabled
                           value="{{ request('month', now()->format('Y-m')) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div id="retur-year" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun</label>
                    <input type="number" name="year" min="2000" max="{{ now()->year }}" disabled
                           value="{{ request('year', now()->year) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                           placeholder="Contoh: {{ now()->year }}">
                </div>
                <button type="submit"
                        class="w-full px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-sm font-semibold rounded-lg hover:from-teal-600 hover:to-teal-700 transition">
                    Unduh PDF
                </button>
            </form>
        </div>

        {{-- Laporan Rata-Rata Pesanan --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-cyan-100 rounded-lg">
                    <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Laporan Rata-Rata Pesanan</h3>
            </div>
            <form method="GET" action="{{ route('laporan.ratarata.pdf') }}" class="space-y-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Rentang Waktu</label>
                    <select name="range" data-report-range data-description-target="ratarata-desc" data-field-prefix="ratarata"
                            class="report-range-select w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        @foreach($reportRangeOptions as $option)
                            <option value="{{ $option['value'] }}" data-description="{{ $option['description'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                    <p id="ratarata-desc" class="text-xs text-gray-500 mt-1">{{ $reportRangeOptions[0]['description'] }}</p>
                </div>
                <div id="ratarata-date" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="date" disabled
                           value="{{ request('date', now()->toDateString()) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div id="ratarata-month" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bulan</label>
                    <input type="month" name="month" disabled
                           value="{{ request('month', now()->format('Y-m')) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div id="ratarata-year" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun</label>
                    <input type="number" name="year" min="2000" max="{{ now()->year }}" disabled
                           value="{{ request('year', now()->year) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                           placeholder="Contoh: {{ now()->year }}">
                </div>
                <button type="submit"
                        class="w-full px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-sm font-semibold rounded-lg hover:from-teal-600 hover:to-teal-700 transition">
                    Unduh PDF
                </button>
            </form>
        </div>

        {{-- Export PDF Keuangan --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Export PDF Keuangan</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">Unduh laporan transaksi keuangan</p>
            <a href="{{ route('keuangan.export.pdf') }}"
               class="block w-full px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-sm font-semibold rounded-lg hover:from-teal-600 hover:to-teal-700 transition text-center">
                Unduh PDF
            </a>
        </div>

        {{-- Export PDF Hutang Supplier --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Export PDF Hutang Supplier</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">Unduh laporan hutang supplier</p>
            <a href="{{ route('keuangan.hutang.export.pdf') }}"
               class="block w-full px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-sm font-semibold rounded-lg hover:from-teal-600 hover:to-teal-700 transition text-center">
                Unduh PDF
            </a>
        </div>
    </div>

    {{-- JavaScript untuk Report Range Selector --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selects = document.querySelectorAll('.report-range-select');
            
            selects.forEach(select => {
                const prefix = select.getAttribute('data-field-prefix');
                const descTarget = select.getAttribute('data-description-target');
                
                select.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const description = selectedOption.getAttribute('data-description');
                    const value = this.value;
                    
                    // Update description
                    if (descTarget) {
                        const descElement = document.getElementById(descTarget);
                        if (descElement) {
                            descElement.textContent = description;
                        }
                    }
                    
                    // Show/hide date fields
                    const dateField = document.getElementById(prefix + '-date');
                    const monthField = document.getElementById(prefix + '-month');
                    const yearField = document.getElementById(prefix + '-year');
                    
                    if (dateField) dateField.classList.add('hidden');
                    if (monthField) monthField.classList.add('hidden');
                    if (yearField) yearField.classList.add('hidden');
                    
                    if (value === 'harian') {
                        if (dateField) {
                            dateField.classList.remove('hidden');
                            dateField.querySelector('input').disabled = false;
                        }
                    } else if (value === 'bulanan') {
                        if (monthField) {
                            monthField.classList.remove('hidden');
                            monthField.querySelector('input').disabled = false;
                        }
                    } else if (value === 'tahunan') {
                        if (yearField) {
                            yearField.classList.remove('hidden');
                            yearField.querySelector('input').disabled = false;
                        }
                    }
                });
            });
        });
    </script>
@endsection


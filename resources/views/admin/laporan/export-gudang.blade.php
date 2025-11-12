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
        {{-- Laporan Stok --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-teal-100 rounded-lg">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5m-16.5 0v-1.125c0-.621.504-1.125 1.125-1.125h13.5c.621 0 1.125.504 1.125 1.125V7.5m-16.5 0h16.5"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Laporan Stok</h3>
            </div>
            <form method="GET" action="{{ route('gudang.laporan-stok.pdf') }}" class="space-y-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Periode</label>
                    <select name="periode" id="stok-periode" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="harian">Harian</option>
                        <option value="mingguan">Mingguan</option>
                        <option value="bulanan">Bulanan</option>
                        <option value="tahunan">Tahunan</option>
                    </select>
                </div>
                <div id="stok-tanggal" class="space-y-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal"
                           value="{{ request('tanggal', now()->toDateString()) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div id="stok-bulan" class="hidden space-y-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bulan</label>
                    <input type="month" name="bulan"
                           value="{{ request('bulan', now()->format('Y-m')) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div id="stok-tahun" class="hidden space-y-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun</label>
                    <input type="number" name="tahun" min="2000" max="{{ now()->year }}"
                           value="{{ request('tahun', now()->year) }}"
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
            <form method="GET" action="{{ route('gudang.laporan.retur.pdf') }}" class="space-y-3">
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
    </div>

    {{-- JavaScript untuk Report Range Selector --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle stok periode selector
            const stokPeriode = document.getElementById('stok-periode');
            if (stokPeriode) {
                stokPeriode.addEventListener('change', function() {
                    const tanggalField = document.getElementById('stok-tanggal');
                    const bulanField = document.getElementById('stok-bulan');
                    const tahunField = document.getElementById('stok-tahun');
                    
                    if (tanggalField) tanggalField.classList.add('hidden');
                    if (bulanField) bulanField.classList.add('hidden');
                    if (tahunField) tahunField.classList.add('hidden');
                    
                    if (this.value === 'harian') {
                        if (tanggalField) tanggalField.classList.remove('hidden');
                    } else if (this.value === 'bulanan') {
                        if (bulanField) bulanField.classList.remove('hidden');
                    } else if (this.value === 'tahunan') {
                        if (tahunField) tahunField.classList.remove('hidden');
                    }
                });
            }

            // Handle retur report range selector
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


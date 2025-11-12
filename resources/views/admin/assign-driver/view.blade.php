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
                    <h1 class="text-3xl font-bold text-white mb-2">🚚 Atur Kurir</h1>
                    <p class="text-teal-100">Kelola pengiriman dan pengambilan barang retur</p>
                </div>
                <div class="flex gap-3 items-center">
                    <div class="text-right text-white">
                        <p class="text-sm opacity-90">Total Pengiriman</p>
                        <p class="text-lg font-semibold">{{ $pengirimanNormal->count() + $pengirimanRetur->count() }}</p>
                    </div>
                    <div class="relative">
                        <button id="driverReportBtn"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/20 backdrop-blur-sm text-white font-medium rounded-lg hover:bg-white/30 transition-all duration-300 shadow-lg border border-white/20">
                            📄 Laporan Driver
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="driverReportDropdown" class="hidden absolute right-0 mt-2 w-72 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                            <div class="py-3 px-4 space-y-3">
                                <h4 class="text-sm font-semibold text-gray-700">Unduh Laporan Aktivitas Driver</h4>
                                <form method="GET" action="{{ route('owner.laporan.driver') }}" class="space-y-2">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Rentang Waktu</label>
                                        <select name="range" data-report-range data-description-target="driver-desc" data-field-prefix="driver"
                                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                            @foreach($reportRangeOptions as $option)
                                                <option value="{{ $option['value'] }}" data-description="{{ $option['description'] }}">{{ $option['label'] }}</option>
                                            @endforeach
                                        </select>
                                        <p id="driver-desc" class="text-[11px] text-gray-400 mt-1">{{ $reportRangeOptions[0]['description'] }}</p>
                                    </div>
                                    <div id="driver-date" class="hidden space-y-1">
                                        <label class="block text-xs font-semibold text-gray-600">Tanggal</label>
                                        <input type="date" name="date" disabled
                                               value="{{ request('date', now()->toDateString()) }}"
                                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                    </div>
                                    <div id="driver-month" class="hidden space-y-1">
                                        <label class="block text-xs font-semibold text-gray-600">Bulan</label>
                                        <input type="month" name="month" disabled
                                               value="{{ request('month', now()->format('Y-m')) }}"
                                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                    </div>
                                    <div id="driver-year" class="hidden space-y-1">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-100 text-emerald-800 px-6 py-4 rounded-xl mb-6 border border-emerald-200">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pengiriman Normal</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pengirimanNormal->count() }}</p>
                </div>
                <div class="p-3 bg-teal-100 rounded-lg">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pengiriman Retur</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pengirimanRetur->count() }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m5 3v6a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h2"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Driver Aktif</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ \App\Models\Employee::where('role', 'driver')->where('active', true)->count() }}</p>
                </div>
                <div class="p-3 bg-emerald-100 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Pengiriman Normal Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                </svg>
                Daftar Pengiriman Normal
                <span class="ml-2 bg-teal-100 text-teal-800 text-sm font-medium px-2.5 py-0.5 rounded-full">
                    {{ $pengirimanNormal->count() }}
                </span>
            </h3>
        </div>
        
        @if($pengirimanNormal->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Alamat</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Driver</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($pengirimanNormal as $invoice)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-teal-600">{{ $invoice->code }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $invoice->id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $invoice->customer->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ Str::limit($invoice->address, 30) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $invoice->receive_date ? \Carbon\Carbon::parse($invoice->receive_date)->format('d M Y') : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $invoice->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 
                                           ($invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $invoice->driver ? $invoice->driver->name : 'Belum ditugaskan' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.assign-driver.create', $invoice->id) }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-sm font-medium rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Assign Driver
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak ada pengiriman normal</h3>
                    <p class="text-gray-500">Pengiriman akan muncul di sini setelah ada transaksi</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Pengiriman Retur Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m5 3v6a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h2"/>
                </svg>
                Daftar Pengiriman Retur
                <span class="ml-2 bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded-full">
                    {{ $pengirimanRetur->count() }}
                </span>
            </h3>
        </div>
        
        @if($pengirimanRetur->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Alamat</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Driver</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($pengirimanRetur as $invoice)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-red-600">{{ $invoice->code }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $invoice->id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $invoice->customer->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ Str::limit($invoice->address, 30) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $invoice->receive_date ? \Carbon\Carbon::parse($invoice->receive_date)->format('d M Y') : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        Retur
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $invoice->driver ? $invoice->driver->name : 'Belum ditugaskan' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.assign-driver.create', $invoice->id) }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-medium rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Assign Driver
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m5 3v6a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h2"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak ada pengiriman retur</h3>
                    <p class="text-gray-500">Pengiriman retur akan muncul di sini setelah ada barang retur</p>
                </div>
            </div>
        @endif
    </div>

    <script>
        document.getElementById('driverReportBtn').addEventListener('click', function() {
            const dropdown = document.getElementById('driverReportDropdown');
            dropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('driverReportDropdown');
            const button = document.getElementById('driverReportBtn');

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
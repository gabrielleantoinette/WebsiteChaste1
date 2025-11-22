@extends('layouts.admin')

@section('content')
    {{-- Header dengan Gradient Background --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl mb-4 sm:mb-6 lg:mb-8">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-4 sm:px-6 lg:px-8 py-4 sm:py-5 lg:py-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mb-1 sm:mb-2">📊 Dashboard Owner</h1>
                    <p class="text-sm sm:text-base text-teal-100">Selamat datang di panel administrasi</p>
                </div>
                <div class="flex gap-3">
                    <div class="text-right text-white">
                        <p class="text-xs sm:text-sm opacity-90">Tanggal</p>
                        <p class="text-base sm:text-lg font-semibold">{{ now()->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan Statistik dengan Card Modern --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 lg:gap-6 mb-4 sm:mb-6 lg:mb-8">
        <div class="bg-white rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Total Produk</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">{{ $productCount ?? 0 }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-teal-100 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Total Pembeli</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">{{ $customerCount ?? 0 }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-blue-100 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Total Pegawai</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">{{ $employeeCount ?? 0 }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-green-100 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Penjualan Hari Ini</p>
                    <p class="text-lg sm:text-xl lg:text-2xl font-bold text-emerald-600 truncate">Rp {{ number_format($totalPenjualan ?? 0) }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-emerald-100 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Transaksi Terbaru dengan Card Layout - Responsive --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-4 sm:mb-6 lg:mb-8">
        <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Transaksi Hari Ini
            </h3>
        </div>
        
        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($recentInvoices ?? [] as $invoice)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-medium text-teal-600">{{ $invoice->code }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-900">{{ $invoice->customer->name }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-semibold text-emerald-600">Rp {{ number_format($invoice->grand_total) }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-600">{{ $invoice->receive_date ? \Carbon\Carbon::parse($invoice->receive_date)->format('d M Y') : ($invoice->created_at ? $invoice->created_at->format('d M Y') : '-') }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $invoice->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 
                                       ($invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 sm:px-6 py-8 sm:py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Belum ada transaksi hari ini</h3>
                                    <p class="text-sm text-gray-500">Transaksi akan muncul di sini setelah ada penjualan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse ($recentInvoices ?? [] as $invoice)
                <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-teal-600 truncate">{{ $invoice->code }}</p>
                            <p class="text-sm text-gray-900 truncate">{{ $invoice->customer->name }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold ml-2 flex-shrink-0
                            {{ $invoice->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 
                               ($invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <p class="text-xs text-gray-600">{{ $invoice->receive_date ? \Carbon\Carbon::parse($invoice->receive_date)->format('d M Y') : ($invoice->created_at ? $invoice->created_at->format('d M Y') : '-') }}</p>
                        <p class="text-sm font-semibold text-emerald-600">Rp {{ number_format($invoice->grand_total) }}</p>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900 mb-2">Belum ada transaksi hari ini</h3>
                        <p class="text-sm text-gray-500">Transaksi akan muncul di sini setelah ada penjualan</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Charts Section - Responsive --}}
    <div class="space-y-4 sm:space-y-6 lg:space-y-8 mb-4 sm:mb-6 lg:mb-8">
        {{-- Grafik Penjualan 7 Hari Terakhir - Full Width --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 sm:p-5 lg:p-6">
            <h3 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800 mb-4 sm:mb-5 lg:mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Penjualan 7 Hari Terakhir
            </h3>
            <div class="h-64 sm:h-72 lg:h-80">
                <canvas id="dailySalesChart"></canvas>
            </div>
        </div>

        {{-- Grafik Penjualan 30 Hari Terakhir - Full Width --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 sm:p-5 lg:p-6">
            <h3 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800 mb-4 sm:mb-5 lg:mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Penjualan 30 Hari Terakhir
            </h3>
            <div class="h-64 sm:h-72 lg:h-80">
                <canvas id="monthlySalesChart"></canvas>
            </div>
        </div>

        {{-- Grafik Penjualan 12 Bulan Terakhir - Full Width --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 sm:p-5 lg:p-6">
            <h3 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800 mb-4 sm:mb-5 lg:mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Penjualan 12 Bulan Terakhir
            </h3>
            <div class="h-64 sm:h-72 lg:h-80">
                <canvas id="yearlySalesChart"></canvas>
            </div>
        </div>

        {{-- Grafik Penjualan 5 Tahun Terakhir - Full Width --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 sm:p-5 lg:p-6">
            <h3 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800 mb-4 sm:mb-5 lg:mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Penjualan 5 Tahun Terakhir
            </h3>
            <div class="h-64 sm:h-72 lg:h-80">
                <canvas id="yearlyComparisonChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart.js dengan konfigurasi sederhana untuk performa yang lebih baik --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Initialize charts dengan konfigurasi sederhana
        document.addEventListener('DOMContentLoaded', function() {
            // Grafik Penjualan 7 Hari Terakhir
            const dailyCtx = document.getElementById('dailySalesChart').getContext('2d');
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($dailyLabels ?? []) !!},
                    datasets: [{
                        label: 'Penjualan',
                        data: {!! json_encode($dailySales ?? []) !!},
                        borderColor: 'rgb(20, 184, 166)',
                        backgroundColor: 'rgba(20, 184, 166, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });

            // Grafik Penjualan 30 Hari Terakhir
            const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyLabels ?? []) !!},
                    datasets: [{
                        label: 'Penjualan',
                        data: {!! json_encode($monthlySales ?? []) !!},
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });

            // Grafik Penjualan 12 Bulan Terakhir
            const yearlyCtx = document.getElementById('yearlySalesChart').getContext('2d');
            new Chart(yearlyCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($yearLabels ?? []) !!},
                    datasets: [{
                        label: 'Penjualan',
                        data: {!! json_encode($yearSales ?? []) !!},
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });

            // Grafik Penjualan 5 Tahun Terakhir
            const yearlyComparisonCtx = document.getElementById('yearlyComparisonChart').getContext('2d');
            new Chart(yearlyComparisonCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($yearlyLabels ?? []) !!},
                    datasets: [{
                        label: 'Penjualan',
                        data: {!! json_encode($yearlySales ?? []) !!},
                        backgroundColor: 'rgba(147, 51, 234, 0.8)',
                        borderColor: 'rgb(147, 51, 234)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
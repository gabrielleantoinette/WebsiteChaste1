@extends('layouts.admin')

@section('content')
    {{-- Header dengan Gradient Background --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-teal-500 via-teal-600 to-teal-700 rounded-xl mb-4 sm:mb-6 lg:mb-8">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-4 sm:px-6 lg:px-8 py-4 sm:py-5 lg:py-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mb-1 sm:mb-2">📊 Dashboard Admin</h1>
                    <p class="text-sm sm:text-base text-blue-100">Panel administrasi untuk mengelola sistem</p>
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

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 lg:gap-6 mb-6 sm:mb-8 lg:mb-10">
        <!-- Box 1: Pesanan Belum Diproses -->
        <div class="bg-white p-4 sm:p-5 rounded-lg shadow border border-gray-200">
            <h2 class="text-xs sm:text-sm text-gray-600 mb-2">Pesanan Belum Diproses</h2>
            <p class="text-xl sm:text-2xl font-bold text-teal-600">{{ $pendingOrders->count() ?? 0 }}</p>
            <a href="{{ url('/admin/invoices') }}" class="text-xs sm:text-sm text-blue-600 hover:underline mt-2 inline-block">Lihat Detail</a>
        </div>

        <!-- Box 2: Stok Hampir Habis -->
        <div class="bg-white p-4 sm:p-5 rounded-lg shadow border border-gray-200">
            <h2 class="text-xs sm:text-sm text-gray-600 mb-2">Stok Hampir Habis</h2>
            <p class="text-xl sm:text-2xl font-bold text-red-500">{{ $lowStocks->count() ?? 0 }}</p>
            <a href="{{ url('/admin/products') }}" class="text-xs sm:text-sm text-blue-600 hover:underline mt-2 inline-block">Lihat Detail</a>
        </div>

        <!-- Box 3: Permintaan Retur -->
        <div class="bg-white p-4 sm:p-5 rounded-lg shadow border border-gray-200">
            <h2 class="text-xs sm:text-sm text-gray-600 mb-2">Permintaan Retur</h2>
            <p class="text-xl sm:text-2xl font-bold text-yellow-600">{{ $returCount ?? 0 }}</p>
            <a href="{{ route('admin.retur.index') }}" class="text-xs sm:text-sm text-blue-600 hover:underline mt-2 inline-block">Lihat Detail</a>
        </div>
    </div>

    <div class="mt-6 sm:mt-8 lg:mt-10">
        <h2 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800">Pesanan yang Harus Diproses</h2>
        <div class="bg-white rounded-lg shadow border border-gray-200 p-3 sm:p-4">
            <ul class="text-xs sm:text-sm divide-y divide-gray-100">
                @forelse ($pendingOrders as $order)
                    <li class="py-2 truncate">#{{ $order->code }} - {{ $order->customer->name }} ({{ $order->status }})</li>
                @empty
                    <li class="py-2 text-gray-400 italic">Tidak ada pesanan menunggu proses.</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Charts Section untuk Admin - Responsive --}}
    <div class="mt-6 sm:mt-8 lg:mt-10 space-y-4 sm:space-y-6 lg:space-y-8">
        {{-- Grafik Pesanan 7 Hari Terakhir - Full Width --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 sm:p-5 lg:p-6">
            <h3 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800 mb-4 sm:mb-5 lg:mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Pesanan 7 Hari Terakhir
            </h3>
            <div class="h-64 sm:h-72 lg:h-80">
                <canvas id="adminOrdersChart"></canvas>
            </div>
        </div>

        {{-- Grafik Status Pesanan - Full Width --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 sm:p-5 lg:p-6">
            <h3 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800 mb-4 sm:mb-5 lg:mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                Status Pesanan
            </h3>
            <div class="h-64 sm:h-72 lg:h-80">
                <canvas id="adminStatusChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart.js untuk Admin Dashboard --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Grafik Pesanan 7 Hari Terakhir
            const ordersCtx = document.getElementById('adminOrdersChart').getContext('2d');
            const ordersChartLabels = @json(($ordersChartLabels ?? collect())->values());
            const ordersChartData = @json(($ordersChartData ?? collect())->values());

            const ordersLabels = ordersChartLabels.length ? ordersChartLabels : ['-'];
            const ordersData = ordersChartData.length ? ordersChartData : [0];

            const tealLineColor = 'rgb(20, 184, 166)';
            const tealFillColor = 'rgba(20, 184, 166, 0.15)';

            new Chart(ordersCtx, {
                type: 'line',
                data: {
                    labels: ordersLabels,
                    datasets: [{
                        label: 'Jumlah Pesanan',
                        data: ordersData,
                        borderColor: tealLineColor,
                        backgroundColor: tealFillColor,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
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
                                stepSize: 5
                            }
                        }
                    }
                }
            });

            // Grafik Status Pesanan
            const statusCtx = document.getElementById('adminStatusChart').getContext('2d');
            const statusChartLabels = @json(($statusChartLabels ?? []));
            const rawStatusData = @json(($statusChartData ?? []));
            const statusLabels = statusChartLabels.length ? statusChartLabels : ['Tidak Ada Data'];
            const statusData = rawStatusData.length ? rawStatusData : [1];

            const baseColors = [
                'rgba(13, 148, 136, 0.85)',   // teal-600
                'rgba(45, 212, 191, 0.85)',   // teal-400
                'rgba(15, 118, 110, 0.85)',   // teal-700
                'rgba(94, 234, 212, 0.85)',   // teal-300
                'rgba(20, 83, 75, 0.85)'      // teal-800
            ];
            const borderColors = [
                'rgb(13, 148, 136)',
                'rgb(45, 212, 191)',
                'rgb(15, 118, 110)',
                'rgb(94, 234, 212)',
                'rgb(20, 83, 75)'
            ];

            const computedBackground = statusLabels.map((_, idx) => baseColors[idx % baseColors.length]);
            const computedBorder = statusLabels.map((_, idx) => borderColors[idx % borderColors.length]);

            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: computedBackground,
                        borderColor: computedBorder,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection

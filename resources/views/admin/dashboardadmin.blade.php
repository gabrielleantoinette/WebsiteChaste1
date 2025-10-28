@extends('layouts.admin')

@section('content')
    {{-- Header dengan Gradient Background --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl mb-8">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">📊 Dashboard Admin</h1>
                    <p class="text-blue-100">Panel administrasi untuk mengelola sistem</p>
                </div>
                <div class="flex gap-3">
                    <div class="text-right text-white">
                        <p class="text-sm opacity-90">Tanggal</p>
                        <p class="text-lg font-semibold">{{ now()->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- Box 1: Pesanan Belum Diproses -->
        <div class="bg-white p-5 rounded-lg shadow border border-gray-200">
            <h2 class="text-sm text-gray-600 mb-2">Pesanan Belum Diproses</h2>
            <p class="text-2xl font-bold text-teal-600">{{ $pendingOrders->count() ?? 0 }}</p>
            <a href="{{ url('/admin/invoices') }}" class="text-sm text-blue-600 hover:underline mt-2 inline-block">Lihat Detail</a>
        </div>

        <!-- Box 2: Stok Hampir Habis -->
        <div class="bg-white p-5 rounded-lg shadow border border-gray-200">
            <h2 class="text-sm text-gray-600 mb-2">Stok Hampir Habis</h2>
            <p class="text-2xl font-bold text-red-500">{{ $lowStocks->count() ?? 0 }}</p>
            <a href="{{ url('/admin/products') }}" class="text-sm text-blue-600 hover:underline mt-2 inline-block">Lihat Detail</a>
        </div>

        <!-- Box 3: Permintaan Retur -->
        <div class="bg-white p-5 rounded-lg shadow border border-gray-200">
            <h2 class="text-sm text-gray-600 mb-2">Permintaan Retur</h2>
            <p class="text-2xl font-bold text-yellow-600">{{ $returCount ?? 0 }}</p>
            <a href="{{ route('admin.retur.index') }}" class="text-sm text-blue-600 hover:underline mt-2 inline-block">Lihat Detail</a>
        </div>
    </div>

    <div class="mt-10">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Pesanan yang Harus Diproses</h2>
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <ul class="text-sm divide-y divide-gray-100">
                @forelse ($pendingOrders as $order)
                    <li class="py-2">#{{ $order->code }} - {{ $order->customer->name }} ({{ $order->status }})</li>
                @empty
                    <li class="py-2 text-gray-400 italic">Tidak ada pesanan menunggu proses.</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Charts Section untuk Admin - LAYOUT MENYAMPING KE KANAN --}}
    <div class="mt-10 space-y-8">
        {{-- Grafik Pesanan 7 Hari Terakhir - Full Width --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Pesanan 7 Hari Terakhir
            </h3>
            <div class="h-80">
                <canvas id="adminOrdersChart"></canvas>
            </div>
        </div>

        {{-- Grafik Status Pesanan - Full Width --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                Status Pesanan
            </h3>
            <div class="h-80">
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
            new Chart(ordersCtx, {
                type: 'line',
                data: {
                    labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                    datasets: [{
                        label: 'Jumlah Pesanan',
                        data: [12, 19, 8, 15, 22, 18, 25],
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
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
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Menunggu Pembayaran', 'Diproses', 'Selesai', 'Dibatalkan'],
                    datasets: [{
                        data: [{{ $pendingOrders->where('status', 'Menunggu Pembayaran')->count() }}, 
                               {{ $pendingOrders->where('status', 'Diproses')->count() }}, 
                               15, 3],
                        backgroundColor: [
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(107, 114, 128, 0.8)'
                        ],
                        borderColor: [
                            'rgb(239, 68, 68)',
                            'rgb(245, 158, 11)',
                            'rgb(34, 197, 94)',
                            'rgb(107, 114, 128)'
                        ],
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

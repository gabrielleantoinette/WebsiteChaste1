@extends('layouts.admin')

@section('content')
    <div class="py-6">

        {{-- Ringkasan Statistik --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                <p class="text-sm text-gray-500 mb-1">Total Produk</p>
                <p class="text-2xl font-bold text-teal-600">{{ $productCount ?? 0 }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                <p class="text-sm text-gray-500 mb-1">Total Pembeli</p>
                <p class="text-2xl font-bold text-teal-600">{{ $customerCount ?? 0 }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                <p class="text-sm text-gray-500 mb-1">Total Pegawai</p>
                <p class="text-2xl font-bold text-teal-600">{{ $employeeCount ?? 0 }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                <p class="text-sm text-gray-500 mb-1">Total Penjualan Hari Ini</p>
                <p class="text-2xl font-bold text-teal-600">Rp {{ number_format($totalPenjualan ?? 0) }}</p>
            </div>
        </div>

        {{-- Transaksi Terbaru --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Transaksi Hari Ini</h2>
            <table class="w-full text-sm text-gray-700">
                <thead class="bg-[#D9F2F2] text-gray-800 font-medium">
                    <tr>
                        <th class="px-4 py-2 text-left">Invoice</th>
                        <th class="px-4 py-2 text-left">Customer</th>
                        <th class="px-4 py-2 text-left">Total</th>
                        <th class="px-4 py-2 text-left">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentInvoices ?? [] as $invoice)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $invoice->code }}</td>
                            <td class="px-4 py-2">{{ $invoice->customer->name }}</td>
                            <td class="px-4 py-2">Rp {{ number_format($invoice->grand_total) }}</td>
                            <td class="px-4 py-2">{{ $invoice->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    {{-- Grafik Penjualan 7 Hari Terakhir --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mt-10">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Penjualan 7 Hari Terakhir</h2>
        <canvas id="salesChart" height="100"></canvas>
    </div>
    {{-- Grafik Penjualan 30 Hari Terakhir --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mt-10">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Penjualan 30 Hari Terakhir</h2>
        <canvas id="monthlySalesChart" height="100"></canvas>
    </div>

    {{-- Grafik Penjualan 12 Bulan Terakhir --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mt-10">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Penjualan 12 Bulan Terakhir</h2>
        <canvas id="yearlySalesChart" height="100"></canvas>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Grafik 7 hari terakhir (sudah ada)
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($days) !!},
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: {!! json_encode($sales) !!},
                    borderColor: '#14b8a6',
                    backgroundColor: 'rgba(20, 184, 166, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            }
        });
    
        // Grafik 30 hari terakhir
        const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyLabels) !!},
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: {!! json_encode($monthlySales) !!},
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14, 165, 233, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            }
        });
    
        // Grafik 12 bulan terakhir
        const yearlyCtx = document.getElementById('yearlySalesChart').getContext('2d');
        new Chart(yearlyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($yearLabels) !!},
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: {!! json_encode($yearSales) !!},
                    backgroundColor: 'rgba(52, 211, 153, 0.6)',
                    borderColor: '#34d399',
                    borderWidth: 1
                }]
            }
        });
    </script>
    @endpush    
    </div>
@endsection

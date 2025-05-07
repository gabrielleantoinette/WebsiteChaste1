@extends('layouts.admin')

@section('content')
    <div class="py-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

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
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Transaksi Terbaru</h2>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
    </div>
@endsection

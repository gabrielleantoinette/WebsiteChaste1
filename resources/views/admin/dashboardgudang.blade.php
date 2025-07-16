@extends('layouts.admin')

@section('content')
<div class="py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Gudang</h1>

    {{-- Ringkasan Pesanan Siap Proses --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Pesanan Siap Diproses</p>
            <p class="text-2xl font-bold text-teal-600">{{ $siapProsesCount ?? 0 }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Total Produk Perlu Disiapkan</p>
            <p class="text-2xl font-bold text-teal-600">{{ $totalProdukDisiapkan ?? 0 }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Tanggal</p>
            <p class="text-2xl font-bold text-teal-600">{{ now()->format('d M Y') }}</p>
        </div>
    </div>

    {{-- Daftar Pesanan Siap Proses --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-10">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Daftar Pesanan Siap Diproses Gudang</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Kode Invoice</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Total</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-teal-50 transition">
                            <td class="px-4 py-3 font-mono text-teal-700 font-semibold">{{ $order->code }}</td>
                            <td class="px-4 py-3">{{ $order->customer->name ?? '-' }}</td>
                            <td class="px-4 py-3 font-semibold">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-teal-100 text-teal-700">{{ ucfirst($order->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-gray-500 py-4">Tidak ada pesanan siap diproses.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Rangkuman Produk/Terpal Perlu Disiapkan --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Produk/Terpal Perlu Disiapkan</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nama Produk/Terpal</th>
                        <th class="px-4 py-3 text-left font-semibold">Total Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($produkDisiapkan as $produk)
                        <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-teal-50 transition">
                            <td class="px-4 py-3">{{ $produk['nama'] }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $produk['qty'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-gray-500 py-4">Tidak ada produk perlu disiapkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 
@extends('layouts.admin')

@section('content')
<div class="py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Gudang</h1>

    {{-- Ringkasan Pesanan Siap Proses --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-10">
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Pesanan Siap Diproses</p>
            <p class="text-2xl font-bold text-teal-600">{{ $siapProsesCount ?? 0 }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Total Produk Perlu Disiapkan</p>
            <p class="text-2xl font-bold text-teal-600">{{ $totalProdukDisiapkan ?? 0 }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Returan Perlu Diproses</p>
            <p class="text-2xl font-bold text-orange-600">{{ $returanCount ?? 0 }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Surat Perintah Menunggu</p>
            <p class="text-2xl font-bold text-blue-600">{{ $workOrderStats['pending'] ?? 0 }}</p>
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
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-10">
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

    {{-- Daftar Returan Perlu Diproses --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-10">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-700">Returan Perlu Diproses</h2>
            <a href="{{ route('gudang.barang-rusak') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded text-sm transition">
                Lihat Barang Rusak
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">ID Retur</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Invoice</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($returans as $retur)
                        <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-orange-50 transition">
                            <td class="px-4 py-3 font-mono text-orange-700 font-semibold">#{{ $retur->id }}</td>
                            <td class="px-4 py-3">{{ $retur->invoice->customer->name ?? '-' }}</td>
                            <td class="px-4 py-3 font-mono">{{ $retur->invoice->code ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold 
                                    @if($retur->status === 'diajukan') bg-yellow-100 text-yellow-700
                                    @elseif($retur->status === 'diproses') bg-blue-100 text-blue-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ ucfirst($retur->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $retur->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-gray-500 py-4">Tidak ada returan perlu diproses.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Daftar Surat Perintah Kerja --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-700">Surat Perintah Kerja</h2>
            <a href="{{ url('/gudang/work-orders') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition">
                Lihat Semua
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold">Dibuat Oleh</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Progress</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($workOrders as $workOrder)
                        <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-blue-50 transition">
                            <td class="px-4 py-3 font-mono text-blue-700 font-semibold">{{ $workOrder->code }}</td>
                            <td class="px-4 py-3">{{ $workOrder->createdBy->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold {{ $workOrder->status_color }}">
                                    {{ $workOrder->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-12 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $workOrder->progress_percentage }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600">{{ $workOrder->progress_percentage }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $workOrder->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ url('/gudang/work-orders/' . $workOrder->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-gray-500 py-4">Tidak ada surat perintah kerja yang ditugaskan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 
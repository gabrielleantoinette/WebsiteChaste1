@extends('layouts.admin')

@section('content')
<div class="py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Pengiriman (Kurir/Driver)</h1>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Pesanan Siap Dikirim</p>
            <p class="text-2xl font-bold text-teal-600">{{ $ordersReady->count() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Retur Siap Diambil</p>
            <p class="text-2xl font-bold text-teal-600">{{ $returnsReady->count() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Pengiriman Selesai</p>
            <p class="text-2xl font-bold text-teal-600">{{ $ordersHistory->count() }}</p>
        </div>
    </div>

    {{-- Daftar Pesanan Siap Dikirim --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-10">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Daftar Pesanan Siap Dikirim</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Kode Invoice</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Alamat Pengiriman</th>
                        <th class="px-4 py-3 text-left font-semibold">Detail Barang</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ordersReady as $order)
                        <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-teal-50 transition">
                            <td class="px-4 py-3 font-mono text-teal-700 font-semibold">{{ $order->code }}</td>
                            <td class="px-4 py-3">{{ $order->customer->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $order->address }}</td>
                            <td class="px-4 py-3">
                                <ul>
                                    @foreach ($order->details as $item)
                                        <li>{{ $item->product->name ?? '-' }} @if($item->variant) ({{ $item->variant->color }}) @endif x{{ $item->quantity }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-teal-100 text-teal-700">{{ ucfirst($order->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-gray-500 py-4">Tidak ada pesanan siap dikirim.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Daftar Retur Siap Diambil --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-10">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Daftar Retur Siap Diambil</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Kode Invoice</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Alamat</th>
                        <th class="px-4 py-3 text-left font-semibold">Deskripsi Retur</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($returnsReady as $retur)
                        <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-teal-50 transition">
                            <td class="px-4 py-3 font-mono text-teal-700 font-semibold">{{ $retur->invoice->code ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $retur->invoice->customer->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $retur->invoice->address ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $retur->description }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-gray-500 py-4">Tidak ada retur siap diambil.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- History Pengiriman Selesai --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">History Pengiriman Selesai</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Kode Invoice</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Alamat Pengiriman</th>
                        <th class="px-4 py-3 text-left font-semibold">Detail Barang</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ordersHistory as $order)
                        <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-teal-50 transition">
                            <td class="px-4 py-3 font-mono text-teal-700 font-semibold">{{ $order->code }}</td>
                            <td class="px-4 py-3">{{ $order->customer->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $order->address }}</td>
                            <td class="px-4 py-3">
                                <ul>
                                    @foreach ($order->details as $item)
                                        <li>{{ $item->product->name ?? '-' }} @if($item->variant) ({{ $item->variant->color }}) @endif x{{ $item->quantity }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-4 py-3">{{ $order->updated_at ? $order->updated_at->format('d M Y H:i') : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-gray-500 py-4">Belum ada history pengiriman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 
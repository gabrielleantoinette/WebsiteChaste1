@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard Pengiriman (Kurir/Driver)</h1>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-600">
                <i class="fas fa-bell mr-2"></i>
                <span id="notificationCount">0</span> notifikasi baru
            </div>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Pesanan Siap Dikirim</p>
            <p class="text-2xl font-bold text-teal-600">{{ $ordersReady->count() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Retur Siap Diambil</p>
            <p class="text-2xl font-bold text-orange-600">{{ $returnsReady->count() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Pengiriman Selesai</p>
            <p class="text-2xl font-bold text-green-600">{{ $ordersHistory->count() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Retur Selesai</p>
            <p class="text-2xl font-bold text-green-600">{{ $returnsHistory->count() }}</p>
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
                        <th class="px-4 py-3 text-left font-semibold">Aksi</th>
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
                            <td class="px-4 py-3">
                                <a href="{{ url('/driver-transaksi/detail/' . $order->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-gray-500 py-4">Tidak ada pesanan siap dikirim.</td></tr>
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
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($returnsReady as $retur)
                        <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-orange-50 transition">
                            <td class="px-4 py-3 font-mono text-orange-700 font-semibold">{{ $retur->code }}</td>
                            <td class="px-4 py-3">{{ $retur->customer->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $retur->address }}</td>
                            <td class="px-4 py-3">
                                @if($retur->returns && $retur->returns->count() > 0)
                                    {{ $retur->returns->first()->description }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">{{ ucfirst($retur->status) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ url('/admin/driver-retur/detail/' . $retur->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detail</a>
                                                                            <form method="POST" action="{{ url('/admin/driver-retur/pickup/' . $retur->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 text-sm" onclick="return confirm('Konfirmasi pengambilan retur ini?')">
                                            Ambil Retur
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-gray-500 py-4">Tidak ada retur siap diambil.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- History Pengiriman Selesai --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-10">
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

    {{-- History Pengambilan Retur Selesai --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">History Pengambilan Retur Selesai</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Kode Invoice</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Alamat</th>
                        <th class="px-4 py-3 text-left font-semibold">Deskripsi Retur</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($returnsHistory as $retur)
                        <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-green-50 transition">
                            <td class="px-4 py-3 font-mono text-green-700 font-semibold">{{ $retur->code }}</td>
                            <td class="px-4 py-3">{{ $retur->customer->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $retur->address }}</td>
                            <td class="px-4 py-3">
                                @if($retur->returns && $retur->returns->count() > 0)
                                    {{ $retur->returns->first()->description }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $retur->updated_at ? $retur->updated_at->format('d M Y H:i') : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-gray-500 py-4">Belum ada history pengambilan retur.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 
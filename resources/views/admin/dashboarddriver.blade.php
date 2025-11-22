@extends('layouts.admin')

@section('content')
<div class="py-4 sm:py-5 lg:py-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 sm:mb-5 lg:mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Dashboard Pengiriman (Kurir/Driver)</h1>
        <div class="flex items-center space-x-4">
            <div class="text-xs sm:text-sm text-gray-600">
                <i class="fas fa-bell mr-2"></i>
                <span id="notificationCount">0</span> notifikasi baru
            </div>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 lg:gap-6 mb-6 sm:mb-8 lg:mb-10">
        <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-5 shadow-sm">
            <p class="text-xs sm:text-sm text-gray-500 mb-1">Pesanan Siap Dikirim</p>
            <p class="text-xl sm:text-2xl font-bold text-teal-600">{{ $ordersReady->count() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-5 shadow-sm">
            <p class="text-xs sm:text-sm text-gray-500 mb-1">Retur Siap Diambil</p>
            <p class="text-xl sm:text-2xl font-bold text-orange-600">{{ $returnsReady->count() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-5 shadow-sm">
            <p class="text-xs sm:text-sm text-gray-500 mb-1">Pengiriman Selesai</p>
            <p class="text-xl sm:text-2xl font-bold text-green-600">{{ $ordersHistory->count() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-5 shadow-sm">
            <p class="text-xs sm:text-sm text-gray-500 mb-1">Retur Selesai</p>
            <p class="text-xl sm:text-2xl font-bold text-green-600">{{ $returnsHistory->count() }}</p>
        </div>
    </div>

    {{-- Daftar Pesanan Siap Dikirim --}}
    <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-5 lg:p-6 shadow-sm mb-6 sm:mb-8 lg:mb-10">
        <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3 sm:mb-4">Daftar Pesanan Siap Dikirim</h2>
        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
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
                                <span class="inline-block px-2 sm:px-3 py-1 rounded-full text-xs font-semibold bg-teal-100 text-teal-700">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('driver-transaksi.detail', $order->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-gray-500 py-4">Tidak ada pesanan siap dikirim.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse ($ordersReady as $order)
                <div class="p-3 sm:p-4 hover:bg-teal-50 transition">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-mono font-semibold text-teal-700 truncate">{{ $order->code }}</p>
                            <p class="text-sm text-gray-800 truncate">{{ $order->customer->name ?? '-' }}</p>
                        </div>
                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-teal-100 text-teal-700 ml-2 flex-shrink-0">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <p class="text-xs text-gray-600 mb-1">Alamat:</p>
                        <p class="text-sm text-gray-800 break-words">{{ $order->address }}</p>
                    </div>
                    <div class="mb-2">
                        <p class="text-xs text-gray-600 mb-1">Detail Barang:</p>
                        <ul class="text-xs text-gray-800 space-y-1">
                            @foreach ($order->details->take(2) as $item)
                                <li>{{ $item->product->name ?? '-' }} @if($item->variant) ({{ $item->variant->color }}) @endif x{{ $item->quantity }}</li>
                            @endforeach
                            @if($order->details->count() > 2)
                                <li class="text-gray-500">+{{ $order->details->count() - 2 }} item lainnya</li>
                            @endif
                        </ul>
                    </div>
                    <a href="{{ route('driver-transaksi.detail', $order->id) }}" class="inline-block text-center w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md transition mt-2">
                        Detail
                    </a>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">Tidak ada pesanan siap dikirim.</div>
            @endforelse
        </div>
    </div>

    {{-- Daftar Retur Siap Diambil --}}
    <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-5 lg:p-6 shadow-sm mb-6 sm:mb-8 lg:mb-10">
        <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3 sm:mb-4">Daftar Retur Siap Diambil</h2>
        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
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
                                    {{ Str::limit($retur->returns->first()->description, 50) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 sm:px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">{{ ucfirst($retur->status) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('driver-retur.detail', $retur->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detail</a>
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
        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse ($returnsReady as $retur)
                <div class="p-3 sm:p-4 hover:bg-orange-50 transition">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-mono font-semibold text-orange-700 truncate">{{ $retur->code }}</p>
                            <p class="text-sm text-gray-800 truncate">{{ $retur->customer->name ?? '-' }}</p>
                        </div>
                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700 ml-2 flex-shrink-0">
                            {{ ucfirst($retur->status) }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <p class="text-xs text-gray-600 mb-1">Alamat:</p>
                        <p class="text-sm text-gray-800 break-words">{{ $retur->address }}</p>
                    </div>
                    @if($retur->returns && $retur->returns->count() > 0)
                    <div class="mb-2">
                        <p class="text-xs text-gray-600 mb-1">Deskripsi:</p>
                        <p class="text-sm text-gray-800 break-words">{{ Str::limit($retur->returns->first()->description, 100) }}</p>
                    </div>
                    @endif
                    <div class="flex gap-2 mt-3">
                        <a href="{{ route('driver-retur.detail', $retur->id) }}" class="flex-1 text-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md transition">
                            Detail
                        </a>
                        <form method="POST" action="{{ url('/admin/driver-retur/pickup/' . $retur->id) }}" class="flex-1" onsubmit="return confirm('Konfirmasi pengambilan retur ini?')">
                            @csrf
                            <button type="submit" class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-md transition">
                                Ambil Retur
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">Tidak ada retur siap diambil.</div>
            @endforelse
        </div>
    </div>

    {{-- History Pengiriman Selesai --}}
    <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-5 lg:p-6 shadow-sm mb-6 sm:mb-8 lg:mb-10">
        <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3 sm:mb-4">History Pengiriman Selesai</h2>
        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
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
                                    @foreach ($order->details->take(3) as $item)
                                        <li>{{ $item->product->name ?? '-' }} @if($item->variant) ({{ $item->variant->color }}) @endif x{{ $item->quantity }}</li>
                                    @endforeach
                                    @if($order->details->count() > 3)
                                        <li class="text-gray-500">+{{ $order->details->count() - 3 }} item lainnya</li>
                                    @endif
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
        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse ($ordersHistory as $order)
                <div class="p-3 sm:p-4 hover:bg-teal-50 transition">
                    <div class="mb-2">
                        <p class="text-sm font-mono font-semibold text-teal-700">{{ $order->code }}</p>
                        <p class="text-sm text-gray-800 truncate">{{ $order->customer->name ?? '-' }}</p>
                    </div>
                    <div class="mb-2">
                        <p class="text-xs text-gray-600 mb-1">Alamat:</p>
                        <p class="text-sm text-gray-800 break-words">{{ $order->address }}</p>
                    </div>
                    <div class="mb-2">
                        <p class="text-xs text-gray-600 mb-1">Detail Barang:</p>
                        <ul class="text-xs text-gray-800 space-y-1">
                            @foreach ($order->details->take(2) as $item)
                                <li>{{ $item->product->name ?? '-' }} @if($item->variant) ({{ $item->variant->color }}) @endif x{{ $item->quantity }}</li>
                            @endforeach
                            @if($order->details->count() > 2)
                                <li class="text-gray-500">+{{ $order->details->count() - 2 }} item lainnya</li>
                            @endif
                        </ul>
                    </div>
                    <p class="text-xs text-gray-600 mt-2">{{ $order->updated_at ? $order->updated_at->format('d M Y H:i') : '-' }}</p>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">Belum ada history pengiriman.</div>
            @endforelse
        </div>
    </div>

    {{-- History Pengambilan Retur Selesai --}}
    <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-5 lg:p-6 shadow-sm">
        <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3 sm:mb-4">History Pengambilan Retur Selesai</h2>
        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
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
                                    {{ Str::limit($retur->returns->first()->description, 50) }}
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
        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse ($returnsHistory as $retur)
                <div class="p-3 sm:p-4 hover:bg-green-50 transition">
                    <div class="mb-2">
                        <p class="text-sm font-mono font-semibold text-green-700">{{ $retur->code }}</p>
                        <p class="text-sm text-gray-800 truncate">{{ $retur->customer->name ?? '-' }}</p>
                    </div>
                    <div class="mb-2">
                        <p class="text-xs text-gray-600 mb-1">Alamat:</p>
                        <p class="text-sm text-gray-800 break-words">{{ $retur->address }}</p>
                    </div>
                    @if($retur->returns && $retur->returns->count() > 0)
                    <div class="mb-2">
                        <p class="text-xs text-gray-600 mb-1">Deskripsi:</p>
                        <p class="text-sm text-gray-800 break-words">{{ Str::limit($retur->returns->first()->description, 100) }}</p>
                    </div>
                    @endif
                    <p class="text-xs text-gray-600 mt-2">{{ $retur->updated_at ? $retur->updated_at->format('d M Y H:i') : '-' }}</p>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">Belum ada history pengambilan retur.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection 
@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Admin</h1>

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
@endsection

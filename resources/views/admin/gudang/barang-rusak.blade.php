@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('gudang.dashboard') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Barang Rusak</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('gudang.laporan.retur.pdf') }}" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition">
                📊 Download Laporan Retur
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produk</th>
                            <th>Variant</th>
                            <th>Quantity</th>
                            <th>Customer</th>
                            <th>Invoice</th>
                            <th>Alasan Kerusakan</th>
                            <th>Status</th>
                            <th>Tanggal Diproses</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($damagedProducts as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->product->name ?? 'Produk tidak ditemukan' }}</td>
                                <td>{{ $item->variant->color ?? '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->retur->invoice->customer->name ?? '-' }}</td>
                                <td>{{ $item->retur->invoice->code ?? '-' }}</td>
                                <td>{{ $item->damage_description }}</td>
                                <td>
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($item->status === 'rusak') bg-red-100 text-red-800
                                        @elseif($item->status === 'diperbaiki') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($item->status === 'rusak')
                                    <form action="{{ route('gudang.barang-rusak.perbaiki', $item->id) }}" method="POST" onsubmit="return confirm('Tandai barang ini sudah diperbaiki?');">
                                        @csrf
                                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs">Sudah Diperbaiki</button>
                                    </form>
                                    @else
                                    <span class="text-green-600 text-xs">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-gray-500">
                                    Belum ada barang rusak yang diproses
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 
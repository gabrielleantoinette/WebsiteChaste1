@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Stok Barang Rusak</h2>
        <a href="{{ route('admin.retur.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition">
            Kembali ke Daftar Retur
        </a>
    </div>

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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-gray-500">
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
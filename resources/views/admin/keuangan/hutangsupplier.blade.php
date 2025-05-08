@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Hutang Supplier</h1>
        <a href="{{ route('keuangan.hutang.create') }}"
        class="bg-teal-600 hover:bg-teal-700 text-white font-semibold px-4 py-2 rounded">
        + Tambah PO Manual
        </a>
        <a href="{{ route('keuangan.hutang.export.pdf') }}"
        class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded">
        Export PDF
        </a>
    </div>

    {{-- Filter & Search (opsional, jika ingin ditambah nanti) --}}

    {{-- Tabel Daftar Hutang --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <form action="{{ route('keuangan.hutang.index') }}" method="GET" class="mb-4 flex gap-2 items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari kode PO atau nama supplier..."
                class="border border-gray-300 px-3 py-2 rounded-md w-64">
            <button type="submit" class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700">
                Cari
            </button>
        </form>        
        <table class="min-w-full text-sm text-left">
            <thead class="bg-teal-100 text-teal-800 uppercase">
                <tr>
                    <th class="px-4 py-3">Tanggal PO</th>
                    <th class="px-4 py-3">Kode PO</th>
                    <th class="px-4 py-3">Supplier</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hutang as $item)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($item->order_date)->format('d M Y') }}</td>
                    <td class="px-4 py-2">{{ $item->code }}</td>
                    <td class="px-4 py-2">{{ $item->supplier->name }}</td>
                    <td class="px-4 py-2">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    <td class="px-4 py-2">
                        <span class="inline-block px-2 py-1 text-xs rounded
                            {{ $item->status == 'belum_dibayar' ? 'bg-red-100 text-red-600' :
                               ($item->status == 'sebagian_dibayar' ? 'bg-yellow-100 text-yellow-700' :
                               'bg-green-100 text-green-700') }}">
                            {{ $item->status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        <a href="{{ route('keuangan.hutang.show', $item->id) }}"
                           class="text-teal-600 hover:underline">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-gray-500">Tidak ada hutang yang tercatat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $hutang->withQueryString()->links() }}
        </div>        
    </div>
</div>
@endsection
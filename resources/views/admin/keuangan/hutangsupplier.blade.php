@extends('layouts.admin')

@section('title', 'Hutang Supplier')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6 text-teal-600">Hutang Supplier</h1>

    {{-- Filter dan Tombol --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('keuangan.hutang.index') }}"
              class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            {{-- KIRI: Search + Filter Status + Tombol Cari --}}
            <div class="flex items-center gap-2">
                <input type="text" name="search"
                       placeholder="Cari kode PO atau nama supplier"
                       value="{{ request('search') }}"
                       class="w-[180px] md:w-[250px] px-4 py-2 rounded-l-md border border-gray-300 focus:ring-teal-500 focus:border-teal-500 text-sm" />
                
                <select name="status" class="px-3 py-2 border border-gray-300 focus:ring-teal-500 focus:border-teal-500 text-sm">
                    <option value="">Semua Status</option>
                    <option value="belum_dibayar" {{ request('status') == 'belum_dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                    <option value="sebagian_dibayar" {{ request('status') == 'sebagian_dibayar' ? 'selected' : '' }}>Sebagian Dibayar</option>
                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
                
                <button type="submit"
                        class="px-4 py-2 bg-teal-600 text-white text-sm rounded-r-md hover:bg-teal-700 transition shrink-0">
                    Cari
                </button>

                @if(request('search') || request('status'))
                    <a href="{{ route('keuangan.hutang.index') }}"
                       class="ml-2 px-3 py-2 bg-gray-200 text-sm rounded-md hover:bg-gray-300 transition text-gray-700">
                        ✕
                    </a>
                @endif
            </div>

            {{-- KANAN: Tombol Tambah & Export --}}
            <div class="flex flex-wrap items-center gap-2 justify-end">
                <a href="{{ route('keuangan.hutang.create') }}"
                   class="px-4 py-2 bg-teal-600 text-white text-sm rounded-md hover:bg-teal-700 transition shrink-0">
                    + Tambah PO Manual
                </a>

                <a href="{{ route('keuangan.hutang.export.pdf', request()->only('search')) }}"
                   class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 transition shrink-0">
                    📄 Export PDF
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel Hutang Supplier --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <h2 class="text-xl font-semibold px-6 py-4 bg-teal-50 text-teal-700 border-b">Daftar Hutang</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left">Tanggal PO</th>
                        <th class="px-6 py-3 text-left">Kode PO</th>
                        <th class="px-6 py-3 text-left">Supplier</th>
                        <th class="px-6 py-3 text-left">Total</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hutang as $item)
                        <tr>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($item->order_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4">{{ $item->code }}</td>
                            <td class="px-6 py-4">{{ $item->supplier->name }}</td>
                            <td class="px-6 py-4">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-2 py-1 text-xs rounded
                                    {{ $item->status == 'belum_dibayar' ? 'bg-red-100 text-red-600' :
                                       ($item->status == 'sebagian_dibayar' ? 'bg-yellow-100 text-yellow-700' :
                                       'bg-green-100 text-green-700') }}">
                                    {{ $item->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('keuangan.hutang.show', $item->id) }}"
                                   class="text-teal-600 hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 py-4">Tidak ada hutang yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($hutang->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan {{ $hutang->firstItem() ?? 0 }} - {{ $hutang->lastItem() ?? 0 }} dari {{ $hutang->total() }} data
                </div>
                <div class="flex items-center space-x-2">
                    {{ $hutang->withQueryString()->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

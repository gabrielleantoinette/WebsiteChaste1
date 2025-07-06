@extends('layouts.admin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6 text-teal-600">Kelola Transaksi</h1>

    {{-- Filter Waktu & Search --}}
    <div class="mb-6">
        <form method="GET" action="" class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-2">
                <label for="filter" class="text-sm font-medium text-gray-700">Filter Waktu:</label>
                <select name="filter" id="filter" onchange="this.form.submit()" class="text-sm px-3 py-2 rounded-md border border-gray-300 focus:ring-teal-500 focus:border-teal-500 transition">
                    <option value="hari" {{ request('filter', $filter) == 'hari' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="minggu" {{ request('filter', $filter) == 'minggu' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="bulan" {{ request('filter', $filter) == 'bulan' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="tahun" {{ request('filter', $filter) == 'tahun' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </div>
            <div class="flex flex-wrap items-center gap-2 justify-end">
                <input type="text" name="search" placeholder="Cari kode/nama/ket" value="{{ request('search', $search) }}" class="w-[180px] md:w-[250px] px-4 py-2 rounded-md border border-gray-300 focus:ring-teal-500 focus:border-teal-500 text-sm" />
                <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-sm rounded-md hover:bg-teal-700 transition shrink-0">Cari</button>
                @if(request('search'))
                    <a href="?filter={{ request('filter', $filter) }}" class="px-3 py-2 bg-gray-200 text-sm rounded-md hover:bg-gray-300 transition text-gray-700">×</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Pendapatan --}}
    <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
        <h2 class="text-xl font-semibold px-6 py-4 bg-teal-50 text-teal-700 border-b">Pendapatan (Penjualan)</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left">Tanggal</th>
                        <th class="px-6 py-3 text-left">Kode</th>
                        <th class="px-6 py-3 text-left">Customer</th>
                        <th class="px-6 py-3 text-left">Total</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Metode Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendapatan as $p)
                        <tr>
                            <td class="px-6 py-4">{{ $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-4">{{ $p->code }}</td>
                            <td class="px-6 py-4">{{ $p->customer->name ?? '-' }}</td>
                            <td class="px-6 py-4">Rp {{ number_format($p->grand_total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ ucfirst($p->status) }}</td>
                            <td class="px-6 py-4">
                                @if($p->payments && $p->payments->count())
                                    {{ $p->payments->pluck('method')->unique()->implode(', ') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-gray-500 py-4">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pengeluaran --}}
    <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
        <h2 class="text-xl font-semibold px-6 py-4 bg-teal-50 text-teal-700 border-b">Pengeluaran (Pembayaran Hutang ke Supplier)</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left">Tanggal Bayar</th>
                        <th class="px-6 py-3 text-left">Supplier</th>
                        <th class="px-6 py-3 text-left">Kode PO</th>
                        <th class="px-6 py-3 text-left">Jumlah Bayar</th>
                        <th class="px-6 py-3 text-left">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengeluaran as $e)
                        <tr>
                            <td class="px-6 py-4">{{ $e->payment_date ? \Carbon\Carbon::parse($e->payment_date)->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-4">{{ $e->purchaseOrder && $e->purchaseOrder->supplier ? $e->purchaseOrder->supplier->name : '-' }}</td>
                            <td class="px-6 py-4">{{ $e->purchaseOrder ? $e->purchaseOrder->code : '-' }}</td>
                            <td class="px-6 py-4">Rp {{ number_format($e->amount_paid, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ $e->notes }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-gray-500 py-4">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Hutang Piutang --}}
    <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
        <h2 class="text-xl font-semibold px-6 py-4 bg-teal-50 text-teal-700 border-b">Hutang Piutang</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left">Tanggal Order</th>
                        <th class="px-6 py-3 text-left">Supplier</th>
                        <th class="px-6 py-3 text-left">Kode PO</th>
                        <th class="px-6 py-3 text-left">Total Hutang</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hutang as $h)
                        <tr>
                            <td class="px-6 py-4">{{ $h->order_date ? \Carbon\Carbon::parse($h->order_date)->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-4">{{ $h->supplier ? $h->supplier->name : '-' }}</td>
                            <td class="px-6 py-4">{{ $h->code }}</td>
                            <td class="px-6 py-4">Rp {{ number_format($h->total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ $h->getStatusLabelAttribute() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-gray-500 py-4">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 
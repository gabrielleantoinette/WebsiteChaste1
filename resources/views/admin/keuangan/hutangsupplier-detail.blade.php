@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-10">
    <h1 class="text-2xl font-semibold mb-4">🧾 Detail Hutang Supplier</h1>

    {{-- Info Utama --}}
    <div class="bg-white p-6 rounded shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p><strong>Kode PO:</strong> {{ $po->code }}</p>
                <p><strong>Supplier:</strong> {{ $po->supplier->name }}</p>
                <p><strong>Kontak:</strong> {{ $po->supplier->contact ?? '-' }}</p>
            </div>
            <div>
                <p><strong>Tanggal Pesan:</strong> {{ \Carbon\Carbon::parse($po->order_date)->format('d M Y') }}</p>
                <p><strong>Jatuh Tempo:</strong> {{ \Carbon\Carbon::parse($po->due_date)->format('d M Y') }}</p>
                <p><strong>Status:</strong>
                    <span class="inline-block px-2 py-1 text-xs rounded
                        {{ $po->status == 'belum_dibayar' ? 'bg-red-100 text-red-600' :
                           ($po->status == 'sebagian_dibayar' ? 'bg-yellow-100 text-yellow-700' :
                           'bg-green-100 text-green-700') }}">
                        {{ $po->status_label }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    {{-- Rincian Item Pembelian --}}
    <div class="bg-white p-6 rounded shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">📦 Daftar Bahan Dibeli</h2>
        <table class="min-w-full text-sm text-left border">
            <thead class="bg-teal-50">
                <tr>
                    <th class="px-4 py-2">Nama Bahan</th>
                    <th class="px-4 py-2">Jumlah</th>
                    <th class="px-4 py-2">Harga Satuan</th>
                    <th class="px-4 py-2">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($po->items as $item)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $item->rawMaterial->name }}</td>
                    <td class="px-4 py-2">{{ $item->quantity }} {{ $item->rawMaterial->unit }}</td>
                    <td class="px-4 py-2">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="px-4 py-2">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="text-right font-semibold mt-4">
            Total: Rp {{ number_format($po->total, 0, ',', '.') }}
        </div>
    </div>

    {{-- Riwayat Pembayaran --}}
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold mb-4">💳 Riwayat Pembayaran</h2>
        @if($po->payments->count())
        <table class="min-w-full text-sm text-left border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Tanggal</th>
                    <th class="px-4 py-2">Jumlah Dibayar</th>
                    <th class="px-4 py-2">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($po->payments as $pay)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') }}</td>
                    <td class="px-4 py-2">Rp {{ number_format($pay->amount_paid, 0, ',', '.') }}</td>
                    <td class="px-4 py-2">{{ $pay->notes ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-gray-500">Belum ada pembayaran tercatat.</p>
        @endif
    </div>
</div>
@endsection

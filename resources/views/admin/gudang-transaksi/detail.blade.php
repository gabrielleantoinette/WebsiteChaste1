@extends('layouts.admin')

@section('content')
<div class="flex justify-center">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 w-full max-w-4xl">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Detail Invoice</h1>

        {{-- Informasi Invoice --}}
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-teal-700 mb-2">Informasi Invoice</h2>
            <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-700">
                <div><strong>Invoice ID:</strong> {{ $invoice->id }}</div>
                <div><strong>Kode Invoice:</strong> {{ $invoice->code }}</div>
                <div><strong>Tanggal Jatuh Tempo:</strong> {{ $invoice->due_date }}</div>
                <div><strong>Tanggal Penerimaan Barang:</strong> {{ $invoice->receive_date }}</div>
            </div>
        </div>

        {{-- Customer --}}
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-teal-700 mb-2">Informasi Customer</h2>
            <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-700">
                <div><strong>Nama:</strong> {{ $invoice->customer->name }}</div>
                <div><strong>Telepon:</strong> {{ $invoice->customer->phone }}</div>
                <div><strong>Email:</strong> {{ $invoice->customer->email }}</div>
            </div>
        </div>

        {{-- Gudang --}}
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-teal-700 mb-2">Staff Gudang</h2>
            @if ($invoice->gudang)
                <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <div><strong>Nama:</strong> {{ $invoice->gudang->name }}</div>
                    <div><strong>Email:</strong> {{ $invoice->gudang->email }}</div>
                    <div><strong>Status:</strong> {{ $invoice->gudang->active ? 'Aktif' : 'Tidak Aktif' }}</div>
                </div>
            @else
                <p class="text-sm text-gray-600 mb-3">Belum ada staff gudang yang ditugaskan.</p>
                <form action="{{ url('/admin/gudang-transaksi/assign-gudang/' . $invoice->id) }}" method="POST">
                    @csrf
                    <button class="bg-teal-600 hover:bg-teal-700 text-white font-semibold px-4 py-2 rounded-md transition">
                        Siapkan Barang
                    </button>
                </form>
            @endif
        </div>

        {{-- Daftar Produk --}}
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-teal-700 mb-4">Daftar Produk</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-gray-700 border border-gray-300 rounded-md">
                    <thead class="bg-[#D9F2F2] text-gray-800 font-medium">
                        <tr>
                            <th class="px-4 py-2 text-left">No</th>
                            <th class="px-4 py-2 text-left">Nama</th>
                            <th class="px-4 py-2 text-left">Harga</th>
                            <th class="px-4 py-2 text-left">Warna</th>
                            <th class="px-4 py-2 text-left">Jumlah</th>
                            <th class="px-4 py-2 text-left">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->details as $detail)
                            <tr class="border-t border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-2">{{ $detail->id }}</td>
                                <td class="px-4 py-2">{{ $detail->product->name }}</td>
                                <td class="px-4 py-2">Rp {{ number_format($detail->price) }}</td>
                                <td class="px-4 py-2">{{ $detail->variant->color }}</td>
                                <td class="px-4 py-2">{{ $detail->quantity }}</td>
                                <td class="px-4 py-2">Rp {{ number_format($detail->subtotal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="text-right mt-4 text-base font-semibold text-gray-800">
                Total: Rp {{ number_format($invoice->grand_total) }}
            </div>
        </div>
    </div>
</div>
@endsection

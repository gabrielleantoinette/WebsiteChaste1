@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Detail Invoice</h1>
    <div>
        <a href="{{ url('/admin/invoices/export-pdf?id=' . $invoice->id) }}"
            class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white font-medium text-sm px-5 py-2 rounded-lg shadow-sm transition">
            📄 Download PDF
        </a>
    </div>
    {{-- Info Invoice --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-3 shadow-sm">
        <h2 class="text-lg font-semibold text-teal-700 mb-2">Informasi Invoice</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
            <div><span class="font-medium">Invoice ID:</span> {{ $invoice->id }}</div>
            <div><span class="font-medium">Kode Invoice:</span> {{ $invoice->code }}</div>
            <div><span class="font-medium">Tanggal Jatuh Tempo:</span> {{ $invoice->due_date }}</div>
            <div><span class="font-medium">Tanggal Penerimaan Barang:</span> {{ $invoice->updated_at }}</div>
        </div>
    </div>

    {{-- Info Customer --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-3 shadow-sm mt-6">
        <h2 class="text-lg font-semibold text-teal-700 mb-2">Customer</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
            <div><span class="font-medium">Nama:</span> {{ $invoice->customer->name }}</div>
            <div><span class="font-medium">Telepon:</span> {{ $invoice->customer->phone }}</div>
            <div><span class="font-medium">Email:</span> {{ $invoice->customer->email }}</div>
        </div>
    </div>

    {{-- Info Pegawai --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-3 shadow-sm mt-6">
        <h2 class="text-lg font-semibold text-teal-700 mb-2">Pegawai</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
            <div><span class="font-medium">Nama:</span> {{ $invoice->employee->name }}</div>
            <div><span class="font-medium">Email:</span> {{ $invoice->employee->email }}</div>
            <div><span class="font-medium">Status:</span> {{ $invoice->employee->active ? 'Aktif' : 'Tidak Aktif' }}</div>
        </div>
    </div>

    {{-- Daftar Produk --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mt-6">
        <h2 class="text-lg font-semibold text-teal-700 mb-4">Daftar Produk</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-gray-700 border border-gray-300 rounded-md">
                <thead class="bg-[#D9F2F2] text-gray-800 font-medium">
                    <tr>
                        <th class="px-4 py-2 text-left">No</th>
                        <th class="px-4 py-2 text-left">Nama</th>
                        <th class="px-4 py-2 text-left">Harga</th>
                        <th class="px-4 py-2 text-left">Warna</th>
                        <th class="px-4 py-2 text-left">Ukuran</th>
                        <th class="px-4 py-2 text-left">Jumlah</th>
                        <th class="px-4 py-2 text-left">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->details as $detail)
                        <tr class="border-t border-gray-200 hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2">{{ $detail->product->name }}</td>
                            <td class="px-4 py-2">Rp {{ number_format($detail->price) }}</td>
                            <td class="px-4 py-2">{{ $detail->variant->color }}</td>
                            <td class="px-4 py-2">
                                @php
                                    $sizeText = '-';
                                    if (!empty($detail->kebutuhan_custom) && preg_match('/\((\d+)x(\d+)\)/', $detail->kebutuhan_custom, $m)) {
                                        $sizeText = $m[1] . 'x' . $m[2];
                                    }
                                @endphp
                                {{ $sizeText }}
                            </td>
                            <td class="px-4 py-2">{{ $detail->quantity }}</td>
                            <td class="px-4 py-2">Rp {{ number_format($detail->subtotal) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="text-right mt-4 space-y-2">
            <div class="text-base text-gray-700">
                <span class="font-medium">Subtotal Produk:</span> 
                Rp {{ number_format($invoice->grand_total - ($invoice->shipping_cost ?? 0), 0, ',', '.') }}
            </div>
            @if($invoice->shipping_cost > 0)
            <div class="text-base text-gray-700">
                <span class="font-medium">Ongkos Kirim:</span>
                @if($invoice->shipping_courier || $invoice->shipping_service)
                    <span class="text-sm text-gray-600">
                        ({{ $invoice->shipping_courier ? ucfirst($invoice->shipping_courier) : 'Kurir Perusahaan' }}
                        @if($invoice->shipping_service)
                            - {{ $invoice->shipping_service }}
                        @endif)
                    </span>
                @endif
                <span class="ml-2">Rp {{ number_format($invoice->shipping_cost, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="text-lg font-semibold text-gray-800 border-t pt-2">
                Total: Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}
            </div>
        </div>
    </div>
@endsection

@extends('layouts.admin')

@section('content')
    <div class="py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detail Transaksi Kurir</h1>
            <a href="{{ url('/admin/driver-transaksi') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm">
                ← Kembali ke Daftar Transaksi
            </a>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Informasi Invoice</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Invoice ID</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Kode Invoice</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->code }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold 
                        @if($invoice->status == 'dikirim') bg-teal-100 text-teal-700
                        @elseif($invoice->status == 'sampai') bg-green-100 text-green-700
                        @else bg-gray-100 text-gray-700
                        @endif">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Pembayaran</p>
                    <p class="font-semibold text-gray-800">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal Jatuh Tempo</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->due_date ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal Penerimaan</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->receive_date ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Informasi Customer</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Nama Customer</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->customer->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Telepon</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->customer->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->customer->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Alamat Pengiriman</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->address ?? '-' }}</p>
                </div>
            </div>
        </div>

        @if($invoice->gudang)
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Staff Gudang</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Nama Staff</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->gudang->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->gudang->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold 
                        @if($invoice->gudang->active) bg-green-100 text-green-700
                        @else bg-red-100 text-red-700
                        @endif">
                        {{ $invoice->gudang->active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
            </div>
        </div>
        @endif

        @if($invoice->details && $invoice->details->count() > 0)
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Detail Produk</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm border border-gray-200">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">No</th>
                            <th class="px-4 py-3 text-left font-semibold">Nama Produk</th>
                            <th class="px-4 py-3 text-left font-semibold">Warna</th>
                            <th class="px-4 py-3 text-left font-semibold">Harga</th>
                            <th class="px-4 py-3 text-left font-semibold">Jumlah</th>
                            <th class="px-4 py-3 text-left font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->details as $detail)
                            <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif">
                                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">{{ $detail->product->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $detail->variant->color ?? '-' }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($detail->price ?? 0, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">{{ $detail->quantity ?? 0 }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-right">
                <p class="text-lg font-bold">Total: Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</p>
            </div>
        </div>
        @endif

        @if ($invoice->status === 'dikirim')
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Upload Bukti Pengiriman</h2>
            <form action="{{ url('/admin/invoices/upload-bukti/' . $invoice->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Foto Bukti Kirim:</label>
                    <input type="file" name="photo" accept="image/*" class="border border-gray-300 p-2 rounded w-full">
                </div>
                <div>
                    <label for="signature" class="block text-sm font-medium text-gray-700 mb-1">Tanda Tangan Penerima:</label>
                    <input type="file" name="signature" accept="image/*" class="border border-gray-300 p-2 rounded w-full">
                </div>
                <button type="submit" class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700 transition">Upload Bukti</button>
            </form>

            @if ($invoice->delivery_proof_photo || $invoice->delivery_signature)
                <div class="mt-6">
                    <h3 class="text-md font-semibold mb-2">Bukti yang Sudah Diunggah:</h3>
                    @if ($invoice->delivery_proof_photo)
                        <div class="mb-4">
                            <p class="font-medium">Foto:</p>
                            <img src="{{ asset('storage/' . $invoice->delivery_proof_photo) }}" alt="Foto Bukti Kirim" class="w-64 border rounded">
                        </div>
                    @endif
                    @if ($invoice->delivery_signature)
                        <div>
                            <p class="font-medium">Tanda Tangan:</p>
                            <img src="{{ asset('storage/' . $invoice->delivery_signature) }}" alt="Tanda Tangan" class="w-64 border rounded">
                        </div>
                    @endif
                </div>
                @if ($invoice->delivery_proof_photo && $invoice->delivery_signature)
                    <form method="POST" action="{{ url('/admin/driver-transaksi/finish/' . $invoice->id) }}" class="mt-6">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                            Tandai Selesai / Sampai
                        </button>
                    </form>
                @endif
            @endif
        </div>
        @endif
    </div>
@endsection

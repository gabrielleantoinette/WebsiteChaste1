@extends('layouts.admin')

@section('content')
<div class="flex justify-center">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 w-full max-w-4xl">
        @if(session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
        @endif
        
        <div class="flex justify-between items-center mb-6">
            <a href="{{ url('/admin/gudang-transaksi') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Detail Invoice</h1>
        </div>

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
            @if (!$invoice->gudang)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <h3 class="text-md font-semibold text-yellow-800 mb-2">Konfirmasi Kualitas Barang</h3>
                    <p class="text-sm text-yellow-700 mb-3">Upload foto bukti kualitas barang sebelum klik "Siapkan Barang".</p>
                    <form action="{{ url('/admin/gudang-transaksi/assign-gudang/' . $invoice->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="quality_proof_photo" class="block text-sm font-medium text-gray-700 mb-1">Foto Bukti Kualitas Barang</label>
                            <input type="file" id="quality_proof_photo" name="quality_proof_photo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100" required>
                            @error('quality_proof_photo')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold px-4 py-2 rounded-md transition">
                            Siapkan Barang
                        </button>
                    </form>
                </div>
            @else
                <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-700 mb-4">
                    <div><strong>Nama:</strong> {{ $invoice->gudang->name }}</div>
                    <div><strong>Email:</strong> {{ $invoice->gudang->email }}</div>
                    <div><strong>Status:</strong> {{ $invoice->gudang->active ? 'Aktif' : 'Tidak Aktif' }}</div>
                </div>
                @if($invoice->quality_proof_photo)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <h3 class="text-md font-semibold text-green-800 mb-2">✅ Foto Bukti Kualitas Sudah Diupload</h3>
                        <div class="mb-3">
                            <img src="{{ Storage::url($invoice->quality_proof_photo) }}" alt="Foto Bukti Kualitas" class="max-w-xs rounded-lg border">
                        </div>
                        <p class="text-xs text-gray-600">Status: {{ ucfirst($invoice->status) }}</p>
                    </div>
                @endif
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
                        @foreach ($cartItems as $item)
                        <tr class="border-t border-gray-200 hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2">{{ $item->product_name ?? 'Custom Terpal' }}</td>
                            <td class="px-4 py-2">Rp {{ number_format($item->product_price ?? $item->harga_custom, 0, ',', '.') }}</td>
                            <td class="px-4 py-2">{{ $item->variant_color ?? $item->warna_custom ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $item->quantity }}</td>
                            <td class="px-4 py-2">Rp {{ number_format(($item->product_price ?? $item->harga_custom) * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="text-right mt-4 text-base font-semibold text-gray-800">
                Total: Rp {{ number_format($total, 0, ',', '.') }}
            </div>            
        </div>
    </div>
</div>
@endsection

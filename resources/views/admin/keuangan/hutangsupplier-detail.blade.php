@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-10">
    <div class="mb-6">
        <x-breadcrumb :items="[
            ['label' => 'Hutang Supplier', 'url' => route('keuangan.hutang.index')],
            ['label' => 'Detail']
        ]" />
        <h1 class="text-2xl font-bold text-gray-800">🧾 Detail Hutang Supplier</h1>
    </div>

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
                    <td class="px-4 py-2">{{ $item->material_name ?? ($item->rawMaterial->name ?? 'N/A') }}</td>
                    <td class="px-4 py-2">{{ $item->quantity }} {{ $item->rawMaterial->unit ?? 'unit' }}</td>
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
    <div class="bg-white p-6 rounded shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">💳 Riwayat Pembayaran</h2>
        @if($po->payments->count())
        <table class="min-w-full text-sm text-left border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Tanggal</th>
                    <th class="px-4 py-2">Jumlah Dibayar</th>
                    <th class="px-4 py-2">Catatan</th>
                    <th class="px-4 py-2">Bukti Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($po->payments as $pay)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') }}</td>
                    <td class="px-4 py-2">Rp {{ number_format($pay->amount_paid, 0, ',', '.') }}</td>
                    <td class="px-4 py-2">{{ $pay->notes ?? '-' }}</td>
                    <td class="px-4 py-2">
                        @if($pay->payment_proof)
                            <a href="{{ asset('storage/' . $pay->payment_proof) }}" target="_blank" 
                               class="text-teal-600 hover:underline text-sm">
                                <i class="fas fa-image mr-1"></i>Lihat Bukti
                            </a>
                        @else
                            <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-gray-500">Belum ada pembayaran tercatat.</p>
        @endif
    </div>

    {{-- Form Pelunasan --}}
    @if($po->status != 'lunas')
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold mb-4">💰 Input Pelunasan</h2>
        
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('keuangan.hutang.payment.store', $po->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="amount_paid" class="block font-medium mb-2">Jumlah Pembayaran</label>
                    <input type="number" name="amount_paid" id="amount_paid" 
                           class="w-full border border-gray-300 rounded px-3 py-2" 
                           min="1" max="{{ $po->total - $po->payments->sum('amount_paid') }}" 
                           value="{{ old('amount_paid') }}" required>
                    <p class="text-sm text-gray-500 mt-1">
                        Sisa hutang: Rp {{ number_format($po->total - $po->payments->sum('amount_paid'), 0, ',', '.') }}
                    </p>
                </div>
                
                <div>
                    <label for="payment_date" class="block font-medium mb-2">Tanggal Pembayaran</label>
                    <input type="date" name="payment_date" id="payment_date" 
                           class="w-full border border-gray-300 rounded px-3 py-2" 
                           value="{{ old('payment_date', date('Y-m-d')) }}" required>
                </div>
            </div>
            
            <div>
                <label for="payment_proof" class="block font-medium mb-2">Upload Bukti Pembayaran</label>
                <input type="file" name="payment_proof" id="payment_proof" 
                       class="w-full border border-gray-300 rounded px-3 py-2" 
                       accept="image/*" required>
                <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB</p>
            </div>
            
            <div>
                <label for="notes" class="block font-medium mb-2">Catatan (opsional)</label>
                <textarea name="notes" id="notes" rows="3" 
                          class="w-full border border-gray-300 rounded px-3 py-2" 
                          placeholder="Catatan tambahan tentang pembayaran ini...">{{ old('notes') }}</textarea>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded font-semibold transition">
                    <i class="fas fa-save mr-2"></i>Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection

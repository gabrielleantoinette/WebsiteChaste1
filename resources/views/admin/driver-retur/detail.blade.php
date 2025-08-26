@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detail Pengambilan Retur</h1>
            <a href="{{ url('/admin/dashboard-driver') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm">
                ← Kembali ke Dashboard
            </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Informasi Invoice</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Kode Invoice</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->code }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal Invoice</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->created_at ? $invoice->created_at->format('d M Y H:i') : '-' }}</p>
                </div>
                @php
                    $paymentMethod = $invoice->payments->first()->method ?? null;
                    $isCOD = $paymentMethod === 'cod';
                @endphp
                @if($isCOD)
                <div>
                    <p class="text-sm text-gray-500">Total Pembayaran (COD)</p>
                    <p class="font-semibold text-red-800">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</p>
                </div>
                @endif
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
                    <p class="text-sm text-gray-500">Alamat Pengiriman</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->address ?? '-' }}</p>
                </div>
            </div>
        </div>

        @if($invoice->returns && $invoice->returns->count() > 0)
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Detail Retur</h2>
            @foreach($invoice->returns as $retur)
            <div class="border-b border-gray-100 pb-4 mb-4 last:border-b-0 last:pb-0 last:mb-0">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Alasan Retur</p>
                        <p class="font-semibold text-gray-800">{{ $retur->description }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status Retur</p>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold 
                            @if($retur->status == 'diajukan') bg-yellow-100 text-yellow-700
                            @elseif($retur->status == 'diproses') bg-blue-100 text-blue-700
                            @else bg-green-100 text-green-700
                            @endif">
                            {{ ucfirst($retur->status) }}
                        </span>
                    </div>
                    @if($retur->media_path)
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Bukti Retur</p>
                        @if(Str::endsWith($retur->media_path, ['.jpg', '.jpeg', '.png', '.gif']))
                            <img src="{{ asset('storage/' . $retur->media_path) }}" alt="Bukti Retur" class="max-w-xs rounded-lg shadow-sm">
                        @else
                            <a href="{{ asset('storage/' . $retur->media_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                Lihat Media
                            </a>
                        @endif
                    </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Pengajuan</p>
                        <p class="font-semibold text-gray-800">{{ $retur->created_at ? $retur->created_at->format('d M Y H:i') : '-' }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if($invoice->status == 'retur_diambil')
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Aksi Pengambilan</h2>
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-orange-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-orange-700 font-medium">Retur ini siap untuk diambil dari customer</p>
                </div>
            </div>
            <form method="POST" action="{{ url('/admin/driver-retur/pickup/' . $invoice->id) }}" class="inline">
                @csrf
                <button type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-md shadow-sm transition"
                        onclick="return confirm('Konfirmasi pengambilan retur ini? Pastikan Anda telah mengambil barang retur dari customer.')">
                    ✓ Konfirmasi Pengambilan Retur
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection 
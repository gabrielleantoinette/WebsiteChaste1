@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md mt-8">
    <h1 class="text-2xl font-bold mb-6 text-center">Detail Transaksi</h1>
    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
    @endif
    <div class="mb-4">
        <div class="mb-2"><strong>Kode Invoice:</strong> {{ $invoice->code }}</div>
        <div class="mb-2"><strong>Tanggal:</strong> {{ $invoice->created_at }}</div>
        <div class="mb-2"><strong>Total:</strong> Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</div>
        <div class="mb-2">
            <strong>Status:</strong> 
            @php
                $statusColors = [
                    'Menunggu Pembayaran' => 'bg-yellow-100 text-yellow-800',
                    'Menunggu Konfirmasi Pembayaran' => 'bg-orange-100 text-orange-800',
                    'Pembayaran Ditolak' => 'bg-red-100 text-red-800',
                    'Dikemas' => 'bg-blue-100 text-blue-800',
                    'Dikirim' => 'bg-purple-100 text-purple-800',
                    'Selesai' => 'bg-green-100 text-green-800',
                ];
                $statusColor = $statusColors[$invoice->status] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">{{ $invoice->status }}</span>
        </div>
    </div>
    <div class="mb-4">
        <div class="mb-2"><strong>Customer:</strong> {{ $invoice->customer->name ?? '-' }}</div>
        <div class="mb-2"><strong>Email:</strong> {{ $invoice->customer->email ?? '-' }}</div>
    </div>
    @if($invoice->transfer_proof)
        <div class="mb-6">
            <strong>Bukti Transfer:</strong><br>
            @php
                $proofPath = $invoice->transfer_proof;
                $imageUrl = null;
                
                if ($proofPath) {
                    // Bersihkan path dari karakter yang tidak valid
                    $cleanPath = ltrim($proofPath, '/');
                    
                    // Gunakan format yang sama seperti produk: /public/storage/{path}
                    // Ini akan menghasilkan URL seperti: https://domain.com/public/storage/bukti_transfer/...
                    $imageUrl = url('/public/storage/' . $cleanPath);
                }
                
                // Jika masih null, set ke placeholder
                if (!$imageUrl) {
                    $imageUrl = asset('images/gulungan-terpal.png');
                }
            @endphp
            <a href="{{ $imageUrl }}" target="_blank" class="inline-block mt-2">
                <img src="{{ $imageUrl }}" 
                     alt="Bukti Transfer" 
                     class="w-64 border rounded hover:opacity-80 transition cursor-pointer"
                     onerror="this.onerror=null; this.src='{{ asset('images/gulungan-terpal.png') }}'; this.alt='Gambar tidak dapat dimuat'; this.style.border='2px dashed #ccc'; this.style.padding='20px';">
            </a>
            <p class="text-xs text-gray-500 mt-1">Klik gambar untuk melihat ukuran penuh</p>
        </div>
    @endif
    @if($invoice->status === 'Menunggu Konfirmasi Pembayaran')
        <div class="flex gap-4">
            <form method="POST" action="{{ route('keuangan.konfirmasi', $invoice->id) }}" class="inline">
                @csrf
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded font-semibold">Konfirmasi & Proses Barang</button>
            </form>
            <form method="POST" action="{{ route('keuangan.tolak', $invoice->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menolak pembayaran ini? Customer akan diberitahu bahwa pembayarannya ditolak.');">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded font-semibold">Tolak Pembayaran</button>
            </form>
        </div>
    @endif
</div>
@endsection

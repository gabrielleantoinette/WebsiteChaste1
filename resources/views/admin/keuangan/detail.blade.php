@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md mt-8">
    <h1 class="text-2xl font-bold mb-6 text-center">Detail Transaksi</h1>
    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    <div class="mb-4">
        <div class="mb-2"><strong>Kode Invoice:</strong> {{ $invoice->code }}</div>
        <div class="mb-2"><strong>Tanggal:</strong> {{ $invoice->created_at }}</div>
        <div class="mb-2"><strong>Total:</strong> Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</div>
        <div class="mb-2"><strong>Status:</strong> <span class="font-semibold">{{ $invoice->status }}</span></div>
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
                
                // Log untuk debugging
                \Log::info('Loading transfer proof', [
                    'invoice_id' => $invoice->id,
                    'invoice_code' => $invoice->code,
                    'transfer_proof_path' => $proofPath
                ]);
                
                // Method 1: Coba Storage::url() dulu (paling reliable)
                if ($proofPath) {
                    try {
                        // Cek apakah file benar-benar ada
                        $fileExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($proofPath);
                        \Log::info('File existence check', [
                            'path' => $proofPath,
                            'exists' => $fileExists
                        ]);
                        
                        if ($fileExists) {
                            $imageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($proofPath);
                            \Log::info('Storage URL generated', ['url' => $imageUrl]);
                        } else {
                            \Log::warning('File not found in storage', ['path' => $proofPath]);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Storage::url() failed', [
                            'path' => $proofPath, 
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
                
                // Method 2: Jika Storage::url() gagal, coba dengan asset()
                if (!$imageUrl && $proofPath) {
                    // Pastikan path dimulai dengan 'storage/'
                    $assetPath = $proofPath;
                    if (!str_starts_with($assetPath, 'storage/')) {
                        $assetPath = 'storage/' . ltrim($assetPath, '/');
                    }
                    $imageUrl = asset($assetPath);
                    \Log::info('Using asset() fallback', ['url' => $imageUrl]);
                }
                
                // Method 3: Fallback ke path langsung jika masih gagal
                if (!$imageUrl && $proofPath) {
                    // Coba dengan path relatif dari public
                    $directPath = 'storage/' . ltrim($proofPath, '/');
                    $fullPath = public_path($directPath);
                    if (file_exists($fullPath)) {
                        $imageUrl = url($directPath);
                        \Log::info('Using direct path', ['url' => $imageUrl, 'full_path' => $fullPath]);
                    } else {
                        \Log::warning('Direct path not found', ['path' => $fullPath]);
                    }
                }
                
                // Method 4: Jika semua gagal, gunakan path asli dari database
                if (!$imageUrl) {
                    $imageUrl = $proofPath ? url('storage/' . ltrim($proofPath, '/')) : '#';
                    \Log::info('Using database path as final fallback', ['url' => $imageUrl]);
                }
            @endphp
            <a href="{{ $imageUrl }}" target="_blank" class="inline-block mt-2">
                <img src="{{ $imageUrl }}" 
                     alt="Bukti Transfer" 
                     class="w-64 border rounded hover:opacity-80 transition cursor-pointer"
                     onerror="this.onerror=null; this.src='{{ asset('images/gulungan-terpal.png') }}'; this.alt='Gambar tidak dapat dimuat'; this.style.border='2px dashed #ccc'; this.style.padding='20px';">
            </a>
            <p class="text-xs text-gray-500 mt-1">Klik gambar untuk melihat ukuran penuh</p>
            @if($proofPath)
            <div class="mt-2 p-2 bg-gray-50 rounded text-xs text-gray-600">
                <p><strong>Path di Database:</strong> {{ $proofPath }}</p>
                <p><strong>URL yang digunakan:</strong> {{ $imageUrl }}</p>
                <p><strong>Invoice ID:</strong> {{ $invoice->id }}</p>
                <p><strong>Invoice Code:</strong> {{ $invoice->code }}</p>
            </div>
            @endif
        </div>
    @endif
    @if($invoice->status === 'Menunggu Konfirmasi Pembayaran')
        <form method="POST" action="{{ route('keuangan.konfirmasi', $invoice->id) }}">
            @csrf
            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded font-semibold">Konfirmasi & Proses Barang</button>
        </form>
    @endif
</div>
@endsection

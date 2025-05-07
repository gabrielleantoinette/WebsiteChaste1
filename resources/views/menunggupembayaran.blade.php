<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Pembayaran | CHASTE</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-white font-sans text-gray-800">
    @include('layouts.customer-nav')

    <div class="mb-6">
        <a href="{{ url()->previous() }}" 
        class="inline-flex items-center gap-2 bg-teal-100 hover:bg-teal-200 text-teal-700 font-medium py-2 px-4 rounded-md text-sm transition shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
    </div>
    <div class="p-6">
        <h2 class="text-xl font-bold mb-4">Pesanan - Menunggu Pembayaran</h2>

        @forelse ($orders as $order)
            <div class="border p-4 rounded mb-3 shadow-sm">
                <p><strong>Kode Invoice:</strong> {{ $order->code }}</p>
                <p><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                <p><strong>Total:</strong> Rp {{ number_format($order->grand_total, 0, ',', '.') }}</p>
                <p><strong>Status:</strong> {{ strtoupper($order->status) }}</p>
                <a href="{{ route('transaksi.detail', $order->id) }}"
                   class="inline-block mt-2 px-3 py-1 bg-teal-500 text-white rounded text-sm hover:bg-teal-600 transition">
                   Detail
                </a>
            </div>
        @empty
            <p class="text-gray-500">Tidak ada pesanan dengan status ini.</p>
        @endforelse
    </div>
    
</body>
@include('layouts.footer')
</html>

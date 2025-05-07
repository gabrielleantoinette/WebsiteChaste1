<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dikemas | CHASTE</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-white font-sans text-gray-800">
    @include('layouts.customer-nav')

    <div class="p-6">
        <h2 class="text-xl font-bold mb-4">Pesanan - Dikemas</h2>

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
</html>

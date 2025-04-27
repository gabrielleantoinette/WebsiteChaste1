<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Transaksi | CHASTE</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto py-10 px-4">
        <h1 class="text-2xl font-bold text-center mb-8">Detail Transaksi</h1>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <p><strong>Kode Invoice:</strong> {{ $transaction->code }}</p>
            <p><strong>Status:</strong> {{ $transaction->status }}</p>
            <p><strong>Alamat Pengiriman:</strong> {{ $transaction->address }}</p>
            <p><strong>Total Pembayaran:</strong> Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</p>
        </div>

        <div class="text-center">
            <a href="{{ route('transaksi') }}" class="inline-block bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 px-6 rounded">
                Kembali ke Daftar Transaksi
            </a>
        </div>
    </div>
</body>
</html>

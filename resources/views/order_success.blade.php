<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil | CHASTE</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
@include('layouts.customer-nav')
    <div class="bg-white p-10 rounded-lg shadow-lg text-center">
        <h1 class="text-2xl font-bold text-green-600 mb-4">Terima kasih!</h1>
        <p class="text-gray-700 mb-6">Pesanan kamu telah berhasil dibuat. Tim kami akan segera memprosesnya!</p>
        <a href="{{ route('produk.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-full">Kembali Belanja</a>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pesanan Saya | CHASTE</title>
  @vite('resources/css/app.css')
</head>

<body class="bg-white font-sans text-gray-800">

<!-- Header -->
@include('layouts.customer-nav')

<!-- Pesanan -->
<section class="px-6 md:px-20 py-12">
  <div class="max-w-3xl mx-auto border rounded-xl p-6 md:p-10 space-y-10">

    <!-- Pesanan Saya -->
    <div>
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Pesanan Saya</h2>
        <a href="{{ route('transaksi') }}" class="text-sm text-gray-600 hover:underline">Lihat Riwayat pesanan &gt;</a>
      </div>

      <div class="grid grid-cols-3 text-center gap-6 text-sm">
        <div>
          <div class="text-4xl text-[#BBD3D3]">📦</div>
          <p class="mt-2 font-semibold">Dikemas</p>
          <p>(0)</p>
        </div>
        <div>
          <div class="text-4xl text-[#BBD3D3]">🚚</div>
          <p class="mt-2 font-semibold">Dikirim</p>
          <p>(0)</p>
        </div>
        <div>
          <div class="text-4xl text-[#BBD3D3]">⭐</div>
          <p class="mt-2 font-semibold">Beri Penilaian</p>
          <p>(0)</p>
        </div>
      </div>
    </div>

    <!-- Keuangan -->
    <div>
      <h2 class="text-xl font-bold mb-4">Keuangan Saya</h2>
      <div class="border rounded-md p-4 flex justify-between text-sm">
        <div class="text-center flex-1">
          <p class="text-gray-600">Hutang</p>
          <p class="text-red-500 font-semibold text-lg">Rp 2.000.000</p>
        </div>
        <div class="border-l"></div>
        <div class="text-center flex-1">
          <p class="text-gray-600">Jumlah Tagihan Nota</p>
          <p class="text-red-500 font-semibold text-lg">2</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-[#D9F2F2] py-10 px-6 md:px-20 text-sm text-gray-700 mt-20">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <div>
      <h3 class="text-xl font-bold text-gray-900 mb-3">CHASTE</h3>
      <p class="mb-4 max-w-xs">Kami membantu anda menyediakan terpal terbaik.</p>
      <div class="flex space-x-4 text-lg">
        <a href="#">📷</a>
        <a href="#">🐦</a>
        <a href="#">📘</a>
      </div>
    </div>
    <div class="space-y-2">
      <h4 class="font-semibold text-gray-800 mb-2">Informasi</h4>
      <a href="#" class="block hover:text-black">Tentang</a>
      <a href="#" class="block hover:text-black">Produk</a>
    </div>
    <div class="space-y-2">
      <h4 class="font-semibold text-gray-800 mb-2">Kontak Kami</h4>
      <p>Telp: 089123231221</p>
      <p>E-mail: xyz@bca</p>
    </div>
  </div>
  <div class="text-center text-xs text-gray-500 mt-8">
    © 2025 Hak Cipta Dilindungi
  </div>
</footer>

</body>
</html>

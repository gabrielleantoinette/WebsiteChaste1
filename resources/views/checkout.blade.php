<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout | CHASTE</title>
  @vite('resources/css/app.css')
</head>

<body class="bg-white font-sans text-gray-800">

<!-- Header -->
@include('layouts.customer-nav')

<!-- Checkout -->
<section class="px-6 md:px-20 py-12">
  <div class="max-w-5xl mx-auto border rounded-xl p-8">
    <h2 class="text-2xl font-bold mb-6">Pengiriman & Pembayaran</h2>

    <div class="flex flex-col gap-8">
      <!-- Kiri -->
      <div class="flex flex-col gap-8">
  <!-- Alamat -->
  <div class="border rounded-md p-4">
    <h3 class="font-semibold mb-2">Alamat Pengiriman</h3>
    <p class="text-sm">Muliyasari Prima Utara VI MM-8, Mulyorejo, Jember, 68112, Jawa Timur, Indonesia</p>
  </div>

  <!-- Pesanan -->
  <div class="border rounded-md p-4">
    <h3 class="font-semibold mb-2">Pesanan</h3>
    <div class="flex items-center gap-4 text-sm">
      <img src="{{ asset('images/terpal-ayam.png') }}" class="w-14 h-14 object-cover rounded border">
      <div class="flex-1">
        <p class="font-medium">Terpal Ayam Jago A5</p>
        <p>1 item - Biru</p>
      </div>
      <p class="font-semibold">Rp 3.700</p>
    </div>
  </div>

  <!-- Pengiriman -->
  <div class="border rounded-md p-4">
    <h3 class="font-semibold mb-2">Pilihan Pengiriman</h3>
    <div class="space-y-2 text-sm">
      <label class="flex items-center gap-2">
        <input type="radio" name="pengiriman" checked>
        Kurir Perusahaan (khusus Surabaya Gratis)
      </label>
      <label class="flex items-center gap-2">
        <input type="radio" name="pengiriman">
        Ekspedisi (Rp 19.000)
      </label>
    </div>
  </div>

  <!-- Rincian Total -->
  <div class="border rounded-md p-4">
    <h3 class="font-semibold mb-2">Rincian Total Bayar</h3>
    <div class="text-sm space-y-1">
      <div class="flex justify-between">
        <span>Subtotal Produk</span>
        <span>Rp 3.700</span>
      </div>
      <div class="flex justify-between">
        <span>Subtotal Pengiriman</span>
        <span>Rp 19.000</span>
      </div>
      <div class="border-t mt-2 pt-2 font-semibold flex justify-between">
        <span>Total Pembayaran</span>
        <span class="text-teal-600">Rp 22.700</span>
      </div>
    </div>
  </div>

  <!-- Metode Pembayaran -->
  <div class="border rounded-md p-4">
    <h3 class="font-semibold mb-2">Metode Pembayaran</h3>
    <div class="space-y-2 text-sm">
      <label class="flex items-center gap-2">
        <input type="radio" name="pembayaran" checked> Transfer Bank
      </label>
      <label class="flex items-center gap-2">
        <input type="radio" name="pembayaran"> E-Wallet (OVO, DANA, ShopeePay)
      </label>
      <label class="flex items-center gap-2">
        <input type="radio" name="pembayaran"> COD (Bayar di Tempat)
      </label>
    </div>
  </div>

  <!-- Tombol Bayar -->
  <div>
    <button class="w-full bg-[#D9F2F2] hover:bg-teal-200 text-gray-800 font-semibold py-3 rounded-md transition">
      Bayar
    </button>
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

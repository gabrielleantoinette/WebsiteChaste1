<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Keranjang Belanja | CHASTE</title>
  @vite('resources/css/app.css')
</head>

<body class="bg-white font-sans text-gray-800">

<!-- Header -->
<header class="flex items-center justify-between px-6 md:px-20 py-5 border-b border-gray-200">
  <div class="text-2xl font-bold tracking-wide">CHASTE</div>
  <nav class="hidden md:flex space-x-8 text-sm font-medium text-gray-600">
    <a href="{{ url('/') }}" class="hover:text-teal-500">Beranda</a>
    <a href="{{ url('/produk') }}" class="hover:text-teal-500">Produk</a>
    <a href="#" class="hover:text-teal-500">Kontak</a>
  </nav>
  <div class="space-x-4 text-xl text-gray-700">
    <a href="#">🛒</a>
    <a href="#">👤</a>
    <a href="#">☰</a>
  </div>
</header>

<!-- Keranjang -->
<section class="px-6 md:px-20 py-12">
  <h2 class="text-xl font-semibold mb-6 bg-[#D9F2F2] inline-block px-4 py-2 rounded-md">🛍️ Keranjang Belanja</h2>

  <!-- Pilih Semua -->
  <div class="mb-4 flex items-center gap-2 border border-gray-300 px-4 py-3 rounded-md w-full text-sm">
    <input type="checkbox" id="checkAll">
    <label for="checkAll" class="cursor-pointer">Pilih Semua</label>
  </div>

  <!-- List Produk -->
  <div class="space-y-8">
    <!-- Item Produk -->
    <div class="flex items-center gap-4 border-b pb-6">
    <input type="checkbox" class="item-checkbox">
      <img src="{{ asset('images/terpal-ayam.png') }}" class="w-20 h-20 object-cover rounded border">
      <div class="flex-1">
        <p class="font-semibold">Terpal Ayam Jago Cap A5</p>
        <p class="text-sm text-gray-500">Variasi: Biru</p>
      </div>
      <p class="w-28 text-sm text-gray-700">Rp 4.500,00</p>

      <!-- Qty -->
      <div class="flex items-center border border-gray-300 w-max rounded-md overflow-hidden">
        <button type="button" class="px-3 py-1 text-lg hover:bg-gray-100">-</button>
        <input type="number" value="1" min="1" class="w-10 text-center border-l border-r border-gray-300 outline-none text-sm py-1 appearance-none">
        <button type="button" class="px-3 py-1 text-lg hover:bg-gray-100">+</button>
      </div>

      <p class="w-28 text-sm font-semibold text-red-500 text-right">Rp 3.700,00</p>
      <a href="#" class="text-sm text-red-500 hover:underline">Hapus</a>
    </div>

    <!-- Item Produk -->
    <div class="flex items-center gap-4 border-b pb-6">
    <input type="checkbox" class="item-checkbox">
      <img src="{{ asset('images/terpal-gajah.png') }}" class="w-20 h-20 object-cover rounded border">
      <div class="flex-1">
        <p class="font-semibold">Terpal Plastik Gajah Surya A2</p>
        <p class="text-sm text-gray-500">Variasi: Oranye</p>
      </div>
      <p class="w-28 text-sm text-gray-700">Rp 2.600,00</p>

      <!-- Qty -->
      <div class="flex items-center border border-gray-300 w-max rounded-md overflow-hidden">
        <button type="button" class="px-3 py-1 text-lg hover:bg-gray-100">-</button>
        <input type="number" value="1" min="1" class="w-10 text-center border-l border-r border-gray-300 outline-none text-sm py-1 appearance-none">
        <button type="button" class="px-3 py-1 text-lg hover:bg-gray-100">+</button>
      </div>

      <p class="w-28 text-sm font-semibold text-red-500 text-right">Rp 2.000,00</p>
      <a href="#" class="text-sm text-red-500 hover:underline">Hapus</a>
    </div>
  </div>
  <div class="flex justify-end mt-8">
    <a href="{{ route('checkout') }}"
      class="bg-[#D9F2F2] hover:bg-teal-200 text-gray-800 font-semibold px-6 py-3 rounded-md transition">
      Lanjut Bayar
    </a>
  </div>
</section>

<!-- Footer -->
<footer class="bg-[#D9F2F2] py-10 px-6 md:px-20 text-sm text-gray-700 mt-20">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- Brand -->
    <div>
      <h3 class="text-xl font-bold text-gray-900 mb-3">CHASTE</h3>
      <p class="mb-4 max-w-xs">Kami membantu anda menyediakan terpal terbaik.</p>
      <div class="flex space-x-4 text-lg">
        <a href="#">📷</a>
        <a href="#">🐦</a>
        <a href="#">📘</a>
      </div>
    </div>

    <!-- Informasi -->
    <div class="space-y-2">
      <h4 class="font-semibold text-gray-800 mb-2">Informasi</h4>
      <a href="#" class="block hover:text-black">Tentang</a>
      <a href="#" class="block hover:text-black">Produk</a>
    </div>

    <!-- Kontak -->
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
<script>
    const checkAll = document.getElementById('checkAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
  
    checkAll.addEventListener('change', function () {
      itemCheckboxes.forEach(cb => {
        cb.checked = checkAll.checked;
      });
    });
  </script>
  
</body>
</html>

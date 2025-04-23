<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Negosiasi Produk | CHASTE</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-white text-gray-900 font-sans">

    @include('layouts.customer-nav')

    <!-- Section Negosiasi -->
    <section class="px-6 md:px-20 py-12">
      <div class="max-w-5xl mx-auto flex flex-col md:flex-row gap-8 items-start border rounded-xl p-6">

        <!-- Gambar Produk -->
        <div class="relative w-full md:w-[300px]">
          <img src="{{ asset('images/terpal-ayam.png') }}" alt="Produk" class="rounded-lg border shadow-md w-full">
          <div class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white p-1 rounded-full shadow">
            <span class="text-2xl text-gray-500">&larr;</span>
          </div>
          <div class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white p-1 rounded-full shadow">
            <span class="text-2xl text-gray-500">&rarr;</span>
          </div>
        </div>

        <!-- Detail & Negosiasi -->
        <div class="flex-1 space-y-4">
          <h2 class="text-xl font-bold">Terpal A5 2x3</h2>
          <p class="text-gray-600">Rp 4.500,00</p>

          <!-- Form Tawar -->
          <div class="space-y-2">
            <label for="penawaran" class="text-sm text-gray-600">Penawaran Anda</label>
            <div class="flex items-center gap-2">
              <input type="number" id="penawaran" name="penawaran" placeholder="Masukkan harga tawar..."
                    class="border rounded-md px-3 py-2 w-1/2 text-sm" />
              <button class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded text-sm">Tawar</button>
            </div>
          </div>

          <!-- Hasil Negosiasi -->
          <div>
            <p class="font-semibold mb-1 text-sm">Hasil Negosiasi</p>
            <div class="border rounded-md px-4 py-2 text-sm space-y-1 max-h-48 overflow-y-auto bg-white">
              <p>Gaby #1: Rp 4.000</p>
              <p><strong>Chaste #1:</strong> Rp 4.200</p>
              <p>Gaby #2: Rp 3.900</p>
              <p><strong>Chaste #2:</strong> Rp 4.100</p>
              <p>Gaby #3: Rp 4.050</p>
              <p><strong class="text-red-500">Chaste #3:</strong> <span class="text-red-500">Rp 4.100</span> <strong class="text-red-500">FINAL</strong></p>
            </div>
          </div>

          <!-- Tombol Deal -->
          <div class="flex gap-4">
            <button class="bg-[#D9F2F2] text-gray-800 hover:bg-teal-200 px-6 py-2 rounded-md shadow">Deal</button>
            <button class="border border-gray-300 hover:bg-gray-100 px-6 py-2 rounded-md shadow text-gray-700">
              Tambah Ke Keranjang
            </button>
          </div>
        </div>
      </div>
    </section>

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

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | CHASTE</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen py-10">
@include('layouts.customer-nav')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Pengiriman & Pembayaran</h1>

        <form action="{{ route('checkout.invoice') }}" method="POST">
            @csrf

            <!-- Section Alamat -->
            <section class="border p-4 rounded mb-6">
                <h2 class="font-semibold text-lg mb-2">Alamat Pengiriman</h2>
                <textarea name="address" id="address" rows="3" class="w-full border rounded p-2" required>{{ old('address', $alamat_default_user ?? '') }}</textarea>
            </section>

            <!-- Section Pesanan -->
            <section class="border p-4 rounded mb-6">
                <h2 class="font-semibold text-lg mb-2">Pesanan</h2>
                <div class="space-y-4">
                @foreach ($produkItems as $item)
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <p class="font-semibold">{{ $item->product_name }}</p>
                        <p class="text-sm text-gray-600">
                            {{ $item->quantity }} item - Warna: {{ $item->variant_color ?? '-' }}
                        </p>
                    </div>
                    <p class="font-semibold">
                        Rp {{ number_format(($item->product_price ?? 0) * ($item->quantity ?? 1), 0, ',', '.') }}
                    </p>
                </div>
            @endforeach

            @foreach ($customItems as $item)
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <p class="font-semibold">Custom Terpal: {{ $item->kebutuhan_custom }}</p>
                        <p class="text-sm text-gray-600">
                            Ukuran: {{ $item->ukuran_custom ?? '-' }}, 
                            Warna: {{ $item->warna_custom ?? '-' }}, 
                            Jumlah Ring: {{ $item->jumlah_ring_custom ?? '-' }},
                            Tali: {{ $item->pakai_tali_custom ?? '-' }}
                        </p>
                    </div>
                    <p class="font-semibold">
                        Rp {{ number_format(($item->harga_custom ?? 0) * ($item->quantity ?? 1), 0, ',', '.') }}
                    </p>
                </div>
            @endforeach

                </div>
            </section>

            <!-- Section Pilihan Pengiriman -->
            <section class="border p-4 rounded mb-6">
                <h2 class="font-semibold text-lg mb-2">Pilihan Pengiriman</h2>

                <div>
                    <label class="flex items-center space-x-2 mb-2">
                        <input type="radio" name="shipping_method" value="kurir" checked onclick="updateShippingCost(0)">
                        <span>Kurir Perusahaan (khusus Surabaya Gratis)</span>
                    </label>

                    <label class="flex items-center space-x-2">
                        <input type="radio" name="shipping_method" value="expedition" onclick="updateShippingCost(19000)">
                        <span>Ekspedisi (Rp 19.000)</span>
                    </label>
                </div>
            </section>

            <script>
                function updateShippingCost(cost) {
                    document.getElementById('shippingCost').innerText = formatRupiah(cost);
                    updateTotal(cost);
                }

                function updateTotal(shippingCost) {
                    var productSubtotal = parseInt(document.getElementById('productSubtotalHidden').value);
                    var total = productSubtotal + shippingCost;
                    document.getElementById('totalCost').innerText = formatRupiah(total);
                }

                function formatRupiah(amount) {
                    return "Rp " + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
            </script>

            <!-- Section Rincian Total Bayar -->
            <section class="border p-4 rounded mb-6">
                <h2 class="font-semibold text-lg mb-2">Rincian Total Bayar</h2>

                <div class="flex justify-between mb-1">
                    <span>Subtotal Produk</span>
                    <span id="productSubtotal">Rp {{ number_format($subtotalProduk, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between mb-1">
                    <span>Subtotal Pengiriman</span>
                    <span id="shippingCost">Rp 0</span> <!-- AWALNYA 0 -->
                </div>

                <div class="flex justify-between font-bold border-t pt-2">
                    <span>Total Pembayaran</span>
                    <span id="totalCost">Rp {{ number_format($subtotalProduk, 0, ',', '.') }}</span> <!-- TOTAL AWAL SAMA DENGAN PRODUK -->
                </div>

                <input type="hidden" id="productSubtotalHidden" value="{{ $subtotalProduk }}">
            </section>


            <!-- Section Metode Pembayaran -->
            <section class="border p-4 rounded mb-6">
                <h2 class="font-semibold text-lg mb-2">Metode Pembayaran</h2>
                <div class="space-y-2">
                    <div>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="payment_method" value="transfer" class="accent-blue-600" required>
                            <span>Transfer Bank</span>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="payment_method" value="ewallet" class="accent-blue-600">
                            <span>E-Wallet (OVO, DANA, ShopeePay)</span>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="payment_method" value="cod" class="accent-blue-600">
                            <span>COD (Bayar di Tempat)</span>
                        </label>
                    </div>
                </div>
            </section>

            <!-- Tombol Bayar -->
            <div class="text-center">
            <form action="{{ route('checkout.invoice') }}" method="POST">
                @csrf

                <!-- kirim semua cart id produk biasa -->
                @foreach ($produkItems as $item)
                    <input type="hidden" name="cart_ids[]" value="{{ $item->id }}">
                @endforeach

                <!-- kirim semua cart id produk custom -->
                @foreach ($customItems as $item)
                    <input type="hidden" name="cart_ids[]" value="{{ $item->id }}">
                @endforeach

                <!-- kirim alamat -->
                <input type="hidden" name="alamat" value="{{ $alamat_default_user }}">

                <!-- default shipping method dan payment method -->
                <input type="hidden" name="shipping_method" value="kurir">
                <input type="hidden" name="payment_method" value="transfer_bank">

                <!-- Tombol Bayar -->
                <button type="submit" class="w-full bg-primary hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Bayar
                </button>
            </form>

            </div>

        </form>
    </div>

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

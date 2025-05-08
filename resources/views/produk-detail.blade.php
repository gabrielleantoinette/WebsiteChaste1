<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Produk | CHASTE</title>
  @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">

  @include('layouts.customer-nav')

  <section class="container mx-auto px-6 py-12">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden md:flex">
      <!-- Gambar Produk (Kotak Persegi) -->
      <div class="md:w-1/2">
        @php
          $imgPath = $product->image
            ? asset('storage/'.$product->image)
            : asset('images/placeholder.png');
        @endphp
        <div class="w-full aspect-square overflow-hidden rounded-l-2xl">
          <img
            src="{{ $imgPath }}"
            alt="{{ $product->name }}"
            class="w-full h-full object-cover"
          >
        </div>
      </div>

      <!-- Details Section -->
      <div class="md:w-1/2 p-8 space-y-6">
        <h1 class="text-3xl font-bold text-gray-800">
          {{ $product->name }}
          <span class="text-lg text-gray-600">({{ $product->size }})</span>
        </h1>
        <p class="text-xl text-teal-600 font-semibold">
          Rp {{ number_format($product->price, 0, ',', '.') }}
          <span class="text-sm text-gray-500">/ unit</span>
        </p>
        <p class="text-gray-600">{{ $product->description }}</p>

        <!-- Variant & Quantity -->
        <form action="{{ route('keranjang.add') }}" method="POST" class="space-y-4">
          @csrf
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
            <select name="variant_id"
                    class="w-full border border-gray-300 rounded-lg p-2 focus:ring-teal-400">
              @foreach ($variants as $variant)
                <option value="{{ $variant->id }}">{{ ucfirst($variant->color) }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
            <div class="inline-flex items-center border border-gray-300 rounded-lg overflow-hidden">
              <button type="button" onclick="changeQty(-1)"
                      class="px-3 py-2 text-lg hover:bg-gray-100">-</button>
              <input type="number" id="qtyInput" name="quantity" value="1" min="1"
                     class="w-16 text-center border-l border-r border-gray-300 focus:outline-none" />
              <button type="button" onclick="changeQty(1)"
                      class="px-3 py-2 text-lg hover:bg-gray-100">+</button>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-3 sm:space-y-0">
            <button type="submit"
                    class="flex-1 bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition">
              Tambah ke Keranjang
            </button>
            <a href="{{ route('produk.negosiasi', $product) }}"
               class="flex-1 text-center bg-gray-300 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
              Tawar Harga
            </a>
          </div>
        </form>
      </div>
    </div>
  </section>

  @include('layouts.footer')

  <script>
    function changeQty(amount) {
      const input = document.getElementById('qtyInput');
      let val = parseInt(input.value) || 1;
      val = Math.max(val + amount, 1);
      input.value = val;
    }
  </script>
</body>
</html>

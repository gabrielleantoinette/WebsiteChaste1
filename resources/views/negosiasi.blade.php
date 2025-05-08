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
          <p class="text-gray-600">Rp 4.500,00 (Harga Satuan)</p>

          <!-- Form Tawar -->
          <div class="space-y-2">
            <form action="{{ route('produk.negosiasi.tawar', $product) }}" method="POST" class="space-y-2">
              @csrf
              <label for="harga" class="text-sm text-gray-600">Penawaran Anda</label>
              <div class="flex items-center gap-2">
                <input 
                  type="number" 
                  id="harga" 
                  name="harga" 
                  min="1" 
                  required
                  placeholder="Masukkan harga tawar..."
                  class="border rounded-md px-3 py-2 w-1/2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"
                  value="{{ old('harga') }}"
                />
                <button type="submit"
                        class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700 transition">
                  Tawar
                </button>
              </div>
            </form>
          </div>

          <!-- Hasil Negosiasi -->
          <div class="mt-4 border border-gray-200 rounded p-4 bg-white text-sm space-y-2">
            @if(isset($neg) && $neg->status)
              @foreach([1,2,3] as $i)
                @php
                  $cust = "cust_nego_$i";
                  $sell = "seller_nego_$i";
                @endphp

                @if($neg->$cust !== null)
                  <p>
                    <strong>You #{{ $i }}:</strong> 
                      Rp {{ number_format($neg->$cust, 0, ',', '.') }}<br>
                    <strong>System #{{ $i }}:</strong> 
                      Rp {{ number_format($neg->$sell, 0, ',', '.') }}
                    @if($neg->status === 'final' && $i === 3)
                      <span class="text-red-600 font-bold ml-2">FINAL</span>
                    @endif
                  </p>
                @endif
              @endforeach
            @else
              <p class="text-gray-500">Belum ada tawaran.</p>
            @endif
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

@include('layouts.footer')

</body>
</html>

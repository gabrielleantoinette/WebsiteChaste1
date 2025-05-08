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
    <div class="max-w-5xl mx-auto flex flex-col md:flex-row gap-8 items-start border border-gray-300 rounded-2xl p-6 shadow-sm">
      
      <!-- Gambar Produk -->
      <div class="relative w-full md:w-[300px]">
        <img src="{{ asset('images/terpal-ayam.png') }}" 
             alt="{{ $product->name }}" 
             class="rounded-lg border shadow-md w-full h-auto">
        <button class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white p-1 rounded-full shadow">
          <span class="text-2xl text-gray-500">&larr;</span>
        </button>
        <button class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white p-1 rounded-full shadow">
          <span class="text-2xl text-gray-500">&rarr;</span>
        </button>
      </div>

      <!-- Detail & Negosiasi -->
      <div class="flex-1 space-y-4">
        <h2 class="text-2xl font-bold text-gray-900">{{ $product->name }} {{ $product->size }}</h2>
        <p class="text-gray-600">Rp {{ number_format($product->price,0,',','.') }} <span class="text-sm">(Harga Satuan)</span></p>

        <!-- Form Tawar -->
        <form action="{{ route('produk.negosiasi.tawar', $product) }}" method="POST" class="mt-4 space-y-2">
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
              class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"
              value="{{ old('harga') }}"
            />
            <button type="submit"
                    class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition">
              Tawar
            </button>
          </div>
        </form>

        <!-- Hasil Negosiasi / Empty State -->
        <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-700">
          @php
            $hasAny = collect([$neg->cust_nego_1, $neg->cust_nego_2, $neg->cust_nego_3])->filter()->isNotEmpty();
          @endphp

          @if(!$hasAny)
            <p class="italic text-gray-500">Belum ada tawaran.</p>
          @else
            <ul class="space-y-3">
              @foreach([1,2,3] as $i)
                @php
                  $cust = "cust_nego_$i";
                  $sell = "seller_nego_$i";
                @endphp

                @if($neg->$cust !== null)
                  <li class="flex justify-between">
                    <span class="font-medium">You #{{ $i }}:</span>
                    <span>Rp {{ number_format($neg->$cust,0,',','.') }}</span>
                  </li>
                  <li class="flex justify-between">
                    <span class="font-medium">System #{{ $i }}:</span>
                    <span>
                      Rp {{ number_format($neg->$sell,0,',','.') }}
                      @if($neg->status==='final' && $i===3)
                        <span class="text-red-600 font-bold ml-2">FINAL</span>
                      @endif
                    </span>
                  </li>
                @endif
              @endforeach
            </ul>
          @endif
        </div>

        <!-- Tombol Actions -->
        <div class="mt-6 flex items-center gap-4">
          <!-- Deal (disabled kecuali final) -->
          <button type="button"
                  class="px-5 py-2 rounded-lg font-semibold transition
                         {{ $neg->status==='final'
                            ? 'bg-teal-100 text-teal-800 hover:bg-teal-200'
                            : 'bg-gray-200 text-gray-500 cursor-not-allowed' }}"
                  {{ $neg->status!=='final' ? 'disabled' : '' }}>
            Deal
          </button>

          <!-- Tambah ke Keranjang -->
          <a href="{{ route('produk.add', $product) }}"
             class="px-5 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition text-gray-700">
            Tambah ke Keranjang
          </a>

          <!-- Reset Negosiasi -->
          <form action="{{ route('produk.negosiasi.reset', $product) }}" method="POST" class="inline">
            @csrf
            <button type="submit"
                    onclick="return confirm('Yakin ingin memulai negosiasi ulang?')"
                    class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
              Reset Negosiasi
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>

  @include('layouts.footer')

</body>
</html>

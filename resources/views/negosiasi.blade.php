<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Negosiasi Produk | CHASTE</title>
  @vite('resources/css/app.css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-white text-gray-900 font-sans">

  @include('layouts.customer-nav')

  <!-- Section Negosiasi -->
  <section class="px-6 md:px-20 py-12">
    <div class="mb-6">
        <x-breadcrumb :items="[
            ['label' => 'Produk', 'url' => route('produk')],
            ['label' => 'Negosiasi']
        ]" />
        <h1 class="text-2xl font-bold text-gray-800">Negosiasi Harga</h1>
    </div>
    <div class="max-w-5xl mx-auto flex flex-col md:flex-row gap-8 items-start border border-gray-300 rounded-2xl p-6 shadow-sm">
      
      <!-- Gambar Produk -->
      <div class="relative w-full md:w-[300px]">
        <img src="{{ $product->image_url }}" 
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
        <h2 class="text-2xl font-bold text-gray-900">{{ $product->name }} - {{ $product->size }}</h2>
        <p class="text-gray-600">Rp {{ number_format($product->price,0,',','.') }} <span class="text-sm">(Harga Normal)</span></p>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
            <p class="text-sm text-blue-800">
                💡 <strong>Tips Negosiasi:</strong> Tawar 60-80% dari harga normal untuk hasil terbaik
            </p>
        </div>

        <!-- Form Tawar -->
        <form action="{{ route('produk.negosiasi.tawar', $product) }}" method="POST" class="mt-4 space-y-4">
          @csrf
          
          <!-- Quantity Input -->
          <div>
            <label for="quantity" class="text-sm text-gray-600">Jumlah yang akan dibeli</label>
            <div class="flex items-center gap-2 mt-1">
              <button type="button" onclick="changeQty(-1)" class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center hover:bg-gray-300">-</button>
              <input type="number" 
                     id="quantity" 
                     name="quantity" 
                     value="{{ session('quantity', request('quantity', old('quantity', $product->min_buying_stock ?? 1))) }}" 
                     min="{{ $product->min_buying_stock ?? 1 }}" 
                     required
                     class="w-20 text-center border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
              <button type="button" onclick="changeQty(1)" class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center hover:bg-gray-300">+</button>
            </div>
            @if($product->min_buying_stock && $product->min_buying_stock > 1)
              <p class="text-xs text-gray-500 mt-1">Minimal {{ $product->min_buying_stock }} pcs untuk tawar menawar</p>
            @endif
          </div>
          
          <!-- Harga Input -->
          <div>
            <label for="harga" class="text-sm text-gray-600">Penawaran Anda</label>
            <div class="flex items-center gap-2">
              <input 
                type="number" 
                id="harga" 
                name="harga" 
                min="1" 
                required
                placeholder="Contoh: Rp {{ number_format($product->price * 0.8, 0, ',', '.') }}"
                class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"
                value="{{ old('harga') }}"
              />
              <button type="submit"
                      class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition">
                Tawar
              </button>
            </div>
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
                  <li class="flex justify-between items-center py-2 border-b border-gray-100">
                    <div>
                      <span class="font-medium text-gray-800">Tawaran Anda #{{ $i }}:</span>
                      <div class="text-sm text-gray-600">Rp {{ number_format($neg->$cust,0,',','.') }}</div>
                    </div>
                  </li>
                  <li class="flex justify-between items-center py-2">
                    <div>
                      <span class="font-medium text-gray-800">Counter Offer #{{ $i }}:</span>
                      <div class="text-sm text-teal-600 font-semibold">Rp {{ number_format($neg->$sell,0,',','.') }}</div>
                    </div>
                    @if($neg->status==='final' && $i===3)
                      <span class="text-red-600 font-bold text-sm bg-red-100 px-2 py-1 rounded">FINAL</span>
                    @endif
                  </li>
                @endif
              @endforeach
            </ul>
          @endif
        </div>

        <!-- Tombol Actions -->
        <div class="mt-6 flex items-center gap-4">
          <!-- Deal (bisa diklik jika ada tawaran) -->
          @php
            $hasAnyOffer = collect([$neg->cust_nego_1, $neg->cust_nego_2, $neg->cust_nego_3])->filter()->isNotEmpty();
            $finalPrice = $neg->status === 'final' ? $neg->final_price : $neg->seller_nego_3 ?? $neg->seller_nego_2 ?? $neg->seller_nego_1;
          @endphp
          
          @if($hasAnyOffer)
            <form action="{{ route('produk.add', $product) }}" method="POST" class="inline">
              @csrf
              <input type="hidden" name="quantity" id="dealQuantity" value="1">
              <input type="hidden" name="negotiated_price" value="{{ $finalPrice }}">
              <button type="submit"
                      class="px-5 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition">
                Deal - Rp {{ number_format($finalPrice,0,',','.') }}
              </button>
            </form>
          @else
            <button type="button"
                    class="px-5 py-2 bg-gray-200 text-gray-500 rounded-lg font-semibold cursor-not-allowed">
              Deal
            </button>
          @endif

          <!-- Tambah ke Keranjang (harga normal) -->
          <form action="{{ route('produk.add', $product) }}" method="POST" class="inline">
            @csrf
            <input type="hidden" name="quantity" id="normalQuantity" value="1">
            <button type="submit"
                    class="px-5 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition text-gray-700">
              Tambah ke Keranjang (Harga Normal)
            </button>
          </form>

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

  <script>
    const minBuyingStock = {{ $product->min_buying_stock ?? 1 }};
    
    function changeQty(amount) {
      const input = document.getElementById('quantity');
      let val = parseInt(input.value) || minBuyingStock;
      val = Math.max(val + amount, minBuyingStock);
      input.value = val;
      
      // Update quantity di tombol Deal dan Normal
      updateButtonQuantities(val);
      
      // Check dan disable/enable aktivitas tawar
      checkNegotiationActivity(val);
    }
    
    function updateButtonQuantities(qty) {
      const dealQuantity = document.getElementById('dealQuantity');
      const normalQuantity = document.getElementById('normalQuantity');
      
      if (dealQuantity) dealQuantity.value = qty;
      if (normalQuantity) normalQuantity.value = qty;
    }
    
    function checkNegotiationActivity(qty) {
      const hargaInput = document.getElementById('harga');
      const tawarButton = document.querySelector('button[type="submit"]');
      const dealButton = document.querySelector('button[type="submit"][class*="bg-teal-600"]');
      
      if (qty < minBuyingStock) {
        // Disable aktivitas tawar
        if (hargaInput) {
          hargaInput.disabled = true;
          hargaInput.placeholder = `Minimal ${minBuyingStock} pcs untuk tawar menawar`;
        }
        if (tawarButton) {
          tawarButton.disabled = true;
          tawarButton.classList.add('bg-gray-300', 'cursor-not-allowed');
          tawarButton.classList.remove('bg-teal-600', 'hover:bg-teal-700');
        }
        if (dealButton) {
          dealButton.disabled = true;
          dealButton.classList.add('bg-gray-300', 'cursor-not-allowed');
          dealButton.classList.remove('bg-teal-600', 'hover:bg-teal-700');
        }
      } else {
        // Enable aktivitas tawar
        if (hargaInput) {
          hargaInput.disabled = false;
          hargaInput.placeholder = `Contoh: Rp {{ number_format($product->price * 0.8, 0, ',', '.') }}`;
        }
        if (tawarButton) {
          tawarButton.disabled = false;
          tawarButton.classList.remove('bg-gray-300', 'cursor-not-allowed');
          tawarButton.classList.add('bg-teal-600', 'hover:bg-teal-700');
        }
        if (dealButton) {
          dealButton.disabled = false;
          dealButton.classList.remove('bg-gray-300', 'cursor-not-allowed');
          dealButton.classList.add('bg-teal-600', 'hover:bg-teal-700');
        }
      }
    }
    
    // Update quantity saat halaman load
    document.addEventListener('DOMContentLoaded', function() {
      const quantityInput = document.getElementById('quantity');
      if (quantityInput) {
        const initialQty = parseInt(quantityInput.value) || 1;
        updateButtonQuantities(initialQty);
        checkNegotiationActivity(initialQty);
        
        // Update quantity saat input berubah
        quantityInput.addEventListener('input', function() {
          const qty = parseInt(this.value) || 1;
          updateButtonQuantities(qty);
          checkNegotiationActivity(qty);
        });
      }
    });
  </script>

</body>
</html>

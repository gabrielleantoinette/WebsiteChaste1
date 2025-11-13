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
            ['label' => $product->name, 'url' => route('produk.detail', $product->id)],
            ['label' => 'Negosiasi']
        ]" />
        <h1 class="text-2xl font-bold text-gray-800">Negosiasi Harga</h1>
    </div>
    <div class="max-w-5xl mx-auto flex flex-col md:flex-row gap-8 items-start border border-gray-300 rounded-2xl p-6 shadow-sm">
      
      <!-- Gambar Produk -->
      <div class="relative w-full md:w-[300px]">
        <img src="{{ asset($product->image_url) }}" 
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
        <h2 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h2>
        <p class="text-gray-600" id="priceDisplay">
          @if(isset($sizeOptions) && count($sizeOptions) > 0)
            @php
              $defaultSize = $sizeOptions->firstWhere('size', session('selected_size', '2x3')) ?? $sizeOptions->first();
            @endphp
            Rp {{ number_format($defaultSize['price'] ?? 0, 0, ',', '.') }}
          @else
            Rp {{ number_format($product->price, 0, ',', '.') }}
          @endif
          <span class="text-sm">(Harga Normal)</span>
        </p>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
            <p class="text-sm text-blue-800 mb-2">
                💡 <strong>Tips Negosiasi:</strong> Tawar 60-80% dari harga normal untuk hasil terbaik
            </p>
            <p class="text-xs text-blue-700 font-medium">
                ⚠️ <strong>Penting:</strong> Tawar menawar adalah harga per pcs (per unit), bukan subtotal. Total harga akan dihitung berdasarkan harga tawar × quantity.
            </p>
        </div>

        <!-- Form Tawar -->
        <form action="{{ route('produk.negosiasi.tawar', $product) }}" method="POST" class="mt-4 space-y-4">
          @csrf
          
          <!-- Size Selection -->
          @if(isset($sizeOptions) && count($sizeOptions) > 0)
          <div>
            <label for="selected_size" class="text-sm text-gray-600">Ukuran</label>
            <select id="selected_size" name="selected_size" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-teal-400 mt-1" onchange="updatePriceAndMinPrice()">
              @foreach($sizeOptions as $opt)
                <option value="{{ $opt['size'] }}" 
                        data-price="{{ $opt['price'] }}" 
                        data-min-price="{{ $opt['min_price'] }}"
                        {{ (session('selected_size', '2x3') == $opt['size']) ? 'selected' : '' }}>
                  {{ $opt['size'] }} (Rp {{ number_format($opt['price'], 0, ',', '.') }})
                </option>
              @endforeach
            </select>
          </div>
          @endif
          
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
          
          <!-- Subtotal Display -->
          <div class="bg-teal-50 border border-teal-200 rounded-lg p-3">
            <div class="flex justify-between items-center mb-1">
              <span class="text-sm font-medium text-gray-700">Subtotal (Harga Normal):</span>
              <span id="subtotalDisplay" class="text-lg font-bold text-teal-600">
                @if(isset($sizeOptions) && count($sizeOptions) > 0)
                  @php
                    $defaultSize = $sizeOptions->firstWhere('size', session('selected_size', '2x3')) ?? $sizeOptions->first();
                    $defaultQty = session('quantity', request('quantity', $product->min_buying_stock ?? 1));
                  @endphp
                  Rp {{ number_format(($defaultSize['price'] ?? 0) * $defaultQty, 0, ',', '.') }}
                @else
                  Rp {{ number_format($product->price * (session('quantity', request('quantity', 1))), 0, ',', '.') }}
                @endif
              </span>
            </div>
            <p class="text-xs text-gray-500" id="subtotalDetail">
              @if(isset($sizeOptions) && count($sizeOptions) > 0)
                @php
                  $defaultSize = $sizeOptions->firstWhere('size', session('selected_size', '2x3')) ?? $sizeOptions->first();
                  $defaultQty = session('quantity', request('quantity', $product->min_buying_stock ?? 1));
                @endphp
                Rp {{ number_format($defaultSize['price'] ?? 0, 0, ',', '.') }} × {{ $defaultQty }} pcs
              @else
                Rp {{ number_format($product->price, 0, ',', '.') }} × {{ session('quantity', request('quantity', 1)) }} pcs
              @endif
            </p>
          </div>

          <!-- Harga Input -->
          <div>
            <label for="harga" class="text-sm text-gray-600">Penawaran Anda (Harga per pcs)</label>
            @php
              $attempts = collect([$neg->cust_nego_1, $neg->cust_nego_2, $neg->cust_nego_3])->filter()->count();
              $remainingAttempts = 3 - $attempts;
            @endphp
            @if($attempts >= 3 || $neg->status === 'final')
              <div class="bg-red-50 border border-red-200 rounded-lg p-2 mb-2">
                <p class="text-xs text-red-800 font-medium">
                  ⚠️ Anda sudah mencapai maksimal 3 kali tawar. Silakan terima deal atau reset negosiasi.
                </p>
              </div>
              <div class="flex items-center gap-2">
                <input 
                  type="number" 
                  id="harga" 
                  name="harga" 
                  min="1" 
                  disabled
                  placeholder="Maksimal 3 kali tawar"
                  class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm bg-gray-100 cursor-not-allowed"
                  value="{{ old('harga') }}"
                />
                <button type="button"
                        disabled
                        class="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg cursor-not-allowed">
                  Tawar
                </button>
              </div>
            @else
              <div class="bg-amber-50 border border-amber-200 rounded-lg p-2 mb-2">
                <p class="text-xs text-amber-800">
                  <strong>Catatan:</strong> Masukkan harga tawar per pcs (per unit), bukan subtotal. Total akan otomatis dihitung: harga tawar × quantity.
                  @if($attempts > 0)
                    <br><strong>Sisa kesempatan: {{ $remainingAttempts }}x</strong>
                  @endif
                </p>
              </div>
              <div class="flex items-center gap-2">
                <input 
                  type="number" 
                  id="harga" 
                  name="harga" 
                  min="1" 
                  required
                  placeholder="Masukkan harga per pcs"
                  class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"
                  value="{{ old('harga') }}"
                />
                <button type="submit"
                        class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition">
                  Tawar ({{ $remainingAttempts }}x tersisa)
                </button>
              </div>
            @endif
            <p class="text-xs text-gray-500 mt-1">
              Harga per pcs: <span id="pricePerPcsDisplay">-</span> | Total penawaran: <span id="totalOfferDisplay">-</span>
            </p>
          </div>
        </form>

        <!-- Hasil Negosiasi / Empty State -->
        <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-700">
          @php
            $hasAny = collect([$neg->cust_nego_1, $neg->cust_nego_2, $neg->cust_nego_3])->filter()->isNotEmpty();
            $attempts = collect([$neg->cust_nego_1, $neg->cust_nego_2, $neg->cust_nego_3])->filter()->count();
            $remainingAttempts = 3 - $attempts;
          @endphp

          @if(!$hasAny)
            <p class="italic text-gray-500">Belum ada tawaran.</p>
            <p class="text-xs text-teal-600 mt-2 font-medium">💡 Anda memiliki 3 kesempatan untuk menawar</p>
          @else
            @if($attempts < 3 && $neg->status !== 'final')
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
                <p class="text-sm text-blue-800 font-medium">
                  ⚡ Anda masih memiliki <strong>{{ $remainingAttempts }} kesempatan</strong> untuk menawar lagi!
                </p>
              </div>
            @elseif($neg->status === 'final')
              <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                <p class="text-sm text-red-800 font-medium">
                  ⚠️ Anda sudah mencapai maksimal 3 kali tawar. Ini adalah tawaran final.
                </p>
              </div>
            @endif
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
        <div class="mt-6 space-y-4">
          <!-- Deal (bisa diklik jika ada tawaran) -->
          @php
            $hasAnyOffer = collect([$neg->cust_nego_1, $neg->cust_nego_2, $neg->cust_nego_3])->filter()->isNotEmpty();
            $finalPrice = $neg->status === 'final' ? $neg->final_price : $neg->seller_nego_3 ?? $neg->seller_nego_2 ?? $neg->seller_nego_1;
            $defaultQty = session('quantity', request('quantity', $product->min_buying_stock ?? 1));
          @endphp
          
          @if($hasAnyOffer)
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 rounded-lg p-4">
              <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span class="text-sm font-semibold text-gray-800">Subtotal Deal</span>
                </div>
                <span id="dealSubtotalDisplay" class="text-xl font-bold text-green-700">
                  Rp {{ number_format($finalPrice * $defaultQty, 0, ',', '.') }}
                </span>
              </div>
              <div class="flex items-center justify-between text-xs text-gray-600">
                <span id="dealSubtotalDetail">
                  Rp {{ number_format($finalPrice, 0, ',', '.') }} × {{ $defaultQty }} pcs
                </span>
                <span class="px-2 py-0.5 bg-green-200 text-green-800 rounded-full font-medium">Harga Deal</span>
              </div>
            </div>
            <form action="{{ route('produk.add', $product) }}" method="POST" class="w-full">
              @csrf
              <input type="hidden" name="quantity" id="dealQuantity" value="{{ $defaultQty }}">
              <input type="hidden" name="negotiated_price" id="dealFinalPrice" value="{{ $finalPrice }}">
              <input type="hidden" name="selected_size" id="dealSelectedSize" value="{{ session('selected_size', '2x3') }}">
              <button type="submit"
                      class="w-full px-5 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg font-semibold hover:from-green-700 hover:to-emerald-700 transition shadow-md hover:shadow-lg">
                ✓ Terima Deal - Rp {{ number_format($finalPrice,0,',','.') }}/unit
              </button>
            </form>
          @else
            <button type="button"
                    class="w-full px-5 py-3 bg-gray-200 text-gray-500 rounded-lg font-semibold cursor-not-allowed">
              Deal
            </button>
          @endif

          <!-- Tambah ke Keranjang (harga normal) -->
          <form action="{{ route('produk.add', $product) }}" method="POST" class="w-full">
            @csrf
            <input type="hidden" name="quantity" id="normalQuantity" value="{{ $defaultQty }}">
            <input type="hidden" name="selected_size" id="normalSelectedSize" value="{{ session('selected_size', '2x3') }}">
            <button type="submit"
                    class="w-full px-5 py-2.5 border-2 border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition text-gray-700 font-medium">
              Tambah ke Keranjang (Harga Normal)
            </button>
          </form>

          <!-- Reset Negosiasi -->
          <form action="{{ route('produk.negosiasi.reset', $product) }}" method="POST" class="w-full">
            @csrf
            <button type="submit"
                    onclick="return confirm('Yakin ingin memulai negosiasi ulang?')"
                    class="w-full px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 border border-red-200 transition font-medium">
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
    const defaultQuantity = {{ session('quantity', request('quantity', $product->min_buying_stock ?? 1)) }};
    
    function changeQty(amount) {
      const input = document.getElementById('quantity');
      let val = parseInt(input.value) || minBuyingStock;
      val = Math.max(val + amount, minBuyingStock);
      input.value = val;
      
      // Update quantity di tombol Deal dan Normal
      updateButtonQuantities(val);
      
      // Update subtotal
      updateSubtotalNegosiasi();
      
      // Check dan disable/enable aktivitas tawar
      checkNegotiationActivity(val);
    }
    
    function updateButtonQuantities(qty) {
      const dealQuantity = document.getElementById('dealQuantity');
      const normalQuantity = document.getElementById('normalQuantity');
      const dealFinalPrice = document.getElementById('dealFinalPrice');
      const dealSubtotalDisplay = document.getElementById('dealSubtotalDisplay');
      const dealSubtotalDetail = document.getElementById('dealSubtotalDetail');
      
      if (dealQuantity) dealQuantity.value = qty;
      if (normalQuantity) normalQuantity.value = qty;
      
      // Update deal subtotal
      if (dealFinalPrice && dealSubtotalDisplay && dealSubtotalDetail) {
        const finalPrice = parseFloat(dealFinalPrice.value) || 0;
        const subtotal = finalPrice * qty;
        dealSubtotalDisplay.textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
        dealSubtotalDetail.textContent = `Rp ${finalPrice.toLocaleString('id-ID')} × ${qty} pcs`;
      }
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
    
    // Update price and min price based on selected size
    function updatePriceAndMinPrice() {
      const sizeSelect = document.getElementById('selected_size');
      const priceDisplay = document.getElementById('priceDisplay');
      const hargaInput = document.getElementById('harga');
      const dealSelectedSize = document.getElementById('dealSelectedSize');
      const normalSelectedSize = document.getElementById('normalSelectedSize');
      const quantityInput = document.getElementById('quantity');
      
      if (!sizeSelect || !priceDisplay) return;
      
      const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
      const price = selectedOption ? parseFloat(selectedOption.dataset.price) : 0;
      const minPrice = selectedOption ? parseFloat(selectedOption.dataset.minPrice) : 0;
      const selectedSizeValue = sizeSelect.value;
      const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;
      
      // Update price display
      priceDisplay.innerHTML = `Rp ${price.toLocaleString('id-ID')} <span class="text-sm">(Harga Normal)</span>`;
      
      // Update placeholder
      if (hargaInput) {
        const suggestedPrice = Math.round(price * 0.8);
        hargaInput.placeholder = `Contoh: Rp ${suggestedPrice.toLocaleString('id-ID')}`;
      }
      
      // Update hidden inputs for selected_size
      if (dealSelectedSize) {
        dealSelectedSize.value = selectedSizeValue;
      }
      if (normalSelectedSize) {
        normalSelectedSize.value = selectedSizeValue;
      }
      
      // Update subtotal
      updateSubtotalNegosiasi();
    }
    
    // Update subtotal in negotiation page
    function updateSubtotalNegosiasi() {
      const sizeSelect = document.getElementById('selected_size');
      const quantityInput = document.getElementById('quantity');
      const subtotalDisplay = document.getElementById('subtotalDisplay');
      const subtotalDetail = document.getElementById('subtotalDetail');
      const hargaInput = document.getElementById('harga');
      const totalOfferDisplay = document.getElementById('totalOfferDisplay');
      
      if (!sizeSelect || !quantityInput) return;
      
      const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
      const price = selectedOption ? parseFloat(selectedOption.dataset.price) : 0;
      const quantity = parseInt(quantityInput.value) || 1;
      const subtotal = price * quantity;
      
      if (subtotalDisplay) {
        subtotalDisplay.textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
      }
      
      if (subtotalDetail) {
        subtotalDetail.textContent = `Rp ${price.toLocaleString('id-ID')} × ${quantity} pcs`;
      }
      
    }
    
    // Update total offer display when harga input changes
    function updateTotalOffer() {
      const hargaInput = document.getElementById('harga');
      const totalOfferDisplay = document.getElementById('totalOfferDisplay');
      const pricePerPcsDisplay = document.getElementById('pricePerPcsDisplay');
      const quantityInput = document.getElementById('quantity');
      
      if (hargaInput && totalOfferDisplay && quantityInput) {
        const offerPrice = parseFloat(hargaInput.value) || 0;
        const currentQty = parseInt(quantityInput.value) || 1;
        const totalOffer = offerPrice * currentQty;
        
        // Update harga per pcs display
        if (pricePerPcsDisplay) {
          pricePerPcsDisplay.textContent = offerPrice > 0 ? `Rp ${offerPrice.toLocaleString('id-ID')}` : '-';
        }
        
        // Update total penawaran display
        totalOfferDisplay.textContent = offerPrice > 0 ? `Rp ${totalOffer.toLocaleString('id-ID')}` : '-';
      }
    }
    
    // Update quantity saat halaman load
    document.addEventListener('DOMContentLoaded', function() {
      updatePriceAndMinPrice();
      updateSubtotalNegosiasi();
      const quantityInput = document.getElementById('quantity');
      const hargaInput = document.getElementById('harga');
      
      if (quantityInput) {
        const initialQty = parseInt(quantityInput.value) || 1;
        updateButtonQuantities(initialQty);
        checkNegotiationActivity(initialQty);
        
        // Add event listener for quantity change
        quantityInput.addEventListener('input', function() {
          updateSubtotalNegosiasi();
          updateTotalOffer();
          const qty = parseInt(this.value) || 1;
          updateButtonQuantities(qty);
          checkNegotiationActivity(qty);
        });
      }
      
      // Update deal subtotal on page load
      const dealFinalPrice = document.getElementById('dealFinalPrice');
      if (dealFinalPrice) {
        const initialQty = quantityInput ? parseInt(quantityInput.value) || 1 : defaultQuantity;
        updateButtonQuantities(initialQty);
      }
      
      // Add event listener for harga input
      if (hargaInput) {
        hargaInput.addEventListener('input', function() {
          updateTotalOffer();
        });
      }
    });
  </script>

</body>
</html>

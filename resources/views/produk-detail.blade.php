<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Produk | CHASTE</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 text-gray-900 font-sans antialiased">

    @include('layouts.customer-nav')

    <section class="container mx-auto px-6 py-12">
        <div class="mb-6">
            <x-breadcrumb :items="[
                ['label' => 'Produk', 'url' => route('produk')],
                ['label' => $product->name]
            ]" />
        </div>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden md:flex">
            <!-- Gambar Produk (Kotak Persegi) -->
            <div class="md:w-1/2">
                @php
                    $imgPath = $product->image
                        ? asset('storage/' . $product->image)
                        : asset('images/logo-perusahaan.png');
                @endphp
                <div class="w-full aspect-square overflow-hidden rounded-l-2xl">
                    <img src="{{ $imgPath }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>
            </div>

            <!-- Details Section -->
            <div class="md:w-1/2 p-8 space-y-6">
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                @endif
                <h1 class="text-3xl font-bold text-gray-800">
                    {{ $product->name }}
                    <span class="text-lg text-gray-600">({{ $product->size }})</span>
                </h1>
                <p class="text-xl text-teal-600 font-semibold">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                    <span class="text-sm text-gray-500">/ unit</span>
                </p>
                <p class="text-sm text-yellow-600 bg-yellow-50 px-3 py-2 rounded-lg border border-pink-200">
                     <strong>Ambil banyak untuk harga lebih murah!</strong>
                </p>
                <p class="text-gray-600">{{ $product->description }}</p>

                <!-- Variant & Quantity -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
                        <select id="variantSelect"
                            class="w-full border border-gray-300 rounded-lg p-2 focus:ring-teal-400">
                            @foreach ($variants as $variant)
                                <option value="{{ $variant->id }}" data-stock="{{ $variant->stock }}">
                                    {{ ucfirst($variant->color) }} 
                                    {{-- @if($variant->stock > 0)
                                        - Stok: {{ $variant->stock }}
                                    @else
                                        - Stok Habis
                                    @endif --}}
                                </option>
                            @endforeach
                        </select>
                        <!-- Stock Info Display -->
                        <div id="stockInfo" class="mt-2 text-sm">
                            <span id="stockText" class="px-3 py-1 rounded-full text-sm font-medium"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                        <div class="inline-flex items-center border border-gray-300 rounded-lg overflow-hidden">
                            <button type="button" onclick="changeQty(-1)"
                                class="px-3 py-2 text-lg hover:bg-gray-100">-</button>
                            <input type="number" id="qtyInput" value="1" min="1"
                                class="w-16 text-center border-l border-r border-gray-300 focus:outline-none" />
                            <button type="button" onclick="changeQty(1)"
                                class="px-3 py-2 text-lg hover:bg-gray-100">+</button>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-3 sm:space-y-0">
                        <!-- Form Tambah ke Keranjang -->
                        <form action="{{ route('produk.add', $product->id) }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="variant_id" id="cartVariantId" value="">
                            <input type="hidden" name="quantity" id="cartQuantity" value="1">
                            <button type="submit"
                                class="w-full bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition">
                                Tambah ke Keranjang
                            </button>
                        </form>
                        
                        <!-- Form Tawar Harga -->
                        @if($product->min_buying_stock && $product->min_buying_stock > 1)
                            <div class="flex-1 text-center">
                                <form action="{{ route('produk.negosiasi', $product) }}" method="GET" class="inline">
                                    <input type="hidden" name="quantity" id="negosiasiQuantity" value="1">
                                    <button type="submit"
                                            id="tawarButton"
                                            class="w-full bg-gray-300 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
                                        Tawar Harga
                                    </button>
                                </form>
                                <p class="text-xs text-gray-500 mt-1" id="tawarInfo">
                                    Minimal {{ $product->min_buying_stock }} pcs untuk tawar menawar
                                </p>
                            </div>
                        @else
                            <form action="{{ route('produk.negosiasi', $product) }}" method="GET" class="flex-1">
                                <input type="hidden" name="quantity" id="negosiasiQuantity2" value="1">
                                <button type="submit"
                                        class="w-full bg-gray-300 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
                                    Tawar Harga
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Review Section -->
    <section class="container mx-auto px-6 py-12">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Review & Rating</h2>
                <div class="flex items-center gap-4">
                    @if($totalReviews > 0)
                        <div class="text-center">
                            <div class="text-3xl font-bold text-yellow-500">{{ number_format($averageRating, 1) }}</div>
                            <div class="text-sm text-gray-600">{{ $totalReviews }} review</div>
                        </div>
                        <div class="flex items-center gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $averageRating)
                                    <i class="fas fa-star text-yellow-400 text-xl"></i>
                                @else
                                    <i class="far fa-star text-gray-300 text-xl"></i>
                                @endif
                            @endfor
                        </div>
                    @else
                        <div class="text-gray-500">Belum ada review</div>
                    @endif
                </div>
            </div>

            @if($totalReviews > 0)
                <div class="space-y-6">
                    @foreach($reviews as $review)
                        <div class="border-b border-gray-200 pb-6 last:border-b-0">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-teal-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-800">
                                            {{ $review->user->name ?? 'Customer' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->rating)
                                            <i class="fas fa-star text-yellow-400"></i>
                                        @else
                                            <i class="far fa-star text-gray-300"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            @if($review->comment)
                                <div class="text-gray-700 ml-13">
                                    "{{ $review->comment }}"
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">
                        <i class="far fa-comment-dots"></i>
                    </div>
                    <h3 class="text-xl font-medium text-gray-600 mb-2">Belum ada review</h3>
                    <p class="text-gray-500">Jadilah yang pertama memberikan review untuk produk ini!</p>
                </div>
            @endif
        </div>
    </section>

    @include('layouts.footer')

    <script>
        const minBuyingStock = {{ $product->min_buying_stock ?? 1 }};
        
        // Initialize stock display on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateStockDisplay();
            
            // Add event listener for variant selection change
            const variantSelect = document.getElementById('variantSelect');
            if (variantSelect) {
                variantSelect.addEventListener('change', function() {
                    updateStockDisplay();
                    checkTawarButton();
                });
            }
        });
        
        function updateStockDisplay() {
            const variantSelect = document.getElementById('variantSelect');
            const stockText = document.getElementById('stockText');
            
            if (!variantSelect || !stockText) return;
            
            const selectedOption = variantSelect.options[variantSelect.selectedIndex];
            const stock = selectedOption ? parseInt(selectedOption.dataset.stock) : 0;
            
            if (stock > 0) {
                stockText.textContent = `Stok tersedia: ${stock} unit`;
                stockText.className = 'px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
            } else {
                stockText.textContent = 'Stok habis';
                stockText.className = 'px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800';
            }
        }
        
        function changeQty(amount) {
            const input = document.getElementById('qtyInput');
            let val = parseInt(input.value) || 1;
            val = Math.max(val + amount, 1);
            input.value = val;
            checkTawarButton();
        }
        
        function checkTawarButton() {
            const qtyInput = document.getElementById('qtyInput');
            const variantSelect = document.getElementById('variantSelect');
            const tawarButton = document.getElementById('tawarButton');
            const tawarInfo = document.getElementById('tawarInfo');
            const negosiasiQuantity = document.getElementById('negosiasiQuantity');
            const negosiasiQuantity2 = document.getElementById('negosiasiQuantity2');
            const cartQuantity = document.getElementById('cartQuantity');
            const cartVariantId = document.getElementById('cartVariantId');
            
            const currentQty = parseInt(qtyInput.value) || 1;
            const currentVariant = variantSelect ? variantSelect.value : '';
            
            // Get current stock for selected variant
            const selectedOption = variantSelect ? variantSelect.options[variantSelect.selectedIndex] : null;
            const currentStock = selectedOption ? parseInt(selectedOption.dataset.stock) : 0;
            
            // Update quantity di form cart
            if (cartQuantity) cartQuantity.value = currentQty;
            if (cartVariantId) cartVariantId.value = currentVariant;
            
            // Update quantity di form negosiasi
            if (negosiasiQuantity) negosiasiQuantity.value = currentQty;
            if (negosiasiQuantity2) negosiasiQuantity2.value = currentQty;
            
            if (!tawarButton || !tawarInfo) return;
            
            // Check if quantity meets minimum requirement and stock is available
            if (currentQty >= minBuyingStock && currentStock >= currentQty) {
                // Quantity dan stok cukup, enable tombol
                tawarButton.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
                tawarButton.classList.add('bg-gray-300', 'text-gray-800', 'hover:bg-gray-400');
                tawarButton.style.pointerEvents = 'auto';
                tawarInfo.classList.remove('text-red-500');
                tawarInfo.classList.add('text-gray-500');
                tawarInfo.textContent = `Minimal ${minBuyingStock} pcs untuk tawar menawar`;
            } else {
                // Quantity atau stok tidak cukup, disable tombol
                tawarButton.classList.remove('bg-gray-300', 'text-gray-800', 'hover:bg-gray-400');
                tawarButton.classList.add('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
                tawarButton.style.pointerEvents = 'none';
                tawarInfo.classList.remove('text-gray-500');
                tawarInfo.classList.add('text-red-500');
                
                if (currentQty < minBuyingStock) {
                    tawarInfo.textContent = `Minimal ${minBuyingStock} pcs untuk tawar menawar (quantity tidak cukup)`;
                } else if (currentStock < currentQty) {
                    tawarInfo.textContent = `Stok tidak mencukupi untuk quantity ${currentQty} (tersedia: ${currentStock})`;
                }
            }
        }
        
        // Check saat halaman load
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi variant yang dipilih saat pertama kali load
            const variantSelect = document.getElementById('variantSelect');
            const cartVariantId = document.getElementById('cartVariantId');
            
            // Pastikan cartVariantId diisi dengan variant yang dipilih
            if (variantSelect && cartVariantId) {
                cartVariantId.value = variantSelect.value;
                console.log('Variant initialized:', variantSelect.value); // Debug
            }
            
            checkTawarButton();
            
            // Check saat input berubah
            const qtyInput = document.getElementById('qtyInput');
            if (qtyInput) {
                qtyInput.addEventListener('input', checkTawarButton);
            }
            
            // Check saat variant berubah
            if (variantSelect) {
                variantSelect.addEventListener('change', function() {
                    // Update cartVariantId saat variant berubah
                    if (cartVariantId) {
                        cartVariantId.value = this.value;
                        console.log('Variant changed to:', this.value); // Debug
                    }
                    checkTawarButton();
                });
            }
        });
    </script>
</body>

</html>

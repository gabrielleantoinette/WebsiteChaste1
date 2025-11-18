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
                <div class="w-full aspect-square overflow-hidden rounded-l-2xl">
                    <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
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
                </h1>
                <p class="text-xl text-teal-600 font-semibold" id="priceDisplay">
                    @if(isset($sizeOptions) && count($sizeOptions) > 0)
                        @php
                            $defaultSize = $sizeOptions->firstWhere('size', '2x3') ?? $sizeOptions->first();
                        @endphp
                        Rp {{ number_format($defaultSize['price'] ?? 0, 0, ',', '.') }}
                    @else
                        Rp {{ $priceRange }}
                    @endif
                    <span class="text-sm text-gray-500">/ unit</span>
                </p>
                <p class="text-sm text-yellow-600 bg-yellow-50 px-3 py-2 rounded-lg border border-pink-200">
                     <strong>Ambil banyak untuk harga lebih murah!</strong>
                </p>
                
                <!-- Deskripsi Produk (Collapsible) -->
                @if($product->description)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button onclick="toggleDescription()" class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition-colors text-left">
                        <span class="font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Deskripsi Produk
                        </span>
                        <svg id="descriptionIcon" class="w-5 h-5 text-gray-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="descriptionContent" class="hidden p-4 bg-white border-t border-gray-200">
                        <p class="text-gray-600 leading-relaxed whitespace-pre-line">{{ $product->description }}</p>
                    </div>
                </div>
                @endif
                
                <!-- Tata Cara Menawar Harga (Collapsible) -->
                @if($product->min_buying_stock && $product->min_buying_stock > 0)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button onclick="toggleNegotiationGuide()" class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition-colors text-left">
                        <span class="font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Tata Cara Menawar Harga
                        </span>
                        <svg id="negotiationIcon" class="w-5 h-5 text-gray-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="negotiationContent" class="hidden p-4 bg-white border-t border-gray-200">
                        <div class="space-y-3 text-sm text-gray-700">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="font-semibold text-blue-900 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Tips Negosiasi
                                </p>
                                <p class="text-blue-800">Tawar 60-80% dari harga normal untuk hasil terbaik</p>
                            </div>
                            <div class="space-y-2">
                                <p class="font-semibold text-gray-800">Cara Menawar:</p>
                                <ol class="list-decimal list-inside space-y-1 text-gray-600 ml-2">
                                    <li>Pilih ukuran, warna, dan jumlah produk yang diinginkan</li>
                                    <li>Pastikan jumlah minimal {{ $product->min_buying_stock }} pcs untuk bisa menawar</li>
                                    <li>Klik tombol "Tawar Harga" di bawah ketika sudah menyala</li>
                                    <li>Masukkan harga tawaran Anda (harga per pcs/unit) dengan maksimal 3 kali tawar</li>
                                    <li>Tunggu konfirmasi dari sistem</li>
                                    <li>Jika deal, harga akan dihitung: <strong>Harga Tawar × Jumlah</strong></li>
                                </ol>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-3">
                                <p class="text-yellow-800 text-xs font-medium flex items-start gap-2">
                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <span><strong>Penting:</strong> Tawar menawar adalah harga per pcs (per unit), bukan subtotal. Total harga akan dihitung berdasarkan harga tawar × quantity.</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Size, Variant & Quantity -->
                <div class="space-y-4">
                    @if(isset($sizeOptions) && count($sizeOptions) > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran</label>
                        <select id="sizeSelect" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-teal-400">
                            @foreach($sizeOptions as $opt)
                                <option value="{{ $opt['size'] }}" data-price="{{ $opt['price'] }}" {{ $opt['size'] == '2x3' ? 'selected' : '' }}>
                                    {{ $opt['size'] }} (Rp {{ number_format($opt['price'], 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
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
                                class="w-16 text-center border-l border-r border-gray-300 focus:outline-none" 
                                onchange="validateQuantity()" oninput="validateQuantity()" />
                            <button type="button" onclick="changeQty(1)"
                                class="px-3 py-2 text-lg hover:bg-gray-100">+</button>
                        </div>
                    </div>

                    <!-- Subtotal Display -->
                    <div class="bg-teal-50 border border-teal-200 rounded-lg p-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Subtotal:</span>
                            <span id="subtotalDisplay" class="text-lg font-bold text-teal-600">
                                @if(isset($sizeOptions) && count($sizeOptions) > 0)
                                    @php
                                        $defaultSize = $sizeOptions->firstWhere('size', '2x3') ?? $sizeOptions->first();
                                    @endphp
                                    Rp {{ number_format($defaultSize['price'] ?? 0, 0, ',', '.') }}
                                @else
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                @endif
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1" id="subtotalDetail">
                            @if(isset($sizeOptions) && count($sizeOptions) > 0)
                                @php
                                    $defaultSize = $sizeOptions->firstWhere('size', '2x3') ?? $sizeOptions->first();
                                @endphp
                                Rp {{ number_format($defaultSize['price'] ?? 0, 0, ',', '.') }} × 1 pcs
                            @else
                                Rp {{ number_format($product->price, 0, ',', '.') }} × 1 pcs
                            @endif
                        </p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-3 sm:space-y-0">
                        <!-- Form Tambah ke Keranjang -->
                        <form action="{{ route('produk.add', $product->id) }}" method="POST" class="flex-1" id="addToCartForm">
                            @csrf
                            <input type="hidden" name="variant_id" id="cartVariantId" value="">
                            <input type="hidden" name="quantity" id="cartQuantity" value="1">
                            <input type="hidden" name="selected_size" id="cartSelectedSize" value="2x3">
                            <button type="submit" id="addToCartButton"
                                class="w-full bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition">
                                Tambah ke Keranjang
                            </button>
                        </form>
                        
                        <!-- Form Tawar Harga -->
                        <div class="flex-1">
                            @if($product->min_buying_stock && $product->min_buying_stock > 0)
                                <form action="{{ route('produk.negosiasi', $product) }}" method="GET" class="inline w-full">
                                    <input type="hidden" name="quantity" id="negosiasiQuantity" value="1">
                                    <input type="hidden" name="selected_size" id="negosiasiSelectedSize" value="2x3">
                                    <button type="submit"
                                            id="tawarButton"
                                            class="w-full bg-gray-300 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
                                        Tawar Harga
                                    </button>
                                </form>
                                <p class="text-xs text-gray-500 mt-1" id="tawarInfo">
                                    Minimal {{ $product->min_buying_stock }} pcs untuk tawar menawar
                                </p>
                            @else
                                <button type="button"
                                        id="tawarButton"
                                        disabled
                                        class="w-full bg-gray-200 text-gray-400 px-6 py-3 rounded-lg cursor-not-allowed">
                                    Tawar Harga
                                </button>
                                <p class="text-xs text-red-500 mt-1" id="tawarInfo">
                                    Barang tidak dapat ditawar
                                </p>
                            @endif
                        </div>
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
        const minBuyingStock = {{ $product->min_buying_stock ?? 0 }};
        
        // Toggle functions for collapsible sections
        function toggleDescription() {
            const content = document.getElementById('descriptionContent');
            const icon = document.getElementById('descriptionIcon');
            if (content && icon) {
                content.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
            }
        }
        
        function toggleNegotiationGuide() {
            const content = document.getElementById('negotiationContent');
            const icon = document.getElementById('negotiationIcon');
            if (content && icon) {
                content.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
            }
        }
        
        // Initialize stock display on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateStockDisplay();
            updatePriceDisplay();
            updateSubtotal();
            
            // Add event listener for size selection change
            const sizeSelect = document.getElementById('sizeSelect');
            if (sizeSelect) {
                sizeSelect.addEventListener('change', function() {
                    updatePriceDisplay();
                    updateCartForm();
                    updateSubtotal();
                });
            }
            
            // Add event listener for quantity input
            const qtyInput = document.getElementById('qtyInput');
            if (qtyInput) {
                qtyInput.addEventListener('input', function() {
                    updateSubtotal();
                });
            }
            
            // Add event listener for variant selection change
            const variantSelect = document.getElementById('variantSelect');
            if (variantSelect) {
                variantSelect.addEventListener('change', function() {
                    updateStockDisplay();
                    checkTawarButton();
                });
            }
            
            // Add event listener for form submission validation
            const addToCartForm = document.getElementById('addToCartForm');
            if (addToCartForm) {
                addToCartForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const qtyInput = document.getElementById('qtyInput');
                    const variantSelect = document.getElementById('variantSelect');
                    const selectedOption = variantSelect ? variantSelect.options[variantSelect.selectedIndex] : null;
                    const currentStock = selectedOption ? parseInt(selectedOption.dataset.stock) : 0;
                    const requestedQty = parseInt(qtyInput.value) || 1;
                    
                    if (currentStock === 0) {
                        alert('Stok habis untuk warna yang dipilih!');
                        return;
                    }
                    
                    if (requestedQty > currentStock) {
                        alert(`Stok tidak mencukupi! Tersedia: ${currentStock} unit, diminta: ${requestedQty} unit`);
                        return;
                    }
                    
                    // If validation passes, submit the form
                    this.submit();
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
        
        function updatePriceDisplay() {
            const sizeSelect = document.getElementById('sizeSelect');
            const priceDisplay = document.getElementById('priceDisplay');
            const qtyInput = document.getElementById('qtyInput');
            
            if (!sizeSelect || !priceDisplay) return;
            
            const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
            const price = selectedOption ? parseFloat(selectedOption.dataset.price) : 0;
            const quantity = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
            
            priceDisplay.innerHTML = `Rp ${price.toLocaleString('id-ID')} <span class="text-sm text-gray-500">/ unit</span>`;
            
            // Update subtotal
            updateSubtotal();
        }
        
        function updateSubtotal() {
            const sizeSelect = document.getElementById('sizeSelect');
            const qtyInput = document.getElementById('qtyInput');
            const subtotalDisplay = document.getElementById('subtotalDisplay');
            const subtotalDetail = document.getElementById('subtotalDetail');
            
            if (!sizeSelect || !qtyInput || !subtotalDisplay) return;
            
            const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
            const price = selectedOption ? parseFloat(selectedOption.dataset.price) : 0;
            const quantity = parseInt(qtyInput.value) || 1;
            const subtotal = price * quantity;
            
            subtotalDisplay.textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
            if (subtotalDetail) {
                subtotalDetail.textContent = `Rp ${price.toLocaleString('id-ID')} × ${quantity} pcs`;
            }
        }
        
        function updateCartForm() {
            const sizeSelect = document.getElementById('sizeSelect');
            const variantSelect = document.getElementById('variantSelect');
            const cartVariantId = document.getElementById('cartVariantId');
            const cartSelectedSize = document.getElementById('cartSelectedSize');
            
            if (sizeSelect && cartSelectedSize) {
                cartSelectedSize.value = sizeSelect.value;
            }
            
            if (variantSelect && cartVariantId) {
                cartVariantId.value = variantSelect.value;
            }
        }
        
        function validateQuantity() {
            const input = document.getElementById('qtyInput');
            const variantSelect = document.getElementById('variantSelect');
            
            let val = parseInt(input.value) || 1;
            val = Math.max(val, 1);
            
            // Get current stock for selected variant
            const selectedOption = variantSelect ? variantSelect.options[variantSelect.selectedIndex] : null;
            const currentStock = selectedOption ? parseInt(selectedOption.dataset.stock) : 0;
            
            // Limit quantity to available stock
            if (currentStock > 0 && val > currentStock) {
                val = currentStock;
                input.value = val;
                alert(`Maksimal quantity: ${currentStock} unit (sesuai stok tersedia)`);
            }
            
            updateSubtotal();
            checkTawarButton();
        }
        
        function changeQty(amount) {
            const input = document.getElementById('qtyInput');
            const variantSelect = document.getElementById('variantSelect');
            
            let val = parseInt(input.value) || 1;
            val = Math.max(val + amount, 1);
            
            // Get current stock for selected variant
            const selectedOption = variantSelect ? variantSelect.options[variantSelect.selectedIndex] : null;
            const currentStock = selectedOption ? parseInt(selectedOption.dataset.stock) : 0;
            
            // Limit quantity to available stock
            if (currentStock > 0) {
                val = Math.min(val, currentStock);
            }
            
            input.value = val;
            updateSubtotal();
            checkTawarButton();
        }
        
        function checkTawarButton() {
            const qtyInput = document.getElementById('qtyInput');
            const variantSelect = document.getElementById('variantSelect');
            const tawarButton = document.getElementById('tawarButton');
            const tawarInfo = document.getElementById('tawarInfo');
            const negosiasiQuantity = document.getElementById('negosiasiQuantity');
            const negosiasiSelectedSize = document.getElementById('negosiasiSelectedSize');
            const cartQuantity = document.getElementById('cartQuantity');
            const cartVariantId = document.getElementById('cartVariantId');
            const sizeSelect = document.getElementById('sizeSelect');
            
            const currentQty = parseInt(qtyInput.value) || 1;
            const currentVariant = variantSelect ? variantSelect.value : '';
            const currentSize = sizeSelect ? sizeSelect.value : '2x3';
            
            // Get current stock for selected variant
            const selectedOption = variantSelect ? variantSelect.options[variantSelect.selectedIndex] : null;
            const currentStock = selectedOption ? parseInt(selectedOption.dataset.stock) : 0;
            
            // Update quantity di form cart
            if (cartQuantity) cartQuantity.value = currentQty;
            if (cartVariantId) cartVariantId.value = currentVariant;
            
            // Update quantity dan size di form negosiasi
            if (negosiasiQuantity) negosiasiQuantity.value = currentQty;
            if (negosiasiSelectedSize) negosiasiSelectedSize.value = currentSize;
            
            // Jika tidak ada min_buying_stock atau min_buying_stock = 0, disable button
            if (!tawarButton || !tawarInfo) return;
            
            if (minBuyingStock === 0 || !minBuyingStock) {
                // Barang tidak dapat ditawar
                tawarButton.disabled = true;
                tawarButton.classList.remove('bg-gray-300', 'text-gray-800', 'hover:bg-gray-400');
                tawarButton.classList.add('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
                tawarInfo.classList.remove('text-gray-500');
                tawarInfo.classList.add('text-red-500');
                tawarInfo.textContent = 'Barang tidak dapat ditawar';
                return;
            }
            
            // Check if quantity meets minimum requirement and stock is available
            if (currentQty >= minBuyingStock && currentStock >= currentQty) {
                // Quantity dan stok cukup, enable tombol
                tawarButton.disabled = false;
                tawarButton.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
                tawarButton.classList.add('bg-gray-300', 'text-gray-800', 'hover:bg-gray-400');
                tawarInfo.classList.remove('text-red-500');
                tawarInfo.classList.add('text-gray-500');
                tawarInfo.textContent = `Minimal ${minBuyingStock} pcs untuk tawar menawar`;
            } else {
                // Quantity atau stok tidak cukup, disable tombol
                tawarButton.disabled = true;
                tawarButton.classList.remove('bg-gray-300', 'text-gray-800', 'hover:bg-gray-400');
                tawarButton.classList.add('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
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

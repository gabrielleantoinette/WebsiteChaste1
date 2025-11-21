<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Keranjang Belanja | CHASTE</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-white font-sans text-gray-800">

    @include('layouts.customer-nav')

    <section class="px-4 sm:px-6 lg:px-12 py-8 min-h-screen">
        <div class="mb-6">
            <x-breadcrumb :items="[
                ['label' => 'Keranjang']
            ]" />
        </div>
        <div class="max-w-5xl mx-auto">
            <br>
            <h2 class="text-2xl font-bold mb-6">Keranjang Belanja</h2>

            @if($cartItems->isEmpty())
                {{-- Empty Cart State --}}
                <div class="flex flex-col items-center justify-center py-16 px-4 min-h-[60vh]">
                    <div class="mb-8">
                        {{-- Icon Keranjang Kosong --}}
                        <div class="flex items-center justify-center">
                            <svg class="w-32 h-32 text-gray-300 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <h3 class="text-3xl font-bold text-gray-800 mb-3">Keranjang Belanja Anda Kosong</h3>
                    <p class="text-gray-500 text-center mb-8 max-w-md text-lg">
                        Belum ada produk di keranjang belanja Anda. Mulai berbelanja dan tambahkan produk favorit Anda ke keranjang!
                    </p>
                    
                    <a href="{{ route('produk') }}" 
                       class="inline-flex items-center gap-2 bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Mulai Berbelanja
                    </a>
                </div>
            @else
            <form action="{{ route('checkout') }}" method="GET" class="space-y-6">
                @csrf
                <div class="flex items-center mb-4">
                    <input type="checkbox" id="selectAll" class="mr-2">
                    <label for="selectAll" class="text-sm">Pilih Semua</label>
                </div>

                @foreach ($cartItems as $item)
                    <div class="flex items-center gap-4 border-b pb-6">
                        <input type="checkbox" class="item-checkbox" name="selected_items[]"
                            value="{{ $item->id }}">
                        
                        <!-- Gambar Produk -->
                        @if ($item->variant && $item->variant->product)
                            <img src="{{ asset($item->variant->product->image_url) }}" 
                                 alt="{{ $item->variant->product->name ?? 'Produk' }}"
                                 class="w-20 h-20 object-cover rounded-md">
                        @else
                            <img src="{{ asset('images/gulungan-terpal.png') }}" 
                                 alt="Produk"
                                 class="w-20 h-20 object-cover rounded-md">
                        @endif

                        <div class="flex-1">
                            <p class="font-semibold">
                                @if ($item->variant && $item->variant->product)
                                    {{ $item->variant->product->name ?? 'Nama Produk' }}
                                @else
                                    Custom Terpal
                                @endif
                            </p>

                            @if (!$item->variant)
                                <div class="text-sm text-gray-500 mt-1 space-y-1">
                                    @if ($item->bahan_custom)
                                        <div><span class="font-medium">Bahan:</span> {{ $item->bahan_custom }}</div>
                                    @endif
                                    @if ($item->kebutuhan_custom)
                                        <div><span class="font-medium">Kebutuhan:</span> {{ $item->kebutuhan_custom }}</div>
                                    @endif
                                    @if ($item->ukuran_custom)
                                        <div><span class="font-medium">Ukuran:</span> {{ $item->ukuran_custom }}</div>
                                    @endif
                                    @if ($item->warna_custom)
                                        <div><span class="font-medium">Warna:</span> {{ $item->warna_custom }}</div>
                                    @endif
                                    @if ($item->jumlah_ring_custom)
                                        <div><span class="font-medium">Jumlah Ring:</span> {{ $item->jumlah_ring_custom }} buah</div>
                                    @endif
                                    @if ($item->pakai_tali_custom)
                                        <div><span class="font-medium">Tali:</span> 
                                            @if($item->pakai_tali_custom == 'ya' || $item->pakai_tali_custom == '1' || $item->pakai_tali_custom == 1)
                                                Ya, perlu tali
                                            @elseif($item->pakai_tali_custom == 'tidak' || $item->pakai_tali_custom == '0' || $item->pakai_tali_custom == 0)
                                                Tidak
                                            @else
                                                {{ $item->pakai_tali_custom }}
                                            @endif
                                        </div>
                                    @endif
                                    @if ($item->catatan_custom)
                                        <div><span class="font-medium">Catatan:</span> {{ $item->catatan_custom }}</div>
                                    @endif
                                </div>
                            @else
                                <p class="text-sm text-gray-500">
                                    Warna: {{ ucfirst($item->variant->color ?? '-') }}
                                    @if ($item->selected_size)
                                        <br>Ukuran: {{ $item->selected_size }}
                                    @endif
                                </p>
                            @endif
                        </div>

                        <div class="text-right">
                            <p class="font-semibold text-gray-700" id="item-subtotal-{{ $item->id }}">
                                @if ($item->harga_custom && $item->kebutuhan_custom && str_contains($item->kebutuhan_custom, 'Hasil negosiasi'))
                                    <!-- Harga hasil negosiasi -->
                                    <span class="text-teal-600">Rp {{ number_format($item->harga_custom * $item->quantity, 0, ',', '.') }}</span>
                                    <div class="text-xs text-gray-500">(Hasil Negosiasi - Rp {{ number_format($item->harga_custom, 0, ',', '.') }} × <span id="item-qty-display-{{ $item->id }}">{{ $item->quantity }}</span>)</div>
                                @elseif ($item->variant)
                                    <!-- Harga normal produk berdasarkan ukuran yang dipilih -->
                                    @php
                                        $selectedSize = $item->selected_size ?? '2x3';
                                        $calculatedPrice = $item->variant->product->getPriceForSize($selectedSize);
                                    @endphp
                                    <span class="text-teal-600">Rp {{ number_format($calculatedPrice * $item->quantity, 0, ',', '.') }}</span>
                                    <div class="text-xs text-gray-500">(Rp {{ number_format($calculatedPrice, 0, ',', '.') }} × <span id="item-qty-display-{{ $item->id }}">{{ $item->quantity }}</span>)</div>
                                @else
                                    <!-- Harga custom terpal -->
                                    <span class="text-teal-600">Rp {{ number_format(($item->harga_custom ?? 0) * $item->quantity, 0, ',', '.') }}</span>
                                    <div class="text-xs text-gray-500">(Rp {{ number_format($item->harga_custom ?? 0, 0, ',', '.') }} × <span id="item-qty-display-{{ $item->id }}">{{ $item->quantity }}</span>)</div>
                                @endif
                            </p>
                            
                            {{-- Quantity Control dengan tombol + dan - --}}
                            <div class="flex items-center justify-end gap-2 mt-2">
                                <button type="button" 
                                        onclick="decrementQuantity({{ $item->id }})"
                                        class="w-8 h-8 flex items-center justify-center bg-gray-200 hover:bg-gray-300 rounded-md text-gray-700 font-semibold transition">
                                    -
                                </button>
                                <input type="number" 
                                       id="quantity-{{ $item->id }}"
                                       value="{{ $item->quantity }}" 
                                       min="1"
                                       max="{{ $item->variant && $item->variant->stock ? $item->variant->stock : 999 }}"
                                       class="w-16 text-center border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
                                       onchange="updateQuantity({{ $item->id }}, this.value)"
                                       onblur="validateQuantity({{ $item->id }})">
                                <button type="button" 
                                        onclick="incrementQuantity({{ $item->id }})"
                                        class="w-8 h-8 flex items-center justify-center bg-gray-200 hover:bg-gray-300 rounded-md text-gray-700 font-semibold transition">
                                    +
                                </button>
                            </div>

                            <a href="{{ route('keranjang.delete', $item->id) }}"
                                class="text-red-500 text-xs mt-2 inline-block hover:underline">Hapus
                            </a>
                        </div>
                    </div>
                @endforeach

                <!-- Total Harga -->
                <div class="border-t pt-6 mt-6">
                    <div class="flex justify-between items-center">
                        <div class="text-lg font-semibold text-gray-800">
                            Total Belanja:
                        </div>
                        <div class="text-xl font-bold text-teal-600" id="totalHarga">
                            Rp 0
                        </div>
                    </div>
                    <div class="text-sm text-gray-500 mt-2" id="totalDetail">
                        Pilih item untuk melihat total
                    </div>
                </div>

                <div class="flex justify-end mt-8">
                    <button type="submit"
                        class="bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 px-6 rounded-md">
                        Lanjut Bayar
                    </button>
                </div>
            </form>
            @endif
        </div>
    </section>

    @include('layouts.footer')

    {{-- JavaScript Checkbox All & Total Calculation --}}
    <script>
        // Data harga untuk setiap item
        const itemPrices = {
            @if($cartItems->isNotEmpty())
            @foreach ($cartItems as $item)
                {{ $item->id }}: {
                    price: @if ($item->harga_custom && $item->kebutuhan_custom && str_contains($item->kebutuhan_custom, 'Hasil negosiasi'))
                        {{ $item->harga_custom }}
                    @elseif ($item->variant)
                        @php
                            $selectedSize = $item->selected_size ?? '2x3';
                            $calculatedPrice = $item->variant->product->getPriceForSize($selectedSize);
                        @endphp
                        {{ $calculatedPrice }}
                    @else
                        {{ $item->harga_custom ?? 0 }}
                    @endif,
                    quantity: {{ $item->quantity }},
                    name: "{{ $item->variant && $item->variant->product ? $item->variant->product->name : 'Custom Terpal' }}",
                    color: "{{ $item->variant ? ucfirst($item->variant->color) : ($item->warna_custom ?? '-') }}",
                    size: "{{ $item->selected_size ?? ($item->ukuran_custom ?? '-') }}",
                    isNegotiated: {{ $item->harga_custom && $item->kebutuhan_custom && str_contains($item->kebutuhan_custom, 'Hasil negosiasi') ? 'true' : 'false' }},
                    maxStock: {{ $item->variant && $item->variant->stock ? $item->variant->stock : 999 }}
                },
            @endforeach
            @endif
        };

        // Function untuk increment quantity
        function incrementQuantity(itemId) {
            const input = document.getElementById('quantity-' + itemId);
            const currentQty = parseInt(input.value) || 1;
            const maxStock = itemPrices[itemId]?.maxStock || 999;
            
            if (currentQty < maxStock) {
                const newQty = currentQty + 1;
                input.value = newQty;
                updateQuantity(itemId, newQty);
            } else {
                alert('Stok tidak mencukupi. Stok tersedia: ' + maxStock);
            }
        }

        // Function untuk decrement quantity
        function decrementQuantity(itemId) {
            const input = document.getElementById('quantity-' + itemId);
            const currentQty = parseInt(input.value) || 1;
            
            if (currentQty > 1) {
                const newQty = currentQty - 1;
                input.value = newQty;
                updateQuantity(itemId, newQty);
            }
        }

        // Function untuk validate quantity
        function validateQuantity(itemId) {
            const input = document.getElementById('quantity-' + itemId);
            let qty = parseInt(input.value) || 1;
            const maxStock = itemPrices[itemId]?.maxStock || 999;
            
            if (qty < 1) {
                qty = 1;
                input.value = qty;
            } else if (qty > maxStock) {
                qty = maxStock;
                input.value = qty;
                alert('Stok tidak mencukupi. Stok tersedia: ' + maxStock);
            }
            
            if (qty !== itemPrices[itemId].quantity) {
                updateQuantity(itemId, qty);
            }
        }

        // Function untuk update quantity via AJAX
        function updateQuantity(itemId, quantity) {
            const input = document.getElementById('quantity-' + itemId);
            const qty = parseInt(quantity) || 1;
            
            // Disable input saat update
            input.disabled = true;
            
            fetch(`{{ route('keranjang.update', ':id') }}`.replace(':id', itemId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ quantity: qty })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'HTTP error! status: ' + response.status);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update quantity di itemPrices
                    itemPrices[itemId].quantity = data.quantity;
                    
                    // Update display quantity
                    const qtyDisplay = document.getElementById('item-qty-display-' + itemId);
                    if (qtyDisplay) {
                        qtyDisplay.textContent = data.quantity;
                    }
                    
                    // Update subtotal display
                    const subtotalEl = document.getElementById('item-subtotal-' + itemId);
                    if (subtotalEl) {
                        const item = itemPrices[itemId];
                        const subtotal = item.price * data.quantity;
                        const priceText = item.isNegotiated ? 
                            `Rp ${item.price.toLocaleString('id-ID')} (Hasil Negosiasi)` : 
                            `Rp ${item.price.toLocaleString('id-ID')}`;
                        
                        subtotalEl.innerHTML = `
                            <span class="text-teal-600">Rp ${subtotal.toLocaleString('id-ID')}</span>
                            <div class="text-xs text-gray-500">(${priceText} × ${data.quantity})</div>
                        `;
                    }
                    
                    // Update total jika item terpilih
                    updateTotal();
                } else {
                    alert(data.message || 'Gagal mengupdate quantity');
                    // Revert input value
                    input.value = itemPrices[itemId].quantity;
                }
                
                // Enable input kembali
                input.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengupdate quantity: ' + error.message);
                // Revert input value
                input.value = itemPrices[itemId].quantity;
                input.disabled = false;
            });
        }

        function updateTotal() {
            // Cek apakah elemen total ada (hanya ada jika cart tidak kosong)
            const totalHargaEl = document.getElementById('totalHarga');
            const totalDetailEl = document.getElementById('totalDetail');
            
            if (!totalHargaEl || !totalDetailEl) {
                return; // Cart kosong, tidak perlu update total
            }
            
            let total = 0;
            let selectedItems = [];
            let checkboxes = document.querySelectorAll('.item-checkbox:checked');
            
            checkboxes.forEach(checkbox => {
                const itemId = parseInt(checkbox.value);
                const item = itemPrices[itemId];
                if (item) {
                    const itemTotal = item.price * item.quantity;
                    total += itemTotal;
                    
                    // Tambahkan detail item
                    const priceText = item.isNegotiated ? 
                        `Rp ${item.price.toLocaleString('id-ID')} (Hasil Negosiasi)` : 
                        `Rp ${item.price.toLocaleString('id-ID')}`;
                    
                    selectedItems.push({
                        name: item.name,
                        color: item.color,
                        size: item.size,
                        quantity: item.quantity,
                        price: priceText,
                        total: itemTotal
                    });
                }
            });

            // Update total harga
            totalHargaEl.textContent = `Rp ${total.toLocaleString('id-ID')}`;
            
            // Update detail
            if (selectedItems.length === 0) {
                totalDetailEl.textContent = 'Pilih item untuk melihat total';
            } else {
                let detailText = selectedItems.map(item => 
                    `${item.name} (${item.color}, ${item.size}) - ${item.quantity} pcs × ${item.price} = Rp ${item.total.toLocaleString('id-ID')}`
                ).join('\n');
                totalDetailEl.innerHTML = detailText.replace(/\n/g, '<br>');
            }
        }

        // Event listener untuk select all (jika ada)
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                let checkboxes = document.querySelectorAll('.item-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateTotal();
            });
        }

        // Event listener untuk setiap checkbox item
        document.querySelectorAll('.item-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateTotal);
        });

        // Update total saat halaman load
        document.addEventListener('DOMContentLoaded', function() {
            updateTotal();
        });

        // Handle form submission untuk memastikan checkbox terpilih dikirim (jika form ada)
        const checkoutForm = document.querySelector('form[action="{{ route("checkout") }}"]');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function(e) {
            let checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Pilih minimal satu item untuk checkout.');
                return false;
            }
            
            // Pastikan form dikirim dengan method GET dan parameter selected_items
            let form = this;
            let selectedItems = Array.from(checkedBoxes).map(cb => cb.value);
            
            // Build URL dengan parameter selected_items
            let url = new URL(form.action);
            selectedItems.forEach(itemId => {
                url.searchParams.append('selected_items[]', itemId);
            });
            
                // Redirect ke URL yang sudah dibangun
                window.location.href = url.toString();
                e.preventDefault();
            });
        }
    </script>

</body>

</html>

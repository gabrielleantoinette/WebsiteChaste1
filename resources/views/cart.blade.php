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

    <section class="px-6 md:px-30 py-8 min-h-screen">
        <div class="mb-6">
            <x-breadcrumb :items="[
                ['label' => 'Keranjang']
            ]" />
        </div>
        <div class="max-w-5xl mx-auto">
            <br>
            <h2 class="text-2xl font-bold mb-6">Keranjang Belanja</h2>

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
                                    @if ($item->kebutuhan_custom)
                                        <div>Kebutuhan: {{ $item->kebutuhan_custom }}</div>
                                    @endif
                                    @if ($item->ukuran_custom)
                                        <div>Ukuran: {{ $item->ukuran_custom }}</div>
                                    @endif
                                    @if ($item->warna_custom)
                                        <div>Warna: {{ $item->warna_custom }}</div>
                                    @endif
                                    @if ($item->jumlah_ring_custom)
                                        <div>Jumlah Ring: {{ $item->jumlah_ring_custom }}</div>
                                    @endif
                                    @if ($item->pakai_tali_custom)
                                        <div>Pakai Tali: {{ $item->pakai_tali_custom }}</div>
                                    @endif
                                    @if ($item->catatan_custom)
                                        <div>Catatan: {{ $item->catatan_custom }}</div>
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
                            <p class="font-semibold text-gray-700">
                                @if ($item->harga_custom && $item->kebutuhan_custom && str_contains($item->kebutuhan_custom, 'Hasil negosiasi'))
                                    <!-- Harga hasil negosiasi -->
                                    <span class="text-teal-600">Rp {{ number_format($item->harga_custom * $item->quantity, 0, ',', '.') }}</span>
                                    <div class="text-xs text-gray-500">(Hasil Negosiasi - Rp {{ number_format($item->harga_custom, 0, ',', '.') }} × {{ $item->quantity }})</div>
                                @elseif ($item->variant)
                                    <!-- Harga normal produk berdasarkan ukuran yang dipilih -->
                                    @php
                                        $pricePerM2 = $item->variant->product->price / (2 * 3); // Assuming current price is for 2x3
                                        $sizeMap = [
                                            '2x3' => ['width' => 2, 'height' => 3],
                                            '3x4' => ['width' => 3, 'height' => 4],
                                            '4x6' => ['width' => 4, 'height' => 6],
                                            '6x8' => ['width' => 6, 'height' => 8]
                                        ];
                                        $selectedSize = $item->selected_size ?? '2x3';
                                        $size = $sizeMap[$selectedSize] ?? $sizeMap['2x3'];
                                        $calculatedPrice = $pricePerM2 * $size['width'] * $size['height'];
                                    @endphp
                                    Rp {{ number_format($calculatedPrice * $item->quantity, 0, ',', '.') }}
                                    <div class="text-xs text-gray-500">(Rp {{ number_format($calculatedPrice, 0, ',', '.') }} × {{ $item->quantity }})</div>
                                @else
                                    <!-- Harga custom terpal -->
                                    Rp {{ number_format($item->harga_custom ?? 0, 0, ',', '.') }}
                                @endif
                            </p>
                            <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>

                            <a href="{{ route('keranjang.delete', $item->id) }}"
                                class="text-red-500 text-xs mt-2 hover:underline">Hapus
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
        </div>
    </section>

    @include('layouts.footer')

    {{-- JavaScript Checkbox All & Total Calculation --}}
    <script>
        // Data harga untuk setiap item
        const itemPrices = {
            @foreach ($cartItems as $item)
                {{ $item->id }}: {
                    price: @if ($item->harga_custom && $item->kebutuhan_custom && str_contains($item->kebutuhan_custom, 'Hasil negosiasi'))
                        {{ $item->harga_custom }}
                    @elseif ($item->variant)
                        @php
                            $pricePerM2 = $item->variant->product->price / (2 * 3); // Assuming current price is for 2x3
                            $sizeMap = [
                                '2x3' => ['width' => 2, 'height' => 3],
                                '3x4' => ['width' => 3, 'height' => 4],
                                '4x6' => ['width' => 4, 'height' => 6],
                                '6x8' => ['width' => 6, 'height' => 8]
                            ];
                            $selectedSize = $item->selected_size ?? '2x3';
                            $size = $sizeMap[$selectedSize] ?? $sizeMap['2x3'];
                            $calculatedPrice = $pricePerM2 * $size['width'] * $size['height'];
                        @endphp
                        {{ $calculatedPrice }}
                    @else
                        {{ $item->harga_custom ?? 0 }}
                    @endif,
                    quantity: {{ $item->quantity }},
                    name: "{{ $item->variant && $item->variant->product ? $item->variant->product->name : 'Custom Terpal' }}",
                    color: "{{ $item->variant ? ucfirst($item->variant->color) : ($item->warna_custom ?? '-') }}",
                    size: "{{ $item->selected_size ?? ($item->ukuran_custom ?? '-') }}",
                    isNegotiated: {{ $item->harga_custom && $item->kebutuhan_custom && str_contains($item->kebutuhan_custom, 'Hasil negosiasi') ? 'true' : 'false' }}
                },
            @endforeach
        };

        function updateTotal() {
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
            document.getElementById('totalHarga').textContent = `Rp ${total.toLocaleString('id-ID')}`;
            
            // Update detail
            const totalDetail = document.getElementById('totalDetail');
            if (selectedItems.length === 0) {
                totalDetail.textContent = 'Pilih item untuk melihat total';
            } else {
                let detailText = selectedItems.map(item => 
                    `${item.name} (${item.color}, ${item.size}) - ${item.quantity} pcs × ${item.price} = Rp ${item.total.toLocaleString('id-ID')}`
                ).join('\n');
                totalDetail.innerHTML = detailText.replace(/\n/g, '<br>');
            }
        }

        // Event listener untuk select all
        document.getElementById('selectAll').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateTotal();
        });

        // Event listener untuk setiap checkbox item
        document.querySelectorAll('.item-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateTotal);
        });

        // Update total saat halaman load
        document.addEventListener('DOMContentLoaded', function() {
            updateTotal();
        });

        // Handle form submission untuk memastikan checkbox terpilih dikirim
        document.querySelector('form').addEventListener('submit', function(e) {
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
    </script>

</body>

</html>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja | CHASTE</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-white font-sans text-gray-800">

    @include('layouts.customer-nav')

    <section class="px-6 md:px-30 py-8 min-h-screen">
        <a href="{{ url()->previous() }}"
            class="inline-flex items-center gap-2 bg-teal-100 hover:bg-teal-200 text-teal-700 font-medium py-2 px-4 rounded-md text-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <div class="max-w-5xl mx-auto">
            <br>
            <h2 class="text-2xl font-bold mb-6">🛍️ Keranjang Belanja</h2>

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
                        <img src="{{ asset('images/terpal-ayam.png') }}" alt="Gambar Produk"
                            class="w-20 h-20 object-cover rounded-md">

                        <div class="flex-1">
                            <p class="font-semibold">
                                @if ($item->variant)
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
                                <p class="text-sm text-gray-500">Variasi: {{ $item->variant->color ?? '-' }}</p>
                            @endif
                        </div>

                        <div class="text-right">
                            <p class="font-semibold text-gray-700">
                                @if ($item->variant)
                                    Rp
                                    {{ number_format($item->variant->product->price * $item->quantity, 0, ',', '.') }}
                                @else
                                    Rp {{ number_format($item->harga_custom ?? 0, 0, ',', '.') }}
                                @endif
                            </p>

                            <a href="{{ route('keranjang.delete', $item->id) }}"
                                class="text-red-500 text-xs mt-2 hover:underline">Hapus
                            </a>
                        </div>
                    </div>
                @endforeach

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

    {{-- JavaScript Checkbox All --}}
    <script>
        document.getElementById('selectAll').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>

</body>

</html>

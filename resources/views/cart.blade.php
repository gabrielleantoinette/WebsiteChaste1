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

    <section class="px-6 md:px-20 py-16 min-h-screen">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-2xl font-bold mb-6">🛍️ Keranjang Belanja</h2>

            <form action="" method="POST" class="space-y-6">
                @csrf
                <div class="flex items-center mb-4">
                    <input type="checkbox" id="selectAll" class="mr-2">
                    <label for="selectAll" class="text-sm">Pilih Semua</label>
                </div>

                @foreach ($cartItems as $item)
                    <div class="flex items-center gap-4 border-b pb-6">
                        <input type="checkbox" class="item-checkbox">
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
                    <a href="{{ route('checkout') }}"
                        class="bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 px-6 rounded-md">
                        Lanjut Bayar
                    </a>
                </div>
            </form>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-[#D9F2F2] py-10 px-6 md:px-20 text-sm text-gray-700 mt-20">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">CHASTE</h3>
                <p class="mb-4 max-w-xs">Kami membantu anda menyediakan terpal terbaik.</p>
            </div>
            <div class="space-y-2">
                <h4 class="font-semibold text-gray-800 mb-2">Informasi</h4>
                <a href="#" class="block hover:text-black">Tentang</a>
                <a href="#" class="block hover:text-black">Produk</a>
            </div>
            <div class="space-y-2">
                <h4 class="font-semibold text-gray-800 mb-2">Kontak Kami</h4>
                <p>Telp: 089123231221</p>
                <p>E-mail: xyz@bca</p>
            </div>
        </div>
        <div class="text-center text-xs text-gray-500 mt-8">
            © 2025 Hak Cipta Dilindungi
        </div>
    </footer>

    {{-- JavaScript Checkbox All --}}
    <script>
        document.getElementById('selectAll').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>

</body>

</html>

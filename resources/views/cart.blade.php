<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja | CHASTE</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-white font-sans text-gray-800">
    {{-- Header --}}
    @include('layouts.customer-nav')

    <!-- Keranjang -->
    <section class="px-[100px] h-screen">
        <h2 class="text-xl font-semibold mb-6 bg-[#D9F2F2] inline-block px-4 py-2 rounded-md">🛍️ Keranjang Belanja</h2>

        <!-- Pilih Semua -->
        <div class="mb-4 flex items-center gap-2 border border-gray-300 px-4 py-3 rounded-md w-full text-sm">
            <input type="checkbox" id="checkAll">
            <label for="checkAll" class="cursor-pointer">Pilih Semua</label>
        </div>

        <!-- List Produk -->
        <div class="space-y-8">
            @foreach ($cart as $item)
                <!-- Item Produk -->
                <div class="flex items-center gap-4 border-b pb-6">
                    <input type="checkbox" class="item-checkbox">
                    <img src="{{ asset('images/terpal-ayam.png') }}" class="w-20 h-20 object-cover rounded border">
                    <div class="flex-1">
                        <p class="font-semibold">{{ $item->variant->product->name }}</p>
                        <p class="text-sm text-gray-500">Variasi: {{ $item->variant->color }}</p>
                    </div>
                    <p class="w-28 text-sm text-gray-700">Rp
                        {{ number_format($item->variant->product->price) }}</p>

                    <!-- Qty -->
                    <div class="flex items-center border border-gray-300 w-max rounded-md overflow-hidden">
                        <button type="button" class="px-3 py-1 text-lg hover:bg-gray-100">-</button>
                        <input type="number" value="{{ $item->quantity }}" min="1"
                            class="w-10 text-center border-l border-r border-gray-300 outline-none text-sm py-1 appearance-none">
                        <button type="button" class="px-3 py-1 text-lg hover:bg-gray-100">+</button>
                    </div>

                    <p class="w-28 text-sm font-semibold text-red-500 text-right">Rp
                        {{ number_format($item->variant->product->price * $item->quantity) }}</p>
                    <form method="post" action="{{ route('keranjang.delete', $item->id) }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-500 hover:underline">Hapus</button>
                    </form>
                </div>
        </div>
        @endforeach
        </div>
        <div class="flex justify-end mt-8">
            <a href="{{ route('checkout') }}"
                class="bg-[#D9F2F2] hover:bg-teal-200 text-gray-800 font-semibold px-6 py-3 rounded-md transition">
                Lanjut Bayar
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#D9F2F2] py-10 px-6 md:px-20 text-sm text-gray-700 mt-20">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Brand -->
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">CHASTE</h3>
                <p class="mb-4 max-w-xs">Kami membantu anda menyediakan terpal terbaik.</p>
                <div class="flex space-x-4 text-lg">
                    <a href="#">📷</a>
                    <a href="#">🐦</a>
                    <a href="#">📘</a>
                </div>
            </div>

            <!-- Informasi -->
            <div class="space-y-2">
                <h4 class="font-semibold text-gray-800 mb-2">Informasi</h4>
                <a href="#" class="block hover:text-black">Tentang</a>
                <a href="#" class="block hover:text-black">Produk</a>
            </div>

            <!-- Kontak -->
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
    <script>
        const checkAll = document.getElementById('checkAll');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');

        checkAll.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => {
                cb.checked = checkAll.checked;
            });
        });
    </script>

</body>

</html>

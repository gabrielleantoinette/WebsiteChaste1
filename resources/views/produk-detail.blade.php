<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk | CHASTE</title>
    @vite('resources/css/app.css')
</head>

<script>
    function changeQty(amount) {
        const input = document.getElementById('qtyInput');
        let current = parseInt(input.value);
        const min = parseInt(input.min) || 1;

        if (!isNaN(current)) {
            let newVal = current + amount;
            if (newVal < min) newVal = min;
            input.value = newVal;
        }
    }
    let index = 0;

    function slide(direction) {
        const slider = document.getElementById('slider');
        const slides = slider.children.length;
        index += direction;

        if (index < 0) index = slides - 1;
        if (index >= slides) index = 0;

        slider.style.transform = `translateX(-${index * 100}%)`;
    }
</script>

<style>
    /* Hilangkan spinner untuk input type number di semua browser */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
        /* Firefox */
    }
</style>


<body class="bg-white font-sans text-gray-800">
    <div class="px-[100px] h-screen">
        <!-- Header -->
        <header class="flex items-center justify-between py-5 border-gray-200">
            <div class="text-2xl font-bold tracking-wide">CHASTE</div>
            <nav class="hidden md:flex space-x-8 text-sm font-medium text-gray-600">
                <a href="{{ url('/') }}" class="hover:text-teal-500 transition">Beranda</a>
                <a href="{{ route('produk') }}" class="hover:text-teal-500 transition">Produk</a>
                <a href="#" class="hover:text-teal-500 transition">Kontak</a>
            </nav>
            <div class="space-x-4 text-xl text-gray-700 flex items-center gap-4">
                <a href="{{ route('keranjang') }}" class="hover:text-teal-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>
                </a>
                <a href="{{ route('login') }}" class="hover:text-teal-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </a>
                <span>|</span>
                <a href="{{ route('login') }}" class="hover:text-teal-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5M12 17.25h8.25" />
                    </svg>
                </a>
            </div>

        </header>

        <!-- Detail Produk -->
        <section class="py-16 bg-white">
            <div class="flex flex-col md:flex-row gap-10">
                <!-- Gambar Produk -->
                <div class="relative w-full overflow-hidden rounded-xl border border-gray-200 max-w-1/2">
                    <div id="slider" class="flex transition-transform duration-300 ease-in-out w-[300%]">
                        <img src="{{ asset('images/terpal-ayam.png') }}" class="w-full object-cover h-[400px]">
                        <img src="{{ asset('images/terpal-gajah.png') }}" class="w-full object-cover h-[400px]">
                        <img src="{{ asset('images/terpal-lumba.png') }}" class="w-full object-cover h-[400px]">
                    </div>

                    <!-- Panah -->
                    <button onclick="slide(-1)"
                        class="absolute left-4 top-1/2 -translate-y-1/2 bg-white p-2 rounded-full shadow text-xl z-10">❮</button>
                    <button onclick="slide(1)"
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-white p-2 rounded-full shadow text-xl z-10">❯</button>
                </div>


                <!-- Info Produk -->
                <div class="w-full md:w-1/2 space-y-6">
                    <div>
                        <h2 class="text-2xl font-bold">{{ $product->name }}</h2>
                        <p class="text-xl text-teal-600 font-semibold mt-1">Rp {{ number_format($product->price) }}</p>
                        <p class="text-sm text-gray-600 mt-2">{{ $product->description }}</p>
                    </div>

                    <!-- Pilih Warna -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Pilihan Warna</label>
                        <select class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-teal-300">
                            @foreach ($variants as $variant)
                                <option>{{ $variant->color }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jumlah -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Jumlah</label>
                        <div class="flex items-center border border-gray-300 w-max rounded-md overflow-hidden">
                            <button type="button" class="px-3 py-2 text-lg hover:bg-gray-100"
                                onclick="changeQty(-1)">-</button>

                            <input type="number" id="qtyInput" value="1" min="1"
                                class="w-12 text-center border-l border-r border-gray-300 outline-none text-sm py-2">

                            <button type="button" class="px-3 py-2 text-lg hover:bg-gray-100"
                                onclick="changeQty(1)">+</button>
                        </div>

                    </div>

                    <!-- Tombol -->
                    <button
                        class="w-full bg-[#D9F2F2] hover:bg-teal-200 text-gray-800 font-semibold py-3 rounded-md transition">
                        Tambah ke Keranjang
                    </button>
                </div>
            </div>
        </section>


    </div>
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
</body>

</html>

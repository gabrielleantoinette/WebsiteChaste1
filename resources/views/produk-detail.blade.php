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
    <!-- Header -->
    @include('layouts.customer-nav')
    <div class="px-[100px] h-screen">
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
                <form method="POST" class="w-full md:w-1/2 space-y-6">
                    @csrf
                    <div>
                        <h2 class="text-2xl font-bold">{{ $product->name }}</h2>
                        <p class="text-xl text-teal-600 font-semibold mt-1">Rp {{ number_format($product->price) }}
                        </p>
                        <p class="text-sm text-gray-600 mt-2">{{ $product->description }}</p>
                    </div>

                    <!-- Pilih Warna -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Pilihan Warna</label>
                        <select class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-teal-300"
                            name="variant_id">
                            @foreach ($variants as $variant)
                                <option value="{{ $variant->id }}">{{ $variant->color }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jumlah -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Jumlah</label>
                        <div class="flex items-center border border-gray-300 w-max rounded-md overflow-hidden">
                            <button type="button" class="px-3 py-2 text-lg hover:bg-gray-100"
                                onclick="changeQty(-1)">-</button>

                            <input type="number" id="qtyInput" value="1" min="1" name="quantity"
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
                    <a href="{{ route('produk.negosiasi', ['id' => $product->id]) }}"
                    class="block text-center bg-gray-300 hover:bg-gray-400 text-white font-semibold py-3 rounded-md transition">
                        Tawar Harga
                    </a>

                </form>
            </div>
        </section>


    </div>

    @include('layouts.footer')
    
</body>

</html>

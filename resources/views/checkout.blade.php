<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout | CHASTE</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen py-10">
    @include('layouts.customer-nav')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <a href="{{ url()->previous() }}"
            class="inline-flex items-center gap-2 bg-teal-100 hover:bg-teal-200 text-teal-700 font-medium py-2 px-4 rounded-md text-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold mb-6 text-center">Pengiriman & Pembayaran</h1>

        <form action="{{ route('checkout.invoice') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Section Alamat -->
            <section class="border p-4 rounded mb-6">
                <h2 class="font-semibold text-lg mb-2">Alamat Pengiriman</h2>
                <textarea name="address" id="address" rows="3" class="w-full border rounded p-2" required>{{ old('address', $alamat_default_user ?? '') }}</textarea>
            </section>

            <!-- Section Pesanan -->
            <section class="border p-4 rounded mb-6">
                <h2 class="font-semibold text-lg mb-2">Pesanan</h2>
                <div class="divide-y divide-gray-200">
                    @php $no = 1; @endphp

                    @foreach ($produkItems as $item)
                        <div class="flex justify-between items-center py-4">
                            <div>
                                <p class="font-semibold">{{ $no++ }}. {{ $item->product_name }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $item->quantity }} item - Warna: {{ $item->variant_color ?? '-' }}
                                </p>
                            </div>
                            <p class="font-semibold">
                                Rp
                                {{ number_format(($item->product_price ?? 0) * ($item->quantity ?? 1), 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach

                    @foreach ($customItems as $item)
                        <div class="flex justify-between items-center py-4">
                            <div>
                                <p class="font-semibold">{{ $no++ }}. Custom Terpal:
                                    {{ $item->kebutuhan_custom }}</p> <!-- Nomor urut -->
                                <p class="text-sm text-gray-600">
                                    Ukuran: {{ $item->ukuran_custom ?? '-' }},
                                    Warna: {{ $item->warna_custom ?? '-' }},
                                    Jumlah Ring: {{ $item->jumlah_ring_custom ?? '-' }},
                                    Tali: {{ $item->pakai_tali_custom ?? '-' }}
                                </p>
                            </div>
                            <p class="font-semibold">
                                Rp
                                {{ number_format(($item->harga_custom ?? 0) * ($item->quantity ?? 1), 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </section>


            <!-- Section Pilihan Pengiriman -->
            <section class="border p-4 rounded mb-6">
                <h2 class="font-semibold text-lg mb-2">Pilihan Pengiriman</h2>

                <div>
                    <label class="flex items-center space-x-2 mb-2">
                        <input type="radio" name="shipping_method" value="kurir" class="accent-teal-600" checked
                            onclick="updateShippingCost(0)">
                        <span>Kurir Perusahaan (khusus Surabaya Gratis)</span>
                    </label>

                    <label class="flex items-center space-x-2">
                        <input type="radio" name="shipping_method" value="expedition" class="accent-teal-600"
                            onclick="updateShippingCost(19000)">
                        <span>Ekspedisi (Rp 19.000)</span>
                    </label>
                </div>
            </section>

            <!-- Section Rincian Total Bayar -->
            <section class="border p-4 rounded mb-6">
                <h2 class="font-semibold text-lg mb-2">Rincian Total Bayar</h2>

                <div class="flex justify-between mb-1">
                    <span>Subtotal Produk</span>
                    <span id="productSubtotal">Rp {{ number_format($subtotalProduk, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between mb-1">
                    <span>Subtotal Pengiriman</span>
                    <span id="shippingCost">Rp 0</span>
                </div>

                <div class="flex justify-between font-bold border-t pt-2">
                    <span>Total Pembayaran</span>
                    <span id="totalCost">Rp {{ number_format($subtotalProduk, 0, ',', '.') }}</span>
                </div>

                <input type="hidden" id="productSubtotalHidden" value="{{ $subtotalProduk }}">
            </section>


            <!-- Section Metode Pembayaran -->
            <section class="border p-4 rounded mb-6">
                <h2 class="font-semibold text-lg mb-2">Metode Pembayaran</h2>
                <div class="space-y-2 ">
                    <div>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="payment_method" value="transfer" class="accent-teal-600"
                                required onchange="showPaymentInfo()">
                            <span>Transfer Bank</span>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="payment_method" value="midtrans" class="accent-teal-600"
                                onchange="showPaymentInfo()">
                            <span>E-Wallet (OVO, DANA, ShopeePay)</span>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="payment_method" value="cod" class="accent-teal-600"
                                onchange="showPaymentInfo()">
                            <span>COD (Bayar di Tempat)</span>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="payment_method" value="hutang" class="accent-teal-600"
                                onchange="showPaymentInfo()" id="hutang" {{ !$bolehHutang ? 'disabled' : '' }}>
                            <span>Bayar Nanti (Hutang)</span>
                        </label>
                        @if(!$bolehHutang)
                            <span class="text-xs text-red-500 ml-2">Minimal 1x transaksi lunas untuk bisa hutang</span>
                        @endif
                    </div>
                </div>

                <!-- DIV KETERANGAN -->
                <div id="paymentInfo" class="mt-4 hidden bg-teal-50 p-4 rounded text-sm text-gray-700">
                    <div>
                        <strong>Transfer ke:</strong><br>
                        Bank BCA - 1234567890<br>
                        a.n PT. Chaste Gemilang Mandiri<br>
                        <em>(Dicek Manual)</em>
                    </div>
                    <div id="uploadBuktiTransfer" class="mt-4">
                        <label class="block mb-2 font-medium">Upload Bukti Transfer <span class="text-red-500">*</span></label>
                        <input type="file" name="bukti_transfer" id="bukti_transfer" accept="image/*" class="block w-full border rounded p-2">
                        <span id="buktiError" class="text-xs text-red-500 hidden">Bukti transfer wajib diupload.</span>
                    </div>
                </div>
            </section>


            <!-- Tombol Bayar -->
            <div class="text-center">
                <!-- kirim semua cart id produk biasa -->
                @foreach ($produkItems as $item)
                    <input type="hidden" name="cart_ids[]" value="{{ $item->id }}">
                @endforeach

                <!-- kirim semua cart id produk custom -->
                @foreach ($customItems as $item)
                    <input type="hidden" name="cart_ids[]" value="{{ $item->id }}">
                @endforeach

                {{-- shipping cost --}}
                <input type="hidden" id="shippingCostValue" name="shipping_cost">

                @if (!empty($disableCheckout) && $disableCheckout)
                    <div class="mb-4 text-red-600 font-semibold">Checkout dinonaktifkan karena hutang melebihi Rp 10.000.000 atau ada hutang jatuh tempo yang belum dilunasi.</div>
                @endif
                <!-- Tombol Bayar -->
                <button id="btnBayar" type="submit"
                    class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded @if(!empty($disableCheckout) && $disableCheckout) opacity-50 cursor-not-allowed @endif"
                    @if(!empty($disableCheckout) && $disableCheckout) disabled @endif>
                    Bayar
                </button>
            </div>
        </form>
    </div>

    @include('layouts.footer')

    <script>
        function updateShippingCost(cost) {
            document.getElementById('shippingCost').innerText = formatRupiah(cost);
            document.getElementById('shippingCostValue').value = cost;
            updateTotal(cost);
        }

        function updateTotal(shippingCost) {
            var productSubtotal = parseInt(document.getElementById('productSubtotalHidden').value);
            var total = productSubtotal + shippingCost;
            document.getElementById('totalCost').innerText = formatRupiah(total);
        }

        function formatRupiah(amount) {
            return "Rp " + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function showPaymentInfo() {
            const paymentInfo = document.getElementById('paymentInfo');
            const selected = document.querySelector('input[name="payment_method"]:checked');
            const uploadBukti = document.getElementById('uploadBuktiTransfer');
            if (!selected) {
                paymentInfo.classList.add('hidden');
                paymentInfo.innerHTML = '';
                return;
            }
            if (selected.value === 'transfer') {
                paymentInfo.innerHTML = `
                <div>
                    <strong>Transfer ke:</strong><br>
                    Bank BCA - 1234567890<br>
                    a.n PT. Chaste Gemilang Mandiri<br>
                    <em>(Dicek Manual)</em>
                </div>
                <div id="uploadBuktiTransfer" class="mt-4">
                    <label class="block mb-2 font-medium">Upload Bukti Transfer <span class="text-red-500">*</span></label>
                    <input type="file" name="bukti_transfer" id="bukti_transfer" accept="image/*" class="block w-full border rounded p-2">
                    <span id="buktiError" class="text-xs text-red-500 hidden">Bukti transfer wajib diupload.</span>
                </div>
            `;
                paymentInfo.classList.remove('hidden');
                setTimeout(() => { // tunggu render
                    const btnBayar = document.getElementById('btnBayar');
                    const buktiInput = document.getElementById('bukti_transfer');
                    btnBayar.disabled = true;
                    buktiInput.addEventListener('change', function() {
                        if (buktiInput.files.length > 0) {
                            btnBayar.disabled = false;
                            document.getElementById('buktiError').classList.add('hidden');
                        } else {
                            btnBayar.disabled = true;
                        }
                    });
                }, 100);
            } else {
                paymentInfo.classList.add('hidden');
                paymentInfo.innerHTML = '';
                document.getElementById('btnBayar').disabled = false;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const hutangRadio = document.getElementById('hutang');
            const btnBayar = document.getElementById('btnBayar');
            if (hutangRadio && hutangRadio.disabled) {
                hutangRadio.addEventListener('click', function(e) {
                    e.preventDefault();
                });
            }
            // Disable button jika hutang dipilih tapi tidak boleh
            document.querySelectorAll('input[name=payment_method]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    if (hutangRadio && hutangRadio.checked && hutangRadio.disabled) {
                        btnBayar.disabled = true;
                    } else {
                        btnBayar.disabled = false;
                    }
                });
            });
            // Inisialisasi: jika hutang terpilih dan disabled, button disable
            if (hutangRadio && hutangRadio.checked && hutangRadio.disabled) {
                btnBayar.disabled = true;
            }
        });

        // Validasi sebelum submit
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const selected = document.querySelector('input[name="payment_method"]:checked');
            if (selected && selected.value === 'transfer') {
                const buktiInput = document.getElementById('bukti_transfer');
                if (!buktiInput || buktiInput.files.length === 0) {
                    e.preventDefault();
                    document.getElementById('buktiError').classList.remove('hidden');
                    buktiInput.scrollIntoView({behavior: 'smooth', block: 'center'});
                }
            }
        });
    </script>
</body>

</html>

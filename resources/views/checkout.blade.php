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
        <div class="mb-6">
            <x-breadcrumb :items="[
                ['label' => 'Keranjang', 'url' => route('keranjang')],
                ['label' => 'Checkout']
            ]" />
            <h1 class="text-2xl font-bold text-gray-800">Pengiriman & Pembayaran</h1>
        </div>

        <form action="{{ route('checkout.invoice') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Section Alamat -->
            <section class="border border-gray-200 rounded-lg p-6 mb-6 bg-white shadow-sm">
                <h2 class="font-bold text-xl mb-4 text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Alamat Pengiriman
                </h2>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <textarea name="address" id="address" rows="3" 
                        class="w-full border-0 bg-transparent resize-none focus:outline-none focus:ring-0 text-gray-700" 
                        placeholder="Masukkan alamat pengiriman lengkap (contoh: Jl. Contoh No. 123, Surabaya, Jawa Timur 60264)..." 
                        required
                        oninput="checkShippingOptions()"
                        onblur="checkShippingOptions()">{{ old('address', $alamat_default_user ?? '') }}</textarea>
                    <p class="text-xs text-gray-500 mt-2">Pastikan mencantumkan nama kota/kabupaten untuk perhitungan ongkir otomatis</p>
                </div>
            </section>

            <!-- Section Pesanan -->
            <section class="border border-gray-200 rounded-lg p-6 mb-6 bg-white shadow-sm">
                <h2 class="font-bold text-xl mb-4 text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Detail Pesanan
                </h2>
                
                <div class="space-y-4">
                    @php $no = 1; @endphp

                    {{-- Debug: Tampilkan jumlah items --}}
                    @if(count($produkItems) == 0 && count($customItems) == 0)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center text-red-600">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-semibold">Tidak ada item dalam keranjang</span>
                            </div>
                            <div class="text-sm text-red-600 mt-1">
                                Produk Items: {{ count($produkItems) }}, Custom Items: {{ count($customItems) }}
                            </div>
                        </div>
                    @endif

                    @foreach ($produkItems as $item)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <span class="bg-teal-100 text-teal-800 text-xs font-semibold px-2 py-1 rounded-full mr-3">
                                            #{{ $no++ }}
                                        </span>
                                        <h3 class="font-bold text-gray-800">{{ $item->product_name }}</h3>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 mb-3">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            <span class="font-medium">Warna:</span>
                                            <span class="ml-1">{{ $item->variant_color ?? 'Standard' }}</span>
                                        </div>
                                        @if($item->selected_size)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                                </svg>
                                                <span class="font-medium">Ukuran:</span>
                                                <span class="ml-1">{{ $item->selected_size }}</span>
                                            </div>
                                        @endif
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <span class="font-medium">Qty:</span>
                                            <span class="ml-1">{{ $item->quantity }} pcs</span>
                                        </div>
                                    </div>
                                    
                                    @if($item->harga_custom && str_contains($item->kebutuhan_custom ?? '', 'Hasil negosiasi'))
                                        <div class="bg-teal-50 border border-teal-200 rounded-md p-2 mb-2">
                                            <div class="flex items-center text-teal-700 text-sm">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="font-medium">{{ $item->kebutuhan_custom }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="text-right ml-4">
                                    @php
                                        $itemPrice = $item->calculated_price ?? $item->harga_custom ?? $item->product_price ?? 0;
                                        $itemTotal = $itemPrice * ($item->quantity ?? 1);
                                    @endphp
                                    <div class="text-lg font-bold text-teal-600">
                                        Rp {{ number_format($itemTotal, 0, ',', '.') }}
                                    </div>
                                    @if($item->harga_custom && str_contains($item->kebutuhan_custom ?? '', 'Hasil negosiasi'))
                                        <div class="text-xs text-gray-500">
                                            @ {{ number_format($item->harga_custom, 0, ',', '.') }}/pcs
                                        </div>
                                    @elseif(!$item->harga_custom)
                                        <div class="text-xs text-gray-500">
                                            @ {{ number_format($itemPrice, 0, ',', '.') }}/pcs
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @foreach ($customItems as $item)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-1 rounded-full mr-3">
                                            #{{ $no++ }}
                                        </span>
                                        <h3 class="font-bold text-gray-800">Custom Terpal</h3>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 mb-3">
                                        @if($item->ukuran_custom)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                                </svg>
                                                <span class="font-medium">Ukuran:</span>
                                                <span class="ml-1">{{ $item->ukuran_custom }}</span>
                                            </div>
                                        @endif
                                        @if($item->warna_custom)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                                <span class="font-medium">Warna:</span>
                                                <span class="ml-1">{{ $item->warna_custom }}</span>
                                            </div>
                                        @endif
                                        @if($item->jumlah_ring_custom)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="font-medium">Ring:</span>
                                                <span class="ml-1">{{ $item->jumlah_ring_custom }}</span>
                                            </div>
                                        @endif
                                        @if($item->pakai_tali_custom)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                                <span class="font-medium">Tali:</span>
                                                <span class="ml-1">{{ $item->pakai_tali_custom }}</span>
                                            </div>
                                        @endif
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <span class="font-medium">Qty:</span>
                                            <span class="ml-1">{{ $item->quantity }} pcs</span>
                                        </div>
                                    </div>
                                    
                                    @if(str_contains($item->kebutuhan_custom ?? '', 'Hasil negosiasi'))
                                        <div class="bg-teal-50 border border-teal-200 rounded-md p-2 mb-2">
                                            <div class="flex items-center text-teal-700 text-sm">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="font-medium">{{ $item->kebutuhan_custom }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="text-right ml-4">
                                    <div class="text-lg font-bold text-purple-600">
                                        Rp {{ number_format(($item->harga_custom ?? 0) * ($item->quantity ?? 1), 0, ',', '.') }}
                                    </div>
                                    @if(str_contains($item->kebutuhan_custom ?? '', 'Hasil negosiasi'))
                                        <div class="text-xs text-gray-500">
                                            @ {{ number_format($item->harga_custom ?? 0, 0, ',', '.') }}/pcs
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>


            <!-- Section Pilihan Pengiriman -->
            <section class="border border-gray-200 rounded-lg p-6 mb-6 bg-white shadow-sm">
                <h2 class="font-bold text-xl mb-4 text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Pilihan Pengiriman
                </h2>

                <div class="space-y-3" id="shippingOptions">
                    {{-- Opsi Kurir Perusahaan --}}
                    <label id="kurirOption" class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition cursor-pointer {{ $isFromSurabaya ? '' : 'hidden' }}">
                        <input type="radio" name="shipping_method" value="kurir" class="accent-teal-600 mr-3" {{ $isFromSurabaya ? 'checked' : '' }}
                            data-cost="0" data-courier="kurir" data-service="Kurir Perusahaan"
                            onclick="updateShippingCost(0, 'kurir', 'Kurir Perusahaan')">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-800">Kurir Perusahaan</div>
                                <div class="text-sm text-gray-600">Khusus Surabaya Gratis</div>
                            </div>
                            <div class="text-teal-600 font-semibold">Gratis</div>
                        </label>

                    {{-- Pesan Kurir Tidak Tersedia --}}
                    <div id="kurirNotAvailable" class="p-4 bg-red-50 border border-red-200 rounded-lg {{ $isFromSurabaya ? 'hidden' : '' }}">
                            <div class="flex items-center text-red-600">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-semibold">Kurir Perusahaan Tidak Tersedia</span>
                            </div>
                            <div class="text-sm text-red-600 mt-1">
                                Kurir perusahaan hanya melayani pengiriman di Surabaya. Silakan pilih ekspedisi untuk pengiriman ke luar Surabaya.
                            </div>
                        </div>

                    {{-- Loading State --}}
                    <div id="shippingLoading" class="hidden p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center text-blue-600">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Menghitung ongkir...</span>
                        </div>
                    </div>

                    {{-- Error Message --}}
                    <div id="shippingError" class="hidden p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center text-red-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span id="shippingErrorMessage"></span>
                        </div>
                            </div>

                    {{-- Container untuk opsi ekspedisi dari Biteship --}}
                    <div id="shippingOptionsList"></div>
                </div>
            </section>

            <!-- Section Rincian Total Bayar -->
            <section class="border border-gray-200 rounded-lg p-6 mb-6 bg-white shadow-sm">
                <h2 class="font-bold text-xl mb-4 text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Rincian Total Bayar
                </h2>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Subtotal Produk</span>
                        <span class="font-semibold text-gray-800" id="productSubtotal">Rp {{ number_format($subtotalProduk, 0, ',', '.') }}</span>
                </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Subtotal Pengiriman</span>
                        <span class="font-semibold text-gray-800" id="shippingCost">Rp 0</span>
                </div>

                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-800">Total Pembayaran</span>
                            <span class="text-xl font-bold text-teal-600" id="totalCost">Rp {{ number_format($subtotalProduk, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="productSubtotalHidden" value="{{ $subtotalProduk }}">
                <input type="hidden" id="shippingCostValue" name="shipping_cost" value="0">
                <input type="hidden" id="shippingMethodValue" name="shipping_method_value" value="">
                <input type="hidden" id="shippingCourierValue" name="shipping_courier" value="">
                <input type="hidden" id="shippingServiceValue" name="shipping_service" value="">
                <input type="hidden" id="shippingServiceCodeValue" name="shipping_service_code" value="">
            </section>

            <!-- Section Metode Pembayaran -->
            <section class="border border-gray-200 rounded-lg p-6 mb-6 bg-white shadow-sm">
                <h2 class="font-bold text-xl mb-4 text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Metode Pembayaran
                </h2>
                
                <div class="space-y-3">
                    <label class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition cursor-pointer">
                        <input type="radio" name="payment_method" value="transfer" class="accent-teal-600 mr-3"
                                required onchange="showPaymentInfo()">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">Transfer Bank</div>
                            <div class="text-sm text-gray-600">Bank BCA</div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        </label>

                    <label class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition cursor-pointer">
                        <input type="radio" name="payment_method" value="midtrans" class="accent-teal-600 mr-3"
                            onchange="showPaymentInfo()">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">E-Wallet</div>
                            <div class="text-sm text-gray-600">Virtual Account, Kartu Kredit, OVO, DANA, ShopeePay</div>
                    </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </label>

                    <label class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition cursor-pointer">
                        <input type="radio" name="payment_method" value="dp_midtrans" class="accent-teal-600 mr-3"
                            onchange="showPaymentInfo()">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">DP 50% (Midtrans)</div>
                            <div class="text-sm text-gray-600">Bayar 50% via Midtrans, sisanya dibayar ke driver saat kirim</div>
                    </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </label>

                    @php
                        $codDisabled = !$isFromSurabaya;
                        $codDisabledReason = '';
                        if (!$isFromSurabaya) {
                            $codDisabledReason = 'COD hanya tersedia untuk pengiriman di Surabaya. Silakan pilih metode pembayaran lain.';
                        }
                    @endphp
                    
                    <label id="codLabel" class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-100 transition {{ $codDisabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 cursor-pointer' }}">
                        <input type="radio" name="payment_method" value="cod" id="codRadio" class="accent-teal-600 mr-3"
                                onchange="showPaymentInfo()" {{ $codDisabled ? 'disabled' : '' }}>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">COD</div>
                            <div class="text-sm text-gray-600">Bayar di Tempat</div>
                            @if($codDisabled)
                                <div class="text-xs text-red-500 mt-1">{{ $codDisabledReason }}</div>
                            @endif
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </label>

                    <label class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition cursor-pointer {{ !$bolehHutang || $melebihiLimit ? 'opacity-50 cursor-not-allowed' : '' }}">
                        <input type="radio" name="payment_method" value="hutang" class="accent-teal-600 mr-3"
                                onchange="showPaymentInfo()" id="hutang" 
                                {{ !$bolehHutang || $melebihiLimit ? 'disabled' : '' }}>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">Bayar Nanti</div>
                            <div class="text-sm text-gray-600">Hutang (Limit Rp 10.000.000)</div>
                        @if(!$bolehHutang)
                                <div class="text-xs text-red-500 mt-1">Minimal 1x transaksi lunas untuk bisa hutang</div>
                        @elseif($melebihiLimit)
                                <div class="text-xs text-red-500 mt-1">Total hutang akan melebihi limit</div>
                        @endif
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </label>
                        
                        @if($bolehHutang && !$melebihiLimit)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-800">
                            <div class="font-medium mb-2">Info Hutang:</div>
                            <div class="grid grid-cols-3 gap-2 text-xs">
                                <div>Hutang saat ini: <span class="font-semibold">Rp {{ number_format($totalHutangAktif, 0, ',', '.') }}</span></div>
                                <div>Limit hutang: <span class="font-semibold">Rp {{ number_format($limitHutang, 0, ',', '.') }}</span></div>
                                <div>Sisa limit: <span class="font-semibold text-green-600">Rp {{ number_format($limitHutang - $totalHutangAktif, 0, ',', '.') }}</span></div>
                            </div>
                            </div>
                        @endif
                </div>

                <!-- DIV KETERANGAN -->
                <div id="paymentInfo" class="mt-4 hidden bg-teal-50 border border-teal-200 rounded-lg p-4 text-sm text-gray-700">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 text-teal-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800 mb-2">Transfer ke:</div>
                            <div class="bg-white rounded p-3 border border-gray-200">
                                <div class="font-medium">Bank BCA - 1234567890</div>
                                <div class="text-sm text-gray-600">a.n PT. Chaste Gemilang Mandiri</div>
                                <div class="text-xs text-gray-500 italic">(Dicek Manual)</div>
                            </div>
                        </div>
                    </div>
                    <div id="uploadBuktiTransfer" class="mt-4">
                        <label class="block mb-2 font-medium text-gray-700">Upload Bukti Transfer <span class="text-red-500">*</span></label>
                        <input type="file" name="bukti_transfer" id="bukti_transfer" accept="image/*" 
                            class="block w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-teal-400 focus:border-teal-400">
                        <span id="buktiError" class="text-xs text-red-500 hidden">Bukti transfer wajib diupload.</span>
                    </div>
                </div>
            </section>


            <!-- Tombol Bayar -->
            <div class="text-center mt-8">
                <!-- kirim semua cart id produk biasa -->
                @foreach ($produkItems as $item)
                    <input type="hidden" name="cart_ids[]" value="{{ $item->id }}">
                @endforeach

                <!-- kirim semua cart id produk custom -->
                @foreach ($customItems as $item)
                    <input type="hidden" name="cart_ids[]" value="{{ $item->id }}">
                @endforeach

                @if (!empty($disableCheckout) && $disableCheckout)
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center text-red-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold">Checkout Dinonaktifkan</span>
                        </div>
                        <div class="text-sm text-red-600 mt-1">
                            Hutang melebihi Rp 10.000.000 atau ada hutang jatuh tempo yang belum dilunasi.
                        </div>
                    </div>
                @endif
                
                <!-- Tombol Bayar -->
                <button id="btnBayar" type="submit"
                    class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-4 px-6 rounded-lg text-lg shadow-lg transform transition duration-200 hover:scale-105 @if(!empty($disableCheckout) && $disableCheckout) opacity-50 cursor-not-allowed @endif"
                    @if(!empty($disableCheckout) && $disableCheckout) disabled @endif>
                    <div class="flex items-center justify-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Bayar Sekarang
                    </div>
                </button>
            </div>
        </form>
    </div>

    @include('layouts.footer')

    <script>
        // Hitung berat total (estimasi: minimal 1000 gram untuk menghindari error)
        function calculateTotalWeight() {
            const totalItems = {{ count($produkItems) + count($customItems) }};
            // Estimasi berat: 1000 gram per item (minimal 1 kg untuk menghindari error API)
            const weight = totalItems * 1000;
            // Pastikan minimal 1000 gram
            return Math.max(weight, 1000);
        }

        // Extract city name from address
        function extractCityFromAddress(address) {
            if (!address || address.trim().length === 0) {
                return null;
            }
            
            // Coba ekstrak kota dari alamat
            // Format umum: "Alamat, Kecamatan, Kota/Kabupaten, Provinsi"
            const parts = address.split(',');
            
            // Cari bagian yang mengandung "Kabupaten" atau "Kota"
            for (let i = parts.length - 1; i >= 0; i--) {
                const part = parts[i].trim();
                const partLower = part.toLowerCase();
                
                // Jika mengandung "Kabupaten" atau "Kota", ekstrak nama kotanya
                if (partLower.includes('kabupaten') || partLower.includes('kota')) {
                    // Hapus kata "Kabupaten" atau "Kota" dan ambil nama kotanya
                    let cityName = part.replace(/kabupaten\s*/i, '').replace(/kota\s*/i, '').trim();
                    if (cityName) {
                        return cityName;
                    }
                }
            }
            
            // Jika tidak ada "Kabupaten" atau "Kota", coba cari di bagian "Kec."
            for (let i = parts.length - 1; i >= 0; i--) {
                const part = parts[i].trim();
                const partLower = part.toLowerCase();
                
                // Jika mengandung "Kec." atau "Kecamatan", ambil bagian setelahnya atau bagian sebelumnya
                if (partLower.includes('kec.') || partLower.includes('kecamatan')) {
                    // Coba ambil bagian setelah "Kec."
                    let cityName = part.replace(/kec\.?\s*/i, '').replace(/kecamatan\s*/i, '').trim();
                    if (cityName && !cityName.match(/^\d+$/)) { // Pastikan bukan hanya angka
                        return cityName;
                    }
                }
            }
            
            // Jika tidak ada koma, coba cari kata kota umum
            const cityKeywords = ['jakarta', 'bandung', 'surabaya', 'yogyakarta', 'semarang', 'medan', 'makassar', 'palembang', 'denpasar', 'malang', 'sidoarjo', 'gresik', 'banyuwangi', 'jember', 'kediri', 'pasuruan', 'mojokerto'];
            const addressLower = address.toLowerCase();
            for (const keyword of cityKeywords) {
                if (addressLower.includes(keyword)) {
                    return keyword.charAt(0).toUpperCase() + keyword.slice(1);
                }
            }
            return null;
        }

        // Flag untuk mencegah multiple API calls
        let isCheckingShipping = false;
        let lastCheckedCity = '';
        let lastCheckedAddress = '';

        // Check shipping cost via Biteship
        function checkBiteshipCost() {
            // Prevent multiple simultaneous calls
            if (isCheckingShipping) {
                return;
            }

            const addressText = document.getElementById('address').value.trim();
            
            if (!addressText || addressText.length < 5) {
                return;
            }

            // Reset lastCheckedCity jika alamat berubah
            if (lastCheckedAddress !== addressText) {
                lastCheckedCity = '';
                lastCheckedAddress = addressText;
            }

            const cityName = extractCityFromAddress(addressText);
            if (!cityName) {
                showShippingError('Mohon cantumkan nama kota/kabupaten di alamat pengiriman (contoh: Jl. Contoh, Kabupaten Banyuwangi, Jawa Timur)');
                return;
            }

            // Skip jika city sama dengan yang baru saja dicek (tapi hanya jika alamat juga sama)
            if (lastCheckedCity === cityName && lastCheckedAddress === addressText) {
                return;
            }

            // Set flag dan update last checked city
            isCheckingShipping = true;
            lastCheckedCity = cityName;
            lastCheckedAddress = addressText;

            const weight = calculateTotalWeight();
            const loadingEl = document.getElementById('shippingLoading');
            const errorEl = document.getElementById('shippingError');
            const shippingOptionsList = document.getElementById('shippingOptionsList');

            // Show loading
            loadingEl.classList.remove('hidden');
            errorEl.classList.add('hidden');
            shippingOptionsList.innerHTML = '';

            // Call API - use route helper or base URL
            const shippingUrl = '{{ route("shipping.check-cost") }}';
            fetch(shippingUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    destination_city: cityName,
                    weight: weight
                })
            })
            .then(response => {
                if (!response.ok) {
                    // Jika response bukan 200-299, coba parse sebagai JSON dulu
                    return response.json().then(data => {
                        throw new Error(data.message || `HTTP error! status: ${response.status}`);
                    }).catch(() => {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                loadingEl.classList.add('hidden');
                isCheckingShipping = false;
                
                if (data.success) {
                    displayShippingOptions(data.couriers, data.destination_city);
                } else {
                    showShippingError(data.message || 'Gagal mendapatkan data ongkir');
                }
            })
            .catch(error => {
                loadingEl.classList.add('hidden');
                isCheckingShipping = false;
                // Reset lastCheckedCity agar bisa dicoba lagi
                lastCheckedCity = '';
                showShippingError('Terjadi kesalahan saat menghitung ongkir: ' + error.message);
            });
        }

        // Display Biteship shipping options
        function displayShippingOptions(couriers, destinationCity) {
            const container = document.getElementById('shippingOptionsList');
            container.innerHTML = '';

            if (!couriers || couriers.length === 0) {
                showShippingError('Tidak ada layanan pengiriman tersedia untuk tujuan ini.');
                return;
            }

            // Info destination
            const infoDiv = document.createElement('div');
            infoDiv.className = 'mb-3 p-3 bg-teal-50 border border-teal-200 rounded-lg';
            infoDiv.innerHTML = `
                <div class="text-sm text-teal-700">
                    <strong>Tujuan:</strong> ${destinationCity}
                </div>
            `;
            container.appendChild(infoDiv);

            // Display each courier
            couriers.forEach(courier => {
                const courierDiv = document.createElement('div');
                courierDiv.className = 'mb-3';
                
                const courierName = document.createElement('div');
                courierName.className = 'text-sm font-semibold text-gray-700 mb-2';
                courierName.textContent = `Kurir ${courier.courier_name}`;
                courierDiv.appendChild(courierName);

                // Display each service
                courier.services.forEach(service => {
                    const serviceCost = service.cost[0].value;
                    const serviceEtd = service.cost[0].etd || '-';
                    const serviceName = service.service;
                    const serviceCode = service.service_code || serviceName.toLowerCase(); // Gunakan service_code jika ada
                    const serviceDescription = service.description || '';

                    const label = document.createElement('label');
                    label.className = 'flex items-center p-3 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition cursor-pointer mb-2';
                    
                    label.innerHTML = `
                        <input type="radio" name="shipping_method" value="biteship" 
                               class="accent-teal-600 mr-3" 
                               data-cost="${serviceCost}"
                               data-courier="${courier.courier}"
                               data-service="${serviceName}"
                               data-service-code="${serviceCode}"
                               onclick="updateShippingCost(${serviceCost}, '${courier.courier}', '${serviceName}', '${serviceCode}')">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">${courier.courier_name} - ${serviceName}</div>
                            <div class="text-xs text-gray-600">${serviceDescription || 'Estimasi: ' + serviceEtd}</div>
                        </div>
                        <div class="text-teal-600 font-semibold">${formatRupiah(serviceCost)}</div>
                    `;
                    
                    courierDiv.appendChild(label);
                });

                container.appendChild(courierDiv);
            });
        }

        // Show shipping error
        function showShippingError(message) {
            const errorEl = document.getElementById('shippingError');
            const errorMessageEl = document.getElementById('shippingErrorMessage');
            errorMessageEl.textContent = message;
            errorEl.classList.remove('hidden');
        }

        // Debounce untuk checkShippingOptions
        let checkShippingTimeout = null;
        
        function checkShippingOptions() {
            // Clear previous timeout
            if (checkShippingTimeout) {
                clearTimeout(checkShippingTimeout);
            }
            
            // Debounce: tunggu 500ms setelah user selesai mengetik
            checkShippingTimeout = setTimeout(() => {
                const addressText = document.getElementById('address').value.toLowerCase();
                const isSurabaya = addressText.includes('surabaya');
            
            const kurirOption = document.getElementById('kurirOption');
            const kurirNotAvailable = document.getElementById('kurirNotAvailable');
            const shippingOptionsList = document.getElementById('shippingOptionsList');
            const shippingLoading = document.getElementById('shippingLoading');
            const shippingError = document.getElementById('shippingError');
            
            // Update COD option berdasarkan alamat pengiriman
            const codRadio = document.querySelector('input[name="payment_method"][value="cod"]');
            const codLabel = document.getElementById('codLabel');
            const codReasonText = codLabel ? codLabel.querySelector('.text-xs.text-red-500') : null;
            
            // Jika alamat mengandung "Surabaya"
            if (isSurabaya) {
                // Tampilkan Kurir Perusahaan, sembunyikan opsi Biteship
                if (kurirOption) {
                    kurirOption.classList.remove('hidden');
                }
                if (kurirNotAvailable) {
                    kurirNotAvailable.classList.add('hidden');
                }
                shippingOptionsList.innerHTML = '';
                shippingLoading.classList.add('hidden');
                shippingError.classList.add('hidden');
                
                // Set default ke kurir jika belum ada yang dipilih
                const kurirRadio = kurirOption ? kurirOption.querySelector('input[value="kurir"]') : null;
                if (kurirRadio && !document.querySelector('input[name="shipping_method"]:checked')) {
                    kurirRadio.checked = true;
                    updateShippingCost(0, 'kurir', 'Kurir Perusahaan');
                }
                
                // Aktifkan COD jika alamat Surabaya
                if (codLabel) {
                    codLabel.classList.remove('opacity-50', 'cursor-not-allowed');
                    codLabel.classList.add('hover:bg-gray-100', 'cursor-pointer');
                }
                if (codRadio) {
                    codRadio.disabled = false;
                }
                if (codReasonText) {
                    codReasonText.textContent = '';
                    codReasonText.classList.add('hidden');
                }
            } else {
                // Jika bukan Surabaya, sembunyikan Kurir Perusahaan
                if (kurirOption) {
                    kurirOption.classList.add('hidden');
                    const kurirRadio = kurirOption.querySelector('input[value="kurir"]');
                    if (kurirRadio && kurirRadio.checked) {
                        kurirRadio.checked = false;
                    }
                }
                if (kurirNotAvailable) {
                    kurirNotAvailable.classList.remove('hidden');
                }
                
                // Nonaktifkan COD jika alamat bukan Surabaya
                if (codLabel) {
                    codLabel.classList.add('opacity-50', 'cursor-not-allowed');
                    codLabel.classList.remove('hover:bg-gray-100', 'cursor-pointer');
                }
                if (codRadio) {
                    codRadio.disabled = true;
                    if (codRadio.checked) {
                        codRadio.checked = false;
                        // Pilih transfer sebagai default jika COD terpilih
                        const transferRadio = document.querySelector('input[name="payment_method"][value="transfer"]');
                        if (transferRadio) {
                            transferRadio.checked = true;
                            showPaymentInfo();
                        }
                    }
                }
                if (codReasonText) {
                    codReasonText.textContent = 'COD hanya tersedia untuk pengiriman di Surabaya. Silakan pilih metode pembayaran lain.';
                    codReasonText.classList.remove('hidden');
                }
                
                // Cek ongkir via Biteship
                checkBiteshipCost();
            }
            }, 500); // Debounce 500ms
        }

        function updateShippingCost(cost, courier = '', service = '', serviceCode = '') {
            document.getElementById('shippingCost').innerText = formatRupiah(cost);
            document.getElementById('shippingCostValue').value = cost;
            document.getElementById('shippingCourierValue').value = courier;
            document.getElementById('shippingServiceValue').value = service;
            document.getElementById('shippingServiceCodeValue').value = serviceCode || service.toLowerCase();
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
            } else if (selected.value === 'dp_midtrans') {
                paymentInfo.innerHTML = `
                <div>
                    <strong>DP 50% via Midtrans</strong><br>
                    Anda akan dibawa ke Midtrans untuk membayar 50% dari total pesanan.<br>
                    Sisa 50% akan ditagihkan oleh driver saat pengiriman.
                </div>
            `;
                paymentInfo.classList.remove('hidden');
                document.getElementById('btnBayar').disabled = false;
            } else {
                paymentInfo.classList.add('hidden');
                paymentInfo.innerHTML = '';
                document.getElementById('btnBayar').disabled = false;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const hutangRadio = document.getElementById('hutang');
            const btnBayar = document.getElementById('btnBayar');
            const codRadio = document.querySelector('input[name="payment_method"][value="cod"]');
            const codLabel = document.getElementById('codLabel');
            
            // Prevent click on disabled COD option
            if (codLabel && codRadio) {
                codLabel.addEventListener('click', function(e) {
                    if (codRadio.disabled) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                });
            }
            
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
                    } else if (codRadio && codRadio.checked && codRadio.disabled) {
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
            // Inisialisasi: jika COD terpilih dan disabled, button disable
            if (codRadio && codRadio.checked && codRadio.disabled) {
                btnBayar.disabled = true;
            }
        });

        // Validasi sebelum submit
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const selected = document.querySelector('input[name="payment_method"]:checked');
            
            // Validasi COD: jika COD dipilih tapi di-disable, prevent submit
            if (selected && selected.value === 'cod' && selected.disabled) {
                e.preventDefault();
                const codLabel = document.getElementById('codLabel');
                const reasonText = codLabel ? codLabel.querySelector('.text-xs.text-red-500')?.textContent || '' : '';
                alert('COD tidak tersedia. ' + reasonText);
                return false;
            }
            
            if (selected && selected.value === 'transfer') {
                const buktiInput = document.getElementById('bukti_transfer');
                if (!buktiInput || buktiInput.files.length === 0) {
                    e.preventDefault();
                    document.getElementById('buktiError').classList.remove('hidden');
                    buktiInput.scrollIntoView({behavior: 'smooth', block: 'center'});
                }
            }
            
            // Validasi shipping method: jika alamat Surabaya, harus pilih kurir
            const addressText = document.getElementById('address').value.toLowerCase();
            const isSurabaya = addressText.includes('surabaya');
            const shippingMethod = document.querySelector('input[name="shipping_method"]:checked');
            
            if (isSurabaya && shippingMethod && shippingMethod.value === 'expedition') {
                e.preventDefault();
                alert('Untuk alamat pengiriman di Surabaya, silakan pilih Kurir Perusahaan. Ekspedisi tidak tersedia untuk Surabaya.');
                return false;
            }
        });
        
        // Panggil checkShippingOptions saat halaman dimuat untuk inisialisasi (tanpa debounce)
        document.addEventListener('DOMContentLoaded', function() {
            // Clear timeout jika ada
            if (checkShippingTimeout) {
                clearTimeout(checkShippingTimeout);
            }
            // Panggil langsung tanpa debounce untuk inisialisasi
            const addressText = document.getElementById('address').value.toLowerCase();
            const isSurabaya = addressText.includes('surabaya');
            
            const kurirOption = document.getElementById('kurirOption');
            const kurirNotAvailable = document.getElementById('kurirNotAvailable');
            const shippingOptionsList = document.getElementById('shippingOptionsList');
            const shippingLoading = document.getElementById('shippingLoading');
            const shippingError = document.getElementById('shippingError');
            
            // Update COD option berdasarkan alamat pengiriman dan syarat transaksi
            const codRadio = document.querySelector('input[name="payment_method"][value="cod"]');
            const codLabel = document.getElementById('codLabel');
            const codReasonText = codLabel ? codLabel.querySelector('.text-xs.text-red-500') : null;
            
            // Jika alamat mengandung "Surabaya"
            if (isSurabaya) {
                // Tampilkan Kurir Perusahaan, sembunyikan opsi Biteship
                if (kurirOption) {
                    kurirOption.classList.remove('hidden');
                }
                if (kurirNotAvailable) {
                    kurirNotAvailable.classList.add('hidden');
                }
                shippingOptionsList.innerHTML = '';
                shippingLoading.classList.add('hidden');
                shippingError.classList.add('hidden');
                
                // Set default ke kurir jika belum ada yang dipilih
                const kurirRadio = kurirOption ? kurirOption.querySelector('input[value="kurir"]') : null;
                if (kurirRadio && !document.querySelector('input[name="shipping_method"]:checked')) {
                    kurirRadio.checked = true;
                    updateShippingCost(0, 'kurir', 'Kurir Perusahaan');
                }
                
                // Aktifkan COD jika alamat Surabaya
                if (codLabel) {
                    codLabel.classList.remove('opacity-50', 'cursor-not-allowed');
                    codLabel.classList.add('hover:bg-gray-100', 'cursor-pointer');
                }
                if (codRadio) {
                    codRadio.disabled = false;
                }
                if (codReasonText) {
                    codReasonText.textContent = '';
                    codReasonText.classList.add('hidden');
                }
            } else {
                // Jika bukan Surabaya, sembunyikan Kurir Perusahaan
                if (kurirOption) {
                    kurirOption.classList.add('hidden');
                    const kurirRadio = kurirOption.querySelector('input[value="kurir"]');
                    if (kurirRadio && kurirRadio.checked) {
                        kurirRadio.checked = false;
                    }
                }
                if (kurirNotAvailable) {
                    kurirNotAvailable.classList.remove('hidden');
                }
                
                // Nonaktifkan COD jika alamat bukan Surabaya
                if (codLabel) {
                    codLabel.classList.add('opacity-50', 'cursor-not-allowed');
                    codLabel.classList.remove('hover:bg-gray-100', 'cursor-pointer');
                }
                if (codRadio) {
                    codRadio.disabled = true;
                    if (codRadio.checked) {
                        codRadio.checked = false;
                        // Pilih transfer sebagai default jika COD terpilih
                        const transferRadio = document.querySelector('input[name="payment_method"][value="transfer"]');
                        if (transferRadio) {
                            transferRadio.checked = true;
                            showPaymentInfo();
                        }
                    }
                }
                if (codReasonText) {
                    codReasonText.textContent = 'COD hanya tersedia untuk pengiriman di Surabaya. Silakan pilih metode pembayaran lain.';
                    codReasonText.classList.remove('hidden');
                }
                
                // Reset flag untuk memastikan API bisa dipanggil
                isCheckingShipping = false;
                lastCheckedCity = '';
                lastCheckedAddress = '';
                
                // Cek ongkir via Biteship (hanya jika alamat sudah diisi)
                if (addressText && addressText.length >= 5) {
                    // Delay sedikit untuk memastikan DOM sudah siap
                    setTimeout(() => {
                        checkBiteshipCost();
                    }, 100);
                }
            }
            
            // Pastikan shipping cost ter-update jika sudah ada yang terpilih
            const selectedShipping = document.querySelector('input[name="shipping_method"]:checked');
            if (selectedShipping) {
                const cost = selectedShipping.getAttribute('data-cost') || 0;
                const courier = selectedShipping.getAttribute('data-courier') || '';
                const service = selectedShipping.getAttribute('data-service') || '';
                
                // Update hidden fields
                document.getElementById('shippingCostValue').value = cost;
                document.getElementById('shippingCourierValue').value = courier;
                document.getElementById('shippingServiceValue').value = service;
                
                // Update display
                updateShippingCost(parseInt(cost), courier, service);
            }
        });
    </script>
</body>

</html>

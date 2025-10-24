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
                        placeholder="Masukkan alamat pengiriman lengkap..." 
                        required>{{ old('address', $alamat_default_user ?? '') }}</textarea>
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
                                        @if($item->product_size)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                                </svg>
                                                <span class="font-medium">Ukuran:</span>
                                                <span class="ml-1">{{ $item->product_size }}</span>
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
                                        $itemPrice = $item->harga_custom ?? $item->product_price ?? 0;
                                        $itemTotal = $itemPrice * ($item->quantity ?? 1);
                                    @endphp
                                    <div class="text-lg font-bold text-teal-600">
                                        Rp {{ number_format($itemTotal, 0, ',', '.') }}
                                    </div>
                                    @if($item->harga_custom && str_contains($item->kebutuhan_custom ?? '', 'Hasil negosiasi'))
                                        <div class="text-xs text-gray-500">
                                            @ {{ number_format($item->harga_custom, 0, ',', '.') }}/pcs
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

                <div class="space-y-3">
                    @if($isFromSurabaya)
                        <label class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition cursor-pointer">
                            <input type="radio" name="shipping_method" value="kurir" class="accent-teal-600 mr-3" checked
                                onclick="updateShippingCost(0)">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-800">Kurir Perusahaan</div>
                                <div class="text-sm text-gray-600">Khusus Surabaya Gratis</div>
                            </div>
                            <div class="text-teal-600 font-semibold">Gratis</div>
                        </label>

                        <label class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition cursor-pointer">
                            <input type="radio" name="shipping_method" value="expedition" class="accent-teal-600 mr-3"
                                onclick="updateShippingCost(19000)">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-800">Ekspedisi</div>
                                <div class="text-sm text-gray-600">Pengiriman ke seluruh Indonesia</div>
                            </div>
                            <div class="text-teal-600 font-semibold">Rp 19.000</div>
                        </label>
                    @else
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
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

                        <label class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition cursor-pointer">
                            <input type="radio" name="shipping_method" value="expedition" class="accent-teal-600 mr-3" checked
                                onclick="updateShippingCost(19000)">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-800">Ekspedisi</div>
                                <div class="text-sm text-gray-600">Pengiriman ke seluruh Indonesia</div>
                            </div>
                            <div class="text-teal-600 font-semibold">Rp 19.000</div>
                        </label>
                    @endif
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

                    @if($isFromSurabaya)
                        <label class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition cursor-pointer">
                            <input type="radio" name="payment_method" value="cod" class="accent-teal-600 mr-3"
                                    onchange="showPaymentInfo()">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-800">COD</div>
                                <div class="text-sm text-gray-600">Bayar di Tempat</div>
                        </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            </label>
                    @else
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center text-red-600">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-semibold">COD Tidak Tersedia</span>
                            </div>
                            <div class="text-sm text-red-600 mt-1">
                                COD hanya tersedia untuk pengiriman di Surabaya. Silakan pilih metode pembayaran lain.
                            </div>
                        </div>
                    @endif

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

                {{-- shipping cost --}}
                <input type="hidden" id="shippingCostValue" name="shipping_cost">

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

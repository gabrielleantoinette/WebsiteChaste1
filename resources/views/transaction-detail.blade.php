<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Transaksi | CHASTE</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen">
    @include('layouts.customer-nav')

    <div class="max-w-4xl mx-auto py-10 px-4">
        <div class="mb-6">
            <x-breadcrumb :items="[
                ['label' => 'Transaksi', 'url' => route('transaksi')],
                ['label' => 'Detail']
            ]" />
            <h1 class="text-2xl font-bold text-gray-800">Detail Transaksi</h1>
        </div>

        {{-- Informasi Transaksi --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Informasi Transaksi
                </h2>
        </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Kolom Kiri --}}
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 mb-1">Kode Invoice</p>
                                <p class="text-base font-mono font-semibold text-gray-900">{{ $transaction->code }}</p>
                    </div>
                </div>
                
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 mb-1">Status</p>
                                @php
                                    $statusLower = strtolower($transaction->status);
                                    $statusColors = [
                                        'dikemas' => 'bg-teal-100 text-teal-700 border-teal-200',
                                        'dibayar' => 'bg-teal-100 text-teal-700 border-teal-200',
                                        'menunggu konfirmasi pembayaran' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                        'pembayaran ditolak' => 'bg-red-100 text-red-700 border-red-200',
                                        'dikirim' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'sampai' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'diterima' => 'bg-green-100 text-green-700 border-green-200',
                                        'selesai' => 'bg-green-100 text-green-700 border-green-200',
                                    ];
                                    $statusColor = $statusColors[$statusLower] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium border {{ $statusColor }}">
                                    {{ ucwords(str_replace('_', ' ', $transaction->status)) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 mb-1">Alamat Pengiriman</p>
                                <p class="text-base text-gray-900 leading-relaxed">{{ $transaction->address }}</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Kolom Kanan --}}
                    <div class="space-y-4">
                        @if($transaction->shipping_cost > 0)
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 mb-1">Pengiriman</p>
                                <p class="text-base font-semibold text-gray-900">
                                    {{ $transaction->shipping_courier ? ucfirst($transaction->shipping_courier) : 'Kurir Perusahaan' }}
                                    @if($transaction->shipping_service)
                                        <span class="text-gray-600 font-normal">- {{ $transaction->shipping_service }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 mb-1">Ongkos Kirim</p>
                                <p class="text-lg font-bold text-indigo-600">Rp {{ number_format($transaction->shipping_cost, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @endif
                        
                        <div class="flex items-start gap-3 pt-4 border-t border-gray-200">
                            <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 mb-1">Total Pembayaran</p>
                                <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Tombol Download Invoice --}}
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('invoice.download', $transaction->id) }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Download Invoice PDF</span>
                    </a>
                    <p class="text-xs text-gray-500 mt-2">Unduh invoice dalam format PDF untuk keperluan dokumentasi</p>
                </div>
            </div>
        </div>

        @if($transaction->details && $transaction->details->count() > 0)
        @php
            $detailCount = $transaction->details->count();
            $showCollapse = $detailCount > 1;
        @endphp
        {{-- Detail Produk yang Dibeli --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Detail Produk yang Dibeli
                        <span class="text-sm font-normal opacity-90">({{ $detailCount }} barang)</span>
                    </h2>
                    @if($showCollapse)
                    <button onclick="toggleProductDetails()" 
                            id="toggleBtn"
                            class="text-white hover:text-teal-100 transition flex items-center gap-2 text-sm font-medium">
                        <span id="toggleText">Lihat Semua</span>
                        <svg id="toggleIcon" class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>
            
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Produk</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Warna</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Ukuran</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Harga</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700">Qty</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($transaction->details as $detail)
                                <tr class="hover:bg-gray-50 transition product-row {{ $showCollapse && $loop->iteration > 1 ? 'hidden' : '' }}">
                                    <td class="px-4 py-3 text-gray-600">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            @if($detail->product && $detail->product->image_url)
                                            <img src="{{ asset($detail->product->image_url) }}" 
                                                 alt="{{ $detail->product->name }}" 
                                                 class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                                            @endif
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">
                                                    @if($detail->product && $detail->product->name)
                                                        {{ $detail->product->name }}
                                                    @elseif($detail->kebutuhan_custom)
                                                        <span class="text-blue-600">Custom Terpal</span>
                                                    @else
                                                        -
                                                    @endif
                                                </p>
                                                @if($detail->product && $detail->product->description)
                                                <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ Str::limit($detail->product->description, 50) }}</p>
                                                @endif
                                                {{-- Detail Custom Terpal --}}
                                                @if($detail->kebutuhan_custom && !$detail->product)
                                                <div class="mt-3 p-4 bg-gradient-to-br from-blue-50 to-teal-50 border border-blue-200 rounded-lg shadow-sm">
                                                    <div class="flex items-center gap-2 mb-3">
                                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <div class="font-semibold text-blue-900 text-sm">Detail Custom Terpal</div>
                                                    </div>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                                                        @if($detail->bahan_custom)
                                                        <div class="flex items-start gap-2 bg-white p-2 rounded border border-blue-100">
                                                            <span class="font-semibold text-gray-600 min-w-[90px]">Bahan:</span>
                                                            <span class="text-gray-900 font-medium flex-1">{{ $detail->bahan_custom }}</span>
                                                        </div>
                                                        @endif
                                                        
                                                        @if($detail->kebutuhan_custom)
                                                        <div class="flex items-start gap-2 bg-white p-2 rounded border border-blue-100">
                                                            <span class="font-semibold text-gray-600 min-w-[90px]">Kebutuhan:</span>
                                                            <span class="text-gray-900 flex-1">{{ $detail->kebutuhan_custom }}</span>
                                                        </div>
                                                        @endif
                                                        
                                                        @if($detail->ukuran_custom)
                                                        <div class="flex items-start gap-2 bg-white p-2 rounded border border-blue-100">
                                                            <span class="font-semibold text-gray-600 min-w-[90px]">Ukuran:</span>
                                                            <span class="text-gray-900 font-semibold flex-1">{{ $detail->ukuran_custom }}</span>
                                                        </div>
                                                        @endif
                                                        
                                                        @if($detail->warna_custom)
                                                        <div class="flex items-start gap-2 bg-white p-2 rounded border border-blue-100">
                                                            <span class="font-semibold text-gray-600 min-w-[90px]">Warna:</span>
                                                            <span class="text-gray-900 flex-1">{{ $detail->warna_custom }}</span>
                                                        </div>
                                                        @endif
                                                        
                                                        @if($detail->jumlah_ring_custom)
                                                        <div class="flex items-start gap-2 bg-white p-2 rounded border border-blue-100">
                                                            <span class="font-semibold text-gray-600 min-w-[90px]">Jumlah Ring:</span>
                                                            <span class="text-gray-900 flex-1">{{ $detail->jumlah_ring_custom }} buah</span>
                                                        </div>
                                                        @endif
                                                        
                                                        @if($detail->pakai_tali_custom)
                                                        <div class="flex items-start gap-2 bg-white p-2 rounded border border-blue-100">
                                                            <span class="font-semibold text-gray-600 min-w-[90px]">Tali:</span>
                                                            <span class="text-gray-900 flex-1">
                                                                @if($detail->pakai_tali_custom == 'ya' || $detail->pakai_tali_custom == '1' || $detail->pakai_tali_custom == 1)
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Ya, perlu tali</span>
                                                                @elseif($detail->pakai_tali_custom == 'tidak' || $detail->pakai_tali_custom == '0' || $detail->pakai_tali_custom == 0)
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Tidak</span>
                                                                @else
                                                                    {{ $detail->pakai_tali_custom }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                        @endif
                                                        
                                                        @if($detail->catatan_custom)
                                                        <div class="flex items-start gap-2 bg-white p-2 rounded border border-blue-100 md:col-span-2">
                                                            <span class="font-semibold text-gray-600 min-w-[90px]">Catatan:</span>
                                                            <span class="text-gray-900 flex-1">{{ $detail->catatan_custom }}</span>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        @if($detail->variant && $detail->variant->color)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $detail->variant->color }}
                                            </span>
                                        @elseif($detail->warna_custom)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $detail->warna_custom }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        @php
                                            $sizeText = '-';
                                            // Prioritas 1: Jika ada ukuran_custom langsung
                                            if (!empty($detail->ukuran_custom)) {
                                                $sizeText = $detail->ukuran_custom;
                                            }
                                            // Prioritas 2: Ekstrak dari kebutuhan_custom
                                            elseif (!empty($detail->kebutuhan_custom)) {
                                                // Coba format (2x3) atau (2 x 3)
                                                if (preg_match('/\(?\s*(\d+)\s*[xX×]\s*(\d+)\s*\)?/', $detail->kebutuhan_custom, $matches)) {
                                                    $sizeText = $matches[1] . 'x' . $matches[2] . ' m';
                                                } 
                                                // Coba format "ukuran: 2x3" atau "ukuran 2x3"
                                                elseif (preg_match('/ukuran\s*:?\s*(\d+)\s*[xX×]\s*(\d+)/i', $detail->kebutuhan_custom, $matches)) {
                                                    $sizeText = $matches[1] . 'x' . $matches[2] . ' m';
                                                }
                                                // Coba cari pola angka x angka di mana saja
                                                elseif (preg_match('/(\d+)\s*[xX×]\s*(\d+)/', $detail->kebutuhan_custom, $matches)) {
                                                    $sizeText = $matches[1] . 'x' . $matches[2] . ' m';
                                                }
                                                else {
                                                    $sizeText = 'Custom';
                                                }
                                            }
                                            // Prioritas 3: Untuk produk regular
                                            elseif($detail->selected_size) {
                                                $sizeText = $detail->selected_size;
                                            }
                                        @endphp
                                        <span class="font-medium">{{ $sizeText }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-900">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center text-gray-900">{{ $detail->quantity }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                            @if($transaction->shipping_cost > 0)
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-right font-semibold text-gray-700">
                                    Subtotal Produk:
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                    Rp {{ number_format($transaction->grand_total - ($transaction->shipping_cost ?? 0), 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-right font-semibold text-gray-700">
                                    Ongkos Kirim
                                    @if($transaction->shipping_courier || $transaction->shipping_service)
                                        <span class="text-sm text-gray-500 font-normal">
                                            ({{ $transaction->shipping_courier ? ucfirst($transaction->shipping_courier) : 'Kurir Perusahaan' }}
                                            @if($transaction->shipping_service)
                                                - {{ $transaction->shipping_service }}
                                            @endif)
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                    Rp {{ number_format($transaction->shipping_cost, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endif
                            @if($transaction->tracking_number)
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-right font-semibold text-gray-700">
                                    Nomor Resi:
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="font-mono font-semibold text-blue-600">{{ $transaction->tracking_number }}</span>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-right font-bold text-gray-900 text-lg">Total:</td>
                                <td class="px-4 py-3 text-right font-bold text-teal-600 text-xl">
                                    Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        @if($showCollapse)
        <script>
            let isExpanded = false;
            function toggleProductDetails() {
                const rows = document.querySelectorAll('.product-row');
                const toggleText = document.getElementById('toggleText');
                const toggleIcon = document.getElementById('toggleIcon');
                
                isExpanded = !isExpanded;
                
                rows.forEach((row, index) => {
                    if (index > 0) { // Skip first row
                        if (isExpanded) {
                            row.classList.remove('hidden');
                        } else {
                            row.classList.add('hidden');
                        }
                    }
                });
                
                if (isExpanded) {
                    toggleText.textContent = 'Sembunyikan';
                    toggleIcon.classList.add('rotate-180');
                } else {
                    toggleText.textContent = 'Lihat Semua';
                    toggleIcon.classList.remove('rotate-180');
                }
            }
        </script>
        @endif
        @endif

        {{-- Tombol Batalkan Pesanan --}}
        @php
            $canCancel = in_array($transaction->status, ['Menunggu Pembayaran', 'Menunggu Konfirmasi Pembayaran', 'Dikemas']);
            $createdAt = \Carbon\Carbon::parse($transaction->created_at);
            $minutesDiff = $createdAt->diffInMinutes(now());
            $withinTimeLimit = $minutesDiff <= 15;
            $showCancelButton = $canCancel && $withinTimeLimit;
        @endphp
        @if($showCancelButton)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Batalkan Pesanan</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Anda dapat membatalkan pesanan ini maksimal 15 menit setelah pembuatan. 
                        @if($minutesDiff > 0)
                            <span class="font-medium text-orange-600">Waktu tersisa: {{ 15 - $minutesDiff }} menit</span>
                        @endif
                    </p>
                    <button onclick="showCancelModal()" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batalkan Pesanan
                    </button>
                </div>
            </div>
            
            {{-- Modal Cancel --}}
            <div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Batalkan Pesanan</h3>
                    <form method="POST" action="{{ route('transaksi.cancel', $transaction->id) }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Pembatalan <span class="text-red-500">*</span></label>
                            <textarea name="cancellation_reason" id="cancellation_reason" rows="4" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                                placeholder="Masukkan alasan pembatalan pesanan (minimal 10 karakter)" required></textarea>
                            <p class="text-xs text-gray-500 mt-1">Minimal 10 karakter, maksimal 500 karakter</p>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" onclick="hideCancelModal()" 
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                Batal
                            </button>
                            <button type="submit" 
                                class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                                Konfirmasi Pembatalan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
                function showCancelModal() {
                    document.getElementById('cancelModal').classList.remove('hidden');
                }
                function hideCancelModal() {
                    document.getElementById('cancelModal').classList.add('hidden');
                }
                // Close modal when clicking outside
                document.getElementById('cancelModal')?.addEventListener('click', function(e) {
                    if (e.target === this) {
                        hideCancelModal();
                    }
                });
            </script>
        @elseif($canCancel && !$withinTimeLimit)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
                <p class="text-sm text-yellow-800">
                    <strong>Pesanan tidak dapat dibatalkan.</strong> Waktu pembatalan (15 menit setelah pembuatan) telah habis.
                </p>
            </div>
        @elseif(in_array($transaction->status, ['Dikirim', 'dikirim', 'sampai']))
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                <p class="text-sm text-red-800">
                    <strong>Pesanan tidak dapat dibatalkan.</strong> Pesanan dengan status "{{ $transaction->status }}" tidak dapat dibatalkan karena sudah dalam proses pengiriman.
                </p>
            </div>
        @endif

        @if ($transaction->status == 'sampai')
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl p-6 mb-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Pesanan Sudah Sampai!</h3>
                        <p class="text-gray-700 mb-4">Pesanan kamu sudah sampai di tujuan. Silakan klik tombol di bawah untuk menyelesaikan transaksi.</p>
                <form method="POST" action="{{ route('transaksi.diterima', $transaction->id) }}">
                    @csrf
                            <button class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg transform transition duration-200 hover:scale-105">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                        Selesaikan Pesanan
                    </button>
                </form>
                    </div>
                </div>
            </div>
        @endif

        @if ($transaction->status == 'diterima')
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        Beri Review
                    </h2>
                </div>
                
                <div class="p-6">
                @if (!$hasReviewed)
                    <div class="mb-6">
                        <p class="text-gray-700 mb-1">Bagaimana pengalaman kamu dengan produk ini?</p>
                        <p class="text-sm text-gray-500">Review kamu sangat membantu untuk meningkatkan kualitas layanan kami.</p>
                    </div>
                    
                    <!-- Review Form -->
                    <div id="reviewForm" class="space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="font-medium">Rating:</span>
                            <div class="flex items-center gap-1">
                                <input type="hidden" name="rating" id="rating" value="0">
                                <div class="flex gap-1" id="starRating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-7 h-7 text-gray-300 cursor-pointer star hover:text-yellow-400 transition-colors"
                                            data-value="{{ $i }}" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285-5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.563.563 0 0 1 .321-.988l5.518-.442c.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                        </svg>
                                        <svg class="w-7 h-7 text-yellow-400 cursor-pointer star-filled hidden"
                                            data-value="{{ $i }}" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="review" class="block font-medium text-gray-700 mb-2">Komentar (opsional):</label>
                            <textarea name="review" id="review" placeholder="Bagaimana pengalaman kamu dengan produk ini?" 
                                      class="w-full border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 resize-none" 
                                      rows="4"></textarea>
                        </div>
                        
                        <button type="button" id="submitReviewBtn" 
                                class="bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Kirim Review
                        </button>
                    </div>

                    <!-- Review Success Message -->
                    <div id="reviewSuccess" class="hidden bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 text-green-800 px-4 py-4 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">Review berhasil dikirim! Terima kasih atas feedback kamu.</span>
                        </div>
                    </div>

                    <!-- Review Error Message -->
                    <div id="reviewError" class="hidden bg-gradient-to-r from-red-50 to-pink-50 border-2 border-red-200 text-red-800 px-4 py-4 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span id="errorMessage" class="font-medium"></span>
                        </div>
                    </div>
                @else
                    <!-- Sudah Review Message -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 text-blue-800 px-4 py-4 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">Terima kasih! Kamu sudah memberikan review untuk pesanan ini.</span>
                        </div>
                    </div>
                @endif
                </div>
            </div>
        @endif

        @php
            $isSampai = $transaction->status === 'sampai';
            $sampaiKurangDari24Jam = $isSampai && \Carbon\Carbon::parse($transaction->updated_at)->diffInHours(now()) < 24;
        @endphp

        @if ($sampaiKurangDari24Jam)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Ingin Mengembalikan Barang?</h3>
                        <p class="text-sm text-gray-600">Kamu masih bisa mengajukan retur dalam 24 jam setelah pesanan sampai.</p>
                    </div>
                <a href="{{ url('/retur/' . $transaction->id) }}"
                       class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg shadow-lg transform transition duration-200 hover:scale-105 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Retur Barang
                    </a>
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');
            const starsFilled = document.querySelectorAll('.star-filled');
            const ratingInput = document.getElementById('rating');
            const submitReviewBtn = document.getElementById('submitReviewBtn');
            const reviewForm = document.getElementById('reviewForm');
            const reviewSuccess = document.getElementById('reviewSuccess');
            const reviewError = document.getElementById('reviewError');
            const errorMessage = document.getElementById('errorMessage');

            function updateStars(value) {
                stars.forEach((star, index) => {
                    const starFilled = starsFilled[index];
                    if (index < value) {
                        star.classList.add('hidden');
                        starFilled.classList.remove('hidden');
                    } else {
                        star.classList.remove('hidden');
                        starFilled.classList.add('hidden');
                    }
                });
            }

            stars.forEach((star, index) => {
                const starFilled = starsFilled[index];

                star.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    ratingInput.value = value;
                    updateStars(value);
                });

                starFilled.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    ratingInput.value = value;
                    updateStars(value);
                });

                // Add hover effect
                star.addEventListener('mouseover', function() {
                    const value = this.getAttribute('data-value');
                    updateStars(value);
                });

                starFilled.addEventListener('mouseover', function() {
                    const value = this.getAttribute('data-value');
                    updateStars(value);
                });

                star.addEventListener('mouseout', function() {
                    const currentValue = ratingInput.value;
                    updateStars(currentValue);
                });

                starFilled.addEventListener('mouseout', function() {
                    const currentValue = ratingInput.value;
                    updateStars(currentValue);
                });
            });

            submitReviewBtn.addEventListener('click', function() {
                const rating = ratingInput.value;
                const review = document.getElementById('review').value;

                if (rating === '0') {
                    errorMessage.textContent = 'Silakan beri rating terlebih dahulu.';
                    reviewError.classList.remove('hidden');
                    reviewSuccess.classList.add('hidden');
                    return;
                }

                                 submitReviewBtn.disabled = true;
                 submitReviewBtn.textContent = 'Mengirim...';
                 
                 console.log('Sending review data:', {
                     order_id: {{ $transaction->id }},
                     product_id: {{ $productId ?? 1 }},
                     rating: rating,
                     comment: review
                 });

                                 fetch(`{{ route('review.submit') }}`, {
                     method: 'POST',
                     headers: {
                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                         'Content-Type': 'application/json',
                     },
                     body: JSON.stringify({
                         order_id: {{ $transaction->id }},
                         product_id: {{ $productId ?? 1 }}, // Product ID dari dinvoice
                         rating: parseInt(rating),
                         comment: review,
                     }),
                 })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        reviewForm.classList.add('hidden');
                        reviewSuccess.classList.remove('hidden');
                        errorMessage.textContent = '';
                    } else {
                        errorMessage.textContent = data.message || 'Gagal mengirim review.';
                        reviewError.classList.remove('hidden');
                        reviewSuccess.classList.add('hidden');
                    }
                                         submitReviewBtn.disabled = false;
                     submitReviewBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Kirim Review';
                })
                                 .catch(error => {
                     console.error('Error:', error);
                     errorMessage.textContent = 'Terjadi kesalahan saat mengirim review. Silakan coba lagi.';
                     reviewError.classList.remove('hidden');
                     reviewSuccess.classList.add('hidden');
                     submitReviewBtn.disabled = false;
                     submitReviewBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Kirim Review';
                 });
            });
        });
    </script>

@include('layouts.footer')

</body>

</html>

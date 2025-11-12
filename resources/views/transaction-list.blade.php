<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>List Transaksi | CHASTE</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-white font-sans text-gray-800">
    {{-- Header --}}
    @include('layouts.customer-nav')
    
    <!-- Transaksi -->
    <section class="px-4 sm:px-6 lg:px-12 min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
            <x-breadcrumb :items="[
                ['label' => 'Transaksi']
            ]" />
                <div class="mt-4">
                    <h1 class="text-3xl font-bold text-gray-900">List Transaksi</h1>
                    <p class="text-gray-600 mt-1">Kelola dan lacak semua transaksi Anda</p>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <form method="GET" action="{{ route('transaksi') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari berdasarkan nomor invoice atau nama barang..."
                               class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition shadow-sm">
                        <svg class="absolute left-3.5 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" 
                                class="px-6 py-3 bg-gradient-to-r from-teal-600 to-teal-700 text-white rounded-lg hover:from-teal-700 hover:to-teal-800 transition font-medium shadow-md hover:shadow-lg flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('transaksi', ['status' => request('status')]) }}" 
                               class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Reset
                            </a>
                        @endif
                    </div>
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                </form>
        </div>

        {{-- Filter Tab Status --}}
        @php
        $statusOptions = [
                '' => ['label' => 'Semua', 'icon' => 'M4 6h16M4 12h16M4 18h16'],
                'menunggukonfirmasi' => ['label' => 'Menunggu Konfirmasi Pembayaran', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                'dikemas' => ['label' => 'Sedang Dikemas', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                'dikirim' => ['label' => 'Dikirim', 'icon' => 'M5 13l4 4L19 7'],
                'diterima' => ['label' => 'Selesai', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                'pengembalian' => ['label' => 'Pengembalian', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                'beripenilaian' => ['label' => 'Beri Penilaian', 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z']
        ];

        $currentStatus = request('status');
        @endphp

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                <div class="flex gap-1 text-sm font-medium overflow-x-auto scrollbar-hide px-4 py-2">
                @foreach ($statusOptions as $key => $option)
            @php
                        $label = $option['label'];
                        $icon = $option['icon'];
                        
                        // Count total transaksi per status (tanpa filter search untuk menunjukkan total)
                $count = \App\Models\HInvoice::where('customer_id', Session::get('user')['id'])
                            ->when($key !== '', function ($q) use ($key) {
                                if ($key === 'dikirim') {
                                    $q->whereIn('status', ['dikirim', 'sampai']);
                                } else if ($key === 'menunggukonfirmasi') {
                                    $q->where('status', 'Menunggu Konfirmasi Pembayaran');
                                } else if ($key === 'dikemas') {
                                            $q->whereIn('status', ['dibayar', 'Dikemas', 'dikemas']);
                                } else if ($key === 'beripenilaian') {
                                    $q->where('status', 'diterima');
                                } else if ($key === 'pengembalian') {
                                            $q->where('status', 'retur_diajukan');
                                } else {
                                    $q->where('status', $key);
                                }
                            })
                            ->count();
                        
                        // Build URL dengan mempertahankan search jika ada
                        $urlParams = [];
                        if ($key !== '') {
                            $urlParams['status'] = $key;
                        }
                        if (request('search')) {
                            $urlParams['search'] = request('search');
                        }
                        $url = route('transaksi', $urlParams);
            @endphp

                    <a href="{{ $url }}"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg whitespace-nowrap transition-all duration-200 {{ $currentStatus === $key ? 'bg-teal-50 text-teal-700 font-semibold border border-teal-200' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                        </svg>
                        <span>{{ $label }}</span>
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $currentStatus === $key ? 'bg-teal-100 text-teal-700' : 'bg-gray-100 text-gray-600' }}">{{ $count }}</span>
            </a>
        @endforeach
        </div>
            </div>

            {{-- Transaction List --}}
            <div class="space-y-3">
                @forelse ($transactions as $transaction)
                    @php
                        $statusLower = strtolower($transaction->status);
                        $statusClass = '';
                        $statusText = '';
                        
                        if ($statusLower == 'dikemas' || $statusLower == 'dibayar') {
                            $statusClass = 'bg-teal-50 text-teal-600 border-teal-200';
                            $statusText = 'Sedang Dikemas';
                        } elseif ($statusLower == 'menunggu konfirmasi pembayaran') {
                            $statusClass = 'bg-yellow-50 text-yellow-600 border-yellow-200';
                            $statusText = 'Menunggu Konfirmasi';
                        } elseif ($statusLower == 'selesai' || $statusLower == 'diterima') {
                            $statusClass = 'bg-green-50 text-green-600 border-green-200';
                            $statusText = 'Selesai';
                        } elseif ($statusLower == 'dikirim' || $statusLower == 'sampai') {
                            $statusClass = 'bg-blue-50 text-blue-600 border-blue-200';
                            $statusText = 'Dikirim';
                        } else {
                            $statusClass = 'bg-gray-50 text-gray-600 border-gray-200';
                            $statusText = ucwords(str_replace('_', ' ', $transaction->status));
                        }
                        
                        $date = \Carbon\Carbon::parse($transaction->created_at);
                        $details = $transaction->details;
                        $totalItems = $details->sum('quantity');
                    @endphp
                    
                    <div class="bg-white rounded-lg border border-gray-200 hover:border-teal-300 hover:shadow-sm transition-all duration-200 overflow-hidden">
                        {{-- Header: Invoice & Status --}}
                        <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-500">No. Invoice:</span>
                                <span class="text-xs font-mono text-gray-700">{{ $transaction->code }}</span>
                                <span class="text-xs text-gray-400">•</span>
                                <span class="text-xs text-gray-500">{{ $date->translatedFormat('d M Y, H:i') }}</span>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium border {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </div>
                        
                        {{-- Content: Products --}}
                        <div class="p-4">
                            <div class="flex flex-col md:flex-row gap-4">
                                {{-- Product Images & Info --}}
                                <div class="flex-1 space-y-3">
                                    @foreach($details->take(3) as $detail)
                                        @php
                                            // Check if this is a custom item
                                            // Custom item: has kebutuhan_custom OR (no product_id or product_id = 0) AND no variant_id
                                            $isCustom = !empty($detail->kebutuhan_custom) || 
                                                       ((!$detail->product_id || $detail->product_id == 0) && (!$detail->variant_id || $detail->variant_id == 0));
                                            
                                            if ($isCustom) {
                                                // Custom terpal item
                                                $productName = 'Terpal Custom';
                                                $productImage = asset('images/gulungan-terpal.png');
                                                $variantName = $detail->warna_custom ? 'Warna: ' . ucfirst($detail->warna_custom) : '';
                                                $customInfo = $detail->kebutuhan_custom ?? 'Terpal Custom';
                                            } else {
                                                // Regular product - try to load product
                                                $product = $detail->product;
                                                $variant = $detail->variant;
                                                
                                                if ($product && $product->exists) {
                                                    // Product found
                                                    $productImage = asset($product->image_url);
                                                    $productName = $product->name;
                                                    $variantName = $variant && $variant->exists ? $variant->color : '';
                                                } else {
                                                    // Product not found (might be deleted or invalid product_id)
                                                    // Check if we have any info to display
                                                    if ($detail->product_id) {
                                                        $productName = 'Produk tidak ditemukan';
                                                        $productImage = asset('images/gulungan-terpal.png');
                                                    } else {
                                                        // No product_id and no custom info - treat as custom
                                                        $productName = 'Terpal Custom';
                                                        $productImage = asset('images/gulungan-terpal.png');
                                                        $customInfo = 'Item pesanan';
                                                    }
                                                    $variantName = '';
                                                }
                                            }
                                        @endphp
                                        <div class="flex gap-3">
                                            {{-- Product Image --}}
                                            <a href="{{ route('transaksi.detail', $transaction->id) }}" class="flex-shrink-0">
                                                <img src="{{ $productImage }}" 
                                                     alt="{{ $productName }}"
                                                     class="w-16 h-16 md:w-20 md:h-20 object-cover rounded border border-gray-200">
                                            </a>
                                            
                                            {{-- Product Info --}}
                                            <div class="flex-1 min-w-0">
                                                <a href="{{ route('transaksi.detail', $transaction->id) }}" class="block">
                                                    <h4 class="text-sm font-medium text-gray-900 line-clamp-2 hover:text-teal-600 transition">
                                                        {{ $productName }}
                                                    </h4>
                                                </a>
                                                @if($isCustom && $customInfo)
                                                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $customInfo }}</p>
                                                @endif
                                                @if($variantName)
                                                    <p class="text-xs text-gray-500 mt-0.5">{{ $isCustom ? $variantName : 'Varian: ' . ucfirst($variantName) }}</p>
                                                @endif
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-xs text-gray-500">x{{ $detail->quantity }}</span>
                                                    <span class="text-xs text-gray-400">•</span>
                                                    <span class="text-sm font-semibold text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if($details->count() > 3)
                                        <div class="text-xs text-gray-500 pt-2 border-t border-gray-100">
                                            +{{ $details->count() - 3 }} produk lainnya
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Right: Total & Action --}}
                                <div class="md:w-48 flex flex-col justify-between gap-3 md:border-l md:pl-4 md:border-gray-200">
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500 mb-1">Total Pembayaran</p>
                                        <p class="text-lg font-bold text-teal-600">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</p>
                                        @if($transaction->shipping_cost > 0)
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $totalItems }} barang + ongkir Rp {{ number_format($transaction->shipping_cost, 0, ',', '.') }}
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-500 mt-1">{{ $totalItems }} barang</p>
                                        @endif
                                    </div>
                                <a href="{{ route('transaksi.detail', $transaction->id) }}"
                                       class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition font-medium text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Detail Pesanan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12">
                        <div class="text-center">
                            @if(request('search'))
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Tidak ada transaksi yang ditemukan</h3>
                                        <p class="text-gray-600 mb-4">Coba gunakan kata kunci lain untuk pencarian</p>
                                        <a href="{{ route('transaksi', ['status' => request('status')]) }}" 
                                           class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition font-medium">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Reset Pencarian
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum ada transaksi</h3>
                                        <p class="text-gray-600">Mulai berbelanja untuk melihat transaksi Anda di sini</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    <!-- Footer -->
    @include('layouts.footer')

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

</body>

</html>

@extends('layouts.admin')

@section('content')
<div class="flex justify-center px-4 sm:px-0">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 sm:p-5 lg:p-6 w-full max-w-4xl">
        @if(session('success'))
            <div class="bg-green-100 text-green-800 px-3 sm:px-4 py-2 sm:py-3 rounded mb-3 sm:mb-4">
                <span class="text-xs sm:text-sm">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-800 px-3 sm:px-4 py-2 sm:py-3 rounded mb-3 sm:mb-4">
                <span class="text-xs sm:text-sm">{{ session('error') }}</span>
            </div>
        @endif
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-3 sm:gap-0">
            <a href="{{ url('/admin/gudang-transaksi') }}" class="text-gray-600 hover:text-gray-800 transition-colors text-xs sm:text-sm flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Detail Invoice</h1>
        </div>

        {{-- Informasi Invoice --}}
        <div class="mb-4 sm:mb-6">
            <h2 class="text-base sm:text-lg font-semibold text-teal-700 mb-2 sm:mb-3">Informasi Invoice</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 text-xs sm:text-sm text-gray-700">
                <div><strong>Invoice ID:</strong> {{ $invoice->id }}</div>
                <div><strong>Kode Invoice:</strong> {{ $invoice->code }}</div>
                <div><strong>Tanggal Jatuh Tempo:</strong> {{ $invoice->due_date }}</div>
                <div><strong>Tanggal Penerimaan Barang:</strong> {{ $invoice->receive_date }}</div>
            </div>
        </div>

        {{-- Customer --}}
        <div class="mb-4 sm:mb-6">
            <h2 class="text-base sm:text-lg font-semibold text-teal-700 mb-2 sm:mb-3">Informasi Customer</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 text-xs sm:text-sm text-gray-700">
                <div><strong>Nama:</strong> {{ $invoice->customer->name }}</div>
                <div><strong>Telepon:</strong> {{ $invoice->customer->phone }}</div>
                <div class="sm:col-span-2"><strong>Email:</strong> {{ $invoice->customer->email }}</div>
            </div>
        </div>

        {{-- Gudang --}}
        <div class="mb-4 sm:mb-6">
            <h2 class="text-base sm:text-lg font-semibold text-teal-700 mb-2 sm:mb-3">Staff Gudang</h2>
            @if (!$invoice->gudang)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                    <h3 class="text-sm sm:text-base font-semibold text-yellow-800 mb-1 sm:mb-2">Konfirmasi Kualitas Barang</h3>
                    <p class="text-xs sm:text-sm text-yellow-700 mb-2 sm:mb-3">Unggah foto bukti kualitas barang satu per satu. Minimal 1 foto wajib diupload sebelum klik "Siapkan Barang".</p>
                    
                    <div class="mb-3">
                        <label for="quality_proof_photo" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Unggah Foto Bukti Kualitas</label>
                        <div class="flex gap-2">
                            <input type="file" id="quality_proof_photo" accept="image/*" class="block flex-1 text-xs sm:text-sm text-gray-500 file:mr-4 file:py-1.5 sm:file:py-2 file:px-3 sm:file:px-4 file:rounded-full file:border-0 file:text-xs sm:file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                            <button type="button" id="uploadBtn" class="bg-teal-600 hover:bg-teal-700 text-white text-xs sm:text-sm font-semibold px-4 py-2 rounded-md transition whitespace-nowrap">
                                Unggah
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Format: jpeg, png, jpg - maks 2MB per gambar</p>
                        <div id="uploadMessage" class="mt-2"></div>
                    </div>
                    
                    <div id="uploadedPhotos" class="mb-3 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        @if($invoice->quality_proof_photo)
                            @php
                                $photos = json_decode($invoice->quality_proof_photo, true);
                                if (!is_array($photos)) {
                                    $photos = [$invoice->quality_proof_photo];
                                }
                            @endphp
                            @foreach($photos as $index => $proofPath)
                                @php
                                    $imageUrl = null;
                                    if ($proofPath) {
                                        $cleanPath = ltrim($proofPath, '/');
                                        $imageUrl = url('/public/storage/' . $cleanPath);
                                    }
                                    if (!$imageUrl) {
                                        $imageUrl = asset('images/gulungan-terpal.png');
                                    }
                                @endphp
                                <div class="relative group uploaded-photo-item" data-index="{{ $index }}">
                                    <a href="{{ $imageUrl }}" target="_blank" class="inline-block w-full">
                                        <img src="{{ $imageUrl }}" 
                                             alt="Foto {{ $index + 1 }}" 
                                             class="w-full h-32 sm:h-40 object-cover rounded-lg border border-gray-200 hover:border-teal-400 hover:shadow-lg transition-all cursor-pointer"
                                             onerror="this.onerror=null; this.src='{{ asset('images/gulungan-terpal.png') }}';">
                                    </a>
                                    <button type="button" class="delete-photo absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition" data-index="{{ $index }}" data-invoice-id="{{ $invoice->id }}">
                                        ✕
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    
                    @if(!$invoice->quality_proof_photo || count(json_decode($invoice->quality_proof_photo, true) ?? []) < 1)
                        <div class="mb-3 text-xs text-yellow-700 bg-yellow-100 p-2 rounded">
                            ⚠️ Minimal 1 foto harus diupload sebelum bisa menyiapkan barang
                        </div>
                    @endif
                    
                    <form action="{{ url('/admin/gudang-transaksi/finalize/' . $invoice->id) }}" method="POST" id="finalizeForm">
                        @csrf
                        <button type="submit" id="finalizeBtn" class="w-full sm:w-auto text-center bg-teal-600 hover:bg-teal-700 text-white text-xs sm:text-sm font-semibold px-4 py-2 rounded-md transition {{ (!$invoice->quality_proof_photo || count(json_decode($invoice->quality_proof_photo, true) ?? []) < 1) ? 'opacity-50 cursor-not-allowed' : '' }}" {{ (!$invoice->quality_proof_photo || count(json_decode($invoice->quality_proof_photo, true) ?? []) < 1) ? 'disabled' : '' }}>
                            Siapkan Barang
                        </button>
                    </form>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 text-xs sm:text-sm text-gray-700 mb-3 sm:mb-4">
                    <div><strong>Nama:</strong> {{ $invoice->gudang->name }}</div>
                    <div><strong>Email:</strong> {{ $invoice->gudang->email }}</div>
                    <div><strong>Status:</strong> {{ $invoice->gudang->active ? 'Aktif' : 'Tidak Aktif' }}</div>
                </div>
                @if($invoice->quality_proof_photo)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                        <h3 class="text-sm sm:text-base font-semibold text-green-800 mb-2">✅ Foto Bukti Kualitas Sudah Diupload</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-3">
                            @php
                                $photos = json_decode($invoice->quality_proof_photo, true);
                                
                                if (!is_array($photos)) {
                                    $photos = [$invoice->quality_proof_photo];
                                }
                            @endphp
                            @foreach($photos as $index => $proofPath)
                                @php
                                    $imageUrl = null;
                                    
                                    if ($proofPath) {
                                        $cleanPath = ltrim($proofPath, '/');
                                        $imageUrl = url('/public/storage/' . $cleanPath);
                                    }
                                    
                                    if (!$imageUrl) {
                                        $imageUrl = asset('images/gulungan-terpal.png');
                                    }
                                @endphp
                                <div class="relative group">
                                    <a href="{{ $imageUrl }}" target="_blank" class="inline-block w-full">
                                        <img src="{{ $imageUrl }}" 
                                             alt="Foto Bukti Kualitas {{ $index + 1 }}" 
                                             class="w-full h-32 sm:h-40 object-cover rounded-lg border border-gray-200 hover:border-teal-400 hover:shadow-lg transition-all cursor-pointer"
                                             onerror="this.onerror=null; this.src='{{ asset('images/gulungan-terpal.png') }}'; this.alt='Gambar tidak dapat dimuat'; this.style.border='2px dashed #ccc'; this.style.padding='20px';">
                                    </a>
                                    <div class="absolute top-1 right-1 bg-black/50 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">
                                        Lihat
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500">Klik gambar untuk melihat ukuran penuh ({{ count($photos) }} foto)</p>
                        <p class="text-xs text-gray-600 mt-2">Status: {{ ucfirst($invoice->status) }}</p>
                    </div>
                @endif
            @endif
        </div>

        {{-- Daftar Produk --}}
        <div class="mb-4 sm:mb-6">
            <h2 class="text-base sm:text-lg font-semibold text-teal-700 mb-3 sm:mb-4">Daftar Produk yang Harus Disiapkan</h2>
            @if($cartItems->count() > 0)
                {{-- Desktop Table View --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-sm text-gray-700 border border-gray-300 rounded-md">
                        <thead class="bg-[#D9F2F2] text-gray-800 font-medium">
                            <tr>
                                <th class="px-4 py-2 text-left">No</th>
                                <th class="px-4 py-2 text-left">Nama Produk</th>
                                <th class="px-4 py-2 text-left">Ukuran</th>
                                <th class="px-4 py-2 text-left">Warna</th>
                                <th class="px-4 py-2 text-left">Jumlah</th>
                                <th class="px-4 py-2 text-left">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartItems as $item)
                            <tr class="border-t border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2 font-medium">
                                    @if($item->product_name)
                                        {{ $item->product_name }}
                                    @else
                                        <span class="text-blue-600">Custom Terpal</span>
                                        {{-- Detail Custom Terpal --}}
                                        @if($item->kebutuhan_custom || $item->bahan_custom || $item->ukuran_custom || $item->warna_custom || $item->jumlah_ring_custom || $item->pakai_tali_custom || $item->catatan_custom)
                                        <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded text-xs space-y-1">
                                            @if($item->bahan_custom)
                                            <div><span class="font-medium">Bahan:</span> <span class="font-semibold">{{ $item->bahan_custom }}</span></div>
                                            @endif
                                            @if($item->kebutuhan_custom)
                                            <div><span class="font-medium">Kebutuhan:</span> {{ $item->kebutuhan_custom }}</div>
                                            @endif
                                            @if($item->ukuran_custom)
                                            <div><span class="font-medium">Ukuran:</span> <span class="font-semibold">{{ $item->ukuran_custom }}</span></div>
                                            @endif
                                            @if($item->warna_custom)
                                            <div><span class="font-medium">Warna:</span> {{ $item->warna_custom }}</div>
                                            @endif
                                            @if($item->jumlah_ring_custom)
                                            <div><span class="font-medium">Jumlah Ring:</span> {{ $item->jumlah_ring_custom }} buah</div>
                                            @endif
                                            @if($item->pakai_tali_custom)
                                            <div><span class="font-medium">Tali:</span> 
                                                @if($item->pakai_tali_custom == 'ya' || $item->pakai_tali_custom == '1' || $item->pakai_tali_custom == 1)
                                                    Ya, perlu tali
                                                @elseif($item->pakai_tali_custom == 'tidak' || $item->pakai_tali_custom == '0' || $item->pakai_tali_custom == 0)
                                                    Tidak
                                                @else
                                                    {{ $item->pakai_tali_custom }}
                                                @endif
                                            </div>
                                            @endif
                                            @if($item->catatan_custom)
                                            <div><span class="font-medium">Catatan:</span> {{ $item->catatan_custom }}</div>
                                            @endif
                                        </div>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    @php
                                        $sizeText = '-';
                                        
                                        // Prioritas 1: Jika ada ukuran_custom langsung dari cart/database
                                        if (!empty($item->ukuran_custom)) {
                                            $sizeText = $item->ukuran_custom;
                                        }
                                        // Prioritas 2: Untuk produk custom, coba ekstrak ukuran dari kebutuhan_custom
                                        elseif (!empty($item->kebutuhan_custom)) {
                                            // Coba format (2x3) atau (2 x 3) di awal atau akhir string
                                            if (preg_match('/\(?\s*(\d+)\s*[xX×]\s*(\d+)\s*\)?/', $item->kebutuhan_custom, $matches)) {
                                                $sizeText = $matches[1] . 'x' . $matches[2] . ' m';
                                            } 
                                            // Coba format "ukuran: 2x3" atau "ukuran 2x3"
                                            elseif (preg_match('/ukuran\s*:?\s*(\d+)\s*[xX×]\s*(\d+)/i', $item->kebutuhan_custom, $matches)) {
                                                $sizeText = $matches[1] . 'x' . $matches[2] . ' m';
                                            }
                                            // Coba cari pola angka x angka di mana saja dalam string
                                            elseif (preg_match('/(\d+)\s*[xX×]\s*(\d+)/', $item->kebutuhan_custom, $matches)) {
                                                $sizeText = $matches[1] . 'x' . $matches[2] . ' m';
                                            }
                                            // Jika tidak ada format ukuran yang jelas, tampilkan Custom
                                            else {
                                                $sizeText = 'Custom';
                                            }
                                        } 
                                        // Prioritas 3: Untuk produk regular, gunakan selected_size yang dipilih customer
                                        elseif (!empty($item->selected_size)) {
                                            $sizeText = $item->selected_size;
                                        }
                                    @endphp
                                    <span class="text-gray-700 font-medium">{{ $sizeText }}</span>
                                </td>
                                <td class="px-4 py-2">
                                    @if(isset($item->variant_color) && $item->variant_color)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $item->variant_color }}
                                        </span>
                                    @elseif(isset($item->warna_custom) && $item->warna_custom)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $item->warna_custom }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    <span class="font-semibold text-teal-600">{{ $item->quantity }} pcs</span>
                                </td>
                                <td class="px-4 py-2">
                                    @if(isset($item->kebutuhan_custom) && $item->kebutuhan_custom)
                                        <span class="text-sm text-gray-600">{{ $item->kebutuhan_custom }}</span>
                                    @else
                                        <span class="text-sm text-gray-500">Produk standar</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Mobile Card View --}}
                <div class="lg:hidden divide-y divide-gray-200 mt-3 sm:mt-4">
                    @foreach ($cartItems as $item)
                        <div class="p-4 hover:bg-gray-50 transition">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        @if($item->product_name)
                                            {{ $item->product_name }}
                                        @else
                                            <span class="text-blue-600">Custom Terpal</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">#{{ $loop->iteration }}</p>
                                </div>
                                <span class="inline-block px-2 py-1 rounded text-xs font-semibold bg-teal-100 text-teal-700 ml-2 flex-shrink-0">
                                    {{ $item->quantity }} pcs
                                </span>
                            </div>
                            <div class="space-y-2 text-xs sm:text-sm mb-3">
                                @php
                                    $sizeText = '-';
                                    if (!empty($item->ukuran_custom)) {
                                        $sizeText = $item->ukuran_custom;
                                    } elseif (!empty($item->kebutuhan_custom)) {
                                        if (preg_match('/\(?\s*(\d+)\s*[xX×]\s*(\d+)\s*\)?/', $item->kebutuhan_custom, $matches)) {
                                            $sizeText = $matches[1] . 'x' . $matches[2] . ' m';
                                        } elseif (preg_match('/ukuran\s*:?\s*(\d+)\s*[xX×]\s*(\d+)/i', $item->kebutuhan_custom, $matches)) {
                                            $sizeText = $matches[1] . 'x' . $matches[2] . ' m';
                                        } elseif (preg_match('/(\d+)\s*[xX×]\s*(\d+)/', $item->kebutuhan_custom, $matches)) {
                                            $sizeText = $matches[1] . 'x' . $matches[2] . ' m';
                                        } else {
                                            $sizeText = 'Custom';
                                        }
                                    } elseif (!empty($item->selected_size)) {
                                        $sizeText = $item->selected_size;
                                    }
                                @endphp
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Ukuran:</span>
                                    <span class="text-gray-900 font-medium">{{ $sizeText }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Warna:</span>
                                    @if(isset($item->variant_color) && $item->variant_color)
                                        <span class="inline-block px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">{{ $item->variant_color }}</span>
                                    @elseif(isset($item->warna_custom) && $item->warna_custom)
                                        <span class="inline-block px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">{{ $item->warna_custom }}</span>
                                    @else
                                        <span class="text-gray-900">-</span>
                                    @endif
                                </div>
                                @if($item->kebutuhan_custom)
                                    <div class="pt-2 border-t border-gray-200">
                                        <p class="text-xs text-gray-600 mb-1">Keterangan:</p>
                                        <p class="text-xs text-gray-900 break-words">{{ $item->kebutuhan_custom }}</p>
                                    </div>
                                @endif
                                @if($item->product_name === null && ($item->bahan_custom || $item->ukuran_custom || $item->warna_custom || $item->jumlah_ring_custom || $item->pakai_tali_custom || $item->catatan_custom))
                                    <div class="pt-2 border-t border-gray-200 bg-blue-50 p-2 rounded text-xs space-y-1">
                                        @if($item->bahan_custom)
                                            <div><span class="font-medium">Bahan:</span> <span class="font-semibold">{{ $item->bahan_custom }}</span></div>
                                        @endif
                                        @if($item->jumlah_ring_custom)
                                            <div><span class="font-medium">Jumlah Ring:</span> {{ $item->jumlah_ring_custom }} buah</div>
                                        @endif
                                        @if($item->pakai_tali_custom)
                                            <div><span class="font-medium">Tali:</span> 
                                                @if($item->pakai_tali_custom == 'ya' || $item->pakai_tali_custom == '1' || $item->pakai_tali_custom == 1)
                                                    Ya
                                                @else
                                                    Tidak
                                                @endif
                                            </div>
                                        @endif
                                        @if($item->catatan_custom)
                                            <div><span class="font-medium">Catatan:</span> {{ $item->catatan_custom }}</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 sm:mt-4 p-3 sm:p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2 text-sm"></i>
                        <span class="text-xs sm:text-sm text-blue-800">
                            Total <strong>{{ $cartItems->sum('quantity') }} pcs</strong> produk yang perlu disiapkan
                        </span>
                    </div>
                </div>
            @else
                <div class="text-center py-6 sm:py-8">
                    <i class="fas fa-box-open text-gray-400 text-3xl sm:text-4xl mb-3 sm:mb-4"></i>
                    <p class="text-sm sm:text-base text-gray-500">Belum ada detail produk yang tersedia</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('quality_proof_photo');
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadedPhotos = document.getElementById('uploadedPhotos');
    const uploadMessage = document.getElementById('uploadMessage');
    const finalizeBtn = document.getElementById('finalizeBtn');
    
    @if(!$invoice->gudang)
    const invoiceId = {{ $invoice->id }};
    
    function updateFinalizeButton() {
        if (!finalizeBtn || !uploadedPhotos) return;
        
        const photoCount = uploadedPhotos.querySelectorAll('.uploaded-photo-item').length;
        const warningDiv = document.querySelector('.text-yellow-700.bg-yellow-100');
        
        if (photoCount >= 1) {
            finalizeBtn.disabled = false;
            finalizeBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            if (warningDiv) {
                warningDiv.style.display = 'none';
            }
        } else {
            finalizeBtn.disabled = true;
            finalizeBtn.classList.add('opacity-50', 'cursor-not-allowed');
            if (warningDiv) {
                warningDiv.style.display = 'block';
            }
        }
    }
    
    if (uploadBtn && fileInput) {
        uploadBtn.addEventListener('click', function() {
            if (!fileInput.files || fileInput.files.length === 0) {
                if (uploadMessage) {
                    uploadMessage.innerHTML = '<p class="text-red-500 text-xs">Pilih foto terlebih dahulu</p>';
                }
                return;
            }
            
            const formData = new FormData();
            formData.append('photo', fileInput.files[0]);
            formData.append('_token', '{{ csrf_token() }}');
            
            uploadBtn.disabled = true;
            uploadBtn.textContent = 'Mengupload...';
            if (uploadMessage) {
                uploadMessage.innerHTML = '<p class="text-blue-500 text-xs">Sedang mengupload...</p>';
            }
            
            fetch(`/admin/gudang-transaksi/upload-photo/${invoiceId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const contentType = response.headers.get("content-type");
                let errorData;
                
                if (!response.ok) {
                    if (contentType && contentType.includes("application/json")) {
                        errorData = await response.json();
                        throw new Error(errorData.message || errorData.error || 'Upload gagal');
                    } else {
                        const text = await response.text();
                        throw new Error(`Upload gagal: ${response.status} ${response.statusText}`);
                    }
                }
                
                if (contentType && contentType.includes("application/json")) {
                    return response.json();
                } else {
                    const text = await response.text();
                    throw new Error('Response tidak valid');
                }
            })
            .then(data => {
                if (data.success) {
                    if (uploadMessage) {
                        uploadMessage.innerHTML = '<p class="text-green-500 text-xs">✓ Foto berhasil diupload</p>';
                    }
                    
                    const photoDiv = document.createElement('div');
                    photoDiv.className = 'relative group uploaded-photo-item';
                    photoDiv.setAttribute('data-index', data.total_photos - 1);
                    photoDiv.innerHTML = `
                        <a href="${data.url}" target="_blank" class="inline-block w-full">
                            <img src="${data.url}" 
                                 alt="Foto ${data.total_photos}" 
                                 class="w-full h-32 sm:h-40 object-cover rounded-lg border border-gray-200 hover:border-teal-400 hover:shadow-lg transition-all cursor-pointer"
                                 onerror="this.onerror=null; this.src='{{ asset('images/gulungan-terpal.png') }}';">
                        </a>
                        <button type="button" class="delete-photo absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition" data-index="${data.total_photos - 1}" data-invoice-id="${invoiceId}">
                            ✕
                        </button>
                    `;
                    if (uploadedPhotos) {
                        uploadedPhotos.appendChild(photoDiv);
                    }
                    
                    fileInput.value = '';
                    updateFinalizeButton();
                    
                    setTimeout(() => {
                        if (uploadMessage) {
                            uploadMessage.innerHTML = '';
                        }
                    }, 3000);
                } else {
                    if (uploadMessage) {
                        uploadMessage.innerHTML = `<p class="text-red-500 text-xs">${data.message || 'Gagal mengupload foto'}</p>`;
                    }
                }
            })
            .catch(error => {
                if (uploadMessage) {
                    uploadMessage.innerHTML = `<p class="text-red-500 text-xs">${error.message || 'Terjadi kesalahan saat mengupload'}</p>`;
                }
                console.error('Upload Error:', error);
                console.error('Error Details:', {
                    message: error.message,
                    stack: error.stack,
                    name: error.name
                });
            })
            .finally(() => {
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Unggah';
            });
        });
    }
    
    if (uploadedPhotos) {
        uploadedPhotos.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-photo') || e.target.closest('.delete-photo')) {
                const deleteBtn = e.target.classList.contains('delete-photo') ? e.target : e.target.closest('.delete-photo');
                if (!deleteBtn) return;
                
                const photoIndex = deleteBtn.getAttribute('data-index');
                const invoiceIdBtn = deleteBtn.getAttribute('data-invoice-id');
                const photoItem = deleteBtn.closest('.uploaded-photo-item');
                
                if (!confirm('Yakin ingin menghapus foto ini?')) {
                    return;
                }
                
                deleteBtn.disabled = true;
                deleteBtn.textContent = '...';
                
                fetch(`/admin/gudang-transaksi/delete-photo/${invoiceIdBtn}/${photoIndex}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Hapus gagal');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        if (photoItem) {
                            photoItem.remove();
                        }
                        updateFinalizeButton();
                        
                        if (data.total_photos === 0) {
                            if (uploadMessage) {
                                uploadMessage.innerHTML = '<p class="text-yellow-500 text-xs">⚠️ Minimal 1 foto harus diupload</p>';
                            }
                        } else {
                            if (uploadMessage) {
                                uploadMessage.innerHTML = '<p class="text-green-500 text-xs">✓ Foto berhasil dihapus</p>';
                            }
                            setTimeout(() => {
                                if (uploadMessage) {
                                    uploadMessage.innerHTML = '';
                                }
                            }, 3000);
                        }
                    } else {
                        if (uploadMessage) {
                            uploadMessage.innerHTML = `<p class="text-red-500 text-xs">${data.message || 'Gagal menghapus foto'}</p>`;
                        }
                    }
                })
                .catch(error => {
                    if (uploadMessage) {
                        uploadMessage.innerHTML = `<p class="text-red-500 text-xs">${error.message || 'Terjadi kesalahan saat menghapus'}</p>`;
                    }
                    console.error('Error:', error);
                })
                .finally(() => {
                    deleteBtn.disabled = false;
                    deleteBtn.textContent = '✕';
                });
            }
        });
    }
    
    updateFinalizeButton();
    @endif
});
</script>
@endpush
@endsection

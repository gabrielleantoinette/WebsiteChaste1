@extends('layouts.admin')

@section('content')
    @php
        $paymentMethod = $invoice->payments->first()->method ?? null;
        $paymentType = $invoice->payments->first()->type ?? null;
        $isCOD = $paymentMethod === 'cod';
        $isDP = $paymentMethod === 'midtrans' && $paymentType === 'dp';
        $remainingAmount = $invoice->remaining_amount ?? 0;
        $dpAmount = $invoice->dp_amount ?? 0;
        $dpPaidAt = $invoice->dp_paid_at;
        $remainingPaidAt = $invoice->remaining_paid_at;
    @endphp
    <div class="py-4 sm:py-5 lg:py-6 px-4 sm:px-0">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-3 sm:gap-0">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Detail Transaksi Kurir</h1>
            <a href="{{ url('/admin/driver-transaksi') }}" class="w-full sm:w-auto text-center bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-xs sm:text-sm transition">
                ← Kembali ke Daftar Transaksi
            </a>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-3 sm:px-4 py-2 sm:py-3 rounded mb-3 sm:mb-4">
                <span class="text-xs sm:text-sm">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-5 lg:p-6 shadow-sm mb-4 sm:mb-6">
            <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3 sm:mb-4">Informasi Invoice</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div>
                    <p class="text-xs sm:text-sm text-gray-500 mb-1">Invoice ID</p>
                    <p class="text-sm sm:text-base font-semibold text-gray-800">{{ $invoice->id }}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-gray-500 mb-1">Kode Invoice</p>
                    <p class="text-sm sm:text-base font-semibold text-gray-800">{{ $invoice->code }}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-gray-500 mb-1">Status</p>
                    <span class="inline-block px-2 sm:px-3 py-1 rounded-full text-xs font-semibold 
                        @if($invoice->status == 'dikirim') bg-teal-100 text-teal-700
                        @elseif($invoice->status == 'sampai') bg-green-100 text-green-700
                        @else bg-gray-100 text-gray-700
                        @endif">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
                @if($isCOD)
                <div class="sm:col-span-2 bg-red-50 border-2 border-red-300 rounded-lg p-3 sm:p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <p class="text-sm sm:text-base font-bold text-red-800">💰 PESANAN COD - HARUS MENAGIH UANG</p>
                    </div>
                    <p class="text-xs sm:text-sm text-red-700 mb-1">Pembayaran dilakukan saat pengiriman ke customer</p>
                    <p class="text-base sm:text-lg font-bold text-red-900">Total yang Harus Ditagih: Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</p>
                </div>
                @elseif($isDP && $dpPaidAt && $remainingAmount > 0 && !$remainingPaidAt)
                <div class="sm:col-span-2 bg-red-50 border-2 border-red-300 rounded-lg p-3 sm:p-4">
                    <div class="flex items-center gap-2 mb-2">
                        {{-- <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg> --}}
                        <p class="text-sm sm:text-base font-bold text-red-800"> PESANAN DP - HARUS MENAGIH SISA PEMBAYARAN</p>
                    </div>
                    <p class="text-xs sm:text-sm text-red-700 mb-2">Pembayaran sisa dilakukan saat pengiriman ke customer</p>
                    <div class="space-y-1.5 text-xs sm:text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-red-700">Total Pesanan:</span>
                            <span class="font-semibold text-red-900">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-red-700">DP (50%) sudah dibayar:</span>
                            <span class="font-semibold text-green-700">Rp {{ number_format($dpAmount, 0, ',', '.') }} ✓</span>
                        </div>
                        <div class="pt-1.5 border-t border-red-300">
                            <div class="flex justify-between items-center">
                                <span class="text-red-800 font-medium">Sisa yang harus ditagih:</span>
                                <span class="text-base sm:text-lg font-bold text-red-900">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif($isDP && $dpPaidAt && $remainingPaidAt)
                <div class="sm:col-span-2 bg-green-50 border-2 border-green-300 rounded-lg p-3 sm:p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm sm:text-base font-bold text-green-800">✅ PESANAN DP - SUDAH LUNAS</p>
                    </div>
                    <div class="space-y-2 text-xs sm:text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-green-700">Total Pesanan:</span>
                            <span class="font-semibold text-green-900">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-green-700">DP (50%) sudah dibayar:</span>
                            <span class="font-semibold text-green-700">Rp {{ number_format($dpAmount, 0, ',', '.') }} ✓</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-green-700">Sisa sudah dibayar:</span>
                            <span class="font-semibold text-green-700">Rp {{ number_format($remainingAmount, 0, ',', '.') }} ✓</span>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm text-green-700 mt-2">Pembayaran sudah lengkap</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-gray-500">Tanggal Jatuh Tempo</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->due_date ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal Penerimaan</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->receive_date ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-5 lg:p-6 shadow-sm mb-4 sm:mb-6">
            <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3 sm:mb-4">Informasi Customer</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div>
                    <p class="text-xs sm:text-sm text-gray-500 mb-1">Nama Customer</p>
                    <p class="text-sm sm:text-base font-semibold text-gray-800 truncate">{{ $invoice->customer->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-gray-500 mb-1">Telepon</p>
                    <p class="text-sm sm:text-base font-semibold text-gray-800">{{ $invoice->customer->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-gray-500 mb-1">Email</p>
                    <p class="text-sm sm:text-base font-semibold text-gray-800 truncate">{{ $invoice->customer->email ?? '-' }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs sm:text-sm text-gray-500 mb-1">Alamat Pengiriman</p>
                    <p class="text-sm sm:text-base font-semibold text-gray-800 break-words">{{ $invoice->address ?? '-' }}</p>
                </div>
            </div>
        </div>

        @if($invoice->gudang)
        <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-5 lg:p-6 shadow-sm mb-4 sm:mb-6">
            <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3 sm:mb-4">Staff Gudang</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div>
                    <p class="text-sm text-gray-500">Nama Staff</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->gudang->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->gudang->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold 
                        @if($invoice->gudang->active) bg-green-100 text-green-700
                        @else bg-red-100 text-red-700
                        @endif">
                        {{ $invoice->gudang->active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
            </div>
        </div>
        @endif

        @if($invoice->details && $invoice->details->count() > 0)
        <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-5 lg:p-6 shadow-sm mb-4 sm:mb-6">
            <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3 sm:mb-4">Detail Produk</h2>
            {{-- Desktop Table View --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full table-auto text-sm border border-gray-200">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">No</th>
                            <th class="px-4 py-3 text-left font-semibold">Nama Produk</th>
                            <th class="px-4 py-3 text-left font-semibold">Warna</th>
                            @if($isCOD || ($isDP && $dpPaidAt && $remainingAmount > 0 && !$remainingPaidAt))
                            <th class="px-4 py-3 text-left font-semibold">Harga</th>
                            @endif
                            <th class="px-4 py-3 text-left font-semibold">Jumlah</th>
                            @if($isCOD || ($isDP && $dpPaidAt && $remainingAmount > 0 && !$remainingPaidAt))
                            <th class="px-4 py-3 text-left font-semibold">Subtotal</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->details as $detail)
                            <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif">
                                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">{{ $detail->product->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $detail->variant->color ?? '-' }}</td>
                                @if($isCOD || ($isDP && $dpPaidAt && $remainingAmount > 0 && !$remainingPaidAt))
                                <td class="px-4 py-3">Rp {{ number_format($detail->price ?? 0, 0, ',', '.') }}</td>
                                @endif
                                <td class="px-4 py-3">{{ $detail->quantity ?? 0 }}</td>
                                @if($isCOD || ($isDP && $dpPaidAt && $remainingAmount > 0 && !$remainingPaidAt))
                                <td class="px-4 py-3">Rp {{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Mobile Card View --}}
            <div class="lg:hidden divide-y divide-gray-200">
                @foreach ($invoice->details as $detail)
                    <div class="p-4 hover:bg-gray-50 transition">
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-sm font-medium text-gray-900 truncate flex-1 mr-2">{{ $detail->product->name ?? '-' }}</p>
                            <span class="text-xs text-gray-500 flex-shrink-0">#{{ $loop->iteration }}</span>
                        </div>
                        <div class="space-y-2 text-xs sm:text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Warna:</span>
                                <span class="text-gray-900">{{ $detail->variant->color ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jumlah:</span>
                                <span class="text-gray-900 font-semibold">{{ $detail->quantity ?? 0 }}</span>
                            </div>
                            @if($isCOD || ($isDP && $dpPaidAt && $remainingAmount > 0 && !$remainingPaidAt))
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Harga:</span>
                                    <span class="text-gray-900">Rp {{ number_format($detail->price ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-200">
                                    <span class="text-gray-600 font-medium">Subtotal:</span>
                                    <span class="text-gray-900 font-semibold">Rp {{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if($isCOD)
            <div class="mt-4 text-right pt-4 border-t border-gray-200">
                <p class="text-base sm:text-lg font-bold text-red-800">Total yang Harus Ditagih: Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</p>
            </div>
            @endif
        </div>
        @endif

        @if ($invoice->status === 'dikirim_ke_agen')
        <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-5 lg:p-6 shadow-sm mb-4 sm:mb-6">
            <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3 sm:mb-4">Input Nomor Resi Ekspedisi</h2>
            <p class="text-xs sm:text-sm text-gray-600 mb-3 sm:mb-4">Pesanan ini menggunakan ekspedisi {{ ucfirst($invoice->shipping_courier) }}. Silakan input nomor resi setelah mengirim ke agen.</p>
            <form method="POST" action="{{ url('/admin/driver-transaksi/finish/' . $invoice->id) }}" class="space-y-3 sm:space-y-4">
                @csrf
                <div>
                    <label for="tracking_number" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nomor Resi <span class="text-red-500">*</span></label>
                    <input type="text" name="tracking_number" id="tracking_number" required
                           class="border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-sm w-full focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                           placeholder="Masukkan nomor resi ekspedisi">
                </div>
                <button type="submit" class="w-full sm:w-auto text-center bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700 transition text-sm sm:text-base">
                    Simpan Nomor Resi
                </button>
            </form>
        </div>
        @endif

        @if ($invoice->status === 'dikirim')
        <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-5 lg:p-6 shadow-sm">
            <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3 sm:mb-4">Upload Bukti Pengiriman</h2>
            @if($invoice->tracking_number)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                <p class="text-xs sm:text-sm text-blue-800">
                    <strong>Nomor Resi:</strong> {{ $invoice->tracking_number }}<br>
                    <strong>Ekspedisi:</strong> {{ ucfirst($invoice->shipping_courier) }}
                </p>
            </div>
            @endif
            <form action="{{ url('/admin/invoices/upload-bukti/' . $invoice->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3 sm:space-y-4">
                @csrf
                <div>
                    <label for="photo" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Foto Bukti Kirim:</label>
                    <input type="file" name="photo" accept="image/*" class="border border-gray-300 p-2 rounded w-full text-xs sm:text-sm">
                </div>
                <div>
                    <label for="signature" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Tanda Tangan Penerima:</label>
                    <input type="file" name="signature" accept="image/*" class="border border-gray-300 p-2 rounded w-full text-xs sm:text-sm">
                </div>
                <button type="submit" class="w-full sm:w-auto text-center bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700 transition text-sm sm:text-base">
                    Upload Bukti
                </button>
            </form>

            @if ($invoice->delivery_proof_photo || $invoice->delivery_signature)
                <div class="mt-4 sm:mt-6">
                    <h3 class="text-sm sm:text-base font-semibold mb-2 sm:mb-3">Bukti yang Sudah Diunggah:</h3>
                    @if ($invoice->delivery_proof_photo)
                        <div class="mb-3 sm:mb-4">
                            <p class="text-xs sm:text-sm font-medium mb-2">Foto:</p>
                            @php
                                $proofPath = $invoice->delivery_proof_photo;
                                $imageUrl = null;
                                
                                if ($proofPath) {
                                    $cleanPath = ltrim($proofPath, '/');
                                    $imageUrl = url('/public/storage/' . $cleanPath);
                                }
                                
                                if (!$imageUrl) {
                                    $imageUrl = asset('images/gulungan-terpal.png');
                                }
                            @endphp
                            <a href="{{ $imageUrl }}" target="_blank" class="inline-block">
                                <img src="{{ $imageUrl }}" alt="Foto Bukti Kirim" class="w-full sm:w-64 h-auto sm:h-64 object-cover border rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                            </a>
                        </div>
                    @endif
                    @if ($invoice->delivery_signature)
                        <div class="mb-3 sm:mb-4">
                            <p class="text-xs sm:text-sm font-medium mb-2">Tanda Tangan:</p>
                            @php
                                $signaturePath = $invoice->delivery_signature;
                                $signatureUrl = null;
                                
                                if ($signaturePath) {
                                    $cleanPath = ltrim($signaturePath, '/');
                                    $signatureUrl = url('/public/storage/' . $cleanPath);
                                }
                                
                                if (!$signatureUrl) {
                                    $signatureUrl = asset('images/gulungan-terpal.png');
                                }
                            @endphp
                            <a href="{{ $signatureUrl }}" target="_blank" class="inline-block">
                                <img src="{{ $signatureUrl }}" alt="Tanda Tangan" class="w-full sm:w-64 h-auto sm:h-64 object-cover border rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                            </a>
                        </div>
                    @endif
                </div>
                @if ($invoice->delivery_proof_photo && $invoice->delivery_signature)
                    <form method="POST" action="{{ url('/admin/driver-transaksi/finish/' . $invoice->id) }}" class="mt-4 sm:mt-6">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto text-center bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition text-sm sm:text-base">
                            Tandai Selesai / Sampai
                        </button>
                    </form>
                @endif
            @endif
        </div>
        @endif
    </div>
@endsection

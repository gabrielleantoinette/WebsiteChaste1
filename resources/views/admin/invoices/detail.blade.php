@extends('layouts.admin')

@section('content')
<div class="py-6">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
    <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Detail Invoice</h1>
            <p class="text-gray-600">Informasi lengkap transaksi dan detail produk</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.invoices.view') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali
            </a>
            <a href="{{ url('/admin/invoices/export-pdf?id=' . $invoice->id) }}" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Download PDF
            </a>
        </div>
    </div>

    {{-- Status Badge --}}
    <div class="mb-6">
        @php
            $statusColors = [
                'Menunggu Pembayaran' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                'Menunggu Konfirmasi Pembayaran' => 'bg-orange-100 text-orange-800 border-orange-200',
                'Dikemas' => 'bg-blue-100 text-blue-800 border-blue-200',
                'Dikirim' => 'bg-purple-100 text-purple-800 border-purple-200',
                'Dikirim ke Agen' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                'Selesai' => 'bg-green-100 text-green-800 border-green-200',
                'Dibatalkan' => 'bg-red-100 text-red-800 border-red-200',
            ];
            $statusColor = $statusColors[$invoice->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
        @endphp
        <span class="inline-flex items-center px-4 py-2 rounded-lg border font-semibold {{ $statusColor }}">
            {{ $invoice->status }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri - Info Utama --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Info Invoice --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-teal-100 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-teal-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h11.25c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Informasi Invoice</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <p class="text-sm text-gray-500">Kode Invoice</p>
                        <p class="font-mono font-semibold text-gray-900 text-lg">{{ $invoice->code }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-500">Invoice ID</p>
                        <p class="font-semibold text-gray-900">#{{ $invoice->id }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-500">Tanggal Dibuat</p>
                        <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($invoice->created_at)->translatedFormat('d F Y, H:i') }}</p>
                    </div>
                    @if($invoice->due_date)
                    <div class="space-y-1">
                        <p class="text-sm text-gray-500">Tanggal Jatuh Tempo</p>
                        <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($invoice->due_date)->translatedFormat('d F Y') }}</p>
                    </div>
                    @endif
                    <div class="space-y-1">
                        <p class="text-sm text-gray-500">Jenis Pembelian</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $invoice->is_online ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ $invoice->is_online ? 'Online' : 'Offline' }}
                        </span>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-500">Status Pembayaran</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $invoice->is_paid ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $invoice->is_paid ? 'Lunas' : 'Belum Lunas' }}
                        </span>
                    </div>
        </div>
    </div>

    {{-- Daftar Produk --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5m-16.5 0v-1.125c0-.621.504-1.125 1.125-1.125h13.5c.621 0 1.125.504 1.125 1.125V7.5m-16.5 0h16.5" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Daftar Produk</h2>
                </div>
        <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Nama Produk</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Warna</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Ukuran</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Harga</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700">Qty</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Subtotal</th>
                    </tr>
                </thead>
                        <tbody class="divide-y divide-gray-200">
                    @foreach ($invoice->details as $detail)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-gray-600">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $detail->product->name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $detail->variant->color ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-600">
                                @php
                                    $sizeText = '-';
                                    if (!empty($detail->kebutuhan_custom) && preg_match('/\((\d+)x(\d+)\)/', $detail->kebutuhan_custom, $m)) {
                                        $sizeText = $m[1] . 'x' . $m[2];
                                            } elseif($detail->selected_size) {
                                                $sizeText = $detail->selected_size;
                                    }
                                @endphp
                                {{ $sizeText }}
                            </td>
                                    <td class="px-4 py-3 text-right text-gray-900">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center text-gray-900">{{ $detail->quantity }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

                <div class="mt-6 pt-6 border-t border-gray-200 space-y-2">
                    <div class="flex justify-between text-base text-gray-700">
                <span class="font-medium">Subtotal Produk:</span> 
                        <span class="font-semibold">Rp {{ number_format($invoice->grand_total - ($invoice->shipping_cost ?? 0), 0, ',', '.') }}</span>
            </div>
            @if($invoice->shipping_cost > 0)
                    <div class="flex justify-between text-base text-gray-700">
                        <span class="font-medium">
                            Ongkos Kirim
                @if($invoice->shipping_courier || $invoice->shipping_service)
                                <span class="text-sm text-gray-500 font-normal">
                        ({{ $invoice->shipping_courier ? ucfirst($invoice->shipping_courier) : 'Kurir Perusahaan' }}
                        @if($invoice->shipping_service)
                            - {{ $invoice->shipping_service }}
                        @endif)
                    </span>
                @endif
                        </span>
                        <span class="font-semibold">Rp {{ number_format($invoice->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($invoice->tracking_number)
                    <div class="flex justify-between text-base text-gray-700">
                        <span class="font-medium">Nomor Resi:</span>
                        <span class="font-mono font-semibold text-blue-600">{{ $invoice->tracking_number }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t border-gray-300">
                        <span>Total:</span>
                        <span class="text-teal-600">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan - Info Tambahan --}}
        <div class="space-y-6">
            {{-- Info Customer --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-purple-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-800">Customer</h2>
                </div>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Nama</p>
                        <p class="font-semibold text-gray-900">{{ $invoice->customer->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Email</p>
                        <p class="text-gray-900">{{ $invoice->customer->email }}</p>
                    </div>
                    @if($invoice->customer->phone)
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Telepon</p>
                        <p class="text-gray-900">{{ $invoice->customer->phone }}</p>
                    </div>
                    @endif
                    @if($invoice->address)
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Alamat Pengiriman</p>
                        <p class="text-gray-900 text-sm">{{ $invoice->address }}</p>
            </div>
            @endif
                </div>
            </div>

            {{-- Info Pegawai --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-green-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-800">Pegawai</h2>
                </div>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Nama</p>
                        <p class="font-semibold text-gray-900">{{ $invoice->employee->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Email</p>
                        <p class="text-gray-900">{{ $invoice->employee->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Status</p>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $invoice->employee->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $invoice->employee->active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.admin')

@section('content')

    {{-- Header dengan Gradient Background --}}
    <div class="relative bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl mb-4 sm:mb-6 lg:mb-8 overflow-visible">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-4 sm:px-6 lg:px-8 py-4 sm:py-5 lg:py-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mb-1 sm:mb-2">💰 Daftar Penjualan</h1>
                    <p class="text-sm sm:text-base text-teal-100">Kelola transaksi dan penjualan Anda</p>
                </div>
                <div class="flex gap-3 w-full sm:w-auto">
            @if (Session::get('user')->role !== 'owner')
                <a href="{{ url('/admin/invoices/create-customer') }}"
                           class="inline-flex items-center gap-2 px-4 sm:px-6 py-2 sm:py-3 bg-white/20 backdrop-blur-sm text-white text-sm sm:text-base font-medium rounded-lg hover:bg-white/30 transition-all duration-300 shadow-lg border border-white/20 w-full sm:w-auto justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                                 stroke-width="2" stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                    <span class="hidden sm:inline">+ Tambah Transaksi Toko</span>
                    <span class="sm:hidden">+ Tambah Transaksi</span>
                </a>
            @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 lg:gap-6 mb-4 sm:mb-6 lg:mb-8">
        <div class="bg-white rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Total Transaksi</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">{{ $totalInvoices }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-teal-100 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Transaksi Selesai</p>
                    <p class="text-xl sm:text-2xl font-bold text-emerald-600 truncate">{{ $completedInvoices }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-emerald-100 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Transaksi Pending</p>
                    <p class="text-xl sm:text-2xl font-bold text-yellow-600 truncate">{{ $pendingInvoices }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-yellow-100 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Total Penjualan</p>
                    <p class="text-lg sm:text-xl lg:text-2xl font-bold text-green-600 break-words">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-green-100 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Search dan Filter dengan Card --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 sm:p-5 lg:p-6 mb-4 sm:mb-6 lg:mb-8">
        <form method="GET" action="{{ route('admin.invoices.view') }}" class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4 w-full lg:w-auto">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                    </svg>
                    <label for="status" class="text-xs sm:text-sm font-semibold text-gray-700">Filter Status:</label>
                </div>
                <select name="status" id="status" onchange="this.form.submit()" class="px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 bg-white shadow-sm w-full sm:w-auto text-sm">
                    <option value="">Semua Status</option>
                    <option value="menunggu_pembayaran" {{ request('status') == 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="dikemas" {{ request('status') == 'dikemas' ? 'selected' : '' }}>Dikemas</option>
                    <option value="dikirim" {{ request('status') == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full lg:w-auto">
                <div class="relative flex-1 sm:flex-none">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" placeholder="Cari kode/nama customer..." value="{{ request('search') }}" class="pl-10 pr-4 py-2 w-full sm:w-64 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 shadow-sm text-sm" />
                </div>
                <button type="submit" class="px-4 sm:px-6 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-sm sm:text-base font-medium rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all duration-200 shadow-lg hover:shadow-xl w-full sm:w-auto">Cari</button>
                @if(request('search') || request('status'))
                    <a href="{{ url('/admin/invoices') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm sm:text-base font-medium rounded-lg hover:bg-gray-200 transition-all duration-200 w-full sm:w-auto text-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel dengan Card Layout --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Data Penjualan
            </h3>
        </div>
        
        {{-- Desktop Table View --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Code</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Admin</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Driver</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Gudang</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keuangan</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Grand Total</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Alamat Pengiriman</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis Pembelian</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
            </tr>
        </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($invoices as $invoice)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ ($invoices->currentPage() - 1) * $invoices->perPage() + $loop->iteration }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-medium text-teal-600">{{ $invoice->code }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-900">{{ $invoice->customer->name }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-600">{{ $invoice->employee->name }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-600">{{ $invoice->driver ? $invoice->driver->name : '-' }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-600">{{ $invoice->gudang ? $invoice->gudang->name : '-' }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-600">{{ $invoice->accountant ? $invoice->accountant->name : '-' }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-semibold text-emerald-600">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-600">{{ Str::limit($invoice->address, 30) }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'Menunggu Pembayaran' => 'bg-yellow-100 text-yellow-800',
                                        'Menunggu Konfirmasi Pembayaran' => 'bg-orange-100 text-orange-800',
                                        'Pembayaran Ditolak' => 'bg-red-100 text-red-800',
                                        'Dikemas' => 'bg-blue-100 text-blue-800',
                                        'Dikirim' => 'bg-purple-100 text-purple-800',
                                        'Selesai' => 'bg-emerald-100 text-emerald-800',
                                    ];
                                    $statusColor = $statusColors[$invoice->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                    {{ $invoice->status }}
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $invoice->is_online ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $invoice->is_online ? 'Online' : 'Offline' }}
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ url('/admin/invoices/detail/' . $invoice->id) }}" 
                                   class="inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-xs sm:text-sm font-medium rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Detail
                        </a>
                    </td>
                </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-4 sm:px-6 py-8 sm:py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Tidak ada data penjualan</h3>
                                    <p class="text-sm sm:text-base text-gray-500 mb-4">Mulai dengan menambahkan transaksi baru</p>
                                    @if (Session::get('user')->role !== 'owner')
                                        <a href="{{ url('/admin/invoices/create-customer') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-sm sm:text-base font-medium rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all duration-200 shadow-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            Tambah Transaksi
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
        </tbody>
    </table>
        </div>
        {{-- Mobile Card View --}}
        <div class="lg:hidden divide-y divide-gray-200">
            @forelse ($invoices as $invoice)
                @php
                    $statusColors = [
                        'Menunggu Pembayaran' => 'bg-yellow-100 text-yellow-800',
                        'Menunggu Konfirmasi Pembayaran' => 'bg-orange-100 text-orange-800',
                        'Pembayaran Ditolak' => 'bg-red-100 text-red-800',
                        'Dikemas' => 'bg-blue-100 text-blue-800',
                        'Dikirim' => 'bg-purple-100 text-purple-800',
                        'Selesai' => 'bg-emerald-100 text-emerald-800',
                    ];
                    $statusColor = $statusColors[$invoice->status] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-mono font-semibold text-teal-600 truncate">{{ $invoice->code }}</p>
                            <p class="text-sm text-gray-900 truncate">{{ $invoice->customer->name }}</p>
                            <p class="text-xs text-gray-600 mt-1">ID: {{ ($invoices->currentPage() - 1) * $invoices->perPage() + $loop->iteration }}</p>
                        </div>
                        <div class="flex flex-col gap-2 ml-3 flex-shrink-0">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                {{ Str::limit($invoice->status, 15) }}
                            </span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                {{ $invoice->is_online ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $invoice->is_online ? 'Online' : 'Offline' }}
                            </span>
                        </div>
                    </div>
                    <div class="space-y-2 mb-3">
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600">Admin:</span>
                            <span class="text-gray-900 font-medium">{{ $invoice->employee->name }}</span>
                        </div>
                        @if($invoice->driver)
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600">Driver:</span>
                            <span class="text-gray-900 font-medium">{{ $invoice->driver->name }}</span>
                        </div>
                        @endif
                        @if($invoice->gudang)
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600">Gudang:</span>
                            <span class="text-gray-900 font-medium">{{ $invoice->gudang->name }}</span>
                        </div>
                        @endif
                        @if($invoice->accountant)
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600">Keuangan:</span>
                            <span class="text-gray-900 font-medium">{{ $invoice->accountant->name }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600">Total:</span>
                            <span class="text-emerald-600 font-semibold">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</span>
                        </div>
                        <div class="text-xs sm:text-sm">
                            <span class="text-gray-600">Alamat:</span>
                            <p class="text-gray-900 break-words">{{ Str::limit($invoice->address, 100) }}</p>
                        </div>
                    </div>
                    <a href="{{ url('/admin/invoices/detail/' . $invoice->id) }}" 
                       class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-sm font-medium rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all duration-200 shadow-md hover:shadow-lg w-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Detail
                    </a>
                </div>
            @empty
                <div class="px-4 py-8 sm:py-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Tidak ada data penjualan</h3>
                        <p class="text-sm sm:text-base text-gray-500 mb-4">Mulai dengan menambahkan transaksi baru</p>
                        @if (Session::get('user')->role !== 'owner')
                            <a href="{{ url('/admin/invoices/create-customer') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-sm sm:text-base font-medium rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all duration-200 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Tambah Transaksi
                            </a>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    @if($invoices->hasPages())
    <div class="mt-4 sm:mt-6 lg:mt-8 flex justify-center">
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 p-2 sm:p-4">
            {{ $invoices->links() }}
        </div>
    </div>
    @endif

@endsection
@extends('layouts.admin')

@section('content')
    {{-- Header dengan Gradient Background --}}
    <div class="relative bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl mb-4 sm:mb-6 lg:mb-8 overflow-visible">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-4 sm:px-6 lg:px-8 py-4 sm:py-5 lg:py-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mb-1 sm:mb-2">🚚 Atur Kurir</h1>
                    <p class="text-sm sm:text-base text-teal-100">Kelola pengiriman dan pengambilan barang retur</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center w-full sm:w-auto">
                    <div class="text-left sm:text-right text-white">
                        <p class="text-xs sm:text-sm opacity-90">Total Pengiriman</p>
                        <p class="text-base sm:text-lg font-semibold">{{ $pengirimanNormal->count() + $pengirimanRetur->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-100 text-emerald-800 px-4 sm:px-6 py-3 sm:py-4 rounded-xl mb-4 sm:mb-6 border border-emerald-200">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm sm:text-base">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 lg:gap-6 mb-4 sm:mb-6 lg:mb-8">
        <div class="bg-white rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Pengiriman Normal</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">{{ $pengirimanNormal->count() }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-teal-100 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Pengiriman Retur</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">{{ $pengirimanRetur->count() }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-red-100 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m5 3v6a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h2"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Driver Aktif</p>
                    <p class="text-xl sm:text-2xl font-bold text-emerald-600 truncate">{{ \App\Models\Employee::where('role', 'driver')->where('active', true)->count() }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-emerald-100 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Pengiriman Normal Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-4 sm:mb-6 lg:mb-8">
        <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 flex items-center gap-2 flex-wrap">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                </svg>
                Daftar Pengiriman Normal
                <span class="bg-teal-100 text-teal-800 text-xs sm:text-sm font-medium px-2 sm:px-2.5 py-0.5 rounded-full">
                    {{ $pengirimanNormal->count() }}
                </span>
            </h3>
        </div>
        
        @if($pengirimanNormal->count() > 0)
            {{-- Desktop Table View --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Alamat</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Driver</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($pengirimanNormal as $invoice)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                    <div class="text-xs sm:text-sm font-medium text-teal-600">{{ $invoice->code }}</div>
                                    <div class="text-xs sm:text-sm text-gray-500">ID: {{ $invoice->id }}</div>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 truncate">{{ $invoice->customer->name }}</td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-600 truncate">{{ Str::limit($invoice->address, 30) }}</td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-600">{{ $invoice->receive_date ? \Carbon\Carbon::parse($invoice->receive_date)->format('d M Y') : '-' }}</td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $invoice->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 
                                           ($invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-600 truncate">
                                    {{ $invoice->driver ? $invoice->driver->name : 'Belum ditugaskan' }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.assign-driver.create', $invoice->id) }}" 
                                       class="inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-xs sm:text-sm font-medium rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Assign Driver
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Mobile Card View --}}
            <div class="lg:hidden divide-y divide-gray-200">
                @foreach ($pengirimanNormal as $invoice)
                    <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-teal-600 truncate">{{ $invoice->code }}</p>
                                <p class="text-xs text-gray-500">ID: {{ $invoice->id }}</p>
                                <p class="text-sm text-gray-900 truncate mt-1">{{ $invoice->customer->name }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold ml-2 flex-shrink-0
                                {{ $invoice->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 
                                   ($invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </div>
                        <div class="space-y-2 mb-3 text-xs sm:text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Alamat:</span>
                                <span class="text-gray-900 text-right break-words ml-2">{{ Str::limit($invoice->address, 50) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal:</span>
                                <span class="text-gray-900">{{ $invoice->receive_date ? \Carbon\Carbon::parse($invoice->receive_date)->format('d M Y') : '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Driver:</span>
                                <span class="text-gray-900 truncate ml-2">{{ $invoice->driver ? $invoice->driver->name : 'Belum ditugaskan' }}</span>
                            </div>
                        </div>
                        <a href="{{ route('admin.assign-driver.create', $invoice->id) }}" 
                           class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white text-sm font-medium rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all duration-200 shadow-md hover:shadow-lg w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Assign Driver
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-4 sm:px-6 py-8 sm:py-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                        </svg>
                    </div>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Tidak ada pengiriman normal</h3>
                    <p class="text-sm sm:text-base text-gray-500">Pengiriman akan muncul di sini setelah ada transaksi</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Pengiriman Retur Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 flex items-center gap-2 flex-wrap">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m5 3v6a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h2"/>
                </svg>
                Daftar Pengiriman Retur
                <span class="bg-red-100 text-red-800 text-xs sm:text-sm font-medium px-2 sm:px-2.5 py-0.5 rounded-full">
                    {{ $pengirimanRetur->count() }}
                </span>
            </h3>
        </div>
        
        @if($pengirimanRetur->count() > 0)
            {{-- Desktop Table View --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Alamat</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Driver</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($pengirimanRetur as $invoice)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                    <div class="text-xs sm:text-sm font-medium text-red-600">{{ $invoice->code }}</div>
                                    <div class="text-xs sm:text-sm text-gray-500">ID: {{ $invoice->id }}</div>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 truncate">{{ $invoice->customer->name }}</td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-600 truncate">{{ Str::limit($invoice->address, 30) }}</td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-600">{{ $invoice->receive_date ? \Carbon\Carbon::parse($invoice->receive_date)->format('d M Y') : '-' }}</td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        Retur
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-600 truncate">
                                    {{ $invoice->driver ? $invoice->driver->name : 'Belum ditugaskan' }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.assign-driver.create', $invoice->id) }}" 
                                       class="inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs sm:text-sm font-medium rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Assign Driver
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Mobile Card View --}}
            <div class="lg:hidden divide-y divide-gray-200">
                @foreach ($pengirimanRetur as $invoice)
                    <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-red-600 truncate">{{ $invoice->code }}</p>
                                <p class="text-xs text-gray-500">ID: {{ $invoice->id }}</p>
                                <p class="text-sm text-gray-900 truncate mt-1">{{ $invoice->customer->name }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 ml-2 flex-shrink-0">
                                Retur
                            </span>
                        </div>
                        <div class="space-y-2 mb-3 text-xs sm:text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Alamat:</span>
                                <span class="text-gray-900 text-right break-words ml-2">{{ Str::limit($invoice->address, 50) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal:</span>
                                <span class="text-gray-900">{{ $invoice->receive_date ? \Carbon\Carbon::parse($invoice->receive_date)->format('d M Y') : '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Driver:</span>
                                <span class="text-gray-900 truncate ml-2">{{ $invoice->driver ? $invoice->driver->name : 'Belum ditugaskan' }}</span>
                            </div>
                        </div>
                        <a href="{{ route('admin.assign-driver.create', $invoice->id) }}" 
                           class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-medium rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Assign Driver
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-4 sm:px-6 py-8 sm:py-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m5 3v6a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h2"/>
                        </svg>
                    </div>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Tidak ada pengiriman retur</h3>
                    <p class="text-sm sm:text-base text-gray-500">Pengiriman retur akan muncul di sini setelah ada barang retur</p>
                </div>
            </div>
        @endif
    </div>

@endsection
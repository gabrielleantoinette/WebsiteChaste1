@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <x-breadcrumb :items="[
            ['label' => 'Atur Kurir', 'url' => route('admin.assign-driver.index')],
            ['label' => 'Assign Driver']
        ]" />
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden max-w-4xl mx-auto px-4 sm:px-0">
        <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-5 lg:py-6 bg-gradient-to-r from-teal-500 to-teal-600">
            <h1 class="text-xl sm:text-2xl font-bold text-white">Assign Driver</h1>
            <p class="text-sm sm:text-base text-teal-100 mt-1">Pilih driver untuk pengiriman ini</p>
        </div>

        <div class="p-4 sm:p-6 lg:p-8">
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-3 sm:px-4 py-2 sm:py-3 rounded-lg mb-4 sm:mb-6">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs sm:text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{-- Invoice Info --}}
            <div class="bg-gray-50 rounded-lg p-4 sm:p-5 lg:p-6 mb-4 sm:mb-6 border border-gray-200">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Informasi Pesanan</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">Kode Invoice</p>
                        <p class="text-sm sm:text-base font-semibold text-teal-600 truncate">{{ $invoice->code }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">Customer</p>
                        <p class="text-sm sm:text-base font-semibold text-gray-900 truncate">{{ $invoice->customer->name }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs sm:text-sm text-gray-600">Alamat Pengiriman</p>
                        <p class="text-sm sm:text-base text-gray-900 break-words">{{ $invoice->address }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">Status</p>
                        <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-semibold
                            {{ $invoice->status === 'retur_diajukan' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucwords(str_replace('_', ' ', $invoice->status)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">Total</p>
                        <p class="text-sm sm:text-base font-semibold text-gray-900">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Form Assign Driver --}}
            <form action="{{ route('admin.assign-driver.assign', $invoice->id) }}" method="POST">
                @csrf
                
                <div class="mb-4 sm:mb-6">
                    <label for="driver_id" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                        Pilih Driver <span class="text-red-500">*</span>
                    </label>
                    <select name="driver_id" id="driver_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">-- Pilih Driver --</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}" {{ old('driver_id', $invoice->driver_id) == $driver->id ? 'selected' : '' }}>
                                {{ $driver->name }} 
                                @if($driver->phone)
                                    - {{ $driver->phone }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('driver_id')
                        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if($drivers->count() === 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 sm:p-4 mb-4 sm:mb-6">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p class="text-xs sm:text-sm text-yellow-800">Tidak ada driver aktif. Silakan tambahkan driver terlebih dahulu.</p>
                        </div>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 justify-end">
                    <a href="{{ route('admin.assign-driver.index') }}" 
                       class="w-full sm:w-auto text-center px-4 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm sm:text-base font-medium">
                        Batal
                    </a>
                    <button type="submit" 
                            {{ $drivers->count() === 0 ? 'disabled' : '' }}
                            class="w-full sm:w-auto text-center px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-teal-600 to-teal-700 text-white rounded-lg hover:from-teal-700 hover:to-teal-800 transition text-sm sm:text-base font-medium shadow-md hover:shadow-lg {{ $drivers->count() === 0 ? 'opacity-50 cursor-not-allowed' : '' }}">
                        Assign Driver
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

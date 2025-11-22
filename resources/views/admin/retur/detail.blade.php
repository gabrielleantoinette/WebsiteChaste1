@extends('layouts.admin')

@section('content')
<div class="container px-4 sm:px-0 mt-4 sm:mt-5 lg:mt-6">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-3 sm:mb-4">Detail Retur Barang</h2>
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm mt-3 sm:mt-4">
        <div class="p-4 sm:p-5 lg:p-6">
            <div class="space-y-3 sm:space-y-4">
                <div class="border-b border-gray-100 pb-3 sm:pb-4">
                    <p class="text-xs sm:text-sm text-gray-600 mb-1">Nomor Invoice</p>
                    <p class="text-sm sm:text-base font-semibold text-gray-900">{{ $retur->invoice->code ?? '-' }}</p>
                </div>
                <div class="border-b border-gray-100 pb-3 sm:pb-4">
                    <p class="text-xs sm:text-sm text-gray-600 mb-1">Nama Customer</p>
                    <p class="text-sm sm:text-base font-semibold text-gray-900">{{ $retur->customer->name ?? '-' }}</p>
                </div>
                <div class="border-b border-gray-100 pb-3 sm:pb-4">
                    <p class="text-xs sm:text-sm text-gray-600 mb-1">Alasan Retur</p>
                    <p class="text-sm sm:text-base text-gray-900 break-words">{{ $retur->description }}</p>
                </div>
                <div class="border-b border-gray-100 pb-3 sm:pb-4">
                    <p class="text-xs sm:text-sm text-gray-600 mb-2">Media (Foto/Video)</p>
                    @if($retur->media_path)
                        @if(Str::endsWith($retur->media_path, ['.jpg', '.jpeg', '.png', '.gif']))
                            <img src="{{ asset('storage/' . $retur->media_path) }}" alt="Foto Retur" class="max-w-full sm:max-w-xs rounded-lg border border-gray-200">
                        @else
                            <a href="{{ asset('storage/' . $retur->media_path) }}" target="_blank" class="text-teal-600 hover:text-teal-700 text-sm sm:text-base underline">
                                Lihat Media
                            </a>
                        @endif
                    @else
                        <p class="text-sm sm:text-base text-gray-500">-</p>
                    @endif
                </div>
                <div class="border-b border-gray-100 pb-3 sm:pb-4">
                    <p class="text-xs sm:text-sm text-gray-600 mb-1">Kurir Pengambil Retur</p>
                    <p class="text-sm sm:text-base font-semibold text-gray-900">{{ $retur->driver->name ?? '-' }}</p>
                </div>
                <div class="pb-3 sm:pb-4">
                    <p class="text-xs sm:text-sm text-gray-600 mb-1">Status Retur</p>
                    <span class="inline-block px-3 py-1 rounded-full text-xs sm:text-sm font-semibold 
                        @if($retur->status == 'diajukan') bg-yellow-100 text-yellow-800
                        @elseif($retur->status == 'diproses') bg-blue-100 text-blue-800
                        @elseif($retur->status == 'selesai') bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($retur->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <!-- Tombol proses retur ke barang rusak -->
    @if($retur->status == 'diajukan')
    <form action="{{ route('admin.retur.process', $retur->id) }}" method="POST" class="mt-3 sm:mt-4">
        @csrf
        <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg text-sm sm:text-base font-semibold transition">
            Proses ke Barang Rusak
        </button>
    </form>
    @endif
    
    <!-- Link ke halaman stok barang rusak -->
    <div class="mt-3 sm:mt-4">
        <a href="{{ route('admin.retur.damaged-products') }}" class="inline-block w-full sm:w-auto text-center bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg text-sm sm:text-base font-semibold transition">
            Lihat Stok Barang Rusak
        </a>
    </div>
</div>
@endsection 
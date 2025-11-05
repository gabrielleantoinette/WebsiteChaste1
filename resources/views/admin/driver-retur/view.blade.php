@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Daftar Retur Kurir</h1>
    </div>

    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto data-table text-sm border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-[#004d4d] text-gray-700 dark:text-[#ccf2f2]">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Kode Invoice</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Alamat</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal Retur</th>
                        <th class="px-4 py-3 text-left font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($returns as $return)
                        <tr class="border-b border-gray-100 dark:border-gray-700 @if($loop->odd) bg-gray-50 dark:bg-[#2c2c2c] @endif hover:bg-teal-50 dark:hover:bg-[#003333] transition">
                            <td class="px-4 py-3">{{ $return->id }}</td>
                            <td class="px-4 py-3 font-mono text-teal-700 dark:text-[#80C0CE] font-semibold">{{ $return->code }}</td>
                            <td class="px-4 py-3">{{ $return->customer->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold 
                                    @if($return->status == 'retur_diajukan') bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300
                                    @elseif($return->status == 'retur_diambil') bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300
                                    @elseif($return->status == 'retur_selesai') bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300
                                    @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                    @endif">
                                    @if($return->status == 'retur_diajukan')
                                        Diajukan
                                    @elseif($return->status == 'retur_diambil')
                                        Siap Diambil
                                    @elseif($return->status == 'retur_selesai')
                                        Selesai
                                    @else
                                        {{ ucfirst(str_replace('retur_', '', $return->status)) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $return->address ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($return->returns && $return->returns->count() > 0)
                                    {{ $return->returns->first()->created_at ? $return->returns->first()->created_at->format('d M Y H:i') : '-' }}
                                @else
                                    {{ $return->receive_date ? \Carbon\Carbon::parse($return->receive_date)->format('d M Y') : '-' }}
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('driver-retur.detail', $return->id) }}" class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded shadow transition font-semibold text-sm">
                                        Detail
                                    </a>
                                    @if($return->status == 'retur_diambil')
                                        <form method="POST" action="{{ route('driver-retur.pickup', $return->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-block px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow transition font-semibold text-sm"
                                                    onclick="return confirm('Konfirmasi pengambilan retur ini? Pastikan Anda telah mengambil barang retur dari customer.')">
                                                ✓ Ambil
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                Tidak ada retur yang tersedia saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Legend untuk status retur --}}
    <div class="mt-6 bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-4">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Keterangan Status Retur:</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300 mr-3">
                    Diajukan
                </span>
                <span class="text-sm text-gray-600 dark:text-gray-400">Retur telah diajukan oleh customer</span>
            </div>
            <div class="flex items-center">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300 mr-3">
                    Siap Diambil
                </span>
                <span class="text-sm text-gray-600 dark:text-gray-400">Retur siap untuk diambil dari customer</span>
            </div>
            <div class="flex items-center">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 mr-3">
                    Selesai
                </span>
                <span class="text-sm text-gray-600 dark:text-gray-400">Retur telah selesai diambil</span>
            </div>
        </div>
    </div>
@endsection


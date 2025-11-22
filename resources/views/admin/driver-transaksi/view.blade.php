@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-4 sm:mb-6 px-4 sm:px-0">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Transaksi Kurir</h1>
    </div>

    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-4 sm:p-5 lg:p-6 px-4 sm:px-0">
        <h2 class="text-base sm:text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3 sm:mb-4">Daftar Tugas Kurir</h2>
        {{-- Desktop Table View --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full table-auto data-table text-sm border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-[#004d4d] text-gray-700 dark:text-[#ccf2f2]">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Jenis</th>
                        <th class="px-4 py-3 text-left font-semibold">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Driver</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Alamat</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal Diterima</th>
                        <th class="px-4 py-3 text-left font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($allTasks as $task)
                        <tr class="border-b border-gray-100 dark:border-gray-700 @if($loop->odd) bg-gray-50 dark:bg-[#2c2c2c] @endif hover:bg-teal-50 dark:hover:bg-[#003333] transition">
                            <td class="px-4 py-3">{{ $task['id'] }}</td>
                            <td class="px-4 py-3">
                                @if($task['type'] == 'delivery')
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">
                                        📦 Pengiriman
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300">
                                        🔄 Retur
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-teal-700 dark:text-[#80C0CE] font-semibold">{{ $task['code'] }}</td>
                            <td class="px-4 py-3">{{ $task['customer_name'] }}</td>
                            <td class="px-4 py-3">{{ $task['driver_name'] }}</td>
                            <td class="px-4 py-3">
                                @if($task['type'] == 'delivery')
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold 
                                        @if($task['status'] == 'dikirim') bg-teal-100 dark:bg-teal-900 text-teal-700 dark:text-teal-300
                                        @elseif($task['status'] == 'sampai') bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300
                                        @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                        @endif">
                                        {{ ucfirst($task['status']) }}
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold 
                                        @if($task['status'] == 'retur_diajukan') bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300
                                        @elseif($task['status'] == 'retur_diambil') bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300
                                        @elseif($task['status'] == 'retur_selesai') bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300
                                        @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                        @endif">
                                        {{ ucfirst(str_replace('retur_', '', $task['status'])) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $task['address'] }}</td>
                            <td class="px-4 py-3">{{ $task['receive_date'] }}</td>
                            <td class="px-4 py-3">
                                @if($task['type'] == 'delivery')
                                    <a href="{{ route('driver-transaksi.detail', $task['id']) }}" class="inline-block px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded shadow transition font-semibold text-sm">
                                        Detail
                                    </a>
                                @else
                                    <div class="flex gap-2">
                                        <a href="{{ url('/admin/driver-retur/detail/' . $task['id']) }}" class="inline-block px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded shadow transition font-semibold text-sm">
                                            Detail
                                        </a>
                                        @if($task['status'] == 'retur_diambil')
                                            <form method="POST" action="{{ url('/admin/driver-retur/pickup/' . $task['id']) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-block px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow transition font-semibold text-sm"
                                                        onclick="return confirm('Konfirmasi pengambilan retur ini?')">
                                                    Ambil
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                Tidak ada tugas kurir yang tersedia saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Mobile Card View --}}
        <div class="lg:hidden divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($allTasks as $task)
                <div class="p-4 hover:bg-teal-50 dark:hover:bg-[#003333] transition">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-mono font-semibold text-teal-700 dark:text-[#80C0CE] truncate">{{ $task['code'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">ID: {{ $task['id'] }}</p>
                        </div>
                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold ml-2 flex-shrink-0
                            @if($task['type'] == 'delivery') bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300
                            @else bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300
                            @endif">
                            @if($task['type'] == 'delivery') 📦 Pengiriman
                            @else 🔄 Retur
                            @endif
                        </span>
                    </div>
                    <div class="space-y-2 mb-3 text-xs sm:text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Customer:</span>
                            <span class="text-gray-900 dark:text-gray-200 font-medium truncate ml-2">{{ $task['customer_name'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Driver:</span>
                            <span class="text-gray-900 dark:text-gray-200 truncate ml-2">{{ $task['driver_name'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Status:</span>
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold ml-2
                                @if($task['type'] == 'delivery')
                                    @if($task['status'] == 'dikirim') bg-teal-100 dark:bg-teal-900 text-teal-700 dark:text-teal-300
                                    @elseif($task['status'] == 'sampai') bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300
                                    @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                    @endif
                                @else
                                    @if($task['status'] == 'retur_diajukan') bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300
                                    @elseif($task['status'] == 'retur_diambil') bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300
                                    @elseif($task['status'] == 'retur_selesai') bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300
                                    @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                    @endif
                                @endif">
                                @if($task['type'] == 'delivery')
                                    {{ ucfirst($task['status']) }}
                                @else
                                    {{ ucfirst(str_replace('retur_', '', $task['status'])) }}
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Tanggal:</span>
                            <span class="text-gray-900 dark:text-gray-200">{{ $task['receive_date'] }}</span>
                        </div>
                        <div class="pt-2">
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Alamat:</p>
                            <p class="text-sm text-gray-900 dark:text-gray-200 break-words">{{ $task['address'] }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        @if($task['type'] == 'delivery')
                            <a href="{{ route('driver-transaksi.detail', $task['id']) }}" class="flex-1 inline-block text-center px-3 sm:px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-md text-xs sm:text-sm font-semibold transition shadow-sm">
                                Detail
                            </a>
                        @else
                            <div class="flex gap-2 w-full">
                                <a href="{{ url('/admin/driver-retur/detail/' . $task['id']) }}" class="flex-1 inline-block text-center px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs sm:text-sm font-semibold transition shadow-sm">
                                    Detail
                                </a>
                                @if($task['status'] == 'retur_diambil')
                                    <form method="POST" action="{{ url('/admin/driver-retur/pickup/' . $task['id']) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full px-3 sm:px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-xs sm:text-sm font-semibold transition shadow-sm"
                                                onclick="return confirm('Konfirmasi pengambilan retur ini?')">
                                            Ambil
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    <p class="text-sm">Tidak ada tugas kurir yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Legend untuk jenis tugas --}}
    <div class="mt-4 sm:mt-6 bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-3 sm:p-4">
        <h3 class="text-base sm:text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2 sm:mb-3">Keterangan Jenis Tugas:</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            <div class="flex items-center flex-wrap">
                <span class="inline-block px-2 sm:px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 mr-2 sm:mr-3 mb-1 sm:mb-0">
                    📦 Pengiriman
                </span>
                <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Tugas pengiriman barang normal ke customer</span>
            </div>
            <div class="flex items-center flex-wrap">
                <span class="inline-block px-2 sm:px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300 mr-2 sm:mr-3 mb-1 sm:mb-0">
                    🔄 Retur
                </span>
                <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Tugas pengambilan barang retur dari customer</span>
            </div>
        </div>
    </div>
@endsection

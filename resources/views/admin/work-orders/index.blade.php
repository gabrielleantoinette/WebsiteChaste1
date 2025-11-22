@extends('layouts.admin')

@section('content')
    @php 
        $user = Session::get('user');
        $role = is_array($user) ? $user['role'] ?? '' : $user->role ?? '';
    @endphp

    {{-- Header dengan Gradient Background --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl mb-4 sm:mb-6 lg:mb-8">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-4 sm:px-6 lg:px-8 py-4 sm:py-5 lg:py-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mb-1 sm:mb-2">📋 Surat Perintah Kerja</h1>
                    <p class="text-sm sm:text-base text-teal-100">Manajemen surat perintah kerja dan tugas produksi</p>
                </div>
                <div class="flex gap-3 w-full sm:w-auto">
                    @if(in_array($role, ['admin', 'owner']))
                        <a href="{{ route('admin.work-orders.create') }}" 
                           class="inline-flex items-center gap-2 px-4 sm:px-6 py-2 sm:py-3 bg-white/20 backdrop-blur-sm text-white text-sm sm:text-base font-medium rounded-lg hover:bg-white/30 transition-all duration-300 shadow-lg border border-white/20 w-full sm:w-auto justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                                 stroke-width="2" stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Buat Surat Perintah
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan Statistik dengan Card Modern --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 lg:gap-6 mb-4 sm:mb-6 lg:mb-8">
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Total Surat Perintah</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 truncate">{{ $workOrders->count() }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Menunggu Pengerjaan</p>
                    <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 truncate">{{ $workOrders->where('status', 'dibuat')->count() }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Sedang Dikerjakan</p>
                    <p class="text-xl sm:text-2xl font-bold text-yellow-600 dark:text-yellow-400 truncate">{{ $workOrders->where('status', 'dikerjakan')->count() }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Selesai</p>
                    <p class="text-xl sm:text-2xl font-bold text-green-600 dark:text-green-400 truncate">{{ $workOrders->where('status', 'selesai')->count() }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-green-100 dark:bg-green-900/30 rounded-lg flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Surat Perintah Kerja --}}
    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-4 sm:p-5 lg:p-6 shadow-lg">
        <h2 class="text-base sm:text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3 sm:mb-4">Daftar Surat Perintah Kerja</h2>
        {{-- Desktop Table View --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full table-auto data-table text-sm border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-[#004d4d] text-gray-700 dark:text-[#ccf2f2]">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left font-semibold">Dibuat Oleh</th>
                        <th class="px-4 py-3 text-left font-semibold">Ditugaskan Ke</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Progress</th>
                        <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($workOrders as $workOrder)
                        <tr class="border-b border-gray-100 dark:border-gray-700 @if($loop->odd) bg-gray-50 dark:bg-[#2c2c2c] @endif hover:bg-teal-50 dark:hover:bg-[#003333] transition">
                            <td class="px-4 py-3">
                                <span class="font-mono text-teal-700 dark:text-[#80C0CE] font-semibold">{{ $workOrder->code }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-800 dark:text-gray-200">{{ $workOrder->order_date->format('d M Y') }}</div>
                                    @if($workOrder->due_date)
                                        <div class="text-gray-500 dark:text-gray-400 text-xs mt-1">Due: {{ $workOrder->due_date->format('d M Y') }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-800 dark:text-gray-200">{{ $workOrder->createdBy->name ?? '-' }}</div>
                                    <div class="text-gray-500 dark:text-gray-400 text-xs mt-1">{{ $workOrder->created_at->format('d M Y H:i') }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-800 dark:text-gray-200">{{ $workOrder->assignedTo->name ?? '-' }}</div>
                                    <div class="text-gray-500 dark:text-gray-400 text-xs mt-1">{{ $workOrder->assignedTo->role ?? '-' }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'dibuat' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                                        'dikerjakan' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                                        'selesai' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                                    ];
                                    $statusLabels = [
                                        'dibuat' => 'Dibuat',
                                        'dikerjakan' => 'Dikerjakan',
                                        'selesai' => 'Selesai',
                                    ];
                                    $statusColor = $statusColors[$workOrder->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
                                    $statusLabel = $statusLabels[$workOrder->status] ?? ucfirst($workOrder->status);
                                @endphp
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $totalItems = $workOrder->items->count() ?? 0;
                                    $completedItems = $workOrder->items->where('status', 'selesai')->count() ?? 0;
                                    $progressPercentage = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;
                                @endphp
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                        <div class="bg-teal-600 dark:bg-teal-500 h-2 rounded-full transition-all duration-300" style="width: {{ $progressPercentage }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $progressPercentage }}%</span>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $completedItems }}/{{ $totalItems }} item selesai
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ $role === 'gudang' ? route('gudang.work-orders.show', $workOrder->id) : route('admin.work-orders.show', $workOrder->id) }}" 
                                       class="inline-block px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white rounded-md text-xs font-semibold transition shadow-sm">
                                        Detail
                                    </a>
                                    @if(in_array($role, ['admin', 'owner']) && in_array($workOrder->status, ['dibuat', 'dikerjakan']))
                                        <a href="{{ route('admin.work-orders.edit', $workOrder->id) }}" 
                                           class="inline-block px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-xs font-semibold transition shadow-sm">
                                            Edit
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium">Belum ada surat perintah kerja</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Buat surat perintah kerja pertama Anda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Mobile Card View --}}
        <div class="lg:hidden divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($workOrders as $workOrder)
                @php
                    $statusColors = [
                        'dibuat' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                        'dikerjakan' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                        'selesai' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                    ];
                    $statusLabels = [
                        'dibuat' => 'Dibuat',
                        'dikerjakan' => 'Dikerjakan',
                        'selesai' => 'Selesai',
                    ];
                    $statusColor = $statusColors[$workOrder->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
                    $statusLabel = $statusLabels[$workOrder->status] ?? ucfirst($workOrder->status);
                    $totalItems = $workOrder->items->count() ?? 0;
                    $completedItems = $workOrder->items->where('status', 'selesai')->count() ?? 0;
                    $progressPercentage = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;
                @endphp
                <div class="p-4 hover:bg-teal-50 dark:hover:bg-[#003333] transition">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-mono font-semibold text-teal-700 dark:text-[#80C0CE] truncate">{{ $workOrder->code }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $workOrder->order_date->format('d M Y') }}</p>
                            @if($workOrder->due_date)
                                <p class="text-xs text-gray-500 dark:text-gray-400">Due: {{ $workOrder->due_date->format('d M Y') }}</p>
                            @endif
                        </div>
                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold ml-2 flex-shrink-0 {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <div class="space-y-2 mb-3 text-xs sm:text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Dibuat Oleh:</span>
                            <span class="text-gray-900 dark:text-gray-200 font-medium">{{ $workOrder->createdBy->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Ditugaskan Ke:</span>
                            <span class="text-gray-900 dark:text-gray-200 font-medium">{{ $workOrder->assignedTo->name ?? '-' }}</span>
                        </div>
                        @if($workOrder->assignedTo)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Role:</span>
                                <span class="text-gray-900 dark:text-gray-200">{{ $workOrder->assignedTo->role ?? '-' }}</span>
                            </div>
                        @endif
                        <div class="mt-2">
                            <div class="flex items-center mb-1">
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                    <div class="bg-teal-600 dark:bg-teal-500 h-2 rounded-full transition-all duration-300" style="width: {{ $progressPercentage }}%"></div>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400 flex-shrink-0">{{ $progressPercentage }}%</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $completedItems }}/{{ $totalItems }} item selesai</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ $role === 'gudang' ? route('gudang.work-orders.show', $workOrder->id) : route('admin.work-orders.show', $workOrder->id) }}" 
                           class="flex-1 inline-block text-center px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white rounded-md text-xs font-semibold transition shadow-sm">
                            Detail
                        </a>
                        @if(in_array($role, ['admin', 'owner']) && in_array($workOrder->status, ['dibuat', 'dikerjakan']))
                            <a href="{{ route('admin.work-orders.edit', $workOrder->id) }}" 
                               class="flex-1 inline-block text-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-xs font-semibold transition shadow-sm">
                                Edit
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-base font-medium">Belum ada surat perintah kerja</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Buat surat perintah kerja pertama Anda</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection

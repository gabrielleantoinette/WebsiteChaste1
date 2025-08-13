@extends('layouts.admin')

@section('content')
<div class="py-6">
    @php 
        $user = Session::get('user');
        $role = is_array($user) ? $user['role'] ?? '' : $user->role ?? '';
    @endphp
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ $role === 'gudang' ? route('gudang.dashboard') : url('/admin') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Surat Perintah Kerja</h1>
        </div>
        @if(in_array($role, ['admin', 'owner']))
            <a href="{{ route('admin.work-orders.create') }}" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Buat Surat Perintah
            </a>
        @endif
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Total Surat Perintah</p>
            <p class="text-2xl font-bold text-teal-600">{{ $workOrders->count() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Menunggu Pengerjaan</p>
            <p class="text-2xl font-bold text-blue-600">{{ $workOrders->where('status', 'dibuat')->count() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Sedang Dikerjakan</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $workOrders->where('status', 'dikerjakan')->count() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Selesai</p>
            <p class="text-2xl font-bold text-green-600">{{ $workOrders->where('status', 'selesai')->count() }}</p>
        </div>
    </div>

    <!-- Tabel Surat Perintah Kerja -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Daftar Surat Perintah Kerja</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">Kode</th>
                        <th class="px-6 py-3 text-left font-semibold">Tanggal</th>
                        <th class="px-6 py-3 text-left font-semibold">Dibuat Oleh</th>
                        <th class="px-6 py-3 text-left font-semibold">Ditugaskan Ke</th>
                        <th class="px-6 py-3 text-left font-semibold">Status</th>
                        <th class="px-6 py-3 text-left font-semibold">Progress</th>
                        <th class="px-6 py-3 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($workOrders as $workOrder)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-teal-700 font-semibold">{{ $workOrder->code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="font-medium">{{ $workOrder->order_date->format('d M Y') }}</div>
                                    @if($workOrder->due_date)
                                        <div class="text-gray-500">Due: {{ $workOrder->due_date->format('d M Y') }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="font-medium">{{ $workOrder->createdBy->name ?? '-' }}</div>
                                    <div class="text-gray-500">{{ $workOrder->created_at->format('d M Y H:i') }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="font-medium">{{ $workOrder->assignedTo->name ?? '-' }}</div>
                                    <div class="text-gray-500">{{ $workOrder->assignedTo->role ?? '-' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $workOrder->status_color }}">
                                    {{ $workOrder->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-teal-600 h-2 rounded-full" style="width: {{ $workOrder->progress_percentage }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $workOrder->progress_percentage }}%</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $workOrder->completed_items }}/{{ $workOrder->total_items }} item selesai
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ $role === 'gudang' ? route('gudang.work-orders.show', $workOrder->id) : route('admin.work-orders.show', $workOrder->id) }}" 
                                       class="text-teal-600 hover:text-teal-800 text-sm font-medium">
                                        Detail
                                    </a>
                                    @if(in_array($role, ['admin', 'owner']) && in_array($workOrder->status, ['dibuat', 'dikerjakan']))
                                        <a href="{{ route('admin.work-orders.edit', $workOrder->id) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Edit
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-2"></i>
                                    <p class="text-lg">Belum ada surat perintah kerja</p>
                                    <p class="text-sm">Buat surat perintah kerja pertama Anda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

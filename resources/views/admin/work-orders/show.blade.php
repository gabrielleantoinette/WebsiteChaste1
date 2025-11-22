@extends('layouts.admin')

@section('content')
<div class="py-4 sm:py-5 lg:py-6 px-4 sm:px-0">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-3 sm:gap-0">
        <div class="flex items-center space-x-2 sm:space-x-4">
            @php 
                $user = Session::get('user');
                $role = is_array($user) ? $user['role'] ?? '' : $user->role ?? '';
            @endphp
            <a href="{{ $role === 'gudang' ? route('gudang.work-orders.index') : route('admin.work-orders.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors text-xs sm:text-sm flex items-center">
                <i class="fas fa-arrow-left mr-2"></i><span class="hidden sm:inline">Kembali</span>
            </a>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Detail Surat Perintah Kerja</h1>
        </div>
        <div class="flex space-x-2 w-full sm:w-auto">
            @if(in_array($workOrder->status, ['dibuat', 'dikerjakan']) && in_array(session('user')->role, ['admin', 'owner']))
                <a href="{{ route('admin.work-orders.edit', $workOrder->id) }}" class="w-full sm:w-auto text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-xs sm:text-sm">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
        </div>
    </div>

    <!-- Surat Perintah Kerja -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-4 sm:mb-6">
        <div class="p-4 sm:p-5 lg:p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-start gap-3 sm:gap-0">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800">SURAT PERINTAH POTONG</h2>
                    <p class="text-sm sm:text-base text-gray-600 mt-1">Kode: {{ $workOrder->code }}</p>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-xs sm:text-sm text-gray-600">Tanggal: {{ $workOrder->order_date->format('d/m/Y') }}</p>
                    @if($workOrder->due_date)
                        <p class="text-xs sm:text-sm text-gray-600">Deadline: {{ $workOrder->due_date->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informasi Umum -->
        <div class="p-4 sm:p-5 lg:p-6 border-b border-gray-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 lg:gap-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Dibuat Oleh:</h3>
                    <p class="text-gray-800">{{ $workOrder->createdBy->name ?? '-' }}</p>
                    <p class="text-sm text-gray-600">{{ $workOrder->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Ditugaskan Ke:</h3>
                    <p class="text-gray-800">{{ $workOrder->assignedTo->name ?? '-' }}</p>
                    <p class="text-sm text-gray-600">{{ $workOrder->assignedTo->role ?? '-' }}</p>
                </div>
            </div>
            
            @if($workOrder->description)
                <div class="mt-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi:</h3>
                    <p class="text-gray-800">{{ $workOrder->description }}</p>
                </div>
            @endif

            <div class="mt-4">
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold {{ $workOrder->status_color }}">
                    Status: {{ $workOrder->status_label }}
                </span>
            </div>
        </div>

        <!-- Tabel Item -->
        <div class="p-4 sm:p-5 lg:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-3 sm:mb-4">Detail Item</h3>
            {{-- Desktop Table View --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full border border-gray-300 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-left font-semibold text-gray-700">No.</th>
                            <th class="border border-gray-300 px-4 py-2 text-left font-semibold text-gray-700">
                                UKURAN + BAHAN
                                <div class="text-xs font-normal text-gray-500">Terpal</div>
                            </th>
                            <th class="border border-gray-300 px-4 py-2 text-left font-semibold text-gray-700">WARNA</th>
                            <th class="border border-gray-300 px-4 py-2 text-left font-semibold text-gray-700">KETERANGAN</th>
                            @if(session('user')->role === 'gudang')
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold text-gray-700">STATUS</th>
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold text-gray-700">AKSI</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workOrder->items as $index => $item)
                            <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                <td class="border border-gray-300 px-4 py-2 text-center font-medium">{{ $index + 1 }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <div class="font-medium">{{ $item->size_material }}</div>
                                    <div class="text-sm text-gray-600">Qty: {{ $item->quantity }}</div>
                                </td>
                                <td class="border border-gray-300 px-4 py-2">{{ $item->color }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $item->remarks ?? '-' }}</td>
                                @if(session('user')->role === 'gudang')
                                    <td class="border border-gray-300 px-4 py-2">
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold {{ $item->status_color }}">
                                            {{ $item->status_label }}
                                        </span>
                                        @if($item->status !== 'pending')
                                            <div class="text-xs text-gray-600 mt-1">
                                                {{ $item->completed_quantity }}/{{ $item->quantity }} selesai
                                            </div>
                                        @endif
                                    </td>

                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ session('user')->role === 'gudang' ? 6 : 4 }}" class="border border-gray-300 px-4 py-8 text-center text-gray-500">
                                    Tidak ada item
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Mobile Card View --}}
            <div class="lg:hidden divide-y divide-gray-200 mt-3 sm:mt-4">
                @forelse($workOrder->items as $index => $item)
                    <div class="p-4 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">Item #{{ $index + 1 }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $item->size_material }} - {{ $item->color }}</p>
                            </div>
                            @if(session('user')->role === 'gudang')
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold ml-2 flex-shrink-0 {{ $item->status_color }}">
                                    {{ $item->status_label }}
                                </span>
                            @endif
                        </div>
                        <div class="space-y-2 text-xs sm:text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jumlah:</span>
                                <span class="text-gray-900 font-semibold">{{ $item->quantity }} item</span>
                            </div>
                            @if($item->remarks)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Keterangan:</span>
                                    <span class="text-gray-900 break-words ml-2 text-right">{{ $item->remarks }}</span>
                                </div>
                            @endif
                            @if(session('user')->role === 'gudang' && $item->status !== 'pending')
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Progress:</span>
                                    <span class="text-gray-900">{{ $item->completed_quantity }}/{{ $item->quantity }} selesai</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500">
                        <p class="text-sm">Tidak ada item</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="p-4 sm:p-5 lg:p-6 border-t border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs sm:text-sm font-semibold text-gray-700">Progress Pengerjaan</h3>
                <span class="text-xs sm:text-sm text-gray-600">{{ $workOrder->progress_percentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 sm:h-3">
                <div class="bg-teal-600 h-2 sm:h-3 rounded-full transition-all duration-300" style="width: {{ $workOrder->progress_percentage }}%"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>{{ $workOrder->completed_items }} dari {{ $workOrder->total_items }} item selesai</span>
            </div>
        </div>

        <!-- Catatan -->
        @if($workOrder->notes)
            <div class="p-4 sm:p-5 lg:p-6 border-t border-gray-200">
                <h3 class="text-xs sm:text-sm font-semibold text-gray-700 mb-2">Catatan:</h3>
                <p class="text-xs sm:text-sm text-gray-800 break-words">{{ $workOrder->notes }}</p>
            </div>
        @endif

        <!-- Timeline -->
        <div class="p-4 sm:p-5 lg:p-6 border-t border-gray-200">
            <h3 class="text-xs sm:text-sm font-semibold text-gray-700 mb-3 sm:mb-4">Timeline</h3>
            <div class="space-y-2 sm:space-y-3">
                <div class="flex items-center">
                    <div class="w-2 h-2 sm:w-3 sm:h-3 bg-green-500 rounded-full mr-2 sm:mr-3 flex-shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-medium">Dibuat</p>
                        <p class="text-xs text-gray-600">{{ $workOrder->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                
                @if($workOrder->started_at)
                    <div class="flex items-center">
                        <div class="w-2 h-2 sm:w-3 sm:h-3 bg-yellow-500 rounded-full mr-2 sm:mr-3 flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-medium">Mulai Dikerjakan</p>
                            <p class="text-xs text-gray-600">{{ $workOrder->started_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                @endif
                
                @if($workOrder->completed_at)
                    <div class="flex items-center">
                        <div class="w-2 h-2 sm:w-3 sm:h-3 bg-blue-500 rounded-full mr-2 sm:mr-3 flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-medium">Selesai</p>
                            <p class="text-xs text-gray-600">{{ $workOrder->completed_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Update Status (untuk gudang) -->
    @if(session('user')->role === 'gudang' && $workOrder->assigned_to == session('user')->id)
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mt-4 sm:mt-6">
            <div class="p-4 sm:p-5 lg:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-3 sm:mb-4">Update Status Surat Perintah</h3>
                <form action="{{ route('gudang.work-orders.update-status', $workOrder->id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-3 sm:mb-4">
                        <div>
                            <label for="status" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Status</label>
                            <select name="status" id="status" class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                <option value="dibuat" {{ $workOrder->status === 'dibuat' ? 'selected' : '' }}>Dibuat</option>
                                <option value="dikerjakan" {{ $workOrder->status === 'dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                                <option value="selesai" {{ $workOrder->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan" {{ $workOrder->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                        <div>
                            <label for="notes" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Catatan</label>
                            <textarea name="notes" id="notes" rows="2" class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="Catatan update status...">{{ $workOrder->notes }}</textarea>
                        </div>
                    </div>
                    
                    <!-- Input Jumlah Selesai untuk setiap item -->
                    <div class="mb-3 sm:mb-4">
                        <h4 class="text-xs sm:text-sm font-medium text-gray-700 mb-2 sm:mb-3">Jumlah Selesai per Item:</h4>
                        <div class="space-y-2 sm:space-y-3">
                            @foreach($workOrder->items as $item)
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0 p-2 sm:p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ $item->size_material }} - {{ $item->color }}</p>
                                        <p class="text-xs text-gray-600">Target: {{ $item->quantity }} item</p>
                                    </div>
                                    <div class="flex items-center space-x-2 w-full sm:w-auto">
                                        <label class="text-xs sm:text-sm text-gray-700">Selesai:</label>
                                        <input type="number" 
                                               name="completed_quantities[{{ $item->id }}]" 
                                               value="{{ $item->completed_quantity }}" 
                                               min="0" 
                                               max="{{ $item->quantity }}"
                                               class="w-16 sm:w-20 px-2 py-1 border border-gray-300 rounded text-xs sm:text-sm">
                                        <span class="text-xs text-gray-500">/ {{ $item->quantity }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="w-full sm:w-auto text-center bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700 transition text-sm sm:text-base">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>



@endsection



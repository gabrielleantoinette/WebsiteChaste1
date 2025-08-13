@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            @php 
                $user = Session::get('user');
                $role = is_array($user) ? $user['role'] ?? '' : $user->role ?? '';
            @endphp
            <a href="{{ $role === 'gudang' ? route('gudang.work-orders.index') : route('admin.work-orders.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Detail Surat Perintah Kerja</h1>
        </div>
        <div class="flex space-x-2">
            @if(in_array($workOrder->status, ['dibuat', 'dikerjakan']) && in_array(session('user')->role, ['admin', 'owner']))
                <a href="{{ route('admin.work-orders.edit', $workOrder->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
        </div>
    </div>

    <!-- Surat Perintah Kerja -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">SURAT PERINTAH POTONG</h2>
                    <p class="text-gray-600 mt-1">Kode: {{ $workOrder->code }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Tanggal: {{ $workOrder->order_date->format('d/m/Y') }}</p>
                    @if($workOrder->due_date)
                        <p class="text-sm text-gray-600">Deadline: {{ $workOrder->due_date->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informasi Umum -->
        <div class="p-6 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Detail Item</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
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
                                    <td class="border border-gray-300 px-4 py-2">
                                        @if($workOrder->assigned_to == session('user')->id)
                                            <button type="button" 
                                                    onclick="openUpdateModal({{ $item->id }}, '{{ $item->status }}', {{ $item->completed_quantity }}, {{ $item->quantity }})"
                                                    class="text-blue-600 hover:text-blue-800 text-sm">
                                                Update
                                            </button>
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
        </div>

        <!-- Progress Bar -->
        <div class="p-6 border-t border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Progress Pengerjaan</h3>
                <span class="text-sm text-gray-600">{{ $workOrder->progress_percentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-teal-600 h-3 rounded-full transition-all duration-300" style="width: {{ $workOrder->progress_percentage }}%"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>{{ $workOrder->completed_items }} dari {{ $workOrder->total_items }} item selesai</span>
            </div>
        </div>

        <!-- Catatan -->
        @if($workOrder->notes)
            <div class="p-6 border-t border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Catatan:</h3>
                <p class="text-gray-800">{{ $workOrder->notes }}</p>
            </div>
        @endif

        <!-- Timeline -->
        <div class="p-6 border-t border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Timeline</h3>
            <div class="space-y-3">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium">Dibuat</p>
                        <p class="text-xs text-gray-600">{{ $workOrder->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                
                @if($workOrder->started_at)
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium">Mulai Dikerjakan</p>
                            <p class="text-xs text-gray-600">{{ $workOrder->started_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                @endif
                
                @if($workOrder->completed_at)
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium">Selesai</p>
                            <p class="text-xs text-gray-600">{{ $workOrder->completed_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Update Status (untuk gudang) -->
    @if(session('user')->role === 'gudang' && $workOrder->assigned_to == session('user')->id)
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Update Status Surat Perintah</h3>
                <form action="{{ route('gudang.work-orders.update-status', $workOrder->id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                <option value="dibuat" {{ $workOrder->status === 'dibuat' ? 'selected' : '' }}>Dibuat</option>
                                <option value="dikerjakan" {{ $workOrder->status === 'dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                                <option value="selesai" {{ $workOrder->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan" {{ $workOrder->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                            <textarea name="notes" id="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="Catatan update status...">{{ $workOrder->notes }}</textarea>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<!-- Modal Update Item Status -->
@if(session('user')->role === 'gudang')
<div id="updateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Update Status Item</h3>
                <form id="updateItemForm" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="modal_status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="modal_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                <option value="pending">Menunggu</option>
                                <option value="in_progress">Sedang Dikerjakan</option>
                                <option value="completed">Selesai</option>
                            </select>
                        </div>
                        <div>
                            <label for="modal_completed_quantity" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Selesai</label>
                            <input type="number" name="completed_quantity" id="modal_completed_quantity" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Dari total: <span id="modal_total_quantity">0</span></p>
                        </div>
                        <div>
                            <label for="modal_notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                            <textarea name="notes" id="modal_notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="Catatan untuk item ini..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeUpdateModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
@if(session('user')->role === 'gudang')
<script>
function openUpdateModal(itemId, currentStatus, completedQty, totalQty) {
    document.getElementById('modal_status').value = currentStatus;
    document.getElementById('modal_completed_quantity').value = completedQty;
    document.getElementById('modal_total_quantity').textContent = totalQty;
    document.getElementById('modal_completed_quantity').max = totalQty;
    
    const form = document.getElementById('updateItemForm');
    form.action = `/admin/work-orders/{{ $workOrder->id }}/items/${itemId}/status`;
    
    document.getElementById('updateModal').classList.remove('hidden');
}

function closeUpdateModal() {
    document.getElementById('updateModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('updateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUpdateModal();
    }
});
</script>
@endif
@endpush

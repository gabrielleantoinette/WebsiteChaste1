@extends('layouts.admin')

@section('content')
<div class="py-4 sm:py-5 lg:py-6 px-4 sm:px-0">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-3 sm:gap-0">
        <div class="flex items-center space-x-2 sm:space-x-4">
            <a href="{{ route('admin.work-orders.show', $workOrder->id) }}" class="text-gray-600 hover:text-gray-800 transition-colors text-xs sm:text-sm flex items-center">
                <i class="fas fa-arrow-left mr-2"></i><span class="hidden sm:inline">Kembali</span>
            </a>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Edit Surat Perintah Kerja</h1>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-4 sm:p-5 lg:p-6 border-b border-gray-200">
            <h2 class="text-base sm:text-lg font-semibold text-gray-700">Edit Surat Perintah Kerja - {{ $workOrder->code }}</h2>
        </div>

        <form action="{{ route('admin.work-orders.update', $workOrder->id) }}" method="POST" class="p-4 sm:p-5 lg:p-6">
            @csrf
            @method('PUT')
            
            <!-- Informasi Umum -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 lg:gap-6 mb-6 sm:mb-8">
                <div>
                    <label for="order_date" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Tanggal Surat Perintah *</label>
                    <input type="date" name="order_date" id="order_date" value="{{ old('order_date', $workOrder->order_date->format('Y-m-d')) }}" 
                           class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" required>
                    @error('order_date')
                        <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="due_date" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Tanggal Deadline</label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $workOrder->due_date ? $workOrder->due_date->format('Y-m-d') : '') }}" 
                           class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    @error('due_date')
                        <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="assigned_to" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Ditugaskan Ke *</label>
                    <select name="assigned_to" id="assigned_to" 
                            class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" required>
                        <option value="">Pilih Staff Gudang</option>
                        @foreach($gudangEmployees as $employee)
                            <option value="{{ $employee->id }}" {{ old('assigned_to', $workOrder->assigned_to) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} ({{ $employee->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" 
                              class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                              placeholder="Deskripsi singkat tentang surat perintah kerja ini...">{{ old('description', $workOrder->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="status" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Status</label>
                    <select name="status" id="status" 
                            class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        <option value="dibuat" {{ old('status', $workOrder->status) === 'dibuat' ? 'selected' : '' }}>Dibuat</option>
                        <option value="dikerjakan" {{ old('status', $workOrder->status) === 'dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                        <option value="selesai" {{ old('status', $workOrder->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ old('status', $workOrder->status) === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Informasi Item (Read Only) -->
            <div class="mb-6 sm:mb-8">
                <h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-3 sm:mb-4">Detail Item (Tidak Dapat Diedit)</h3>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 sm:p-4">
                    {{-- Desktop Table View --}}
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="min-w-full border border-gray-300 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-left font-semibold text-gray-700">No.</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left font-semibold text-gray-700">Ukuran + Bahan</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left font-semibold text-gray-700">Warna</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left font-semibold text-gray-700">Jumlah</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left font-semibold text-gray-700">Keterangan</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left font-semibold text-gray-700">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($workOrder->items as $index => $item)
                                    <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $index + 1 }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $item->size_material }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $item->color }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $item->quantity }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $item->remarks ?? '-' }}</td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold {{ $item->status_color }}">
                                                {{ $item->status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="border border-gray-300 px-4 py-4 text-center text-gray-500">
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
                            <div class="p-3 sm:p-4 bg-white rounded-lg border border-gray-200">
                                <div class="flex justify-between items-start mb-2">
                                    <p class="text-xs sm:text-sm font-semibold text-gray-700">Item #{{ $index + 1 }}</p>
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold {{ $item->status_color }}">
                                        {{ $item->status_label }}
                                    </span>
                                </div>
                                <div class="space-y-2 text-xs sm:text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Ukuran + Bahan:</span>
                                        <span class="text-gray-900 font-medium">{{ $item->size_material }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Warna:</span>
                                        <span class="text-gray-900">{{ $item->color }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Jumlah:</span>
                                        <span class="text-gray-900 font-semibold">{{ $item->quantity }}</span>
                                    </div>
                                    @if($item->remarks)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Keterangan:</span>
                                            <span class="text-gray-900 break-words ml-2 text-right">{{ $item->remarks }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-3 sm:p-4 text-center text-gray-500">
                                <p class="text-xs sm:text-sm">Tidak ada item</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <p class="text-xs sm:text-sm text-gray-600 mt-2 sm:mt-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    Item tidak dapat diedit setelah surat perintah dibuat. Untuk mengubah item, buat surat perintah baru.
                </p>
            </div>

            <!-- Catatan -->
            <div class="mb-6 sm:mb-8">
                <label for="notes" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Catatan Tambahan</label>
                <textarea name="notes" id="notes" rows="3" 
                          class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                          placeholder="Catatan khusus untuk staff gudang...">{{ old('notes', $workOrder->notes) }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Informasi Timeline -->
            <div class="mb-6 sm:mb-8 bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                <h3 class="text-xs sm:text-sm font-semibold text-blue-800 mb-2">Timeline Surat Perintah</h3>
                <div class="space-y-1.5 sm:space-y-2 text-xs sm:text-sm text-blue-700">
                    <div class="flex justify-between flex-wrap">
                        <span>Dibuat:</span>
                        <span class="ml-2">{{ $workOrder->created_at->format('d M Y H:i') }}</span>
                    </div>
                    @if($workOrder->started_at)
                        <div class="flex justify-between flex-wrap">
                            <span>Mulai Dikerjakan:</span>
                            <span class="ml-2">{{ $workOrder->started_at->format('d M Y H:i') }}</span>
                        </div>
                    @endif
                    @if($workOrder->completed_at)
                        <div class="flex justify-between flex-wrap">
                            <span>Selesai:</span>
                            <span class="ml-2">{{ $workOrder->completed_at->format('d M Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-4">
                <a href="{{ route('admin.work-orders.show', $workOrder->id) }}" 
                   class="w-full sm:w-auto text-center px-4 sm:px-6 py-2 text-sm sm:text-base border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto text-center px-4 sm:px-6 py-2 text-sm sm:text-base bg-teal-600 text-white rounded-md hover:bg-teal-700 transition">
                    <i class="fas fa-save mr-2"></i>Update Surat Perintah
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

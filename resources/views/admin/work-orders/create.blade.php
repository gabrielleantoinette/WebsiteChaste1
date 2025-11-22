@extends('layouts.admin')

@section('content')
<div class="py-4 sm:py-5 lg:py-6 px-4 sm:px-0">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-3 sm:gap-0">
            <div class="flex items-center space-x-2 sm:space-x-4">
                <a href="{{ route('admin.work-orders.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors text-xs sm:text-sm flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i><span class="hidden sm:inline">Kembali</span>
                </a>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Buat Surat Perintah Kerja</h1>
            </div>
        </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-4 sm:p-5 lg:p-6 border-b border-gray-200">
            <h2 class="text-base sm:text-lg font-semibold text-gray-700">Form Surat Perintah Kerja</h2>
        </div>

        <form action="/admin/work-orders" method="POST" class="p-4 sm:p-5 lg:p-6" id="workOrderForm">
            @csrf
            
            <!-- Informasi Umum -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 lg:gap-6 mb-6 sm:mb-8">
                <div>
                    <label for="order_date" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Tanggal Surat Perintah *</label>
                    <input type="date" name="order_date" id="order_date" value="{{ old('order_date', date('Y-m-d')) }}" 
                           class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" required>
                    @error('order_date')
                        <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="due_date" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Tanggal Deadline</label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" 
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
                            <option value="{{ $employee->id }}" {{ old('assigned_to') == $employee->id ? 'selected' : '' }}>
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
                              placeholder="Deskripsi singkat tentang surat perintah kerja ini...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Daftar Item -->
            <div class="mb-6 sm:mb-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3 sm:mb-4 gap-3 sm:gap-0">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-700">Daftar Item yang Akan Dipotong</h3>
                    <button type="button" id="addItemBtn" class="bg-teal-600 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-md text-xs sm:text-sm hover:bg-teal-700 transition w-full sm:w-auto text-center">
                        <i class="fas fa-plus mr-1"></i>Tambah Item
                    </button>
                </div>

                <div id="itemsContainer">
                    <!-- Item akan ditambahkan di sini -->
                </div>

                @error('items')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Catatan -->
            <div class="mb-6 sm:mb-8">
                <label for="notes" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Catatan Tambahan</label>
                <textarea name="notes" id="notes" rows="3" 
                          class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                          placeholder="Catatan khusus untuk staff gudang...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Submit -->
            <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-4">
                <a href="{{ route('admin.work-orders.index') }}" 
                   class="w-full sm:w-auto text-center px-4 sm:px-6 py-2 text-sm sm:text-base border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto text-center px-4 sm:px-6 py-2 text-sm sm:text-base bg-teal-600 text-white rounded-md hover:bg-teal-700 transition">
                    <i class="fas fa-save mr-2"></i>Buat Surat Perintah
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template untuk item -->
<template id="itemTemplate">
    <div class="item-row border border-gray-200 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4 bg-gray-50">
        <div class="flex justify-between items-start mb-3 sm:mb-4">
            <h4 class="text-sm sm:text-base font-medium text-gray-700">Item #<span class="item-number"></span></h4>
            <button type="button" class="remove-item-btn text-red-600 hover:text-red-800 p-1">
                <i class="fas fa-trash text-sm sm:text-base"></i>
            </button>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Bahan Baku *</label>
                <select name="items[INDEX][raw_material_id]" 
                        class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                        required>
                    <option value="">Pilih Bahan Baku</option>
                    @foreach($rawMaterials as $material)
                        <option value="{{ $material->id }}" data-color="{{ $material->color }}">
                            {{ $material->name }} ({{ $material->color }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Ukuran *</label>
                <input type="text" name="items[INDEX][size]" 
                       class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                       placeholder="Contoh: 2x3, 3x4" required>
            </div>
            
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Warna *</label>
                <input type="text" name="items[INDEX][color]" 
                       class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent bg-gray-100"
                       placeholder="Warna akan terisi otomatis" readonly>
            </div>
            
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Jumlah *</label>
                <input type="number" name="items[INDEX][quantity]" min="1"
                       class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                       placeholder="0" required>
            </div>
            
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Keterangan</label>
                <input type="text" name="items[INDEX][remarks]" 
                       class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                       placeholder="Contoh: Dikoli=50">
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 0;
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemBtn = document.getElementById('addItemBtn');
    const itemTemplate = document.getElementById('itemTemplate');

    function addItem() {
        const itemHtml = itemTemplate.innerHTML.replace(/INDEX/g, itemIndex);
        const itemDiv = document.createElement('div');
        itemDiv.innerHTML = itemHtml;
        itemDiv.querySelector('.item-number').textContent = itemIndex + 1;
        
        // Add remove functionality
        const removeBtn = itemDiv.querySelector('.remove-item-btn');
        removeBtn.addEventListener('click', function() {
            itemDiv.remove();
            updateItemNumbers();
        });
        
        // Add material selection functionality
        const materialSelect = itemDiv.querySelector('select[name*="[raw_material_id]"]');
        const colorInput = itemDiv.querySelector('input[name*="[color]"]');
        
        materialSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const color = selectedOption.getAttribute('data-color');
            colorInput.value = color || '';
        });
        
        itemsContainer.appendChild(itemDiv);
        itemIndex++;
    }

    function updateItemNumbers() {
        const items = itemsContainer.querySelectorAll('.item-row');
        items.forEach((item, index) => {
            item.querySelector('.item-number').textContent = index + 1;
        });
    }

    addItemBtn.addEventListener('click', addItem);

    // Add first item by default
    addItem();
});
</script>
@endpush

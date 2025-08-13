@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Buat Surat Perintah Kerja</h1>
        <a href="{{ route('admin.work-orders.index') }}" class="text-teal-600 hover:text-teal-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Form Surat Perintah Kerja</h2>
        </div>

        <form action="{{ route('admin.work-orders.store') }}" method="POST" class="p-6">
            @csrf
            
            <!-- Informasi Umum -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="order_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Surat Perintah *</label>
                    <input type="date" name="order_date" id="order_date" value="{{ old('order_date', date('Y-m-d')) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" required>
                    @error('order_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Deadline</label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    @error('due_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Ditugaskan Ke *</label>
                    <select name="assigned_to" id="assigned_to" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" required>
                        <option value="">Pilih Staff Gudang</option>
                        @foreach($gudangEmployees as $employee)
                            <option value="{{ $employee->id }}" {{ old('assigned_to') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} ({{ $employee->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                              placeholder="Deskripsi singkat tentang surat perintah kerja ini...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Daftar Item -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Daftar Item yang Akan Dipotong</h3>
                    <button type="button" id="addItemBtn" class="bg-teal-600 text-white px-3 py-1 rounded-md text-sm hover:bg-teal-700">
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
            <div class="mb-8">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan</label>
                <textarea name="notes" id="notes" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                          placeholder="Catatan khusus untuk staff gudang...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Submit -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.work-orders.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700">
                    <i class="fas fa-save mr-2"></i>Buat Surat Perintah
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template untuk item -->
<template id="itemTemplate">
    <div class="item-row border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50">
        <div class="flex justify-between items-start mb-4">
            <h4 class="text-md font-medium text-gray-700">Item #<span class="item-number"></span></h4>
            <button type="button" class="remove-item-btn text-red-600 hover:text-red-800">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran + Bahan *</label>
                <input type="text" name="items[INDEX][size_material]" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                       placeholder="Contoh: A2 2x3" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Warna *</label>
                <input type="text" name="items[INDEX][color]" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                       placeholder="Contoh: BS Cap GSY" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah *</label>
                <input type="number" name="items[INDEX][quantity]" min="1"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                       placeholder="0" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                <input type="text" name="items[INDEX][remarks]" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
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

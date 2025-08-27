@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-10">
    <div class="mb-6">
        <x-breadcrumb :items="[
            ['label' => 'Hutang Supplier', 'url' => route('keuangan.hutang.index')],
            ['label' => 'Tambah PO']
        ]" />
        <h1 class="text-2xl font-bold text-gray-800">+ Tambah PO Manual</h1>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('keuangan.hutang.store') }}" method="POST" class="bg-white p-6 rounded shadow space-y-6">
        @csrf

        <!-- Informasi Dasar PO -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="supplier_name" class="block font-medium mb-1">Nama Supplier</label>
                <input type="text" name="supplier_name" id="supplier_name" value="{{ old('supplier_name') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div>
                <label for="po_code" class="block font-medium mb-1">Nomor PO</label>
                <input type="text" name="po_code" id="po_code" value="{{ old('po_code') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div>
                <label for="order_date" class="block font-medium mb-1">Tanggal PO</label>
                <input type="date" name="order_date" id="order_date" value="{{ old('order_date', date('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div>
                <label for="due_date" class="block font-medium mb-1">Tanggal Jatuh Tempo</label>
                <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
        </div>

        <!-- Daftar Item -->
        <div>
            <h3 class="text-lg font-semibold mb-4">📦 Daftar Item yang Dibeli</h3>
            <div id="items-container" class="space-y-4">
                <div class="item-row border border-gray-200 rounded p-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block font-medium mb-1">Nama Bahan</label>
                            <input type="text" name="items[0][material_name]" class="w-full border border-gray-300 rounded px-3 py-2" 
                                   placeholder="Contoh: Terpal A8, Kain Canvas, dll" required>
                        </div>
                        
                        <div>
                            <label class="block font-medium mb-1">Jumlah</label>
                            <input type="number" name="items[0][quantity]" class="quantity-input w-full border border-gray-300 rounded px-3 py-2" 
                                   min="1" step="any" required>
                        </div>
                        
                        <div>
                            <label class="block font-medium mb-1">Harga Satuan</label>
                            <input type="number" name="items[0][unit_price]" class="unit-price-input w-full border border-gray-300 rounded px-3 py-2" 
                                   min="1" step="any" required>
                        </div>
                        
                        <div>
                            <label class="block font-medium mb-1">Subtotal</label>
                            <input type="number" name="items[0][subtotal]" class="subtotal-input w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" 
                                   readonly>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="button" id="add-item" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Tambah Item
            </button>
        </div>

        <!-- Total -->
        <div class="border-t pt-4">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold">Total Pembelian</h3>
                <div class="text-right">
                    <input type="number" name="total" id="total" value="{{ old('total') }}"
                           class="text-xl font-bold text-teal-600 bg-transparent border-none" readonly>
                    <span class="text-sm text-gray-500">(Otomatis terhitung)</span>
                </div>
            </div>
        </div>

        <div class="text-right pt-4 border-t">
            <a href="{{ route('keuangan.hutang.index') }}" class="text-sm text-gray-600 hover:underline mr-4">Batal</a>
            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded font-semibold">
                <i class="fas fa-save mr-2"></i>Simpan PO
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 1;
    
    // Add new item row
    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const newRow = document.createElement('div');
        newRow.className = 'item-row border border-gray-200 rounded p-4 bg-gray-50';
        newRow.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block font-medium mb-1">Nama Bahan</label>
                    <input type="text" name="items[${itemIndex}][material_name]" class="w-full border border-gray-300 rounded px-3 py-2" 
                           placeholder="Contoh: Terpal A8, Kain Canvas, dll" required>
                </div>
                
                <div>
                    <label class="block font-medium mb-1">Jumlah</label>
                    <input type="number" name="items[${itemIndex}][quantity]" class="quantity-input w-full border border-gray-300 rounded px-3 py-2" 
                           min="1" step="any" required>
                </div>
                
                <div>
                    <label class="block font-medium mb-1">Harga Satuan</label>
                    <input type="number" name="items[${itemIndex}][unit_price]" class="unit-price-input w-full border border-gray-300 rounded px-3 py-2" 
                           min="1" step="any" required>
                </div>
                
                <div>
                    <label class="block font-medium mb-1">Subtotal</label>
                    <input type="number" name="items[${itemIndex}][subtotal]" class="subtotal-input w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" 
                           readonly>
                </div>
            </div>
            <button type="button" class="remove-item mt-2 px-2 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                <i class="fas fa-trash mr-1"></i>Hapus
            </button>
        `;
        
        container.appendChild(newRow);
        itemIndex++;
        
        // Add event listeners to new row
        addEventListeners(newRow);
    });
    
    // Add event listeners to initial row
    addEventListeners(document.querySelector('.item-row'));
    
    function addEventListeners(row) {
        const quantityInput = row.querySelector('.quantity-input');
        const unitPriceInput = row.querySelector('.unit-price-input');
        const subtotalInput = row.querySelector('.subtotal-input');
        
        // Calculate subtotal when quantity or unit price changes
        quantityInput.addEventListener('input', calculateSubtotal);
        unitPriceInput.addEventListener('input', calculateSubtotal);
        
        function calculateSubtotal() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const unitPrice = parseFloat(unitPriceInput.value) || 0;
            const subtotal = quantity * unitPrice;
            subtotalInput.value = subtotal.toFixed(2);
            calculateTotal();
        }
        
        // Remove item button
        const removeBtn = row.querySelector('.remove-item');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                row.remove();
                calculateTotal();
            });
        }
    }
    
    function calculateTotal() {
        const subtotals = document.querySelectorAll('.subtotal-input');
        let total = 0;
        subtotals.forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('total').value = total.toFixed(2);
    }
});
</script>
@endsection

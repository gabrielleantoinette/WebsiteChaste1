@extends('layouts.admin')

@section('content')
    {{-- Header dengan Gradient Background --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl mb-8">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">📦 Kelola Bahan Baku</h1>
                    <p class="text-teal-100">Manajemen stok bahan baku dan inventori</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="openCreateModal()" 
                            class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm text-white font-medium rounded-lg hover:bg-white/30 transition-all duration-300 shadow-lg border border-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                             stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Bahan Baku
                    </button>
                    <a href="{{ url('/admin/products') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm text-white font-medium rounded-lg hover:bg-white/30 transition-all duration-300 shadow-lg border border-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                             stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5l4.5-3.75m0 0l4.5 3.75M3.75 7.5h16.5" />
                        </svg>
                        Lihat Produk
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Ringkasan Stok dengan Card Modern --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Bahan Baku</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $rawMaterials->count() }}</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Stok (m²)</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($rawMaterials->sum('stock'), 2) }}</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Stok Rendah (≤10 m²)</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $rawMaterials->where('stock', '<=', 10)->count() }}</p>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Stok Aman (>50 m²)</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $rawMaterials->where('stock', '>', 50)->count() }}</p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Stok Bahan Baku --}}
    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-lg">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Daftar Stok Bahan Baku</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto data-table text-sm border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-[#004d4d] text-gray-700 dark:text-[#ccf2f2]">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nama Bahan Baku</th>
                        <th class="px-4 py-3 text-left font-semibold">Warna</th>
                        <th class="px-4 py-3 text-left font-semibold">Stok Saat Ini (m²)</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rawMaterials as $material)
                        <tr class="border-b border-gray-100 dark:border-gray-700 @if($loop->odd) bg-gray-50 dark:bg-[#2c2c2c] @endif hover:bg-teal-50 dark:hover:bg-[#003333] transition">
                            <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-200">{{ $material->name }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    {{ $material->color ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold text-lg">
                                <span class="{{ $material->stock <= 10 ? 'text-red-600 dark:text-red-400' : ($material->stock <= 50 ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400') }}">
                                    {{ number_format($material->stock, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($material->stock <= 10)
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                                        Stok Rendah
                                    </span>
                                @elseif($material->stock <= 50)
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300">
                                        Stok Menengah
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                        Stok Aman
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <!-- Update Stok -->
                                    <button onclick="openUpdateModal({{ $material->id }}, '{{ $material->name }}', {{ $material->stock }})" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-md text-xs font-semibold transition shadow-sm">
                                        ✏️ Update
                                    </button>
                                    
                                    <!-- Tambah Stok -->
                                    <button onclick="openAddModal({{ $material->id }}, '{{ $material->name }}')" 
                                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-md text-xs font-semibold transition shadow-sm">
                                        ➕ Tambah
                                    </button>
                                    
                                    <!-- Kurangi Stok -->
                                    <button onclick="openReduceModal({{ $material->id }}, '{{ $material->name }}', {{ $material->stock }})" 
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-md text-xs font-semibold transition shadow-sm">
                                        ➖ Kurangi
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada bahan baku</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Mulai dengan menambahkan bahan baku baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

<!-- Modal Update Stok -->
<div id="updateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-black dark:bg-opacity-70 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl shadow-xl p-6 w-full max-w-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Update Stok Bahan Baku</h3>
            <form id="updateForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Bahan Baku</label>
                    <input type="text" id="updateMaterialName" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-gray-50 dark:bg-[#2c2c2c] text-gray-700 dark:text-gray-300" readonly>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stok Baru (m²)</label>
                    <input type="number" name="stock" id="updateStock" step="0.01" min="0" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-[#2c2c2c] text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan (Opsional)</label>
                    <textarea name="notes" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-[#2c2c2c] text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2" placeholder="Alasan update stok..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeUpdateModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition font-semibold">
                        Update Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Stok -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-black dark:bg-opacity-70 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl shadow-xl p-6 w-full max-w-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Tambah Stok Bahan Baku</h3>
            <form id="addForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Bahan Baku</label>
                    <input type="text" id="addMaterialName" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-gray-50 dark:bg-[#2c2c2c] text-gray-700 dark:text-gray-300" readonly>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jumlah Tambahan (m²)</label>
                    <input type="number" name="add_stock" step="0.01" min="0.01" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-[#2c2c2c] text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan (Opsional)</label>
                    <textarea name="notes" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-[#2c2c2c] text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500" rows="2" placeholder="Alasan penambahan stok..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeAddModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition font-semibold">
                        Tambah Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Kurangi Stok -->
<div id="reduceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-black dark:bg-opacity-70 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl shadow-xl p-6 w-full max-w-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Kurangi Stok Bahan Baku</h3>
            <form id="reduceForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Bahan Baku</label>
                    <input type="text" id="reduceMaterialName" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-gray-50 dark:bg-[#2c2c2c] text-gray-700 dark:text-gray-300" readonly>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stok Saat Ini (m²)</label>
                    <input type="text" id="reduceCurrentStock" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-gray-50 dark:bg-[#2c2c2c] text-gray-700 dark:text-gray-300" readonly>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jumlah Pengurangan (m²)</label>
                    <input type="number" name="reduce_stock" step="0.01" min="0.01" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-[#2c2c2c] text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan (Opsional)</label>
                    <textarea name="notes" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-[#2c2c2c] text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500" rows="2" placeholder="Alasan pengurangan stok..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeReduceModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition font-semibold">
                        Kurangi Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Bahan Baku Baru -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 dark:bg-black dark:bg-opacity-70 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl shadow-xl w-full max-w-md border border-gray-200 dark:border-gray-700">
            <div class="bg-gradient-to-r from-teal-500 to-teal-600 text-white px-6 py-4 rounded-t-xl">
                <h3 class="text-lg font-semibold">Tambah Bahan Baku Baru</h3>
            </div>
            <form id="createForm" method="POST" action="{{ route('admin.raw-material-stock.create') }}" class="p-6">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Bahan Baku *</label>
                    <input type="text" name="name" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-[#2c2c2c] text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500" required placeholder="Contoh: Terpal">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Warna *</label>
                    <input type="text" name="color" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-[#2c2c2c] text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500" required placeholder="Contoh: Biru, Hitam, Hijau">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stok Awal (m²) *</label>
                    <input type="number" name="stock" step="0.01" min="0" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-[#2c2c2c] text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500" required placeholder="0.00">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan (Opsional)</label>
                    <textarea name="notes" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-[#2c2c2c] text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500" rows="2" placeholder="Catatan tambahan..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeCreateModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-md transition-colors font-semibold">
                        Tambah Bahan Baku
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openUpdateModal(id, name, stock) {
    document.getElementById('updateMaterialName').value = name;
    document.getElementById('updateStock').value = stock;
    document.getElementById('updateForm').action = `/admin/raw-material-stock/${id}/update`;
    document.getElementById('updateModal').classList.remove('hidden');
}

function closeUpdateModal() {
    document.getElementById('updateModal').classList.add('hidden');
}

function openAddModal(id, name) {
    document.getElementById('addMaterialName').value = name;
    document.getElementById('addForm').action = `/admin/raw-material-stock/${id}/add`;
    document.getElementById('addModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}

function openReduceModal(id, name, stock) {
    document.getElementById('reduceMaterialName').value = name;
    document.getElementById('reduceCurrentStock').value = stock;
    document.getElementById('reduceForm').action = `/admin/raw-material-stock/${id}/reduce`;
    document.getElementById('reduceModal').classList.remove('hidden');
}

function closeReduceModal() {
    document.getElementById('reduceModal').classList.add('hidden');
}

function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed')) {
        event.target.classList.add('hidden');
    }
});
</script>
@endsection

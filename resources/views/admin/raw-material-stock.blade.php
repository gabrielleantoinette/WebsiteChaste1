@extends('layouts.admin')

@section('content')
<div class="py-6">
    {{-- Breadcrumb --}}
    <div class="mb-4">
        <a href="{{ url('/admin') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
            ← Kembali ke Dashboard
        </a>
    </div>
    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Kelola Bahan Baku</h1>
        <div class="flex space-x-3">
            <button onclick="openCreateModal()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition">
                ➕ Tambah Bahan Baku
            </button>
            <a href="{{ url('/admin/products') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition">
                📦 Lihat Produk
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Ringkasan Stok --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Total Bahan Baku</p>
            <p class="text-2xl font-bold text-blue-600">{{ $rawMaterials->count() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Total Stok (m²)</p>
            <p class="text-2xl font-bold text-green-600">{{ number_format($rawMaterials->sum('stock'), 2) }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Stok Rendah (≤10 m²)</p>
            <p class="text-2xl font-bold text-red-600">{{ $rawMaterials->where('stock', '<=', 10)->count() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Stok Aman (>50 m²)</p>
            <p class="text-2xl font-bold text-purple-600">{{ $rawMaterials->where('stock', '>', 50)->count() }}</p>
        </div>
    </div>

    {{-- Tabel Stok Bahan Baku --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Daftar Stok Bahan Baku</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
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
                        <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-orange-50 transition">
                            <td class="px-4 py-3 font-semibold">{{ $material->name }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                    {{ $material->color ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold text-lg">
                                <span class="{{ $material->stock <= 10 ? 'text-red-600' : ($material->stock <= 50 ? 'text-yellow-600' : 'text-green-600') }}">
                                    {{ number_format($material->stock, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($material->stock <= 10)
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        Stok Rendah
                                    </span>
                                @elseif($material->stock <= 50)
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                        Stok Menengah
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        Stok Aman
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex space-x-2">
                                    <!-- Update Stok -->
                                    <button onclick="openUpdateModal({{ $material->id }}, '{{ $material->name }}', {{ $material->stock }})" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs transition">
                                        ✏️ Update
                                    </button>
                                    
                                    <!-- Tambah Stok -->
                                    <button onclick="openAddModal({{ $material->id }}, '{{ $material->name }}')" 
                                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs transition">
                                        ➕ Tambah
                                    </button>
                                    
                                    <!-- Kurangi Stok -->
                                    <button onclick="openReduceModal({{ $material->id }}, '{{ $material->name }}', {{ $material->stock }})" 
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs transition">
                                        ➖ Kurangi
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-gray-500 py-4">Tidak ada bahan baku.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Update Stok -->
<div id="updateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Update Stok Bahan Baku</h3>
            <form id="updateForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bahan Baku</label>
                    <input type="text" id="updateMaterialName" class="w-full border border-gray-300 rounded-md px-3 py-2" readonly>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stok Baru (m²)</label>
                    <input type="number" name="stock" id="updateStock" step="0.01" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="notes" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2" placeholder="Alasan update stok..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeUpdateModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition">
                        Update Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Stok -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Tambah Stok Bahan Baku</h3>
            <form id="addForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bahan Baku</label>
                    <input type="text" id="addMaterialName" class="w-full border border-gray-300 rounded-md px-3 py-2" readonly>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Tambahan (m²)</label>
                    <input type="number" name="add_stock" step="0.01" min="0.01" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="notes" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2" placeholder="Alasan penambahan stok..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition">
                        Batal
                    </button>
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition">
                        Tambah Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Kurangi Stok -->
<div id="reduceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Kurangi Stok Bahan Baku</h3>
            <form id="reduceForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bahan Baku</label>
                    <input type="text" id="reduceMaterialName" class="w-full border border-gray-300 rounded-md px-3 py-2" readonly>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stok Saat Ini (m²)</label>
                    <input type="text" id="reduceCurrentStock" class="w-full border border-gray-300 rounded-md px-3 py-2" readonly>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Pengurangan (m²)</label>
                    <input type="number" name="reduce_stock" step="0.01" min="0.01" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="notes" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2" placeholder="Alasan pengurangan stok..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeReduceModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition">
                        Batal
                    </button>
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition">
                        Kurangi Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Bahan Baku Baru -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="bg-teal-600 text-white px-6 py-4 rounded-t-lg">
                <h3 class="text-lg font-semibold">Tambah Bahan Baku Baru</h3>
            </div>
            <form id="createForm" method="POST" action="{{ route('admin.raw-material-stock.create') }}" class="p-6">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bahan Baku *</label>
                    <input type="text" name="name" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500" required placeholder="Contoh: Terpal">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warna *</label>
                    <input type="text" name="color" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500" required placeholder="Contoh: Biru, Hitam, Hijau">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stok Awal (m²) *</label>
                    <input type="number" name="stock" step="0.01" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500" required placeholder="0.00">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="notes" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500" rows="2" placeholder="Catatan tambahan..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCreateModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-md transition-colors">
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

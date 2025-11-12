@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto">
  {{-- Breadcrumb --}}
  <nav class="mb-3">
    <ol class="flex items-center space-x-1 text-xs text-gray-500">
      <li><a href="{{ url('/admin/products') }}" class="hover:text-teal-600 transition">Produk</a></li>
      <li class="flex items-center">
        <svg class="w-3 h-3 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700">Detail</span>
      </li>
    </ol>
  </nav>

  <div class="flex items-center justify-between mb-4">
    <h2 class="text-xl font-bold text-gray-800">Edit: {{ $product->name }}</h2>
    <button onclick="window.location.href='{{ url('/admin/products') }}'" class="text-sm text-gray-600 hover:text-teal-600 flex items-center gap-1">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
      </svg>
      Kembali
    </button>
  </div>

  <form method="POST" enctype="multipart/form-data" class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      {{-- Nama Produk --}}
      <div class="md:col-span-2">
        <label class="block text-xs font-medium text-gray-700 mb-1">Nama Produk</label>
        <input type="text" name="name" class="w-full border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-2 focus:ring-teal-300 focus:border-teal-500" value="{{ $product->name }}">
      </div>

      {{-- Deskripsi --}}
      <div class="md:col-span-2">
        <label class="block text-xs font-medium text-gray-700 mb-1">Deskripsi</label>
        <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-1.5 text-sm resize-none focus:ring-2 focus:ring-teal-300 focus:border-teal-500">{{ $product->description }}</textarea>
      </div>

      {{-- Gambar Produk --}}
      <div class="md:col-span-2">
        <label class="block text-xs font-medium text-gray-700 mb-1">Gambar Produk</label>
        <div class="flex items-start gap-3">
          <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}" 
               class="w-20 h-20 object-cover rounded border border-gray-200 flex-shrink-0">
          <div class="flex-1">
            <input type="file" name="image" accept="image/*"
                   class="w-full text-xs text-gray-600
                          file:py-1.5 file:px-3 file:rounded file:border-0
                          file:text-xs file:font-medium
                          file:bg-teal-50 file:text-teal-700
                          hover:file:bg-teal-100 focus:outline-none">
            <p class="text-xs text-gray-500 mt-1">Upload baru untuk mengganti (opsional)</p>
          </div>
        </div>
      </div>

      {{-- Harga & Ukuran --}}
      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Harga Dasar (2x3)</label>
        <input type="number" name="price" required class="w-full border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-2 focus:ring-teal-300 focus:border-teal-500" value="{{ $product->price }}">
      </div>

      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
        <select name="live" class="w-full border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-2 focus:ring-teal-300 focus:border-teal-500">
          <option value="1" {{ $product->live ? 'selected' : '' }}>Tampil</option>
          <option value="0" {{ !$product->live ? 'selected' : '' }}>Tidak Tampil</option>
        </select>
      </div>

      {{-- Tombol Update --}}
      <div class="md:col-span-2 pt-2">
        <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-medium py-2 rounded-md transition text-sm">
          Update Produk
        </button>
      </div>
    </div>
  </form>

  {{-- Pengaturan Owner (Collapsible) --}}
  @if(isset($isOwner) && $isOwner)
  <div class="mt-4 space-y-3">
    {{-- Harga per Ukuran --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
      <button type="button" onclick="toggleSection('sizePrices')" class="w-full flex items-center justify-between p-3 text-left hover:bg-gray-50 transition">
        <div class="flex items-center gap-2">
          <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="text-sm font-medium text-gray-800">Harga per Ukuran</span>
          <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-xs rounded">Owner</span>
        </div>
        <svg id="icon-sizePrices" class="w-4 h-4 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div id="content-sizePrices" class="hidden px-3 pb-3">
        <form method="POST" action="{{ url('/admin/products/detail/' . $product->id . '/size-prices') }}">
          @csrf
          <div class="grid grid-cols-2 gap-2 mb-3">
            @php
              $sizePrices = $product->size_prices ?? [];
              $sizes = ['2x3', '3x4', '4x6', '6x8'];
            @endphp
            @foreach($sizes as $size)
              <div>
                <label class="block text-xs text-gray-600 mb-1">{{ $size }} @if($size == '2x3')<span class="text-gray-400">(Dasar)</span>@endif</label>
                <input type="number" 
                       name="size_prices[{{ $size }}]" 
                       value="{{ $sizePrices[$size] ?? ($size == '2x3' ? $product->price : '') }}" 
                       min="0" step="100"
                       {{ $size == '2x3' ? 'required' : '' }}
                       placeholder="Auto"
                       class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:ring-1 focus:ring-blue-500">
              </div>
            @endforeach
          </div>
          <div class="flex gap-2">
            <button type="submit" class="flex-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition">Simpan</button>
            <button type="button" onclick="resetSizePrices()" class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-xs rounded transition">Reset</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Batas Tawar per Ukuran --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
      <button type="button" onclick="toggleSection('minPrices')" class="w-full flex items-center justify-between p-3 text-left hover:bg-gray-50 transition">
        <div class="flex items-center gap-2">
          <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="text-sm font-medium text-gray-800">Batas Tawar per Ukuran</span>
          <span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 text-xs rounded">Owner</span>
        </div>
        <svg id="icon-minPrices" class="w-4 h-4 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div id="content-minPrices" class="hidden px-3 pb-3">
        <p class="text-xs text-gray-600 mb-3">Batas bawah harga yang diperbolehkan untuk ditawar. Tawaran di bawah batas ini tidak akan diterima.</p>
        <form method="POST" action="{{ url('/admin/products/detail/' . $product->id . '/min-price-per-size') }}" class="mb-3">
          @csrf
          <div class="grid grid-cols-2 gap-2 mb-3">
            @php
              $minPricePerSize = $product->min_price_per_size ?? [];
              $sizes = ['2x3', '3x4', '4x6', '6x8'];
            @endphp
            @foreach($sizes as $size)
              <div>
                <label class="block text-xs text-gray-600 mb-1">Batas {{ $size }}</label>
                <input type="number" 
                       name="min_price_per_size[{{ $size }}]" 
                       value="{{ $minPricePerSize[$size] ?? '' }}" 
                       min="0" step="100"
                       placeholder="Auto"
                       class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:ring-1 focus:ring-amber-500">
              </div>
            @endforeach
          </div>
          <div class="flex gap-2">
            <button type="submit" class="flex-1 px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-medium rounded transition">Simpan</button>
            <button type="button" onclick="resetMinPricePerSize()" class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-xs rounded transition">Reset</button>
          </div>
        </form>
        <form method="POST" action="{{ url('/admin/products/detail/' . $product->id . '/min-buying-stock') }}" class="border-t pt-3">
          @csrf
          <label class="block text-xs text-gray-600 mb-1">Min Quantity untuk Tawar</label>
          <p class="text-xs text-gray-500 mb-2">Set 0 untuk menonaktifkan fitur tawar menawar</p>
          <div class="flex gap-2">
            <input type="number" name="min_buying_stock" value="{{ $product->min_buying_stock ?? 0 }}" min="0" required
                   class="flex-1 border border-gray-300 rounded px-2 py-1 text-xs focus:ring-1 focus:ring-amber-500">
            <button type="submit" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs rounded transition">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif

  {{-- Bagian Varian --}}
  <div class="mt-4 bg-white border border-gray-200 rounded-lg shadow-sm">
    <div class="flex justify-between items-center p-3 border-b">
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
        </svg>
        <h3 class="text-sm font-semibold text-gray-800">Varian Produk</h3>
        <span class="px-1.5 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">{{ $product->variants->count() }} varian</span>
      </div>
      <button onclick="openVariantModal()" 
              class="flex items-center gap-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs px-3 py-1.5 rounded-md transition shadow-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Varian
      </button>
    </div>

    <div class="p-3">
      @forelse ($product->variants as $variant)
        <div class="flex items-center justify-between p-2.5 mb-2 bg-gray-50 rounded-lg border border-gray-200 hover:border-teal-300 hover:bg-teal-50/30 transition group">
          <div class="flex items-center gap-3 flex-1">
            {{-- Color Badge --}}
            <div class="flex-shrink-0">
              <div class="w-8 h-8 rounded-full border-2 border-white shadow-sm flex items-center justify-center text-xs font-medium
                @if(str_contains(strtolower($variant->color), 'biru')) bg-blue-500 text-white
                @elseif(str_contains(strtolower($variant->color), 'oranye')) bg-orange-500 text-white
                @elseif(str_contains(strtolower($variant->color), 'coklat')) bg-amber-700 text-white
                @elseif(str_contains(strtolower($variant->color), 'hijau')) bg-green-500 text-white
                @else bg-gray-400 text-white
                @endif">
                {{ strtoupper(substr($variant->color, 0, 1)) }}
              </div>
            </div>
            
            {{-- Info --}}
            <div class="flex-1 min-w-0">
              <div class="text-sm font-medium text-gray-800">{{ ucfirst($variant->color) }}</div>
              <div class="flex items-center gap-2 mt-0.5">
                <span class="text-xs text-gray-500">Stok:</span>
                <span class="text-xs font-semibold {{ $variant->stock > 10 ? 'text-green-600' : ($variant->stock > 0 ? 'text-orange-600' : 'text-red-600') }}">
                  {{ $variant->stock }} pcs
                </span>
              </div>
            </div>
          </div>

          {{-- Actions --}}
          <div class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
            <button onclick="editVariant({{ $variant->id }}, '{{ $variant->color }}', {{ $variant->stock }})" 
                    class="p-1.5 text-teal-600 hover:bg-teal-100 rounded transition" 
                    title="Edit varian">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </button>
            <button onclick="deleteVariant({{ $variant->id }})" 
                    class="p-1.5 text-red-600 hover:bg-red-100 rounded transition"
                    title="Hapus varian">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>
      @empty
        <div class="text-center py-8">
          <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
          </svg>
          <p class="text-sm text-gray-500 mb-2">Belum ada varian produk</p>
          <button onclick="openVariantModal()" 
                  class="text-xs text-teal-600 hover:text-teal-700 font-medium">
            + Tambah varian pertama
          </button>
        </div>
      @endforelse
    </div>
  </div>

  {{-- Modal Tambah/Edit Varian --}}
  <div id="variantModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="flex justify-between items-center p-4 border-b">
          <h3 class="text-base font-semibold text-gray-800" id="modalTitle">Tambah Varian</h3>
          <button onclick="closeVariantModal()" class="text-gray-400 hover:text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <form id="variantForm" class="p-4">
          @csrf
          <input type="hidden" id="variantId" name="variant_id">
          
          <div class="mb-3">
            <label class="block text-xs font-medium text-gray-700 mb-1">Warna Varian</label>
            <select id="variantColor" name="color" class="w-full border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-2 focus:ring-teal-300 focus:border-teal-500" required>
              <option value="">Pilih Warna</option>
              <option value="biru silver">Biru Silver</option>
              <option value="biru polos">Biru Polos</option>
              <option value="oranye silver">Oranye Silver</option>
              <option value="oranye polos">Oranye Polos</option>
              <option value="coklat polos">Coklat Polos</option>
              <option value="coklat silver">Coklat Silver</option>
              <option value="hijau silver">Hijau Silver</option>
              <option value="hijau polos">Hijau Polos</option>
            </select>
          </div>

          <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-1">Stok</label>
            <input type="number" id="variantStock" name="stock" placeholder="Masukkan jumlah stok" min="0" 
                   class="w-full border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-2 focus:ring-teal-300 focus:border-teal-500" required>
          </div>

          <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white font-medium py-1.5 rounded-md transition text-sm">
              <span id="submitText">Tambah Varian</span>
            </button>
            <button type="button" onclick="closeVariantModal()" 
                    class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-1.5 rounded-md transition text-sm">
              Batal
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function toggleSection(sectionId) {
  const content = document.getElementById('content-' + sectionId);
  const icon = document.getElementById('icon-' + sectionId);
  if (content.classList.contains('hidden')) {
    content.classList.remove('hidden');
    icon.classList.add('rotate-180');
  } else {
    content.classList.add('hidden');
    icon.classList.remove('rotate-180');
  }
}

function resetSizePrices() {
  if (confirm('Reset semua harga per ukuran ke auto-calculate?')) {
    const inputs = document.querySelectorAll('input[name^="size_prices"]');
    inputs.forEach(input => {
      if (input.name !== 'size_prices[2x3]') {
        input.value = '';
      }
    });
  }
}

function resetMinPricePerSize() {
  if (confirm('Reset semua batas tawar per ukuran ke auto-calculate?')) {
    const inputs = document.querySelectorAll('input[name^="min_price_per_size"]');
    inputs.forEach(input => {
      input.value = '';
    });
  }
}

function openVariantModal() {
  console.log('Opening variant modal...');
  document.getElementById('variantModal').classList.remove('hidden');
  document.getElementById('modalTitle').textContent = 'Tambah Varian';
  document.getElementById('submitText').textContent = 'Tambah Varian';
  document.getElementById('variantForm').reset();
  document.getElementById('variantId').value = '';
  
  // Debug: Check if form elements exist
  console.log('Form elements check:', {
    variantModal: !!document.getElementById('variantModal'),
    variantForm: !!document.getElementById('variantForm'),
    variantId: !!document.getElementById('variantId'),
    variantColor: !!document.getElementById('variantColor'),
    variantStock: !!document.getElementById('variantStock')
  });
}

function closeVariantModal() {
  document.getElementById('variantModal').classList.add('hidden');
}

function editVariant(id, color, stock) {
  console.log('Edit variant called with:', {id, color, stock});
  
  // Show modal first
  document.getElementById('variantModal').classList.remove('hidden');
  document.getElementById('modalTitle').textContent = 'Edit Varian';
  document.getElementById('submitText').textContent = 'Update Varian';
  
  // Wait a bit for modal to be visible, then set values
  setTimeout(() => {
    // Set form values
    const variantIdEl = document.getElementById('variantId');
    const variantColorEl = document.getElementById('variantColor');
    const variantStockEl = document.getElementById('variantStock');
    
    if (variantIdEl) variantIdEl.value = id;
    if (variantColorEl) variantColorEl.value = color;
    if (variantStockEl) variantStockEl.value = stock;
    
    console.log('Form elements found:', {
      variantId: !!variantIdEl,
      variantColor: !!variantColorEl,
      variantStock: !!variantStockEl
    });
    
    console.log('Form values set:', {
      variantId: variantIdEl ? variantIdEl.value : 'NOT FOUND',
      variantColor: variantColorEl ? variantColorEl.value : 'NOT FOUND',
      variantStock: variantStockEl ? variantStockEl.value : 'NOT FOUND'
    });
  }, 100);
}

function deleteVariant(id) {
  if (confirm('Apakah Anda yakin ingin menghapus varian ini?')) {
    console.log('Deleting variant with ID:', id);
    
    const url = `/admin/products/detail/{{ $product->id }}/variants/${id}/delete`;
    
    console.log('Delete request data:', {
      url: url,
      method: 'DELETE'
    });
    
    fetch(url, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json',
      }
    })
    .then(response => {
      console.log('Delete response status:', response.status);
      console.log('Delete response URL:', response.url);
      
      if (!response.ok) {
        // Try to get error message from response
        return response.text().then(text => {
          console.log('Delete error response body:', text);
          throw new Error(`HTTP error! status: ${response.status} - ${text}`);
        });
      }
      return response.json();
    })
    .then(data => {
      console.log('Delete response data:', data);
      if (data.success) {
        alert('Variant berhasil dihapus!');
        location.reload();
      } else {
        alert('Gagal menghapus varian: ' + (data.message || 'Error tidak diketahui'));
      }
    })
    .catch(error => {
      console.error('Delete error:', error);
      alert('Terjadi kesalahan: ' + error.message);
    });
  }
}

// Handle form submission
document.getElementById('variantForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  // Get form elements directly to ensure we have the values
  const variantIdEl = document.getElementById('variantId');
  const colorEl = document.getElementById('variantColor');
  const stockEl = document.getElementById('variantStock');
  
  const variantId = variantIdEl ? variantIdEl.value : '';
  const color = colorEl ? colorEl.value : '';
  const stock = stockEl ? stockEl.value : '';
  
  console.log('Form elements check:', {
    variantIdEl: !!variantIdEl,
    colorEl: !!colorEl,
    stockEl: !!stockEl,
    variantId: variantId,
    color: color,
    stock: stock
  });
  
  // Validate required fields
  if (!color) {
    alert('Warna varian harus dipilih!');
    return;
  }
  if (!stock || stock < 0) {
    alert('Stok harus diisi dan tidak boleh negatif!');
    return;
  }
  
  const formData = new FormData();
  formData.append('color', color);
  formData.append('stock', stock);
  formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
  
  // For update, use POST with _method field
  if (variantId) {
    formData.append('_method', 'PUT');
  }
  
  const url = variantId ? 
    `/admin/products/detail/{{ $product->id }}/variants/${variantId}/update` : 
    `/admin/products/detail/{{ $product->id }}/variants/create`;
  const method = 'POST'; // Always use POST, with _method field for PUT
  
  console.log('Form data:', {
    variantId: variantId,
    url: url,
    method: method,
    color: color,
    stock: stock,
    _token: formData.get('_token')
  });
  
  // Show loading state
  const submitBtn = this.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.innerHTML = 'Menyimpan...';
  submitBtn.disabled = true;
  
  fetch(url, {
    method: method,
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Accept': 'application/json',
    },
    body: formData
  })
  .then(response => {
    console.log('Response status:', response.status);
    console.log('Response headers:', response.headers);
    console.log('Response URL:', response.url);
    
    if (!response.ok) {
      // Try to get error message from response
      return response.text().then(text => {
        console.log('Error response body:', text);
        throw new Error(`HTTP error! status: ${response.status} - ${text}`);
      });
    }
    return response.json();
  })
  .then(data => {
    console.log('Response data:', data);
    if (data.success) {
      alert('Variant berhasil disimpan!');
      closeVariantModal();
      location.reload();
    } else {
      alert('Gagal menyimpan varian: ' + (data.message || 'Error tidak diketahui'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Terjadi kesalahan: ' + error.message);
  })
  .finally(() => {
    // Reset button state
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
  });
});
</script>
@endsection

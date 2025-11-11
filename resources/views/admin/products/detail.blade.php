@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto">
  {{-- Breadcrumb --}}
  <nav class="mb-6">
    <ol class="flex items-center space-x-2 text-sm text-gray-500">
      <li>
        <a href="{{ url('/admin') }}" class="hover:text-teal-600 transition">Dashboard</a>
      </li>
      <li class="flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mx-2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ url('/admin/products') }}" class="hover:text-teal-600 transition">Kelola Produk</a>
      </li>
      <li class="flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mx-2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <span class="text-gray-900 font-medium">Edit: {{ $product->name }}</span>
      </li>
    </ol>
  </nav>

  <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Produk: {{ $product->name }}</h2>

  <form method="POST" enctype="multipart/form-data" class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-4">
    @csrf

    {{-- Nama Produk --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
      <input type="text" name="name" class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300" value="{{ $product->name }}">
    </div>

    {{-- Deskripsi --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
      <textarea name="description" rows="3" class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm resize-none focus:ring-2 focus:ring-teal-300">{{ $product->description }}</textarea>
    </div>

    {{-- Gambar Produk --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
      
      {{-- Tampilkan gambar yang sudah ada --}}
      <div class="mb-3">
        <p class="text-sm text-gray-600 mb-2">Gambar saat ini:</p>
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
             class="w-32 h-32 object-cover rounded-lg border border-gray-200">
      </div>
      
      {{-- Upload gambar baru --}}
      <input type="file" name="image" accept="image/*"
             class="w-full text-sm text-gray-600
                    file:py-2 file:px-4 file:rounded file:border-0
                    file:text-sm file:font-semibold
                    file:bg-teal-100 file:text-teal-700
                    hover:file:bg-teal-200 focus:outline-none focus:ring-2 focus:ring-teal-300">
      <p class="text-xs text-gray-500 mt-1">Upload gambar baru untuk mengganti gambar saat ini (opsional)</p>
    </div>

    {{-- Harga --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
      <input type="number" name="price" required class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300" value="{{ $product->price }}">
    </div>

    {{-- Ukuran --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran</label>
      <select name="size" required class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300">
        <option disabled>Pilih Ukuran</option>
        <option value="2x3" {{ $product->size == '2x3' ? 'selected' : '' }}>2x3</option>
        <option value="3x4" {{ $product->size == '3x4' ? 'selected' : '' }}>3x4</option>
        <option value="4x6" {{ $product->size == '4x6' ? 'selected' : '' }}>4x6</option>
        <option value="6x8" {{ $product->size == '6x8' ? 'selected' : '' }}>6x8</option>
      </select>
    </div>

    {{-- Tampilkan atau Tidak --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Status Tampilkan</label>
      <select name="live" class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300">
        <option value="1" {{ $product->live ? 'selected' : '' }}>Tampil</option>
        <option value="0" {{ !$product->live ? 'selected' : '' }}>Tidak Tampil</option>
      </select>
    </div>

    {{-- Tombol Update --}}
    <div class="pt-2">
      <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 rounded-md transition">
        Update Produk
      </button>
    </div>
  </form>

  {{-- Bagian Varian --}}
  <div class="mt-10 flex justify-between items-center">
    <h3 class="text-xl font-bold text-gray-800">Varian Produk</h3>
    <button onclick="openVariantModal()" 
            class="bg-teal-600 hover:bg-teal-700 text-white text-sm px-4 py-2 rounded-md transition">
      + Tambah Varian
    </button>
  </div>

  <div class="mt-4 overflow-x-auto">
    <table class="w-full table-auto border text-sm">
      <thead class="bg-gray-100 text-gray-700 font-semibold">
        <tr>
          <th class="px-4 py-2 text-left">Warna</th>
          <th class="px-4 py-2 text-left">Stok</th>
          <th class="px-4 py-2 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($product->variants as $variant)
          <tr class="border-t">
            <td class="px-4 py-2">{{ $variant->color }}</td>
            <td class="px-4 py-2">{{ $variant->stock }}</td>
            <td class="px-4 py-2 text-center">
              <button onclick="editVariant({{ $variant->id }}, '{{ $variant->color }}', {{ $variant->stock }})" 
                      class="bg-white border border-teal-600 text-teal-600 hover:bg-teal-50 text-xs px-3 py-1 rounded-md transition mr-1">
                Edit
              </button>
              <button onclick="deleteVariant({{ $variant->id }})" 
                      class="bg-white border border-red-600 text-red-600 hover:bg-red-50 text-xs px-3 py-1 rounded-md transition">
                Hapus
              </button>
            </td>
          </tr>
        @empty
          <tr class="border-t">
            <td colspan="3" class="px-4 py-2 text-center text-gray-500">Belum ada varian</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Modal Tambah/Edit Varian --}}
  <div id="variantModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="flex justify-between items-center p-6 border-b">
          <h3 class="text-lg font-semibold text-gray-800" id="modalTitle">Tambah Varian</h3>
          <button onclick="closeVariantModal()" class="text-gray-400 hover:text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <form id="variantForm" class="p-6">
          @csrf
          <input type="hidden" id="variantId" name="variant_id">
          
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Warna Varian</label>
            <select id="variantColor" name="color" class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300" required>
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

          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
            <input type="number" id="variantStock" name="stock" placeholder="Masukkan jumlah stok" min="0" 
                   class="w-full border border-teal-600 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300" required>
          </div>

          <div class="flex gap-3">
            <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 rounded-md transition">
              <span id="submitText">Tambah Varian</span>
            </button>
            <button type="button" onclick="closeVariantModal()" 
                    class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 rounded-md transition">
              Batal
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
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

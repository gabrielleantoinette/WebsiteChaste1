<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Custom Terpal | CHASTE</title>
  @vite('resources/css/app.css')
</head>
<script>
    function changeQty(amount) {
        const input = document.getElementById('qtyInput');
        let current = parseInt(input.value);
        const min = parseInt(input.min) || 0;

        if (!isNaN(current)) {
            let newVal = current + amount;
            if (newVal < min) newVal = min;
            input.value = newVal;
        }
    }
    let index = 0;
    function toggleTinggi() {
    const checkbox = document.getElementById('isVolumeCheckbox');
    const tinggiInput = document.getElementById('inputTinggi');

    if (checkbox.checked) {
      tinggiInput.disabled = false;
      tinggiInput.classList.remove('bg-gray-200', 'text-gray-600');
    } else {
      tinggiInput.disabled = true;
      tinggiInput.classList.add('bg-gray-200', 'text-gray-600');
    }
  }
</script>
<style>
    /* Hilangkan spinner untuk input type number di semua browser */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
        /* Firefox */
    }
</style>
<body class="bg-white font-sans text-gray-800">

@include('layouts.customer-nav')

<!-- Form Custom Terpal -->
<section class="px-6 md:px-20 py-16">
  <div class="max-w-5xl mx-auto border rounded-xl p-8">
    <h2 class="text-2xl font-bold mb-6">Custom Terpal</h2>

    <form class="grid md:grid-cols-2 gap-8">
      <!-- Kolom Kiri -->
      <div class="space-y-4">
      <div>
        <label class="text-sm font-medium text-gray-700">Apa kebutuhan anda? <span class="text-red-500">*</span></label>
        <select id="kebutuhanSelect" class="w-full border border-gray-300 rounded-md p-2 text-sm">
          <option value="">-- Pilih Kebutuhan --</option>
          <option value="tambak">Kebutuhan tambak/kolam</option>
          <option value="bertani">Kebutuhan bertani</option>
          <option value="angkutan">Kebutuhan melindungi angkutan</option>
          <option value="tenda">Kebutuhan tenda</option>
          <option value="kebocoran">Kebutuhan kebocoran</option>
          <option value="bangunan">Kebutuhan bangunan</option>
          <option value="garam">Kebutuhan melindungi garam</option>
        </select>
      </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Bahan yang Dipesan <span class="text-red-500">*</span></label>
          <input type="text" placeholder="Tulis Bahan Yang Ingin Dipesan..." class="w-full border border-gray-300 rounded-md p-2 text-sm">
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Ukuran yang Dipesan <span class="text-red-500">*</span></label>
          <div class="flex items-center gap-2 mb-2">
            <input type="checkbox" id="isVolumeCheckbox" onclick="toggleTinggi()"> 
            <span class="text-sm">Terpal Bervolume (Klik jika butuh)</span>
          </div>
          <div class="space-y-2">
            <input type="text" placeholder="Masukkan Panjang (dalam meter)*" class="w-full border border-gray-300 rounded-md p-2 text-sm">
            <input type="text" placeholder="Masukkan Lebar (dalam meter)*" class="w-full border border-gray-300 rounded-md p-2 text-sm">
            <input type="text" id="inputTinggi" placeholder="Masukkan Tinggi (dalam meter)" 
                class="w-full border border-gray-300 rounded-md p-2 text-sm bg-gray-200 text-gray-600" 
                disabled>
          </div>
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Warna <span class="text-red-500">*</span></label>
          <select class="w-full border border-gray-300 rounded-md p-2 text-sm">
            <option>Pilih...</option>
          </select>
        </div>
      </div>

      <!-- Kolom Kanan -->
      <div class="space-y-4">
        <div>
          <label class="text-sm font-medium text-gray-700">Bahan yang Direkomendasikan</label>
          <input id="bahanRekomendasi" type="text" value="" 
                class="w-full border border-gray-300 rounded-md p-2 text-sm bg-gray-100" disabled>
        </div>
        <div id="deskripsiBahan" class="mt-2 text-sm text-gray-600 italic"></div>

        <div>
          <label class="text-sm font-medium text-gray-700">Ring (Rp 50/Ring)</label>
            <div class="flex items-center border border-gray-300 w-max rounded-md overflow-hidden">
                <button type="button" class="px-3 py-2 text-lg hover:bg-gray-100"
                    onclick="changeQty(-1)">-</button>

                <input type="number" id="qtyInput" value="0" min="0"
                    class="w-12 text-center border-l border-r border-gray-300 outline-none text-sm py-2">

                <button type="button" class="px-3 py-2 text-lg hover:bg-gray-100"
                    onclick="changeQty(1)">+</button>
            </div>
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Tali (Rp 500 x Keliling Terpal)</label>
          <div class="flex items-center gap-2">
            <input type="checkbox"> <span class="text-sm">Perlu Tali</span>
          </div>
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Catatan Tambahan/Pesan:</label>
          <textarea rows="4" class="w-full border border-gray-300 rounded-md p-2 text-sm resize-none"></textarea>
        </div>
      </div>
    </form>

    <!-- Biaya + Tombol -->
    <div class="mt-8 flex flex-col md:flex-row items-center justify-between gap-4">
      <div class="text-lg font-medium">Total Biaya Custom: <span class="font-bold text-teal-600">Rp 510.000</span></div>
      <button class="bg-[#D9F2F2] hover:bg-teal-200 text-gray-800 font-semibold py-3 px-6 rounded-md transition">
        Tambah Ke Keranjang
      </button>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-[#D9F2F2] py-10 px-6 md:px-20 text-sm text-gray-700 mt-20">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- Brand -->
    <div>
      <h3 class="text-xl font-bold text-gray-900 mb-3">CHASTE</h3>
      <p class="mb-4 max-w-xs">Kami membantu anda menyediakan terpal terbaik.</p>
      <div class="flex space-x-4 text-lg">
        <a href="#">📷</a>
        <a href="#">🐦</a>
        <a href="#">📘</a>
      </div>
    </div>

    <!-- Informasi -->
    <div class="space-y-2">
      <h4 class="font-semibold text-gray-800 mb-2">Informasi</h4>
      <a href="#" class="block hover:text-black">Tentang</a>
      <a href="#" class="block hover:text-black">Produk</a>
    </div>

    <!-- Kontak -->
    <div class="space-y-2">
      <h4 class="font-semibold text-gray-800 mb-2">Kontak Kami</h4>
      <p>Telp: 089123231221</p>
      <p>E-mail: xyz@bca</p>
    </div>
  </div>
  <div class="text-center text-xs text-gray-500 mt-8">
    © 2025 Hak Cipta Dilindungi
  </div>
</footer>

<script>
  const kebutuhanSelect = document.getElementById('kebutuhanSelect');
  const bahanRekomendasi = document.getElementById('bahanRekomendasi');
  const deskripsiBahan = document.getElementById('deskripsiBahan');

  const rekomendasiMap = {
  tambak: {
    bahan: 'A7, A8',
    deskripsi: [
      'A7 lebih ekonomis untuk tambak kecil',
      'A8 lebih tebal dan tahan lama untuk tambak besar'
    ]
  },
  bertani: {
    bahan: 'A3, A4',
    deskripsi: [
      'A3 cocok untuk penutup lahan ringan',
      'A4 sedikit lebih kuat dan tahan air'
    ]
  },
  angkutan: {
    bahan: 'Keep Jep, Ulin Orchid',
    deskripsi: [
      'Keep Jep cocok untuk barang kering',
      'Ulin Orchid lebih kuat untuk beban berat dan tahan hujan'
    ]
  },
  tenda: {
    bahan: 'A4, A5',
    deskripsi: [
      'A4 untuk tenda indoor atau sementara',
      'A5 lebih kuat untuk outdoor dan cuaca ekstrem'
    ]
  },
  kebocoran: {
    bahan: 'A4, A5',
    deskripsi: [
      'A4 cukup untuk tutup bocoran ringan',
      'A5 lebih tahan terhadap tekanan dan tahan sobek'
    ]
  },
  bangunan: {
    bahan: 'A8, A10',
    deskripsi: [
      'A8 digunakan untuk pelindung proyek ringan',
      'A10 lebih tebal untuk proyek konstruksi berat'
    ]
  },
  garam: {
    bahan: 'A12, A15',
    deskripsi: [
      'A12 cukup untuk tempat penyimpanan sementara',
      'A15 tahan garam dan sinar UV'
    ]
  }
};


kebutuhanSelect.addEventListener('change', function () {
  const value = kebutuhanSelect.value;
  const data = rekomendasiMap[value] || { bahan: '', deskripsi: [] };

  bahanRekomendasi.value = data.bahan;

  // Tampilkan dalam bentuk bullet
  if (data.deskripsi.length > 0) {
    deskripsiBahan.innerHTML = `<ul class="list-disc pl-5 space-y-1 text-gray-600 text-sm italic">
      ${data.deskripsi.map(item => `<li>${item}</li>`).join('')}
    </ul>`;
  } else {
    deskripsiBahan.innerHTML = '';
  }
});
</script>



</body>
</html>

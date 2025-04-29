<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Custom Terpal | CHASTE</title>
  @vite('resources/css/app.css')
</head>
<body class="bg-white font-sans text-gray-800">

@include('layouts.customer-nav')

<section class="px-6 md:px-20 py-8">
  <a href="{{ url()->previous() }}" 
      class="inline-flex items-center gap-2 bg-teal-100 hover:bg-teal-200 text-teal-700 font-medium py-2 px-4 rounded-md text-sm transition">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
      Kembali
  </a>
  <div class="max-w-5xl mx-auto border rounded-xl p-8 bg-white">
    <h2 class="text-2xl font-bold mb-6">Custom Terpal</h2>

    <form method="POST" action="{{ route('keranjang.custom.add') }}" class="grid md:grid-cols-2 gap-8">
      @csrf
      <input type="hidden" name="harga_custom" id="hargaCustomInput" value="0">
      <input type="hidden" name="kebutuhan_custom" id="kebutuhanCustomInput">
      <input type="hidden" name="ukuran_custom" id="ukuranCustomInput">
      <input type="hidden" name="warna_custom" id="warnaCustomInput">
      <input type="hidden" name="jumlah_ring_custom" id="jumlahRingCustomInput">
      <input type="hidden" name="pakai_tali_custom" id="pakaiTaliCustomInput">
      <input type="hidden" name="catatan_custom" id="catatanCustomInput">


      <div class="space-y-4">
        <div>
          <label class="text-sm font-medium text-gray-700">Kebutuhan Anda <span class="text-red-500">*</span></label>
          <select name="kebutuhan" id="kebutuhanSelect" required class="w-full border border-gray-300 rounded-md p-2 text-sm">
            <option value="">-- Pilih Kebutuhan --</option>
            @foreach ($kebutuhanOptions as $key => $label)
              <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Pilih Bahan <span class="text-red-500">*</span></label>
          <select name="bahan" id="bahanSelect" required class="w-full border border-gray-300 rounded-md p-2 text-sm">
            <option value="">-- Pilih Bahan --</option>
            @foreach ($materials as $material)
              <option value="{{ $material->id }}">{{ $material->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Ukuran Terpal (meter) <span class="text-red-500">*</span></label>
          <input type="number" name="panjang" placeholder="Panjang" required class="w-full border border-gray-300 rounded-md p-2 text-sm">
          <input type="number" name="lebar" placeholder="Lebar" required class="w-full border border-gray-300 rounded-md p-2 text-sm mt-2">
          <div class="flex items-center gap-2 mt-2">
            <input type="checkbox" id="isVolumeCheckbox" onclick="toggleTinggi()">
            <span class="text-sm">Terpal Bervolume</span>
          </div>
          <input type="number" id="inputTinggi" name="tinggi" placeholder="Tinggi" disabled class="w-full border border-gray-300 rounded-md p-2 text-sm bg-gray-200 text-gray-600 mt-2">
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Pilih Warna <span class="text-red-500">*</span></label>
          <select name="warna" id="warnaSelect" required class="w-full border border-gray-300 rounded-md p-2 text-sm">
            <option value="">-- Pilih Warna --</option>
          </select>
        </div>
      </div>

      <div class="space-y-4">
        <div>
          <label class="text-sm font-medium text-gray-700">Rekomendasi Bahan</label>
          <input type="text" id="bahanRekomendasi" class="w-full border border-gray-300 rounded-md p-2 text-sm bg-gray-100" disabled>
          <div id="deskripsiBahan" class="mt-2 text-sm text-gray-600 italic"></div>
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Jumlah Ring</label>
          <div class="flex items-center border border-gray-300 w-max rounded-md overflow-hidden">
            <button type="button" onclick="changeQty(-1)" class="px-3 py-2 text-lg hover:bg-gray-100">-</button>
            <input type="number" id="qtyInput" name="jumlah_ring" value="0" min="0" class="w-12 text-center border-l border-r border-gray-300 outline-none text-sm py-2">
            <button type="button" onclick="changeQty(1)" class="px-3 py-2 text-lg hover:bg-gray-100">+</button>
          </div>
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Butuh Tali?</label>
          <div class="flex items-center gap-2">
            <input type="checkbox" name="pakai_tali" value="1">
            <span class="text-sm">Ya, perlu tali</span>
          </div>
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Jumlah Barang <span class="text-red-500">*</span></label>
          <div class="flex items-center border border-gray-300 w-max rounded-md overflow-hidden">
            <button type="button" onclick="changeBarangQty(-1)" class="px-3 py-2 text-lg hover:bg-gray-100">-</button>
            <input type="number" id="barangQtyInput" name="quantity" value="1" min="1" class="w-12 text-center border-l border-r border-gray-300 outline-none text-sm py-2">
            <button type="button" onclick="changeBarangQty(1)" class="px-3 py-2 text-lg hover:bg-gray-100">+</button>
          </div>
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Catatan Tambahan</label>
          <textarea name="catatan" rows="3" class="w-full border border-gray-300 rounded-md p-2 text-sm resize-none"></textarea>
        </div>
      </div>

      <div id="rincianHarga" class="col-span-2 mt-6 hidden">
        <div class="bg-[#D9F2F2] p-6 rounded-md">
          <h3 class="font-bold mb-4 text-gray-800 text-center">Rincian Estimasi Biaya</h3>
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <div id="detailBahan" class="flex justify-between">
                <span>Harga Bahan</span><span>Rp 0</span>
              </div>
              <div id="detailRing" class="flex justify-between">
                <span>Harga Ring</span><span>Rp 0</span>
              </div>
              <div id="detailTali" class="flex justify-between">
                <span>Harga Tali</span><span>Rp 0</span>
              </div>
            </div>
            <div class="flex flex-col justify-center items-center bg-white rounded-md p-4">
              <span class="text-xs text-gray-500">Estimasi Total</span>
              <div id="totalPrice" class="text-2xl font-bold text-teal-600 mt-1">Rp 0</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-span-2 flex flex-col md:flex-row items-center justify-between mt-8 gap-4">
        <div class="text-lg font-semibold text-gray-800"></div>
        <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 px-6 rounded-md transition">
          Tambah Ke Keranjang
        </button>
      </div>

    </form>
  </div>
</section>

{{-- FOOTER --}}
@include('layouts.footer')


{{-- JAVASCRIPT --}}
<script>
let selectedPrice = 0;

function toggleTinggi() {
  const checkbox = document.getElementById('isVolumeCheckbox');
  const tinggiInput = document.getElementById('inputTinggi');
  tinggiInput.disabled = !checkbox.checked;
  if (!checkbox.checked) {
    tinggiInput.classList.add('bg-gray-200', 'text-gray-600');
  } else {
    tinggiInput.classList.remove('bg-gray-200', 'text-gray-600');
  }
}

function changeQty(amount) {
  const input = document.getElementById('qtyInput');
  let val = parseInt(input.value) + amount;
  if (val < 0) val = 0;
  input.value = val;
  calculateTotal();
}

function changeBarangQty(amount) {
  const input = document.getElementById('barangQtyInput');
  let val = parseInt(input.value) + amount;
  if (val < 1) val = 1;
  input.value = val;
}


const bahanSelect = document.getElementById('bahanSelect');
const warnaSelect = document.getElementById('warnaSelect');
const kebutuhanSelect = document.getElementById('kebutuhanSelect');
const bahanRekomendasi = document.getElementById('bahanRekomendasi');
const deskripsiBahan = document.getElementById('deskripsiBahan');
const panjangInput = document.querySelector('input[name="panjang"]');
const lebarInput = document.querySelector('input[name="lebar"]');
const tinggiInput = document.querySelector('input[name="tinggi"]');
const jumlahRingInput = document.getElementById('qtyInput');
const pakaiTaliInput = document.querySelector('input[name="pakai_tali"]');
const totalPriceText = document.getElementById('totalPrice');

bahanSelect.addEventListener('change', function() {
  fetch(`/api/custom-materials/${this.value}/colors`)
    .then(response => response.json())
    .then(data => {
      warnaSelect.innerHTML = '<option value="">-- Pilih Warna --</option>';
      data.variants.forEach(warna => {
        warnaSelect.innerHTML += `<option value="${warna.color}">${warna.color}</option>`;
      });
      selectedPrice = data.price || 0;
      calculateTotal();
    });
});

function calculateTotal() {
  const panjang = parseFloat(panjangInput.value) || 0;
  const lebar = parseFloat(lebarInput.value) || 0;
  const jumlahRing = parseInt(jumlahRingInput.value) || 0;
  const pakaiTali = pakaiTaliInput.checked;

  const luas = panjang * lebar;
  const hargaBahan = luas * selectedPrice;
  const hargaRing = jumlahRing * 50;
  const keliling = 2 * (panjang + lebar);
  const hargaTali = pakaiTali ? keliling * 500 : 0;
  const quantity = parseInt(document.getElementById('barangQtyInput')?.value) || 1;
  const totalPerItem = hargaBahan + hargaRing + hargaTali;
  const grandTotal = totalPerItem * quantity;


  document.getElementById('detailBahan').innerHTML = `<span>Harga Bahan</span><span>Rp ${hargaBahan.toLocaleString('id-ID')}</span>`;
  document.getElementById('detailRing').innerHTML = `<span>Harga Ring</span><span>Rp ${hargaRing.toLocaleString('id-ID')}</span>`;
  document.getElementById('detailTali').innerHTML = `<span>Harga Tali</span><span>Rp ${hargaTali.toLocaleString('id-ID')}</span>`;
  document.getElementById('totalPrice').innerHTML = `Rp ${grandTotal.toLocaleString('id-ID')}`;

  document.getElementById('hargaCustomInput').value = Math.round(grandTotal);
  document.getElementById('rincianHarga').style.display = 'block';

}

function changeBarangQty(amount) {
  const input = document.getElementById('barangQtyInput');
  let val = parseInt(input.value) + amount;
  if (val < 1) val = 1;
  input.value = val;
  calculateTotal(); // << Tambahkan ini supaya langsung update harga
  updateHiddenInputs(); // << Sekalian update hidden inputnya
}

panjangInput.addEventListener('input', calculateTotal);
lebarInput.addEventListener('input', calculateTotal);
tinggiInput.addEventListener('input', calculateTotal);
jumlahRingInput.addEventListener('input', calculateTotal);
pakaiTaliInput.addEventListener('change', calculateTotal);
document.getElementById('barangQtyInput').addEventListener('input', () => {
  calculateTotal();
  updateHiddenInputs();
});


const rekomendasiMap = @json($rekomendasiMap);

function updateHiddenInputs() {
  // Ambil data dari form
  const kebutuhan = kebutuhanSelect.options[kebutuhanSelect.selectedIndex]?.text || '';
  const panjang = parseFloat(panjangInput.value) || 0;
  const lebar = parseFloat(lebarInput.value) || 0;
  const tinggi = parseFloat(tinggiInput.value) || 0;
  const ukuranText = tinggi > 0 ? `${panjang}m x ${lebar}m x ${tinggi}m` : `${panjang}m x ${lebar}m`;
  const warna = warnaSelect.options[warnaSelect.selectedIndex]?.text || '';
  const jumlahRing = parseInt(jumlahRingInput.value) || 0;
  const pakaiTali = pakaiTaliInput.checked ? 'Ya' : 'Tidak';
  const catatan = document.querySelector('textarea[name="catatan"]').value || '';

  // Isi ke hidden inputs
  document.getElementById('kebutuhanCustomInput').value = kebutuhan;
  document.getElementById('ukuranCustomInput').value = ukuranText;
  document.getElementById('warnaCustomInput').value = warna;
  document.getElementById('jumlahRingCustomInput').value = jumlahRing;
  document.getElementById('pakaiTaliCustomInput').value = pakaiTali;
  document.getElementById('catatanCustomInput').value = catatan;
}

// Event supaya hidden inputs selalu terupdate
panjangInput.addEventListener('input', () => {
  calculateTotal();
  updateHiddenInputs();
});

lebarInput.addEventListener('input', () => {
  calculateTotal();
  updateHiddenInputs();
});

tinggiInput.addEventListener('input', () => {
  calculateTotal();
  updateHiddenInputs();
});

jumlahRingInput.addEventListener('input', () => {
  calculateTotal();
  updateHiddenInputs();
});

pakaiTaliInput.addEventListener('change', () => {
  calculateTotal();
  updateHiddenInputs();
});

warnaSelect.addEventListener('change', updateHiddenInputs);

kebutuhanSelect.addEventListener('change', () => {
  const value = kebutuhanSelect.value;
  const data = rekomendasiMap[value] || { bahan: '', deskripsi: [] };

  bahanRekomendasi.value = data.bahan;

  if (data.deskripsi.length > 0) {
    deskripsiBahan.innerHTML = `<ul class="list-disc pl-5 space-y-1 text-gray-600 text-sm italic">
      ${data.deskripsi.map(item => `<li>${item}</li>`).join('')}
    </ul>`;
  } else {
    deskripsiBahan.innerHTML = '';
  }

  calculateTotal();
  updateHiddenInputs();
});

document.querySelector('textarea[name="catatan"]').addEventListener('input', updateHiddenInputs);


kebutuhanSelect.addEventListener('change', function () {
  const value = kebutuhanSelect.value;
  const data = rekomendasiMap[value] || { bahan: '', deskripsi: [] };

  bahanRekomendasi.value = data.bahan;

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

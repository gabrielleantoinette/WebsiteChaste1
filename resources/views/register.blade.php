<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | CHASTE</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-white font-sans">
    @include('layouts.customer-nav')

    <!-- Form Daftar -->
    <main class="min-h-screen bg-white flex items-center justify-center px-4 py-10">
        <div class="flex flex-col md:flex-row w-full max-w-5xl rounded-[20px] shadow-lg border border-gray-200 overflow-hidden bg-white">
            <!-- Gambar Kiri (Desktop only) -->
            <div class="hidden md:block md:w-1/2">
                <img src="{{ asset('images/terpal-login.png') }}" alt="Terpal Gulungan"
                    class="w-full h-full object-cover">
            </div>

            <!-- Form Kanan -->
            <div class="w-full md:w-1/2 p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">DAFTAR</h2>
                <form method="POST" action="{{ url('/register') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" required
                            class="w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-teal-300 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" name="name" required
                            class="w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-teal-300 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                        <input type="tel" name="phone" placeholder="+62" required
                            class="w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-teal-300 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-teal-300 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                        <input type="text" name="address"
                            class="w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-teal-300 outline-none">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Provinsi</label>
                            <select name="province" id="province" required
                                class="w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-teal-300 outline-none">
                                <option value="">Pilih Provinsi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kota</label>
                            <select name="city" id="city" required
                                class="w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-teal-300 outline-none">
                                <option value="">Pilih Kota/Kabupaten</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kode Pos</label>
                        <input type="text" name="postal_code"
                            class="w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-teal-300 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select name="gender" required
                            class="w-full mt-1 px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-teal-300 outline-none">
                            <option value="" disabled selected>Pilih jenis kelamin</option>
                            <option value="male">Laki-laki</option>
                            <option value="female">Perempuan</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full bg-[#D9F2F2] hover:bg-teal-200 text-gray-800 font-semibold py-3 rounded-md transition">
                        Daftar
                    </button>
                </form>

                <div class="text-center text-sm mt-4">
                    Sudah punya akun?
                    <a href="{{ url('/login') }}" class="text-red-500 font-medium">Masuk sekarang</a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    @include('layouts.footer')

    <script>
fetch('/indonesia_wilayah.json')
  .then(res => res.json())
  .then(data => {
    const provinsiSelect = document.getElementById('province');
    const kotaSelect = document.getElementById('city');
    // Isi dropdown provinsi
    Object.keys(data).forEach(prov => {
      const opt = document.createElement('option');
      opt.value = prov;
      opt.textContent = prov;
      provinsiSelect.appendChild(opt);
    });
    // Saat provinsi dipilih, isi dropdown kota
    provinsiSelect.addEventListener('change', function() {
      kotaSelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
      if (data[this.value]) {
        data[this.value].forEach(kota => {
          const opt = document.createElement('option');
          opt.value = kota;
          opt.textContent = kota;
          kotaSelect.appendChild(opt);
        });
      }
    });
  });
</script>

</body>

</html>

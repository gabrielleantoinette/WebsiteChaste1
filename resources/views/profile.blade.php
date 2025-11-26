@php
use Illuminate\Support\Facades\Storage;
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Produk Pembeli | CHASTE</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-white font-sans text-gray-800">
    {{-- Header --}}
    @include('layouts.customer-nav')
    <div class="px-4 sm:px-6 lg:px-12 py-10 sm:py-12 space-y-10 max-w-5xl mx-auto">
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <x-breadcrumb :items="[['label' => 'Profil']]" />
                <a href="{{ route('logout') }}" class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-md transition">
                    Keluar
                </a>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-800">Selamat datang, {{ $customer->name }}</h1>
        </div>

        {{-- Form Edit Profil --}}
        <div class="border rounded-xl p-6 md:p-10 space-y-6 bg-white shadow-sm">
            <h2 class="text-xl font-bold mb-4">Profil Saya</h2>
            <form class="space-y-4" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" onsubmit="return confirm('Apakah Anda yakin ingin menyimpan perubahan profil?')">
                @csrf
                <div class="flex flex-col items-center mb-6">
                    @if ($customer->profile_picture)
                        <img src="{{ Storage::url('photos/' . $customer->profile_picture) }}" alt="Foto Profil" class="w-30 h-30 rounded-full object-cover" style="width:120px;height:120px;">
                    @else
                        <div class="w-30 h-30 rounded-full bg-gray-200 flex items-center justify-center text-4xl font-bold text-gray-500" style="width:120px;height:120px;">
                            {{ strtoupper(substr($customer->name,0,1)) }}
                        </div>
                    @endif
                    <label for="profile_picture" class="mt-3 inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded cursor-pointer text-sm">Ubah Foto Profil</label>
                    <input type="file" name="profile_picture" id="profile_picture" class="hidden">
                </div>
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" name="name" id="name"
                        class="w-full border border-gray-300 rounded-md px-4 py-2" value="{{ $customer->name }}">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="text" name="email" id="email"
                        class="w-full border border-gray-300 rounded-md px-4 py-2" value="{{ $customer->email }}">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" name="phone" id="phone"
                        class="w-full border border-gray-300 rounded-md px-4 py-2" value="{{ $customer->phone }}">
                </div>
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <input type="text" name="address" id="address"
                        class="w-full border border-gray-300 rounded-md px-4 py-2" value="{{ $customer->address }}">
                </div>
                <div>
                    <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                    <select name="province" id="province" class="w-full border border-gray-300 rounded-md px-4 py-2" required>
                        <option value="">Pilih Provinsi</option>
                    </select>
                </div>
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                    <select name="city" id="city" class="w-full border border-gray-300 rounded-md px-4 py-2" required>
                        <option value="">Pilih Kota/Kabupaten</option>
                    </select>
                </div>
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                    <input type="text" name="postal_code" id="postal_code"
                        class="w-full border border-gray-300 rounded-md px-4 py-2" value="{{ $customer->postal_code }}">
                </div>
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" id="birth_date"
                        class="w-full border border-gray-300 rounded-md px-4 py-2" value="{{ $customer->birth_date }}">
                </div>
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                    <select name="gender" id="gender" class="w-full border border-gray-300 rounded-md px-4 py-2">
                        <option value="" disabled {{ $customer->gender == null ? 'selected' : '' }}>Pilih</option>
                        <option value="Laki-laki" {{ $customer->gender == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ $customer->gender == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="relative">
                    <input type="password" name="password" id="password"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 pr-10"
                        value="{{ $customer->password }}">
                    
                    <button type="button" onclick="togglePassword()" 
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                <button type="submit"
                    class="w-full bg-teal-500 text-white px-4 py-2 rounded-md hover:bg-teal-600 transition">
                    Simpan
                </button>
            </form>
        </div>


        {{-- Info Akun --}}
        <div class="border rounded-xl p-6 md:p-10 space-y-10 bg-white shadow-sm">
            {{-- Pesanan Saya --}}
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold">Pesanan Saya</h2>
                    <a href="{{ url('transaksi') }}" class="text-sm text-gray-600 hover:underline">
                        Lihat Riwayat pesanan &gt;
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 text-center gap-6 text-sm">
                    <div>
                    <a href="{{ url('transaksi?status=menunggukonfirmasi') }}" class="block">
                        <div class="text-4xl text-[#BBD3D3]">💸</div>
                        <p class="mt-2 font-semibold">Menunggu Konfirmasi Pembayaran</p>
                        <p>({{ $menungguPembayaranCount }})</p>
                    </a>
                    </div>
                    <div>
                    <a href="{{ url('transaksi?status=dikemas') }}" class="block">
                        <div class="text-4xl text-[#BBD3D3]">📦</div>
                        <p class="mt-2 font-semibold">Dikemas</p>
                        <p>({{ $dikemasCount }})</p>
                    </a>
                    </div>
                    <div>
                    <a href="{{ url('transaksi?status=dikirim') }}" class="block">
                        <div class="text-4xl text-[#BBD3D3]">🚚</div>
                        <p class="mt-2 font-semibold">Dikirim</p>
                        <p>({{ $dikirimCount }})</p>
                    </a>
                    </div>
                    <div>
                    <a href="{{ url('transaksi?status=beripenilaian') }}" class="block">
                        <div class="text-4xl text-[#BBD3D3]">⭐</div>
                        <p class="mt-2 font-semibold">Beri Penilaian</p>
                        <p>({{ $reviewCount }})</p>
                    </a>
                    </div>
                </div>
            </div>

            {{-- Keuangan --}}
            <div>
                <h2 class="text-xl font-bold mb-4">Keuangan Saya</h2>
                <a href="{{ route('profile.hutang') }}" class="block">
                    <div class="border rounded-md p-4 flex justify-between text-sm bg-[#F9F9F9] hover:bg-gray-100 cursor-pointer">
                        <div class="text-center flex-1">
                            <p class="text-gray-600">Hutang</p>
                            <p class="text-red-500 font-semibold text-lg">Rp {{ number_format($totalHutang, 0, ',', '.') }}</p>
                        </div>
                        <div class="border-l"></div>
                        <div class="text-center flex-1">
                            <p class="text-gray-600">Jumlah Tagihan Nota</p>
                            <p class="text-red-500 font-semibold text-lg">{{ $jumlahNotaBelumLunas }}</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    @include('layouts.footer')

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.96 9.96 0 012.042-3.338M9.88 9.88a3 3 0 104.24 4.24m1.436-1.436A3 3 0 019.88 9.88m9.043-4.043L4.12 19.88" />
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }

        fetch('/indonesia_wilayah.json')
  .then(res => res.json())
  .then(data => {
    const provinsiSelect = document.getElementById('province');
    const kotaSelect = document.getElementById('city');
    const selectedProv = "{{ $customer->province }}";
    const selectedKota = "{{ $customer->city }}";

    // Isi dropdown provinsi
    Object.keys(data).forEach(prov => {
      const opt = document.createElement('option');
      opt.value = prov;
      opt.textContent = prov;
      if (prov === selectedProv) opt.selected = true;
      provinsiSelect.appendChild(opt);
    });

    // Jika sudah ada provinsi, isi kota
    function fillKota(prov) {
      kotaSelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
      if (data[prov]) {
        data[prov].forEach(kota => {
          const opt = document.createElement('option');
          opt.value = kota;
          opt.textContent = kota;
          if (kota === selectedKota) opt.selected = true;
          kotaSelect.appendChild(opt);
        });
      }
    }
    if (selectedProv) fillKota(selectedProv);

    provinsiSelect.addEventListener('change', function() {
      fillKota(this.value);
    });
  });
    </script>
</body>

</html>

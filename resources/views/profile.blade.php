<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Pembeli | CHASTE</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-white font-sans text-gray-800">
    {{-- Header --}}
    @include('layouts.customer-nav')
    <div class="px-[100px] py-12 space-y-10 max-w-5xl mx-auto">
        <a href="{{ url()->previous() }}" 
            class="inline-flex items-center gap-2 bg-teal-100 hover:bg-teal-200 text-teal-700 font-medium py-2 px-4 rounded-md text-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        {{-- Heading --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Selamat datang, {{ $customer->name }}</h1>
            <a href="{{ route('logout') }}"
                class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-md transition">
                Keluar
            </a>
        </div>

        {{-- Form Edit Profil --}}
        <div class="border rounded-xl p-6 md:p-10 space-y-6 bg-white shadow-sm">
            <h2 class="text-xl font-bold mb-4">Profil Saya</h2>
            <form class="space-y-4">
                @csrf
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
                    <a href="{{ route('transaksi') }}" class="text-sm text-gray-600 hover:underline">
                        Lihat Riwayat pesanan &gt;
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 text-center gap-6 text-sm">
                    <div>
                        <div class="text-4xl text-[#BBD3D3]">📦</div>
                        <p class="mt-2 font-semibold">Dikemas</p>
                        <p>({{ $dikemasCount }})</p>
                    </div>
                    <div>
                        <div class="text-4xl text-[#BBD3D3]">🚚</div>
                        <p class="mt-2 font-semibold">Dikirim</p>
                        <p>({{ $dikirimCount }})</p>
                    </div>
                    <div>
                        <div class="text-4xl text-[#BBD3D3]">⭐</div>
                        <p class="mt-2 font-semibold">Beri Penilaian</p>
                        <p>({{ $reviewCount }})</p>
                    </div>
                </div>
            </div>

            {{-- Keuangan --}}
            <div>
                <h2 class="text-xl font-bold mb-4">Keuangan Saya</h2>
                <div class="border rounded-md p-4 flex justify-between text-sm bg-[#F9F9F9]">
                    <div class="text-center flex-1">
                        <p class="text-gray-600">Hutang</p>
                        <p class="text-red-500 font-semibold text-lg">Rp 2.000.000</p>
                    </div>
                    <div class="border-l"></div>
                    <div class="text-center flex-1">
                        <p class="text-gray-600">Jumlah Tagihan Nota</p>
                        <p class="text-red-500 font-semibold text-lg">2</p>
                    </div>
                </div>
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
    </script>
</body>

</html>

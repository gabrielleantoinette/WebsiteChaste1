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
                            class="w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-black outline-none text-black">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" name="name" required
                            class="w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-black outline-none text-black">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                        <input type="tel" name="phone" placeholder="+62" required
                            class="w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-black outline-none text-black">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="w-full px-4 py-3 pr-12 rounded-md border border-gray-300 focus:ring-2 focus:ring-black outline-none text-black">
                            <button type="button" onclick="togglePassword('password')" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <svg id="password-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <svg id="password-eye-slash" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 1-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ulangi Kata Sandi</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full px-4 py-3 pr-12 rounded-md border border-gray-300 focus:ring-2 focus:ring-black outline-none text-black">
                            <button type="button" onclick="togglePassword('password_confirmation')" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <svg id="password_confirmation-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <svg id="password_confirmation-eye-slash" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 1-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <div id="password-match-message" class="text-sm mt-1 hidden"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                        <input type="text" name="address"
                            class="w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-black outline-none text-black">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Provinsi</label>
                            <select name="province" id="province" required
                                class="w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-black outline-none text-black">
                                <option value="">Pilih Provinsi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kota</label>
                            <select name="city" id="city" required
                                class="w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-black outline-none text-black">
                                <option value="">Pilih Kota/Kabupaten</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kode Pos</label>
                        <input type="text" name="postal_code"
                            class="w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-black outline-none text-black">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select name="gender" required
                            class="w-full mt-1 px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-black outline-none text-black">
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
        // Toggle password visibility
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(fieldId + '-eye');
            const eyeSlashIcon = document.getElementById(fieldId + '-eye-slash');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }

        // Password confirmation validation
        function validatePasswordMatch() {
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            const messageDiv = document.getElementById('password-match-message');
            
            if (passwordConfirmation === '') {
                messageDiv.classList.add('hidden');
                return;
            }
            
            if (password === passwordConfirmation) {
                messageDiv.textContent = '✓ Kata sandi cocok';
                messageDiv.className = 'text-sm mt-1 text-green-600';
                messageDiv.classList.remove('hidden');
            } else {
                messageDiv.textContent = '✗ Kata sandi tidak cocok';
                messageDiv.className = 'text-sm mt-1 text-red-600';
                messageDiv.classList.remove('hidden');
            }
        }

        // Add event listeners for password validation
        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('password');
            const passwordConfirmationField = document.getElementById('password_confirmation');
            
            passwordField.addEventListener('input', validatePasswordMatch);
            passwordConfirmationField.addEventListener('input', validatePasswordMatch);
        });

        // Indonesia wilayah data
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

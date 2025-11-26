@extends('layouts.admin')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-0">
        <h1 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-gray-800 border-b pb-2">Profil Saya</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('employee.profile.update') }}" 
              enctype="multipart/form-data"
              class="bg-white p-4 sm:p-5 lg:p-6 rounded-lg shadow-md border space-y-4">
            @csrf

            {{-- Foto Profil --}}
            <div class="flex flex-col items-center mb-6">
                @if ($employee->profile_picture)
                    <img src="{{ asset('storage/photos/' . $employee->profile_picture) }}" 
                         alt="Foto Profil" 
                         class="w-32 h-32 rounded-full object-cover border-4 border-teal-200 shadow-md">
                @else
                    <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center text-4xl font-bold text-gray-500 border-4 border-teal-200">
                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                    </div>
                @endif
                <label for="profile_picture" class="mt-3 inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded cursor-pointer text-sm transition">
                    Ubah Foto Profil
                </label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="hidden">
            </div>

            {{-- Nama --}}
            <div>
                <label for="name" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="name" id="name" value="{{ $employee->name }}"
                       class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300"
                       required>
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ $employee->email }}"
                       class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300"
                       required>
            </div>

            {{-- Nomor Telepon --}}
            <div>
                <label for="phone" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                <input type="text" name="phone" id="phone" value="{{ $employee->phone }}"
                       class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            {{-- Nomor KTP --}}
            <div>
                <label for="ktp" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nomor KTP</label>
                <input type="text" name="ktp" id="ktp" value="{{ $employee->ktp }}"
                       class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="password" id="password" 
                       placeholder="Kosongkan jika tidak ingin mengubah password"
                       class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password</p>
            </div>

            {{-- Role (Read-only) --}}
            <div>
                <label for="role" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Peran</label>
                <input type="text" id="role" value="{{ ucfirst($employee->role) }}" 
                       class="w-full border border-gray-300 bg-gray-50 rounded-md px-3 sm:px-4 py-2 text-sm text-gray-600 cursor-not-allowed"
                       readonly>
            </div>

            {{-- Status (Read-only) --}}
            <div>
                <label for="status" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status</label>
                <input type="text" id="status" value="{{ $employee->active ? 'Aktif' : 'Tidak Aktif' }}" 
                       class="w-full border border-gray-300 bg-gray-50 rounded-md px-3 sm:px-4 py-2 text-sm text-gray-600 cursor-not-allowed"
                       readonly>
            </div>

            {{-- Tombol Submit --}}
            <div class="pt-4">
                <button type="submit"
                        class="w-full bg-teal-500 hover:bg-teal-600 text-white text-sm sm:text-base font-semibold py-2 px-4 sm:px-6 rounded-md transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        // Preview image saat dipilih
        document.getElementById('profile_picture').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.querySelector('img[alt="Foto Profil"]');
                    if (img) {
                        img.src = e.target.result;
                    } else {
                        const div = document.querySelector('.w-32.h-32.rounded-full.bg-gray-200');
                        if (div) {
                            div.innerHTML = `<img src="${e.target.result}" alt="Foto Profil" class="w-32 h-32 rounded-full object-cover border-4 border-teal-200 shadow-md">`;
                        }
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection


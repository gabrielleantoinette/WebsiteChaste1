@extends('layouts.admin')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-0">
        <h1 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-gray-800 border-b pb-2">Detail Pegawai</h1>

        <form method="POST" action="{{ route('employees.updateEmployeeAction', $employee->id) }}"
              enctype="multipart/form-data"
              class="bg-white p-4 sm:p-5 lg:p-6 rounded-lg shadow-md border">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5 lg:gap-6">
                <!-- Form Kolom Kiri (2/3) -->
                <div class="lg:col-span-2 space-y-3 sm:space-y-4">
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <input type="text" name="name" value="{{ $employee->name }}"
                               class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ $employee->email }}"
                               class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password"
                               class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password</p>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="phone" value="{{ $employee->phone }}"
                               class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nomor KTP</label>
                        <input type="text" name="ktp" value="{{ $employee->ktp }}"
                               class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Peran</label>
                        <select name="role" class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                            <option value="admin" {{ $employee->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ $employee->role == 'user' ? 'selected' : '' }}>User</option>
                            <option value="owner" {{ $employee->role == 'owner' ? 'selected' : '' }}>Owner</option>
                            <option value="driver" {{ $employee->role == 'driver' ? 'selected' : '' }}>Driver</option>
                            <option value="gudang" {{ $employee->role == 'gudang' ? 'selected' : '' }}>Gudang</option>
                            <option value="keuangan" {{ $employee->role == 'keuangan' ? 'selected' : '' }}>Keuangan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="active" class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                            <option value="true" {{ $employee->active ? 'selected' : '' }}>Aktif</option>
                            <option value="false" {{ !$employee->active ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <!-- Kolom Kanan: Foto Profil -->
                <div class="space-y-3 text-center lg:text-left">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700">Foto Wajah</label>
                    @if ($employee->profile_picture)
                        <img src="{{ asset('storage/photos/' . $employee->profile_picture) }}"
                             class="w-32 h-32 sm:w-40 sm:h-40 object-cover rounded-full mx-auto lg:mx-0 border shadow">
                    @else
                        <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-full bg-gray-200 flex items-center justify-center mx-auto lg:mx-0">
                            <span class="text-gray-500 text-xs sm:text-sm">Belum ada foto</span>
                        </div>
                    @endif

                    <input type="file" name="profile_picture"
                           class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm">
                </div>
            </div>

            <div class="text-left sm:text-end pt-4 sm:pt-6">
                <button type="submit"
                        class="w-full sm:w-auto bg-teal-500 hover:bg-teal-600 text-white text-sm sm:text-base font-semibold py-2 px-4 sm:px-6 rounded-md transition">
                    Update
                </button>
            </div>
        </form>
    </div>
@endsection

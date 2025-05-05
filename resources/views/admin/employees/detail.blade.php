@extends('layouts.admin')

@section('content')
    <div class="max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-2">Detail Pegawai</h1>

        <form method="POST" action="{{ route('employees.updateEmployeeAction', $employee->id) }}"
              enctype="multipart/form-data"
              class="bg-white p-6 rounded-lg shadow-md border">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Form Kolom Kiri (2/3) -->
                <div class="md:col-span-2 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <input type="text" name="name" value="{{ $employee->name }}"
                               class="w-full border border-gray-300 rounded-md p-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ $employee->email }}"
                               class="w-full border border-gray-300 rounded-md p-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="text" name="password" value="{{ $employee->password }}"
                               class="w-full border border-gray-300 rounded-md p-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="phone" value="{{ $employee->phone }}"
                               class="w-full border border-gray-300 rounded-md p-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor KTP</label>
                        <input type="text" name="ktp" value="{{ $employee->ktp }}"
                               class="w-full border border-gray-300 rounded-md p-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
                        <select name="role" class="w-full border border-gray-300 rounded-md p-2">
                            <option value="admin" {{ $employee->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ $employee->role == 'user' ? 'selected' : '' }}>User</option>
                            <option value="owner" {{ $employee->role == 'owner' ? 'selected' : '' }}>Owner</option>
                            <option value="driver" {{ $employee->role == 'driver' ? 'selected' : '' }}>Driver</option>
                            <option value="gudang" {{ $employee->role == 'gudang' ? 'selected' : '' }}>Gudang</option>
                            <option value="keuangan" {{ $employee->role == 'keuangan' ? 'selected' : '' }}>Keuangan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="active" class="w-full border border-gray-300 rounded-md p-2">
                            <option value="true" {{ $employee->active ? 'selected' : '' }}>Aktif</option>
                            <option value="false" {{ !$employee->active ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <!-- Kolom Kanan: Foto Profil -->
                <div class="space-y-3 text-center">
                    <label class="block text-sm font-medium text-gray-700">Foto Wajah</label>
                    @if ($employee->profile_picture)
                        <img src="{{ asset('storage/photos/' . $employee->profile_picture) }}"
                             class="w-40 h-40 object-cover rounded-full mx-auto border shadow">
                    @else
                        <div class="w-40 h-40 rounded-full bg-gray-200 flex items-center justify-center mx-auto">
                            <span class="text-gray-500 text-sm">Belum ada foto</span>
                        </div>
                    @endif

                    <input type="file" name="profile_picture"
                           class="w-full border border-gray-300 rounded-md p-2">
                </div>
            </div>

            <div class="text-end pt-6">
                <button type="submit"
                        class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded-md transition">
                    Update
                </button>
            </div>
        </form>
    </div>
@endsection

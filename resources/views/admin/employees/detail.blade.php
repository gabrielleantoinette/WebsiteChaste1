@extends('layouts.admin')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-2">Detail Pegawai</h1>

        <form method="POST" class="space-y-4 bg-white p-6 rounded-lg shadow-md border">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="name" value="{{ $employee->name }}"
                    class="input input-primary w-full border border-gray-300 rounded-md p-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ $employee->email }}"
                    class="input input-primary w-full border border-gray-300 rounded-md p-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="text" name="password" value="{{ $employee->password }}"
                    class="input input-primary w-full border border-gray-300 rounded-md p-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
                <select name="role" class="select select-primary w-full border border-gray-300 rounded-md p-2">
                    <option value="admin" {{ $employee->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ $employee->role == 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="active" class="select select-primary w-full border border-gray-300 rounded-md p-2">
                    <option value="true" {{ $employee->active ? 'selected' : '' }}>Aktif</option>
                    <option value="false" {{ !$employee->active ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>

            <div class="text-end pt-4">
                <button type="submit"
                    class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-4 rounded-md transition">
                    Update
                </button>
            </div>
        </form>
    </div>
@endsection

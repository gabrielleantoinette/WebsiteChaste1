@extends('layouts.admin')

@section('content')
    <div class="max-w-xl mx-auto px-4 sm:px-0">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 border-b pb-2">Detail Customer</h1>

        <form method="POST" class="bg-white border border-gray-200 rounded-lg p-4 sm:p-5 lg:p-6 shadow-md space-y-3 sm:space-y-4">
            @csrf

            {{-- Nama --}}
            <div>
                <label for="name" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" id="name" name="name" placeholder="Masukkan nama"
                    value="{{ $customer->name }}"
                    class="w-full border border-teal-600 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" placeholder="Masukkan email"
                    value="{{ $customer->email }}"
                    class="w-full border border-teal-600 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password"
                    value="{{ $customer->password }}"
                    class="w-full border border-teal-600 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            {{-- Telepon --}}
            <div>
                <label for="phone" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                <input type="text" id="phone" name="phone" placeholder="Masukkan nomor telepon"
                    value="{{ $customer->phone }}"
                    class="w-full border border-teal-600 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            {{-- Status --}}
            <div>
                <label for="active" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="active" name="active"
                    class="w-full border border-teal-600 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="true" {{ $customer->active ? 'selected' : '' }}>Aktif</option>
                    <option value="false" {{ !$customer->active ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>

            {{-- Tombol Update --}}
            <div class="pt-2">
                <button type="submit"
                    class="w-full bg-teal-600 text-white text-sm sm:text-base font-semibold py-2 sm:py-2.5 rounded-md hover:bg-teal-700 transition">
                    Update
                </button>
            </div>
        </form>
    </div>
@endsection

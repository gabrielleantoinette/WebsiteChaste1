@extends('layouts.admin')

@section('content')
<div class="flex justify-center px-4 sm:px-0">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 sm:p-5 lg:p-6 max-w-xl w-full mt-4 sm:mt-5 lg:mt-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 text-center">Tambah Customer Baru</h1>

        <form method="POST" class="space-y-3 sm:space-y-4">
            @csrf

            {{-- Nama --}}
            <div>
                <label for="name" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" id="name" name="name" placeholder="Masukkan nama"
                    class="w-full border border-teal-600 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" placeholder="Masukkan email"
                    class="w-full border border-teal-600 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password"
                    class="w-full border border-teal-600 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            {{-- No HP --}}
            <div>
                <label for="phone" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                <input type="text" id="phone" name="phone" placeholder="Masukkan nomor telepon"
                    class="w-full border border-teal-600 rounded-md px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            {{-- Submit --}}
            <div class="pt-2">
                <button type="submit"
                    class="w-full bg-teal-600 text-white text-sm sm:text-base font-semibold py-2 sm:py-2.5 rounded-md hover:bg-teal-700 transition">
                    Simpan Customer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('content')
    <div class="max-w-xl mx-auto px-4 sm:px-0">
        {{-- Breadcrumb --}}
        <div class="mb-4 sm:mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 sm:space-x-2 md:space-x-3 flex-wrap">
                    <li class="inline-flex items-center">
                        <a href="{{ url('/admin/customers') }}" class="inline-flex items-center text-xs sm:text-sm font-medium text-gray-700 hover:text-teal-600">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            <span class="hidden sm:inline">Kelola Pembeli</span>
                            <span class="sm:hidden">Pembeli</span>
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 sm:w-6 sm:h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-xs sm:text-sm font-medium text-gray-500 md:ml-2">Detail Customer</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 border-b pb-2">Detail Customer</h1>

        <form method="POST" action="{{ url('/admin/customers/detail/' . $customer->id) }}" class="bg-white border border-gray-200 rounded-lg p-4 sm:p-5 lg:p-6 shadow-md space-y-3 sm:space-y-4">
            @csrf

            {{-- ID Customer --}}
            <div>
                <label for="id" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">ID Customer</label>
                <input type="text" id="id" name="id" value="{{ $customer->id }}" readonly
                    class="w-full border border-gray-300 bg-gray-50 rounded-md px-3 sm:px-4 py-2 text-sm text-gray-600 cursor-not-allowed">
            </div>

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

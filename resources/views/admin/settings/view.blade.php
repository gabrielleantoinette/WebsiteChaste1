@extends('layouts.admin')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-8 max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Pengaturan Website</h1>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800 font-medium border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ $setting->phone }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700">Nama Perusahaan</label>
                    <input type="text" name="company_name" value="{{ $setting->company_name }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700">Email Perusahaan</label>
                    <input type="email" name="company_email" value="{{ $setting->company_email }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-1 text-sm font-semibold text-gray-700">Kebijakan Perusahaan</label>
                    <textarea name="company_policy" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500">{{ $setting->company_policy }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-1 text-sm font-semibold text-gray-700">Alamat Perusahaan</label>
                    <textarea name="company_address" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500">{{ $setting->company_address }}</textarea>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700">Tema Website</label>
                    <select name="theme"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500">
                        <option value="light" {{ $setting->theme == 'light' ? 'selected' : '' }}>Light</option>
                        <option value="dark" {{ $setting->theme == 'dark' ? 'selected' : '' }}>Dark</option>
                    </select>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
@endsection

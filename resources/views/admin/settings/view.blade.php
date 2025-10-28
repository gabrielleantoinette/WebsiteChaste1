@extends('layouts.admin')

@section('content')
    {{-- Header dengan Gradient Background --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl mb-8">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">⚙️ Pengaturan Website</h1>
                    <p class="text-teal-100">Kelola konfigurasi dan pengaturan sistem</p>
                </div>
                <div class="flex gap-3">
                    <div class="text-right text-white">
                        <p class="text-sm opacity-90">Status Sistem</p>
                        <p class="text-lg font-semibold">Aktif</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-emerald-100 text-emerald-800 px-6 py-4 rounded-xl mb-6 border border-emerald-200">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- Settings Form dengan Card Layout --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Konfigurasi Sistem
            </h3>
        </div>
        
        <form method="POST" action="{{ route('admin.settings.update') }}" class="p-6 space-y-6">
            @csrf

            {{-- Informasi Perusahaan --}}
            <div class="space-y-6">
                <h4 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Informasi Perusahaan
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Nama Perusahaan</label>
                        <input type="text" name="company_name" value="{{ $setting->company_name }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Email Perusahaan</label>
                        <input type="email" name="company_email" value="{{ $setting->company_email }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Nomor Telepon</label>
                        <input type="text" name="phone" value="{{ $setting->phone }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Tema Website</label>
                        <select name="theme" class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200">
                            <option value="light" {{ $setting->theme == 'light' ? 'selected' : '' }}>Light</option>
                            <option value="dark" {{ $setting->theme == 'dark' ? 'selected' : '' }}>Dark</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Alamat Perusahaan</label>
                    <textarea name="company_address" rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200">{{ $setting->company_address }}</textarea>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Kebijakan Perusahaan</label>
                    <textarea name="company_policy" rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200">{{ $setting->company_policy }}</textarea>
                </div>
            </div>

            {{-- Pengaturan Sistem --}}
            <div class="space-y-6 pt-6 border-t border-gray-200">
                <h4 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Pengaturan Sistem
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Status Maintenance</label>
                        <select name="maintenance_mode" class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200">
                            <option value="0" {{ $setting->maintenance_mode == '0' ? 'selected' : '' }}>Normal</option>
                            <option value="1" {{ $setting->maintenance_mode == '1' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Timezone</label>
                        <select name="timezone" class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200">
                            <option value="Asia/Jakarta" {{ $setting->timezone == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta</option>
                            <option value="Asia/Makassar" {{ $setting->timezone == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar</option>
                            <option value="Asia/Jayapura" {{ $setting->timezone == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                <button type="button" onclick="resetForm()" 
                    class="px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-all duration-200">
                    Reset
                </button>
                <button type="submit" 
                    class="px-6 py-3 bg-gradient-to-r from-teal-500 to-teal-600 text-white font-medium rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    <script>
        function resetForm() {
            if (confirm('Yakin ingin mereset semua pengaturan ke nilai default?')) {
                location.reload();
            }
        }
    </script>
@endsection
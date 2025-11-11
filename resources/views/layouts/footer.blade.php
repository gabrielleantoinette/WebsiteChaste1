@php
    use App\Models\Setting;
    $setting = Setting::first();
@endphp

<footer class="bg-[#D9F2F2] py-10 px-4 sm:px-6 lg:px-16 text-sm text-gray-700 mt-20">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Brand -->
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">
                {{ $setting->company_name ?? 'CHASTE' }}
            </h3>
            <p class="mb-4 max-w-xs">
                Kami membantu anda menyediakan terpal terbaik.
            </p>
            <div class="flex space-x-4 text-lg">
                <a href="#">📷</a>
                <a href="#">🐦</a>
                <a href="#">📘</a>
            </div>
        </div>

        <!-- Informasi -->
        <div class="space-y-2">
            <h4 class="font-semibold text-gray-800 mb-2">Informasi</h4>
            <a href="#" class="block hover:text-black">Tentang</a>
            <a href="#" class="block hover:text-black">Produk</a>
        </div>

        <!-- Kontak -->
        <div class="space-y-2">
            <h4 class="font-semibold text-gray-800 mb-2">Kontak Kami</h4>
            <p>Telp: {{ $setting->phone ?? '-' }}</p>
            <p>Email: {{ $setting->company_email ?? '-' }}</p>
            <p>Alamat: {{ $setting->company_address ?? '-' }}</p>
        </div>
    </div>

    <div class="text-center text-xs text-gray-500 mt-8">
        © 2025 {{ $setting->company_name ?? 'CHASTE' }}. Hak Cipta Dilindungi.
    </div>
</footer>

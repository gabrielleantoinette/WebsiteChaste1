@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Edit Bahan Custom: {{ $material->name }}</h1>

    {{-- Form Update Bahan --}}
    <form method="POST" action="{{ url('/admin/custom-materials/' . $material->id . '/update') }}"
        class="bg-white border border-gray-200 rounded-lg p-6 space-y-6 shadow-sm mb-10">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bahan</label>
            <input type="text" name="name" value="{{ $material->name }}"
                class="w-full border border-teal-500 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm text-gray-800">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
            <input type="number" name="price" value="{{ $material->price }}"
                class="w-full border border-teal-500 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm text-gray-800">
        </div>

        <div class="pt-2">
            <button type="submit"
                class="bg-teal-600 hover:bg-teal-700 text-white font-semibold px-5 py-2 rounded-md transition">
                Update Bahan
            </button>
        </div>
    </form>

    {{-- List Warna Varian --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Daftar Varian Warna</h2>

            <form method="POST" action="{{ route('custom-materials.variants.store', $material->id) }}"
                class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <input type="text" name="color" placeholder="Warna"
                    class="w-full border border-teal-500 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm text-gray-800" required>

                <input type="number" name="stock" placeholder="Stok"
                    class="w-full border border-teal-500 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm text-gray-800" required>

                <div class="md:col-span-2">
                    <button type="submit"
                        class="bg-teal-600 hover:bg-teal-700 text-white font-semibold px-5 py-2 rounded-md transition w-full md:w-auto">
                        + Tambah Warna
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto mt-4">
            <table class="w-full text-sm text-gray-700 border border-gray-300 rounded-md">
                <thead class="bg-[#D9F2F2] text-gray-800 font-medium">
                    <tr>
                        <th class="px-4 py-2 text-left">Warna</th>
                        <th class="px-4 py-2 text-left">Stok</th>
                        <th class="px-4 py-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($material->variants as $variant)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $variant->color }}</td>
                            <td class="px-4 py-2">{{ $variant->stock }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ url('/admin/custom-materials/variants/edit/' . $variant->id) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-md text-xs font-semibold transition">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-gray-500">Belum ada varian warna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

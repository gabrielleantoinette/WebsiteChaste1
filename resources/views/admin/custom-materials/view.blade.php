@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Kelola Bahan Custom</h1>

    <div class="flex justify-end mb-6">
        <a href="{{ url('/admin/custom-materials/create') }}" 
           class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-md text-sm font-semibold transition">
           + Tambah Bahan
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-gray-700 border border-gray-300 rounded-md shadow-sm">
            <thead class="bg-[#D9F2F2] text-gray-800 font-semibold">
                <tr>
                    <th class="px-4 py-2 text-left">Nama Bahan</th>
                    <th class="px-4 py-2 text-left">Harga per m²</th>
                    <th class="px-4 py-2 text-left">Stok per Warna</th>
                    <th class="px-4 py-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customMaterials as $material)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2 font-semibold">{{ $material->name }}</td>
                        <td class="px-4 py-2">Rp {{ number_format($material->price) }}</td>
                        <td class="px-4 py-2 space-y-1">
                            @if ($material->variants->count() > 0)
                                @foreach ($material->variants as $variant)
                                    <div>- {{ $variant->color }}: {{ $variant->stock }} pcs</div>
                                @endforeach
                            @else
                                <div class="text-gray-400 italic">Belum ada varian warna</div>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('custom-materials.edit', $material->id) }}" class="inline-flex items-center gap-2 px-3 py-1.5 border border-teal-600 text-teal-600 text-xs font-medium rounded-md hover:bg-teal-50 transition">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-6 text-gray-500">Belum ada bahan custom tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@extends('layouts.admin')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Tambah Bahan Custom</h1>

        <form method="POST" action="{{ route('custom-materials.store') }}" class="..."
            class="space-y-4 bg-white p-6 rounded-lg shadow-md border border-gray-200">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bahan</label>
                <input type="text" name="name" required
                    class="w-full border border-teal-600 rounded-md p-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga per m² (Rp)</label>
                <input type="number" name="price" required min="0"
                    class="w-full border border-teal-600 rounded-md p-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 rounded-md transition">
                    Simpan Bahan
                </button>
            </div>
        </form>
    </div>
@endsection
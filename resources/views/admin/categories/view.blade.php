@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Kelola Kategori</h1>
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.categories.store') }}" method="POST" class="mb-6 flex gap-4">
        @csrf
        <input type="text" name="name" placeholder="Nama Kategori"
            class="border border-gray-300 rounded px-4 py-2 w-full max-w-sm focus:outline-teal-600" required>
        <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded hover:bg-teal-700 transition">
            Tambah
        </button>
    </form>

    <div class="bg-white shadow rounded-md p-4">
        <table class="table-auto w-full data-table text-sm">
            <thead>
                <tr class="bg-[#D9F2F2] text-gray-800">
                    <th class="py-3 px-4 text-left">#</th>
                    <th class="py-3 px-4 text-left">Nama Kategori</th>
                    <th class="py-3 px-4 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $index => $category)
                    <tr class="border-b">
                        <td class="py-3 px-4">{{ $loop->iteration }}</td>
                        <td class="py-3 px-4">{{ $category->name }}</td>
                        <td class="py-3 px-4 flex gap-2">
                            @if (Session::get('user')->role !== 'admin')
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin hapus kategori ini?')">
                                    @csrf
                                    <button
                                        class="text-sm px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">Hapus</button>
                                </form>
                            @endif
                            <button onclick="editCategory({{ $category->id }})"
                                class="text-sm px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500">
                                Edit
                            </button>
                            <button onclick="window.location.href='{{ route('admin.categories.detail', $category->id) }}'"
                                class="text-sm px-3 py-1 bg-blue-400 text-white rounded hover:bg-blue-500">
                                Detail
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Edit -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded p-6 w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Edit Kategori</h2>
            <form id="editForm" method="POST">
                @csrf
                @method('POST')
                <input type="text" name="name" id="editName" class="border border-gray-300 w-full p-2 rounded mb-4"
                    required>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal()"
                        class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">
                        Batal
                    </button>
                    <button type="submit" class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editCategory(id) {
            fetch(`/admin/categories/edit/${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('editName').value = data.name;
                    document.getElementById('editForm').action = `/admin/categories/edit/${id}`;
                    document.getElementById('editModal').classList.remove('hidden');
                });
        }

        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
@endsection

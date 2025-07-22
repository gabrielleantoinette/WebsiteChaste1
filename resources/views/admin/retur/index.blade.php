@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-gray-800">Daftar Permintaan Retur</h2>
</div>
<div class="bg-white shadow rounded-md p-4">
    <table id="retur-table" class="table-auto w-full text-sm">
        <thead>
            <tr class="bg-[#D9F2F2] text-gray-800">
                <th class="py-3 px-4 text-left">#</th>
                <th class="py-3 px-4 text-left">Nomor Invoice</th>
                <th class="py-3 px-4 text-left">Customer</th>
                <th class="py-3 px-4 text-left">Alasan Retur</th>
                <th class="py-3 px-4 text-left">Status</th>
                <th class="py-3 px-4 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returs as $retur)
            <tr class="border-b">
                <td class="py-3 px-4">{{ $loop->iteration }}</td>
                <td class="py-3 px-4">{{ $retur->invoice->code ?? '-' }}</td>
                <td class="py-3 px-4">{{ $retur->customer->name ?? '-' }}</td>
                <td class="py-3 px-4">{{ $retur->description }}</td>
                <td class="py-3 px-4">{{ $retur->status }}</td>
                <td class="py-3 px-4">
                    <a href="{{ route('admin.retur.detail', $retur->id) }}" class="text-sm px-3 py-1 bg-blue-400 text-white rounded hover:bg-blue-500">Lihat Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada permintaan retur.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@push('scripts')
<script>
    $(function() {
        $('#retur-table').DataTable({
            order: [],
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Tidak ada data",
                zeroRecords: "Tidak ada permintaan retur.",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                },
            }
        });
    });
</script>
@endpush
@endsection 
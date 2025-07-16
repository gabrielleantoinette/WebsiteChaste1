@extends('layouts.admin')

@section('content')
    <div class="flex justify-between mb-5">
        <h1 class="text-xl font-bold mb-5">Transaksi</h1>
    </div>
    <div class="overflow-x-auto">
        <table id="datatable-gudang" class="min-w-full table-auto text-sm border border-gray-200">
            <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">ID</th>
                    <th class="px-4 py-3 text-left font-semibold">Kode</th>
                    <th class="px-4 py-3 text-left font-semibold">Customer</th>
                    <th class="px-4 py-3 text-left font-semibold">Tanggal Diterima</th>
                    <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                    <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-teal-50 transition">
                        <td class="px-4 py-3">{{ $invoice->id }}</td>
                        <td class="px-4 py-3 font-mono text-teal-700 font-semibold">{{ $invoice->code }}</td>
                        <td class="px-4 py-3">{{ $invoice->customer->name }}</td>
                        <td class="px-4 py-3">{{ $invoice->receive_date }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ url('/admin/gudang-transaksi/detail/' . $invoice->id) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white text-sm font-semibold rounded-md shadow-sm hover:bg-teal-700 transition">
                                Detail
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <style>
        /* Custom DataTables style */
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            padding: 6px 12px;
            font-size: 0.95rem;
        }
        .dataTables_wrapper .dataTables_length select {
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            padding: 4px 8px;
            font-size: 0.95rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 6px;
            border: 1px solid #14b8a6;
            background: #fff;
            color: #14b8a6 !important;
            margin: 0 2px;
            padding: 4px 12px;
            font-weight: 500;
            transition: background 0.2s, color 0.2s;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #14b8a6 !important;
            color: #fff !important;
        }
        .dataTables_wrapper .dataTables_info {
            font-size: 0.95rem;
            color: #64748b;
            margin-top: 8px;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#datatable-gudang').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoEmpty: "Tidak ada data",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya"
                    }
                },
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                responsive: true
            });
        });
    </script>
@endsection

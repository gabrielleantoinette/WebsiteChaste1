@extends('layouts.admin')

@section('content')
    <div class="flex justify-between mb-5">
        <h1 class="text-xl font-bold mb-5">Daftar Pembeli</h1>

        <div class="flex gap-2">
            <a href="{{ url('/admin/customers/create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 border border-teal-600 text-teal-600 font-medium rounded-md hover:bg-teal-50 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Create
            </a>

            <a href="{{ route('laporan.customer.pdf') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white font-medium rounded-md hover:bg-teal-700 transition shadow-sm">
                📄 Export Laporan Pembeli
            </a>
        </div>
    </div>


    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <td>ID</td>
                <td>Name</td>
                <td>Email</td>
                <td>Password</td>
                <td>Phone</td>
                <td>Active</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->password }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->active ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ url('/admin/customers/detail/' . $customer->id) }}"
                        class="inline-flex items-center gap-2 px-3 py-1.5 border border-teal-600 text-teal-600 text-sm font-medium rounded-md hover:bg-teal-50 transition shadow-sm">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>Detail</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@extends('layouts.admin')

@section('content')
    <div class="flex justify-between mb-5">
        <h1 class="text-xl font-bold mb-5">Employee List</h1>
        <a href="{{ url('/admin/employees/create') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-teal-600 text-teal-600 font-medium rounded-md hover:bg-teal-50 transition shadow-sm">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
         stroke-width="2" stroke="currentColor" class="w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
    </svg>Create</a>
    </div>

    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <td>ID</td>
                <td>Name</td>
                <td>Email</td>
                <td>Password</td>
                <td>Role</td>
                <td>Phone</td>
                <td>KTP</td>
                <td>Plat Kendaraan</td>
                <td>Jenis Kendaraan</td>
                <td>Active</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->email }}</td>
                    <td>{{ $product->password }}</td>
                    <td>{{ $product->role }}</td>
                    <td>{{ $product->phone }}</td>
                    <td>{{ $product->ktp }}</td>
                    <td>{{ $product->car_plate }}</td>
                    <td>{{ $product->car_type }}</td>
                    <td>{{ $product->active ? 'Active' : 'Inactive' }}</td>
                    <td><a href="{{ url('/admin/employees/detail/' . $product->id) }}"
                    class="inline-flex items-center gap-2 px-3 py-1.5 border border-teal-600 text-teal-600 text-sm font-medium rounded-md hover:bg-teal-50 transition shadow-sm">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>Detail</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

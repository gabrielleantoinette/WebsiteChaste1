@extends('layouts.admin')

@section('content')
    <div class="flex justify-between mb-5">
        <h1 class="text-xl font-bold mb-5">Employee List</h1>
        <a href="{{ url('/admin/employees/create') }}" class="btn btn-primary">Create</a>
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
                            class="btn btn-sm btn-primary">Detail</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

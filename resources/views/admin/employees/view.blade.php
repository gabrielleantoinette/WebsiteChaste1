@extends('layouts.admin')

@section('content')
    <div class="flex justify-between mb-5">
        <h1 class="text-xl font-bold mb-5">Employee List</h1>
        <a href="{{ url('/admin/employees/create') }}" class="btn btn-primary">Create</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <td>ID</td>
                <td>Name</td>
                <td>Email</td>
                <td>Password</td>
                <td>Role</td>
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
                    <td>{{ $product->active ? 'Active' : 'Inactive' }}</td>
                    <td><a href="{{ url('/admin/employees/detail/' . $product->id) }}"
                            class="btn btn-xs btn-primary">Detail</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

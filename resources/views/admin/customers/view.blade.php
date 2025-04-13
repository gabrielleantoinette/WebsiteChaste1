@extends('layouts.admin')

@section('content')
    <div class="flex justify-between mb-5">
        <h1 class="text-xl font-bold mb-5">Customer List</h1>
        <a href="{{ url('/admin/customers/create') }}" class="btn btn-primary">Create</a>
    </div>

    <table class="table table-bordered">
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
                            class="btn btn-xs btn-primary">Detail</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

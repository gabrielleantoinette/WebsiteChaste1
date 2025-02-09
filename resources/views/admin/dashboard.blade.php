@extends('layouts.admin')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ url('/admin/products') }}" class="btn btn-outline-primary">Products</a>
            <a href="{{ url('/admin/employees') }}" class="btn btn-outline-secondary">Employees</a>
        </div>
    </div>
@endsection

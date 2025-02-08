@extends('layouts.admin')

@section('content')
    <div class="p-3">
        <a href="{{ url('/admin/products') }}">Products</a>
        <a href="{{ url('/admin/employees') }}">Employees</a>
    </div>
@endsection

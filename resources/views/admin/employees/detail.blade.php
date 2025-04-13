@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-bold mb-5">Employee Detail</h1>
    <form method="POST" class="flex flex-col gap-3">
        @csrf
        <input type="text" name="name" placeholder="Name" class="input input-primary w-full" value="{{ $employee->name }}">
        <input type="email" name="email" placeholder="Email" class="input input-primary w-full"
            value="{{ $employee->email }}">
        <input type="text" name="password" placeholder="Password" class="input input-primary w-full"
            value="{{ $employee->password }}">
        <select name="role" class="select select-primary w-full">
            <option value="admin" {{ $employee->role == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="user" {{ $employee->role == 'user' ? 'selected' : '' }}>User</option>
        </select>
        <select name="active" class="select select-primary w-full">
            <option value="true" {{ $employee->active ? 'selected' : '' }}>Active</option>
            <option value="false" {{ !$employee->active ? 'selected' : '' }}>Inactive</option>
        </select>
        <button class="btn btn-primary">Update</button>
    </form>
@endsection

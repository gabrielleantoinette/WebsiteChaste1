@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-bold mb-5">Create Employee</h1>
    <form method="POST" class="flex flex-col gap-3">
        @csrf
        <input type="text" name="name" placeholder="Name" class="input input-primary w-full">
        <input type="email" name="email" placeholder="Email" class="input input-primary w-full">
        <input type="password" name="password" placeholder="Password" class="input input-primary w-full">
        <select name="role" class="select select-primary w-full">
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select>
        <select name="active" class="select select-primary w-full">
            <option value="true">Active</option>
            <option value="false">Inactive</option>
        </select>
        <button class="btn btn-primary">Submit</button>
    </form>
@endsection

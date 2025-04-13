@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-bold mb-5">Customer Detail</h1>
    <form method="POST" class="flex flex-col gap-3">
        @csrf
        <input type="text" name="name" placeholder="Name" class="input input-primary w-full" value="{{ $customer->name }}">
        <input type="email" name="email" placeholder="Email" class="input input-primary w-full"
            value="{{ $customer->email }}">
        <input type="text" name="password" placeholder="Password" class="input input-primary w-full"
            value="{{ $customer->password }}">
        <input type="text" name="phone" placeholder="Phone" class="input input-primary w-full"
            value="{{ $customer->phone }}">
        <select name="active" class="select select-primary w-full">
            <option value="true" {{ $customer->active ? 'selected' : '' }}>Active</option>
            <option value="false" {{ !$customer->active ? 'selected' : '' }}>Inactive</option>
        </select>
        <button class="btn btn-primary">Update</button>
    </form>
@endsection

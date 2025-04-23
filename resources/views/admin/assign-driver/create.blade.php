@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-bold mb-5">Create Customer</h1>
    <form method="POST" class="flex flex-col gap-3">
        @csrf
        <input type="text" name="name" placeholder="Name" class="input input-primary w-full">
        <input type="email" name="email" placeholder="Email" class="input input-primary w-full">
        <input type="password" name="password" placeholder="Password" class="input input-primary w-full">
        <input type="text" name="phone" placeholder="Phone" class="input input-primary w-full">
        <button class="btn btn-primary">Submit</button>
    </form>
@endsection

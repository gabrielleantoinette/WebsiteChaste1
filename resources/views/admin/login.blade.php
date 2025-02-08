@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-center align-items-center vw100 vh100 bg-body-secondary" style="height: 100vh">
        <div class="w-50 border px-5 py-5 rounded-4 bg-white">
            <h3 class="text-center">Login Admin</h3>
            <form method="POST">
                @csrf
                <labe class="form-label">Username:</labe>
                <input type="text" name="username" class="form-control">
                <label class="form-label">Password:</label>
                <input type="password" name="password" class="form-control">
                <button class="btn btn-primary mt-2">Masuk</button>
            </form>
        </div>
    </div>
@endsection

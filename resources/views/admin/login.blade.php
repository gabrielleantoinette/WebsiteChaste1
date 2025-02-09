@extends('layouts.admin')

@section('content')
    <div class="container d-flex flex-column align-items-center justify-content-center vh-100">
        <div class="col-md-6 col-lg-4 border p-4 rounded-4 bg-white shadow-lg">
            <h3 class="text-center mb-4 fw-bold">Login Admin</h3>
            <form method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Username:</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Masuk</button>
            </form>
        </div>
    </div>
@endsection

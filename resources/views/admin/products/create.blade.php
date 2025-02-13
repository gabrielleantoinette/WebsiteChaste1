@extends('layouts.admin')

@section('content')
    <h3>Create Product</h3>
    <form method="POST" class="form-group mt-3">
        @csrf
        <input type="text" name="name" placeholder="name" class="form-control mb-3">
        <textarea name="description" placeholder="description" class="form-control mb-3"></textarea>
        <input type="number" name="stock" placeholder="stock" class="form-control mb-3">
        <input type="number" name="price" placeholder="price" class="form-control mb-3">
        <input type="text" name="image" placeholder="image url" class="form-control mb-3">
        <select name="live" class="form-control mb-3">
            <option value="true">live</option>
            <option value="false">hidden</option>
        </select>
        <button class="btn btn-primary">Submit</button>
    </form>
@endsection

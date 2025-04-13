@extends('layouts.admin')

@section('content')
    <h3 class="text-2xl font-bold mb-5">Create Product Variant for {{ $product->name }}</h3>
    <form method="POST" class="flex flex-col gap-4">
        @csrf
        <select type="text" name="size" placeholder="size" class="select select-primary w-full">
            <option value="2x3">2x3</option>
            <option value="3x4">3x4</option>
            <option value="4x6">4x6</option>
            <option value="6x8">6x8</option>
        </select>
        <input type="text" name="color" placeholder="color" class="input input-primary w-full">
        <input type="number" name="price" placeholder="price" class="input input-primary w-full">
        <input type="number" name="stock" placeholder="stock" class="input input-primary w-full">
        <button class="btn btn-primary">Submit</button>
    </form>
@endsection

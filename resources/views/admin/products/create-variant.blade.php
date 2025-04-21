@extends('layouts.admin')

@section('content')
    <h3 class="text-2xl font-bold mb-5">Create Product Variant for {{ $product->name }}</h3>
    <form method="POST" class="flex flex-col gap-4">
        @csrf
        <select name="color" placeholder="color" class="input input-select w-full">
            <option value="biru-silver">Biru Silver</option>
            <option value="biru-polos">Biru Polos</option>
            <option value="oranye-silver">Oranye Silver</option>
            <option value="oranye-polos">Oranye Polos</option>
            <option value="coklat-polos">Coklat Polos</option>
            <option value="coklat-silver">Coklat Silver</option>
        </select>
        <input type="number" name="stock" placeholder="stock" class="input input-primary w-full">
        <button class="btn btn-primary">Submit</button>
    </form>
@endsection

@extends('layouts.admin')

@section('content')
    <h3 class="text-2xl font-bold mb-5">Create Product</h3>
    <form method="POST" class="flex flex-col gap-4">
        @csrf
        <input type="text" name="name" placeholder="name" class="input input-primary w-full">
        <textarea name="description" placeholder="description" class="textarea textarea-primary w-full"></textarea>
        <input type="text" name="image" placeholder="image url" class="input input-primary w-full">
        <input type="number" name="price" placeholder="harga" class="input input-primary w-full" required>
        <select name="size" class="select select-primary w-full" required>
            <option selected disabled>Pilih Ukuran</option>
            <option value="2x3">2x3</option>
            <option value="3x4">3x4</option>
            <option value="4x6">4x6</option>
            <option value="6x8">6x8</option>
        </select>
        <select name="live" class="select select-primary w-full">
            <option value="1" selected>Tampil</option>
            <option value="0">Tidak Tampil</option>
        </select>
        <button class="btn btn-primary">Submit</button>
    </form>
@endsection

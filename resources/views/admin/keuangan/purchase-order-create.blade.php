@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto px-6 py-10">
    <h1 class="text-2xl font-semibold mb-6">+ Tambah PO Manual</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('keuangan.hutang.store') }}" method="POST" class="bg-white p-6 rounded shadow space-y-4">
        @csrf

        <div>
            <label for="supplier_id" class="block font-medium mb-1">Supplier</label>
            <label for="supplier_name" class="block font-medium mb-1">Nama Supplier</label>
            <input type="text" name="supplier_name" id="supplier_name" value="{{ old('supplier_name') }}"
                class="w-full border border-gray-300 rounded px-3 py-2">
            </select>
        </div>

        <label for="po_code" class="block font-medium mb-1">Nomor PO</label>
        <input type="text" name="po_code" id="po_code" value="{{ old('po_code') }}"
            class="w-full border border-gray-300 rounded px-3 py-2">


        <div>
            <label for="order_date" class="block font-medium mb-1">Tanggal PO</label>
            <input type="date" name="order_date" id="order_date" value="{{ old('order_date') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label for="due_date" class="block font-medium mb-1">Tanggal Jatuh Tempo</label>
            <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label for="total" class="block font-medium mb-1">Total Pembelian</label>
            <input type="number" name="total" id="total" value="{{ old('total') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2" min="1" step="any">
        </div>

        <div class="text-right pt-4">
            <a href="{{ route('keuangan.hutang.index') }}" class="text-sm text-gray-600 hover:underline mr-4">Batal</a>
            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded font-semibold">
                Simpan PO
            </button>
        </div>
    </form>
</div>
@endsection

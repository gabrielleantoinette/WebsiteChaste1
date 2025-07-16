@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md mt-8">
    <h1 class="text-2xl font-bold mb-6 text-center">Detail Transaksi</h1>
    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    <div class="mb-4">
        <div class="mb-2"><strong>Kode Invoice:</strong> {{ $invoice->code }}</div>
        <div class="mb-2"><strong>Tanggal:</strong> {{ $invoice->created_at }}</div>
        <div class="mb-2"><strong>Total:</strong> Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</div>
        <div class="mb-2"><strong>Status:</strong> <span class="font-semibold">{{ $invoice->status }}</span></div>
    </div>
    <div class="mb-4">
        <div class="mb-2"><strong>Customer:</strong> {{ $invoice->customer->name ?? '-' }}</div>
        <div class="mb-2"><strong>Email:</strong> {{ $invoice->customer->email ?? '-' }}</div>
    </div>
    @if($invoice->transfer_proof)
        <div class="mb-6">
            <strong>Bukti Transfer:</strong><br>
            <img src="{{ asset('storage/' . $invoice->transfer_proof) }}" alt="Bukti Transfer" class="w-64 border rounded mt-2">
        </div>
    @endif
    @if($invoice->status === 'Menunggu Konfirmasi Pembayaran')
        <form method="POST" action="{{ route('keuangan.konfirmasi', $invoice->id) }}">
            @csrf
            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded font-semibold">Konfirmasi & Proses Barang</button>
        </form>
    @endif
</div>
@endsection

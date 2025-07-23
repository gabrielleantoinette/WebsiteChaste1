@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Detail Retur Barang</h2>
    <div class="card mt-3">
        <div class="card-body">
            <div class="mb-2"><strong>Nomor Invoice:</strong> {{ $retur->invoice->code ?? '-' }}</div>
            <div class="mb-2"><strong>Nama Customer:</strong> {{ $retur->customer->name ?? '-' }}</div>
            <div class="mb-2"><strong>Alasan Retur:</strong> {{ $retur->description }}</div>
            <div class="mb-2"><strong>Media (Foto/Video):</strong><br>
                @if($retur->media_path)
                    @if(Str::endsWith($retur->media_path, ['.jpg', '.jpeg', '.png', '.gif']))
                        <img src="{{ asset('storage/' . $retur->media_path) }}" alt="Foto Retur" style="max-width:300px;">
                    @else
                        <a href="{{ asset('storage/' . $retur->media_path) }}" target="_blank">Lihat Media</a>
                    @endif
                @else
                    -
                @endif
            </div>
            <div class="mb-2"><strong>Kurir Pengambil Retur:</strong> {{ $retur->driver->name ?? '-' }}</div>
            <div class="mb-2"><strong>Status Retur:</strong> {{ $retur->status }}</div>
        </div>
    </div>
    <!-- Tombol proses retur ke barang rusak -->
    @if($retur->status == 'diajukan')
    <form action="{{ route('admin.retur.process', $retur->id) }}" method="POST" class="mt-3">
        @csrf
        <button type="submit" class="btn btn-danger">Proses ke Barang Rusak</button>
    </form>
    @endif
    
    <!-- Link ke halaman stok barang rusak -->
    <div class="mt-3">
        <a href="{{ route('admin.retur.damaged-products') }}" class="btn btn-info">
            Lihat Stok Barang Rusak
        </a>
    </div>
</div>
@endsection 
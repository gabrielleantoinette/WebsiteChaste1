@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-bold mb-5">Invoice Detail</h1>
    <div class="mt-5">
        <div class="flex flex-row gap-2">
            <p class="font-bold">Invoice ID:</p>
            <p>{{ $invoice->id }}</p>
        </div>
        <div class="flex flex-row gap-2">
            <p class="font-bold">Invoice Code:</p>
            <p>{{ $invoice->code }}</p>
        </div>
        <div class="flex flex-row gap-2">
            <p class="font-bold">Tanggal Jatuh Tempo:</p>
            <p>{{ $invoice->due_date }}</p>
        </div>
        <div class="flex flex-row gap-2">
            <p class="font-bold">Tanggal Penerimaan Barang:</p>
            <p>{{ $invoice->receive_date }}</p>
        </div>
    </div>
    <div class="mt-5">
        <div class="text-lg font-bold">Customer</div>
        <div class="flex flex-row gap-2">
            <p class="font-bold">Name:</p>
            <p>{{ $invoice->customer->name }}</p>
        </div>
        <div class="flex flex-row gap-2">
            <p class="font-bold">Phone:</p>
            <p>{{ $invoice->customer->phone }}</p>
        </div>
        <div class="flex flex-row gap-2">
            <p class="font-bold">Email:</p>
            <p>{{ $invoice->customer->email }}</p>
        </div>
    </div>
    <div class="mt-5">
        <div class="text-lg font-bold">Staff Gudang Yang Menyiapkan Barang</div>
        @if ($invoice->gudang)
            <div class="flex flex-row gap-2">
                <p class="font-bold">Name:</p>
                <p>{{ $invoice->gudang->name }}</p>
            </div>
            <div class="flex flex-row gap-2">
                <p class="font-bold">Email:</p>
                <p>{{ $invoice->gudang->email }}</p>
            </div>
            <div class="flex flex-row gap-2">
                <p class="font-bold">Aktif:</p>
                <p>{{ $invoice->gudang->active ? 'Aktif' : 'Tidak Aktif' }}</p>
            </div>
        @else
            <div>
                <p>Belum ada staff gudang yang menyiapkan barang</p>
                <form action="{{ url('/admin/gudang-transaksi/assign-gudang/' . $invoice->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary">Siapkan Barang</button>
                </form>
            </div>
        @endif
    </div>
    <div class="mt-5">
        <div class="text-lg font-bold">Produk</div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Warna</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->details as $detail)
                    <tr>
                        <td>{{ $detail->id }}</td>
                        <td>{{ $detail->product->name }}</td>
                        <td>{{ $detail->price }}</td>
                        <td>{{ $detail->variant->color }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>{{ $detail->subtotal }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="text-lg font-bold">Total : Rp {{ number_format($invoice->grand_total) }}</div>
    </div>
    @if ($invoice->status === 'dikirim')
        <div class="mt-10">
            <div class="text-lg font-bold mb-3">Upload Bukti Kirim</div>
            <form action="{{ url('/admin/invoices/upload-bukti/' . $invoice->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label for="photo" class="block font-medium mb-1">Foto Bukti Kirim:</label>
                    <input type="file" name="photo" accept="image/*" class="border border-gray-300 p-2 rounded w-full">
                </div>
                <div>
                    <label for="signature" class="block font-medium mb-1">Tanda Tangan Penerima:</label>
                    <input type="file" name="signature" accept="image/*" class="border border-gray-300 p-2 rounded w-full">
                </div>
                <button type="submit" class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700 transition">Upload Bukti</button>
            </form>

            @if ($invoice->delivery_proof_photo || $invoice->delivery_signature)
                <div class="mt-6">
                    <div class="text-md font-semibold mb-2">Bukti yang Sudah Diunggah:</div>
                    @if ($invoice->delivery_proof_photo)
                        <div class="mb-4">
                            <p class="font-medium">Foto:</p>
                            <img src="{{ asset('storage/' . $invoice->delivery_proof_photo) }}" alt="Foto Bukti Kirim" class="w-64 border rounded">
                        </div>
                    @endif
                    @if ($invoice->delivery_signature)
                        <div>
                            <p class="font-medium">Tanda Tangan:</p>
                            <img src="{{ asset('storage/' . $invoice->delivery_signature) }}" alt="Tanda Tangan" class="w-64 border rounded">
                        </div>
                    @endif
                </div>
                @if ($invoice->delivery_proof_photo && $invoice->delivery_signature)
                    <form method="POST" action="{{ url('/admin/driver-transaksi/finish/' . $invoice->id) }}" class="mt-6">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                            Tandai Selesai / Sampai
                        </button>
                    </form>
                @endif
            @endif
        </div>
    @endif
@endsection

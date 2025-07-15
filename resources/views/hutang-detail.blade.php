<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Hutang | CHASTE</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 min-h-screen py-10">
    @include('layouts.customer-nav')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <a href="{{ route('profile') }}" class="inline-flex items-center gap-2 bg-teal-100 hover:bg-teal-200 text-teal-700 font-medium py-2 px-4 rounded-md text-sm transition mb-4">
            &larr; Kembali ke Profil
        </a>
        <h1 class="text-2xl font-bold mb-6 text-center">Detail Hutang & Pelunasan</h1>

        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-2">Daftar Tagihan Belum Lunas</h2>
            <table class="w-full border text-sm mb-2">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="border px-2 py-1">No</th>
                        <th class="border px-2 py-1">No. Invoice</th>
                        <th class="border px-2 py-1">Tanggal</th>
                        <th class="border px-2 py-1">Total</th>
                        <th class="border px-2 py-1">Sisa Hutang</th>
                        <th class="border px-2 py-1">Status</th>
                        <th class="border px-2 py-1">Jatuh Tempo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $inv)
                        <tr>
                            <td class="border px-2 py-1 text-center">{{ $loop->iteration }}</td>
                            <td class="border px-2 py-1">{{ $inv->code }}</td>
                            <td class="border px-2 py-1">{{ $inv->created_at->format('d-m-Y') }}</td>
                            <td class="border px-2 py-1">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</td>
                            <td class="border px-2 py-1">Rp {{ number_format($inv->grand_total - ($inv->paid_amount ?? 0), 0, ',', '.') }}</td>
                            <td class="border px-2 py-1">Belum Lunas</td>
                            <td class="border px-2 py-1">
                                @php $p = $inv->payments->first(); @endphp
                                @if($p && $p->method == 'hutang')
                                    {{ $inv->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('d-m-Y') : $inv->created_at->addMonth()->format('d-m-Y') }}
                                    @php
                                        $jatuhTempo = $inv->due_date ? \Carbon\Carbon::parse($inv->due_date) : $inv->created_at->addMonth();
                                    @endphp
                                    @if(now()->gt($jatuhTempo) && ($inv->grand_total - ($inv->paid_amount ?? 0)) > 0)
                                        <span class="text-red-500 font-bold ml-1">(Terlambat)</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-2">Tidak ada hutang aktif.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-2 text-right font-bold text-lg">
                Total Hutang: <span class="text-red-500">Rp {{ number_format($totalHutang, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="mb-4">
            <h2 class="text-lg font-semibold mb-2">Upload Pelunasan Hutang</h2>
            <form action="{{ route('profile.hutang.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label for="amount_paid" class="block text-sm font-medium mb-1">Nominal Pembayaran</label>
                    <input type="number" name="amount_paid" id="amount_paid" class="w-full border rounded px-3 py-2" min="1000" required>
                </div>
                <div>
                    <label for="payment_date" class="block text-sm font-medium mb-1">Tanggal Pembayaran</label>
                    <input type="date" name="payment_date" id="payment_date" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label for="payment_proof" class="block text-sm font-medium mb-1">Upload Bukti Pembayaran</label>
                    <input type="file" name="payment_proof" id="payment_proof" class="w-full border rounded px-3 py-2" accept="image/*" required>
                </div>
                <div>
                    <label for="notes" class="block text-sm font-medium mb-1">Catatan (opsional)</label>
                    <textarea name="notes" id="notes" class="w-full border rounded px-3 py-2"></textarea>
                </div>
                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">Kirim Bukti Pelunasan</button>
            </form>
        </div>
    </div>
    @include('layouts.footer')
</body>
</html> 
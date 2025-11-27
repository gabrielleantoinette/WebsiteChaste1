<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Hutang | CHASTE</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen py-10">
    @include('layouts.customer-nav')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <div class="mb-6">
            <x-breadcrumb :items="[
                ['label' => 'Profil', 'url' => route('profile')],
                ['label' => 'Hutang']
            ]" />
            <h1 class="text-2xl font-bold text-gray-800">Detail Hutang & Pelunasan</h1>
        </div>

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
                            <td class="border px-2 py-1">
                                @php $p = $inv->payments->first(); @endphp
                                @if($p && $p->method == 'cod')
                                    COD (Belum Lunas)
                                @elseif($p && $p->method == 'hutang')
                                    Hutang (Belum Lunas)
                                @else
                                    Belum Lunas
                                @endif
                            </td>
                            <td class="border px-2 py-1">
                                @php $p = $inv->payments->first(); @endphp
                                @if($p && $p->method == 'hutang')
                                    {{ $inv->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('d-m-Y') : $inv->created_at->copy()->addMonth()->format('d-m-Y') }}
                                    @php
                                        $jatuhTempo = $inv->due_date ? \Carbon\Carbon::parse($inv->due_date) : $inv->created_at->copy()->addMonth();
                                    @endphp
                                    @if(now()->gt($jatuhTempo) && ($inv->grand_total - ($inv->paid_amount ?? 0)) > 0)
                                        <span class="text-red-500 font-bold ml-1">(Terlambat)</span>
                                    @endif
                                @elseif($p && $p->method == 'cod')
                                    Bayar saat terima
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
                    <label for="invoice_id" class="block text-sm font-medium mb-1">Pilih Invoice</label>
                    <select name="invoice_id" id="invoice_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Pilih Invoice --</option>
                        @foreach($invoices as $inv)
                            @php
                                $currentPaid = $inv->paid_amount ?? 0;
                                $remainingDebt = $inv->grand_total - $currentPaid;
                                $p = $inv->payments->first();
                            @endphp
                            <option value="{{ $inv->id }}" data-remaining="{{ $remainingDebt }}">
                                {{ $inv->code }} - Sisa Hutang: Rp {{ number_format($remainingDebt, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="amount_paid" class="block text-sm font-medium mb-1">Nominal Pembayaran</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-600">Rp</span>
                        <input type="text" name="amount_paid" id="amount_paid" class="w-full border rounded px-10 py-2" placeholder="0" required>
                        <input type="hidden" name="amount_paid_raw" id="amount_paid_raw">
                    </div>
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

    <script>
        // Format rupiah untuk input nominal pembayaran
        function formatRupiah(angka) {
            // Hapus semua karakter non-digit
            let number_string = angka.replace(/[^,\d]/g, '').toString();
            let split = number_string.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah == '' ? '' : rupiah;
        }

        // Event listener untuk format input
        document.getElementById('amount_paid').addEventListener('input', function(e) {
            let value = e.target.value;
            let formattedValue = formatRupiah(value);
            e.target.value = formattedValue;
            
            // Simpan nilai asli (tanpa format) ke hidden input
            let rawValue = value.replace(/[^,\d]/g, '').replace(',', '.');
            document.getElementById('amount_paid_raw').value = rawValue;
        });

        // Auto-fill maksimal pembayaran berdasarkan invoice yang dipilih
        document.getElementById('invoice_id').addEventListener('change', function(e) {
            let selectedOption = e.target.options[e.target.selectedIndex];
            let remainingDebt = selectedOption.getAttribute('data-remaining');
            
            if (remainingDebt && parseFloat(remainingDebt) > 0) {
                // Set placeholder dengan sisa hutang
                let amountInput = document.getElementById('amount_paid');
                amountInput.placeholder = 'Maksimal: Rp ' + parseFloat(remainingDebt).toLocaleString('id-ID');
            }
        });

        // Validasi form sebelum submit
        document.querySelector('form').addEventListener('submit', function(e) {
            let invoiceSelect = document.getElementById('invoice_id');
            let amountInput = document.getElementById('amount_paid');
            let rawAmount = document.getElementById('amount_paid_raw').value;
            
            // Validasi invoice dipilih
            if (!invoiceSelect.value) {
                e.preventDefault();
                alert('Silakan pilih invoice yang akan dibayar');
                return false;
            }
            
            // Validasi jumlah pembayaran tidak melebihi sisa hutang
            let selectedOption = invoiceSelect.options[invoiceSelect.selectedIndex];
            let remainingDebt = parseFloat(selectedOption.getAttribute('data-remaining'));
            let amountPaid = parseFloat(rawAmount);
            
            if (amountPaid > remainingDebt) {
                e.preventDefault();
                alert('Jumlah pembayaran tidak boleh melebihi sisa hutang (Rp ' + remainingDebt.toLocaleString('id-ID') + ')');
                return false;
            }
            
            // Set nilai asli ke input amount_paid untuk dikirim ke server
            amountInput.value = rawAmount;
            
            // Validasi minimum amount
            if (amountPaid < 1000) {
                e.preventDefault();
                alert('Nominal pembayaran minimum Rp 1.000');
                return false;
            }
        });
    </script>
</body>
</html> 
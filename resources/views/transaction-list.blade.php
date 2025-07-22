<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Transaksi | CHASTE</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-white font-sans text-gray-800">
    {{-- Header --}}
    @include('layouts.customer-nav')
    
    <!-- Transaksi -->
    <section class="px-[100px] h-screen">
    <div class="mb-6">
        <a href="{{ url()->previous() }}" 
        class="inline-flex items-center gap-2 bg-teal-100 hover:bg-teal-200 text-teal-700 font-medium py-2 px-4 rounded-md text-sm transition shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
    </div>
        <h2 class="text-xl font-semibold mb-6 bg-[#D9F2F2] inline-block px-4 py-2 rounded-md">🛍️ List Transaksi</h2>
        {{-- Filter Tab Status --}}
        @php
        $statusOptions = [
            '' => 'Semua',
            'menunggukonfirmasi' => 'Menunggu Konfirmasi Pembayaran',
            'dikemas' => 'Sedang Dikemas',
            'dikirim' => 'Dikirim',
            'diterima' => 'Selesai',
            'pengembalian' => 'Pengembalian',
            'beripenilaian' => 'Beri Penilaian'
        ];

        $currentStatus = request('status');
        @endphp

        <div class="flex gap-6 text-sm font-medium mb-6 border-b border-gray-200 overflow-x-auto">
        @foreach ($statusOptions as $key => $label)
            @php
                $count = \App\Models\HInvoice::where('customer_id', Session::get('user')['id'])
                            ->when($key !== '', function ($q) use ($key) {
                                if ($key === 'dikirim') {
                                    $q->whereIn('status', ['dikirim', 'sampai']);
                                } else if ($key === 'menunggukonfirmasi') {
                                    $q->where('status', 'Menunggu Konfirmasi Pembayaran');
                                } else if ($key === 'beripenilaian') {
                                    $q->where('status', 'diterima');
                                } else if ($key === 'pengembalian') {
                                    $q->where('status', 'retur_diajukan'); // Perbaikan di sini
                                } else {
                                    $q->where('status', $key);
                                }
                            })
                            ->count();
            @endphp

            <a href="{{ url('transaksi') . ($key !== '' ? '?status=' . $key : '') }}"
            class="pb-2 whitespace-nowrap transition-colors duration-200 {{ $currentStatus === $key ? 'text-teal-600 border-b-2 border-teal-500 font-semibold' : 'text-gray-800 hover:text-teal-500' }}">
                {{ $label }} ({{ $count }})
            </a>
        @endforeach
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">No</th>
                        <th class="px-4 py-3 text-left font-semibold">Kode Invoice</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left font-semibold">Total</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                        <tr class="border-b border-gray-100 @if($loop->odd) bg-gray-50 @endif hover:bg-teal-50 transition">
                            <td class="px-4 py-3">{{ $transaction->id }}</td>
                            <td class="px-4 py-3 font-mono text-teal-700 font-semibold">{{ $transaction->code }}</td>
                            <td class="px-4 py-3">{{ $transaction->created_at }}</td>
                            <td class="px-4 py-3 font-semibold">Rp {{ number_format($transaction->grand_total) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                    @if(strtolower($transaction->status) == 'dikemas') bg-teal-100 text-teal-700
                                    @elseif(strtolower($transaction->status) == 'menunggu konfirmasi pembayaran') bg-yellow-100 text-yellow-700
                                    @elseif(strtolower($transaction->status) == 'selesai' || strtolower($transaction->status) == 'diterima') bg-green-100 text-green-700
                                    @else bg-gray-200 text-gray-700 @endif">
                                    {{ ucwords(str_replace('_', ' ', $transaction->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('transaksi.detail', $transaction->id) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white text-sm font-semibold rounded-md shadow-sm hover:bg-teal-700 transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    <!-- Footer -->
    @include('layouts.footer')

    <script>
        const checkAll = document.getElementById('checkAll');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');

        checkAll.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => {
                cb.checked = checkAll.checked;
            });
        });
    </script>

</body>

</html>

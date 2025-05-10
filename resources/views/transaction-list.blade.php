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
            'dikemas' => 'Sedang Dikemas',
            'dikirim' => 'Dikirim',
            'diterima' => 'Selesai',
            'pengembalian' => 'Pengembalian'
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
                                } else {
                                    $q->where('status', $key);
                                }
                            })
                            ->count();
            @endphp

            <a href="{{ url('transaksi') . ($key !== '' ? '?status=' . $key : '') }}"
            class="pb-2 whitespace-nowrap {{ $currentStatus === $key ? 'text-red-600 border-b-2 border-red-500' : 'text-gray-800 hover:text-red-500' }}">
                {{ $label }}{{ $count > 0 ? ' ('.$count.')' : '' }}
            </a>
        @endforeach
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Invoice</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->id }}</td>
                        <td>{{ $transaction->code }}</td>
                        <td>{{ $transaction->created_at }}</td>
                        <td>Rp {{ number_format($transaction->grand_total) }}</td>
                        <td>{{ $transaction->status }}</td>
                        <td>
                            <a href="{{ route('transaksi.detail', $transaction->id) }}" class="btn btn-primary">Detail</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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

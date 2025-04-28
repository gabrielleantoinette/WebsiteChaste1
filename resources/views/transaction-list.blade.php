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
        <h2 class="text-xl font-semibold mb-6 bg-[#D9F2F2] inline-block px-4 py-2 rounded-md">🛍️ List Transaksi</h2>

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

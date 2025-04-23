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
    <footer class="bg-[#D9F2F2] py-10 px-6 md:px-20 text-sm text-gray-700 mt-20">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Brand -->
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">CHASTE</h3>
                <p class="mb-4 max-w-xs">Kami membantu anda menyediakan terpal terbaik.</p>
                <div class="flex space-x-4 text-lg">
                    <a href="#">📷</a>
                    <a href="#">🐦</a>
                    <a href="#">📘</a>
                </div>
            </div>

            <!-- Informasi -->
            <div class="space-y-2">
                <h4 class="font-semibold text-gray-800 mb-2">Informasi</h4>
                <a href="#" class="block hover:text-black">Tentang</a>
                <a href="#" class="block hover:text-black">Produk</a>
            </div>

            <!-- Kontak -->
            <div class="space-y-2">
                <h4 class="font-semibold text-gray-800 mb-2">Kontak Kami</h4>
                <p>Telp: 089123231221</p>
                <p>E-mail: xyz@bca</p>
            </div>
        </div>
        <div class="text-center text-xs text-gray-500 mt-8">
            © 2025 Hak Cipta Dilindungi
        </div>
    </footer>
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

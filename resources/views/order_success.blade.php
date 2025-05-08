<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil | CHASTE</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">

    {{-- Navbar Customer --}}
    @include('layouts.customer-nav')

    {{-- Main Content --}}
    <main class="flex-1 flex items-center justify-center p-6">
        <div class="bg-white p-10 rounded-2xl shadow-md text-center w-full max-w-md">
            <div class="flex justify-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-teal-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-teal-600 mb-2">Terima Kasih!</h1>
            <p class="text-gray-600 mb-6">Pesanan kamu telah berhasil dibuat. Tim kami akan segera memprosesnya!</p>

            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('produk') }}"
                    class="inline-block bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded-lg">
                    Kembali Belanja
                </a>
                <a href="{{ route('invoice.view', ['id' => session('last_invoice_id')]) }}" target="_blank"
                    class="inline-block bg-white border border-teal-500 text-teal-500 hover:bg-teal-500 hover:text-white font-semibold py-2 px-6 rounded-lg">
                    Lihat Invoice
                </a>
                <a href="{{ route('invoice.download', ['id' => session('last_invoice_id')]) }}"
                    class="inline-block bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded-lg">
                    Download Invoice
                </a>
                <button id="pay-button"
                    class="inline-block bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded-lg">
                    Bayar Sekarang
                </button>
            </div>

        </div>
    </main>

    @include('layouts.footer')

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-_63DbX6J3paRjarh"></script>
    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function() {
            window.snap.pay("{{ $snapToken }}", {
                onSuccess: function(result) {
                    alert("Pembayaran berhasil!");
                    console.log(result);
                },
                onPending: function(result) {
                    alert("Menunggu pembayaran!");
                    console.log(result);
                },
                onError: function(result) {
                    alert("Pembayaran gagal!");
                    console.log(result);
                },
                onClose: function() {
                    alert('Anda menutup pop-up tanpa menyelesaikan pembayaran');
                }
            });
        });
    </script>
</body>

</html>

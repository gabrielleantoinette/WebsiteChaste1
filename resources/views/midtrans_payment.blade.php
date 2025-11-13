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

    @include('layouts.footer')

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-_63DbX6J3paRjarh"></script>
    <script type="text/javascript">
        window.snap.pay("{{ $snapToken }}", {
            onSuccess: function(result) {
                console.log("Pembayaran berhasil!", result);
                // Redirect akan dilakukan oleh Midtrans via finish_redirect_url
                // Tapi sebagai backup, kita juga redirect manual
                window.location.href = "{{ url('/checkout/midtrans-result?paymentId=' . $paymentId . '&status=success') }}";
            },
            onPending: function(result) {
                console.log("Menunggu pembayaran!", result);
                // Redirect akan dilakukan oleh Midtrans via unfinish_redirect_url
                window.location.href = "{{ url('/checkout/midtrans-result?paymentId=' . $paymentId . '&status=pending') }}";
            },
            onError: function(result) {
                console.log("Pembayaran gagal!", result);
                // Redirect akan dilakukan oleh Midtrans via error_redirect_url
                window.location.href = "{{ url('/checkout/midtrans-result?paymentId=' . $paymentId . '&status=error') }}";
            },
            onClose: function() {
                // User menutup popup, redirect ke halaman produk
                if (confirm('Anda menutup pop-up tanpa menyelesaikan pembayaran. Apakah Anda ingin kembali ke halaman produk?')) {
                    window.location.href = "{{ url('/produk') }}";
                }
            }
        });
    </script>
</body>

</html>

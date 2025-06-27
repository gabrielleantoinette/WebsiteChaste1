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
                alert("Pembayaran berhasil!");
                console.log(result);

                window.location.href = "/checkout/midtrans-result?paymentId={{ $paymentId }}&status=success";
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
        // });
    </script>
</body>

</html>

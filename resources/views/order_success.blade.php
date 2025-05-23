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
                <a href="{{ route('invoice.view', ['id' => session('last_invoice_id')]) }}" target="_blank"
                    class="text-sm bg-white border border-teal-500 text-teal-500 hover:bg-teal-500 hover:text-white font-semibold py-2 px-6 rounded-lg">
                    Lihat Invoice
                </a>
                <a href="{{ route('invoice.download', ['id' => session('last_invoice_id')]) }}"
                    class="text-sm bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded-lg">
                    Download Invoice
                </a>
                <a href="{{ route('produk') }}"
                    class="text-sm bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded-lg col-span-2">
                    Kembali Belanja
                </a>
            </div>

        </div>
    </main>

    @include('layouts.footer')
</body>

</html>

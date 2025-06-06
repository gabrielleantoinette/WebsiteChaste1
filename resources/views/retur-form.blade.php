<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Retur Barang</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 min-h-screen">
    @include('layouts.customer-nav')

    <div class="max-w-3xl mx-auto py-10 px-6">
        <h1 class="text-2xl font-bold text-center mb-6">Form Retur Barang</h1>

        <div class="bg-white rounded-lg shadow p-6 space-y-4">
            <p><strong>Kode Invoice:</strong> {{ $transaction->code }}</p>
            <p><strong>Status:</strong> {{ $transaction->status }}</p>
            <p><strong>Total Pembayaran:</strong> Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</p>

            <form method="POST" action="{{ route('retur.store', $transaction->id) }}" enctype="multipart/form-data" class="space-y-4 mt-4">
                @csrf

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Alasan Retur</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full mt-1 border border-gray-300 rounded-md p-2 focus:ring-teal-500 focus:border-teal-500"
                              required>{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="media" class="block text-sm font-medium text-gray-700">Upload Bukti (Foto/Video)</label>
                    <input type="file" name="media" id="media"
                           class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md file:bg-teal-50 file:border-0 file:px-4 file:py-2 file:cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1">Format: jpg, jpeg, png, mp4 (max 10MB)</p>
                </div>

                <div class="text-right">
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-2 rounded-md shadow">
                        Kirim Retur
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('layouts.footer')
</body>
</html>

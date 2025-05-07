<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi | CHASTE</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen">
    @include('layouts.customer-nav')

    <div class="max-w-4xl mx-auto py-10 px-4">
    <div class="mb-6">
        <a href="{{ url()->previous() }}" 
        class="inline-flex items-center gap-2 bg-teal-100 hover:bg-teal-200 text-teal-700 font-medium py-2 px-4 rounded-md text-sm transition shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
    </div>
        <h1 class="text-2xl font-bold text-center mb-8">Detail Transaksi</h1>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <p><strong>Kode Invoice:</strong> {{ $transaction->code }}</p>
            <p><strong>Status:</strong> {{ $transaction->status }}</p>
            <p><strong>Alamat Pengiriman:</strong> {{ $transaction->address }}</p>
            <p><strong>Total Pembayaran:</strong> Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</p>
        </div>

        @if ($transaction->status == 'sampai')
            <div class="mt-5 space-y-2 text-sm">
                <div>Pesanan kamu sudah sampai, silahkan klik tombol dibawah untuk menyelesaikan transaksi.</div>
                <form method="POST" action="{{ route('transaksi.diterima', $transaction->id) }}">
                    @csrf
                    <button class="bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 px-6 rounded">
                        Selesaikan Pesanan
                    </button>
                </form>
            </div>
        @endif

        @if ($transaction->status == 'diterima')
            <div class="mt-5 space-y-2 text-sm bg-white p-4 rounded-lg shadow">
                <div>Beri review untuk pesanan kamu yuk</div>
                <form method="POST" action="{{ route('transaksi.diterima', $transaction->id) }}" class="space-y-2">
                    @csrf
                    <div class="flex items-center gap-2">
                        <span>Rating</span>
                        <div class="flex items-center gap-1">
                            <input type="hidden" name="rating" id="rating" value="0">
                            <div class="flex gap-1" id="starRating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-6 h-6 text-gray-300 cursor-pointer star"
                                        data-value="{{ $i }}" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                    </svg>
                                    <svg class="w-6 h-6 text-yellow-400 cursor-pointer star-filled hidden"
                                        data-value="{{ $i }}" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <textarea name="review" id="review" class="w-full border bg-white border-gray-300 rounded-md p-2"></textarea>
                    <button class="bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 px-6 rounded">
                        Beri Review
                    </button>
                </form>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');
            const starsFilled = document.querySelectorAll('.star-filled');
            const ratingInput = document.getElementById('rating');

            function updateStars(value) {
                stars.forEach((star, index) => {
                    const starFilled = starsFilled[index];
                    if (index < value) {
                        star.classList.add('hidden');
                        starFilled.classList.remove('hidden');
                    } else {
                        star.classList.remove('hidden');
                        starFilled.classList.add('hidden');
                    }
                });
            }

            stars.forEach((star, index) => {
                const starFilled = starsFilled[index];

                star.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    ratingInput.value = value;
                    updateStars(value);
                });

                starFilled.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    ratingInput.value = value;
                    updateStars(value);
                });

                // Add hover effect
                star.addEventListener('mouseover', function() {
                    const value = this.getAttribute('data-value');
                    updateStars(value);
                });

                starFilled.addEventListener('mouseover', function() {
                    const value = this.getAttribute('data-value');
                    updateStars(value);
                });

                star.addEventListener('mouseout', function() {
                    const currentValue = ratingInput.value;
                    updateStars(currentValue);
                });

                starFilled.addEventListener('mouseout', function() {
                    const currentValue = ratingInput.value;
                    updateStars(currentValue);
                });
            });
        });
    </script>

@include('layouts.footer')

</body>

</html>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Transaksi | CHASTE</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen">
    @include('layouts.customer-nav')

    <div class="max-w-4xl mx-auto py-10 px-4">
        <div class="mb-6">
            <x-breadcrumb :items="[
                ['label' => 'Transaksi', 'url' => route('transaksi')],
                ['label' => 'Detail']
            ]" />
            <h1 class="text-2xl font-bold text-gray-800">Detail Transaksi</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6 text-black">
            <p><strong>Kode Invoice:</strong> {{ $transaction->code }}</p>
            <p><strong>Status:</strong> {{ $transaction->status }}</p>
            <p><strong>Alamat Pengiriman:</strong> {{ $transaction->address }}</p>
            <p><strong>Total Pembayaran:</strong> Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</p>
        </div>

        @if($product)
        <!-- Detail Produk yang Dibeli -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Detail Produk yang Dibeli</h2>
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Foto Produk -->
                <div class="md:w-1/3">
                    <div class="bg-gray-100 rounded-lg p-4 flex items-center justify-center">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-48 object-cover rounded-lg">
                        @else
                            <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-4xl"></i>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Informasi Produk -->
                <div class="md:w-2/3">
                    <div class="space-y-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600">Ukuran: {{ $product->size }}</p>
                        </div>
                        
                        <div>
                            <p class="text-lg font-bold text-teal-600">
                                Rp {{ number_format($product->price, 0, ',', '.') }} / unit
                            </p>
                        </div>
                        
                        <div>
                            <p class="text-gray-700">{{ $product->description }}</p>
                        </div>
                        
                        @if($dinvoice)
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-sm text-gray-600">
                                <strong>Jumlah yang dibeli:</strong> {{ $dinvoice->quantity }} unit
                            </p>
                            <p class="text-sm text-gray-600">
                                <strong>Subtotal:</strong> Rp {{ number_format($dinvoice->quantity * $product->price, 0, ',', '.') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

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
                @if (!$hasReviewed)
                    <div class="font-medium text-gray-800 mb-3">Beri review untuk pesanan kamu yuk</div>
                    
                    <!-- Review Form -->
                    <div id="reviewForm" class="space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="font-medium">Rating:</span>
                            <div class="flex items-center gap-1">
                                <input type="hidden" name="rating" id="rating" value="0">
                                <div class="flex gap-1" id="starRating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-7 h-7 text-gray-300 cursor-pointer star hover:text-yellow-400 transition-colors"
                                            data-value="{{ $i }}" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285-5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.563.563 0 0 1 .321-.988l5.518-.442c.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                        </svg>
                                        <svg class="w-7 h-7 text-yellow-400 cursor-pointer star-filled hidden"
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
                        
                        <div>
                            <label for="review" class="block font-medium text-gray-700 mb-2">Komentar (opsional):</label>
                            <textarea name="review" id="review" placeholder="Bagaimana pengalaman kamu dengan produk ini?" 
                                      class="w-full border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 resize-none" 
                                      rows="4"></textarea>
                        </div>
                        
                        <button type="button" id="submitReviewBtn" 
                                class="bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Kirim Review
                        </button>
                    </div>

                    <!-- Review Success Message -->
                    <div id="reviewSuccess" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        <i class="fas fa-check-circle mr-2"></i>
                        Review berhasil dikirim! Terima kasih atas feedback kamu.
                    </div>

                    <!-- Review Error Message -->
                    <div id="reviewError" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span id="errorMessage"></span>
                    </div>
                @else
                    <!-- Sudah Review Message -->
                    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded">
                        <i class="fas fa-check-circle mr-2"></i>
                        <strong>Terima kasih!</strong> Kamu sudah memberikan review untuk pesanan ini.
                    </div>
                @endif
            </div>
        @endif

        @php
            $isSampai = $transaction->status === 'sampai';
            $sampaiKurangDari24Jam = $isSampai && \Carbon\Carbon::parse($transaction->updated_at)->diffInHours(now()) < 24;
        @endphp

        @if ($sampaiKurangDari24Jam)
            <div class="mt-4">
                <a href="{{ url('/retur/' . $transaction->id) }}"
                class="inline-block bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-md shadow text-sm">
                    🔁 Retur Barang
                </a>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');
            const starsFilled = document.querySelectorAll('.star-filled');
            const ratingInput = document.getElementById('rating');
            const submitReviewBtn = document.getElementById('submitReviewBtn');
            const reviewForm = document.getElementById('reviewForm');
            const reviewSuccess = document.getElementById('reviewSuccess');
            const reviewError = document.getElementById('reviewError');
            const errorMessage = document.getElementById('errorMessage');

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

            submitReviewBtn.addEventListener('click', function() {
                const rating = ratingInput.value;
                const review = document.getElementById('review').value;

                if (rating === '0') {
                    errorMessage.textContent = 'Silakan beri rating terlebih dahulu.';
                    reviewError.classList.remove('hidden');
                    reviewSuccess.classList.add('hidden');
                    return;
                }

                                 submitReviewBtn.disabled = true;
                 submitReviewBtn.textContent = 'Mengirim...';
                 
                 console.log('Sending review data:', {
                     order_id: {{ $transaction->id }},
                     product_id: {{ $productId ?? 1 }},
                     rating: rating,
                     comment: review
                 });

                                 fetch(`{{ route('review.submit') }}`, {
                     method: 'POST',
                     headers: {
                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                         'Content-Type': 'application/json',
                     },
                     body: JSON.stringify({
                         order_id: {{ $transaction->id }},
                         product_id: {{ $productId ?? 1 }}, // Product ID dari dinvoice
                         rating: parseInt(rating),
                         comment: review,
                     }),
                 })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        reviewForm.classList.add('hidden');
                        reviewSuccess.classList.remove('hidden');
                        errorMessage.textContent = '';
                    } else {
                        errorMessage.textContent = data.message || 'Gagal mengirim review.';
                        reviewError.classList.remove('hidden');
                        reviewSuccess.classList.add('hidden');
                    }
                                         submitReviewBtn.disabled = false;
                     submitReviewBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Kirim Review';
                })
                                 .catch(error => {
                     console.error('Error:', error);
                     errorMessage.textContent = 'Terjadi kesalahan saat mengirim review. Silakan coba lagi.';
                     reviewError.classList.remove('hidden');
                     reviewSuccess.classList.add('hidden');
                     submitReviewBtn.disabled = false;
                     submitReviewBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Kirim Review';
                 });
            });
        });
    </script>

@include('layouts.footer')

</body>

</html>

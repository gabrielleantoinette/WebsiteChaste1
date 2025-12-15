<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Reset Password | CHASTE</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-white font-sans">
    @include('layouts.customer-nav')

    <main class="flex items-center justify-center min-h-screen px-4 md:px-0 bg-white">
        <div
            class="flex flex-col md:flex-row overflow-hidden rounded-[20px] shadow-lg border border-gray-200 max-w-5xl w-full h-[500px]">
            <div class="hidden md:block w-[400px] h-full bg-gray-50 relative">
                <img src="{{ asset('images/terpal-login.png') }}" alt="Terpal Gulungan"
                    class="absolute inset-0 w-full h-full object-cover" style="will-change: auto;">
            </div>

            <div class="w-full md:w-[700px] bg-white p-8 flex flex-col justify-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-3 text-center">VERIFIKASI RESET PASSWORD</h2>
                
                @if (session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md text-sm mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md text-sm mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md text-sm mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <p class="text-sm text-gray-600 mb-2 text-center">
                    Masukkan kode verifikasi 6 digit yang telah dikirim ke email Anda
                </p>
                <p class="text-sm text-gray-500 mb-4 text-center">
                    Email: <strong>{{ $email }}</strong>
                </p>

                <form method="POST" action="{{ route('password.verify-otp') }}" class="mt-3">
                    @csrf

                    <label class="block mb-2 text-sm font-medium text-gray-700">Kode Verifikasi</label>
                    <input type="text" name="otp" id="otp" maxlength="6" required
                        class="w-full mb-4 px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-black outline-none text-black text-center text-2xl tracking-widest"
                        placeholder="000000" autocomplete="off">

                    <button type="submit"
                        class="w-full bg-[#D9F2F2] hover:bg-teal-200 text-gray-800 font-semibold py-3 rounded-md transition mb-3">
                        Verifikasi
                    </button>
                </form>

                <form method="POST" action="{{ route('password.resend-otp') }}" class="text-center">
                    @csrf
                    <button type="submit"
                        class="text-sm text-gray-600 hover:text-gray-800 underline">
                        Kirim ulang kode verifikasi
                    </button>
                </form>

                <div class="text-center text-sm mt-4">
                    <a href="{{ route('password.request') }}" class="text-red-500 font-medium">Kembali ke Lupa Password</a>
                </div>
            </div>
        </div>
    </main>

    @include('layouts.footer')

    <script>
        // Auto-focus on OTP input
        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otp');
            if (otpInput) {
                otpInput.focus();
            }

            // Only allow numbers
            otpInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
</body>

</html>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Masuk</title>
    @vite('resources/css/app.css')
</head>

<body>
    <div class="flex flex-col items-center justify-center h-screen">
        <div class="w-full max-w-md p-4 border border-gray-200 rounded-md shadow-lg">
            <a href="{{ url('/') }}" class="text-blue-500">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                    Kembali
                </div>
            </a>
            <h3 class="text-center text-2xl font-bold">Masuk</h3>
            <form method="POST">
                @csrf
                <div class="mb-3">
                    <div class="form-label fw-semibold">Email:</div>
                    <input type="email" name="email" class="input input-primary w-full" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password:</label>
                    <input type="password" name="password" class="input input-primary w-full" required>
                </div>

                <button type="submit" class="btn btn-primary w-full">Masuk</button>
            </form>

            <div class="text-center mt-4">Belum punya akun? <a href="{{ url('/register') }}"
                    class="text-blue-500">Daftar</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

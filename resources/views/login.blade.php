<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Masuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="container d-flex flex-column align-items-center justify-content-center vh-100">
        <div class="w-100 text-start">

        </div>

        <div class="col-md-6 col-lg-4 border p-4 rounded-4 bg-white shadow-lg">
            <a href="{{ url('/') }}" class="btn btn-danger">Kembali</a>
            <h3 class="text-center mb-4 fw-bold">Masuk</h3>
            <form method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Username:</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Masuk</button>
            </form>

            <div class="text-center mt-4">Belum punya akun? <a href="{{ url('/register') }}">Daftar</a></div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

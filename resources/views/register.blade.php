<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Daftar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="container d-flex flex-column align-items-center justify-content-center vh-100">
        <div class="w-100 text-start">

        </div>

        <div class="col-md-6 col-lg-4 border p-4 rounded-4 bg-white shadow-lg">
            <a href="{{ url('/') }}" class="btn btn-danger">Kembali</a>
            <h3 class="text-center mb-4 fw-bold">Daftar</h3>
            <form method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Username:</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor Telepon:</label>
                    <input type="tel" name="phone" class="form-control" required pattern="[0-9]{10,15}" placeholder="Masukkan nomor telepon">
                    <small class="text-muted">Gunakan angka saja (10-15 digit).</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>


            <div class="text-center mt-4">Sudah punya akun? <a href="{{ url('/login') }}">Masuk</a></div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

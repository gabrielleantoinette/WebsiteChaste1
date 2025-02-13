<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="container py-4">
        <header class="d-flex flex-wrap align-items-center justify-content-between py-3 mb-4 border-bottom">
            <a href="/" class="d-flex align-items-center text-dark text-decoration-none">
                <span class="fs-4 fw-bold">Admin</span>
            </a>
            <ul class="nav nav-pills">
                <li class="nav-item"><a href="{{ url('/') }}" class="nav-link">Dashboard</a></li>
                <li class="nav-item"><a href="{{ url('/admin/products') }}" class="nav-link text-secondary">Products</a>
                </li>
                <li class="nav-item"><a href="{{ url('/admin/employees') }}"
                        class="nav-link text-secondary">Employees</a></li>
                <li class="nav-item"><a href="{{ url('/') }}" class="nav-link text-secondary">Setting</a></li>
            </ul>
            <ul class="nav nav-pills">
                <!-- <li class="nav-item"><a href="#" class="nav-link active" aria-current="page">Home</a></li> -->
                <!-- <li class="nav-item"><a href="{{ url('/admin/login') }}" class="nav-link">Admin</a></li> -->
                <!-- <li class="nav-item"><a href="#" class="nav-link">Features</a></li>
                    <li class="nav-item"><a href="#" class="nav-link">Pricing</a></li>
                    <li class="nav-item"><a href="#" class="nav-link">FAQs</a></li>
                    <li class="nav-item"><a href="#" class="nav-link">About</a></li> -->
                <li class="nav-item"><a href="{{ url('login') }}" class="nav-link">Login</a></li>
            </ul>
        </header>
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

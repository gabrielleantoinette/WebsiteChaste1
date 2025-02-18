<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chaste App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .carousel-item img {
            height: 400px; 
            object-fit: cover; 
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="container">
        <header class="d-flex flex-wrap align-items-center justify-content-between py-3 mb-4 border-bottom">
            <a href="/" class="d-flex align-items-center text-dark text-decoration-none">
                <svg class="bi me-2" width="40" height="32">
                    <use xlink:href="#bootstrap"></use>
                </svg>
                <span class="fs-4 fw-bold">Welcome to PT. Chaste Gemilang Mandiri</span>
            </a>

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
    </div>

    <div id="carouselExampleIndicators" class="carousel slide mb-5" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://down-id.img.susercontent.com/file/id-11134207-7r98p-lsryo37ct7ba26.webp" class="d-block w-100" alt="Terpal 1">
            </div>
            <div class="carousel-item">
                <img src="https://down-id.img.susercontent.com/file/id-11134207-7r98u-lyvqgf3hn4dkca@resize_w450_nl.webp" class="d-block w-100" alt="Terpal 2">
            </div>
            <div class="carousel-item">
                <img src="https://down-id.img.susercontent.com/file/id-11134207-7r98q-lte34yqqjhah59.webp" class="d-block w-100" alt="Terpal 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="container">
        <h3 class="mb-3">Produk Kami</h3>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card shadow">
                    <img src="https://down-id.img.susercontent.com/file/id-11134207-7r98p-lsryo37ct7ba26.webp" class="card-img-top" alt="Terpal A">
                    <div class="card-body">
                        <h5 class="card-title">Terpal A</h5>
                        <p class="card-text">Harga: Rp 100.000</p>
                        <a href="#" class="btn btn-primary">Beli Sekarang</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow">
                    <img src="https://down-id.img.susercontent.com/file/id-11134207-7r98u-lyvqgf3hn4dkca@resize_w450_nl.webp" class="card-img-top" alt="Terpal B">
                    <div class="card-body">
                        <h5 class="card-title">Terpal B</h5>
                        <p class="card-text">Harga: Rp 120.000</p>
                        <a href="#" class="btn btn-primary">Beli Sekarang</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow">
                    <img src="https://down-id.img.susercontent.com/file/id-11134207-7r98q-lte34yqqjhah59.webp" class="card-img-top" alt="Terpal C">
                    <div class="card-body">
                        <h5 class="card-title">Terpal C</h5>
                        <p class="card-text">Harga: Rp 150.000</p>
                        <a href="#" class="btn btn-primary">Beli Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

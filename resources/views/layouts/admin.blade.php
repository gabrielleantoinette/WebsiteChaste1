<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin</title>
    @vite('resources/css/app.css')
</head>

<body>
    <header class="bg-tosca py-5">
        <div class="max-w-[90%] mx-auto flex justify-between items-center gap-10">
            <div class="w-[200px] text-white text-2xl font-semibold">Admin</div>
            <div class="flex justify-center items-center gap-2 flex-wrap">
                <a href="{{ url('/admin') }}">
                    <div class="btn btn-primary">Dashboard</div>
                </a>
                <a href="{{ url('/admin/products') }}">
                    <div class="btn btn-primary">Products</div>
                </a>
                <a href="{{ url('/admin/employees') }}">
                    <div class="btn btn-primary">Employees</div>
                </a>
                <a href="{{ url('/admin/customers') }}">
                    <div class="btn btn-primary">Customers</div>
                </a>
            </div>
            <div class="flex items-center gap-2">
                <div>{{ Session::get('user')->name }}</div>
                <div>|</div>
                <div><a href="{{ url('logout') }}" class="nav-link">Logout</a></div>
            </div>
        </div>
    </header>
    <div class="max-w-[90%] mx-auto pt-10">
        @yield('content')
    </div>
</body>

</html>

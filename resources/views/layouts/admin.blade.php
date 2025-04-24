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
    <header class="bg-[#D9F2F2] py-5">
        <div class="max-w-[90%] mx-auto flex justify-between items-center gap-10">
            <div class="text-xl font-bold text-gray-800">Master</div>
            <div class="flex justify-center items-center gap-3 flex-wrap mt-4">
                <a href="{{ url('/admin') }}" class="px-4 py-2 border border-teal-600 text-teal-600 font-semibold rounded-md hover:bg-teal-50 transition">Dashboard</a>

                @if (Session::get('user')->role == 'owner')
                    <a href="{{ url('/admin/products') }}" class="px-4 py-2 border border-teal-600 text-teal-600 font-semibold rounded-md hover:bg-teal-50 transition">Products</a>
                    <a href="{{ url('/admin/employees') }}" class="px-4 py-2 border border-teal-600 text-teal-600 font-semibold rounded-md hover:bg-teal-50 transition">Employees</a>
                    <a href="{{ url('/admin/customers') }}" class="px-4 py-2 border border-teal-600 text-teal-600 font-semibold rounded-md hover:bg-teal-50 transition">Customers</a>
                    <a href="{{ url('/admin/invoices') }}" class="px-4 py-2 border border-teal-600 text-teal-600 font-semibold rounded-md hover:bg-teal-50 transition">Penjualan</a>
                    <a href="{{ url('/admin/assign-driver') }}" class="px-4 py-2 border border-teal-600 text-teal-600 font-semibold rounded-md hover:bg-teal-50 transition">Assign Driver</a>
                    <a href="{{ url('/admin/gudang-transaksi') }}" class="px-4 py-2 border border-teal-600 text-teal-600 font-semibold rounded-md hover:bg-teal-50 transition">Transaksi Gudang</a>
                    <a href="{{ url('/admin/driver-transaksi') }}" class="px-4 py-2 border border-teal-600 text-teal-600 font-semibold rounded-md hover:bg-teal-50 transition">Transaksi Driver</a>

                @elseif (Session::get('user')->role == 'admin')
                    <a href="{{ url('/admin/products') }}" class="px-4 py-2 border border-teal-600 text-teal-600 font-semibold rounded-md hover:bg-teal-50 transition">Products</a>
                    <a href="{{ url('/admin/employees') }}" class="px-4 py-2 border border-teal-600 text-teal-600 font-semibold rounded-md hover:bg-teal-50 transition">Employees</a>
                    <a href="{{ url('/admin/customers') }}" class="px-4 py-2 border border-teal-600 text-teal-600 font-semibold rounded-md hover:bg-teal-50 transition">Customers</a>
                    <a href="{{ url('/admin/invoices') }}" class="px-4 py-2 border border-teal-600 text-teal-600 font-semibold rounded-md hover:bg-teal-50 transition">Penjualan</a>

                @elseif (Session::get('user')->role == 'driver')
                    <a href="{{ url('/admin/driver-transaksi') }}" class="px-4 py-2 border border-teal-600 text-teal-600 font-semibold rounded-md hover:bg-teal-50 transition">Transaksi Driver</a>

                @elseif (Session::get('user')->role == 'gudang')
                    <a href="{{ url('/admin/gudang-transaksi') }}" class="px-4 py-2 border border-teal-600 text-teal-600 font-semibold rounded-md hover:bg-teal-50 transition">Transaksi Gudang</a>
                @endif
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
</head>

<body>
<header class="bg-[#D9F2F2] py-5">
    <div class="max-w-[90%] mx-auto flex justify-between items-center gap-10">
        <div class="text-xl font-bold text-gray-800">Master</div>

        <div class="flex justify-center items-center gap-3 flex-wrap mt-4">
            <a href="{{ url('/admin') }}"
               class="px-4 py-2 border font-semibold rounded-md transition
                      {{ request()->is('admin') ? 'bg-teal-600 text-white' : 'border-teal-600 text-teal-600 hover:bg-teal-50' }}">
               Dashboard
            </a>

            @if (Session::get('user')->role == 'owner')
                <a href="{{ url('/admin/products') }}"
                   class="px-4 py-2 border font-semibold rounded-md transition
                          {{ request()->is('admin/products*') ? 'bg-teal-600 text-white' : 'border-teal-600 text-teal-600 hover:bg-teal-50' }}">
                   Kelola Produk
                </a>

                <a href="{{ url('/admin/categories') }}"
                    class="px-4 py-2 border font-semibold rounded-md transition
                            {{ request()->is('admin/categories*') ? 'bg-teal-600 text-white' : 'border-teal-600 text-teal-600 hover:bg-teal-50' }}">
                    Kelola Kategori
                </a>

                <a href="{{ url('/admin/employees') }}"
                   class="px-4 py-2 border font-semibold rounded-md transition
                          {{ request()->is('admin/employees*') ? 'bg-teal-600 text-white' : 'border-teal-600 text-teal-600 hover:bg-teal-50' }}">
                   Kelola Pegawai
                </a>

                <a href="{{ url('/admin/customers') }}"
                   class="px-4 py-2 border font-semibold rounded-md transition
                          {{ request()->is('admin/customers*') ? 'bg-teal-600 text-white' : 'border-teal-600 text-teal-600 hover:bg-teal-50' }}">
                   Kelola Pembeli
                </a>

                <a href="{{ url('/admin/invoices') }}"
                   class="px-4 py-2 border font-semibold rounded-md transition
                          {{ request()->is('admin/invoices*') ? 'bg-teal-600 text-white' : 'border-teal-600 text-teal-600 hover:bg-teal-50' }}">
                   Penjualan
                </a>

                <a href="{{ url('/admin/assign-driver') }}"
                   class="px-4 py-2 border font-semibold rounded-md transition
                          {{ request()->is('admin/assign-driver') ? 'bg-teal-600 text-white' : 'border-teal-600 text-teal-600 hover:bg-teal-50' }}">
                   Atur Kurir
                </a>

                <a href="{{ url('/admin/gudang-transaksi') }}"
                   class="px-4 py-2 border font-semibold rounded-md transition
                          {{ request()->is('admin/gudang-transaksi') ? 'bg-teal-600 text-white' : 'border-teal-600 text-teal-600 hover:bg-teal-50' }}">
                   Transaksi Gudang
                </a>

                <a href="{{ url('/admin/driver-transaksi') }}"
                   class="px-4 py-2 border font-semibold rounded-md transition
                          {{ request()->is('admin/driver-transaksi') ? 'bg-teal-600 text-white' : 'border-teal-600 text-teal-600 hover:bg-teal-50' }}">
                   Transaksi Driver
                </a>

            @elseif (Session::get('user')->role == 'admin')
                <a href="{{ url('/admin/products') }}"
                   class="px-4 py-2 border font-semibold rounded-md transition
                          {{ request()->is('admin/products*') ? 'bg-teal-600 text-white' : 'border-teal-600 text-teal-600 hover:bg-teal-50' }}">
                   Kelola Produk
                </a>

                <a href="{{ url('/admin/custom-materials') }}"
                    class="px-4 py-2 border font-semibold rounded-md transition
                            {{ request()->is('admin/custom-materials*') ? 'bg-teal-600 text-white' : 'border-teal-600 text-teal-600 hover:bg-teal-50' }}">
                    Kelola Bahan Custom
                </a>

                <a href="{{ url('/admin/categories') }}"
                    class="px-4 py-2 border font-semibold rounded-md transition
                            {{ request()->is('admin/categories*') ? 'bg-teal-600 text-white' : 'border-teal-600 text-teal-600 hover:bg-teal-50' }}">
                    Kelola Kategori
                </a>

                <a href="{{ url('/admin/customers') }}"
                   class="px-4 py-2 border font-semibold rounded-md transition
                          {{ request()->is('admin/customers*') ? 'bg-teal-600 text-white' : 'border-teal-600 text-teal-600 hover:bg-teal-50' }}">
                   Kelola Pembeli
                </a>

                <a href="{{ url('/admin/invoices') }}"
                   class="px-4 py-2 border font-semibold rounded-md transition
                          {{ request()->is('admin/invoices*') ? 'bg-teal-600 text-white' : 'border-teal-600 text-teal-600 hover:bg-teal-50' }}">
                   Kelola Penjualan
                </a>

            @elseif (Session::get('user')->role == 'driver')
                <a href="{{ url('/admin/driver-transaksi') }}"
                   class="px-4 py-2 border font-semibold rounded-md transition
                          {{ request()->is('admin/driver-transaksi') ? 'bg-teal-600 text-white' : 'border-teal-600 text-teal-600 hover:bg-teal-50' }}">
                   Transaksi Kurir
                </a>

            @elseif (Session::get('user')->role == 'gudang')
                <a href="{{ url('/admin/gudang-transaksi') }}"
                   class="px-4 py-2 border font-semibold rounded-md transition
                          {{ request()->is('admin/gudang-transaksi') ? 'bg-teal-600 text-white' : 'border-teal-600 text-teal-600 hover:bg-teal-50' }}">
                   Transaksi Gudang
                </a>
            @endif

            @if (Session::get('user')->role == 'keuangan')
                <a href="{{ route('keuangan.view') }}"
                class="px-4 py-2 border font-semibold rounded-md transition
                        {{ request()->is('admin/keuangan') ? 'bg-teal-600 text-white' : 'border-teal-600 text-teal-600 hover:bg-teal-50' }}">
                Laporan Transaksi Pembeli
                </a>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.11.2/js/jquery.dataTables.min.js"></script>
{{-- <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script> --}}
<script>
    $(document).ready(function() {
        $(".harga").on("input", function() {
            // Remove commas and non-numeric characters from the input value
            let rawValue = $(this).val().replace(/[^0-9]/g, '');

            // Format the input value with thousand separators
            let formattedValue = Number(rawValue).toLocaleString();

            // Update the input value with the formatted value
            $(this).val(formattedValue);
        });

        let table = $('.data-table').DataTable({
            'order': []
        });
    });
</script>

</html>

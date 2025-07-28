@php
    use App\Models\Setting;
    $setting = Setting::first();
    $theme = $setting?->theme ?? 'light';
@endphp

<!DOCTYPE html>
<html lang="en" class="{{ $theme === 'dark' ? 'dark' : '' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>

<body class="{{ $theme }}">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#D9F2F2] text-gray-800 p-6 space-y-4">
        <h2 class="text-xl font-bold mb-6">CHASTE Master</h2>

        <nav class="flex flex-col gap-2 text-sm font-medium">
            @php $role = Session::get('user')->role ?? ''; @endphp
            @if (Session::get('user')->role == 'driver')
                <a href="{{ url('/admin/dashboard-driver') }}" class="{{ request()->is('admin/dashboard-driver') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Dashboard Driver</a>
                <a href="{{ url('/admin/driver-transaksi') }}" class="{{ request()->is('admin/driver-transaksi') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Transaksi Kurir</a>
            @elseif ($role == 'keuangan')
                <a href="{{ route('keuangan.dashboard') }}" class="{{ request()->routeIs('keuangan.dashboard') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Dashboard</a>
            @elseif ($role == 'gudang')
                <a href="{{ route('gudang.dashboard') }}" class="{{ request()->routeIs('gudang.dashboard') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Dashboard Gudang</a>
                <a href="{{ url('/admin/gudang-transaksi') }}" class="{{ request()->is('admin/gudang-transaksi') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Transaksi Gudang</a>
                <a href="{{ route('gudang.barang-rusak') }}" class="{{ request()->routeIs('gudang.barang-rusak') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Barang Rusak</a>
            @else
                <a href="{{ url('/admin') }}" class="{{ request()->is('admin') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Dashboard</a>
            @endif

            @if (Session::get('user')->role == 'owner')
                <a href="{{ url('/admin/products') }}" class="{{ request()->is('admin/products*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Produk</a>
                <a href="{{ url('/admin/custom-materials') }}" class="{{ request()->is('admin/custom-materials*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Bahan Custom</a>
                <a href="{{ url('/admin/categories') }}" class="{{ request()->is('admin/categories*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Kategori</a>
                <a href="{{ url('/admin/employees') }}" class="{{ request()->is('admin/employees*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Pegawai</a>
                <a href="{{ url('/admin/customers') }}" class="{{ request()->is('admin/customers*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Pembeli</a>
                <a href="{{ url('/admin/invoices') }}" class="{{ request()->is('admin/invoices*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Penjualan</a>
                <a href="{{ url('/admin/transactions') }}" class="{{ request()->is('admin/transactions*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Transaksi</a>
                <a href="{{ url('/admin/assign-driver') }}" class="{{ request()->is('admin/assign-driver') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Atur Kurir</a>

                <a href="{{ url('/admin/settings') }}" class="{{ request()->is('admin/settings*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Pengaturan Website</a>
            @endif

            @if (Session::get('user')->role == 'admin')
                <a href="{{ url('/admin/products') }}" class="{{ request()->is('admin/products*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Produk</a>
                <a href="{{ url('/admin/custom-materials') }}" class="{{ request()->is('admin/custom-materials*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Bahan Custom</a>
                <a href="{{ url('/admin/categories') }}" class="{{ request()->is('admin/categories*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Kategori</a>
                <a href="{{ url('/admin/customers') }}" class="{{ request()->is('admin/customers*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Pembeli</a>
                <a href="{{ url('/admin/invoices') }}" class="{{ request()->is('admin/invoices*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Penjualan</a>
                <a href="{{ route('admin.retur.index') }}" class="{{ request()->routeIs('admin.retur.index') || request()->routeIs('admin.retur.detail') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Retur</a>
            @endif

            @if (Session::get('user')->role == 'keuangan')
                <a href="{{ route('keuangan.view') }}" class="{{ request()->is('admin/keuangan') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Laporan Transaksi</a>
                <a href="{{ route('keuangan.hutang.index') }}"
                class="{{ request()->is('admin/keuangan/keuangan/hutang*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">
                    Hutang Supplier
                </a>
            @endif
        </nav>

        <!-- Profil + Logout -->
        <div class="pt-10 border-t text-sm text-gray-600">
            <div class="mb-2">{{ Session::get('user')->name }}</div>
            <a href="{{ url('logout') }}" class="text-red-600 hover:underline">Logout</a>
        </div>
    </aside>

    <!-- Konten Utama -->
    <main class="flex-1 bg-white dark:bg-gray-900 text-gray-800 dark:text-white p-8">
        @yield('content')
    </main>
</div>

@stack('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.11.2/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $(".harga").on("input", function() {
            let rawValue = $(this).val().replace(/[^0-9]/g, '');
            let formattedValue = Number(rawValue).toLocaleString();
            $(this).val(formattedValue);
        });
        $('.data-table').DataTable({ order: [] });
    });
</script>
</body>
</html>

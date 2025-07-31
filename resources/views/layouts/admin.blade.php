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
            @php 
                $user = Session::get('user');
                $role = is_array($user) ? $user['role'] ?? '' : $user->role ?? '';
            @endphp
            @if ($role == 'driver')
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

            @if ($role == 'owner')
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

            @if ($role == 'admin')
                <a href="{{ url('/admin/products') }}" class="{{ request()->is('admin/products*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Produk</a>
                <a href="{{ url('/admin/custom-materials') }}" class="{{ request()->is('admin/custom-materials*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Bahan Custom</a>
                <a href="{{ url('/admin/categories') }}" class="{{ request()->is('admin/categories*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Kategori</a>
                <a href="{{ url('/admin/customers') }}" class="{{ request()->is('admin/customers*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Pembeli</a>
                <a href="{{ url('/admin/invoices') }}" class="{{ request()->is('admin/invoices*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Penjualan</a>
                <a href="{{ route('admin.retur.index') }}" class="{{ request()->routeIs('admin.retur.index') || request()->routeIs('admin.retur.detail') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Kelola Retur</a>
            @endif

            @if ($role == 'keuangan')
                <a href="{{ route('keuangan.view') }}" class="{{ request()->is('admin/keuangan') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">Laporan Transaksi</a>
                <a href="{{ route('keuangan.hutang.index') }}"
                class="{{ request()->is('admin/keuangan/keuangan/hutang*') ? 'bg-teal-600 text-white' : 'text-teal-700 hover:bg-teal-100' }} px-4 py-2 rounded">
                    Hutang Supplier
                </a>
            @endif
        </nav>

        <!-- Profil + Logout -->
        <div class="pt-10 border-t text-sm text-gray-600">
            <div class="mb-2">{{ is_array($user) ? $user['name'] : $user->name }}</div>
            <div class="flex items-center justify-between mb-2">
                <a href="#" id="notifBell" class="text-teal-600 hover:text-teal-800 relative">
                    <i class="fas fa-bell text-xl"></i>
                    <div id="notificationBadge" class="notification-badge bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center absolute -top-2 -right-2 shadow-lg animate-pulse" style="display: none;">
                        <span class="count font-bold">0</span>
                    </div>
                    <!-- Popover -->
                    <div id="notifPopover" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 z-50" style="top:2.5rem;">
                        <div class="p-4 border-b border-gray-100 font-semibold text-gray-800 flex justify-between items-center">
                            <span class="text-teal-600">Notifikasi</span>
                            <button id="notifPopoverClose" class="text-gray-400 hover:text-red-500 text-lg transition-colors">&times;</button>
                        </div>
                        <div id="notifPopoverContent" class="max-h-[350px] overflow-y-auto divide-y divide-gray-50"></div>
                    </div>
                </a>
                <a href="{{ url('logout') }}" class="text-red-600 hover:underline">Logout</a>
            </div>
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
        
        // Update notification badge on page load
        updateNotificationBadge();
        
        // Update notification badge every 30 seconds
        setInterval(updateNotificationBadge, 30000);
    });
    
    function updateNotificationBadge() {
        fetch('/notifications/unread-count')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notificationBadge');
                const countSpan = badge.querySelector('.count');
                
                if (data.count > 0) {
                    countSpan.textContent = data.count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error updating notification badge:', error);
            });
    }

    $(function() {
        const bell = document.getElementById('notifBell');
        const popover = document.getElementById('notifPopover');
        const popoverContent = document.getElementById('notifPopoverContent');
        const popoverClose = document.getElementById('notifPopoverClose');
        let popoverOpen = false;

        function renderNotifList(list, unreadCount) {
            if (!list.length) {
                popoverContent.innerHTML = `<div class='p-8 text-center text-gray-400'>
                    <i class="fas fa-bell text-3xl mb-3"></i>
                    <div class="text-sm">Tidak ada notifikasi</div>
                </div>`;
                return;
            }
            popoverContent.innerHTML = list.map(n => `
                <div class='p-4 hover:bg-gray-50 transition-colors duration-200 flex gap-3 items-start ${!n.is_read ? 'bg-teal-50' : ''}' data-id="${n.id}">
                    <div class='flex-shrink-0 w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center'>
                        <i class='${n.icon || 'fas fa-bell'} text-teal-600 text-sm'></i>
                    </div>
                    <div class='flex-1 min-w-0'>
                        <div class='font-medium text-sm text-gray-800 mb-1'>${n.title}</div>
                        <div class='text-xs text-gray-600 mb-2 leading-relaxed'>${n.message}</div>
                        <div class='flex items-center justify-between'>
                            <div class='text-[10px] text-gray-400'>${n.created_at ? new Date(n.created_at).toLocaleString('id-ID') : ''}</div>
                            ${!n.is_read ? `<button onclick="markAsRead(${n.id}, event)" class="text-teal-600 hover:text-teal-800 text-xs font-medium transition-colors" data-notification-id="${n.id}">Tandai Dibaca</button>` : ''}
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function markAsRead(notificationId, event) {
            event.preventDefault();
            event.stopPropagation();
            
            // Disable tombol agar tidak bisa diklik lagi
            const button = event.target;
            button.disabled = true;
            button.textContent = 'Memproses...';
            button.classList.add('opacity-50');
            
            fetch(`/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the notification item
                    const item = event.target.closest('[data-id]');
                    if (item) {
                        item.classList.remove('bg-teal-50');
                        item.classList.add('opacity-75');
                        // Hapus tombol "Tandai Dibaca" dengan benar
                        const button = item.querySelector('button');
                        if (button) {
                            button.remove();
                        }
                    }
                    // Update badge count
                    updateNotificationBadge();
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
                // Re-enable tombol jika error
                button.disabled = false;
                button.textContent = 'Tandai Dibaca';
                button.classList.remove('opacity-50');
            });
        }

        if (bell) {
            bell.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Bell clicked!'); // Debug
                if (popoverOpen) {
                    popover.classList.add('hidden');
                    popoverOpen = false;
                    return;
                }
                fetch('/notifications/latest')
                    .then(res => res.json())
                    .then(data => {
                        console.log('Notifications data:', data); // Debug
                        renderNotifList(data.notifications, data.unread_count);
                        popover.classList.remove('hidden');
                        popoverOpen = true;
                    })
                    .catch(error => {
                        console.error('Error fetching notifications:', error);
                    });
            });

            if (popoverClose) {
                popoverClose.addEventListener('click', function() {
                    popover.classList.add('hidden');
                    popoverOpen = false;
                });
            }

            document.addEventListener('mousedown', function(e) {
                if (popoverOpen && !popover.contains(e.target) && !bell.contains(e.target)) {
                    popover.classList.add('hidden');
                    popoverOpen = false;
                }
            });
        }
    });
</script>
</body>
</html>

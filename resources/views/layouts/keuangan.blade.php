<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CHASTE Master - Keuangan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold text-gray-900">CHASTE Master</h1>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Notification Bell -->
                    <div class="relative">
                        <button id="notificationBell" class="relative p-2 text-gray-600 hover:text-gray-900 transition-colors">
                            <i class="fas fa-bell text-xl"></i>
                            <span id="notificationBadge" class="notification-badge hidden">0</span>
                        </button>
                        
                        <!-- Notification Popover -->
                        <div id="notifPopover" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50 hidden">
                            <div class="p-4 border-b border-gray-200">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-semibold text-gray-900">Notifikasi</h3>
                                    <button id="closeNotifPopover" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="notifList" class="max-h-96 overflow-y-auto">
                                <!-- Notifications will be loaded here -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- User Menu -->
                    <div class="relative">
                        <button class="flex items-center space-x-2 text-gray-600 hover:text-gray-900">
                            <i class="fas fa-user-circle text-xl"></i>
                            <span>{{ session('user')->name ?? 'Keuangan' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 min-h-screen">
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <a href="{{ route('keuangan.dashboard') }}" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors {{ request()->routeIs('keuangan.dashboard') ? 'bg-gray-700 text-white' : '' }}">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('keuangan.view') }}" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors {{ request()->routeIs('keuangan.view') ? 'bg-gray-700 text-white' : '' }}">
                        <i class="fas fa-file-invoice mr-3"></i>
                        Kelola Invoice
                    </a>
                    <a href="{{ route('keuangan.hutang.index') }}" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors {{ request()->routeIs('keuangan.hutang.index') ? 'bg-gray-700 text-white' : '' }}">
                        <i class="fas fa-hand-holding-usd mr-3"></i>
                        Hutang Supplier
                    </a>
                    <a href="{{ route('keuangan.hutang.create') }}" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors {{ request()->routeIs('keuangan.hutang.create') ? 'bg-gray-700 text-white' : '' }}">
                        <i class="fas fa-plus-circle mr-3"></i>
                        Buat Purchase Order
                    </a>
                </div>
                
                <div class="mt-8 px-4 border-t border-gray-600 pt-4">
                    <a href="{{ route('logout') }}" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Logout
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Content Area -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
    
    <script>
        // Notification System
        let popoverOpen = false;
        const bell = document.getElementById('notificationBell');
        const popover = document.getElementById('notifPopover');
        const closeBtn = document.getElementById('closeNotifPopover');
        const notifList = document.getElementById('notifList');
        const badge = document.getElementById('notificationBadge');

        // Global function untuk update notification badge
        window.updateNotificationBadge = function() {
            console.log('Updating notification badge...'); // Debug
            fetch('/notifications/unread-count')
                .then(res => res.json())
                .then(data => {
                    console.log('Badge count response:', data); // Debug
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error updating badge:', error);
                });
        };

        // Global function untuk mark as read
        window.markAsRead = function(notificationId, event) {
            event.preventDefault();
            event.stopPropagation();
            
            console.log('=== MARK AS READ CLICKED ===');
            console.log('Notification ID:', notificationId);
            
            // Disable tombol agar tidak bisa diklik lagi
            const button = event.target;
            button.disabled = true;
            button.textContent = 'Memproses...';
            button.classList.add('opacity-50');
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            console.log('CSRF Token:', csrfToken);
            
            // Buat form data untuk POST request
            const formData = new FormData();
            formData.append('_token', csrfToken || '');
            
            fetch(`/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.success) {
                    console.log('Success! Updating UI...');
                    
                    // Update the notification item
                    const item = event.target.closest('[data-id]');
                    console.log('Notification item:', item);
                    
                    if (item) {
                        // Hapus background teal
                        item.classList.remove('bg-teal-50');
                        // Tambah opacity
                        item.classList.add('opacity-75');
                        
                        // Hapus tombol "Tandai Dibaca"
                        const buttonToRemove = item.querySelector('button');
                        if (buttonToRemove) {
                            buttonToRemove.remove();
                        }
                    }
                    
                    // Update badge count
                    updateNotificationBadge();
                    console.log('Notification marked as read successfully');
                } else {
                    console.error('Failed:', data.error);
                    // Re-enable tombol
                    button.disabled = false;
                    button.textContent = 'Tandai Dibaca';
                    button.classList.remove('opacity-50');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Re-enable tombol
                button.disabled = false;
                button.textContent = 'Tandai Dibaca';
                button.classList.remove('opacity-50');
            });
        };

        function renderNotifList(notifications) {
            if (notifications.length === 0) {
                notifList.innerHTML = '<div class="p-4 text-center text-gray-500">Tidak ada notifikasi</div>';
                return;
            }

            const html = notifications.map(n => `
                <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors ${!n.is_read ? 'bg-teal-50' : 'opacity-75'}" data-id="${n.id}">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <i class="${n.icon} text-lg ${n.priority === 'urgent' ? 'text-red-500' : n.priority === 'high' ? 'text-orange-500' : 'text-blue-500'}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">${n.title}</p>
                            <p class="text-sm text-gray-600 mt-1">${n.message}</p>
                            <p class="text-xs text-gray-400 mt-2">${new Date(n.created_at).toLocaleString('id-ID')}</p>
                            ${!n.is_read ? `<button onclick="markAsRead(${n.id}, event)" class="text-teal-600 hover:text-teal-800 text-xs font-medium transition-colors mt-2" data-notification-id="${n.id}">Tandai Dibaca</button>` : ''}
                        </div>
                    </div>
                </div>
            `).join('');

            notifList.innerHTML = html;
        }

        if (bell) {
            bell.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Bell clicked!'); // Debug
                console.log('Popover element:', popover); // Debug
                console.log('Popover open state:', popoverOpen); // Debug
                
                if (popoverOpen) {
                    popover.classList.add('hidden');
                    popoverOpen = false;
                    return;
                }
                fetch('/notifications/latest')
                    .then(res => {
                        console.log('Response status:', res.status); // Debug
                        if (!res.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return res.json();
                    })
                    .then(data => {
                        console.log('Notifications data:', data); // Debug
                        console.log('Popover element before show:', popover); // Debug
                        renderNotifList(data.notifications);
                        popover.classList.remove('hidden');
                        popoverOpen = true;
                        console.log('Popover should be visible now'); // Debug
                    })
                    .catch(error => {
                        console.error('Error fetching notifications:', error);
                        notifList.innerHTML = '<div class="p-4 text-center text-red-500">Error loading notifications</div>';
                        popover.classList.remove('hidden');
                        popoverOpen = true;
                    });
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                popover.classList.add('hidden');
                popoverOpen = false;
            });
        }

        // Close popover when clicking outside
        document.addEventListener('click', function(e) {
            if (!bell.contains(e.target) && !popover.contains(e.target)) {
                popover.classList.add('hidden');
                popoverOpen = false;
            }
        });

        // Auto-update notification badge every 30 seconds
        updateNotificationBadge();
        setInterval(updateNotificationBadge, 30000);
    </script>
</body>
</html> 
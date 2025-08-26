<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Header -->
<header class="flex items-center justify-between py-5 border-gray-200 px-[100px]">
    <a href="{{ url('/') }}" class="text-2xl font-bold tracking-wide text-black">CHASTE</a>

    <!-- Menu Utama -->
    <nav class="hidden md:flex space-x-8 text-sm font-medium">
        <a href="{{ url('/') }}"
            class="{{ request()->is('/') ? 'text-black font-semibold underline' : 'text-gray-600 hover:text-teal-500' }}">
            Beranda
        </a>
        <a href="{{ route('produk') }}"
            class="transition duration-200 {{ request()->is('produk*') ? 'text-black font-semibold underline' : 'text-gray-600 hover:text-teal-500' }}">
            Produk
        </a>
    </nav>

    <!-- Icon Navigasi -->
    <div class="space-x-4 text-xl text-gray-700 flex items-center gap-4">
        <a href="{{ route('keranjang') }}"
            class="{{ request()->is('keranjang*') ? 'text-teal-600' : 'hover:text-teal-500' }}">
            <!-- Kantong Belanja SVG -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16.5 6.75V5.25A4.5 4.5 0 0012 0.75a4.5 4.5 0 00-4.5 4.5v1.5M4.5 6.75h15l-.964 12.858a2.25 2.25 0 01-2.246 2.117H7.71a2.25 2.25 0 01-2.246-2.117L4.5 6.75z" />
            </svg>
        </a>

        <a href="{{ route('profile') }}"
            class="flex justify-center items-center gap-2 {{ request()->routeIs('profile') ? 'text-teal-600' : 'hover:text-teal-500' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
            @if (session('user'))
                <span class="text-sm">{{ session('user')['name'] }}</span>
            @endif
        </a>

        @if (session('user'))
<a href="#" id="notifBell" class="relative hover:text-teal-500">
    <i class="fas fa-bell text-xl"></i>
    <div id="customerNotificationBadge" class="notification-badge bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center absolute -top-2 -right-2 shadow-lg animate-pulse" style="display: none;">
        <span class="count font-bold">0</span>
    </div>
    <!-- Popover -->
    <div id="notifPopover" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 z-50" style="top:2.5rem;">
        <div class="p-4 border-b border-gray-100 font-semibold text-gray-800 flex justify-between items-center">
            <span class="text-teal-600">Notifikasi</span>
            <div class="flex items-center gap-2">
                <button id="markAllAsRead" class="text-xs text-teal-600 hover:text-teal-800 font-medium transition-colors">Tandai Semua Dibaca</button>
                <button id="notifPopoverClose" class="text-gray-400 hover:text-red-500 text-lg transition-colors">&times;</button>
            </div>
        </div>
        <div id="notifPopoverContent" class="max-h-[350px] overflow-y-auto divide-y divide-gray-50"></div>
    </div>
</a>
@endif

        <!-- <span>|</span> -->

        <!-- <a href="{{ route('pesanan') }}"
            class="{{ request()->routeIs('pesanan') ? 'text-teal-600' : 'hover:text-teal-500' }}">
            Pesanan Icon -->
            <!-- <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M12 17.25h8.25" />
            </svg>
        </a> -->
        
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update notification badge on page load
    updateCustomerNotificationBadge();
    
    // Update notification badge every 30 seconds
    setInterval(updateCustomerNotificationBadge, 30000);

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
        
        console.log('Mark as read clicked for notification:', notificationId); // Debug
        
        // Disable tombol agar tidak bisa diklik lagi
        const button = event.target;
        button.disabled = true;
        button.textContent = 'Memproses...';
        button.classList.add('opacity-50');
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        console.log('CSRF Token:', csrfToken); // Debug
        
        fetch(`/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            console.log('Response status:', response.status); // Debug
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data); // Debug
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
                
                // Update badge count dari server
                updateCustomerNotificationBadge();
            } else {
                console.error('Mark as read failed:', data); // Debug
                // Re-enable tombol jika error
                button.disabled = false;
                button.textContent = 'Tandai Dibaca';
                button.classList.remove('opacity-50');
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

        // Handle mark all as read
        const markAllAsReadBtn = document.getElementById('markAllAsRead');
        if (markAllAsReadBtn) {
            markAllAsReadBtn.addEventListener('click', function() {
                // Disable tombol
                markAllAsReadBtn.disabled = true;
                markAllAsReadBtn.textContent = 'Memproses...';
                markAllAsReadBtn.classList.add('opacity-50');
                
                fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update semua item notifikasi
                        const items = popoverContent.querySelectorAll('[data-id]');
                        items.forEach(item => {
                            item.classList.remove('bg-teal-50');
                            item.classList.add('opacity-75');
                            const button = item.querySelector('button');
                            if (button) {
                                button.remove();
                            }
                        });
                        
                        // Sembunyikan badge
                        const badge = document.getElementById('customerNotificationBadge');
                        if (badge) {
                            badge.style.display = 'none';
                            badge.classList.remove('animate-pulse');
                        }
                        
                        // Update counter
                        updateCustomerNotificationBadge();
                    }
                })
                .catch(error => {
                    console.error('Error marking all notifications as read:', error);
                    // Re-enable tombol jika error
                    markAllAsReadBtn.disabled = false;
                    markAllAsReadBtn.textContent = 'Tandai Semua Dibaca';
                    markAllAsReadBtn.classList.remove('opacity-50');
                });
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

function updateCustomerNotificationBadge() {
    fetch('/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('customerNotificationBadge');
            if (badge) {
                const countSpan = badge.querySelector('.count');
                console.log('Updating badge count to:', data.count); // Debug
                if (data.count > 0) {
                    countSpan.textContent = data.count;
                    badge.style.display = 'flex';
                    // Tambahkan animasi pulse jika ada notifikasi baru
                    badge.classList.add('animate-pulse');
                } else {
                    countSpan.textContent = '0';
                    badge.style.display = 'none';
                    badge.classList.remove('animate-pulse');
                }
            }
        })
        .catch(error => {
            console.error('Error updating customer notification badge:', error);
        });
}
</script>

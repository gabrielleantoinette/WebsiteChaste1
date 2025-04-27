<!-- Header -->
<header class="flex items-center justify-between py-5 border-gray-200 px-[100px]">
    <a href="{{ url('/') }}" class="text-2xl font-bold tracking-wide">CHASTE</a>

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

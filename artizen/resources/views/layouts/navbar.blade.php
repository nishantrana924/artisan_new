<!-- Main Floating Header Wrapper -->
    <div class="sticky top-0 z-50 w-full bg-transparent px-4 py-3 transition-all duration-300 header-wrapper">
        <header
            class="w-full bg-white border border-[#EAEAEA] shadow-md rounded-2xl overflow-hidden transition-all duration-300 header-premium flex flex-col">

            <!-- Row 1: Top Announcement Bar -->
            <div
                class="announcement-bar w-full bg-white text-black h-9 border-b border-[#EAEAEA] flex items-center overflow-hidden select-none relative font-heading font-bold text-[10px] md:text-xs uppercase tracking-wider transition-all duration-300">
                <div class="animate-marquee whitespace-nowrap flex items-center py-1">
                    <span>
                        <span class="text-[#EA741D]">PLAN, BOOK & RELAX</span> |
                        Instant Event
                        Booking is Now Live • Book Birthdays, Acoustic Nights, Proposals & House Parties in 2 Minutes •
                        <span class="text-[#EA741D] underline">10% FLAT OFF</span> on Your First Booking | Use Code:
                        <span class="text-[#EA741D]">ARTIZEN10</span> | Choose Your Package and Relax •
                        <span class="text-[#EA741D]">PLAN, BOOK & RELAX</span> |
                        Instant Event
                        Booking is Now Live • Book Birthdays, Acoustic Nights, Proposals & House Parties in 2 Minutes •
                        <span class="text-[#EA741D] underline">10% FLAT OFF</span> on Your First Booking | Use Code:
                        <span class="text-[#EA741D]">ARTIZEN10</span> | Choose Your Package and Relax •
                    </span>
                </div>
            </div>

            <!-- Row 2: Main Navigation Bar -->
            <div
                class="navbar-main-row w-full px-4 md:px-6 lg:px-8 h-20 flex items-center justify-between transition-all duration-300">

                <!-- Logo & Brand (Left) -->
                <a href="{{ route('home') }}" class="flex items-center group relative shrink-0 text-left">
                    <img src="{{ asset('assets/images/logo/artizen.png') }}" alt="Artizen Logo" class="h-16 w-auto object-contain transition-transform duration-300 group-hover:scale-105 rounded-lg shadow-sm">
                </a>

                <!-- Center Navigation Links (Premium semi-bold/medium text weight) -->
                <nav
                    class="hidden md:flex items-center gap-6 lg:gap-8 font-heading text-[14px] lg:text-[15px] font-medium tracking-tight relative">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-[#EA741D] active-nav font-semibold' : 'text-black hover:text-[#EA741D]' }} transition-colors py-2 outline-none focus:outline-none">Home</a>

                    <!-- Packages Dropdown Trigger -->
                    <div class="relative group py-2">
                        <a href="{{ route('events.index') }}"
                            class="{{ request()->routeIs('events.*') ? 'text-[#EA741D] active-nav font-semibold' : 'text-black hover:text-[#EA741D]' }} transition-colors outline-none focus:outline-none">
                            Packages
                        </a>

                        <!-- Mega Menu Dropdown -->
                        <div
                            class="absolute left-1/2 -translate-x-1/2 top-full mt-2 w-[540px] bg-white border border-[#EAEAEA] shadow-2xl rounded-2xl p-5 grid grid-cols-2 gap-3 z-50 opacity-0 scale-95 pointer-events-none group-hover:opacity-100 group-hover:scale-100 group-hover:pointer-events-auto transition-all duration-300 ease-out mega-menu-dropdown">
                            <!-- Birthday -->
                            <a onclick="openEventLightbox(1)"
                                class="flex gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition-colors cursor-pointer text-left">
                                <div
                                    class="h-8 w-8 rounded-lg bg-[#EA741D]/10 flex items-center justify-center text-[#EA741D] text-sm shrink-0">
                                    <i class="fa-solid fa-cake-candles text-base" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h5 class="text-xs font-bold text-black dark:text-white uppercase">Birthday Party</h5>
                                    <p class="text-[9px] text-gray-500 font-normal mt-0.5">Themes, setups, balloon decor</p>
                                </div>
                            </a>
                            <!-- House Party -->
                            <a onclick="openEventLightbox(2)"
                                class="flex gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition-colors cursor-pointer text-left">
                                <div
                                    class="h-8 w-8 rounded-lg bg-[#EA741D]/10 flex items-center justify-center text-[#EA741D] text-sm shrink-0">
                                    <i class="fa-solid fa-champagne-glasses text-base" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h5 class="text-xs font-bold text-black dark:text-white uppercase">House Party</h5>
                                    <p class="text-[9px] text-gray-500 font-normal mt-0.5">Sound column setups, DJs & lighting</p>
                                </div>
                            </a>
                            <!-- Proposal -->
                            <a onclick="openEventLightbox(3)"
                                class="flex gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition-colors cursor-pointer text-left">
                                <div
                                    class="h-8 w-8 rounded-lg bg-[#EA741D]/10 flex items-center justify-center text-[#EA741D] text-sm shrink-0">
                                    <i class="fa-regular fa-heart text-base" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h5 class="text-xs font-bold text-black dark:text-white uppercase">Proposal Setups</h5>
                                    <p class="text-[9px] text-gray-500 font-normal mt-0.5">Romantic lights, rose paths & violin</p>
                                </div>
                            </a>
                            <!-- Anniversary -->
                            <a onclick="openEventLightbox(4)"
                                class="flex gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition-colors cursor-pointer text-left">
                                <div
                                    class="h-8 w-8 rounded-lg bg-[#EA741D]/10 flex items-center justify-center text-[#EA741D] text-sm shrink-0">
                                    <i class="fa-regular fa-heart text-base" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h5 class="text-xs font-bold text-black dark:text-white uppercase">Anniversary</h5>
                                    <p class="text-[9px] text-gray-500 font-normal mt-0.5">Premium curated romantic decors</p>
                                </div>
                            </a>
                            <!-- Wedding/Sangeet -->
                            <a onclick="openEventLightbox(5)"
                                class="flex gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition-colors cursor-pointer text-left">
                                <div
                                    class="h-8 w-8 rounded-lg bg-[#EA741D]/10 flex items-center justify-center text-[#EA741D] text-sm shrink-0">
                                    <i class="fa-solid fa-ring text-base" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h5 class="text-xs font-bold text-black dark:text-white uppercase">Wedding & Sangeet</h5>
                                    <p class="text-[9px] text-gray-500 font-normal mt-0.5">Traditional design & stage setups</p>
                                </div>
                            </a>
                            <!-- DJ Night -->
                            <a onclick="openEventLightbox(6)"
                                class="flex gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition-colors cursor-pointer text-left">
                                <div
                                    class="h-8 w-8 rounded-lg bg-[#EA741D]/10 flex items-center justify-center text-[#EA741D] text-sm shrink-0">
                                    <i class="fa-solid fa-music text-base" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h5 class="text-xs font-bold text-black dark:text-white uppercase">DJ Night</h5>
                                    <p class="text-[9px] text-gray-500 font-normal mt-0.5">Professional DJs with truss rigs</p>
                                </div>
                            </a>
                            <!-- Acoustic Night -->
                            <a onclick="openEventLightbox(7)"
                                class="flex gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition-colors cursor-pointer text-left">
                                <div
                                    class="h-8 w-8 rounded-lg bg-[#EA741D]/10 flex items-center justify-center text-[#EA741D] text-sm shrink-0">
                                    <i class="fa-solid fa-guitar text-base" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h5 class="text-xs font-bold text-black dark:text-white uppercase">Acoustic Night</h5>
                                    <p class="text-[9px] text-gray-500 font-normal mt-0.5">Live guitarist and acoustic setup</p>
                                </div>
                            </a>
                            <!-- Corporate -->
                            <a onclick="openEventLightbox(8)"
                                class="flex gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition-colors cursor-pointer text-left">
                                <div
                                    class="h-8 w-8 rounded-lg bg-[#EA741D]/10 flex items-center justify-center text-[#EA741D] text-sm shrink-0">
                                    <i class="fa-solid fa-briefcase text-base" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h5 class="text-xs font-bold text-black dark:text-white uppercase">Corporate Events</h5>
                                    <p class="text-[9px] text-gray-500 font-normal mt-0.5">Sound, screens & stage decor</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('gallery.index') }}" class="{{ request()->routeIs('gallery.*') ? 'text-[#EA741D] active-nav font-semibold' : 'text-black hover:text-[#EA741D]' }} transition-colors py-2 outline-none focus:outline-none">Gallery</a>
                    <a href="{{ route('about-us') }}" class="{{ request()->routeIs('about-us') ? 'text-[#EA741D] active-nav font-semibold' : 'text-black hover:text-[#EA741D]' }} transition-colors py-2 outline-none focus:outline-none">About</a>
                    <a href="{{ route('contact.index') }}" class="{{ request()->routeIs('contact.*') ? 'text-[#EA741D] active-nav font-semibold' : 'text-black hover:text-[#EA741D]' }} transition-colors py-2 outline-none focus:outline-none">Inquire</a>
                    <a href="{{ route('booking.track') }}" class="{{ request()->routeIs('booking.track*') ? 'text-[#EA741D] active-nav font-semibold' : 'text-black hover:text-[#EA741D]' }} transition-colors py-2 outline-none focus:outline-none">Track Booking</a>
                </nav>

                <!-- Right Side Controls & CTA Buttons -->
                <div class="flex items-center gap-3">
                    <!-- Search box (capsule outline) -->
                    <div class="relative hidden xl:block">
                        <span
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-xs text-gray-400" aria-hidden="true"></i>
                        </span>
                        <input type="text" id="search-input" placeholder="Search events, artists..."
                            class="w-44 lg:w-56 bg-white border border-[#EAEAEA] pl-9 pr-4 py-2 text-xs rounded-xl font-body text-black placeholder-gray-400 focus:outline-none focus:border-[#EA741D] focus:ring-1 focus:ring-[#EA741D] transition-all">
                    </div>

                    <!-- WhatsApp Support Button -->
                    <a href="https://wa.me/919131668156" target="_blank" rel="noopener noreferrer"
                        class="flex items-center gap-1.5 border border-[#25D366] bg-white dark:bg-transparent text-[#25D366] hover:bg-[#25D366]/5 dark:hover:bg-[#25D366]/10 p-2.5 md:px-3.5 md:py-2 text-sm font-heading font-semibold rounded-xl transition-all duration-200">
                        <i class="fa-brands fa-whatsapp text-base"></i>
                        <span class="hidden md:inline">WhatsApp</span>
                    </a>

                    <!-- Book Now Button (Solid Orange with Calendar Icon) -->
                    <a href="{{ route('booking.index') }}"
                        class="hidden md:inline-flex items-center gap-1.5 bg-[#EA741D] hover:bg-[#D6630F] text-white px-4 py-2 text-sm font-heading font-semibold rounded-xl shadow-[0_2px_10px_rgba(234,116,29,0.2)] transition-all duration-200">
                        <i class="fa-regular fa-calendar text-xs" aria-hidden="true"></i>
                        Book Now
                    </a>

                    <!-- Divider -->
                    <span class="h-5 w-[1px] bg-gray-200 hidden md:block"></span>

                    <!-- Profile Icon (Links to admin dashboard) -->
                    <a href="{{ route('admin.dashboard') }}"
                        class="hidden md:flex p-2 text-gray-700 hover:text-[#EA741D] transition-colors items-center justify-center"
                        aria-label="Admin Dashboard">
                        <i class="fa-regular fa-user text-base" aria-hidden="true"></i>
                    </a>

                    <!-- Theme Toggle Button -->
                    <button onclick="toggleTheme()"
                        class="p-2 text-gray-700 hover:text-[#EA741D] transition-colors flex items-center justify-center"
                        aria-label="Toggle Theme">
                        <!-- Sun Icon (visible in dark mode) -->
                        <i id="theme-toggle-sun" class="fa-regular fa-sun text-base hidden" aria-hidden="true"></i>
                        <!-- Moon Icon (visible in light mode) -->
                        <i id="theme-toggle-moon" class="fa-regular fa-moon text-base" aria-hidden="true"></i>
                    </button>

                    <!-- Wishlist Icon -->
                    <button
                        class="hidden md:flex p-2 text-gray-700 hover:text-[#EA741D] transition-colors items-center justify-center"
                        aria-label="Wishlist">
                        <i class="fa-regular fa-heart text-base" aria-hidden="true"></i>
                    </button>

                    <!-- Cart Drawer Trigger Button -->
                    <button id="cart-open-btn" onclick="toggleCartDrawer()"
                        class="hidden md:flex relative p-2 text-gray-700 hover:text-[#EA741D] transition-colors items-center justify-center"
                        aria-label="Open Cart">
                        <i class="fa-solid fa-bag-shopping text-base" aria-hidden="true"></i>
                        <span id="cart-badge" aria-live="polite"
                            class="absolute -top-0.5 -right-0.5 bg-[#EA741D] text-white font-heading font-bold text-[8px] w-4 h-4 flex items-center justify-center rounded-full">0</span>
                    </button>

                    <!-- Mobile Hamburger Toggle Menu -->
                    <button onclick="toggleMobileNav()"
                        class="md:hidden p-2 text-gray-700 hover:text-[#EA741D] transition-colors flex items-center justify-center"
                        aria-label="Menu">
                        <i class="fa-solid fa-bars text-lg" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </header>
    </div>

    <!-- Mobile Drawer Slide-in Navigation Menu -->
    <div id="mobile-nav"
        class="fixed inset-y-0 right-0 z-[100] w-full max-w-[320px] bg-white dark:bg-[#121214] p-6 flex flex-col justify-between transform translate-x-full transition-transform duration-300 md:hidden border-l border-[#EAEAEA] dark:border-white/10 shadow-2xl">
        <div>
            <div class="flex items-center justify-between pb-6 border-b border-[#EAEAEA] dark:border-white/10">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('assets/images/logo/artizen.png') }}" alt="Artizen Logo" class="h-14 w-auto object-contain rounded-md">
                </a>
                <button onclick="toggleMobileNav()"
                    class="p-2 border border-[#EAEAEA] dark:border-white/10 rounded-full hover:border-[#EA741D] flex items-center justify-center" aria-label="Close menu">
                    <i class="fa-solid fa-xmark text-sm text-black dark:text-white" aria-hidden="true"></i>
                </button>
            </div>

            <!-- Mobile Search -->
            <div class="relative mt-6">
                <input type="text" id="search-input-mobile" placeholder="Search packages..."
                    class="w-full bg-gray-50 dark:bg-white/5 border border-[#EAEAEA] dark:border-white/10 px-4 py-2.5 text-xs rounded-xl font-body text-black dark:text-white outline-none focus:border-[#EA741D] focus:bg-white dark:focus:bg-white/10 transition-all">
                <span class="absolute right-4 top-3 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs" aria-hidden="true"></i>
                </span>
            </div>

            <!-- Quick Actions Section inside drawer -->
            <div
                class="flex items-center justify-around py-4 mt-6 border-t border-b border-[#EAEAEA] dark:border-white/10">
                <!-- Profile -->
                <a href="{{ route('admin.dashboard') }}" onclick="toggleMobileNav()"
                    class="flex flex-col items-center gap-1.5 text-gray-700 dark:text-gray-300 hover:text-[#EA741D] transition-colors"
                    aria-label="Admin Dashboard">
                    <i class="fa-regular fa-user text-base" aria-hidden="true"></i>
                    <span class="text-[9px] font-heading font-semibold uppercase tracking-wider">Account</span>
                </a>

                <!-- Wishlist -->
                <button onclick="toggleMobileNav();"
                    class="flex flex-col items-center gap-1.5 text-gray-700 dark:text-gray-300 hover:text-[#EA741D] transition-colors"
                    aria-label="Wishlist">
                    <i class="fa-regular fa-heart text-base" aria-hidden="true"></i>
                    <span class="text-[9px] font-heading font-semibold uppercase tracking-wider">Wishlist</span>
                </button>

                <!-- Cart -->
                <button id="cart-open-btn-mobile" onclick="toggleMobileNav(); toggleCartDrawer();"
                    class="relative flex flex-col items-center gap-1.5 text-gray-700 dark:text-gray-300 hover:text-[#EA741D] transition-colors"
                    aria-label="Open Cart">
                    <div class="relative">
                        <i class="fa-solid fa-bag-shopping text-base" aria-hidden="true"></i>
                        <span id="cart-badge-mobile" aria-live="polite"
                            class="absolute -top-1.5 -right-2 bg-[#EA741D] text-white font-heading font-bold text-[8px] w-4 h-4 flex items-center justify-center rounded-full">0</span>
                    </div>
                    <span class="text-[9px] font-heading font-semibold uppercase tracking-wider">Cart</span>
                </button>
            </div>

            <!-- Mobile Links -->
            <nav class="flex flex-col gap-2 mt-6 font-heading font-medium text-[15px] tracking-tight text-left">
                <a href="{{ route('home') }}" onclick="toggleMobileNav()"
                    class="py-2.5 border-b border-gray-100 dark:border-white/5 {{ request()->routeIs('home') ? 'text-[#EA741D] font-semibold' : 'text-black dark:text-white hover:text-[#EA741D]' }} text-left">Home</a>
                <a href="{{ route('events.index') }}" onclick="toggleMobileNav()"
                    class="py-2.5 border-b border-gray-100 dark:border-white/5 {{ request()->routeIs('events.*') ? 'text-[#EA741D] font-semibold' : 'text-black dark:text-white hover:text-[#EA741D]' }} text-left">Packages</a>
                <a href="{{ route('gallery.index') }}" onclick="toggleMobileNav()"
                    class="py-2.5 border-b border-gray-100 dark:border-white/5 {{ request()->routeIs('gallery.*') ? 'text-[#EA741D] font-semibold' : 'text-black dark:text-white hover:text-[#EA741D]' }} text-left">Gallery</a>
                <a href="{{ route('about-us') }}" onclick="toggleMobileNav()"
                    class="py-2.5 border-b border-gray-100 dark:border-white/5 {{ request()->routeIs('about-us') ? 'text-[#EA741D] font-semibold' : 'text-black dark:text-white hover:text-[#EA741D]' }} text-left">About</a>
                <a href="{{ route('contact.index') }}" onclick="toggleMobileNav()"
                    class="py-2.5 border-b border-gray-100 dark:border-white/5 {{ request()->routeIs('contact.*') ? 'text-[#EA741D] font-semibold' : 'text-black dark:text-white hover:text-[#EA741D]' }} text-left">Inquire</a>
                <a href="{{ route('booking.track') }}" onclick="toggleMobileNav()"
                    class="py-2.5 border-b border-gray-100 dark:border-white/5 {{ request()->routeIs('booking.track*') ? 'text-[#EA741D] font-semibold' : 'text-black dark:text-white hover:text-[#EA741D]' }} text-left">Track Booking</a>
                <a href="{{ route('admin.dashboard') }}" onclick="toggleMobileNav()"
                    class="py-2.5 text-black dark:text-white hover:text-[#EA741D] text-left">Admin Panel</a>
            </nav>
        </div>

        <!-- Mobile CTA Buttons -->
        <div class="flex flex-col gap-3 mt-auto pt-4">
            <a href="https://wa.me/919131668156" target="_blank"
                class="w-full flex items-center justify-center gap-2 border border-[#25D366] text-[#25D366] py-3 rounded-xl font-heading font-semibold text-sm hover:bg-[#25D366]/5 transition-all">
                <i class="fa-brands fa-whatsapp text-base"></i>
                WhatsApp
            </a>
            <a href="{{ route('events.index') }}" onclick="toggleMobileNav()"
                class="w-full flex items-center justify-center bg-[#EA741D] hover:bg-[#D6630F] text-white py-3 rounded-xl font-heading font-semibold text-sm shadow-md text-center transition-all">
                Book Event Setup
            </a>
        </div>
    </div>

    <!-- Mobile Drawer Overlay Backdrop -->
    <div id="mobile-nav-overlay" onclick="toggleMobileNav()"
        class="fixed inset-0 bg-black/80 backdrop-blur-md z-[90] hidden transition-opacity duration-300 opacity-0 pointer-events-none md:hidden">
    </div>

    <!-- Hero Section with Full-Width Background Autoplay Video -->
    
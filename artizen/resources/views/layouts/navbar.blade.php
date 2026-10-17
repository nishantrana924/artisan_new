@php
    $categoriesList = \App\Services\JsonStorageService::read('categories.json');
    $celebrationPackages = \App\Services\CelebrationCatalogService::getAllPackages();
    $searchItems = [];
    foreach ($celebrationPackages as $pkg) {
        $searchItems[] = [
            'id' => $pkg['id'],
            'title' => $pkg['title'],
            'subcategory' => $pkg['subcategory'],
            'category' => $pkg['category_name'],
            'category_id' => $pkg['category_id'],
            'price' => '₹' . number_format($pkg['price']),
            'image' => $pkg['image'],
            'url' => route('events.show', ['slug' => $pkg['slug'] ?? \Illuminate\Support\Str::slug($pkg['title'])]),
            'tags' => strtolower(implode(' ', array_merge([$pkg['title'], $pkg['subcategory'], $pkg['category_name'], $pkg['desc']], $pkg['tags'] ?? []))),
        ];
    }
@endphp

<!-- Full-Width Sticky Header (Compact & Clean Design System matching reference) -->
<header class="sticky top-0 z-50 w-full bg-white dark:bg-[#171719] border-b border-[#E6E2D8] dark:border-[#292929] shadow-xs dark:shadow-md header-premium select-none text-[#171719] dark:text-white">

    <!-- Tier 1: Top Utility & Trust Strip (Compact, Elegant, Single Row) -->
    <div id="header-top-bar" class="w-full bg-[#FAF9F6] dark:bg-[#0C0C0E] border-b border-[#E6E2D8] dark:border-[#232326] py-1 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between text-[11px] font-heading font-medium text-gray-700 dark:text-gray-300">
            
            <!-- Left: Location & Social Proof with Infinite Loop Rotator -->
            <div class="flex items-center gap-2.5 sm:gap-3 text-[11px]">
                <div class="inline-flex items-center gap-1.5 cursor-pointer hover:text-black dark:hover:text-white transition-colors">
                    <i class="fa-solid fa-location-dot text-gray-600 dark:text-gray-400 text-xs shrink-0"></i>
                    <div class="relative h-[16px] overflow-hidden min-w-[75px] text-left">
                        <div id="header-location-rotator" class="flex flex-col transition-transform duration-500 ease-in-out">
                            <span class="h-[16px] leading-[16px] whitespace-nowrap font-semibold text-gray-800 dark:text-gray-200">Indore, MP</span>
                            <span class="h-[16px] leading-[16px] whitespace-nowrap font-semibold text-gray-800 dark:text-gray-200">Vijay Nagar</span>
                            <span class="h-[16px] leading-[16px] whitespace-nowrap font-semibold text-gray-800 dark:text-gray-200">Palasia</span>
                            <span class="h-[16px] leading-[16px] whitespace-nowrap font-semibold text-gray-800 dark:text-gray-200">Bhawarkua</span>
                            <span class="h-[16px] leading-[16px] whitespace-nowrap font-semibold text-gray-800 dark:text-gray-200">Saket Nagar</span>
                            <span class="h-[16px] leading-[16px] whitespace-nowrap font-semibold text-gray-800 dark:text-gray-200">Super Corridor</span>
                            <span class="h-[16px] leading-[16px] whitespace-nowrap font-semibold text-gray-800 dark:text-gray-200">Rau & Bypass</span>
                        </div>
                    </div>
                </div>
                <span class="hidden sm:inline text-gray-300 dark:text-white/20">|</span>
                <div class="hidden sm:inline-flex items-center gap-1.5 text-gray-600 dark:text-gray-300">
                    <i id="header-feature-icon" class="fa-solid fa-bolt text-gray-600 dark:text-gray-400 text-[10px] shrink-0 transition-all duration-300"></i>
                    <div class="relative h-[16px] overflow-hidden min-w-[190px] text-left">
                        <div id="header-feature-rotator" class="flex flex-col transition-transform duration-500 ease-in-out">
                            <span class="h-[16px] leading-[16px] whitespace-nowrap font-medium text-gray-700 dark:text-gray-300">500+ Celebrations in Indore</span>
                            <span class="h-[16px] leading-[16px] whitespace-nowrap font-medium text-gray-700 dark:text-gray-300">100% On-Time Setup Guarantee</span>
                            <span class="h-[16px] leading-[16px] whitespace-nowrap font-medium text-gray-700 dark:text-gray-300">Zero Upfront Advance Required</span>
                            <span class="h-[16px] leading-[16px] whitespace-nowrap font-medium text-gray-700 dark:text-gray-300">Instant Booking • Offline Payment</span>
                            <span class="h-[16px] leading-[16px] whitespace-nowrap font-medium text-gray-700 dark:text-gray-300">Custom Theme & DJ Solutions</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Center: Launch Benefit -->
            <a href="{{ route('events.index') }}" class="hidden md:inline-flex items-center gap-2 group cursor-pointer text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white transition-colors">
                <span class="bg-[#FFD600] text-[#171719] text-[9.5px] font-heading font-extrabold uppercase px-2 py-0.5 rounded-md tracking-wider shadow-2xs flex items-center gap-1 group-hover:brightness-95 transition-all">
                    <i class="fa-solid fa-calendar-check text-[9px]"></i>
                    <span>LAUNCH BENEFIT</span>
                </span>
                <span class="text-gray-300 dark:text-white/20">|</span>
                <span class="font-medium text-[11px] text-gray-700 dark:text-gray-300 group-hover:text-black dark:group-hover:text-white transition-colors">
                    Flat 10% Off on All Event Packages in Indore
                </span>
                <i class="fa-solid fa-arrow-right text-[9px] text-[#FFD600] group-hover:translate-x-0.5 transition-transform"></i>
            </a>

            <!-- Right: Quick Utility Links -->
            <div class="flex items-center gap-2.5 sm:gap-3.5 text-[11px] font-medium text-gray-600 dark:text-gray-300">
                <a href="{{ route('booking.track') }}" class="group hover:text-[#171719] dark:hover:text-white transition-colors flex items-center gap-1.5" title="Track Booking" aria-label="Track Booking">
                    <i class="fa-solid fa-truck-fast text-gray-500 dark:text-gray-400 group-hover:text-[#171719] dark:group-hover:text-white transition-colors text-xs"></i>
                    <span class="hidden sm:inline">Track Booking</span>
                </a>
                <span class="text-gray-300 dark:text-white/20">|</span>
                <a href="{{ route('contact.index') }}" class="group hover:text-[#171719] dark:hover:text-white transition-colors flex items-center gap-1.5" title="Help & Inquire" aria-label="Help & Inquire">
                    <i class="fa-solid fa-headset text-gray-500 dark:text-gray-400 group-hover:text-[#171719] dark:group-hover:text-white transition-colors text-xs"></i>
                    <span class="hidden sm:inline">Help & Inquire</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Tier 2: Main Primary Header (Compact: Logo + Expanded Omnisearch + 4 Circular Icons + Book Setup CTA) -->
    <div class="w-full bg-white dark:bg-[#171719] py-2 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-3 sm:gap-5 lg:gap-7">
            
            <!-- Brand Logo (Left - Clean, Crisp & Vertically Centered - No Hover Scaling) -->
            <a href="{{ route('home') }}" class="flex items-center shrink-0 text-left">
                <picture>
                    <source srcset="{{ asset('assets/images/logo/artizen.webp') }}" type="image/webp">
                    <img src="{{ asset('assets/images/logo/artizen.png') }}" alt="Artizen Logo" class="h-10 sm:h-11 md:h-12 w-auto object-contain block" width="447" height="500">
                </picture>
            </a>

            <!-- Omnisearch Bar with Autocomplete Suggestions & Clear Icon (Expanded & Centered) -->
            <div class="relative hidden md:block flex-1 max-w-xl lg:max-w-2xl mx-auto md:mx-3 lg:mx-6" id="header-search-container">
                <form action="{{ route('events.index') }}" method="GET" class="relative w-full" id="header-search-form">
                    <div class="relative flex items-center">
                        <span class="z-20 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none transition-colors">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" 
                               id="header-search-input"
                               name="q" 
                               value="{{ request('q', '') }}"
                               autocomplete="off"
                               placeholder="Search setups, decor, DJ, weddings, birthdays..." 
                               class="w-full pl-10 pr-10 py-2 bg-[#FAF9F6] dark:bg-[#202024] border border-[#E6E2D8] dark:border-[#2E2E33] rounded-full text-xs font-medium text-[#171719] dark:text-white placeholder-gray-400 focus:outline-none focus:border-[#171719] dark:focus:border-white/40 focus:bg-white dark:focus:bg-[#242428] shadow-none ring-0 focus:ring-0">
                        
                        <!-- Clear 'X' Button -->
                        <button type="button" 
                                id="header-search-clear" 
                                onclick="clearHeaderSearch()" 
                                class="z-20 absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#171719] dark:hover:text-white transition-colors cursor-pointer {{ request('q') ? '' : 'hidden' }} p-0.5 rounded-full hover:bg-black/5 dark:hover:bg-white/10"
                                aria-label="Clear search">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                </form>

                <!-- Live Search Suggestions Dropdown -->
                <div id="header-search-suggestions" 
                     class="absolute left-0 top-full mt-2 w-full bg-white dark:bg-[#171719] border border-[#E6E2D8] dark:border-[#2E2E33] shadow-2xl rounded-2xl p-2.5 z-50 hidden max-h-[380px] overflow-y-auto overscroll-contain transition-all text-left">
                    <div id="header-suggestions-content"></div>
                </div>
            </div>

            <!-- Right Controls: Theme Toggle + Wishlist + Booking Cart + Login (Icon-only) + Book Setup CTA + Mobile Menu -->
            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">

                <!-- Theme Switcher Button (Single Icon: Moon in Light, Sun in Dark) - Commented out -->
                {{-- 
                <button type="button"
                        id="theme-toggle-btn"
                        onclick="toggleTheme()" 
                        class="w-9 h-9 sm:w-9.5 sm:h-9.5 rounded-full border border-[#E6E2D8] dark:border-white/10 hover:border-gray-400 dark:hover:border-white/30 bg-[#FAF9F6] dark:bg-[#1E1E24] hover:bg-black/5 dark:hover:bg-white/10 transition-all flex items-center justify-center cursor-pointer shadow-2xs group active:scale-95"
                        title="Switch Dark / Light Theme"
                        aria-label="Toggle Dark/Light Mode">
                    <i id="theme-toggle-icon" class="fa-solid fa-moon text-xs text-gray-700 transition-all duration-300 group-hover:scale-110"></i>
                </button>
                --}}

                <!-- 2. Mobile Search Trigger Button (Icon-Only on Mobile < md) -->
                <button type="button" 
                        id="mobile-search-trigger-btn"
                        onclick="toggleMobileSearchModal()"
                        class="md:hidden w-9 h-9 sm:w-9.5 sm:h-9.5 rounded-full border border-[#E6E2D8] dark:border-[#2E2E33] hover:border-gray-400 dark:hover:border-gray-500 bg-[#FAF9F6] dark:bg-[#202024] hover:bg-black/5 dark:hover:bg-white/10 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white transition-all flex items-center justify-center cursor-pointer shadow-2xs group"
                        title="Search Celebrations"
                        aria-label="Open Search">
                    <i class="fa-solid fa-magnifying-glass text-xs text-gray-700 dark:text-gray-300 group-hover:scale-110 transition-transform"></i>
                </button>

                <!-- 3. Order History & Booking Details Icon -->
                <a href="{{ route('orders.index') }}" 
                   class="w-9 h-9 sm:w-9.5 sm:h-9.5 rounded-full border border-[#E6E2D8] dark:border-[#2E2E33] hover:border-gray-400 dark:hover:border-gray-500 bg-[#FAF9F6] dark:bg-[#202024] hover:bg-black/5 dark:hover:bg-white/10 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white transition-all flex items-center justify-center cursor-pointer shadow-2xs group relative"
                   title="My Bookings & Orders"
                   aria-label="My Bookings & Orders">
                    <i class="fa-solid fa-bag-shopping text-xs text-gray-700 dark:text-gray-300 group-hover:text-black dark:group-hover:text-white group-hover:scale-110 transition-all duration-200"></i>
                </a>

                <!-- 4. Account / Avatar Button -->
                @if(Auth::check())
                <div class="relative" id="user-avatar-dropdown-container">
                    <button type="button" 
                            id="user-avatar-btn"
                            onclick="toggleUserAvatarDropdown(event)"
                            class="w-9 h-9 sm:w-9.5 sm:h-9.5 rounded-full border border-[#E6E2D8] dark:border-[#2E2E33] hover:border-[#FFD600] dark:hover:border-[#FFD600] bg-white dark:bg-[#18181B] transition-all flex items-center justify-center cursor-pointer shadow-2xs hover:shadow-xs p-0.5 relative group"
                            title="{{ Auth::user()->name }} (Account & Bookings)"
                            aria-expanded="false"
                            aria-haspopup="true">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" 
                                 alt="{{ Auth::user()->name }}" 
                                 class="w-full h-full object-cover rounded-full" 
                                 referrerpolicy="no-referrer"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span class="w-full h-full rounded-full hidden items-center justify-center font-heading font-black text-xs shadow-inner" style="background: linear-gradient(135deg, #18181B 0%, #2A2A2E 100%) !important; color: #FFD600 !important; border: 1.5px solid rgba(255, 214, 0, 0.45);">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </span>
                            @if(Auth::user()->google_id)
                                <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-white dark:bg-[#18181B] border border-gray-200 dark:border-gray-700 flex items-center justify-center shadow-xs" title="Google Connected">
                                    <svg class="w-2 h-2" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.87c2.27-2.09 3.67-5.17 3.67-9.15z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.87-3.05c-1.08.72-2.45 1.16-4.06 1.16-3.13 0-5.78-2.11-6.73-4.96H1.25v3.15C3.25 21.36 7.35 24 12 24z"/><path fill="#FBBC05" d="M5.27 14.24c-.25-.72-.39-1.5-.39-2.24s.14-1.52.39-2.24V6.61H1.25C.45 8.2 0 10.04 0 12s.45 3.8 1.25 5.39l4.02-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.35 0 3.25 2.64 1.25 6.61l4.02 3.15c.95-2.85 3.6-4.96 6.73-4.96z"/></svg>
                                </span>
                            @else
                                <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white dark:border-[#18181B] shadow-xs"></span>
                            @endif
                        @else
                            <span class="w-full h-full rounded-full flex items-center justify-center font-heading font-black text-xs shadow-inner" style="background: linear-gradient(135deg, #18181B 0%, #2A2A2E 100%) !important; color: #FFD600 !important; border: 1.5px solid rgba(255, 214, 0, 0.45);">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </span>
                            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white dark:border-[#18181B] shadow-xs"></span>
                        @endif
                    </button>

                    <!-- Luxury User Account Dropdown -->
                    <div id="user-avatar-menu" 
                         class="hidden absolute right-0 top-full mt-2 w-64 bg-white dark:bg-[#18181B] border border-[#E6E2D8] dark:border-[#2E2E33] rounded-2xl shadow-2xl p-2.5 z-50 text-left transition-all origin-top-right">
                        <!-- Profile Card Header -->
                        <div class="p-3 rounded-xl bg-[#FAF9F6] dark:bg-white/5 border border-black/5 dark:border-white/5 flex items-center gap-3 mb-2">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover shrink-0 ring-1 ring-black/10" referrerpolicy="no-referrer">
                            @else
                                <span class="w-10 h-10 rounded-full flex items-center justify-center font-heading font-black text-sm shrink-0 shadow-inner" style="background: linear-gradient(135deg, #18181B 0%, #2A2A2E 100%) !important; color: #FFD600 !important; border: 1.5px solid rgba(255, 214, 0, 0.45);">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                </span>
                            @endif
                            <div class="min-w-0 flex-1">
                                <div class="font-heading font-bold text-xs text-[#171719] dark:text-white truncate">
                                    {{ Auth::user()->name }}
                                </div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400 truncate">
                                    {{ Auth::user()->email }}
                                </div>
                                @if(Auth::user()->google_id)
                                    <div class="mt-1 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-white dark:bg-black/40 border border-gray-200 dark:border-gray-700 text-[9px] font-semibold text-gray-700 dark:text-gray-300">
                                        <svg class="w-2.5 h-2.5" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.87c2.27-2.09 3.67-5.17 3.67-9.15z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.87-3.05c-1.08.72-2.45 1.16-4.06 1.16-3.13 0-5.78-2.11-6.73-4.96H1.25v3.15C3.25 21.36 7.35 24 12 24z"/><path fill="#FBBC05" d="M5.27 14.24c-.25-.72-.39-1.5-.39-2.24s.14-1.52.39-2.24V6.61H1.25C.45 8.2 0 10.04 0 12s.45 3.8 1.25 5.39l4.02-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.35 0 3.25 2.64 1.25 6.61l4.02 3.15c.95-2.85 3.6-4.96 6.73-4.96z"/></svg>
                                        <span>Google Account</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Menu Links -->
                        <div class="space-y-1">
                            <a href="{{ route('orders.index') }}" 
                               class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/10 hover:text-black dark:hover:text-white transition-colors group">
                                <span class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-bag-shopping text-xs text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors"></i>
                                    <span>My Bookings &amp; Orders</span>
                                </span>
                                <i class="fa-solid fa-chevron-right text-[9px] text-gray-300 dark:text-gray-600"></i>
                            </a>

                            <a href="{{ route('booking.track') }}" 
                               class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/10 hover:text-black dark:hover:text-white transition-colors group">
                                <span class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-magnifying-glass-location text-xs text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors"></i>
                                    <span>Track by Reference ID</span>
                                </span>
                                <i class="fa-solid fa-chevron-right text-[9px] text-gray-300 dark:text-gray-600"></i>
                            </a>

                            @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" 
                               class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 transition-colors group">
                                <span class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-shield-halved text-xs text-amber-500"></i>
                                    <span>Admin Panel</span>
                                </span>
                                <i class="fa-solid fa-chevron-right text-[9px] text-amber-300"></i>
                            </a>
                            @endif
                        </div>

                        <!-- Divider -->
                        <div class="my-1.5 border-t border-[#E6E2D8] dark:border-white/10"></div>

                        <!-- Logout Form -->
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors cursor-pointer text-left">
                                <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}" 
                   class="w-9 h-9 sm:w-9.5 sm:h-9.5 rounded-full border border-[#E6E2D8] dark:border-[#2E2E33] hover:border-gray-400 dark:hover:border-gray-500 bg-[#FAF9F6] dark:bg-[#202024] hover:bg-black/5 dark:hover:bg-white/10 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white transition-all flex items-center justify-center cursor-pointer shadow-2xs group"
                   title="Login"
                   aria-label="Login">
                    <i class="fa-solid fa-user text-xs text-gray-700 dark:text-gray-300 group-hover:text-black dark:group-hover:text-white group-hover:scale-110 transition-all duration-200"></i>
                </a>
                @endif

                <!-- 5. Primary CTA: Book Setup Button (Directs to Packages Catalog) -->
                <a href="{{ route('events.index') }}" 
                   class="w-9 h-9 sm:w-auto sm:h-9 sm:px-4 md:px-5 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] text-[11.5px] font-heading font-extrabold uppercase tracking-wider rounded-full shadow-2xs transition-transform hover:scale-[1.02] whitespace-nowrap ml-1 inline-flex items-center justify-center sm:gap-1.5 shrink-0"
                   title="Book Setup"
                   aria-label="Book Setup">
                    <i class="fa-solid fa-calendar-check text-xs"></i>
                    <span class="hidden sm:inline">Book Setup</span>
                </a>

                <!-- 6. Mobile Hamburger Button -->
                <button onclick="toggleMobileNav()" 
                        class="lg:hidden w-9 h-9 rounded-full border border-[#E6E2D8] dark:border-[#2E2E33] hover:border-gray-400 dark:hover:border-gray-500 text-[#171719] dark:text-white flex items-center justify-center transition-colors cursor-pointer bg-[#FAF9F6] dark:bg-[#202024] shadow-2xs ml-1"
                        aria-label="Open Navigation Menu">
                    <i class="fa-solid fa-bars text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Tier 3: Direct Category Navigation Ribbon with Large Multi-Column Mega Menus (Compact & Centered) -->
    <div id="category-nav-ribbon" class="w-full bg-white dark:bg-[#171719] border-t border-[#E6E2D8] dark:border-[#242428] py-1.5 hidden lg:block select-none transition-colors relative z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <nav class="flex items-center justify-between xl:justify-center gap-3 lg:gap-4 xl:gap-6 font-heading text-[12px] font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap flex-nowrap">
                @foreach($navCategories as $cat)
                    @php
                        $validSubs = $cat->activeSubcategories->filter(fn($s) => !empty(trim($s->name)));
                        $groupedSubs = $validSubs->groupBy(function($item) {
                            return trim($item->group_name) !== '' ? trim($item->group_name) : 'Setup Themes';
                        });
                        $colCount = min(max($groupedSubs->count(), 1), 3);
                    @endphp
                    <!-- {{ $cat->title }} -->
                    <div class="mega-nav-item group py-0.5 shrink-0">
                        <a href="{{ route('events.index', ['category' => $cat->nav_slug]) }}" class="flex items-center gap-1.5 py-1 hover:text-black dark:hover:text-white transition-colors {{ request('category') === $cat->nav_slug ? 'text-black dark:text-white font-bold' : '' }}">
                            <i class="{{ $cat->icon ?: 'fa-solid fa-cake-candles' }} text-xs text-gray-500 dark:text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors"></i>
                            <span>{{ $cat->title }}</span>
                            @if($validSubs->count() > 0)
                                <i class="fa-solid fa-chevron-down text-[7px] text-gray-400 group-hover:text-black dark:group-hover:text-white group-hover:rotate-180 transition-transform duration-200 ml-0.5"></i>
                            @endif
                        </a>
                        @if($validSubs->count() > 0)
                            <!-- Mega Dropdown (Spacious & Balanced + GSAP Curtain Motion) -->
                            <div class="mega-dropdown-panel absolute left-1/2 -translate-x-1/2 top-full pt-3 z-50 pointer-events-none invisible before:content-[''] before:absolute before:-top-4 before:inset-x-0 before:h-6">
                                <div class="mega-dropdown-card w-[1060px] xl:w-[1140px] 2xl:w-[1200px] max-w-[96vw] min-h-[420px] bg-white dark:bg-[#18181B] border border-[#E6E2D8] dark:border-[#2E2E33] shadow-2xl rounded-2xl overflow-hidden text-left flex">
                                    <div class="grid {{ $colCount === 1 ? 'grid-cols-1' : ($colCount === 2 ? 'grid-cols-2' : 'grid-cols-3') }} gap-8 xl:gap-10 flex-1 divide-x divide-gray-100 dark:divide-gray-800/80 p-7 xl:p-9">
                                        @foreach($groupedSubs->take(3) as $groupTitle => $subs)
                                            <div class="space-y-4 {{ !$loop->first ? 'pl-8 xl:pl-10' : '' }} flex flex-col justify-between">
                                                <div>
                                                    <div class="pb-2.5 mb-3.5 border-b border-gray-100 dark:border-gray-800/80">
                                                        <h6 class="text-[12px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">{{ $groupTitle }}</h6>
                                                    </div>
                                                    <ul class="space-y-2.5 text-[13px] text-gray-600 dark:text-gray-300 font-normal">
                                                        @foreach($subs->take(6) as $sub)
                                                            <li>
                                                                <a href="{{ route('events.index', ['category' => $cat->nav_slug, 'sub' => $sub->slug]) }}" class="flex items-center justify-between gap-3 px-2.5 py-1.5 -mx-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 hover:text-black dark:hover:text-white transition-all group/item">
                                                                    <span class="group-hover/item:font-semibold group-hover/item:text-black dark:group-hover/item:text-white group-hover/item:translate-x-0.5 transition-all text-gray-700 dark:text-gray-200 leading-snug">{{ $sub->name }}</span>
                                                                    @if($sub->badge)
                                                                        @php
                                                                            $b = strtolower($sub->badge);
                                                                            $badgeClass = match($b) {
                                                                                'trending' => 'bg-red-50 text-red-600 border border-red-200/60 dark:bg-red-950/40 dark:text-red-300 dark:border-red-800/40',
                                                                                'special'  => 'bg-purple-50 text-purple-600 border border-purple-200/60 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/40',
                                                                                'top'      => 'bg-indigo-50 text-indigo-600 border border-indigo-200/60 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800/40',
                                                                                'new'      => 'bg-emerald-50 text-emerald-600 border border-emerald-200/60 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/40',
                                                                                default    => 'bg-amber-50 text-amber-800 border border-amber-200/60 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/40',
                                                                            };
                                                                        @endphp
                                                                        <span class="text-[9.5px] font-bold {{ $badgeClass }} px-2 py-0.5 rounded-full shrink-0 shadow-2xs">{{ $sub->badge }}</span>
                                                                    @endif
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                        @if($subs->count() > 6)
                                                            <li class="pt-1">
                                                                <a href="{{ route('events.index', ['category' => $cat->nav_slug]) }}" class="inline-flex items-center gap-1.5 text-[11.5px] font-bold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 hover:underline">
                                                                    <span>+ {{ $subs->count() - 6 }} more in {{ $groupTitle }}</span>
                                                                    <i class="fa-solid fa-arrow-right text-[9px]"></i>
                                                                </a>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                                @if($loop->last)
                                                    <div class="pt-4 mt-3 border-t border-gray-100 dark:border-gray-800/80">
                                                        <a href="{{ route('events.index', ['category' => $cat->nav_slug]) }}" class="inline-flex items-center gap-2 text-[13px] font-bold text-gray-900 dark:text-[#FFD600] hover:text-[#E5B200] dark:hover:text-amber-300 transition-colors group/all">
                                                            <span>View All {{ $cat->title }}</span>
                                                            <i class="fa-solid fa-arrow-right text-xs group-hover/all:translate-x-1.5 transition-transform"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="w-72 xl:w-84 2xl:w-92 self-stretch relative shrink-0 block border-l border-[#E6E2D8] dark:border-[#2E2E33] bg-gray-100 dark:bg-[#202024] overflow-hidden">
                                        @if($cat->dropdown_image)
                                            @php
                                                $cleanNavDrop = ltrim(parse_url($cat->dropdown_image, PHP_URL_PATH) ?? '', '/');
                                                $optNavDrop = preg_replace('/\.webp$/i', '.opt.webp', $cleanNavDrop);
                                                $hasOptNavDrop = ($optNavDrop !== $cleanNavDrop && file_exists(public_path($optNavDrop)));
                                            @endphp
                                            @if($hasOptNavDrop)
                                                <picture>
                                                    <source srcset="{{ asset($optNavDrop) }}" type="image/webp">
                                                    <img src="{{ asset($cat->dropdown_image) }}" alt="{{ $cat->title }}" class="w-full h-full object-cover block" loading="lazy" width="800" height="800">
                                                </picture>
                                            @else
                                                <img src="{{ asset($cat->dropdown_image) }}" alt="{{ $cat->title }}" class="w-full h-full object-cover block" loading="lazy" width="800" height="800">
                                            @endif
                                        @else
                                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 p-6 text-center">
                                                <i class="fa-regular fa-image text-3xl mb-2 opacity-50"></i>
                                                <span class="text-xs font-semibold">{{ $cat->title }}</span>
                                            </div>
                                        @endif
                                        @if($cat->dropdown_badge)
                                            <span class="absolute top-4 left-4 bg-[#FFD600] text-[#171719] text-[9.5px] font-heading font-extrabold uppercase px-3 py-1 rounded-full shadow-md tracking-wider z-10">{{ $cat->dropdown_badge }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </nav>
        </div>
    </div>
</header>

<!-- Mobile Drawer Slide-in Navigation Menu -->
<div id="mobile-nav"
    class="fixed inset-y-0 right-0 z-[1000] w-full max-w-[320px] sm:max-w-[340px] bg-white dark:bg-[#171719] text-[#171719] dark:text-white flex flex-col h-full h-[100dvh] max-h-screen overflow-hidden transform translate-x-full transition-all duration-300 lg:hidden border-l border-[#E6E2D8] dark:border-[#292929] invisible pointer-events-none hidden shadow-2xl overscroll-contain select-none">
    
    <!-- 1. Drawer Header (Pinned at Top) -->
    <div class="shrink-0 px-5 py-3.5 border-b border-[#E6E2D8] dark:border-[#292929] flex items-center justify-between bg-white dark:bg-[#171719]">
        <a href="{{ route('home') }}" class="flex items-center">
            <picture>
                <source srcset="{{ asset('assets/images/logo/artizen.webp') }}" type="image/webp">
                <img src="{{ asset('assets/images/logo/artizen.png') }}" alt="Artizen Logo" class="h-10 w-auto object-contain rounded-md" width="447" height="500">
            </picture>
        </a>
        <div class="flex items-center gap-2">
            <!-- Quick Theme Switch -->
            <button type="button" onclick="toggleTheme()"
                class="w-8.5 h-8.5 rounded-full border border-[#E6E2D8] dark:border-[#2E2E33] bg-[#FAF9F6] dark:bg-[#202024] hover:border-gray-400 dark:hover:border-gray-500 flex items-center justify-center transition-colors cursor-pointer text-[#171719] dark:text-white"
                aria-label="Toggle Theme" title="Toggle Theme">
                <i id="mobile-theme-icon" class="fa-solid fa-moon text-xs text-gray-700 dark:text-gray-300"></i>
            </button>
            <!-- Close Button -->
            <button type="button" onclick="toggleMobileNav()"
                class="w-8.5 h-8.5 border border-[#E6E2D8] dark:border-[#2E2E33] bg-[#FAF9F6] dark:bg-[#202024] rounded-full hover:border-gray-400 dark:hover:border-gray-500 flex items-center justify-center transition-colors cursor-pointer text-[#171719] dark:text-white"
                aria-label="Close menu">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </div>

    <!-- 2. Drawer Body (Scrollable with Momentum & Overscroll Containment) -->
    <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-4 space-y-4 text-left" style="-webkit-overflow-scrolling: touch;">
        <!-- Main Pages Links (Navigation Only, No Categories) -->
        <div class="pt-1">
            <span class="text-[10px] font-heading font-extrabold uppercase tracking-widest text-gray-400 dark:text-gray-500 block mb-2 px-1">Navigation</span>
            <nav class="flex flex-col gap-1 font-heading font-semibold text-xs tracking-tight text-left">
                <a href="{{ route('home') }}" onclick="toggleMobileNav()"
                    class="py-2.5 px-3 rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 text-gray-800 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between transition-colors group {{ request()->routeIs('home') ? 'bg-[#FFD600]/15 text-black dark:text-white font-bold' : '' }}">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-solid fa-house text-xs text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors w-4 text-center"></i>
                        <span>Home</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-[9px] text-gray-300 dark:text-gray-600"></i>
                </a>

                <a href="{{ route('events.index') }}" onclick="toggleMobileNav()"
                    class="py-2.5 px-3 rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 text-gray-800 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between transition-colors group {{ request()->routeIs('events.*') ? 'bg-[#FFD600]/15 text-black dark:text-white font-bold' : '' }}">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-solid fa-wand-magic-sparkles text-xs text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors w-4 text-center"></i>
                        <span>All Packages</span>
                    </span>
                    <span class="text-[9.5px] bg-[#FFD600] text-[#171719] font-extrabold px-2 py-0.5 rounded-full uppercase tracking-wider">Explore</span>
                </a>

                <a href="{{ route('gallery.index') }}" onclick="toggleMobileNav()"
                    class="py-2.5 px-3 rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 text-gray-800 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between transition-colors group {{ request()->routeIs('gallery.*') ? 'bg-[#FFD600]/15 text-black dark:text-white font-bold' : '' }}">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-regular fa-images text-xs text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors w-4 text-center"></i>
                        <span>Gallery</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-[9px] text-gray-300 dark:text-gray-600"></i>
                </a>

                <a href="{{ route('reviews.index') }}" onclick="toggleMobileNav()"
                    class="py-2.5 px-3 rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 text-gray-800 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between transition-colors group {{ request()->routeIs('reviews.*') ? 'bg-[#FFD600]/15 text-black dark:text-white font-bold' : '' }}">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-regular fa-star text-xs text-amber-500 w-4 text-center"></i>
                        <span>Customer Reviews</span>
                    </span>
                    <span class="text-[10px] text-amber-600 dark:text-amber-400 font-bold">★ 4.9</span>
                </a>

                <a href="{{ route('about-us') }}" onclick="toggleMobileNav()"
                    class="py-2.5 px-3 rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 text-gray-800 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between transition-colors group {{ request()->routeIs('about-us') ? 'bg-[#FFD600]/15 text-black dark:text-white font-bold' : '' }}">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-regular fa-circle-question text-xs text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors w-4 text-center"></i>
                        <span>About Artizen</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-[9px] text-gray-300 dark:text-gray-600"></i>
                </a>

                <a href="{{ route('faq.index') }}" onclick="toggleMobileNav()"
                    class="py-2.5 px-3 rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 text-gray-800 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between transition-colors group {{ request()->routeIs('faq.*') ? 'bg-[#FFD600]/15 text-black dark:text-white font-bold' : '' }}">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-info text-xs text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors w-4 text-center"></i>
                        <span>FAQs</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-[9px] text-gray-300 dark:text-gray-600"></i>
                </a>

                <a href="{{ route('contact.index') }}" onclick="toggleMobileNav()"
                    class="py-2.5 px-3 rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 text-gray-800 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between transition-colors group {{ request()->routeIs('contact.*') ? 'bg-[#FFD600]/15 text-black dark:text-white font-bold' : '' }}">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-regular fa-envelope text-xs text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors w-4 text-center"></i>
                        <span>Contact Us</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-[9px] text-gray-300 dark:text-gray-600"></i>
                </a>
            </nav>
        </div>

        <!-- Bookings & Account Links -->
        <div class="pt-3 border-t border-[#E6E2D8] dark:border-[#292929]">
            <span class="text-[10px] font-heading font-extrabold uppercase tracking-widest text-gray-400 dark:text-gray-500 block mb-2 px-1">Bookings & Services</span>
            <nav class="flex flex-col gap-1 font-heading font-semibold text-xs tracking-tight text-left">
                <a href="{{ route('orders.index') }}" onclick="toggleMobileNav()"
                    class="py-2.5 px-3 rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 text-gray-800 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between transition-colors group">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-solid fa-bag-shopping text-xs text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors w-4 text-center"></i>
                        <span>My Bookings &amp; Orders</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-[9px] text-gray-300 dark:text-gray-600"></i>
                </a>

                <a href="{{ route('booking.track') }}" onclick="toggleMobileNav()"
                    class="py-2.5 px-3 rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 text-gray-800 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between transition-colors group">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-solid fa-magnifying-glass-location text-xs text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors w-4 text-center"></i>
                        <span>Track Reference ID</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-[9px] text-gray-300 dark:text-gray-600"></i>
                </a>

                @if(Auth::check())
                <div class="py-2.5 px-3 rounded-2xl bg-[#FAF9F6] dark:bg-white/5 border border-black/5 dark:border-white/10 flex items-center justify-between mt-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        @if(Auth::user()->avatar)
                            <div class="relative w-8 h-8 shrink-0">
                                <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover ring-1 ring-black/10" referrerpolicy="no-referrer">
                                @if(Auth::user()->google_id)
                                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-white flex items-center justify-center shadow-xs">
                                        <svg class="w-2 h-2" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.87c2.27-2.09 3.67-5.17 3.67-9.15z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.87-3.05c-1.08.72-2.45 1.16-4.06 1.16-3.13 0-5.78-2.11-6.73-4.96H1.25v3.15C3.25 21.36 7.35 24 12 24z"/><path fill="#FBBC05" d="M5.27 14.24c-.25-.72-.39-1.5-.39-2.24s.14-1.52.39-2.24V6.61H1.25C.45 8.2 0 10.04 0 12s.45 3.8 1.25 5.39l4.02-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.35 0 3.25 2.64 1.25 6.61l4.02 3.15c.95-2.85 3.6-4.96 6.73-4.96z"/></svg>
                                    </span>
                                @endif
                            </div>
                        @else
                            <span class="w-8 h-8 rounded-full flex items-center justify-center font-heading font-black text-xs shrink-0 shadow-inner" style="background: linear-gradient(135deg, #18181B 0%, #2A2A2E 100%) !important; color: #FFD600 !important; border: 1.5px solid rgba(255, 214, 0, 0.45);">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </span>
                        @endif
                        <div class="truncate">
                            <span class="block text-xs font-bold text-gray-800 dark:text-gray-200 truncate">{{ Auth::user()->name }}</span>
                            <span class="block text-[10px] text-gray-500 truncate">{{ Auth::user()->email }}</span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline shrink-0 ml-2">
                        @csrf
                        <button type="submit" class="text-[11px] font-bold text-red-600 hover:underline cursor-pointer">Logout</button>
                    </form>
                </div>
                @else
                <a href="{{ route('login') }}" onclick="toggleMobileNav()"
                    class="py-2.5 px-3 rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 text-gray-800 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between transition-colors group">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-regular fa-user text-xs text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors w-4 text-center"></i>
                        <span>Login</span>
                    </span>
                    <i class="fa-solid fa-arrow-right-to-bracket text-[10px] text-gray-400"></i>
                </a>
                @endif
            </nav>
        </div>
    </div>

    <!-- 3. Drawer Bottom Book CTA (Pinned at Bottom) -->
    <div class="shrink-0 p-4 border-t border-[#E6E2D8] dark:border-[#292929] bg-[#FAF9F6] dark:bg-[#1C1C1F] flex flex-col gap-2">
        <a href="{{ route('events.index') }}" onclick="toggleMobileNav()"
            class="w-full flex items-center justify-center gap-2 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] py-3 rounded-xl font-heading font-extrabold text-xs uppercase tracking-wider shadow-xs text-center transition-transform active:scale-[0.98]">
            <i class="fa-solid fa-calendar-check text-xs"></i>
            <span>Book Event Setup</span>
        </a>
        <div class="flex items-center justify-center gap-3 pt-1 text-[11px] text-gray-500 dark:text-gray-400">
            <a href="https://wa.me/917489565502" target="_blank" rel="noopener" class="flex items-center gap-1.5 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                <i class="fa-brands fa-whatsapp text-emerald-500 text-xs"></i>
                <span>WhatsApp</span>
            </a>
            <span class="text-gray-300 dark:text-gray-700">•</span>
            <a href="tel:+917489565502" class="flex items-center gap-1.5 hover:text-black dark:hover:text-white transition-colors">
                <i class="fa-solid fa-phone text-[10px]"></i>
                <span>Call Support</span>
            </a>
        </div>
    </div>
</div>

<!-- Mobile Drawer Overlay Backdrop -->
<div id="mobile-nav-overlay" onclick="toggleMobileNav()"
    class="fixed inset-0 bg-black/80 backdrop-blur-xs z-[995] hidden transition-opacity duration-300 opacity-0 pointer-events-none lg:hidden touch-none overscroll-contain">
</div>

<!-- Mobile Slide-Down Search Modal Overlay -->
<div id="mobile-search-modal" 
     class="fixed inset-0 z-[1050] bg-black/75 backdrop-blur-xs hidden opacity-0 transition-opacity duration-200 flex flex-col justify-start touch-none">
    <div id="mobile-search-panel" 
         class="w-full bg-white dark:bg-[#171719] border-b border-[#E6E2D8] dark:border-[#2E2E33] shadow-2xl p-4 sm:p-5 pt-3 transform -translate-y-4 transition-transform duration-200 max-h-[88vh] flex flex-col touch-auto select-none">
        
        <!-- Search Input Bar & Close Button -->
        <div class="flex items-center gap-2.5">
            <form action="{{ route('events.index') }}" method="GET" class="relative flex-1" id="mobile-modal-search-form">
                <div class="relative flex items-center">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#B89700] dark:text-[#FFD600] pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" 
                           id="mobile-modal-search-input"
                           name="q" 
                           value="{{ request('q', '') }}"
                           autocomplete="off"
                           placeholder="Search setups, decor, DJ, weddings..." 
                           class="w-full pl-9 pr-9 py-2.5 bg-[#FAF9F6] dark:bg-[#242428] border border-[#E6E2D8] dark:border-[#333338] rounded-xl text-xs sm:text-sm font-medium text-[#171719] dark:text-white placeholder-gray-400 outline-none focus:border-[#FFD600] dark:focus:border-[#FFD600] shadow-none ring-0 focus:ring-0">
                    
                    <button type="button" 
                            id="mobile-modal-search-clear" 
                            onclick="clearMobileModalSearch()" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#171719] dark:hover:text-white transition-colors cursor-pointer {{ request('q') ? '' : 'hidden' }} p-0.5"
                            aria-label="Clear search">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>
            </form>
            <button type="button" 
                    onclick="toggleMobileSearchModal()"
                    class="shrink-0 px-3 py-2 rounded-xl text-xs font-heading font-bold text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                Cancel
            </button>
        </div>

        <!-- Popular Quick Search Tag Chips -->
        <div id="mobile-search-quick-tags" class="pt-3 pb-1">
            <span class="text-[10px] font-heading font-extrabold uppercase tracking-widest text-gray-400 dark:text-gray-500 block mb-2">Popular Celebrations</span>
            <div class="flex flex-wrap gap-1.5 text-xs">
                <button type="button" onclick="selectQuickSearchTag('Birthday')" class="px-2.5 py-1 rounded-full bg-[#FAF9F6] dark:bg-[#202024] border border-[#E6E2D8] dark:border-[#2E2E33] hover:border-[#FFD600] text-gray-700 dark:text-gray-200 text-[11px] font-semibold transition-colors flex items-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-cake-candles text-[10px] text-amber-500"></i> Birthday
                </button>
                <button type="button" onclick="selectQuickSearchTag('Proposal')" class="px-2.5 py-1 rounded-full bg-[#FAF9F6] dark:bg-[#202024] border border-[#E6E2D8] dark:border-[#2E2E33] hover:border-[#FFD600] text-gray-700 dark:text-gray-200 text-[11px] font-semibold transition-colors flex items-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-heart text-[10px] text-rose-500"></i> Proposal
                </button>
                <button type="button" onclick="selectQuickSearchTag('House Party & DJ')" class="px-2.5 py-1 rounded-full bg-[#FAF9F6] dark:bg-[#202024] border border-[#E6E2D8] dark:border-[#2E2E33] hover:border-[#FFD600] text-gray-700 dark:text-gray-200 text-[11px] font-semibold transition-colors flex items-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-volume-high text-[10px] text-indigo-500"></i> DJ & Music
                </button>
                <button type="button" onclick="selectQuickSearchTag('Anniversaries')" class="px-2.5 py-1 rounded-full bg-[#FAF9F6] dark:bg-[#202024] border border-[#E6E2D8] dark:border-[#2E2E33] hover:border-[#FFD600] text-gray-700 dark:text-gray-200 text-[11px] font-semibold transition-colors flex items-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-gem text-[10px] text-purple-500"></i> Anniversary
                </button>
                <button type="button" onclick="selectQuickSearchTag('Weddings')" class="px-2.5 py-1 rounded-full bg-[#FAF9F6] dark:bg-[#202024] border border-[#E6E2D8] dark:border-[#2E2E33] hover:border-[#FFD600] text-gray-700 dark:text-gray-200 text-[11px] font-semibold transition-colors flex items-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-rings-wedding text-[10px] text-amber-600"></i> Weddings
                </button>
            </div>
        </div>

        <!-- Live Search Suggestions Container -->
        <div id="mobile-modal-search-suggestions" 
             class="mt-2 flex-1 overflow-y-auto overscroll-contain hidden max-h-[55vh] pt-1 text-left">
            <div id="mobile-modal-suggestions-content"></div>
        </div>
    </div>
    <!-- Dismiss Backdrop Area -->
    <div class="flex-1" onclick="toggleMobileSearchModal()"></div>
</div>

<!-- Header JavaScript: Rotators & Live Autocomplete Search -->
<script>
    const searchCatalog = @json($searchItems);

    function initSearchAutocomplete(inputId, clearBtnId, suggestionsId, contentId) {
        const input = document.getElementById(inputId);
        const clearBtn = document.getElementById(clearBtnId);
        const dropdown = document.getElementById(suggestionsId);
        const content = document.getElementById(contentId);

        if (!input || !dropdown || !content) return;

        function openDropdown() {
            dropdown.classList.remove('hidden');
            document.documentElement.classList.add('overflow-hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeDropdown() {
            dropdown.classList.add('hidden');
            const anyOpen = document.querySelectorAll('#header-search-suggestions:not(.hidden), #mobile-modal-search-suggestions:not(.hidden)').length > 0;
            const mobileNav = document.getElementById('mobile-nav');
            const mobileNavOpen = mobileNav && !mobileNav.classList.contains('translate-x-full') && !mobileNav.classList.contains('hidden');
            const searchModal = document.getElementById('mobile-search-modal');
            const searchModalOpen = searchModal && !searchModal.classList.contains('hidden');
            if (!anyOpen && !mobileNavOpen && !searchModalOpen) {
                document.documentElement.classList.remove('overflow-hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        function renderSuggestions(query) {
            const q = query.trim().toLowerCase();
            if (!q) {
                closeDropdown();
                return;
            }

            const matches = searchCatalog.filter(item => 
                item.title.toLowerCase().includes(q) || 
                item.category.toLowerCase().includes(q) ||
                (item.subcategory && item.subcategory.toLowerCase().includes(q)) ||
                (item.tags && item.tags.toLowerCase().includes(q))
            ).slice(0, 8);

            if (matches.length === 0) {
                content.innerHTML = `
                    <div class="py-6 px-4 text-center text-xs text-gray-500 dark:text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-gray-300 dark:text-gray-600 text-xl mb-2 block"></i>
                        <p class="font-semibold text-gray-800 dark:text-gray-200 text-xs">No matching celebrations found</p>
                        <p class="text-[11px] mt-0.5 text-gray-400">Try searching "Birthday", "Proposal", "Anniversary", "DJ", "Wedding"</p>
                    </div>
                `;
            } else {
                let html = `
                    <div class="flex items-center justify-between px-2.5 pt-1 pb-2 border-b border-gray-100 dark:border-white/5 mb-2">
                        <span class="text-[10px] font-heading font-extrabold uppercase tracking-wider text-gray-400 dark:text-gray-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-sparkles text-[9px] text-[#B89700]"></i> Suggested Setups (${matches.length})
                        </span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                `;
                matches.forEach(item => {
                    html += `
                        <a href="${item.url}" class="flex items-center gap-3 p-2 rounded-xl bg-white hover:bg-[#FAF7F2] dark:bg-[#1C1C1F] dark:hover:bg-[#252529] border border-[#E8DFC8] dark:border-white/5 transition-all text-left min-w-0 select-none cursor-pointer">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-[#FAF7F2] shrink-0 border border-[#E8DFC8]/60">
                                <img src="${item.image}" alt="${item.title}" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[9px] font-heading font-extrabold uppercase tracking-wider text-[#B89700] block truncate leading-tight">${item.subcategory || item.category}</span>
                                <h6 class="text-xs font-bold text-gray-950 dark:text-gray-100 truncate leading-snug">${item.title}</h6>
                            </div>
                        </a>
                    `;
                });
                html += `
                    </div>
                    <div class="pt-2.5 mt-2.5 border-t border-[#EFE7D8] dark:border-white/5">
                        <a href="{{ route('events.index') }}?q=${encodeURIComponent(query)}" class="flex items-center justify-between px-3 py-1.5 rounded-xl hover:bg-[#FAF7F2] dark:hover:bg-white/5 text-xs font-heading font-semibold text-gray-800 dark:text-gray-200 transition-colors">
                            <span>Search all celebrations for "<strong class="text-gray-950 dark:text-white">${query}</strong>"</span>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Press Enter ↵</span>
                        </a>
                    </div>
                `;
                content.innerHTML = html;
            }
            openDropdown();
        }

        input.addEventListener('input', function () {
            if (this.value.trim().length > 0) {
                if (clearBtn) clearBtn.classList.remove('hidden');
                renderSuggestions(this.value);
            } else {
                if (clearBtn) clearBtn.classList.add('hidden');
                closeDropdown();
            }
        });

        input.addEventListener('focus', function () {
            if (this.value.trim().length > 0) {
                renderSuggestions(this.value);
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                closeDropdown();
            }
        });
    }

    function clearHeaderSearch() {
        const input = document.getElementById('header-search-input');
        const clearBtn = document.getElementById('header-search-clear');
        const dropdown = document.getElementById('header-search-suggestions');
        if (input) {
            input.value = '';
            input.focus();
        }
        if (clearBtn) clearBtn.classList.add('hidden');
        if (dropdown) {
            dropdown.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    window.toggleMobileSearchModal = function () {
        const modal = document.getElementById('mobile-search-modal');
        const panel = document.getElementById('mobile-search-panel');
        const input = document.getElementById('mobile-modal-search-input');
        if (!modal || !panel) return;

        const isClosed = modal.classList.contains('hidden');
        if (isClosed) {
            modal.classList.remove('hidden');
            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100');
                panel.classList.remove('-translate-y-4');
                panel.classList.add('translate-y-0');
            });
            document.documentElement.classList.add('overflow-hidden');
            document.body.classList.add('overflow-hidden');
            if (input) {
                setTimeout(() => input.focus(), 120);
            }
        } else {
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            panel.classList.remove('translate-y-0');
            panel.classList.add('-translate-y-4');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 210);
            const mobileNav = document.getElementById('mobile-nav');
            const mobileNavOpen = mobileNav && !mobileNav.classList.contains('translate-x-full') && !mobileNav.classList.contains('hidden');
            if (!mobileNavOpen) {
                document.documentElement.classList.remove('overflow-hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }
    };

    window.clearMobileModalSearch = function () {
        const input = document.getElementById('mobile-modal-search-input');
        const clearBtn = document.getElementById('mobile-modal-search-clear');
        const dropdown = document.getElementById('mobile-modal-search-suggestions');
        if (input) {
            input.value = '';
            input.focus();
        }
        if (clearBtn) clearBtn.classList.add('hidden');
        if (dropdown) dropdown.classList.add('hidden');
    };

    window.selectQuickSearchTag = function (tag) {
        const input = document.getElementById('mobile-modal-search-input');
        if (input) {
            input.value = tag;
            input.dispatchEvent(new Event('input'));
            input.focus();
        }
    };

    // Close mobile search on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('mobile-search-modal');
            if (modal && !modal.classList.contains('hidden')) {
                toggleMobileSearchModal();
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Search Autocompletes
        initSearchAutocomplete('header-search-input', 'header-search-clear', 'header-search-suggestions', 'header-suggestions-content');
        initSearchAutocomplete('mobile-modal-search-input', 'mobile-modal-search-clear', 'mobile-modal-search-suggestions', 'mobile-modal-suggestions-content');

        // 1. Location Rotator
        const locRotator = document.getElementById('header-location-rotator');
        if (locRotator) {
            const locCount = locRotator.children.length;
            let currentLoc = 0;
            setInterval(function () {
                currentLoc = (currentLoc + 1) % locCount;
                locRotator.style.transform = `translateY(-${currentLoc * 16}px)`;
            }, 2800);
        }

        // 2. Feature / Guarantee Rotator
        const featRotator = document.getElementById('header-feature-rotator');
        const featIcon = document.getElementById('header-feature-icon');
        const icons = ['fa-bolt', 'fa-shield-check', 'fa-handshake', 'fa-star', 'fa-music'];
        if (featRotator) {
            const featCount = featRotator.children.length;
            let currentFeat = 0;
            setInterval(function () {
                currentFeat = (currentFeat + 1) % featCount;
                featRotator.style.transform = `translateY(-${currentFeat * 16}px)`;
                if (featIcon && icons[currentFeat]) {
                    featIcon.style.opacity = '0';
                    setTimeout(function () {
                        featIcon.className = `fa-solid ${icons[currentFeat]} text-gray-500 dark:text-gray-400 text-[10px] shrink-0 transition-all duration-300`;
                        featIcon.style.opacity = '1';
                    }, 150);
                }
            }, 3600);
        }

        // 3. Scroll Down: Smoothly collapse Tier 1 Top Bar without flickering
        const headerEl = document.querySelector('header.header-premium');
        if (headerEl) {
            let isScrolled = false;
            let ticking = false;

            const updateHeaderState = function () {
                const currentScrollY = window.pageYOffset || document.documentElement.scrollTop;

                // Use hysteresis: collapse when scrolling past 60px, restore only when back at top (<= 10px)
                if (currentScrollY > 60) {
                    if (!isScrolled) {
                        isScrolled = true;
                        headerEl.classList.add('scrolled');
                    }
                } else if (currentScrollY <= 10) {
                    if (isScrolled) {
                        isScrolled = false;
                        headerEl.classList.remove('scrolled');
                    }
                }
                ticking = false;
            };

            window.addEventListener('scroll', function () {
                if (!ticking) {
                    window.requestAnimationFrame(updateHeaderState);
                    ticking = true;
                }
            }, { passive: true });

            updateHeaderState(); // Initial check on load
        }
    });

    // Avatar Dropdown Menu Controller
    function toggleUserAvatarDropdown(event) {
        if (event) {
            event.stopPropagation();
        }
        const menu = document.getElementById('user-avatar-menu');
        const btn = document.getElementById('user-avatar-btn');
        if (!menu) return;

        const isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            menu.classList.remove('hidden');
            if (btn) btn.setAttribute('aria-expanded', 'true');
        } else {
            menu.classList.add('hidden');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        }
    }

    document.addEventListener('click', function(e) {
        const container = document.getElementById('user-avatar-dropdown-container');
        const menu = document.getElementById('user-avatar-menu');
        if (container && menu && !container.contains(e.target)) {
            menu.classList.add('hidden');
            const btn = document.getElementById('user-avatar-btn');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const menu = document.getElementById('user-avatar-menu');
            if (menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
                const btn = document.getElementById('user-avatar-btn');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }
        }
    });
</script>

<style>
    /* Prevent any focus ring, purple box-shadow, or jumping animations on search inputs */
    #header-search-input,
    #header-search-input:focus,
    #header-search-input:active,
    #mobile-search-input,
    #mobile-search-input:focus,
    #mobile-search-input:active {
        box-shadow: none !important;
        -webkit-box-shadow: none !important;
        transform: none !important;
        -webkit-transform: none !important;
        outline: none !important;
    }
</style>
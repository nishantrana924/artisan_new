@php
    $categoriesList = \App\Services\JsonStorageService::read('categories.json');
    $packagesRaw = \App\Services\JsonStorageService::read('packages.json');
    $searchItems = [];
    foreach ($packagesRaw as $catId => $cat) {
        if (!($cat['active'] ?? true)) continue;
        $catTitle = $cat['title'] ?? 'Setup';
        foreach ($cat['tiers'] ?? [] as $tIdx => $tier) {
            $searchItems[] = [
                'id' => (int)$catId,
                'tier' => (int)$tIdx,
                'title' => $tier['name'] ?? ($catTitle . ' - Tier ' . ($tIdx + 1)),
                'category' => $catTitle,
                'price' => isset($tier['price']) ? '₹' . number_format($tier['price']) : '',
                'image' => !empty($tier['image']) ? $tier['image'] : (!empty($cat['image']) ? $cat['image'] : asset('assets/images/ic/artizen (2).png')),
                'url' => route('events.show', ['slug' => $catId, 'tier' => $tIdx]),
            ];
        }
    }
@endphp

<!-- Full-Width Sticky Header (Compact & Clean Design System matching reference) -->
<header class="sticky top-0 z-50 w-full bg-white dark:bg-[#171719] border-b border-[#E6E2D8] dark:border-[#292929] shadow-xs dark:shadow-md transition-all duration-300 header-premium select-none text-[#171719] dark:text-white">

    <!-- Tier 1: Top Utility & Trust Strip (Compact, Elegant, Single Row) -->
    <div id="header-top-bar" class="w-full bg-[#FAF9F6] dark:bg-[#0C0C0E] border-b border-[#E6E2D8] dark:border-[#232326] py-1 transition-all duration-300 overflow-hidden">
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
                    <i class="fa-solid fa-chevron-down text-[7px] text-gray-400 ml-0.5"></i>
                </div>
                <span class="text-gray-300 dark:text-white/20">|</span>
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
                <a href="{{ route('booking.track') }}" class="group hover:text-[#171719] dark:hover:text-white transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-truck-fast text-gray-500 dark:text-gray-400 group-hover:text-[#171719] dark:group-hover:text-white transition-colors text-xs"></i>
                    <span>Track Booking</span>
                </a>
                <span class="text-gray-300 dark:text-white/20">|</span>
                <a href="{{ route('contact.index') }}" class="group hover:text-[#171719] dark:hover:text-white transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-headset text-gray-500 dark:text-gray-400 group-hover:text-[#171719] dark:group-hover:text-white transition-colors text-xs"></i>
                    <span>Help & Inquire</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Tier 2: Main Primary Header (Compact: Logo + Expanded Omnisearch + 4 Circular Icons + Book Setup CTA) -->
    <div class="w-full bg-white dark:bg-[#171719] py-2 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-3 sm:gap-5 lg:gap-7">
            
            <!-- Brand Logo (Left - Clean, Crisp & Vertically Centered - No Hover Scaling) -->
            <a href="{{ route('home') }}" class="flex items-center shrink-0 text-left">
                <img src="{{ asset('assets/images/logo/artizen.png') }}" alt="Artizen Logo" class="h-10 sm:h-11 md:h-12 w-auto object-contain block">
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
                               class="w-full pl-10 pr-10 py-2 bg-[#FAF9F6] dark:bg-[#202024] border border-[#E6E2D8] dark:border-[#2E2E33] rounded-full text-xs font-medium text-[#171719] dark:text-white placeholder-gray-400 focus:outline-none focus:border-[#171719] dark:focus:border-white/40 focus:bg-white dark:focus:bg-[#242428] focus:ring-2 focus:ring-black/5 dark:focus:ring-white/10 transition-all shadow-2xs">
                        
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

                <!-- 1. Theme Switcher Button -->
                <button onclick="toggleTheme()" 
                        class="w-9 h-9 sm:w-9.5 sm:h-9.5 rounded-full border border-[#E6E2D8] dark:border-[#2E2E33] hover:border-gray-400 dark:hover:border-gray-500 bg-[#FAF9F6] dark:bg-[#202024] hover:bg-black/5 dark:hover:bg-white/10 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white transition-all flex items-center justify-center cursor-pointer shadow-2xs group relative"
                        title="Switch Theme (Dark / Light)"
                        aria-label="Toggle Dark/Light Mode">
                    <i id="theme-toggle-sun" class="fa-solid fa-sun text-xs text-amber-400 hidden group-hover:rotate-90 group-hover:scale-110 transition-all duration-200" aria-hidden="true"></i>
                    <i id="theme-toggle-moon" class="fa-solid fa-moon text-xs text-gray-700 dark:text-indigo-400 group-hover:-rotate-12 group-hover:scale-110 transition-all duration-200" aria-hidden="true"></i>
                </button>

                <!-- 2. Wishlist / Favorites Icon -->
                <a href="{{ route('events.index') }}" 
                   class="w-9 h-9 sm:w-9.5 sm:h-9.5 rounded-full border border-[#E6E2D8] dark:border-[#2E2E33] hover:border-gray-400 dark:hover:border-gray-500 bg-[#FAF9F6] dark:bg-[#202024] hover:bg-black/5 dark:hover:bg-white/10 text-gray-700 dark:text-gray-200 hover:text-rose-600 dark:hover:text-rose-400 transition-all flex items-center justify-center cursor-pointer shadow-2xs group"
                   title="Wishlist & Saved Packages"
                   aria-label="Favorites">
                    <i class="fa-solid fa-heart text-xs text-gray-700 dark:text-gray-300 group-hover:text-rose-500 group-hover:scale-110 transition-all duration-200"></i>
                </a>

                <!-- 3. Booking Cart / Inquiries Icon -->
                <a href="{{ route('booking.index') }}" 
                   class="w-9 h-9 sm:w-9.5 sm:h-9.5 rounded-full border border-[#E6E2D8] dark:border-[#2E2E33] hover:border-gray-400 dark:hover:border-gray-500 bg-[#FAF9F6] dark:bg-[#202024] hover:bg-black/5 dark:hover:bg-white/10 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white transition-all flex items-center justify-center cursor-pointer shadow-2xs group relative"
                   title="Booking Cart & Request"
                   aria-label="Booking Cart">
                    <i class="fa-solid fa-bag-shopping text-xs text-gray-700 dark:text-gray-300 group-hover:text-amber-500 group-hover:scale-110 transition-all duration-200"></i>
                </a>

                <!-- 4. Account / Login Button (Icon-Only, No Text) -->
                <a href="{{ route('admin.login') }}" 
                   class="w-9 h-9 sm:w-9.5 sm:h-9.5 rounded-full border border-[#E6E2D8] dark:border-[#2E2E33] hover:border-gray-400 dark:hover:border-gray-500 bg-[#FAF9F6] dark:bg-[#202024] hover:bg-black/5 dark:hover:bg-white/10 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white transition-all flex items-center justify-center cursor-pointer shadow-2xs group"
                   title="Account & Admin Login"
                   aria-label="Account Login">
                    <i class="fa-solid fa-user text-xs text-gray-700 dark:text-gray-300 group-hover:text-black dark:group-hover:text-white group-hover:scale-110 transition-all duration-200"></i>
                </a>

                <!-- 5. Primary CTA: Book Setup Button (Compact Yellow Pill) -->
                <a href="{{ route('booking.index') }}" 
                   class="inline-flex items-center justify-center gap-1.5 h-9 px-4 sm:px-5 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] text-[11.5px] font-heading font-extrabold uppercase tracking-wider rounded-full shadow-2xs transition-transform hover:scale-[1.02] whitespace-nowrap ml-1">
                    <i class="fa-solid fa-calendar-check text-xs"></i>
                    <span>Book Setup</span>
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
                
                <!-- 1. Birthdays -->
                <div class="mega-nav-item group py-0.5 shrink-0">
                    <a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="flex items-center gap-1.5 py-1 hover:text-black dark:hover:text-white transition-colors {{ request('category') === 'cat-birthdays' ? 'text-black dark:text-white font-bold' : '' }}">
                        <i class="fa-solid fa-cake-candles text-xs text-gray-500 dark:text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors"></i>
                        <span>Birthdays</span>
                        <i class="fa-solid fa-chevron-down text-[7px] text-gray-400 group-hover:text-black dark:group-hover:text-white group-hover:rotate-180 transition-transform duration-200 ml-0.5"></i>
                    </a>
                    <!-- Mega Dropdown (Centered 90vw + GSAP Curtain Motion) -->
                    <div class="mega-dropdown-panel absolute left-1/2 -translate-x-1/2 top-full pt-3 z-50 pointer-events-none invisible before:content-[''] before:absolute before:-top-4 before:inset-x-0 before:h-6">
                        <div class="mega-dropdown-card w-[90vw] max-w-7xl bg-white dark:bg-[#18181B] border border-[#E6E2D8] dark:border-[#2E2E33] shadow-2xl rounded-2xl overflow-hidden text-left flex">
                            <div class="grid grid-cols-3 gap-6 flex-1 divide-x divide-gray-100 dark:divide-gray-800/80 p-6 xl:p-8">
                                <div class="space-y-3">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Setup Themes</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="flex items-center justify-between gap-2 py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all group/item"><span class="group-hover/item:font-medium">Balloon Arch Backdrops</span><span class="text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 px-1.5 py-0.5 rounded-full shrink-0">Popular</span></a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="flex items-center justify-between gap-2 py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all group/item"><span class="group-hover/item:font-medium">Neon Sign Ring Arch</span><span class="text-[9px] font-bold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 px-1.5 py-0.5 rounded-full shrink-0">Trending</span></a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Pastel Floral Theme</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Club & Stage Lighting</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Cake Table Styling</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Custom Photobooths</a></li>
                                    </ul>
                                </div>
                                <div class="space-y-3 pl-6">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">By Milestones</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="flex items-center justify-between gap-2 py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all group/item"><span class="group-hover/item:font-medium">1st Birthday Specials</span><span class="text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 px-1.5 py-0.5 rounded-full shrink-0">Top</span></a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Sweet 16 & 18th Years</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">21st & 25th Club Party</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">30th to 50th Jubilee</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Birthday For Her</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Birthday For Him</a></li>
                                    </ul>
                                </div>
                                <div class="space-y-3 pl-6">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Experiences</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Live DJ Sound Columns</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">DSLR Photography</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Heavy Low Fog Effects</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Party Anchor & Emcee</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">LED Disco Lights</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="block pt-1 font-bold text-[#FFD600] hover:underline">View All Birthdays →</a></li>
                                    </ul>
                                </div>
                            </div>
                            <a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="w-72 xl:w-80 self-stretch relative shrink-0 block border-l border-[#E6E2D8] dark:border-[#2E2E33] bg-gray-100 dark:bg-[#202024] overflow-hidden">
                                <img src="{{ asset('images/dropdowns/birthdays.webp') }}" alt="Birthdays Celebration Setup" class="w-full h-full object-cover block" loading="lazy">
                                <span class="absolute top-4 left-4 bg-[#FFD600] text-[#171719] text-[9.5px] font-heading font-extrabold uppercase px-2.5 py-0.5 rounded-full shadow-md tracking-wider">Popular Choice</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 2. House Party & DJ -->
                <div class="mega-nav-item group py-0.5 shrink-0">
                    <a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="flex items-center gap-1.5 py-1 hover:text-black dark:hover:text-white transition-colors {{ request('category') === 'cat-house-party' ? 'text-black dark:text-white font-bold' : '' }}">
                        <i class="fa-solid fa-volume-high text-xs text-gray-500 dark:text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors"></i>
                        <span>House Party & DJ</span>
                        <i class="fa-solid fa-chevron-down text-[7px] text-gray-400 group-hover:text-black dark:group-hover:text-white group-hover:rotate-180 transition-transform duration-200 ml-0.5"></i>
                    </a>
                    <!-- Mega Dropdown (Centered 90vw + GSAP Curtain Motion) -->
                    <div class="mega-dropdown-panel absolute left-1/2 -translate-x-1/2 top-full pt-3 z-50 pointer-events-none invisible before:content-[''] before:absolute before:-top-4 before:inset-x-0 before:h-6">
                        <div class="mega-dropdown-card w-[90vw] max-w-7xl bg-white dark:bg-[#18181B] border border-[#E6E2D8] dark:border-[#2E2E33] shadow-2xl rounded-2xl overflow-hidden text-left flex">
                            <div class="grid grid-cols-3 gap-6 flex-1 divide-x divide-gray-100 dark:divide-gray-800/80 p-6 xl:p-8">
                                <div class="space-y-3">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Sound & DJ Rigs</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="flex items-center justify-between gap-2 py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all group/item"><span class="group-hover/item:font-medium">High-Bass Column Speakers</span><span class="text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 px-1.5 py-0.5 rounded-full shrink-0">Popular</span></a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="flex items-center justify-between gap-2 py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all group/item"><span class="group-hover/item:font-medium">Live Mixing DJ Console</span><span class="text-[9px] font-bold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 px-1.5 py-0.5 rounded-full shrink-0">New</span></a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Active Dual Speakers</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Wireless Karaoke Mics</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Bluetooth Plug & Play</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Power Amplifiers</a></li>
                                    </ul>
                                </div>
                                <div class="space-y-3 pl-6">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Lighting & FX</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Sound-Active Strobes</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Heavy Smoke Machines</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Laser & Disco Pars</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">RGB Ambient Washes</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Club Lighting Stands</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Moving Beam Spots</a></li>
                                    </ul>
                                </div>
                                <div class="space-y-3 pl-6">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Venues</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Rooftop & Terrace Bash</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Living Room & Flat</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Farmhouse & Poolside</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Basement Club Setup</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Lawn & Garden Rig</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="block pt-1 font-bold text-[#FFD600] hover:underline">View All House Party →</a></li>
                                    </ul>
                                </div>
                            </div>
                            <a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="w-72 xl:w-80 self-stretch relative shrink-0 block border-l border-[#E6E2D8] dark:border-[#2E2E33] bg-gray-100 dark:bg-[#202024] overflow-hidden">
                                <img src="{{ asset('images/dropdowns/house-party.webp') }}" alt="House Party DJ Sound Rig" class="w-full h-full object-cover block" loading="lazy">
                                <span class="absolute top-4 left-4 bg-[#FFD600] text-[#171719] text-[9.5px] font-heading font-extrabold uppercase px-2.5 py-0.5 rounded-full shadow-md tracking-wider">Party Rigs</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 3. Proposals -->
                <div class="mega-nav-item group py-0.5 shrink-0">
                    <a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="flex items-center gap-1.5 py-1 hover:text-black dark:hover:text-white transition-colors {{ request('category') === 'cat-proposal-anniversary' ? 'text-black dark:text-white font-bold' : '' }}">
                        <i class="fa-regular fa-heart text-xs text-gray-500 dark:text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors"></i>
                        <span>Proposals</span>
                        <i class="fa-solid fa-chevron-down text-[7px] text-gray-400 group-hover:text-black dark:group-hover:text-white group-hover:rotate-180 transition-transform duration-200 ml-0.5"></i>
                    </a>
                    <!-- Mega Dropdown (Centered 90vw + GSAP Curtain Motion) -->
                    <div class="mega-dropdown-panel absolute left-1/2 -translate-x-1/2 top-full pt-3 z-50 pointer-events-none invisible before:content-[''] before:absolute before:-top-4 before:inset-x-0 before:h-6">
                        <div class="mega-dropdown-card w-[90vw] max-w-7xl bg-white dark:bg-[#18181B] border border-[#E6E2D8] dark:border-[#2E2E33] shadow-2xl rounded-2xl overflow-hidden text-left flex">
                            <div class="grid grid-cols-3 gap-6 flex-1 divide-x divide-gray-100 dark:divide-gray-800/80 p-6 xl:p-8">
                                <div class="space-y-3">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Romantic Setups</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="flex items-center justify-between gap-2 py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all group/item"><span class="group-hover/item:font-medium">'Marry Me' Neon Arch</span><span class="text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 px-1.5 py-0.5 rounded-full shrink-0">Top</span></a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Candlelight Pathway Trail</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Rose Petal Heart Carpet</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Fairy Light Cabana</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Floral Ring Cylinders</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Romantic Sunset Terrace</a></li>
                                    </ul>
                                </div>
                                <div class="space-y-3 pl-6">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Proposal Venues</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Private Rooftop Terrace</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Hotel Balcony Decor</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Garden & Lawn Cabana</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Farmhouse Romantic Trail</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Living Room Surprise</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Secret Outdoor Sunset Spot</a></li>
                                    </ul>
                                </div>
                                <div class="space-y-3 pl-6">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Surprise Add-ons</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="flex items-center justify-between gap-2 py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all group/item"><span class="group-hover/item:font-medium">Live Violinist Entry</span><span class="text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 px-1.5 py-0.5 rounded-full shrink-0">New</span></a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Cold Pyro Fireworks</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Surprise DSLR Shoot</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Champagne & Cake Table</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Giant Lighted Letters</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block pt-1 font-bold text-[#FFD600] hover:underline">View All Proposals →</a></li>
                                    </ul>
                                </div>
                            </div>
                            <a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="w-72 xl:w-80 self-stretch relative shrink-0 block border-l border-[#E6E2D8] dark:border-[#2E2E33] bg-gray-100 dark:bg-[#202024] overflow-hidden">
                                <img src="{{ asset('images/dropdowns/proposals.webp') }}" alt="Romantic Proposal Setup" class="w-full h-full object-cover block" loading="lazy">
                                <span class="absolute top-4 left-4 bg-[#FFD600] text-[#171719] text-[9.5px] font-heading font-extrabold uppercase px-2.5 py-0.5 rounded-full shadow-md tracking-wider">Romantic Special</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 4. Anniversaries -->
                <div class="mega-nav-item group py-0.5 shrink-0">
                    <a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="flex items-center gap-1.5 py-1 hover:text-black dark:hover:text-white transition-colors">
                        <i class="fa-regular fa-gem text-xs text-gray-500 dark:text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors"></i>
                        <span>Anniversaries</span>
                        <i class="fa-solid fa-chevron-down text-[7px] text-gray-400 group-hover:text-black dark:group-hover:text-white group-hover:rotate-180 transition-transform duration-200 ml-0.5"></i>
                    </a>
                    <!-- Mega Dropdown (Centered 90vw + GSAP Curtain Motion) -->
                    <div class="mega-dropdown-panel absolute left-1/2 -translate-x-1/2 top-full pt-3 z-50 pointer-events-none invisible before:content-[''] before:absolute before:-top-4 before:inset-x-0 before:h-6">
                        <div class="mega-dropdown-card w-[90vw] max-w-7xl bg-white dark:bg-[#18181B] border border-[#E6E2D8] dark:border-[#2E2E33] shadow-2xl rounded-2xl overflow-hidden text-left flex">
                            <div class="grid grid-cols-3 gap-6 flex-1 divide-x divide-gray-100 dark:divide-gray-800/80 p-6 xl:p-8">
                                <div class="space-y-3">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Milestones</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="flex items-center justify-between gap-2 py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all group/item"><span class="group-hover/item:font-medium">1st Paper Anniversary</span><span class="text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 px-1.5 py-0.5 rounded-full shrink-0">Top</span></a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">5th & 10th Milestones</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="flex items-center justify-between gap-2 py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all group/item"><span class="group-hover/item:font-medium">25th Silver Jubilee</span><span class="text-[9px] font-bold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 px-1.5 py-0.5 rounded-full shrink-0">Special</span></a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">50th Golden Jubilee</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Parents Anniversary</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Grand Floral Backdrop</a></li>
                                    </ul>
                                </div>
                                <div class="space-y-3 pl-6">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Romantic Decor</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Warm Fairy Lights</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Candlelight Dinner Table</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Floral Ring Backdrop</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Memory Photo Wall</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Neon Love Signs</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Red Carpet Walkway</a></li>
                                    </ul>
                                </div>
                                <div class="space-y-3 pl-6">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Music & Sound</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Live Acoustic Guitarist</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Bollywood Unplugged</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Dual Column Speakers</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Couple Photography</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Montage Projector</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="block pt-1 font-bold text-[#FFD600] hover:underline">View All Anniversaries →</a></li>
                                    </ul>
                                </div>
                            </div>
                            <a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="w-72 xl:w-80 self-stretch relative shrink-0 block border-l border-[#E6E2D8] dark:border-[#2E2E33] bg-gray-100 dark:bg-[#202024] overflow-hidden">
                                <img src="{{ asset('images/dropdowns/anniversaries.webp') }}" alt="Anniversary Celebration Decor" class="w-full h-full object-cover block" loading="lazy">
                                <span class="absolute top-4 left-4 bg-[#FFD600] text-[#171719] text-[9.5px] font-heading font-extrabold uppercase px-2.5 py-0.5 rounded-full shadow-md tracking-wider">Celebrate Love</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 5. Weddings & Sangeet -->
                <div class="mega-nav-item group py-0.5 shrink-0">
                    <a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="flex items-center gap-1.5 py-1 hover:text-black dark:hover:text-white transition-colors {{ request('category') === 'cat-weddings-sangeet' ? 'text-black dark:text-white font-bold' : '' }}">
                        <i class="fa-solid fa-ring text-xs text-gray-500 dark:text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors"></i>
                        <span>Weddings & Sangeet</span>
                        <i class="fa-solid fa-chevron-down text-[7px] text-gray-400 group-hover:text-black dark:group-hover:text-white group-hover:rotate-180 transition-transform duration-200 ml-0.5"></i>
                    </a>
                    <!-- Mega Dropdown (Centered 90vw + GSAP Curtain Motion) -->
                    <div class="mega-dropdown-panel absolute left-1/2 -translate-x-1/2 top-full pt-3 z-50 pointer-events-none invisible before:content-[''] before:absolute before:-top-4 before:inset-x-0 before:h-6">
                        <div class="mega-dropdown-card w-[90vw] max-w-7xl bg-white dark:bg-[#18181B] border border-[#E6E2D8] dark:border-[#2E2E33] shadow-2xl rounded-2xl overflow-hidden text-left flex">
                            <div class="grid grid-cols-3 gap-6 flex-1 divide-x divide-gray-100 dark:divide-gray-800/80 p-6 xl:p-8">
                                <div class="space-y-3">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Pre-Wedding Events</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="flex items-center justify-between gap-2 py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all group/item"><span class="group-hover/item:font-medium">Haldi Brass Urli Setup</span><span class="text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 px-1.5 py-0.5 rounded-full shrink-0">Top</span></a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Mehendi Colorful Drapes</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Sangeet DJ & Dance Stage</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Cocktail Club Lighting</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Ring Ceremony Stage</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Grand Entrance Arch</a></li>
                                    </ul>
                                </div>
                                <div class="space-y-3 pl-6">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Stage & Decor</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Marigold & Genda Decor</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Pastel Mandap Setups</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Bolsters & Diwan Seating</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">LED Par Light Ambient Washes</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Brass Props & Hanging Bells</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Royal Photobooths</a></li>
                                    </ul>
                                </div>
                                <div class="space-y-3 pl-6">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Music & FX</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="flex items-center justify-between gap-2 py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all group/item"><span class="group-hover/item:font-medium">High-Power DJ Rigs</span><span class="text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 px-1.5 py-0.5 rounded-full shrink-0">Top</span></a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Punjabi Dhol Players</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">LED Truss Stage Lighting</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Cold Pyro & Low-Fog Entry</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Audio Mixing Console Board</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="block pt-1 font-bold text-[#FFD600] hover:underline">View All Weddings →</a></li>
                                    </ul>
                                </div>
                            </div>
                            <a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="w-72 xl:w-80 self-stretch relative shrink-0 block border-l border-[#E6E2D8] dark:border-[#2E2E33] bg-gray-100 dark:bg-[#202024] overflow-hidden">
                                <img src="{{ asset('images/dropdowns/weddings-sangeet.webp') }}" alt="Weddings & Haldi Decor" class="w-full h-full object-cover block" loading="lazy">
                                <span class="absolute top-4 left-4 bg-[#FFD600] text-[#171719] text-[9.5px] font-heading font-extrabold uppercase px-2.5 py-0.5 rounded-full shadow-md tracking-wider">Royal Setup</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 6. Baby Shower & Kids -->
                <div class="mega-nav-item group py-0.5 shrink-0">
                    <a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="flex items-center gap-1.5 py-1 hover:text-black dark:hover:text-white transition-colors {{ request('category') === 'cat-kids-cozy' ? 'text-black dark:text-white font-bold' : '' }}">
                        <i class="fa-solid fa-child text-xs text-gray-500 dark:text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors"></i>
                        <span>Baby Shower & Kids</span>
                        <i class="fa-solid fa-chevron-down text-[7px] text-gray-400 group-hover:text-black dark:group-hover:text-white group-hover:rotate-180 transition-transform duration-200 ml-0.5"></i>
                    </a>
                    <!-- Mega Dropdown (Centered 90vw + GSAP Curtain Motion) -->
                    <div class="mega-dropdown-panel absolute left-1/2 -translate-x-1/2 top-full pt-3 z-50 pointer-events-none invisible before:content-[''] before:absolute before:-top-4 before:inset-x-0 before:h-6">
                        <div class="mega-dropdown-card w-[90vw] max-w-7xl bg-white dark:bg-[#18181B] border border-[#E6E2D8] dark:border-[#2E2E33] shadow-2xl rounded-2xl overflow-hidden text-left flex">
                            <div class="grid grid-cols-3 gap-6 flex-1 divide-x divide-gray-100 dark:divide-gray-800/80 p-6 xl:p-8">
                                <div class="space-y-3">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Baby Shower</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="flex items-center justify-between gap-2 py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all group/item"><span class="group-hover/item:font-medium">Pastel Balloon Arch</span><span class="text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 px-1.5 py-0.5 rounded-full shrink-0">Top</span></a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Gender Reveal Smoke & Props</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">'Oh Baby' Golden Neon Signs</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Mom-to-Be Throne Chair</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Teddy Bear Backdrop Setup</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Baby Shower Custom Cutouts</a></li>
                                    </ul>
                                </div>
                                <div class="space-y-3 pl-6">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Kids Themes</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="flex items-center justify-between gap-2 py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all group/item"><span class="group-hover/item:font-medium">Jungle Safari Wonderland</span><span class="text-[9px] font-bold bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300 px-1.5 py-0.5 rounded-full shrink-0">Popular</span></a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Space & Astronaut Adventure</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Princess Castle & Fairy Tale</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Dinosaur & Jurassic Theme</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Superhero Avengers Stage</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Cartoon & Cocomelon Styling</a></li>
                                    </ul>
                                </div>
                                <div class="space-y-3 pl-6">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Experiences</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Cozy Teepee Tent Village</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Kids Party PA Sound & Mic</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Magic Show & Game Host</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Balloon Sculptor & Painter</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Bubble Machine & Popcorn</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="block pt-1 font-bold text-[#FFD600] hover:underline">View All Kids Setups →</a></li>
                                    </ul>
                                </div>
                            </div>
                            <a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="w-72 xl:w-80 self-stretch relative shrink-0 block border-l border-[#E6E2D8] dark:border-[#2E2E33] bg-gray-100 dark:bg-[#202024] overflow-hidden">
                                <img src="{{ asset('images/dropdowns/baby-shower-kids.webp') }}" alt="Baby Shower & Kids Celebration" class="w-full h-full object-cover block" loading="lazy">
                                <span class="absolute top-4 left-4 bg-[#FFD600] text-[#171719] text-[9.5px] font-heading font-extrabold uppercase px-2.5 py-0.5 rounded-full shadow-md tracking-wider">Sweet Moments</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 7. Corporate Events -->
                <div class="mega-nav-item group py-0.5 shrink-0">
                    <a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="flex items-center gap-1.5 py-1 hover:text-black dark:hover:text-white transition-colors {{ request('category') === 'cat-baby-corporate' ? 'text-black dark:text-white font-bold' : '' }}">
                        <i class="fa-solid fa-briefcase text-xs text-gray-500 dark:text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors"></i>
                        <span>Corporate Events</span>
                        <i class="fa-solid fa-chevron-down text-[7px] text-gray-400 group-hover:text-black dark:group-hover:text-white group-hover:rotate-180 transition-transform duration-200 ml-0.5"></i>
                    </a>
                    <!-- Mega Dropdown (Centered 90vw + GSAP Curtain Motion) -->
                    <div class="mega-dropdown-panel absolute left-1/2 -translate-x-1/2 top-full pt-3 z-50 pointer-events-none invisible before:content-[''] before:absolute before:-top-4 before:inset-x-0 before:h-6">
                        <div class="mega-dropdown-card w-[90vw] max-w-7xl bg-white dark:bg-[#18181B] border border-[#E6E2D8] dark:border-[#2E2E33] shadow-2xl rounded-2xl overflow-hidden text-left flex">
                            <div class="grid grid-cols-3 gap-6 flex-1 divide-x divide-gray-100 dark:divide-gray-800/80 p-6 xl:p-8">
                                <div class="space-y-3">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Seminars & AV</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="flex items-center justify-between gap-2 py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all group/item"><span class="group-hover/item:font-medium">Dual PA Sound Columns</span><span class="text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 px-1.5 py-0.5 rounded-full shrink-0">Top</span></a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Podium & Wireless Lapel Mics</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Stage Presentation Backdrop</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">LED Projector & Screen</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Audio Mixer & Technician</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Registration Counter Desk</a></li>
                                    </ul>
                                </div>
                                <div class="space-y-3 pl-6">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Galas & Nights</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Annual Day & Awards Stage</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">VIP Red Carpet & Selfie Booth</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Corporate DJ & Dance Party</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Ambient Stage Light Washes</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Stage Truss & Follow Spotlight</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Product Launch Reveal Setup</a></li>
                                    </ul>
                                </div>
                                <div class="space-y-3 pl-6">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Office Events</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Festive Office Decor & Lights</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Team Farewell & Welcome Party</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Standup Comedy Open Mic</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Networking Cocktail Sound</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Custom Brand Backdrops</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="block pt-1 font-bold text-[#FFD600] hover:underline">View All Corporate →</a></li>
                                    </ul>
                                </div>
                            </div>
                            <a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="w-72 xl:w-80 self-stretch relative shrink-0 block border-l border-[#E6E2D8] dark:border-[#2E2E33] bg-gray-100 dark:bg-[#202024] overflow-hidden">
                                <img src="{{ asset('images/dropdowns/corporate-events.webp') }}" alt="Corporate Summit & Conference Stage" class="w-full h-full object-cover block" loading="lazy">
                                <span class="absolute top-4 left-4 bg-[#FFD600] text-[#171719] text-[9.5px] font-heading font-extrabold uppercase px-2.5 py-0.5 rounded-full shadow-md tracking-wider">Pro Audio & Stages</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 8. Live DJ & Acoustic -->
                <div class="mega-nav-item group py-0.5 shrink-0">
                    <a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="flex items-center gap-1.5 py-1 hover:text-black dark:hover:text-white transition-colors {{ request('category') === 'cat-dj-acoustic' ? 'text-black dark:text-white font-bold' : '' }}">
                        <i class="fa-solid fa-music text-xs text-gray-500 dark:text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors"></i>
                        <span>Live DJ & Acoustic</span>
                        <i class="fa-solid fa-chevron-down text-[7px] text-gray-400 group-hover:text-black dark:group-hover:text-white group-hover:rotate-180 transition-transform duration-200 ml-0.5"></i>
                    </a>
                    <!-- Mega Dropdown (Centered 90vw + GSAP Curtain Motion) -->
                    <div class="mega-dropdown-panel absolute left-1/2 -translate-x-1/2 top-full pt-3 z-50 pointer-events-none invisible before:content-[''] before:absolute before:-top-4 before:inset-x-0 before:h-6">
                        <div class="mega-dropdown-card w-[90vw] max-w-7xl bg-white dark:bg-[#18181B] border border-[#E6E2D8] dark:border-[#2E2E33] shadow-2xl rounded-2xl overflow-hidden text-left flex">
                            <div class="grid grid-cols-3 gap-6 flex-1 divide-x divide-gray-100 dark:divide-gray-800/80 p-6 xl:p-8">
                                <div class="space-y-3">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">DJ Consoles</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="flex items-center justify-between gap-2 py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all group/item"><span class="group-hover/item:font-medium">Live Mixing Club DJ</span><span class="text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 px-1.5 py-0.5 rounded-full shrink-0">Top</span></a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Pioneer DJ Console Setup</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Dual Bass Subwoofers</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Illuminated DJ Facade Booth</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Bollywood, Punjabi & EDM Set</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Private House Party DJ</a></li>
                                    </ul>
                                </div>
                                <div class="space-y-3 pl-6">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Acoustic Artists</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="flex items-center justify-between gap-2 py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all group/item"><span class="group-hover/item:font-medium">Singer & Acoustic Guitarist</span><span class="text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 px-1.5 py-0.5 rounded-full shrink-0">Top</span></a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Sufi & Bollywood Unplugged</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Live Violinist / Sax Entry</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">2-Piece Acoustic Duo Band</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Cocktail Dinner Music</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Vocal Mics & Stage Monitor</a></li>
                                    </ul>
                                </div>
                                <div class="space-y-3 pl-6">
                                    <div class="pb-2 border-b border-gray-100 dark:border-gray-800">
                                        <h6 class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-900 dark:text-white">Concert FX & Lights</h6>
                                    </div>
                                    <ul class="space-y-2 text-[12.5px] text-gray-600 dark:text-gray-300 font-normal">
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Heavy Fog & Dry Ice Effects</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Sound-Sync Strobe & Pars</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Stage Truss & Beam Sharpys</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Cold Pyro Firework Fountains</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block py-0.5 hover:text-black dark:hover:text-white hover:translate-x-0.5 transition-all">Atmospheric Laser Show</a></li>
                                        <li><a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="block pt-1 font-bold text-[#FFD600] hover:underline">View All Music →</a></li>
                                    </ul>
                                </div>
                            </div>
                            <a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" class="w-72 xl:w-80 self-stretch relative shrink-0 block border-l border-[#E6E2D8] dark:border-[#2E2E33] bg-gray-100 dark:bg-[#202024] overflow-hidden">
                                <img src="{{ asset('images/dropdowns/live-dj-acoustic.webp') }}" alt="Live Acoustic & DJ Artists" class="w-full h-full object-cover block" loading="lazy">
                                <span class="absolute top-4 left-4 bg-[#FFD600] text-[#171719] text-[9.5px] font-heading font-extrabold uppercase px-2.5 py-0.5 rounded-full shadow-md tracking-wider">Live Artists</span>
                            </a>
                        </div>
                    </div>
                </div>

            </nav>
        </div>
    </div>
</header>

<!-- Mobile Drawer Slide-in Navigation Menu -->
<div id="mobile-nav"
    class="fixed inset-y-0 right-0 z-[100] w-full max-w-[320px] bg-white dark:bg-[#171719] text-[#171719] dark:text-white p-6 flex flex-col justify-between transform translate-x-full transition-transform duration-300 lg:hidden border-l border-[#E6E2D8] dark:border-[#292929] shadow-2xl">
    <div>
        <!-- Drawer Header -->
        <div class="flex items-center justify-between pb-4 border-b border-[#E6E2D8] dark:border-[#292929]">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('assets/images/logo/artizen.png') }}" alt="Artizen Logo" class="h-11 w-auto object-contain rounded-md">
            </a>
            <button onclick="toggleMobileNav()"
                class="w-8 h-8 border border-[#E6E2D8] dark:border-[#292929] rounded-full hover:border-gray-400 flex items-center justify-center transition-colors cursor-pointer text-[#171719] dark:text-white" aria-label="Close menu">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- City Indicator -->
        <div class="mt-4 p-2.5 rounded-xl bg-[#FAF9F6] dark:bg-[#202024] border border-[#E6E2D8] dark:border-[#292929] flex items-center justify-between text-xs">
            <span class="flex items-center gap-1.5 font-bold text-[#171719] dark:text-gray-200">
                <i class="fa-solid fa-location-dot text-gray-700 dark:text-gray-300"></i>
                Indore, MP
            </span>
            <span class="text-[10px] text-gray-500 dark:text-gray-400 font-semibold">
                Active Service
            </span>
        </div>

        <!-- Mobile Search with Clear Icon & Autocomplete -->
        <div class="relative mt-4" id="mobile-search-container">
            <form action="{{ route('events.index') }}" method="GET" class="relative">
                <div class="relative flex items-center">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" 
                           id="mobile-search-input"
                           name="q" 
                           value="{{ request('q', '') }}"
                           autocomplete="off"
                           placeholder="Search setups, decor, DJ, weddings..." 
                           class="w-full pl-9 pr-9 py-2.5 bg-[#FAF9F6] dark:bg-[#242428] border border-[#E6E2D8] dark:border-[#333338] rounded-xl text-xs font-medium text-[#171719] dark:text-white placeholder-gray-400 outline-none focus:border-[#171719] dark:focus:border-white/40 transition-all">
                    
                    <button type="button" 
                            id="mobile-search-clear" 
                            onclick="clearMobileSearch()" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#171719] dark:hover:text-white transition-colors cursor-pointer {{ request('q') ? '' : 'hidden' }} p-0.5"
                            aria-label="Clear search">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>
            </form>

            <!-- Mobile Live Search Suggestions Dropdown -->
            <div id="mobile-search-suggestions" 
                 class="absolute left-0 top-full mt-1.5 w-full bg-white dark:bg-[#171719] border border-[#E6E2D8] dark:border-[#2E2E33] shadow-2xl rounded-xl p-2 z-50 hidden max-h-[300px] overflow-y-auto overscroll-contain text-left">
                <div id="mobile-suggestions-content"></div>
            </div>
        </div>

        <!-- Category Links (Icons, No Emojis) -->
        <div class="mt-5">
            <span class="text-[10px] font-heading font-extrabold uppercase tracking-widest text-gray-400 dark:text-gray-500 block mb-2 px-1">Browse Categories</span>
            <nav class="flex flex-col gap-1 font-heading font-semibold text-xs tracking-tight text-left">
                <a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" onclick="toggleMobileNav()"
                    class="py-2 px-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-cake-candles text-xs text-gray-400"></i>
                        <span>Birthdays</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                </a>
                <a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" onclick="toggleMobileNav()"
                    class="py-2 px-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-volume-high text-xs text-gray-400"></i>
                        <span>House Party & DJ</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                </a>
                <a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" onclick="toggleMobileNav()"
                    class="py-2 px-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-heart text-xs text-gray-400"></i>
                        <span>Proposals</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                </a>
                <a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" onclick="toggleMobileNav()"
                    class="py-2 px-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-ring text-xs text-gray-400"></i>
                        <span>Wedding & Sangeet</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                </a>
                <a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" onclick="toggleMobileNav()"
                    class="py-2 px-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-baby text-xs text-gray-400"></i>
                        <span>Baby Shower & Kids</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                </a>
                <a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" onclick="toggleMobileNav()"
                    class="py-2 px-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-briefcase text-xs text-gray-400"></i>
                        <span>Corporate Events</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                </a>
                <a href="{{ route('events.index', ['category' => 'cat-dj-acoustic']) }}" onclick="toggleMobileNav()"
                    class="py-2 px-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-music text-xs text-gray-400"></i>
                        <span>Live DJ & Acoustic</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                </a>
            </nav>
        </div>

        <!-- Main Pages Links -->
        <div class="mt-4 pt-3 border-t border-[#E6E2D8] dark:border-[#292929]">
            <span class="text-[10px] font-heading font-extrabold uppercase tracking-widest text-gray-400 dark:text-gray-500 block mb-2 px-1">Navigation</span>
            <nav class="flex flex-col gap-1 font-heading font-semibold text-xs tracking-tight text-left">
                <a href="{{ route('home') }}" onclick="toggleMobileNav()"
                    class="py-2 px-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white">Home</a>
                <a href="{{ route('events.index') }}" onclick="toggleMobileNav()"
                    class="py-2 px-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white">All Packages</a>
                <a href="{{ route('gallery.index') }}" onclick="toggleMobileNav()"
                    class="py-2 px-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white">Gallery</a>
                <a href="{{ route('about-us') }}" onclick="toggleMobileNav()"
                    class="py-2 px-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white">About Artizen</a>
                <a href="{{ route('events.index') }}" onclick="toggleMobileNav()"
                    class="py-2 px-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center gap-1.5">
                    <i class="fa-regular fa-heart text-[11px] text-gray-500 dark:text-gray-400"></i>
                    <span>Wishlist / Saved</span>
                </a>
                <a href="{{ route('booking.index') }}" onclick="toggleMobileNav()"
                    class="py-2 px-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center gap-1.5">
                    <i class="fa-solid fa-bag-shopping text-[11px] text-gray-500 dark:text-gray-400"></i>
                    <span>Booking Cart & Requests</span>
                </a>
                <a href="{{ route('booking.track') }}" onclick="toggleMobileNav()"
                    class="py-2 px-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center gap-1.5">
                    <i class="fa-solid fa-truck-fast text-[11px] text-gray-500 dark:text-gray-400"></i>
                    <span>Track Booking Status</span>
                </a>
                <a href="{{ route('admin.login') }}" onclick="toggleMobileNav()"
                    class="py-2 px-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center gap-1.5">
                    <i class="fa-regular fa-user text-[11px] text-gray-500 dark:text-gray-400"></i>
                    <span>Admin / Account Login</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Mobile Drawer Bottom Book CTA -->
    <div class="flex flex-col gap-2 pt-4 border-t border-[#E6E2D8] dark:border-[#292929] mt-auto">
        <a href="{{ route('booking.index') }}" onclick="toggleMobileNav()"
            class="w-full flex items-center justify-center gap-2 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] py-3 rounded-xl font-heading font-extrabold text-xs uppercase tracking-wider shadow-xs text-center transition-colors">
            <i class="fa-solid fa-calendar-check"></i>
            <span>Book Event Setup</span>
        </a>
    </div>
</div>

<!-- Mobile Drawer Overlay Backdrop -->
<div id="mobile-nav-overlay" onclick="toggleMobileNav()"
    class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[90] hidden transition-opacity duration-300 opacity-0 pointer-events-none lg:hidden">
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
            document.body.classList.add('overflow-hidden');
        }

        function closeDropdown() {
            dropdown.classList.add('hidden');
            const anyOpen = document.querySelectorAll('#header-search-suggestions:not(.hidden), #mobile-search-suggestions:not(.hidden)').length > 0;
            if (!anyOpen) {
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
                item.category.toLowerCase().includes(q)
            ).slice(0, 8);

            if (matches.length === 0) {
                content.innerHTML = `
                    <div class="py-5 px-4 text-center text-xs text-gray-500 dark:text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-gray-300 dark:text-gray-600 text-lg mb-2 block"></i>
                        <p class="font-semibold text-gray-800 dark:text-gray-200 text-xs">No matching setups found</p>
                        <p class="text-[11px] mt-0.5 text-gray-400">Try searching "Birthday", "House Party", "DJ", "Wedding"</p>
                    </div>
                `;
            } else {
                let html = `
                    <div class="flex items-center justify-between px-2.5 pt-1 pb-2 border-b border-gray-100 dark:border-white/5 mb-2">
                        <span class="text-[10px] font-heading font-extrabold uppercase tracking-wider text-gray-400 dark:text-gray-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-sparkles text-[9px] text-[#FFD600]"></i> Suggested Setups (${matches.length})
                        </span>
                        <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-white/10 px-2 py-0.5 rounded-full">Indore</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                `;
                matches.forEach(item => {
                    html += `
                        <a href="${item.url}" class="flex items-center gap-2.5 p-2 rounded-xl bg-gray-50/80 hover:bg-gray-100 dark:bg-white/5 dark:hover:bg-white/10 border border-black/5 dark:border-white/5 hover:border-[#FFD600]/30 transition-all group/item cursor-pointer text-left min-w-0">
                            <img src="${item.image}" alt="${item.title}" class="w-10 h-10 rounded-lg object-cover bg-gray-200 dark:bg-gray-800 shrink-0 border border-black/5 dark:border-white/10 shadow-2xs">
                            <div class="min-w-0 flex-1">
                                <h6 class="text-xs font-semibold text-gray-900 dark:text-gray-100 truncate group-hover/item:text-black dark:group-hover/item:text-[#FFD600] transition-colors">${item.title}</h6>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate mt-0.5">${item.category}</p>
                            </div>
                        </a>
                    `;
                });
                html += `
                    </div>
                    <div class="pt-2 mt-2 border-t border-gray-100 dark:border-white/5">
                        <a href="{{ route('events.index') }}?q=${encodeURIComponent(query)}" class="flex items-center justify-between px-3 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-[11px] font-medium text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-white transition-colors">
                            <span>Search all setups for "<strong>${query}</strong>"</span>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Press Enter ↵</span>
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

    function clearMobileSearch() {
        const input = document.getElementById('mobile-search-input');
        const clearBtn = document.getElementById('mobile-search-clear');
        const dropdown = document.getElementById('mobile-search-suggestions');
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

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Search Autocompletes
        initSearchAutocomplete('header-search-input', 'header-search-clear', 'header-search-suggestions', 'header-suggestions-content');
        initSearchAutocomplete('mobile-search-input', 'mobile-search-clear', 'mobile-search-suggestions', 'mobile-suggestions-content');

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
</script>
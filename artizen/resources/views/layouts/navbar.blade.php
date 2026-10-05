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

                <!-- Theme Switcher Button (Single Icon: Moon in Light, Sun in Dark) -->
                <button type="button"
                        id="theme-toggle-btn"
                        onclick="toggleTheme()" 
                        class="w-9 h-9 sm:w-9.5 sm:h-9.5 rounded-full border border-[#E6E2D8] dark:border-white/10 hover:border-gray-400 dark:hover:border-white/30 bg-[#FAF9F6] dark:bg-[#1E1E24] hover:bg-black/5 dark:hover:bg-white/10 transition-all flex items-center justify-center cursor-pointer shadow-2xs group active:scale-95"
                        title="Switch Dark / Light Theme"
                        aria-label="Toggle Dark/Light Mode">
                    <i id="theme-toggle-icon" class="fa-solid fa-moon text-xs text-gray-700 transition-all duration-300 group-hover:scale-110"></i>
                </button>

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
                                            <img src="{{ asset($cat->dropdown_image) }}" alt="{{ $cat->title }}" class="w-full h-full object-cover block" loading="lazy">
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
    class="fixed inset-y-0 right-0 z-[100] w-full max-w-[320px] bg-white dark:bg-[#171719] text-[#171719] dark:text-white p-6 flex flex-col justify-between transform translate-x-full transition-all duration-300 lg:hidden border-l border-[#E6E2D8] dark:border-[#292929] invisible pointer-events-none hidden">
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
                           class="w-full pl-9 pr-9 py-2.5 bg-[#FAF9F6] dark:bg-[#242428] border border-[#E6E2D8] dark:border-[#333338] rounded-xl text-xs font-medium text-[#171719] dark:text-white placeholder-gray-400 outline-none focus:border-[#171719] dark:focus:border-white/40 shadow-none ring-0 focus:ring-0">
                    
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
                @foreach($navCategories as $cat)
                    <a href="{{ route('events.index', ['category' => $cat->nav_slug]) }}" onclick="toggleMobileNav()"
                        class="py-2 px-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <i class="{{ $cat->icon ?: 'fa-solid fa-cake-candles' }} text-xs text-gray-400"></i>
                            <span>{{ $cat->title }}</span>
                        </span>
                        <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                    </a>
                @endforeach
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
                <button type="button" onclick="toggleTheme()"
                    class="py-2 px-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center justify-between w-full text-left font-heading font-semibold text-xs tracking-tight">
                    <span class="flex items-center gap-2">
                        <i id="mobile-theme-icon" class="fa-solid fa-moon text-xs text-gray-400"></i>
                        <span id="mobile-theme-text">Switch Theme</span>
                    </span>
                    <span class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Theme</span>
                </button>
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
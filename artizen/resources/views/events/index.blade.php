@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-8 pb-12 px-4 md:px-12 bg-main-bg text-main-text relative">
    <div class="w-full">
        <style>
            @keyframes text-slide {
                0%, 20% { transform: translateY(0); }
                25%, 45% { transform: translateY(-24px); }
                50%, 70% { transform: translateY(-48px); }
                75%, 95% { transform: translateY(-72px); }
                100% { transform: translateY(0); }
            }
            .animate-text-slide {
                animation: text-slide 12s infinite cubic-bezier(0.645, 0.045, 0.355, 1);
            }
        </style>

        <!-- New Compact Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-[#EAEAEA] dark:border-white/5 pb-6 mb-10 gap-4 text-left">
            <div>
                <span class="text-[9px] font-heading font-bold uppercase tracking-widest text-gold mb-1 block">ARTIZEN CATALOG</span>
                <h1 class="font-heading font-extrabold text-2xl md:text-4xl uppercase tracking-tight text-black dark:text-white leading-none">
                    {{ (!empty($selectedCategory) && $selectedCategory !== 'all') ? $selectedCategoryName : 'Explore Packages' }}
                </h1>
            </div>
            
            <!-- Right Side Animated Text Block -->
            <div class="flex items-center gap-3 bg-white dark:bg-[#121212] border border-[#EAEAEA] dark:border-white/10 rounded-2xl py-2.5 px-4 shadow-sm max-w-md w-full md:w-auto">
                <span class="flex h-2.5 w-2.5 relative shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gold opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-gold"></span>
                </span>
                <div class="text-left font-heading text-[10px] font-bold uppercase tracking-wider text-black dark:text-white relative overflow-hidden h-6 min-w-[260px] md:min-w-[300px]">
                    <div class="absolute inset-0 flex flex-col gap-0 animate-text-slide leading-6">
                        <span class="text-gold">Creating Celebration Vibes</span>
                        <span>We don't just plan, we share feelings</span>
                        <span class="text-gold">Delivering Happy Movements in Indore</span>
                        <span>Reimagined Easy Event Bookings</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Responsive Grid Layout (2 Columns on Desktop) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left Side: Fixed/Sticky Dropdown Filters Panel (Super Compact Desktop / Slide-up Drawer Mobile) -->
            <div id="filter-container" class="lg:col-span-3 lg:sticky lg:top-44 z-40 w-full max-lg:fixed max-lg:inset-0 max-lg:bg-black/60 max-lg:backdrop-blur-sm max-lg:hidden max-lg:flex max-lg:items-end max-lg:justify-center transition-all duration-300">
                <div class="bg-card-bg border border-primary-border rounded-2xl lg:p-5 max-lg:p-6 shadow-sm flex flex-col gap-4 w-full max-lg:rounded-t-3xl max-lg:max-h-[80vh] max-lg:overflow-y-auto max-lg:shadow-2xl">
                    <!-- Mobile Drawer Header -->
                    <div class="flex items-center justify-between lg:hidden border-b border-primary-border pb-3">
                        <h3 class="font-heading font-extrabold text-sm uppercase tracking-wider text-main-text">Filters & Sort</h3>
                        <button onclick="toggleMobileFilters(false)" class="text-main-text hover:text-gold cursor-pointer focus:outline-none bg-transparent border-0" aria-label="Close filters">
                            <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
                        </button>
                    </div>
                    
                    <!-- Search Input -->
                    <div>
                        <label for="catalog-search" class="block text-[9px] font-heading font-bold uppercase tracking-wider text-muted-text mb-1.5">
                            Search Package
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-magnifying-glass text-xs" aria-hidden="true"></i>
                            </span>
                            <input type="text" id="catalog-search" placeholder="Type keywords..."
                                class="w-full bg-card-bg border border-primary-border pl-9 pr-3 py-2 text-xs rounded-xl font-body text-main-text placeholder-gray-400 focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold transition-all">
                        </div>
                    </div>

                    <!-- Sort Selection -->
                    <div class="border-t border-primary-border pt-3">
                        <label class="block text-[9px] font-heading font-bold uppercase tracking-wider text-muted-text mb-1.5">
                            Sort Results
                        </label>
                        <div class="relative custom-dropdown" id="dropdown-sort">
                            <button type="button" class="w-full bg-card-bg border border-primary-border pl-3 pr-8 py-2 text-xs rounded-xl font-heading font-semibold text-main-text text-left flex justify-between items-center focus:outline-none focus:border-gold transition-all cursor-pointer dropdown-toggle">
                                <span class="selected-text">Recommended</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-muted-text transition-transform duration-200 chevron" aria-hidden="true"></i>
                            </button>
                            <div class="dropdown-menu absolute left-0 right-0 mt-1.5 z-30 bg-card-bg border border-primary-border rounded-xl shadow-lg hidden py-1.5 transition-all">
                                <div class="dropdown-item px-3 py-2 text-xs font-heading font-semibold text-main-text hover:bg-gold hover:text-white cursor-pointer transition-colors rounded-lg mx-1" data-value="default">Recommended</div>
                                <div class="dropdown-item px-3 py-2 text-xs font-heading font-semibold text-main-text hover:bg-gold hover:text-white cursor-pointer transition-colors rounded-lg mx-1" data-value="price-low">Price: Low to High</div>
                                <div class="dropdown-item px-3 py-2 text-xs font-heading font-semibold text-main-text hover:bg-gold hover:text-white cursor-pointer transition-colors rounded-lg mx-1" data-value="price-high">Price: High to Low</div>
                                <div class="dropdown-item px-3 py-2 text-xs font-heading font-semibold text-main-text hover:bg-gold hover:text-white cursor-pointer transition-colors rounded-lg mx-1" data-value="rating">Rating: High to Low</div>
                            </div>
                            <input type="hidden" id="catalog-sort" value="default">
                        </div>
                    </div>

                    <!-- Budget Filter Dropdown -->
                    <div class="border-t border-primary-border pt-3">
                        <label class="block text-[9px] font-heading font-bold uppercase tracking-wider text-muted-text mb-1.5">
                            Filter by Budget
                        </label>
                        <div class="relative custom-dropdown" id="dropdown-budget">
                            <button type="button" class="w-full bg-card-bg border border-primary-border pl-3 pr-8 py-2 text-xs rounded-xl font-heading font-semibold text-main-text text-left flex justify-between items-center focus:outline-none focus:border-gold transition-all cursor-pointer dropdown-toggle">
                                <span class="selected-text">All Prices</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-muted-text transition-transform duration-200 chevron" aria-hidden="true"></i>
                            </button>
                            <div class="dropdown-menu absolute left-0 right-0 mt-1.5 z-30 bg-card-bg border border-primary-border rounded-xl shadow-lg hidden py-1.5 transition-all">
                                <div class="dropdown-item px-3 py-2 text-xs font-heading font-semibold text-main-text hover:bg-gold hover:text-white cursor-pointer transition-colors rounded-lg mx-1" data-value="all">All Prices</div>
                                <div class="dropdown-item px-3 py-2 text-xs font-heading font-semibold text-main-text hover:bg-gold hover:text-white cursor-pointer transition-colors rounded-lg mx-1" data-value="under-8k">Under ₹8,000</div>
                                <div class="dropdown-item px-3 py-2 text-xs font-heading font-semibold text-main-text hover:bg-gold hover:text-white cursor-pointer transition-colors rounded-lg mx-1" data-value="8k-15k">₹8,000 - ₹15,000</div>
                                <div class="dropdown-item px-3 py-2 text-xs font-heading font-semibold text-main-text hover:bg-gold hover:text-white cursor-pointer transition-colors rounded-lg mx-1" data-value="15k-30k">₹15,000 - ₹30,000</div>
                                <div class="dropdown-item px-3 py-2 text-xs font-heading font-semibold text-main-text hover:bg-gold hover:text-white cursor-pointer transition-colors rounded-lg mx-1" data-value="above-30k">₹30,000+</div>
                            </div>
                            <input type="hidden" id="catalog-budget" value="all">
                        </div>
                    </div>

                    <!-- Category Filter Dropdown -->
                    <div class="border-t border-primary-border pt-3">
                        <label class="block text-[9px] font-heading font-bold uppercase tracking-wider text-muted-text mb-1.5">
                            Filter by Category
                        </label>
                        <div class="relative custom-dropdown" id="dropdown-category">
                            <button type="button" class="w-full bg-card-bg border border-primary-border pl-3 pr-8 py-2 text-xs rounded-xl font-heading font-semibold text-main-text text-left flex justify-between items-center focus:outline-none focus:border-gold transition-all cursor-pointer dropdown-toggle">
                                <span class="selected-text">{{ $selectedCategoryName ?? 'All Categories' }}</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-muted-text transition-transform duration-200 chevron" aria-hidden="true"></i>
                            </button>
                            <div class="dropdown-menu absolute left-0 right-0 mt-1.5 z-30 bg-card-bg border border-primary-border rounded-xl shadow-lg hidden max-h-60 overflow-y-auto py-1.5 transition-all">
                                <div class="dropdown-item px-3 py-2 text-xs font-heading font-semibold text-main-text hover:bg-gold hover:text-white cursor-pointer transition-colors rounded-lg mx-1" data-value="all">All Categories</div>
                                @foreach($categories as $id => $name)
                                    <div class="dropdown-item px-3 py-2 text-xs font-heading font-semibold text-main-text hover:bg-gold hover:text-white cursor-pointer transition-colors rounded-lg mx-1" data-value="{{ $id }}">{{ $name }}</div>
                                @endforeach
                            </div>
                            <input type="hidden" id="catalog-category" value="{{ $selectedCategory ?? 'all' }}">
                        </div>
                    </div>

                    <!-- Reset Filters Button -->
                    <button onclick="resetAllFilters()" class="w-full bg-main-text text-main-bg border border-primary-border hover:bg-gold hover:text-white transition-all py-2.5 text-[10px] font-heading font-bold uppercase tracking-wider rounded-xl transition-all duration-200 mt-1 cursor-pointer">
                        Reset Filters
                    </button>
                </div>
            </div>

            <!-- Right Side: Packages Grid -->
            <div class="lg:col-span-9 w-full">
                <!-- No Packages Fallback -->
                <div id="no-packages-found" class="hidden text-center py-20 bg-white dark:bg-[#121212] border border-[#EAEAEA] dark:border-white/10 rounded-2xl shadow-sm mb-12">
                    <i class="fa-regular fa-face-frown text-5xl mx-auto text-gray-300 dark:text-white/20 mb-4 block" aria-hidden="true"></i>
                    <h3 class="font-heading font-bold text-lg uppercase tracking-wider text-black dark:text-white">No Packages Match</h3>
                    <p class="font-body text-xs text-muted-text mt-2">Try adjusting your filters, search keyword or category choices.</p>
                </div>

                <!-- Catalog Grid -->
                <div id="packages-grid" class="grid grid-cols-2 lg:grid-cols-3 gap-3 md:gap-6 items-start">
                    @php
                        $dividerShown = false;
                        $hasActiveCategory = (!empty($selectedCategory) && $selectedCategory !== 'all');
                        $cleanSel = $hasActiveCategory ? str_replace('cat-', '', $selectedCategory) : '';
                    @endphp
                    @foreach($packages as $package)
                        @php
                            $cardCat = $package['category_id'] ?? '';
                            $cleanCardCat = str_replace('cat-', '', $cardCat);
                            $isMatchingCategory = $hasActiveCategory 
                                ? ($cardCat === $selectedCategory || $cleanCardCat === $cleanSel || str_contains($cleanCardCat, $cleanSel) || str_contains($cleanSel, $cleanCardCat))
                                : true;
                        @endphp

                        @if(!$dividerShown && $hasActiveCategory && !$isMatchingCategory)
                            @php $dividerShown = true; @endphp
                            <div id="other-packages-divider" class="col-span-2 lg:col-span-3 pt-6 pb-2 border-t border-[#EAEAEA] dark:border-white/10 mt-4 mb-2 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-heading font-extrabold text-[#EA741D] uppercase tracking-widest block mb-0.5">Explore More</span>
                                    <h4 class="text-sm md:text-base font-heading font-extrabold text-black dark:text-white uppercase tracking-wider">Other Celebration Packages</h4>
                                </div>
                                <span class="text-[11px] text-muted-text font-body hidden sm:inline-block">Browse all celebration packages</span>
                            </div>
                        @endif

                        <div class="experience-card relative flex flex-col !h-auto bg-white dark:bg-[#161615] rounded-sm overflow-hidden border border-[#EAEAEA] dark:border-white/5 shadow-sm"
                            data-id="{{ $package['id'] }}"
                            data-event-id="{{ $package['event_id'] }}"
                            data-tier-index="{{ $package['tier_index'] }}"
                            data-category="{{ $package['category_id'] }}"
                            data-price="{{ $package['price'] }}"
                            data-rating="{{ $package['rating'] }}"
                            data-title="{{ $package['title'] }}"
                            data-desc="{{ $package['desc'] }}">
                            
                            <!-- Card Header Image -->
                            <div class="experience-card-img-container relative h-28 md:h-40 w-full overflow-hidden bg-gray-100 rounded-sm" data-card-images='@json($package['all_images'] ?? [$package['image']])'>
                                <img src="{{ $package['image'] }}" alt="{{ $package['title'] }}" class="w-full h-full object-cover primary-img" loading="lazy">
                                @if(!empty($package['secondary_image']) && $package['secondary_image'] !== $package['image'])
                                    <img src="{{ $package['secondary_image'] }}" alt="{{ $package['title'] }}" class="w-full h-full object-cover secondary-img" loading="lazy">
                                @endif
                                <div class="experience-card-badge absolute top-2.5 left-2.5 bg-black/70 backdrop-blur-md text-[8px] md:text-[10px] font-heading font-extrabold uppercase tracking-wider py-0.5 md:py-1 px-2 md:px-3.5 rounded-md border border-white/10">
                                    {{ $package['badge'] }}
                                </div>
                                @if(!empty($package['all_images']) && count($package['all_images']) > 1)
                                    <div class="card-img-indicators">
                                        @foreach($package['all_images'] as $i => $img)
                                            <span class="card-img-dot {{ $i === 0 ? 'active' : '' }}"></span>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <!-- Sleek Compare Toggle Checkbox -->
                                <label class="absolute top-2.5 right-2.5 z-10 flex items-center gap-1 bg-black/70 backdrop-blur-md border border-white/10 text-white rounded-md py-1 px-2 cursor-pointer hover:bg-black/90 transition-colors" onclick="event.stopPropagation();">
                                    <input type="checkbox" class="compare-checkbox hidden" 
                                        data-id="{{ $package['id'] }}" 
                                        data-title="{{ $package['title'] }}" 
                                        data-price="{{ $package['price'] }}" 
                                        data-image="{{ $package['image'] }}" 
                                        data-rating="{{ $package['rating'] }}" 
                                        data-inclusions='@json($package['inclusions'])' 
                                        data-url="javascript:openEventLightbox({{ $package['event_id'] }}, {{ $package['tier_index'] }})"
                                        onchange="toggleComparePackage(this)">
                                    <span class="compare-label text-[8px] md:text-[9px] font-heading font-extrabold uppercase tracking-wider select-none text-gray-300">Compare</span>
                                    <i class="fa-solid fa-code-compare text-[10px] compare-icon text-gray-400 transition-colors" aria-hidden="true"></i>
                                </label>
                            </div>

                            <!-- Card Body -->
                            <div class="p-3 md:p-4 flex flex-col flex-grow text-left">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <h4 class="font-heading font-extrabold text-xs md:text-sm uppercase text-black dark:text-white tracking-wide line-clamp-1 flex-1 mb-0">
                                        {{ $package['title'] }}
                                    </h4>
                                    <div class="flex items-center gap-1 text-[10px] md:text-[11px] font-heading font-extrabold text-gold shrink-0 pt-0.5">
                                        <i class="fa-solid fa-star text-gold text-[10px]" aria-hidden="true"></i>
                                        <span>{{ $package['rating'] }}</span>
                                    </div>
                                </div>
                                <p class="font-body text-[10px] md:text-xs text-muted-text leading-relaxed mb-2.5 line-clamp-2">
                                    {{ \Illuminate\Support\Str::limit($package['desc'], 85) }}
                                </p>

                                <!-- Inclusions Toggle Accordion -->
                                <div class="border-t border-[#EAEAEA] dark:border-white/5 py-2 md:py-3">
                                    <button onclick="toggleInclusions(this)" class="flex items-center justify-between w-full text-left text-xs font-heading font-bold uppercase tracking-wider text-black dark:text-white hover:text-gold transition-colors focus:outline-none">
                                        <span>What's Included</span>
                                        <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300 transform" aria-hidden="true"></i>
                                    </button>
                                    <div class="inclusions-panel hidden mt-2">
                                        <ul class="space-y-1.5">
                                            @foreach($package['inclusions'] as $inclusion)
                                                <li class="flex items-start gap-2 text-xs text-gray-500">
                                                    <i class="fa-solid fa-check text-gold text-xs shrink-0 mt-0.5" aria-hidden="true"></i>
                                                    <span class="font-body">{{ $inclusion }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <!-- Card Footer -->
                                <div class="border-t border-[#EAEAEA] dark:border-white/5 pt-3 mt-auto flex items-center justify-between gap-1.5">
                                    <div class="flex flex-col">
                                        <span class="text-[8px] uppercase tracking-wider text-gray-400">Price</span>
                                        <span class="font-heading font-extrabold text-xs md:text-sm lg:text-base text-black dark:text-white">₹{{ number_format($package['price']) }}</span>
                                    </div>
                                    <span onclick="openEventLightbox({{ $package['event_id'] }}, {{ $package['tier_index'] }})" class="experience-card-btn cursor-pointer !text-[9px] md:!text-xs !py-1 md:!py-1.5 !px-2 md:!px-3.5 flex items-center gap-1">
                                        Book
                                        <i class="fa-solid fa-chevron-right text-[9px]" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if(!$dividerShown)
                        <div id="other-packages-divider" class="hidden col-span-2 lg:col-span-3 pt-6 pb-2 border-t border-[#EAEAEA] dark:border-white/10 mt-4 mb-2 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-heading font-extrabold text-[#EA741D] uppercase tracking-widest block mb-0.5">Explore More</span>
                                <h4 class="text-sm md:text-base font-heading font-extrabold text-black dark:text-white uppercase tracking-wider">Other Celebration Packages</h4>
                            </div>
                            <span class="text-[11px] text-muted-text font-body hidden sm:inline-block">Browse all celebration packages</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Floating Mobile Filters Trigger Button -->
            <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 lg:hidden">
                <button onclick="toggleMobileFilters(true)" class="flex items-center gap-2 bg-main-text text-main-bg px-6 py-3 rounded-full font-heading text-xs font-bold uppercase tracking-widest shadow-2xl border border-primary-border cursor-pointer transition-transform active:scale-95">
                    <i class="fa-solid fa-sliders text-gold text-xs" aria-hidden="true"></i>
                    Filter & Sort
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Floating Compare Bar -->
<div id="compare-bar" class="fixed bottom-20 md:bottom-6 left-1/2 -translate-x-1/2 z-40 bg-black/85 backdrop-blur-md border border-white/10 rounded-2xl py-3 px-4 md:px-6 shadow-2xl flex items-center justify-between gap-4 md:gap-8 w-[92%] max-w-xl transition-all duration-500 transform translate-y-28 opacity-0 hidden">
    <div class="flex items-center gap-3">
        <span class="bg-[#EA741D] text-white font-heading font-extrabold text-[10px] md:text-xs uppercase tracking-wider py-1 px-2.5 rounded-lg shadow-md" id="compare-count-badge">
            Compare (0/3)
        </span>
        <!-- Selected thumbnails container -->
        <div class="flex items-center -space-x-2" id="compare-thumbnails">
            <!-- Thumbnail bubbles will be injected here by JS -->
        </div>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="clearCompare()" class="text-[10px] md:text-xs font-heading font-bold uppercase tracking-wider text-gray-400 hover:text-white transition-colors cursor-pointer bg-transparent border-0 focus:outline-none">
            Clear
        </button>
        <button onclick="openCompareModal()" class="bg-[#EA741D] text-white font-heading font-bold text-[10px] md:text-xs uppercase tracking-wider py-2 px-4 rounded-xl hover:opacity-90 transition-opacity cursor-pointer shadow-lg shadow-orange-500/20 focus:outline-none border-0">
            Compare Now
        </button>
    </div>
</div>

<!-- Compare Modal -->
<div id="compare-modal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="bg-card-bg border border-primary-border w-full max-w-4xl rounded-2xl p-6 shadow-2xl flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-primary-border pb-4 mb-4">
            <h3 class="font-heading font-extrabold text-base uppercase tracking-wider text-main-text">Compare Packages</h3>
            <button onclick="closeCompareModal()" class="text-main-text hover:text-gold cursor-pointer bg-transparent border-0 focus:outline-none" aria-label="Close compare modal">
                <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
            </button>
        </div>
        
        <!-- Content columns -->
        <div class="overflow-x-auto flex-grow scrollbar-hide">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr id="compare-table-header" class="border-b border-primary-border">
                        <!-- JS will inject header titles, images, and pricing -->
                    </tr>
                </thead>
                <tbody id="compare-table-body">
                    <!-- JS will inject rows for Rating, Inclusions, and Actions -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Inline Filtering Logic Script -->
<script>
    let activeCategory = '{{ $selectedCategory ?? "all" }}';
    let activeBudget = 'all';
    let searchQuery = '';

    // Check URL parameters for category
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const catQuery = urlParams.get('category') || urlParams.get('cat');
        if (catQuery && catQuery !== 'all') {
            activeCategory = catQuery;
        }
    } catch(e) {}

    // Initialize Page
    document.addEventListener('DOMContentLoaded', () => {
        // Sync activeCategory with dropdown UI if pre-selected
        if (activeCategory !== 'all') {
            const catInput = document.getElementById('catalog-category');
            if (catInput) catInput.value = activeCategory;

            const dropdownCategory = document.getElementById('dropdown-category');
            if (dropdownCategory) {
                const cleanActive = activeCategory.replace(/^cat-/, '').toLowerCase();
                const items = dropdownCategory.querySelectorAll('.dropdown-item');
                let foundItem = null;
                items.forEach(item => {
                    const val = item.getAttribute('data-value');
                    const cleanVal = val.replace(/^cat-/, '').toLowerCase();
                    if (val === activeCategory || cleanVal === cleanActive || (cleanActive && cleanVal.includes(cleanActive)) || (cleanVal && cleanActive.includes(cleanVal))) {
                        foundItem = item;
                    }
                });

                if (foundItem) {
                    const selText = dropdownCategory.querySelector('.selected-text');
                    if (selText) selText.textContent = foundItem.textContent;
                    activeCategory = foundItem.getAttribute('data-value');
                }
            }
        }

        // Search listener
        const searchInput = document.getElementById('catalog-search');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                searchQuery = e.target.value.toLowerCase().trim();
                applyFilters();
            });
        }

        // Setup custom dropdowns
        const customDropdowns = document.querySelectorAll('.custom-dropdown');
        customDropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            const menu = dropdown.querySelector('.dropdown-menu');
            const chevron = dropdown.querySelector('.chevron');
            const input = dropdown.querySelector('input[type="hidden"]');
            const selectedText = dropdown.querySelector('.selected-text');
            const items = dropdown.querySelectorAll('.dropdown-item');

            // Open/Close Dropdown
            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                // Close all other dropdowns
                customDropdowns.forEach(other => {
                    if (other !== dropdown) {
                        other.querySelector('.dropdown-menu').classList.add('hidden');
                        other.querySelector('.chevron').classList.remove('rotate-180');
                    }
                });
                menu.classList.toggle('hidden');
                chevron.classList.toggle('rotate-180');
            });

            // Select Dropdown Option
            items.forEach(item => {
                item.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const val = item.getAttribute('data-value');
                    const text = item.textContent;

                    // Update UI text and hidden input value
                    selectedText.textContent = text;
                    input.value = val;

                    // Close menu
                    menu.classList.add('hidden');
                    chevron.classList.remove('rotate-180');

                    // Trigger filter updates
                    if (input.id === 'catalog-category') {
                        selectCategory(val);
                    } else if (input.id === 'catalog-budget') {
                        selectBudget(val);
                    } else if (input.id === 'catalog-sort') {
                        sortPackages();
                    }
                });
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', () => {
            customDropdowns.forEach(dropdown => {
                dropdown.querySelector('.dropdown-menu').classList.add('hidden');
                dropdown.querySelector('.chevron').classList.remove('rotate-180');
            });
        });

        applyFilters();
    });

    // Inclusions Accordion Toggle
    function toggleInclusions(button) {
        const panel = button.nextElementSibling;
        const svg = button.querySelector('svg');
        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            svg.classList.add('rotate-180');
        } else {
            panel.classList.add('hidden');
            svg.classList.remove('rotate-180');
        }
    }

    // Helper to test if card matches active category
    function isCardMatchingCategory(card, targetCategory) {
        if (!targetCategory || targetCategory === 'all') return true;
        const cardCat = (card.getAttribute('data-category') || '').toLowerCase();
        const cleanTarget = targetCategory.replace(/^cat-/, '').toLowerCase();
        const cleanCard = cardCat.replace(/^cat-/, '').toLowerCase();

        return (
            cardCat === targetCategory.toLowerCase() ||
            cleanCard === cleanTarget ||
            cleanCard.includes(cleanTarget) ||
            cleanTarget.includes(cleanCard)
        );
    }

    // Filter by Category Select Option
    function selectCategory(catId) {
        activeCategory = catId;
        if (window.history && window.history.replaceState) {
            const newUrl = catId === 'all' ? window.location.pathname : `${window.location.pathname}?category=${encodeURIComponent(catId)}`;
            window.history.replaceState({}, '', newUrl);
        }
        applyFilters();
    }

    // Filter by Budget Select Option
    function selectBudget(budgetRange) {
        activeBudget = budgetRange;
        applyFilters();
    }

    // Apply Combined Filters
    function applyFilters() {
        const cards = document.querySelectorAll('#packages-grid > .experience-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const cardPrice = parseInt(card.getAttribute('data-price')) || 0;
            const cardTitle = (card.getAttribute('data-title') || '').toLowerCase();
            const cardDesc = (card.getAttribute('data-desc') || '').toLowerCase();

            // 1. Budget Check
            let matchesBudget = false;
            if (activeBudget === 'all') {
                matchesBudget = true;
            } else if (activeBudget === 'under-8k' && cardPrice < 8000) {
                matchesBudget = true;
            } else if (activeBudget === '8k-15k' && cardPrice >= 8000 && cardPrice <= 15000) {
                matchesBudget = true;
            } else if (activeBudget === '15k-30k' && cardPrice > 15000 && cardPrice <= 30000) {
                matchesBudget = true;
            } else if (activeBudget === 'above-30k' && cardPrice > 30000) {
                matchesBudget = true;
            }

            // 2. Search Query Check
            const matchesSearch = (searchQuery === '' || cardTitle.includes(searchQuery) || cardDesc.includes(searchQuery));

            // Selected category shows first, other categories show below.
            // Cards are shown if they pass budget & search filters.
            if (matchesBudget && matchesSearch) {
                card.classList.remove('hidden');
                card.style.removeProperty('display');
                visibleCount++;
            } else {
                card.classList.add('hidden');
                card.style.setProperty('display', 'none', 'important');
            }
        });

        // Re-order DOM: matching category cards first, then divider, then other cards below
        sortPackages();

        // Toggle No-Packages Found Warning
        const fallback = document.getElementById('no-packages-found');
        const grid = document.getElementById('packages-grid');
        if (visibleCount === 0) {
            fallback.classList.remove('hidden');
            grid.classList.add('hidden');
        } else {
            fallback.classList.add('hidden');
            grid.classList.remove('hidden');
        }
    }

    // Sort Packages in DOM (priority to active category, then sort criteria)
    function sortPackages() {
        const select = document.getElementById('catalog-sort');
        const sortBy = select ? select.value : 'default';
        const grid = document.getElementById('packages-grid');
        if (!grid) return;

        const cards = Array.from(grid.querySelectorAll('.experience-card'));
        const divider = document.getElementById('other-packages-divider');

        const compareFn = (a, b) => {
            const priceA = parseInt(a.getAttribute('data-price')) || 0;
            const priceB = parseInt(b.getAttribute('data-price')) || 0;
            const ratingA = parseFloat(a.getAttribute('data-rating')) || 0;
            const ratingB = parseFloat(b.getAttribute('data-rating')) || 0;
            const idA = parseInt(a.getAttribute('data-id')) || 0;
            const idB = parseInt(b.getAttribute('data-id')) || 0;

            if (sortBy === 'price-low') {
                return priceA - priceB;
            } else if (sortBy === 'price-high') {
                return priceB - priceA;
            } else if (sortBy === 'rating') {
                return ratingB - ratingA;
            } else {
                return idA - idB; // Default sorted by list ID
            }
        };

        const hasCategoryPriority = (activeCategory && activeCategory !== 'all');

        if (hasCategoryPriority) {
            const primaryCards = [];
            const otherCards = [];

            cards.forEach(card => {
                if (isCardMatchingCategory(card, activeCategory)) {
                    primaryCards.push(card);
                } else {
                    otherCards.push(card);
                }
            });

            primaryCards.sort(compareFn);
            otherCards.sort(compareFn);

            // Re-append primary matching cards first
            primaryCards.forEach(card => grid.appendChild(card));

            // Position and toggle divider
            if (divider) {
                const visiblePrimary = primaryCards.filter(c => !c.classList.contains('hidden'));
                const visibleOther = otherCards.filter(c => !c.classList.contains('hidden'));
                if (visiblePrimary.length > 0 && visibleOther.length > 0) {
                    divider.classList.remove('hidden');
                    grid.appendChild(divider);
                } else {
                    divider.classList.add('hidden');
                }
            }

            // Re-append other cards below
            otherCards.forEach(card => grid.appendChild(card));
        } else {
            if (divider) divider.classList.add('hidden');
            cards.sort(compareFn);
            cards.forEach(card => grid.appendChild(card));
        }
    }

    // Reset Filters Button
    function resetAllFilters() {
        const searchInput = document.getElementById('catalog-search');
        if (searchInput) searchInput.value = '';
        searchQuery = '';
        
        // Reset category dropdown
        const catSelect = document.getElementById('catalog-category');
        if (catSelect) {
            catSelect.value = 'all';
            const catSelectedText = document.querySelector('#dropdown-category .selected-text');
            if (catSelectedText) catSelectedText.textContent = 'All Categories';
        }
        activeCategory = 'all';

        // Reset budget dropdown
        const budgetSelect = document.getElementById('catalog-budget');
        if (budgetSelect) {
            budgetSelect.value = 'all';
            const budgetSelectedText = document.querySelector('#dropdown-budget .selected-text');
            if (budgetSelectedText) budgetSelectedText.textContent = 'All Prices';
        }
        activeBudget = 'all';

        // Reset sort dropdown
        const sortSelect = document.getElementById('catalog-sort');
        if (sortSelect) {
            sortSelect.value = 'default';
            const sortSelectedText = document.querySelector('#dropdown-sort .selected-text');
            if (sortSelectedText) sortSelectedText.textContent = 'Recommended';
        }

        if (window.history && window.history.replaceState) {
            window.history.replaceState({}, '', window.location.pathname);
        }

        applyFilters();
    }

    // Toggle Mobile Drawer Filters
    function toggleMobileFilters(isOpen) {
        const filterContainer = document.getElementById('filter-container');
        if (filterContainer) {
            if (isOpen) {
                filterContainer.classList.remove('max-lg:hidden');
                document.body.style.overflow = 'hidden'; // Lock scrolling
            } else {
                filterContainer.classList.add('max-lg:hidden');
                document.body.style.overflow = ''; // Unlock scrolling
            }
        }
    }

    // Backdrop click close listener
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('filter-container');
        if (container) {
            container.addEventListener('click', (e) => {
                if (e.target === container) {
                    toggleMobileFilters(false);
                }
            });
        }

        // Compare Modal backdrop click closer
        const compareModal = document.getElementById('compare-modal');
        if (compareModal) {
            compareModal.addEventListener('click', (e) => {
                if (e.target === compareModal) {
                    closeCompareModal();
                }
            });
        }
    });

    // Package Comparison Feature Logic
    let comparedPackages = [];

    function toggleComparePackage(checkbox) {
        const id = checkbox.getAttribute('data-id');
        const title = checkbox.getAttribute('data-title');
        const price = parseInt(checkbox.getAttribute('data-price'));
        const image = checkbox.getAttribute('data-image');
        const rating = checkbox.getAttribute('data-rating');
        const inclusions = JSON.parse(checkbox.getAttribute('data-inclusions'));
        const url = checkbox.getAttribute('data-url');
        const label = checkbox.parentElement.querySelector('.compare-label');
        const icon = checkbox.parentElement.querySelector('.compare-icon');

        if (checkbox.checked) {
            if (comparedPackages.length >= 3) {
                checkbox.checked = false;
                alert("You can compare a maximum of 3 packages.");
                return;
            }
            comparedPackages.push({ id, title, price, image, rating, inclusions, url });
            
            // Flip container styling to orange
            checkbox.parentElement.classList.remove('bg-black/70', 'text-white');
            checkbox.parentElement.classList.add('bg-[#EA741D]', 'text-white', 'border-[#EA741D]');
            
            // Flip elements color
            label.classList.remove('text-gray-300');
            label.classList.add('text-white');
            icon.classList.remove('text-gray-400');
            icon.classList.add('text-white');
        } else {
            comparedPackages = comparedPackages.filter(pkg => pkg.id !== id);
            
            // Restore container styling
            checkbox.parentElement.classList.remove('bg-[#EA741D]', 'text-white', 'border-[#EA741D]');
            checkbox.parentElement.classList.add('bg-black/70', 'text-white');
            
            // Restore elements color
            label.classList.remove('text-white');
            label.classList.add('text-gray-300');
            icon.classList.remove('text-white');
            icon.classList.add('text-gray-400');
        }

        updateCompareBar();
    }

    function updateCompareBar() {
        const bar = document.getElementById('compare-bar');
        const badge = document.getElementById('compare-count-badge');
        const thumbnailsContainer = document.getElementById('compare-thumbnails');

        if (comparedPackages.length > 0) {
            badge.textContent = `Compare (${comparedPackages.length}/3)`;
            
            // Build thumbnail bubbles
            thumbnailsContainer.innerHTML = '';
            comparedPackages.forEach(pkg => {
                const img = document.createElement('img');
                img.src = pkg.image;
                img.className = 'w-7 h-7 rounded-full border-2 border-[#1E1E24] object-cover';
                thumbnailsContainer.appendChild(img);
            });

            // Show compare bar
            bar.classList.remove('hidden');
            setTimeout(() => {
                bar.classList.remove('opacity-0', 'translate-y-28');
            }, 10);
        } else {
            // Hide compare bar
            bar.classList.add('opacity-0', 'translate-y-28');
            setTimeout(() => {
                bar.classList.add('hidden');
            }, 300);
        }
    }

    function clearCompare() {
        comparedPackages = [];
        document.querySelectorAll('.compare-checkbox').forEach(cb => {
            cb.checked = false;
            
            // Restore container styling
            cb.parentElement.classList.remove('bg-[#EA741D]', 'text-white', 'border-[#EA741D]');
            cb.parentElement.classList.add('bg-black/70', 'text-white');
            
            // Restore elements color
            const label = cb.parentElement.querySelector('.compare-label');
            const icon = cb.parentElement.querySelector('.compare-icon');
            label.classList.remove('text-white');
            label.classList.add('text-gray-300');
            icon.classList.remove('text-white');
            icon.classList.add('text-gray-400');
        });
        updateCompareBar();
    }

    function openCompareModal() {
        if (comparedPackages.length === 0) return;
        const modal = document.getElementById('compare-modal');
        const headerRow = document.getElementById('compare-table-header');
        const body = document.getElementById('compare-table-body');

        // Build header row
        let headerHtml = `<th class="py-4 px-3 font-heading text-xs font-bold uppercase tracking-wider text-muted-text">Feature</th>`;
        comparedPackages.forEach(pkg => {
            headerHtml += `
                <th class="py-4 px-4 w-1/3">
                    <div class="flex flex-col gap-2">
                        <img src="${pkg.image}" class="w-full h-24 object-cover rounded-xl border border-primary-border bg-gray-100 dark:bg-gray-900">
                        <h4 class="font-heading font-extrabold text-[10px] md:text-xs uppercase tracking-wide text-main-text line-clamp-2 leading-tight">${pkg.title}</h4>
                    </div>
                </th>
            `;
        });
        headerRow.innerHTML = headerHtml;

        // Build table body
        let bodyHtml = '';

        // Price Row
        bodyHtml += `
            <tr class="border-b border-primary-border">
                <td class="py-3 px-3 font-heading text-[10px] md:text-xs font-bold uppercase tracking-wider text-muted-text">Price</td>
        `;
        comparedPackages.forEach(pkg => {
            bodyHtml += `<td class="py-3 px-4 font-heading font-extrabold text-xs md:text-sm text-gold">₹${new Intl.NumberFormat('en-IN').format(pkg.price)}</td>`;
        });
        bodyHtml += `</tr>`;

        // Rating Row
        bodyHtml += `
            <tr class="border-b border-primary-border">
                <td class="py-3 px-3 font-heading text-[10px] md:text-xs font-bold uppercase tracking-wider text-muted-text">Rating</td>
        `;
        comparedPackages.forEach(pkg => {
            bodyHtml += `
                <td class="py-3 px-4">
                    <div class="flex items-center gap-1 font-heading font-extrabold text-xs text-gold">
                        <i class="fa-solid fa-star text-gold text-[10px]" aria-hidden="true"></i>
                        <span>${pkg.rating}</span>
                    </div>
                </td>
            `;
        });
        bodyHtml += `</tr>`;

        // Gather all unique inclusions
        let allInclusions = new Set();
        comparedPackages.forEach(pkg => {
            pkg.inclusions.forEach(inc => allInclusions.add(inc));
        });

        allInclusions.forEach(inc => {
            bodyHtml += `
                <tr class="border-b border-primary-border">
                    <td class="py-3 px-3 font-body text-[10px] md:text-xs text-muted-text leading-relaxed">${inc}</td>
            `;
            comparedPackages.forEach(pkg => {
                const hasInclusion = pkg.inclusions.includes(inc);
                if (hasInclusion) {
                    bodyHtml += `
                        <td class="py-3 px-4 text-gold">
                            <i class="fa-solid fa-check text-gold text-sm" aria-hidden="true"></i>
                        </td>
                    `;
                } else {
                    bodyHtml += `
                        <td class="py-3 px-4 text-gray-500">
                            <i class="fa-solid fa-xmark text-gray-400 dark:text-gray-600 text-sm" aria-hidden="true"></i>
                        </td>
                    `;
                }
            });
            bodyHtml += `</tr>`;
        });

        // Booking CTA Action Row
        bodyHtml += `
            <tr>
                <td class="py-4 px-3 font-heading text-[10px] md:text-xs font-bold uppercase tracking-wider text-muted-text">Action</td>
        `;
        comparedPackages.forEach(pkg => {
            const actionUrl = pkg.url.startsWith('javascript:') ? pkg.url.substring(11) : pkg.url;
            bodyHtml += `
                <td class="py-4 px-4">
                    <span onclick="closeCompareModal(); ${actionUrl};" class="cursor-pointer bg-[#EA741D] text-white font-heading font-bold uppercase tracking-wider rounded-xl py-1.5 md:py-2 px-3 md:px-4 inline-flex items-center justify-center gap-1 hover:opacity-90 transition-opacity text-[9px] md:text-[10px] border border-[#EA741D]">
                        Book Now
                        <i class="fa-solid fa-chevron-right text-[9px]" aria-hidden="true"></i>
                    </span>
                </td>
            `;
        });
        bodyHtml += `</tr>`;

        body.innerHTML = bodyHtml;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCompareModal() {
        document.getElementById('compare-modal').classList.add('hidden');
        const filterContainer = document.getElementById('filter-container');
        if (!filterContainer || filterContainer.classList.contains('max-lg:hidden') || filterContainer.classList.contains('hidden')) {
            document.body.style.overflow = '';
        }
    }
</script>
@endsection

@extends('layouts.app')

@section('title', 'Explore All Celebrations & Packages in Indore | ARTIZEN')
@section('meta_description', 'Browse and book curated celebration packages, birthday decorations, proposal setups, anniversary stages, DJ sound rigs, and live artists in Indore.')

@section('content')
<div class="min-h-screen bg-[#FCFBF8] text-gray-950 font-body select-none">

    <!-- ========================================================
         SECTION A: CLEAN MINIMAL PAGE HEADER
         ======================================================== -->
    <section class="border-b border-[#EFE7D8] bg-[#FAF7F2] py-8 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-heading font-extrabold text-2xl sm:text-3xl md:text-4xl text-gray-950 tracking-tight mb-1.5">
                Explore Celebrations
            </h1>
            <p class="text-xs sm:text-sm text-gray-600 font-normal max-w-2xl">
                Discover curated setups, romantic decors, DJ party rigs, and live artists across Indore.
            </p>
        </div>
    </section>


    <!-- ========================================================
         SECTION B: HORIZONTAL CATEGORIES & FILTERS BUTTON (NON-STICKY)
         ======================================================== -->
    <section class="bg-[#FCFBF8] border-b border-[#EFE7D8] py-2.5 sm:py-3 shadow-none">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-3">
            
            <!-- Category Tabs Rail (Scrollable) -->
            <div id="category-tabs-rail" class="flex items-center gap-2 overflow-x-auto scrollbar-hide py-0.5 flex-1 min-w-0" style="scrollbar-width: none; -ms-overflow-style: none;">
                
                <!-- Tab: All Celebrations -->
                @php $isAllActive = ($selectedCategory === 'all' || empty($selectedCategory)); @endphp
                <button type="button" 
                        onclick="selectCategoryFilter('all', this)" 
                        data-cat-slug="all"
                        class="cat-tab-pill shrink-0 inline-flex items-center px-4 py-2 rounded-xl text-xs sm:text-[13px] font-heading font-bold transition-colors duration-150 cursor-pointer {{ $isAllActive ? 'bg-gray-950 text-white' : 'bg-white text-gray-700 hover:text-gray-950 border border-[#E8DFC8] hover:border-gray-400' }}">
                    <span>All Celebrations</span>
                </button>

                <!-- Category Tabs (Clean text only, no badges or emojis) -->
                @foreach($categoriesTaxonomy as $slug => $cat)
                    @if($slug !== 'all')
                        @php $isActive = ($selectedCategory === $slug); @endphp
                        <button type="button" 
                                onclick="selectCategoryFilter('{{ $slug }}', this)" 
                                data-cat-slug="{{ $slug }}"
                                class="cat-tab-pill shrink-0 inline-flex items-center px-4 py-2 rounded-xl text-xs sm:text-[13px] font-heading font-bold transition-colors duration-150 cursor-pointer {{ $isActive ? 'bg-gray-950 text-white' : 'bg-white text-gray-700 hover:text-gray-950 border border-[#E8DFC8] hover:border-gray-400' }}">
                            <span>{{ $cat['name'] }}</span>
                        </button>
                    @endif
                @endforeach

            </div>

            <!-- Open Filter Sidebar Button -->
            <button type="button" 
                    onclick="openFilterDrawer()" 
                    id="open-filter-btn"
                    class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-[#E8DFC8] hover:border-gray-950 bg-white hover:bg-gray-50 text-xs sm:text-[13px] font-heading font-bold text-gray-900 transition-colors cursor-pointer shadow-none">
                <i class="fa-solid fa-sliders text-xs text-gray-700"></i>
                <span>Filters</span>
                <span id="active-filter-count-badge" class="hidden text-[11px] font-bold px-1.5 py-0.2 rounded-md bg-[#FAF7F2] border border-[#E8DFC8] text-gray-950 leading-tight"></span>
            </button>

        </div>
    </section>


    <!-- ========================================================
         SECTION C: RESULTS STATUS & ACTIVE FILTER CHIPS
         ======================================================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-1">
        <div class="flex items-center justify-between flex-wrap gap-2 text-xs text-gray-600">
            <div class="flex items-center gap-2 flex-wrap" id="active-chips-container">
                <span class="font-heading font-bold text-gray-900 text-xs" id="results-count-text">
                    Showing <span class="text-gray-950 font-extrabold" id="filtered-count-number">{{ count($allPackages) }}</span> celebrations in Indore
                </span>

                <!-- Active Filter Chips -->
                <span id="active-category-chip" style="display: none;" class="items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white border border-[#E8DFC8] text-[11px] font-semibold text-gray-800 shadow-none">
                    <span class="chip-label"></span>
                    <button type="button" onclick="selectCategoryFilter('all')" class="hover:text-red-600 cursor-pointer ml-1"><i class="fa-solid fa-xmark text-[10px]"></i></button>
                </span>

                <span id="active-subcategory-chip" style="display: none;" class="items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white border border-[#E8DFC8] text-[11px] font-semibold text-gray-800 shadow-none">
                    <span class="chip-label"></span>
                    <button type="button" onclick="selectSubcategoryFilter('all')" class="hover:text-red-600 cursor-pointer ml-1"><i class="fa-solid fa-xmark text-[10px]"></i></button>
                </span>

                <span id="active-budget-chip" style="display: none;" class="items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white border border-[#E8DFC8] text-[11px] font-semibold text-gray-800 shadow-none">
                    <span class="chip-label"></span>
                    <button type="button" onclick="handleBudgetFilter('all')" class="hover:text-red-600 cursor-pointer ml-1"><i class="fa-solid fa-xmark text-[10px]"></i></button>
                </span>

                <span id="active-for-chip" style="{{ !empty($forFilter) ? '' : 'display: none;' }}" class="items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-[#FFF5F5] border border-rose-200 text-[11px] font-semibold text-rose-900 shadow-none">
                    <span>For: <strong class="chip-label">{{ ucfirst($forFilter) }}</strong></span>
                    <button type="button" onclick="setDrawerFor('all')" class="hover:text-red-600 cursor-pointer ml-1"><i class="fa-solid fa-xmark text-[10px]"></i></button>
                </span>
            </div>

            <!-- Clear all link (if any filters active) -->
            <button type="button" 
                    id="clear-all-filters-link" 
                    onclick="resetAllCatalogFilters()" 
                    class="hidden text-[11.5px] font-heading font-bold text-gray-600 hover:text-gray-950 underline cursor-pointer">
                Clear all filters
            </button>
        </div>
    </section>


    <!-- ========================================================
         SECTION D: COMPLETE SERVICE CATALOG GRID
         ======================================================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6 pb-16">
        
        <!-- Clean Empty State -->
        <div id="catalog-empty-state" class="hidden text-center py-16 sm:py-20 bg-white border border-[#E8DFC8] rounded-2xl shadow-none max-w-md mx-auto my-8 px-6">
            <div class="w-12 h-12 rounded-full bg-[#FAF7F2] border border-[#E8DFC8] flex items-center justify-center mx-auto mb-3 text-gray-500 text-lg">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <h3 class="font-heading font-extrabold text-base text-gray-950 mb-1">
                No Celebrations Found
            </h3>
            <p class="text-xs text-gray-500 font-normal leading-relaxed mb-4">
                Try adjusting or clearing your filters in the sidebar.
            </p>
            <button type="button" 
                    onclick="resetAllCatalogFilters()" 
                    class="inline-flex items-center gap-2 bg-gray-950 hover:bg-gray-800 text-white font-heading font-bold text-xs px-4 py-2 rounded-xl shadow-none transition-colors cursor-pointer">
                <i class="fa-solid fa-rotate-left text-xs"></i>
                <span>Reset Filters</span>
            </button>
        </div>

        <!-- 4-Column Responsive Grid matching Homepage Service Card UI -->
        <div id="catalog-grid" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-4.5">
            
            @foreach($allPackages as $item)
                @php
                    $itemPrice = (int)$item['price'];
                    $origPrice = (int)($item['original_price'] ?? round($itemPrice * 1.15));
                    $eventDetailsUrl = route('events.show', ['slug' => $item['slug'] ?? \Illuminate\Support\Str::slug($item['title'])]);
                    $tagsString = strtolower(implode(' ', array_merge([$item['title'], $item['subcategory'], $item['category_name'], $item['desc']], $item['tags'] ?? [])));
                @endphp

                <!-- Service Card (EXACT SAME UI DESIGN AS HOMEPAGE, NO HOVER ANIMATION, NO SHADOW) -->
                <div class="catalog-item-card" 
                     data-id="{{ $item['id'] }}"
                     data-category="{{ $item['category_id'] }}"
                     data-subcategory="{{ strtolower($item['subcategory']) }}"
                     data-title="{{ strtolower($item['title']) }}"
                     data-price="{{ $itemPrice }}"
                     data-rating="{{ $item['rating'] ?? 4.9 }}"
                     data-reviews="{{ $item['reviews_count'] ?? 50 }}"
                     data-recipients="{{ strtolower(implode(',', (array)($item['recipients'] ?? []))) }}"
                     data-search-terms="{{ $tagsString }}">
                    
                    <a href="{{ $eventDetailsUrl }}" 
                       class="block w-full bg-white rounded-2xl border border-[#E8DFC8] overflow-hidden shadow-none text-left flex flex-col justify-between h-full select-none cursor-pointer">
                        
                        <!-- Image Box (Aspect 4/3.8 Crisp & Clean, No Hover Zoom) -->
                        <div class="relative w-full aspect-[4/3.8] overflow-hidden bg-[#FAF7F2] shrink-0">
                            <img src="{{ $item['image'] }}" 
                                 alt="{{ $item['title'] }}" 
                                 class="w-full h-full object-cover select-none pointer-events-none" 
                                 draggable="false"
                                 loading="lazy">
                            
                            <!-- Rating Badge Top-Right -->
                            <span class="absolute top-2.5 right-2.5 bg-white/95 backdrop-blur-xs px-2 py-0.5 rounded text-[11px] font-bold text-gray-900 flex items-center gap-1 shadow-none z-20">
                                <i class="fa-solid fa-star text-amber-400 text-[10px]"></i>
                                <span>{{ $item['rating'] ?? '4.9' }}</span>
                            </span>
                        </div>

                        <!-- Body -->
                        <div class="p-3 sm:p-3.5 flex flex-col justify-between flex-1">
                            <div class="mb-2.5">
                                <span class="text-[9.5px] sm:text-[10px] font-heading font-extrabold uppercase tracking-wider text-[#B89700] block mb-0.5 truncate">
                                    {{ $item['subcategory'] }}
                                </span>
                                <h4 class="font-heading font-bold text-[13.5px] sm:text-[14.5px] text-gray-900 leading-snug line-clamp-1">
                                    {{ $item['title'] }}
                                </h4>
                            </div>

                            <!-- Pricing Line (Discount Offer & Actual Price) -->
                            <div class="pt-2.5 border-t border-[#F2ECE0] flex items-baseline justify-between mt-auto">
                                <div class="flex items-baseline gap-1.5 flex-wrap">
                                    <span class="font-heading font-extrabold text-[15px] sm:text-base text-gray-950 tracking-tight">
                                        ₹{{ number_format($itemPrice) }}
                                    </span>
                                    <span class="text-[11px] text-gray-400 line-through font-normal">
                                        ₹{{ number_format($origPrice) }}
                                    </span>
                                    <span class="text-[10.5px] font-bold text-emerald-600">
                                        15% OFF
                                    </span>
                                </div>
                            </div>
                        </div>

                    </a>

                </div>
            @endforeach

        </div>

    </section>


    <!-- ========================================================
         SECTION E: CUSTOM CELEBRATION CONSULTATION BANNER
         ======================================================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="relative rounded-3xl overflow-hidden border border-[#E8DFC8] bg-[#FAF7F2]">
            <!-- Background Image with Soft Clean Gradient Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('images/banners/custom_tree_canopy.webp') }}" 
                     alt="Custom Event Celebration in Indore" 
                     class="w-full h-full object-cover object-right select-none pointer-events-none"
                     loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-r from-[#FAF7F2] via-[#FAF7F2]/95 md:via-[#FAF7F2]/85 to-transparent"></div>
            </div>

            <!-- Content Area (Clean Typography & Instant CTAs) -->
            <div class="relative z-10 p-6 sm:p-10 md:p-12 max-w-2xl text-left">
                <h3 class="font-heading font-extrabold text-xl sm:text-2xl md:text-3xl text-gray-950 tracking-tight mb-2.5">
                    Looking for a Custom Setup?
                </h3>

                <p class="text-xs sm:text-sm text-gray-600 font-normal leading-relaxed mb-6">
                    Can't find the exact theme or planning a grand celebration at a farmhouse, rooftop, or private lawn? Our celebration stylists will design a bespoke decor, sound, and lighting package for you in Indore.
                </p>

                <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap">
                    <a href="https://wa.me/919109109100?text=Hi%20Artizen,%20I%20want%20to%20discuss%20a%20custom%20celebration%20package%20in%20Indore" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="inline-flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#20bd5a] text-white font-heading font-bold text-xs sm:text-[13px] px-5 py-3 rounded-xl shadow-none transition-colors cursor-pointer">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>Chat on WhatsApp</span>
                    </a>

                    <a href="{{ route('contact.index') }}" 
                       class="inline-flex items-center justify-center gap-2 bg-gray-950 hover:bg-gray-800 text-white font-heading font-bold text-xs sm:text-[13px] px-5 py-3 rounded-xl shadow-none transition-colors cursor-pointer">
                        <i class="fa-solid fa-headset text-xs"></i>
                        <span>Contact Specialist</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>


<!-- ========================================================
     SLIDE-OUT FILTER SIDEBAR DRAWER (ACCORDION & DROPDOWN STYLE)
     ======================================================== -->
<div id="filter-drawer-backdrop" 
     onclick="closeFilterDrawer()" 
     class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs transition-opacity duration-200 opacity-0 pointer-events-none">
</div>

<div id="filter-drawer" 
     class="fixed top-0 right-0 bottom-0 z-50 w-full max-w-md bg-white border-l border-[#E8DFC8] shadow-2xl flex flex-col justify-between transform translate-x-full transition-transform duration-300 ease-out select-none text-left overscroll-contain"
     style="touch-action: pan-y;">
    
    <!-- Drawer Header -->
    <div class="px-5 py-4 border-b border-[#EFE7D8] flex items-center justify-between bg-[#FAF7F2] shrink-0">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-sliders text-sm text-gray-800"></i>
            <h3 class="font-heading font-extrabold text-base text-gray-950">Filters</h3>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" 
                    onclick="resetAllCatalogFilters()" 
                    class="text-xs font-heading font-bold text-gray-500 hover:text-gray-950 transition-colors cursor-pointer">
                Reset all
            </button>
            <button type="button" 
                    onclick="closeFilterDrawer()" 
                    class="w-8 h-8 rounded-full bg-white border border-[#E8DFC8] hover:bg-gray-100 flex items-center justify-center text-gray-600 transition-colors cursor-pointer">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>
    </div>

    <!-- Drawer Scrollable Content -->
    <div class="p-5 overflow-y-auto flex-1 space-y-6 overscroll-contain" style="-webkit-overflow-scrolling: touch; touch-action: pan-y;">
        
        <!-- 1. Categories & Subcategories (Clean Dropdown / Accordion Type) -->
        <div>
            <h4 class="text-xs font-heading font-extrabold uppercase tracking-wider text-gray-900 mb-3">
                Celebration Categories
            </h4>

            <div class="space-y-2">
                
                <!-- Option: All Celebrations -->
                <button type="button" 
                        onclick="setDrawerCategory('all')" 
                        data-drawer-cat="all"
                        class="drawer-cat-btn w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-[#E8DFC8] bg-white text-xs font-heading font-bold text-gray-900 hover:border-gray-950 transition-colors cursor-pointer">
                    <span>All Celebrations</span>
                    <span class="text-[11px] text-gray-400 font-normal">33 setups</span>
                </button>

                <!-- Accordion Dropdown for Each Category -->
                @foreach($subcategoriesByCategory as $catSlug => $subs)
                    @php $catInfo = $categoriesTaxonomy[$catSlug] ?? null; @endphp
                    @if($catInfo)
                        <div class="category-accordion-group border border-[#E8DFC8] rounded-xl overflow-hidden bg-white transition-colors" data-accordion-cat="{{ $catSlug }}">
                            
                            <!-- Accordion Header: Category Title + Subcategory Count + Chevron -->
                            <div class="flex items-center justify-between px-3.5 py-3 cursor-pointer hover:bg-[#FAF7F2] transition-colors" 
                                 onclick="toggleCategoryAccordion('{{ $catSlug }}')">
                                <span class="text-xs font-heading font-bold text-gray-950">{{ $catInfo['name'] }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10.5px] text-gray-400 font-medium">{{ count($subs) }} themes</span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 accordion-chevron transition-transform duration-200"></i>
                                </div>
                            </div>

                            <!-- Accordion Subcategory Dropdown Content -->
                            <div class="accordion-sub-content hidden px-3.5 pb-3 pt-1 border-t border-[#F2ECE0] bg-[#FAF7F2]/40">
                                <div class="flex flex-wrap gap-1.5 pt-1.5">
                                    <button type="button" 
                                            onclick="setDrawerCategory('{{ $catSlug }}')" 
                                            data-drawer-sub="all-{{ $catSlug }}"
                                            class="drawer-sub-btn text-[11px] px-2.5 py-1 rounded-lg bg-white border border-[#E8DFC8] hover:border-gray-950 text-gray-800 font-medium transition-colors cursor-pointer">
                                        All in {{ $catInfo['name'] }}
                                    </button>
                                    @foreach($subs as $subName)
                                        <button type="button" 
                                                onclick="setDrawerSubcategory('{{ $catSlug }}', '{{ strtolower($subName) }}')" 
                                                data-drawer-sub="{{ strtolower($subName) }}"
                                                class="drawer-sub-btn text-[11px] px-2.5 py-1 rounded-lg bg-white border border-[#E8DFC8] hover:border-gray-950 text-gray-700 font-medium transition-colors cursor-pointer">
                                            {{ $subName }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    @endif
                @endforeach

            </div>
        </div>

        <!-- 1.b Celebration For / For Whom Filter -->
        @if(isset($personas) && $personas->isNotEmpty())
        <div>
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-xs font-heading font-extrabold uppercase tracking-wider text-gray-900">
                    Celebration For / For Whom
                </h4>
                <span class="text-[11px] text-gray-400 font-medium">Storefront Personas</span>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <!-- Option: Anyone / All -->
                <button type="button" 
                        onclick="setDrawerFor('all')" 
                        data-drawer-for="all" 
                        class="drawer-for-btn h-11 p-0 overflow-hidden text-xs font-heading font-bold rounded-xl border border-[#E8DFC8] bg-white text-gray-800 text-center hover:border-gray-950 transition-colors cursor-pointer col-span-2 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-users text-xs text-gray-400"></i>
                    <span>Anyone / All Celebrations</span>
                </button>

                @foreach($personas as $pItem)
                    <button type="button" 
                            onclick="setDrawerFor('{{ $pItem->slug }}')" 
                            data-drawer-for="{{ $pItem->slug }}" 
                            class="drawer-for-btn h-12 p-0 overflow-hidden text-xs font-heading font-bold rounded-xl border border-[#E8DFC8] bg-white text-gray-800 hover:border-gray-950 transition-colors cursor-pointer flex items-center text-left group">
                        @if($pItem->image)
                            <div class="h-full w-14 shrink-0 overflow-hidden bg-[#FFF5F5] border-r border-black/5">
                                <img src="{{ asset($pItem->image) }}" alt="{{ $pItem->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                            </div>
                        @else
                            <div class="h-full w-14 shrink-0 overflow-hidden bg-gray-100 border-r border-black/5 flex items-center justify-center text-gray-400">
                                <i class="fa-regular fa-image text-xs"></i>
                            </div>
                        @endif
                        <span class="px-3 truncate flex-1 font-bold">{{ $pItem->name }}</span>
                    </button>
                @endforeach
            </div>
        </div>
        @endif

        <!-- 2. Price Range -->
        <div>
            <h4 class="text-xs font-heading font-extrabold uppercase tracking-wider text-gray-900 mb-3">
                Price Range
            </h4>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="setDrawerBudget('all')" data-drawer-budget="all" class="drawer-budget-btn p-2.5 text-xs font-heading font-bold rounded-xl border border-[#E8DFC8] bg-white text-gray-800 text-center hover:border-gray-950 transition-colors cursor-pointer">Any Price</button>
                <button type="button" onclick="setDrawerBudget('under-5k')" data-drawer-budget="under-5k" class="drawer-budget-btn p-2.5 text-xs font-heading font-bold rounded-xl border border-[#E8DFC8] bg-white text-gray-800 text-center hover:border-gray-950 transition-colors cursor-pointer">Under ₹5,000</button>
                <button type="button" onclick="setDrawerBudget('5k-7k')" data-drawer-budget="5k-7k" class="drawer-budget-btn p-2.5 text-xs font-heading font-bold rounded-xl border border-[#E8DFC8] bg-white text-gray-800 text-center hover:border-gray-950 transition-colors cursor-pointer">₹5,000 - ₹7,000</button>
                <button type="button" onclick="setDrawerBudget('7k-9k')" data-drawer-budget="7k-9k" class="drawer-budget-btn p-2.5 text-xs font-heading font-bold rounded-xl border border-[#E8DFC8] bg-white text-gray-800 text-center hover:border-gray-950 transition-colors cursor-pointer">₹7,000 - ₹9,000</button>
                <button type="button" onclick="setDrawerBudget('above-9k')" data-drawer-budget="above-9k" class="drawer-budget-btn p-2.5 text-xs font-heading font-bold rounded-xl border border-[#E8DFC8] bg-white text-gray-800 text-center hover:border-gray-950 transition-colors cursor-pointer col-span-2">Above ₹9,000</button>
            </div>
        </div>

        <!-- 3. Sort By -->
        <div>
            <h4 class="text-xs font-heading font-extrabold uppercase tracking-wider text-gray-900 mb-3">
                Sort Order
            </h4>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="setDrawerSort('default')" data-drawer-sort="default" class="drawer-sort-btn p-2.5 text-xs font-heading font-bold rounded-xl border border-[#E8DFC8] bg-white text-gray-800 text-center hover:border-gray-950 transition-colors cursor-pointer">Featured</button>
                <button type="button" onclick="setDrawerSort('price-low')" data-drawer-sort="price-low" class="drawer-sort-btn p-2.5 text-xs font-heading font-bold rounded-xl border border-[#E8DFC8] bg-white text-gray-800 text-center hover:border-gray-950 transition-colors cursor-pointer">Price: Low to High</button>
                <button type="button" onclick="setDrawerSort('price-high')" data-drawer-sort="price-high" class="drawer-sort-btn p-2.5 text-xs font-heading font-bold rounded-xl border border-[#E8DFC8] bg-white text-gray-800 text-center hover:border-gray-950 transition-colors cursor-pointer">Price: High to Low</button>
                <button type="button" onclick="setDrawerSort('rating')" data-drawer-sort="rating" class="drawer-sort-btn p-2.5 text-xs font-heading font-bold rounded-xl border border-[#E8DFC8] bg-white text-gray-800 text-center hover:border-gray-950 transition-colors cursor-pointer">Top Rated</button>
            </div>
        </div>

    </div>

    <!-- Drawer Sticky Footer -->
    <div class="p-4 border-t border-[#EFE7D8] bg-[#FAF7F2] flex items-center justify-between gap-3">
        <button type="button" 
                onclick="resetAllCatalogFilters()" 
                class="px-4 py-3 rounded-xl border border-[#E8DFC8] bg-white text-xs font-heading font-bold text-gray-700 hover:text-gray-950 transition-colors cursor-pointer">
            Clear all
        </button>
        <button type="button" 
                onclick="closeFilterDrawer()" 
                class="flex-1 px-5 py-3 rounded-xl bg-gray-950 hover:bg-gray-800 text-white text-xs sm:text-sm font-heading font-bold text-center transition-colors cursor-pointer shadow-none">
            Show <span id="drawer-results-count">{{ count($allPackages) }}</span> Celebrations
        </button>
    </div>

</div>


<!-- ========================================================
     CLIENT-SIDE FILTER & SIDEBAR ENGINE
     ======================================================== -->
<script>
    (function() {
        const state = {
            category: '{{ $selectedCategory ?? "all" }}',
            subcategory: 'all',
            budget: '{{ $budgetFilter ?? "all" }}',
            sort: '{{ $sortFilter ?? "default" }}',
            for: '{{ !empty($forFilter) ? strtolower($forFilter) : "all" }}'
        };

        const categoriesMap = @json($categoriesTaxonomy);

        function filterAndSortCatalog() {
            const items = Array.from(document.querySelectorAll('.catalog-item-card'));
            let visibleCount = 0;

            items.forEach(function(item) {
                const itemCat = item.getAttribute('data-category');
                const itemSub = (item.getAttribute('data-subcategory') || '').toLowerCase();
                const itemPrice = parseInt(item.getAttribute('data-price') || '0', 10);

                // 1. Category Match
                let matchesCat = (state.category === 'all' || itemCat === state.category);

                // 2. Subcategory Match
                let matchesSub = (state.subcategory === 'all' || itemSub === state.subcategory.toLowerCase());

                // 3. Budget Match
                let matchesBudget = true;
                if (state.budget === 'under-5k') matchesBudget = (itemPrice < 5000);
                else if (state.budget === '5k-7k') matchesBudget = (itemPrice >= 5000 && itemPrice <= 7000);
                else if (state.budget === '7k-9k') matchesBudget = (itemPrice > 7000 && itemPrice <= 9000);
                else if (state.budget === 'above-9k') matchesBudget = (itemPrice > 9000);

                // 4. Recipient ("For Whom") Match
                let matchesFor = true;
                if (state.for && state.for !== 'all') {
                    const itemRecipients = (item.getAttribute('data-recipients') || '').split(',').map(s => s.trim().toLowerCase());
                    const itemSearchTerms = (item.getAttribute('data-search-terms') || '').toLowerCase();
                    const itemTitle = (item.getAttribute('data-title') || '').toLowerCase();

                    if (itemRecipients.includes(state.for.toLowerCase())) {
                        matchesFor = true;
                    } else {
                        // Fallback intelligent persona keywords
                        const forVal = state.for.toLowerCase();
                        if (forVal === 'him') matchesFor = itemSearchTerms.includes('him') || itemSearchTerms.includes('men') || itemSearchTerms.includes('birthday');
                        else if (forVal === 'her') matchesFor = itemSearchTerms.includes('her') || itemSearchTerms.includes('women') || itemSearchTerms.includes('princess') || itemSearchTerms.includes('proposal');
                        else if (forVal === 'kids') matchesFor = itemSearchTerms.includes('kid') || itemSearchTerms.includes('baby');
                        else if (forVal === 'friend') matchesFor = itemSearchTerms.includes('party') || itemSearchTerms.includes('dj') || itemSearchTerms.includes('club');
                        else if (forVal === 'wife') matchesFor = itemSearchTerms.includes('proposal') || itemSearchTerms.includes('anniversary') || itemSearchTerms.includes('cabana');
                        else if (forVal === 'husband') matchesFor = itemSearchTerms.includes('anniversary') || itemSearchTerms.includes('party');
                        else if (forVal === 'parents') matchesFor = itemSearchTerms.includes('parent') || itemSearchTerms.includes('jubilee');
                        else matchesFor = itemSearchTerms.includes(forVal) || itemTitle.includes(forVal);
                    }
                }

                if (matchesCat && matchesSub && matchesBudget && matchesFor) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Sorting
            const grid = document.getElementById('catalog-grid');
            const sortedItems = items.filter(i => i.style.display !== 'none');

            sortedItems.sort(function(a, b) {
                const priceA = parseInt(a.getAttribute('data-price') || '0', 10);
                const priceB = parseInt(b.getAttribute('data-price') || '0', 10);
                const titleA = (a.getAttribute('data-title') || '').toLowerCase();
                const titleB = (b.getAttribute('data-title') || '').toLowerCase();
                const reviewsA = parseInt(a.getAttribute('data-reviews') || '0', 10);
                const reviewsB = parseInt(b.getAttribute('data-reviews') || '0', 10);

                if (state.sort === 'price-low') return priceA - priceB;
                if (state.sort === 'price-high') return priceB - priceA;
                if (state.sort === 'name-az') return titleA.localeCompare(titleB);
                if (state.sort === 'rating') return reviewsB - reviewsA;
                return 0; // Default order
            });

            sortedItems.forEach(item => grid.appendChild(item));

            // Update Counters
            const emptyState = document.getElementById('catalog-empty-state');
            const countElem = document.getElementById('filtered-count-number');
            const drawerCountElem = document.getElementById('drawer-results-count');
            
            if (countElem) countElem.textContent = visibleCount;
            if (drawerCountElem) drawerCountElem.textContent = visibleCount;

            if (visibleCount === 0) {
                if (emptyState) emptyState.classList.remove('hidden');
                if (grid) grid.classList.add('hidden');
            } else {
                if (emptyState) emptyState.classList.add('hidden');
                if (grid) grid.classList.remove('hidden');
            }

            updateChipsAndPills();
            updateDrawerUI();
            updateUrl();
        }

        function updateChipsAndPills() {
            // Category Chip
            const catChip = document.getElementById('active-category-chip');
            if (catChip) {
                if (state.category !== 'all' && categoriesMap[state.category]) {
                    catChip.querySelector('.chip-label').textContent = 'Category: ' + categoriesMap[state.category].name;
                    catChip.style.display = 'inline-flex';
                } else {
                    catChip.style.display = 'none';
                }
            }

            // Subcategory Chip
            const subChip = document.getElementById('active-subcategory-chip');
            if (subChip) {
                if (state.subcategory !== 'all') {
                    subChip.querySelector('.chip-label').textContent = 'Theme: ' + state.subcategory;
                    subChip.style.display = 'inline-flex';
                } else {
                    subChip.style.display = 'none';
                }
            }

            // Budget Chip
            const budgetChip = document.getElementById('active-budget-chip');
            if (budgetChip) {
                if (state.budget !== 'all') {
                    const budgetLabels = {
                        'under-5k': 'Under ₹5,000',
                        '5k-7k': '₹5,000 - ₹7,000',
                        '7k-9k': '₹7,000 - ₹9,000',
                        'above-9k': 'Above ₹9,000'
                    };
                    budgetChip.querySelector('.chip-label').textContent = 'Budget: ' + (budgetLabels[state.budget] || state.budget);
                    budgetChip.style.display = 'inline-flex';
                } else {
                    budgetChip.style.display = 'none';
                }
            }

            // Recipient ("For Whom") Chip
            const forChip = document.getElementById('active-for-chip');
            if (forChip) {
                if (state.for !== 'all') {
                    forChip.querySelector('.chip-label').textContent = state.for.charAt(0).toUpperCase() + state.for.slice(1);
                    forChip.style.display = 'inline-flex';
                } else {
                    forChip.style.display = 'none';
                }
            }

            // Active Filters Badge Count
            let activeFilterCount = 0;
            if (state.category !== 'all') activeFilterCount++;
            if (state.subcategory !== 'all') activeFilterCount++;
            if (state.budget !== 'all') activeFilterCount++;
            if (state.for !== 'all') activeFilterCount++;
            if (state.sort !== 'default') activeFilterCount++;

            const filterBadge = document.getElementById('active-filter-count-badge');
            const filterBtn = document.getElementById('open-filter-btn');
            const clearAllLink = document.getElementById('clear-all-filters-link');

            if (filterBadge) {
                if (activeFilterCount > 0) {
                    filterBadge.textContent = activeFilterCount;
                    filterBadge.classList.remove('hidden');
                } else {
                    filterBadge.classList.add('hidden');
                }
            }
            if (filterBtn) {
                if (activeFilterCount > 0) {
                    filterBtn.classList.add('border-gray-900', 'bg-[#FAF7F2]');
                    filterBtn.classList.remove('bg-white');
                } else {
                    filterBtn.classList.remove('border-gray-900', 'bg-[#FAF7F2]');
                    filterBtn.classList.add('bg-white');
                }
            }
            if (clearAllLink) {
                if (activeFilterCount > 0) clearAllLink.classList.remove('hidden');
                else clearAllLink.classList.add('hidden');
            }
        }

        function updateDrawerUI() {
            // Category Buttons in Drawer
            document.querySelectorAll('.drawer-cat-btn').forEach(function(btn) {
                const cat = btn.getAttribute('data-drawer-cat');
                if (cat === state.category) {
                    btn.classList.add('bg-gray-950', 'text-white', 'border-gray-950');
                    btn.classList.remove('bg-white', 'text-gray-900', 'border-[#E8DFC8]');
                } else {
                    btn.classList.remove('bg-gray-950', 'text-white', 'border-gray-950');
                    btn.classList.add('bg-white', 'text-gray-900', 'border-[#E8DFC8]');
                }
            });

            // Accordion Groups highlighting & open state
            document.querySelectorAll('.category-accordion-group').forEach(function(group) {
                const cat = group.getAttribute('data-accordion-cat');
                const isCurrentCat = (cat === state.category);
                const content = group.querySelector('.accordion-sub-content');
                const chevron = group.querySelector('.accordion-chevron');

                if (isCurrentCat) {
                    group.classList.add('border-gray-900');
                    if (content && chevron) {
                        content.classList.remove('hidden');
                        chevron.classList.add('rotate-180');
                    }
                } else {
                    group.classList.remove('border-gray-900');
                }
            });

            // Subcategory Buttons in Drawer
            document.querySelectorAll('.drawer-sub-btn').forEach(function(btn) {
                const sub = btn.getAttribute('data-drawer-sub');
                if (sub === state.subcategory.toLowerCase() || (sub === `all-${state.category}` && state.subcategory === 'all')) {
                    btn.classList.add('bg-gray-950', 'text-white', 'border-gray-950');
                    btn.classList.remove('bg-white', 'text-gray-700', 'text-gray-800', 'border-[#E8DFC8]');
                } else {
                    btn.classList.remove('bg-gray-950', 'text-white', 'border-gray-950');
                    btn.classList.add('bg-white', 'text-gray-700', 'border-[#E8DFC8]');
                }
            });

            // Budget Buttons in Drawer
            document.querySelectorAll('.drawer-budget-btn').forEach(function(btn) {
                const b = btn.getAttribute('data-drawer-budget');
                if (b === state.budget) {
                    btn.classList.add('bg-gray-950', 'text-white', 'border-gray-950');
                    btn.classList.remove('bg-white', 'text-gray-800', 'border-[#E8DFC8]');
                } else {
                    btn.classList.remove('bg-gray-950', 'text-white', 'border-gray-950');
                    btn.classList.add('bg-white', 'text-gray-800', 'border-[#E8DFC8]');
                }
            });

            // Sort Buttons in Drawer
            document.querySelectorAll('.drawer-sort-btn').forEach(function(btn) {
                const s = btn.getAttribute('data-drawer-sort');
                if (s === state.sort) {
                    btn.classList.add('bg-gray-950', 'text-white', 'border-gray-950');
                    btn.classList.remove('bg-white', 'text-gray-800', 'border-[#E8DFC8]');
                } else {
                    btn.classList.remove('bg-gray-950', 'text-white', 'border-gray-950');
                    btn.classList.add('bg-white', 'text-gray-800', 'border-[#E8DFC8]');
                }
            });

            // Recipient / For Whom Buttons in Drawer
            document.querySelectorAll('.drawer-for-btn').forEach(function(btn) {
                const f = btn.getAttribute('data-drawer-for');
                if (f === state.for) {
                    btn.classList.add('bg-gray-950', 'text-white', 'border-gray-950');
                    btn.classList.remove('bg-white', 'text-gray-800', 'border-[#E8DFC8]');
                } else {
                    btn.classList.remove('bg-gray-950', 'text-white', 'border-gray-950');
                    btn.classList.add('bg-white', 'text-gray-800', 'border-[#E8DFC8]');
                }
            });
        }

        function updateUrl() {
            const params = new URLSearchParams();
            if (state.category !== 'all') params.set('category', state.category);
            if (state.subcategory !== 'all') params.set('theme', state.subcategory);
            if (state.budget !== 'all') params.set('budget', state.budget);
            if (state.for && state.for !== 'all') params.set('for', state.for);
            if (state.sort !== 'default') params.set('sort', state.sort);

            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.history.replaceState({}, '', newUrl);
        }

        // Global Actions
        window.openFilterDrawer = function() {
            const backdrop = document.getElementById('filter-drawer-backdrop');
            const drawer = document.getElementById('filter-drawer');
            const waWidget = document.getElementById('whatsapp-floating-widget');

            if (backdrop && drawer) {
                backdrop.classList.remove('pointer-events-none', 'opacity-0');
                backdrop.classList.add('opacity-100');
                drawer.classList.remove('translate-x-full');
                
                // Prevent background page from scrolling
                document.documentElement.style.overflow = 'hidden';
                document.body.style.overflow = 'hidden';
                document.body.style.touchAction = 'none';

                // Hide floating WhatsApp button when sidebar is open
                if (waWidget) {
                    waWidget.style.display = 'none';
                }
            }
        };

        window.closeFilterDrawer = function() {
            const backdrop = document.getElementById('filter-drawer-backdrop');
            const drawer = document.getElementById('filter-drawer');
            const waWidget = document.getElementById('whatsapp-floating-widget');

            if (backdrop && drawer) {
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                drawer.classList.add('translate-x-full');
                
                // Restore background page scrolling
                document.documentElement.style.overflow = '';
                document.body.style.overflow = '';
                document.body.style.touchAction = '';

                // Restore floating WhatsApp button
                if (waWidget) {
                    waWidget.style.display = '';
                }
            }
        };

        window.toggleCategoryAccordion = function(catSlug) {
            const group = document.querySelector(`.category-accordion-group[data-accordion-cat="${catSlug}"]`);
            if (!group) return;
            const content = group.querySelector('.accordion-sub-content');
            const chevron = group.querySelector('.accordion-chevron');
            
            const isHidden = content.classList.contains('hidden');
            
            // Close other accordions
            document.querySelectorAll('.category-accordion-group').forEach(function(g) {
                const c = g.querySelector('.accordion-sub-content');
                const ch = g.querySelector('.accordion-chevron');
                if (c) c.classList.add('hidden');
                if (ch) ch.classList.remove('rotate-180');
            });

            if (isHidden) {
                content.classList.remove('hidden');
                chevron.classList.add('rotate-180');
            }
        };

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.closeFilterDrawer();
            }
        });

        window.selectCategoryFilter = function(catSlug, btnElem) {
            state.category = catSlug;
            state.subcategory = 'all'; // Reset subcategory when category changes

            // Update Tab UI
            document.querySelectorAll('.cat-tab-pill').forEach(function(pill) {
                const isSelected = (pill.getAttribute('data-cat-slug') === catSlug);
                pill.className = 'cat-tab-pill shrink-0 inline-flex items-center px-4 py-2 rounded-xl text-xs sm:text-[13px] font-heading font-bold transition-colors duration-150 cursor-pointer ' + 
                    (isSelected ? 'bg-gray-950 text-white' : 'bg-white text-gray-700 hover:text-gray-950 border border-[#E8DFC8] hover:border-gray-400');
            });

            if (btnElem && typeof btnElem.scrollIntoView === 'function') {
                btnElem.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            }

            filterAndSortCatalog();
        };

        window.selectSubcategoryFilter = function(subName) {
            state.subcategory = subName;
            filterAndSortCatalog();
        };

        window.setDrawerCategory = function(catSlug) {
            state.category = catSlug;
            state.subcategory = 'all';
            
            // Sync top pill
            const pill = document.querySelector(`.cat-tab-pill[data-cat-slug="${catSlug}"]`);
            if (pill) {
                window.selectCategoryFilter(catSlug, pill);
            } else {
                filterAndSortCatalog();
            }
        };

        window.setDrawerSubcategory = function(catSlug, subName) {
            state.category = catSlug;
            state.subcategory = subName;
            
            // Sync top pill
            const pill = document.querySelector(`.cat-tab-pill[data-cat-slug="${catSlug}"]`);
            if (pill) {
                document.querySelectorAll('.cat-tab-pill').forEach(function(p) {
                    const isSelected = (p.getAttribute('data-cat-slug') === catSlug);
                    p.className = 'cat-tab-pill shrink-0 inline-flex items-center px-4 py-2 rounded-xl text-xs sm:text-[13px] font-heading font-bold transition-colors duration-150 cursor-pointer ' + 
                        (isSelected ? 'bg-gray-950 text-white' : 'bg-white text-gray-700 hover:text-gray-950 border border-[#E8DFC8] hover:border-gray-400');
                });
            }

            filterAndSortCatalog();
        };

        window.setDrawerBudget = function(val) {
            state.budget = val;
            filterAndSortCatalog();
        };

        window.setDrawerSort = function(val) {
            state.sort = val;
            filterAndSortCatalog();
        };

        window.handleBudgetFilter = function(val) {
            state.budget = val;
            filterAndSortCatalog();
        };

        window.setDrawerFor = function(val) {
            state.for = val;
            filterAndSortCatalog();
        };

        window.resetAllCatalogFilters = function() {
            state.category = 'all';
            state.subcategory = 'all';
            state.budget = 'all';
            state.for = 'all';
            state.sort = 'default';

            const allTab = document.querySelector('.cat-tab-pill[data-cat-slug="all"]');
            window.selectCategoryFilter('all', allTab);
        };

        document.addEventListener('DOMContentLoaded', function() {
            filterAndSortCatalog();
        });
    })();
</script>

<style>
    /* Absolute reset for cards: Zero hover animations, zero shadows, zero image zoom */
    #catalog-grid,
    #catalog-grid *,
    #catalog-grid .catalog-item-card,
    #catalog-grid .catalog-item-card *,
    #catalog-grid .catalog-item-card a,
    #catalog-grid .catalog-item-card a:hover,
    #catalog-grid .catalog-item-card a:focus,
    #catalog-grid .catalog-item-card a:active,
    .catalog-item-card,
    .catalog-item-card *,
    .catalog-item-card a,
    .catalog-item-card a:hover,
    .catalog-item-card a:focus,
    .catalog-item-card a:active {
        box-shadow: none !important;
        -webkit-box-shadow: none !important;
        transform: none !important;
        -webkit-transform: none !important;
        transition: none !important;
        -webkit-transition: none !important;
        animation: none !important;
        -webkit-animation: none !important;
        text-decoration: none !important;
    }
    #catalog-grid img,
    #catalog-grid a img,
    #catalog-grid a:hover img,
    #catalog-grid img:hover,
    .catalog-item-card img,
    .catalog-item-card a img,
    .catalog-item-card a:hover img,
    .catalog-item-card img:hover {
        transform: none !important;
        -webkit-transform: none !important;
        scale: none !important;
        transition: none !important;
        -webkit-transition: none !important;
        animation: none !important;
        filter: none !important;
        -webkit-filter: none !important;
    }

    #open-filter-btn,
    #open-filter-btn:focus {
        box-shadow: none !important;
        -webkit-box-shadow: none !important;
        transform: none !important;
        -webkit-transform: none !important;
        outline: none !important;
    }
</style>
@endsection

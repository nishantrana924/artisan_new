<!-- ARTIZEN — Shop By Bestsellers (Clean Luxury Service Card UI) -->
<section id="events" class="py-10 sm:py-14 bg-[#FDF8F0] relative scroll-mt-20 md:scroll-mt-24 select-none" style="background-color: #FDF8F0 !important;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @php
            // ------------------------------------------------------------------
            // 100% Dynamic: Automatically load categories & packages from DB
            // Any category that has event packages marked BESTSELLER or LUXURY
            // will automatically appear as a category tab and show its packages.
            // ------------------------------------------------------------------
            $activeDbCategories = \App\Models\Category::where('active', true)
                ->orderBy('display_order', 'asc')
                ->orderBy('id', 'asc')
                ->with(['packages' => function ($query) {
                    $query->where('active', true)
                        ->where(function ($q) {
                            $q->whereRaw("UPPER(TRIM(COALESCE(badge, ''))) = ?", ['BESTSELLER'])
                              ->orWhereRaw("UPPER(TRIM(COALESCE(badge, ''))) = ?", ['LUXURY'])
                              ->orWhereRaw("UPPER(COALESCE(badge, '')) LIKE ?", ['%BESTSELLER%'])
                              ->orWhereRaw("UPPER(COALESCE(badge, '')) LIKE ?", ['%LUXURY%']);
                        })
                        ->orderByRaw("CASE WHEN UPPER(COALESCE(badge, '')) LIKE '%BESTSELLER%' THEN 1 WHEN UPPER(COALESCE(badge, '')) LIKE '%LUXURY%' THEN 2 ELSE 3 END")
                        ->orderBy('id', 'asc')
                        ->with('subcategories');
                }])
                ->get();

            $bestsellerCategories = [];

            foreach ($activeDbCategories as $cat) {
                // Only include category if it has at least one BESTSELLER or LUXURY package
                if ($cat->packages->isEmpty()) {
                    continue;
                }

                $catKey = 'cat-bestseller-' . $cat->id;
                $catImg = $cat->dropdown_image 
                    ? asset($cat->dropdown_image) 
                    : ($cat->slider_image ? asset($cat->slider_image) : ($cat->image ? asset($cat->image) : asset('images/dropdowns/birthdays.webp')));

                $items = [];
                foreach ($cat->packages as $p) {
                    $subcat = $p->subcategories->first()?->name ?? ($p->tag ?: 'Celebration Setup');
                    $price = (int)$p->price;
                    $items[] = [
                        'id'             => $p->id,
                        'slug'           => $p->slug,
                        'subcategory'    => $subcat,
                        'title'          => $p->title,
                        'price'          => '₹' . number_format($price),
                        'raw_price'      => $price,
                        'original_price' => (int)($p->original_price ?: round($price * 1.15)),
                        'image'          => $p->image ? asset($p->image) : $catImg,
                        'badge'          => $p->badge ?: 'BESTSELLER',
                        'route'          => route('events.show', ['slug' => $p->slug]),
                    ];
                }

                $bestsellerCategories[$catKey] = [
                    'id'    => $cat->id,
                    'name'  => $cat->title,
                    'img'   => $catImg,
                    'alt'   => $cat->title,
                    'items' => $items,
                ];
            }

            $activeInitialSlug = !empty($bestsellerCategories) ? array_key_first($bestsellerCategories) : '';
        @endphp

        <!-- Section Header (Warm Luxury Champagne Header) -->
        <div class="mb-6 sm:mb-8">
            <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-gray-950 tracking-tight mb-1.5">
                Shop By Bestsellers
            </h2>
            <p class="text-xs sm:text-sm text-gray-700 max-w-2xl font-normal leading-relaxed">
                Discover India's favourite celebration setups, curated bestsellers that make every celebration extra special
            </p>

            <!-- Circular Avatar Filter Tabs with Smooth Sliding Active Line -->
            <div id="category-tabs-track" 
                 class="relative flex items-center gap-6 sm:gap-8 overflow-x-auto scrollbar-hide pt-5 border-b border-[#EFE7D8]"
                 style="scrollbar-width: none; -ms-overflow-style: none;">
                
                @foreach($bestsellerCategories as $slug => $cat)
                    @php $isActive = ($slug === $activeInitialSlug); @endphp
                    <button type="button" 
                            onclick="filterEventCategory('{{ $slug }}', this)"
                            class="event-filter-tab relative flex items-center gap-2.5 pb-3 shrink-0 transition-colors duration-200 cursor-pointer {{ $isActive ? 'text-gray-950 font-bold active' : 'text-gray-600 hover:text-gray-950 font-medium' }}">
                        
                        <!-- Circular Thumbnail -->
                        <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-full overflow-hidden border border-[#E0D2B8] p-0.5 bg-white shrink-0 shadow-2xs pointer-events-none">
                            <img src="{{ $cat['img'] }}" alt="{{ $cat['alt'] }}" class="w-full h-full object-cover rounded-full pointer-events-none" draggable="false">
                        </span>
                        
                        <!-- Label -->
                        <span class="text-xs sm:text-[13.5px] whitespace-nowrap">{{ $cat['name'] }}</span>
                    </button>
                @endforeach

                <!-- Smooth Sliding Active Indicator Bar (Animates Left / Right) -->
                <div id="category-sliding-indicator" 
                     class="absolute bottom-0 h-[2.5px] bg-gray-950 rounded-t-sm pointer-events-none z-10 transition-all duration-300 ease-out"
                     style="left: 0; width: 0; transform: translateX(0);">
                </div>

            </div>
        </div>

        <!-- Dynamic Category Packages Container -->
        <div id="packages-container" class="w-full">
            @foreach($bestsellerCategories as $catSlug => $catData)
                @php $isInitiallyVisible = ($catSlug === $activeInitialSlug); @endphp
                <div class="category-package-group {{ $isInitiallyVisible ? '' : 'hidden' }}" 
                     data-category-group="{{ $catSlug }}">
                    
                    <!-- 4-Column Clean Grid on Desktop, 2-Column on Tablet/Mobile -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 lg:gap-4">
                        @foreach($catData['items'] as $item)
                            @php
                                $itemPrice = is_numeric($item['price']) ? (int)$item['price'] : (int)preg_replace('/[^0-9]/', '', $item['price']);
                                $origPrice = !empty($item['original_price']) ? (int)$item['original_price'] : round($itemPrice * 1.15);
                                $discountPercent = ($origPrice > $itemPrice) ? round((($origPrice - $itemPrice) / $origPrice) * 100) : 15;
                                $cardUrl = $item['route'] ?? route('events.show', ['slug' => $item['slug'] ?? \Illuminate\Support\Str::slug($item['title'])]);
                            @endphp

                            <a href="{{ $cardUrl }}" 
                               class="block w-full bg-white rounded-2xl border border-[#E8DFC8] overflow-hidden shadow-none text-left flex flex-col justify-between h-full select-none cursor-pointer">
                                
                                <!-- Image Box (Aspect 4/3.8 Crisp & Clean, No Hover Zoom) -->
                                <div class="relative w-full aspect-[4/3.8] overflow-hidden bg-[#FAF7F2] shrink-0">
                                    <img src="{{ $item['image'] }}" 
                                         alt="{{ $item['title'] }}" 
                                         class="w-full h-full object-cover select-none pointer-events-none" 
                                         draggable="false"
                                         loading="lazy">
                                    
                                    <!-- Rating Badge Top-Right -->
                                    <span class="absolute top-2.5 right-2.5 bg-white/95 backdrop-blur-xs px-2 py-0.5 rounded text-[11px] font-bold text-gray-900 flex items-center gap-1 shadow-2xs z-20">
                                        <i class="fa-solid fa-star text-amber-400 text-[10px]"></i>
                                        <span>4.9</span>
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
                                                {{ $discountPercent }}% OFF
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </a>
                        @endforeach
                    </div>

                </div>
            @endforeach
        </div>

        <!-- Custom Event CTA Banner with Clean Realistic Visual Background (No Arrow) -->
        <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl border border-[#E8DFC8] shadow-xs max-w-5xl mx-auto mt-12 sm:mt-16 bg-[#FFFDF9]">
            
            <!-- Background Image Layer (Positioned on the Right Side) -->
            <div class="absolute inset-0 bg-right bg-no-repeat bg-cover opacity-95 pointer-events-none hidden sm:block"
                 style="background-image: url('{{ asset('images/banners/custom-event-banner.webp') }}'); background-position: right center;">
            </div>

            <!-- Soft Gradient Overlay for Seamless Text Readability -->
            <div class="absolute inset-0 bg-gradient-to-r from-[#FFFDF9] via-[#FFFDF9]/90 to-transparent pointer-events-none"></div>

            <!-- Content Area (Spacious Left Side) -->
            <div class="relative z-10 px-6 py-8 sm:px-10 sm:py-12 max-w-xl text-left">
                
                <h3 class="font-heading font-extrabold text-2xl sm:text-3xl text-gray-950 tracking-tight mb-2.5 leading-tight">
                    Can't Find Your Perfect Package?
                </h3>
                
                <p class="text-xs sm:text-sm text-gray-700 font-normal leading-relaxed mb-6">
                    Create a custom event tailored to your specific budget, favorite theme, DJ sound rig, and celebration requirements.
                </p>
                
                <!-- Dual Action CTAs: WhatsApp + Contact Specialist -->
                <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap">
                    <a href="https://wa.me/919109109100?text=Hi%20Artizen,%20I%20want%20to%20plan%20a%20custom%20celebration%20event%20in%20Indore" 
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

    </div>
</section>

<script>
    function updateCategoryIndicator(btn, instant = false) {
        const indicator = document.getElementById('category-sliding-indicator');
        if (!indicator || !btn) return;

        if (instant) {
            indicator.style.transition = 'none';
        }

        const left = btn.offsetLeft;
        const width = btn.offsetWidth;

        indicator.style.transform = `translateX(${left}px)`;
        indicator.style.width = `${width}px`;

        if (instant) {
            // Force reflow and re-enable smooth transition for user clicks
            void indicator.offsetWidth;
            indicator.style.transition = '';
        }
    }

    function filterEventCategory(targetSlug, btn) {
        // 1. Update active tab text styling
        document.querySelectorAll('.event-filter-tab').forEach(t => {
            t.classList.remove('text-gray-950', 'font-bold', 'active');
            t.classList.add('text-gray-600', 'font-medium');
        });

        if (btn) {
            btn.classList.add('text-gray-950', 'font-bold', 'active');
            btn.classList.remove('text-gray-600', 'font-medium');
            
            // Smoothly slide the indicator line to the clicked button
            updateCategoryIndicator(btn, false);
        }

        // 2. Show only the clicked category's packages
        const groups = document.querySelectorAll('.category-package-group');
        groups.forEach(g => {
            if (g.getAttribute('data-category-group') === targetSlug) {
                g.classList.remove('hidden');
            } else {
                g.classList.add('hidden');
            }
        });
    }

    // Position indicator on page load and recalculate after fonts load
    function initCategoryIndicator(instant = false) {
        const activeTab = document.querySelector('.event-filter-tab.active');
        if (activeTab) {
            updateCategoryIndicator(activeTab, instant);
        }
    }

    // Run immediately without transition animation
    initCategoryIndicator(true);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => initCategoryIndicator(true));
    }

    // Recalculate dimensions once custom web fonts finish loading
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(() => initCategoryIndicator(false));
    }

    window.addEventListener('load', () => initCategoryIndicator(false));
    window.addEventListener('resize', () => initCategoryIndicator(true));
</script>

<style>
    /* Strictly disable all card shadows, hover animations and image transforms */
    #events a,
    #events a:hover,
    .category-package-group a,
    .category-package-group a:hover {
        box-shadow: none !important;
        transform: none !important;
        transition: none !important;
    }
    #events img,
    #events a:hover img,
    .category-package-group img,
    .category-package-group a:hover img {
        transform: none !important;
        scale: none !important;
        transition: none !important;
        animation: none !important;
    }
</style>

<!-- ARTIZEN — 3 Relatable Celebration Dimensions (Birthdays & Family, Proposals & Romance, Weddings & DJ Parties) -->
<section id="relatable-celebrations" class="py-12 sm:py-16 bg-[#FCFBF8] relative select-none border-t border-[#F2ECE0]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Main Section Header -->
        <div class="mb-6 sm:mb-8 text-left">
            <h2 class="font-heading font-extrabold text-2xl sm:text-3xl md:text-[34px] text-gray-950 tracking-tight leading-tight">
                How Would You Like to Celebrate?
            </h2>
        </div>

        <div class="flex flex-col gap-10 sm:gap-14">

            <!-- ========================================================
                 ROW 1: BIRTHDAYS & FAMILY CELEBRATIONS
                 ======================================================== -->
            @php
                $row1CatIds = [1, 6]; // Birthdays, Baby Shower & Kids
                $row2CatIds = [3, 4]; // Proposals, Anniversaries
                $row3CatIds = [2, 5, 7, 8]; // House Party & DJ, Weddings & Sangeet, Corporate, Live DJ & Acoustic

                $allRelatablePkgs = \App\Models\Package::with(['category', 'subcategories'])
                    ->where('active', true)
                    ->orderByRaw("CASE WHEN UPPER(COALESCE(badge, '')) LIKE '%BESTSELLER%' THEN 1 WHEN UPPER(COALESCE(badge, '')) LIKE '%POPULAR%' THEN 2 ELSE 3 END")
                    ->orderBy('id', 'asc')
                    ->get();

                $birthdayCelebrations = $allRelatablePkgs->filter(fn($p) => in_array($p->category_id, $row1CatIds) || in_array($p->category?->nav_slug, ['cat-birthdays', 'cat-kids-cozy']))->map(function($p) {
                    $price = (int)$p->price;
                    return [
                        'id'             => $p->id,
                        'slug'           => $p->slug,
                        'subcategory'    => $p->subcategories->first()?->name ?? ($p->tag ?: 'Birthday Setup'),
                        'title'          => $p->title,
                        'price'          => '₹' . number_format($price),
                        'raw_price'      => $price,
                        'original_price' => (int)($p->original_price ?: round($price * 1.15)),
                        'image'          => $p->image ?: asset('images/dropdowns/birthdays.webp'),
                        'badge'          => $p->badge ?: '15% OFF',
                        'category'       => $p->category?->nav_slug ?? 'cat-birthdays',
                    ];
                });

                $romanticCelebrations = $allRelatablePkgs->filter(fn($p) => in_array($p->category_id, $row2CatIds) || $p->category?->nav_slug === 'cat-proposal-anniversary')->map(function($p) {
                    $price = (int)$p->price;
                    return [
                        'id'             => $p->id,
                        'slug'           => $p->slug,
                        'subcategory'    => $p->subcategories->first()?->name ?? ($p->tag ?: 'Romantic Setup'),
                        'title'          => $p->title,
                        'price'          => '₹' . number_format($price),
                        'raw_price'      => $price,
                        'original_price' => (int)($p->original_price ?: round($price * 1.15)),
                        'image'          => $p->image ?: asset('images/dropdowns/proposals.webp'),
                        'badge'          => $p->badge ?: '15% OFF',
                        'category'       => $p->category?->nav_slug ?? 'cat-proposal-anniversary',
                    ];
                });

                $partyCelebrations = $allRelatablePkgs->filter(fn($p) => in_array($p->category_id, $row3CatIds) || in_array($p->category?->nav_slug, ['cat-house-party', 'cat-weddings-sangeet', 'cat-dj-acoustic', 'cat-baby-corporate']))->map(function($p) {
                    $price = (int)$p->price;
                    return [
                        'id'             => $p->id,
                        'slug'           => $p->slug,
                        'subcategory'    => $p->subcategories->first()?->name ?? ($p->tag ?: 'Party Setup'),
                        'title'          => $p->title,
                        'price'          => '₹' . number_format($price),
                        'raw_price'      => $price,
                        'original_price' => (int)($p->original_price ?: round($price * 1.15)),
                        'image'          => $p->image ?: asset('images/dropdowns/house-party.webp'),
                        'badge'          => $p->badge ?: '15% OFF',
                        'category'       => $p->category?->nav_slug ?? 'cat-house-party',
                    ];
                });
            @endphp

            <div class="relative">
                <!-- Row Header -->
                <div class="mb-3 sm:mb-3.5">
                    <h3 class="font-heading font-extrabold text-lg sm:text-xl text-gray-950 tracking-tight">
                        Birthdays & Kids
                    </h3>
                </div>

                <!-- Swiper Rail -->
                <div class="swiper celebrations-swiper overflow-hidden">
                    <div class="swiper-wrapper">
                        @foreach($birthdayCelebrations as $item)
                            <div class="swiper-slide">
                                <a href="{{ route('events.show', ['slug' => $item['slug'] ?? \Illuminate\Support\Str::slug($item['title'])]) }}" 
                                   draggable="false"
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

                                        @php
                                            $itemPrice = is_numeric($item['price']) ? (int)$item['price'] : (int)preg_replace('/[^0-9]/', '', $item['price']);
                                            $origPrice = round($itemPrice * 1.15);
                                        @endphp

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
                </div>
            </div>


            <!-- ========================================================
                 ROW 2: PROPOSALS & ROMANTIC MILESTONES
                 ======================================================== -->


            <div class="relative">
                <!-- Row Header -->
                <div class="mb-3 sm:mb-3.5">
                    <h3 class="font-heading font-extrabold text-lg sm:text-xl text-gray-950 tracking-tight">
                        Proposals & Anniversaries
                    </h3>
                </div>

                <!-- Swiper Rail -->
                <div class="swiper celebrations-swiper overflow-hidden">
                    <div class="swiper-wrapper">
                        @foreach($romanticCelebrations as $item)
                            <div class="swiper-slide">
                                <a href="{{ route('events.show', ['slug' => $item['slug'] ?? \Illuminate\Support\Str::slug($item['title'])]) }}" 
                                   draggable="false"
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

                                        @php
                                            $itemPrice = is_numeric($item['price']) ? (int)$item['price'] : (int)preg_replace('/[^0-9]/', '', $item['price']);
                                            $origPrice = round($itemPrice * 1.15);
                                        @endphp

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
                </div>
            </div>


            <!-- ========================================================
                 ROW 3: WEDDINGS, SANGEET & LIVE DJ PARTIES
                 ======================================================== -->


            <div class="relative">
                <!-- Row Header -->
                <div class="mb-3 sm:mb-3.5">
                    <h3 class="font-heading font-extrabold text-lg sm:text-xl text-gray-950 tracking-tight">
                        Weddings & DJ Parties
                    </h3>
                </div>

                <!-- Swiper Rail -->
                <div class="swiper celebrations-swiper overflow-hidden">
                    <div class="swiper-wrapper">
                        @foreach($partyCelebrations as $item)
                            <div class="swiper-slide">
                                <a href="{{ route('events.show', ['slug' => $item['slug'] ?? \Illuminate\Support\Str::slug($item['title'])]) }}" 
                                   draggable="false"
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

                                        @php
                                            $itemPrice = is_numeric($item['price']) ? (int)$item['price'] : (int)preg_replace('/[^0-9]/', '', $item['price']);
                                            $origPrice = round($itemPrice * 1.15);
                                        @endphp

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
                </div>
            </div>

        </div>

    </div>
</section>

<style>
    /* Rail layout with overflow strictly hidden to prevent blank trailing whitespace */
    .celebrations-swiper {
        position: relative;
        overflow: hidden !important;
        width: 100%;
        contain: layout;
    }

    .celebrations-swiper .swiper-wrapper {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        box-sizing: border-box;
        will-change: transform;
    }

    /* Fixed slide widths for exact precision matching across all rows */
    .celebrations-swiper .swiper-slide {
        flex-shrink: 0 !important;
        box-sizing: border-box;
        width: 200px !important;
    }
    @media (min-width: 640px) {
        .celebrations-swiper .swiper-slide {
            width: 230px !important;
        }
    }
    @media (min-width: 1024px) {
        .celebrations-swiper .swiper-slide {
            width: 240px !important;
        }
    }

    /* Pre-Swiper spacing ONLY before JS initializes to prevent double-margin calculation */
    .celebrations-swiper:not(.swiper-initialized) .swiper-slide {
        margin-right: 14px;
    }
    @media (min-width: 640px) {
        .celebrations-swiper:not(.swiper-initialized) .swiper-slide {
            margin-right: 18px;
        }
    }
    @media (min-width: 1024px) {
        .celebrations-swiper:not(.swiper-initialized) .swiper-slide {
            margin-right: 20px;
        }
    }

    /* Strictly disable all card shadows, hover animations and image transforms */
    .celebrations-swiper a {
        -webkit-user-drag: none;
        user-drag: none;
        user-select: none;
    }
    #relatable-celebrations a,
    #relatable-celebrations a:hover,
    #relatable-celebrations .swiper-slide a,
    #relatable-celebrations .swiper-slide a:hover {
        box-shadow: none !important;
        transform: none !important;
        transition: none !important;
    }
    #relatable-celebrations img,
    #relatable-celebrations a:hover img,
    #relatable-celebrations .swiper-slide:hover img {
        transform: none !important;
        scale: none !important;
        transition: none !important;
        animation: none !important;
    }
</style>

<script>
    (function() {
        function initCelebrationsSwipers() {
            if (typeof Swiper === 'undefined') {
                requestAnimationFrame(initCelebrationsSwipers);
                return;
            }

            const swiperConfig = {
                slidesPerView: 'auto',
                spaceBetween: 14,
                grabCursor: true,
                simulateTouch: true,
                touchRatio: 1,
                threshold: 5,
                preventClicks: true,
                preventClicksPropagation: true,
                touchStartPreventDefault: false,
                watchOverflow: true,
                watchSlidesProgress: true,
                resistance: true,
                resistanceRatio: 0,
                touchReleaseOnEdges: true,
                observer: true,
                observeParents: true,
                resizeObserver: true,
                breakpoints: {
                    640: { spaceBetween: 18 },
                    1024: { spaceBetween: 20 },
                },
            };

            document.querySelectorAll('.celebrations-swiper').forEach(function(el) {
                if (el.swiper) {
                    el.swiper.update();
                } else {
                    new Swiper(el, swiperConfig);
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCelebrationsSwipers);
        } else {
            initCelebrationsSwipers();
        }
        window.addEventListener('load', initCelebrationsSwipers);
    })();
</script>

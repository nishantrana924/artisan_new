<style>
    /* CSS slide spacing fallback: eliminates 0px gap flash (FOUC) on page refresh */
    .occasion-swiper {
        min-height: 145px;
        contain: layout;
    }
    .occasion-swiper .swiper-wrapper {
        display: flex;
    }
    .occasion-swiper .swiper-slide {
        width: auto !important;
        flex-shrink: 0;
        margin-right: 14px;
    }
    .occasion-swiper .swiper-slide:last-child {
        margin-right: 0;
    }

    .hero-banner-swiper {
        min-height: 165px;
        contain: layout;
    }
    .hero-banner-swiper .swiper-wrapper {
        display: flex;
    }
    .hero-banner-swiper .swiper-slide {
        flex-shrink: 0;
        margin-right: 16px;
    }
    @media (min-width: 640px) {
        .hero-banner-swiper {
            min-height: 185px;
        }
        .hero-banner-swiper .swiper-slide {
            margin-right: 20px;
        }
    }
    @media (min-width: 768px) {
        .hero-banner-swiper {
            min-height: 205px;
        }
    }
    @media (min-width: 1024px) {
        .hero-banner-swiper {
            min-height: 215px;
        }
        .hero-banner-swiper .swiper-slide {
            margin-right: 24px;
        }
    }

    /* Completely eliminate any hover card effects, glow, or box-shadow on Tier 4 Trust Bar */
    .trust-pillar-item,
    .trust-pillar-item:hover,
    .trust-pillar-item:focus {
        transform: none !important;
        box-shadow: none !important;
        background: transparent !important;
        border: none !important;
        outline: none !important;
        transition: none !important;
    }
</style>

<!-- Artizen Multi-Tier Event Discovery Hero Hub (Inspired by IGP & FNP Occasion Discovery Architecture) -->
<section class="w-full bg-[#FAF9F6] dark:bg-[#0F0F12] border-b border-[#E6E2D8] dark:border-[#232326] transition-colors overflow-hidden select-none">



    <!-- ==========================================
         TIER 2: "SETUPS FOR EVERY OCCASION"
         (Dynamic Pastel Occasion Capsules — Powered by Admin Categories)
         ========================================== -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8 pb-3">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl sm:text-2xl md:text-3xl font-heading font-extrabold text-[#111827] dark:text-white tracking-tight">
                Setups For Every Occasion
            </h2>
            <a href="{{ route('events.index') }}" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-heading font-bold text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-[#FFD600] transition-colors group">
                <span>View All Setups</span>
                <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>

        <!-- Swiper.js Occasion Rail -->
        <div class="swiper occasion-swiper overflow-hidden cursor-grab active:cursor-grabbing select-none">
            <div class="swiper-wrapper py-1">

                @foreach($navCategories as $ocasionCat)
                @php
                    $navSlug   = $ocasionCat->nav_slug ?? ('cat-' . \Illuminate\Support\Str::slug($ocasionCat->title));
                    $bgColor   = $ocasionCat->bg_color ?? '#F6CFB2';
                    $sliderImg = $ocasionCat->slider_image ?? null;
                    $altText   = $ocasionCat->title . ' Events';
                @endphp
                <div class="swiper-slide !w-auto">
                    <a href="{{ route('events.index', ['category' => $navSlug]) }}"
                       draggable="false"
                       class="relative shrink-0 w-[220px] sm:w-[255px] h-[135px] sm:h-[145px] rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xs select-none block"
                       style="background: {{ $bgColor }};">
                        @if($sliderImg)
                        <img src="{{ $sliderImg }}"
                             alt="{{ $altText }}"
                             draggable="false"
                             class="w-full h-full object-cover object-right block select-none pointer-events-none"
                             style="user-select: none; -webkit-user-drag: none;"
                             loading="eager" decoding="async">
                        @endif
                        <div class="absolute top-3.5 left-4 z-10 pointer-events-none select-none">
                            <span class="text-[14px] sm:text-[15px] font-heading font-extrabold text-gray-900 dark:text-white flex items-center gap-1 drop-shadow-sm">
                                {{ $ocasionCat->title }} <i class="fa-solid fa-angle-right text-[11px] opacity-75"></i>
                            </span>
                        </div>
                    </a>
                </div>
                @endforeach

            </div>
        </div>
    </div>


    <!-- ==========================================
         TIER 3: PANORAMIC PROMOTIONAL HERO BANNER CAROUSEL
         (Compact Width, Centered Alignment, High-Contrast Typography & Side Peeks)
         ========================================== -->
    <div class="w-full overflow-hidden pt-3 pb-8 sm:pb-10 relative select-none">
        
        <!-- Relative Wrapper for Swiper -->
        <div class="relative w-full max-w-full">

            <!-- Swiper Container with Centered Compact Slides & Side Peeks -->
            <div class="swiper hero-banner-swiper overflow-visible cursor-grab active:cursor-grabbing">
                <div class="swiper-wrapper py-1">

                    <!-- Slide 1: Blooming Love & Celebrations (Deep Navy Floral) -->
                    <div class="swiper-slide !w-[86vw] sm:!w-[520px] md:!w-[600px] lg:!w-[680px] xl:!w-[720px]">
                        <a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" 
                           draggable="false"
                           class="block relative w-full h-[165px] sm:h-[185px] md:h-[205px] lg:h-[215px] rounded-2xl sm:rounded-3xl overflow-hidden shadow-md cursor-pointer select-none group"
                           style="background-color: #0c1b33 !important;">
                            <img src="{{ asset('images/banners/banner-navy-floral.webp') }}" 
                                 alt="Blooming Love Celebrations" 
                                 draggable="false"
                                 class="absolute inset-0 w-full h-full object-cover object-right pointer-events-none select-none" 
                                 style="user-select: none; -webkit-user-drag: none;"
                                 loading="eager">
                            
                            <!-- Left Gradient Wash for High Contrast -->
                            <div class="absolute inset-0 bg-gradient-to-r from-[#0c1b33]/95 via-[#0c1b33]/75 to-transparent pointer-events-none"></div>

                            <!-- Left Typography & CTA Button -->
                            <div class="relative z-10 p-4 sm:p-6 md:p-7 max-w-[65%] sm:max-w-sm text-left">
                                <h2 class="text-base sm:text-lg md:text-xl lg:text-2xl font-heading font-extrabold tracking-tight leading-tight mb-1 sm:mb-1.5" style="color: #ffffff !important;">
                                    Blooming Love,<br>Wrapped in Flowers
                                </h2>
                                <p class="font-body text-[11px] sm:text-xs mb-2.5 sm:mb-3 font-normal leading-relaxed line-clamp-2" style="color: rgba(255, 255, 255, 0.85) !important;">
                                    Send freshly-sourced blooms to brighten their special day
                                </p>
                                <span class="inline-flex items-center gap-1 px-3 sm:px-4 py-1.5 rounded-lg font-heading font-bold text-[11px] sm:text-xs shadow-md transition-all"
                                      style="background-color: #ffffff !important; color: #0c1b33 !important;">
                                    <span style="color: #0c1b33 !important;">Order Now</span>
                                    <i class="fa-solid fa-angle-right text-[9px] group-hover:translate-x-0.5 transition-transform" style="color: #0c1b33 !important;"></i>
                                </span>
                            </div>
                        </a>
                    </div>

                    <!-- Slide 2: Royal Haldi & Weddings (Deep Emerald Green) -->
                    <div class="swiper-slide !w-[86vw] sm:!w-[520px] md:!w-[600px] lg:!w-[680px] xl:!w-[720px]">
                        <a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" 
                           draggable="false"
                           class="block relative w-full h-[165px] sm:h-[185px] md:h-[205px] lg:h-[215px] rounded-2xl sm:rounded-3xl overflow-hidden shadow-md cursor-pointer select-none group"
                           style="background-color: #0a291e !important;">
                            <img src="{{ asset('images/banners/banner-emerald-wedding.webp') }}" 
                                 alt="Royal Haldi & Wedding Setups" 
                                 draggable="false"
                                 class="absolute inset-0 w-full h-full object-cover object-right pointer-events-none select-none" 
                                 style="user-select: none; -webkit-user-drag: none;"
                                 loading="eager" decoding="async">
                            
                            <!-- Left Gradient Wash -->
                            <div class="absolute inset-0 bg-gradient-to-r from-[#0a291e]/95 via-[#0a291e]/75 to-transparent pointer-events-none"></div>

                            <!-- Left Typography & CTA Button -->
                            <div class="relative z-10 p-4 sm:p-6 md:p-7 max-w-[65%] sm:max-w-sm text-left">
                                <h2 class="text-base sm:text-lg md:text-xl lg:text-2xl font-heading font-extrabold tracking-tight leading-tight mb-1 sm:mb-1.5" style="color: #ffffff !important;">
                                    Royal Haldi &<br>Wedding Urli Decor
                                </h2>
                                <p class="font-body text-[11px] sm:text-xs mb-2.5 sm:mb-3 font-normal leading-relaxed line-clamp-2" style="color: rgba(255, 255, 255, 0.85) !important;">
                                    Authentic brass urlis with marigold florals & mandap setups
                                </p>
                                <span class="inline-flex items-center gap-1 px-3 sm:px-4 py-1.5 rounded-lg font-heading font-bold text-[11px] sm:text-xs shadow-md transition-all"
                                      style="background-color: #ffffff !important; color: #0a291e !important;">
                                    <span style="color: #0a291e !important;">Book Setup</span>
                                    <i class="fa-solid fa-angle-right text-[9px] group-hover:translate-x-0.5 transition-transform" style="color: #0a291e !important;"></i>
                                </span>
                            </div>
                        </a>
                    </div>

                    <!-- Slide 3: High-Bass DJ & House Party (Warm Golden Amber) -->
                    <div class="swiper-slide !w-[86vw] sm:!w-[520px] md:!w-[600px] lg:!w-[680px] xl:!w-[720px]">
                        <a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" 
                           draggable="false"
                           class="block relative w-full h-[165px] sm:h-[185px] md:h-[205px] lg:h-[215px] rounded-2xl sm:rounded-3xl overflow-hidden shadow-md cursor-pointer select-none group"
                           style="background-color: #d4a373 !important;">
                            <img src="{{ asset('images/banners/banner-gold-dj.webp') }}" 
                                 alt="High-Bass DJ Rigs" 
                                 draggable="false"
                                 class="absolute inset-0 w-full h-full object-cover object-right pointer-events-none select-none" 
                                 style="user-select: none; -webkit-user-drag: none;"
                                 loading="eager" decoding="async">
                            
                            <!-- Left Gradient Wash -->
                            <div class="absolute inset-0 bg-gradient-to-r from-[#d4a373]/95 via-[#d4a373]/75 to-transparent pointer-events-none"></div>

                            <!-- Left Typography & CTA Button -->
                            <div class="relative z-10 p-4 sm:p-6 md:p-7 max-w-[65%] sm:max-w-sm text-left">
                                <h2 class="text-base sm:text-lg md:text-xl lg:text-2xl font-heading font-extrabold tracking-tight leading-tight mb-1 sm:mb-1.5" style="color: #1c1208 !important;">
                                    High-Bass DJ &<br>Concert Sound Rigs
                                </h2>
                                <p class="font-body text-[11px] sm:text-xs mb-2.5 sm:mb-3 font-medium leading-relaxed line-clamp-2" style="color: #3b220e !important;">
                                    Dual active column speakers, moving lasers & low smoke
                                </p>
                                <span class="inline-flex items-center gap-1 px-3 sm:px-4 py-1.5 rounded-lg font-heading font-bold text-[11px] sm:text-xs shadow-md transition-all"
                                      style="background-color: #171719 !important; color: #ffffff !important;">
                                    <span style="color: #ffffff !important;">Explore Setups</span>
                                    <i class="fa-solid fa-angle-right text-[9px] text-gray-300 group-hover:translate-x-0.5 transition-transform" style="color: #ffffff !important;"></i>
                                </span>
                            </div>
                        </a>
                    </div>

                    <!-- Slide 4: Artisanal Birthday Celebrations (Warm Champagne Beige) -->
                    <div class="swiper-slide !w-[86vw] sm:!w-[520px] md:!w-[600px] lg:!w-[680px] xl:!w-[720px]">
                        <a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" 
                           draggable="false"
                           class="block relative w-full h-[165px] sm:h-[185px] md:h-[205px] lg:h-[215px] rounded-2xl sm:rounded-3xl overflow-hidden shadow-md cursor-pointer select-none group"
                           style="background-color: #E8DDD1 !important;">
                            <img src="{{ asset('images/banners/artizen-banner-birthday.webp') }}" 
                                 alt="Birthday Celebrations Setup" 
                                 draggable="false"
                                 class="absolute inset-0 w-full h-full object-cover object-right pointer-events-none select-none" 
                                 style="user-select: none; -webkit-user-drag: none;"
                                 loading="eager" decoding="async">
                            
                            <!-- Left Gradient Wash -->
                            <div class="absolute inset-0 bg-gradient-to-r from-[#E8DDD1]/95 via-[#E8DDD1]/75 to-transparent pointer-events-none"></div>

                            <!-- Left Typography & CTA Button -->
                            <div class="relative z-10 p-4 sm:p-6 md:p-7 max-w-[65%] sm:max-w-sm text-left">
                                <h2 class="text-base sm:text-lg md:text-xl lg:text-2xl font-heading font-extrabold tracking-tight leading-tight mb-1 sm:mb-1.5" style="color: #2F1607 !important;">
                                    Artisanal Cakes &<br>Birthday Setups
                                </h2>
                                <p class="font-body text-[11px] sm:text-xs mb-2.5 sm:mb-3 font-medium leading-relaxed line-clamp-2" style="color: #5A3825 !important;">
                                    Bespoke themed cakes, champagne balloons & dessert tables
                                </p>
                                <span class="inline-flex items-center gap-1 px-3 sm:px-4 py-1.5 rounded-lg font-heading font-bold text-[11px] sm:text-xs shadow-md transition-all"
                                      style="background-color: #2F1607 !important; color: #ffffff !important;">
                                    <span style="color: #ffffff !important;">Explore Setups</span>
                                    <i class="fa-solid fa-angle-right text-[9px] text-gray-300 group-hover:translate-x-0.5 transition-transform" style="color: #ffffff !important;"></i>
                                </span>
                            </div>
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Swiper Initialization Script -->
    <script>
        (function() {
            function initHeroSwipers() {
                if (typeof Swiper === 'undefined') return false;

                // 1. Occasion Horizontal Rail
                var occEl = document.querySelector('.occasion-swiper');
                if (occEl) {
                    if (occEl.swiper) {
                        occEl.swiper.update();
                    } else {
                        new Swiper('.occasion-swiper', {
                            slidesPerView: 'auto',
                            spaceBetween: 14,
                            observer: true,
                            observeParents: true,
                            resizeObserver: true,
                            freeMode: {
                                enabled: true,
                                momentum: true,
                                momentumRatio: 0.8,
                                momentumVelocityRatio: 0.8,
                            },
                            grabCursor: true,
                            simulateTouch: true,
                            touchStartPreventDefault: true,
                            preventClicks: true,
                            preventClicksPropagation: true,
                            resistance: true,
                            resistanceRatio: 0.85,
                            mousewheel: {
                                forceToAxis: true,
                            },
                        });
                    }
                }

                // 2. Hero Panoramic Banner Swiper
                var bannerEl = document.querySelector('.hero-banner-swiper');
                if (bannerEl) {
                    if (bannerEl.swiper) {
                        bannerEl.swiper.update();
                    } else {
                        new Swiper('.hero-banner-swiper', {
                            slidesPerView: 'auto',
                            centeredSlides: true,
                            loop: true,
                            loopedSlides: 4,
                            spaceBetween: 16,
                            speed: 600,
                            observer: true,
                            observeParents: true,
                            resizeObserver: true,
                            autoplay: {
                                delay: 4500,
                                disableOnInteraction: false,
                                pauseOnMouseEnter: true,
                            },
                            grabCursor: true,
                            breakpoints: {
                                640: {
                                    spaceBetween: 20,
                                },
                                1024: {
                                    spaceBetween: 24,
                                }
                            }
                        });
                    }
                }
                return true;
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initHeroSwipers);
            } else {
                initHeroSwipers();
            }
            window.addEventListener('load', initHeroSwipers);
        })();
    </script>

    <!-- ==========================================
         TIER 4: VALUE & TRUST GUARANTEE BAR
         (4 Premium Trust Pillars: Zero Advance, On-Time, All Indore Areas, Dedicated Planner)
         ========================================== -->
    <div class="w-full bg-[#FFFFFF] dark:bg-[#121215] border-t border-[#E6E2D8] dark:border-[#222226] py-4 sm:py-5 transition-colors select-none">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
                
                <!-- Pillar 1: Zero Upfront Advance -->
                <div class="trust-pillar-item flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-400/10 border border-amber-200/80 dark:border-amber-400/20 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 shadow-2xs">
                        <i class="fa-solid fa-shield-halved text-base"></i>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-[13px] font-heading font-extrabold text-gray-900 dark:text-white leading-tight">Zero Upfront Advance</h4>
                        <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-normal">Pay offline after setup is ready</p>
                    </div>
                </div>

                <!-- Pillar 2: 100% On-Time Guarantee -->
                <div class="trust-pillar-item flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-400/10 border border-emerald-200/80 dark:border-emerald-400/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-2xs">
                        <i class="fa-solid fa-clock-rotate-left text-base"></i>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-[13px] font-heading font-extrabold text-gray-900 dark:text-white leading-tight">100% On-Time Setup</h4>
                        <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-normal">Ready 1 hr before guests arrive</p>
                    </div>
                </div>

                <!-- Pillar 3: All Indore Areas Covered -->
                <div class="trust-pillar-item flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-400/10 border border-rose-200/80 dark:border-rose-400/20 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 shadow-2xs">
                        <i class="fa-solid fa-truck-fast text-base"></i>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-[13px] font-heading font-extrabold text-gray-900 dark:text-white leading-tight">All Indore Covered</h4>
                        <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-normal">Vijay Nagar, Palasia, Bypass & more</p>
                    </div>
                </div>

                <!-- Pillar 4: Dedicated Coordinator -->
                <div class="trust-pillar-item flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-400/10 border border-indigo-200/80 dark:border-indigo-400/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 shadow-2xs">
                        <i class="fa-solid fa-headset text-base"></i>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-[13px] font-heading font-extrabold text-gray-900 dark:text-white leading-tight">Dedicated Coordinator</h4>
                        <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-normal">Direct planner on call & WhatsApp</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

</section>
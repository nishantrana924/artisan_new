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
        min-height: 175px;
        contain: layout;
    }
    .hero-banner-swiper .swiper-wrapper {
        display: flex;
    }
    .hero-banner-swiper .swiper-slide {
        flex-shrink: 0;
        box-sizing: border-box;
    }

    /* Pre-initialization fallback: exact widths matching Swiper breakpoints to eliminate FOUC layout shift */
    .hero-banner-swiper:not(.swiper-initialized) .swiper-wrapper {
        display: flex !important;
        flex-wrap: nowrap !important;
    }
    .hero-banner-swiper:not(.swiper-initialized) .swiper-slide {
        width: 85% !important;
        margin-right: 14px !important;
        flex-shrink: 0 !important;
    }
    .hero-banner-swiper:not(.swiper-initialized) .swiper-slide:first-child {
        margin-left: calc((100% - 85%) / 2) !important;
    }

    @media (min-width: 640px) {
        .hero-banner-swiper {
            min-height: 195px;
        }
        .hero-banner-swiper:not(.swiper-initialized) .swiper-slide {
            width: 78% !important;
            margin-right: 18px !important;
        }
        .hero-banner-swiper:not(.swiper-initialized) .swiper-slide:first-child {
            margin-left: calc((100% - 78%) / 2) !important;
        }
    }
    @media (min-width: 768px) {
        .hero-banner-swiper {
            min-height: 215px;
        }
        .hero-banner-swiper:not(.swiper-initialized) .swiper-slide {
            width: 74% !important;
            margin-right: 20px !important;
        }
        .hero-banner-swiper:not(.swiper-initialized) .swiper-slide:first-child {
            margin-left: calc((100% - 74%) / 2) !important;
        }
    }
    @media (min-width: 1024px) {
        .hero-banner-swiper {
            min-height: 230px;
        }
        .hero-banner-swiper:not(.swiper-initialized) .swiper-slide {
            width: 65% !important;
            margin-right: 22px !important;
        }
        .hero-banner-swiper:not(.swiper-initialized) .swiper-slide:first-child {
            margin-left: calc((100% - 65%) / 2) !important;
        }
    }
    @media (min-width: 1280px) {
        .hero-banner-swiper:not(.swiper-initialized) .swiper-slide {
            width: 56% !important;
            margin-right: 24px !important;
        }
        .hero-banner-swiper:not(.swiper-initialized) .swiper-slide:first-child {
            margin-left: calc((100% - 56%) / 2) !important;
        }
    }

    /* Hero Banner High Contrast & Color Protection across all themes */
    .hero-banner-swiper h2,
    .hero-banner-swiper .hero-banner-title,
    [data-theme="light"] .hero-banner-swiper h2,
    [data-theme="light"] .hero-banner-swiper h2.text-white,
    [data-theme="light"] .hero-banner-swiper .hero-banner-title,
    [data-theme="dark"] .hero-banner-swiper h2,
    [data-theme="dark"] .hero-banner-swiper h2.text-white {
        color: #ffffff !important;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.9), 0 2px 8px rgba(0, 0, 0, 0.75) !important;
    }
    .hero-banner-swiper p,
    .hero-banner-swiper .hero-banner-desc,
    [data-theme="light"] .hero-banner-swiper p,
    [data-theme="light"] .hero-banner-swiper p.text-white,
    [data-theme="dark"] .hero-banner-swiper p {
        color: rgba(255, 255, 255, 0.95) !important;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.85) !important;
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
                        @php
                            $cleanSlider = ltrim(parse_url($sliderImg, PHP_URL_PATH) ?? '', '/');
                            $optSlider   = preg_replace('/\.webp$/i', '.opt.webp', $cleanSlider);
                            $hasOptSlider = ($optSlider !== $cleanSlider && file_exists(public_path($optSlider)));
                        @endphp
                        @if($hasOptSlider)
                        <picture>
                            <source srcset="{{ asset($optSlider) }}" type="image/webp">
                            <img src="{{ $sliderImg }}"
                                 alt="{{ $altText }}"
                                 draggable="false"
                                 class="w-full h-full object-cover object-right block select-none pointer-events-none"
                                 style="user-select: none; -webkit-user-drag: none;"
                                 width="800"
                                 height="537"
                                 loading="eager" decoding="async">
                        </picture>
                        @else
                        <img src="{{ $sliderImg }}"
                             alt="{{ $altText }}"
                             draggable="false"
                             class="w-full h-full object-cover object-right block select-none pointer-events-none"
                             style="user-select: none; -webkit-user-drag: none;"
                             width="800"
                             height="537"
                             loading="eager" decoding="async">
                        @endif
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
            @php
                $rawHeroSlides = $slides ?? ($cms['hero_slides'] ?? []);
                $activeSlides = array_values(array_filter($rawHeroSlides, function($s) {
                    return ($s['is_active'] ?? true);
                }));

                // Safe fallback slides if no active slides configured
                if (empty($activeSlides)) {
                    $activeSlides = [
                        [
                            'id' => null,
                            'image' => 'images/banners/banner-navy-floral.webp',
                            'badge' => null,
                            'title' => "Blooming Love,\nWrapped in Flowers",
                            'desc' => "Send freshly-sourced blooms to brighten their special day",
                            'btn1Text' => "Order Now",
                            'link1' => route('events.index', ['category' => 'cat-proposal-anniversary']),
                            'bg_color' => '#0c1b33',
                        ],
                        [
                            'id' => null,
                            'image' => 'images/banners/banner-emerald-wedding.webp',
                            'badge' => null,
                            'title' => "Royal Haldi &\nWedding Urli Decor",
                            'desc' => "Authentic brass urlis with marigold florals & mandap setups",
                            'btn1Text' => "Book Setup",
                            'link1' => route('events.index', ['category' => 'cat-weddings-sangeet']),
                            'bg_color' => '#0a291e',
                        ],
                        [
                            'id' => null,
                            'image' => 'images/banners/banner-gold-dj.webp',
                            'badge' => null,
                            'title' => "High-Bass DJ &\nConcert Sound Rigs",
                            'desc' => "Dual active column speakers, moving lasers & low smoke",
                            'btn1Text' => "Explore Setups",
                            'link1' => route('events.index', ['category' => 'cat-house-party']),
                            'bg_color' => '#d4a373',
                        ],
                        [
                            'id' => null,
                            'image' => 'images/banners/artizen-banner-birthday.webp',
                            'badge' => null,
                            'title' => "Artisanal Cakes &\nBirthday Setups",
                            'desc' => "Bespoke themed cakes, champagne balloons & dessert tables",
                            'btn1Text' => "Book Cakes",
                            'link1' => route('events.index', ['category' => 'cat-birthdays']),
                            'bg_color' => '#E8DDD1',
                        ],
                    ];
                }

                $paletteColors = ['#0c1b33', '#0a291e', '#d4a373', '#2F1607', '#1E1E24'];
            @endphp
            <div class="swiper hero-banner-swiper overflow-visible cursor-grab active:cursor-grabbing">
                <div class="swiper-wrapper py-1">
                    @foreach($activeSlides as $sIdx => $slide)
                        @php
                            $rawImg = $slide['image'] ?? '/assets/images/hero/1.jpg';
                            $cleanImg = ltrim($rawImg, '/');
                            $optImg = str_replace(['.webp', '.jpg', '.png', '.jpeg'], '.opt.webp', $cleanImg);
                            $hasOpt = file_exists(public_path($optImg));
                            $slideBg = $slide['bg_color'] ?? ($paletteColors[$sIdx % count($paletteColors)] ?? '#0c1b33');
                            $targetLink = !empty($slide['link1']) ? $slide['link1'] : route('events.index');
                            $buttonLabel = !empty($slide['btn1Text']) ? $slide['btn1Text'] : 'Explore Now';
                        @endphp
                        <div class="swiper-slide">
                            <a href="{{ $targetLink }}" 
                               draggable="false"
                               class="block relative w-full h-[175px] sm:h-[195px] md:h-[215px] lg:h-[230px] rounded-2xl sm:rounded-3xl overflow-hidden shadow-md cursor-pointer select-none group"
                               style="background-color: {{ $slideBg }} !important;">
                                <picture>
                                    @if($hasOpt)
                                        <source srcset="{{ asset($optImg) }}" type="image/webp">
                                    @endif
                                    <img src="{{ asset($cleanImg) }}" 
                                         alt="{{ strip_tags($slide['title'] ?? 'Artizen Celebration') }}" 
                                         draggable="false"
                                         class="absolute inset-0 w-full h-full object-cover object-right pointer-events-none select-none" 
                                         style="user-select: none; -webkit-user-drag: none;"
                                         width="1376" height="768"
                                         {{ $loop->first ? 'loading="eager"' : 'loading="eager" decoding="async"' }}>
                                </picture>
                                
                                <!-- Left Gradient Wash for High Contrast -->
                                <div class="absolute inset-0 bg-gradient-to-r from-black/95 via-black/80 to-transparent pointer-events-none"
                                     style="background: linear-gradient(90deg, rgba(0,0,0,0.92) 0%, rgba(0,0,0,0.75) 42%, rgba(0,0,0,0.2) 65%, transparent 100%) !important;"></div>

                                <!-- Left Typography & CTA Button (Compact, Short Lines, Confined to Left Side Only) -->
                                <div class="relative z-10 hero-text-container p-4 sm:p-5 md:p-6 max-w-[55%] sm:max-w-[48%] md:max-w-[340px] text-left flex flex-col justify-center h-full">
                                    @if(!empty($slide['badge']))
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] sm:text-[11px] font-heading font-extrabold uppercase tracking-wider text-amber-300 bg-black/50 mb-1">
                                            {{ $slide['badge'] }}
                                        </span>
                                    @endif
                                    <h2 class="text-base sm:text-lg md:text-xl font-heading font-extrabold tracking-tight leading-tight mb-1 sm:mb-1.5 text-white hero-banner-title max-w-[260px] sm:max-w-[300px]"
                                        style="color: #ffffff !important; text-shadow: 0 1px 3px rgba(0,0,0,0.95), 0 2px 8px rgba(0,0,0,0.8) !important;">
                                        {!! nl2br(e($slide['title'] ?? '')) !!}
                                    </h2>
                                    @if(!empty($slide['desc']))
                                        <p class="font-body text-[11px] sm:text-xs mb-2 sm:mb-2.5 font-normal leading-relaxed text-white/95 hero-banner-desc max-w-[240px] sm:max-w-[280px]"
                                           style="color: rgba(255, 255, 255, 0.95) !important; text-shadow: 0 1px 3px rgba(0,0,0,0.9) !important; -webkit-line-clamp: unset !important; -webkit-box-orient: unset !important; display: block !important;">
                                            {{ strip_tags($slide['desc']) }}
                                        </p>
                                    @endif
                                    <div class="mt-0.5">
                                        <span class="inline-flex items-center gap-1 px-3 sm:px-4 py-1.5 rounded-lg font-heading font-bold text-[11px] sm:text-xs shadow-md transition-all bg-white text-gray-950 group-hover:bg-amber-400">
                                            <span>{{ $buttonLabel }}</span>
                                            <i class="fa-solid fa-angle-right text-[9px] group-hover:translate-x-0.5 transition-transform"></i>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
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
                            slidesPerView: 1.15,
                            centeredSlides: true,
                            loop: bannerEl.querySelectorAll('.swiper-slide').length > 1,
                            spaceBetween: 14,
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
                                    slidesPerView: 1.25,
                                    spaceBetween: 18,
                                },
                                768: {
                                    slidesPerView: 1.32,
                                    spaceBetween: 20,
                                },
                                1024: {
                                    slidesPerView: 1.5,
                                    spaceBetween: 22,
                                },
                                1280: {
                                    slidesPerView: 1.75,
                                    spaceBetween: 24,
                                }
                            }
                        });
                    }
                }
                return true;
            }

            // Mount immediately since Swiper bundle is loaded synchronously in <head>
            initHeroSwipers();

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initHeroSwipers);
            }
            window.addEventListener('load', initHeroSwipers);
        })();
    </script>

    <!-- ==========================================
         TIER 4: VALUE & TRUST GUARANTEE BAR
         (4 Unified Luxury Trust Pillars: Consistent Brand Gold Styling & 3D WebP Badges)
         ========================================== -->
    <div class="w-full bg-[#FFFFFF] dark:bg-[#121215] border-t border-[#E6E2D8] dark:border-[#222226] py-3.5 sm:py-4.5 transition-colors select-none">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-6 lg:gap-8">
                
                <!-- Pillar 1: Zero Upfront Advance -->
                <div class="trust-pillar-item flex items-center gap-3 group">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-[#FFFBF0] dark:bg-[#1A1813] border border-[#F5E6BE]/90 dark:border-[#382E19] p-1.5 flex items-center justify-center shrink-0 shadow-2xs">
                        <img src="{{ asset('images/trust/trust-shield-advance.webp') }}" 
                             alt="Zero Upfront Advance" 
                             width="44" height="44" 
                             class="w-full h-full object-contain filter drop-shadow-2xs" 
                             loading="eager">
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-[13px] font-heading font-extrabold text-gray-900 dark:text-white leading-tight">Zero Upfront Advance</h4>
                        <p class="text-[10.5px] sm:text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-normal leading-snug">Pay offline after setup is ready</p>
                    </div>
                </div>

                <!-- Pillar 2: 100% On-Time Guarantee -->
                <div class="trust-pillar-item flex items-center gap-3 group">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-[#FFFBF0] dark:bg-[#1A1813] border border-[#F5E6BE]/90 dark:border-[#382E19] p-1.5 flex items-center justify-center shrink-0 shadow-2xs">
                        <img src="{{ asset('images/trust/trust-clock-ontime.webp') }}" 
                             alt="100% On-Time Setup" 
                             width="44" height="44" 
                             class="w-full h-full object-contain filter drop-shadow-2xs" 
                             loading="eager">
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-[13px] font-heading font-extrabold text-gray-900 dark:text-white leading-tight">100% On-Time Setup</h4>
                        <p class="text-[10.5px] sm:text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-normal leading-snug">Ready 1 hr before guests arrive</p>
                    </div>
                </div>

                <!-- Pillar 3: All Indore Areas Covered -->
                <div class="trust-pillar-item flex items-center gap-3 group">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-[#FFFBF0] dark:bg-[#1A1813] border border-[#F5E6BE]/90 dark:border-[#382E19] p-1.5 flex items-center justify-center shrink-0 shadow-2xs">
                        <img src="{{ asset('images/trust/trust-pin-indore.webp') }}" 
                             alt="All Indore Covered" 
                             width="44" height="44" 
                             class="w-full h-full object-contain filter drop-shadow-2xs" 
                             loading="eager">
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-[13px] font-heading font-extrabold text-gray-900 dark:text-white leading-tight">All Indore Covered</h4>
                        <p class="text-[10.5px] sm:text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-normal leading-snug">Vijay Nagar, Palasia, Bypass & more</p>
                    </div>
                </div>

                <!-- Pillar 4: Dedicated Coordinator -->
                <div class="trust-pillar-item flex items-center gap-3 group">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-[#FFFBF0] dark:bg-[#1A1813] border border-[#F5E6BE]/90 dark:border-[#382E19] p-1.5 flex items-center justify-center shrink-0 shadow-2xs">
                        <img src="{{ asset('images/trust/trust-headset-coord.webp') }}" 
                             alt="Dedicated Coordinator" 
                             width="44" height="44" 
                             class="w-full h-full object-contain filter drop-shadow-2xs" 
                             loading="eager">
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-[13px] font-heading font-extrabold text-gray-900 dark:text-white leading-tight">Dedicated Coordinator</h4>
                        <p class="text-[10.5px] sm:text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-normal leading-snug">Direct planner on call & WhatsApp</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

</section>

<!-- ARTIZEN — Celebrations for Everyone (FOUC-Free Swiper.js Carousel) -->
<section id="celebrations-for-everyone" class="py-10 sm:py-14 bg-white relative scroll-mt-20 md:scroll-mt-24 select-none">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section Header -->
        <div class="mb-5 sm:mb-7">
            <h2 class="font-heading font-extrabold text-2xl sm:text-3xl md:text-[32px] text-gray-950 tracking-tight">
                Celebrations for Everyone
            </h2>
        </div>

        @php
            $celebrationRecipients = \App\Models\CelebrationRecipient::active()->ordered()->get();
            $recipients = $celebrationRecipients->isNotEmpty() ? $celebrationRecipients : [
                [
                    'name' => 'Him',
                    'slug' => 'him',
                    'image' => asset('images/for-everyone/him.webp'),
                    'category' => 'cat-birthdays',
                ],
                [
                    'name' => 'Her',
                    'slug' => 'her',
                    'image' => asset('images/for-everyone/her.webp'),
                    'category' => 'cat-proposal-anniversary',
                ],
                [
                    'name' => 'Kids',
                    'slug' => 'kids',
                    'image' => asset('images/for-everyone/kids.webp'),
                    'category' => 'cat-kids-cozy',
                ],
                [
                    'name' => 'Friend',
                    'slug' => 'friend',
                    'image' => asset('images/for-everyone/friend.webp'),
                    'category' => 'cat-house-party',
                ],
                [
                    'name' => 'Wife',
                    'slug' => 'wife',
                    'image' => asset('images/for-everyone/wife.webp'),
                    'category' => 'cat-proposal-anniversary',
                ],
                [
                    'name' => 'Husband',
                    'slug' => 'husband',
                    'image' => asset('images/for-everyone/husband.webp'),
                    'category' => 'cat-weddings-sangeet',
                ],
                [
                    'name' => 'Parents',
                    'slug' => 'parents',
                    'image' => asset('images/for-everyone/parents.webp'),
                    'category' => 'cat-weddings-sangeet',
                ],
            ];
        @endphp

        <!-- Swiper.js Slider Container -->
        <div class="relative group/everyone">

            <!-- Left Navigation Button (Starts Hidden with 0 Opacity by Default) -->
            <button type="button" 
                    class="everyone-swiper-prev swiper-button-disabled w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white shadow-md border border-gray-200 flex items-center justify-center text-gray-700 hover:text-black hover:shadow-lg transition-all duration-200 cursor-pointer"
                    aria-label="Previous">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>

            <!-- Swiper Main Track (Pre-locked with pure CSS to prevent FOUC / layout shift) -->
            <div class="swiper everyone-swiper overflow-hidden">
                <div class="swiper-wrapper">
                    @foreach($recipients as $recipient)
                        @php
                            $recipientName = is_array($recipient) ? $recipient['name'] : $recipient->name;
                            $recipientImage = is_array($recipient) 
                                ? $recipient['image'] 
                                : (str_starts_with($recipient->image ?? '', 'http') ? $recipient->image : asset(ltrim($recipient->image ?? '', '/')));
                            $recipientUrl = is_array($recipient) 
                                ? route('events.index', ['for' => $recipient['slug']]) 
                                : $recipient->target_url;
                        @endphp
                        <div class="swiper-slide">
                            <a href="{{ $recipientUrl }}" 
                               draggable="false"
                               class="group/item flex flex-col items-center w-full cursor-pointer text-decoration-none">
                                
                                <!-- Card Image Box (Matching Reference: Rounded rectangular with pastel backdrop) -->
                                <div class="w-full aspect-[16/10.5] rounded-2xl overflow-hidden bg-[#FFF5F5] border border-black/5 shadow-2xs relative transition-transform duration-200 group-hover/item:scale-[1.02]">
                                    <img src="{{ $recipientImage }}" 
                                         alt="{{ $recipientName }}" 
                                         class="w-full h-full object-cover select-none pointer-events-none" 
                                         draggable="false"
                                         loading="eager">
                                </div>

                                <!-- Label Below Card -->
                                <span class="block text-center font-heading font-bold text-[14px] sm:text-[15px] text-gray-900 mt-2.5 transition-colors group-hover/item:text-black">
                                    {{ $recipientName }}
                                </span>

                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right Navigation Button (Next) -->
            <button type="button" 
                    class="everyone-swiper-next w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white shadow-md border border-gray-200 flex items-center justify-center text-gray-700 hover:text-black hover:shadow-lg transition-all duration-200 cursor-pointer"
                    aria-label="Next">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>

        </div>

    </div>
</section>

<style>
    /* Section and track layout */
    .everyone-swiper {
        position: relative;
        overflow: hidden;
        width: 100%;
        contain: layout;
    }

    .everyone-swiper .swiper-wrapper {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        box-sizing: border-box;
        will-change: transform;
    }

    .everyone-swiper .swiper-slide {
        flex-shrink: 0 !important;
        box-sizing: border-box;
    }

    /* Disable native browser link dragging so Swiper mouse drag works flawlessly */
    .everyone-swiper a {
        -webkit-user-drag: none;
        user-drag: none;
        user-select: none;
    }

    /* Pre-Swiper sizing to match slidesPerView integers with ZERO layout shift before JS runs */
    .everyone-swiper:not(.swiper-initialized) .swiper-wrapper {
        gap: 12px;
    }
    .everyone-swiper:not(.swiper-initialized) .swiper-slide {
        width: calc((100% - 12px) / 2) !important;
    }

    @media (min-width: 480px) {
        .everyone-swiper:not(.swiper-initialized) .swiper-wrapper {
            gap: 14px;
        }
        .everyone-swiper:not(.swiper-initialized) .swiper-slide {
            width: calc((100% - 28px) / 3) !important;
        }
    }

    @media (min-width: 640px) {
        .everyone-swiper:not(.swiper-initialized) .swiper-wrapper {
            gap: 16px;
        }
        .everyone-swiper:not(.swiper-initialized) .swiper-slide {
            width: calc((100% - 32px) / 3) !important;
        }
    }

    @media (min-width: 768px) {
        .everyone-swiper:not(.swiper-initialized) .swiper-wrapper {
            gap: 18px;
        }
        .everyone-swiper:not(.swiper-initialized) .swiper-slide {
            width: calc((100% - 54px) / 4) !important;
        }
    }

    @media (min-width: 1024px) {
        .everyone-swiper:not(.swiper-initialized) .swiper-wrapper {
            gap: 20px;
        }
        .everyone-swiper:not(.swiper-initialized) .swiper-slide {
            width: calc((100% - 80px) / 5) !important;
        }
    }

    /* Floating navigation buttons positioning */
    .everyone-swiper-prev {
        position: absolute !important;
        left: -12px !important;
        right: auto !important;
        top: 38% !important;
        transform: translateY(-50%) !important;
        z-index: 20 !important;
    }

    .everyone-swiper-next {
        position: absolute !important;
        right: -12px !important;
        left: auto !important;
        top: 38% !important;
        transform: translateY(-50%) !important;
        z-index: 20 !important;
    }

    @media (min-width: 640px) {
        .everyone-swiper-prev {
            left: -18px !important;
        }
        .everyone-swiper-next {
            right: -18px !important;
        }
    }

    /* Seamless navigation button fade without initial pop */
    .everyone-swiper-prev.swiper-button-disabled,
    .everyone-swiper-next.swiper-button-disabled {
        opacity: 0 !important;
        pointer-events: none !important;
        visibility: hidden !important;
    }
</style>

<script>
    (function() {
        function initEveryoneSwiper() {
            const swiperEl = document.querySelector('.everyone-swiper');
            if (!swiperEl) return;

            if (swiperEl.swiper) {
                swiperEl.swiper.update();
                return;
            }

            if (typeof Swiper === 'undefined') {
                requestAnimationFrame(initEveryoneSwiper);
                return;
            }

            new Swiper('.everyone-swiper', {
                slidesPerView: 2,
                spaceBetween: 12,
                slidesPerGroup: 1,
                grabCursor: true,
                simulateTouch: true,
                touchRatio: 1,
                threshold: 5,
                preventClicks: true,
                preventClicksPropagation: true,
                watchOverflow: true,
                watchSlidesProgress: true,
                resistance: true,
                resistanceRatio: 0,
                observer: true,
                observeParents: true,
                resizeObserver: true,
                navigation: {
                    nextEl: '.everyone-swiper-next',
                    prevEl: '.everyone-swiper-prev',
                },
                breakpoints: {
                    480: {
                        slidesPerView: 3,
                        spaceBetween: 14,
                    },
                    640: {
                        slidesPerView: 3,
                        spaceBetween: 16,
                    },
                    768: {
                        slidesPerView: 4,
                        spaceBetween: 18,
                    },
                    1024: {
                        slidesPerView: 5,
                        spaceBetween: 20,
                    },
                },
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initEveryoneSwiper);
        } else {
            initEveryoneSwiper();
        }
        window.addEventListener('load', initEveryoneSwiper);
    })();
</script>
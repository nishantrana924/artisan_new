<section id="events" class="py-20 bg-white dark:bg-[#000000] relative scroll-mt-20 md:scroll-mt-24">
    <div class="w-full px-4 md:px-12">

        @php
            $catList = $categories ?? [];
            if (empty($catList)) {
                $catList = \App\Services\JsonStorageService::read('categories.json');
            }

            // Create lookup map of explicitly inactive category slugs from categories.json
            $inactiveCategoryMap = [];
            foreach ($catList as $c) {
                if (isset($c['active']) && $c['active'] === false) {
                    $slugStr = \Illuminate\Support\Str::slug($c['title'] ?? '');
                    $inactiveCategoryMap[$slugStr] = true;
                    $inactiveCategoryMap['cat-' . $slugStr] = true;
                    if ($slugStr === 'birthdays') {
                        $inactiveCategoryMap['adult-birthdays'] = true;
                        $inactiveCategoryMap['cat-adult-birthdays'] = true;
                    }
                }
            }

            $pkgList = $packages ?? [];
            if (empty($pkgList)) {
                $pkgList = \App\Services\JsonStorageService::read('packages.json');
            }
        @endphp

        <!-- Sticky Category Navigation Bar (App style) -->
        <div class="category-sticky-nav-wrapper mb-12">
            <div class="category-sticky-nav scrollbar-hide">
                @php
                    $categoryFaIcons = [
                        'birthdays' => 'fa-solid fa-cake-candles',
                        'kids-cozy' => 'fa-solid fa-baby',
                        'house-party' => 'fa-solid fa-champagne-glasses',
                        'proposal-anniversary' => 'fa-regular fa-heart',
                        'dj-acoustic' => 'fa-solid fa-music',
                        'weddings-sangeet' => 'fa-solid fa-ring',
                        'baby-corporate' => 'fa-solid fa-briefcase',
                        'all-celebrations' => 'fa-solid fa-gift',
                        'custom-setups' => 'fa-solid fa-wand-magic-sparkles',
                        'more' => 'fa-solid fa-ellipsis',
                    ];
                @endphp
                @foreach($catList as $index => $cat)
                    @if($cat['active'] ?? true)
                        @php
                            $rawSlug = \Illuminate\Support\Str::slug($cat['title'] ?? '');
                            $catSlug = 'cat-' . $rawSlug;
                            $faIcon = $categoryFaIcons[$rawSlug] ?? 'fa-solid fa-gift';
                        @endphp
                        <button onclick="scrollToCategoryRow('{{ $catSlug }}')" class="category-nav-pill {{ $index === 0 ? 'active' : '' }}" id="pill-{{ $catSlug }}">
                            <i class="{{ $faIcon }} text-sm" aria-hidden="true"></i>
                            {{ $cat['title'] ?? '' }}
                        </button>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Dynamic Category & Package Service Cards -->
        @foreach($pkgList as $catKey => $catData)
            @php
                $slugStr = \Illuminate\Support\Str::slug($catData['title'] ?? '');
                // Normalize slug map to match categories.json
                $slugNormMap = [
                    'adult-birthdays'    => 'birthdays',
                    'house-party-rigs'   => 'house-party',
                    'proposal-setups'    => 'proposal-anniversary',
                    'wedding-sangeet'    => 'weddings-sangeet',
                    'corporate-events'   => 'baby-corporate',
                ];
                $slugStr = $slugNormMap[$slugStr] ?? $slugStr;
                $catSlug = 'cat-' . $slugStr;
                $isCategoryActive = ($catData['active'] ?? true) && empty($inactiveCategoryMap[$slugStr]);
            @endphp
            @if($isCategoryActive)
                <div id="{{ $catSlug }}" data-cat-alt="cat-{{ \Illuminate\Support\Str::slug($catData['title'] ?? '') }}" class="category-row-wrapper mb-16 scroll-mt-32">
                    <div class="flex items-center justify-between mb-6 px-2">
                        <h3 class="font-heading font-extrabold text-lg md:text-2xl uppercase tracking-tight text-black dark:text-white">
                            {{ $catData['title'] ?? '' }}
                        </h3>
                        <a href="{{ route('events.index', ['category' => $catSlug]) }}" class="swipe-indicator group">
                            <span>Swipe to explore</span>
                            <i class="fa-solid fa-arrow-right text-[11px] transition-transform group-hover:translate-x-1" aria-hidden="true"></i>
                        </a>
                    </div>

                    <div class="category-row-scroll scrollbar-hide cols-4">
                        @foreach(($catData['tiers'] ?? []) as $tierIdx => $tier)
                            @php
                                $badgeClass = ($tierIdx == 1) ? 'gold' : '';
                                $badgeText = $tier['badge'] ?? (($tierIdx == 0) ? 'Essential Setup' : (($tierIdx == 1) ? 'Best Seller' : 'Luxury Tier'));
                                $tierImage = !empty($tier['image']) ? $tier['image'] : ($catData['image'] ?? '');

                                $gallery = $catData['gallery'] ?? [];
                                $allTiers = $catData['tiers'] ?? [];
                                $nextTier = $allTiers[($tierIdx + 1) % max(1, count($allTiers))] ?? [];
                                $secondaryImage = !empty($gallery[$tierIdx]) 
                                    ? $gallery[$tierIdx] 
                                    : (!empty($gallery[0]) && $gallery[0] !== $tierImage 
                                        ? $gallery[0] 
                                        : (!empty($nextTier['image']) && $nextTier['image'] !== $tierImage 
                                            ? $nextTier['image'] 
                                            : ($catData['image'] ?? $tierImage)));

                                $cardImages = array_values(array_filter(array_unique([
                                    $tierImage,
                                    $secondaryImage,
                                    $gallery[0] ?? '',
                                    $gallery[1] ?? '',
                                    $gallery[2] ?? ''
                                ])));
                            @endphp
                            <div class="experience-card card-tier-{{ $tierIdx == 0 ? 'basic' : ($tierIdx == 1 ? 'standard' : 'premium') }} filtering-in cursor-pointer" onclick="openEventLightbox({{ $catKey }}, {{ $tierIdx }})">
                                <div class="flex flex-col flex-grow">
                                    <div class="experience-card-img-container" data-card-images='@json($cardImages)'>
                                        <img src="{{ $tierImage }}" alt="{{ $tier['name'] ?? '' }}" class="experience-card-img primary-img" loading="lazy">
                                        @if(!empty($secondaryImage) && $secondaryImage !== $tierImage)
                                            <img src="{{ $secondaryImage }}" alt="{{ $tier['name'] ?? '' }}" class="experience-card-img secondary-img" loading="lazy">
                                        @endif
                                        <div class="experience-card-badge {{ $badgeClass }}">{{ $badgeText }}</div>
                                        @if(count($cardImages) > 1)
                                            <div class="card-img-indicators">
                                                @foreach($cardImages as $i => $img)
                                                    <span class="card-img-dot {{ $i === 0 ? 'active' : '' }}"></span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="experience-card-header">
                                        <h3 class="experience-card-title">{{ $tier['name'] ?? '' }}</h3>
                                        <div class="experience-card-rating flex items-center gap-1">
                                            <i class="fa-solid fa-star text-gold text-xs" aria-hidden="true"></i>
                                            <span>4.9</span>
                                        </div>
                                    </div>
                                    <p class="experience-card-desc">{{ $tier['desc'] ?? '' }}</p>
                                </div>

                                <div class="experience-card-footer mt-auto">
                                    <div class="experience-card-price-stack">
                                        <div class="experience-card-price">From <span>₹{{ number_format($tier['price'] ?? 0) }}</span></div>
                                    </div>
                                    <span class="experience-card-btn">
                                        Book Now
                                        <i class="fa-solid fa-chevron-right text-[10px] experience-card-arrow" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        <!-- Custom Event CTA -->
        <div class="custom-cta-wrapper experiences-header-animate" style="animation-delay: 0.3s;">
            <h3 class="custom-cta-title">Can't Find Your Perfect Package?</h3>
            <p class="custom-cta-desc">Create a Custom Event tailored to your specific budget and requirements.</p>
            <a href="#newsletter" class="custom-cta-btn">Plan My Event</a>
        </div>

    </div>
</section>               const target = pill.getAttribute('data-target');
                        if (target === sectionId || target === altAttr || ('cat-' + target) === sectionId) {
                            pill.classList.add('active');
                            // Smoothly scroll nav bar horizontally to keep active pill visible
                            if (navContainer) {
                                const pillLeft = pill.offsetLeft;
                                const pillWidth = pill.offsetWidth;
                                const containerScroll = navContainer.scrollLeft;
                                const containerWidth = navContainer.offsetWidth;

                                if (pillLeft < containerScroll || (pillLeft + pillWidth) > (containerScroll + containerWidth)) {
                                    navContainer.scrollTo({
                                        left: pillLeft - (containerWidth / 2) + (pillWidth / 2),
                                        behavior: 'smooth'
                                    });
                                }
                            }
                        } else {
                            pill.classList.remove('active');
                        }
                    });
                }
            });
        }, observerOptions);

        sections.forEach(sec => sectionObserver.observe(sec));
    });
</script>
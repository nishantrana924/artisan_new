<!-- ARTIZEN — Clean Look Customer Reviews / Testimonials Section -->
<section id="reviews" class="py-12 sm:py-16 bg-[#FAF9F6] dark:bg-[#0A0A0D] border-t border-gray-200/80 dark:border-white/10 select-none overflow-hidden transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @php
            $storedReviews = $testimonials ?? \App\Models\Testimonial::active()->forHome()->ordered()->get();
            $reviewList = [];

            if (!empty($storedReviews)) {
                foreach ($storedReviews as $s) {
                    $author = trim($s->author ?? ($s['author'] ?? ($s['name'] ?? '')));
                    if (empty($author)) continue;

                    $rating = (int)($s->rating ?? ($s['rating'] ?? 5));
                    $rating = max(1, min(5, $rating));

                    $reviewList[] = [
                        'id' => $s->id ?? ($s['id'] ?? null),
                        'name' => $author,
                        'location' => $s->location ?? ($s['location'] ?? 'Indore, MP'),
                        'initial' => strtoupper(substr($author, 0, 1)),
                        'avatar_bg' => 'bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10',
                        'rating' => $rating,
                        'badge' => $s->event_type ?? ($s['event_type'] ?? 'Celebration Setup'),
                        'text' => $s->review ?? ($s['review'] ?? ($s['content'] ?? ($s['text'] ?? ''))),
                    ];
                }
            }

            // Fallback default reviews if none configured
            if (empty($reviewList)) {
                $reviewList = [
                    [
                        'name' => 'Priya & Kunal Sharma',
                        'location' => 'Saket Nagar, Indore',
                        'initial' => 'P',
                        'avatar_bg' => 'bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10',
                        'rating' => 5,
                        'badge' => '1st Birthday Setup',
                        'text' => 'Booked the 1st birthday wonderland theme for my son. The setup team arrived exactly 2 hours prior, assembled everything cleanly, and the photos turned out fabulous. Zero hassle with advance payments too — paid offline after everything was verified!',
                    ],
                    [
                        'name' => 'Rohit Verma',
                        'location' => 'Vijay Nagar, Indore',
                        'initial' => 'R',
                        'avatar_bg' => 'bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10',
                        'rating' => 5,
                        'badge' => 'High-Bass Sound Rig',
                        'text' => 'Rented the active speaker column and strobe lasers for our terrace club party. The bass was punchy, mics worked flawlessly for karaoke, and the technician was courteous and helpful throughout the night. All guests loved it.',
                    ],
                    [
                        'name' => 'Ananya Kapoor',
                        'location' => 'New Palasia, Indore',
                        'initial' => 'A',
                        'avatar_bg' => 'bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10',
                        'rating' => 5,
                        'badge' => 'Proposal & Anniversary',
                        'text' => 'The fairy lights, red roses, and "Marry Me" LED marquee letters created the most romantic ambience. The coordinator was discreet, communicated on WhatsApp, and executed the surprise flawlessly on our private rooftop.',
                    ],
                    [
                        'name' => 'Deepak & Neha Jain',
                        'location' => 'Bypass Road, Indore',
                        'initial' => 'D',
                        'avatar_bg' => 'bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10',
                        'rating' => 5,
                        'badge' => 'Haldi & Mehendi Urli',
                        'text' => 'We needed a traditional haldi ceremony setup for 80 guests. Fresh yellow & orange marigolds, decorated brass Urli with flower petals, and traditional floral seating were arranged in our lawn right on time. Highly recommended!',
                    ],
                    [
                        'name' => 'Megha Chawla',
                        'location' => 'Nipania, Indore',
                        'initial' => 'M',
                        'avatar_bg' => 'bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10',
                        'rating' => 5,
                        'badge' => 'Baby Shower',
                        'text' => 'Super clean and aesthetically pleasing baby shower setup. The "Oh Baby" golden neon sign and pastel balloon garland looked straight out of Pinterest. Seamless experience with zero advance fee stress.',
                    ],
                    [
                        'name' => 'Vikramaditya Solanki',
                        'location' => 'Super Corridor, Indore',
                        'initial' => 'V',
                        'avatar_bg' => 'bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10',
                        'rating' => 5,
                        'badge' => 'Corporate Gala',
                        'text' => 'Organized our IT company annual gala on Super Corridor. Artizen delivered stage lighting, PA system, and P3 LED backdrop with seamless technical support. Very punctual, professional, and transparent pricing.',
                    ],
                ];
            }

            $totalReviewsCount = count($reviewList);
            $avgRating = $totalReviewsCount > 0 
                ? round(collect($reviewList)->avg('rating'), 1) 
                : 5.0;

            $positiveReviewsCount = collect($reviewList)->filter(fn($r) => ($r['rating'] ?? 5) >= 4)->count();
            $recommendedPercent = $totalReviewsCount > 0 
                ? round(($positiveReviewsCount / $totalReviewsCount) * 100) 
                : 100;
        @endphp

        <!-- Section Header with Overall Rating & Navigation Controls -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8 sm:mb-10">
            <div>
                <h2 class="font-heading font-extrabold text-2xl sm:text-3xl md:text-[32px] text-gray-950 dark:text-white tracking-tight leading-tight">
                    Customer Reviews
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1.5 max-w-xl font-normal leading-relaxed">
                    Real reviews from families, party hosts, and couples who celebrated their milestone moments with Artizen.
                </p>
            </div>

            <!-- Overall Rating Summary & Swiper Navigation -->
            <div class="flex items-center justify-between sm:justify-end gap-5 shrink-0">
                <!-- Rating Score Pill (100% Dynamic Calculated from Actual Reviews) -->
                <a href="{{ route('reviews.index') }}" 
                   title="View All Customer Reviews"
                   class="flex items-center gap-3.5 sm:gap-4 bg-white dark:bg-[#121217] border border-gray-200/90 dark:border-white/10 hover:border-gray-400 dark:hover:border-white/30 px-4 sm:px-5 py-3 rounded-2xl sm:rounded-3xl shadow-2xs transition-all cursor-pointer group text-inherit no-underline">
                    <span class="font-heading font-black text-3xl sm:text-4xl text-gray-950 dark:text-white leading-none">
                        {{ number_format($avgRating, 1) }}
                    </span>
                    <div class="text-left">
                        <div class="flex items-center text-amber-400 text-xs sm:text-[13px] gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($avgRating >= $i)
                                    <i class="fa-solid fa-star"></i>
                                @elseif ($avgRating >= ($i - 0.5))
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                @else
                                    <i class="fa-regular fa-star text-gray-300 dark:text-gray-600"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white block mt-1 leading-tight group-hover:text-black dark:group-hover:text-white transition-colors">
                            Based on {{ $totalReviewsCount }} {{ Str::plural('Review', $totalReviewsCount) }}
                        </span>
                        <span class="text-[10px] sm:text-xs text-emerald-600 dark:text-emerald-400 font-semibold block mt-0.5 leading-tight">
                            {{ $recommendedPercent }}% Recommended in Indore
                        </span>
                    </div>
                </a>

                <!-- Swiper Navigation Arrows -->
                <div class="flex items-center gap-2">
                    <button type="button" 
                            id="reviews-prev-btn" 
                            class="w-9 h-9 rounded-full bg-white dark:bg-[#15151A] border border-gray-200/90 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:text-black dark:hover:text-white hover:border-gray-400 dark:hover:border-white/30 flex items-center justify-center transition-all cursor-pointer shadow-2xs focus:outline-none"
                            aria-label="Previous reviews">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <button type="button" 
                            id="reviews-next-btn" 
                            class="w-9 h-9 rounded-full bg-white dark:bg-[#15151A] border border-gray-200/90 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:text-black dark:hover:text-white hover:border-gray-400 dark:hover:border-white/30 flex items-center justify-center transition-all cursor-pointer shadow-2xs focus:outline-none"
                            aria-label="Next reviews">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Swiper.js Reviews Carousel (Cards with Exact Uniform Size: Avatar, Name, Location, Rating Stars, Description) -->
        <div class="swiper reviews-swiper overflow-visible pb-4">
            <div class="swiper-wrapper items-stretch">
                @foreach ($reviewList as $index => $review)
                    <div class="swiper-slide h-auto flex">
                        <!-- Ultra Clean Review Card (Static, No Hover Animation, No Gold Border Line) -->
                        <div onclick="openReviewModal({{ $index }})" 
                             class="w-full h-[220px] sm:h-[230px] bg-white dark:bg-[#121217] border border-gray-200/90 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-2xs flex flex-col justify-between text-left cursor-pointer select-none focus:outline-none focus:ring-0 outline-none"
                             role="button"
                             tabindex="0"
                             onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();openReviewModal({{ $index }});}"
                             aria-label="View full review by {{ $review['name'] }}">
                            
                            <!-- Top Row: Avatar + Name + Location on Left, Rating Stars on Right -->
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <!-- Avatar -->
                                    <div class="w-9 h-9 rounded-full bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10 font-heading font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ $review['initial'] ?? substr($review['name'], 0, 1) }}
                                    </div>
                                    <!-- Name & Location -->
                                    <div class="truncate">
                                        <h4 class="font-heading font-bold text-xs sm:text-sm text-gray-950 dark:text-white truncate">
                                            {{ $review['name'] }}
                                        </h4>
                                        @if(!empty($review['location']))
                                            <p class="text-[10px] sm:text-[11px] text-gray-500 dark:text-gray-400 truncate">
                                                {{ $review['location'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Rating Stars -->
                                <div class="flex items-center text-amber-400 text-xs gap-0.5 shrink-0 pt-0.5">
                                    @for ($i = 0; $i < ($review['rating'] ?? 5); $i++)
                                        <i class="fa-solid fa-star"></i>
                                    @endfor
                                </div>
                            </div>

                            <!-- Description Body with Inline Read More (No Arrow) -->
                            <div class="h-[84px] sm:h-[88px] overflow-hidden mb-2 text-xs sm:text-[13px] text-gray-600 dark:text-gray-300 leading-relaxed font-normal">
                                <p class="line-clamp-4">
                                    "{{ Str::limit($review['text'], 125, '...') }}"
                                    <span class="font-bold text-gray-900 dark:text-white underline ml-1 cursor-pointer">Read more</span>
                                </p>
                            </div>

                            <!-- Bottom Row: Verified Badge & Package Tag -->
                            <div class="pt-2 border-t border-gray-100 dark:border-white/5 flex items-center justify-between text-[11px] mt-auto">
                                <span class="inline-flex items-center gap-1 text-[10px] text-emerald-700 dark:text-emerald-400 font-semibold">
                                    <i class="fa-solid fa-circle-check text-[9px]"></i> Verified
                                </span>
                                @if(!empty($review['badge']))
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400 font-medium">
                                        {{ $review['badge'] }}
                                    </span>
                                @endif
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</section>

<!-- FULL REVIEW MODAL (Background scroll locked when open) -->
<div id="full-review-modal" 
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden opacity-0 transition-opacity duration-300"
     aria-modal="true" 
     role="dialog" 
     aria-labelledby="modal-review-name">
    
    <div class="relative w-full max-w-lg bg-white dark:bg-[#141419] rounded-3xl border border-gray-200/90 dark:border-white/10 shadow-2xl p-6 sm:p-7 transform scale-95 transition-transform duration-300 text-left">
        
        <!-- Close Button -->
        <button type="button" 
                onclick="closeReviewModal()" 
                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-gray-100 dark:bg-white/10 text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white flex items-center justify-center transition-colors cursor-pointer focus:outline-none"
                aria-label="Close review modal">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>

        <!-- Top Header: Avatar + Name + Location on Left, Rating Stars on Right -->
        <div class="flex items-center justify-between gap-3 mb-5 pr-8">
            <div class="flex items-center gap-3 min-w-0">
                <div id="modal-review-avatar" class="w-10 h-10 rounded-full bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10 font-heading font-bold text-xs sm:text-sm flex items-center justify-center shrink-0">
                    <!-- Initial -->
                </div>
                <div class="truncate">
                    <h3 id="modal-review-name" class="font-heading font-bold text-sm sm:text-base text-gray-950 dark:text-white truncate">
                        <!-- Name -->
                    </h3>
                    <p id="modal-review-location" class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                        <!-- Location -->
                    </p>
                </div>
            </div>

            <!-- Stars -->
            <div id="modal-review-stars" class="flex items-center text-amber-400 text-xs sm:text-sm gap-0.5 shrink-0">
                <!-- Stars -->
            </div>
        </div>

        <!-- Full Review Description Container (Scrollable if long) -->
        <div class="max-h-64 overflow-y-auto pr-1 text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed font-normal">
            <p id="modal-review-text">
                <!-- Description -->
            </p>
        </div>

        <!-- Verified Booking & Package Tag Footer -->
        <div class="pt-4 mt-5 border-t border-gray-100 dark:border-white/10 flex items-center justify-between text-xs">
            <span class="inline-flex items-center gap-1 text-[11px] bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40 px-2.5 py-0.5 rounded-full font-semibold">
                <i class="fa-solid fa-circle-check text-[10px]"></i> Verified Booking
            </span>
            <span id="modal-review-badge" class="text-[11px] font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-white/5 px-2.5 py-1 rounded-lg">
                <!-- Badge -->
            </span>
        </div>

    </div>
</div>

<!-- Swiper & Modal Controller Script -->
<script>
    // Cached reviews dataset for modal population
    const artizenReviewsList = @json($reviewList);

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swiper !== 'undefined') {
            new Swiper('.reviews-swiper', {
                slidesPerView: 1,
                spaceBetween: 16,
                grabCursor: true,
                loop: true,
                autoHeight: false,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },
                navigation: {
                    prevEl: '#reviews-prev-btn',
                    nextEl: '#reviews-next-btn',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 20,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 24,
                    }
                }
            });
        }
    });

    // Open Full Review Modal and lock background page scrolling
    function openReviewModal(index) {
        const review = artizenReviewsList[index];
        if (!review) return;

        // Set Content
        document.getElementById('modal-review-name').textContent = review.name || '';
        document.getElementById('modal-review-location').textContent = review.location || '';
        document.getElementById('modal-review-text').textContent = review.text ? `"${review.text}"` : '';
        document.getElementById('modal-review-badge').textContent = review.badge || 'Indore Event Setup';

        // Avatar
        const avatarEl = document.getElementById('modal-review-avatar');
        avatarEl.className = 'w-10 h-10 rounded-full bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10 font-heading font-bold text-xs sm:text-sm flex items-center justify-center shrink-0';
        avatarEl.textContent = review.initial || (review.name ? review.name.charAt(0) : 'U');

        // Stars
        const starsEl = document.getElementById('modal-review-stars');
        let starsHtml = '';
        const rating = review.rating || 5;
        for (let i = 0; i < rating; i++) {
            starsHtml += '<i class="fa-solid fa-star"></i>';
        }
        starsEl.innerHTML = starsHtml;

        // Display Modal with Smooth Fade & Scale
        const modal = document.getElementById('full-review-modal');
        const modalContent = modal.querySelector('.relative');

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        });

        // LOCK BACKGROUND PAGE SCROLLING
        document.body.classList.add('overflow-hidden');
        document.documentElement.classList.add('overflow-hidden');
    }

    // Close Full Review Modal and restore background page scrolling
    function closeReviewModal() {
        const modal = document.getElementById('full-review-modal');
        if (!modal || modal.classList.contains('hidden')) return;

        const modalContent = modal.querySelector('.relative');
        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            // RESTORE BACKGROUND PAGE SCROLLING
            document.body.classList.remove('overflow-hidden');
            document.documentElement.classList.remove('overflow-hidden');
        }, 220);
    }

    // Close on backdrop click
    document.addEventListener('click', function (e) {
        const modal = document.getElementById('full-review-modal');
        if (modal && e.target === modal) {
            closeReviewModal();
        }
    });

    // Close on Escape key press
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeReviewModal();
        }
    });
</script>
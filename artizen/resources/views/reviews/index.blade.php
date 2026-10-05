@extends('layouts.app')

@section('title', 'Customer Reviews & Stories | ARTIZEN Indore Event Platform')
@section('meta_description', 'Read verified customer reviews and celebration experiences from 1,200+ event setups in Indore. Real feedback on birthday decors, sound rigs, and wedding setups.')

@section('content')
<div class="min-h-screen pt-8 pb-16 px-4 md:px-12 bg-white dark:bg-[#0c0c0c] text-black dark:text-white relative select-none">
    <div class="max-w-7xl mx-auto">

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6 font-medium">
            <a href="{{ route('home') }}" class="hover:text-black dark:hover:text-white transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            @if(!empty($selectedPackageSlug))
                <a href="{{ route('reviews.index') }}" class="hover:text-black dark:hover:text-white transition-colors">Customer Reviews</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-black dark:text-white font-bold">{{ $selectedPackageTitle }}</span>
            @else
                <span class="text-black dark:text-white font-bold">Customer Reviews</span>
            @endif
        </nav>

        <!-- Reviews Hero Banner Header with Rating Metric -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center border-b border-gray-200 dark:border-white/10 pb-8 mb-8 gap-6 text-left">
            <div>
                <span class="text-[10px] font-heading font-extrabold uppercase tracking-widest text-amber-700 dark:text-amber-400 mb-2 inline-flex items-center gap-1.5 bg-amber-500/10 px-3 py-1 rounded-full border border-amber-500/30">
                    <i class="fa-solid fa-star text-xs"></i> {{ !empty($selectedPackageSlug) ? '100% VERIFIED PACKAGE REVIEWS' : '100% VERIFIED INDORE BOOKINGS' }}
                </span>
                <h1 class="font-heading font-extrabold text-3xl md:text-5xl uppercase tracking-tight text-gray-900 dark:text-white leading-tight">
                    @if(!empty($selectedPackageSlug))
                        Reviews: <span class="text-[#D97706] dark:text-[#FFD600]">{{ $selectedPackageTitle }}</span>
                    @else
                        Customer Reviews
                    @endif
                </h1>
                <p class="text-sm md:text-base text-gray-600 dark:text-gray-300 max-w-2xl mt-2 leading-relaxed font-normal">
                    @if(!empty($selectedPackageSlug))
                        Verified customer experiences, decoration ratings, and setup feedback specifically for <span class="font-bold text-gray-900 dark:text-white">{{ $selectedPackageTitle }}</span> in Indore.
                    @else
                        Discover how Artizen turns milestone celebrations into unforgettable memories. Real stories, punctual on-site execution, and transparent offline payments across Indore.
                    @endif
                </p>
            </div>

            <!-- Score Summary Card (Dynamic from Actual Reviews) -->
            <div class="flex items-center gap-4 bg-[#FAF9F6] dark:bg-[#121217] border border-gray-200/90 dark:border-white/10 rounded-2xl py-3.5 px-6 shadow-sm shrink-0">
                <span class="font-heading font-black text-4xl text-gray-950 dark:text-white leading-none">
                    {{ number_format($avgRating, 1) }}
                </span>
                <div class="text-left">
                    <div class="flex items-center text-amber-400 text-xs gap-0.5">
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
                    <span class="text-xs font-bold text-gray-900 dark:text-white block mt-1">
                        Based on {{ $totalReviewsCount }} {{ Str::plural('Review', $totalReviewsCount) }}
                    </span>
                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">
                        {{ $recommendedPercent }}% Recommended in Indore
                    </span>
                </div>
            </div>
        </div>

        @if(!empty($selectedPackageSlug))
            <!-- Active Package Filter Notice Bar -->
            <div class="mb-8 p-4 sm:p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#FFD600] text-[#171719] flex items-center justify-center font-bold text-base shrink-0 shadow-2xs">
                        <i class="fa-solid fa-filter"></i>
                    </div>
                    <div>
                        <p class="text-xs font-heading font-extrabold uppercase tracking-wide text-gray-950 dark:text-white">
                            Filtered by Package: <span class="text-amber-600 dark:text-[#FFD600]">{{ $selectedPackageTitle }}</span> ({{ $totalReviewsCount }} {{ Str::plural('Review', $totalReviewsCount) }})
                        </p>
                        <p class="text-[11px] text-gray-600 dark:text-gray-400 mt-0.5">
                            Showing verified customer experiences specifically for this celebration package.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('reviews.index') }}" class="px-3.5 py-2 bg-white dark:bg-[#1A1A22] border border-gray-300 dark:border-white/15 hover:border-gray-500 text-xs font-heading font-bold rounded-xl transition-all text-gray-900 dark:text-white shadow-2xs inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-xmark text-[11px]"></i> Clear Filter (View All)
                    </a>
                </div>
            </div>

            <!-- Filter Pills when filtered by Package -->
            <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-8 scrollbar-hide">
                <span class="px-4 py-2 rounded-xl text-xs font-bold shrink-0 bg-[#FFD600] text-[#171719] shadow-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-tag text-[10px]"></i>
                    <span>{{ $selectedPackageTitle }} ({{ $totalReviewsCount }})</span>
                </span>
                <a href="{{ route('reviews.index') }}" class="review-filter-btn px-4 py-2 rounded-xl text-xs font-medium shrink-0 transition-all bg-gray-100 dark:bg-[#15151A] text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10">
                    All Reviews ({{ $allReviewsCount ?? count($reviewList) }})
                </a>
            </div>
        @else
            <!-- Standard Category Filter Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-8 scrollbar-hide">
                <button onclick="filterReviews('all', this)" class="review-filter-btn active px-4 py-2 rounded-xl text-xs font-bold shrink-0 transition-all bg-black text-white dark:bg-white dark:text-black shadow-sm cursor-pointer">
                    All Reviews ({{ count($reviewList) }})
                </button>
                <button onclick="filterReviews('birthdays', this)" class="review-filter-btn px-4 py-2 rounded-xl text-xs font-medium shrink-0 transition-all bg-gray-100 dark:bg-[#15151A] text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 cursor-pointer">
                    Birthdays
                </button>
                <button onclick="filterReviews('proposals', this)" class="review-filter-btn px-4 py-2 rounded-xl text-xs font-medium shrink-0 transition-all bg-gray-100 dark:bg-[#15151A] text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 cursor-pointer">
                    Proposals & Anniversary
                </button>
                <button onclick="filterReviews('house-party', this)" class="review-filter-btn px-4 py-2 rounded-xl text-xs font-medium shrink-0 transition-all bg-gray-100 dark:bg-[#15151A] text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 cursor-pointer">
                    House Parties & Sound
                </button>
                <button onclick="filterReviews('weddings', this)" class="review-filter-btn px-4 py-2 rounded-xl text-xs font-medium shrink-0 transition-all bg-gray-100 dark:bg-[#15151A] text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 cursor-pointer">
                    Weddings & Haldi
                </button>
                <button onclick="filterReviews('kids', this)" class="review-filter-btn px-4 py-2 rounded-xl text-xs font-medium shrink-0 transition-all bg-gray-100 dark:bg-[#15151A] text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 cursor-pointer">
                    Baby Shower & Kids
                </button>
                <button onclick="filterReviews('corporate', this)" class="review-filter-btn px-4 py-2 rounded-xl text-xs font-medium shrink-0 transition-all bg-gray-100 dark:bg-[#15151A] text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 cursor-pointer">
                    Corporate Events
                </button>
            </div>
        @endif

        <!-- Reviews Grid -->
        <div id="reviews-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($reviewList as $index => $review)
                <div class="review-card-item" data-category="{{ $review['category'] ?? 'general' }}">
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
                                    <h3 class="font-heading font-bold text-xs sm:text-sm text-gray-950 dark:text-white truncate">
                                        {{ $review['name'] }}
                                    </h3>
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

                        <!-- Description Body with Inline Read More -->
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

        <!-- Bottom CTA Bar -->
        <div class="mt-14 sm:mt-16 p-8 rounded-3xl bg-[#FAF9F6] dark:bg-[#121217] border border-gray-200/90 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-6 text-center sm:text-left">
            <div>
                <h3 class="text-lg sm:text-xl font-heading font-extrabold text-gray-950 dark:text-white tracking-tight">
                    Ready to create your own celebration story?
                </h3>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-xl">
                    Browse ready-to-book celebration setups with zero online advance fees and guaranteed on-time setup across Indore.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('events.index') }}" class="px-5 py-3 rounded-xl bg-black text-white dark:bg-white dark:text-black font-heading font-bold text-xs uppercase tracking-wider hover:opacity-90 transition-opacity shadow-sm">
                    Explore Packages
                </a>
                <a href="{{ route('contact.index') }}" class="px-5 py-3 rounded-xl bg-white dark:bg-[#1A1A22] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white font-heading font-bold text-xs uppercase tracking-wider hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                    Contact Planner
                </a>
            </div>
        </div>

    </div>
</div>

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

<script>
    const artizenReviewsList = @json($reviewList);

    function filterReviews(category, btn) {
        document.querySelectorAll('.review-filter-btn').forEach(b => {
            b.classList.remove('active', 'bg-black', 'text-white', 'dark:bg-white', 'dark:text-black', 'shadow-sm');
            b.classList.add('bg-gray-100', 'dark:bg-[#15151A]', 'text-gray-700', 'dark:text-gray-300');
        });

        btn.classList.add('active', 'bg-black', 'text-white', 'dark:bg-white', 'dark:text-black', 'shadow-sm');
        btn.classList.remove('bg-gray-100', 'dark:bg-[#15151A]', 'text-gray-700', 'dark:text-gray-300');

        const items = document.querySelectorAll('.review-card-item');
        items.forEach(item => {
            const itemCat = item.getAttribute('data-category');
            if (category === 'all' || itemCat === category) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function openReviewModal(index) {
        const review = artizenReviewsList[index];
        if (!review) return;

        document.getElementById('modal-review-name').textContent = review.name || '';
        document.getElementById('modal-review-location').textContent = review.location || '';
        document.getElementById('modal-review-text').textContent = review.text ? `"${review.text}"` : '';
        document.getElementById('modal-review-badge').textContent = review.badge || 'Indore Event Setup';

        const avatarEl = document.getElementById('modal-review-avatar');
        avatarEl.className = 'w-10 h-10 rounded-full bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10 font-heading font-bold text-xs sm:text-sm flex items-center justify-center shrink-0';
        avatarEl.textContent = review.initial || (review.name ? review.name.charAt(0) : 'U');

        const starsEl = document.getElementById('modal-review-stars');
        let starsHtml = '';
        const rating = review.rating || 5;
        for (let i = 0; i < rating; i++) {
            starsHtml += '<i class="fa-solid fa-star"></i>';
        }
        starsEl.innerHTML = starsHtml;

        const modal = document.getElementById('full-review-modal');
        const modalContent = modal.querySelector('.relative');

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        });

        document.body.classList.add('overflow-hidden');
        document.documentElement.classList.add('overflow-hidden');
    }

    function closeReviewModal() {
        const modal = document.getElementById('full-review-modal');
        if (!modal || modal.classList.contains('hidden')) return;

        const modalContent = modal.querySelector('.relative');
        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            document.documentElement.classList.remove('overflow-hidden');
        }, 220);
    }

    document.addEventListener('click', function (e) {
        const modal = document.getElementById('full-review-modal');
        if (modal && e.target === modal) {
            closeReviewModal();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeReviewModal();
        }
    });
</script>
@endsection

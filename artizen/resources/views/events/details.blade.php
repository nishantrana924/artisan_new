@extends('layouts.app')

@section('content')

    <script>
        window.currentMatchedId = {{ json_encode($eventId ?? 1) }};
        window.currentMatchedTierIdx = {{ json_encode($selectedTierIdx ?? 0) }};
        if (document.body) {
            document.body.classList.add('hospitality-page');
        } else {
            document.addEventListener('DOMContentLoaded', function() {
                document.body.classList.add('hospitality-page');
            });
        }
    </script>

    <!-- Clean Hospitality Theme Container for Details Page -->
    <div class="package-detail-page min-h-screen bg-white dark:bg-[#0C0C0E] text-[#171717] font-body selection:bg-[#FFD600] selection:text-[#171719] pb-24 pt-4 md:pt-6">
        <div class="max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8">

            <!-- 1. Breadcrumb Navigation -->
            <nav class="flex items-center gap-2 text-xs text-[#666666] mb-6 font-medium text-left">
                <a href="{{ route('home') }}" class="hover:text-[#171717] transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-[#999999]" aria-hidden="true"></i>
                <a href="{{ route('events.index') }}" class="hover:text-[#171717] transition-colors">Packages</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-[#999999]" aria-hidden="true"></i>
                <span id="breadcrumb-package" class="text-[#171717] font-semibold truncate max-w-[240px] sm:max-w-none">
                    {{ $event['tiers'][$selectedTierIdx]['name'] ?? ($event['title'] ?? 'Package Details') }}
                </span>
            </nav>

            <!-- 2. PACKAGE HERO (Left 9-Column Main Area + Right 3-Column Sticky Summary) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-10">

                <!-- Left 9 Columns Area: Gallery, Package Info & Dedicated About Section -->
                <div class="lg:col-span-9 flex flex-col gap-6 w-full">

                    <!-- Top Row: Gallery (5 cols) & Package Info (4 cols) -->
                    <div class="grid grid-cols-1 md:grid-cols-9 gap-6 items-start">
                        <!-- Left Column (5 cols): Gallery & Thumbnails -->
                        <div class="md:col-span-5 flex flex-col-reverse sm:flex-row gap-3.5 w-full items-stretch">
                            <!-- Thumbnails column (Vertical on desktop, horizontal on mobile) -->
                            <div class="flex flex-row sm:flex-col gap-2 shrink-0 justify-center items-center">
                                <button id="thumb-prev" aria-label="Previous image"
                                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-full border border-[#E8E5DF] flex items-center justify-center hover:border-gray-400 bg-white text-gray-700 shadow-sm transition-all cursor-pointer">
                                    <i class="fa-solid fa-chevron-up text-xs -rotate-90 sm:rotate-0" aria-hidden="true"></i>
                                </button>
                                <div id="detail-thumbnails-container"
                                    class="flex flex-row sm:flex-col gap-2 overflow-x-auto sm:overflow-y-auto max-w-[280px] sm:max-w-none sm:max-h-[360px] scrollbar-hide select-none scroll-smooth py-1">
                                    <!-- Populated dynamically via JS -->
                                </div>
                                <button id="thumb-next" aria-label="Next image"
                                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-full border border-[#E8E5DF] flex items-center justify-center hover:border-gray-400 bg-white text-gray-700 shadow-sm transition-all cursor-pointer">
                                    <i class="fa-solid fa-chevron-down text-xs -rotate-90 sm:rotate-0" aria-hidden="true"></i>
                                </button>
                            </div>

                            <!-- Showcase Box -->
                            <div class="flex-grow aspect-square overflow-hidden bg-white border border-[#E8E5DF] rounded-2xl shadow-sm relative group">
                                <img id="detail-image" src="{{ $event['tiers'][$selectedTierIdx]['image'] ?? ($event['image'] ?? asset('assets/images/hero/1.jpg')) }}" alt="Main Package Image" class="w-full h-full object-cover">
                                <!-- Expand / View Full Gallery Button -->
                                <button onclick="openFullGalleryModal()" aria-label="Expand image"
                                    class="absolute top-3.5 right-3.5 w-8 h-8 rounded-full bg-white/95 hover:bg-white text-[#171717] shadow-sm flex items-center justify-center border border-[#E8E5DF] transition-all cursor-pointer z-10">
                                    <i class="fa-solid fa-expand text-xs" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Center Column (4 cols): Package Information -->
                        <div class="md:col-span-4 flex flex-col gap-3 text-left">
                            <!-- Top Info Block -->
                            <div class="flex flex-col gap-2">
                                <!-- Category / Tier Tag -->
                                <div class="inline-flex">
                                    <span id="detail-tag"
                                        class="text-[10px] font-heading font-extrabold text-[#171719] dark:text-[#FFD600] bg-[#FFD600]/20 border border-[#FFD600]/40 px-2.5 py-1 rounded-md uppercase tracking-wider leading-none">
                                        {{ $event['tiers'][$selectedTierIdx]['badge'] ?? 'LUXURY PACKAGE' }}
                                    </span>
                                </div>

                                <!-- Package Title & Pricing -->
                                <div>
                                    <h1 id="detail-title"
                                        class="font-heading font-extrabold text-2xl sm:text-3xl text-main-text uppercase tracking-tight leading-tight">
                                        {{ $event['tiers'][$selectedTierIdx]['name'] ?? ($event['title'] ?? 'Package') }}
                                    </h1>
                                    <div class="text-xs font-semibold text-[#666666] dark:text-[#A1A1AA] mt-1.5">
                                        Starting from <span id="detail-starting-price" class="font-extrabold text-main-text text-base ml-1">₹{{ number_format($event['tiers'][$selectedTierIdx]['price'] ?? 6000) }}</span>
                                    </div>
                                    <p id="detail-desc" class="font-body text-xs text-[#666666] dark:text-[#A1A1AA] leading-relaxed mt-1.5">
                                        {{ $event['tiers'][$selectedTierIdx]['desc'] ?? ($event['desc'] ?? 'Select your perfect celebration setup from our tiered packages. We handle decoration, sound, lighting, and coordination.') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Key Inclusions Block -->
                            <div class="pt-2 border-t border-[#E8E5DF] dark:border-white/10">
                                <h4 class="text-[10px] font-heading font-extrabold text-gold uppercase tracking-wider mb-2">KEY INCLUSIONS</h4>
                                <ul id="meta-inclusions-list" class="grid grid-cols-1 sm:grid-cols-2 gap-x-2 gap-y-1.5 list-none pl-0">
                                    @foreach(array_slice($event['tiers'][$selectedTierIdx]['inclusions'] ?? [], 0, 6) as $inc)
                                        <li class="flex items-center gap-2 py-0.5 text-xs text-[#333333] dark:text-[#D4D4D8] font-medium font-body">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#FFD600] shrink-0"></span>
                                            <span class="truncate">{{ $inc }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Setup Highlights & Perks (Clean Minimal Dots) -->
                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-[#E8E5DF] dark:border-white/10 text-xs text-[#555555] dark:text-[#D4D4D8]">
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#FFD600] shrink-0"></span>
                                    <span class="text-[11px] font-medium text-main-text">Customizable Themes</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#FFD600] shrink-0"></span>
                                    <span class="text-[11px] font-medium text-main-text">Verified Team & Crew</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#FFD600] shrink-0"></span>
                                    <span class="text-[11px] font-medium text-main-text">Flexible Setup Timings</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#FFD600] shrink-0"></span>
                                    <span class="text-[11px] font-medium text-main-text">All Indore Locations</span>
                                </div>
                            </div>

                            <!-- 3 Simple Facts (In Elegant Fact Card) -->
                            <div class="grid grid-cols-3 gap-2 bg-[#FAF9F5] dark:bg-white/[0.04] border border-[#E8E5DF] dark:border-white/10 rounded-xl p-3">
                                <!-- Setup Time -->
                                <div class="flex items-center gap-2">
                                    <i class="fa-regular fa-clock text-gold text-base shrink-0" aria-hidden="true"></i>
                                    <div class="leading-none text-left">
                                        <span id="detail-duration" class="font-heading text-xs font-bold text-main-text block">3-4 Hours</span>
                                        <span class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium block mt-1">Setup Time</span>
                                    </div>
                                </div>

                                <!-- Ideal For -->
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-users text-gold text-base shrink-0" aria-hidden="true"></i>
                                    <div class="leading-none text-left">
                                        <span id="detail-ideal-for" class="font-heading text-xs font-bold text-main-text block">100+ Guests</span>
                                        <span class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium block mt-1">Ideal For</span>
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-location-dot text-gold text-base shrink-0" aria-hidden="true"></i>
                                    <div class="leading-none text-left">
                                        <span id="detail-setup-location" class="font-heading text-xs font-bold text-main-text block">Indoor & Out</span>
                                        <span class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium block mt-1">Location</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. ABOUT THIS PACKAGE SECTION (Dedicated Section with Reduced Width - 9 Columns!) -->
                    <div class="border border-[#E8E5DF] dark:border-white/10 bg-white dark:bg-[#141416] p-6 sm:p-7 rounded-2xl text-left flex flex-col gap-4 shadow-sm">
                        <div>
                            <h3 class="font-heading font-extrabold text-base uppercase tracking-wider text-main-text mb-2 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-gold"></i> About This Package
                            </h3>
                            <p id="detail-about-desc" class="font-body text-xs sm:text-[13px] text-[#555555] dark:text-[#A1A1AA] leading-relaxed">
                                {{ $event['tiers'][$selectedTierIdx]['desc'] ?? ($event['desc'] ?? 'Experience a flawless celebration with our premium setup. This curated package comes fully equipped with complete decor, professional sound, ambient illumination, and on-site coordination. Our verified team manages the entire setup and logistics, ensuring every detail is executed to perfection.') }}
                            </p>
                        </div>

                        <!-- 4 Compact Factual Highlights (Clean Dots) -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
                            <!-- Fact 1 -->
                            <div class="border border-[#E8E5DF] dark:border-white/10 rounded-xl p-3 flex items-center gap-2.5 bg-[#FAF9F5] dark:bg-white/[0.04]">
                                <span class="w-2 h-2 rounded-full bg-[#FFD600] shrink-0"></span>
                                <div>
                                    <h4 class="font-heading font-bold text-xs text-main-text">Affordable Price</h4>
                                    <p class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium">Best value for money</p>
                                </div>
                            </div>

                            <!-- Fact 2 -->
                            <div class="border border-[#E8E5DF] dark:border-white/10 rounded-xl p-3 flex items-center gap-2.5 bg-[#FAF9F5] dark:bg-white/[0.04]">
                                <span class="w-2 h-2 rounded-full bg-[#FFD600] shrink-0"></span>
                                <div>
                                    <h4 class="font-heading font-bold text-xs text-main-text">Hassle Free</h4>
                                    <p class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium">We handle everything</p>
                                </div>
                            </div>

                            <!-- Fact 3 -->
                            <div class="border border-[#E8E5DF] dark:border-white/10 rounded-xl p-3 flex items-center gap-2.5 bg-[#FAF9F5] dark:bg-white/[0.04]">
                                <span class="w-2 h-2 rounded-full bg-[#FFD600] shrink-0"></span>
                                <div>
                                    <h4 class="font-heading font-bold text-xs text-main-text">Professional Team</h4>
                                    <p class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium">Experienced & verified</p>
                                </div>
                            </div>

                            <!-- Fact 4 -->
                            <div class="border border-[#E8E5DF] dark:border-white/10 rounded-xl p-3 flex items-center gap-2.5 bg-[#FAF9F5] dark:bg-white/[0.04]">
                                <span class="w-2 h-2 rounded-full bg-[#FFD600] shrink-0"></span>
                                <div>
                                    <h4 class="font-heading font-bold text-xs text-main-text">On-Time Delivery</h4>
                                    <p class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium">Always on schedule</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column (Col span 3 on desktop): Sticky Booking Summary Card -->
                <div class="lg:col-span-3 lg:sticky lg:top-24 flex flex-col gap-3">
                    <!-- Package Summary Card -->
                    <div class="border border-[#E8E5DF] bg-white p-5 rounded-2xl shadow-sm flex flex-col gap-3.5 text-left relative overflow-hidden">
                        <h3 class="font-heading font-extrabold text-base text-main-text leading-tight">
                            Package Summary
                        </h3>

                        <!-- Price Box -->
                        <div class="bg-[#FAF9F6] border border-[#E8E5DF] p-3.5 rounded-xl flex flex-col gap-0.5">
                            <span class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-bold uppercase tracking-wider">Total Package Price</span>
                            <div class="flex items-baseline gap-2">
                                <span id="booking-card-price" class="font-heading font-extrabold text-2xl sm:text-3xl text-main-text">
                                    ₹{{ number_format($event['tiers'][$selectedTierIdx]['price'] ?? 6000) }}
                                </span>
                            </div>
                        </div>

                        <!-- Price Breakdown (Appears when Add-ons or surcharges are selected) -->
                        <div id="price-breakdown-container" class="hidden text-xs text-[#666666] dark:text-[#A1A1AA] space-y-1.5 border-b border-[#E8E5DF] dark:border-white/10 pb-2.5">
                            <div class="flex justify-between items-center">
                                <span>Base Package:</span>
                                <span id="breakdown-base" class="font-semibold text-main-text"></span>
                            </div>
                            <div id="breakdown-addons-row" class="flex justify-between items-center hidden">
                                <span>Selected Add-ons:</span>
                                <span id="breakdown-addons" class="font-semibold text-main-text"></span>
                            </div>
                            <div id="breakdown-travel-row" class="flex justify-between items-center hidden">
                                <span>Travel/Setup:</span>
                                <span id="breakdown-travel" class="font-semibold text-main-text"></span>
                            </div>
                        </div>

                        <!-- Service Details List -->
                        <div class="space-y-2 text-xs text-[#666666] dark:text-[#A1A1AA] font-medium border-b border-[#E8E5DF] dark:border-white/10 pb-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[#777777] dark:text-[#A1A1AA]">Service Location:</span>
                                <span class="font-bold text-main-text">Indore, MP</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#777777] dark:text-[#A1A1AA]">Setup & Logistics:</span>
                                <span class="font-bold text-[#16A34A]">INCLUDED (Free)</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#777777] dark:text-[#A1A1AA]">Payment Mode:</span>
                                <span class="font-bold text-main-text">Offline Post Confirmation</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-2 pt-0.5">
                            <!-- Primary CTA Button -->
                            <button onclick="bookPackageNow()"
                                class="w-full py-3.5 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] text-xs font-heading font-extrabold uppercase tracking-widest rounded-xl shadow-sm hover:shadow transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fa-solid fa-calendar-check text-xs" aria-hidden="true"></i> Book This Package
                            </button>

                            <!-- WhatsApp Secondary CTA Button -->
                            <button onclick="enquireOnWhatsAppFromDetails()"
                                class="w-full py-2.5 bg-white hover:bg-gray-50 border border-[#E8E5DF] hover:border-gray-400 text-main-text text-xs font-heading font-bold uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fa-brands fa-whatsapp text-emerald-500 text-sm" aria-hidden="true"></i> WhatsApp Inquiry
                            </button>
                        </div>

                        <p class="text-[10px] text-[#888888] dark:text-[#A1A1AA] text-center font-medium leading-relaxed">
                            Click <strong class="font-bold text-main-text">Book This Package</strong> to enter your event date, setup time & venue address on the booking checkout page.
                        </p>

                        <!-- Clean Factual Highlights with Minimal Dots -->
                        <div class="grid grid-cols-2 gap-2 pt-3 border-t border-[#E8E5DF] dark:border-white/10 text-left">
                            <div class="flex items-center gap-2.5 p-2 rounded-lg bg-[#FAF9F5] dark:bg-white/[0.04] border border-[#EFECE6] dark:border-white/10">
                                <span class="w-2 h-2 rounded-full bg-[#16A34A] dark:bg-[#22C55E] shrink-0 ml-0.5"></span>
                                <div class="leading-tight">
                                    <div class="text-[11px] font-bold text-main-text">Instant Confirm</div>
                                    <div class="text-[9px] text-[#777777] dark:text-[#A1A1AA]">Online request</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2.5 p-2 rounded-lg bg-[#FAF9F5] dark:bg-white/[0.04] border border-[#EFECE6] dark:border-white/10">
                                <span class="w-2 h-2 rounded-full bg-[#16A34A] dark:bg-[#22C55E] shrink-0 ml-0.5"></span>
                                <div class="leading-tight">
                                    <div class="text-[11px] font-bold text-main-text">Best Price</div>
                                    <div class="text-[9px] text-[#777777] dark:text-[#A1A1AA]">Fixed transparent</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2.5 p-2 rounded-lg bg-[#FAF9F5] dark:bg-white/[0.04] border border-[#EFECE6] dark:border-white/10">
                                <span class="w-2 h-2 rounded-full bg-[#16A34A] dark:bg-[#22C55E] shrink-0 ml-0.5"></span>
                                <div class="leading-tight">
                                    <div class="text-[11px] font-bold text-main-text">Free Custom</div>
                                    <div class="text-[9px] text-[#777777] dark:text-[#A1A1AA]">Venue tailored</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2.5 p-2 rounded-lg bg-[#FAF9F5] dark:bg-white/[0.04] border border-[#EFECE6] dark:border-white/10">
                                <span class="w-2 h-2 rounded-full bg-[#16A34A] dark:bg-[#22C55E] shrink-0 ml-0.5"></span>
                                <div class="leading-tight">
                                    <div class="text-[11px] font-bold text-main-text">24×7 Support</div>
                                    <div class="text-[9px] text-[#777777] dark:text-[#A1A1AA]">Event coordinator</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 4. TWO-COLUMN GRID: WHAT'S INCLUDED & ADD-ONS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start mb-10">

                <!-- Left: What's Included -->
                <div class="border border-[#E8E5DF] bg-white p-6 sm:p-7 rounded-xl text-left flex flex-col gap-4 shadow-sm h-full">
                    <h3 class="font-heading font-bold text-base uppercase tracking-wider text-[#171717] mb-2">
                        What's Included
                    </h3>
                    <div id="full-inclusions-checklist" class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                        <!-- Populated dynamically via JS -->
                    </div>

                    <!-- Travel Note Alert -->
                    <div class="border border-[#FFD600]/30 bg-[#FFD600]/5 p-3.5 rounded-lg flex items-start gap-2.5 mt-auto text-left">
                        <i class="fa-solid fa-circle-info text-gold text-sm shrink-0 mt-0.5" aria-hidden="true"></i>
                        <p class="text-xs text-[#171719] dark:text-[#FFD600] font-medium leading-normal">
                            Note: Travel charges may apply based on your exact venue location in Indore.
                        </p>
                    </div>
                </div>

                <!-- Right: Make It More Special (Add-ons) (Clean horizontal list rows) -->
                <div class="border border-[#E8E5DF] bg-white p-6 sm:p-7 rounded-xl text-left flex flex-col gap-4 shadow-sm h-full">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="font-heading font-bold text-base uppercase tracking-wider text-[#171717]">
                            Make It More Special (Add-ons)
                        </h3>
                    </div>
                    <div id="addons-container" class="flex flex-col gap-2.5">
                        <!-- Populated dynamically as horizontal list rows via JS -->
                    </div>
                </div>

            </div>

            <!-- 5. PACKAGE GALLERY SECTION -->
            <div class="border border-[#E8E5DF] bg-white p-6 sm:p-7 rounded-xl text-left shadow-sm mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading font-bold text-base uppercase tracking-wider text-[#171717]">
                        Package Gallery
                    </h3>
                    <button onclick="openFullGalleryModal()"
                        class="text-xs font-heading font-bold text-[#666666] hover:text-[#171717] border border-[#E8E5DF] hover:border-gray-400 px-3.5 py-1.5 rounded-lg transition-all inline-flex items-center gap-1.5 cursor-pointer">
                        View Full Gallery <i class="fa-solid fa-arrow-right text-[10px]" aria-hidden="true"></i>
                    </button>
                </div>
                <div id="package-gallery-grid" class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>

            <!-- 6. CUSTOMER REVIEWS (Package-Particular Reviews & Interactive Review Form) -->
            @php
                $reviewsInfo = $packageReviewsData ?? \App\Http\Controllers\ReviewController::getReviewsForPackage(
                    $event['slug'] ?? ($slug ?? ''),
                    $event['title'] ?? '',
                    $event['category'] ?? ''
                );
                $packageReviews = $reviewsInfo['reviews'] ?? [];
                $totalReviewsCount = $reviewsInfo['total_count'] ?? count($packageReviews);
                $avgRating = $reviewsInfo['avg_rating'] ?? '4.9';
                $recommendPercent = $reviewsInfo['recommend_percent'] ?? 98;
                $displayedReviews = $packageReviews;
                $sliderReviews = count($displayedReviews) > 0 && count($displayedReviews) < 6 
                    ? array_merge($displayedReviews, $displayedReviews) 
                    : $displayedReviews;
            @endphp

            <div class="border border-[#E8E5DF] bg-white p-6 sm:p-7 rounded-xl text-left shadow-sm mb-10" id="reviews-section">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-[#EFECE6]">
                    <div>
                        <h3 class="font-heading font-extrabold text-base sm:text-lg uppercase tracking-wider text-[#171717]">
                            Customer Reviews
                        </h3>
                        <p class="text-xs text-[#777777] mt-0.5">
                            Verified experiences for <span class="font-bold text-gray-900">{{ $event['title'] ?? 'this package' }}</span> in Indore
                        </p>
                    </div>
                    <div class="flex items-center gap-2.5 shrink-0">
                        <button type="button" onclick="openWriteReviewModal()"
                            class="px-4 py-2 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] text-xs font-heading font-extrabold uppercase tracking-wider rounded-xl transition-all inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
                            <i class="fa-solid fa-pen-to-square text-xs" aria-hidden="true"></i> Write a Review
                        </button>
                        <a href="{{ route('reviews.index', ['package' => $event['slug'] ?? ($slug ?? ''), 'title' => $event['title'] ?? '']) }}"
                            class="text-xs font-heading font-bold text-gray-800 dark:text-gray-200 hover:text-black dark:hover:text-white border border-[#E8DFC8] dark:border-white/20 hover:border-gray-400 px-4 py-2 rounded-xl transition-all inline-flex items-center cursor-pointer shadow-2xs">
                            View All (<span class="header-review-count">{{ $totalReviewsCount }}</span>)
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                    <!-- Left: Overall Dynamic Rating Box -->
                    <div class="lg:col-span-3 bg-[#FAF9F5] border border-[#E8E5DF] rounded-xl p-5 sm:p-6 flex flex-col items-center justify-center text-center">
                        <span id="display-avg-rating" class="font-heading font-extrabold text-5xl text-[#171717] leading-none mb-2">{{ $avgRating }}</span>
                        <div class="flex items-center gap-1 text-amber-400 text-sm mb-2">
                            @for($s = 1; $s <= 5; $s++)
                                <i class="fa-solid fa-star {{ $s <= round((float)$avgRating) ? 'text-amber-400' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                        <p class="text-xs text-[#777777] font-medium mb-3">Based on <span id="display-review-count" class="font-bold text-gray-900">{{ $totalReviewsCount }}</span> reviews</p>
                        <div class="flex flex-wrap gap-1.5 justify-center mb-5">
                            <span class="text-[11px] bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/40 px-3 py-1 rounded-full font-bold">
                                {{ $recommendPercent }}% Recommend
                            </span>
                        </div>
                        <button type="button" onclick="openWriteReviewModal()"
                            class="w-full py-3 px-4 bg-[#FFD600] hover:bg-[#E6C200] active:scale-[0.98] text-[#171719] text-xs font-heading font-extrabold uppercase tracking-wider rounded-xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer group">
                            <i class="fa-solid fa-pen-to-square text-xs transition-transform group-hover:scale-110"></i>
                            <span>Write a Review</span>
                        </button>
                    </div>

                    <!-- Right: Swiper Package-Specific Review Cards with Autoplay Loop -->
                    <div class="lg:col-span-9 relative flex flex-col justify-center min-w-0 overflow-hidden">
                        <div class="swiper package-reviews-swiper w-full select-none py-1">
                            <div class="swiper-wrapper items-stretch" id="reviews-cards-grid">
                                @forelse($sliderReviews as $idx => $rev)
                                    @php
                                        $origIdx = count($displayedReviews) > 0 ? ($idx % count($displayedReviews)) : 0;
                                    @endphp
                                    <div class="swiper-slide h-auto">
                                        <div onclick="openSingleReviewModal({{ $origIdx }})"
                                             class="h-full border border-[#E8E5DF] dark:border-white/10 rounded-2xl p-4 sm:p-5 bg-white dark:bg-[#151518] hover:border-gray-400 dark:hover:border-white/30 transition-all flex flex-col justify-between shadow-2xs text-left cursor-pointer group hover:shadow-md"
                                             role="button"
                                             tabindex="0"
                                             onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();openSingleReviewModal({{ $origIdx }});}">
                                            <div>
                                                <div class="flex items-start justify-between mb-3">
                                                    <div class="flex items-center gap-2.5 min-w-0">
                                                        <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10 flex items-center justify-center font-bold text-xs shrink-0">
                                                            {{ $rev['initial'] ?? strtoupper(substr($rev['name'] ?? 'U', 0, 1)) }}
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="font-heading font-bold text-xs text-[#171717] dark:text-white truncate">{{ $rev['name'] }}</p>
                                                            <p class="text-[10px] text-[#777777] dark:text-gray-400 truncate">{{ $rev['location'] }} · {{ $rev['date'] }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center text-amber-400 text-[10px] shrink-0">
                                                        @for($s = 1; $s <= 5; $s++)
                                                            <i class="fa-solid fa-star {{ $s <= ($rev['rating'] ?? 5) ? 'text-amber-400' : 'text-gray-300' }}"></i>
                                                        @endfor
                                                    </div>
                                                </div>
                                                <p class="text-xs text-[#555555] dark:text-gray-300 leading-relaxed line-clamp-4">
                                                    @if(strlen($rev['text'] ?? '') > 115)
                                                        {{ \Illuminate\Support\Str::limit($rev['text'], 110, '') }}... <span class="font-bold text-gray-900 dark:text-[#FFD600] group-hover:underline">read more</span>
                                                    @else
                                                        {{ $rev['text'] }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="swiper-slide w-full">
                                        <div class="w-full py-8 text-center text-gray-500 text-xs bg-[#FAF9F5] rounded-xl border border-dashed border-[#E8E5DF]">
                                            No reviews submitted yet for this package. Be the first to share your experience!
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 7. SIMILAR CELEBRATIONS YOU MAY LIKE (Exact Home Page Event Card UI Design & Real Catalog Data) -->
            <div id="similar-celebrations" class="mt-14 pt-8 border-t border-[#E8E5DF] dark:border-white/10 text-left">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-heading font-extrabold text-lg sm:text-xl uppercase tracking-wide text-gray-950 dark:text-white">
                            Similar Celebrations You May Like
                        </h3>
                        <p class="text-xs text-[#666666] dark:text-[#A1A1AA] mt-1 font-body">
                            Handcrafted setups with end-to-end decor, sound, and on-site Indore coordination
                        </p>
                    </div>

                    <a href="{{ route('events.index') }}"
                        class="text-xs font-heading font-bold text-gray-900 dark:text-white border border-[#E8DFC8] dark:border-white/10 hover:border-gray-400 px-4 py-2 rounded-xl transition-all uppercase tracking-wider shrink-0 cursor-pointer shadow-xs">
                        View All Packages
                    </a>
                </div>

                @php
                    $displayItems = !empty($similarCelebrations) ? $similarCelebrations : [];
                    if (empty($displayItems)) {
                        $allCatPkgs = \App\Services\CelebrationCatalogService::getAllPackages();
                        $displayItems = array_slice($allCatPkgs, 0, 4);
                    }
                @endphp

                <!-- 4-Column Responsive Grid matching Home Page Event Card UI Design Exactly -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 lg:gap-4">
                    @foreach($displayItems as $simItem)
                        @php
                            $itemPrice = (int)$simItem['price'];
                            $origPrice = (int)($simItem['original_price'] ?? round($itemPrice * 1.15));
                            $eventDetailsUrl = route('events.show', ['slug' => $simItem['slug'] ?? \Illuminate\Support\Str::slug($simItem['title'])]);
                        @endphp

                        <a href="{{ $eventDetailsUrl }}" 
                           class="block w-full bg-white rounded-2xl border border-[#E8DFC8] overflow-hidden shadow-none text-left flex flex-col justify-between h-full select-none cursor-pointer">
                            
                            <!-- Image Box (Aspect 4/3.8 Crisp & Clean, No Hover Zoom) -->
                            <div class="relative w-full aspect-[4/3.8] overflow-hidden bg-[#FAF7F2] shrink-0">
                                <img src="{{ $simItem['image'] }}" 
                                     alt="{{ $simItem['title'] }}" 
                                     class="w-full h-full object-cover select-none pointer-events-none" 
                                     draggable="false"
                                     loading="lazy">
                                
                                <!-- Rating Badge Top-Right -->
                                <span class="absolute top-2.5 right-2.5 bg-white/95 backdrop-blur-xs px-2 py-0.5 rounded text-[11px] font-bold text-gray-900 flex items-center gap-1 shadow-2xs z-20">
                                    <i class="fa-solid fa-star text-amber-400 text-[10px]"></i>
                                    <span>{{ $simItem['rating'] ?? '4.9' }}</span>
                                </span>
                            </div>

                            <!-- Body -->
                            <div class="p-3 sm:p-3.5 flex flex-col justify-between flex-1">
                                <div class="mb-2.5">
                                    <span class="text-[9.5px] sm:text-[10px] font-heading font-extrabold uppercase tracking-wider text-[#B89700] block mb-0.5 truncate">
                                        {{ $simItem['subcategory'] ?? ($simItem['category_name'] ?? 'Celebration Setup') }}
                                    </span>
                                    <h4 class="font-heading font-bold text-[13.5px] sm:text-[14.5px] text-gray-900 leading-snug line-clamp-1">
                                        {{ $simItem['title'] }}
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
                    @endforeach
                </div>
            </div>

            <style>
                /* Strictly disable all card shadows, hover animations, image zoom, and border hover transforms */
                #similar-celebrations a,
                #similar-celebrations a:hover,
                #similar-celebrations a:focus,
                #similar-celebrations a:active {
                    box-shadow: none !important;
                    -webkit-box-shadow: none !important;
                    transform: none !important;
                    -webkit-transform: none !important;
                    transition: none !important;
                    -webkit-transition: none !important;
                    border-color: #E8DFC8 !important;
                }
                #similar-celebrations img,
                #similar-celebrations a:hover img,
                #similar-celebrations img:hover {
                    transform: none !important;
                    -webkit-transform: none !important;
                    scale: none !important;
                    transition: none !important;
                    -webkit-transition: none !important;
                    animation: none !important;
                }
                body.overflow-hidden,
                html.overflow-hidden {
                    overflow: hidden !important;
                    height: 100% !important;
                    touch-action: none;
                    -webkit-overflow-scrolling: auto;
                }
            </style>

        </div>
    </div>

    <!-- Mobile Floating Sticky CTA Bar (Visible on Mobile < 768px) -->
    <div class="package-detail-sticky-bar md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-[#141416]/95 backdrop-blur-md border-t border-[#E8E5DF] dark:border-white/10 p-3.5 flex items-center justify-between gap-3 shadow-2xl">
        <div class="flex flex-col text-left">
            <span class="text-[9px] font-bold text-[#777777] dark:text-gray-400 uppercase tracking-wider">Total Package Price</span>
            <span id="mobile-sticky-price" class="text-base font-extrabold text-main-text">₹{{ number_format($event['tiers'][$selectedTierIdx]['price'] ?? 6000) }}</span>
        </div>
        <button onclick="bookPackageNow()"
            class="px-5 py-2.5 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] text-xs font-extrabold uppercase tracking-wider rounded-lg shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
            <i class="fa-solid fa-calendar-check"></i> Book Package
        </button>
    </div>

    <!-- Full Gallery Lightbox Modal -->
    <div id="full-gallery-modal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="bg-white border border-[#E8E5DF] w-full max-w-4xl rounded-xl p-6 shadow-2xl flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between border-b border-[#E8E5DF] pb-3 mb-4">
                <h3 class="font-heading font-extrabold text-sm uppercase tracking-wider text-[#171717]">Package Photo Gallery</h3>
                <button onclick="closeFullGalleryModal()" class="text-gray-500 hover:text-black cursor-pointer bg-transparent border-0 focus:outline-none" aria-label="Close gallery modal">
                    <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
                </button>
            </div>
            <div class="overflow-y-auto max-h-[70vh] grid grid-cols-2 md:grid-cols-3 gap-3 p-1" id="modal-gallery-container">
                <!-- Injected via JS -->
            </div>
        </div>
    </div>

    <!-- Single Review Details Modal (Read Full Review without Page Scroll) -->
    <div id="single-review-modal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#151518] border border-[#E8E5DF] dark:border-white/10 w-full max-w-lg rounded-2xl p-6 sm:p-7 shadow-2xl flex flex-col relative text-left text-gray-900 dark:text-white max-h-[90vh]">
            <!-- Top Row: Avatar, Reviewer Info & Close Button -->
            <div class="flex items-start justify-between pb-4 border-b border-[#EFECE6] dark:border-white/10 mb-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div id="single-modal-avatar" class="w-10 h-10 rounded-full bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10 flex items-center justify-center font-bold text-sm shrink-0">
                        U
                    </div>
                    <div class="min-w-0">
                        <h4 id="single-modal-author" class="font-heading font-extrabold text-sm sm:text-base text-gray-950 dark:text-white truncate">
                            Verified Customer
                        </h4>
                        <p id="single-modal-meta" class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                            Indore, MP
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeSingleReviewModal()" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/20 text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white flex items-center justify-center transition-colors cursor-pointer focus:outline-none shrink-0" aria-label="Close review modal">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Rating -->
            <div class="flex items-center gap-2 mb-4">
                <div id="single-modal-stars" class="flex items-center text-amber-400 text-sm gap-1">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                </div>
            </div>

            <!-- Full Review Description Body -->
            <div class="overflow-y-auto max-h-[50vh] pr-1 mb-5">
                <p id="single-modal-text" class="text-xs sm:text-sm text-[#444444] dark:text-gray-300 leading-relaxed font-normal whitespace-pre-line">
                    <!-- Loaded dynamically via JS -->
                </p>
            </div>

            <!-- Bottom Package Tag & Close CTA -->
            <div class="pt-3 border-t border-[#EFECE6] dark:border-white/10 flex items-center justify-between gap-3 text-xs">
                <div class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                    Package: <span class="font-bold text-gray-900 dark:text-white">{{ $event['title'] ?? 'Celebration Setup' }}</span>
                </div>
                <button type="button" onclick="closeSingleReviewModal()"
                    class="px-4 py-2 bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/20 text-gray-800 dark:text-gray-200 font-heading font-bold text-xs rounded-xl transition-colors cursor-pointer shrink-0">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Write a Review Modal -->
    @php
        $reviewAuthUser = Auth::user();
        $isReviewUserLoggedIn = $reviewAuthUser || session('user_logged_in');
        $reviewAuthName = $reviewAuthUser ? $reviewAuthUser->name : session('user_name');
        $reviewAuthEmail = $reviewAuthUser ? $reviewAuthUser->email : session('user_email');
    @endphp

    <div id="write-review-modal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#151518] border border-[#E8E5DF] dark:border-white/10 w-full max-w-md rounded-3xl p-6 sm:p-7 shadow-2xl flex flex-col relative text-left text-gray-900 dark:text-white">
            <!-- Close Button -->
            <button type="button" onclick="closeWriteReviewModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-white/10 dark:hover:bg-white/20 text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white flex items-center justify-center transition-colors cursor-pointer focus:outline-none" aria-label="Close modal">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            @if($isReviewUserLoggedIn)
                <!-- Logged In: Clean Review Form -->
                <div class="pb-3 mb-3 border-b border-gray-100 dark:border-white/10 pr-8">
                    <h3 class="font-heading font-extrabold text-base text-[#171717] dark:text-white">Write a Review</h3>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 truncate">Posting as <span class="font-semibold text-gray-900 dark:text-white">{{ $reviewAuthName }}</span></p>
                </div>

                <form id="write-review-form" onsubmit="submitCustomerReview(event)" class="flex flex-col gap-4">
                    @csrf
                    <input type="hidden" name="package_slug" value="{{ $event['slug'] ?? ($slug ?? '') }}">
                    <input type="hidden" name="event_title" value="{{ $event['title'] ?? 'Celebration Setup' }}">

                    <!-- Interactive Star Rating -->
                    <div>
                        <label class="block text-xs font-heading font-bold text-gray-900 dark:text-white mb-1.5">Rating *</label>
                        <div class="flex items-center gap-2.5">
                            <div id="star-rating-selector" class="flex items-center gap-1.5 text-2xl text-gray-300">
                                <i class="fa-solid fa-star text-amber-400 cursor-pointer transition-colors" data-val="1" onclick="selectStarRating(1)"></i>
                                <i class="fa-solid fa-star text-amber-400 cursor-pointer transition-colors" data-val="2" onclick="selectStarRating(2)"></i>
                                <i class="fa-solid fa-star text-amber-400 cursor-pointer transition-colors" data-val="3" onclick="selectStarRating(3)"></i>
                                <i class="fa-solid fa-star text-amber-400 cursor-pointer transition-colors" data-val="4" onclick="selectStarRating(4)"></i>
                                <i class="fa-solid fa-star text-amber-400 cursor-pointer transition-colors" data-val="5" onclick="selectStarRating(5)"></i>
                            </div>
                            <span id="star-rating-text" class="text-xs font-bold text-emerald-600">5 Stars (Exceptional!)</span>
                        </div>
                        <input type="hidden" id="selected-rating-input" name="rating" value="5">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-heading font-bold text-gray-900 dark:text-white mb-1">Your Review *</label>
                        <textarea name="review" id="review-input-text" required rows="4" placeholder="How was your celebration experience? (punctuality, decor quality, team coordination)..."
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-300 dark:border-white/15 bg-white dark:bg-[#1D1D22] text-[#171719] dark:text-white placeholder-gray-400 focus:border-black dark:focus:border-[#FFD600] focus:ring-0 outline-none transition-colors"></textarea>
                    </div>

                    <div id="review-form-alert" class="hidden p-3 rounded-xl text-xs font-medium"></div>

                    <!-- Buttons -->
                    <div class="pt-2 border-t border-gray-100 dark:border-white/10 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeWriteReviewModal()"
                            class="px-4 py-2.5 rounded-xl border border-gray-300 dark:border-white/20 text-xs font-heading font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" id="submit-review-btn"
                            class="px-5 py-2.5 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] text-xs font-heading font-extrabold uppercase tracking-wider rounded-xl transition-all shadow-sm flex items-center gap-1.5 cursor-pointer">
                            <span>Submit Review</span>
                        </button>
                    </div>
                </form>
            @else
                <!-- Logged Out: Clean, Minimal, Premium Login Modal (No unwanted text, perfectly formatted buttons) -->
                <div class="pt-3 pb-2 text-center flex flex-col items-center justify-center">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-500/10 text-amber-500 flex items-center justify-center mb-3 shadow-xs border border-amber-200/60 dark:border-amber-500/20">
                        <i class="fa-solid fa-user-lock text-lg"></i>
                    </div>

                    <h3 class="font-heading font-extrabold text-base sm:text-lg text-gray-950 dark:text-white mb-1.5">
                        Sign in to leave a review
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 max-w-xs mb-6 leading-relaxed">
                        Log in to your account to share your verified celebration experience.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full">
                        <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                            class="flex-1 w-full py-3 px-4 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] text-xs font-heading font-extrabold uppercase tracking-wider rounded-xl transition-all shadow-sm text-center flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer">
                            <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                            <span>Log In</span>
                        </a>
                        <a href="{{ route('register') }}"
                            class="flex-1 w-full py-3 px-4 border border-gray-300 dark:border-white/20 hover:bg-gray-50 dark:hover:bg-white/5 text-gray-800 dark:text-gray-200 text-xs font-heading font-bold uppercase tracking-wider rounded-xl transition-all text-center flex items-center justify-center whitespace-nowrap cursor-pointer">
                            <span>Create Account</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Details Data & Controller Script -->
    <script>
        window.eventDatabase = @json($packagesDb ?? []);
        window.currentMatchedId = "{{ $eventId ?? 1 }}";
        window.currentMatchedTierIdx = {{ $selectedTierIdx ?? 0 }};
        window.packageReviewsList = @json($packageReviews ?? []);

        function lockBodyScroll() {
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
            document.body.classList.add('overflow-hidden');
            document.documentElement.classList.add('overflow-hidden');
        }

        function unlockBodyScroll() {
            const writeModal = document.getElementById('write-review-modal');
            const singleModal = document.getElementById('single-review-modal');
            const galleryModal = document.getElementById('full-gallery-modal');

            const isAnyOpen = (writeModal && !writeModal.classList.contains('hidden')) ||
                              (singleModal && !singleModal.classList.contains('hidden')) ||
                              (galleryModal && !galleryModal.classList.contains('hidden'));

            if (!isAnyOpen) {
                document.body.style.overflow = '';
                document.documentElement.style.overflow = '';
                document.body.classList.remove('overflow-hidden');
                document.documentElement.classList.remove('overflow-hidden');
            }
        }

        function openFullGalleryModal() {
            const modal = document.getElementById('full-gallery-modal');
            const container = document.getElementById('modal-gallery-container');
            if (!modal || !container) return;

            const db = window.eventDatabase || {};
            const data = db[window.currentMatchedId] || Object.values(db)[0] || {};
            const selectedTier = (data.tiers && data.tiers[window.currentMatchedTierIdx]) ? data.tiers[window.currentMatchedTierIdx] : (data.tiers ? data.tiers[0] : data);

            const allImages = [
                selectedTier.image,
                ...(selectedTier.gallery || []),
                data.image,
                ...(data.gallery || []),
                ...((data.tiers || []).map(t => t.image))
            ].filter(Boolean).filter((img, pos, self) => self.indexOf(img) === pos);

            container.innerHTML = '';
            allImages.forEach(imgSrc => {
                const imgDiv = document.createElement('div');
                imgDiv.className = 'aspect-[4/3] rounded-lg overflow-hidden border border-[#E8E5DF] dark:border-white/10 bg-white dark:bg-white/5 cursor-pointer';
                imgDiv.innerHTML = `<img src="${imgSrc}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">`;
                imgDiv.addEventListener('click', () => {
                    const mainImg = document.getElementById('detail-image');
                    if (mainImg) mainImg.src = imgSrc;
                    closeFullGalleryModal();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
                container.appendChild(imgDiv);
            });

            modal.classList.remove('hidden');
            lockBodyScroll();
        }

        function closeFullGalleryModal() {
            const modal = document.getElementById('full-gallery-modal');
            if (modal) modal.classList.add('hidden');
            unlockBodyScroll();
        }

        // Swiper Initialization for Package Reviews with Loop & Autoplay
        function initPackageReviewsSwiper() {
            const swiperEl = document.querySelector('.package-reviews-swiper');
            if (!swiperEl || typeof Swiper === 'undefined') return;

            if (window.packageReviewsSwiper && typeof window.packageReviewsSwiper.destroy === 'function') {
                try {
                    window.packageReviewsSwiper.destroy(true, true);
                } catch(e) {}
            }

            const slides = swiperEl.querySelectorAll('.swiper-wrapper > .swiper-slide');
            if (slides.length <= 0) return;

            window.packageReviewsSwiper = new Swiper('.package-reviews-swiper', {
                slidesPerView: 1.15,
                spaceBetween: 16,
                grabCursor: true,
                loop: slides.length > 1,
                loopAdditionalSlides: 2,
                autoplay: {
                    delay: 3200,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },
                speed: 750,
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 16,
                    },
                    1024: {
                        slidesPerView: 2.6,
                        spaceBetween: 20,
                    },
                    1280: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    }
                }
            });
        }

        // Open Individual Review Details Modal (Background Scroll Locked)
        function openSingleReviewModal(idx) {
            if (window.packageReviewsSwiper && window.packageReviewsSwiper.autoplay) {
                window.packageReviewsSwiper.autoplay.stop();
            }

            const list = window.packageReviewsList || [];
            const rev = list[idx];
            if (!rev) return;

            const modal = document.getElementById('single-review-modal');
            if (!modal) return;

            const authorEl = document.getElementById('single-modal-author');
            if (authorEl) authorEl.textContent = rev.name || rev.author || 'Verified Customer';

            const metaEl = document.getElementById('single-modal-meta');
            if (metaEl) metaEl.textContent = `${rev.location || 'Indore, MP'} · ${rev.date || rev.created_at || 'Recently Verified'}`;

            const textEl = document.getElementById('single-modal-text');
            if (textEl) textEl.textContent = rev.text || rev.review || '';

            const avatarEl = document.getElementById('single-modal-avatar');
            if (avatarEl) {
                avatarEl.className = 'w-10 h-10 rounded-full bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10 flex items-center justify-center font-bold text-sm shrink-0';
                avatarEl.textContent = rev.initial || (rev.name || rev.author || 'U').charAt(0).toUpperCase();
            }

            const starsEl = document.getElementById('single-modal-stars');
            if (starsEl) {
                const rating = parseInt(rev.rating) || 5;
                let starsHtml = '';
                for (let i = 1; i <= 5; i++) {
                    starsHtml += `<i class="fa-solid fa-star ${i <= rating ? 'text-amber-400' : 'text-gray-300 dark:text-gray-600'}"></i>`;
                }
                starsEl.innerHTML = starsHtml;
            }

            modal.classList.remove('hidden');
            lockBodyScroll();
        }

        function closeSingleReviewModal() {
            const modal = document.getElementById('single-review-modal');
            if (modal) modal.classList.add('hidden');
            unlockBodyScroll();

            if (window.packageReviewsSwiper && window.packageReviewsSwiper.autoplay) {
                window.packageReviewsSwiper.autoplay.start();
            }
        }

        // Review Modal Controls & Interactive Rating
        function openWriteReviewModal() {
            const modal = document.getElementById('write-review-modal');
            if (modal) {
                modal.classList.remove('hidden');
                lockBodyScroll();
            }
        }

        function closeWriteReviewModal() {
            const modal = document.getElementById('write-review-modal');
            if (modal) {
                modal.classList.add('hidden');
                unlockBodyScroll();
            }
            const alertBox = document.getElementById('review-form-alert');
            if (alertBox) {
                alertBox.className = 'hidden';
                alertBox.innerHTML = '';
            }
        }

        // Prevent background rubber-banding on touch devices for all modal overlays
        ['write-review-modal', 'single-review-modal', 'full-gallery-modal'].forEach(function(modalId) {
            const m = document.getElementById(modalId);
            if (m) {
                m.addEventListener('touchmove', function(e) {
                    if (e.target === m) {
                        e.preventDefault();
                    }
                }, { passive: false });
            }
        });

        function selectStarRating(val) {
            const ratingInput = document.getElementById('selected-rating-input');
            const ratingText = document.getElementById('star-rating-text');
            const stars = document.querySelectorAll('#star-rating-selector i');
            if (ratingInput) ratingInput.value = val;

            const ratingDescriptions = {
                1: '1 Star (Needs Improvement)',
                2: '2 Stars (Fair Experience)',
                3: '3 Stars (Good Experience)',
                4: '4 Stars (Very Good!)',
                5: '5 Stars (Exceptional!)'
            };

            if (ratingText) {
                ratingText.textContent = ratingDescriptions[val] || `${val} Stars`;
                ratingText.className = val >= 4 ? 'text-xs font-bold text-emerald-600' : 'text-xs font-bold text-amber-600';
            }

            stars.forEach(s => {
                const starVal = parseInt(s.getAttribute('data-val') || 0);
                if (starVal <= val) {
                    s.className = 'fa-solid fa-star text-amber-400 cursor-pointer transition-colors';
                } else {
                    s.className = 'fa-solid fa-star text-gray-300 cursor-pointer transition-colors';
                }
            });
        }

        function submitCustomerReview(e) {
            e.preventDefault();
            const form = document.getElementById('write-review-form');
            const btn = document.getElementById('submit-review-btn');
            const alertBox = document.getElementById('review-form-alert');
            if (!form || !btn) return;

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Submitting...';

            const formData = new FormData(form);

            fetch('{{ route("reviews.store") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                btn.disabled = false;
                btn.innerHTML = '<span>Submit Review</span>';

                if (!res.ok && (res.status === 401 || data.require_login)) {
                    alertBox.className = 'p-3 rounded-xl text-xs font-medium bg-amber-50 text-amber-900 border border-amber-200 block';
                    alertBox.innerHTML = '<i class="fa-solid fa-lock text-amber-600 mr-1.5"></i> ' + (data.message || 'Please log in to submit a review.') + ' <a href="{{ route("login") }}?redirect=' + encodeURIComponent(window.location.href) + '" class="font-bold underline ml-1">Log in here</a>';
                    return;
                }

                if (data.success) {
                    alertBox.className = 'p-3 rounded-xl text-xs font-medium bg-emerald-50 text-emerald-800 border border-emerald-200 block';
                    alertBox.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-600 mr-1.5"></i> ' + data.message;

                    const rev = data.review;
                    const starIcons = Array.from({length: 5}, (_, i) => 
                        `<i class="fa-solid fa-star ${i < rev.rating ? 'text-amber-400' : 'text-gray-300'}"></i>`
                    ).join('');

                    window.packageReviewsList = window.packageReviewsList || [];
                    window.packageReviewsList.unshift({
                        name: rev.author,
                        location: rev.location || 'Indore, MP',
                        date: rev.created_at || 'Just now',
                        rating: rev.rating,
                        text: rev.review,
                        initial: (rev.author || 'U').charAt(0).toUpperCase(),
                        avatar_bg: 'bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10'
                    });

                    const newCardHtml = `
                        <div class="swiper-slide h-auto">
                            <div onclick="openSingleReviewModal(0)"
                                 class="h-full border border-[#E8E5DF] dark:border-white/10 rounded-2xl p-4 sm:p-5 bg-white dark:bg-[#151518] hover:border-gray-400 dark:hover:border-white/30 transition-all flex flex-col justify-between shadow-2xs text-left cursor-pointer group hover:shadow-md"
                                 role="button"
                                 tabindex="0">
                                <div>
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10 flex items-center justify-center font-bold text-xs shrink-0">
                                                ${(rev.author || 'U').charAt(0).toUpperCase()}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-heading font-bold text-xs text-[#171717] dark:text-white truncate">${rev.author}</p>
                                                <p class="text-[10px] text-[#777777] dark:text-gray-400 truncate">${rev.location} · ${rev.created_at || 'Just now'}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center text-amber-400 text-[10px] shrink-0">
                                            ${starIcons}
                                        </div>
                                    </div>
                                    <p class="text-xs text-[#555555] dark:text-gray-300 leading-relaxed line-clamp-4">
                                        ${(rev.review && rev.review.length > 115) 
                                            ? rev.review.substring(0, 110) + '... <span class="font-bold text-gray-900 dark:text-[#FFD600] group-hover:underline">read more</span>' 
                                            : (rev.review || '')}
                                    </p>
                                </div>
                            </div>
                        </div>
                    `;

                    const grid = document.getElementById('reviews-cards-grid');
                    if (grid) {
                        if (grid.children.length === 1 && grid.querySelector('.border-dashed')) {
                            grid.innerHTML = '';
                        }
                        grid.insertAdjacentHTML('afterbegin', newCardHtml);
                        initPackageReviewsSwiper();
                    }

                    document.querySelectorAll('.header-review-count').forEach(el => {
                        const cur = parseInt(el.textContent) || 0;
                        el.textContent = cur + 1;
                    });
                    const dispCount = document.getElementById('display-review-count');
                    if (dispCount) {
                        dispCount.textContent = (parseInt(dispCount.textContent) || 0) + 1;
                    }

                    setTimeout(() => {
                        form.reset();
                        selectStarRating(5);
                        closeWriteReviewModal();
                    }, 1400);

                } else {
                    alertBox.className = 'p-3 rounded-xl text-xs font-medium bg-red-50 text-red-800 border border-red-200 block';
                    alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-red-600 mr-1.5"></i> ' + (data.message || 'Please check your inputs and try again.');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<span>Submit Review</span>';
                alertBox.className = 'p-3 rounded-xl text-xs font-medium bg-red-50 text-red-800 border border-red-200 block';
                alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-red-600 mr-1.5"></i> Submission failed. Please try again.';
            });
        }

        // Modal backdrop and escape key event listeners
        document.addEventListener('click', function(e) {
            const singleModal = document.getElementById('single-review-modal');
            if (singleModal && e.target === singleModal) {
                closeSingleReviewModal();
            }
            const writeModal = document.getElementById('write-review-modal');
            if (writeModal && e.target === writeModal) {
                closeWriteReviewModal();
            }
            const galleryModal = document.getElementById('full-gallery-modal');
            if (galleryModal && e.target === galleryModal) {
                closeFullGalleryModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSingleReviewModal();
                closeWriteReviewModal();
                closeFullGalleryModal();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            initPackageReviewsSwiper();

            if (typeof Swiper !== 'undefined') {
                new Swiper('.explore-other-events-swiper', {
                    slidesPerView: 1.15,
                    spaceBetween: 16,
                    grabCursor: true,
                    loop: false,
                    navigation: {
                        nextEl: '.explore-next-btn',
                        prevEl: '.explore-prev-btn',
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 2.2,
                            spaceBetween: 18,
                        },
                        1024: {
                            slidesPerView: 3.2,
                            spaceBetween: 20,
                        },
                        1280: {
                            slidesPerView: 4,
                            spaceBetween: 24,
                        }
                    }
                });
            }
        });
    </script>
    <script src="{{ asset('js/details.js') }}"></script>

@endsection
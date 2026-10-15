@extends('layouts.app')

@section('content')

    <script>
        window.currentMatchedId = @json($eventId ?? 1);
        window.currentMatchedTierIdx = @json($selectedTierIdx ?? 0);
        if (document.body) {
            document.body.classList.add('hospitality-page');
        } else {
            document.addEventListener('DOMContentLoaded', function() {
                document.body.classList.add('hospitality-page');
            });
        }
    </script>

    <!-- Clean Hospitality Theme Container for Details Page -->
    <div class="package-detail-page min-h-screen bg-white dark:bg-[#0C0C0E] text-[#171717] font-body selection:bg-neutral-800 selection:text-white pb-12 md:pb-16 pt-4 md:pt-6">
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

            <!-- 2. PACKAGE HERO (Left 8-Column Main Area + Right 4-Column Sticky Summary) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-10">

                <!-- Left 8 Columns Area: Gallery, Package Info & Dedicated About Section -->
                <div class="lg:col-span-8 flex flex-col gap-6 w-full">

                    <!-- Top Row: Gallery (5 cols) & Package Info (4 cols) -->
                    <div class="grid grid-cols-1 md:grid-cols-9 gap-6 items-start">
                        <!-- Left Column (5 cols): Gallery & Thumbnails with Swiper.js -->
                        <div class="md:col-span-5 flex flex-col-reverse sm:flex-row gap-3.5 w-full items-stretch">
                            <!-- Thumbnails column (Clean without next/previous arrow buttons) -->
                            <div class="flex flex-row sm:flex-col gap-2 shrink-0 justify-start items-center">
                                <div id="detail-thumbnails-container"
                                    class="flex flex-row sm:flex-col gap-2 overflow-x-auto sm:overflow-y-auto max-w-[280px] sm:max-w-none sm:max-h-[420px] scrollbar-hide select-none scroll-smooth py-1">
                                    <!-- Populated dynamically via JS -->
                                </div>
                            </div>

                            <!-- Showcase Box with Swiper.js Carousel (Drag left-to-right & right-to-left) -->
                            <div class="flex-grow aspect-square overflow-hidden bg-white border border-[#E8E5DF] rounded-2xl shadow-sm relative group select-none">
                                <div class="swiper detail-main-swiper w-full h-full cursor-grab active:cursor-grabbing">
                                    <div class="swiper-wrapper" id="detail-swiper-wrapper">
                                        <div class="swiper-slide w-full h-full flex items-center justify-center bg-gray-50">
                                            <img id="detail-image" src="{{ $event['tiers'][$selectedTierIdx]['image'] ?? ($event['image'] ?? asset('assets/images/hero/1.jpg')) }}" alt="Main Package Image" class="w-full h-full object-cover select-none pointer-events-none">
                                        </div>
                                    </div>
                                    <!-- Dynamic Pagination Bullets -->
                                    <div class="swiper-pagination detail-swiper-pagination !bottom-3 !left-1/2 !-translate-x-1/2 flex items-center justify-center gap-1.5 z-10"></div>
                                </div>

                                <!-- Expand / View Full Gallery Button -->
                                <button onclick="openFullGalleryModal()" aria-label="Expand image"
                                    class="absolute top-3.5 right-3.5 w-8 h-8 rounded-full bg-white/95 hover:bg-white text-[#171719] shadow-sm flex items-center justify-center border border-[#E8E5DF] transition-all cursor-pointer z-20">
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
                                        class="text-[10px] font-heading font-extrabold text-neutral-800 dark:text-neutral-200 bg-neutral-100 dark:bg-white/10 border border-neutral-300 dark:border-white/10 px-2.5 py-1 rounded-md uppercase tracking-wider leading-none">
                                        {{ $event['tiers'][$selectedTierIdx]['badge'] ?? 'LUXURY PACKAGE' }}
                                    </span>
                                </div>

                                <!-- Package Title & Pricing -->
                                <div>
                                    <h1 id="detail-title"
                                        class="font-heading font-extrabold text-2xl sm:text-3xl text-main-text uppercase tracking-tight leading-tight">
                                        {{ $event['title'] ?? ($event['tiers'][$selectedTierIdx]['name'] ?? 'Package') }}
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
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-[10px] font-heading font-extrabold text-neutral-900 dark:text-white uppercase tracking-wider">KEY INCLUSIONS</h4>
                                    <span class="text-[10px] text-[#777777] dark:text-gray-400 font-medium">Included in setup</span>
                                </div>
                                <ul id="meta-inclusions-list" class="grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-1.5 list-none pl-0">
                                    @foreach(array_slice($event['tiers'][$selectedTierIdx]['inclusions'] ?? [], 0, 6) as $inc)
                                        <li class="flex items-start gap-2 py-1 text-xs text-[#222222] dark:text-[#E4E4E7] font-medium font-body leading-snug">
                                            <i class="fa-solid fa-check text-neutral-800 dark:text-neutral-200 text-xs shrink-0 mt-0.5" aria-hidden="true"></i>
                                            <span class="line-clamp-2">{{ $inc }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Setup Highlights & Perks -->
                            <div class="grid grid-cols-2 gap-2 pt-2.5 border-t border-[#E8E5DF] dark:border-white/10">
                                <div class="flex items-center gap-2 p-1.5 rounded-lg bg-[#FAF9F5] dark:bg-white/[0.03] border border-[#EFECE6] dark:border-white/10">
                                    <div class="w-5 h-5 rounded-md bg-neutral-100 dark:bg-white/10 text-neutral-700 dark:text-neutral-300 border border-neutral-200 dark:border-white/10 flex items-center justify-center shrink-0 text-[10px]">
                                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                                    </div>
                                    <span class="text-[11px] font-semibold text-main-text leading-tight">Customizable Themes</span>
                                </div>
                                <div class="flex items-center gap-2 p-1.5 rounded-lg bg-[#FAF9F5] dark:bg-white/[0.03] border border-[#EFECE6] dark:border-white/10">
                                    <div class="w-5 h-5 rounded-md bg-neutral-100 dark:bg-white/10 text-neutral-700 dark:text-neutral-300 border border-neutral-200 dark:border-white/10 flex items-center justify-center shrink-0 text-[10px]">
                                        <i class="fa-solid fa-user-shield"></i>
                                    </div>
                                    <span class="text-[11px] font-semibold text-main-text leading-tight">Verified Team & Crew</span>
                                </div>
                                <div class="flex items-center gap-2 p-1.5 rounded-lg bg-[#FAF9F5] dark:bg-white/[0.03] border border-[#EFECE6] dark:border-white/10">
                                    <div class="w-5 h-5 rounded-md bg-neutral-100 dark:bg-white/10 text-neutral-700 dark:text-neutral-300 border border-neutral-200 dark:border-white/10 flex items-center justify-center shrink-0 text-[10px]">
                                        <i class="fa-regular fa-calendar-check"></i>
                                    </div>
                                    <span class="text-[11px] font-semibold text-main-text leading-tight">Flexible Timings</span>
                                </div>
                                <div class="flex items-center gap-2 p-1.5 rounded-lg bg-[#FAF9F5] dark:bg-white/[0.03] border border-[#EFECE6] dark:border-white/10">
                                    <div class="w-5 h-5 rounded-md bg-neutral-100 dark:bg-white/10 text-neutral-700 dark:text-neutral-300 border border-neutral-200 dark:border-white/10 flex items-center justify-center shrink-0 text-[10px]">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <span class="text-[11px] font-semibold text-main-text leading-tight">All Indore Locations</span>
                                </div>
                            </div>

                            <!-- 3 Simple Facts (In Elegant Fact Card) -->
                            <div class="grid grid-cols-3 gap-2 bg-[#FAF9F5] dark:bg-white/[0.04] border border-[#E8E5DF] dark:border-white/10 rounded-xl p-3">
                                <!-- Setup Time -->
                                <div class="flex items-center gap-2">
                                    <i class="fa-regular fa-clock text-neutral-700 dark:text-neutral-300 text-base shrink-0" aria-hidden="true"></i>
                                    <div class="leading-none text-left">
                                        <span id="detail-duration" class="font-heading text-xs font-bold text-main-text block">3-4 Hours</span>
                                        <span class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium block mt-1">Setup Time</span>
                                    </div>
                                </div>

                                <!-- Ideal For -->
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-users text-neutral-700 dark:text-neutral-300 text-base shrink-0" aria-hidden="true"></i>
                                    <div class="leading-none text-left">
                                        <span id="detail-ideal-for" class="font-heading text-xs font-bold text-main-text block">100+ Guests</span>
                                        <span class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium block mt-1">Ideal For</span>
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-location-dot text-neutral-700 dark:text-neutral-300 text-base shrink-0" aria-hidden="true"></i>
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
                                <i class="fa-solid fa-circle-info text-neutral-700 dark:text-neutral-300"></i> About This Package
                            </h3>
                            <p id="detail-about-desc" class="font-body text-xs sm:text-[13px] text-[#555555] dark:text-[#A1A1AA] leading-relaxed">
                                {{ $event['tiers'][$selectedTierIdx]['desc'] ?? ($event['desc'] ?? 'Experience a flawless celebration with our premium setup. This curated package comes fully equipped with complete decor, professional sound, ambient illumination, and on-site coordination. Our verified team manages the entire setup and logistics, ensuring every detail is executed to perfection.') }}
                            </p>
                        </div>

                        <!-- 4 Compact Factual Highlights -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
                            <!-- Fact 1 -->
                            <div class="border border-[#E8E5DF] dark:border-white/10 rounded-xl p-3 flex items-center gap-3 bg-[#FAF9F5] dark:bg-white/[0.04]">
                                <div class="w-8 h-8 rounded-lg bg-neutral-100 dark:bg-white/10 text-neutral-700 dark:text-neutral-300 border border-neutral-200 dark:border-white/10 flex items-center justify-center shrink-0 text-xs">
                                    <i class="fa-solid fa-tags"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-heading font-bold text-xs text-main-text truncate">Affordable Price</h4>
                                    <p class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium truncate">Best value for money</p>
                                </div>
                            </div>

                            <!-- Fact 2 -->
                            <div class="border border-[#E8E5DF] dark:border-white/10 rounded-xl p-3 flex items-center gap-3 bg-[#FAF9F5] dark:bg-white/[0.04]">
                                <div class="w-8 h-8 rounded-lg bg-neutral-100 dark:bg-white/10 text-neutral-700 dark:text-neutral-300 border border-neutral-200 dark:border-white/10 flex items-center justify-center shrink-0 text-xs">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-heading font-bold text-xs text-main-text truncate">Hassle Free</h4>
                                    <p class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium truncate">We handle everything</p>
                                </div>
                            </div>

                            <!-- Fact 3 -->
                            <div class="border border-[#E8E5DF] dark:border-white/10 rounded-xl p-3 flex items-center gap-3 bg-[#FAF9F5] dark:bg-white/[0.04]">
                                <div class="w-8 h-8 rounded-lg bg-neutral-100 dark:bg-white/10 text-neutral-700 dark:text-neutral-300 border border-neutral-200 dark:border-white/10 flex items-center justify-center shrink-0 text-xs">
                                    <i class="fa-solid fa-user-check"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-heading font-bold text-xs text-main-text truncate">Professional Team</h4>
                                    <p class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium truncate">Experienced & verified</p>
                                </div>
                            </div>

                            <!-- Fact 4 -->
                            <div class="border border-[#E8E5DF] dark:border-white/10 rounded-xl p-3 flex items-center gap-3 bg-[#FAF9F5] dark:bg-white/[0.04]">
                                <div class="w-8 h-8 rounded-lg bg-neutral-100 dark:bg-white/10 text-neutral-700 dark:text-neutral-300 border border-neutral-200 dark:border-white/10 flex items-center justify-center shrink-0 text-xs">
                                    <i class="fa-solid fa-truck-fast"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-heading font-bold text-xs text-main-text truncate">On-Time Delivery</h4>
                                    <p class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium truncate">Always on schedule</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column (Col span 4 on desktop): Sticky Booking Summary Card -->
                <div class="lg:col-span-4 lg:sticky lg:top-24 flex flex-col gap-3">
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
                        <div class="space-y-2.5 text-xs text-[#666666] dark:text-[#A1A1AA] font-medium border-b border-[#E8E5DF] dark:border-white/10 pb-3">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[#777777] dark:text-[#A1A1AA] whitespace-nowrap shrink-0">Service Location:</span>
                                <span class="font-bold text-main-text text-right">Indore, MP</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[#777777] dark:text-[#A1A1AA] whitespace-nowrap shrink-0">Setup &amp; Logistics:</span>
                                <span class="font-bold text-neutral-800 dark:text-neutral-200 text-right">Included</span>
                            </div>
                            <div class="flex items-baseline justify-between gap-3">
                                <span class="text-[#777777] dark:text-[#A1A1AA] whitespace-nowrap shrink-0">Payment Mode:</span>
                                <span class="font-bold text-main-text text-right leading-snug">Offline Post Confirmation</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-2 pt-0.5">
                            <!-- Primary CTA Button -->
                            <button onclick="bookPackageNow()"
                                class="w-full py-3.5 bg-[#18181B] hover:bg-black text-white text-xs font-heading font-extrabold uppercase tracking-widest rounded-xl shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fa-solid fa-calendar-check text-xs" aria-hidden="true"></i> Book This Package
                            </button>

                            <!-- WhatsApp Secondary CTA Button -->
                            <button onclick="enquireOnWhatsAppFromDetails()"
                                class="w-full py-2.5 bg-white hover:bg-gray-50 border border-[#E8E5DF] hover:border-gray-400 text-main-text text-xs font-heading font-bold uppercase tracking-wider rounded-xl flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fa-brands fa-whatsapp text-neutral-700 dark:text-neutral-300 text-sm" aria-hidden="true"></i> WhatsApp Inquiry
                            </button>
                        </div>

                        <p class="text-[10px] text-[#888888] dark:text-[#A1A1AA] text-center font-medium leading-relaxed">
                            Click <strong class="font-bold text-main-text">Book This Package</strong> to enter your event date, setup time & venue address on the booking checkout page.
                        </p>

                        <!-- Clean Factual Highlights with Premium Micro-Icons -->
                        <div class="grid grid-cols-2 gap-2 sm:gap-2.5 pt-3.5 border-t border-[#E8E5DF] dark:border-white/10 text-left">
                            <div class="flex items-center gap-2 sm:gap-2.5 p-2.5 rounded-xl bg-[#FAF9F5] dark:bg-white/[0.04] border border-[#EFECE6] dark:border-white/10 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-neutral-100 dark:bg-white/10 text-neutral-800 dark:text-neutral-200 border border-neutral-200 dark:border-white/10 flex items-center justify-center shrink-0 text-xs">
                                    <i class="fa-solid fa-bolt-lightning"></i>
                                </div>
                                <div class="min-w-0 flex-1 leading-none">
                                    <div class="text-[11px] font-bold text-main-text leading-tight truncate">Instant Confirm</div>
                                    <div class="text-[10px] text-[#777777] dark:text-[#A1A1AA] mt-0.5 leading-tight truncate">Online request</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 sm:gap-2.5 p-2.5 rounded-xl bg-[#FAF9F5] dark:bg-white/[0.04] border border-[#EFECE6] dark:border-white/10 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-neutral-100 dark:bg-white/10 text-neutral-800 dark:text-neutral-200 border border-neutral-200 dark:border-white/10 flex items-center justify-center shrink-0 text-xs">
                                    <i class="fa-solid fa-tag"></i>
                                </div>
                                <div class="min-w-0 flex-1 leading-none">
                                    <div class="text-[11px] font-bold text-main-text leading-tight truncate">Best Price</div>
                                    <div class="text-[10px] text-[#777777] dark:text-[#A1A1AA] mt-0.5 leading-tight truncate">Fixed transparent</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 sm:gap-2.5 p-2.5 rounded-xl bg-[#FAF9F5] dark:bg-white/[0.04] border border-[#EFECE6] dark:border-white/10 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-neutral-100 dark:bg-white/10 text-neutral-800 dark:text-neutral-200 border border-neutral-200 dark:border-white/10 flex items-center justify-center shrink-0 text-xs">
                                    <i class="fa-solid fa-sliders"></i>
                                </div>
                                <div class="min-w-0 flex-1 leading-none">
                                    <div class="text-[11px] font-bold text-main-text leading-tight truncate">Free Custom</div>
                                    <div class="text-[10px] text-[#777777] dark:text-[#A1A1AA] mt-0.5 leading-tight truncate">Venue tailored</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 sm:gap-2.5 p-2.5 rounded-xl bg-[#FAF9F5] dark:bg-white/[0.04] border border-[#EFECE6] dark:border-white/10 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-neutral-100 dark:bg-white/10 text-neutral-800 dark:text-neutral-200 border border-neutral-200 dark:border-white/10 flex items-center justify-center shrink-0 text-xs">
                                    <i class="fa-solid fa-headset"></i>
                                </div>
                                <div class="min-w-0 flex-1 leading-none">
                                    <div class="text-[11px] font-bold text-main-text leading-tight truncate">24×7 Support</div>
                                    <div class="text-[10px] text-[#777777] dark:text-[#A1A1AA] mt-0.5 leading-tight truncate">Event coordinator</div>
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
                    <div class="border border-neutral-200 dark:border-white/10 bg-neutral-50 dark:bg-white/[0.03] p-3.5 rounded-lg flex items-start gap-2.5 mt-auto text-left">
                        <i class="fa-solid fa-circle-info text-neutral-600 dark:text-neutral-400 text-sm shrink-0 mt-0.5" aria-hidden="true"></i>
                        <p class="text-xs text-neutral-700 dark:text-neutral-300 font-medium leading-normal">
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
                $userReview = $reviewsInfo['user_review'] ?? null;
                $hasUserReviewed = !empty($userReview);
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
                        <button type="button" onclick="openWriteReviewModal()" id="header-review-btn"
                            class="px-4 py-2 bg-[#18181B] hover:bg-black text-white text-xs font-heading font-extrabold uppercase tracking-wider rounded-xl inline-flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-pen-to-square text-xs" aria-hidden="true"></i> 
                            <span id="header-review-btn-text">{{ $hasUserReviewed ? 'Edit Your Review' : 'Write a Review' }}</span>
                        </button>
                        <a href="{{ route('reviews.index', ['package' => $event['slug'] ?? ($slug ?? ''), 'title' => $event['title'] ?? '']) }}"
                            class="text-xs font-heading font-bold text-gray-800 dark:text-gray-200 hover:text-black dark:hover:text-white border border-[#E8DFC8] dark:border-white/20 hover:border-gray-400 px-4 py-2 rounded-xl inline-flex items-center cursor-pointer">
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
                            <span class="text-[11px] bg-neutral-100 dark:bg-white/10 text-neutral-800 dark:text-neutral-200 border border-neutral-200 dark:border-white/10 px-3 py-1 rounded-full font-bold">
                                {{ $recommendPercent }}% Recommend
                            </span>
                        </div>
                        <button type="button" onclick="openWriteReviewModal()" id="card-review-btn"
                            class="w-full py-3 px-4 bg-[#18181B] hover:bg-black text-white text-xs font-heading font-extrabold uppercase tracking-wider rounded-xl flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                            <span id="card-review-btn-text">{{ $hasUserReviewed ? 'Edit Your Review' : 'Write a Review' }}</span>
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
                                             class="h-full border border-[#E8E5DF] dark:border-white/10 rounded-2xl p-4 sm:p-5 bg-white dark:bg-[#151518] flex flex-col justify-between text-left cursor-pointer"
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
                                                        {{ \Illuminate\Support\Str::limit($rev['text'], 110, '') }}... <span class="font-bold text-gray-900 dark:text-white underline">read more</span>
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
                                    <span class="text-[9.5px] sm:text-[10px] font-heading font-extrabold uppercase tracking-wider text-neutral-600 dark:text-neutral-400 block mb-0.5 truncate">
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
                                        <span class="text-[10.5px] font-bold text-neutral-800 dark:text-neutral-200">
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
                /* Disable card hover scaling, card effect animations, and image hover zooms without breaking Swiper transforms */
                .package-detail-page a:not(.swiper-slide):hover,
                .package-detail-page button:not(.swiper-pagination-bullet):hover,
                .package-detail-page [class*="rounded-"]:not(.swiper):not(.swiper-wrapper):not(.swiper-slide):not(.swiper-pagination-bullet):hover {
                    transform: none !important;
                    -webkit-transform: none !important;
                    animation: none !important;
                    -webkit-animation: none !important;
                }
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
                .detail-swiper-pagination .swiper-pagination-bullet {
                    background: #171719;
                    opacity: 0.35;
                    width: 6px;
                    height: 6px;
                }
                .detail-swiper-pagination .swiper-pagination-bullet-active {
                    background: #18181B !important;
                    opacity: 1;
                    width: 18px;
                    border-radius: 9999px;
                }
                body.overflow-hidden,
                html.overflow-hidden {
                    overflow: hidden !important;
                    height: 100% !important;
                    touch-action: none;
                    -webkit-overflow-scrolling: auto;
                }
                /* Hide WhatsApp floating button when any modal is open / body scroll locked */
                body.overflow-hidden #whatsapp-floating-widget,
                html.overflow-hidden #whatsapp-floating-widget,
                body.overflow-hidden #wa-chat-bubble,
                #full-gallery-modal:not(.hidden) ~ #whatsapp-floating-widget,
                body:has(#full-gallery-modal:not(.hidden)) #whatsapp-floating-widget {
                    display: none !important;
                    visibility: hidden !important;
                    opacity: 0 !important;
                    pointer-events: none !important;
                }
                /* Swiper Lightbox Styles */
                .modal-lightbox-swiper {
                    width: 100%;
                    height: 100%;
                    max-height: 90vh;
                }
                .modal-lightbox-swiper .swiper-slide {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 100%;
                    height: 100%;
                }
                /* Ensure footer bottom is completely visible above sticky CTA bar on mobile */
                @media (max-width: 767px) {
                    footer {
                        padding-bottom: calc(7.5rem + env(safe-area-inset-bottom, 0px)) !important;
                    }
                }
            </style>

        </div>
    </div>

    <!-- Mobile Floating Sticky CTA Bar (Visible on Mobile < 768px) -->
    <div class="package-detail-sticky-bar md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-[#141416]/95 backdrop-blur-md border-t border-[#E8E5DF] dark:border-white/10 p-3.5 flex items-center justify-between gap-3 shadow-sm">
        <div class="flex flex-col text-left">
            <span class="text-[9px] font-bold text-[#777777] dark:text-gray-400 uppercase tracking-wider">Total Package Price</span>
            <span id="mobile-sticky-price" class="text-base font-extrabold text-main-text">₹{{ number_format($event['tiers'][$selectedTierIdx]['price'] ?? 6000) }}</span>
        </div>
        <button onclick="bookPackageNow()"
            class="px-5 py-2.5 bg-[#18181B] hover:bg-black text-white text-xs font-extrabold uppercase tracking-wider rounded-lg shadow-sm flex items-center gap-1.5 cursor-pointer">
            <i class="fa-solid fa-calendar-check"></i> Book Package
        </button>
    </div>

    <!-- Fullscreen Clean Image Lightbox Modal with Swiper.js (Drag/Swipe right to left & left to right) -->
    <div id="full-gallery-modal" class="fixed inset-0 z-[100] bg-black/92 backdrop-blur-md hidden flex items-center justify-center select-none transition-all duration-300">
        <!-- Backdrop click listener to close (clicking outside image) -->
        <div class="absolute inset-0 cursor-zoom-out" onclick="closeFullGalleryModal()" aria-label="Close modal backdrop"></div>

        <!-- Top Right Close Button -->
        <button type="button" onclick="closeFullGalleryModal()" 
            class="absolute top-4 right-4 sm:top-6 sm:right-6 w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-white/15 hover:bg-white text-white hover:text-[#171719] border border-white/20 shadow-2xl flex items-center justify-center transition-all duration-200 cursor-pointer z-[110] focus:outline-none focus:ring-2 focus:ring-white" 
            aria-label="Close image modal">
            <i class="fa-solid fa-xmark text-xl sm:text-2xl" aria-hidden="true"></i>
        </button>

        <!-- Swiper Container for Lightbox: Drag and Swipe Left to Right and Right to Left -->
        <div class="swiper modal-lightbox-swiper w-full h-full max-h-[92vh] max-w-[96vw] sm:max-w-[90vw] relative z-[105] cursor-grab active:cursor-grabbing">
            <div class="swiper-wrapper h-full items-center" id="modal-swiper-wrapper">
                <!-- Dynamically populated with slides -->
            </div>
        </div>

        <!-- Image Counter indicator at bottom (e.g. 1 / 5) -->
        <div id="lightbox-counter" class="absolute bottom-4 sm:bottom-6 left-1/2 -translate-x-1/2 px-3.5 py-1 rounded-full bg-black/60 border border-white/15 text-white/90 text-xs font-semibold tracking-wider z-[110] pointer-events-none">
            <span id="lightbox-current-idx">1</span> / <span id="lightbox-total-count">5</span>
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
                <!-- Logged In: Clean Review Form (Supports Write & Edit Review) -->
                <div class="pb-3 mb-3 border-b border-gray-100 dark:border-white/10 pr-8">
                    <h3 id="review-modal-title" class="font-heading font-extrabold text-base text-[#171717] dark:text-white">
                        {{ $hasUserReviewed ? 'Edit Your Review' : 'Write a Review' }}
                    </h3>
                    <p id="review-modal-subtitle" class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                        {{ $hasUserReviewed ? 'Updating review as' : 'Posting as' }} <span class="font-semibold text-gray-900 dark:text-white">{{ $reviewAuthName }}</span>
                    </p>
                </div>

                <form id="write-review-form" onsubmit="submitCustomerReview(event)" class="flex flex-col gap-4">
                    @csrf
                    <input type="hidden" name="review_id" id="review-id-input" value="{{ $userReview['id'] ?? '' }}">
                    <input type="hidden" name="package_slug" value="{{ $event['slug'] ?? ($slug ?? '') }}">
                    <input type="hidden" name="event_title" value="{{ $event['title'] ?? 'Celebration Setup' }}">

                    <!-- Interactive Star Rating -->
                    <div>
                        <label class="block text-xs font-heading font-bold text-gray-900 dark:text-white mb-1.5">Rating *</label>
                        <div class="flex items-center gap-2.5">
                            <div id="star-rating-selector" class="flex items-center gap-1.5 text-2xl text-gray-300">
                                @php
                                    $userRatingVal = (int)($userReview['rating'] ?? 5);
                                @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= $userRatingVal ? 'text-amber-400' : 'text-gray-300' }} cursor-pointer transition-colors" data-val="{{ $i }}" onclick="selectStarRating({{ $i }})"></i>
                                @endfor
                            </div>
                            <span id="star-rating-text" class="text-xs font-bold text-neutral-800 dark:text-neutral-200">
                                @if($userRatingVal == 5) 5 Stars (Exceptional!)
                                @elseif($userRatingVal == 4) 4 Stars (Very Good!)
                                @elseif($userRatingVal == 3) 3 Stars (Good Experience)
                                @elseif($userRatingVal == 2) 2 Stars (Fair Experience)
                                @else 1 Star (Needs Improvement)
                                @endif
                            </span>
                        </div>
                        <input type="hidden" id="selected-rating-input" name="rating" value="{{ $userRatingVal }}">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-heading font-bold text-gray-900 dark:text-white mb-1">Your Review *</label>
                        <textarea name="review" id="review-input-text" required rows="4" placeholder="How was your celebration experience? (punctuality, decor quality, team coordination)..."
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-300 dark:border-white/15 bg-white dark:bg-[#1D1D22] text-[#171719] dark:text-white placeholder-gray-400 focus:border-black dark:focus:border-white focus:ring-0 outline-none">{{ $userReview['review'] ?? ($userReview['text'] ?? '') }}</textarea>
                    </div>

                    <div id="review-form-alert" class="hidden p-3 rounded-xl text-xs font-medium"></div>

                    <!-- Buttons -->
                    <div class="pt-2 border-t border-gray-100 dark:border-white/10 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeWriteReviewModal()"
                            class="px-4 py-2.5 rounded-xl border border-gray-300 dark:border-white/20 text-xs font-heading font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" id="submit-review-btn"
                            class="px-5 py-2.5 bg-[#18181B] hover:bg-black text-white text-xs font-heading font-extrabold uppercase tracking-wider rounded-xl shadow-sm flex items-center gap-1.5 cursor-pointer">
                            <span id="submit-review-btn-text">{{ $hasUserReviewed ? 'Update Review' : 'Submit Review' }}</span>
                        </button>
                    </div>
                </form>
            @else
                <!-- Logged Out: Clean, Minimal, Premium Login Modal (No unwanted text, perfectly formatted buttons) -->
                <div class="pt-3 pb-2 text-center flex flex-col items-center justify-center">
                    <div class="w-12 h-12 rounded-2xl bg-neutral-100 dark:bg-white/10 text-neutral-700 dark:text-neutral-300 flex items-center justify-center mb-3 border border-neutral-200 dark:border-white/10">
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
                            class="flex-1 w-full py-3 px-4 bg-[#18181B] hover:bg-black text-white text-xs font-heading font-extrabold uppercase tracking-wider rounded-xl shadow-sm text-center flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer">
                            <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                            <span>Log In</span>
                        </a>
                        <a href="{{ route('register', ['redirect' => url()->current()]) }}"
                            class="flex-1 w-full py-3 px-4 border border-gray-300 dark:border-white/20 hover:bg-gray-50 dark:hover:bg-white/5 text-gray-800 dark:text-gray-200 text-xs font-heading font-bold uppercase tracking-wider rounded-xl text-center flex items-center justify-center whitespace-nowrap cursor-pointer">
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
        window.currentEventData = @json($event ?? null);
        window.packageReviewsList = @json($packageReviews ?? []);
        window.userReviewData = @json($userReview ?? null);
        window.hasUserReviewed = {{ $hasUserReviewed ? 'true' : 'false' }};

        function lockBodyScroll() {
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
            document.body.classList.add('overflow-hidden');
            document.documentElement.classList.add('overflow-hidden');
            document.body.style.touchAction = 'none';

            // Strictly hide WhatsApp floating widget when modal is open
            const waWidget = document.getElementById('whatsapp-floating-widget');
            if (waWidget) {
                waWidget.style.setProperty('display', 'none', 'important');
                waWidget.style.setProperty('visibility', 'hidden', 'important');
                waWidget.style.setProperty('opacity', '0', 'important');
                waWidget.classList.add('!hidden');
            }
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
                document.body.style.touchAction = '';

                // Restore WhatsApp floating button
                const waWidget = document.getElementById('whatsapp-floating-widget');
                if (waWidget) {
                    waWidget.style.removeProperty('display');
                    waWidget.style.removeProperty('visibility');
                    waWidget.style.removeProperty('opacity');
                    waWidget.classList.remove('!hidden');
                }
            }
        }

        let currentLightboxIndex = 0;
        let currentLightboxImages = [];
        window.modalLightboxSwiper = null;

        function openFullGalleryModal(customTarget) {
            const modal = document.getElementById('full-gallery-modal');
            const wrapper = document.getElementById('modal-swiper-wrapper');
            if (!modal || !wrapper) return;

            // 1. Gather all active gallery images for current package & tier
            const db = window.eventDatabase || {};
            const data = (window.currentEventData && window.currentEventData.title) ? window.currentEventData : (db[window.currentMatchedId] || Object.values(db)[0] || {});
            const selectedTier = (data.tiers && data.tiers[window.currentMatchedTierIdx]) ? data.tiers[window.currentMatchedTierIdx] : (data.tiers ? data.tiers[0] : data);

            // Determine active image currently visible in Swiper or main showcase
            let activeSrc = '';
            if (window.detailMainSwiper && typeof window.detailMainSwiper.activeIndex === 'number') {
                const slides = document.querySelectorAll('.detail-main-swiper .swiper-slide img');
                const activeImg = slides[window.detailMainSwiper.activeIndex];
                if (activeImg) activeSrc = activeImg.src;
            }
            if (!activeSrc) {
                const detailImg = document.getElementById('detail-image');
                if (detailImg) activeSrc = detailImg.src;
            }

            // Prefer window.currentGalleryImages computed by details.js, or fallback to raw list
            const tierGallery = selectedTier.gallery || [];
            const rawImages = (window.currentGalleryImages && window.currentGalleryImages.length > 0)
                ? window.currentGalleryImages
                : [
                    activeSrc,
                    selectedTier.image,
                    ...(tierGallery.filter(Boolean)),
                    data.image,
                    ...(data.gallery || []),
                    ...((data.tiers || []).map(t => t.image))
                ].filter(Boolean);

            // Strictly deduplicate by normalized path so full URLs and relative paths never duplicate
            const seen = new Set();
            currentLightboxImages = [];
            rawImages.forEach(img => {
                const norm = (img || '').replace(/^https?:\/\/[^\/]+/, '');
                if (norm && !seen.has(norm)) {
                    seen.add(norm);
                    currentLightboxImages.push(img);
                }
            });

            if (currentLightboxImages.length === 0 && activeSrc) {
                currentLightboxImages = [activeSrc];
            }

            // Set current index based on click target or active swiper image
            if (typeof customTarget === 'number' && customTarget >= 0 && customTarget < currentLightboxImages.length) {
                currentLightboxIndex = customTarget;
            } else if (typeof customTarget === 'string' && customTarget) {
                const normTarget = customTarget.replace(/^https?:\/\/[^\/]+/, '');
                const foundIdx = currentLightboxImages.findIndex(src => src.replace(/^https?:\/\/[^\/]+/, '') === normTarget);
                currentLightboxIndex = foundIdx !== -1 ? foundIdx : 0;
            } else {
                const normActive = (activeSrc || '').replace(/^https?:\/\/[^\/]+/, '');
                const matchedIdx = currentLightboxImages.findIndex(src => src.replace(/^https?:\/\/[^\/]+/, '') === normActive);
                currentLightboxIndex = matchedIdx !== -1 ? matchedIdx : 0;
            }

            // Populate Swiper slides inside modal
            wrapper.innerHTML = '';
            currentLightboxImages.forEach((imgUrl, idx) => {
                const slide = document.createElement('div');
                slide.className = 'swiper-slide flex items-center justify-center w-full h-full p-2 sm:p-4 select-none cursor-zoom-out';
                slide.onclick = function(e) {
                    if (e.target === slide) {
                        closeFullGalleryModal();
                    }
                };

                const img = document.createElement('img');
                img.src = imgUrl;
                img.alt = `Package preview ${idx + 1}`;
                img.className = 'max-w-full max-h-[85vh] sm:max-h-[88vh] w-auto h-auto object-contain rounded-xl sm:rounded-2xl shadow-2xl pointer-events-auto border border-white/10 select-none cursor-default';
                img.draggable = false;
                img.onclick = function(e) {
                    e.stopPropagation();
                };

                slide.appendChild(img);
                wrapper.appendChild(slide);
            });

            // Update Counter
            const counterEl = document.getElementById('lightbox-counter');
            const currentIdxEl = document.getElementById('lightbox-current-idx');
            const totalCountEl = document.getElementById('lightbox-total-count');
            if (counterEl) {
                if (currentLightboxImages.length > 1) {
                    counterEl.classList.remove('hidden');
                    if (currentIdxEl) currentIdxEl.innerText = (currentLightboxIndex + 1);
                    if (totalCountEl) totalCountEl.innerText = currentLightboxImages.length;
                } else {
                    counterEl.classList.add('hidden');
                }
            }

            // Show Modal and Lock Scroll
            modal.classList.remove('hidden');
            lockBodyScroll();

            // Destroy existing instance if any
            if (window.modalLightboxSwiper && typeof window.modalLightboxSwiper.destroy === 'function') {
                try {
                    window.modalLightboxSwiper.destroy(true, true);
                } catch(err) {}
                window.modalLightboxSwiper = null;
            }

            // Initialize Swiper for modal with horizontal drag & swipe (right to left & left to right)
            if (typeof Swiper !== 'undefined') {
                setTimeout(() => {
                    window.modalLightboxSwiper = new Swiper('.modal-lightbox-swiper', {
                        slidesPerView: 1,
                        spaceBetween: 24,
                        centeredSlides: true,
                        grabCursor: true,
                        speed: 350,
                        resistanceRatio: 0.85,
                        initialSlide: currentLightboxIndex,
                        keyboard: {
                            enabled: true,
                            onlyInViewport: false,
                        },
                        on: {
                            slideChange: function() {
                                currentLightboxIndex = this.activeIndex;
                                if (currentIdxEl) {
                                    currentIdxEl.innerText = (currentLightboxIndex + 1);
                                }
                                if (window.detailMainSwiper && typeof window.detailMainSwiper.slideTo === 'function') {
                                    window.detailMainSwiper.slideTo(currentLightboxIndex);
                                }
                            }
                        }
                    });
                    if (window.modalLightboxSwiper) {
                        window.modalLightboxSwiper.update();
                        window.modalLightboxSwiper.slideTo(currentLightboxIndex, 0);
                    }
                }, 20);
            }
        }

        function closeFullGalleryModal(e) {
            if (e && typeof e.stopPropagation === 'function') {
                e.stopPropagation();
            }
            const modal = document.getElementById('full-gallery-modal');
            if (modal) modal.classList.add('hidden');

            if (window.modalLightboxSwiper && typeof window.modalLightboxSwiper.destroy === 'function') {
                try {
                    window.modalLightboxSwiper.destroy(true, true);
                } catch(err) {}
                window.modalLightboxSwiper = null;
            }

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
            if (!modal) return;

            // If user has an existing review, ensure inputs and ratings are populated
            if (window.userReviewData) {
                const idInput = document.getElementById('review-id-input');
                const textInput = document.getElementById('review-input-text');
                const modalTitle = document.getElementById('review-modal-title');
                const submitBtnText = document.getElementById('submit-review-btn-text');

                if (idInput && window.userReviewData.id) idInput.value = window.userReviewData.id;
                if (textInput && (window.userReviewData.review || window.userReviewData.text)) {
                    textInput.value = window.userReviewData.review || window.userReviewData.text;
                }
                if (window.userReviewData.rating) selectStarRating(window.userReviewData.rating);
                if (modalTitle) modalTitle.textContent = 'Edit Your Review';
                if (submitBtnText) submitBtnText.textContent = 'Update Review';
            }

            modal.classList.remove('hidden');
            lockBodyScroll();
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
                ratingText.className = 'text-xs font-bold text-neutral-800 dark:text-neutral-200';
            }

            stars.forEach(s => {
                const starVal = parseInt(s.getAttribute('data-val') || 0);
                if (starVal <= val) {
                    s.className = 'fa-solid fa-star text-amber-400 cursor-pointer';
                } else {
                    s.className = 'fa-solid fa-star text-gray-300 cursor-pointer';
                }
            });
        }

        function submitCustomerReview(e) {
            e.preventDefault();
            const form = document.getElementById('write-review-form');
            const btn = document.getElementById('submit-review-btn');
            const submitBtnText = document.getElementById('submit-review-btn-text');
            const alertBox = document.getElementById('review-form-alert');
            if (!form || !btn) return;

            const isEditMode = window.hasUserReviewed || !!document.getElementById('review-id-input')?.value;

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> ' + (isEditMode ? 'Updating...' : 'Submitting...');

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
                btn.innerHTML = '<span id="submit-review-btn-text">' + (isEditMode ? 'Update Review' : 'Submit Review') + '</span>';

                if (!res.ok && (res.status === 401 || data.require_login)) {
                    alertBox.className = 'p-3 rounded-xl text-xs font-medium bg-neutral-100 text-neutral-800 border border-neutral-300 block';
                    alertBox.innerHTML = '<i class="fa-solid fa-lock text-neutral-700 mr-1.5"></i> ' + (data.message || 'Please log in to submit a review.') + ' <a href="{{ route("login") }}?redirect=' + encodeURIComponent(window.location.href) + '" class="font-bold underline ml-1">Log in here</a>';
                    return;
                }

                if (data.success) {
                    alertBox.className = 'p-3 rounded-xl text-xs font-medium bg-neutral-100 text-neutral-800 border border-neutral-300 block';
                    alertBox.innerHTML = '<i class="fa-solid fa-check text-neutral-800 mr-1.5"></i> ' + data.message;

                    const rev = data.review;
                    const isEdit = data.is_edit || isEditMode;

                    // Update local state to reflect that user has reviewed
                    window.hasUserReviewed = true;
                    window.userReviewData = {
                        id: rev.id,
                        rating: rev.rating,
                        review: rev.review,
                        text: rev.review,
                        author: rev.author,
                        created_at: rev.created_at
                    };

                    // Update UI buttons & modal labels to "Edit Your Review"
                    const headerBtnText = document.getElementById('header-review-btn-text');
                    const cardBtnText = document.getElementById('card-review-btn-text');
                    const modalTitle = document.getElementById('review-modal-title');
                    const submitText = document.getElementById('submit-review-btn-text');
                    const reviewIdInput = document.getElementById('review-id-input');

                    if (headerBtnText) headerBtnText.textContent = 'Edit Your Review';
                    if (cardBtnText) cardBtnText.textContent = 'Edit Your Review';
                    if (modalTitle) modalTitle.textContent = 'Edit Your Review';
                    if (submitText) submitText.textContent = 'Update Review';
                    if (reviewIdInput) reviewIdInput.value = rev.id;

                    window.packageReviewsList = window.packageReviewsList || [];
                    
                    if (isEdit) {
                        // Find and update existing review in list
                        const existingIdx = window.packageReviewsList.findIndex(r => 
                            r.id === rev.id || r.author === rev.author || r.name === rev.author
                        );
                        if (existingIdx !== -1) {
                            window.packageReviewsList[existingIdx].rating = rev.rating;
                            window.packageReviewsList[existingIdx].text = rev.review;
                            window.packageReviewsList[existingIdx].review = rev.review;
                            window.packageReviewsList[existingIdx].date = rev.created_at || 'Recently Verified';
                        }
                    } else {
                        // Prepend new review to list and increment counts
                        window.packageReviewsList.unshift({
                            id: rev.id,
                            name: rev.author,
                            author: rev.author,
                            location: rev.location || 'Indore, MP',
                            date: rev.created_at || 'Just now',
                            rating: rev.rating,
                            text: rev.review,
                            review: rev.review,
                            initial: (rev.author || 'U').charAt(0).toUpperCase(),
                            avatar_bg: 'bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10'
                        });

                        document.querySelectorAll('.header-review-count').forEach(el => {
                            const cur = parseInt(el.textContent) || 0;
                            el.textContent = cur + 1;
                        });
                        const dispCount = document.getElementById('display-review-count');
                        if (dispCount) {
                            dispCount.textContent = (parseInt(dispCount.textContent) || 0) + 1;
                        }
                    }

                    // Rebuild / refresh swiper cards dynamically so updated content is instantly visible
                    const grid = document.getElementById('reviews-cards-grid');
                    if (grid) {
                        grid.innerHTML = window.packageReviewsList.map((r, idx) => {
                            const stars = Array.from({length: 5}, (_, i) => 
                                `<i class="fa-solid fa-star ${i < (r.rating || 5) ? 'text-amber-400' : 'text-gray-300'}"></i>`
                            ).join('');
                            const rText = r.text || r.review || '';
                            const truncatedText = (rText.length > 115) 
                                ? rText.substring(0, 110) + '... <span class="font-bold text-gray-900 dark:text-white underline">read more</span>' 
                                : rText;
                            const rName = r.name || r.author || 'Verified Customer';
                            const rInit = r.initial || rName.charAt(0).toUpperCase();

                            return `
                                <div class="swiper-slide h-auto">
                                    <div onclick="openSingleReviewModal(${idx})"
                                         class="h-full border border-[#E8E5DF] dark:border-white/10 rounded-2xl p-4 sm:p-5 bg-white dark:bg-[#151518] flex flex-col justify-between text-left cursor-pointer"
                                         role="button"
                                         tabindex="0">
                                        <div>
                                            <div class="flex items-start justify-between mb-3">
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-white/10 flex items-center justify-center font-bold text-xs shrink-0">
                                                        ${rInit}
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="font-heading font-bold text-xs text-[#171717] dark:text-white truncate">${rName}</p>
                                                        <p class="text-[10px] text-[#777777] dark:text-gray-400 truncate">${r.location || 'Indore, MP'} · ${r.date || 'Recently'}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center text-amber-400 text-[10px] shrink-0">
                                                    ${stars}
                                                </div>
                                            </div>
                                            <p class="text-xs text-[#555555] dark:text-gray-300 leading-relaxed line-clamp-4">
                                                ${truncatedText}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }).join('');

                        initPackageReviewsSwiper();
                    }

                    setTimeout(() => {
                        closeWriteReviewModal();
                    }, 1200);

                } else {
                    alertBox.className = 'p-3 rounded-xl text-xs font-medium bg-red-50 text-red-800 border border-red-200 block';
                    alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-red-600 mr-1.5"></i> ' + (data.message || 'Please check your inputs and try again.');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<span id="submit-review-btn-text">' + (isEditMode ? 'Update Review' : 'Submit Review') + '</span>';
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
            const galleryModal = document.getElementById('full-gallery-modal');
            const isGalleryOpen = galleryModal && !galleryModal.classList.contains('hidden');

            if (e.key === 'Escape') {
                closeSingleReviewModal();
                closeWriteReviewModal();
                closeFullGalleryModal();
            } else if (isGalleryOpen && e.key === 'ArrowLeft') {
                if (window.modalLightboxSwiper && typeof window.modalLightboxSwiper.slidePrev === 'function') {
                    window.modalLightboxSwiper.slidePrev();
                }
            } else if (isGalleryOpen && e.key === 'ArrowRight') {
                if (window.modalLightboxSwiper && typeof window.modalLightboxSwiper.slideNext === 'function') {
                    window.modalLightboxSwiper.slideNext();
                }
            }
        });

        const galleryModalOverlay = document.getElementById('full-gallery-modal');
        if (galleryModalOverlay) {
            galleryModalOverlay.addEventListener('wheel', function(e) {
                e.preventDefault();
            }, { passive: false });
            galleryModalOverlay.addEventListener('touchmove', function(e) {
                // Prevent touch scroll on background while allowing Swiper gestures inside the modal
                if (!e.target.closest('.modal-lightbox-swiper')) {
                    e.preventDefault();
                }
            }, { passive: false });
        }

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
    <script src="{{ asset('js/details.js') }}?v={{ filemtime(public_path('js/details.js')) }}"></script>

@endsection
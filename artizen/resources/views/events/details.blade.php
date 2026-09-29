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
    <div class="package-detail-page min-h-screen bg-white dark:bg-[#0C0C0E] text-[#171717] font-body selection:bg-[#EA741D] selection:text-white pb-24 pt-4 md:pt-6">
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
                                        class="text-[10px] font-heading font-extrabold text-[#EA741D] bg-[#EA741D]/10 border border-[#EA741D]/25 px-2.5 py-1 rounded-md uppercase tracking-wider leading-none">
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
                                <h4 class="text-[10px] font-heading font-extrabold text-[#EA741D] uppercase tracking-wider mb-2">KEY INCLUSIONS</h4>
                                <ul id="meta-inclusions-list" class="grid grid-cols-1 sm:grid-cols-2 gap-x-2 gap-y-1.5 list-none pl-0">
                                    @foreach(array_slice($event['tiers'][$selectedTierIdx]['inclusions'] ?? [], 0, 6) as $inc)
                                        <li class="flex items-center gap-2 py-0.5 text-xs text-[#333333] dark:text-[#D4D4D8] font-medium font-body">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#EA741D] shrink-0"></span>
                                            <span class="truncate">{{ $inc }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Setup Highlights & Perks (Clean Minimal Dots) -->
                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-[#E8E5DF] dark:border-white/10 text-xs text-[#555555] dark:text-[#D4D4D8]">
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#EA741D] shrink-0"></span>
                                    <span class="text-[11px] font-medium text-main-text">Customizable Themes</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#EA741D] shrink-0"></span>
                                    <span class="text-[11px] font-medium text-main-text">Verified Team & Crew</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#EA741D] shrink-0"></span>
                                    <span class="text-[11px] font-medium text-main-text">Flexible Setup Timings</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#EA741D] shrink-0"></span>
                                    <span class="text-[11px] font-medium text-main-text">All Indore Locations</span>
                                </div>
                            </div>

                            <!-- 3 Simple Facts (In Elegant Fact Card) -->
                            <div class="grid grid-cols-3 gap-2 bg-[#FAF9F5] dark:bg-white/[0.04] border border-[#E8E5DF] dark:border-white/10 rounded-xl p-3">
                                <!-- Setup Time -->
                                <div class="flex items-center gap-2">
                                    <i class="fa-regular fa-clock text-[#EA741D] text-base shrink-0" aria-hidden="true"></i>
                                    <div class="leading-none text-left">
                                        <span id="detail-duration" class="font-heading text-xs font-bold text-main-text block">3-4 Hours</span>
                                        <span class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium block mt-1">Setup Time</span>
                                    </div>
                                </div>

                                <!-- Ideal For -->
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-users text-[#EA741D] text-base shrink-0" aria-hidden="true"></i>
                                    <div class="leading-none text-left">
                                        <span id="detail-ideal-for" class="font-heading text-xs font-bold text-main-text block">100+ Guests</span>
                                        <span class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium block mt-1">Ideal For</span>
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-location-dot text-[#EA741D] text-base shrink-0" aria-hidden="true"></i>
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
                                <i class="fa-solid fa-circle-info text-[#EA741D]"></i> About This Package
                            </h3>
                            <p id="detail-about-desc" class="font-body text-xs sm:text-[13px] text-[#555555] dark:text-[#A1A1AA] leading-relaxed">
                                {{ $event['tiers'][$selectedTierIdx]['desc'] ?? ($event['desc'] ?? 'Experience a flawless celebration with our premium setup. This curated package comes fully equipped with complete decor, professional sound, ambient illumination, and on-site coordination. Our verified team manages the entire setup and logistics, ensuring every detail is executed to perfection.') }}
                            </p>
                        </div>

                        <!-- 4 Compact Factual Highlights (Clean Dots) -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
                            <!-- Fact 1 -->
                            <div class="border border-[#E8E5DF] dark:border-white/10 rounded-xl p-3 flex items-center gap-2.5 bg-[#FAF9F5] dark:bg-white/[0.04]">
                                <span class="w-2 h-2 rounded-full bg-[#EA741D] shrink-0"></span>
                                <div>
                                    <h4 class="font-heading font-bold text-xs text-main-text">Affordable Price</h4>
                                    <p class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium">Best value for money</p>
                                </div>
                            </div>

                            <!-- Fact 2 -->
                            <div class="border border-[#E8E5DF] dark:border-white/10 rounded-xl p-3 flex items-center gap-2.5 bg-[#FAF9F5] dark:bg-white/[0.04]">
                                <span class="w-2 h-2 rounded-full bg-[#EA741D] shrink-0"></span>
                                <div>
                                    <h4 class="font-heading font-bold text-xs text-main-text">Hassle Free</h4>
                                    <p class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium">We handle everything</p>
                                </div>
                            </div>

                            <!-- Fact 3 -->
                            <div class="border border-[#E8E5DF] dark:border-white/10 rounded-xl p-3 flex items-center gap-2.5 bg-[#FAF9F5] dark:bg-white/[0.04]">
                                <span class="w-2 h-2 rounded-full bg-[#EA741D] shrink-0"></span>
                                <div>
                                    <h4 class="font-heading font-bold text-xs text-main-text">Professional Team</h4>
                                    <p class="text-[10px] text-[#777777] dark:text-[#A1A1AA] font-medium">Experienced & verified</p>
                                </div>
                            </div>

                            <!-- Fact 4 -->
                            <div class="border border-[#E8E5DF] dark:border-white/10 rounded-xl p-3 flex items-center gap-2.5 bg-[#FAF9F5] dark:bg-white/[0.04]">
                                <span class="w-2 h-2 rounded-full bg-[#EA741D] shrink-0"></span>
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
                                class="w-full py-3.5 bg-[#EA741D] hover:bg-[#D6630F] text-white text-xs font-heading font-extrabold uppercase tracking-widest rounded-xl shadow-sm hover:shadow transition-all flex items-center justify-center gap-2 cursor-pointer">
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
                    <div class="border border-[#EA741D]/25 bg-[#EA741D]/5 p-3.5 rounded-lg flex items-start gap-2.5 mt-auto text-left">
                        <i class="fa-solid fa-circle-info text-[#EA741D] text-sm shrink-0 mt-0.5" aria-hidden="true"></i>
                        <p class="text-xs text-[#EA741D] dark:text-gray-300 font-medium leading-normal">
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

            <!-- 6. CUSTOMER REVIEWS SECTION -->
            <div class="border border-[#E8E5DF] bg-white p-6 sm:p-7 rounded-xl text-left shadow-sm mb-10" id="reviews-section">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-heading font-bold text-base uppercase tracking-wider text-[#171717]">
                        Customer Reviews
                    </h3>
                    <button onclick="document.getElementById('all-reviews-modal').classList.remove('hidden')"
                        class="text-xs font-heading font-bold text-[#666666] hover:text-[#171717] border border-[#E8E5DF] hover:border-gray-400 px-3.5 py-1.5 rounded-lg transition-all inline-flex items-center gap-1.5 cursor-pointer">
                        View All Reviews <i class="fa-solid fa-arrow-right text-[10px]" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                    <!-- Left: Overall Rating Box -->
                    <div class="lg:col-span-3 bg-[#FAF9F5] border border-[#E8E5DF] rounded-xl p-5 sm:p-6 flex flex-col items-center justify-center text-center">
                        <span class="font-heading font-extrabold text-5xl text-[#171717] leading-none mb-2">4.8</span>
                        <div class="flex items-center gap-1 text-[#EA741D] text-sm mb-2">
                            <i class="fa-solid fa-star" aria-hidden="true"></i>
                            <i class="fa-solid fa-star" aria-hidden="true"></i>
                            <i class="fa-solid fa-star" aria-hidden="true"></i>
                            <i class="fa-solid fa-star" aria-hidden="true"></i>
                            <i class="fa-solid fa-star" aria-hidden="true"></i>
                        </div>
                        <p class="text-xs text-[#777777] font-medium mb-3">Based on 248 reviews</p>
                        <div class="flex flex-wrap gap-1.5 justify-center">
                            <span class="text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full font-semibold">97% Recommend</span>
                            <span class="text-[10px] bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-full font-semibold">Verified Buyers</span>
                        </div>
                    </div>

                    <!-- Right: 3 Clean Review Cards -->
                    <div class="lg:col-span-9 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Review 1 -->
                        <div class="border border-[#E8E5DF] rounded-xl p-4 sm:p-5 bg-white flex flex-col justify-between shadow-xs">
                            <div>
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs shrink-0">
                                            P
                                        </div>
                                        <div>
                                            <p class="font-heading font-bold text-xs text-[#171717]">Priya Sharma</p>
                                            <p class="text-[10px] text-[#777777]">Saket, Indore · 12 Jun 2024</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-[#EA741D] text-[10px]">
                                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                    </div>
                                </div>
                                <span class="inline-block text-[9px] bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full font-semibold mb-2">✓ Verified Buyer</span>
                                <h4 class="font-heading font-bold text-xs text-[#171717] mb-1">Absolutely magical setup!</h4>
                                <p class="text-xs text-[#555555] leading-relaxed line-clamp-4">
                                    The team arrived on time, decorated everything exactly as shown in the photos. Balloon arch was stunning and the LED setup created such a vibe! My husband was completely surprised.
                                </p>
                            </div>
                        </div>

                        <!-- Review 2 -->
                        <div class="border border-[#E8E5DF] rounded-xl p-4 sm:p-5 bg-white flex flex-col justify-between shadow-xs">
                            <div>
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs shrink-0">
                                            R
                                        </div>
                                        <div>
                                            <p class="font-heading font-bold text-xs text-[#171717]">Rohit Verma</p>
                                            <p class="text-[10px] text-[#777777]">Vijay Nagar, Indore · 6 Jun 2024</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-[#EA741D] text-[10px]">
                                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                    </div>
                                </div>
                                <span class="inline-block text-[9px] bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full font-semibold mb-2">✓ Verified Buyer</span>
                                <h4 class="font-heading font-bold text-xs text-[#171717] mb-1">Great experience, minor delay</h4>
                                <p class="text-xs text-[#555555] leading-relaxed line-clamp-4">
                                    Setup was beautiful overall. Team was professional and courteous. Only issue was they arrived 20 mins late but made up for it with extra effort. Decoration quality was top-notch.
                                </p>
                            </div>
                        </div>

                        <!-- Review 3 -->
                        <div class="border border-[#E8E5DF] rounded-xl p-4 sm:p-5 bg-white flex flex-col justify-between shadow-xs">
                            <div>
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">
                                            A
                                        </div>
                                        <div>
                                            <p class="font-heading font-bold text-xs text-[#171717]">Ananya Kapoor</p>
                                            <p class="text-[10px] text-[#777777]">Nipania, Indore · 1 Jun 2024</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-[#EA741D] text-[10px]">
                                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                    </div>
                                </div>
                                <span class="inline-block text-[9px] bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full font-semibold mb-2">✓ Verified Buyer</span>
                                <h4 class="font-heading font-bold text-xs text-[#171717] mb-1">Best birthday surprise ever!</h4>
                                <p class="text-xs text-[#555555] leading-relaxed line-clamp-4">
                                    Booked the platinum package for my mom's 50th. The floral arch, fairy lights, and photo corner were beyond expectations. Guests couldn't stop complimenting. Customer support was responsive throughout.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7. SIMILAR PACKAGES YOU MAY LIKE -->
            <div class="mb-10 text-left">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading font-bold text-lg uppercase tracking-wider text-[#171717]">
                        Similar Packages You May Like
                    </h3>
                    <div class="flex items-center gap-2">
                        <button id="similar-prev" aria-label="Previous similar packages"
                            class="w-8 h-8 rounded-lg border border-[#E8E5DF] hover:border-gray-400 bg-white text-[#171717] flex items-center justify-center shadow-xs transition-all cursor-pointer">
                            <i class="fa-solid fa-chevron-left text-xs" aria-hidden="true"></i>
                        </button>
                        <button id="similar-next" aria-label="Next similar packages"
                            class="w-8 h-8 rounded-lg border border-[#E8E5DF] hover:border-gray-400 bg-white text-[#171717] flex items-center justify-center shadow-xs transition-all cursor-pointer">
                            <i class="fa-solid fa-chevron-right text-xs" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <div id="similar-packages-grid"
                    class="category-row-scroll scrollbar-hide cols-4">
                    <!-- Populated dynamically via JS with exact home page experience-cards -->
                </div>
            </div>

            <!-- 8. EXPLORE OTHER EVENTS SWIPER SECTION -->
            <div class="mt-14 pt-8 border-t border-[#E8E5DF] dark:border-white/10 text-left">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-heading font-extrabold text-lg uppercase tracking-wide text-main-text">Explore Other Events</h3>
                        <p class="text-xs text-[#666666] dark:text-[#A1A1AA] mt-0.5 font-body">Discover more celebrations we specialise in across Indore</p>
                    </div>

                    <!-- Swiper Navigation Controls & View All Button -->
                    <div class="flex items-center gap-2">
                        <button type="button" class="explore-prev-btn w-8 h-8 rounded-lg border border-[#E8E5DF] dark:border-white/10 bg-white dark:bg-[#141416] hover:bg-gray-100 dark:hover:bg-white/10 text-main-text flex items-center justify-center transition-all cursor-pointer shadow-xs">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </button>
                        <button type="button" class="explore-next-btn w-8 h-8 rounded-lg border border-[#E8E5DF] dark:border-white/10 bg-white dark:bg-[#141416] hover:bg-gray-100 dark:hover:bg-white/10 text-main-text flex items-center justify-center transition-all cursor-pointer shadow-xs">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>
                        <a href="{{ route('events.index') }}"
                            class="text-xs font-heading font-bold text-main-text border border-[#E8E5DF] dark:border-white/10 hover:border-gray-400 px-3.5 py-1.5 rounded-lg transition-all uppercase tracking-wider shrink-0 cursor-pointer ml-1">
                            View All
                        </a>
                    </div>
                </div>

                <!-- Swiper Carousel Container -->
                <div class="swiper explore-other-events-swiper overflow-hidden relative rounded-xl p-1">
                    <div class="swiper-wrapper">
                        @php
                            $exploreList = [];
                            if (!empty($packagesDb)) {
                                foreach ($packagesDb as $pKey => $pCat) {
                                    if ((string)$pKey === (string)$eventId) continue;
                                    if (!($pCat['active'] ?? true)) continue;
                                    $firstTier = $pCat['tiers'][0] ?? null;
                                    if (!$firstTier) continue;

                                    $primaryImg = $firstTier['image'] ?? ($pCat['image'] ?? asset('assets/images/hero/1.jpg'));
                                    $catGallery = $pCat['gallery'] ?? [];
                                    $allCatTiers = $pCat['tiers'] ?? [];
                                    $nextTier = $allCatTiers[1] ?? [];
                                    $secondaryImg = !empty($catGallery[0]) && $catGallery[0] !== $primaryImg 
                                        ? $catGallery[0] 
                                        : (!empty($nextTier['image']) && $nextTier['image'] !== $primaryImg 
                                            ? $nextTier['image'] 
                                            : ($catGallery[1] ?? ($pCat['image'] ?? $primaryImg)));

                                    $cardImages = array_values(array_filter(array_unique([
                                        $primaryImg,
                                        $secondaryImg,
                                        $catGallery[0] ?? '',
                                        $catGallery[1] ?? '',
                                        $nextTier['image'] ?? ''
                                    ])));

                                    $exploreList[] = [
                                        'id' => $pKey,
                                        'catTitle' => $pCat['title'] ?? 'Event Setup',
                                        'name' => $firstTier['name'] ?? $pCat['title'],
                                        'price' => $firstTier['price'] ?? 4999,
                                        'desc' => $firstTier['desc'] ?? ($pCat['desc'] ?? 'Complete celebration setup with verified team and coordination.'),
                                        'image' => $primaryImg,
                                        'secondary_image' => $secondaryImg,
                                        'all_images' => $cardImages,
                                        'badge' => $firstTier['badge'] ?? 'Essential Setup',
                                    ];
                                }
                            }

                            // Fallback if packagesDb had fewer than 4 items
                            if (count($exploreList) < 4) {
                                $exploreList = [
                                    [
                                        'id' => 3,
                                        'catTitle' => 'Proposal & Anniversary',
                                        'name' => 'Proposal & Anniversary Setup',
                                        'price' => 3999,
                                        'desc' => 'Candlelit setup, romantic petal trail, ring box spotlight and warm ambient fairy lighting.',
                                        'image' => 'https://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?q=80&w=600&auto=format&fit=crop',
                                        'secondary_image' => 'https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=600&auto=format&fit=crop',
                                        'all_images' => [
                                            'https://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?q=80&w=600&auto=format&fit=crop',
                                            'https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=600&auto=format&fit=crop'
                                        ],
                                        'badge' => 'Best Seller',
                                    ],
                                    [
                                        'id' => 4,
                                        'catTitle' => 'Kids Birthday & Cozy',
                                        'name' => 'Kids Birthday & Cozy Setup',
                                        'price' => 2999,
                                        'desc' => 'Themed kids backdrop, soft play zone, organic pastel balloon arch and kids activity tables.',
                                        'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?q=80&w=600&auto=format&fit=crop',
                                        'secondary_image' => 'https://images.unsplash.com/photo-1464349095431-e9a21285b5f3?q=80&w=600&auto=format&fit=crop',
                                        'all_images' => [
                                            'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?q=80&w=600&auto=format&fit=crop',
                                            'https://images.unsplash.com/photo-1464349095431-e9a21285b5f3?q=80&w=600&auto=format&fit=crop'
                                        ],
                                        'badge' => 'Popular Choice',
                                    ],
                                    [
                                        'id' => 5,
                                        'catTitle' => 'DJ & Live Acoustic',
                                        'name' => 'DJ & Live Acoustic Nights',
                                        'price' => 4999,
                                        'desc' => 'Pro DJ setup, high-watt sound columns, strobes, laser lights and intelligent fog machine.',
                                        'image' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?q=80&w=600&auto=format&fit=crop',
                                        'secondary_image' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=600&auto=format&fit=crop',
                                        'all_images' => [
                                            'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?q=80&w=600&auto=format&fit=crop',
                                            'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=600&auto=format&fit=crop'
                                        ],
                                        'badge' => 'Essential Setup',
                                    ],
                                    [
                                        'id' => 11,
                                        'catTitle' => 'Wedding & Sangeet',
                                        'name' => 'Wedding & Sangeet Setup',
                                        'price' => 14999,
                                        'desc' => 'Grand floral mandap, royal velvet entrance runner, brass urli setup and stage illumination.',
                                        'image' => 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?q=80&w=600&auto=format&fit=crop',
                                        'secondary_image' => 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=600&auto=format&fit=crop',
                                        'all_images' => [
                                            'https://images.unsplash.com/photo-1519225421980-715cb0215aed?q=80&w=600&auto=format&fit=crop',
                                            'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=600&auto=format&fit=crop'
                                        ],
                                        'badge' => 'Luxury Tier',
                                    ],
                                    [
                                        'id' => 2,
                                        'catTitle' => 'House Party Rigs',
                                        'name' => 'House Party Sound & Lights',
                                        'price' => 5999,
                                        'desc' => 'Compact club audio rig, RGB beam lights, smoke machine and party coordinator.',
                                        'image' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=600&auto=format&fit=crop',
                                        'secondary_image' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?q=80&w=600&auto=format&fit=crop',
                                        'all_images' => [
                                            'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=600&auto=format&fit=crop',
                                            'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?q=80&w=600&auto=format&fit=crop'
                                        ],
                                        'badge' => 'Weekend Special',
                                    ]
                                ];
                            }
                        @endphp

                        @foreach($exploreList as $item)
                            @php
                                $isGold = stripos($item['badge'], 'seller') !== false || stripos($item['badge'], 'luxury') !== false;
                                $itemImages = !empty($item['all_images']) ? $item['all_images'] : array_filter([$item['image'], $item['secondary_image'] ?? '']);
                            @endphp
                            <div class="swiper-slide h-auto flex">
                                <div class="experience-card card-tier-basic cursor-pointer w-full flex flex-col justify-between" onclick="window.location.href='{{ route('events.show', $item['id']) }}?tier=0'">
                                    <div class="flex flex-col flex-grow">
                                        <div class="experience-card-img-container" data-card-images='@json($itemImages)'>
                                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="experience-card-img primary-img" loading="lazy">
                                            @if(!empty($item['secondary_image']) && $item['secondary_image'] !== $item['image'])
                                                <img src="{{ $item['secondary_image'] }}" alt="{{ $item['name'] }}" class="experience-card-img secondary-img" loading="lazy">
                                            @endif
                                            <div class="experience-card-badge {{ $isGold ? 'gold' : '' }}">{{ $item['badge'] }}</div>
                                            @if(count($itemImages) > 1)
                                                <div class="card-img-indicators">
                                                    @foreach($itemImages as $i => $img)
                                                        <span class="card-img-dot {{ $i === 0 ? 'active' : '' }}"></span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="experience-card-header">
                                            <h3 class="experience-card-title">{{ $item['name'] }}</h3>
                                            <div class="experience-card-rating flex items-center gap-1">
                                                <i class="fa-solid fa-star text-gold text-xs" aria-hidden="true"></i>
                                                <span>4.9</span>
                                            </div>
                                        </div>
                                        <p class="experience-card-desc">{{ $item['desc'] }}</p>
                                    </div>

                                    <div class="experience-card-footer mt-auto">
                                        <div class="experience-card-price-stack">
                                            <div class="experience-card-price">From <span>₹{{ number_format($item['price']) }}</span></div>
                                        </div>
                                        <span class="experience-card-btn">
                                            Book Now
                                            <i class="fa-solid fa-chevron-right text-[10px] experience-card-arrow" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Mobile Floating Sticky CTA Bar (Visible on Mobile < 768px) -->
    <div class="package-detail-sticky-bar md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-[#141416]/95 backdrop-blur-md border-t border-[#E8E5DF] dark:border-white/10 p-3.5 flex items-center justify-between gap-3 shadow-2xl">
        <div class="flex flex-col text-left">
            <span class="text-[9px] font-bold text-[#777777] dark:text-gray-400 uppercase tracking-wider">Total Package Price</span>
            <span id="mobile-sticky-price" class="text-base font-extrabold text-main-text">₹{{ number_format($event['tiers'][$selectedTierIdx]['price'] ?? 6000) }}</span>
        </div>
        <button onclick="bookPackageNow()"
            class="px-5 py-2.5 bg-[#EA741D] hover:bg-[#D6630F] text-white text-xs font-extrabold uppercase tracking-wider rounded-lg shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
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

    <!-- Reviews Modal for "View All Reviews" -->
    <div id="all-reviews-modal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="bg-white border border-[#E8E5DF] w-full max-w-2xl rounded-xl p-6 shadow-2xl flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between border-b border-[#E8E5DF] pb-3 mb-4">
                <h3 class="font-heading font-extrabold text-sm uppercase tracking-wider text-[#171717]">All Customer Reviews (248)</h3>
                <button onclick="document.getElementById('all-reviews-modal').classList.add('hidden')" class="text-gray-500 hover:text-black cursor-pointer bg-transparent border-0 focus:outline-none" aria-label="Close reviews modal">
                    <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
                </button>
            </div>
            <div class="overflow-y-auto max-h-[65vh] flex flex-col gap-4 p-1 text-left">
                <!-- Review List -->
                <div class="border-b border-[#E8E5DF] pb-3">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-heading font-bold text-xs text-[#171717]">Priya Sharma (Saket, Indore)</span>
                        <div class="text-[#EA741D] text-xs"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                    </div>
                    <p class="text-xs text-[#555555]">The team arrived on time, decorated everything exactly as shown in the photos. Balloon arch was stunning and the LED setup created such a vibe!</p>
                </div>
                <div class="border-b border-[#E8E5DF] pb-3">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-heading font-bold text-xs text-[#171717]">Rohit Verma (Vijay Nagar, Indore)</span>
                        <div class="text-[#EA741D] text-xs"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                    </div>
                    <p class="text-xs text-[#555555]">Setup was beautiful overall. Team was professional and courteous. Decoration quality was top-notch.</p>
                </div>
                <div class="border-b border-[#E8E5DF] pb-3">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-heading font-bold text-xs text-[#171717]">Ananya Kapoor (Nipania, Indore)</span>
                        <div class="text-[#EA741D] text-xs"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                    </div>
                    <p class="text-xs text-[#555555]">Booked the platinum package for my mom's 50th. The floral arch, fairy lights, and photo corner were beyond expectations. 10/10!</p>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-heading font-bold text-xs text-[#171717]">Siddharth Jain (Bypass Road, Indore)</span>
                        <div class="text-[#EA741D] text-xs"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                    </div>
                    <p class="text-xs text-[#555555]">Everything from WhatsApp inquiry to final event setup was smooth. The team understood our vision perfectly.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Data & Controller Script -->
    <script>
        window.eventDatabase = @json($packagesDb ?? []);
        window.currentMatchedId = "{{ $eventId ?? 1 }}";
        window.currentMatchedTierIdx = {{ $selectedTierIdx ?? 0 }};

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
        }

        function closeFullGalleryModal() {
            const modal = document.getElementById('full-gallery-modal');
            if (modal) modal.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', () => {
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
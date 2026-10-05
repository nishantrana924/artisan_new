@extends('layouts.app')

@section('title', 'Book Event Package in Indore - ARTIZEN')

@section('content')
<div class="min-h-screen pt-8 pb-20 px-4 md:px-8 lg:px-12 bg-[#F8F9FA] dark:bg-[#0C0C0E] text-[#1E1E24] dark:text-[#E4E4E7] transition-colors duration-300">
    <div class="max-w-7xl mx-auto">

        <!-- Breadcrumb Navigation -->
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-6 font-medium">
            <a href="{{ route('home') }}" class="hover:text-gold transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-house text-[10px]"></i>
                <span>Home</span>
            </a>
            <i class="fa-solid fa-chevron-right text-[9px] text-gray-400 dark:text-gray-600"></i>
            <a href="{{ route('events.index') }}" class="hover:text-gold transition-colors">Packages</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-gray-400 dark:text-gray-600"></i>
            <span class="text-[#1E1E24] dark:text-white font-semibold">Reserve Experience</span>
        </nav>

        <!-- Page Header & Trust Banner -->
        <div class="relative bg-white dark:bg-[#141417] border border-gray-200/80 dark:border-white/10 rounded-3xl p-6 md:p-10 mb-8 shadow-sm overflow-hidden">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-[#FFD600]/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="max-w-2xl text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-heading font-black tracking-wider uppercase bg-[#FFD600]/15 text-[#997300] dark:text-[#FFD600] border border-[#FFD600]/30 mb-3 shadow-sm">
                        <i class="fa-solid fa-shield-halved text-[10px]"></i>
                        ZERO ADVANCE PAYMENT • 100% OFFLINE SETTLEMENT
                    </div>
                    <h1 class="font-heading font-black text-3xl sm:text-4xl md:text-5xl uppercase tracking-tight text-[#1E1E24] dark:text-white leading-[1.1]">
                        Book Your <span class="text-[#D4A300] dark:text-[#FFD600]">Celebration</span> Setup
                    </h1>
                    <p class="text-sm md:text-base text-gray-600 dark:text-gray-300 mt-2.5 leading-relaxed font-normal">
                        Select your preferred setup tier and provide your event details in Indore. Our dedicated event coordinator reviews availability and contacts you within 15 minutes.
                    </p>
                </div>

                <!-- 4 Quick Assurance Pills -->
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-2 gap-3 w-full lg:w-auto text-left">
                    <div class="flex items-center gap-2.5 p-3 rounded-2xl bg-gray-50 dark:bg-white/[0.04] border border-gray-200/60 dark:border-white/5">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-wallet text-xs"></i>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-gray-900 dark:text-white block leading-tight">Pay After Setup</span>
                            <span class="text-[10px] text-gray-500">₹0 due today</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 p-3 rounded-2xl bg-gray-50 dark:bg-white/[0.04] border border-gray-200/60 dark:border-white/5">
                        <div class="w-8 h-8 rounded-xl bg-[#FFD600]/15 text-[#B89000] dark:text-[#FFD600] flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-truck-fast text-xs"></i>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-gray-900 dark:text-white block leading-tight">Indore Crew</span>
                            <span class="text-[10px] text-gray-500">Free transport & setup</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 p-3 rounded-2xl bg-gray-50 dark:bg-white/[0.04] border border-gray-200/60 dark:border-white/5">
                        <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-gray-900 dark:text-white block leading-tight">Free Date Swap</span>
                            <span class="text-[10px] text-gray-500">Up to 24h prior</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 p-3 rounded-2xl bg-gray-50 dark:bg-white/[0.04] border border-gray-200/60 dark:border-white/5">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-star text-xs"></i>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-gray-900 dark:text-white block leading-tight">4.9/5 Rating</span>
                            <span class="text-[10px] text-gray-500">Verified Indore setups</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Validation Errors Alert -->
        @if (isset($errors) && $errors->any())
            <div class="mb-8 p-5 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/60 text-red-700 dark:text-red-300 text-xs font-semibold shadow-sm">
                <div class="flex items-center gap-2.5 font-bold text-sm mb-2">
                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                    <span>Please correct the following before submitting:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 ml-1 text-xs font-normal">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Form & Realtime Summary Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start text-left">
            
            <!-- Left Form Area (7 Cols) -->
            <div class="lg:col-span-7">
                <form action="{{ route('booking.save') }}" method="POST" id="artizen-booking-form" class="space-y-8">
                    @csrf

                    <!-- Hidden calculated inputs for backend validation and safety -->
                    <input type="hidden" name="price" id="form-price-input" value="{{ $selectedPrice > 0 ? $selectedPrice : 4999 }}">
                    <input type="hidden" name="total_price" id="form-total-price-input" value="{{ $selectedPrice > 0 ? $selectedPrice : 4999 }}">
                    <input type="hidden" name="category" id="form-category-input" value="{{ $selectedCat ?: ($categoriesList[0]['title'] ?? 'Adult Birthdays') }}">
                    <input type="hidden" name="package" id="form-package-input" value="{{ $selectedPkgName ?: '' }}">

                    <!-- Step 1: Package & Experience Selection -->
                    <div class="bg-white dark:bg-[#141417] border border-gray-200/80 dark:border-white/10 p-6 md:p-8 rounded-3xl shadow-sm relative">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-[#FFD600] text-[#171719] text-xs font-heading font-black flex items-center justify-center shadow-sm">
                                    1
                                </span>
                                <div>
                                    <h2 class="font-heading font-black text-lg md:text-xl uppercase tracking-tight text-[#1E1E24] dark:text-white">
                                        Choose Event & Package Tier
                                    </h2>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Select celebration category and setup scale</p>
                                </div>
                            </div>
                        </div>

                        <!-- Category Selector Pills -->
                        <div class="mb-6">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-2.5">
                                Select Event Category
                            </label>
                            <div class="flex flex-wrap gap-2" id="category-pills-container">
                                @foreach($categoriesList as $cat)
                                    @if($cat['active'] ?? true)
                                        @php
                                            $isCatActive = ($selectedCat === $cat['title']) || (!$selectedCat && $loop->first);
                                        @endphp
                                        <button type="button" 
                                            onclick="selectCategory('{{ addslashes($cat['title']) }}')"
                                            data-category="{{ $cat['title'] }}"
                                            class="category-pill px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 border {{ $isCatActive ? 'bg-[#FFD600] text-[#171719] border-[#FFD600] shadow-sm' : 'bg-gray-50 dark:bg-white/[0.04] text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/10 hover:border-[#FFD600]/60' }}">
                                            <i class="fa-solid {{ match(strtolower($cat['title'])) {
                                                'birthdays', 'adult birthdays' => 'fa-cake-candles',
                                                'kids & cozy' => 'fa-shapes',
                                                'house party', 'house party rigs' => 'fa-compact-disc',
                                                'candlelight & dinner', 'anniversaries' => 'fa-heart',
                                                'proposals & date' => 'fa-ring',
                                                'baby shower' => 'fa-baby',
                                                default => 'fa-sparkles'
                                            } }} text-[11px]"></i>
                                            <span>{{ $cat['title'] }}</span>
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Package Tiers Interactive Grid -->
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-2.5 flex items-center justify-between">
                                <span>Available Setup Tiers for Selected Category</span>
                                <span class="text-[10px] text-gray-400 font-normal lowercase">click to select tier</span>
                            </label>
                            
                            <div id="tiers-container" class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                <!-- Populated dynamically by JavaScript -->
                            </div>

                            <!-- Fallback Dropdown for Accessibility/Screen Readers -->
                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/5">
                                <label for="booking-package-select" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">
                                    Alternative Package Dropdown
                                </label>
                                <select id="booking-package-select" onchange="handleDropdownPackageChange(this.value)" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-[#1A1A1E] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-gold">
                                    <!-- Populated by JS -->
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Date & Setup Timing -->
                    <div class="bg-white dark:bg-[#141417] border border-gray-200/80 dark:border-white/10 p-6 md:p-8 rounded-3xl shadow-sm">
                        <div class="flex items-center gap-3 mb-5">
                            <span class="w-8 h-8 rounded-xl bg-[#FFD600] text-[#171719] text-xs font-heading font-black flex items-center justify-center shadow-sm">
                                2
                            </span>
                            <div>
                                <h2 class="font-heading font-black text-lg md:text-xl uppercase tracking-tight text-[#1E1E24] dark:text-white">
                                    Event Schedule & Attendance
                                </h2>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Date, setup slot, and expected guest count</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Event Date Input -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                    Event Date *
                                </label>
                                <div class="relative">
                                    <input type="date" 
                                        name="event_date" 
                                        id="event_date_input"
                                        value="{{ old('event_date', date('Y-m-d', strtotime('+2 days'))) }}" 
                                        min="{{ date('Y-m-d') }}" 
                                        required 
                                        onchange="updateScheduleSummary()"
                                        class="w-full px-4 py-3 bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600] transition-colors font-medium">
                                </div>

                                <!-- Quick Date Selectors -->
                                <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                                    <button type="button" onclick="setQuickDate(1)" class="px-2.5 py-1 text-[10px] font-semibold bg-gray-100 hover:bg-gray-200 dark:bg-white/5 dark:hover:bg-white/10 rounded-md text-gray-700 dark:text-gray-300 transition-colors">
                                        Tomorrow
                                    </button>
                                    <button type="button" onclick="setQuickDate(3)" class="px-2.5 py-1 text-[10px] font-semibold bg-gray-100 hover:bg-gray-200 dark:bg-white/5 dark:hover:bg-white/10 rounded-md text-gray-700 dark:text-gray-300 transition-colors">
                                        In 3 Days
                                    </button>
                                    <button type="button" onclick="setNextWeekend()" class="px-2.5 py-1 text-[10px] font-semibold bg-gray-100 hover:bg-gray-200 dark:bg-white/5 dark:hover:bg-white/10 rounded-md text-gray-700 dark:text-gray-300 transition-colors">
                                        Upcoming Weekend
                                    </button>
                                </div>
                            </div>

                            <!-- Guest Count with Stepper -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                    Expected Guest Count *
                                </label>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="adjustGuestCount(-5)" class="w-11 h-11 rounded-xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-700 dark:text-white flex items-center justify-center font-bold hover:bg-[#FFD600] hover:text-[#171719] transition-all">
                                        <i class="fa-solid fa-minus text-xs"></i>
                                    </button>
                                    <input type="number" 
                                        name="guest_count" 
                                        id="guest_count_input" 
                                        value="{{ old('guest_count', 30) }}" 
                                        min="1" 
                                        max="2000" 
                                        required 
                                        oninput="updateScheduleSummary()"
                                        class="w-full text-center py-3 bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-xl text-xs font-bold text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#FFD600]">
                                    <button type="button" onclick="adjustGuestCount(5)" class="w-11 h-11 rounded-xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-700 dark:text-white flex items-center justify-center font-bold hover:bg-[#FFD600] hover:text-[#171719] transition-all">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                    </button>
                                </div>

                                <!-- Guest Count Pills -->
                                <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                                    <button type="button" onclick="setGuestCount(15)" class="px-2 py-1 text-[10px] font-semibold bg-gray-100 hover:bg-gray-200 dark:bg-white/5 dark:hover:bg-white/10 rounded-md text-gray-700 dark:text-gray-300">
                                        15 guests
                                    </button>
                                    <button type="button" onclick="setGuestCount(30)" class="px-2 py-1 text-[10px] font-semibold bg-gray-100 hover:bg-gray-200 dark:bg-white/5 dark:hover:bg-white/10 rounded-md text-gray-700 dark:text-gray-300">
                                        30 guests
                                    </button>
                                    <button type="button" onclick="setGuestCount(50)" class="px-2 py-1 text-[10px] font-semibold bg-gray-100 hover:bg-gray-200 dark:bg-white/5 dark:hover:bg-white/10 rounded-md text-gray-700 dark:text-gray-300">
                                        50 guests
                                    </button>
                                    <button type="button" onclick="setGuestCount(100)" class="px-2 py-1 text-[10px] font-semibold bg-gray-100 hover:bg-gray-200 dark:bg-white/5 dark:hover:bg-white/10 rounded-md text-gray-700 dark:text-gray-300">
                                        100+ guests
                                    </button>
                                </div>
                            </div>

                            <!-- Setup Time Slots as Visual Cards -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                                    Preferred Setup Window *
                                </label>
                                <input type="hidden" name="event_time" id="event_time_input" value="{{ old('event_time', 'Evening (04:00 PM - 08:00 PM)') }}">
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5" id="time-slots-container">
                                    @php
                                        $slots = [
                                            ['label' => 'Morning (09:00 AM - 12:00 PM)', 'title' => 'Morning', 'time' => '9 AM - 12 PM', 'desc' => 'Brunches & pujas', 'icon' => 'fa-sun'],
                                            ['label' => 'Afternoon (12:00 PM - 04:00 PM)', 'title' => 'Afternoon', 'time' => '12 PM - 4 PM', 'desc' => 'Kids & family time', 'icon' => 'fa-cloud-sun'],
                                            ['label' => 'Evening (04:00 PM - 08:00 PM)', 'title' => 'Evening', 'time' => '4 PM - 8 PM', 'desc' => 'Most Popular', 'icon' => 'fa-star', 'badge' => 'Popular'],
                                            ['label' => 'Night (08:00 PM - Midnight)', 'title' => 'Night', 'time' => '8 PM - 12 AM', 'desc' => 'Club & party rigs', 'icon' => 'fa-moon'],
                                        ];
                                        $currentSlot = old('event_time', 'Evening (04:00 PM - 08:00 PM)');
                                    @endphp

                                    @foreach($slots as $s)
                                        @php $isSelected = ($currentSlot === $s['label']); @endphp
                                        <button type="button" 
                                            onclick="selectTimeSlot('{{ $s['label'] }}')"
                                            data-slot="{{ $s['label'] }}"
                                            class="time-slot-card p-3 rounded-2xl border text-left transition-all relative flex flex-col justify-between {{ $isSelected ? 'border-[#FFD600] bg-[#FFD600]/10 ring-1 ring-[#FFD600]' : 'border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/[0.04] hover:border-[#FFD600]/50' }}">
                                            @if(isset($s['badge']))
                                                <span class="absolute top-2 right-2 text-[9px] font-black uppercase tracking-wider bg-[#FFD600] text-[#171719] px-1.5 py-0.5 rounded">
                                                    {{ $s['badge'] }}
                                                </span>
                                            @endif
                                            <div class="flex items-center gap-2 mb-1.5">
                                                <i class="fa-solid {{ $s['icon'] }} text-gold text-xs"></i>
                                                <span class="font-heading font-extrabold text-xs text-gray-900 dark:text-white">{{ $s['title'] }}</span>
                                            </div>
                                            <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 block">{{ $s['time'] }}</span>
                                            <span class="text-[10px] text-gray-500 block mt-0.5">{{ $s['desc'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Venue in Indore -->
                    <div class="bg-white dark:bg-[#141417] border border-gray-200/80 dark:border-white/10 p-6 md:p-8 rounded-3xl shadow-sm">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-[#FFD600] text-[#171719] text-xs font-heading font-black flex items-center justify-center shadow-sm">
                                    3
                                </span>
                                <div>
                                    <h2 class="font-heading font-black text-lg md:text-xl uppercase tracking-tight text-[#1E1E24] dark:text-white">
                                        Venue Location (Indore)
                                    </h2>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Where should our team set up the decor and equipment?</p>
                                </div>
                            </div>
                            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                <i class="fa-solid fa-map-pin text-[10px]"></i> Indore City Delivery
                            </span>
                        </div>

                        <!-- Indore Area Quick-Select Pills -->
                        <div class="mb-4">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-1.5">
                                Popular Indore Localities (tap to autofill)
                            </label>
                            <div class="flex flex-wrap gap-1.5">
                                @php
                                    $indoreAreas = [
                                        ['name' => 'Vijay Nagar', 'pin' => '452010'],
                                        ['name' => 'Palasia', 'pin' => '452001'],
                                        ['name' => 'Saket', 'pin' => '452018'],
                                        ['name' => 'Nipania', 'pin' => '452010'],
                                        ['name' => 'Bhawarkua', 'pin' => '452014'],
                                        ['name' => 'Annapurna', 'pin' => '452009'],
                                        ['name' => 'AB Road', 'pin' => '452008'],
                                        ['name' => 'Super Corridor', 'pin' => '452005'],
                                        ['name' => 'Rau', 'pin' => '453331'],
                                        ['name' => 'Geeta Bhawan', 'pin' => '452001']
                                    ];
                                @endphp
                                @foreach($indoreAreas as $a)
                                    <button type="button" onclick="setArea('{{ $a['name'] }}', '{{ $a['pin'] }}')" class="px-2.5 py-1 text-[11px] font-medium bg-gray-100 hover:bg-[#FFD600]/20 hover:text-[#B89000] dark:bg-white/5 dark:hover:bg-white/10 rounded-lg text-gray-700 dark:text-gray-300 border border-gray-200/60 dark:border-white/5 transition-colors">
                                        {{ $a['name'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                    Full Venue Address *
                                </label>
                                <textarea name="address" 
                                    id="venue_address_input"
                                    rows="2" 
                                    required 
                                    placeholder="e.g. Bungalow 14, Lotus Villa, Near Shekhar Central..." 
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600] transition-colors leading-relaxed">{{ old('address') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                    Area / Colony *
                                </label>
                                <input type="text" 
                                    name="area" 
                                    id="venue_area_input"
                                    value="{{ old('area') }}" 
                                    required 
                                    placeholder="e.g. Vijay Nagar, Scheme 54" 
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600] transition-colors">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                    Landmark
                                </label>
                                <input type="text" 
                                    name="landmark" 
                                    id="venue_landmark_input"
                                    value="{{ old('landmark') }}" 
                                    placeholder="e.g. Opp. C21 Mall / Near Club House" 
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600] transition-colors">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                    City & State
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" name="city" value="Indore" readonly class="w-full px-3.5 py-3 bg-gray-100 dark:bg-white/10 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white font-bold cursor-not-allowed text-center">
                                    <input type="text" name="state" value="Madhya Pradesh" readonly class="w-full px-3.5 py-3 bg-gray-100 dark:bg-white/10 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white font-bold cursor-not-allowed text-center truncate">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                    Pincode *
                                </label>
                                <input type="text" 
                                    name="pincode" 
                                    id="venue_pincode_input"
                                    value="{{ old('pincode', '452010') }}" 
                                    required 
                                    maxlength="6"
                                    placeholder="e.g. 452010" 
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600] transition-colors">
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Contact & Customization Notes -->
                    <div class="bg-white dark:bg-[#141417] border border-gray-200/80 dark:border-white/10 p-6 md:p-8 rounded-3xl shadow-sm">
                        <div class="flex items-center gap-3 mb-5">
                            <span class="w-8 h-8 rounded-xl bg-[#FFD600] text-[#171719] text-xs font-heading font-black flex items-center justify-center shadow-sm">
                                4
                            </span>
                            <div>
                                <h2 class="font-heading font-black text-lg md:text-xl uppercase tracking-tight text-[#1E1E24] dark:text-white">
                                    Contact & Special Requests
                                </h2>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Where should we send your booking details?</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                    Your Full Name *
                                </label>
                                <div class="relative">
                                    <i class="fa-regular fa-user absolute left-4 top-3.5 text-gray-400 text-xs"></i>
                                    <input type="text" 
                                        name="name" 
                                        value="{{ old('name') }}" 
                                        required 
                                        placeholder="e.g. Rohan Agarwal" 
                                        class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600] transition-colors">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                    Mobile Phone Number *
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3.5 text-gray-500 text-xs font-bold">+91</span>
                                    <input type="tel" 
                                        name="mobile" 
                                        id="customer_mobile_input"
                                        value="{{ old('mobile') }}" 
                                        required 
                                        placeholder="98260XXXXX" 
                                        oninput="handleMobileInput(this.value)"
                                        class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600] transition-colors font-medium">
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                        WhatsApp Number *
                                    </label>
                                    <label class="flex items-center gap-1.5 cursor-pointer text-[10px] text-emerald-600 dark:text-emerald-400 font-bold select-none">
                                        <input type="checkbox" id="same_as_mobile_check" checked onchange="toggleSameAsMobile(this.checked)" class="rounded text-[#FFD600] focus:ring-0">
                                        <span>Same as Mobile</span>
                                    </label>
                                </div>
                                <div class="relative">
                                    <i class="fa-brands fa-whatsapp absolute left-4 top-3.5 text-emerald-500 text-sm"></i>
                                    <input type="tel" 
                                        name="whatsapp" 
                                        id="customer_whatsapp_input"
                                        value="{{ old('whatsapp') }}" 
                                        required 
                                        placeholder="98260XXXXX" 
                                        class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600] transition-colors font-medium">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                    Email Address (Optional)
                                </label>
                                <div class="relative">
                                    <i class="fa-regular fa-envelope absolute left-4 top-3.5 text-gray-400 text-xs"></i>
                                    <input type="email" 
                                        name="email" 
                                        value="{{ old('email') }}" 
                                        placeholder="rohan@gmail.com" 
                                        class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600] transition-colors">
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                    Special Customization Notes & Preferences
                                </label>
                                <textarea name="notes" 
                                    rows="2" 
                                    placeholder="e.g. Preferred balloon color palette: Black & Metallic Gold. Setup needed on terrace. Need extra microphone." 
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600] transition-colors leading-relaxed">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <!-- Notice Box -->
                        <div class="mt-6 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-900 dark:text-amber-200 flex items-start gap-3">
                            <i class="fa-solid fa-circle-info text-amber-500 text-sm mt-0.5 shrink-0"></i>
                            <div class="leading-relaxed">
                                <span class="font-bold">Offline Payment Guarantee:</span> No credit card or advance money is collected on this website. Our team will verify your venue access, confirm slot availability, and collect payment offline.
                            </div>
                        </div>

                        <!-- Mobile Submit Button (Visible on small screens) -->
                        <div class="mt-6 lg:hidden">
                            <button type="submit" class="w-full py-4 bg-[#FFD600] hover:bg-[#E6C200] active:scale-[0.99] text-[#171719] font-heading font-black text-sm uppercase tracking-wider rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                                <span>Submit Request (₹0 Advance)</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Right Sticky Booking Summary & Trust Center (5 Cols) -->
            <div class="lg:col-span-5 lg:sticky lg:top-24 space-y-6">
                
                <!-- Live Summary Card -->
                <div class="bg-white dark:bg-[#141417] border border-gray-200/80 dark:border-white/10 rounded-3xl p-6 shadow-md relative overflow-hidden">
                    <div class="flex items-center justify-between mb-4 border-b border-gray-100 dark:border-white/5 pb-3">
                        <span class="text-[10px] font-heading font-black text-[#D4A300] dark:text-[#FFD600] uppercase tracking-widest flex items-center gap-1.5">
                            <i class="fa-solid fa-circle text-[6px] text-emerald-500 animate-pulse"></i> LIVE SUMMARY
                        </span>
                        <span id="summary-category-badge" class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-300">
                            {{ $selectedCat ?: 'Event Setup' }}
                        </span>
                    </div>

                    <!-- Selected Tier Thumbnail & Title -->
                    <div class="flex items-center gap-4 mb-5">
                        <div class="w-20 h-20 rounded-2xl overflow-hidden bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 shrink-0 relative">
                            <img id="summary-tier-img" src="/assets/images/hero/1.jpg" alt="Selected package setup" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0 text-left">
                            <h3 id="summary-package-title" class="font-heading font-black text-base md:text-lg text-gray-900 dark:text-white leading-tight truncate">
                                Standard Setup
                            </h3>
                            <p id="summary-tier-desc" class="text-[11px] text-gray-500 dark:text-gray-400 line-clamp-2 mt-1 leading-snug">
                                Complete celebration decor with premium backdrop and ambient lighting.
                            </p>
                        </div>
                    </div>

                    <!-- What's Included Preview -->
                    <div class="mb-5 bg-gray-50 dark:bg-white/[0.03] p-3.5 rounded-2xl border border-gray-100 dark:border-white/5 text-left">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 block mb-2">
                            Key Inclusions in this Tier:
                        </span>
                        <ul id="summary-inclusions-list" class="space-y-1.5 text-xs text-gray-700 dark:text-gray-300">
                            <!-- Populated dynamically -->
                        </ul>
                    </div>

                    <!-- Schedule & Venue Snapshot -->
                    <div class="space-y-2.5 text-xs text-gray-600 dark:text-gray-300 border-t border-b border-gray-100 dark:border-white/5 py-4 font-medium text-left">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar text-[11px]"></i> Date & Time:
                            </span>
                            <span id="summary-date-time" class="font-bold text-gray-900 dark:text-white text-right">
                                Upcoming Slot
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 flex items-center gap-1.5">
                                <i class="fa-solid fa-users text-[11px]"></i> Guest Count:
                            </span>
                            <span id="summary-guest-count" class="font-bold text-gray-900 dark:text-white">
                                30 Guests
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-[11px]"></i> Coverage:
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white">
                                Indore, MP (Free Setup)
                            </span>
                        </div>
                    </div>

                    <!-- Price Breakdown Sheet -->
                    <div class="space-y-2 pt-4 text-xs text-left">
                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                            <span>Base Package Price:</span>
                            <span id="summary-base-price" class="font-bold text-gray-900 dark:text-white text-sm">₹4,999</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                            <span class="flex items-center gap-1">
                                <span>White-Glove Crew & Setup:</span>
                            </span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">FREE</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                            <span>Advance Payment Required:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">₹0 (Pay Later)</span>
                        </div>

                        <!-- Grand Total Highlight -->
                        <div class="pt-3 mt-2 border-t border-gray-200 dark:border-white/10 flex items-baseline justify-between">
                            <div>
                                <span class="font-heading font-black text-sm text-gray-900 dark:text-white uppercase block">
                                    Total Package Value:
                                </span>
                                <span class="text-[10px] text-gray-500">Pay offline after event confirmation</span>
                            </div>
                            <span id="summary-total-price" class="font-heading font-black text-3xl text-[#D4A300] dark:text-[#FFD600]">
                                ₹4,999
                            </span>
                        </div>
                    </div>

                    <!-- Primary Desktop Submit CTA Button -->
                    <div class="mt-6 hidden lg:block">
                        <button type="button" onclick="document.getElementById('artizen-booking-form').requestSubmit()" class="w-full py-4 bg-[#FFD600] hover:bg-[#E6C200] active:scale-[0.99] text-[#171719] font-heading font-black text-sm uppercase tracking-wider rounded-2xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                            <span>Submit Request (₹0 Advance)</span>
                        </button>
                    </div>

                    <p class="text-[11px] text-gray-500 text-center font-medium mt-3 leading-snug">
                        By submitting, you reserve priority date availability. An event stylist will coordinate setup details via WhatsApp.
                    </p>
                </div>

                <!-- 3-Step Process Flow Card -->
                <div class="bg-white dark:bg-[#141417] border border-gray-200/80 dark:border-white/10 rounded-3xl p-5 text-left text-xs shadow-sm">
                    <span class="text-[10px] font-heading font-black text-gray-400 uppercase tracking-widest block mb-3">
                        HOW ARTIZEN BOOKING WORKS
                    </span>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-[#FFD600]/20 text-[#B89000] dark:text-[#FFD600] text-[10px] font-black flex items-center justify-center shrink-0 mt-0.5">
                                1
                            </div>
                            <div>
                                <span class="font-bold text-gray-900 dark:text-white block">Submit Free Request</span>
                                <span class="text-gray-500 text-[11px]">Lock in your date with zero upfront advance payment.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-[#FFD600]/20 text-[#B89000] dark:text-[#FFD600] text-[10px] font-black flex items-center justify-center shrink-0 mt-0.5">
                                2
                            </div>
                            <div>
                                <span class="font-bold text-gray-900 dark:text-white block">15-Min WhatsApp / Call Sync</span>
                                <span class="text-gray-500 text-[11px]">We confirm setup timing, themes, and venue dimensions.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-[#FFD600]/20 text-[#B89000] dark:text-[#FFD600] text-[10px] font-black flex items-center justify-center shrink-0 mt-0.5">
                                3
                            </div>
                            <div>
                                <span class="font-bold text-gray-900 dark:text-white block">On-Site Setup & Settlement</span>
                                <span class="text-gray-500 text-[11px]">Our crew installs everything cleanly and payment is done offline.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Direct Help Card -->
                <div class="p-5 rounded-3xl bg-emerald-500/10 border border-emerald-500/20 text-left flex items-center justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-heading font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">
                            NEED CUSTOM QUOTE?
                        </span>
                        <p class="text-xs text-gray-700 dark:text-gray-300 font-medium mt-0.5">
                            Speak directly with our Indore event styling specialist.
                        </p>
                    </div>
                    <a href="https://wa.me/919131668156?text={{ urlencode('Hi Artizen, I need assistance choosing an event package in Indore.') }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shrink-0 flex items-center gap-1.5 shadow-sm transition-all">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>WhatsApp</span>
                    </a>
                </div>

            </div>

        </div>

    </div>
</div>

<script>
    const packagesData = @json($packagesDb);
    const initialSelectedCategory = @json($selectedCat);
    const initialSelectedPkg = @json($selectedPkgName);
    const initialSelectedPrice = @json($selectedPrice);

    let currentSelectedCategory = initialSelectedCategory || Object.values(packagesData)[0]?.title || 'Adult Birthdays';
    let currentSelectedTier = null;

    /**
     * Change category selection and refresh available tiers
     */
    function selectCategory(catTitle) {
        currentSelectedCategory = catTitle;
        document.getElementById('form-category-input').value = catTitle;

        // Update category pill styles
        document.querySelectorAll('.category-pill').forEach(btn => {
            if (btn.dataset.category.toLowerCase() === catTitle.toLowerCase()) {
                btn.className = "category-pill px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 border bg-[#FFD600] text-[#171719] border-[#FFD600] shadow-sm";
            } else {
                btn.className = "category-pill px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 border bg-gray-50 dark:bg-white/[0.04] text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/10 hover:border-[#FFD600]/60";
            }
        });

        renderTiersForCategory(catTitle);
    }

    /**
     * Render the tier cards for the selected category
     */
    function renderTiersForCategory(catTitle) {
        const tiersContainer = document.getElementById('tiers-container');
        const dropdownSelect = document.getElementById('booking-package-select');
        tiersContainer.innerHTML = '';
        dropdownSelect.innerHTML = '';

        let matchedCategory = null;
        Object.values(packagesData).forEach(cat => {
            if (cat.title.toLowerCase() === catTitle.toLowerCase() || cat.title.toLowerCase().includes(catTitle.toLowerCase())) {
                matchedCategory = cat;
            }
        });

        const tiers = matchedCategory && matchedCategory.tiers && matchedCategory.tiers.length > 0 
            ? matchedCategory.tiers 
            : [{
                name: `${catTitle} Essential Setup`,
                price: 4999,
                desc: 'Classic decoration arch, warm lighting, and complete on-site logistics.',
                image: matchedCategory?.image || '/assets/images/hero/1.jpg',
                inclusions: ['Balloon Backdrop Arch', 'Cake Table Setup', 'Ambient Lighting', 'Setup & Packup Service']
            }];

        let matchedTierToSelect = null;

        tiers.forEach((tier, index) => {
            const fullTierName = `${catTitle} - ${tier.name}`;
            const price = tier.price || 4999;
            const image = tier.image || matchedCategory?.image || '/assets/images/hero/1.jpg';
            const desc = tier.desc || 'Complete celebration setup with premium props and lighting.';
            const inclusions = Array.isArray(tier.inclusions) ? tier.inclusions : ['Theme Decoration', 'On-Site Team', 'Sound & Lights'];

            // Determine if this tier was requested via query params or is first
            const isMatch = (initialSelectedPkg && (
                tier.name.toLowerCase().includes(initialSelectedPkg.toLowerCase()) || 
                initialSelectedPkg.toLowerCase().includes(tier.name.toLowerCase())
            ));

            if (isMatch && !matchedTierToSelect) {
                matchedTierToSelect = { fullTierName, name: tier.name, price, image, desc, inclusions, category: catTitle };
            }

            // Create Option in fallback dropdown
            const opt = document.createElement('option');
            opt.value = fullTierName;
            opt.dataset.price = price;
            opt.dataset.image = image;
            opt.dataset.desc = desc;
            opt.textContent = `${tier.name} — ₹${price.toLocaleString('en-IN')}`;
            dropdownSelect.appendChild(opt);

            // Create Tier Card
            const card = document.createElement('div');
            card.id = `tier-card-${index}`;
            card.className = `tier-card p-4 rounded-2xl border cursor-pointer transition-all duration-200 relative flex flex-col justify-between border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/[0.02] hover:border-[#FFD600]/60`;
            card.onclick = () => selectTier({ fullTierName, name: tier.name, price, image, desc, inclusions, category: catTitle, index });

            card.innerHTML = `
                <div>
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <span class="text-[10px] font-heading font-black tracking-wider uppercase text-gold block">
                                ${index === 0 ? 'STANDARD' : (index === 1 ? 'MOST POPULAR' : 'VIP LUXURY')}
                            </span>
                            <h4 class="font-heading font-black text-sm text-gray-900 dark:text-white leading-tight">
                                ${tier.name}
                            </h4>
                        </div>
                        <div class="w-6 h-6 rounded-full border border-gray-300 dark:border-white/20 flex items-center justify-center shrink-0 tier-radio-icon text-transparent text-xs">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>

                    <div class="flex items-baseline gap-1 my-2">
                        <span class="font-heading font-black text-xl text-gray-900 dark:text-white">
                            ₹${price.toLocaleString('en-IN')}
                        </span>
                        <span class="text-[10px] text-gray-500">all inclusive</span>
                    </div>

                    <p class="text-[11px] text-gray-500 dark:text-gray-400 line-clamp-2 leading-snug mb-3">
                        ${desc}
                    </p>

                    <div class="space-y-1 pt-2 border-t border-gray-200/60 dark:border-white/5">
                        ${inclusions.slice(0, 3).map(inc => `
                            <div class="flex items-center gap-1.5 text-[11px] text-gray-600 dark:text-gray-300">
                                <i class="fa-solid fa-check text-emerald-500 text-[10px] shrink-0"></i>
                                <span class="truncate">${inc}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;

            tiersContainer.appendChild(card);
        });

        // Default to first tier if none matched
        if (!matchedTierToSelect) {
            const firstTier = tiers[0];
            matchedTierToSelect = {
                fullTierName: `${catTitle} - ${firstTier.name}`,
                name: firstTier.name,
                price: firstTier.price || 4999,
                image: firstTier.image || matchedCategory?.image || '/assets/images/hero/1.jpg',
                desc: firstTier.desc || '',
                inclusions: Array.isArray(firstTier.inclusions) ? firstTier.inclusions : ['Theme Decoration', 'On-Site Team'],
                category: catTitle,
                index: 0
            };
        }

        selectTier(matchedTierToSelect);
    }

    /**
     * Select a specific tier and update UI & summary
     */
    function selectTier(tierObj) {
        currentSelectedTier = tierObj;

        // Update form hidden inputs
        document.getElementById('form-package-input').value = tierObj.fullTierName;
        document.getElementById('form-price-input').value = tierObj.price;
        document.getElementById('form-total-price-input').value = tierObj.price;

        // Update tier card highlight
        document.querySelectorAll('.tier-card').forEach((c, idx) => {
            const radioIcon = c.querySelector('.tier-radio-icon');
            if (tierObj.index !== undefined && idx === tierObj.index) {
                c.className = "tier-card p-4 rounded-2xl border cursor-pointer transition-all duration-200 relative flex flex-col justify-between border-[#FFD600] bg-[#FFD600]/10 ring-1 ring-[#FFD600]";
                if (radioIcon) {
                    radioIcon.className = "w-6 h-6 rounded-full bg-[#FFD600] text-[#171719] flex items-center justify-center shrink-0 tier-radio-icon text-xs shadow-sm";
                }
            } else {
                c.className = "tier-card p-4 rounded-2xl border cursor-pointer transition-all duration-200 relative flex flex-col justify-between border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/[0.02] hover:border-[#FFD600]/60";
                if (radioIcon) {
                    radioIcon.className = "w-6 h-6 rounded-full border border-gray-300 dark:border-white/20 flex items-center justify-center shrink-0 tier-radio-icon text-transparent text-xs";
                }
            }
        });

        // Sync dropdown
        const dropdown = document.getElementById('booking-package-select');
        for (let i = 0; i < dropdown.options.length; i++) {
            if (dropdown.options[i].value === tierObj.fullTierName) {
                dropdown.selectedIndex = i;
                break;
            }
        }

        // Update Summary Card
        document.getElementById('summary-package-title').innerText = tierObj.name;
        document.getElementById('summary-category-badge').innerText = tierObj.category;
        document.getElementById('summary-tier-desc').innerText = tierObj.desc || `${tierObj.category} celebration setup`;
        document.getElementById('summary-tier-img').src = tierObj.image || '/assets/images/hero/1.jpg';
        document.getElementById('summary-base-price').innerText = `₹${tierObj.price.toLocaleString('en-IN')}`;
        document.getElementById('summary-total-price').innerText = `₹${tierObj.price.toLocaleString('en-IN')}`;

        // Inclusions List in Summary
        const incList = document.getElementById('summary-inclusions-list');
        incList.innerHTML = '';
        (tierObj.inclusions || []).slice(0, 4).forEach(inc => {
            const li = document.createElement('li');
            li.className = 'flex items-center gap-2';
            li.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-500 text-[10px] shrink-0"></i> <span>${inc}</span>`;
            incList.appendChild(li);
        });

        updateScheduleSummary();
    }

    /**
     * Handle manual change from the alternative dropdown
     */
    function handleDropdownPackageChange(fullTierName) {
        const dropdown = document.getElementById('booking-package-select');
        const selectedOpt = dropdown.options[dropdown.selectedIndex];
        if (!selectedOpt) return;

        const price = parseInt(selectedOpt.dataset.price) || 4999;
        const image = selectedOpt.dataset.image || '/assets/images/hero/1.jpg';
        const desc = selectedOpt.dataset.desc || '';

        selectTier({
            fullTierName: fullTierName,
            name: selectedOpt.text.split('—')[0].trim(),
            price: price,
            image: image,
            desc: desc,
            inclusions: ['Complete Theme Setup', 'Ambient Lighting', 'Sound System', 'Free Logistics'],
            category: currentSelectedCategory
        });
    }

    /**
     * Time Slot Picker
     */
    function selectTimeSlot(slotValue) {
        document.getElementById('event_time_input').value = slotValue;
        document.querySelectorAll('.time-slot-card').forEach(btn => {
            if (btn.dataset.slot === slotValue) {
                btn.className = "time-slot-card p-3 rounded-2xl border text-left transition-all relative flex flex-col justify-between border-[#FFD600] bg-[#FFD600]/10 ring-1 ring-[#FFD600]";
            } else {
                btn.className = "time-slot-card p-3 rounded-2xl border text-left transition-all relative flex flex-col justify-between border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/[0.04] hover:border-[#FFD600]/50";
            }
        });
        updateScheduleSummary();
    }

    /**
     * Quick Date Helpers
     */
    function setQuickDate(daysAhead) {
        const d = new Date();
        d.setDate(d.getDate() + daysAhead);
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        document.getElementById('event_date_input').value = `${yyyy}-${mm}-${dd}`;
        updateScheduleSummary();
    }

    function setNextWeekend() {
        const d = new Date();
        const day = d.getDay();
        // Days until next Saturday (day 6)
        const diff = (6 - day + 7) % 7 || 7;
        d.setDate(d.getDate() + diff);
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        document.getElementById('event_date_input').value = `${yyyy}-${mm}-${dd}`;
        updateScheduleSummary();
    }

    /**
     * Guest Count Steppers
     */
    function adjustGuestCount(delta) {
        const input = document.getElementById('guest_count_input');
        let current = parseInt(input.value) || 30;
        current = Math.max(5, Math.min(2000, current + delta));
        input.value = current;
        updateScheduleSummary();
    }

    function setGuestCount(count) {
        document.getElementById('guest_count_input').value = count;
        updateScheduleSummary();
    }

    /**
     * Area autofill
     */
    function setArea(areaName, pincode) {
        document.getElementById('venue_area_input').value = areaName;
        if (pincode) {
            document.getElementById('venue_pincode_input').value = pincode;
        }
    }

    /**
     * Mobile <-> WhatsApp Auto-sync
     */
    function handleMobileInput(val) {
        const sameAsMobileCheck = document.getElementById('same_as_mobile_check');
        if (sameAsMobileCheck && sameAsMobileCheck.checked) {
            document.getElementById('customer_whatsapp_input').value = val;
        }
    }

    function toggleSameAsMobile(isChecked) {
        const whatsappInput = document.getElementById('customer_whatsapp_input');
        const mobileInput = document.getElementById('customer_mobile_input');
        if (isChecked) {
            whatsappInput.value = mobileInput.value;
        }
    }

    /**
     * Keep summary live updated
     */
    function updateScheduleSummary() {
        const dateVal = document.getElementById('event_date_input').value;
        const timeVal = document.getElementById('event_time_input').value;
        const guestVal = document.getElementById('guest_count_input').value || 30;

        let formattedDate = 'Upcoming Date';
        if (dateVal) {
            const parts = dateVal.split('-');
            if (parts.length === 3) {
                const dObj = new Date(parts[0], parts[1] - 1, parts[2]);
                formattedDate = dObj.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
            }
        }

        let timeShort = timeVal.split('(')[0].trim();
        document.getElementById('summary-date-time').innerText = `${formattedDate} • ${timeShort}`;
        document.getElementById('summary-guest-count').innerText = `${guestVal} Guests`;
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        selectCategory(currentSelectedCategory);
        updateScheduleSummary();
    });
</script>
@endsection

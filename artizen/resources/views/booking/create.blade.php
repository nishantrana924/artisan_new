@extends('layouts.app')

@section('title', 'Checkout - ARTIZEN Indore')

@section('content')
<div class="min-h-screen pt-4 pb-16 px-4 md:px-8 lg:px-12 bg-[#FAF9F5] text-main-text">
    <div class="max-w-6xl mx-auto">

        <!-- Breadcrumbs -->
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-[#777777] mb-4 font-medium">
            <a href="{{ route('home') }}" class="hover:text-black transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-[#CCCCCC]"></i>
            <a href="{{ route('events.index') }}" class="hover:text-black transition-colors">Packages</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-[#CCCCCC]"></i>
            <span class="text-main-text font-bold">Checkout</span>
        </nav>

        <!-- Compact Header -->
        <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[#E8E5DF] pb-3 text-left">
            <div>
                <h1 class="font-heading font-extrabold text-xl sm:text-2xl text-main-text tracking-tight">
                    Event Booking Checkout
                </h1>
                <p class="text-xs text-[#777777] mt-0.5">Indore On-Site Setup • Pay Offline Post-Setup • Zero Advance</p>
            </div>
            <div class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-neutral-700 bg-white border border-[#E8E5DF] px-3 py-1 rounded-full self-start sm:self-auto">
                <i class="fa-solid fa-shield-halved text-neutral-800 text-xs"></i> 100% Offline Settlement
            </div>
        </div>

        <!-- Validation Errors -->
        @if (isset($errors) && $errors->any())
            <div class="mb-5 p-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium text-left">
                <div class="font-bold mb-1">Please check the following fields:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form & Sidebar Grid -->
        <form action="{{ route('booking.save') }}" method="POST" id="artizen-checkout-form">
            @csrf

            <!-- Hidden Inputs -->
            <input type="hidden" name="package" id="input-package-name" value="{{ $matchedPackage['title'] ?? 'Event Package' }}">
            <input type="hidden" name="category" id="input-category-name" value="{{ $matchedPackage['category_name'] ?? 'Celebrations' }}">
            <input type="hidden" name="tier" id="input-tier-name" value="{{ $matchedPackage['tiers'][$selectedTierIndex]['name'] ?? 'Standard Setup' }}">
            <input type="hidden" name="price" id="input-base-price" value="{{ $matchedPackage['tiers'][$selectedTierIndex]['price'] ?? 4999 }}">
            <input type="hidden" name="addons" id="input-addons-json" value="{{ json_encode($preselectedAddonIds ?? []) }}">
            <input type="hidden" name="total_price" id="input-total-price" value="{{ $matchedPackage['tiers'][$selectedTierIndex]['price'] ?? 4999 }}">
            <input type="hidden" name="city" value="Indore">
            <input type="hidden" name="state" value="Madhya Pradesh">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start text-left">
                
                <!-- Main Form Column (8 Cols) -->
                <div class="lg:col-span-8 flex flex-col gap-4">

                    <!-- STEP 1: Address & Contact -->
                    <div class="border border-[#E8E5DF] bg-white p-4 sm:p-5 rounded-xl shadow-2xs">
                        <div class="flex items-center gap-2 mb-3 pb-2 border-b border-[#E8E5DF]">
                            <span class="checkout-step-num w-5.5 h-5.5 rounded-full bg-neutral-900 text-white text-[11px] font-extrabold flex items-center justify-center shrink-0" style="color: #ffffff !important; background-color: #18181b !important;">1</span>
                            <h2 class="font-heading font-bold text-sm text-main-text tracking-tight">Delivery Address &amp; Contact</h2>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Full Name -->
                            <div>
                                <label for="customer_name" class="block text-xs font-semibold text-[#333333] mb-1">Full Name *</label>
                                <input type="text" name="name" id="customer_name" value="{{ old('name', $user->name ?? (Auth::user()->name ?? session('user_name', ''))) }}" required placeholder="e.g. Rohan Sharma"
                                    class="w-full px-3 py-2 bg-white border border-[#D4D4D8] rounded-lg text-xs text-main-text focus:outline-none focus:border-neutral-900 transition-colors">
                            </div>

                            <!-- Mobile Number -->
                            <div>
                                <label for="customer_mobile" class="block text-xs font-semibold text-[#333333] mb-1">Mobile Number *</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-2.5 rounded-l-lg border border-r-0 border-[#D4D4D8] bg-neutral-50 text-[#555555] text-xs font-bold">+91</span>
                                    <input type="tel" name="mobile" id="customer_mobile" value="{{ old('mobile', $user->mobile ?? ($user->phone ?? (Auth::user()->mobile ?? (Auth::user()->phone ?? '')))) }}" required maxlength="10" placeholder="98260XXXXX"
                                        oninput="syncWhatsAppNumber(this.value)"
                                        class="w-full px-3 py-2 bg-white border border-[#D4D4D8] rounded-r-lg text-xs text-main-text focus:outline-none focus:border-neutral-900 transition-colors">
                                </div>
                            </div>

                            <!-- WhatsApp -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label for="customer_whatsapp" class="text-xs font-semibold text-[#333333]">WhatsApp Number *</label>
                                    <label class="flex items-center gap-1 cursor-pointer text-[11px] text-[#666666] select-none">
                                        <input type="checkbox" id="same_as_mobile_check" checked onchange="toggleSameAsMobile(this.checked)" class="rounded text-neutral-900 focus:ring-0">
                                        <span>Same as mobile</span>
                                    </label>
                                </div>
                                <div class="flex">
                                    <span class="inline-flex items-center px-2.5 rounded-l-lg border border-r-0 border-[#D4D4D8] bg-neutral-50 text-[#555555] text-xs font-bold">+91</span>
                                    <input type="tel" name="whatsapp" id="customer_whatsapp" value="{{ old('whatsapp', $user->whatsapp ?? ($user->mobile ?? ($user->phone ?? (Auth::user()->whatsapp ?? (Auth::user()->mobile ?? ''))))) }}" required maxlength="10" placeholder="98260XXXXX"
                                        class="w-full px-3 py-2 bg-white border border-[#D4D4D8] rounded-r-lg text-xs text-main-text focus:outline-none focus:border-neutral-900 transition-colors">
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="customer_email" class="block text-xs font-semibold text-[#333333] mb-1">Email Address *</label>
                                <input type="email" name="email" id="customer_email" value="{{ old('email', $user->email ?? (Auth::user()->email ?? session('user_email', ''))) }}" required placeholder="name@example.com"
                                    class="w-full px-3 py-2 bg-white border border-[#D4D4D8] rounded-lg text-xs text-main-text focus:outline-none focus:border-neutral-900 transition-colors">
                            </div>

                            <!-- Venue Address -->
                            <div class="sm:col-span-2">
                                <label for="venue_address" class="block text-xs font-semibold text-[#333333] mb-1">Venue Address (House / Flat / Venue Name) *</label>
                                <input type="text" name="address" id="venue_address" value="{{ old('address') }}" required placeholder="e.g. Flat 302, Silver Heights / Banquet Hall name"
                                    class="w-full px-3 py-2 bg-white border border-[#D4D4D8] rounded-lg text-xs text-main-text focus:outline-none focus:border-neutral-900 transition-colors">
                            </div>

                            <!-- Area & Landmark -->
                            <div>
                                <label for="venue_area" class="block text-xs font-semibold text-[#333333] mb-1">Area / Locality in Indore *</label>
                                <input type="text" name="area" id="venue_area" value="{{ old('area', 'Vijay Nagar') }}" required placeholder="e.g. Vijay Nagar, Palasia"
                                    class="w-full px-3 py-2 bg-white border border-[#D4D4D8] rounded-lg text-xs text-main-text focus:outline-none focus:border-neutral-900 transition-colors">
                                <!-- Subtle Quick Locality Pills -->
                                <div class="flex flex-wrap gap-1 mt-1.5">
                                    @foreach(['Vijay Nagar', 'Palasia', 'Saket', 'Nipania', 'Bhawarkua', 'Rau'] as $ar)
                                        <button type="button" onclick="autofillArea('{{ $ar }}')"
                                            class="px-2 py-0.5 text-[10px] font-medium bg-[#FAF9F5] hover:bg-neutral-100 text-[#444444] border border-[#E8E5DF] rounded cursor-pointer">
                                            {{ $ar }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label for="venue_landmark" class="block text-xs font-semibold text-[#333333] mb-1">Landmark (Optional)</label>
                                <input type="text" name="landmark" id="venue_landmark" value="{{ old('landmark') }}" placeholder="e.g. Near C21 Mall / Club House"
                                    class="w-full px-3 py-2 bg-white border border-[#D4D4D8] rounded-lg text-xs text-main-text focus:outline-none focus:border-neutral-900 transition-colors">
                            </div>

                            <!-- Pincode & City -->
                            <div class="sm:col-span-2 flex items-center gap-3 pt-1">
                                <div class="w-36 shrink-0">
                                    <label for="venue_pincode" class="block text-xs font-semibold text-[#333333] mb-1">Pincode *</label>
                                    <input type="text" name="pincode" id="venue_pincode" value="{{ old('pincode', '452010') }}" required maxlength="6" placeholder="452010"
                                        class="w-full px-3 py-2 bg-white border border-[#D4D4D8] rounded-lg text-xs text-main-text focus:outline-none focus:border-neutral-900 transition-colors">
                                </div>
                                <div class="text-xs text-[#777777] font-medium pt-5">
                                    <span class="inline-flex items-center gap-1 bg-[#FAF9F5] border border-[#E8E5DF] px-2.5 py-1 rounded-md text-[#444444]">
                                        <i class="fa-solid fa-location-dot text-neutral-700 text-[10px]"></i> Indore, Madhya Pradesh
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Event Date & Timing -->
                    <div class="border border-[#E8E5DF] bg-white p-4 sm:p-5 rounded-xl shadow-2xs">
                        <div class="flex items-center gap-2 mb-3 pb-2 border-b border-[#E8E5DF]">
                            <span class="checkout-step-num w-5.5 h-5.5 rounded-full bg-neutral-900 text-white text-[11px] font-extrabold flex items-center justify-center shrink-0" style="color: #ffffff !important; background-color: #18181b !important;">2</span>
                            <h2 class="font-heading font-bold text-sm text-main-text tracking-tight">Event Schedule &amp; Timing</h2>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <!-- Event Date -->
                            <div>
                                <label for="event_date" class="block text-xs font-semibold text-[#333333] mb-1">Event Date *</label>
                                <input type="date" name="event_date" id="event_date" value="{{ old('event_date', date('Y-m-d', strtotime('+2 days'))) }}" min="{{ date('Y-m-d') }}" required
                                    class="w-full px-3 py-2 bg-white border border-[#D4D4D8] rounded-lg text-xs text-main-text focus:outline-none focus:border-neutral-900 transition-colors">
                                <div class="flex gap-1.5 mt-1.5">
                                    <button type="button" onclick="setQuickDate(1)" class="px-2 py-0.5 text-[10px] bg-[#FAF9F5] hover:bg-neutral-100 border border-[#E8E5DF] rounded text-[#444444] cursor-pointer">Tomorrow</button>
                                    <button type="button" onclick="setQuickDate(3)" class="px-2 py-0.5 text-[10px] bg-[#FAF9F5] hover:bg-neutral-100 border border-[#E8E5DF] rounded text-[#444444] cursor-pointer">In 3 Days</button>
                                    <button type="button" onclick="setNextWeekend()" class="px-2 py-0.5 text-[10px] bg-[#FAF9F5] hover:bg-neutral-100 border border-[#E8E5DF] rounded text-[#444444] cursor-pointer">Weekend</button>
                                </div>
                            </div>

                            <!-- Guest Count -->
                            <div>
                                <label for="guest_count" class="block text-xs font-semibold text-[#333333] mb-1">Guest Count *</label>
                                <div class="flex items-center gap-1.5">
                                    <button type="button" onclick="adjustGuestCount(-5)" class="w-8 h-8 rounded-lg bg-neutral-100 border border-[#D4D4D8] text-main-text font-bold hover:bg-neutral-200 flex items-center justify-center cursor-pointer shrink-0">
                                        <i class="fa-solid fa-minus text-[10px]"></i>
                                    </button>
                                    <input type="number" name="guest_count" id="guest_count" value="{{ old('guest_count', 30) }}" min="5" max="5000" required
                                        class="w-full text-center py-2 bg-white border border-[#D4D4D8] rounded-lg text-xs font-bold text-main-text focus:outline-none focus:border-neutral-900">
                                    <button type="button" onclick="adjustGuestCount(5)" class="w-8 h-8 rounded-lg bg-neutral-100 border border-[#D4D4D8] text-main-text font-bold hover:bg-neutral-200 flex items-center justify-center cursor-pointer shrink-0">
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                    </button>
                                </div>
                                <div class="flex gap-1.5 mt-1.5">
                                    <button type="button" onclick="setGuests(15)" class="px-2 py-0.5 text-[10px] bg-[#FAF9F5] hover:bg-neutral-100 border border-[#E8E5DF] rounded text-[#444444] cursor-pointer">15</button>
                                    <button type="button" onclick="setGuests(30)" class="px-2 py-0.5 text-[10px] bg-[#FAF9F5] hover:bg-neutral-100 border border-[#E8E5DF] rounded text-[#444444] cursor-pointer">30</button>
                                    <button type="button" onclick="setGuests(50)" class="px-2 py-0.5 text-[10px] bg-[#FAF9F5] hover:bg-neutral-100 border border-[#E8E5DF] rounded text-[#444444] cursor-pointer">50</button>
                                    <button type="button" onclick="setGuests(100)" class="px-2 py-0.5 text-[10px] bg-[#FAF9F5] hover:bg-neutral-100 border border-[#E8E5DF] rounded text-[#444444] cursor-pointer">100+</button>
                                </div>
                            </div>
                        </div>

                        <!-- Setup Slot -->
                        <div>
                            <label class="block text-xs font-semibold text-[#333333] mb-1.5">Preferred Setup Window *</label>
                            <input type="hidden" name="event_time" id="input-event-time" value="{{ old('event_time', 'Evening (04:00 PM - 08:00 PM)') }}">
                            
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2" id="setup-slots-container">
                                @php
                                    $slots = [
                                        ['label' => 'Morning (09:00 AM - 12:00 PM)', 'title' => 'Morning', 'time' => '9 AM – 12 PM'],
                                        ['label' => 'Afternoon (12:00 PM - 04:00 PM)', 'title' => 'Afternoon', 'time' => '12 PM – 4 PM'],
                                        ['label' => 'Evening (04:00 PM - 08:00 PM)', 'title' => 'Evening', 'time' => '4 PM – 8 PM'],
                                        ['label' => 'Night (08:00 PM - Midnight)', 'title' => 'Night', 'time' => '8 PM – 12 AM'],
                                    ];
                                    $currentSlot = old('event_time', 'Evening (04:00 PM - 08:00 PM)');
                                @endphp

                                @foreach($slots as $s)
                                    @php $isSelected = ($currentSlot === $s['label']); @endphp
                                    <button type="button" onclick="selectSlot('{{ $s['label'] }}')" data-slot="{{ $s['label'] }}"
                                        class="slot-btn py-2 px-2.5 rounded-lg border text-left transition-all cursor-pointer {{ $isSelected ? 'border-neutral-900 bg-neutral-900 text-white' : 'border-[#E8E5DF] bg-[#FAF9F5] text-main-text hover:border-neutral-400' }}">
                                        <span class="font-bold text-xs block leading-tight">{{ $s['title'] }}</span>
                                        <span class="text-[10px] block opacity-80 mt-0.5">{{ $s['time'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: Selected Package & Add-ons -->
                    <div class="border border-[#E8E5DF] bg-white p-4 sm:p-5 rounded-xl shadow-2xs">
                        <div class="flex items-center gap-2 mb-3 pb-2 border-b border-[#E8E5DF]">
                            <span class="checkout-step-num w-5.5 h-5.5 rounded-full bg-neutral-900 text-white text-[11px] font-extrabold flex items-center justify-center shrink-0" style="color: #ffffff !important; background-color: #18181b !important;">3</span>
                            <h2 class="font-heading font-bold text-sm text-main-text tracking-tight">Package &amp; Optional Add-ons</h2>
                        </div>

                        <!-- Compact Package Row -->
                        <div class="p-3 rounded-lg bg-[#FAF9F5] border border-[#E8E5DF] flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between mb-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-neutral-200 border border-[#E8E5DF] shrink-0">
                                    <img id="card-pkg-img" src="{{ $matchedPackage['image'] ?? '/assets/images/hero/1.jpg' }}" alt="Package" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-xs sm:text-sm text-main-text truncate" id="card-pkg-title">
                                            {{ $matchedPackage['title'] ?? 'Event Setup' }}
                                        </h3>
                                        <a href="{{ route('events.index') }}" class="text-[10px] text-neutral-500 hover:text-black underline font-medium whitespace-nowrap">
                                            Change
                                        </a>
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="text-[10px] text-[#666666]">Tier:</span>
                                        <select id="package-tier-select" onchange="onTierChanged(this.value)" class="text-xs font-semibold py-0.5 px-2 rounded border border-[#D4D4D8] bg-white text-main-text focus:outline-none cursor-pointer">
                                            @foreach($matchedPackage['tiers'] ?? [] as $tIdx => $t)
                                                <option value="{{ $tIdx }}" {{ $tIdx == $selectedTierIndex ? 'selected' : '' }} data-price="{{ $t['price'] }}" data-name="{{ $t['name'] }}">
                                                    {{ $t['name'] }} (₹{{ number_format($t['price']) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="text-left sm:text-right shrink-0">
                                <span class="text-[10px] text-[#777777] block">Base Price</span>
                                <span id="card-tier-price" class="font-bold text-base text-main-text">
                                    ₹{{ number_format($matchedPackage['tiers'][$selectedTierIndex]['price'] ?? 4999) }}
                                </span>
                            </div>
                        </div>

                        <!-- Compact Add-on Rows (No giant descriptions) -->
                        <div class="mb-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-main-text">Add-ons (Optional)</span>
                                <span id="addons-count-badge" class="text-[11px] font-semibold text-[#666666]">0 Selected</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2" id="addons-grid-container">
                                @foreach($masterAddons as $addon)
                                    @php $isPreselected = in_array($addon['id'], $preselectedAddonIds); @endphp
                                    <div class="addon-card px-3 py-2 rounded-lg border transition-all flex items-center justify-between gap-2 {{ $isPreselected ? 'border-neutral-900 bg-neutral-900/5' : 'border-[#E8E5DF] bg-[#FAF9F5]' }}"
                                         id="addon-row-{{ $addon['id'] }}" data-id="{{ $addon['id'] }}">
                                        <div class="min-w-0">
                                            <div class="font-medium text-xs text-main-text truncate">{{ $addon['name'] }}</div>
                                            <div class="text-[11px] font-bold text-main-text">+₹{{ number_format($addon['price']) }}</div>
                                        </div>
                                        <button type="button" onclick="toggleAddon('{{ $addon['id'] }}')" id="addon-btn-{{ $addon['id'] }}"
                                            class="px-2.5 py-1 rounded text-xs font-bold shrink-0 transition-colors cursor-pointer {{ $isPreselected ? 'bg-neutral-900 text-white' : 'bg-white hover:bg-neutral-100 text-main-text border border-[#D4D4D8]' }}">
                                            {{ $isPreselected ? 'Added ✓' : '+ Add' }}
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Special Requests / Notes -->
                        <div>
                            <label for="booking_notes" class="block text-xs font-semibold text-[#333333] mb-1">Special Instructions (Optional)</label>
                            <input type="text" name="notes" id="booking_notes" value="{{ old('notes') }}"
                                placeholder="e.g. Balloon color preference, terrace setup, or call before arrival"
                                class="w-full px-3 py-2 bg-white border border-[#D4D4D8] rounded-lg text-xs text-main-text focus:outline-none focus:border-neutral-900 transition-colors">
                        </div>
                    </div>

                    <!-- STEP 4: Payment Method (Ultra Compact) -->
                    <div class="border border-[#E8E5DF] bg-white p-3.5 sm:p-4 rounded-xl shadow-2xs">
                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-[#E8E5DF]">
                            <span class="checkout-step-num w-5.5 h-5.5 rounded-full bg-neutral-900 text-white text-[11px] font-extrabold flex items-center justify-center shrink-0" style="color: #ffffff !important; background-color: #18181b !important;">4</span>
                            <h2 class="font-heading font-bold text-sm text-main-text tracking-tight">Payment Method</h2>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <input type="radio" checked disabled class="text-neutral-900 focus:ring-0">
                            <div>
                                <span class="font-bold text-xs text-main-text">Pay Offline Post-Setup (Cash or UPI)</span>
                                <span class="text-[11px] text-[#666666] block">₹0 due today. Settle on event day once our team finishes your setup to satisfaction.</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column (4 Cols): Price Details Sidebar -->
                <div class="lg:col-span-4 lg:sticky lg:top-20 flex flex-col gap-3">
                    
                    <div class="border border-[#E8E5DF] bg-white p-4 sm:p-5 rounded-xl shadow-sm text-left">
                        <h3 class="font-bold text-xs uppercase tracking-wider text-[#777777] pb-2.5 border-b border-[#E8E5DF]">
                            Price Details
                        </h3>

                        <div class="space-y-2.5 pt-3 text-xs text-[#555555]">
                            <div class="flex items-center justify-between">
                                <span>Base Package (<span id="summary-tier-name">{{ $matchedPackage['tiers'][$selectedTierIndex]['name'] ?? 'Standard' }}</span>):</span>
                                <span id="summary-base-price" class="font-bold text-main-text">
                                    ₹{{ number_format($matchedPackage['tiers'][$selectedTierIndex]['price'] ?? 4999) }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between" id="summary-addons-row">
                                <span>Add-ons (<span id="summary-addons-count">0</span> items):</span>
                                <span id="summary-addons-total" class="font-bold text-main-text">₹0</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span>Indore Setup &amp; Delivery:</span>
                                <span class="font-bold text-emerald-700">FREE</span>
                            </div>

                            <div class="flex items-center justify-between text-[#777777]">
                                <span>Advance Due Today:</span>
                                <span class="font-bold text-neutral-800">₹0</span>
                            </div>

                            <div class="border-t border-[#E8E5DF] pt-3 flex items-baseline justify-between text-main-text">
                                <div>
                                    <span class="font-bold text-xs uppercase tracking-wider block">Total Amount</span>
                                    <span class="text-[10px] text-[#777777]">Pay offline after setup</span>
                                </div>
                                <span id="summary-grand-total" class="font-heading font-extrabold text-2xl text-main-text">
                                    ₹{{ number_format($matchedPackage['tiers'][$selectedTierIndex]['price'] ?? 4999) }}
                                </span>
                            </div>
                        </div>

                        <!-- Place Order Button -->
                        <div class="mt-4 pt-3 border-t border-[#E8E5DF]">
                            <button type="submit" id="submit-booking-btn"
                                class="w-full py-3 bg-neutral-900 hover:bg-black text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-sm transition-all cursor-pointer">
                                Place Booking Request
                            </button>
                            <p class="text-[10px] text-[#888888] text-center mt-2">
                                Our coordinator will confirm date and slot via WhatsApp / Call.
                            </p>
                        </div>
                    </div>

                    <!-- Help Assistance Pill -->
                    <div class="p-3 rounded-lg border border-[#E8E5DF] bg-white flex items-center justify-between text-xs">
                        <span class="text-[#666666] font-medium">Need help with booking?</span>
                        <a href="https://wa.me/919131668156?text=Hi%20Artizen,%20I%20need%20help%20with%20booking" target="_blank"
                            class="font-bold text-main-text hover:underline inline-flex items-center gap-1">
                            <i class="fa-brands fa-whatsapp text-neutral-800 text-sm"></i> WhatsApp
                        </a>
                    </div>

                </div>

            </div>
        </form>

    </div>
</div>

<!-- Checkout Controller Script -->
<script>
    const packageData = @json($matchedPackage);
    const masterAddonsList = @json($masterAddons);
    let selectedTierIdx = {{ (int)$selectedTierIndex }};
    let selectedAddonIds = new Set(@json($preselectedAddonIds ?? []));

    // Client-side payload recovery if redirected from details page
    try {
        const stored = sessionStorage.getItem('artizen_checkout_pkg');
        if (stored) {
            const parsed = JSON.parse(stored);
            if (parsed && typeof parsed.tier_index !== 'undefined' && parsed.tier_index !== null) {
                selectedTierIdx = parseInt(parsed.tier_index) || 0;
                const tierSelect = document.getElementById('package-tier-select');
                if (tierSelect) tierSelect.value = selectedTierIdx;
            }
            if (parsed && parsed.addons && Array.isArray(parsed.addons)) {
                parsed.addons.forEach(a => {
                    if (a.id) selectedAddonIds.add(a.id);
                });
            }
        }
    } catch(e) {}

    function onTierChanged(newIdx) {
        selectedTierIdx = parseInt(newIdx);
        updateOrderCalculation();
    }

    function toggleAddon(addonId) {
        if (selectedAddonIds.has(addonId)) {
            selectedAddonIds.delete(addonId);
        } else {
            selectedAddonIds.add(addonId);
        }
        updateAddonsUI();
        updateOrderCalculation();
    }

    function updateAddonsUI() {
        masterAddonsList.forEach(a => {
            const isAdded = selectedAddonIds.has(a.id);
            const row = document.getElementById(`addon-row-${a.id}`);
            const btn = document.getElementById(`addon-btn-${a.id}`);

            if (row) {
                row.className = isAdded 
                    ? 'addon-card px-3 py-2 rounded-lg border transition-all flex items-center justify-between gap-2 border-neutral-900 bg-neutral-900/5' 
                    : 'addon-card px-3 py-2 rounded-lg border transition-all flex items-center justify-between gap-2 border-[#E8E5DF] bg-[#FAF9F5]';
            }
            if (btn) {
                btn.className = isAdded 
                    ? 'px-2.5 py-1 rounded text-xs font-bold shrink-0 transition-colors cursor-pointer bg-neutral-900 text-white' 
                    : 'px-2.5 py-1 rounded text-xs font-bold shrink-0 transition-colors cursor-pointer bg-white hover:bg-neutral-100 text-main-text border border-[#D4D4D8]';
                btn.textContent = isAdded ? 'Added ✓' : '+ Add';
            }
        });

        const badge = document.getElementById('addons-count-badge');
        if (badge) badge.textContent = `${selectedAddonIds.size} Selected`;
    }

    function updateOrderCalculation() {
        const tier = (packageData.tiers && packageData.tiers[selectedTierIdx]) ? packageData.tiers[selectedTierIdx] : { name: 'Standard Setup', price: 4999 };
        const basePrice = parseInt(tier.price) || 4999;

        let addonsTotal = 0;
        selectedAddonIds.forEach(id => {
            const found = masterAddonsList.find(a => a.id === id);
            if (found) addonsTotal += parseInt(found.price) || 0;
        });

        const grandTotal = basePrice + addonsTotal;

        // Hidden inputs
        document.getElementById('input-package-name').value = packageData.title || 'Event Package';
        document.getElementById('input-category-name').value = packageData.category_name || 'Celebrations';
        document.getElementById('input-tier-name').value = tier.name;
        document.getElementById('input-base-price').value = basePrice;
        document.getElementById('input-addons-json').value = JSON.stringify(Array.from(selectedAddonIds));
        document.getElementById('input-total-price').value = grandTotal;

        // Visual elements
        document.getElementById('card-tier-price').textContent = `₹${basePrice.toLocaleString('en-IN')}`;
        document.getElementById('summary-tier-name').textContent = tier.name;
        document.getElementById('summary-base-price').textContent = `₹${basePrice.toLocaleString('en-IN')}`;
        document.getElementById('summary-addons-count').textContent = selectedAddonIds.size;
        document.getElementById('summary-addons-total').textContent = `₹${addonsTotal.toLocaleString('en-IN')}`;
        document.getElementById('summary-grand-total').textContent = `₹${grandTotal.toLocaleString('en-IN')}`;
    }

    function selectSlot(slotValue) {
        document.getElementById('input-event-time').value = slotValue;
        document.querySelectorAll('.slot-btn').forEach(btn => {
            if (btn.dataset.slot === slotValue) {
                btn.className = "slot-btn py-2 px-2.5 rounded-lg border text-left transition-all cursor-pointer border-neutral-900 bg-neutral-900 text-white";
            } else {
                btn.className = "slot-btn py-2 px-2.5 rounded-lg border text-left transition-all cursor-pointer border-[#E8E5DF] bg-[#FAF9F5] text-main-text hover:border-neutral-400";
            }
        });
    }

    function setQuickDate(daysAhead) {
        const d = new Date();
        d.setDate(d.getDate() + daysAhead);
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        document.getElementById('event_date').value = `${yyyy}-${mm}-${dd}`;
    }

    function setNextWeekend() {
        const d = new Date();
        const day = d.getDay();
        const diff = (6 - day + 7) % 7 || 7;
        d.setDate(d.getDate() + diff);
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        document.getElementById('event_date').value = `${yyyy}-${mm}-${dd}`;
    }

    function adjustGuestCount(delta) {
        const el = document.getElementById('guest_count');
        let current = parseInt(el.value) || 30;
        el.value = Math.max(5, Math.min(5000, current + delta));
    }

    function setGuests(num) {
        document.getElementById('guest_count').value = num;
    }

    function autofillArea(area) {
        document.getElementById('venue_area').value = area;
    }

    function syncWhatsAppNumber(val) {
        const sameAs = document.getElementById('same_as_mobile_check');
        if (sameAs && sameAs.checked) {
            document.getElementById('customer_whatsapp').value = val;
        }
    }

    function toggleSameAsMobile(isChecked) {
        const mobile = document.getElementById('customer_mobile').value;
        if (isChecked) {
            document.getElementById('customer_whatsapp').value = mobile;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateAddonsUI();
        updateOrderCalculation();

        const form = document.getElementById('artizen-checkout-form');
        if (form) {
            form.addEventListener('submit', () => {
                updateOrderCalculation();
            });
        }
    });
</script>
@endsection

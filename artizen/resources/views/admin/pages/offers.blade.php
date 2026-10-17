@extends('layouts.admin')

@section('page_title', 'Offers Section Manager')
@section('page_heading', 'Offers Section Manager')
@section('page_subheading', 'Manage homepage promotional discounts, voucher cards, and category offer coupons')

@section('content')

{{-- ===================================================================
     1. FLASH MESSAGES
     =================================================================== --}}
@if(session('success'))
    <div class="mb-5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-2xl flex items-center gap-2 shadow-2xs">
        <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if(session('error'))
    <div class="mb-5 px-4 py-3 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-2xl flex items-center gap-2 shadow-2xs">
        <i class="fa-solid fa-triangle-exclamation text-rose-500 text-sm"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

@php
    $offersData = $offersConfig ?? ($cms['offers'] ?? []);
    $isSectionActive = $offersData['is_active'] ?? true;
    $sectionTitle = $offersData['section_title'] ?? 'Save More with Offers';
    $offerItems = $offersData['items'] ?? [];
    $totalOffers = count($offerItems);
    $activeOffersCount = count(array_filter($offerItems, fn($o) => ($o['is_active'] ?? true)));
@endphp

{{-- ===================================================================
     2. PAGE HEADER BAR
     =================================================================== --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200/80 pb-5 mb-6">
    <div>
        <h2 class="text-xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2.5">
            <i class="fa-solid fa-tags text-indigo-600 text-lg"></i>
            <span>Offers Section Manager</span>
        </h2>
        <p class="text-xs text-gray-500 mt-1">Configure discount vouchers, category celebration perks, promotional terms, and visual icons.</p>
    </div>
    <div class="flex items-center gap-2.5">
        <button type="button" 
                onclick="addNewOffer()" 
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl shadow-xs hover:shadow transition-all cursor-pointer shrink-0">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Add New Offer</span>
        </button>
    </div>
</div>

{{-- ===================================================================
     3. SUMMARY METRIC CARDS
     =================================================================== --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 mb-6">
    <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Section Status</span>
            <span class="w-7 h-7 rounded-lg {{ $isSectionActive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center text-xs">
                <i class="fa-solid {{ $isSectionActive ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
            </span>
        </div>
        <span class="text-2xl font-extrabold {{ $isSectionActive ? 'text-emerald-600' : 'text-gray-400' }} block mt-2" id="metric-section-status">
            {{ $isSectionActive ? 'Enabled' : 'Disabled' }}
        </span>
        <span class="text-[11px] text-gray-400 mt-0.5 block">Homepage visibility</span>
    </div>

    <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total Coupons</span>
            <span class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 text-xs">
                <i class="fa-solid fa-ticket"></i>
            </span>
        </div>
        <span class="text-2xl font-extrabold text-gray-900 block mt-2" id="metric-total-offers">{{ $totalOffers }}</span>
        <span class="text-[11px] text-gray-400 mt-0.5 block">Configured offer cards</span>
    </div>

    <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Active Deals</span>
            <span class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 text-xs">
                <i class="fa-solid fa-circle-check"></i>
            </span>
        </div>
        <span class="text-2xl font-extrabold text-emerald-600 block mt-2" id="metric-active-offers">{{ $activeOffersCount }}</span>
        <span class="text-[11px] text-gray-400 mt-0.5 block">Visible to customers</span>
    </div>

    <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Icon Library</span>
            <span class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-xs">
                <i class="fa-solid fa-icons"></i>
            </span>
        </div>
        <span class="text-2xl font-extrabold text-indigo-600 block mt-2">FontAwesome</span>
        <span class="text-[11px] text-gray-400 mt-0.5 block">Visual icon picker</span>
    </div>
</div>

{{-- ===================================================================
     4. OFFERS SECTION FORM
     =================================================================== --}}
<form id="offers-form" action="{{ route('admin.offers.save') }}" method="POST" class="flex flex-col gap-6">
    @csrf

    {{-- Section Global Settings Card --}}
    <div class="bg-white border border-gray-200/80 rounded-2xl p-5 sm:p-6 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4 mb-5">
            <div>
                <h3 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-indigo-500"></i>
                    <span>Section Visibility & Title</span>
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Control the homepage section title and master visibility switch.</p>
            </div>
            <label class="inline-flex items-center gap-3 bg-gray-50 hover:bg-gray-100 px-4 py-2 rounded-xl border border-gray-200 cursor-pointer transition-colors select-none">
                <input type="checkbox" 
                       id="section-active-toggle" 
                       name="offers[is_active]" 
                       value="1" 
                       {{ $isSectionActive ? 'checked' : '' }} 
                       onchange="updateSectionStatusMetric(this)"
                       class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500">
                <span class="text-xs font-bold text-gray-800">Enable Offers Section on Homepage</span>
            </label>
        </div>

        <div class="flex flex-col gap-1.5 max-w-lg">
            <label class="text-[11px] text-gray-600 font-extrabold uppercase tracking-wider">Section Heading</label>
            <input type="text" 
                   name="offers[section_title]" 
                   value="{{ $sectionTitle }}" 
                   placeholder="e.g. Save More with Offers" 
                   required
                   class="w-full bg-white border border-gray-300 px-3.5 py-2.5 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900 transition-colors">
        </div>
    </div>

    {{-- Individual Offer Tickets Grid --}}
    <div class="bg-white border border-gray-200/80 rounded-2xl p-5 sm:p-6 shadow-2xs">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
            <div>
                <h3 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-ticket text-indigo-500"></i>
                    <span>Offer Vouchers & Coupons</span>
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Individual discount coupons displayed in the homepage 3-column ticket banner.</p>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl shadow-xs hover:shadow transition-all cursor-pointer flex items-center gap-2 shrink-0">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Save Offers</span>
            </button>
        </div>

        <div id="offers-container" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @foreach ($offerItems as $oIdx => $offer)
                <div class="offer-card-wrapper border border-gray-200/90 rounded-2xl p-4 sm:p-5 bg-gray-50/60 flex flex-col gap-3.5 relative shadow-2xs" data-offer-index="{{ $oIdx }}">
                    <div class="flex justify-between items-center border-b border-gray-200/80 pb-2.5">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-gray-900 text-white text-[11px] font-bold flex items-center justify-center offer-number-badge">
                                {{ $oIdx + 1 }}
                            </span>
                            <span class="text-xs text-gray-900 font-extrabold uppercase tracking-wide offer-label">Offer Ticket #{{ $oIdx + 1 }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="moveOfferUp(this)" title="Move Left / Earlier" class="w-7 h-7 rounded-lg bg-white hover:bg-gray-200 border border-gray-200 text-gray-600 text-xs flex items-center justify-center transition-colors cursor-pointer">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            </button>
                            <button type="button" onclick="moveOfferDown(this)" title="Move Right / Later" class="w-7 h-7 rounded-lg bg-white hover:bg-gray-200 border border-gray-200 text-gray-600 text-xs flex items-center justify-center transition-colors cursor-pointer">
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </button>
                            <button type="button" onclick="removeOffer(this)" title="Delete Offer" class="w-7 h-7 rounded-lg bg-white hover:bg-rose-50 border border-gray-200 text-rose-600 text-xs flex items-center justify-center transition-colors cursor-pointer ml-1">
                                <i class="fa-solid fa-trash-can text-[11px]"></i>
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="offers[items][{{ $oIdx }}][id]" value="{{ $offer['id'] ?? ($oIdx + 1) }}" class="offer-id-input">
                    <input type="hidden" name="offers[items][{{ $oIdx }}][display_order]" value="{{ $offer['display_order'] ?? ($oIdx + 1) }}" class="offer-order-input">

                    {{-- Visual Icon Picker Block --}}
                    <div class="flex items-center gap-3 bg-white p-3 rounded-xl border border-gray-200">
                        <div class="w-11 h-11 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center text-lg {{ $offer['icon_color'] ?? 'text-amber-600' }} shrink-0 icon-preview-box" id="icon-preview-{{ $oIdx }}">
                            <i class="{{ $offer['icon'] ?? 'fa-solid fa-cake-candles' }}"></i>
                        </div>
                        <div class="flex-grow flex flex-col gap-1">
                            <span class="text-[10px] text-gray-400 font-extrabold uppercase tracking-wider">Icon Identifier</span>
                            <div class="flex items-center gap-2">
                                <input type="text" 
                                       id="icon-input-{{ $oIdx }}" 
                                       name="offers[items][{{ $oIdx }}][icon]" 
                                       value="{{ $offer['icon'] ?? 'fa-solid fa-cake-candles' }}" 
                                       readonly 
                                       class="w-full text-xs font-mono font-bold text-gray-800 bg-transparent border-none p-0 outline-none">
                                <button type="button" 
                                        onclick="openIconPicker({{ $oIdx }})" 
                                        class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[11px] font-bold rounded-lg transition-colors cursor-pointer shrink-0 border border-indigo-200">
                                    <i class="fa-solid fa-magnifying-glass text-[10px]"></i> Pick Icon
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Icon Color & Category Name --}}
                    <div class="grid grid-cols-2 gap-2.5">
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] text-gray-600 font-extrabold uppercase tracking-wider">Icon Color</label>
                            <select name="offers[items][{{ $oIdx }}][icon_color]" 
                                    onchange="updateIconColorPreview(this, {{ $oIdx }})"
                                    class="w-full bg-white border border-gray-300 px-2.5 py-1.5 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900">
                                <option value="text-amber-600" {{ ($offer['icon_color'] ?? '') === 'text-amber-600' ? 'selected' : '' }}>Amber / Gold</option>
                                <option value="text-rose-600" {{ ($offer['icon_color'] ?? '') === 'text-rose-600' ? 'selected' : '' }}>Rose / Romantic</option>
                                <option value="text-purple-600" {{ ($offer['icon_color'] ?? '') === 'text-purple-600' ? 'selected' : '' }}>Purple / Party</option>
                                <option value="text-emerald-600" {{ ($offer['icon_color'] ?? '') === 'text-emerald-600' ? 'selected' : '' }}>Emerald / Haldi</option>
                                <option value="text-indigo-600" {{ ($offer['icon_color'] ?? '') === 'text-indigo-600' ? 'selected' : '' }}>Indigo / Premium</option>
                                <option value="text-sky-600" {{ ($offer['icon_color'] ?? '') === 'text-sky-600' ? 'selected' : '' }}>Sky / Daytime</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] text-gray-600 font-extrabold uppercase tracking-wider">Category Label</label>
                            <input type="text" 
                                   name="offers[items][{{ $oIdx }}][category_name]" 
                                   value="{{ $offer['category_name'] ?? 'Celebration Offer' }}" 
                                   placeholder="e.g. Birthdays & Parties" 
                                   class="w-full bg-white border border-gray-300 px-2.5 py-1.5 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900">
                        </div>
                    </div>

                    {{-- Top Small Subheading / Promo Code --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] text-gray-600 font-extrabold uppercase tracking-wider">Top Eyebrow / Discount Subheading</label>
                        <input type="text" 
                               name="offers[items][{{ $oIdx }}][subheading]" 
                               value="{{ $offer['subheading'] ?? '' }}" 
                               placeholder="e.g. FLAT ₹500 OFF" 
                               class="w-full bg-white border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900">
                    </div>

                    {{-- Main Discount Headline --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] text-gray-600 font-extrabold uppercase tracking-wider">Main Discount Headline</label>
                        <input type="text" 
                               name="offers[items][{{ $oIdx }}][discount_title]" 
                               value="{{ $offer['discount_title'] ?? '' }}" 
                               placeholder="e.g. Flat ₹500 Off on Theme Decor" 
                               required
                               class="w-full bg-white border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900">
                    </div>

                    {{-- Terms & Conditions --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] text-gray-600 font-extrabold uppercase tracking-wider">Terms & Conditions / Coupon Rules</label>
                        <input type="text" 
                               name="offers[items][{{ $oIdx }}][terms]" 
                               value="{{ $offer['terms'] ?? '' }}" 
                               placeholder="e.g. Min. booking ₹4,999 • Code: ARTIZEN500" 
                               class="w-full bg-white border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900">
                    </div>

                    {{-- Target Link URL --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] text-gray-600 font-extrabold uppercase tracking-wider">Destination Link URL</label>
                        <input type="text" 
                               name="offers[items][{{ $oIdx }}][link]" 
                               value="{{ $offer['link'] ?? '/events' }}" 
                               placeholder="e.g. /events?category=birthdays" 
                               class="w-full bg-white border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900">
                    </div>

                    {{-- Active Checkbox --}}
                    <div class="flex items-center justify-between border-t border-gray-200/80 pt-2.5 mt-1">
                        <label class="text-xs font-semibold text-gray-700 cursor-pointer flex items-center gap-2 select-none">
                            <input type="checkbox" 
                                   name="offers[items][{{ $oIdx }}][is_active]" 
                                   value="1" 
                                   {{ ($offer['is_active'] ?? true) ? 'checked' : '' }} 
                                   onchange="updateOfferCounts()"
                                   class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500">
                            <span>Active on Voucher Bar</span>
                        </label>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex items-center justify-between border-t border-gray-100 pt-5 mt-6">
            <button type="button" 
                    onclick="addNewOffer()" 
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Add Another Offer Ticket</span>
            </button>

            <button type="submit" 
                    class="px-6 py-2.5 bg-gray-900 hover:bg-black text-white rounded-xl text-xs font-extrabold uppercase tracking-wider transition-all cursor-pointer shadow-xs hover:shadow flex items-center gap-2">
                <i class="fa-solid fa-check text-xs"></i>
                <span>Save Offers Section</span>
            </button>
        </div>
    </div>
</form>

{{-- ===================================================================
     5. SEARCHABLE VISUAL ICON PICKER MODAL
     =================================================================== --}}
<div id="icon-picker-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-xs hidden transition-opacity">
    <div class="bg-white rounded-2xl max-w-xl w-full border border-gray-200 shadow-2xl flex flex-col max-h-[85vh] overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        {{-- Modal Header --}}
        <div class="p-4 sm:p-5 border-b border-gray-200 flex items-center justify-between bg-gray-50/50">
            <div>
                <h4 class="text-sm font-extrabold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-icons text-indigo-600"></i>
                    <span>Select Celebration Icon</span>
                </h4>
                <p class="text-xs text-gray-500 mt-0.5">Click any icon below to apply it to your offer ticket.</p>
            </div>
            <button type="button" onclick="closeIconPicker()" class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-200 flex items-center justify-center transition-colors cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        {{-- Search Input Bar --}}
        <div class="p-4 border-b border-gray-100 bg-white">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" 
                       id="icon-search-input" 
                       oninput="filterIcons(this.value)" 
                       placeholder="Search icons (e.g., cake, wedding, ring, gift, music, sound, tag, star, fire)..." 
                       class="w-full bg-gray-50 border border-gray-200 pl-9 pr-4 py-2 text-xs font-semibold text-gray-800 rounded-xl outline-none focus:border-gray-900 focus:bg-white transition-all">
            </div>
        </div>

        {{-- Icon Grid --}}
        <div class="p-4 sm:p-5 overflow-y-auto flex-1 grid grid-cols-3 sm:grid-cols-4 gap-2.5" id="icon-grid-container">
            @php
                $availableIcons = [
                    ['icon' => 'fa-solid fa-cake-candles',       'name' => 'Cake / Birthday',        'tags' => 'cake candles birthday celebration party dessert'],
                    ['icon' => 'fa-solid fa-ring',               'name' => 'Ring / Wedding',         'tags' => 'ring wedding proposal engagement anniversary stage'],
                    ['icon' => 'fa-solid fa-heart',              'name' => 'Heart / Romantic',       'tags' => 'heart love romance anniversary proposal canopy'],
                    ['icon' => 'fa-solid fa-compact-disc',       'name' => 'DJ Sound / CD',          'tags' => 'compact disc dj sound music bass concert club party'],
                    ['icon' => 'fa-solid fa-music',              'name' => 'Music Note',             'tags' => 'music note sound songs audio melody'],
                    ['icon' => 'fa-solid fa-champagne-glasses',  'name' => 'Champagne / Toast',      'tags' => 'champagne glasses toast drink party luxury'],
                    ['icon' => 'fa-solid fa-tag',                'name' => 'Price Tag',              'tags' => 'tag discount sale offer coupon voucher deal'],
                    ['icon' => 'fa-solid fa-tags',               'name' => 'Discount Tags',          'tags' => 'tags multiple sale voucher discount promo promo-code'],
                    ['icon' => 'fa-solid fa-gift',               'name' => 'Gift Box',               'tags' => 'gift present box surprise reward birthday celebration'],
                    ['icon' => 'fa-solid fa-percent',            'name' => 'Percent / Discount',     'tags' => 'percent percentage discount flat off deal saving'],
                    ['icon' => 'fa-solid fa-ticket',             'name' => 'Ticket / Pass',          'tags' => 'ticket pass voucher coupon access pass entry'],
                    ['icon' => 'fa-solid fa-star',               'name' => 'Star / Special',         'tags' => 'star favorite rating special premium featured'],
                    ['icon' => 'fa-solid fa-crown',              'name' => 'Crown / Royal',          'tags' => 'crown royal king queen luxury wedding mandap vip'],
                    ['icon' => 'fa-solid fa-wand-magic-sparkles','name' => 'Magic / Sparkles',       'tags' => 'wand magic sparkles decor lights fairy fairytale glow'],
                    ['icon' => 'fa-solid fa-fire',               'name' => 'Fire / Hot Deal',        'tags' => 'fire flame trending hot viral flash deal instant'],
                    ['icon' => 'fa-solid fa-gem',                'name' => 'Gem / Diamond',          'tags' => 'gem diamond jewel luxury crystal exclusive elite'],
                    ['icon' => 'fa-solid fa-camera',             'name' => 'Camera / Photo',         'tags' => 'camera photo photography memories album snapshot'],
                    ['icon' => 'fa-solid fa-bell',               'name' => 'Bell / Alert',           'tags' => 'bell notification alert reminder sound call'],
                    ['icon' => 'fa-solid fa-calendar-check',     'name' => 'Booking Confirmed',      'tags' => 'calendar date booking schedule confirmed event reserve'],
                    ['icon' => 'fa-solid fa-guitar',             'name' => 'Acoustic Guitar',        'tags' => 'guitar acoustic live music singer instrument song'],
                    ['icon' => 'fa-solid fa-bolt',               'name' => 'Flash / Lightning',      'tags' => 'bolt lightning flash instant speed quick rapid deal'],
                    ['icon' => 'fa-solid fa-sun',                'name' => 'Sun / Day Event',        'tags' => 'sun daylight haldi outdoor terrace poolside'],
                    ['icon' => 'fa-solid fa-moon',               'name' => 'Moon / Night Party',     'tags' => 'moon night rooftop evening cocktails strobe'],
                    ['icon' => 'fa-solid fa-sparkles',           'name' => 'Sparkles / Confetti',    'tags' => 'sparkles confetti celebration fireworks glitter'],
                ];
            @endphp

            @foreach ($availableIcons as $item)
                <button type="button" 
                        onclick="selectIcon('{{ $item['icon'] }}')" 
                        data-tags="{{ $item['tags'] }}" 
                        class="icon-grid-item flex flex-col items-center justify-center p-3 rounded-xl border border-gray-200 hover:border-indigo-600 hover:bg-indigo-50/50 transition-all cursor-pointer group text-center gap-1.5">
                    <span class="w-9 h-9 rounded-lg bg-gray-50 group-hover:bg-indigo-100 flex items-center justify-center text-gray-700 group-hover:text-indigo-600 text-base transition-colors">
                        <i class="{{ $item['icon'] }}"></i>
                    </span>
                    <span class="text-[11px] font-bold text-gray-800 line-clamp-1 group-hover:text-indigo-900">{{ $item['name'] }}</span>
                    <span class="text-[9px] font-mono text-gray-400 group-hover:text-indigo-500 line-clamp-1">{{ $item['icon'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- Modal Footer --}}
        <div class="p-3 sm:p-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
            <span class="text-xs text-gray-500 font-medium">Click any icon to save automatically</span>
            <button type="button" onclick="closeIconPicker()" class="px-4 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold rounded-lg transition-colors cursor-pointer">
                Close
            </button>
        </div>
    </div>
</div>

@endsection

@push('page_scripts')
<script>
    let activeIconTargetIndex = null;

    function openIconPicker(targetIdx) {
        activeIconTargetIndex = targetIdx;
        const modal = document.getElementById('icon-picker-modal');
        const searchInput = document.getElementById('icon-search-input');
        if (modal) modal.classList.remove('hidden');
        if (searchInput) {
            searchInput.value = '';
            filterIcons('');
            searchInput.focus();
        }
    }

    function closeIconPicker() {
        const modal = document.getElementById('icon-picker-modal');
        if (modal) modal.classList.add('hidden');
        activeIconTargetIndex = null;
    }

    function selectIcon(iconClass) {
        if (activeIconTargetIndex !== null) {
            const input = document.getElementById('icon-input-' + activeIconTargetIndex);
            const previewBox = document.getElementById('icon-preview-' + activeIconTargetIndex);
            if (input) input.value = iconClass;
            if (previewBox) {
                previewBox.innerHTML = `<i class="${iconClass}"></i>`;
            }
        }
        closeIconPicker();
    }

    function filterIcons(query) {
        const normalizedQuery = (query || '').toLowerCase().trim();
        const items = document.querySelectorAll('.icon-grid-item');
        items.forEach(item => {
            const tags = (item.getAttribute('data-tags') || '').toLowerCase();
            const text = item.textContent.toLowerCase();
            if (!normalizedQuery || tags.includes(normalizedQuery) || text.includes(normalizedQuery)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    function updateIconColorPreview(selectEl, idx) {
        const previewBox = document.getElementById('icon-preview-' + idx);
        if (!previewBox) return;

        // Strip previous color classes
        previewBox.classList.remove('text-amber-600', 'text-rose-600', 'text-purple-600', 'text-emerald-600', 'text-indigo-600', 'text-sky-600');
        previewBox.classList.add(selectEl.value);
    }

    function updateSectionStatusMetric(checkbox) {
        const metric = document.getElementById('metric-section-status');
        if (metric) {
            metric.textContent = checkbox.checked ? 'Enabled' : 'Disabled';
            if (checkbox.checked) {
                metric.className = 'text-2xl font-extrabold text-emerald-600 block mt-2';
            } else {
                metric.className = 'text-2xl font-extrabold text-gray-400 block mt-2';
            }
        }
    }

    function updateOfferCounts() {
        const container = document.getElementById('offers-container');
        const cards = container.querySelectorAll('.offer-card-wrapper');
        let activeCount = 0;

        cards.forEach((card, newIdx) => {
            card.setAttribute('data-offer-index', newIdx);
            const numBadge = card.querySelector('.offer-number-badge');
            const label = card.querySelector('.offer-label');
            if (numBadge) numBadge.textContent = newIdx + 1;
            if (label) label.textContent = 'Offer Ticket #' + (newIdx + 1);

            const orderInput = card.querySelector('.offer-order-input');
            if (orderInput) orderInput.value = newIdx + 1;

            const activeCheckbox = card.querySelector('input[type="checkbox"]');
            if (activeCheckbox && activeCheckbox.checked) {
                activeCount++;
            }
        });

        const totalMetric = document.getElementById('metric-total-offers');
        const activeMetric = document.getElementById('metric-active-offers');
        if (totalMetric) totalMetric.textContent = cards.length;
        if (activeMetric) activeMetric.textContent = activeCount;
    }

    function moveOfferUp(button) {
        const card = button.closest('.offer-card-wrapper');
        const prev = card.previousElementSibling;
        if (prev) {
            card.parentNode.insertBefore(card, prev);
            updateOfferCounts();
        }
    }

    function moveOfferDown(button) {
        const card = button.closest('.offer-card-wrapper');
        const next = card.nextElementSibling;
        if (next) {
            card.parentNode.insertBefore(next, card);
            updateOfferCounts();
        }
    }

    function removeOffer(button) {
        const card = button.closest('.offer-card-wrapper');
        const cardsCount = document.querySelectorAll('.offer-card-wrapper').length;
        if (cardsCount <= 1) {
            alert('At least one offer ticket is required.');
            return;
        }

        if (confirm('Are you sure you want to delete this offer ticket? This action will take effect once saved.')) {
            card.remove();
            updateOfferCounts();
        }
    }

    function addNewOffer() {
        const container = document.getElementById('offers-container');
        const newIdx = container.querySelectorAll('.offer-card-wrapper').length;

        const html = `
            <div class="offer-card-wrapper border border-gray-200/90 rounded-2xl p-4 sm:p-5 bg-gray-50/60 flex flex-col gap-3.5 relative shadow-2xs" data-offer-index="${newIdx}">
                <div class="flex justify-between items-center border-b border-gray-200/80 pb-2.5">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-gray-900 text-white text-[11px] font-bold flex items-center justify-center offer-number-badge">
                            ${newIdx + 1}
                        </span>
                        <span class="text-xs text-gray-900 font-extrabold uppercase tracking-wide offer-label">Offer Ticket #${newIdx + 1}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="moveOfferUp(this)" title="Move Left / Earlier" class="w-7 h-7 rounded-lg bg-white hover:bg-gray-200 border border-gray-200 text-gray-600 text-xs flex items-center justify-center transition-colors cursor-pointer">
                            <i class="fa-solid fa-chevron-left text-[10px]"></i>
                        </button>
                        <button type="button" onclick="moveOfferDown(this)" title="Move Right / Later" class="w-7 h-7 rounded-lg bg-white hover:bg-gray-200 border border-gray-200 text-gray-600 text-xs flex items-center justify-center transition-colors cursor-pointer">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </button>
                        <button type="button" onclick="removeOffer(this)" title="Delete Offer" class="w-7 h-7 rounded-lg bg-white hover:bg-rose-50 border border-gray-200 text-rose-600 text-xs flex items-center justify-center transition-colors cursor-pointer ml-1">
                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                        </button>
                    </div>
                </div>

                <input type="hidden" name="offers[items][${newIdx}][id]" value="${newIdx + 1}" class="offer-id-input">
                <input type="hidden" name="offers[items][${newIdx}][display_order]" value="${newIdx + 1}" class="offer-order-input">

                <div class="flex items-center gap-3 bg-white p-3 rounded-xl border border-gray-200">
                    <div class="w-11 h-11 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center text-lg text-amber-600 shrink-0 icon-preview-box" id="icon-preview-${newIdx}">
                        <i class="fa-solid fa-gift"></i>
                    </div>
                    <div class="flex-grow flex flex-col gap-1">
                        <span class="text-[10px] text-gray-400 font-extrabold uppercase tracking-wider">Icon Identifier</span>
                        <div class="flex items-center gap-2">
                            <input type="text" 
                                   id="icon-input-${newIdx}" 
                                   name="offers[items][${newIdx}][icon]" 
                                   value="fa-solid fa-gift" 
                                   readonly 
                                   class="w-full text-xs font-mono font-bold text-gray-800 bg-transparent border-none p-0 outline-none">
                            <button type="button" 
                                    onclick="openIconPicker(${newIdx})" 
                                    class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[11px] font-bold rounded-lg transition-colors cursor-pointer shrink-0 border border-indigo-200">
                                <i class="fa-solid fa-magnifying-glass text-[10px]"></i> Pick Icon
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] text-gray-600 font-extrabold uppercase tracking-wider">Icon Color</label>
                        <select name="offers[items][${newIdx}][icon_color]" 
                                onchange="updateIconColorPreview(this, ${newIdx})"
                                class="w-full bg-white border border-gray-300 px-2.5 py-1.5 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900">
                            <option value="text-amber-600" selected>Amber / Gold</option>
                            <option value="text-rose-600">Rose / Romantic</option>
                            <option value="text-purple-600">Purple / Party</option>
                            <option value="text-emerald-600">Emerald / Haldi</option>
                            <option value="text-indigo-600">Indigo / Premium</option>
                            <option value="text-sky-600">Sky / Daytime</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] text-gray-600 font-extrabold uppercase tracking-wider">Category Label</label>
                        <input type="text" 
                               name="offers[items][${newIdx}][category_name]" 
                               value="Celebration Offer" 
                               placeholder="e.g. Birthdays & Parties" 
                               class="w-full bg-white border border-gray-300 px-2.5 py-1.5 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900">
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[10px] text-gray-600 font-extrabold uppercase tracking-wider">Top Eyebrow / Discount Subheading</label>
                    <input type="text" 
                           name="offers[items][${newIdx}][subheading]" 
                           value="SPECIAL DISCOUNT" 
                           placeholder="e.g. FLAT ₹500 OFF" 
                           class="w-full bg-white border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[10px] text-gray-600 font-extrabold uppercase tracking-wider">Main Discount Headline</label>
                    <input type="text" 
                           name="offers[items][${newIdx}][discount_title]" 
                           value="Flat ₹500 Off on Setup" 
                           placeholder="e.g. Flat ₹500 Off on Theme Decor" 
                           required
                           class="w-full bg-white border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[10px] text-gray-600 font-extrabold uppercase tracking-wider">Terms & Conditions / Coupon Rules</label>
                    <input type="text" 
                           name="offers[items][${newIdx}][terms]" 
                           value="Applicable on all bookings above ₹4,999" 
                           placeholder="e.g. Min. booking ₹4,999 • Code: ARTIZEN500" 
                           class="w-full bg-white border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[10px] text-gray-600 font-extrabold uppercase tracking-wider">Destination Link URL</label>
                    <input type="text" 
                           name="offers[items][${newIdx}][link]" 
                           value="/events" 
                           placeholder="e.g. /events?category=birthdays" 
                           class="w-full bg-white border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900">
                </div>

                <div class="flex items-center justify-between border-t border-gray-200/80 pt-2.5 mt-1">
                    <label class="text-xs font-semibold text-gray-700 cursor-pointer flex items-center gap-2 select-none">
                        <input type="checkbox" 
                               name="offers[items][${newIdx}][is_active]" 
                               value="1" 
                               checked 
                               onchange="updateOfferCounts()"
                               class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500">
                        <span>Active on Voucher Bar</span>
                    </label>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', html);
        updateOfferCounts();
    }
</script>
@endpush

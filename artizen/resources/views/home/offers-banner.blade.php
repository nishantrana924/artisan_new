@php
    $offersConfig = $offers ?? ($cms['offers'] ?? []);
    $offersActive = $offersConfig['is_active'] ?? true;
    $rawOfferItems = $offersConfig['items'] ?? [];
    
    // Filter active offer items
    $offersList = array_values(array_filter($rawOfferItems, function($o) {
        return ($o['is_active'] ?? true);
    }));

    // Default fallback offers if no active configured items
    if (empty($offersList)) {
        $offersList = [
            [
                'id' => 1,
                'category_name' => 'Birthdays & House Parties',
                'icon' => 'fa-solid fa-cake-candles',
                'icon_color' => 'text-amber-600',
                'subheading' => 'BIRTHDAYS & TERRACE DECORS',
                'discount_title' => 'Up to 20% OFF',
                'terms' => 'T&C Apply | Applicable on Birthday & Terrace Setups',
                'link' => route('events.index', ['category' => 'cat-birthdays']),
            ],
            [
                'id' => 2,
                'category_name' => 'Anniversaries & Proposals',
                'icon' => 'fa-solid fa-heart',
                'icon_color' => 'text-rose-600',
                'subheading' => 'ROMANTIC CANOPIES & STAGES',
                'discount_title' => 'Up to 15% OFF',
                'terms' => 'T&C Apply | Applicable on Canopies & Flower Panels',
                'link' => route('events.index', ['category' => 'cat-proposal-anniversary']),
            ],
            [
                'id' => 3,
                'category_name' => 'Weddings & DJ Sound',
                'icon' => 'fa-solid fa-compact-disc',
                'icon_color' => 'text-purple-600',
                'subheading' => 'GRAND SANGEET & PARTY RIGS',
                'discount_title' => 'Up to 25% OFF',
                'terms' => 'T&C Apply | Applicable on Stage Decors & DJ Rigs',
                'link' => route('events.index', ['category' => 'cat-weddings-sangeet']),
            ],
        ];
    }
@endphp

@if($offersActive && !empty($offersList))
<!-- ARTIZEN — Save More with Offers (Category-Wise Discount Offers) -->
<section id="offers-promos" class="py-10 sm:py-14 bg-white select-none overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="mb-6 sm:mb-8">
            <h2 class="font-heading font-extrabold text-2xl sm:text-3xl md:text-[32px] text-gray-950 tracking-tight">
                {{ $offersConfig['section_title'] ?? 'Save More with Offers' }}
            </h2>
        </div>

        <!-- 3-Column Ticket Voucher Coupons Grid (Category-Wise Percentage Discount Offers) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6">
            @foreach($offersList as $offer)
            <a href="{{ $offer['link'] ?? route('events.index') }}" 
               class="relative bg-[#FFF7CC] border border-[#F5E898] hover:border-amber-400 rounded-3xl p-6 sm:p-7 shadow-none overflow-hidden flex flex-col justify-between h-48 sm:h-52 text-left block text-decoration-none cursor-pointer transition-colors">
                
                <!-- Ticket Top Semi-Circle Notch -->
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-6 h-6 rounded-full bg-white border border-[#F5E898]"></div>
                <!-- Ticket Bottom Semi-Circle Notch -->
                <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 w-6 h-6 rounded-full bg-white border border-[#F5E898]"></div>

                <!-- Watermark % Icon Top Right -->
                <div class="absolute -top-3 -right-3 w-24 h-24 rounded-full bg-[#FFE57A]/40 flex items-center justify-center pointer-events-none">
                    <span class="text-5xl font-extrabold text-[#F0CF42]/70">%</span>
                </div>

                <!-- Top Brand Header -->
                <div class="relative z-10 flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-full bg-white shadow-2xs flex items-center justify-center p-1">
                        <i class="{{ $offer['icon'] ?? 'fa-solid fa-tag' }} {{ $offer['icon_color'] ?? 'text-amber-600' }} text-sm"></i>
                    </div>
                    <div>
                        <span class="font-heading font-extrabold text-sm text-gray-950 leading-tight block">
                            {{ $offer['category_name'] ?? 'Celebration Offer' }}
                        </span>
                    </div>
                </div>

                <!-- Middle Main Discount Title -->
                <div class="relative z-10 my-auto">
                    @if(!empty($offer['subheading']))
                    <span class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-700 block mb-0.5">
                        {{ $offer['subheading'] }}
                    </span>
                    @endif
                    <h3 class="font-heading font-black text-2xl sm:text-3xl text-gray-950 tracking-tight leading-none">
                        {{ $offer['discount_title'] ?? 'Special Offer' }}
                    </h3>
                </div>

                <!-- Bottom T&C Line -->
                <div class="relative z-10 pt-2 border-t border-black/5 flex items-center justify-between">
                    <span class="text-[10.5px] text-gray-600 font-normal">
                        {{ $offer['terms'] ?? 'T&C Apply' }}
                    </span>
                </div>
            </a>
            @endforeach
        </div>

    </div>
</section>
@endif

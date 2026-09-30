@extends('layouts.app')

@section('content')
<!-- About Us Header Banner -->
<section class="relative bg-black py-24 flex items-center justify-center overflow-hidden border-b border-white/10">
    <div class="absolute inset-0 bg-cover bg-center opacity-30 pointer-events-none" style="background-image: url('{{ $about['image'] ?? '/assets/images/hero/1.jpg' }}');"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/80 to-transparent pointer-events-none"></div>
    
    <div class="relative z-10 text-center px-4">
        <span class="inline-flex items-center gap-1.5 text-[10px] md:text-xs font-bold text-gold uppercase tracking-wider mb-3">
            <span class="w-1.5 h-1.5 rounded-full bg-gold"></span> Dynamic Portal CMS
        </span>
        <h1 class="font-heading font-extrabold text-4xl md:text-6xl text-white uppercase tracking-tight leading-none">
            About Us
        </h1>
        <p class="font-body text-xs md:text-sm text-gray-400 mt-3 max-w-xl mx-auto leading-relaxed">
            Discover the vision behind Artizen's premium Indore celebrations booking platform.
        </p>
    </div>
</section>

<!-- Main About Content Split -->
<section class="bg-[#0a0a0a] text-white py-20 relative overflow-hidden">
    <div class="w-full px-4 md:px-12 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative z-10">
        
        <!-- Left Column: Main Uploaded Image (Swiper Slider) -->
        <div class="lg:col-span-6 flex justify-center">
            <div class="relative w-full aspect-[4/3] max-w-xl rounded-2xl overflow-hidden border border-white/10 shadow-2xl group">
                <div class="swiper about-swiper w-full h-full">
                    <div class="swiper-wrapper">
                        @foreach ($about['images'] ?? [ $about['image'] ?? '/assets/images/hero/1.jpg' ] as $imgUrl)
                            <div class="swiper-slide w-full h-full">
                                <img src="{{ $imgUrl }}" alt="Artizen Experience Setup" class="w-full h-full object-cover group-hover:scale-102 transition-all duration-500">
                            </div>
                        @endforeach
                    </div>
                    <!-- Pagination dots -->
                    <div class="swiper-pagination about-swiper-pagination"></div>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent z-10 pointer-events-none"></div>
                <!-- Dynamic badge overlay -->
                <span class="absolute bottom-4 left-4 bg-black/85 backdrop-blur-md text-[#FFD600] text-[10px] md:text-xs font-bold px-3.5 py-1.5 rounded-full uppercase tracking-wider border border-[#FFD600]/40 shadow-lg z-20 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FFD600]"></span>
                    Indore's Top Booking Platform
                </span>
            </div>
        </div>

        <!-- Right Column: Text & Content -->
        <div class="lg:col-span-6 text-left flex flex-col gap-6">
            <div>
                <span class="inline-flex items-center gap-1.5 text-[10px] md:text-xs font-bold text-gold uppercase tracking-wider mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-gold"></span> {{ $about['badge'] ?? 'INSTANT BOOKING' }}
                </span>
                <h2 class="font-heading font-bold text-3xl md:text-5xl uppercase tracking-tighter leading-none text-white">
                    {!! nl2br(e($about['title'] ?? 'BOOKING CONFIRMED IN SECONDS')) !!}
                </h2>
            </div>
            
            <div class="font-body text-xs md:text-sm text-gray-300 leading-relaxed text-left max-w-2xl text-editor-content">
                {!! $about['desc'] ?? 'We engineered the fastest event booking flow in the industry. Choose your setup, book instantly, and relax.' !!}
            </div>
        </div>
    </div>
</section>

<!-- Dynamic Values Grid Section -->
<section class="bg-[#121212] border-t border-white/10 text-white py-20 relative">
    <div class="w-full px-4 md:px-12 relative z-10 text-center">
        <div class="max-w-2xl mx-auto mb-16 text-center">
            <span class="inline-flex items-center gap-1.5 text-[10px] md:text-xs font-bold text-gold uppercase tracking-wider mb-3">
                EXCELLENCE IN SERVICES
            </span>
            <h2 class="font-heading font-bold text-3xl md:text-4xl uppercase tracking-tighter text-white">
                WHY CHOOSE ARTIZEN
            </h2>
            <p class="font-body text-xs text-gray-400 mt-2">
                We design and manage every function directly to guarantee visual beauty and reliability.
            </p>
        </div>

        <!-- Value Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 justify-center">
            @foreach ($about['cards'] ?? [] as $card)
                <div class="bg-[#0a0a0a] border border-white/5 p-6 rounded-2xl flex flex-col justify-between hover:border-gold/40 transition-all duration-300 text-left shadow-lg">
                    <div class="text-gold mb-6 text-2xl">
                        <i class="{{ $card['icon'] ?? 'fa-solid fa-star' }} text-gold text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-sm uppercase tracking-wider text-white mb-2">{{ $card['title'] ?? 'SERVICE FEATURE' }}</h3>
                        <div class="font-body text-[11px] text-gray-400 leading-relaxed text-left text-editor-content">
                            {!! $card['desc'] ?? '' !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<style>
    /* Styling elements inside rich text descriptions */
    .text-editor-content p {
        margin-bottom: 1rem;
    }
    .text-editor-content ul {
        list-style-type: disc;
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }
    .text-editor-content ol {
        list-style-type: decimal;
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof Swiper !== 'undefined') {
            new Swiper('.about-swiper', {
                effect: 'fade',
                fadeEffect: { crossFade: true },
                loop: true,
                autoplay: { delay: 4000, disableOnInteraction: false },
                pagination: { el: '.about-swiper-pagination', clickable: true }
            });
        }
    });
</script>
@endsection

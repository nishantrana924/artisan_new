<section class="border-t border-white/10 bg-[#0a0a0a] text-white py-20 overflow-hidden relative">
    <div class="w-full px-4 md:px-12 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative z-10">
        
        <!-- Left Column: Main Image Split (Swiper Slider) -->
        <div class="lg:col-span-5 flex justify-center">
            <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden border border-white/10 shadow-2xl group">
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
                <div class="absolute inset-0 bg-gradient-to-t from-black/55 to-transparent z-10 pointer-events-none"></div>
                <!-- Premium Overlay Tag -->
                <span class="absolute bottom-4 left-4 bg-black/85 backdrop-blur-md text-[#FFD600] text-[10px] md:text-xs font-bold px-3.5 py-1.5 rounded-full uppercase tracking-wider border border-[#FFD600]/40 shadow-lg z-20 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FFD600]"></span>
                    Indore Event Experts
                </span>
            </div>
        </div>

        <!-- Right Column (Text Copy & Details) -->
        <div class="lg:col-span-7 text-left flex flex-col gap-6">
            <div>
                <span class="inline-flex items-center gap-1.5 text-[10px] md:text-xs font-bold text-gold uppercase tracking-wider mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-gold"></span> {{ $about['badge'] }}
                </span>
                <h2 class="font-heading font-bold text-3xl md:text-5xl uppercase tracking-tighter leading-none mb-4">
                    {!! nl2br(e($about['title'])) !!}
                </h2>
                <div class="font-body text-xs md:text-sm text-gray-300 leading-relaxed text-left text-editor-content">
                    {!! $about['desc'] !!}
                </div>
            </div>

            <!-- Value Cards (Dynamic Grid of Icons) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-2">
                @foreach ($about['cards'] as $card)
                    <div class="bg-[#121212] border border-white/5 p-4 rounded-xl flex flex-col justify-between hover:border-gold/40 transition-all duration-300 text-left">
                        <div class="text-gold mb-3 text-lg">
                            <i class="{{ $card['icon'] ?? 'fa-solid fa-star' }} text-gold text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-xs uppercase tracking-wider text-white mb-1">{{ $card['title'] }}</h3>
                            <div class="font-body text-[10px] text-gray-400 leading-relaxed text-left text-editor-content">
                                {!! $card['desc'] !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</section>

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



    <!-- Brand Credo / About Section -->
    
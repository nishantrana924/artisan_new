<section class="hero-section relative flex items-center justify-center border-b border-white/10 overflow-hidden bg-[#0a0a0a]">
    <!-- Full-Width Background Image Slider (Swiper) -->
    <div class="absolute inset-0 w-full h-full z-0 overflow-hidden bg-black">
        <div class="hero-swiper swiper w-full h-full">
            <div class="swiper-wrapper h-full">
                @foreach ($slides as $slide)
                    <div class="swiper-slide w-full h-full relative">
                        <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}" class="w-full h-full object-cover animate-zoom-slow">
                        <!-- Gradient Overlay for readability -->
                        <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/40 to-transparent z-10 pointer-events-none"></div>
                        <div class="hero-text-container absolute inset-y-0 left-0 w-full flex flex-col justify-center px-6 md:px-12 lg:px-20 z-20 text-left max-w-3xl pointer-events-none">
                            <span class="inline-flex items-center gap-1.5 text-xs font-heading font-semibold text-[#EA741D] tracking-wider uppercase mb-4 pointer-events-auto">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#EA741D]"></span> {{ $slide['badge'] }}
                            </span>
                            <h1 class="hero-title font-heading font-bold text-white tracking-[-0.025em] mb-6 pointer-events-auto">
                                {!! nl2br(e($slide['title'])) !!}
                            </h1>
                            <p class="hero-desc font-body text-sm sm:text-base md:text-lg text-gray-200 max-w-2xl leading-relaxed mb-8 pointer-events-auto">
                                {!! strip_tags($slide['desc']) !!}
                            </p>
                            <div class="hero-btns flex flex-wrap gap-4 pointer-events-auto">
                                <a href="{{ $slide['link1'] }}" class="bg-[#EA741D] hover:bg-[#D6630F] text-white px-6 py-3.5 text-sm font-heading font-semibold rounded-xl transition-all duration-200 shadow-lg">
                                    {{ $slide['btn1Text'] }}
                                </a>
                                <a href="{{ $slide['link2'] }}" target="_blank" class="border border-white/20 hover:border-white/50 bg-white/5 hover:bg-white/10 text-white px-6 py-3.5 text-sm font-heading font-semibold rounded-xl transition-all duration-200">
                                    {{ $slide['btn2Text'] }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>
    
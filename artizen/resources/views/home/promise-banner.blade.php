@php
    $pbConfig = $promiseBanner ?? ($cms['promise_banner'] ?? []);
    $pbActive = $pbConfig['is_active'] ?? true;
    $pbImage = $pbConfig['image'] ?? '/images/banners/artizen-promise-banner.webp';
    $pbTitle = $pbConfig['title'] ?? 'Artizen — Curating Unforgettable Moments, Luxury Weddings & Celebrations';
    $pbLink = $pbConfig['link'] ?? route('events.index');

    $cleanPbImage = ltrim($pbImage, '/');
    $optPbImage = str_replace(['.webp', '.jpg', '.png', '.jpeg'], '.opt.webp', $cleanPbImage);
    $hasPbOpt = file_exists(public_path($optPbImage));
@endphp

@if($pbActive && !empty($pbImage))
<!-- ARTIZEN — Full Width Promise Banner Section (Below Categories) -->
<section class="w-full py-4 sm:py-8 bg-transparent select-none">
    <div class="w-full px-4 md:px-12">
        <div class="w-full overflow-hidden rounded-2xl md:rounded-3xl shadow-sm border border-gray-200/60 dark:border-white/10 bg-[#FAF7F2] dark:bg-[#16161A]">
            <a href="{{ $pbLink }}" class="block w-full" aria-label="{{ $pbTitle }}">
                <picture>
                    @if($hasPbOpt)
                        <source srcset="{{ asset($optPbImage) }}" type="image/webp">
                    @endif
                    <img src="{{ asset($cleanPbImage) }}" 
                         alt="{{ $pbTitle }}" 
                         class="w-full h-auto max-h-[180px] sm:max-h-[240px] md:max-h-[300px] lg:max-h-[360px] object-cover object-center select-none"
                         loading="lazy">
                </picture>
            </a>
        </div>
    </div>
</section>
@endif

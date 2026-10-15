@extends('layouts.app')

@section('title', 'Setup Photo Gallery | ARTIZEN Indore Events')
@section('meta_description', 'Explore real setup photos from birthdays, house parties, proposals, sound rigs, and wedding celebrations executed across Indore by Artizen.')

@section('content')
<div class="min-h-screen pt-8 pb-20 px-4 md:px-12 bg-[#FAF9F6] dark:bg-[#080808] text-[#171719] dark:text-white relative">
    <div class="max-w-7xl mx-auto">



        <!-- Clean, Elegant Page Header -->
        <div class="border-b border-[#E6E2D8] dark:border-white/10 pb-8 mb-10 text-left">
            <h1 class="font-heading font-extrabold text-3xl md:text-5xl uppercase tracking-tight text-[#171719] dark:text-white leading-tight">
                Setup Photo Gallery
            </h1>
            <p class="text-sm md:text-base text-gray-600 dark:text-gray-400 max-w-2xl mt-2 leading-relaxed">
                Real celebration setups, decor themes, sound rigs, and lighting delivered across Indore.
            </p>
        </div>

        <!-- Clean Gallery Grid (Square Sharp Corners, Unobstructed Images) -->
        @if(count($galleryItems) > 0)
            <div id="gallery-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($galleryItems as $item)
                    @php
                        $pkgUrl = !empty($item['package_slug']) ? route('events.show', $item['package_slug']) : route('events.index');
                    @endphp
                    <div class="group relative bg-white dark:bg-[#121215] border border-[#E6E2D8] dark:border-white/10 hover:border-black dark:hover:border-white/40 rounded-none overflow-hidden transition-all duration-300 flex flex-col justify-between">
                        <!-- Image Container with Lightbox Trigger (Sharp Square, 100% Clear on Hover) -->
                        <div class="aspect-[4/3] relative overflow-hidden bg-gray-100 dark:bg-[#1E1E24] cursor-pointer rounded-none" onclick="openGalleryLightbox('{{ $item['image'] }}', '{{ addslashes($item['title']) }}', '{{ addslashes($item['category'] ?? '') }}', '{{ $pkgUrl }}')">
                            <img src="{{ $item['image'] }}" 
                                 alt="{{ $item['title'] }}" 
                                 loading="lazy" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out select-none" 
                                 onerror="this.src='/assets/images/hero/1.jpg'">
                        </div>

                        <!-- Card Caption Info & Setup Link -->
                        <div class="p-4 flex items-center justify-between gap-3 text-left border-t border-[#E6E2D8] dark:border-white/10">
                            <div class="min-w-0 flex-1">
                                <h2 class="text-xs font-bold text-[#171719] dark:text-white truncate" title="{{ $item['title'] }}">
                                    {{ $item['title'] }}
                                </h2>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate mt-0.5 font-medium">
                                    {{ $item['category'] ?? 'Artizen Setup' }}
                                </p>
                            </div>
                            <a href="{{ $pkgUrl }}" 
                               class="text-[11px] font-bold text-gray-700 dark:text-gray-300 hover:text-black dark:hover:text-[#FFD600] inline-flex items-center gap-1.5 shrink-0 py-1.5 px-3 rounded-none border border-[#E6E2D8] dark:border-white/10 hover:border-black dark:hover:border-[#FFD600] bg-[#FAF9F6] dark:bg-[#1E1E24] transition-all"
                               title="View Complete Package">
                                <span>Setup</span>
                                <i class="fa-solid fa-arrow-right text-[9px]"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Minimal Empty State (Square Sharp Corners) -->
            <div class="bg-white dark:bg-[#121215] border border-[#E6E2D8] dark:border-white/10 p-16 rounded-none text-center flex flex-col items-center justify-center gap-4">
                <h3 class="text-xl font-bold text-[#171719] dark:text-white">Gallery Photos Being Updated</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md">Our team is uploading recent celebration setup photos across Indore. Please check back shortly!</p>
                <a href="{{ route('events.index') }}" class="px-5 py-2.5 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] text-xs font-bold rounded-none shadow-none transition-all">
                    Explore Event Packages
                </a>
            </div>
        @endif

    </div>
</div>

<!-- Clean Lightbox Modal (Sharp Square Corners) -->
<div id="gallery-lightbox-modal" class="fixed inset-0 bg-black/90 backdrop-blur-md z-50 hidden flex items-center justify-center p-4" onclick="closeGalleryLightbox()">
    <div class="relative max-w-4xl w-full flex flex-col items-center gap-4" onclick="event.stopPropagation()">
        <button onclick="closeGalleryLightbox()" aria-label="Close image lightbox" class="absolute -top-10 right-0 text-white/80 hover:text-white text-2xl font-bold cursor-pointer transition-colors">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
        <img id="lightbox-modal-img" src="" alt="Gallery Preview" class="max-h-[75vh] w-auto max-w-full object-contain rounded-none border border-white/10 shadow-2xl">
        <div class="flex items-center justify-between w-full max-w-xl px-2 gap-4">
            <div class="text-left min-w-0">
                <div id="lightbox-modal-caption" class="text-sm font-bold tracking-wide truncate" style="color: #ffffff !important;"></div>
                <div id="lightbox-modal-category" class="text-xs font-medium" style="color: #9ca3af !important;"></div>
            </div>
            <a id="lightbox-modal-pkg-link" href="#" class="px-4 py-2 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] text-xs font-heading font-extrabold uppercase tracking-wider rounded-none transition-all shrink-0 flex items-center gap-2">
                <span>View Package</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>
</div>

<script>
    function openGalleryLightbox(src, title, category, pkgUrl) {
        const modal = document.getElementById('gallery-lightbox-modal');
        const img = document.getElementById('lightbox-modal-img');
        const cap = document.getElementById('lightbox-modal-caption');
        const cat = document.getElementById('lightbox-modal-category');
        const pkgLink = document.getElementById('lightbox-modal-pkg-link');

        img.src = src;
        cap.innerText = title;
        cat.innerText = category;
        if (pkgLink && pkgUrl) {
            pkgLink.href = pkgUrl;
        }
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeGalleryLightbox() {
        const modal = document.getElementById('gallery-lightbox-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeGalleryLightbox();
        }
    });
</script>
@endsection

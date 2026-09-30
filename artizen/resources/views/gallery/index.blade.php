@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-8 pb-16 px-4 md:px-12 bg-white dark:bg-[#0c0c0c] text-black dark:text-white relative">
    <div class="max-w-7xl mx-auto">

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6 font-medium">
            <a href="{{ route('home') }}" class="hover:text-black dark:hover:text-white transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-black dark:text-white font-bold">Gallery</span>
        </nav>

        <!-- Gallery Hero Banner Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-200 dark:border-white/10 pb-8 mb-10 gap-6 text-left">
            <div>
                <span class="text-[10px] font-heading font-extrabold uppercase tracking-widest text-gold mb-2 inline-flex items-center gap-1.5 bg-gold/10 px-3 py-1 rounded-full border border-gold/30">
                    <i class="fa-solid fa-camera"></i> ARTIZEN CELEBRATION MEMORIES
                </span>
                <h1 class="font-heading font-extrabold text-3xl md:text-5xl uppercase tracking-tight text-gray-900 dark:text-white leading-tight">
                    Setup Photo Gallery
                </h1>
                <p class="text-sm md:text-base text-gray-600 dark:text-gray-300 max-w-2xl mt-2">
                    Browse real setup photos from birthdays, house parties, proposals, sound rigs, and wedding functions delivered across Indore.
                </p>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-[#121212] border border-gray-200 dark:border-white/10 rounded-2xl py-3 px-5 shadow-sm shrink-0">
                <div class="w-10 h-10 rounded-xl bg-gold/10 border border-gold/20 text-gold flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-images"></i>
                </div>
                <div class="text-left">
                    <span class="text-lg font-extrabold text-gray-900 dark:text-white block leading-none">{{ count($galleryItems) }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Verified Photos</span>
                </div>
            </div>
        </div>

        <!-- Category Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-8 scrollbar-hide">
            <button onclick="filterGallery('all', this)" class="gallery-filter-btn active px-4 py-2 rounded-xl text-xs font-extrabold shrink-0 transition-all bg-[#FFD600] text-[#171719] shadow-md border border-transparent">
                All Photos ({{ count($galleryItems) }})
            </button>
            @foreach($categoriesList as $cat)
                @if($cat['active'] ?? true)
                    @php
                        $cSlug = $cat['slug'] ?? \Illuminate\Support\Str::slug($cat['title'] ?? '');
                        if ($cSlug === 'adult-birthdays') $cSlug = 'birthdays';
                    @endphp
                    <button onclick="filterGallery('{{ $cSlug }}', this)" class="gallery-filter-btn px-4 py-2 rounded-xl text-xs font-bold shrink-0 transition-all bg-white dark:bg-[#121212] text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-white/10 hover:border-gold">
                        {{ $cat['title'] ?? '' }}
                    </button>
                @endif
            @endforeach
        </div>

        <!-- Gallery Grid -->
        @if(count($galleryItems) > 0)
            <div id="gallery-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($galleryItems as $item)
                    <div class="gallery-card group relative bg-white dark:bg-[#121212] border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300" data-category="{{ $item['category_slug'] ?? 'all' }}">
                        <!-- Image Container -->
                        <div class="aspect-[4/3] relative overflow-hidden bg-gray-100 dark:bg-white/5">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 cursor-pointer" onclick="openGalleryLightbox('{{ $item['image'] }}', '{{ addslashes($item['title']) }}')" onerror="this.src='/assets/images/hero/1.jpg'">
                            <span class="absolute top-3 left-3 bg-black/80 backdrop-blur-md text-white text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-md tracking-wider">
                                {{ $item['category'] ?? 'Artizen' }}
                            </span>
                        </div>

                        <!-- Card Footer Info -->
                        <div class="p-4 flex items-center justify-between gap-2 border-t border-gray-100 dark:border-white/5">
                            <span class="text-xs font-bold text-gray-900 dark:text-white truncate">
                                {{ $item['title'] }}
                            </span>
                            <a href="{{ route('category.show', $item['package_slug'] ?? 'birthdays') }}" class="px-3 py-1.5 bg-gold/10 hover:bg-gold/20 text-[#171719] dark:text-gold border border-gold/20 text-[10px] font-bold uppercase tracking-wider rounded-lg transition-all shrink-0">
                                View Setup
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Clean Empty State -->
            <div class="bg-white dark:bg-[#121212] border border-gray-200 dark:border-white/10 p-16 rounded-3xl text-center flex flex-col items-center justify-center gap-4">
                <div class="w-16 h-16 rounded-full bg-gold/10 border border-gold/20 text-gold flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-camera-retro"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">No Gallery Images Available Yet</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md">Gallery setup photos are currently being updated by the Artizen team. Check back soon!</p>
                <a href="{{ route('events.index') }}" class="px-5 py-2.5 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] text-xs font-bold rounded-xl shadow-md">
                    Explore Event Packages
                </a>
            </div>
        @endif

    </div>
</div>

<!-- Simple Lightbox Modal -->
<div id="gallery-lightbox-modal" class="fixed inset-0 bg-black/90 backdrop-blur-md z-50 hidden flex items-center justify-center p-4" onclick="closeGalleryLightbox()">
    <div class="relative max-w-4xl w-full flex flex-col items-center gap-4" onclick="event.stopPropagation()">
        <button onclick="closeGalleryLightbox()" aria-label="Close image lightbox" class="absolute -top-10 right-0 text-white hover:text-gold text-2xl font-bold">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
        <img id="lightbox-modal-img" src="" alt="Gallery Preview" class="max-h-[80vh] w-auto object-contain rounded-2xl border border-white/10 shadow-2xl">
        <span id="lightbox-modal-caption" class="text-white text-sm font-bold tracking-wide bg-black/60 px-4 py-1.5 rounded-full"></span>
    </div>
</div>

<script>
    function filterGallery(category, btn) {
        document.querySelectorAll('.gallery-filter-btn').forEach(b => {
            b.classList.remove('bg-[#FFD600]', 'text-[#171719]', 'shadow-md');
            b.classList.add('bg-white', 'dark:bg-[#121212]', 'text-gray-700', 'dark:text-gray-200');
        });
        btn.classList.remove('bg-white', 'dark:bg-[#121212]', 'text-gray-700', 'dark:text-gray-200');
        btn.classList.add('bg-[#FFD600]', 'text-[#171719]', 'shadow-md');

        document.querySelectorAll('.gallery-card').forEach(card => {
            if (category === 'all' || card.dataset.category === category) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function openGalleryLightbox(src, caption) {
        const modal = document.getElementById('gallery-lightbox-modal');
        const img = document.getElementById('lightbox-modal-img');
        const cap = document.getElementById('lightbox-modal-caption');

        img.src = src;
        cap.innerText = caption;
        modal.classList.remove('hidden');
    }

    function closeGalleryLightbox() {
        document.getElementById('gallery-lightbox-modal').classList.add('hidden');
    }
</script>
@endsection

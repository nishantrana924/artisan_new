@extends('layouts.admin')

@section('page_title', 'Hero Banners Manager')
@section('page_heading', 'Hero Banners Manager')
@section('page_subheading', 'Manage homepage carousel slides, headlines, imagery, and button call-to-actions.')

@section('content')

{{-- ===================================================================
     1. FLASH NOTIFICATIONS
     =================================================================== --}}
@if(session('success'))
    <div class="mb-5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-2xl flex items-center gap-2.5 shadow-2xs">
        <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if(session('error'))
    <div class="mb-5 px-4 py-3 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-2xl flex items-center gap-2.5 shadow-2xs">
        <i class="fa-solid fa-triangle-exclamation text-rose-500 text-sm"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif
@if($errors->any())
    <div class="mb-5 px-4 py-3 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-2xl flex flex-col gap-1 shadow-2xs">
        <div class="flex items-center gap-2 text-rose-700 font-bold">
            <i class="fa-solid fa-circle-xmark"></i>
            <span>Please correct the following errors:</span>
        </div>
        <ul class="list-disc list-inside text-[11px] text-rose-600 pl-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $heroSlidesList = $slides ?? ($cms['hero_slides'] ?? []);
    if (empty($heroSlidesList)) {
        $heroSlidesList = [
            [
                'id'           => 1,
                'image'        => '/assets/images/hero/1.jpg',
                'title'        => 'ELEVATE YOUR CELEBRATIONS',
                'desc'         => 'Book complete premium decor, sound, and lighting setups instantly for birthdays, house parties, and sangeet.',
                'link1'        => '/events',
                'btn1Text'     => 'Explore Packages',
                'display_order'=> 0,
                'is_active'    => true,
            ]
        ];
    }
@endphp

{{-- ===================================================================
     2. CLEAN HEADER & ACTION BAR
     =================================================================== --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200/80 pb-5 mb-6">
    <div>
        <div class="flex items-center gap-3">
            <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">Hero Banners Manager</h2>
            <span id="slide-count-badge" class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-gray-100 text-gray-700 border border-gray-200">
                {{ count($heroSlidesList) }} {{ count($heroSlidesList) === 1 ? 'Slide' : 'Slides' }}
            </span>
        </div>
        <p class="text-xs text-gray-500 mt-1">Configure homepage panoramic slides, high-impact headlines, and action buttons.</p>
    </div>
    <div class="flex items-center gap-2.5">
        <button type="button" 
                onclick="addNewSlide()" 
                class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 text-xs font-bold rounded-xl shadow-2xs hover:shadow-xs transition-all cursor-pointer shrink-0">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Add New Slide</span>
        </button>
        <button type="button" 
                onclick="document.getElementById('hero-slider-form').requestSubmit()" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl shadow-xs hover:shadow transition-all cursor-pointer shrink-0">
            <i class="fa-solid fa-floppy-disk text-xs"></i>
            <span>Save All Slides</span>
        </button>
    </div>
</div>

{{-- ===================================================================
     3. HERO SLIDES FORM & CARDS
     =================================================================== --}}
<form id="hero-slider-form" action="{{ route('admin.hero.save') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateHeroForm()" class="flex flex-col gap-6">
    @csrf

    {{-- Grid Layout: 2 spacious columns on desktop for generous card width (~550px) --}}
    <div id="hero-slides-grid" class="grid grid-cols-1 xl:grid-cols-2 gap-6 lg:gap-8">
        @foreach ($heroSlidesList as $idx => $slide)
            @php
                $cleanDesc = trim(strip_tags($slide['desc'] ?? ''));
                $descWords = $cleanDesc === '' ? 0 : count(preg_split('/\s+/', $cleanDesc));
                $isActive = $slide['is_active'] ?? true;
                $hasImage = !empty($slide['image']);
            @endphp
            <div class="slide-card-wrapper bg-white border border-gray-200/90 rounded-2xl p-6 flex flex-col gap-5 shadow-xs hover:shadow-md transition-all relative" data-slide-index="{{ $idx }}">
                
                {{-- Card Header: Slide Counter & Status & Reorder / Delete Actions --}}
                <div class="flex justify-between items-center border-b border-gray-100 pb-3.5">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-gray-900 text-white text-xs font-extrabold flex items-center justify-center slide-number-badge shadow-2xs">
                            {{ $idx + 1 }}
                        </span>
                        <span class="text-xs text-gray-900 font-extrabold uppercase tracking-wide slide-label">Slide #{{ $idx + 1 }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold slide-status-pill status-pill-{{ $idx }} {{ $isActive ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                            {{ $isActive ? 'Active' : 'Draft' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="moveSlideUp(this)" title="Move Left / Earlier" class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-600 text-xs flex items-center justify-center transition-colors cursor-pointer">
                            <i class="fa-solid fa-chevron-left text-[11px]"></i>
                        </button>
                        <button type="button" onclick="moveSlideDown(this)" title="Move Right / Later" class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-600 text-xs flex items-center justify-center transition-colors cursor-pointer">
                            <i class="fa-solid fa-chevron-right text-[11px]"></i>
                        </button>
                        <button type="button" onclick="removeSlide(this)" title="Delete Slide" class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-200/60 text-rose-600 text-xs flex items-center justify-center transition-colors cursor-pointer ml-1">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </div>
                </div>

                {{-- Hidden Fields for Stable Persistence --}}
                <input type="hidden" name="hero_slides[{{ $idx }}][id]" value="{{ $slide['id'] ?? '' }}" class="slide-id-input">
                <input type="hidden" name="hero_slides[{{ $idx }}][display_order]" value="{{ $slide['display_order'] ?? $idx }}" class="slide-order-input">

                {{-- Field 1: Slide Image Upload with Live 16:9 Preview / Clean Placeholder --}}
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <label class="!text-[11px] !font-extrabold !text-gray-700 !uppercase !tracking-wider !mb-0">Slide Image</label>
                        <span class="text-[11px] text-gray-400 font-medium">16:9 ratio recommended (1376×768)</span>
                    </div>
                    <div class="relative group aspect-[16/9] rounded-2xl overflow-hidden border-2 border-dashed border-gray-200 hover:border-gray-400 bg-gray-50 flex flex-col items-center justify-center cursor-pointer shadow-inner transition-all" onclick="document.getElementById('slide-input-{{ $idx }}').click()">
                        {{-- Actual Preview Image --}}
                        <img id="slide-preview-{{ $idx }}" 
                             src="{{ $slide['image'] ?? '' }}" 
                             alt="Slide Preview" 
                             class="{{ $hasImage ? '' : 'hidden' }} slide-preview-img absolute inset-0 w-full h-full object-cover group-hover:scale-102 transition-transform duration-200">

                        {{-- Empty Upload Placeholder (shown when no image selected) --}}
                        <div id="slide-placeholder-{{ $idx }}" class="{{ $hasImage ? 'hidden' : '' }} slide-upload-placeholder flex flex-col items-center justify-center p-6 text-center select-none">
                            <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-500 mb-2 group-hover:scale-105 group-hover:bg-gray-200 transition-all">
                                <i class="fa-solid fa-cloud-arrow-up text-lg text-gray-600"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-800">Click to Upload Slide Image</span>
                            <span class="text-[11px] text-gray-400 mt-0.5">JPG, PNG, or WebP up to 5MB</span>
                        </div>

                        {{-- Hover Overlay for Existing Image --}}
                        <div id="slide-overlay-{{ $idx }}" class="{{ $hasImage ? '' : 'hidden' }} slide-hover-overlay absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white text-xs font-bold gap-1.5 z-10">
                            <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                            <span>Click to Change Image</span>
                        </div>

                        {{-- Aspect Ratio Badge --}}
                        <span id="ratio-badge-{{ $idx }}" class="{{ $hasImage ? '' : 'hidden' }} slide-ratio-badge absolute bottom-2.5 right-2.5 px-2.5 py-1 rounded-lg bg-black/80 text-[10px] text-white font-bold uppercase border border-white/10 font-mono shadow-md z-20">
                            16:9
                        </span>
                    </div>

                    {{-- File Input & Existing Image Path --}}
                    <input type="file" 
                           id="slide-input-{{ $idx }}" 
                           name="hero_slides[{{ $idx }}][image_file]" 
                           class="hidden slide-file-input" 
                           accept="image/jpeg,image/png,image/webp,image/gif" 
                           onchange="previewSlideImage(this, {{ $idx }})">
                    <input type="hidden" 
                           name="hero_slides[{{ $idx }}][image]" 
                           value="{{ $slide['image'] ?? '' }}" 
                           class="slide-existing-image-input">
                </div>

                {{-- Field 2: Main Headline --}}
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center justify-between">
                        <label class="!text-[11px] !font-extrabold !text-gray-700 !uppercase !tracking-wider !mb-0">
                            <span>Main Headline</span>
                            <span class="text-rose-500 font-bold ml-0.5">*</span>
                        </label>
                    </div>
                    <input type="text" 
                           name="hero_slides[{{ $idx }}][title]" 
                           value="{{ $slide['title'] ?? '' }}" 
                           placeholder="e.g. ELEVATE YOUR CELEBRATIONS" 
                           required 
                           class="slide-title-input w-full bg-white border border-gray-300 px-3.5 py-2.5 text-xs font-bold text-gray-900 rounded-xl outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-all">
                </div>

                {{-- Field 3: Description (Normal Textarea, Max 50 Words) --}}
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center justify-between">
                        <label class="!text-[11px] !font-extrabold !text-gray-700 !uppercase !tracking-wider !mb-0">Description</label>
                        <span id="word-count-{{ $idx }}" class="slide-word-count text-[11px] font-semibold {{ $descWords > 50 ? 'text-rose-600 font-bold' : 'text-gray-400' }}">
                            {{ $descWords }} / 50 words
                        </span>
                    </div>
                    <textarea name="hero_slides[{{ $idx }}][desc]" 
                              rows="3" 
                              placeholder="Short celebration description (max 50 words)..."
                              oninput="handleWordCount(this, {{ $idx }})" 
                              class="slide-desc-input w-full bg-white border border-gray-300 p-3 text-xs text-gray-800 rounded-xl outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 resize-y min-h-[75px] transition-all leading-relaxed">{{ $cleanDesc }}</textarea>
                </div>

                {{-- Field 4: Button Details (Guaranteed Pixel-Perfect Horizontal Alignment) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
                    <div class="flex flex-col gap-1.5">
                        <div class="h-6 flex items-center">
                            <label class="!text-[11px] !font-bold !text-gray-700 !uppercase !tracking-wider !mb-0">Button Text</label>
                        </div>
                        <input type="text" 
                               name="hero_slides[{{ $idx }}][btn1Text]" 
                               value="{{ $slide['btn1Text'] ?? '' }}" 
                               placeholder="e.g. Explore Packages" 
                               class="slide-btn-text-input w-full bg-white border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-all">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <div class="h-6 flex items-center">
                            <label class="!text-[11px] !font-bold !text-gray-700 !uppercase !tracking-wider !mb-0">Button Link URL</label>
                        </div>
                        <input type="text" 
                               name="hero_slides[{{ $idx }}][link1]" 
                               value="{{ $slide['link1'] ?? '' }}" 
                               placeholder="e.g. /events" 
                               class="slide-btn-link-input w-full bg-white border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-all">
                    </div>
                </div>

                {{-- Field 5: Active / Inactive Toggle Switch --}}
                <div class="border-t border-gray-100 pt-3.5 mt-auto">
                    <label class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-gray-100/70 border border-gray-200/80 cursor-pointer select-none transition-colors !mb-0">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full slide-status-dot status-dot-{{ $idx }} {{ $isActive ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                            <span class="text-xs font-bold text-gray-800 slide-status-label status-label-{{ $idx }}">
                                {{ $isActive ? 'Visible on Homepage' : 'Hidden from Homepage' }}
                            </span>
                        </div>
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="hero_slides[{{ $idx }}][is_active]" value="0" class="slide-toggle-hidden">
                            <input type="checkbox" 
                                   name="hero_slides[{{ $idx }}][is_active]" 
                                   value="1" 
                                   {{ $isActive ? 'checked' : '' }} 
                                   onchange="toggleSlideStatus(this, {{ $idx }})" 
                                   class="sr-only peer slide-toggle-checkbox">
                            <div class="w-9 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                        </div>
                    </label>
                </div>

            </div>
        @endforeach
    </div>

    {{-- Bottom Action Bar --}}
    <div class="flex items-center justify-between border-t border-gray-200/80 pt-5 mt-2">
        <button type="button" 
                onclick="addNewSlide()" 
                class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer shadow-2xs">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Add Another Slide</span>
        </button>

        <button type="submit" 
                class="px-6 py-2.5 bg-gray-900 hover:bg-black text-white rounded-xl text-xs font-extrabold uppercase tracking-wider transition-all cursor-pointer shadow-xs hover:shadow flex items-center gap-2">
            <i class="fa-solid fa-check text-xs"></i>
            <span>Save Hero Banners</span>
        </button>
    </div>
</form>

@endsection

@push('page_scripts')
<script>
    // Real-time word counter
    function countWords(str) {
        const cleaned = (str || '').trim();
        if (!cleaned) return 0;
        return cleaned.split(/\s+/).length;
    }

    function handleWordCount(textarea, idx) {
        const badge = document.getElementById('word-count-' + idx);
        if (!badge) return;
        const words = countWords(textarea.value);
        badge.textContent = `${words} / 50 words`;

        if (words > 50) {
            badge.className = 'slide-word-count text-[11px] font-bold text-rose-600';
            textarea.classList.add('border-rose-400');
            textarea.classList.remove('border-gray-300');
        } else {
            badge.className = 'slide-word-count text-[11px] font-semibold text-gray-400';
            textarea.classList.remove('border-rose-400');
            textarea.classList.add('border-gray-300');
        }
    }

    // Toggle switch status label & indicator dot & pill
    function toggleSlideStatus(checkbox, idx) {
        const card = checkbox.closest('.slide-card-wrapper');
        const label = card ? card.querySelector('.slide-status-label') : document.querySelector('.status-label-' + idx);
        const dot = card ? card.querySelector('.slide-status-dot') : document.querySelector('.status-dot-' + idx);
        const pill = card ? card.querySelector('.slide-status-pill') : document.querySelector('.status-pill-' + idx);

        if (checkbox.checked) {
            if (label) label.textContent = 'Visible on Homepage';
            if (dot) {
                dot.classList.remove('bg-gray-400');
                dot.classList.add('bg-emerald-500');
            }
            if (pill) {
                pill.textContent = 'Active';
                pill.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold slide-status-pill bg-emerald-50 text-emerald-700 border border-emerald-200';
            }
        } else {
            if (label) label.textContent = 'Hidden from Homepage';
            if (dot) {
                dot.classList.remove('bg-emerald-500');
                dot.classList.add('bg-gray-400');
            }
            if (pill) {
                pill.textContent = 'Draft';
                pill.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold slide-status-pill bg-gray-100 text-gray-600 border border-gray-200';
            }
        }
    }

    // Image preview with live aspect ratio calculation
    function previewSlideImage(input, idx) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                const card = input.closest('.slide-card-wrapper');
                const previewImg = card ? card.querySelector('.slide-preview-img') : document.getElementById('slide-preview-' + idx);
                const placeholder = card ? card.querySelector('.slide-upload-placeholder') : document.getElementById('slide-placeholder-' + idx);
                const overlay = card ? card.querySelector('.slide-hover-overlay') : document.getElementById('slide-overlay-' + idx);
                const badge = card ? card.querySelector('.slide-ratio-badge') : document.getElementById('ratio-badge-' + idx);

                if (previewImg) {
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('hidden');
                }
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
                if (overlay) {
                    overlay.classList.remove('hidden');
                }
                if (badge) {
                    badge.classList.remove('hidden');
                }

                const img = new Image();
                img.onload = function() {
                    if (badge) {
                        const ratio = (this.width / this.height).toFixed(2);
                        badge.textContent = `${this.width}×${this.height} (${ratio}:1)`;
                    }
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    // Reorder & Index Synchronization
    function updateSlideOrderIndices() {
        const container = document.getElementById('hero-slides-grid');
        const cards = container.querySelectorAll('.slide-card-wrapper');

        cards.forEach((card, newIdx) => {
            card.setAttribute('data-slide-index', newIdx);

            const numBadge = card.querySelector('.slide-number-badge');
            const label = card.querySelector('.slide-label');
            if (numBadge) numBadge.textContent = newIdx + 1;
            if (label) label.textContent = 'Slide #' + (newIdx + 1);

            const orderInput = card.querySelector('.slide-order-input');
            if (orderInput) {
                orderInput.value = newIdx;
                orderInput.name = `hero_slides[${newIdx}][display_order]`;
            }

            const idInput = card.querySelector('.slide-id-input');
            if (idInput) idInput.name = `hero_slides[${newIdx}][id]`;

            const fileInput = card.querySelector('.slide-file-input');
            if (fileInput) {
                fileInput.name = `hero_slides[${newIdx}][image_file]`;
                fileInput.id = `slide-input-${newIdx}`;
                fileInput.setAttribute('onchange', `previewSlideImage(this, ${newIdx})`);
            }

            const imgPreviewWrapper = card.querySelector('.aspect-\\[16\\/9\\]');
            if (imgPreviewWrapper) {
                imgPreviewWrapper.setAttribute('onclick', `document.getElementById('slide-input-${newIdx}').click()`);
            }

            const previewImg = card.querySelector('.slide-preview-img');
            if (previewImg) previewImg.id = `slide-preview-${newIdx}`;

            const placeholder = card.querySelector('.slide-upload-placeholder');
            if (placeholder) placeholder.id = `slide-placeholder-${newIdx}`;

            const overlay = card.querySelector('.slide-hover-overlay');
            if (overlay) overlay.id = `slide-overlay-${newIdx}`;

            const ratioBadge = card.querySelector('.slide-ratio-badge');
            if (ratioBadge) ratioBadge.id = `ratio-badge-${newIdx}`;

            const existingImg = card.querySelector('.slide-existing-image-input');
            if (existingImg) existingImg.name = `hero_slides[${newIdx}][image]`;

            const titleInput = card.querySelector('.slide-title-input');
            if (titleInput) titleInput.name = `hero_slides[${newIdx}][title]`;

            const descTextarea = card.querySelector('.slide-desc-input');
            if (descTextarea) {
                descTextarea.name = `hero_slides[${newIdx}][desc]`;
                descTextarea.setAttribute('oninput', `handleWordCount(this, ${newIdx})`);
            }

            const wordBadge = card.querySelector('.slide-word-count');
            if (wordBadge) wordBadge.id = `word-count-${newIdx}`;

            const btn1Input = card.querySelector('.slide-btn-text-input');
            if (btn1Input) btn1Input.name = `hero_slides[${newIdx}][btn1Text]`;

            const link1Input = card.querySelector('.slide-btn-link-input');
            if (link1Input) link1Input.name = `hero_slides[${newIdx}][link1]`;

            const hiddenActive = card.querySelector('.slide-toggle-hidden');
            if (hiddenActive) hiddenActive.name = `hero_slides[${newIdx}][is_active]`;

            const checkActive = card.querySelector('.slide-toggle-checkbox');
            if (checkActive) {
                checkActive.name = `hero_slides[${newIdx}][is_active]`;
                checkActive.setAttribute('onchange', `toggleSlideStatus(this, ${newIdx})`);
            }

            const statusDot = card.querySelector('.slide-status-dot');
            if (statusDot) {
                statusDot.className = statusDot.className.replace(/status-dot-\d+/, `status-dot-${newIdx}`);
            }

            const statusLabel = card.querySelector('.slide-status-label');
            if (statusLabel) {
                statusLabel.className = statusLabel.className.replace(/status-label-\d+/, `status-label-${newIdx}`);
            }

            const statusPill = card.querySelector('.slide-status-pill');
            if (statusPill) {
                statusPill.className = statusPill.className.replace(/status-pill-\d+/, `status-pill-${newIdx}`);
            }
        });

        // Update header count badge
        const countBadge = document.getElementById('slide-count-badge');
        if (countBadge) {
            countBadge.textContent = `${cards.length} ${cards.length === 1 ? 'Slide' : 'Slides'}`;
        }
    }

    function moveSlideUp(button) {
        const card = button.closest('.slide-card-wrapper');
        const prev = card.previousElementSibling;
        if (prev) {
            card.parentNode.insertBefore(card, prev);
            updateSlideOrderIndices();
        }
    }

    function moveSlideDown(button) {
        const card = button.closest('.slide-card-wrapper');
        const next = card.nextElementSibling;
        if (next) {
            card.parentNode.insertBefore(next, card);
            updateSlideOrderIndices();
        }
    }

    function removeSlide(button) {
        const card = button.closest('.slide-card-wrapper');
        const cardsCount = document.querySelectorAll('.slide-card-wrapper').length;
        if (cardsCount <= 1) {
            alert('At least one hero banner slide is required.');
            return;
        }

        if (confirm('Are you sure you want to delete this hero slide? Remember to click Save All Slides to persist your changes.')) {
            card.remove();
            updateSlideOrderIndices();
        }
    }

    // Add New Slide: Clean, blank template with NO prefilled demo text and NO prefilled default image
    function addNewSlide() {
        const container = document.getElementById('hero-slides-grid');
        const newIdx = container.querySelectorAll('.slide-card-wrapper').length;

        const html = `
            <div class="slide-card-wrapper bg-white border border-gray-200/90 rounded-2xl p-6 flex flex-col gap-5 shadow-xs hover:shadow-md transition-all relative" data-slide-index="${newIdx}">
                
                {{-- Card Header --}}
                <div class="flex justify-between items-center border-b border-gray-100 pb-3.5">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-gray-900 text-white text-xs font-extrabold flex items-center justify-center slide-number-badge shadow-2xs">
                            ${newIdx + 1}
                        </span>
                        <span class="text-xs text-gray-900 font-extrabold uppercase tracking-wide slide-label">Slide #${newIdx + 1}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold slide-status-pill status-pill-${newIdx} bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Active
                        </span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="moveSlideUp(this)" title="Move Left / Earlier" class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-600 text-xs flex items-center justify-center transition-colors cursor-pointer">
                            <i class="fa-solid fa-chevron-left text-[11px]"></i>
                        </button>
                        <button type="button" onclick="moveSlideDown(this)" title="Move Right / Later" class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-600 text-xs flex items-center justify-center transition-colors cursor-pointer">
                            <i class="fa-solid fa-chevron-right text-[11px]"></i>
                        </button>
                        <button type="button" onclick="removeSlide(this)" title="Delete Slide" class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-200/60 text-rose-600 text-xs flex items-center justify-center transition-colors cursor-pointer ml-1">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </div>
                </div>

                <input type="hidden" name="hero_slides[${newIdx}][id]" value="" class="slide-id-input">
                <input type="hidden" name="hero_slides[${newIdx}][display_order]" value="${newIdx}" class="slide-order-input">

                {{-- Field 1: Blank Upload Dropzone (No Fake Concert Image) --}}
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <label class="!text-[11px] !font-extrabold !text-gray-700 !uppercase !tracking-wider !mb-0">Slide Image</label>
                        <span class="text-[11px] text-gray-400 font-medium">16:9 ratio recommended (1376×768)</span>
                    </div>
                    <div class="relative group aspect-[16/9] rounded-2xl overflow-hidden border-2 border-dashed border-gray-200 hover:border-gray-400 bg-gray-50 flex flex-col items-center justify-center cursor-pointer shadow-inner transition-all" onclick="document.getElementById('slide-input-${newIdx}').click()">
                        <img id="slide-preview-${newIdx}" 
                             src="" 
                             alt="Slide Preview" 
                             class="hidden slide-preview-img absolute inset-0 w-full h-full object-cover group-hover:scale-102 transition-transform duration-200">

                        <div id="slide-placeholder-${newIdx}" class="slide-upload-placeholder flex flex-col items-center justify-center p-6 text-center select-none">
                            <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-500 mb-2 group-hover:scale-105 group-hover:bg-gray-200 transition-all">
                                <i class="fa-solid fa-cloud-arrow-up text-lg text-gray-600"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-800">Click to Upload Slide Image</span>
                            <span class="text-[11px] text-gray-400 mt-0.5">JPG, PNG, or WebP up to 5MB</span>
                        </div>

                        <div id="slide-overlay-${newIdx}" class="hidden slide-hover-overlay absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white text-xs font-bold gap-1.5 z-10">
                            <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                            <span>Click to Change Image</span>
                        </div>

                        <span id="ratio-badge-${newIdx}" class="hidden slide-ratio-badge absolute bottom-2.5 right-2.5 px-2.5 py-1 rounded-lg bg-black/80 text-[10px] text-white font-bold uppercase border border-white/10 font-mono shadow-md z-20">
                            16:9
                        </span>
                    </div>

                    <input type="file" 
                           id="slide-input-${newIdx}" 
                           name="hero_slides[${newIdx}][image_file]" 
                           class="hidden slide-file-input" 
                           accept="image/jpeg,image/png,image/webp,image/gif" 
                           onchange="previewSlideImage(this, ${newIdx})">
                    <input type="hidden" 
                           name="hero_slides[${newIdx}][image]" 
                           value="" 
                           class="slide-existing-image-input">
                </div>

                {{-- Field 2: Main Headline (Completely Empty) --}}
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center justify-between">
                        <label class="!text-[11px] !font-extrabold !text-gray-700 !uppercase !tracking-wider !mb-0">
                            <span>Main Headline</span>
                            <span class="text-rose-500 font-bold ml-0.5">*</span>
                        </label>
                    </div>
                    <input type="text" 
                           name="hero_slides[${newIdx}][title]" 
                           value="" 
                           placeholder="e.g. ELEVATE YOUR CELEBRATIONS" 
                           required 
                           class="slide-title-input w-full bg-white border border-gray-300 px-3.5 py-2.5 text-xs font-bold text-gray-900 rounded-xl outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-all">
                </div>

                {{-- Field 3: Description (Completely Empty) --}}
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center justify-between">
                        <label class="!text-[11px] !font-extrabold !text-gray-700 !uppercase !tracking-wider !mb-0">Description</label>
                        <span id="word-count-${newIdx}" class="slide-word-count text-[11px] font-semibold text-gray-400">
                            0 / 50 words
                        </span>
                    </div>
                    <textarea name="hero_slides[${newIdx}][desc]" 
                              rows="3" 
                              placeholder="Short celebration description (max 50 words)..."
                              oninput="handleWordCount(this, ${newIdx})" 
                              class="slide-desc-input w-full bg-white border border-gray-300 p-3 text-xs text-gray-800 rounded-xl outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 resize-y min-h-[75px] transition-all leading-relaxed"></textarea>
                </div>

                {{-- Field 4: Button Details (Completely Empty + Guaranteed Horizontal Alignment) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
                    <div class="flex flex-col gap-1.5">
                        <div class="h-6 flex items-center">
                            <label class="!text-[11px] !font-bold !text-gray-700 !uppercase !tracking-wider !mb-0">Button Text</label>
                        </div>
                        <input type="text" 
                               name="hero_slides[${newIdx}][btn1Text]" 
                               value="" 
                               placeholder="e.g. Explore Packages" 
                               class="slide-btn-text-input w-full bg-white border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-all">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <div class="h-6 flex items-center">
                            <label class="!text-[11px] !font-bold !text-gray-700 !uppercase !tracking-wider !mb-0">Button Link URL</label>
                        </div>
                        <input type="text" 
                               name="hero_slides[${newIdx}][link1]" 
                               value="" 
                               placeholder="e.g. /events" 
                               class="slide-btn-link-input w-full bg-white border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-all">
                    </div>
                </div>

                {{-- Field 5: Active / Inactive Toggle Switch --}}
                <div class="border-t border-gray-100 pt-3.5 mt-auto">
                    <label class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-gray-100/70 border border-gray-200/80 cursor-pointer select-none transition-colors !mb-0">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full slide-status-dot status-dot-${newIdx} bg-emerald-500"></span>
                            <span class="text-xs font-bold text-gray-800 slide-status-label status-label-${newIdx}">Visible on Homepage</span>
                        </div>
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="hero_slides[${newIdx}][is_active]" value="0" class="slide-toggle-hidden">
                            <input type="checkbox" 
                                   name="hero_slides[${newIdx}][is_active]" 
                                   value="1" 
                                   checked 
                                   onchange="toggleSlideStatus(this, ${newIdx})" 
                                   class="sr-only peer slide-toggle-checkbox">
                            <div class="w-9 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                        </div>
                    </label>
                </div>

            </div>
        `;

        container.insertAdjacentHTML('beforeend', html);
        updateSlideOrderIndices();
        
        // Scroll smoothly to newly created slide
        const newCard = container.lastElementChild;
        if (newCard) {
            newCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            const titleInput = newCard.querySelector('.slide-title-input');
            if (titleInput) titleInput.focus();
        }
    }

    // Client-side validation before submit
    function validateHeroForm() {
        const textareas = document.querySelectorAll('.slide-desc-input');
        for (let i = 0; i < textareas.length; i++) {
            const ta = textareas[i];
            const words = countWords(ta.value);
            if (words > 50) {
                alert(`Slide #${i + 1} description exceeds 50 words (currently ${words} words). Please shorten it before saving.`);
                ta.focus();
                return false;
            }
        }
        return true;
    }
</script>
@endpush

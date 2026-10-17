@extends('layouts.admin')

@section('page_title', 'Promise Banner Manager')
@section('page_heading', 'Promise Banner Manager')
@section('page_subheading', 'Manage the full-width panoramic trust and guarantee banner displayed on the homepage')

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
    $pb = $promiseBanner ?? ($cms['promise_banner'] ?? []);
    $isBannerActive = $pb['is_active'] ?? true;
    $bannerImg = $pb['image'] ?? '/images/banners/artizen-promise-banner.webp';
    $bannerTitle = $pb['title'] ?? 'Artizen — Curating Unforgettable Moments, Luxury Weddings & Celebrations';
    $bannerLink = $pb['link'] ?? '/events';
@endphp

{{-- ===================================================================
     2. PAGE HEADER BAR
     =================================================================== --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200/80 pb-5 mb-6">
    <div>
        <h2 class="text-xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2.5">
            <i class="fa-solid fa-gem text-indigo-600 text-lg"></i>
            <span>Promise Banner Manager</span>
        </h2>
        <p class="text-xs text-gray-500 mt-1">Manage the panoramic full-width trust assurance banner, image upload, destination link, and visibility.</p>
    </div>
    <div class="flex items-center gap-2.5">
        <a href="/" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold rounded-xl transition-all border border-gray-200 shadow-2xs">
            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
            <span>Preview on Live Site</span>
        </a>
    </div>
</div>

{{-- ===================================================================
     3. SUMMARY METRIC CARDS
     =================================================================== --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 mb-6">
    <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Banner Status</span>
            <span class="w-7 h-7 rounded-lg {{ $isBannerActive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center text-xs">
                <i class="fa-solid {{ $isBannerActive ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
            </span>
        </div>
        <span class="text-2xl font-extrabold {{ $isBannerActive ? 'text-emerald-600' : 'text-gray-400' }} block mt-2" id="metric-banner-status">
            {{ $isBannerActive ? 'Active' : 'Hidden' }}
        </span>
        <span class="text-[11px] text-gray-400 mt-0.5 block">Homepage display status</span>
    </div>

    <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Aspect Ratio</span>
            <span class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 text-xs">
                <i class="fa-solid fa-expand"></i>
            </span>
        </div>
        <span class="text-2xl font-extrabold text-gray-900 block mt-2">Panoramic</span>
        <span class="text-[11px] text-gray-400 mt-0.5 block">21:9 / 16:6 widescreen</span>
    </div>

    <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Optimization</span>
            <span class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-xs">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </span>
        </div>
        <span class="text-2xl font-extrabold text-indigo-600 block mt-2">WebP Auto</span>
        <span class="text-[11px] text-gray-400 mt-0.5 block">Lossless compression</span>
    </div>

    <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Placement</span>
            <span class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 text-xs">
                <i class="fa-solid fa-layer-group"></i>
            </span>
        </div>
        <span class="text-2xl font-extrabold text-amber-600 block mt-2">Below Categories</span>
        <span class="text-[11px] text-gray-400 mt-0.5 block">Prime trust position</span>
    </div>
</div>

{{-- ===================================================================
     4. BANNER CONFIGURATION FORM
     =================================================================== --}}
<form action="{{ route('admin.promise-banner.save') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
    @csrf

    <div class="bg-white border border-gray-200/80 rounded-2xl p-5 sm:p-6 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4 mb-6">
            <div>
                <h3 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-panorama text-indigo-500"></i>
                    <span>Panoramic Promise Banner</span>
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Upload panoramic banner image, set alt text, and configure destination URL.</p>
            </div>
            <label class="inline-flex items-center gap-3 bg-gray-50 hover:bg-gray-100 px-4 py-2 rounded-xl border border-gray-200 cursor-pointer transition-colors select-none">
                <input type="checkbox" 
                       name="promise_banner[is_active]" 
                       value="1" 
                       {{ $isBannerActive ? 'checked' : '' }} 
                       onchange="updatePromiseBannerMetric(this)"
                       class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500">
                <span class="text-xs font-bold text-gray-800">Banner Enabled on Homepage</span>
            </label>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {{-- Panoramic Image Picker Zone --}}
            <div class="lg:col-span-7 flex flex-col gap-2">
                <label class="text-[11px] text-gray-600 font-extrabold uppercase tracking-wider flex items-center justify-between">
                    <span>Banner Panoramic Image</span>
                    <span class="text-[10px] text-gray-400 font-normal">Recommended 1920x600px (JPEG, PNG, WebP)</span>
                </label>

                <div class="relative group aspect-[21/9] sm:aspect-[16/6] rounded-2xl overflow-hidden border border-gray-200/90 hover:border-gray-900 transition-all bg-gray-100 flex flex-col items-center justify-center cursor-pointer shadow-inner" onclick="document.getElementById('promise-banner-file-input').click()">
                    <img id="promise-banner-preview" 
                         src="{{ $bannerImg }}" 
                         alt="{{ $bannerTitle }}" 
                         class="absolute inset-0 w-full h-full object-cover group-hover:scale-102 transition-transform duration-200">
                    <div class="absolute inset-0 bg-black/55 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white text-xs font-bold gap-1.5 z-10">
                        <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                        <span>Click to Upload New Panoramic Banner</span>
                        <span class="text-[10px] text-gray-300 font-normal">Max size: 5MB</span>
                    </div>
                    <span id="pb-ratio-badge" class="absolute bottom-2.5 right-2.5 px-2.5 py-1 rounded bg-black/80 text-[10px] text-white font-bold uppercase border border-white/10 font-mono shadow-md z-20">
                        Panoramic Banner
                    </span>
                </div>

                <input type="file" 
                       id="promise-banner-file-input" 
                       name="promise_banner[image_file]" 
                       class="hidden" 
                       accept="image/jpeg,image/png,image/webp,image/gif" 
                       onchange="previewPromiseBannerImage(this)">
                <input type="hidden" 
                       name="promise_banner[image]" 
                       value="{{ $bannerImg }}">
                
                <p class="text-[11px] text-gray-500 mt-1">
                    <i class="fa-solid fa-circle-info text-indigo-500 mr-1"></i>
                    Uploaded banners are automatically converted and optimized into responsive WebP assets with safe original-image fallback.
                </p>
            </div>

            {{-- Text & Link Inputs --}}
            <div class="lg:col-span-5 flex flex-col gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] text-gray-600 font-extrabold uppercase tracking-wider">Alt Text & Title</label>
                    <input type="text" 
                           name="promise_banner[title]" 
                           value="{{ $bannerTitle }}" 
                           placeholder="e.g. Artizen — Curating Unforgettable Moments, Luxury Weddings & Celebrations" 
                           required
                           class="w-full bg-white border border-gray-300 px-3.5 py-2.5 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900 transition-colors">
                    <span class="text-[10px] text-gray-400">Used for accessibility screen-readers, SEO meta alt attribute, and hover tooltips.</span>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] text-gray-600 font-extrabold uppercase tracking-wider">Destination Link URL</label>
                    <input type="text" 
                           name="promise_banner[link]" 
                           value="{{ $bannerLink }}" 
                           placeholder="e.g. /events or https://wa.me/919131668156" 
                           required
                           class="w-full bg-white border border-gray-300 px-3.5 py-2.5 text-xs font-semibold text-gray-900 rounded-xl outline-none focus:border-gray-900 transition-colors">
                    <span class="text-[10px] text-gray-400">Customers clicking the banner will be directed to this page or WhatsApp link.</span>
                </div>

                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200/80 flex flex-col gap-2 mt-2">
                    <span class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-shield-halved text-emerald-600 text-xs"></i>
                        <span>Offline Execution Guarantee</span>
                    </span>
                    <p class="text-[11px] text-gray-600 leading-relaxed">
                        This section represents the Artizen hallmark promise of verified on-time venue setups, 100% offline payment security, and direct Indore local crew coordination.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end border-t border-gray-100 pt-5 mt-6">
            <button type="submit" 
                    class="px-6 py-2.5 bg-gray-900 hover:bg-black text-white rounded-xl text-xs font-extrabold uppercase tracking-wider transition-all cursor-pointer shadow-xs hover:shadow flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Save Promise Banner</span>
            </button>
        </div>
    </div>
</form>

@endsection

@push('page_scripts')
<script>
    function previewPromiseBannerImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('promise-banner-preview');
                if (previewImg) {
                    previewImg.src = e.target.result;
                }
                const img = new Image();
                img.onload = function() {
                    const badge = document.getElementById('pb-ratio-badge');
                    if (badge) {
                        const ratio = (this.width / this.height).toFixed(2);
                        badge.textContent = `${this.width}x${this.height} (${ratio}:1)`;
                    }
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    function updatePromiseBannerMetric(checkbox) {
        const metric = document.getElementById('metric-banner-status');
        if (metric) {
            metric.textContent = checkbox.checked ? 'Active' : 'Hidden';
            if (checkbox.checked) {
                metric.className = 'text-2xl font-extrabold text-emerald-600 block mt-2';
            } else {
                metric.className = 'text-2xl font-extrabold text-gray-400 block mt-2';
            }
        }
    }
</script>
@endpush

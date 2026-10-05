@extends('layouts.admin')

@section('page_title', $isEdit ? 'Edit Category: ' . $category->title : 'Add New Category')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12">

    {{-- Top Navigation / Breadcrumbs & Action Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-gray-200/80">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.categories') }}" 
               class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-2xs">
                <i class="fa-solid fa-arrow-left text-[11px]"></i>
                <span>Back to Categories</span>
            </a>
            <span class="text-gray-300">/</span>
            <span class="text-xs font-bold text-gray-900">
                {{ $isEdit ? 'Edit Category' : 'Create Category' }}
            </span>
        </div>

        @if($isEdit)
        <div class="flex items-center gap-2">
            <span class="text-xs font-mono font-semibold text-gray-400 bg-gray-100 px-2.5 py-1 rounded-lg">ID: #{{ $category->id }}</span>
            <form action="{{ route('admin.categories.delete', $category->id) }}" method="POST" class="inline m-0">
                @csrf
                <button type="button" 
                        onclick="confirmDelete(this, '{{ addslashes($category->title) }}')"
                        class="px-3 py-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                    <span>Delete Category</span>
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- Main Category Form --}}
    <form action="{{ route('admin.categories.save') }}" 
          method="POST" 
          enctype="multipart/form-data" 
          id="category-editor-form" 
          onsubmit="serializeSubcategories()">
        @csrf
        @if($isEdit)
            <input type="hidden" name="id" value="{{ $category->id }}">
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- =========================================================
                 LEFT 2 COLUMNS: Form Fields & Subcategory Manager
                 ========================================================= --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- 1. Category Identity & Settings --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 shadow-2xs space-y-5">
                    <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-gray-900 tracking-tight">Category Information</h2>
                                <p class="text-xs text-gray-500">Core details, visual branding, and display preferences.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-wider">Step 1</span>
                    </div>

                    {{-- Row 1: Title & Nav Slug --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Category Title --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="cat-title" class="block text-xs font-bold text-gray-700">
                                    Category Title <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Required</span>
                            </div>
                            <input type="text" 
                                   id="cat-title" 
                                   name="title" 
                                   value="{{ old('title', $category->title) }}" 
                                   required 
                                   placeholder="e.g. Birthdays, House Party & DJ"
                                   class="w-full h-11 bg-white border border-gray-300 rounded-xl px-3.5 text-xs font-semibold text-gray-900 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition-all placeholder:text-gray-400 shadow-2xs"
                                   oninput="updateOccasionLivePreview()">
                            <p class="text-[11px] text-gray-400">Headline for cards, event filters, and navigation.</p>
                        </div>

                        {{-- Nav Slug --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="cat-nav-slug" class="block text-xs font-bold text-gray-700">
                                    Nav Slug <span class="text-gray-400 font-normal">(Filter query param)</span>
                                </label>
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">URL Filter Key</span>
                            </div>
                            <input type="text" 
                                   id="cat-nav-slug" 
                                   name="nav_slug" 
                                   value="{{ old('nav_slug', $category->nav_slug) }}" 
                                   placeholder="cat-birthdays"
                                   class="w-full h-11 bg-white border border-gray-300 rounded-xl px-3.5 text-xs font-mono font-medium text-gray-800 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition-all placeholder:text-gray-400 shadow-2xs"
                                   oninput="updateLiveSlugPreview()">
                            <div class="flex items-center gap-1.5 text-[11px] text-gray-500">
                                <span class="text-gray-400">Used in:</span>
                                <code class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-900 border border-amber-200/60 font-mono text-[11px] inline-flex items-center">
                                    <span>/events?category=</span><strong id="live-slug-param" class="text-amber-700 font-bold">{{ old('nav_slug', $category->nav_slug) ?: 'cat-' }}</strong>
                                </code>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Visual Styling & Card Color --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-1">
                        {{-- Icon Dropdown & Presets --}}
                        <div class="relative space-y-1.5" id="cat-icon-dropdown-container">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-gray-700">
                                    FontAwesome Icon
                                </label>
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Vector Icon</span>
                            </div>
                            
                            <input type="hidden" 
                                   id="cat-icon" 
                                   name="icon" 
                                   value="{{ old('icon', $category->icon ?: 'fa-solid fa-cake-candles') }}">

                            {{-- Dropdown Trigger Button --}}
                            <button type="button" 
                                    id="cat-icon-dropdown-btn" 
                                    onclick="toggleIconDropdown(event)"
                                    class="w-full h-11 flex items-center justify-between bg-white border border-gray-300 hover:border-gray-400 rounded-xl px-3 text-xs font-semibold text-gray-800 focus:outline-none focus:border-gray-900 transition-colors cursor-pointer shadow-2xs">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-7 h-7 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-800 text-sm shrink-0" id="cat-icon-preview">
                                        <i class="{{ old('icon', $category->icon ?: 'fa-solid fa-cake-candles') }}"></i>
                                    </span>
                                    <span class="text-xs font-medium text-gray-500">Choose Icon</span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200" id="cat-icon-chevron"></i>
                            </button>

                            {{-- Dropdown of Pure Icons (No Text) --}}
                            <div id="cat-icon-menu" 
                                 class="hidden absolute z-50 left-0 right-0 top-full mt-1.5 p-2 bg-white border border-gray-200 rounded-2xl shadow-xl max-h-56 overflow-y-auto">
                                @php
                                    $currentIcon = old('icon', $category->icon ?: 'fa-solid fa-cake-candles');
                                    $iconList = [
                                        'fa-solid fa-cake-candles',
                                        'fa-solid fa-volume-high',
                                        'fa-solid fa-music',
                                        'fa-solid fa-heart',
                                        'fa-solid fa-ring',
                                        'fa-solid fa-baby',
                                        'fa-solid fa-briefcase',
                                        'fa-solid fa-champagne-glasses',
                                        'fa-solid fa-sparkles',
                                        'fa-solid fa-wand-magic-sparkles',
                                        'fa-solid fa-gift',
                                        'fa-solid fa-star',
                                        'fa-solid fa-masks-theater',
                                        'fa-solid fa-camera',
                                        'fa-solid fa-spa',
                                        'fa-solid fa-guitar',
                                        'fa-solid fa-lightbulb',
                                        'fa-solid fa-crown',
                                        'fa-solid fa-wine-glass',
                                        'fa-solid fa-martini-glass-citrus',
                                        'fa-solid fa-fire',
                                        'fa-solid fa-gem',
                                        'fa-solid fa-utensils',
                                        'fa-solid fa-bell',
                                        'fa-solid fa-bullhorn',
                                        'fa-solid fa-calendar-days',
                                        'fa-solid fa-compact-disc',
                                        'fa-solid fa-microphone',
                                        'fa-solid fa-headphones',
                                        'fa-solid fa-face-smile',
                                    ];
                                @endphp
                                <div class="grid grid-cols-6 gap-2">
                                    @foreach($iconList as $val)
                                        <button type="button" 
                                                onclick="selectIcon('{{ $val }}')" 
                                                data-icon="{{ $val }}"
                                                title="{{ $val }}"
                                                class="cat-icon-item w-9 h-9 rounded-xl border flex items-center justify-center text-sm transition-all cursor-pointer {{ $currentIcon === $val ? 'bg-gray-900 text-white border-gray-900 shadow-sm' : 'border-gray-100 hover:border-gray-300 bg-gray-50 hover:bg-gray-100 text-gray-700' }}">
                                            <i class="{{ $val }}"></i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Quick Icon Chips --}}
                            <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-0.5">Presets:</span>
                                <button type="button" onclick="selectIcon('fa-solid fa-cake-candles')" class="px-2 py-0.5 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 text-[11px] font-medium transition-colors cursor-pointer">Cake</button>
                                <button type="button" onclick="selectIcon('fa-solid fa-volume-high')" class="px-2 py-0.5 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 text-[11px] font-medium transition-colors cursor-pointer">DJ Sound</button>
                                <button type="button" onclick="selectIcon('fa-solid fa-heart')" class="px-2 py-0.5 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 text-[11px] font-medium transition-colors cursor-pointer">Heart</button>
                                <button type="button" onclick="selectIcon('fa-solid fa-ring')" class="px-2 py-0.5 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 text-[11px] font-medium transition-colors cursor-pointer">Ring</button>
                                <button type="button" onclick="selectIcon('fa-solid fa-baby')" class="px-2 py-0.5 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 text-[11px] font-medium transition-colors cursor-pointer">Baby</button>
                                <button type="button" onclick="selectIcon('fa-solid fa-briefcase')" class="px-2 py-0.5 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 text-[11px] font-medium transition-colors cursor-pointer">Corporate</button>
                                <button type="button" onclick="selectIcon('fa-solid fa-music')" class="px-2 py-0.5 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 text-[11px] font-medium transition-colors cursor-pointer">Music</button>
                            </div>
                        </div>

                        {{-- Pastel Background Color --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="cat-bg-color" class="block text-xs font-bold text-gray-700">
                                    Slider Card Pastel Background
                                </label>
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Card Theme</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="color" 
                                       id="cat-bg-color" 
                                       value="{{ old('bg_color', $category->bg_color ?: '#F6CFB2') }}" 
                                       class="w-11 h-11 rounded-xl border border-gray-300 cursor-pointer p-1 shrink-0 bg-white shadow-2xs"
                                       oninput="syncColor(this.value)">
                                <div class="relative flex-1">
                                    <input type="text" 
                                           id="cat-bg-color-text" 
                                           name="bg_color"
                                           value="{{ old('bg_color', $category->bg_color ?: '#F6CFB2') }}" 
                                           maxlength="7"
                                           placeholder="#F6CFB2"
                                           class="w-full h-11 bg-white border border-gray-300 rounded-xl px-3.5 text-xs font-mono font-medium text-gray-800 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition-all shadow-2xs uppercase"
                                           oninput="syncColorFromText(this.value)">
                                </div>
                            </div>
                            {{-- Quick Pastel Chips --}}
                            <div class="flex items-center gap-2 flex-wrap pt-0.5">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-0.5">Pastels:</span>
                                <button type="button" onclick="syncColor('#F6CFB2')" class="w-5 h-5 rounded-full border border-black/10 hover:scale-110 transition-transform shadow-2xs cursor-pointer" style="background:#F6CFB2" title="Pastel Peach"></button>
                                <button type="button" onclick="syncColor('#F4D2B3')" class="w-5 h-5 rounded-full border border-black/10 hover:scale-110 transition-transform shadow-2xs cursor-pointer" style="background:#F4D2B3" title="Warm Apricot"></button>
                                <button type="button" onclick="syncColor('#F8C6CF')" class="w-5 h-5 rounded-full border border-black/10 hover:scale-110 transition-transform shadow-2xs cursor-pointer" style="background:#F8C6CF" title="Blush Rose"></button>
                                <button type="button" onclick="syncColor('#E8D7F1')" class="w-5 h-5 rounded-full border border-black/10 hover:scale-110 transition-transform shadow-2xs cursor-pointer" style="background:#E8D7F1" title="Soft Lavender"></button>
                                <button type="button" onclick="syncColor('#FFE7BA')" class="w-5 h-5 rounded-full border border-black/10 hover:scale-110 transition-transform shadow-2xs cursor-pointer" style="background:#FFE7BA" title="Pale Gold"></button>
                                <button type="button" onclick="syncColor('#D7EAF3')" class="w-5 h-5 rounded-full border border-black/10 hover:scale-110 transition-transform shadow-2xs cursor-pointer" style="background:#D7EAF3" title="Sky Ice"></button>
                                <button type="button" onclick="syncColor('#D8F3DC')" class="w-5 h-5 rounded-full border border-black/10 hover:scale-110 transition-transform shadow-2xs cursor-pointer" style="background:#D8F3DC" title="Soft Mint"></button>
                            </div>
                        </div>
                    </div>

                    {{-- Row 3: Display Order & Visibility Status --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2 border-t border-gray-100">
                        {{-- Display Order --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="cat-display-order" class="block text-xs font-bold text-gray-700">
                                    Display Sort Order
                                </label>
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Position</span>
                            </div>
                            <input type="number" 
                                   id="cat-display-order" 
                                   name="display_order" 
                                   value="{{ old('display_order', $category->display_order ?? 1) }}" 
                                   min="1"
                                   class="w-full h-11 bg-white border border-gray-300 rounded-xl px-3.5 text-xs font-semibold text-gray-900 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition-all shadow-2xs">
                        </div>

                        {{-- Visibility Status --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="block text-xs font-bold text-gray-700">
                                    Visibility Status
                                </span>
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Live Status</span>
                            </div>
                            <div id="cat-active-card"
                                 onclick="toggleVisibilitySwitch()"
                                 class="flex items-center justify-between h-11 px-3.5 bg-emerald-50/50 border border-emerald-200 hover:border-emerald-300 rounded-xl cursor-pointer transition-all shadow-2xs select-none">
                                <div class="flex items-center gap-2.5">
                                    <span id="cat-active-icon-badge" class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs shrink-0 transition-colors">
                                        <i class="fa-solid fa-eye"></i>
                                    </span>
                                    <span id="cat-active-label" class="text-xs font-bold text-emerald-950">Active & Published</span>
                                </div>

                                {{-- Modern Switch Track & Knob --}}
                                <div class="flex items-center shrink-0">
                                    <input type="checkbox" 
                                           id="cat-active" 
                                           name="active" 
                                           value="1" 
                                           {{ old('active', $category->active ?? true) ? 'checked' : '' }} 
                                           class="hidden"
                                           onchange="updateVisibilityCard(this.checked)">
                                    <div id="cat-active-switch" 
                                         class="w-10 h-6 bg-emerald-500 rounded-full transition-colors relative flex items-center p-0.5 pointer-events-none">
                                        <div id="cat-active-knob" 
                                             class="w-5 h-5 bg-white rounded-full shadow-sm transition-transform transform translate-x-4"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Dedicated Image Assets (Separated) --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 shadow-2xs space-y-5">
                    <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                <i class="fa-solid fa-images text-indigo-500"></i> Distinct Image Assets
                            </h2>
                            <p class="text-xs text-gray-500 mt-0.5">Separate images for the homepage occasion slider and navbar mega-menu.</p>
                        </div>
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Step 2</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Asset A: Homepage Slider Occasion Image --}}
                        <div class="border border-gray-200 rounded-2xl p-4 bg-gray-50/50 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                                    <i class="fa-solid fa-film text-blue-500"></i> Homepage Slider Image
                                </span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md">Occasion Rail</span>
                            </div>
                            <p class="text-[11px] text-gray-500 leading-tight">Shown inside the pastel capsule card in the "Setups For Every Occasion" slider.</p>

                            {{-- Hidden native file input --}}
                            <input type="file" 
                                   name="slider_image_file" 
                                   id="input-slider-file" 
                                   accept="image/*" 
                                   class="hidden"
                                   onchange="handleFileSelect(this, 'slider')">

                            {{-- Hidden existing image value --}}
                            <input type="hidden" 
                                   name="slider_image" 
                                   id="cat-slider-image" 
                                   value="{{ old('slider_image', $category->slider_image) }}">

                            {{-- Interactive Drag & Drop / Paste Dropzone --}}
                            <div id="dropzone-slider"
                                 tabindex="0"
                                 onclick="document.getElementById('input-slider-file').click()"
                                 ondragover="handleDragOver(event, 'slider')"
                                 ondragleave="handleDragLeave(event, 'slider')"
                                 ondrop="handleDrop(event, 'slider')"
                                 onpaste="handlePaste(event, 'slider')"
                                 class="relative min-h-[140px] flex flex-col items-center justify-center p-3 border-2 border-dashed border-gray-300 hover:border-gray-900 rounded-2xl bg-white hover:bg-gray-50/80 transition-all duration-200 cursor-pointer text-center group outline-none focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10">

                                {{-- When image is uploaded: show JUST the image with close/remove icon on top-right --}}
                                <div id="slider-thumb-box" class="{{ $category->slider_image ? '' : 'hidden' }} w-full relative">
                                    <img id="slider-thumb-img" 
                                         src="{{ $category->slider_image ?: '' }}" 
                                         alt="Slider Image" 
                                         class="w-full h-36 object-cover rounded-xl border border-gray-200 shadow-2xs">
                                    <button type="button" 
                                            onclick="removeUploadedImage(event, 'slider')"
                                            class="absolute top-2 right-2 w-7 h-7 rounded-full bg-black/75 hover:bg-rose-600 text-white flex items-center justify-center text-xs shadow-md transition-colors cursor-pointer z-10"
                                            title="Remove image">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>

                                {{-- When NO image is uploaded: show dropzone prompt --}}
                                <div id="slider-prompt-box" class="{{ $category->slider_image ? 'hidden' : 'space-y-1.5' }} py-3 pointer-events-none">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto text-base group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                    </div>
                                    <p class="text-xs font-bold text-gray-800">
                                        <span class="underline decoration-gray-400 group-hover:decoration-gray-900">Click to upload</span> or drag and drop
                                    </p>
                                    <p class="text-[10px] text-gray-400 font-medium">
                                        PNG, JPG, WEBP • <span class="text-gray-600 font-semibold">Ctrl+V / ⌘V to paste</span>
                                    </p>
                                </div>

                                {{-- Feedback toast --}}
                                <div id="slider-drop-feedback" class="hidden absolute bottom-2 px-3 py-1 rounded-lg bg-gray-900 text-white text-[10px] font-bold shadow-md transition-opacity z-20">
                                    Image loaded!
                                </div>
                            </div>
                        </div>

                        {{-- Asset B: Header Dropdown Image --}}
                        <div class="border border-gray-200 rounded-2xl p-4 bg-gray-50/50 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                                    <i class="fa-solid fa-rectangle-list text-purple-500"></i> Dropdown Panel Image
                                </span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md">Mega Menu</span>
                            </div>
                            <p class="text-[11px] text-gray-500 leading-tight">Shown as the tall featured right-hand cover inside the navbar mega-menu dropdown.</p>

                            {{-- Hidden native file input --}}
                            <input type="file" 
                                   name="dropdown_image_file" 
                                   id="input-dropdown-file" 
                                   accept="image/*" 
                                   class="hidden"
                                   onchange="handleFileSelect(this, 'dropdown')">

                            {{-- Hidden existing image value --}}
                            <input type="hidden" 
                                   name="dropdown_image" 
                                   id="cat-dropdown-image" 
                                   value="{{ old('dropdown_image', $category->dropdown_image) }}">

                            {{-- Interactive Drag & Drop / Paste Dropzone --}}
                            <div id="dropzone-dropdown"
                                 tabindex="0"
                                 onclick="document.getElementById('input-dropdown-file').click()"
                                 ondragover="handleDragOver(event, 'dropdown')"
                                 ondragleave="handleDragLeave(event, 'dropdown')"
                                 ondrop="handleDrop(event, 'dropdown')"
                                 onpaste="handlePaste(event, 'dropdown')"
                                 class="relative min-h-[140px] flex flex-col items-center justify-center p-3 border-2 border-dashed border-gray-300 hover:border-gray-900 rounded-2xl bg-white hover:bg-gray-50/80 transition-all duration-200 cursor-pointer text-center group outline-none focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10">

                                {{-- When image is uploaded: show JUST the image with close/remove icon on top-right --}}
                                <div id="dropdown-thumb-box" class="{{ $category->dropdown_image ? '' : 'hidden' }} w-full relative">
                                    <img id="dropdown-thumb-img" 
                                         src="{{ $category->dropdown_image ?: '' }}" 
                                         alt="Dropdown Image" 
                                         class="w-full h-36 object-cover rounded-xl border border-gray-200 shadow-2xs">
                                    <button type="button" 
                                            onclick="removeUploadedImage(event, 'dropdown')"
                                            class="absolute top-2 right-2 w-7 h-7 rounded-full bg-black/75 hover:bg-rose-600 text-white flex items-center justify-center text-xs shadow-md transition-colors cursor-pointer z-10"
                                            title="Remove image">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>

                                {{-- When NO image is uploaded: show dropzone prompt --}}
                                <div id="dropdown-prompt-box" class="{{ $category->dropdown_image ? 'hidden' : 'space-y-1.5' }} py-3 pointer-events-none">
                                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mx-auto text-base group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                    </div>
                                    <p class="text-xs font-bold text-gray-800">
                                        <span class="underline decoration-gray-400 group-hover:decoration-gray-900">Click to upload</span> or drag and drop
                                    </p>
                                    <p class="text-[10px] text-gray-400 font-medium">
                                        PNG, JPG, WEBP • <span class="text-gray-600 font-semibold">Ctrl+V / ⌘V to paste</span>
                                    </p>
                                </div>

                                {{-- Feedback toast --}}
                                <div id="dropdown-drop-feedback" class="hidden absolute bottom-2 px-3 py-1 rounded-lg bg-gray-900 text-white text-[10px] font-bold shadow-md transition-opacity z-20">
                                    Image loaded!
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 mb-1">Promotional Badge Text</label>
                                <input type="text" 
                                       id="cat-dropdown-badge" 
                                       name="dropdown_badge" 
                                       value="{{ old('dropdown_badge', $category->dropdown_badge) }}" 
                                       placeholder="e.g. Popular Choice, Party Setup"
                                       class="w-full bg-white border border-gray-300 rounded-xl px-3 py-2 text-xs font-semibold text-gray-900 focus:border-gray-900 focus:outline-none transition-colors"
                                       oninput="updateDropdownBadge(this.value)">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Subcategories Manager --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 shadow-2xs space-y-4">
                    <div class="border-b border-gray-100 pb-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center text-sm shrink-0">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="text-sm font-bold text-gray-900">Linked Subcategories</h2>
                                    <span id="subcat-counter-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200/60">
                                        {{ $isEdit ? $category->subcategories->count() : 0 }} / 18 items
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">These navigation links populate the columns inside this category's dropdown mega-menu.</p>
                            </div>
                        </div>
                        <button type="button" 
                                id="add-subcat-btn"
                                onclick="addSubcategoryRow('', '', '', true)" 
                                class="h-9 px-3.5 rounded-xl bg-gray-900 hover:bg-black active:scale-95 text-white text-xs font-bold transition-all shadow-xs flex items-center justify-center gap-1.5 cursor-pointer shrink-0">
                            <i class="fa-solid fa-plus text-[10px]"></i> Add Subcategory
                        </button>
                    </div>

                    {{-- Unified Subcategories Data Table --}}
                    <div class="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                        <div class="overflow-x-auto">
                            <div class="min-w-[620px]">
                                {{-- Column Headers --}}
                                <div class="grid grid-cols-[40px_1fr_210px_140px_48px] items-center gap-3 px-4 py-2.5 bg-gray-50/90 border-b border-gray-200/80 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    <span class="text-center">#</span>
                                    <span>Subcategory Name</span>
                                    <span>Group / Section</span>
                                    <span>Badge Pill</span>
                                    <span class="text-center">Action</span>
                                </div>

                                {{-- Dynamic Subcategory Rows List --}}
                                <div id="subcategories-container" class="divide-y divide-gray-100 max-h-[380px] overflow-y-auto">
                                    {{-- Pre-populated rows --}}
                                    @if($isEdit && $category->subcategories->count())
                                        @foreach($category->subcategories as $idx => $sub)
                                            <div class="subcat-row grid grid-cols-[40px_1fr_210px_140px_48px] items-center gap-3 px-4 py-2.5 hover:bg-gray-50/60 transition-colors">
                                                <span class="row-index text-center text-xs font-bold text-gray-400 select-none">{{ $idx + 1 }}</span>
                                                <div>
                                                    <input type="text" 
                                                           placeholder="e.g. Balloon Arch Backdrops" 
                                                           value="{{ $sub->name }}" 
                                                           class="subcat-name w-full h-9 bg-white border border-gray-200 hover:border-gray-300 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 rounded-lg px-3 text-xs font-medium text-gray-900 placeholder:text-gray-400 focus:outline-none transition-all shadow-2xs">
                                                </div>
                                                <div class="relative">
                                                    <input type="text" 
                                                           placeholder="Group (e.g. Setup Themes)" 
                                                           value="{{ $sub->group_name }}" 
                                                           list="group-presets"
                                                           class="subcat-group w-full h-9 bg-white border border-gray-200 hover:border-gray-300 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 rounded-lg px-3 text-xs font-medium text-gray-800 placeholder:text-gray-400 focus:outline-none transition-all shadow-2xs">
                                                </div>
                                                <div class="relative">
                                                    <select class="subcat-badge w-full h-9 bg-white border border-gray-200 hover:border-gray-300 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 rounded-lg pl-3 pr-7 text-xs font-semibold text-gray-700 focus:outline-none transition-all shadow-2xs appearance-none cursor-pointer">
                                                        <option value="" {{ !$sub->badge ? 'selected' : '' }}>— None —</option>
                                                        <option value="Popular" {{ $sub->badge === 'Popular' ? 'selected' : '' }}>Popular</option>
                                                        <option value="Top" {{ $sub->badge === 'Top' ? 'selected' : '' }}>Top</option>
                                                        <option value="Trending" {{ $sub->badge === 'Trending' ? 'selected' : '' }}>Trending</option>
                                                        <option value="New" {{ $sub->badge === 'New' ? 'selected' : '' }}>New</option>
                                                        <option value="Special" {{ $sub->badge === 'Special' ? 'selected' : '' }}>Special</option>
                                                    </select>
                                                    <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] text-gray-400 pointer-events-none"></i>
                                                </div>
                                                <div class="text-center">
                                                    <button type="button" 
                                                            onclick="removeSubcategoryRow(this)" 
                                                            class="w-8 h-8 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors cursor-pointer mx-auto"
                                                            title="Remove subcategory">
                                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                {{-- Empty state --}}
                                <div id="subcat-empty-state" class="py-10 text-center px-4 hidden">
                                    <div class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-200 text-gray-400 flex items-center justify-center mx-auto mb-2 text-sm">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </div>
                                    <h4 class="text-xs font-bold text-gray-800">No subcategories linked</h4>
                                    <p class="text-[11px] text-gray-400 mt-0.5">Click below to add navigation links inside this category's dropdown menu.</p>
                                    <button type="button" 
                                            onclick="addSubcategoryRow('', '', '', true)" 
                                            class="mt-3 px-3 py-1.5 rounded-lg bg-gray-900 hover:bg-black text-white text-xs font-bold inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
                                        <i class="fa-solid fa-plus text-[10px]"></i> Add First Subcategory
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- Datalist for preset groups --}}
                    <datalist id="group-presets">
                        <option value="Setup Themes">
                        <option value="By Milestones">
                        <option value="Experiences">
                        <option value="Sound & DJ Rigs">
                        <option value="Lighting & FX">
                        <option value="Venues">
                        <option value="Romantic Setups">
                        <option value="Pre-Wedding Events">
                        <option value="Stage & Decor">
                        <option value="Baby Shower">
                        <option value="Kids Themes">
                        <option value="Seminars & AV">
                        <option value="Galas & Nights">
                    </datalist>

                    <input type="hidden" name="subcategories_json" id="subcategories-json">
                </div>

            </div>


            {{-- =========================================================
                 RIGHT COLUMN: Live Previews & Action Card
                 ========================================================= --}}
            <div class="space-y-6">

                {{-- Action / Save Card --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-2xs space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Save Changes</h3>
                    
                    <button type="submit" 
                            class="w-full py-3 px-4 rounded-xl bg-gray-900 hover:bg-black active:scale-[0.99] text-white text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>{{ $isEdit ? 'Update Category' : 'Save Category' }}</span>
                    </button>

                    <a href="{{ route('admin.categories') }}" 
                       class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-colors flex items-center justify-center text-center">
                        Cancel & Return
                    </a>
                </div>

                {{-- Live Preview A: Occasion Slider Card --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-2xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-900">Slider Capsule Preview</span>
                        <span class="text-[9px] font-bold uppercase tracking-wider text-gray-400">Homepage</span>
                    </div>

                    <div id="live-slider-box" 
                         class="relative w-full h-[145px] rounded-2xl overflow-hidden shadow-xs border border-black/5 transition-colors"
                         style="background: {{ $category->bg_color ?: '#F6CFB2' }};">
                        <img id="preview-slider-img" 
                             src="{{ $category->slider_image ?: '' }}" 
                             alt="Preview" 
                             class="{{ $category->slider_image ? '' : 'hidden' }} w-full h-full object-cover object-right block select-none pointer-events-none">
                        <div id="preview-slider-empty" class="{{ $category->slider_image ? 'hidden' : 'flex' }} absolute inset-0 items-center justify-center text-gray-700/30 text-xs font-semibold gap-1.5 select-none pointer-events-none">
                            <i class="fa-regular fa-image text-sm"></i> No image
                        </div>
                        <div class="absolute top-3.5 left-4 z-10 pointer-events-none">
                            <span id="live-slider-title" class="text-[14px] font-heading font-extrabold text-gray-900 flex items-center gap-1 drop-shadow-sm">
                                {{ $category->title ?: 'Category Title' }} <i class="fa-solid fa-angle-right text-[11px] opacity-75"></i>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Live Preview B: Mega-Dropdown Panel --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-2xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-900">Dropdown Panel Preview</span>
                        <span class="text-[9px] font-bold uppercase tracking-wider text-gray-400">Navbar Menu</span>
                    </div>

                    <div class="relative w-full h-[160px] rounded-2xl overflow-hidden shadow-xs border border-gray-200 bg-gray-100 flex items-center justify-center">
                        <img id="preview-dropdown-img" 
                             src="{{ $category->dropdown_image ?: '' }}" 
                             alt="Dropdown Preview" 
                             class="{{ $category->dropdown_image ? '' : 'hidden' }} w-full h-full object-cover block">
                        <div id="preview-dropdown-empty" class="{{ $category->dropdown_image ? 'hidden' : 'flex' }} flex-col items-center justify-center text-gray-400 text-xs font-medium gap-1.5 select-none pointer-events-none">
                            <i class="fa-regular fa-image text-2xl text-gray-300"></i>
                            <span>No image uploaded</span>
                        </div>
                        <span id="live-dropdown-badge" 
                              class="absolute top-3 left-3 bg-[#FFD600] text-[#171719] text-[9.5px] font-heading font-extrabold uppercase px-2.5 py-0.5 rounded-full shadow-md tracking-wider {{ $category->dropdown_badge ? '' : 'hidden' }}">
                            {{ $category->dropdown_badge ?: 'Popular Choice' }}
                        </span>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>

@push('page_scripts')
<script>
    // Live slug URL preview updater
    function updateLiveSlugPreview() {
        const slugInput = document.getElementById('cat-nav-slug');
        const previewEl = document.getElementById('live-slug-param');
        if (!slugInput || !previewEl) return;
        
        let val = slugInput.value.trim();
        previewEl.textContent = val || 'cat-';
    }

    // Live preview sync
    function updateOccasionLivePreview() {
        const title = document.getElementById('cat-title').value.trim();
        const el = document.getElementById('live-slider-title');
        if (el) {
            el.innerHTML = (title || 'Category Title') + ' <i class="fa-solid fa-angle-right text-[11px] opacity-75"></i>';
        }

        // Auto-slug if empty and user is typing
        const slugInput = document.getElementById('cat-nav-slug');
        if (slugInput && !slugInput.dataset.manual) {
            const cleanSlug = title ? ('cat-' + title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '')) : 'cat-';
            slugInput.value = cleanSlug;
        }
        updateLiveSlugPreview();
    }

    document.getElementById('cat-nav-slug').addEventListener('input', function() {
        this.dataset.manual = 'true';
        updateLiveSlugPreview();
    });

    function syncColor(hex) {
        if (!hex) return;
        const hexUpper = hex.toUpperCase();
        const hexLower = hex.toLowerCase();

        const colorInput = document.getElementById('cat-bg-color');
        const textInput = document.getElementById('cat-bg-color-text');
        const liveSlider = document.getElementById('live-slider-box');

        if (colorInput) colorInput.value = hexLower;
        if (textInput) textInput.value = hexUpper;
        if (liveSlider) liveSlider.style.background = hex;
    }

    function syncColorFromText(val) {
        if (!val) return;
        let clean = val.trim();

        // If user typed hex characters without leading '#', auto-prepend '#'
        if (!clean.startsWith('#') && /^[0-9a-fA-F]{1,6}$/.test(clean)) {
            clean = '#' + clean;
            const textInput = document.getElementById('cat-bg-color-text');
            if (textInput) {
                textInput.value = clean.toUpperCase();
            }
        }

        // Expand 3-digit hex like #abc -> #aabbcc
        let fullHex = clean;
        if (/^#[0-9a-fA-F]{3}$/.test(clean)) {
            fullHex = '#' + clean[1] + clean[1] + clean[2] + clean[2] + clean[3] + clean[3];
        }

        // When valid 6-digit hex is reached, show color on picker & slider preview
        if (/^#[0-9a-fA-F]{6}$/.test(fullHex)) {
            const hexLower = fullHex.toLowerCase();
            const colorInput = document.getElementById('cat-bg-color');
            const liveSlider = document.getElementById('live-slider-box');

            if (colorInput) {
                colorInput.value = hexLower;
            }
            if (liveSlider) {
                liveSlider.style.background = fullHex;
            }
        }
    }

    function toggleIconDropdown(e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }
        const menu = document.getElementById('cat-icon-menu');
        const chevron = document.getElementById('cat-icon-chevron');
        if (!menu) return;
        const isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            menu.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');
        } else {
            menu.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    }

    function selectIcon(iconClass) {
        const iconInput = document.getElementById('cat-icon');
        if (iconInput) iconInput.value = iconClass;

        const previewI = document.querySelector('#cat-icon-preview i');
        if (previewI) previewI.className = iconClass;

        // Update active class on grid items
        document.querySelectorAll('.cat-icon-item').forEach(btn => {
            if (btn.getAttribute('data-icon') === iconClass) {
                btn.className = 'cat-icon-item w-9 h-9 rounded-xl border flex items-center justify-center text-sm transition-all cursor-pointer bg-gray-900 text-white border-gray-900 shadow-sm';
            } else {
                btn.className = 'cat-icon-item w-9 h-9 rounded-xl border flex items-center justify-center text-sm transition-all cursor-pointer border-gray-100 hover:border-gray-300 bg-gray-50 hover:bg-gray-100 text-gray-700';
            }
        });

        // Close dropdown
        const menu = document.getElementById('cat-icon-menu');
        const chevron = document.getElementById('cat-icon-chevron');
        if (menu) menu.classList.add('hidden');
        if (chevron) chevron.classList.remove('rotate-180');
    }

    // Close icon dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const container = document.getElementById('cat-icon-dropdown-container');
        const menu = document.getElementById('cat-icon-menu');
        const chevron = document.getElementById('cat-icon-chevron');
        if (container && menu && !container.contains(e.target)) {
            menu.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    });

    function updateDropdownBadge(badge) {
        const el = document.getElementById('live-dropdown-badge');
        if (!el) return;
        if (badge.trim()) {
            el.textContent = badge;
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    }

    // =========================================================================
    // DRAG & DROP, FILE UPLOAD & CLIPBOARD COPY-PASTE ENGINE
    // =========================================================================
    let activeDropzoneType = 'slider';

    document.addEventListener('click', function(e) {
        if (e.target.closest('#dropzone-slider')) activeDropzoneType = 'slider';
        if (e.target.closest('#dropzone-dropdown')) activeDropzoneType = 'dropdown';
    });

    function handleDragOver(e, type) {
        e.preventDefault();
        e.stopPropagation();
        const zone = document.getElementById('dropzone-' + type);
        if (zone) {
            zone.classList.add('border-gray-900', 'bg-gray-100', 'ring-2', 'ring-gray-900/10');
        }
    }

    function handleDragLeave(e, type) {
        e.preventDefault();
        e.stopPropagation();
        const zone = document.getElementById('dropzone-' + type);
        if (zone) {
            zone.classList.remove('border-gray-900', 'bg-gray-100', 'ring-2', 'ring-gray-900/10');
        }
    }

    function handleDrop(e, type) {
        e.preventDefault();
        e.stopPropagation();
        handleDragLeave(e, type);

        const files = e.dataTransfer ? e.dataTransfer.files : null;
        if (files && files.length > 0) {
            for (let i = 0; i < files.length; i++) {
                if (files[i].type.startsWith('image/')) {
                    assignFileToInput(files[i], type);
                    break;
                }
            }
        }
    }

    function handleFileSelect(input, type) {
        if (input.files && input.files[0]) {
            processAndPreviewFile(input.files[0], type);
            showDropFeedback(type, 'File selected!');
        }
    }

    function handlePaste(e, type) {
        const items = (e.clipboardData || e.originalEvent?.clipboardData)?.items;
        if (!items) return;

        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                e.preventDefault();
                e.stopPropagation();
                const blob = items[i].getAsFile();
                if (blob) {
                    const file = new File([blob], `${type}_pasted_${Date.now()}.png`, { type: blob.type || 'image/png' });
                    assignFileToInput(file, type);
                    showDropFeedback(type, 'Pasted image from clipboard!');
                    break;
                }
            }
        }
    }

    // Global paste listener: captures Ctrl+V / ⌘V anywhere on page unless typing in text inputs
    window.addEventListener('paste', function(e) {
        const targetTag = (e.target.tagName || '').toLowerCase();
        if ((targetTag === 'input' && e.target.type === 'text') || targetTag === 'textarea') {
            return;
        }

        const items = (e.clipboardData || e.originalEvent?.clipboardData)?.items;
        if (!items) return;

        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                const blob = items[i].getAsFile();
                if (blob) {
                    e.preventDefault();
                    const type = activeDropzoneType || 'slider';
                    const file = new File([blob], `${type}_pasted_${Date.now()}.png`, { type: blob.type || 'image/png' });
                    assignFileToInput(file, type);
                    showDropFeedback(type, 'Pasted into ' + (type === 'slider' ? 'Slider Image' : 'Dropdown Image') + '!');
                    break;
                }
            }
        }
    });

    function assignFileToInput(file, type) {
        const input = document.getElementById('input-' + type + '-file');
        if (!input) return;

        try {
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
        } catch(err) {
            console.warn('DataTransfer files assignment fallback', err);
        }

        processAndPreviewFile(file, type);
    }

    function removeUploadedImage(e, type) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }

        // 1. Clear hidden image input (so backend knows it was removed)
        const hiddenInput = document.getElementById('cat-' + type + '-image');
        if (hiddenInput) hiddenInput.value = '';

        // 2. Clear native file input
        const fileInput = document.getElementById('input-' + type + '-file');
        if (fileInput) fileInput.value = '';

        // 3. Hide thumbnail preview, reveal upload prompt
        const thumbBox = document.getElementById(type + '-thumb-box');
        const thumbImg = document.getElementById(type + '-thumb-img');
        const promptBox = document.getElementById(type + '-prompt-box');

        if (thumbImg) thumbImg.src = '';
        if (thumbBox) thumbBox.classList.add('hidden');
        if (promptBox) promptBox.classList.remove('hidden');

        // 4. Update sidebar live preview
        if (type === 'slider') {
            const previewSlider = document.getElementById('preview-slider-img');
            const emptySlider = document.getElementById('preview-slider-empty');
            if (previewSlider) {
                previewSlider.src = '';
                previewSlider.classList.add('hidden');
            }
            if (emptySlider) emptySlider.classList.remove('hidden');
        } else if (type === 'dropdown') {
            const previewDropdown = document.getElementById('preview-dropdown-img');
            const emptyDropdown = document.getElementById('preview-dropdown-empty');
            if (previewDropdown) {
                previewDropdown.src = '';
                previewDropdown.classList.add('hidden');
            }
            if (emptyDropdown) emptyDropdown.classList.remove('hidden');
        }
    }

    function processAndPreviewFile(file, type) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const dataUrl = e.target.result;

            // 1. Update dropzone: show image, hide upload prompt
            const thumbBox = document.getElementById(type + '-thumb-box');
            const thumbImg = document.getElementById(type + '-thumb-img');
            const promptBox = document.getElementById(type + '-prompt-box');

            if (thumbImg) thumbImg.src = dataUrl;
            if (thumbBox) thumbBox.classList.remove('hidden');
            if (promptBox) promptBox.classList.add('hidden');

            // 2. Update Live Preview in the sidebar
            if (type === 'slider') {
                const previewSlider = document.getElementById('preview-slider-img');
                const emptySlider = document.getElementById('preview-slider-empty');
                if (previewSlider) {
                    previewSlider.src = dataUrl;
                    previewSlider.classList.remove('hidden');
                }
                if (emptySlider) emptySlider.classList.add('hidden');
            } else if (type === 'dropdown') {
                const previewDropdown = document.getElementById('preview-dropdown-img');
                const emptyDropdown = document.getElementById('preview-dropdown-empty');
                if (previewDropdown) {
                    previewDropdown.src = dataUrl;
                    previewDropdown.classList.remove('hidden');
                }
                if (emptyDropdown) emptyDropdown.classList.remove('hidden');
            }
        };
        reader.readAsDataURL(file);
    }

    function showDropFeedback(type, message) {
        const feedback = document.getElementById(type + '-drop-feedback');
        if (!feedback) return;
        feedback.textContent = message;
        feedback.classList.remove('hidden');
        feedback.style.opacity = '1';
        setTimeout(() => {
            feedback.classList.add('hidden');
        }, 3000);
    }

    const MAX_SUBCAT_LIMIT = 18;

    // Subcategory rows state & helpers
    function updateSubcategoryState() {
        const rows = document.querySelectorAll('#subcategories-container .subcat-row');
        const emptyState = document.getElementById('subcat-empty-state');
        const badge = document.getElementById('subcat-counter-badge');
        const addBtn = document.getElementById('add-subcat-btn');

        const count = rows.length;

        if (badge) {
            if (count >= MAX_SUBCAT_LIMIT) {
                badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200';
                badge.textContent = `${count} / ${MAX_SUBCAT_LIMIT} items (Max)`;
            } else {
                badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200/60';
                badge.textContent = `${count} / ${MAX_SUBCAT_LIMIT} items`;
            }
        }

        if (addBtn) {
            if (count >= MAX_SUBCAT_LIMIT) {
                addBtn.disabled = true;
                addBtn.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                addBtn.title = 'Maximum 18 subcategories reached';
            } else {
                addBtn.disabled = false;
                addBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                addBtn.title = 'Add Subcategory';
            }
        }

        if (count === 0) {
            if (emptyState) emptyState.classList.remove('hidden');
        } else {
            if (emptyState) emptyState.classList.add('hidden');
            rows.forEach((r, idx) => {
                const indexEl = r.querySelector('.row-index');
                if (indexEl) indexEl.textContent = idx + 1;
            });
        }
    }

    function removeSubcategoryRow(btn) {
        const row = btn.closest('.subcat-row');
        if (row) {
            row.remove();
            updateSubcategoryState();
        }
    }

    function addSubcategoryRow(name = '', group = '', badge = '', shouldFocus = false) {
        const container = document.getElementById('subcategories-container');
        const rows = document.querySelectorAll('#subcategories-container .subcat-row');
        
        if (rows.length >= MAX_SUBCAT_LIMIT) {
            alert(`Maximum ${MAX_SUBCAT_LIMIT} subcategories permitted per category.\n\nThis ensures your navbar mega-menu remains balanced (3 columns × 6 rows max) and avoids vertical screen overflow.`);
            return;
        }

        // Smart group default if not supplied
        if (!group) {
            const lastGroupInput = container.querySelector('.subcat-row:last-child .subcat-group');
            group = lastGroupInput ? lastGroupInput.value.trim() : 'Setup Themes';
        }

        const row = document.createElement('div');
        row.className = 'subcat-row grid grid-cols-[40px_1fr_210px_140px_48px] items-center gap-3 px-4 py-2.5 hover:bg-gray-50/60 transition-colors';
        row.innerHTML = `
            <span class="row-index text-center text-xs font-bold text-gray-400 select-none">${rows.length + 1}</span>
            <div>
                <input type="text" 
                       placeholder="e.g. Balloon Arch Backdrops" 
                       value="${esc(name)}" 
                       class="subcat-name w-full h-9 bg-white border border-gray-200 hover:border-gray-300 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 rounded-lg px-3 text-xs font-medium text-gray-900 placeholder:text-gray-400 focus:outline-none transition-all shadow-2xs">
            </div>
            <div class="relative">
                <input type="text" 
                       placeholder="Group (e.g. Setup Themes)" 
                       value="${esc(group)}" 
                       list="group-presets"
                       class="subcat-group w-full h-9 bg-white border border-gray-200 hover:border-gray-300 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 rounded-lg px-3 text-xs font-medium text-gray-800 placeholder:text-gray-400 focus:outline-none transition-all shadow-2xs">
            </div>
            <div class="relative">
                <select class="subcat-badge w-full h-9 bg-white border border-gray-200 hover:border-gray-300 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 rounded-lg pl-3 pr-7 text-xs font-semibold text-gray-700 focus:outline-none transition-all shadow-2xs appearance-none cursor-pointer">
                    <option value="" ${badge === '' ? 'selected' : ''}>— None —</option>
                    <option value="Popular" ${badge === 'Popular' ? 'selected' : ''}>Popular</option>
                    <option value="Top" ${badge === 'Top' ? 'selected' : ''}>Top</option>
                    <option value="Trending" ${badge === 'Trending' ? 'selected' : ''}>Trending</option>
                    <option value="New" ${badge === 'New' ? 'selected' : ''}>New</option>
                    <option value="Special" ${badge === 'Special' ? 'selected' : ''}>Special</option>
                </select>
                <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] text-gray-400 pointer-events-none"></i>
            </div>
            <div class="text-center">
                <button type="button" 
                        onclick="removeSubcategoryRow(this)" 
                        class="w-8 h-8 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors cursor-pointer mx-auto"
                        title="Remove subcategory">
                    <i class="fa-regular fa-trash-can text-xs"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        updateSubcategoryState();

        const nameInput = row.querySelector('.subcat-name');
        if (nameInput && shouldFocus) {
            nameInput.focus();
        }
    }

    function serializeSubcategories() {
        const rows = document.querySelectorAll('#subcategories-container .subcat-row');
        const items = [];
        rows.forEach(r => {
            if (items.length >= MAX_SUBCAT_LIMIT) return; // Enforce maximum 18 limit
            const name = r.querySelector('.subcat-name')?.value?.trim();
            if (!name) return;
            let group = r.querySelector('.subcat-group')?.value?.trim();
            if (!group) {
                group = 'Setup Themes'; // Ensure group is never empty
            }
            items.push({
                name: name,
                group_name: group,
                badge: r.querySelector('.subcat-badge')?.value || ''
            });
        });
        document.getElementById('subcategories-json').value = JSON.stringify(items);
    }

    function esc(s) {
        return (s || '').replace(/"/g, '&quot;');
    }

    function toggleVisibilitySwitch() {
        const checkbox = document.getElementById('cat-active');
        if (!checkbox) return;
        checkbox.checked = !checkbox.checked;
        updateVisibilityCard(checkbox.checked);
    }

    function updateVisibilityCard(isActive) {
        const card = document.getElementById('cat-active-card');
        const badge = document.getElementById('cat-active-icon-badge');
        const label = document.getElementById('cat-active-label');
        const sw = document.getElementById('cat-active-switch');
        const knob = document.getElementById('cat-active-knob');
        const icon = badge ? badge.querySelector('i') : null;

        if (isActive) {
            if (card) {
                card.classList.remove('bg-gray-50', 'border-gray-200', 'hover:border-gray-300');
                card.classList.add('bg-emerald-50/50', 'border-emerald-200', 'hover:border-emerald-300');
            }
            if (badge) {
                badge.className = 'w-6 h-6 rounded-md bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs shrink-0 transition-colors';
            }
            if (icon) {
                icon.className = 'fa-solid fa-eye text-emerald-600';
            }
            if (label) {
                label.textContent = 'Active & Published';
                label.className = 'text-xs font-bold text-emerald-950';
            }
            if (sw) {
                sw.className = 'w-10 h-6 bg-emerald-500 rounded-full transition-colors relative flex items-center p-0.5 pointer-events-none';
            }
            if (knob) {
                knob.className = 'w-5 h-5 bg-white rounded-full shadow-sm transition-transform transform translate-x-4';
            }
        } else {
            if (card) {
                card.classList.remove('bg-emerald-50/50', 'border-emerald-200', 'hover:border-emerald-300');
                card.classList.add('bg-gray-50', 'border-gray-200', 'hover:border-gray-300');
            }
            if (badge) {
                badge.className = 'w-6 h-6 rounded-md bg-gray-200 text-gray-500 flex items-center justify-center text-xs shrink-0 transition-colors';
            }
            if (icon) {
                icon.className = 'fa-solid fa-eye-slash text-gray-500';
            }
            if (label) {
                label.textContent = 'Hidden (Draft)';
                label.className = 'text-xs font-bold text-gray-600';
            }
            if (sw) {
                sw.className = 'w-10 h-6 bg-gray-300 rounded-full transition-colors relative flex items-center p-0.5 pointer-events-none';
            }
            if (knob) {
                knob.className = 'w-5 h-5 bg-white rounded-full shadow-sm transition-transform transform translate-x-0';
            }
        }
    }

    // Add initial row if creating new category and container empty
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('subcategories-container');
        if (container && container.children.length === 0) {
            addSubcategoryRow('', '', '', false);
        } else {
            updateSubcategoryState();
        }

        const activeCheckbox = document.getElementById('cat-active');
        if (activeCheckbox) {
            updateVisibilityCard(activeCheckbox.checked);
        }
    });
</script>
@endpush
@endsection

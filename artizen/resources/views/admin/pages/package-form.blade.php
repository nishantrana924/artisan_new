@extends('layouts.admin')

@section('page_title', $isEdit ? 'Edit Package: ' . $package->title : 'Add New Event Package')
@section('page_heading', $isEdit ? 'Edit Package' : 'Create Event Package')
@section('page_subheading', 'Define package details, category-subcategory taxonomy, pricing tiers, and gallery images.')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-16">

    {{-- Top Navigation / Breadcrumbs & Action Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-gray-200/80">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.packages') }}" 
               class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-2xs">
                <i class="fa-solid fa-arrow-left text-[11px]"></i>
                <span>Back to Packages</span>
            </a>
            <span class="text-gray-300">/</span>
            <span class="text-xs font-bold text-gray-900">
                {{ $isEdit ? 'Edit Package' : 'Create Package' }}
            </span>
        </div>

        @if($isEdit)
        <div class="flex items-center gap-2">
            <span class="text-xs font-mono font-semibold text-gray-500 bg-gray-100 border border-gray-200/60 px-2.5 py-1 rounded-lg">
                ID: #{{ $package->id }}
            </span>
            <form action="{{ route('admin.packages.destroy', $package->id) }}" method="POST" class="inline m-0">
                @csrf
                <button type="button" 
                        onclick="confirmDeletePackage(this, '{{ addslashes($package->title) }}')"
                        class="px-3 py-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                    <span>Delete Package</span>
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- Alerts & Validation Errors --}}
    @if ($errors->any())
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium space-y-1 shadow-2xs">
        <div class="flex items-center gap-2 font-bold text-rose-900">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>Please correct the errors below:</span>
        </div>
        <ul class="list-disc list-inside space-y-0.5 pl-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Main Package Form --}}
    <form action="{{ route('admin.packages.save') }}" 
          method="POST" 
          enctype="multipart/form-data" 
          id="package-editor-form"
          onsubmit="return validateAndSubmitForm()">
        @csrf
        @if($isEdit)
            <input type="hidden" name="id" value="{{ $package->id }}">
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- =========================================================
                 LEFT 2 COLUMNS: Form Configuration Sections
                 ========================================================= --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- CARD 1: Category & Subcategory Taxonomy --}}
                <div class="bg-white border border-gray-200/80 rounded-2xl p-5 sm:p-6 shadow-2xs space-y-5">
                    <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-sitemap"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-gray-900 tracking-tight">Category & Subcategories</h2>
                                <p class="text-xs text-gray-500">Assign category first, then tag associated subcategories for customer discovery.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200/50 text-[10px] font-bold uppercase tracking-wider">Taxonomy</span>
                    </div>

                    {{-- Category Selection Dropdown --}}
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="pkg-category-id" class="block text-xs font-bold text-gray-700">
                                Primary Category <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Required</span>
                        </div>
                        <select id="pkg-category-id" 
                                name="category_id" 
                                required 
                                onchange="onCategoryChanged()"
                                class="w-full h-11 bg-white border border-gray-300 rounded-xl px-3.5 text-xs font-semibold text-gray-900 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition-all shadow-2xs cursor-pointer">
                            <option value="">-- Choose Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" 
                                        {{ old('category_id', $package->category_id) == $cat->id ? 'selected' : '' }}
                                        data-icon="{{ $cat->icon }}"
                                        data-title="{{ $cat->title }}"
                                        data-badge="{{ $cat->badge ?? '' }}">
                                    {{ $cat->title }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-gray-400">Selecting a category reveals its available celebration subcategories below.</p>
                    </div>

                    {{-- Dynamic Subcategories Selector (Dropdown + Added Chips Below) --}}
                    <div class="space-y-3 pt-3 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <label class="block text-xs font-bold text-gray-800">
                                    Associated Subcategories
                                </label>
                                <span id="subcat-count-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200/80">
                                    0 Selected
                                </span>
                            </div>
                            <div class="flex items-center gap-2" id="subcat-bulk-actions" style="display: none;">
                                <button type="button" 
                                        onclick="selectAllSubcategories()" 
                                        class="px-2.5 py-1 rounded-lg text-xs font-bold text-amber-800 hover:bg-amber-50 transition-colors cursor-pointer">
                                    + Add All
                                </button>
                                <span class="text-gray-300 text-xs">•</span>
                                <button type="button" 
                                        onclick="clearAllSubcategories()" 
                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-colors cursor-pointer">
                                    Clear All
                                </button>
                            </div>
                        </div>

                        {{-- Empty Category Notice --}}
                        <div id="subcat-empty-notice" class="p-4 bg-gray-50/70 border border-gray-200/80 rounded-xl text-center py-4 text-xs text-gray-400">
                            <i class="fa-solid fa-hand-pointer mr-1.5 text-gray-300"></i>
                            Select a Category above to choose and assign subcategories.
                        </div>

                        {{-- Dropdown Container & Selected Chips --}}
                        <div id="subcat-dropdown-wrapper" class="space-y-3" style="display: none;">
                            <!-- Dropdown Select Row -->
                            <div class="relative">
                                <div class="relative">
                                    <input type="text"
                                           id="subcat-search-picker"
                                           placeholder="Type to search or click to pick subcategory..."
                                           autocomplete="off"
                                           onclick="openSubcatDropdown()"
                                           oninput="filterSubcatDropdown(this.value)"
                                           class="w-full h-11 bg-white border border-gray-300 hover:border-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 rounded-xl pl-10 pr-10 text-xs font-medium text-gray-900 placeholder:text-gray-400 transition-all outline-none shadow-2xs cursor-pointer">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </span>
                                    <button type="button" 
                                            onclick="toggleSubcatDropdown()" 
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 text-xs p-1 cursor-pointer">
                                        <i class="fa-solid fa-chevron-down text-[11px] transition-transform duration-200" id="subcat-dropdown-arrow"></i>
                                    </button>
                                </div>

                                <!-- Floating Dropdown List -->
                                <div id="subcat-dropdown-menu" 
                                     class="hidden absolute z-30 left-0 right-0 mt-1 max-h-56 overflow-y-auto bg-white border border-gray-200/90 rounded-2xl shadow-xl divide-y divide-gray-100 no-scrollbar">
                                    <!-- Populated via JS -->
                                </div>
                            </div>

                            <!-- Selected Subcategories Below (Clean compact row of selected chips) -->
                            <div class="p-3 bg-gray-50/60 border border-gray-200/80 rounded-xl">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                        Added to Package (<span id="subcat-added-count">0</span>)
                                    </span>
                                </div>
                                <div id="subcat-selected-chips-list" class="flex flex-wrap gap-2 items-center min-h-[32px]">
                                    <!-- Selected chips appear here -->
                                    <span id="subcat-none-selected-text" class="text-xs text-gray-400 italic">
                                        No subcategories added yet. Pick from the dropdown above.
                                    </span>
                                </div>
                            </div>
                        </div>

                        <p class="text-[11px] text-gray-400">Selected subcategories are linked to this package for storefront category discovery.</p>
                    </div>

                    {{-- Dynamic Recipient / "For Whom" Selector (Multi-Select Personas) --}}
                    <div class="space-y-3 pt-4 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-gray-900">
                                Celebration For <span class="text-gray-400 font-normal">(Multi-Select Target Personas)</span>
                            </label>
                            <div class="flex items-center gap-2 text-xs">
                                <button type="button" 
                                        onclick="selectAllRecipients()" 
                                        class="text-xs font-bold text-amber-800 hover:underline cursor-pointer">
                                    + Select All
                                </button>
                                <span class="text-gray-300">•</span>
                                <button type="button" 
                                        onclick="clearAllRecipients()" 
                                        class="text-xs font-semibold text-gray-500 hover:text-gray-800 cursor-pointer">
                                    Clear
                                </button>
                            </div>
                        </div>

                        @php
                            $availableRecipients = $recipientsList ?? \App\Models\CelebrationRecipient::active()->ordered()->get();
                            $currentRecipients = old('recipients', $selectedRecipients ?? (array)($package->recipients ?? []));
                        @endphp

                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-3 pt-1">
                            @foreach($availableRecipients as $r)
                                @php
                                    $isSelected = in_array($r->slug, (array)$currentRecipients);
                                @endphp
                                <label class="recipient-card-item group relative rounded-2xl overflow-hidden border-2 cursor-pointer transition-all duration-200 select-none flex flex-col {{ $isSelected ? 'border-gray-950 shadow-md ring-2 ring-gray-950/10' : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-xs' }}">
                                    <input type="checkbox" 
                                           name="recipients[]" 
                                           value="{{ $r->slug }}" 
                                           {{ $isSelected ? 'checked' : '' }}
                                           onchange="toggleRecipientCard(this)"
                                           class="sr-only">

                                    {{-- Image Box --}}
                                    <div class="relative w-full aspect-[16/10.5] overflow-hidden bg-rose-50/50">
                                        <img src="{{ asset($r->image) }}" 
                                             alt="{{ $r->name }}" 
                                             class="recipient-card-img w-full h-full object-cover transition-all duration-200 group-hover:scale-105 {{ $isSelected ? 'opacity-100 grayscale-0' : 'opacity-65 grayscale-[25%] group-hover:opacity-100 group-hover:grayscale-0' }}">
                                        
                                        {{-- Checkmark Badge in Corner --}}
                                        <div class="recipient-check-badge absolute top-1.5 right-1.5 w-5 h-5 rounded-full flex items-center justify-center transition-all duration-150 {{ $isSelected ? 'bg-emerald-500 text-white shadow-xs scale-100' : 'bg-black/30 text-white opacity-0 scale-75' }}">
                                            <i class="fa-solid fa-check text-[9px]"></i>
                                        </div>
                                    </div>

                                    {{-- Title Bar --}}
                                    <div class="recipient-card-label py-1.5 px-2 text-center transition-colors {{ $isSelected ? 'bg-gray-950 text-white' : 'bg-gray-50 text-gray-700 group-hover:text-gray-900' }}">
                                        <span class="block text-xs font-bold truncate">{{ $r->name }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- CARD 2: Package Identity & Content --}}
                <div class="bg-white border border-gray-200/80 rounded-2xl p-5 sm:p-6 shadow-2xs space-y-5">
                    <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-pen-nib"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-gray-900 tracking-tight">Package Information</h2>
                                <p class="text-xs text-gray-500">Core package title, slug, marketing badge, and descriptions.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-wider">Step 2</span>
                    </div>

                    {{-- Package Title & Auto Slug --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Title --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="pkg-title" class="block text-xs font-bold text-gray-700">
                                    Package Title <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Required</span>
                            </div>
                            <input type="text" 
                                   id="pkg-title" 
                                   name="title" 
                                   value="{{ old('title', $package->title) }}" 
                                   required 
                                   placeholder="e.g. Neon Cyber House Party Setup"
                                   class="w-full h-11 bg-white border border-gray-300 rounded-xl px-3.5 text-xs font-semibold text-gray-900 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition-all placeholder:text-gray-400 shadow-2xs"
                                   oninput="onTitleChanged()">
                            <p class="text-[11px] text-gray-400">Clear, descriptive name displayed on event cards.</p>
                        </div>

                        {{-- Slug --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="pkg-slug" class="block text-xs font-bold text-gray-700">
                                    URL Slug
                                </label>
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">SEO URL</span>
                            </div>
                            <input type="text" 
                                   id="pkg-slug" 
                                   name="slug" 
                                   value="{{ old('slug', $package->slug) }}" 
                                   placeholder="neon-cyber-house-party-setup"
                                   class="w-full h-11 bg-white border border-gray-300 rounded-xl px-3.5 text-xs font-mono font-medium text-gray-800 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition-all placeholder:text-gray-400 shadow-2xs"
                                   oninput="onSlugChanged()">
                            <div class="flex items-center gap-1.5 text-[11px] text-gray-500">
                                <span class="text-gray-400">URL:</span>
                                <code class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-900 border border-purple-200/60 font-mono text-[11px] truncate max-w-full">
                                    <span>/events/</span><strong id="live-slug-text" class="text-purple-700 font-bold">{{ old('slug', $package->slug) ?: 'package-slug' }}</strong>
                                </code>
                            </div>
                        </div>
                    </div>

                    {{-- Badge & Tag Pills --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-1">
                        {{-- Promotional Badge --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="pkg-badge" class="block text-xs font-bold text-gray-700">
                                    Promotional Badge
                                </label>
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Optional</span>
                            </div>
                            <input type="text" 
                                   id="pkg-badge" 
                                   name="badge" 
                                   value="{{ old('badge', $package->badge) }}" 
                                   placeholder="e.g. BESTSELLER, TRENDING, PREMIUM"
                                   class="w-full h-11 bg-white border border-gray-300 rounded-xl px-3.5 text-xs font-bold text-amber-700 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition-all placeholder:text-gray-400 placeholder:font-normal shadow-2xs"
                                   oninput="updateLivePreview()">
                            {{-- Quick Preset Badges --}}
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                @foreach(['BESTSELLER', 'TRENDING', 'PREMIUM', 'POPULAR', 'LUXURY', 'HOT'] as $presetBadge)
                                    <button type="button" 
                                            onclick="setPresetBadge('{{ $presetBadge }}')"
                                            class="px-2 py-0.5 rounded-md bg-gray-100 hover:bg-amber-100 hover:text-amber-800 text-[10px] font-bold text-gray-600 transition-colors cursor-pointer border border-gray-200/50">
                                        {{ $presetBadge }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Event Tag --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="pkg-tag" class="block text-xs font-bold text-gray-700">
                                    Event Tag / Classification
                                </label>
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Internal Tag</span>
                            </div>
                            <input type="text" 
                                   id="pkg-tag" 
                                   name="tag" 
                                   value="{{ old('tag', $package->tag ?: 'decor') }}" 
                                   placeholder="decor, live, dj, setup"
                                   class="w-full h-11 bg-white border border-gray-300 rounded-xl px-3.5 text-xs font-medium text-gray-800 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition-all placeholder:text-gray-400 shadow-2xs">
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                @foreach(['decor', 'live', 'dj', 'catering', 'setup', 'lighting', 'sound'] as $presetTag)
                                    <button type="button" 
                                            onclick="setPresetTag('{{ $presetTag }}')"
                                            class="px-2 py-0.5 rounded-md bg-gray-100 hover:bg-gray-200 text-[10px] font-semibold text-gray-600 transition-colors cursor-pointer border border-gray-200/50">
                                        {{ $presetTag }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="space-y-1.5 pt-1">
                        <label for="pkg-description" class="block text-xs font-bold text-gray-700">
                            Package Description & Inclusions Overview
                        </label>
                        <textarea id="pkg-description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Describe the overall celebration theme, ambiance, setup inclusions, and guest suitability..."
                                  class="w-full bg-white border border-gray-300 rounded-xl p-3.5 text-xs text-gray-800 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition-all placeholder:text-gray-400 shadow-2xs"
                                  oninput="updateLivePreview()">{{ old('description', $package->description) }}</textarea>
                    </div>
                </div>

                {{-- CARD 3: Media & Imagery (No Default Placeholders, Top-Right Remove Icon) --}}
                <div class="bg-white border border-gray-200/80 rounded-2xl p-5 sm:p-6 shadow-2xs space-y-5">
                    <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-image"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-gray-900 tracking-tight">Media & Visual Assets</h2>
                                <p class="text-xs text-gray-500">Primary cover image and secondary gallery banners.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-wider">Step 3</span>
                    </div>

                    {{-- Primary Cover Image Dropzone & Preview --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-gray-700">
                                Primary Cover Image
                            </label>
                            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Event Card Banner</span>
                        </div>

                        {{-- Hidden inputs for file and current URL --}}
                        <input type="file" 
                               name="image_file" 
                               id="pkg-image-file" 
                               accept="image/jpeg,image/png,image/webp,image/gif" 
                               class="hidden" 
                               onchange="handlePrimaryImageSelected(this)">
                        <input type="hidden" 
                               name="image" 
                               id="pkg-image-url" 
                               value="{{ old('image', $package->image) }}">

                        {{-- Active Image Preview Container (Visible ONLY if image is present) --}}
                        <div id="primary-image-preview-container" 
                             class="relative w-full max-w-md rounded-2xl overflow-hidden border border-gray-200 shadow-sm group {{ !empty(old('image', $package->image)) ? '' : 'hidden' }}">
                            <img id="primary-image-img" 
                                 src="{{ old('image', $package->image) ?: '' }}" 
                                 alt="Primary Cover" 
                                 class="w-full h-52 object-cover bg-gray-100">
                            
                            {{-- Top-Right Close/Remove Icon (as requested by user) --}}
                            <button type="button" 
                                    onclick="clearPrimaryImage()" 
                                    class="absolute top-2.5 right-2.5 w-8 h-8 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center shadow-lg transition-all hover:scale-105 cursor-pointer z-10" 
                                    title="Remove this image">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </button>

                            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent p-3 text-white text-[11px] font-medium flex items-center justify-between">
                                <span class="truncate max-w-[200px]" id="primary-image-filename">Current Cover</span>
                                <button type="button" 
                                        onclick="document.getElementById('pkg-image-file').click()" 
                                        class="px-2 py-1 bg-white/20 hover:bg-white/30 backdrop-blur-md rounded-lg text-[10px] font-bold transition-colors cursor-pointer">
                                    Replace
                                </button>
                            </div>
                        </div>

                        {{-- Clean Upload Dropzone (Visible when NO image is selected) --}}
                        <div id="primary-image-dropzone" 
                             onclick="document.getElementById('pkg-image-file').click()"
                             ondragover="event.preventDefault(); this.classList.add('border-gray-900', 'bg-gray-50');"
                             ondragleave="this.classList.remove('border-gray-900', 'bg-gray-50');"
                             ondrop="handlePrimaryImageDrop(event)"
                             class="w-full border-2 border-dashed border-gray-300 hover:border-gray-400 rounded-2xl p-6 text-center transition-all cursor-pointer bg-white group {{ empty(old('image', $package->image)) ? '' : 'hidden' }}">
                            <div class="w-12 h-12 rounded-2xl bg-gray-100 group-hover:bg-gray-200 text-gray-500 group-hover:text-gray-700 flex items-center justify-center mx-auto text-xl transition-colors">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <h4 class="text-xs font-bold text-gray-900 mt-3">Click or Drag & Drop Cover Image</h4>
                            <p class="text-[11px] text-gray-400 mt-1">Recommended: 1200x800 px (WEBP, JPG, PNG up to 5MB)</p>
                        </div>
                    </div>

                    {{-- Secondary Gallery Banners (4 Slots) --}}
                    <div class="space-y-3 pt-3 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-gray-700">
                                Gallery Banners (Optional, up to 4 images)
                            </label>
                            <span class="text-[10px] font-semibold text-gray-400">Additional Showcase</span>
                        </div>

                        @php
                            $galleryList = old('gallery', $package->gallery ?? []);
                            if (is_string($galleryList)) {
                                $galleryList = json_decode($galleryList, true) ?? [];
                            }
                        @endphp

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @for($i = 0; $i < 4; $i++)
                                @php $galUrl = $galleryList[$i] ?? null; @endphp
                                <div class="relative bg-gray-50 border border-gray-200 rounded-xl p-2 flex flex-col justify-between items-center text-center h-36 overflow-hidden group">
                                    <input type="file" 
                                           name="gallery_files[{{ $i }}]" 
                                           id="gal-file-{{ $i }}" 
                                           accept="image/*" 
                                           class="hidden" 
                                           onchange="handleGallerySelected(this, {{ $i }})">
                                    <input type="hidden" 
                                           name="gallery[{{ $i }}]" 
                                           id="gal-url-{{ $i }}" 
                                           value="{{ $galUrl }}">

                                    {{-- Preview Image if available --}}
                                    <div id="gal-preview-box-{{ $i }}" class="w-full h-full relative {{ !empty($galUrl) ? '' : 'hidden' }}">
                                        <img id="gal-img-{{ $i }}" 
                                             src="{{ $galUrl ?: '' }}" 
                                             alt="Gallery {{ $i+1 }}" 
                                             class="w-full h-full object-cover rounded-lg">
                                        <button type="button" 
                                                onclick="clearGallerySlot({{ $i }})" 
                                                class="absolute top-1 right-1 w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center shadow-md text-xs cursor-pointer z-10"
                                                title="Remove image">
                                            <i class="fa-solid fa-xmark text-[10px]"></i>
                                        </button>
                                    </div>

                                    {{-- Empty slot prompt --}}
                                    <div id="gal-empty-box-{{ $i }}" 
                                         onclick="document.getElementById('gal-file-{{ $i }}').click()"
                                         class="w-full h-full flex flex-col items-center justify-center cursor-pointer hover:bg-gray-100 rounded-lg transition-colors p-2 {{ empty($galUrl) ? '' : 'hidden' }}">
                                        <i class="fa-solid fa-plus text-gray-400 text-base mb-1"></i>
                                        <span class="text-[10px] font-bold text-gray-600">Banner {{ $i + 1 }}</span>
                                        <span class="text-[9px] text-gray-400">Click to upload</span>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- CARD 4: Pricing & Package Tiers Manager --}}
                <div class="bg-white border border-gray-200/80 rounded-2xl p-5 sm:p-6 shadow-2xs space-y-5">
                    <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-indian-rupee-sign"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-gray-900 tracking-tight">Pricing & Package Tiers</h2>
                                <p class="text-xs text-gray-500">Configure base celebration pricing and customizable service tiers.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-wider">Step 4</span>
                    </div>

                    {{-- Base Starting Price & Strikethrough Original Price --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="pkg-price" class="block text-xs font-bold text-gray-700">
                                    Base / Starting Price (₹)
                                </label>
                                <span class="text-[10px] font-semibold text-emerald-600 uppercase tracking-wider">Starts From</span>
                            </div>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold">₹</span>
                                <input type="number" 
                                       id="pkg-price" 
                                       name="price" 
                                       value="{{ old('price', $package->price) }}" 
                                       step="1" 
                                       min="0" 
                                       placeholder="4999"
                                       class="w-full h-11 bg-white border border-gray-300 rounded-xl pl-8 pr-3.5 text-xs font-bold text-gray-900 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition-all placeholder:text-gray-400 shadow-2xs"
                                       oninput="calculateDiscount(); updateLivePreview();">
                            </div>
                            <p class="text-[11px] text-gray-400">If tiers are specified, this defaults to the lowest tier price.</p>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="pkg-original-price" class="block text-xs font-bold text-gray-700">
                                    Original Strikethrough Price (₹)
                                </label>
                                <span id="discount-calc-badge" class="text-[10px] font-bold text-rose-600 uppercase tracking-wider">
                                    --
                                </span>
                            </div>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold">₹</span>
                                <input type="number" 
                                       id="pkg-original-price" 
                                       name="original_price" 
                                       value="{{ old('original_price', $package->original_price) }}" 
                                       step="1" 
                                       min="0" 
                                       placeholder="6999"
                                       class="w-full h-11 bg-white border border-gray-300 rounded-xl pl-8 pr-3.5 text-xs font-bold text-gray-500 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition-all placeholder:text-gray-400 shadow-2xs"
                                       oninput="calculateDiscount(); updateLivePreview();">
                            </div>
                            <p class="text-[11px] text-gray-400">Higher benchmark value to display discounted deals.</p>
                        </div>
                    </div>

                    {{-- Dynamic Package Tiers Repeater --}}
                    <div class="space-y-3 pt-3 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xs font-bold text-gray-900">Customizable Package Tiers</h3>
                                <p class="text-[11px] text-gray-400">e.g. Standard, Premium, Luxury VIP</p>
                            </div>
                            <button type="button" 
                                    onclick="addNewTier()"
                                    class="px-3 py-1.5 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-bold inline-flex items-center gap-1.5 cursor-pointer shadow-2xs transition-all">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                                <span>Add Tier</span>
                            </button>
                        </div>

                        {{-- Tiers List Container --}}
                        <div id="tiers-container" class="space-y-3">
                            @php
                                $existingTiers = $package->tiers ?? collect();
                            @endphp

                            @if($existingTiers->count() > 0)
                                @foreach($existingTiers as $idx => $tier)
                                    <div class="tier-card bg-gray-50/70 border border-gray-200 rounded-xl p-4 space-y-3 transition-all" data-index="{{ $idx }}">
                                        <input type="hidden" name="tiers[{{ $idx }}][id]" value="{{ $tier->id }}">
                                        
                                        <div class="flex items-center justify-between border-b border-gray-200/60 pb-2.5">
                                            <div class="flex items-center gap-2">
                                                <span class="w-6 h-6 rounded-lg bg-gray-200/80 text-gray-700 font-bold text-[11px] flex items-center justify-center tier-number">
                                                    {{ $idx + 1 }}
                                                </span>
                                                <span class="text-xs font-bold text-gray-900 tier-title-preview">
                                                    {{ $tier->name ?: 'Package Tier ' . ($idx + 1) }}
                                                </span>
                                            </div>
                                            <button type="button" 
                                                    onclick="removeTier(this)" 
                                                    class="text-xs text-rose-500 hover:text-rose-700 font-bold cursor-pointer transition-colors p-1">
                                                <i class="fa-solid fa-trash-can mr-1"></i> Remove
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <div class="sm:col-span-2 space-y-1">
                                                <label class="block text-[11px] font-bold text-gray-700">Tier Name</label>
                                                <input type="text" 
                                                       name="tiers[{{ $idx }}][name]" 
                                                       value="{{ $tier->name }}" 
                                                       required
                                                       placeholder="e.g. Standard Setup / Premium VIP"
                                                       class="w-full h-9 bg-white border border-gray-300 rounded-lg px-3 text-xs font-semibold text-gray-900 focus:border-gray-900 focus:outline-none"
                                                       oninput="this.closest('.tier-card').querySelector('.tier-title-preview').textContent = this.value || 'Package Tier'; updateLivePreview();">
                                            </div>

                                            <div class="space-y-1">
                                                <label class="block text-[11px] font-bold text-gray-700">Tier Price (₹)</label>
                                                <input type="number" 
                                                       name="tiers[{{ $idx }}][price]" 
                                                       value="{{ (int)$tier->price }}" 
                                                       required
                                                       step="1"
                                                       placeholder="4999"
                                                       class="w-full h-9 bg-white border border-gray-300 rounded-lg px-3 text-xs font-bold text-gray-900 focus:border-gray-900 focus:outline-none"
                                                       oninput="syncTiersToPrice(); updateLivePreview();">
                                            </div>
                                        </div>

                                        <div class="space-y-1">
                                            <label class="block text-[11px] font-bold text-gray-700">
                                                Inclusions List <span class="text-gray-400 font-normal">(One per line)</span>
                                            </label>
                                            <textarea name="tiers[{{ $idx }}][inclusions]" 
                                                      rows="2" 
                                                      placeholder="e.g.&#10;Balloon Arch & Backdrop&#10;LED Spotlights&#10;Professional Sound System"
                                                      class="w-full bg-white border border-gray-300 rounded-lg p-2.5 text-xs text-gray-800 focus:border-gray-900 focus:outline-none"
                                                      oninput="updateLivePreview()">{{ is_array($tier->inclusions) ? implode("\n", $tier->inclusions) : $tier->inclusions }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                {{-- 1 Default Standard Tier for New Package --}}
                                <div class="tier-card bg-gray-50/70 border border-gray-200 rounded-xl p-4 space-y-3 transition-all" data-index="0">
                                    <div class="flex items-center justify-between border-b border-gray-200/60 pb-2.5">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-lg bg-gray-200/80 text-gray-700 font-bold text-[11px] flex items-center justify-center tier-number">
                                                1
                                            </span>
                                            <span class="text-xs font-bold text-gray-900 tier-title-preview">
                                                Standard Setup
                                            </span>
                                        </div>
                                        <button type="button" 
                                                onclick="removeTier(this)" 
                                                class="text-xs text-rose-500 hover:text-rose-700 font-bold cursor-pointer transition-colors p-1">
                                            <i class="fa-solid fa-trash-can mr-1"></i> Remove
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div class="sm:col-span-2 space-y-1">
                                            <label class="block text-[11px] font-bold text-gray-700">Tier Name</label>
                                            <input type="text" 
                                                   name="tiers[0][name]" 
                                                   value="Standard Setup" 
                                                   required
                                                   placeholder="e.g. Standard Setup / Premium VIP"
                                                   class="w-full h-9 bg-white border border-gray-300 rounded-lg px-3 text-xs font-semibold text-gray-900 focus:border-gray-900 focus:outline-none"
                                                   oninput="this.closest('.tier-card').querySelector('.tier-title-preview').textContent = this.value || 'Package Tier'; updateLivePreview();">
                                        </div>

                                        <div class="space-y-1">
                                            <label class="block text-[11px] font-bold text-gray-700">Tier Price (₹)</label>
                                            <input type="number" 
                                                   name="tiers[0][price]" 
                                                   value="{{ old('price', 4999) }}" 
                                                   required
                                                   step="1"
                                                   placeholder="4999"
                                                   class="w-full h-9 bg-white border border-gray-300 rounded-lg px-3 text-xs font-bold text-gray-900 focus:border-gray-900 focus:outline-none"
                                                   oninput="syncTiersToPrice(); updateLivePreview();">
                                        </div>
                                    </div>

                                    <div class="space-y-1">
                                        <label class="block text-[11px] font-bold text-gray-700">
                                            Inclusions List <span class="text-gray-400 font-normal">(One per line)</span>
                                        </label>
                                        <textarea name="tiers[0][inclusions]" 
                                                  rows="2" 
                                                  placeholder="e.g.&#10;Balloon Arch & Backdrop&#10;LED Spotlights&#10;Professional Sound System"
                                                  class="w-full bg-white border border-gray-300 rounded-lg p-2.5 text-xs text-gray-800 focus:border-gray-900 focus:outline-none"
                                                  oninput="updateLivePreview()">Full celebration decor setup&#10;Ambient LED mood lighting&#10;On-site event coordinator</textarea>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- CARD 5: Status & Visibility --}}
                <div class="bg-white border border-gray-200/80 rounded-2xl p-5 sm:p-6 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-eye"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-gray-900 tracking-tight">Storefront Visibility</h2>
                                <p class="text-xs text-gray-500">Publish immediately or save as an internal draft.</p>
                            </div>
                        </div>

                        {{-- iOS Style Toggle Switch --}}
                        <label class="relative inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" 
                                   name="active" 
                                   value="1" 
                                   id="pkg-active-toggle"
                                   {{ old('active', $package->active) ? 'checked' : '' }} 
                                   class="sr-only peer"
                                   onchange="updateLivePreview()">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            <span class="ml-3 text-xs font-bold text-gray-800" id="active-status-label">
                                {{ old('active', $package->active) ? 'Published & Active' : 'Draft / Hidden' }}
                            </span>
                        </label>
                    </div>
                </div>

            </div>

            {{-- =========================================================
                 RIGHT COLUMN: Sticky Summary & Real-time Live Preview (100% Dynamic)
                 ========================================================= --}}
            <div class="lg:col-span-1 space-y-6 lg:sticky lg:top-6 lg:self-start">

                {{-- Primary Action Card --}}
                <div class="bg-white border border-gray-200/80 rounded-2xl p-5 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <span class="text-xs font-bold text-gray-900 tracking-tight flex items-center gap-2">
                            <i class="fa-solid fa-bolt text-amber-500"></i>
                            Action Panel
                        </span>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-md {{ $isEdit ? 'bg-purple-50 text-purple-700 border border-purple-200/60' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' }} font-bold">
                            {{ $isEdit ? 'UPDATE MODE' : 'CREATE MODE' }}
                        </span>
                    </div>

                    <div class="space-y-2">
                        <button type="submit" 
                                id="save-package-btn"
                                class="w-full h-11 bg-gray-900 hover:bg-black active:scale-[0.99] text-white text-xs font-bold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            <span id="save-btn-text">{{ $isEdit ? 'Update Package' : 'Save Package' }}</span>
                        </button>

                        <a href="{{ route('admin.packages') }}" 
                           class="w-full h-9 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                            <span>Cancel & Return</span>
                        </a>

                        @if($isEdit && !empty($package->slug))
                            <a href="{{ route('events.show', ['slug' => $package->slug]) }}" 
                               target="_blank"
                               id="diag-live-link"
                               class="w-full h-9 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200/70 text-xs font-bold rounded-xl transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[11px] text-amber-700"></i>
                                <span>View Live Customer Page</span>
                            </a>
                        @endif
                    </div>

                    {{-- Dynamic Diagnostics & Real-Time Status --}}
                    <div class="pt-2 border-t border-gray-100 space-y-2 text-[11px] text-gray-500 font-medium">
                        <div class="flex justify-between items-center">
                            <span>Storefront Status:</span>
                            <span id="diag-status" class="font-bold text-emerald-700">
                                {{ old('active', $package->active) ? 'Published & Active' : 'Draft / Hidden' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Category:</span>
                            <strong id="diag-cat" class="text-gray-900 font-bold">
                                {{ $package->category?->title ?? 'None' }}
                            </strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Subcategories:</span>
                            <strong id="diag-subcats" class="text-gray-900 font-bold">
                                {{ $package->subcategories?->count() ?? 0 }} Linked
                            </strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Pricing:</span>
                            <strong id="diag-price" class="text-emerald-700 font-bold">
                                ₹{{ number_format(old('price', $package->price ?: 0)) }}
                            </strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Promotional Badge:</span>
                            <span id="diag-badge" class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-800">
                                {{ old('badge', $package->badge) ?: 'None' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center pt-1 border-t border-dashed border-gray-100">
                            <span>Bestseller Feed:</span>
                            <span id="diag-bestseller" class="text-[10.5px]">
                                @php
                                    $curBadge = strtoupper(trim(old('badge', $package->badge) ?? ''));
                                    $isBestsellerFeed = str_contains($curBadge, 'BESTSELLER') || str_contains($curBadge, 'LUXURY');
                                @endphp
                                @if($isBestsellerFeed)
                                    <span class="text-emerald-700 font-bold flex items-center gap-1">
                                        <i class="fa-solid fa-star text-amber-500 text-[10px]"></i> In Shop By Bestsellers
                                    </span>
                                @else
                                    <span class="text-gray-400 font-normal">Standard Listing Only</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Live Storefront Card Preview (Exact 1:1 match to homepage / events catalog) --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between px-1">
                        <span class="text-xs font-bold text-gray-700 tracking-tight uppercase tracking-wider text-[10px] flex items-center gap-1.5">
                            <i class="fa-solid fa-eye text-amber-600"></i>
                            Live Storefront Preview
                        </span>
                        <span class="text-[10px] font-semibold text-gray-400">Customer View</span>
                    </div>

                    {{-- Exact 1:1 Artizen Customer Event Card Simulation --}}
                    <div class="block w-full bg-white rounded-2xl border border-[#E8DFC8] overflow-hidden shadow-none text-left flex flex-col justify-between select-none">
                        
                        <!-- Image Box (Aspect 4/3.8 Crisp & Clean, Exact Storefront Ratio) -->
                        <div class="relative w-full aspect-[4/3.8] overflow-hidden bg-[#FAF7F2] shrink-0" id="preview-img-wrapper">
                            <img id="preview-card-img" 
                                 src="{{ old('image', $package->image) ?: '' }}" 
                                 alt="Card Preview" 
                                 class="w-full h-full object-cover select-none pointer-events-none {{ !empty(old('image', $package->image)) ? '' : 'hidden' }}">
                            
                            {{-- Clean Placeholder Graphic (Shown ONLY when image is missing) --}}
                            <div id="preview-card-placeholder" 
                                 class="w-full h-full flex flex-col items-center justify-center text-gray-300 bg-gradient-to-br from-gray-50 to-gray-100 {{ empty(old('image', $package->image)) ? '' : 'hidden' }}">
                                <i class="fa-solid fa-wand-magic-sparkles text-3xl mb-2 text-gray-300"></i>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Artizen Package</span>
                            </div>

                            <!-- Rating Badge Top-Right (Customer Standard) -->
                            <span class="absolute top-2.5 right-2.5 bg-white/95 backdrop-blur-xs px-2 py-0.5 rounded text-[11px] font-bold text-gray-900 flex items-center gap-1 shadow-2xs z-20">
                                <i class="fa-solid fa-star text-amber-400 text-[10px]"></i>
                                <span>4.9</span>
                            </span>

                            <!-- Promotional Badge Top-Left (Dynamic: BESTSELLER, LUXURY, etc.) -->
                            <span id="preview-badge-pill" 
                                  class="absolute top-2.5 left-2.5 bg-gray-950/90 backdrop-blur-xs px-2.5 py-0.5 rounded-full text-[10px] font-extrabold text-white tracking-wider uppercase z-20 shadow-xs {{ !empty(old('badge', $package->badge)) ? '' : 'hidden' }}">
                                {{ old('badge', $package->badge) ?: 'BESTSELLER' }}
                            </span>

                            <!-- Draft Status Overlay indicator if inactive -->
                            <span id="preview-status-pill" 
                                  class="absolute bottom-2.5 right-2.5 px-2 py-0.5 rounded text-[10px] font-bold backdrop-blur-md bg-gray-900/80 text-white shadow-xs z-20 {{ old('active', $package->active) ? 'hidden' : '' }}">
                                Draft / Hidden
                            </span>
                        </div>

                        <!-- Card Body (Identical Typography, Spacing, and Colors to Frontend) -->
                        <div class="p-3 sm:p-3.5 flex flex-col justify-between flex-1">
                            <div class="mb-2.5">
                                <!-- Gold Uppercase Subcategory / Tag -->
                                <span id="preview-subcat-heading" class="text-[9.5px] sm:text-[10px] font-heading font-extrabold uppercase tracking-wider text-[#B89700] block mb-0.5 truncate">
                                    {{ strtoupper($package->subcategories->first()?->name ?? ($package->tag ?: 'Celebration Setup')) }}
                                </span>
                                <!-- Package Title -->
                                <h4 id="preview-title" class="font-heading font-bold text-[13.5px] sm:text-[14.5px] text-gray-900 leading-snug line-clamp-1">
                                    {{ old('title', $package->title) ?: 'Celebration Package Title' }}
                                </h4>
                            </div>

                            <!-- Pricing Line (Exact Match to Customer Card) -->
                            <div class="pt-2.5 border-t border-[#F2ECE0] flex items-baseline justify-between mt-auto">
                                <div class="flex items-baseline gap-1.5 flex-wrap">
                                    <span id="preview-price" class="font-heading font-extrabold text-[15px] sm:text-base text-gray-950 tracking-tight">
                                        ₹{{ number_format(old('price', $package->price ?: 4999)) }}
                                    </span>
                                    @php
                                        $initialPrice = (float)old('price', $package->price ?: 4999);
                                        $initialOrig = (float)old('original_price', $package->original_price ?: round($initialPrice * 1.15));
                                        $initialPct = ($initialOrig > $initialPrice && $initialOrig > 0) ? round((($initialOrig - $initialPrice) / $initialOrig) * 100) : 15;
                                    @endphp
                                    <span id="preview-orig-price" class="text-[11px] text-gray-400 line-through font-normal">
                                        ₹{{ number_format($initialOrig) }}
                                    </span>
                                    <span id="preview-discount-percent" class="text-[10.5px] font-bold text-emerald-600">
                                        {{ $initialPct }}% OFF
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Admin Context Notice --}}
                <div class="p-4 rounded-2xl bg-amber-500/5 border border-amber-500/20 text-xs text-amber-900 space-y-1">
                    <div class="flex items-center gap-2 font-bold text-amber-950">
                        <i class="fa-solid fa-shield-halved text-amber-600"></i>
                        <span>Offline Event Booking Model</span>
                    </div>
                    <p class="text-[11px] text-amber-800/90 leading-relaxed">
                        Customers submit event booking requests without online payments. Your admin team reviews requirements, confirms pricing, and collects payment offline.
                    </p>
                </div>

            </div>

        </div>
    </form>

</div>

{{-- Category and Subcategories Data Dictionary for Dynamic Javascript --}}
<script>
    const categoriesData = @json($categories->keyBy('id'));
    const preselectedSubcatIds = @json($selectedSubcatIds ?? []);
    let currentSelectedSubcatIds = new Set(preselectedSubcatIds);
    let nextTierIndex = {{ max($package->tiers->count(), 1) }};

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Category and Subcategories
        const catSelect = document.getElementById('pkg-category-id');
        if (catSelect && catSelect.value) {
            renderSubcategories(catSelect.value);
        }

        // Initialize Price Discount
        calculateDiscount();
        updateLivePreview();
    });

    // Toggle Recipient Card UI
    function toggleRecipientCard(checkbox) {
        const card = checkbox.closest('.recipient-card-item');
        if (!card) return;
        const img = card.querySelector('.recipient-card-img');
        const badge = card.querySelector('.recipient-check-badge');
        const label = card.querySelector('.recipient-card-label');

        if (checkbox.checked) {
            card.className = 'recipient-card-item group relative rounded-2xl overflow-hidden border-2 cursor-pointer transition-all duration-200 select-none flex flex-col border-gray-950 shadow-md ring-2 ring-gray-950/10';
            if (img) {
                img.classList.remove('opacity-65', 'grayscale-[25%]');
                img.classList.add('opacity-100', 'grayscale-0');
            }
            if (badge) {
                badge.className = 'recipient-check-badge absolute top-1.5 right-1.5 w-5 h-5 rounded-full flex items-center justify-center transition-all duration-150 bg-emerald-500 text-white shadow-xs scale-100 opacity-100';
            }
            if (label) {
                label.className = 'recipient-card-label py-1.5 px-2 text-center transition-colors bg-gray-950 text-white';
            }
        } else {
            card.className = 'recipient-card-item group relative rounded-2xl overflow-hidden border-2 cursor-pointer transition-all duration-200 select-none flex flex-col border-gray-200 bg-white hover:border-gray-300 hover:shadow-xs';
            if (img) {
                img.classList.add('opacity-65', 'grayscale-[25%]');
                img.classList.remove('opacity-100', 'grayscale-0');
            }
            if (badge) {
                badge.className = 'recipient-check-badge absolute top-1.5 right-1.5 w-5 h-5 rounded-full flex items-center justify-center transition-all duration-150 bg-black/30 text-white opacity-0 scale-75';
            }
            if (label) {
                label.className = 'recipient-card-label py-1.5 px-2 text-center transition-colors bg-gray-50 text-gray-700 group-hover:text-gray-900';
            }
        }
    }

    function toggleRecipientChip(checkbox) {
        toggleRecipientCard(checkbox);
    }

    function selectAllRecipients() {
        document.querySelectorAll('.recipient-card-item input[type="checkbox"]').forEach(cb => {
            cb.checked = true;
            toggleRecipientCard(cb);
        });
    }

    function clearAllRecipients() {
        document.querySelectorAll('.recipient-card-item input[type="checkbox"]').forEach(cb => {
            cb.checked = false;
            toggleRecipientCard(cb);
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('subcat-dropdown-wrapper');
        const menu = document.getElementById('subcat-dropdown-menu');
        if (menu && wrapper && !wrapper.contains(e.target)) {
            closeSubcatDropdown();
        }
    });

    // -------------------------------------------------------------
    // Category & Subcategory Dynamic Binding (Dropdown + Added Chips)
    // -------------------------------------------------------------
    function onCategoryChanged() {
        const catSelect = document.getElementById('pkg-category-id');
        const catId = catSelect.value;
        
        currentSelectedSubcatIds.clear();
        renderSubcategories(catId);
        updateLivePreview();
    }

    function renderSubcategories(catId) {
        const emptyNotice = document.getElementById('subcat-empty-notice');
        const dropdownWrapper = document.getElementById('subcat-dropdown-wrapper');
        const bulkActions = document.getElementById('subcat-bulk-actions');
        const countBadge = document.getElementById('subcat-count-badge');
        const searchInput = document.getElementById('subcat-search-picker');

        if (searchInput) searchInput.value = '';

        if (!catId || !categoriesData[catId]) {
            emptyNotice.style.display = 'block';
            dropdownWrapper.style.display = 'none';
            bulkActions.style.display = 'none';
            countBadge.textContent = '0 Selected';
            return;
        }

        const category = categoriesData[catId];
        const subcategories = category.active_subcategories || category.activeSubcategories || [];

        if (subcategories.length === 0) {
            emptyNotice.innerHTML = '<i class="fa-solid fa-circle-info mr-1 text-gray-300"></i> No subcategories configured for this category yet. You can create subcategories in the Categories module.';
            emptyNotice.style.display = 'block';
            dropdownWrapper.style.display = 'none';
            bulkActions.style.display = 'none';
            countBadge.textContent = '0 Selected';
            return;
        }

        emptyNotice.style.display = 'none';
        dropdownWrapper.style.display = 'block';
        bulkActions.style.display = 'flex';

        renderSubcatDropdownList(catId);
        renderSelectedChips(catId);
        updateSubcatBadgeAndHiddenInputs();
    }

    // Alias for backward compatibility
    function renderSubcategoryChips(catId) {
        renderSubcategories(catId);
    }

    function renderSubcatDropdownList(catId, filterQuery = '') {
        const menu = document.getElementById('subcat-dropdown-menu');
        if (!menu || !categoriesData[catId]) return;

        const subcategories = categoriesData[catId].active_subcategories || categoriesData[catId].activeSubcategories || [];
        menu.innerHTML = '';

        const normalizedQuery = (filterQuery || '').toLowerCase().trim();
        const filtered = subcategories.filter(s => {
            const name = (s.name || s.title || '').toLowerCase();
            return !normalizedQuery || name.includes(normalizedQuery);
        });

        if (filtered.length === 0) {
            menu.innerHTML = `<div class="p-3 text-center text-xs text-gray-400">No matching subcategories found</div>`;
            return;
        }

        filtered.forEach(subcat => {
            const isSelected = currentSelectedSubcatIds.has(subcat.id);
            const subcatName = subcat.name || subcat.title || ('Subcategory #' + subcat.id);
            const subcatBadge = subcat.badge || '';

            const badgeHtml = subcatBadge 
                ? `<span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wider ${isSelected ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-500'} leading-none">${escapeHtml(subcatBadge)}</span>`
                : '';

            const item = document.createElement('button');
            item.type = 'button';
            item.className = `w-full px-3.5 py-2.5 text-left text-xs flex items-center justify-between transition-colors cursor-pointer ${isSelected ? 'bg-amber-50/70 hover:bg-amber-100/70 text-amber-900 font-semibold' : 'hover:bg-gray-50 text-gray-700 font-medium'}`;
            item.onclick = (e) => {
                e.stopPropagation();
                toggleSubcategoryFromDropdown(subcat.id);
            };

            item.innerHTML = `
                <div class="flex items-center gap-2.5">
                    <span class="w-4 h-4 rounded flex items-center justify-center ${isSelected ? 'bg-amber-500 text-white' : 'border border-gray-300 text-transparent'} text-[10px]">
                        <i class="fa-solid fa-check"></i>
                    </span>
                    <span>${escapeHtml(subcatName)}</span>
                </div>
                <div class="flex items-center gap-2">
                    ${badgeHtml}
                    <span class="text-[10.5px] ${isSelected ? 'text-amber-700 font-bold' : 'text-gray-400'}">${isSelected ? 'Added' : '+ Add'}</span>
                </div>
            `;
            menu.appendChild(item);
        });
    }

    function renderSelectedChips(catId) {
        const chipsList = document.getElementById('subcat-selected-chips-list');
        const addedCount = document.getElementById('subcat-added-count');

        if (addedCount) addedCount.textContent = currentSelectedSubcatIds.size;
        if (!chipsList) return;

        chipsList.innerHTML = '';

        if (currentSelectedSubcatIds.size === 0) {
            chipsList.innerHTML = `<span id="subcat-none-selected-text" class="text-xs text-gray-400 italic">No subcategories added yet. Pick from the dropdown above.</span>`;
            return;
        }

        const subcategories = (categoriesData[catId] && (categoriesData[catId].active_subcategories || categoriesData[catId].activeSubcategories)) || [];

        currentSelectedSubcatIds.forEach(id => {
            const subcat = subcategories.find(s => s.id == id);
            const subcatName = subcat ? (subcat.name || subcat.title || 'Subcategory #' + id) : 'Subcategory #' + id;
            const subcatBadge = subcat ? (subcat.badge || '') : '';

            const badgeHtml = subcatBadge 
                ? `<span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wider bg-amber-600/80 text-white leading-none">${escapeHtml(subcatBadge)}</span>`
                : '';

            const chip = document.createElement('div');
            chip.className = 'inline-flex items-center gap-1.5 pl-3 pr-1.5 py-1 rounded-xl text-xs font-semibold bg-amber-500 text-white shadow-2xs group transition-all';
            chip.innerHTML = `
                <i class="fa-solid fa-check text-[10px] text-amber-200"></i>
                <span>${escapeHtml(subcatName)}</span>
                ${badgeHtml}
                <button type="button" 
                        onclick="removeSubcategory(${id})" 
                        class="w-4 h-4 rounded-full bg-white/20 hover:bg-white/40 flex items-center justify-center text-[10px] text-white transition-colors cursor-pointer ml-1"
                        title="Remove tag">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            chipsList.appendChild(chip);
        });
    }

    function toggleSubcategoryFromDropdown(id) {
        const catSelect = document.getElementById('pkg-category-id');
        const catId = catSelect ? catSelect.value : null;

        if (currentSelectedSubcatIds.has(id)) {
            currentSelectedSubcatIds.delete(id);
        } else {
            currentSelectedSubcatIds.add(id);
        }

        if (catId) {
            const searchInput = document.getElementById('subcat-search-picker');
            renderSubcatDropdownList(catId, searchInput ? searchInput.value : '');
            renderSelectedChips(catId);
        }

        updateSubcatBadgeAndHiddenInputs();
        updateLivePreview();
    }

    function removeSubcategory(id) {
        const catSelect = document.getElementById('pkg-category-id');
        const catId = catSelect ? catSelect.value : null;

        currentSelectedSubcatIds.delete(id);

        if (catId) {
            const searchInput = document.getElementById('subcat-search-picker');
            renderSubcatDropdownList(catId, searchInput ? searchInput.value : '');
            renderSelectedChips(catId);
        }

        updateSubcatBadgeAndHiddenInputs();
        updateLivePreview();
    }

    function openSubcatDropdown() {
        const catSelect = document.getElementById('pkg-category-id');
        const catId = catSelect ? catSelect.value : null;
        if (!catId) return;

        const menu = document.getElementById('subcat-dropdown-menu');
        const arrow = document.getElementById('subcat-dropdown-arrow');
        if (menu) menu.classList.remove('hidden');
        if (arrow) arrow.style.transform = 'rotate(180deg)';

        const searchInput = document.getElementById('subcat-search-picker');
        renderSubcatDropdownList(catId, searchInput ? searchInput.value : '');
    }

    function closeSubcatDropdown() {
        const menu = document.getElementById('subcat-dropdown-menu');
        const arrow = document.getElementById('subcat-dropdown-arrow');
        if (menu) menu.classList.add('hidden');
        if (arrow) arrow.style.transform = '';
    }

    function toggleSubcatDropdown() {
        const menu = document.getElementById('subcat-dropdown-menu');
        if (menu && menu.classList.contains('hidden')) {
            openSubcatDropdown();
        } else {
            closeSubcatDropdown();
        }
    }

    function filterSubcatDropdown(query) {
        const catSelect = document.getElementById('pkg-category-id');
        const catId = catSelect ? catSelect.value : null;
        if (!catId) return;

        openSubcatDropdown();
        renderSubcatDropdownList(catId, query);
    }

    function selectAllSubcategories() {
        const catSelect = document.getElementById('pkg-category-id');
        const catId = catSelect ? catSelect.value : null;
        if (!catId || !categoriesData[catId]) return;

        const subcategories = categoriesData[catId].active_subcategories || categoriesData[catId].activeSubcategories || [];
        subcategories.forEach(s => currentSelectedSubcatIds.add(s.id));

        renderSubcategories(catId);
        updateLivePreview();
    }

    function clearAllSubcategories() {
        const catSelect = document.getElementById('pkg-category-id');
        const catId = catSelect ? catSelect.value : null;

        currentSelectedSubcatIds.clear();
        if (catId) {
            renderSubcategories(catId);
        }
        updateLivePreview();
    }

    function updateSubcatBadgeAndHiddenInputs() {
        const count = currentSelectedSubcatIds.size;
        const countBadge = document.getElementById('subcat-count-badge');
        if (countBadge) {
            countBadge.textContent = `${count} ${count === 1 ? 'Selected' : 'Selected'}`;
            countBadge.className = count > 0 
                ? 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white shadow-2xs'
                : 'px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-500 border border-gray-200/60';
        }

        // Remove old hidden inputs
        document.querySelectorAll('input.dynamic-subcat-input').forEach(el => el.remove());

        // Append new hidden inputs for form submission
        const form = document.getElementById('package-editor-form');
        currentSelectedSubcatIds.forEach(id => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'subcategories[]';
            hiddenInput.value = id;
            hiddenInput.className = 'dynamic-subcat-input';
            form.appendChild(hiddenInput);
        });

        // Update diagnostics
        const diagSubcats = document.getElementById('diag-subcats');
        if (diagSubcats) {
            diagSubcats.textContent = `${count} Linked`;
        }
    }

    // -------------------------------------------------------------
    // Slug Generation & Title Synchronization
    // -------------------------------------------------------------
    let slugTouchedManually = {{ $isEdit ? 'true' : 'false' }};

    function onTitleChanged() {
        const title = document.getElementById('pkg-title').value.trim();
        const slugInput = document.getElementById('pkg-slug');

        if (!slugTouchedManually) {
            const slug = generateSlug(title);
            slugInput.value = slug;
            document.getElementById('live-slug-text').textContent = slug || 'package-slug';
        }
        updateLivePreview();
    }

    function onSlugChanged() {
        slugTouchedManually = true;
        const slug = document.getElementById('pkg-slug').value.trim();
        document.getElementById('live-slug-text').textContent = slug || 'package-slug';
    }

    function generateSlug(text) {
        return text.toString().toLowerCase().trim()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    }

    function setPresetBadge(badge) {
        document.getElementById('pkg-badge').value = badge;
        updateLivePreview();
    }

    function setPresetTag(tag) {
        document.getElementById('pkg-tag').value = tag;
        updateLivePreview();
    }

    // -------------------------------------------------------------
    // Image Handling: Zero Default Placeholders + Top-Right Close Button
    // -------------------------------------------------------------
    function handlePrimaryImageSelected(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function (e) {
                const previewImg = document.getElementById('primary-image-img');
                const previewContainer = document.getElementById('primary-image-preview-container');
                const dropzone = document.getElementById('primary-image-dropzone');
                const filenameSpan = document.getElementById('primary-image-filename');

                previewImg.src = e.target.result;
                previewContainer.classList.remove('hidden');
                dropzone.classList.add('hidden');
                filenameSpan.textContent = file.name;

                // Sync live storefront preview card
                const cardImg = document.getElementById('preview-card-img');
                const cardPlaceholder = document.getElementById('preview-card-placeholder');
                cardImg.src = e.target.result;
                cardImg.classList.remove('hidden');
                cardPlaceholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    function handlePrimaryImageDrop(event) {
        event.preventDefault();
        const dropzone = document.getElementById('primary-image-dropzone');
        dropzone.classList.remove('border-gray-900', 'bg-gray-50');

        if (event.dataTransfer.files && event.dataTransfer.files[0]) {
            const fileInput = document.getElementById('pkg-image-file');
            fileInput.files = event.dataTransfer.files;
            handlePrimaryImageSelected(fileInput);
        }
    }

    function clearPrimaryImage() {
        const fileInput = document.getElementById('pkg-image-file');
        const urlInput = document.getElementById('pkg-image-url');
        const previewContainer = document.getElementById('primary-image-preview-container');
        const dropzone = document.getElementById('primary-image-dropzone');
        const previewImg = document.getElementById('primary-image-img');

        fileInput.value = '';
        urlInput.value = '';
        previewImg.src = '';
        previewContainer.classList.add('hidden');
        dropzone.classList.remove('hidden');

        // Sync live card preview to clean placeholder
        const cardImg = document.getElementById('preview-card-img');
        const cardPlaceholder = document.getElementById('preview-card-placeholder');
        cardImg.src = '';
        cardImg.classList.add('hidden');
        cardPlaceholder.classList.remove('hidden');
    }

    function handlePrimaryImageUrl(url) {
        url = (url || '').trim();
        const cardImg = document.getElementById('preview-card-img');
        const cardPlaceholder = document.getElementById('preview-card-placeholder');
        if (cardImg && cardPlaceholder) {
            if (url) {
                cardImg.src = url;
                cardImg.classList.remove('hidden');
                cardPlaceholder.classList.add('hidden');
            } else {
                cardImg.src = '';
                cardImg.classList.add('hidden');
                cardPlaceholder.classList.remove('hidden');
            }
        }
    }

    // Gallery slots handling
    function handleGallerySelected(input, index) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById(`gal-img-${index}`).src = e.target.result;
                document.getElementById(`gal-preview-box-${index}`).classList.remove('hidden');
                document.getElementById(`gal-empty-box-${index}`).classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    function clearGallerySlot(index) {
        document.getElementById(`gal-file-${index}`).value = '';
        document.getElementById(`gal-url-${index}`).value = '';
        document.getElementById(`gal-img-${index}`).src = '';
        document.getElementById(`gal-preview-box-${index}`).classList.add('hidden');
        document.getElementById(`gal-empty-box-${index}`).classList.remove('hidden');
    }

    // -------------------------------------------------------------
    // Pricing & Discount Calculations
    // -------------------------------------------------------------
    function calculateDiscount() {
        const price = parseFloat(document.getElementById('pkg-price').value) || 0;
        const origPrice = parseFloat(document.getElementById('pkg-original-price').value) || 0;
        const discountBadge = document.getElementById('discount-calc-badge');

        if (origPrice > price && price > 0) {
            const pct = Math.round(((origPrice - price) / origPrice) * 100);
            discountBadge.textContent = `Save ${pct}% OFF`;
            discountBadge.className = 'text-[10px] font-bold text-emerald-600 uppercase tracking-wider';
        } else {
            discountBadge.textContent = '--';
            discountBadge.className = 'text-[10px] font-bold text-gray-400 uppercase tracking-wider';
        }
    }

    function syncTiersToPrice() {
        // Automatically set base price to lowest tier price if base price is empty or zero
        const tierPriceInputs = document.querySelectorAll('input[name^="tiers["][name$="[price]"]');
        let minPrice = Infinity;
        tierPriceInputs.forEach(input => {
            const val = parseFloat(input.value);
            if (val > 0 && val < minPrice) minPrice = val;
        });

        const basePriceInput = document.getElementById('pkg-price');
        if (minPrice < Infinity && (!basePriceInput.value || parseFloat(basePriceInput.value) <= 0)) {
            basePriceInput.value = minPrice;
            calculateDiscount();
        }
    }

    // -------------------------------------------------------------
    // Tiers Repeater
    // -------------------------------------------------------------
    function addNewTier() {
        const container = document.getElementById('tiers-container');
        const idx = nextTierIndex++;
        const tierNum = container.querySelectorAll('.tier-card').length + 1;

        const card = document.createElement('div');
        card.className = 'tier-card bg-gray-50/70 border border-gray-200 rounded-xl p-4 space-y-3 transition-all';
        card.setAttribute('data-index', idx);
        card.innerHTML = `
            <div class="flex items-center justify-between border-b border-gray-200/60 pb-2.5">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-gray-200/80 text-gray-700 font-bold text-[11px] flex items-center justify-center tier-number">
                        ${tierNum}
                    </span>
                    <span class="text-xs font-bold text-gray-900 tier-title-preview">
                        Package Tier ${tierNum}
                    </span>
                </div>
                <button type="button" 
                        onclick="removeTier(this)" 
                        class="text-xs text-rose-500 hover:text-rose-700 font-bold cursor-pointer transition-colors p-1">
                    <i class="fa-solid fa-trash-can mr-1"></i> Remove
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2 space-y-1">
                    <label class="block text-[11px] font-bold text-gray-700">Tier Name</label>
                    <input type="text" 
                           name="tiers[${idx}][name]" 
                           value="Premium Tier ${tierNum}" 
                           required
                           placeholder="e.g. Deluxe VIP Setup"
                           class="w-full h-9 bg-white border border-gray-300 rounded-lg px-3 text-xs font-semibold text-gray-900 focus:border-gray-900 focus:outline-none"
                           oninput="this.closest('.tier-card').querySelector('.tier-title-preview').textContent = this.value || 'Package Tier'; updateLivePreview();">
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-gray-700">Tier Price (₹)</label>
                    <input type="number" 
                           name="tiers[${idx}][price]" 
                           value="9999" 
                           required
                           step="1"
                           placeholder="9999"
                           class="w-full h-9 bg-white border border-gray-300 rounded-lg px-3 text-xs font-bold text-gray-900 focus:border-gray-900 focus:outline-none"
                           oninput="syncTiersToPrice(); updateLivePreview();">
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-[11px] font-bold text-gray-700">
                    Inclusions List <span class="text-gray-400 font-normal">(One per line)</span>
                </label>
                <textarea name="tiers[${idx}][inclusions]" 
                          rows="2" 
                          placeholder="e.g.&#10;Upgraded Stage Decor&#10;Fog Machine Effect"
                          class="w-full bg-white border border-gray-300 rounded-lg p-2.5 text-xs text-gray-800 focus:border-gray-900 focus:outline-none"
                          oninput="updateLivePreview()">Upgraded celebration decor&#10;Special lighting effects&#10;Extended coordination support</textarea>
            </div>
        `;

        container.appendChild(card);
        updateTierNumbers();
        updateLivePreview();
    }

    function removeTier(btn) {
        const container = document.getElementById('tiers-container');
        if (container.querySelectorAll('.tier-card').length <= 1) {
            alert('A package must have at least one tier.');
            return;
        }

        const card = btn.closest('.tier-card');
        card.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        setTimeout(() => {
            card.remove();
            updateTierNumbers();
            syncTiersToPrice();
            updateLivePreview();
        }, 150);
    }

    function updateTierNumbers() {
        document.querySelectorAll('#tiers-container .tier-card').forEach((card, index) => {
            const numEl = card.querySelector('.tier-number');
            if (numEl) numEl.textContent = index + 1;
        });
    }

    // -------------------------------------------------------------
    // Live Storefront Card Real-Time Preview (100% Dynamic Sync)
    // -------------------------------------------------------------
    function updateLivePreview() {
        // 1. Package Title
        const titleInput = document.getElementById('pkg-title');
        const titleVal = titleInput ? titleInput.value.trim() : '';
        const previewTitle = document.getElementById('preview-title');
        if (previewTitle) {
            previewTitle.textContent = titleVal || 'Celebration Package Title';
        }

        // 2. Promotional Badge & Live Bestseller Diagnostics
        const badgeInput = document.getElementById('pkg-badge');
        const badgeVal = badgeInput ? badgeInput.value.trim() : '';
        const badgePill = document.getElementById('preview-badge-pill');
        const diagBadge = document.getElementById('diag-badge');
        const diagBestseller = document.getElementById('diag-bestseller');

        if (badgePill) {
            if (badgeVal) {
                badgePill.textContent = badgeVal.toUpperCase();
                badgePill.classList.remove('hidden');
            } else {
                badgePill.classList.add('hidden');
            }
        }

        if (diagBadge) {
            diagBadge.textContent = badgeVal || 'None';
            diagBadge.className = badgeVal 
                ? 'px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300/60'
                : 'px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600';
        }

        if (diagBestseller) {
            const upperBadge = badgeVal.toUpperCase();
            if (upperBadge.includes('BESTSELLER') || upperBadge.includes('LUXURY')) {
                diagBestseller.innerHTML = `<span class="text-emerald-700 font-bold flex items-center gap-1"><i class="fa-solid fa-star text-amber-500 text-[10px]"></i> In Shop By Bestsellers</span>`;
            } else {
                diagBestseller.innerHTML = `<span class="text-gray-400 font-normal">Standard Listing Only</span>`;
            }
        }

        // 3. Category Tag & Diagnostic
        const catSelect = document.getElementById('pkg-category-id');
        const diagCat = document.getElementById('diag-cat');
        let selectedCatName = 'None';
        if (catSelect && catSelect.value && categoriesData[catSelect.value]) {
            selectedCatName = categoriesData[catSelect.value].title;
        }
        if (diagCat) diagCat.textContent = selectedCatName;

        // 4. Subcategory Tag (Gold Heading on Storefront Card) & Diagnostic
        const subcatHeading = document.getElementById('preview-subcat-heading');
        const diagSubcats = document.getElementById('diag-subcats');
        const count = currentSelectedSubcatIds.size;

        if (diagSubcats) {
            diagSubcats.textContent = `${count} Linked`;
        }

        if (subcatHeading) {
            if (count > 0) {
                const firstId = Array.from(currentSelectedSubcatIds)[0];
                const catId = catSelect ? catSelect.value : null;
                let subcatName = '';
                if (catId && categoriesData[catId]) {
                    const subcategories = categoriesData[catId].active_subcategories || categoriesData[catId].activeSubcategories || [];
                    const found = subcategories.find(s => s.id == firstId);
                    if (found) subcatName = found.name || found.title || '';
                }
                subcatHeading.textContent = (subcatName || 'Celebration Setup').toUpperCase();
            } else {
                const tagInput = document.getElementById('pkg-tag');
                const tagVal = tagInput && tagInput.value.trim() ? tagInput.value.trim() : (selectedCatName !== 'None' ? selectedCatName : 'Celebration Setup');
                subcatHeading.textContent = tagVal.toUpperCase();
            }
        }

        // 5. Storefront Status & Diagnostic
        const activeToggle = document.getElementById('pkg-active-toggle');
        const statusPill = document.getElementById('preview-status-pill');
        const diagStatus = document.getElementById('diag-status');
        const statusLabel = document.getElementById('active-status-label');
        const isActive = activeToggle ? activeToggle.checked : true;

        if (statusPill) {
            if (isActive) {
                statusPill.classList.add('hidden');
            } else {
                statusPill.classList.remove('hidden');
                statusPill.textContent = 'Draft / Hidden';
            }
        }

        if (diagStatus) {
            diagStatus.textContent = isActive ? 'Published & Active' : 'Draft / Hidden';
            diagStatus.className = isActive ? 'font-bold text-emerald-700' : 'font-bold text-gray-500';
        }

        if (statusLabel) {
            statusLabel.textContent = isActive ? 'Published & Active' : 'Draft / Hidden';
        }

        // 6. Pricing, Original Strikethrough & Discount Percent
        const priceInput = document.getElementById('pkg-price');
        const origPriceInput = document.getElementById('pkg-original-price');
        const priceVal = parseFloat(priceInput ? priceInput.value : 0) || 0;
        let origVal = parseFloat(origPriceInput ? origPriceInput.value : 0) || 0;

        if (origVal <= 0 && priceVal > 0) {
            origVal = Math.round(priceVal * 1.15);
        }

        const previewPrice = document.getElementById('preview-price');
        const previewOrigPrice = document.getElementById('preview-orig-price');
        const previewDiscount = document.getElementById('preview-discount-percent');
        const diagPrice = document.getElementById('diag-price');

        if (previewPrice) {
            previewPrice.textContent = `₹${priceVal.toLocaleString('en-IN')}`;
        }

        if (previewOrigPrice) {
            previewOrigPrice.textContent = `₹${origVal.toLocaleString('en-IN')}`;
            if (origVal > priceVal) {
                previewOrigPrice.classList.remove('hidden');
            } else {
                previewOrigPrice.classList.add('hidden');
            }
        }

        if (previewDiscount) {
            if (origVal > priceVal && origVal > 0) {
                const discountPct = Math.round(((origVal - priceVal) / origVal) * 100);
                previewDiscount.textContent = `${discountPct}% OFF`;
                previewDiscount.classList.remove('hidden');
            } else {
                previewDiscount.classList.add('hidden');
            }
        }

        if (diagPrice) {
            diagPrice.textContent = `₹${priceVal.toLocaleString('en-IN')}`;
        }
    }

    // -------------------------------------------------------------
    // Form Validation & Submit Handlers
    // -------------------------------------------------------------
    function validateAndSubmitForm() {
        const catSelect = document.getElementById('pkg-category-id');
        if (!catSelect.value) {
            alert('Please select a Category for this package.');
            catSelect.focus();
            return false;
        }

        const titleInput = document.getElementById('pkg-title');
        if (!titleInput.value.trim()) {
            alert('Please provide a package title.');
            titleInput.focus();
            return false;
        }

        // Sync hidden subcategories inputs
        updateSubcatBadgeAndHiddenInputs();

        // Change button state to saving
        const saveBtn = document.getElementById('save-package-btn');
        const saveText = document.getElementById('save-btn-text');
        saveBtn.disabled = true;
        saveBtn.classList.add('opacity-75', 'cursor-not-allowed');
        saveText.textContent = 'Saving Package...';

        return true;
    }

    function confirmDeletePackage(btn, title) {
        if (confirm(`Are you sure you want to permanently delete "${title}"? This action cannot be undone.`)) {
            btn.closest('form').submit();
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
@endsection

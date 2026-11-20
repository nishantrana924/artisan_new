@extends('layouts.admin')

@section('page_title', 'Event Categories')
@section('page_heading', 'Event Categories')
@section('page_subheading', 'Manage categories for the homepage slider & navbar mega-menu')

@section('content')
@php
if (!function_exists('getCategoryFallbackIcon')) {
    function getCategoryFallbackIcon($title, $icon = null) {
        if ($icon && !str_contains($icon, 'fa-sparkles')) {
            return $icon;
        }
        $t = strtolower($title ?? '');
        if (str_contains($t, 'birthday') || str_contains($t, 'cake')) return 'fa-solid fa-cake-candles text-amber-500';
        if (str_contains($t, 'kid') || str_contains($t, 'child') || str_contains($t, 'balloon') || str_contains($t, 'cozy')) return 'fa-solid fa-child-reaching text-orange-500';
        if (str_contains($t, 'dj') || str_contains($t, 'party') || str_contains($t, 'music') || str_contains($t, 'sound') || str_contains($t, 'acoustic')) return 'fa-solid fa-music text-purple-600';
        if (str_contains($t, 'proposal') || str_contains($t, 'anniversary') || str_contains($t, 'romantic') || str_contains($t, 'love')) return 'fa-solid fa-heart text-rose-500';
        if (str_contains($t, 'wedding') || str_contains($t, 'sangeet') || str_contains($t, 'haldi') || str_contains($t, 'reception')) return 'fa-solid fa-ring text-amber-600';
        if (str_contains($t, 'baby') || str_contains($t, 'shower')) return 'fa-solid fa-baby text-pink-500';
        if (str_contains($t, 'corporate') || str_contains($t, 'office') || str_contains($t, 'business')) return 'fa-solid fa-building text-blue-600';
        return 'fa-solid fa-wand-magic-sparkles text-amber-500';
    }
}
@endphp
<style>
    .category-card {
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease, transform 0.2s ease;
    }
    .category-card.is-selected {
        border-color: #111827 !important;
        box-shadow: 0 4px 16px -2px rgba(17, 24, 39, 0.08), 0 0 0 2px rgba(17, 24, 39, 0.12) !important;
        background-color: #fafbfc !important;
    }
    .cat-card-checkbox {
        width: 17px !important;
        height: 17px !important;
        accent-color: #111827 !important;
        border-radius: 5px !important;
        cursor: pointer !important;
        transition: all 0.15s ease-in-out;
    }
</style>
<div class="space-y-6">

    {{-- ===================================================================
         1. PAGE HEADER TOOLBAR
         =================================================================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200/80 pb-5">
        <div>
            <h2 class="text-xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2.5">
                <span>Event Categories</span>
                <span id="header-category-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200/60">
                    {{ $categoriesWithSubs->count() }} {{ $categoriesWithSubs->count() === 1 ? 'Category' : 'Categories' }}
                </span>
            </h2>
            <p class="text-xs text-gray-500 mt-1">Manage homepage occasion slider cards, header navbar mega-menu panels & subcategory hierarchies.</p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('admin.categories.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 hover:bg-black active:scale-[0.98] text-white text-xs font-bold rounded-xl shadow-xs hover:shadow transition-all cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Add Category</span>
            </a>
        </div>
    </div>

    {{-- ===================================================================
         2. SUMMARY METRICS CARDS (Dynamic & Synced with DB)
         =================================================================== --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        {{-- Total Categories --}}
        <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total</span>
                <span class="w-6 h-6 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 text-xs">
                    <i class="fa-solid fa-layer-group"></i>
                </span>
            </div>
            <span id="metric-total-categories" class="text-2xl font-extrabold text-gray-900 block mt-2">{{ $categoriesWithSubs->count() }}</span>
            <span class="text-[11px] text-gray-400 mt-0.5 block">Configured categories</span>
        </div>

        {{-- Active Categories --}}
        <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Active</span>
                <span class="w-6 h-6 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 text-xs">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
            </div>
            <span id="metric-active-categories" class="text-2xl font-extrabold text-emerald-600 block mt-2">{{ $categoriesWithSubs->where('active', true)->count() }}</span>
            <span class="text-[11px] text-gray-400 mt-0.5 block">Visible to customers</span>
        </div>

        {{-- Inactive Categories --}}
        <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Inactive</span>
                <span class="w-6 h-6 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">
                    <i class="fa-solid fa-eye-slash"></i>
                </span>
            </div>
            <span id="metric-inactive-categories" class="text-2xl font-extrabold text-gray-400 block mt-2">{{ $categoriesWithSubs->where('active', false)->count() }}</span>
            <span class="text-[11px] text-gray-400 mt-0.5 block">Hidden from storefront</span>
        </div>

        {{-- Total Subcategories --}}
        <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Subcategories</span>
                <span class="w-6 h-6 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-xs">
                    <i class="fa-solid fa-list-ul"></i>
                </span>
            </div>
            <span id="metric-subcategories-count" class="text-2xl font-extrabold text-indigo-600 block mt-2">{{ $categoriesWithSubs->sum(fn($c) => $c->subcategories->count()) }}</span>
            <span class="text-[11px] text-gray-400 mt-0.5 block">Total grouped items</span>
        </div>
    </div>

    {{-- ===================================================================
         3. SEARCH, FILTER & SORT TOOLBAR
         =================================================================== --}}
    <div class="bg-white border border-gray-200/80 p-3 sm:p-3.5 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-3 shadow-2xs">
        {{-- Search Input --}}
        <div class="relative flex-1 min-w-[240px]">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" 
                   id="category-search-input" 
                   oninput="handleCategorySearch(this.value)" 
                   placeholder="Search categories by title, slug, badge, or subcategory name..." 
                   class="w-full h-10 bg-gray-50/70 hover:bg-gray-50 focus:bg-white border border-gray-200/80 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 pl-10 pr-4 text-xs font-medium text-gray-900 placeholder:text-gray-400 rounded-xl outline-none transition-all">
        </div>

        {{-- Filter Controls --}}
        <div class="flex items-center gap-2.5 flex-wrap shrink-0">
            {{-- Status Filter --}}
            <div class="relative min-w-[130px]">
                <select id="category-status-filter" 
                        onchange="handleCategoryStatusFilter(this.value)"
                        class="w-full h-10 bg-gray-50/70 hover:bg-gray-50 border border-gray-200/80 focus:border-gray-900 rounded-xl pl-3.5 pr-8 text-xs font-semibold text-gray-700 outline-none transition-all cursor-pointer appearance-none">
                    <option value="all">All Status</option>
                    <option value="active">Active Only</option>
                    <option value="inactive">Inactive Only</option>
                </select>
                <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 pointer-events-none"></i>
            </div>

            {{-- Sort Options --}}
            <div class="relative min-w-[150px]">
                <select id="category-sort-select" 
                        onchange="handleCategorySort(this.value)"
                        class="w-full h-10 bg-gray-50/70 hover:bg-gray-50 border border-gray-200/80 focus:border-gray-900 rounded-xl pl-3.5 pr-8 text-xs font-semibold text-gray-700 outline-none transition-all cursor-pointer appearance-none">
                    <option value="order">Order (Default)</option>
                    <option value="title_asc">Title (A - Z)</option>
                    <option value="title_desc">Title (Z - A)</option>
                    <option value="subcats">Subcategories Count</option>
                </select>
                <i class="fa-solid fa-arrow-down-short-wide absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 pointer-events-none"></i>
            </div>

            {{-- Select All Button (Minimalist, Matches Filter Controls) --}}
            <button type="button" 
                    id="btn-toggle-select-all" 
                    onclick="toggleSelectAllCategoriesBtn()" 
                    class="h-10 px-3.5 rounded-xl bg-gray-50/70 hover:bg-gray-100 border border-gray-200/80 text-xs font-semibold text-gray-700 transition-all inline-flex items-center gap-2 cursor-pointer shadow-2xs select-none active:scale-95" 
                    title="Select all visible category cards">
                <i id="select-all-icon" class="fa-regular fa-square text-xs text-gray-400"></i>
                <span id="select-all-text">Select All</span>
            </button>

            {{-- Clear Filters Button --}}
            <button type="button" 
                    id="clear-cat-filters-btn"
                    onclick="resetCategoryFilters()"
                    class="hidden h-10 px-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition-colors items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-xmark text-xs"></i>
                <span>Clear</span>
            </button>

            {{-- Results Counter Pill --}}
            <div class="inline-flex items-center gap-1.5 px-3 h-10 rounded-xl bg-gray-50/80 border border-gray-200/60 text-xs text-gray-500 font-medium shrink-0">
                <span>Showing</span>
                <span id="visible-category-count" class="font-bold text-gray-900">{{ $categoriesWithSubs->count() }}</span>
                <span>of <span id="total-category-count">{{ $categoriesWithSubs->count() }}</span> Categories</span>
            </div>
        </div>
    </div>


    {{-- ===================================================================
         4. CATEGORIES GRID
         =================================================================== --}}
    <div id="categories-grid-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($categoriesWithSubs as $cat)
        @php
            $subcatNames = $cat->subcategories->pluck('name')->implode(' ');
            $searchTerms = strtolower(trim($cat->title . ' ' . $cat->slug . ' ' . $cat->nav_slug . ' ' . ($cat->dropdown_badge ?? '') . ' ' . $subcatNames));
            $pkgCount = $cat->packages ? $cat->packages->count() : 0;
            $subcatCount = $cat->subcategories ? $cat->subcategories->count() : 0;
        @endphp
        <div class="category-card bg-white border border-gray-200/90 rounded-2xl overflow-hidden shadow-2xs flex flex-col justify-between transition-all hover:shadow-xs group"
             data-category-id="{{ $cat->id }}"
             data-status="{{ $cat->active ? 'active' : 'inactive' }}"
             data-title="{{ strtolower($cat->title) }}"
             data-order="{{ (int)$cat->display_order }}"
             data-subcat-count="{{ (int)$subcatCount }}"
             data-search="{{ $searchTerms }}">

            {{-- Top Preview Canvas (Side-by-side Framed Components) --}}
            <div class="p-3.5 bg-gradient-to-b from-gray-50/90 via-gray-50/50 to-white border-b border-gray-100">
                <div class="flex gap-2.5 items-stretch h-[126px]">

                    {{-- Left: Occasion Slider Capsule Preview --}}
                    <div class="flex-1 rounded-2xl relative overflow-hidden shadow-2xs border border-black/5 flex flex-col justify-between p-3 select-none"
                         style="background-color: {{ $cat->bg_color ?? '#F6CFB2' }};">
                        
                        @if($cat->slider_image)
                            <img src="{{ $cat->slider_image }}" 
                                 alt="{{ $cat->title }}" 
                                 class="absolute inset-0 w-full h-full object-cover object-right pointer-events-none"
                                 onerror="this.style.display='none'">
                        @endif

                        {{-- Top Capsule Label & Packages Pill --}}
                        <div class="relative z-10 flex items-center justify-between gap-1">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-white/90 text-gray-800 shadow-2xs backdrop-blur-xs">
                                <i class="fa-solid fa-sliders text-[8px] text-amber-500"></i> Slider
                            </span>
                            @if($pkgCount > 0)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[9px] font-extrabold bg-black/60 text-white shadow-2xs backdrop-blur-xs">
                                    {{ $pkgCount }} Setups
                                </span>
                            @endif
                        </div>

                        {{-- Bottom Category Name & Arrow --}}
                        <div class="relative z-10">
                            <span class="text-xs sm:text-[13px] font-extrabold text-gray-900 drop-shadow-xs flex items-center gap-1 leading-tight">
                                <span>{{ $cat->title }}</span>
                                <i class="fa-solid fa-chevron-right text-[9px] opacity-75"></i>
                            </span>
                        </div>
                    </div>

                    {{-- Right: Header Dropdown Panel Preview --}}
                    <div class="w-[98px] rounded-2xl relative overflow-hidden shadow-2xs border border-black/5 bg-gray-100 shrink-0 flex flex-col justify-between p-2 select-none">
                        @if($cat->dropdown_image)
                            <img src="{{ $cat->dropdown_image }}" 
                                 alt="Dropdown" 
                                 class="absolute inset-0 w-full h-full object-cover pointer-events-none"
                                 onerror="this.style.display='none'">
                        @else
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-300">
                                <i class="fa-regular fa-image text-lg"></i>
                                <span class="text-[9px] font-semibold mt-1">No Image</span>
                            </div>
                        @endif

                        {{-- Top Dropdown Pill --}}
                        <div class="relative z-10 self-end">
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider bg-black/60 text-white shadow-2xs backdrop-blur-xs">
                                Nav
                            </span>
                        </div>

                        {{-- Bottom Badge (if configured) --}}
                        <div class="relative z-10">
                            @if($cat->dropdown_badge)
                                <span class="block text-center text-[8px] font-extrabold bg-amber-400 text-gray-950 rounded px-1.5 py-0.5 truncate shadow-2xs">
                                    {{ $cat->dropdown_badge }}
                                </span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            {{-- Card Details Body --}}
            <div class="p-4 sm:p-5 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    {{-- Clean Card Selection Checkbox --}}
                    <label class="inline-flex items-center justify-center cursor-pointer select-none shrink-0" title="Select category for bulk delete">
                        <input type="checkbox" 
                               class="cat-card-checkbox w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-0 focus:ring-offset-0 cursor-pointer" 
                               style="accent-color: #111827;"
                               value="{{ $cat->id }}"
                               data-title="{{ $cat->title }}"
                               data-subcats="{{ $subcatCount }}"
                               data-packages="{{ $pkgCount }}"
                               onchange="handleCategoryCheckboxChange({{ $cat->id }}, this.checked)">
                    </label>

                    <span class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-200/80 flex items-center justify-center text-sm shrink-0 shadow-2xs">
                        <i class="{{ getCategoryFallbackIcon($cat->title, $cat->icon) }}"></i>
                    </span>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-extrabold text-gray-900 leading-tight truncate">{{ $cat->title }}</h3>
                            
                            {{-- Interactive Status Toggle Button --}}
                            <button type="button" 
                                    onclick="toggleCategoryStatus({{ $cat->id }}, this)"
                                    class="status-toggle-pill shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold transition-all cursor-pointer {{ $cat->active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/70 hover:bg-emerald-100' : 'bg-gray-100 text-gray-500 border border-gray-200 hover:bg-gray-200' }}"
                                    title="Click to toggle active status">
                                <span class="w-1.5 h-1.5 rounded-full {{ $cat->active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                <span class="status-label">{{ $cat->active ? 'Active' : 'Inactive' }}</span>
                            </button>
                        </div>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="font-mono text-[10px] text-gray-400 truncate">{{ $cat->nav_slug ?: 'cat-' . \Illuminate\Support\Str::slug($cat->title) }}</span>
                            <span class="text-gray-300">•</span>
                            <span class="inline-flex items-center gap-1 font-mono text-[10px] text-gray-500">
                                <span class="w-2.5 h-2.5 rounded-full border border-black/10 inline-block shrink-0" style="background-color: {{ $cat->bg_color ?? '#F6CFB2' }};"></span>
                                {{ $cat->bg_color ?? '#F6CFB2' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="text-right shrink-0">
                    <span class="text-[11px] font-semibold text-gray-400 block">
                        Order <strong class="text-gray-700 font-mono">#{{ $cat->display_order }}</strong>
                    </span>
                    <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 border border-indigo-100 px-1.5 py-0.5 rounded-md inline-block mt-0.5">
                        {{ $subcatCount }} Subcats
                    </span>
                </div>
            </div>

            {{-- Subcategories Preview Strip --}}
            @if($cat->subcategories->count())
                <div class="px-4 pb-3 flex flex-wrap gap-1.5 items-center">
                    @foreach($cat->subcategories->take(3) as $s)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200/60 truncate max-w-[130px]">
                            {{ $s->name }}
                        </span>
                    @endforeach
                    @if($cat->subcategories->count() > 3)
                        <span class="text-[10px] font-bold text-gray-400">
                            +{{ $cat->subcategories->count() - 3 }} more
                        </span>
                    @endif
                </div>
            @endif

            {{-- Card Footer / Action Buttons --}}
            <div class="px-4 py-3 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between gap-2 mt-auto">
                <a href="{{ route('admin.categories.edit', $cat->id) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl shadow-2xs hover:shadow-xs transition-all cursor-pointer">
                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                    <span>Edit Category</span>
                </a>

                <button type="button"
                        onclick="confirmDeleteCategory({{ $cat->id }}, '{{ addslashes($cat->title) }}', {{ $subcatCount }}, {{ $pkgCount }})"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-rose-200/80 bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 text-xs font-bold transition-colors cursor-pointer"
                        title="Delete category">
                    <i class="fa-regular fa-trash-can text-xs"></i>
                    <span>Delete</span>
                </button>
            </div>

        </div>
        @empty
        <div class="col-span-full py-12 text-center bg-white border border-dashed border-gray-200 rounded-2xl">
            <i class="fa-solid fa-layer-group text-3xl text-gray-300 mb-2"></i>
            <p class="text-sm font-bold text-gray-700">No categories found</p>
            <p class="text-xs text-gray-400 mt-1 mb-4">Get started by creating your first event occasion category.</p>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl">
                <i class="fa-solid fa-plus text-xs"></i> Add Category
            </a>
        </div>
        @endforelse
    </div>

    {{-- Filter Empty State (Hidden by default) --}}
    <div id="cat-filter-empty-state" class="hidden py-14 text-center px-4 bg-white border border-gray-200/80 rounded-2xl shadow-2xs">
        <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center mx-auto mb-2.5 text-gray-400 text-base">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
        <h4 class="text-xs font-bold text-gray-800">No categories match your filters</h4>
        <p class="text-[11px] text-gray-400 mt-0.5">Try searching with a different keyword or resetting your status filter.</p>
        <button type="button" 
                onclick="resetCategoryFilters()" 
                class="mt-3.5 px-3.5 py-1.5 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-bold inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
            <i class="fa-solid fa-rotate-left text-[10px]"></i> Reset All Filters
        </button>
    </div>

</div>

{{-- ===================================================================
     5. DELETE CONFIRMATION MODAL
     =================================================================== --}}
<div id="delete-category-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-xl border border-gray-200 space-y-4 animate-scale-in">
        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-base border border-rose-100">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-gray-900">Delete Event Category?</h3>
            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                Are you sure you want to delete <span id="delete-category-name" class="font-bold text-gray-800"></span>?
            </p>
            <div id="delete-impact-warning" class="mt-2.5 p-2.5 rounded-xl bg-amber-50 border border-amber-200/70 text-[11px] text-amber-900 leading-snug space-y-1">
                <!-- Populated dynamically with impact warning -->
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 pt-2">
            <button type="button" 
                    onclick="closeDeleteCategoryModal()" 
                    class="px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer">
                Cancel
            </button>
            <form id="delete-category-form" method="POST" action="">
                @csrf
                <button type="submit" 
                        class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors cursor-pointer shadow-xs">
                    Yes, Delete
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ===================================================================
     6. SMART MULTIPLE CATEGORIES BULK DELETE MODAL
     =================================================================== --}}
<div id="bulk-delete-category-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 space-y-4 animate-scale-in">
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg border border-rose-100">
            <i class="fa-solid fa-trash-can"></i>
        </div>
        <div>
            <h3 class="text-base font-extrabold text-gray-900">Delete <span id="modal-bulk-count">0</span> Selected Categories?</h3>
            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                You are about to permanently delete the following categories:
            </p>
            <div id="modal-bulk-category-list" class="mt-2.5 p-3 rounded-xl bg-gray-50 border border-gray-200/80 text-xs text-gray-700 font-medium max-h-36 overflow-y-auto flex flex-wrap gap-1.5">
                <!-- Populated dynamically with selected category names -->
            </div>
            <div class="mt-2.5 p-3 rounded-xl bg-amber-50 border border-amber-200/80 text-[11px] text-amber-900 leading-snug space-y-1">
                <div class="font-bold flex items-center gap-1">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600"></i> Important Safety Notice:
                </div>
                <div>All subcategories linked to these categories will be removed. Existing packages will safely become unassigned without data loss.</div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2.5 pt-2">
            <button type="button" 
                    onclick="closeBulkDeleteModal()" 
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer">
                Cancel
            </button>
            <button type="button" 
                    id="btn-confirm-bulk-delete"
                    onclick="executeBulkDeleteCategories()" 
                    class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all cursor-pointer shadow-xs active:scale-95 flex items-center gap-1.5">
                <i class="fa-solid fa-trash-can"></i>
                <span>Yes, Delete Selected</span>
            </button>
        </div>
    </div>
</div>

{{-- 7. Floating Sticky Bulk Action Bar at Bottom of Screen (Sleek Modern Luxury Pill) --}}
<div id="cat-floating-bulk-bar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 hidden items-center gap-3.5 bg-gray-900/95 backdrop-blur-md text-white px-5 py-2.5 rounded-2xl shadow-2xl border border-white/10 animate-fadeIn flex-wrap max-w-[95vw]">
    <div class="flex items-center gap-2 text-xs font-semibold shrink-0">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        <span class="text-white"><strong id="cat-floating-selected-count" class="font-extrabold text-sm">0</strong> <span id="cat-floating-label">Category Selected</span></span>
    </div>
    <div class="h-4 w-px bg-white/15"></div>
    <button type="button" 
            onclick="bulkDeleteSelectedCategories()" 
            class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 active:scale-95 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer border border-rose-500/80">
        <i class="fa-regular fa-trash-can text-xs"></i>
        <span>Delete Selected (<span id="cat-btn-selected-count">0</span>)</span>
    </button>
    <button type="button" 
            onclick="clearSelectedCategories()" 
            class="px-3 py-1.5 rounded-xl text-xs font-medium text-gray-300 hover:text-white hover:bg-white/10 transition-colors cursor-pointer">
        Deselect All
    </button>
</div>

{{-- Feedback Toast Notification --}}
<div id="toast-notify" class="hidden fixed bottom-5 right-5 z-50 px-4 py-2.5 rounded-xl bg-gray-900 text-white text-xs font-semibold shadow-lg border border-white/10 flex items-center gap-2 transition-all duration-200">
    <i class="fa-solid fa-circle-check text-emerald-400"></i>
    <span id="toast-message">Action completed successfully</span>
</div>

@push('page_scripts')
<script>
    let currentCategorySearch = '';
    let currentCategoryStatus = 'all';
    let currentCategorySort = 'order';

    // Multiple Category Selection Engine
    let selectedCategoryIds = new Set();
    let selectedCategoryMeta = new Map();

    function handleCategoryCheckboxChange(id, isChecked) {
        const checkbox = document.querySelector(`.cat-card-checkbox[value="${id}"]`);
        const card = checkbox ? checkbox.closest('.category-card') : null;
        
        if (isChecked) {
            selectedCategoryIds.add(id);
            if (checkbox) {
                selectedCategoryMeta.set(id, {
                    title: checkbox.getAttribute('data-title') || 'Category #' + id,
                    subcats: parseInt(checkbox.getAttribute('data-subcats') || 0),
                    packages: parseInt(checkbox.getAttribute('data-packages') || 0)
                });
            }
            if (card) {
                card.classList.add('is-selected');
            }
        } else {
            selectedCategoryIds.delete(id);
            selectedCategoryMeta.delete(id);
            if (card) {
                card.classList.remove('is-selected');
            }
        }

        updateCategoryBulkBar();
        updateSelectAllCheckboxState();
    }

    function toggleSelectAllCategoriesBtn() {
        const visibleCheckboxes = Array.from(document.querySelectorAll('.category-card'))
            .filter(card => card.style.display !== 'none')
            .map(card => card.querySelector('.cat-card-checkbox'))
            .filter(Boolean);

        const allChecked = visibleCheckboxes.length > 0 && visibleCheckboxes.every(cb => selectedCategoryIds.has(parseInt(cb.value)));
        toggleSelectAllCategories(!allChecked);
    }

    function toggleSelectAllCategories(isChecked) {
        const visibleCheckboxes = Array.from(document.querySelectorAll('.category-card'))
            .filter(card => card.style.display !== 'none')
            .map(card => card.querySelector('.cat-card-checkbox'))
            .filter(Boolean);

        visibleCheckboxes.forEach(cb => {
            const id = parseInt(cb.value);
            cb.checked = isChecked;
            const card = cb.closest('.category-card');
            if (isChecked) {
                selectedCategoryIds.add(id);
                selectedCategoryMeta.set(id, {
                    title: cb.getAttribute('data-title') || 'Category #' + id,
                    subcats: parseInt(cb.getAttribute('data-subcats') || 0),
                    packages: parseInt(cb.getAttribute('data-packages') || 0)
                });
                if (card) {
                    card.classList.add('is-selected');
                }
            } else {
                selectedCategoryIds.delete(id);
                selectedCategoryMeta.delete(id);
                if (card) {
                    card.classList.remove('is-selected');
                }
            }
        });

        updateCategoryBulkBar();
        updateSelectAllCheckboxState();
    }

    function updateSelectAllCheckboxState() {
        const selectAllBtn = document.getElementById('btn-toggle-select-all');
        const selectAllIcon = document.getElementById('select-all-icon');
        const selectAllText = document.getElementById('select-all-text');

        const visibleCheckboxes = Array.from(document.querySelectorAll('.category-card'))
            .filter(card => card.style.display !== 'none')
            .map(card => card.querySelector('.cat-card-checkbox'))
            .filter(Boolean);

        const count = selectedCategoryIds.size;
        const allChecked = visibleCheckboxes.length > 0 && visibleCheckboxes.every(cb => selectedCategoryIds.has(parseInt(cb.value)));

        if (selectAllBtn && selectAllIcon && selectAllText) {
            if (allChecked) {
                selectAllIcon.className = 'fa-solid fa-square-check text-xs text-gray-900';
                selectAllText.textContent = 'Deselect All';
                selectAllBtn.classList.add('bg-gray-100', 'border-gray-900');
            } else if (count > 0) {
                selectAllIcon.className = 'fa-solid fa-minus-square text-xs text-gray-900';
                selectAllText.textContent = `${count} Selected`;
                selectAllBtn.classList.add('bg-gray-100');
                selectAllBtn.classList.remove('border-gray-900');
            } else {
                selectAllIcon.className = 'fa-regular fa-square text-xs text-gray-400';
                selectAllText.textContent = 'Select All';
                selectAllBtn.classList.remove('bg-gray-100', 'border-gray-900');
            }
        }
    }

    function clearSelectedCategories() {
        selectedCategoryIds.clear();
        selectedCategoryMeta.clear();
        document.querySelectorAll('.cat-card-checkbox').forEach(cb => {
            cb.checked = false;
            const card = cb.closest('.category-card');
            if (card) {
                card.classList.remove('is-selected');
            }
        });
        updateCategoryBulkBar();
        updateSelectAllCheckboxState();
    }

    function updateCategoryBulkBar() {
        const floatingBar = document.getElementById('cat-floating-bulk-bar');
        const count = selectedCategoryIds.size;

        const floatCountEl = document.getElementById('cat-floating-selected-count');
        const floatLabelEl = document.getElementById('cat-floating-label');
        const btnCountEl = document.getElementById('cat-btn-selected-count');

        if (floatCountEl) floatCountEl.textContent = count;
        if (floatLabelEl) floatLabelEl.textContent = count === 1 ? 'Category Selected' : 'Categories Selected';
        if (btnCountEl) btnCountEl.textContent = count;

        if (count > 0) {
            if (floatingBar) { floatingBar.classList.remove('hidden'); floatingBar.classList.add('flex'); }
        } else {
            if (floatingBar) { floatingBar.classList.add('hidden'); floatingBar.classList.remove('flex'); }
        }
    }

    function bulkDeleteSelectedCategories() {
        if (selectedCategoryIds.size === 0) return;

        const modal = document.getElementById('bulk-delete-category-modal');
        const countEl = document.getElementById('modal-bulk-count');
        const listEl = document.getElementById('modal-bulk-category-list');

        const count = selectedCategoryIds.size;
        if (countEl) countEl.textContent = count;

        if (listEl) {
            listEl.innerHTML = '';
            selectedCategoryMeta.forEach((meta, id) => {
                const tag = document.createElement('span');
                tag.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-gray-200 text-xs font-bold text-gray-800 shadow-2xs';
                tag.innerHTML = `<i class="fa-solid fa-layer-group text-[10px] text-amber-500"></i> ${meta.title}`;
                listEl.appendChild(tag);
            });
        }

        if (modal) modal.classList.remove('hidden');
    }

    function closeBulkDeleteModal() {
        const modal = document.getElementById('bulk-delete-category-modal');
        if (modal) modal.classList.add('hidden');
    }

    function executeBulkDeleteCategories() {
        if (selectedCategoryIds.size === 0) return;
        const ids = Array.from(selectedCategoryIds);
        const btn = document.getElementById('btn-confirm-bulk-delete');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-spinner animate-spin"></i> <span>Deleting...</span>`;
        }

        fetch("{{ route('admin.categories.bulk-delete') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(res => res.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = `<i class="fa-solid fa-trash-can"></i> <span>Yes, Delete Selected</span>`;
            }
            closeBulkDeleteModal();

            if (data.success) {
                // Animate and remove cards from DOM
                ids.forEach(id => {
                    const card = document.querySelector(`.category-card[data-category-id="${id}"]`);
                    if (card) {
                        card.style.transition = 'all 0.3s ease-out';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        setTimeout(() => card.remove(), 300);
                    }
                });

                // Clear selection
                clearSelectedCategories();

                // Update summary KPI counters
                setTimeout(() => {
                    const allCards = document.querySelectorAll('.category-card');
                    const totalRemaining = allCards.length;
                    
                    const totalMetric = document.getElementById('metric-total-categories');
                    const activeMetric = document.getElementById('metric-active-categories');
                    const inactiveMetric = document.getElementById('metric-inactive-categories');
                    const subcatMetric = document.getElementById('metric-subcategories-count');
                    const headerBadge = document.getElementById('header-category-badge');
                    const totalCountEl = document.getElementById('total-category-count');

                    if (totalMetric) totalMetric.textContent = data.remaining_count ?? totalRemaining;
                    if (activeMetric) activeMetric.textContent = data.active_count ?? 0;
                    if (inactiveMetric) inactiveMetric.textContent = data.inactive_count ?? 0;
                    if (subcatMetric) subcatMetric.textContent = data.subcategories_count ?? 0;
                    if (totalCountEl) totalCountEl.textContent = data.remaining_count ?? totalRemaining;
                    if (headerBadge) headerBadge.textContent = `${data.remaining_count ?? totalRemaining} Categories`;

                    applyCategoryFilters();
                }, 350);

                showToast(data.message || `${ids.length} categories deleted successfully!`);
            } else {
                alert(data.message || 'Error deleting selected categories.');
            }
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = `<i class="fa-solid fa-trash-can"></i> <span>Yes, Delete Selected</span>`;
            }
            console.error('Failed to bulk delete categories', err);
            alert('Network error while deleting categories.');
        });
    }

    // Live search
    function handleCategorySearch(query) {
        currentCategorySearch = (query || '').toLowerCase().trim();
        applyCategoryFilters();
    }

    // Status filter
    function handleCategoryStatusFilter(status) {
        currentCategoryStatus = (status || 'all').toLowerCase();
        applyCategoryFilters();
    }

    // Sort order
    function handleCategorySort(sortType) {
        currentCategorySort = sortType || 'order';
        sortCategoryCards();
    }

    // Combined filtering
    function applyCategoryFilters() {
        const cards = document.querySelectorAll('.category-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const cardStatus = card.getAttribute('data-status') || '';
            const cardSearch = card.getAttribute('data-search') || '';

            let matchStatus = (currentCategoryStatus === 'all' || cardStatus === currentCategoryStatus);
            let matchSearch = (!currentCategorySearch || cardSearch.includes(currentCategorySearch));

            if (matchStatus && matchSearch) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Update counter
        const countEl = document.getElementById('visible-category-count');
        if (countEl) countEl.textContent = visibleCount;

        // Toggle empty state
        const emptyState = document.getElementById('cat-filter-empty-state');
        if (emptyState) {
            if (visibleCount === 0 && cards.length > 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }

        // Toggle clear button
        const clearBtn = document.getElementById('clear-cat-filters-btn');
        if (clearBtn) {
            const hasFilters = (currentCategorySearch !== '' || currentCategoryStatus !== 'all' || currentCategorySort !== 'order');
            if (hasFilters) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }
    }

    // Sort cards
    function sortCategoryCards() {
        const container = document.getElementById('categories-grid-container');
        if (!container) return;

        const cards = Array.from(container.querySelectorAll('.category-card'));

        cards.sort((a, b) => {
            if (currentCategorySort === 'title_asc') {
                return (a.getAttribute('data-title') || '').localeCompare(b.getAttribute('data-title') || '');
            } else if (currentCategorySort === 'title_desc') {
                return (b.getAttribute('data-title') || '').localeCompare(a.getAttribute('data-title') || '');
            } else if (currentCategorySort === 'subcats') {
                return parseInt(b.getAttribute('data-subcat-count') || 0) - parseInt(a.getAttribute('data-subcat-count') || 0);
            } else {
                // Default display_order
                return parseInt(a.getAttribute('data-order') || 0) - parseInt(b.getAttribute('data-order') || 0);
            }
        });

        cards.forEach(card => container.appendChild(card));
        applyCategoryFilters();
    }

    // Reset filters
    function resetCategoryFilters() {
        currentCategorySearch = '';
        currentCategoryStatus = 'all';
        currentCategorySort = 'order';

        const searchInput = document.getElementById('category-search-input');
        if (searchInput) searchInput.value = '';

        const statusSelect = document.getElementById('category-status-filter');
        if (statusSelect) statusSelect.value = 'all';

        const sortSelect = document.getElementById('category-sort-select');
        if (sortSelect) sortSelect.value = 'order';

        sortCategoryCards();
        applyCategoryFilters();
    }

    // Toggle status via AJAX
    function toggleCategoryStatus(catId, btn) {
        btn.disabled = true;
        btn.style.opacity = '0.6';

        fetch(`/admin/categories/toggle-status/${catId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.style.opacity = '1';

            if (data.success) {
                const isActive = data.active;
                const dot = btn.querySelector('span:first-child');
                const text = btn.querySelector('.status-label');
                const card = btn.closest('.category-card');

                if (isActive) {
                    btn.className = 'status-toggle-pill shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold transition-all cursor-pointer bg-emerald-50 text-emerald-700 border border-emerald-200/70 hover:bg-emerald-100';
                    if (dot) dot.className = 'w-1.5 h-1.5 rounded-full bg-emerald-500';
                    if (text) text.textContent = 'Active';
                    if (card) card.setAttribute('data-status', 'active');
                } else {
                    btn.className = 'status-toggle-pill shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold transition-all cursor-pointer bg-gray-100 text-gray-500 border border-gray-200 hover:bg-gray-200';
                    if (dot) dot.className = 'w-1.5 h-1.5 rounded-full bg-gray-400';
                    if (text) text.textContent = 'Inactive';
                    if (card) card.setAttribute('data-status', 'inactive');
                }

                // Dynamically update Metric Counts
                const allCards = document.querySelectorAll('.category-card');
                let activeTotal = 0;
                let inactiveTotal = 0;
                allCards.forEach(c => {
                    if (c.getAttribute('data-status') === 'active') activeTotal++;
                    else inactiveTotal++;
                });

                const activeMetric = document.getElementById('metric-active-categories');
                const inactiveMetric = document.getElementById('metric-inactive-categories');
                if (activeMetric) activeMetric.textContent = activeTotal;
                if (inactiveMetric) inactiveMetric.textContent = inactiveTotal;

                applyCategoryFilters();
                showToast(data.message || 'Status updated successfully');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.style.opacity = '1';
            console.error('Failed to toggle category status', err);
            showToast('Failed to update status. Please try again.');
        });
    }

    // Delete modal
    function confirmDeleteCategory(id, name, subcatCount, pkgCount) {
        const modal = document.getElementById('delete-category-modal');
        const nameEl = document.getElementById('delete-category-name');
        const warningEl = document.getElementById('delete-impact-warning');
        const form = document.getElementById('delete-category-form');

        if (nameEl) nameEl.textContent = `"${name}"`;
        if (form) form.action = `/admin/categories/delete/${id}`;

        if (warningEl) {
            let msg = `<i class="fa-solid fa-circle-info mr-1 text-amber-600"></i> Notice: Associated subcategories (${subcatCount}) will be removed.`;
            if (pkgCount > 0) {
                msg += `<br><span class="font-bold text-amber-950">${pkgCount} existing packages</span> linked to this category will safely become unassigned without data loss.`;
            }
            warningEl.innerHTML = msg;
        }

        if (modal) modal.classList.remove('hidden');
    }

    function closeDeleteCategoryModal() {
        const modal = document.getElementById('delete-category-modal');
        if (modal) modal.classList.add('hidden');
    }

    // Feedback toast
    function showToast(msg) {
        const toast = document.getElementById('toast-notify');
        const msgEl = document.getElementById('toast-message');
        if (!toast || !msgEl) return;

        msgEl.textContent = msg;
        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }
</script>
@endpush
@endsection

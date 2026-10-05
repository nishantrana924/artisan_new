@extends('layouts.admin')

@section('page_title', 'Event Categories')
@section('page_heading', 'Event Categories')
@section('page_subheading', 'Manage categories for the homepage slider & navbar mega-menu')

@section('content')

{{-- ===================================================================
     FLASH MESSAGES
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

{{-- ===================================================================
     PAGE HEADER
     =================================================================== --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200/80 pb-5 mb-6">
    <div>
        <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">Event Categories</h2>
        <p class="text-xs text-gray-500 mt-1">Manage homepage occasion slider cards, header navbar mega-menu panels & subcategory hierarchies.</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl shadow-xs hover:shadow transition-all cursor-pointer shrink-0">
        <i class="fa-solid fa-plus text-xs"></i>
        <span>Add Category</span>
    </a>
</div>

{{-- ===================================================================
     SUMMARY METRICS
     =================================================================== --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 mb-7">
    <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total</span>
            <span class="w-6 h-6 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 text-xs">
                <i class="fa-solid fa-layer-group"></i>
            </span>
        </div>
        <span class="text-2xl font-extrabold text-gray-900 block mt-2">{{ $categoriesWithSubs->count() }}</span>
        <span class="text-[11px] text-gray-400 mt-0.5 block">Configured categories</span>
    </div>

    <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Active</span>
            <span class="w-6 h-6 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 text-xs">
                <i class="fa-solid fa-circle-check"></i>
            </span>
        </div>
        <span class="text-2xl font-extrabold text-emerald-600 block mt-2">{{ $categoriesWithSubs->where('active', true)->count() }}</span>
        <span class="text-[11px] text-gray-400 mt-0.5 block">Visible to customers</span>
    </div>

    <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Inactive</span>
            <span class="w-6 h-6 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">
                <i class="fa-solid fa-eye-slash"></i>
            </span>
        </div>
        <span class="text-2xl font-extrabold text-gray-400 block mt-2">{{ $categoriesWithSubs->where('active', false)->count() }}</span>
        <span class="text-[11px] text-gray-400 mt-0.5 block">Hidden from storefront</span>
    </div>

    <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Subcategories</span>
            <span class="w-6 h-6 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-xs">
                <i class="fa-solid fa-list-ul"></i>
            </span>
        </div>
        <span class="text-2xl font-extrabold text-indigo-600 block mt-2">{{ $categoriesWithSubs->sum(fn($c) => $c->subcategories->count()) }}</span>
        <span class="text-[11px] text-gray-400 mt-0.5 block">Total grouped items</span>
    </div>
</div>

{{-- ===================================================================
     CATEGORIES GRID
     =================================================================== --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($categoriesWithSubs as $cat)
    <div class="bg-white border border-gray-200/90 rounded-2xl overflow-hidden shadow-2xs flex flex-col justify-between">

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

                    {{-- Top Capsule Label --}}
                    <div class="relative z-10 self-start">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-white/90 text-gray-800 shadow-2xs backdrop-blur-xs">
                            <i class="fa-solid fa-sliders text-[8px] text-amber-500"></i> Slider
                        </span>
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
                <span class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-200/80 flex items-center justify-center text-gray-700 text-sm shrink-0 shadow-2xs">
                    <i class="{{ $cat->icon ?: 'fa-solid fa-sparkles' }}"></i>
                </span>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-extrabold text-gray-900 leading-tight truncate">{{ $cat->title }}</h3>
                        @if($cat->active)
                            <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/70 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                            </span>
                        @else
                            <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inactive
                            </span>
                        @endif
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

            <span class="text-[11px] font-semibold text-gray-400 shrink-0">
                Order <strong class="text-gray-700 font-mono">#{{ $cat->display_order }}</strong>
            </span>
        </div>

        {{-- Card Footer / Action Buttons --}}
        <div class="px-4 py-3 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between gap-2 mt-auto">
            <a href="{{ route('admin.categories.edit', $cat->id) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl shadow-2xs hover:shadow-xs transition-all cursor-pointer">
                <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                <span>Edit</span>
            </a>

            <form action="{{ route('admin.categories.delete', $cat->id) }}" method="POST" class="inline m-0">
                @csrf
                <button type="button"
                        onclick="confirmDelete(this, '{{ addslashes($cat->title) }}')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-rose-200/80 bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 text-xs font-bold transition-colors cursor-pointer"
                        title="Delete category">
                    <i class="fa-regular fa-trash-can text-xs"></i>
                    <span>Delete</span>
                </button>
            </form>
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
@endsection

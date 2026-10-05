@extends('layouts.admin')

@section('page_title', 'Reviews & Testimonials Management')
@section('page_heading', 'Reviews & Testimonials')
@section('page_subheading', 'Manage verified celebration stories, ratings, and independent page visibility')

@section('content')
<div class="space-y-6">

    <!-- 1. METRIC STATISTICS BANNER -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        
        <!-- Total Reviews -->
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-2xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Total Stories</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-gray-900 leading-none">{{ $totalCount }}</span>
                <span class="w-7 h-7 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-comments"></i>
                </span>
            </div>
            <span class="text-[10px] text-gray-500 mt-2 block">All recorded feedback</span>
        </div>

        <!-- Published (Active) -->
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-2xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Active</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-emerald-600 leading-none">{{ $activeCount }}</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
            </div>
            <span class="text-[10px] text-emerald-700 mt-2 block font-medium">Eligible for website</span>
        </div>

        <!-- Featured on Homepage -->
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-2xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">On Homepage</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-amber-600 leading-none">{{ $homeCount }}</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-house"></i>
                </span>
            </div>
            <span class="text-[10px] text-amber-700 mt-2 block font-medium">Homepage carousel</span>
        </div>

        <!-- Reviews Page -->
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-2xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Reviews Page</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-blue-600 leading-none">{{ $reviewsPageCount }}</span>
                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-file-lines"></i>
                </span>
            </div>
            <span class="text-[10px] text-blue-700 mt-2 block font-medium">Dedicated /reviews</span>
        </div>

        <!-- Event Details Specific -->
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-2xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Event Details</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-purple-600 leading-none">{{ $eventDetailsCount }}</span>
                <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-bullseye"></i>
                </span>
            </div>
            <span class="text-[10px] text-purple-700 mt-2 block font-medium">Package particulars</span>
        </div>

        <!-- Average Rating -->
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-2xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Avg Rating</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-amber-500 leading-none">{{ number_format($avgRating, 1) }}★</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-star"></i>
                </span>
            </div>
            <span class="text-[10px] text-gray-500 mt-2 block">Customer satisfaction</span>
        </div>

    </div>

    <!-- 2. SEARCH & FILTER TOOLBAR + ADD REVIEW ACTION -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-2xs overflow-hidden">
        
        <!-- Top Row: Search Input & Add Review Button -->
        <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100">
            
            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.reviews') }}" class="relative flex-grow max-w-md">
                @if($status !== 'all') <input type="hidden" name="status" value="{{ $status }}"> @endif
                @if($visibility !== 'all') <input type="hidden" name="visibility" value="{{ $visibility }}"> @endif
                @if($packageFilter !== 'all') <input type="hidden" name="package" value="{{ $packageFilter }}"> @endif
                @if($ratingFilter !== 'all') <input type="hidden" name="rating" value="{{ $ratingFilter }}"> @endif

                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search customer, review, event..." 
                       class="w-full bg-gray-50 border border-gray-200 pl-10 pr-9 py-2 text-xs font-semibold rounded-lg text-gray-900 outline-none focus:bg-white focus:border-gray-900 transition-colors">
                @if(!empty($search))
                    <a href="{{ route('admin.reviews', request()->except('search')) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs" title="Clear Search">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </a>
                @endif
            </form>

            <!-- Primary Action: Add Review -->
            <a href="{{ route('admin.reviews.create') }}" 
               class="px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg shadow-sm transition-all inline-flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Add Review</span>
            </a>
        </div>

        <!-- Bottom Row: Filter Dropdowns Strip -->
        <div class="px-4 py-3 bg-gray-50/60">
            <form method="GET" action="{{ route('admin.reviews') }}" class="flex flex-wrap items-center gap-2.5">
                @if(!empty($search)) <input type="hidden" name="search" value="{{ $search }}"> @endif

                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1.5 mr-1">
                    <i class="fa-solid fa-filter text-[10px] text-gray-400"></i> Filters:
                </span>

                <!-- Status Filter -->
                <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 rounded-lg outline-none focus:border-gray-900 transition-colors cursor-pointer shadow-2xs">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Status: All</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>✓ Active Only</option>
                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>✕ Inactive Only</option>
                </select>

                <!-- Placement / Visibility Filter -->
                <select name="visibility" onchange="this.form.submit()" class="bg-white border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 rounded-lg outline-none focus:border-gray-900 transition-colors cursor-pointer shadow-2xs">
                    <option value="all" {{ $visibility === 'all' ? 'selected' : '' }}>Placement: All</option>
                    <option value="home" {{ $visibility === 'home' ? 'selected' : '' }}>🏠 Homepage Carousel</option>
                    <option value="reviews_page" {{ $visibility === 'reviews_page' ? 'selected' : '' }}>📄 /reviews Page</option>
                    <option value="event_details" {{ $visibility === 'event_details' ? 'selected' : '' }}>🎯 Event Details</option>
                </select>

                <!-- Celebration Package Filter -->
                <select name="package" onchange="this.form.submit()" class="bg-white border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 rounded-lg outline-none focus:border-gray-900 transition-colors cursor-pointer shadow-2xs max-w-[210px] truncate">
                    <option value="all" {{ $packageFilter === 'all' ? 'selected' : '' }}>Package: All</option>
                    @foreach($packageOptions as $slug => $pkg)
                        <option value="{{ $slug }}" {{ $packageFilter === $slug ? 'selected' : '' }}>
                            {{ $pkg['title'] }}
                        </option>
                    @endforeach
                </select>

                <!-- Rating Filter -->
                <select name="rating" onchange="this.form.submit()" class="bg-white border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 rounded-lg outline-none focus:border-gray-900 transition-colors cursor-pointer shadow-2xs">
                    <option value="all" {{ $ratingFilter === 'all' ? 'selected' : '' }}>Rating: All</option>
                    <option value="5" {{ $ratingFilter === '5' ? 'selected' : '' }}>5 Stars ★★★★★</option>
                    <option value="4" {{ $ratingFilter === '4' ? 'selected' : '' }}>4 Stars ★★★★☆</option>
                    <option value="3" {{ $ratingFilter === '3' ? 'selected' : '' }}>3 Stars ★★★☆☆</option>
                    <option value="2" {{ $ratingFilter === '2' ? 'selected' : '' }}>2 Stars ★★☆☆☆</option>
                    <option value="1" {{ $ratingFilter === '1' ? 'selected' : '' }}>1 Star  ★☆☆☆☆</option>
                </select>

                <!-- Reset Filters Button (visible when any filter or search active) -->
                @if(!empty($search) || $status !== 'all' || $visibility !== 'all' || $packageFilter !== 'all' || $ratingFilter !== 'all')
                    <a href="{{ route('admin.reviews') }}" class="text-xs text-rose-600 hover:text-rose-800 font-bold px-2.5 py-1 inline-flex items-center gap-1.5 bg-rose-50 border border-rose-200 rounded-lg sm:ml-auto transition-colors">
                        <i class="fa-solid fa-xmark text-[10px]"></i> Reset Filters
                    </a>
                @endif

            </form>
        </div>

    </div>

    <!-- 3. REVIEWS LIST -->
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-2xs">
        
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between text-xs text-gray-500 font-medium">
            @if($reviews->total() > 0)
                <span>Showing <strong class="text-gray-900">{{ $reviews->firstItem() }}–{{ $reviews->lastItem() }}</strong> of <strong class="text-gray-900">{{ $reviews->total() }}</strong> verified customer reviews</span>
            @else
                <span>Showing <strong class="text-gray-900">0</strong> verified customer reviews</span>
            @endif
            <span class="text-[11px] text-gray-400">Recent First &bull; 16 per page</span>
        </div>

        @if($reviews->isEmpty())
            <div class="text-center py-16 px-4">
                <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-3 text-lg">
                    <i class="fa-solid fa-star"></i>
                </div>
                <h4 class="text-sm font-bold text-gray-900">No reviews found</h4>
                <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1 mb-4">No reviews match the selected filter criteria or database is empty.</p>
                <a href="{{ route('admin.reviews.create') }}" class="px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg shadow-sm transition-all inline-flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-plus"></i> Add First Review
                </a>
            </div>
        @else
            <div class="divide-y divide-gray-100" id="reviews-list-container">
                @foreach($reviews as $rev)
                    @php
                        $isActive = (bool)$rev->is_active;
                        $isOnHome = (bool)$rev->show_on_home;
                        $isOnReviews = (bool)$rev->show_on_reviews_page;
                        $isOnEvent = (bool)$rev->show_on_event_details;
                        $packageTitle = $packageOptions[$rev->package_slug]['title'] ?? ($rev->event_type ?: 'All Celebrations');
                    @endphp

                    <div class="p-4 sm:p-5 transition-colors hover:bg-gray-50/60 {{ $isActive ? '' : 'bg-gray-50/40 opacity-70' }}" id="review-row-{{ $rev->id }}" data-review="{{ json_encode($rev) }}">
                        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                            
                            <!-- Left: Reviewer & Content Details -->
                            <div class="flex-grow min-w-0 space-y-2">
                                
                                <!-- Meta line: Badges & Tags -->
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                        #{{ $rev->sort_order }}
                                    </span>

                                    <!-- Star Rating Representation -->
                                    <div class="inline-flex items-center gap-0.5 text-amber-400 text-xs px-2 py-0.5 rounded bg-amber-50 border border-amber-200">
                                        @for($s = 1; $s <= 5; $s++)
                                            <i class="fa-solid fa-star {{ $s <= $rev->rating ? 'text-amber-400' : 'text-gray-300' }} text-[10px]"></i>
                                        @endfor
                                        <span class="font-bold text-amber-800 text-[10px] ml-1">{{ $rev->rating }}.0</span>
                                    </div>

                                    <!-- Celebration Package Badge -->
                                    @if(!empty($rev->package_slug))
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            <i class="fa-solid fa-tag text-[9px]"></i> {{ $packageTitle }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                            <i class="fa-solid fa-asterisk text-[9px]"></i> General Celebration
                                        </span>
                                    @endif

                                    <!-- Status indicator badge -->
                                    @if(!$isActive)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="fa-solid fa-ban text-[9px]"></i> Inactive (Hidden Everywhere)
                                        </span>
                                    @endif
                                </div>

                                <!-- Review Text -->
                                <p class="text-xs text-gray-600 leading-relaxed pl-3 border-l-2 border-amber-300 font-normal">
                                    {{ $rev->review }}
                                </p>

                                <!-- Customer Details Footer -->
                                <div class="flex items-center gap-3 text-[11px] text-gray-500 pt-1 flex-wrap">
                                    <span class="font-bold text-gray-900 flex items-center gap-1">
                                        <i class="fa-solid fa-user text-gray-400 text-[10px]"></i> {{ $rev->author }}
                                    </span>
                                    <span class="text-gray-300">•</span>
                                    <span class="flex items-center gap-1 text-gray-600">
                                        <i class="fa-solid fa-location-dot text-amber-500 text-[10px]"></i> {{ $rev->location }}
                                    </span>
                                    @if(!empty($rev->email))
                                        <span class="text-gray-300">•</span>
                                        <span class="text-gray-500">{{ $rev->email }}</span>
                                    @endif
                                    <span class="text-gray-300">•</span>
                                    <span class="text-gray-400">{{ $rev->created_at ? $rev->created_at->format('d M Y') : 'Verified Customer' }}</span>
                                </div>

                            </div>

                            <!-- Right: Independent Visibility Buttons + Edit/Delete Actions -->
                            <div class="flex flex-col sm:flex-row lg:flex-col items-end gap-2.5 shrink-0 pt-0.5">
                                
                                <!-- Independent Button Toggle Controls Grid -->
                                <div class="grid grid-cols-2 gap-1.5 w-full sm:w-auto">
                                    
                                    <!-- Toggle 1: Active / Inactive -->
                                    <button type="button" onclick="quickToggleVisibility('{{ $rev->id }}', 'is_active')" 
                                            id="btn-vis-active-{{ $rev->id }}"
                                            title="Click to toggle publication status"
                                            class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold transition-all flex items-center justify-center gap-1 cursor-pointer border shadow-2xs {{ $isActive ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border-emerald-200' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 border-rose-200' }}">
                                        <i class="fa-solid {{ $isActive ? 'fa-circle-check text-emerald-600' : 'fa-ban text-rose-500' }} text-[10px]"></i>
                                        <span>{{ $isActive ? 'Active' : 'Inactive' }}</span>
                                    </button>

                                    <!-- Toggle 2: Homepage Placement -->
                                    <button type="button" onclick="quickToggleVisibility('{{ $rev->id }}', 'show_on_home')" 
                                            id="btn-vis-home-{{ $rev->id }}"
                                            title="Click to toggle Homepage visibility"
                                            class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold transition-all flex items-center justify-center gap-1 cursor-pointer border shadow-2xs {{ $isOnHome ? 'bg-amber-50 text-amber-800 hover:bg-amber-100 border-amber-300' : 'bg-gray-50 text-gray-500 hover:bg-gray-100 border-gray-200' }}">
                                        <i class="fa-solid fa-house {{ $isOnHome ? 'text-amber-500' : 'text-gray-300' }} text-[10px]"></i>
                                        <span>{{ $isOnHome ? 'Home: ON' : 'Home: OFF' }}</span>
                                    </button>

                                    <!-- Toggle 3: Reviews Page Placement -->
                                    <button type="button" onclick="quickToggleVisibility('{{ $rev->id }}', 'show_on_reviews_page')" 
                                            id="btn-vis-reviews_page-{{ $rev->id }}"
                                            title="Click to toggle Reviews Page visibility"
                                            class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold transition-all flex items-center justify-center gap-1 cursor-pointer border shadow-2xs {{ $isOnReviews ? 'bg-blue-50 text-blue-700 hover:bg-blue-100 border-blue-200' : 'bg-gray-50 text-gray-500 hover:bg-gray-100 border-gray-200' }}">
                                        <i class="fa-solid fa-file-lines {{ $isOnReviews ? 'text-blue-500' : 'text-gray-300' }} text-[10px]"></i>
                                        <span>{{ $isOnReviews ? 'Reviews: ON' : 'Reviews: OFF' }}</span>
                                    </button>

                                    <!-- Toggle 4: Event Details Placement -->
                                    <button type="button" onclick="quickToggleVisibility('{{ $rev->id }}', 'show_on_event_details')" 
                                            id="btn-vis-event_details-{{ $rev->id }}"
                                            title="Click to toggle Event Details page visibility"
                                            class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold transition-all flex items-center justify-center gap-1 cursor-pointer border shadow-2xs {{ $isOnEvent ? 'bg-purple-50 text-purple-700 hover:bg-purple-100 border-purple-200' : 'bg-gray-50 text-gray-500 hover:bg-gray-100 border-gray-200' }}">
                                        <i class="fa-solid fa-bullseye {{ $isOnEvent ? 'text-purple-500' : 'text-gray-300' }} text-[10px]"></i>
                                        <span>{{ $isOnEvent ? 'Event: ON' : 'Event: OFF' }}</span>
                                    </button>

                                </div>

                                <!-- Actions: Edit & Delete -->
                                <div class="flex items-center gap-1.5 self-end sm:self-auto">
                                    <a href="{{ route('admin.reviews.edit', $rev->id) }}" title="Edit Review"
                                       class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 flex items-center justify-center transition-colors cursor-pointer border border-gray-200 shadow-2xs">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>

                                    <form action="{{ route('admin.reviews.delete', $rev->id) }}" method="POST" class="inline m-0">
                                        @csrf
                                        <button type="button" 
                                                onclick="confirmDelete(this, 'Review by {{ addslashes($rev->author) }}')"
                                                title="Delete Review"
                                                class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 hover:text-red-700 flex items-center justify-center transition-colors cursor-pointer border border-red-100 shadow-2xs">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>

                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            @if($reviews->hasPages())
                <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-gray-50/50">
                    <div class="text-xs text-gray-500 font-medium">
                        Showing <strong class="text-gray-900 font-bold">{{ $reviews->firstItem() }}</strong> to <strong class="text-gray-900 font-bold">{{ $reviews->lastItem() }}</strong> of <strong class="text-gray-900 font-bold">{{ $reviews->total() }}</strong> reviews
                    </div>
                    
                    <div class="flex items-center gap-1">
                        {{-- Previous Page Link --}}
                        @if ($reviews->onFirstPage())
                            <span class="w-8 h-8 rounded-lg border border-gray-200 bg-white text-gray-300 flex items-center justify-center text-xs cursor-not-allowed">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            </span>
                        @else
                            <a href="{{ $reviews->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-gray-700 flex items-center justify-center text-xs transition-colors shadow-2xs cursor-pointer">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach ($reviews->getUrlRange(max(1, $reviews->currentPage() - 2), min($reviews->lastPage(), $reviews->currentPage() + 2)) as $page => $url)
                            @if ($page == $reviews->currentPage())
                                <span class="w-8 h-8 rounded-lg bg-gray-900 text-white font-bold flex items-center justify-center text-xs shadow-2xs">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="w-8 h-8 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-gray-700 font-medium flex items-center justify-center text-xs transition-colors shadow-2xs cursor-pointer">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($reviews->hasMorePages())
                            <a href="{{ $reviews->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-gray-700 flex items-center justify-center text-xs transition-colors shadow-2xs cursor-pointer">
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        @else
                            <span class="w-8 h-8 rounded-lg border border-gray-200 bg-white text-gray-300 flex items-center justify-center text-xs cursor-not-allowed">
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        @endif

    </div>

</div>
@endsection

@push('page_scripts')
<script>
    // Quick toggle handler for inline card buttons
    window.quickToggleVisibility = function(reviewId, field) {
        const btn = document.getElementById(`btn-vis-${field.replace("show_on_", "")}-${reviewId}`) || document.getElementById(`btn-vis-${field}-${reviewId}`);
        if (btn) btn.classList.add("opacity-50");

        fetch("{{ route('admin.reviews.toggle-visibility') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || "",
                "Accept": "application/json",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: reviewId, field: field })
        })
        .then(res => res.json())
        .then(data => {
            if (btn) btn.classList.remove("opacity-50");
            if (data.success) {
                updateCardButtonUI(reviewId, field, data.new_value);
                showAdminToast(data.message, "success");
            } else {
                showAdminToast(data.message || "Failed to update visibility.", "error");
            }
        })
        .catch(() => {
            if (btn) btn.classList.remove("opacity-50");
            showAdminToast("Network error while updating visibility.", "error");
        });
    };

    function updateCardButtonUI(reviewId, field, newValue) {
        if (field === "is_active") {
            const btn = document.getElementById(`btn-vis-active-${reviewId}`);
            const row = document.getElementById(`review-row-${reviewId}`);
            if (btn) {
                btn.className = `px-2.5 py-1.5 rounded-lg text-[11px] font-bold transition-all flex items-center justify-center gap-1 cursor-pointer border shadow-2xs ${newValue ? "bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border-emerald-200" : "bg-rose-50 text-rose-700 hover:bg-rose-100 border-rose-200"}`;
                btn.innerHTML = `<i class="fa-solid ${newValue ? "fa-circle-check text-emerald-600" : "fa-ban text-rose-500"} text-[10px]"></i><span>${newValue ? "Active" : "Inactive"}</span>`;
            }
            if (row) {
                if (newValue) {
                    row.classList.remove("bg-gray-50/40", "opacity-70");
                } else {
                    row.classList.add("bg-gray-50/40", "opacity-70");
                }
            }
        } else if (field === "show_on_home") {
            const btn = document.getElementById(`btn-vis-home-${reviewId}`);
            if (btn) {
                btn.className = `px-2.5 py-1.5 rounded-lg text-[11px] font-bold transition-all flex items-center justify-center gap-1 cursor-pointer border shadow-2xs ${newValue ? "bg-amber-50 text-amber-800 hover:bg-amber-100 border-amber-300" : "bg-gray-50 text-gray-500 hover:bg-gray-100 border-gray-200"}`;
                btn.innerHTML = `<i class="fa-solid fa-house ${newValue ? "text-amber-500" : "text-gray-300"} text-[10px]"></i><span>${newValue ? "Home: ON" : "Home: OFF"}</span>`;
            }
        } else if (field === "show_on_reviews_page") {
            const btn = document.getElementById(`btn-vis-reviews_page-${reviewId}`);
            if (btn) {
                btn.className = `px-2.5 py-1.5 rounded-lg text-[11px] font-bold transition-all flex items-center justify-center gap-1 cursor-pointer border shadow-2xs ${newValue ? "bg-blue-50 text-blue-700 hover:bg-blue-100 border-blue-200" : "bg-gray-50 text-gray-500 hover:bg-gray-100 border-gray-200"}`;
                btn.innerHTML = `<i class="fa-solid fa-file-lines ${newValue ? "text-blue-500" : "text-gray-300"} text-[10px]"></i><span>${newValue ? "Reviews: ON" : "Reviews: OFF"}</span>`;
            }
        } else if (field === "show_on_event_details") {
            const btn = document.getElementById(`btn-vis-event_details-${reviewId}`);
            if (btn) {
                btn.className = `px-2.5 py-1.5 rounded-lg text-[11px] font-bold transition-all flex items-center justify-center gap-1 cursor-pointer border shadow-2xs ${newValue ? "bg-purple-50 text-purple-700 hover:bg-purple-100 border-purple-200" : "bg-gray-50 text-gray-500 hover:bg-gray-100 border-gray-200"}`;
                btn.innerHTML = `<i class="fa-solid fa-bullseye ${newValue ? "text-purple-500" : "text-gray-300"} text-[10px]"></i><span>${newValue ? "Event: ON" : "Event: OFF"}</span>`;
            }
        }
    }

    function showAdminToast(message, type = "success") {
        const toast = document.createElement("div");
        toast.className = `fixed bottom-5 right-5 z-50 p-4 rounded-xl border shadow-xl flex items-center gap-3 text-xs font-bold transition-all transform duration-300 ${
            type === "success" ? "bg-emerald-900 text-white border-emerald-700" : "bg-rose-900 text-white border-rose-700"
        }`;
        toast.innerHTML = `<i class="fa-solid ${type === "success" ? "fa-circle-check text-emerald-400" : "fa-circle-exclamation text-rose-400"} text-sm"></i><span>${message}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3500);
    }
</script>
@endpush


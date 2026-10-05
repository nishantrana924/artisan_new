@extends('layouts.admin')

@section('page_title', 'Event Packages')
@section('page_heading', 'Event Packages')
@section('page_subheading', 'Browse, filter and manage all celebration packages, pricing tiers, and category links.')

@section('content')
<div class="space-y-6">

    <!-- Custom Scoped Styles for Scrollbar Hiding -->
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <!-- 1. Header Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200/80 pb-5">
        <div>
            <h2 class="text-xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2.5">
                <span>Event Packages</span>
                <span id="header-count-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200/60">
                    {{ $totalPackages }} {{ $totalPackages === 1 ? 'Package' : 'Packages' }}
                </span>
            </h2>
            <p class="text-xs text-gray-500 mt-1">Manage event setups, tiered pricing, and category-subcategory relationships.</p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('admin.packages.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 hover:bg-black active:scale-[0.98] text-white text-xs font-bold rounded-xl shadow-xs hover:shadow transition-all cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Add Package</span>
            </a>
        </div>
    </div>

    <!-- 2. Summary Metrics Cards (Clean, sleek 4-card grid aligned with categories style) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        <!-- Total Packages -->
        <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total</span>
                <span class="w-6 h-6 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 text-xs">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </span>
            </div>
            <span class="text-2xl font-extrabold text-gray-900 block mt-2">{{ $totalPackages }}</span>
            <span class="text-[11px] text-gray-400 mt-0.5 block">Catalog packages</span>
        </div>

        <!-- Published Packages -->
        <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Active</span>
                <span class="w-6 h-6 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 text-xs">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
            </div>
            <span class="text-2xl font-extrabold text-emerald-600 block mt-2">{{ $publishedCount }}</span>
            <span class="text-[11px] text-gray-400 mt-0.5 block">Live on storefront</span>
        </div>

        <!-- Draft / Inactive Packages -->
        <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Inactive</span>
                <span class="w-6 h-6 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">
                    <i class="fa-solid fa-eye-slash"></i>
                </span>
            </div>
            <span class="text-2xl font-extrabold text-gray-400 block mt-2">{{ $draftCount }}</span>
            <span class="text-[11px] text-gray-400 mt-0.5 block">Draft / hidden</span>
        </div>

        <!-- Categories Covered -->
        <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-purple-600 uppercase tracking-wider">Categories</span>
                <span class="w-6 h-6 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600 text-xs">
                    <i class="fa-solid fa-layer-group"></i>
                </span>
            </div>
            <span class="text-2xl font-extrabold text-purple-600 block mt-2">{{ $categoriesCount }}</span>
            <span class="text-[11px] text-gray-400 mt-0.5 block">Celebration categories</span>
        </div>
    </div>

    <!-- 3. Primary Category Tabs Rail -->
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Filter by Category</span>
            <span id="tab-active-label" class="text-xs font-semibold text-gray-500">Showing All Categories</span>
        </div>

        <div class="flex items-center gap-2 overflow-x-auto py-1 no-scrollbar max-w-full" id="p-category-tabs-rail" style="-ms-overflow-style: none; scrollbar-width: none;">
            <!-- All Categories Pill -->
            <button type="button" 
                    onclick="selectCategoryFilter('all', this)"
                    data-cat-filter="all"
                    class="cat-filter-btn shrink-0 h-9 px-3.5 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-2 cursor-pointer bg-gray-900 text-white shadow-xs">
                <i class="fa-solid fa-border-all text-[11px]"></i>
                <span>All Categories</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] bg-white/20 text-white font-extrabold leading-none">{{ $totalPackages }}</span>
            </button>

            <!-- Dynamic Category Pills -->
            @foreach($dbCategories as $cat)
                @php
                    $catPkgCount = $dbPackages->where('category_id', $cat->id)->count();
                @endphp
                <button type="button" 
                        onclick="selectCategoryFilter('{{ $cat->id }}', this)"
                        data-cat-filter="{{ $cat->id }}"
                        class="cat-filter-btn shrink-0 h-9 px-3.5 rounded-xl text-xs font-semibold transition-all inline-flex items-center gap-2 cursor-pointer bg-white hover:bg-gray-50 text-gray-700 hover:text-gray-900 border border-gray-200/80 shadow-2xs">
                    <i class="{{ $cat->icon ?: 'fa-solid fa-sparkles' }} text-[11px] text-gray-400"></i>
                    <span>{{ $cat->title }}</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] bg-gray-100 text-gray-600 font-bold border border-gray-200/60 leading-none">{{ $catPkgCount }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <!-- 4. Search & Filter Toolbar -->
    <div class="bg-white border border-gray-200/80 p-3 sm:p-3.5 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-3 shadow-2xs">
        <!-- Search Input -->
        <div class="relative flex-1 min-w-[240px]">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" 
                   id="package-search-input" 
                   oninput="handlePackageSearch(this.value)" 
                   placeholder="Search packages by title, category, tier name or keyword..." 
                   class="w-full h-10 bg-gray-50/70 hover:bg-gray-50 focus:bg-white border border-gray-200/80 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 pl-10 pr-4 text-xs font-medium text-gray-900 placeholder:text-gray-400 rounded-xl outline-none transition-all">
        </div>

        <!-- Filter Controls -->
        <div class="flex items-center gap-2.5 flex-wrap shrink-0">
            <!-- Status Filter -->
            <div class="relative min-w-[130px]">
                <select id="package-status-filter" 
                        onchange="handleStatusFilter(this.value)"
                        class="w-full h-10 bg-gray-50/70 hover:bg-gray-50 border border-gray-200/80 focus:border-gray-900 rounded-xl pl-3.5 pr-8 text-xs font-semibold text-gray-700 outline-none transition-all cursor-pointer appearance-none">
                    <option value="all">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
                <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 pointer-events-none"></i>
            </div>

            <!-- Clear Filters Button (hidden by default) -->
            <button type="button" 
                    id="clear-filters-btn"
                    onclick="resetAllFilters()"
                    class="hidden h-10 px-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition-colors items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-xmark text-xs"></i>
                <span>Clear</span>
            </button>

            <!-- Results Counter Pill -->
            <div class="inline-flex items-center gap-1.5 px-3 h-10 rounded-xl bg-gray-50/80 border border-gray-200/60 text-xs text-gray-500 font-medium shrink-0">
                <span>Showing</span>
                <span id="visible-count-number" class="font-bold text-gray-900">{{ $totalPackages }}</span>
                <span>of {{ $totalPackages }}</span>
            </div>
        </div>
    </div>

    <!-- 5. Packages Data Table -->
    <div class="border border-gray-200/90 rounded-2xl overflow-hidden bg-white shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[650px]">
                <thead>
                    <tr class="bg-gray-50/90 border-b border-gray-200/80 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4 min-w-[280px]">Package Details</th>
                        <th class="py-3 px-4 min-w-[180px]">Primary Category</th>
                        <th class="py-3 px-4 w-32 text-center">Visibility</th>
                        <th class="py-3 px-4 w-28 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="packages-table-body" class="divide-y divide-gray-100 text-xs text-gray-800">
                    @forelse($dbPackages as $idx => $pkg)
                        @php
                            $cat = $pkg->category;
                            $searchTerms = strtolower($pkg->title . ' ' . $pkg->slug . ' ' . ($cat->title ?? '') . ' ' . $pkg->tag . ' ' . ($pkg->badge ?? '') . ' ' . $pkg->subcategories->pluck('name')->implode(' '));
                        @endphp
                        <tr class="package-row hover:bg-gray-50/70 transition-colors group"
                            data-package-id="{{ $pkg->id }}"
                            data-category-id="{{ $pkg->category_id }}"
                            data-status="{{ $pkg->active ? 'published' : 'draft' }}"
                            data-search="{{ $searchTerms }}">
                            
                            <!-- Index -->
                            <td class="py-3.5 px-4 text-center text-gray-400 font-bold">
                                {{ $idx + 1 }}
                            </td>

                            <!-- Package Details -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <!-- Thumbnail -->
                                    <div class="w-12 h-12 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden shrink-0 relative shadow-2xs">
                                        @if($pkg->image)
                                            <img src="{{ $pkg->image }}" 
                                                 alt="{{ $pkg->title }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <i class="fa-regular fa-image text-base"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Title, Badge & Slug -->
                                    <div class="space-y-0.5 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-bold text-gray-900 group-hover:text-black text-sm truncate">
                                                {{ $pkg->title }}
                                            </span>
                                            @if($pkg->badge)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200/60 shadow-2xs">
                                                    {{ $pkg->badge }}
                                                </span>
                                            @elseif($pkg->tag)
                                                <span class="px-1.5 py-0.5 rounded-md text-[9.5px] font-extrabold uppercase tracking-wider bg-gray-100 text-gray-600 border border-gray-200/60">
                                                    {{ $pkg->tag }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 text-[11px] text-gray-400 font-mono">
                                            <span>/events/{{ $pkg->slug }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Primary Category -->
                            <td class="py-3.5 px-4">
                                @if($cat)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold border border-black/5 text-gray-900 shadow-2xs"
                                          style="background-color: {{ $cat->bg_color ?: '#F6CFB2' }}80;">
                                        <i class="{{ $cat->icon ?: 'fa-solid fa-sparkles' }} text-[10px] text-gray-700"></i>
                                        <span>{{ $cat->title }}</span>
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 italic">Uncategorized</span>
                                @endif
                            </td>

                            <!-- Visibility Status -->
                            <td class="py-3.5 px-4 text-center">
                                <button type="button" 
                                        onclick="toggleStatus({{ $pkg->id }}, this)"
                                        class="status-btn inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold transition-all cursor-pointer {{ $pkg->active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/80 hover:bg-emerald-100' : 'bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200' }}"
                                        title="Click to toggle status">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $pkg->active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    <span class="status-text">{{ $pkg->active ? 'Published' : 'Draft' }}</span>
                                </button>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Edit Link -->
                                    <a href="{{ route('admin.packages.edit', $pkg->id) }}" 
                                       class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-900 hover:text-white text-gray-700 flex items-center justify-center text-xs transition-colors shadow-2xs"
                                       title="Edit Package Specifications & Tiers">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <button type="button" 
                                            onclick="confirmDeletePackage({{ $pkg->id }}, '{{ addslashes($pkg->title) }}')"
                                            class="w-8 h-8 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center text-xs transition-colors cursor-pointer"
                                            title="Delete Package">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400">
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center mx-auto mb-2 text-gray-400 text-base">
                                    <i class="fa-solid fa-box-open"></i>
                                </div>
                                <h4 class="text-xs font-bold text-gray-800">No packages created yet</h4>
                                <p class="text-[11px] text-gray-400 mt-0.5">Click the "Add New Package" button above to get started.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Filter Empty State (Hidden by default) -->
        <div id="filter-empty-state" class="hidden py-14 text-center px-4">
            <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center mx-auto mb-2.5 text-gray-400 text-base">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <h4 class="text-xs font-bold text-gray-800">No packages match your filters</h4>
            <p class="text-[11px] text-gray-400 mt-0.5">Try searching with a different keyword or resetting your category/status filters.</p>
            <button type="button" 
                    onclick="resetAllFilters()" 
                    class="mt-3.5 px-3.5 py-1.5 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-bold inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
                <i class="fa-solid fa-rotate-left text-[10px]"></i> Reset All Filters
            </button>
        </div>
    </div>

</div>

<!-- Delete Confirmation Modal -->
<div id="delete-package-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-xl border border-gray-200 space-y-4 animate-scale-in">
        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-base border border-rose-100">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-gray-900">Delete Event Package?</h3>
            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                Are you sure you want to delete <span id="delete-package-title" class="font-bold text-gray-800"></span>? This will also remove all its associated pricing tiers.
            </p>
        </div>
        <div class="flex items-center justify-end gap-2 pt-2">
            <button type="button" 
                    onclick="closeDeleteModal()" 
                    class="px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer">
                Cancel
            </button>
            <form id="delete-package-form" method="POST" action="">
                @csrf
                <button type="submit" 
                        class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors cursor-pointer shadow-xs">
                    Yes, Delete
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Toast Feedback Notification -->
<div id="toast-notify" class="hidden fixed bottom-5 right-5 z-50 px-4 py-2.5 rounded-xl bg-gray-900 text-white text-xs font-semibold shadow-lg border border-white/10 flex items-center gap-2 transition-all duration-200">
    <i class="fa-solid fa-circle-check text-emerald-400"></i>
    <span id="toast-message">Action completed successfully</span>
</div>

@push('page_scripts')
<script>
    let currentCategoryFilter = 'all';
    let currentStatusFilter = 'all';
    let currentSearchQuery = '';

    // Category Tab Filter
    function selectCategoryFilter(catId, btn) {
        currentCategoryFilter = String(catId);

        // Update active pill button styling
        document.querySelectorAll('.cat-filter-btn').forEach(b => {
            b.className = 'cat-filter-btn shrink-0 h-9 px-3.5 rounded-xl text-xs font-semibold transition-all inline-flex items-center gap-2 cursor-pointer bg-white hover:bg-gray-50 text-gray-700 hover:text-gray-900 border border-gray-200/80 shadow-2xs';
            const countBadge = b.querySelector('span:last-child');
            if (countBadge) {
                countBadge.className = 'px-1.5 py-0.5 rounded-md text-[10px] bg-gray-100 text-gray-600 font-bold border border-gray-200/60 leading-none';
            }
        });

        if (btn) {
            btn.className = 'cat-filter-btn shrink-0 h-9 px-3.5 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-2 cursor-pointer bg-gray-900 text-white shadow-xs';
            const countBadge = btn.querySelector('span:last-child');
            if (countBadge) {
                countBadge.className = 'px-1.5 py-0.5 rounded-md text-[10px] bg-white/20 text-white font-extrabold leading-none';
            }

            const labelEl = document.getElementById('tab-active-label');
            if (labelEl) {
                const catName = btn.querySelector('span:nth-child(2)')?.textContent?.trim() || 'All Categories';
                labelEl.textContent = `Filtered by: ${catName}`;
            }
        }

        applyFilters();
    }

    // Search Filter
    function handlePackageSearch(query) {
        currentSearchQuery = (query || '').toLowerCase().trim();
        applyFilters();
    }

    // Status Filter
    function handleStatusFilter(status) {
        currentStatusFilter = status;
        applyFilters();
    }

    // Apply combined filters
    function applyFilters() {
        const rows = document.querySelectorAll('.package-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowCatId = String(row.getAttribute('data-category-id') || '');
            const rowStatus = String(row.getAttribute('data-status') || '');
            const rowSearch = String(row.getAttribute('data-search') || '');

            let matchCat = (currentCategoryFilter === 'all' || rowCatId === currentCategoryFilter);
            let matchStatus = (currentStatusFilter === 'all' || rowStatus === currentStatusFilter);
            let matchSearch = (!currentSearchQuery || rowSearch.includes(currentSearchQuery));

            if (matchCat && matchStatus && matchSearch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update count pill
        const countNumber = document.getElementById('visible-count-number');
        if (countNumber) countNumber.textContent = visibleCount;

        // Toggle empty state
        const emptyState = document.getElementById('filter-empty-state');
        const tableBody = document.getElementById('packages-table-body');
        if (emptyState) {
            if (visibleCount === 0 && rows.length > 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }

        // Toggle clear filters button
        const clearBtn = document.getElementById('clear-filters-btn');
        if (clearBtn) {
            const hasActiveFilters = (currentCategoryFilter !== 'all' || currentStatusFilter !== 'all' || currentSearchQuery !== '');
            if (hasActiveFilters) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }
    }

    // Reset filters
    function resetAllFilters() {
        currentCategoryFilter = 'all';
        currentStatusFilter = 'all';
        currentSearchQuery = '';

        const searchInput = document.getElementById('package-search-input');
        if (searchInput) searchInput.value = '';

        const statusFilter = document.getElementById('package-status-filter');
        if (statusFilter) statusFilter.value = 'all';

        const allTabBtn = document.querySelector('.cat-filter-btn[data-cat-filter="all"]');
        if (allTabBtn) {
            selectCategoryFilter('all', allTabBtn);
        } else {
            applyFilters();
        }
    }

    // AJAX Toggle Status
    function toggleStatus(pkgId, btn) {
        btn.disabled = true;
        btn.style.opacity = '0.6';

        fetch(`/admin/packages/toggle-status/${pkgId}`, {
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
                const text = btn.querySelector('.status-text');
                const row = btn.closest('.package-row');

                if (isActive) {
                    btn.className = 'status-btn inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold transition-all cursor-pointer bg-emerald-50 text-emerald-700 border border-emerald-200/80 hover:bg-emerald-100';
                    if (dot) dot.className = 'w-1.5 h-1.5 rounded-full bg-emerald-500';
                    if (text) text.textContent = 'Published';
                    if (row) row.setAttribute('data-status', 'published');
                } else {
                    btn.className = 'status-btn inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold transition-all cursor-pointer bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200';
                    if (dot) dot.className = 'w-1.5 h-1.5 rounded-full bg-gray-400';
                    if (text) text.textContent = 'Draft';
                    if (row) row.setAttribute('data-status', 'draft');
                }

                showToast(data.message || 'Status updated successfully');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.style.opacity = '1';
            console.error('Failed to toggle status', err);
            showToast('Failed to update status. Please try again.');
        });
    }

    // Delete modal confirmation
    function confirmDeletePackage(id, title) {
        const modal = document.getElementById('delete-package-modal');
        const titleEl = document.getElementById('delete-package-title');
        const form = document.getElementById('delete-package-form');

        if (titleEl) titleEl.textContent = `"${title}"`;
        if (form) form.action = `/admin/packages/destroy/${id}`;
        if (modal) modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('delete-package-modal');
        if (modal) modal.classList.add('hidden');
    }

    // Feedback Toast
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

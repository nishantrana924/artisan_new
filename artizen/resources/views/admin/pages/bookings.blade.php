
@extends('layouts.admin')

@section('page_title', 'Bookings Portal')
@section('page_heading', 'Bookings Management')
@section('page_subheading', 'Review customer requests, schedule venue staging & track offline fulfillment.')

@push('head_scripts')
<style>
    /* Custom Scrollbar Utilities */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Subtle Table Row Animations */
    .booking-row {
        transition: all 0.18s ease-in-out;
    }
    .booking-row:hover {
        background-color: #FAFAF9;
    }

    /* Pulse beacon */
    @keyframes subtle-beacon {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.15); }
    }
    .animate-beacon {
        animation: subtle-beacon 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
@endpush

@section('content')
<div class="space-y-5">

    <!-- 1. Page Header & Live Status Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200/80 pb-4">
        <div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">Bookings Management</h2>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-beacon"></span> Live System
                </span>
                <span id="header-total-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200/80">
                    0 Total Bookings
                </span>
            </div>
            <p class="text-xs text-gray-500 font-normal mt-1">Review celebration requests, manage event schedule staging & track offline fulfillment.</p>
        </div>

        <!-- Quick Toolbar -->
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <button type="button" 
                    id="btn-refresh-bookings"
                    onclick="handleRefreshBookings()" 
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-gray-200/90 bg-white hover:bg-gray-50 text-gray-700 text-xs font-bold transition-all shadow-2xs hover:shadow-xs active:scale-[0.98] cursor-pointer group">
                <i class="fa-solid fa-arrows-rotate text-gray-400 group-hover:rotate-180 transition-transform duration-300"></i>
                <span>Refresh</span>
            </button>
            <button type="button" 
                    onclick="exportBookingsCsv()" 
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-gray-200/90 bg-white hover:bg-gray-50 text-gray-700 text-xs font-bold transition-all shadow-2xs hover:shadow-xs active:scale-[0.98] cursor-pointer">
                <i class="fa-solid fa-file-arrow-down text-gray-500"></i>
                <span>Export CSV</span>
            </button>
        </div>
    </div>

    <!-- 2. SaaS KPI Cards Row (4 Interactive Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- Card 1: Total Requests -->
        <div onclick="resetAllBookingFilters()" 
             class="bg-white border border-gray-200/90 hover:border-gray-400 p-4 rounded-2xl shadow-2xs transition-all duration-200 flex items-center justify-between group cursor-pointer"
             title="Click to reset filters and view all bookings">
            <div class="min-w-0 pr-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-500 block truncate">Total Bookings</span>
                <span class="text-2xl font-black text-gray-900 block leading-tight mt-1" id="b-metric-total">0</span>
                <span class="text-[11px] text-gray-400 font-medium block truncate mt-0.5">All customer requests</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-gray-50 border border-gray-200/80 text-gray-700 flex items-center justify-center text-sm shrink-0 group-hover:bg-gray-900 group-hover:text-white transition-all shadow-2xs">
                <i class="fa-solid fa-layer-group"></i>
            </div>
        </div>

        <!-- Card 2: Pending Review (Urgent Callbacks) -->
        <div onclick="filterBookingStatus('Pending')" 
             class="bg-gradient-to-br from-amber-50/60 to-white border border-amber-200/90 hover:border-amber-400 p-4 rounded-2xl shadow-2xs transition-all duration-200 flex items-center justify-between group cursor-pointer" 
             title="Filter Pending requests requiring callback">
            <div class="min-w-0 pr-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-800 block truncate">Pending Review</span>
                <span class="text-2xl font-black text-amber-600 block leading-tight mt-1" id="b-metric-pending">0</span>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-amber-700 truncate mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Requires callback
                </span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-100/80 border border-amber-300 text-amber-700 flex items-center justify-center text-sm shrink-0 group-hover:bg-amber-600 group-hover:text-white transition-all shadow-2xs">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <!-- Card 3: Confirmed Events -->
        <div onclick="filterBookingStatus('Confirmed')" 
             class="bg-gradient-to-br from-emerald-50/40 to-white border border-emerald-200/80 hover:border-emerald-400 p-4 rounded-2xl shadow-2xs transition-all duration-200 flex items-center justify-between group cursor-pointer" 
             title="Filter Confirmed events scheduled for staging">
            <div class="min-w-0 pr-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800 block truncate">Confirmed Events</span>
                <span class="text-2xl font-black text-emerald-600 block leading-tight mt-1" id="b-metric-confirmed">0</span>
                <span class="text-[11px] text-emerald-700 font-semibold block truncate mt-0.5">Scheduled setups</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center text-sm shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-2xs">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>

        <!-- Card 4: Pipeline Revenue (Pending + Confirmed) -->
        <div class="bg-gradient-to-br from-blue-50/40 to-white border border-blue-200/80 hover:border-blue-400 p-4 rounded-2xl shadow-2xs transition-all duration-200 flex items-center justify-between group">
            <div class="min-w-0 pr-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-blue-800 block truncate">Pipeline Value</span>
                <span class="text-2xl font-black text-blue-700 block leading-tight mt-1" id="b-metric-pipeline">₹0</span>
                <span class="text-[11px] text-blue-600 font-medium block truncate mt-0.5" id="b-metric-avg-label">Pending + Confirmed</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200 text-blue-600 flex items-center justify-center text-sm shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-2xs">
                <i class="fa-solid fa-indian-rupee-sign"></i>
            </div>
        </div>
    </div>

    <!-- 3. Consolidated Category Navigation & Smart Filter Controls Bar -->
    <div class="bg-white border border-gray-200/90 rounded-2xl shadow-2xs overflow-hidden flex flex-col divide-y divide-gray-100">
        
        <!-- Row 1: Horizontal Category Pill Navigation Bar (Full width with smooth scroll) -->
        <div class="px-4 py-2.5 bg-gray-50/50 flex items-center">
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5 w-full" id="b-category-tabs-container">
                <!-- Category Tabs Rendered Dynamically -->
            </div>
            <span id="b-distribution-total-label" class="hidden"></span>
        </div>

        <!-- Row 2: Search Input + Status Filter Segmented Controls + Sort Selector -->
        <div class="p-3.5 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            
            <!-- Search Input -->
            <div class="relative w-full lg:w-72 shrink-0">
                <span class="absolute left-3.5 top-2.5 text-xs text-gray-400 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" 
                       id="b-search-input" 
                       oninput="handleBookingSearch(this.value)" 
                       placeholder="Search name, mobile, ID, venue..." 
                       class="w-full bg-gray-50 hover:bg-gray-100/70 focus:bg-white border border-gray-200/90 focus:border-gray-900 pl-9 pr-9 py-2 text-xs font-semibold text-gray-900 rounded-xl outline-none transition-all placeholder:text-gray-400 placeholder:font-normal shadow-2xs">
                <button type="button" 
                        id="b-search-clear-btn" 
                        onclick="clearBookingSearch()" 
                        class="hidden absolute right-3 top-2.5 text-gray-400 hover:text-gray-700 text-xs cursor-pointer">
                    <i class="fa-solid fa-circle-xmark"></i>
                </button>
            </div>

            <!-- Status Filter Segmented Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5 flex-1 min-w-0" id="b-status-pills-container">
                <!-- Status Pills Rendered Dynamically -->
            </div>

            <!-- Sort Selector -->
            <div class="flex items-center gap-1.5 shrink-0 border-t lg:border-t-0 pt-2 lg:pt-0 border-gray-100">
                <label for="b-sort-select" class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider shrink-0">Sort:</label>
                <select id="b-sort-select" 
                        onchange="handleBookingSortChange(this.value)" 
                        class="bg-gray-50 border border-gray-200/90 hover:border-gray-300 px-2.5 py-1.5 text-xs font-bold text-gray-800 rounded-xl outline-none focus:border-gray-900 cursor-pointer shadow-2xs transition-colors">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="event_date">Event Date (Nearest)</option>
                    <option value="highest_val">Amount (High-Low)</option>
                    <option value="lowest_val">Amount (Low-High)</option>
                </select>
            </div>
        </div>

        <!-- Row 3: Fast Quick Views Bar (Matches Reference Image 1 Exactly) -->
        <div class="px-4 py-2.5 bg-white flex items-center justify-between gap-3 border-t border-gray-100 flex-wrap text-xs">
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Chip 1: Next 48 Hours -->
                <button type="button" 
                        id="btn-urgency-48h"
                        onclick="filterBookingDateUrgency('urgent_48h')" 
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl border border-[#fde68a] bg-[#fffdf5] text-[#92400e] text-xs font-bold transition-all shadow-2xs hover:shadow-xs active:scale-95 cursor-pointer">
                    <span class="w-2 h-2 rounded-full bg-[#f59e0b] shrink-0"></span>
                    <span class="text-[#ea580c] text-xs">⚡</span>
                    <span>Next 48 Hours</span>
                </button>

                <!-- Chip 2: This Weekend -->
                <button type="button" 
                        id="btn-urgency-weekend"
                        onclick="filterBookingDateUrgency('weekend')" 
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl border border-[#bfdbfe] bg-[#eff6ff] text-[#1d4ed8] text-xs font-bold transition-all shadow-2xs hover:shadow-xs active:scale-95 cursor-pointer">
                    <span class="text-sm">🥂</span>
                    <span>This Weekend</span>
                </button>

                <!-- Chip 3: Pending Callbacks -->
                <button type="button" 
                        id="btn-urgency-callbacks"
                        onclick="togglePendingCallbacksFilter()" 
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl border border-[#fecdd3] bg-[#fff1f2] text-[#9f1239] text-xs font-bold transition-all shadow-2xs hover:shadow-xs active:scale-95 cursor-pointer">
                    <i class="fa-solid fa-clock-rotate-left text-[#e11d48] text-xs"></i>
                    <span>Pending Callbacks</span>
                </button>
            </div>

            <!-- Right Helper Text -->
            <div class="text-xs text-gray-400 hidden sm:flex items-center gap-1.5 font-medium">
                <span>Quick Filter: Click any</span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-bold bg-[#fffbeb] text-[#b45309] border border-[#fde68a] shadow-2xs">
                    <i class="fa-solid fa-tag text-[#d97706] text-[10px]"></i>
                    <span>Category</span>
                </span>
                <span>in table</span>
                <span class="text-gray-300 mx-1">•</span>
                <span>Press <kbd class="px-2 py-0.5 rounded-md bg-gray-100 border border-gray-300 font-mono text-[11px] text-gray-700 font-bold shadow-2xs">/</kbd> to search</span>
            </div>
        </div>
    </div>

    <!-- Active Filters Feedback Bar -->
    <div id="b-active-filters-bar" class="hidden items-center justify-between bg-amber-50/80 border border-amber-200 px-4 py-2 rounded-xl text-xs text-amber-900 shadow-2xs animate-fadeIn">
        <div class="flex items-center gap-2 font-medium">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            <span class="font-bold text-amber-950">Active Filters:</span>
            <span id="b-active-filters-text" class="text-amber-800 font-semibold"></span>
        </div>
        <button type="button" 
                onclick="resetAllBookingFilters()" 
                class="text-xs font-bold text-amber-800 hover:text-amber-950 underline cursor-pointer ml-3 shrink-0">
            Clear All Filters
        </button>
    </div>

    <!-- Hidden compatibility elements -->
    <div id="b-distribution-bar" class="hidden"></div>
    <div id="b-distribution-legend" class="hidden"></div>
    <span id="b-selected-cat-title" class="hidden"></span>
    <span id="b-selected-cat-desc" class="hidden"></span>

    <!-- 3.5. Multiple Bookings Selected Action Banner (Inline Toolbar) -->
    <div id="b-inline-selection-bar" class="hidden items-center justify-between px-4 py-2.5 bg-gray-900 text-white rounded-2xl shadow-md border border-gray-800 animate-fadeIn flex-wrap gap-2 text-xs">
        <div class="flex items-center gap-2 font-bold shrink-0">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span><span id="b-inline-selected-count">0</span> Bookings Selected</span>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" 
                    onclick="bulkUpdateSelectedStatus('Confirmed')" 
                    class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer active:scale-95">
                <i class="fa-solid fa-check"></i>
                <span>Approve</span>
            </button>
            <button type="button" 
                    onclick="bulkUpdateSelectedStatus('Contacted')" 
                    class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer active:scale-95">
                <i class="fa-solid fa-phone"></i>
                <span>Contacted</span>
            </button>
            <button type="button" 
                    onclick="bulkUpdateSelectedStatus('Cancelled')" 
                    class="px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer active:scale-95">
                <i class="fa-solid fa-xmark"></i>
                <span>Cancel</span>
            </button>
            <button type="button" 
                    onclick="bulkDeleteSelectedBookings()" 
                    class="px-3.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer active:scale-95 border border-rose-500">
                <i class="fa-solid fa-trash-can"></i>
                <span>Delete Selected</span>
            </button>
            <button type="button" 
                    onclick="exportSelectedCsv()" 
                    class="px-3 py-1.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-bold transition-all border border-gray-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-arrow-down"></i>
                <span>Export CSV</span>
            </button>
            <button type="button" 
                    onclick="clearSelectedBookings()" 
                    class="ml-2 text-gray-400 hover:text-white text-xs font-semibold cursor-pointer underline">
                Deselect All
            </button>
        </div>
    </div>

    <!-- 4. Clean Normal Table View (md and above) -->
    <div class="hidden md:block bg-white border border-gray-200/90 rounded-2xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-gray-100 text-gray-900 font-bold text-xs">
                        <th class="py-4 px-3 w-[44px] text-center">
                            <input type="checkbox" id="b-select-all" onchange="toggleSelectAllBookings(this.checked)" class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-0 cursor-pointer" title="Select All on this page">
                        </th>
                        <th class="py-4 px-3 w-[150px] font-bold text-gray-900 text-xs">Enquiry ID</th>
                        <th class="py-4 px-3 min-w-[190px] font-bold text-gray-900 text-xs">Customer</th>
                        <th class="py-4 px-3 min-w-[250px] font-bold text-gray-900 text-xs">Package & Category</th>
                        <th class="py-4 px-3 w-[170px] font-bold text-gray-900 text-xs">Event Date</th>
                        <th class="py-4 px-3 w-[140px] font-bold text-gray-900 text-xs">Venue</th>
                        <th class="py-4 px-3 w-[120px] font-bold text-gray-900 text-xs">Amount</th>
                        <th class="py-4 px-3 w-[130px] font-bold text-gray-900 text-xs">Status</th>
                        <th class="py-4 px-4 min-w-[280px] font-bold text-gray-900 text-xs">Quick Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white" id="bookings-main-tbody">
                    <!-- Loaded dynamically via JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- 5. Mobile Card View (< md) -->
    <div class="block md:hidden flex flex-col gap-3.5" id="bookings-mobile-container">
        <!-- Loaded dynamically via JS -->
    </div>

    <!-- 6. Empty State Container -->
    <div id="b-empty-state" class="hidden flex-col items-center justify-center py-14 px-4 bg-white border border-gray-200/90 rounded-2xl text-center shadow-2xs">
        <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-200/80 flex items-center justify-center text-gray-400 text-xl mb-3 shadow-2xs">
            <i class="fa-solid fa-calendar-xmark"></i>
        </div>
        <h4 class="text-sm font-extrabold text-gray-900" id="b-empty-state-title">No Bookings Found</h4>
        <p class="text-xs text-gray-500 max-w-sm mt-1 mb-4" id="b-empty-state-desc">There are no bookings matching the selected filter criteria or search keyword.</p>
        <button type="button" 
                onclick="resetAllBookingFilters()" 
                class="px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer">
            Reset Filters
        </button>
    </div>

    <!-- 7. Modern Pagination Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white border border-gray-200/90 px-4 py-3 rounded-2xl shadow-2xs text-xs text-gray-600 font-medium">
        <span id="b-pagination-info" class="text-gray-500 font-semibold">Showing 1 to 0 of 0 bookings</span>
        <div class="flex items-center gap-1.5" id="b-pagination-controls">
            <!-- Pagination buttons rendered dynamically -->
        </div>
    </div>

    <!-- 8. Floating Bulk Actions Bar (Sticky Bottom when bookings are selected) -->
    <div id="b-bulk-actions-bar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 hidden items-center gap-3 bg-gray-900 text-white px-5 py-3 rounded-2xl shadow-2xl border border-gray-700 animate-fadeIn flex-wrap max-w-[95vw]">
        <div class="flex items-center gap-2 pr-3 border-r border-gray-700 text-xs font-bold shrink-0">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span id="b-bulk-selected-count">0</span> Bookings Selected
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" 
                    onclick="bulkUpdateSelectedStatus('Confirmed')" 
                    class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer active:scale-95">
                <i class="fa-solid fa-check"></i>
                <span>Approve Selected</span>
            </button>
            <button type="button" 
                    onclick="bulkUpdateSelectedStatus('Contacted')" 
                    class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer active:scale-95">
                <i class="fa-solid fa-phone"></i>
                <span>Mark Contacted</span>
            </button>
            <button type="button" 
                    onclick="bulkUpdateSelectedStatus('Cancelled')" 
                    class="px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer active:scale-95">
                <i class="fa-solid fa-xmark"></i>
                <span>Cancel Selected</span>
            </button>
            <button type="button" 
                    onclick="bulkDeleteSelectedBookings()" 
                    class="px-3.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer active:scale-95 border border-rose-500">
                <i class="fa-solid fa-trash-can"></i>
                <span>Delete Selected</span>
            </button>
            <button type="button" 
                    onclick="exportSelectedCsv()" 
                    class="px-3 py-1.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-bold transition-all border border-gray-600 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-arrow-down"></i>
                <span>Export CSV</span>
            </button>
        </div>
        <button type="button" 
                onclick="clearSelectedBookings()" 
                class="ml-2 text-gray-400 hover:text-white text-xs font-semibold cursor-pointer underline">
            Deselect All
        </button>
    </div>

</div>
@endsection

@push('modals')
    <!-- CUSTOM FLOATING STATUS POPOVER MENU (Zero Emojis, Modern SaaS Design) -->
    <div id="status-change-popover" 
         class="fixed z-[70] hidden bg-white rounded-2xl shadow-2xl border border-gray-200/90 py-2 w-56 text-xs transition-all duration-150 animate-scaleIn">
        <div class="px-3.5 py-2 border-b border-gray-100 flex items-center justify-between">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-400">Update Status</span>
            <span id="popover-booking-label" class="text-[11px] font-mono font-bold text-gray-800 bg-gray-100 px-2 py-0.5 rounded-md"></span>
        </div>
        <div class="py-1.5 flex flex-col gap-0.5 px-1.5">
            <!-- Pending -->
            <button type="button" onclick="selectStatus('Pending')" class="status-popover-item w-full flex items-center justify-between px-3 py-2 rounded-xl text-left hover:bg-[#fffbeb] transition-colors text-gray-800 cursor-pointer group">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#f59e0b] shrink-0"></span>
                    <div>
                        <span class="block font-bold text-xs text-gray-800 group-hover:text-amber-900">Pending</span>
                        <span class="block text-[10px] text-gray-400 group-hover:text-amber-700">Requires callback</span>
                    </div>
                </div>
                <span class="status-check-icon text-amber-600 hidden" data-status="Pending">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </span>
            </button>

            <!-- Contacted -->
            <button type="button" onclick="selectStatus('Contacted')" class="status-popover-item w-full flex items-center justify-between px-3 py-2 rounded-xl text-left hover:bg-[#eff6ff] transition-colors text-gray-800 cursor-pointer group">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#3b82f6] shrink-0"></span>
                    <div>
                        <span class="block font-bold text-xs text-gray-800 group-hover:text-blue-900">Contacted</span>
                        <span class="block text-[10px] text-gray-400 group-hover:text-blue-700">Customer contacted</span>
                    </div>
                </div>
                <span class="status-check-icon text-blue-600 hidden" data-status="Contacted">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </span>
            </button>

            <!-- Confirmed -->
            <button type="button" onclick="selectStatus('Confirmed')" class="status-popover-item w-full flex items-center justify-between px-3 py-2 rounded-xl text-left hover:bg-[#ecfdf5] transition-colors text-gray-800 cursor-pointer group">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#10b981] shrink-0"></span>
                    <div>
                        <span class="block font-bold text-xs text-gray-800 group-hover:text-emerald-900">Confirmed</span>
                        <span class="block text-[10px] text-gray-400 group-hover:text-emerald-700">Scheduled for staging</span>
                    </div>
                </div>
                <span class="status-check-icon text-emerald-600 hidden" data-status="Confirmed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </span>
            </button>

            <!-- Completed -->
            <button type="button" onclick="selectStatus('Completed')" class="status-popover-item w-full flex items-center justify-between px-3 py-2 rounded-xl text-left hover:bg-[#faf5ff] transition-colors text-gray-800 cursor-pointer group">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#8b5cf6] shrink-0"></span>
                    <div>
                        <span class="block font-bold text-xs text-gray-800 group-hover:text-purple-900">Completed</span>
                        <span class="block text-[10px] text-gray-400 group-hover:text-purple-700">Setup fulfilled</span>
                    </div>
                </div>
                <span class="status-check-icon text-purple-600 hidden" data-status="Completed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </span>
            </button>

            <!-- Cancelled -->
            <button type="button" onclick="selectStatus('Cancelled')" class="status-popover-item w-full flex items-center justify-between px-3 py-2 rounded-xl text-left hover:bg-[#fff1f2] transition-colors text-gray-800 cursor-pointer group">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#f43f5e] shrink-0"></span>
                    <div>
                        <span class="block font-bold text-xs text-gray-800 group-hover:text-rose-900">Cancelled</span>
                        <span class="block text-[10px] text-gray-400 group-hover:text-rose-700">Booking discarded</span>
                    </div>
                </div>
                <span class="status-check-icon text-rose-600 hidden" data-status="Cancelled">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </span>
            </button>

            <!-- Divider & Delete Single Booking -->
            <div class="border-t border-gray-100 my-0.5"></div>
            <button type="button" onclick="deleteActiveDropdownBooking()" class="status-popover-item w-full flex items-center justify-between px-3 py-2 rounded-xl text-left hover:bg-rose-50 transition-colors text-rose-700 cursor-pointer group">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-trash-can text-rose-500 text-xs"></i>
                    <div>
                        <span class="block font-bold text-xs text-rose-700 group-hover:text-rose-900">Delete Booking</span>
                        <span class="block text-[10px] text-rose-400 group-hover:text-rose-600">Permanently remove</span>
                    </div>
                </div>
            </button>
        </div>
    </div>

    <!-- RIGHT-SIDE BOOKING DETAIL SLIDE-OVER DRAWER -->
    <div id="booking-drawer-overlay" 
         onclick="closeBookingDrawer()" 
         class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 hidden opacity-0 transition-opacity duration-300"></div>
    
    <aside id="booking-detail-drawer" 
           class="fixed top-0 right-0 h-full w-full max-w-lg bg-white border-l border-gray-200 z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col shadow-2xl">
        
        <!-- Drawer Header -->
        <div class="px-6 py-5 border-b border-gray-200/90 flex items-center justify-between bg-gray-50/80">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 border border-amber-200 text-amber-800 flex items-center justify-center font-bold text-sm shrink-0" id="drawer-customer-avatar">
                    BK
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-mono font-bold text-gray-900 bg-white px-2 py-0.5 rounded border border-gray-200" id="drawer-booking-id">#BK-0000</span>
                        <span id="drawer-status-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">Pending</span>
                    </div>
                    <h3 class="text-base font-extrabold text-gray-900 mt-0.5 truncate max-w-[260px]" id="drawer-customer-title">Booking Details</h3>
                </div>
            </div>
            <button type="button" 
                    onclick="closeBookingDrawer()" 
                    class="w-9 h-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-100 flex items-center justify-center text-gray-500 hover:text-gray-900 transition-colors cursor-pointer shadow-2xs">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Drawer Content Body -->
        <div class="flex-grow overflow-y-auto px-6 py-5 flex flex-col gap-5 text-left text-xs">
            
            <!-- Section 1: Customer Profile Details -->
            <div class="bg-gray-50/60 border border-gray-200/80 rounded-2xl p-4 flex flex-col gap-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-user text-gray-400"></i> Customer Profile
                    </span>
                    <div id="drawer-customer-link-container"></div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                    <div>
                        <span class="text-gray-400 block text-[10px] font-medium uppercase">Full Name</span>
                        <span id="drawer-customer-name" class="font-bold text-gray-900 text-sm block">--</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] font-medium uppercase">Phone / Mobile</span>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span id="drawer-customer-mobile" class="font-bold text-gray-900">--</span>
                            <a id="drawer-call-link" href="#" class="text-blue-600 hover:text-blue-800 text-[11px]" title="Direct Call">
                                <i class="fa-solid fa-phone"></i>
                            </a>
                        </div>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] font-medium uppercase">WhatsApp Number</span>
                        <a id="drawer-whatsapp-link" href="#" target="_blank" class="inline-flex items-center gap-1 text-emerald-700 font-bold hover:underline mt-0.5">
                            <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                            <span>Chat on WhatsApp</span>
                        </a>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] font-medium uppercase">Email Address</span>
                        <span id="drawer-customer-email" class="text-gray-700 font-medium block truncate mt-0.5">--</span>
                    </div>
                </div>
            </div>

            <!-- Section 2: Event & Package Setup Details -->
            <div class="bg-white border border-gray-200/90 rounded-2xl p-4 flex flex-col gap-3">
                <span class="text-[11px] font-extrabold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-500"></i> Package & Event Setup
                </span>
                
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-gray-400 block text-[10px] font-medium uppercase">Package Title</span>
                            <span id="drawer-package-title" class="font-extrabold text-gray-900 text-sm">--</span>
                        </div>
                        <span id="drawer-category-badge" class="px-2.5 py-1 rounded-lg bg-gray-100 border border-gray-200 text-gray-700 text-[10px] font-bold">
                            Category
                        </span>
                    </div>
                    <div class="mt-1">
                        <span class="text-gray-400 block text-[10px] font-medium uppercase">Tier / Deliverables</span>
                        <span id="drawer-package-tier" class="font-semibold text-gray-700 text-xs">--</span>
                    </div>
                    <!-- Addons container -->
                    <div id="drawer-addons-container" class="hidden mt-2 pt-2 border-t border-gray-100">
                        <span class="text-gray-400 block text-[10px] font-medium uppercase mb-1">Included Add-ons</span>
                        <div id="drawer-addons-list" class="flex flex-wrap gap-1.5"></div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Event Schedule & Timing -->
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-50/60 border border-gray-200/80 rounded-2xl p-3.5 flex flex-col">
                    <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider flex items-center gap-1">
                        <i class="fa-regular fa-calendar text-gray-500"></i> Event Date
                    </span>
                    <span id="drawer-event-date" class="font-black text-gray-900 text-sm mt-1">--</span>
                    <span id="drawer-relative-date" class="text-[10px] text-gray-500 font-semibold mt-0.5">--</span>
                </div>
                <div class="bg-gray-50/60 border border-gray-200/80 rounded-2xl p-3.5 flex flex-col">
                    <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider flex items-center gap-1">
                        <i class="fa-regular fa-clock text-gray-500"></i> Event Time & Guests
                    </span>
                    <span id="drawer-event-time" class="font-black text-gray-900 text-sm mt-1">--</span>
                    <span id="drawer-guest-count" class="text-[10px] text-gray-600 font-semibold mt-0.5">-- Guests</span>
                </div>
            </div>

            <!-- Section 4: Venue Staging Address (Full Location Details) -->
            <div class="bg-white border border-gray-200/90 rounded-2xl p-4 flex flex-col gap-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-location-dot text-rose-500"></i> Venue & Staging Location
                    </span>
                    <a id="drawer-maps-link" href="#" target="_blank" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1">
                        <i class="fa-solid fa-map-location-dot"></i> Maps
                    </a>
                </div>
                
                <div class="flex flex-col gap-1 text-gray-800 font-medium">
                    <div class="font-bold text-gray-900 text-xs" id="drawer-venue-address">--</div>
                    <div class="text-gray-500 text-[11px]">
                        <span id="drawer-venue-landmark"></span>
                        <span id="drawer-venue-area" class="font-semibold text-gray-700"></span>
                    </div>
                    <div class="text-gray-600 text-[11px]">
                        <span id="drawer-venue-city">Indore</span>, <span id="drawer-venue-state">Madhya Pradesh</span> - <span id="drawer-venue-pincode" class="font-mono">--</span>
                    </div>
                </div>
            </div>

            <!-- Section 5: Customer Special Instructions / Notes -->
            <div id="drawer-notes-card" class="bg-amber-50/60 border border-amber-200/80 rounded-2xl p-4 flex flex-col gap-1.5">
                <span class="text-[11px] font-extrabold text-amber-900 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-note-sticky text-amber-600"></i> Customer Special Notes
                </span>
                <p id="drawer-customer-notes" class="text-xs text-amber-950 font-medium leading-relaxed italic">
                    No special instructions provided.
                </p>
            </div>

            <!-- Section 6: Financials & Offline Payment Settlement -->
            <div class="bg-gray-50/60 border border-gray-200/80 rounded-2xl p-4 flex flex-col gap-2">
                <span class="text-[11px] font-extrabold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-receipt text-gray-400"></i> Bill & Offline Settlement
                </span>
                <div class="flex flex-col gap-1.5 bg-white border border-gray-200/80 p-3 rounded-xl font-medium">
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Base Package Price</span>
                        <span class="font-bold text-gray-900" id="drawer-price-base">₹0</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-500 text-[11px]">
                        <span>Area Location Surcharge</span>
                        <span id="drawer-price-surcharge">₹0</span>
                    </div>
                    <div class="border-t border-gray-100 pt-2 flex justify-between items-center text-sm">
                        <span class="font-extrabold text-gray-900">Total Booking Amount</span>
                        <span class="font-black text-gray-900 text-base" id="drawer-price-total">₹0</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-[10px] text-gray-500 font-medium mt-0.5">
                    <i class="fa-solid fa-circle-info text-amber-500"></i>
                    <span>Offline Payment: Cash or UPI collected by staff upon setup completion.</span>
                </div>
                <button type="button" onclick="downloadActiveBookingPDF(this)" class="mt-2 w-full py-2.5 px-3 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-bold flex items-center justify-center gap-2 transition-all cursor-pointer shadow-sm">
                    <i class="fa-solid fa-file-pdf text-amber-400"></i> Download Official Booking PDF
                </button>
            </div>

        </div>

        <!-- Drawer Footer: Workflow Status Transitions -->
        <div class="p-4 border-t border-gray-200 bg-gray-50/90 flex flex-col gap-2.5 shrink-0">
            <div class="flex items-center justify-between">
                <span class="text-[11px] text-gray-600 font-extrabold uppercase tracking-wider">Update Booking Lifecycle:</span>
                <button type="button" 
                        onclick="deleteCurrentDrawerBooking()" 
                        class="text-[11px] font-bold text-rose-600 hover:text-rose-800 flex items-center gap-1 cursor-pointer transition-colors"
                        title="Permanently delete this booking">
                    <i class="fa-solid fa-trash-can text-[10px]"></i>
                    <span>Delete Booking</span>
                </button>
            </div>
            <div class="grid grid-cols-2 gap-2" id="drawer-action-buttons">
                <!-- Action buttons rendered dynamically based on active booking -->
            </div>
        </div>
    </aside>
@endpush

@push('page_scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="{{ asset('js/artizen-pdf.js') }}?v=2.2"></script>
    <script>
        // -------------------------------------------------------------
        // ARTIZEN BOOKINGS PORTAL CONTROLLER (Unified High-Performance Engine)
        // -------------------------------------------------------------
        let bookingsDB = @json($bookings);
        let rawPackages = @json($packages);
        let rawCategories = @json($categories);

        const OFFICIAL_CATEGORIES = [
            { slug: 'all', name: 'All Bookings', icon: 'fa-solid fa-list-check' },
            { slug: 'birthdays', name: 'Birthdays', icon: 'fa-solid fa-cake-candles' },
            { slug: 'kids-cozy', name: 'Kids & Cozy', icon: 'fa-solid fa-child-reaching' },
            { slug: 'house-party', name: 'House Party', icon: 'fa-solid fa-house-chimney-user' },
            { slug: 'proposal-anniversary', name: 'Proposal & Anniversary', icon: 'fa-solid fa-heart' },
            { slug: 'dj-acoustic', name: 'DJ & Acoustic', icon: 'fa-solid fa-compact-disc' },
            { slug: 'weddings-sangeet', name: 'Weddings & Sangeet', icon: 'fa-solid fa-ring' },
            { slug: 'baby-corporate', name: 'Baby & Corporate', icon: 'fa-solid fa-building-user' }
        ];

        let currentBookingsCategory = 'all';
        let currentBookingsStatus = 'all';
        let currentBookingsSearch = '';
        let currentBookingsSort = 'newest';
        let currentBookingsDateUrgency = 'all'; // 'all', 'urgent_48h', 'weekend'
        let currentBookingsPage = 1;
        const BOOKINGS_PAGE_SIZE = 15;
        let activeDrawerBookingId = null;
        let selectedBookingIds = new Set();
        let lastRenderedPageData = [];

        /**
         * Detect booking category slug cleanly
         */
        function getBookingCategorySlug(b) {
            if (b.category_slug) return b.category_slug;
            const cat = (b.category || b.category_name || '').toLowerCase();
            const pkg = (b.package || b.package_name || '').toLowerCase();
            const text = `${cat} ${pkg}`;

            if (text.includes('kid') || text.includes('cozy') || text.includes('child')) return 'kids-cozy';
            if (text.includes('proposal') || text.includes('anniversary') || text.includes('romantic') || text.includes('couple')) return 'proposal-anniversary';
            if (text.includes('dj') || text.includes('acoustic') || text.includes('sound') || text.includes('music')) return 'dj-acoustic';
            if (text.includes('wedding') || text.includes('sangeet') || text.includes('haldi') || text.includes('mehendi') || text.includes('marriage')) return 'weddings-sangeet';
            if (text.includes('house') || text.includes('home') || text.includes('party')) return 'house-party';
            if (text.includes('baby') || text.includes('corporate') || text.includes('office')) return 'baby-corporate';
            return 'birthdays';
        }

        function getBookingCategoryName(slug) {
            const cat = OFFICIAL_CATEGORIES.find(c => c.slug === slug);
            return cat ? cat.name : 'Birthdays';
        }

        /**
         * Status badge CSS styling
         */
        function getStatusClass(status) {
            switch (status) {
                case 'Pending':   return 'bg-amber-50 text-amber-700 border border-amber-200';
                case 'Contacted': return 'bg-blue-50 text-blue-700 border border-blue-200';
                case 'Confirmed': return 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                case 'Completed': return 'bg-purple-50 text-purple-700 border border-purple-200';
                case 'Cancelled': return 'bg-rose-50 text-rose-700 border border-rose-200';
                default:          return 'bg-gray-100 text-gray-700 border border-gray-200';
            }
        }

        function getStatusDotClass(status) {
            switch (status) {
                case 'Confirmed':
                case 'Approved':
                    return 'bg-emerald-500';
                case 'Pending':
                case 'Under Review':
                    return 'bg-amber-500';
                case 'Contacted':
                    return 'bg-blue-500';
                case 'Completed':
                    return 'bg-indigo-500';
                case 'Cancelled':
                case 'Rejected':
                    return 'bg-rose-500';
                default:
                    return 'bg-gray-400';
            }
        }

        function getAvatarColorClass(name) {
            const str = (name || '').toLowerCase();
            if (str.includes('artiom') || str.includes('adrian')) {
                return 'bg-[#f3e8ff] text-[#9333ea]';
            }
            if (str.includes('customer four') || str.includes('four') || str.includes('cf')) {
                return 'bg-[#dcfce7] text-[#16a34a]';
            }
            const colors = [
                'bg-[#f3e8ff] text-[#9333ea]', // pastel lilac purple (AA)
                'bg-[#dcfce7] text-[#16a34a]', // pastel mint green (CF)
                'bg-[#eff6ff] text-[#2563eb]', // pastel soft blue
                'bg-[#fff7ed] text-[#ea580c]', // pastel peach orange
                'bg-[#fdf2f8] text-[#db2777]'  // pastel pink
            ];
            let hash = 0;
            for (let i = 0; i < str.length; i++) {
                hash = str.charCodeAt(i) + ((hash << 5) - hash);
            }
            return colors[Math.abs(hash) % colors.length];
        }

        function getCategoryDisplayConfig(catSlug, b) {
            const pkg = (b.package || '').toLowerCase();
            const cat = (b.category || '').toLowerCase();
            const text = `${catSlug} ${cat} ${pkg}`;

            if (text.includes('kid') || text.includes('cozy') || text.includes('child') || text.includes('balloon') || text.includes('teddy')) {
                return {
                    icon: 'fa-solid fa-gift',
                    boxClass: 'bg-[#fff7ed] text-[#ea580c] border-[#ffedd5]',
                    badgeName: 'Kids Birthday',
                    badgeClass: 'bg-[#fff7ed] text-[#c2410c] border-[#ffedd5]'
                };
            }
            if (text.includes('dj') || text.includes('sound') || text.includes('music') || text.includes('party') || text.includes('house') || text.includes('led') || text.includes('spotlight') || text.includes('column') || text.includes('bass')) {
                return {
                    icon: 'fa-solid fa-wand-magic-sparkles',
                    boxClass: 'bg-[#faf5ff] text-[#9333ea] border-[#f3e8ff]',
                    badgeName: 'LED & Events',
                    badgeClass: 'bg-[#fffbeb] text-[#b45309] border-[#fef3c7]'
                };
            }
            if (text.includes('wedding') || text.includes('sangeet') || text.includes('stage') || text.includes('haldi') || text.includes('reception') || text.includes('royal')) {
                return {
                    icon: 'fa-solid fa-archway',
                    boxClass: 'bg-[#eff6ff] text-[#2563eb] border-[#dbeafe]',
                    badgeName: 'Weddings & Receptions',
                    badgeClass: 'bg-[#eff6ff] text-[#1d4ed8] border-[#dbeafe]'
                };
            }
            if (text.includes('proposal') || text.includes('anniversary') || text.includes('romantic')) {
                return {
                    icon: 'fa-solid fa-heart',
                    boxClass: 'bg-[#fff1f2] text-[#e11d48] border-[#ffe4e6]',
                    badgeName: 'Proposal & Anniversary',
                    badgeClass: 'bg-[#fff1f2] text-[#be123c] border-[#ffe4e6]'
                };
            }
            if (text.includes('baby') || text.includes('corporate') || text.includes('office')) {
                return {
                    icon: 'fa-solid fa-building',
                    boxClass: 'bg-[#eef2ff] text-[#4f46e5] border-[#e0e7ff]',
                    badgeName: 'Baby & Corporate',
                    badgeClass: 'bg-[#eef2ff] text-[#4338ca] border-[#e0e7ff]'
                };
            }
            return {
                icon: 'fa-solid fa-gift',
                boxClass: 'bg-[#fff7ed] text-[#ea580c] border-[#ffedd5]',
                badgeName: b.category || 'Birthdays',
                badgeClass: 'bg-[#fff7ed] text-[#c2410c] border-[#ffedd5]'
            };
        }

        function getStatusPillConfig(status) {
            switch (status) {
                case 'Confirmed':
                case 'Approved':
                    return {
                        btnClass: 'bg-[#ecfdf5] hover:bg-[#d1fae5] text-[#065f46] border border-[#a7f3d0]',
                        icon: 'fa-regular fa-circle-check text-[#059669]'
                    };
                case 'Contacted':
                    return {
                        btnClass: 'bg-[#eff6ff] hover:bg-[#dbeafe] text-[#1e40af] border border-[#bfdbfe]',
                        icon: 'fa-solid fa-phone text-[#2563eb]'
                    };
                case 'Completed':
                    return {
                        btnClass: 'bg-[#faf5ff] hover:bg-[#f3e8ff] text-[#6b21a8] border border-[#e9d5ff]',
                        icon: 'fa-regular fa-star text-[#9333ea]'
                    };
                case 'Cancelled':
                case 'Rejected':
                    return {
                        btnClass: 'bg-[#fff1f2] hover:bg-[#ffe4e6] text-[#9f1239] border border-[#fecdd3]',
                        icon: 'fa-regular fa-circle-xmark text-[#e11d48]'
                    };
                default: // Pending
                    return {
                        btnClass: 'bg-[#fffbeb] hover:bg-[#fef3c7] text-[#92400e] border border-[#fde68a]',
                        icon: 'fa-regular fa-clock text-[#d97706]'
                    };
            }
        }

        /**
         * Category & Filter Selection
         */
        function selectCategoryTab(slug) {
            currentBookingsCategory = slug;
            currentBookingsPage = 1;
            renderBookingsTables();
        }

        function filterBookingStatus(status) {
            currentBookingsStatus = status;
            currentBookingsPage = 1;
            renderBookingsTables();
        }

        function filterBookingDateUrgency(urgency) {
            if (currentBookingsDateUrgency === urgency) {
                currentBookingsDateUrgency = 'all';
            } else {
                currentBookingsDateUrgency = urgency;
            }
            currentBookingsPage = 1;
            updateQuickFilterChipsUI();
            renderBookingsTables();
        }

        function togglePendingCallbacksFilter() {
            if (currentBookingsStatus === 'Pending') {
                currentBookingsStatus = 'all';
            } else {
                currentBookingsStatus = 'Pending';
            }
            currentBookingsPage = 1;
            updateQuickFilterChipsUI();
            renderBookingsTables();
        }

        function updateQuickFilterChipsUI() {
            const u48Btn = document.getElementById('btn-urgency-48h');
            const wkndBtn = document.getElementById('btn-urgency-weekend');
            const cbBtn = document.getElementById('btn-urgency-callbacks');

            if (u48Btn) {
                if (currentBookingsDateUrgency === 'urgent_48h') {
                    u48Btn.className = 'inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl border-2 border-[#f59e0b] bg-[#fef3c7] text-[#92400e] text-xs font-bold transition-all shadow-xs ring-2 ring-[#f59e0b]/20 cursor-pointer active:scale-95';
                } else {
                    u48Btn.className = 'inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl border border-[#fde68a] bg-[#fffdf5] hover:bg-[#fef9c3] text-[#92400e] text-xs font-bold transition-all shadow-2xs hover:shadow-xs cursor-pointer active:scale-95';
                }
            }

            if (wkndBtn) {
                if (currentBookingsDateUrgency === 'weekend') {
                    wkndBtn.className = 'inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl border-2 border-[#3b82f6] bg-[#dbeafe] text-[#1d4ed8] text-xs font-bold transition-all shadow-xs ring-2 ring-[#3b82f6]/20 cursor-pointer active:scale-95';
                } else {
                    wkndBtn.className = 'inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl border border-[#bfdbfe] bg-[#eff6ff] hover:bg-[#dbeafe] text-[#1d4ed8] text-xs font-bold transition-all shadow-2xs hover:shadow-xs cursor-pointer active:scale-95';
                }
            }

            if (cbBtn) {
                if (currentBookingsStatus === 'Pending') {
                    cbBtn.className = 'inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl border-2 border-[#f43f5e] bg-[#ffe4e6] text-[#9f1239] text-xs font-bold transition-all shadow-xs ring-2 ring-[#f43f5e]/20 cursor-pointer active:scale-95';
                } else {
                    cbBtn.className = 'inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl border border-[#fecdd3] bg-[#fff1f2] hover:bg-[#ffe4e6] text-[#9f1239] text-xs font-bold transition-all shadow-2xs hover:shadow-xs cursor-pointer active:scale-95';
                }
            }
        }

        // Custom Floating Status Popover Engine
        let activeStatusDropdownBookingId = null;

        function openStatusDropdown(event, bookingId, currentStatus) {
            event.stopPropagation();
            activeStatusDropdownBookingId = bookingId;

            const popover = document.getElementById('status-change-popover');
            if (!popover) return;

            const labelEl = document.getElementById('popover-booking-label');
            if (labelEl) labelEl.textContent = '#' + bookingId;

            // Mark checkmark on active status
            document.querySelectorAll('.status-check-icon').forEach(icon => {
                if (icon.getAttribute('data-status') === currentStatus) {
                    icon.classList.remove('hidden');
                } else {
                    icon.classList.add('hidden');
                }
            });

            // Position popover relative to clicked pill
            const btn = event.currentTarget;
            const rect = btn.getBoundingClientRect();
            
            popover.classList.remove('hidden');
            
            const popoverHeight = 280;
            const popoverWidth = 224;
            let top = rect.bottom + 6;
            let left = rect.left;

            if (top + popoverHeight > window.innerHeight) {
                top = Math.max(10, rect.top - popoverHeight - 6);
            }
            if (left + popoverWidth > window.innerWidth) {
                left = Math.max(10, window.innerWidth - popoverWidth - 14);
            }

            popover.style.top = `${top}px`;
            popover.style.left = `${left}px`;
        }

        function closeStatusDropdown() {
            const popover = document.getElementById('status-change-popover');
            if (popover) {
                popover.classList.add('hidden');
            }
            activeStatusDropdownBookingId = null;
        }

        function selectStatus(newStatus) {
            if (!activeStatusDropdownBookingId) return;
            const bId = activeStatusDropdownBookingId;
            closeStatusDropdown();
            updateBookingStatus(bId, newStatus);
        }

        // Quick row action functions
        function quickApproveBooking(bookingId) {
            updateBookingStatus(bookingId, 'Confirmed');
        }

        function quickCancelBooking(bookingId) {
            if (confirm(`Are you sure you want to cancel booking #${bookingId}?`)) {
                updateBookingStatus(bookingId, 'Cancelled');
            }
        }

        function quickCompleteBooking(bookingId) {
            if (confirm(`Mark celebration #${bookingId} as Completed & fully fulfilled?`)) {
                updateBookingStatus(bookingId, 'Completed');
            }
        }

        function quickReopenBooking(bookingId) {
            updateBookingStatus(bookingId, 'Pending');
        }

        // Bulk Selection Helpers
        function toggleSelectAllBookings(isChecked) {
            if (isChecked) {
                lastRenderedPageData.forEach(b => selectedBookingIds.add(b.id));
            } else {
                lastRenderedPageData.forEach(b => selectedBookingIds.delete(b.id));
            }
            updateBulkActionBar();
            renderSelectionCheckboxes();
        }

        function handleRowSelectChange(bookingId, isChecked) {
            if (isChecked) {
                selectedBookingIds.add(bookingId);
            } else {
                selectedBookingIds.delete(bookingId);
            }
            updateBulkActionBar();
            updateSelectAllCheckboxState();
        }

        function updateSelectAllCheckboxState() {
            const selectAllEl = document.getElementById('b-select-all');
            if (!selectAllEl || lastRenderedPageData.length === 0) return;
            const allSelectedOnPage = lastRenderedPageData.every(b => selectedBookingIds.has(b.id));
            selectAllEl.checked = allSelectedOnPage;
        }

        function clearSelectedBookings() {
            selectedBookingIds.clear();
            const selectAllEl = document.getElementById('b-select-all');
            if (selectAllEl) selectAllEl.checked = false;
            updateBulkActionBar();
            renderSelectionCheckboxes();
        }

        function renderSelectionCheckboxes() {
            document.querySelectorAll('.b-row-checkbox').forEach(cb => {
                cb.checked = selectedBookingIds.has(cb.value);
            });
        }

        function updateBulkActionBar() {
            const bar = document.getElementById('b-bulk-actions-bar');
            const countEl = document.getElementById('b-bulk-selected-count');
            const inlineBar = document.getElementById('b-inline-selection-bar');
            const inlineCountEl = document.getElementById('b-inline-selected-count');

            const count = selectedBookingIds.size;
            if (countEl) countEl.textContent = count;
            if (inlineCountEl) inlineCountEl.textContent = count;

            if (count > 0) {
                if (bar) { bar.classList.remove('hidden'); bar.classList.add('flex'); }
                if (inlineBar) { inlineBar.classList.remove('hidden'); inlineBar.classList.add('flex'); }
            } else {
                if (bar) { bar.classList.add('hidden'); bar.classList.remove('flex'); }
                if (inlineBar) { inlineBar.classList.add('hidden'); inlineBar.classList.remove('flex'); }
            }
        }

        function bulkUpdateSelectedStatus(newStatus) {
            if (selectedBookingIds.size === 0) return;
            const count = selectedBookingIds.size;
            if (!confirm(`Are you sure you want to mark ${count} selected bookings as "${newStatus}"?`)) return;

            const ids = Array.from(selectedBookingIds);
            ids.forEach(id => {
                updateBookingStatus(id, newStatus);
            });
            clearSelectedBookings();
            if (window.showToast) {
                window.showToast(`${count} bookings updated to ${newStatus}`, 'success');
            }
        }

        function bulkDeleteSelectedBookings() {
            if (selectedBookingIds.size === 0) return;
            const count = selectedBookingIds.size;
            const ids = Array.from(selectedBookingIds);

            if (!confirm(`Are you sure you want to permanently delete ${count} selected booking(s)?\n\nBooking IDs: ${ids.join(', ')}\n\nThis action cannot be undone.`)) {
                return;
            }

            fetch("{{ route('admin.bookings.bulk-delete') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const idSet = new Set(ids);
                    bookingsDB = bookingsDB.filter(b => !idSet.has(b.id));

                    clearSelectedBookings();
                    renderBookingsTables();
                    updateOverviewMetrics();

                    if (window.showToast) {
                        window.showToast(`${count} booking(s) permanently deleted.`, 'success');
                    } else {
                        alert(`${count} booking(s) deleted successfully.`);
                    }
                } else {
                    alert(data.message || 'Error deleting selected bookings.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Network error while deleting selected bookings.');
            });
        }

        function deleteCurrentDrawerBooking() {
            if (!activeDrawerBookingId) return;
            const id = activeDrawerBookingId;

            if (!confirm(`Permanently delete booking #${id}?\n\nThis action cannot be undone.`)) {
                return;
            }

            fetch("{{ route('admin.bookings.bulk-delete') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ ids: [id] })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    bookingsDB = bookingsDB.filter(b => b.id !== id);
                    selectedBookingIds.delete(id);

                    closeBookingDrawer();
                    clearSelectedBookings();
                    renderBookingsTables();
                    updateOverviewMetrics();

                    if (window.showToast) {
                        window.showToast(`Booking #${id} deleted successfully.`, 'success');
                    } else {
                        alert(`Booking #${id} deleted successfully.`);
                    }
                } else {
                    alert(data.message || 'Error deleting booking.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Network error while deleting booking.');
            });
        }

        function deleteActiveDropdownBooking() {
            if (!activeStatusDropdownBookingId) return;
            const id = activeStatusDropdownBookingId;
            closeStatusDropdown();

            if (!confirm(`Permanently delete booking #${id}?\n\nThis action cannot be undone.`)) {
                return;
            }

            fetch("{{ route('admin.bookings.bulk-delete') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ ids: [id] })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    bookingsDB = bookingsDB.filter(b => b.id !== id);
                    selectedBookingIds.delete(id);

                    clearSelectedBookings();
                    renderBookingsTables();
                    updateOverviewMetrics();

                    if (window.showToast) {
                        window.showToast(`Booking #${id} deleted successfully.`, 'success');
                    } else {
                        alert(`Booking #${id} deleted successfully.`);
                    }
                } else {
                    alert(data.message || 'Error deleting booking.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Network error while deleting booking.');
            });
        }

        function handleBookingSearch(val) {
            currentBookingsSearch = (val || '').trim();
            currentBookingsPage = 1;
            const clearBtn = document.getElementById('b-search-clear-btn');
            if (clearBtn) {
                if (currentBookingsSearch) clearBtn.classList.remove('hidden');
                else clearBtn.classList.add('hidden');
            }
            renderBookingsTables();
        }

        function clearBookingSearch() {
            const searchEl = document.getElementById('b-search-input');
            if (searchEl) searchEl.value = '';
            handleBookingSearch('');
        }

        function handleBookingSortChange(sortVal) {
            currentBookingsSort = sortVal;
            currentBookingsPage = 1;
            renderBookingsTables();
        }

        function resetAllBookingFilters() {
            currentBookingsCategory = 'all';
            currentBookingsStatus = 'all';
            currentBookingsSearch = '';
            currentBookingsSort = 'newest';
            currentBookingsDateUrgency = 'all';
            currentBookingsPage = 1;
            
            const searchEl = document.getElementById('b-search-input');
            if (searchEl) searchEl.value = '';
            const clearBtn = document.getElementById('b-search-clear-btn');
            if (clearBtn) clearBtn.classList.add('hidden');

            const sortEl = document.getElementById('b-sort-select');
            if (sortEl) sortEl.value = 'newest';

            updateQuickFilterChipsUI();
            renderBookingsTables();
            if (window.showToast) {
                window.showToast('All filters have been reset.', 'info', 2500);
            }
        }

        function handleRefreshBookings() {
            const btn = document.getElementById('btn-refresh-bookings');
            if (btn) {
                const icon = btn.querySelector('i');
                if (icon) icon.classList.add('animate-spin');
                setTimeout(() => {
                    renderBookingsTables();
                    if (icon) icon.classList.remove('animate-spin');
                    if (window.showToast) window.showToast('Bookings view refreshed.', 'success', 2000);
                }, 300);
            } else {
                renderBookingsTables();
            }
        }

        /**
         * Copy Booking ID to Clipboard with tooltip
         */
        function copyBookingId(id, event) {
            if (event) event.stopPropagation();
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(id).then(() => {
                    if (window.showToast) window.showToast(`Booking ID ${id} copied!`, 'info', 2000);
                });
            }
        }

        /**
         * Relative date calculator for event dates
         */
        function formatRelativeEventDate(dateStr) {
            if (!dateStr) return { label: 'TBD', badgeClass: 'bg-gray-100 text-gray-600 border-gray-200' };
            try {
                const eventDate = new Date(dateStr);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                eventDate.setHours(0, 0, 0, 0);
                
                const diffTime = eventDate - today;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if (diffDays === 0) return { label: 'Today', badgeClass: 'bg-emerald-50 text-emerald-800 border-emerald-300 font-extrabold' };
                if (diffDays === 1) return { label: 'Tomorrow', badgeClass: 'bg-amber-50 text-amber-800 border-amber-300 font-bold' };
                if (diffDays > 1 && diffDays <= 7) return { label: `In ${diffDays} days`, badgeClass: 'bg-blue-50 text-blue-800 border-blue-200' };
                if (diffDays > 7) return { label: `In ${diffDays} days`, badgeClass: 'bg-gray-100 text-gray-700 border-gray-200' };
                return { label: 'Event Passed', badgeClass: 'bg-gray-100 text-gray-500 border-gray-200' };
            } catch (e) {
                return { label: dateStr, badgeClass: 'bg-gray-100 text-gray-600 border-gray-200' };
            }
        }

        /**
         * Client initials helper
         */
        function getInitials(name) {
            if (!name) return 'BK';
            const parts = name.trim().split(/\s+/);
            if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        }

        /**
         * Update Booking Status via AJAX POST API
         */
        function updateBookingStatus(bookingId, newStatus) {
            const booking = bookingsDB.find(b => b.id === bookingId);
            if (!booking) return;

            const oldStatus = booking.status;
            booking.status = newStatus;
            
            // Re-render UI immediately
            renderBookingsTables();
            updateOverviewMetrics();

            // Refresh drawer if open for this booking
            if (activeDrawerBookingId === bookingId) {
                openBookingDrawer(bookingId);
            }
            
            // Persist status change via backend POST API
            fetch("{{ route('admin.bookings.update-status') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ id: bookingId, status: newStatus })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (window.showToast) {
                        window.showToast(`Booking ${bookingId} updated to ${newStatus}`, 'success');
                    }
                    if (data.notify_url && (newStatus === 'Confirmed' || newStatus === 'Cancelled')) {
                        if (confirm(`Booking ${bookingId} marked ${newStatus}. Send WhatsApp notification to customer?`)) {
                            window.open(data.notify_url, '_blank');
                        }
                    }
                } else {
                    booking.status = oldStatus;
                    renderBookingsTables();
                    if (window.showToast) {
                        window.showToast(data.message || 'Error updating status', 'error');
                    } else {
                        alert(data.message || 'Error saving status change to backend');
                    }
                }
            })
            .catch(err => {
                console.error(err);
                booking.status = oldStatus;
                renderBookingsTables();
                if (window.showToast) window.showToast('Network error updating status', 'error');
            });
        }

        /**
         * Update Overview Metrics Badges
         */
        function updateOverviewMetrics() {
            const pendingCount = bookingsDB.filter(b => b.status === 'Pending').length;
            const confirmedCount = bookingsDB.filter(b => b.status === 'Confirmed').length;

            const pendingEl = document.getElementById('metric-pending-count');
            const confirmedEl = document.getElementById('metric-confirmed-count');
            const badgeEl = document.getElementById('sidebar-pending-badge');

            if (pendingEl) pendingEl.textContent = pendingCount;
            if (confirmedEl) confirmedEl.textContent = confirmedCount;
            if (badgeEl) badgeEl.textContent = pendingCount;
        }

        /**
         * Export Helper & CSV Generation
         */
        function exportDatasetAsCsv(exportData, filename) {
            if (!exportData || exportData.length === 0) {
                if (window.showToast) window.showToast('No bookings to export.', 'error');
                return;
            }

            const headers = [
                "Booking ID", "Customer Name", "Mobile", "WhatsApp", "Email",
                "Package", "Category", "Tier", "Event Date", "Event Time",
                "Guest Count", "Address", "Area", "City", "Total Amount (INR)",
                "Status", "Placed At"
            ];

            const rows = exportData.map(b => [
                `"${(b.id || '').replace(/"/g, '""')}"`,
                `"${(b.name || '').replace(/"/g, '""')}"`,
                `"${(b.mobile || '').replace(/"/g, '""')}"`,
                `"${(b.whatsapp || b.mobile || '').replace(/"/g, '""')}"`,
                `"${(b.email || '').replace(/"/g, '""')}"`,
                `"${(b.package || '').replace(/"/g, '""')}"`,
                `"${getBookingCategoryName(getBookingCategorySlug(b))}"`,
                `"${(b.tier || 'Standard').replace(/"/g, '""')}"`,
                `"${(b.date || '').replace(/"/g, '""')}"`,
                `"${(b.time || '').replace(/"/g, '""')}"`,
                b.guest_count || 0,
                `"${(b.address || '').replace(/"/g, '""')}"`,
                `"${(b.area || '').replace(/"/g, '""')}"`,
                `"${(b.city || 'Indore').replace(/"/g, '""')}"`,
                parseInt(b.total) || 0,
                `"${b.status || 'Pending'}"`,
                `"${(b.created_at || '').replace(/"/g, '""')}"`
            ]);

            const csvContent = "data:text/csv;charset=utf-8," + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", filename || `artizen_bookings_${new Date().toISOString().slice(0, 10)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            if (window.showToast) {
                window.showToast(`Exported ${exportData.length} bookings to CSV successfully!`, 'success');
            }
        }

        function exportBookingsCsv() {
            let exportData = bookingsDB;
            if (currentBookingsCategory !== 'all') {
                exportData = exportData.filter(b => getBookingCategorySlug(b) === currentBookingsCategory);
            }
            if (currentBookingsStatus !== 'all') {
                exportData = exportData.filter(b => b.status === currentBookingsStatus);
            }
            if (currentBookingsSearch !== '') {
                const lc = currentBookingsSearch.toLowerCase();
                exportData = exportData.filter(b =>
                    (b.name && b.name.toLowerCase().includes(lc)) ||
                    (b.id && b.id.toLowerCase().includes(lc)) ||
                    (b.mobile && b.mobile.includes(lc)) ||
                    (b.email && b.email.toLowerCase().includes(lc)) ||
                    (b.package && b.package.toLowerCase().includes(lc)) ||
                    (b.area && b.area.toLowerCase().includes(lc)) ||
                    (b.city && b.city.toLowerCase().includes(lc))
                );
            }
            exportDatasetAsCsv(exportData, `artizen_bookings_${new Date().toISOString().slice(0, 10)}.csv`);
        }

        function exportSelectedCsv() {
            if (selectedBookingIds.size === 0) return;
            const selectedData = bookingsDB.filter(b => selectedBookingIds.has(b.id));
            exportDatasetAsCsv(selectedData, `artizen_selected_bookings_${selectedData.length}.csv`);
        }

        /**
         * Core Table and Cards Rendering Engine
         */
        function renderBookingsTables() {
            // A. Top Metric Summary Cards Calculation
            const totalCount = bookingsDB.length;
            const pendingCount = bookingsDB.filter(b => b.status === 'Pending').length;
            const confirmedCount = bookingsDB.filter(b => b.status === 'Confirmed').length;
            const pipelineVal = bookingsDB
                .filter(b => b.status === 'Pending' || b.status === 'Confirmed')
                .reduce((sum, b) => sum + (parseInt(b.total) || 0), 0);

            const metricTotalEl = document.getElementById('b-metric-total');
            const metricPendingEl = document.getElementById('b-metric-pending');
            const metricConfirmedEl = document.getElementById('b-metric-confirmed');
            const metricPipelineEl = document.getElementById('b-metric-pipeline');
            const headerTotalBadge = document.getElementById('header-total-badge');

            if (metricTotalEl) metricTotalEl.textContent = totalCount;
            if (metricPendingEl) metricPendingEl.textContent = pendingCount;
            if (metricConfirmedEl) metricConfirmedEl.textContent = confirmedCount;
            if (metricPipelineEl) metricPipelineEl.textContent = `₹${pipelineVal.toLocaleString('en-IN')}`;
            if (headerTotalBadge) headerTotalBadge.textContent = `${totalCount} Total Bookings`;

            const distLabelEl = document.getElementById('b-distribution-total-label');
            if (distLabelEl) distLabelEl.textContent = `${totalCount} Total Bookings`;

            // B. Primary Category Navigation Tabs Render
            const tabsContainer = document.getElementById('b-category-tabs-container');
            if (tabsContainer) {
                tabsContainer.innerHTML = '';
                OFFICIAL_CATEGORIES.forEach(cat => {
                    const count = cat.slug === 'all'
                        ? bookingsDB.length
                        : bookingsDB.filter(b => getBookingCategorySlug(b) === cat.slug).length;

                    const isActive = (currentBookingsCategory === cat.slug);
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.onclick = () => selectCategoryTab(cat.slug);
                    btn.className = isActive
                        ? "px-3.5 py-1.5 rounded-xl text-xs font-bold shrink-0 transition-all flex items-center gap-2 bg-gray-900 text-white shadow-xs border border-gray-900 cursor-pointer"
                        : "px-3.5 py-1.5 rounded-xl text-xs font-semibold shrink-0 transition-all flex items-center gap-2 bg-white text-gray-700 hover:text-gray-900 border border-gray-200/90 hover:bg-gray-100/70 cursor-pointer";

                    btn.innerHTML = `
                        <i class="${cat.icon} text-xs ${isActive ? 'text-amber-400' : 'text-gray-400'}"></i>
                        <span>${cat.name}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${isActive ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600'}">${count}</span>
                    `;
                    tabsContainer.appendChild(btn);
                });
            }

            // C. Filter by Selected Category
            const filteredByCat = (currentBookingsCategory === 'all')
                ? bookingsDB
                : bookingsDB.filter(b => getBookingCategorySlug(b) === currentBookingsCategory);

            // D. Secondary Status Filter Pills Render (Scoped to category)
            const statusPillsContainer = document.getElementById('b-status-pills-container');
            if (statusPillsContainer) {
                statusPillsContainer.innerHTML = '';
                const statuses = [
                    { key: 'all',       label: 'All' },
                    { key: 'Pending',   label: 'Pending' },
                    { key: 'Contacted', label: 'Contacted' },
                    { key: 'Confirmed', label: 'Confirmed' },
                    { key: 'Completed', label: 'Completed' },
                    { key: 'Cancelled', label: 'Cancelled' }
                ];

                statuses.forEach(st => {
                    const count = (st.key === 'all')
                        ? filteredByCat.length
                        : filteredByCat.filter(b => b.status === st.key).length;

                    const isActive = (currentBookingsStatus === st.key);
                    const pill = document.createElement('button');
                    pill.type = 'button';
                    pill.onclick = () => filterBookingStatus(st.key);

                    pill.className = isActive
                        ? "px-3 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap bg-gray-900 text-white cursor-pointer shrink-0"
                        : "px-3 py-1.5 rounded-lg text-xs font-medium transition-all whitespace-nowrap bg-white text-gray-600 hover:text-gray-900 hover:bg-gray-50 border border-gray-200 cursor-pointer shrink-0";

                    pill.innerHTML = `<span>${st.label} (${count})</span>`;
                    statusPillsContainer.appendChild(pill);
                });
            }

            // E. Active Filters Feedback Bar
            const filtersBar = document.getElementById('b-active-filters-bar');
            const filtersText = document.getElementById('b-active-filters-text');
            const isFilterActive = (currentBookingsCategory !== 'all' || currentBookingsStatus !== 'all' || currentBookingsSearch !== '' || currentBookingsDateUrgency !== 'all');

            if (filtersBar) {
                if (isFilterActive) {
                    filtersBar.classList.remove('hidden');
                    filtersBar.classList.add('flex');
                    let activeParts = [];
                    if (currentBookingsCategory !== 'all') activeParts.push(`Category: ${getBookingCategoryName(currentBookingsCategory)}`);
                    if (currentBookingsStatus !== 'all') activeParts.push(`Status: ${currentBookingsStatus}`);
                    if (currentBookingsDateUrgency === 'urgent_48h') activeParts.push('Time: Next 48 Hours');
                    if (currentBookingsDateUrgency === 'weekend') activeParts.push('Time: This Weekend');
                    if (currentBookingsSearch !== '') activeParts.push(`Search: "${currentBookingsSearch}"`);
                    if (filtersText) filtersText.textContent = activeParts.join(' • ');
                } else {
                    filtersBar.classList.add('hidden');
                    filtersBar.classList.remove('flex');
                }
            }

            // F. Apply Status, Urgency & Search Filtering
            let resultData = filteredByCat;
            if (currentBookingsStatus !== 'all') {
                resultData = resultData.filter(b => b.status === currentBookingsStatus);
            }
            if (currentBookingsDateUrgency === 'urgent_48h') {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                resultData = resultData.filter(b => {
                    if (!b.date) return false;
                    const d = new Date(b.date);
                    if (isNaN(d.getTime())) return false;
                    d.setHours(0, 0, 0, 0);
                    const diffDays = Math.ceil((d - today) / (1000 * 60 * 60 * 24));
                    return diffDays >= 0 && diffDays <= 2;
                });
            } else if (currentBookingsDateUrgency === 'weekend') {
                resultData = resultData.filter(b => {
                    if (!b.date) return false;
                    const d = new Date(b.date);
                    if (isNaN(d.getTime())) return false;
                    const day = d.getDay(); // 0: Sunday, 6: Saturday
                    return day === 0 || day === 6;
                });
            }
            if (currentBookingsSearch !== '') {
                const lc = currentBookingsSearch.toLowerCase();
                resultData = resultData.filter(b =>
                    (b.name && b.name.toLowerCase().includes(lc)) ||
                    (b.id && b.id.toLowerCase().includes(lc)) ||
                    (b.mobile && b.mobile.includes(lc)) ||
                    (b.email && b.email.toLowerCase().includes(lc)) ||
                    (b.package && b.package.toLowerCase().includes(lc)) ||
                    (b.area && b.area.toLowerCase().includes(lc)) ||
                    (b.landmark && b.landmark.toLowerCase().includes(lc)) ||
                    (b.city && b.city.toLowerCase().includes(lc))
                );
            }

            // G. Apply Sorting
            resultData.sort((a, b) => {
                if (currentBookingsSort === 'oldest') {
                    return (a.id || '').localeCompare(b.id || '');
                } else if (currentBookingsSort === 'event_date') {
                    return (a.date || '').localeCompare(b.date || '');
                } else if (currentBookingsSort === 'highest_val') {
                    return (parseInt(b.total) || 0) - (parseInt(a.total) || 0);
                } else if (currentBookingsSort === 'lowest_val') {
                    return (parseInt(a.total) || 0) - (parseInt(b.total) || 0);
                } else {
                    // Default: newest (preserves database storage order where latest records are first)
                    return 0;
                }
            });

            // H. Pagination Calculations
            const totalItems = resultData.length;
            const totalPages = Math.ceil(totalItems / BOOKINGS_PAGE_SIZE) || 1;
            if (currentBookingsPage > totalPages) currentBookingsPage = totalPages;

            const startIndex = (currentBookingsPage - 1) * BOOKINGS_PAGE_SIZE;
            const pageData = resultData.slice(startIndex, startIndex + BOOKINGS_PAGE_SIZE);
            lastRenderedPageData = pageData;

            // I. Handle Empty States vs Table Render
            const mainTbody = document.getElementById('bookings-main-tbody');
            const mobileContainer = document.getElementById('bookings-mobile-container');
            const emptyStateEl = document.getElementById('b-empty-state');

            if (totalItems === 0) {
                if (mainTbody) mainTbody.innerHTML = '';
                if (mobileContainer) mobileContainer.innerHTML = '';
                if (emptyStateEl) {
                    emptyStateEl.classList.remove('hidden');
                    emptyStateEl.classList.add('flex');
                }
            } else {
                if (emptyStateEl) {
                    emptyStateEl.classList.add('hidden');
                    emptyStateEl.classList.remove('flex');
                }

                // Render Desktop Table Rows
                if (mainTbody) {
                    mainTbody.innerHTML = '';
                    pageData.forEach(b => {
                        const catSlug = getBookingCategorySlug(b);
                        const catConfig = getCategoryDisplayConfig(catSlug, b);
                        const initials = getInitials(b.name);
                        const avatarColorClass = getAvatarColorClass(b.name);
                        const statusConfig = getStatusPillConfig(b.status);
                        
                        const totalFormatted = `₹${(parseInt(b.total) || 0).toLocaleString('en-IN')}`;
                        const advanceAmt = b.advance ? `Advance ₹${parseInt(b.advance).toLocaleString('en-IN')}` : 'Advance Pending';
                        
                        const phoneClean = (b.mobile || b.whatsapp || '').replace(/[^0-9]/g, '');
                        const displayId = (b.id && String(b.id).startsWith('#')) ? b.id : `#${b.id}`;

                        // Date and Time parsing
                        const dateFormatted = b.date || 'To be decided';
                        let timeLine1 = '';
                        let timeLine2 = '';
                        if (b.time) {
                            const rawTime = String(b.time).trim();
                            if (rawTime.includes('(') && rawTime.includes(')')) {
                                const match = rawTime.match(/^(.*?)\s*(\(.*?\))$/);
                                if (match) {
                                    timeLine1 = match[1].trim();
                                    timeLine2 = match[2].trim();
                                } else {
                                    timeLine1 = rawTime;
                                }
                            } else if (rawTime.toLowerCase().includes('tbd') || rawTime.toLowerCase().includes('decide')) {
                                timeLine1 = 'To be decided';
                            } else {
                                timeLine1 = rawTime;
                            }
                        } else {
                            timeLine1 = 'To be decided';
                        }

                        // Venue 3-line stack
                        const vLandmark = (b.landmark || '').trim();
                        const vArea = (b.area || '').trim();
                        const vCity = (b.city || 'Indore').trim();
                        let venueLine1 = vLandmark || vArea || 'Indore';
                        let venueLine2 = (vLandmark && vArea) ? vArea : (b.address ? b.address.split(',')[0].trim() : '');
                        let venueLine3 = vCity;

                        const isChecked = selectedBookingIds.has(b.id);

                        const row = document.createElement('tr');
                        row.className = "hover:bg-gray-50/70 border-b border-gray-100 text-gray-800 transition-colors";
                        row.innerHTML = `
                            <!-- Checkbox -->
                            <td class="py-4 px-3 text-center align-middle">
                                <input type="checkbox" 
                                       class="b-row-checkbox w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-0 cursor-pointer" 
                                       value="${b.id}" 
                                       onchange="handleRowSelectChange('${b.id}', this.checked)" 
                                       ${isChecked ? 'checked' : ''}>
                            </td>

                            <!-- Enquiry ID -->
                            <td class="py-4 px-3 align-middle">
                                <button type="button" onclick="openBookingDrawer('${b.id}')" class="font-bold text-gray-900 hover:text-amber-600 block text-left text-sm cursor-pointer transition-colors leading-tight">${displayId}</button>
                                <span class="text-xs text-gray-400 block mt-1 leading-tight">${b.created_at || b.date || ''}</span>
                            </td>

                            <!-- Customer -->
                            <td class="py-4 px-3 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full ${avatarColorClass} flex items-center justify-center font-bold text-xs shrink-0 select-none">
                                        ${initials}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-gray-900 text-sm truncate max-w-[150px] leading-tight" title="${b.name || 'Customer'}">${b.name || 'Customer'}</div>
                                        <a href="tel:+91${phoneClean}" class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-800 font-medium mt-1 transition-colors" title="Direct Phone Call">
                                            <i class="fa-solid fa-phone text-[10px]"></i>
                                            <span>+91 ${phoneClean || '9876543209'}</span>
                                        </a>
                                    </div>
                                </div>
                            </td>

                            <!-- Package & Category -->
                            <td class="py-4 px-3 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl ${catConfig.boxClass} flex items-center justify-center text-sm shrink-0 border">
                                        <i class="${catConfig.icon}"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-gray-900 text-sm truncate max-w-[210px] leading-tight" title="${b.package || 'Celebration Setup'}">${b.package || 'Celebration Setup'}</div>
                                        <div class="mt-1">
                                            <button type="button" 
                                                    onclick="event.stopPropagation(); selectCategoryTab('${catSlug}')" 
                                                    title="Filter by ${catConfig.badgeName}" 
                                                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium border ${catConfig.badgeClass} hover:opacity-90 transition-opacity cursor-pointer">
                                                <span>${catConfig.badgeName}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Event Date -->
                            <td class="py-4 px-3 align-middle">
                                <div>
                                    <div class="flex items-center text-xs font-bold text-gray-900 leading-tight">
                                        <i class="fa-regular fa-calendar text-gray-600 text-sm mr-2 shrink-0"></i>
                                        <span>${dateFormatted}</span>
                                    </div>
                                    ${timeLine1 ? `<div class="text-[11px] text-gray-500 mt-1 leading-tight">${timeLine1}</div>` : ''}
                                    ${timeLine2 ? `<div class="text-[11px] text-gray-500 mt-0.5 leading-tight">${timeLine2}</div>` : ''}
                                </div>
                            </td>

                            <!-- Venue -->
                            <td class="py-4 px-3 align-middle">
                                <div class="flex items-start gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <div class="min-w-0 leading-tight">
                                        <div class="text-xs font-bold text-gray-800 truncate max-w-[120px]">${venueLine1}</div>
                                        ${venueLine2 ? `<div class="text-[11px] text-gray-600 truncate max-w-[120px] mt-0.5">${venueLine2}</div>` : ''}
                                        <div class="text-[11px] text-gray-600 truncate max-w-[120px] mt-0.5">${venueLine3}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Amount -->
                            <td class="py-4 px-3 align-middle">
                                <div>
                                    <div class="font-bold text-gray-900 text-sm leading-tight">${totalFormatted}</div>
                                    <div class="text-[11px] text-gray-400 font-medium mt-1 leading-tight">${advanceAmt}</div>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-3 align-middle">
                                <button type="button" 
                                        onclick="openStatusDropdown(event, '${b.id}', '${b.status}')" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold ${statusConfig.btnClass} transition-all cursor-pointer shadow-2xs hover:shadow-xs">
                                    <i class="${statusConfig.icon} text-xs"></i>
                                    <span>${b.status || 'Pending'}</span>
                                    <i class="fa-solid fa-chevron-down text-[9px] opacity-70 ml-0.5"></i>
                                </button>
                            </td>

                            <!-- Quick Actions (Left Aligned to match reference image) -->
                            <td class="py-4 px-4 align-middle whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    ${(b.status === 'Pending' || b.status === 'Contacted') ? `
                                        <button type="button" 
                                                onclick="quickApproveBooking('${b.id}')" 
                                                title="Approve Booking" 
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-[#059669] hover:bg-[#047857] text-white text-xs font-semibold shadow-2xs active:scale-95 transition-all cursor-pointer">
                                            <i class="fa-solid fa-check text-xs"></i>
                                            <span>Approve</span>
                                        </button>
                                        <button type="button" 
                                                onclick="quickCancelBooking('${b.id}')" 
                                                title="Cancel Booking" 
                                                class="inline-flex items-center justify-center px-3.5 py-1.5 rounded-lg bg-[#fff1f2] hover:bg-[#ffe4e6] text-[#e11d48] border border-[#ffe4e6] text-xs font-semibold active:scale-95 transition-all cursor-pointer">
                                            <span>Cancel</span>
                                        </button>
                                    ` : (b.status === 'Confirmed') ? `
                                        <button type="button" 
                                                onclick="quickCompleteBooking('${b.id}')" 
                                                title="Mark Event Setup Completed" 
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-2xs active:scale-95 transition-all cursor-pointer">
                                            <i class="fa-solid fa-flag-checkered text-xs"></i>
                                            <span>Complete</span>
                                        </button>
                                        <button type="button" 
                                                onclick="quickCancelBooking('${b.id}')" 
                                                title="Cancel Booking" 
                                                class="inline-flex items-center justify-center px-3.5 py-1.5 rounded-lg bg-[#fff1f2] hover:bg-[#ffe4e6] text-[#e11d48] border border-[#ffe4e6] text-xs font-semibold active:scale-95 transition-all cursor-pointer">
                                            <span>Cancel</span>
                                        </button>
                                    ` : (b.status === 'Completed') ? `
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold">
                                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Done
                                        </span>
                                    ` : `
                                        <button type="button" 
                                                onclick="quickReopenBooking('${b.id}')" 
                                                title="Re-open Booking" 
                                                class="px-3.5 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition-all cursor-pointer">
                                            Reopen
                                        </button>
                                    `}

                                    <!-- Phone Call Square Button -->
                                    <a href="tel:+91${phoneClean}" 
                                       title="Direct Phone Call" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#eff6ff] hover:bg-[#dbeafe] text-[#2563eb] border border-[#dbeafe] active:scale-95 transition-all cursor-pointer">
                                        <i class="fa-solid fa-phone text-xs"></i>
                                    </a>

                                    <!-- WhatsApp Square Button -->
                                    <a href="https://wa.me/91${phoneClean}?text=${encodeURIComponent('Hi ' + (b.name || 'there') + '! Artizen team here regarding your celebration booking #' + b.id + ' (' + (b.package || 'Setup') + ') scheduled for ' + (b.date || '') + '. Let us coordinate your staging details.')}" 
                                       target="_blank" 
                                       title="Direct WhatsApp with Pre-filled Brief" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#ecfdf5] hover:bg-[#d1fae5] text-[#059669] border border-[#bbf7d0] active:scale-95 transition-all cursor-pointer">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                    </a>

                                    <!-- Full Details Drawer Square Button -->
                                    <button type="button" 
                                            onclick="openBookingDrawer('${b.id}')" 
                                            title="View Full Booking Drawer" 
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#f9fafb] hover:bg-[#f3f4f6] text-[#4b5563] border border-[#e5e7eb] active:scale-95 transition-all cursor-pointer">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        mainTbody.appendChild(row);
                    });
                    updateSelectAllCheckboxState();
                }

                // Render Mobile Cards View (< md)
                if (mobileContainer) {
                    mobileContainer.innerHTML = '';
                    pageData.forEach(b => {
                        const catSlug = getBookingCategorySlug(b);
                        const catName = getBookingCategoryName(catSlug);
                        const relDate = formatRelativeEventDate(b.date);
                        const totalFormatted = `₹${(parseInt(b.total) || 0).toLocaleString('en-IN')}`;
                        const venueLocation = b.area ? `${b.area}, ${b.city || 'Indore'}` : (b.city || 'Indore');

                        const statusConfig = getStatusPillConfig(b.status);

                        const card = document.createElement('div');
                        card.className = "bg-white border border-gray-200 p-4 rounded-xl shadow-2xs flex flex-col gap-2.5 text-xs text-left";
                        card.innerHTML = `
                            <!-- Top Card Header -->
                            <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-mono font-bold text-gray-900 text-xs">#${b.id}</span>
                                    <span class="text-[11px] text-gray-400">• ${b.created_at || 'Recent'}</span>
                                </div>
                                <button type="button" 
                                        onclick="openStatusDropdown(event, '${b.id}', '${b.status}')" 
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold ${statusConfig.btnClass} hover:shadow-xs transition-all cursor-pointer shadow-2xs">
                                    <i class="${statusConfig.icon} text-[10px]"></i>
                                    <span>${b.status}</span>
                                    <i class="fa-solid fa-chevron-down text-[8px] opacity-70 ml-0.5"></i>
                                </button>
                            </div>

                            <!-- Customer & Contact -->
                            <div class="flex flex-col gap-0.5">
                                <span class="text-sm font-bold text-gray-900">${b.name}</span>
                                <div class="flex items-center gap-2">
                                    <a href="tel:+91${b.mobile || b.whatsapp || ''}" class="text-xs text-blue-600 hover:underline font-semibold flex items-center gap-1">
                                        <i class="fa-solid fa-phone text-[10px]"></i> +91 ${b.mobile || b.whatsapp || ''}
                                    </a>
                                </div>
                            </div>

                            <!-- Package & Timing Card -->
                            <div class="bg-gray-50 border border-gray-100 p-2.5 rounded-lg flex flex-col gap-1.5">
                                <span class="font-semibold text-gray-900">${b.package || 'Celebration Setup'}</span>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <button type="button" 
                                            onclick="selectCategoryTab('${catSlug}')" 
                                            class="inline-flex items-center text-[10px] font-semibold px-2 py-0.5 rounded bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors">
                                        <span>${catName}</span>
                                    </button>
                                    <span class="text-[11px] text-gray-600 font-medium">${b.date || 'TBD'} ${b.time ? '• ' + b.time : ''}</span>
                                    ${relDate && relDate.label ? `<span class="text-[10px] font-semibold px-1.5 py-0.2 rounded border ${relDate.badgeClass}">${relDate.label}</span>` : ''}
                                </div>
                                <span class="text-[11px] text-gray-500">${venueLocation}</span>
                            </div>

                            <!-- Footer Pricing & Quick Actions -->
                            <div class="flex items-center justify-between pt-1 flex-wrap gap-2 border-t border-gray-100 mt-1">
                                <span class="text-sm font-black text-gray-900">${totalFormatted}</span>
                                <div class="flex items-center gap-1.5">
                                    ${(b.status === 'Pending' || b.status === 'Contacted') ? `
                                        <button type="button" onclick="quickApproveBooking('${b.id}')" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-all shadow-2xs">
                                            Approve
                                        </button>
                                        <button type="button" onclick="quickCancelBooking('${b.id}')" class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold transition-all">
                                            Cancel
                                        </button>
                                    ` : (b.status === 'Confirmed') ? `
                                        <button type="button" onclick="quickCompleteBooking('${b.id}')" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition-all shadow-2xs">
                                            Complete
                                        </button>
                                    ` : ''}
                                    <a href="tel:+91${b.mobile || b.whatsapp || ''}" class="p-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white border border-blue-200 transition-all cursor-pointer" title="Direct Phone Call">
                                        <i class="fa-solid fa-phone text-xs"></i>
                                    </a>
                                    <a href="https://wa.me/91${b.mobile || b.whatsapp || ''}?text=${encodeURIComponent('Hi ' + b.name + '! Artizen team here regarding your booking #' + b.id)}" target="_blank" class="p-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200" title="WhatsApp">
                                        <i class="fa-brands fa-whatsapp text-xs"></i>
                                    </a>
                                    <button onclick="openBookingDrawer('${b.id}')" class="px-3 py-1 bg-gray-900 text-white rounded-lg text-xs font-medium cursor-pointer">
                                        Details
                                    </button>
                                </div>
                            </div>
                        `;
                        mobileContainer.appendChild(card);
                    });
                }
            }

            // J. Pagination Controls Rendering
            const pagInfoEl = document.getElementById('b-pagination-info');
            const pagControlsEl = document.getElementById('b-pagination-controls');

            if (pagInfoEl) {
                if (totalItems === 0) {
                    pagInfoEl.textContent = 'Showing 0 to 0 of 0 bookings';
                } else {
                    const endItem = Math.min(startIndex + BOOKINGS_PAGE_SIZE, totalItems);
                    pagInfoEl.textContent = `Showing ${startIndex + 1} to ${endItem} of ${totalItems} bookings`;
                }
            }

            if (pagControlsEl) {
                pagControlsEl.innerHTML = '';
                if (totalPages > 1) {
                    // Prev Button
                    const prevBtn = document.createElement('button');
                    prevBtn.type = 'button';
                    prevBtn.disabled = (currentBookingsPage === 1);
                    prevBtn.className = `px-2.5 py-1 rounded-lg text-xs font-bold transition-all ${currentBookingsPage === 1 ? 'opacity-40 cursor-not-allowed text-gray-400 bg-gray-100' : 'bg-gray-100 hover:bg-gray-200 text-gray-800 cursor-pointer'}`;
                    prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left text-[10px]"></i>';
                    prevBtn.onclick = () => { if (currentBookingsPage > 1) { currentBookingsPage--; renderBookingsTables(); } };
                    pagControlsEl.appendChild(prevBtn);

                    // Page Pills
                    for (let p = 1; p <= totalPages; p++) {
                        const isCurrent = (p === currentBookingsPage);
                        const pBtn = document.createElement('button');
                        pBtn.type = 'button';
                        pBtn.className = isCurrent
                            ? "w-7 h-7 rounded-lg text-xs font-bold bg-gray-900 text-white shadow-2xs cursor-default"
                            : "w-7 h-7 rounded-lg text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-800 transition-colors cursor-pointer";
                        pBtn.textContent = p;
                        pBtn.onclick = () => { currentBookingsPage = p; renderBookingsTables(); };
                        pagControlsEl.appendChild(pBtn);
                    }

                    // Next Button
                    const nextBtn = document.createElement('button');
                    nextBtn.type = 'button';
                    nextBtn.disabled = (currentBookingsPage === totalPages);
                    nextBtn.className = `px-2.5 py-1 rounded-lg text-xs font-bold transition-all ${currentBookingsPage === totalPages ? 'opacity-40 cursor-not-allowed text-gray-400 bg-gray-100' : 'bg-gray-100 hover:bg-gray-200 text-gray-800 cursor-pointer'}`;
                    nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right text-[10px]"></i>';
                    nextBtn.onclick = () => { if (currentBookingsPage < totalPages) { currentBookingsPage++; renderBookingsTables(); } };
                    pagControlsEl.appendChild(nextBtn);
                }
            }
        }

        /**
         * Open Slide-Over Detail Drawer with complete data
         */
        function openBookingDrawer(bookingId) {
            const b = bookingsDB.find(item => item.id === bookingId);
            if (!b) return;

            activeDrawerBookingId = bookingId;

            const drawer = document.getElementById('booking-detail-drawer');
            const overlay = document.getElementById('booking-drawer-overlay');

            // Header info
            document.getElementById('drawer-booking-id').textContent = `#${b.id}`;
            document.getElementById('drawer-customer-title').textContent = b.name;
            document.getElementById('drawer-customer-avatar').textContent = getInitials(b.name);

            // Status Badge
            const badge = document.getElementById('drawer-status-badge');
            if (badge) {
                badge.className = `px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider ${getStatusClass(b.status)}`;
                badge.textContent = b.status;
            }

            // Customer Profile
            document.getElementById('drawer-customer-name').textContent = b.name;
            document.getElementById('drawer-customer-mobile').textContent = `+91 ${b.mobile || b.whatsapp || '--'}`;
            document.getElementById('drawer-customer-email').textContent = b.email || 'Not provided';
            
            const callLink = document.getElementById('drawer-call-link');
            if (callLink) callLink.href = `tel:+91${b.mobile || b.whatsapp || ''}`;

            const waLink = document.getElementById('drawer-whatsapp-link');
            if (waLink) waLink.href = `https://wa.me/91${b.mobile || b.whatsapp || ''}`;

            const linkContainer = document.getElementById('drawer-customer-link-container');
            if (linkContainer) {
                if (b.customer_id) {
                    linkContainer.innerHTML = `<a href="{{ route('admin.customers') }}?search=${encodeURIComponent(b.customer_id)}" class="inline-flex items-center gap-1.5 text-xs text-indigo-600 hover:text-indigo-800 font-bold hover:underline"><i class="fa-solid fa-address-card"></i> Profile (CUST-${b.customer_id})</a>`;
                } else {
                    linkContainer.innerHTML = `<span class="text-gray-400 text-[11px] font-medium">Guest Request</span>`;
                }
            }

            // Package & Event Setup
            const catSlug = getBookingCategorySlug(b);
            const catName = getBookingCategoryName(catSlug);
            document.getElementById('drawer-package-title').textContent = b.package || 'Celebration Setup';
            document.getElementById('drawer-category-badge').textContent = catName;
            document.getElementById('drawer-package-tier').textContent = b.tier ? `${b.tier} Deliverables` : 'Standard Setup Package';

            // Addons
            const addonsContainer = document.getElementById('drawer-addons-container');
            const addonsList = document.getElementById('drawer-addons-list');
            if (addonsContainer && addonsList) {
                if (b.addons && Array.isArray(b.addons) && b.addons.length > 0) {
                    addonsContainer.classList.remove('hidden');
                    addonsList.innerHTML = b.addons.map(a => `<span class="px-2 py-0.5 bg-gray-100 border border-gray-200 rounded text-[10px] font-semibold text-gray-700">${typeof a === 'string' ? a : (a.name || 'Add-on')}</span>`).join('');
                } else {
                    addonsContainer.classList.add('hidden');
                }
            }

            // Schedule & Relative Time
            const relDate = formatRelativeEventDate(b.date);
            document.getElementById('drawer-event-date').textContent = b.date || 'TBD';
            document.getElementById('drawer-relative-date').textContent = relDate.label;
            document.getElementById('drawer-event-time').textContent = b.time ? b.time : 'Flexible Timing';
            document.getElementById('drawer-guest-count').textContent = b.guest_count ? `${b.guest_count} Guests Expected` : 'Guest Count Not Specified';

            // Venue Location & Maps
            const fullAddr = b.address || 'Address provided during coordinator callback';
            document.getElementById('drawer-venue-address').textContent = fullAddr;
            document.getElementById('drawer-venue-landmark').textContent = b.landmark ? `Landmark: ${b.landmark} • ` : '';
            document.getElementById('drawer-venue-area').textContent = b.area ? `Area: ${b.area}` : '';
            document.getElementById('drawer-venue-city').textContent = b.city || 'Indore';
            document.getElementById('drawer-venue-state').textContent = b.state || 'Madhya Pradesh';
            document.getElementById('drawer-venue-pincode').textContent = b.pincode || '452001';

            const mapsLink = document.getElementById('drawer-maps-link');
            if (mapsLink) {
                const query = encodeURIComponent(`${fullAddr}, ${b.area || ''}, ${b.city || 'Indore'}`);
                mapsLink.href = `https://www.google.com/maps/search/?api=1&query=${query}`;
            }

            // Customer Special Instructions
            const notesCard = document.getElementById('drawer-notes-card');
            const notesText = document.getElementById('drawer-customer-notes');
            if (notesCard && notesText) {
                if (b.notes && b.notes.trim()) {
                    notesCard.classList.remove('hidden');
                    notesText.textContent = `"${b.notes.trim()}"`;
                } else {
                    notesCard.classList.add('hidden');
                }
            }

            // Pricing Breakdown
            const pkgPrice = parseInt(b.package_price) || (parseInt(b.total) - (parseInt(b.surcharge) || 0)) || parseInt(b.total) || 0;
            const surcharge = parseInt(b.surcharge) || 0;
            const total = parseInt(b.total) || (pkgPrice + surcharge) || 0;

            document.getElementById('drawer-price-base').textContent = `₹${pkgPrice.toLocaleString('en-IN')}`;
            document.getElementById('drawer-price-surcharge').textContent = surcharge > 0 ? `+ ₹${surcharge.toLocaleString('en-IN')}` : '₹0 (Standard Area)';
            document.getElementById('drawer-price-total').textContent = `₹${total.toLocaleString('en-IN')}`;

            // Dynamic Action Buttons with active states
            const actionsBox = document.getElementById('drawer-action-buttons');
            if (actionsBox) {
                const st = b.status;
                actionsBox.innerHTML = `
                    <button onclick="updateBookingStatus('${b.id}', 'Contacted')" 
                            class="px-3 py-2.5 rounded-xl font-bold transition-all text-xs flex items-center justify-center gap-1.5 cursor-pointer ${st === 'Contacted' ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white border border-blue-200'}">
                        <i class="fa-solid fa-phone text-xs"></i>
                        <span>Mark Contacted</span>
                    </button>
                    <button onclick="updateBookingStatus('${b.id}', 'Confirmed')" 
                            class="px-3 py-2.5 rounded-xl font-bold transition-all text-xs flex items-center justify-center gap-1.5 cursor-pointer ${st === 'Confirmed' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200'}">
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>Confirm Event</span>
                    </button>
                    <button onclick="updateBookingStatus('${b.id}', 'Completed')" 
                            class="px-3 py-2.5 rounded-xl font-bold transition-all text-xs flex items-center justify-center gap-1.5 cursor-pointer ${st === 'Completed' ? 'bg-purple-600 text-white shadow-xs' : 'bg-purple-50 hover:bg-purple-600 text-purple-700 hover:text-white border border-purple-200'}">
                        <i class="fa-solid fa-star text-xs"></i>
                        <span>Mark Completed</span>
                    </button>
                    <button onclick="updateBookingStatus('${b.id}', 'Cancelled')" 
                            class="px-3 py-2.5 rounded-xl font-bold transition-all text-xs flex items-center justify-center gap-1.5 cursor-pointer ${st === 'Cancelled' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200'}">
                        <i class="fa-solid fa-ban text-xs"></i>
                        <span>Cancel Request</span>
                    </button>
                `;
            }

            // Show drawer with transition
            if (overlay && drawer) {
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
                drawer.classList.remove('translate-x-full');
            }
        }

        function closeBookingDrawer() {
            const drawer = document.getElementById('booking-detail-drawer');
            const overlay = document.getElementById('booking-drawer-overlay');

            if (drawer && overlay) {
                drawer.classList.add('translate-x-full');
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
            activeDrawerBookingId = null;
        }

        // Keyboard shortcuts: Esc to close dropdown/drawer/deselect, '/' to focus search
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeStatusDropdown();
                if (activeDrawerBookingId) {
                    closeBookingDrawer();
                } else if (selectedBookingIds.size > 0) {
                    clearSelectedBookings();
                }
            }
            if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
                e.preventDefault();
                const searchInput = document.getElementById('b-search-input');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
        });

        // Close custom status dropdown when clicking anywhere outside
        document.addEventListener('click', (e) => {
            const popover = document.getElementById('status-change-popover');
            if (popover && !popover.classList.contains('hidden')) {
                if (!popover.contains(e.target)) {
                    closeStatusDropdown();
                }
            }
        });

        function downloadActiveBookingPDF(btnEl) {
            if (!activeDrawerBookingId) return;
            const booking = bookingsDB.find(b => b.id === activeDrawerBookingId);
            if (!booking) return;
            if (window.ArtizenPDF) {
                window.ArtizenPDF.download(booking, btnEl);
            } else {
                window.open('/booking/success?id=' + booking.id, '_blank');
            }
        }

        // Safe stubs for any legacy layout listeners
        function renderPackages() {}
        function renderCategories() {}
        function renderCustomers() {}
        function renderFAQs() {}

        // Initialize Bookings tables on load
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const searchParam = urlParams.get('search');
            const statusParam = urlParams.get('status');
            const categoryParam = urlParams.get('category');

            if (searchParam) {
                currentBookingsSearch = searchParam.trim();
                const searchInput = document.getElementById('b-search-input');
                if (searchInput) searchInput.value = currentBookingsSearch;
                const clearBtn = document.getElementById('b-search-clear-btn');
                if (clearBtn) clearBtn.classList.remove('hidden');
            }
            if (statusParam) {
                currentBookingsStatus = statusParam.trim();
            }
            if (categoryParam) {
                currentBookingsCategory = categoryParam.trim();
            }

            updateOverviewMetrics();
            updateQuickFilterChipsUI();
            renderBookingsTables();

            // If a specific booking ID was searched in the URL, auto-open the detail drawer for fast inspection!
            if (searchParam) {
                const searchLower = searchParam.trim().toLowerCase();
                const exactMatch = bookingsDB.find(b => (b.id || '').toLowerCase() === searchLower);
                if (exactMatch) {
                    setTimeout(() => {
                        openBookingDrawer(exactMatch.id);
                    }, 200);
                }
            }
        });
    </script>
@endpush

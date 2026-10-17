@extends('layouts.admin')

@section('page_title', 'Customer Directory')
@section('page_heading', 'Customer Directory')
@section('page_subheading', 'Real client directory, verified profiles, and linked event booking histories')

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

{{-- ===================================================================
     2. PAGE HEADER BAR
     =================================================================== --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200/80 pb-5 mb-6">
    <div>
        <h2 class="text-xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2.5">
            <i class="fa-solid fa-users text-indigo-600 text-lg"></i>
            <span>Customer Directory</span>
        </h2>
        <p class="text-xs text-gray-500 mt-1">Real verified customer records loaded directly from the MySQL database with linked booking history.</p>
    </div>
    <div class="flex items-center gap-2.5">
        <a href="{{ route('admin.bookings') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold rounded-xl transition-all border border-gray-200 shadow-2xs">
            <i class="fa-solid fa-list-check text-xs"></i>
            <span>View All Bookings</span>
        </a>
    </div>
</div>

{{-- ===================================================================
     3. SUMMARY METRIC CARDS GRID
     =================================================================== --}}
@php
    $totalCustomersCount = count($customersList ?? []);
    $withBookingsCount   = count(array_filter($customersList ?? [], fn($c) => ($c['total_bookings'] ?? 0) > 0));
    $totalSpendSum       = array_sum(array_column($customersList ?? [], 'total_spend'));
    $indoreClientsCount  = count(array_filter($customersList ?? [], fn($c) => stripos($c['city'] ?? '', 'indore') !== false || stripos($c['state'] ?? '', 'madhya') !== false));
@endphp

<div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 mb-6">
    <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total Clients</span>
            <span class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-xs">
                <i class="fa-solid fa-users"></i>
            </span>
        </div>
        <span class="text-2xl font-extrabold text-gray-900 block mt-2" id="metric-total-customers">{{ $totalCustomersCount }}</span>
        <span class="text-[11px] text-gray-400 mt-0.5 block">Verified customer accounts</span>
    </div>

    <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Active Bookers</span>
            <span class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 text-xs">
                <i class="fa-solid fa-calendar-check"></i>
            </span>
        </div>
        <span class="text-2xl font-extrabold text-emerald-600 block mt-2" id="metric-active-customers">{{ $withBookingsCount }}</span>
        <span class="text-[11px] text-gray-400 mt-0.5 block">Clients with ≥1 event booked</span>
    </div>

    <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Total Client Spend</span>
            <span class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-xs">
                <i class="fa-solid fa-indian-rupee-sign"></i>
            </span>
        </div>
        <span class="text-2xl font-extrabold text-gray-900 block mt-2" id="metric-total-spend">₹{{ number_format($totalSpendSum) }}</span>
        <span class="text-[11px] text-blue-600 font-medium mt-0.5 block">Non-cancelled event orders</span>
    </div>

    <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Launch Hub</span>
            <span class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 text-xs">
                <i class="fa-solid fa-location-dot"></i>
            </span>
        </div>
        <span class="text-2xl font-extrabold text-gray-900 block mt-2">{{ $indoreClientsCount }}</span>
        <span class="text-[11px] text-gray-400 mt-0.5 block">Indore & Madhya Pradesh</span>
    </div>
</div>

{{-- ===================================================================
     4. SEARCH, FILTER & SORT CONTROLS
     =================================================================== --}}
<div class="bg-white border border-gray-200/90 rounded-2xl p-4 shadow-2xs mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="relative flex-1 min-w-[260px]">
        <span class="absolute left-3.5 top-2.5 text-xs text-gray-400"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="customer-search-input" oninput="handleCustomerSearch(this.value)" placeholder="Search customer by name, mobile, email, area, or ID..." class="w-full bg-gray-50 border border-gray-300 pl-10 pr-4 py-2 text-xs font-medium text-gray-900 rounded-xl outline-none focus:bg-white focus:border-indigo-600 transition-colors">
    </div>

    <div class="flex flex-wrap items-center gap-3 shrink-0">
        <div class="flex items-center gap-2">
            <label for="customer-filter-select" class="text-xs font-semibold text-gray-600 shrink-0">Filter:</label>
            <select id="customer-filter-select" onchange="handleCustomerFilter(this.value)" class="bg-white border border-gray-300 px-3 py-2 text-xs font-medium text-gray-900 rounded-xl outline-none focus:border-indigo-600 cursor-pointer">
                <option value="all">All Clients ({{ $totalCustomersCount }})</option>
                <option value="with_bookings">With Bookings ({{ $withBookingsCount }})</option>
                <option value="indore">Indore Region ({{ $indoreClientsCount }})</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <label for="customer-sort-select" class="text-xs font-semibold text-gray-600 shrink-0">Sort:</label>
            <select id="customer-sort-select" onchange="handleCustomerSort(this.value)" class="bg-white border border-gray-300 px-3 py-2 text-xs font-medium text-gray-900 rounded-xl outline-none focus:border-indigo-600 cursor-pointer">
                <option value="newest">Newest First</option>
                <option value="bookings_desc">Most Bookings</option>
                <option value="spend_desc">Highest Spend</option>
                <option value="name_asc">Name (A-Z)</option>
            </select>
        </div>
    </div>
</div>

{{-- ===================================================================
     5. CUSTOMER DIRECTORY TABLE
     =================================================================== --}}
<div class="bg-white border border-gray-200/90 rounded-2xl overflow-hidden shadow-2xs">
    <div class="overflow-x-auto w-full">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="border-b border-gray-200/80 text-gray-700 font-bold bg-gray-50/75">
                    <th class="py-3 px-4 font-semibold text-xs">Customer</th>
                    <th class="py-3 px-4 font-semibold text-xs">Mobile & WhatsApp</th>
                    <th class="py-3 px-4 font-semibold text-xs">Verified Email</th>
                    <th class="py-3 px-4 font-semibold text-xs">Location</th>
                    <th class="py-3 px-4 font-semibold text-xs text-center">Bookings</th>
                    <th class="py-3 px-4 font-semibold text-xs text-right">Total Spend</th>
                    <th class="py-3 px-4 font-semibold text-xs text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-900" id="customers-tbody">
                <!-- Populated via client-side JavaScript engine -->
            </tbody>
        </table>
    </div>
    
    <!-- Empty State -->
    <div id="customers-empty-state" class="hidden py-16 px-4 text-center">
        <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3 text-lg">
            <i class="fa-solid fa-user-slash"></i>
        </div>
        <h4 class="text-sm font-bold text-gray-800">No Customers Found</h4>
        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">No customer records matched your search query or selected filter criteria.</p>
        <button type="button" onclick="resetCustomerSearch()" class="mt-3 px-3.5 py-1.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg transition-colors cursor-pointer">
            Reset Filters
        </button>
    </div>
</div>

{{-- ===================================================================
     6. SLIDE-OVER CUSTOMER DETAIL DRAWER & OVERLAY
     =================================================================== --}}
<div id="customer-drawer-overlay" onclick="closeCustomerDrawer()" class="fixed inset-0 bg-black/50 z-40 hidden opacity-0 transition-opacity duration-300"></div>

<div id="customer-detail-drawer" class="fixed top-0 right-0 h-full w-full sm:w-[480px] md:w-[540px] bg-white z-50 shadow-2xl transform translate-x-full transition-transform duration-300 flex flex-col">
    <!-- Drawer Header -->
    <div class="p-5 border-b border-gray-200 flex items-center justify-between bg-gray-50/75">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-indigo-600 text-white font-extrabold flex items-center justify-center text-sm shadow-2xs" id="cd-avatar">
                C
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-extrabold text-base text-gray-900" id="cd-name">Customer Name</h3>
                    <span id="cd-id-badge" class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200">CUST-#</span>
                </div>
                <span class="text-[11px] text-gray-500 block" id="cd-created-at">Client since --</span>
            </div>
        </div>
        <button type="button" onclick="closeCustomerDrawer()" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>

    <!-- Drawer Body -->
    <div class="flex-grow overflow-y-auto p-5 flex flex-col gap-5 text-xs text-left">
        <!-- Quick Spend & Bookings Metric Bar -->
        <div class="grid grid-cols-3 gap-3 p-3.5 bg-gray-50 border border-gray-200 rounded-xl">
            <div class="text-left">
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block">Bookings</span>
                <span class="text-base font-extrabold text-gray-900 mt-0.5 block" id="cd-total-bookings">0</span>
            </div>
            <div class="text-left">
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Total Spend</span>
                <span class="text-base font-extrabold text-emerald-600 mt-0.5 block" id="cd-total-spend">₹0</span>
            </div>
            <div class="text-left">
                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block">Status</span>
                <span class="text-xs font-extrabold text-indigo-700 mt-1 block">Verified</span>
            </div>
        </div>

        <!-- Contact & Profile Information Card -->
        <div class="border border-gray-200 rounded-xl p-4 flex flex-col gap-3">
            <span class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2 border-b border-gray-100 pb-2">
                <i class="fa-solid fa-id-card text-indigo-500"></i> Profile Information
            </span>
            <div class="grid grid-cols-1 gap-2.5 text-gray-700">
                <div class="flex justify-between items-center py-1 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Full Name</span>
                    <span class="font-bold text-gray-900" id="cd-profile-name">--</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Mobile Number</span>
                    <span class="font-bold text-gray-900" id="cd-profile-mobile">--</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">WhatsApp</span>
                    <a id="cd-profile-whatsapp" href="#" target="_blank" class="font-bold text-emerald-700 hover:underline flex items-center gap-1.5">
                        <i class="fa-brands fa-whatsapp"></i> <span>--</span>
                    </a>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Verified Email</span>
                    <span class="font-bold text-gray-900" id="cd-profile-email">--</span>
                </div>
                <div class="flex flex-col gap-1 py-1">
                    <span class="text-gray-500 font-medium">Delivery / Event Address</span>
                    <span class="font-bold text-gray-900 leading-snug" id="cd-profile-address">--</span>
                </div>
            </div>
        </div>

        <!-- Linked Booking History -->
        <div class="border border-gray-200 rounded-xl p-4 flex flex-col gap-3">
            <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                <span class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-emerald-600"></i> Linked Booking History
                </span>
                <span class="text-[11px] font-bold text-gray-500" id="cd-bookings-count-badge">0 Bookings</span>
            </div>

            <div id="cd-bookings-container" class="flex flex-col gap-3">
                <!-- Injected via JavaScript -->
            </div>
        </div>
    </div>

    <!-- Drawer Footer -->
    <div class="p-4 border-t border-gray-200 bg-gray-50/75 flex items-center justify-between gap-3">
        <a id="cd-whatsapp-direct-btn" href="#" target="_blank" class="flex-1 py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold text-center transition-colors flex items-center justify-center gap-2 shadow-2xs">
            <i class="fa-brands fa-whatsapp text-sm"></i>
            <span>Chat on WhatsApp</span>
        </a>
        <button type="button" onclick="closeCustomerDrawer()" class="py-2.5 px-4 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl text-xs font-bold transition-colors cursor-pointer">
            Close
        </button>
    </div>
</div>

@endsection

@push('page_scripts')
<script>
    // Real Customer Data from MySQL database
    const customersDB = @json($customersList ?? []);
    let currentCustomerSearch = '';
    let currentCustomerFilter = 'all';
    let currentCustomerSort = 'newest';

    function getCustomerStatusBadge(status) {
        switch ((status || '').toLowerCase()) {
            case 'pending': return 'bg-amber-50 text-amber-800 border border-amber-200';
            case 'contacted': return 'bg-blue-50 text-blue-800 border border-blue-200';
            case 'confirmed': return 'bg-emerald-50 text-emerald-800 border border-emerald-200';
            case 'completed': return 'bg-purple-50 text-purple-800 border border-purple-200';
            case 'cancelled': return 'bg-red-50 text-red-800 border border-red-200';
            default: return 'bg-gray-100 text-gray-700 border border-gray-200';
        }
    }

    function renderCustomers() {
        const tbody = document.getElementById('customers-tbody');
        const emptyState = document.getElementById('customers-empty-state');
        if (!tbody) return;

        tbody.innerHTML = '';

        // 1. Filter
        let filtered = customersDB.filter(c => {
            if (currentCustomerFilter === 'with_bookings' && (c.total_bookings || 0) <= 0) return false;
            if (currentCustomerFilter === 'indore') {
                const loc = ((c.city || '') + ' ' + (c.state || '')).toLowerCase();
                if (!loc.includes('indore') && !loc.includes('madhya')) return false;
            }
            if (currentCustomerSearch) {
                const query = currentCustomerSearch.toLowerCase();
                const matchName = (c.name || '').toLowerCase().includes(query);
                const matchMobile = (c.mobile || '').includes(query);
                const matchEmail = (c.email || '').toLowerCase().includes(query);
                const matchArea = ((c.area || '') + ' ' + (c.city || '')).toLowerCase().includes(query);
                const matchId = String(c.id) === query || `cust-${c.id}`.toLowerCase() === query;
                if (!matchName && !matchMobile && !matchEmail && !matchArea && !matchId) return false;
            }
            return true;
        });

        // 2. Sort
        filtered.sort((a, b) => {
            if (currentCustomerSort === 'bookings_desc') {
                return (b.total_bookings || 0) - (a.total_bookings || 0);
            } else if (currentCustomerSort === 'spend_desc') {
                return (b.total_spend || 0) - (a.total_spend || 0);
            } else if (currentCustomerSort === 'name_asc') {
                return (a.name || '').localeCompare(b.name || '');
            } else {
                // newest first
                return (b.id || 0) - (a.id || 0);
            }
        });

        if (filtered.length === 0) {
            emptyState.classList.remove('hidden');
            return;
        } else {
            emptyState.classList.add('hidden');
        }

        filtered.forEach(c => {
            const initial = (c.name || 'C').charAt(0).toUpperCase();
            const locationStr = [c.area, c.city].filter(Boolean).join(', ') || 'Indore';
            const emailDisplay = c.email 
                ? `<span class="font-medium text-gray-900">${c.email}</span>` 
                : `<span class="text-gray-400 font-normal italic">Not provided</span>`;

            const row = document.createElement('tr');
            row.className = "hover:bg-gray-50/75 transition-colors border-b border-gray-100 text-gray-800";
            row.innerHTML = `
                <td class="py-3.5 px-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 font-extrabold flex items-center justify-center text-xs shrink-0">
                            ${initial}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-900 block leading-tight">${c.name}</span>
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-gray-100 text-gray-600 border border-gray-200">CUST-${c.id}</span>
                            </div>
                            <span class="text-[10px] text-gray-400 block mt-0.5">${c.created_at || 'Registered Client'}</span>
                        </div>
                    </div>
                </td>
                <td class="py-3.5 px-4 font-mono font-medium">
                    <div class="flex flex-col gap-0.5">
                        <span class="text-gray-900 font-bold">+91 ${c.mobile || '--'}</span>
                        <a href="https://wa.me/91${c.whatsapp || c.mobile || ''}" target="_blank" class="text-[10px] text-emerald-600 font-semibold hover:underline inline-flex items-center gap-1">
                            <i class="fa-brands fa-whatsapp text-xs"></i> WhatsApp
                        </a>
                    </div>
                </td>
                <td class="py-3.5 px-4">
                    ${emailDisplay}
                </td>
                <td class="py-3.5 px-4 text-gray-600 font-medium">
                    <span>${locationStr}</span>
                </td>
                <td class="py-3.5 px-4 text-center">
                    <span class="px-2.5 py-1 rounded-full text-xs font-extrabold ${c.total_bookings > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-gray-100 text-gray-500'}">
                        ${c.total_bookings} ${c.total_bookings === 1 ? 'Booking' : 'Bookings'}
                    </span>
                </td>
                <td class="py-3.5 px-4 text-right font-bold text-gray-900">
                    ₹${Number(c.total_spend || 0).toLocaleString('en-IN')}
                </td>
                <td class="py-3.5 px-4 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" onclick="openCustomerDrawer(${c.id})" class="px-2.5 py-1.5 bg-gray-900 hover:bg-black text-white text-[11px] font-bold rounded-lg shadow-2xs transition-colors cursor-pointer flex items-center gap-1">
                            <i class="fa-solid fa-address-card text-[10px]"></i> Profile
                        </button>
                        <a href="https://wa.me/91${c.whatsapp || c.mobile || ''}" target="_blank" title="WhatsApp Customer" class="px-2 py-1.5 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 rounded-lg text-xs font-semibold transition-all">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function handleCustomerSearch(val) {
        currentCustomerSearch = (val || '').trim();
        renderCustomers();
    }

    function resetCustomerSearch() {
        currentCustomerSearch = '';
        currentCustomerFilter = 'all';
        currentCustomerSort = 'newest';
        document.getElementById('customer-search-input').value = '';
        document.getElementById('customer-filter-select').value = 'all';
        document.getElementById('customer-sort-select').value = 'newest';
        renderCustomers();
    }

    function handleCustomerFilter(val) {
        currentCustomerFilter = val;
        renderCustomers();
    }

    function handleCustomerSort(val) {
        currentCustomerSort = val;
        renderCustomers();
    }

    // Slide-over Customer Drawer Engine
    function openCustomerDrawer(customerId) {
        const c = customersDB.find(item => item.id === Number(customerId));
        if (!c) return;

        const overlay = document.getElementById('customer-drawer-overlay');
        const drawer  = document.getElementById('customer-detail-drawer');

        const initial = (c.name || 'C').charAt(0).toUpperCase();
        document.getElementById('cd-avatar').textContent = initial;
        document.getElementById('cd-name').textContent = c.name || 'Customer';
        document.getElementById('cd-id-badge').textContent = `CUST-${c.id}`;
        document.getElementById('cd-created-at').textContent = `Client since: ${c.created_at || 'Recent'}`;

        document.getElementById('cd-total-bookings').textContent = c.total_bookings || 0;
        document.getElementById('cd-total-spend').textContent = `₹${Number(c.total_spend || 0).toLocaleString('en-IN')}`;

        document.getElementById('cd-profile-name').textContent = c.name || '--';
        document.getElementById('cd-profile-mobile').textContent = `+91 ${c.mobile || '--'}`;

        const waLink = document.getElementById('cd-profile-whatsapp');
        if (waLink) {
            waLink.href = `https://wa.me/91${c.whatsapp || c.mobile || ''}`;
            waLink.querySelector('span').textContent = `+91 ${c.whatsapp || c.mobile || '--'}`;
        }

        const directWaBtn = document.getElementById('cd-whatsapp-direct-btn');
        if (directWaBtn) {
            directWaBtn.href = `https://wa.me/91${c.whatsapp || c.mobile || ''}`;
        }

        document.getElementById('cd-profile-email').textContent = c.email || 'Not provided';

        const fullAddr = [c.address, c.area, c.city, c.state, c.pincode].filter(Boolean).join(', ') || 'Indore, Madhya Pradesh';
        document.getElementById('cd-profile-address').textContent = fullAddr;

        // Render Linked Bookings List
        const bookingsBox = document.getElementById('cd-bookings-container');
        document.getElementById('cd-bookings-count-badge').textContent = `${(c.bookings || []).length} Bookings`;

        if (bookingsBox) {
            bookingsBox.innerHTML = '';
            if (!c.bookings || c.bookings.length === 0) {
                bookingsBox.innerHTML = `
                    <div class="py-6 px-3 text-center bg-gray-50 border border-gray-200 rounded-xl text-gray-500">
                        <i class="fa-solid fa-calendar-xmark text-lg text-gray-400 mb-1.5 block"></i>
                        <span class="font-semibold text-xs block">No Bookings Yet</span>
                        <span class="text-[10px] text-gray-400">This client has not placed any booking orders.</span>
                    </div>
                `;
            } else {
                c.bookings.forEach(b => {
                    const card = document.createElement('div');
                    card.className = "p-3.5 bg-gray-50 border border-gray-200 rounded-xl flex flex-col gap-2 hover:border-gray-300 transition-colors";
                    card.innerHTML = `
                        <div class="flex items-center justify-between border-b border-gray-200/80 pb-2">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-gray-900">${b.id}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${getCustomerStatusBadge(b.status)}">${b.status}</span>
                            </div>
                            <span class="font-extrabold text-gray-900">₹${Number(b.total_amount || 0).toLocaleString('en-IN')}</span>
                        </div>
                        <div class="flex flex-col gap-0.5 text-gray-700">
                            <span class="font-bold text-gray-900">${b.package_name || 'Event Package'}</span>
                            <span class="text-[11px] text-gray-500">${b.category_name || 'Occasion'} • ${b.tier_name || 'Standard Setup'}</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-gray-500 pt-1">
                            <span><i class="fa-solid fa-calendar text-gray-400 mr-1"></i> ${b.event_date || 'Date TBD'} (${b.event_time || 'Schedule'})</span>
                            <a href="{{ route('admin.bookings') }}?search=${encodeURIComponent(b.id)}" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold hover:underline">
                                Details &rarr;
                            </a>
                        </div>
                    `;
                    bookingsBox.appendChild(card);
                });
            }
        }

        if (overlay && drawer) {
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            drawer.classList.remove('translate-x-full');
        }
    }

    function closeCustomerDrawer() {
        const overlay = document.getElementById('customer-drawer-overlay');
        const drawer  = document.getElementById('customer-detail-drawer');
        if (drawer && overlay) {
            drawer.classList.add('translate-x-full');
            overlay.classList.add('opacity-0');
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }
    }

    // Auto-search or open customer drawer if query parameter is provided (e.g. ?search=8 or ?id=8)
    document.addEventListener('DOMContentLoaded', () => {
        renderCustomers();

        const params = new URLSearchParams(window.location.search);
        const searchParam = params.get('search');
        const idParam = params.get('id');

        if (idParam) {
            openCustomerDrawer(idParam);
        } else if (searchParam) {
            const input = document.getElementById('customer-search-input');
            if (input) {
                input.value = searchParam;
                handleCustomerSearch(searchParam);
            }
        }
    });
</script>
@endpush

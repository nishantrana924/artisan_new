@extends('layouts.admin')

@section('page_title', 'Hub Settings')
@section('page_heading', 'Hub Settings')
@section('page_subheading', 'Maintain Indore area surcharges, business hours, and contact info')

@push('head_scripts')

@endpush

@section('content')
<div class="border-b border-gray-200 pb-4">
                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">Website & SEO Config</h2>
                    <p class="text-xs text-gray-500 font-normal mt-0.5">Control metadata, operating hubs, and zone delivery logistics</p>
                </div>

                <form action="{{ route('admin.settings.save') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start w-full">
                    @csrf
                    
                    <!-- Left Form Panel (Spans 8 cols) -->
                    <div class="lg:col-span-8 bg-white border border-gray-200 p-6 rounded-xl shadow-sm flex flex-col gap-5">
                        <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3 mb-2">Meta Details</h3>
                        
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs text-gray-600 font-semibold uppercase tracking-wider">Meta Title Template</label>
                            <input type="text" name="meta_title" value="{{ $settings['meta_title'] ?? '' }}" class="w-full bg-white border border-gray-300 p-2.5 text-xs font-semibold text-gray-900 rounded-lg outline-none focus:border-gray-900">
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs text-gray-600 font-semibold uppercase tracking-wider">Meta Description</label>
                            <textarea name="meta_description" rows="3" class="w-full bg-white border border-gray-300 p-2.5 text-xs font-semibold text-gray-900 rounded-lg outline-none focus:border-gray-900">{{ $settings['meta_description'] ?? '' }}</textarea>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs text-gray-600 font-semibold uppercase tracking-wider">Keywords</label>
                            <input type="text" name="meta_keywords" value="{{ $settings['meta_keywords'] ?? '' }}" class="w-full bg-white border border-gray-300 p-2.5 text-xs font-semibold text-gray-900 rounded-lg outline-none focus:border-gray-900">
                        </div>

                        <button type="submit" class="px-6 py-3 bg-gray-900 hover:bg-black text-white rounded-lg text-xs font-bold uppercase tracking-wider transition-colors mt-4 self-end cursor-pointer shadow-sm">
                            Save Config Settings
                        </button>
                    </div>

                    <!-- Right zone management (Spans 4 cols) -->
                    <div class="lg:col-span-4 bg-white border border-gray-200 p-6 rounded-xl shadow-sm flex flex-col gap-4">
                        <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Delivery Surcharges</h3>
                        
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-xs text-gray-900 font-semibold">Vijay Nagar</span>
                                <input type="text" name="surcharges[Vijay Nagar]" value="₹{{ $settings['surcharges']['Vijay Nagar'] ?? 0 }}" class="w-24 bg-white border border-gray-300 px-3 py-1.5 text-xs rounded-lg text-gray-900 font-bold text-center">
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-xs text-gray-900 font-semibold">Saket / Palasia</span>
                                <input type="text" name="surcharges[Saket / Palasia]" value="₹{{ $settings['surcharges']['Saket / Palasia'] ?? 299 }}" class="w-24 bg-white border border-gray-300 px-3 py-1.5 text-xs rounded-lg text-gray-900 font-bold text-center">
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-xs text-gray-900 font-semibold">Bypass / Nipania</span>
                                <input type="text" name="surcharges[Bypass / Nipania]" value="₹{{ $settings['surcharges']['Bypass / Nipania'] ?? 499 }}" class="w-24 bg-white border border-gray-300 px-3 py-1.5 text-xs rounded-lg text-gray-900 font-bold text-center">
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-xs text-gray-900 font-semibold">Rau / Silicon City</span>
                                <input type="text" name="surcharges[Rau / Silicon City]" value="₹{{ $settings['surcharges']['Rau / Silicon City'] ?? 999 }}" class="w-20 bg-white border border-border-color px-3 py-1.5 text-xs rounded-lg text-main-text font-heading font-bold text-center">
                            </div>
                        </div>
                    </div>

                </form>
@endsection

@push('modals')
@endpush

@push('page_scripts')
    <script>
        // 1. Sidebar tab switching engine
        function switchTab(tabId) {
            // Update browser URL query parameter without full reload
            if (window.history && window.history.pushState) {
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + tabId;
                window.history.pushState({ tab: tabId }, '', newUrl);
            }

            // Hide all tab panels
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.add('hidden');
            });

            // Show target tab panel
            let activePanel = document.getElementById(`panel-${tabId}`);
            if (!activePanel && (tabId === 'hero-manager' || tabId === 'about-manager')) {
                activePanel = document.getElementById('panel-cms') || document.getElementById('panel-hero-manager') || document.getElementById('panel-about-manager');
            }
            if (activePanel) {
                activePanel.classList.remove('hidden');
            }

            // Trigger specific tab render functions to populate dynamic data on switch
            if (tabId === 'overview') { if (typeof updateOverviewMetrics === 'function') updateOverviewMetrics(); }
            else if (tabId === 'bookings') { if (typeof renderBookingsTables === 'function') renderBookingsTables(); }
            else if (tabId === 'packages') { if (typeof renderPackages === 'function') renderPackages(); }
            else if (tabId === 'categories') { if (typeof renderCategories === 'function') renderCategories(); }
            else if (tabId === 'customers') { if (typeof renderCustomers === 'function') renderCustomers(); }
            else if (tabId === 'testimonials') { if (typeof renderFAQs === 'function') renderFAQs(); }

            // Remove active states from sidebar nav elements
            document.querySelectorAll('#sidebar-nav [data-tab-btn]').forEach(el => {
                el.className = "flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 text-gray-600 hover:text-gray-900 hover:bg-gray-50 text-left w-full";
            });

            // Set active class to clicked button/link
            const activeBtn = document.querySelector(`#sidebar-nav [data-tab-btn="${tabId}"]`);
            if (activeBtn) {
                activeBtn.className = "flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-150 text-gray-900 bg-gray-100 text-left w-full";
            }

            // Update title headers
            const headerTitle = document.getElementById('current-view-title');
            const headerSubtitle = document.getElementById('current-view-subtitle');

            const viewTitles = {
                overview: { title: "Overview Panel", desc: "Live bookings & performance insights" },
                bookings: { title: "Bookings Portal", desc: "Approve, confirm, or track event booking requests" },
                packages: { title: "Packages Editor", desc: "Edit specifications, inclusions & base pricing" },
                categories: { title: "Category Settings", desc: "Add or disable target event category types" },
                customers: { title: "Customer Database", desc: "View contact history & bookings records" },
                testimonials: { title: "FAQs & Testimonials Manager", desc: "Edit guest reviews & common portal help articles" },
                'hero-manager': { title: "Hero Slider Manager", desc: "Manage Hero background slider images and headings" },
                'about-manager': { title: "About Page Manager", desc: "Manage About page story, stats and photo gallery" },
                'legal-manager': { title: "Legal Pages Manager", desc: "Manage Privacy Policy, Terms & Conditions, Cancellation Policy, and Booking Policy" },
                cms: { title: "CMS Layout Editor", desc: "Manage Hero background slider and About copy" },
                settings: { title: "Hub Settings", desc: "Maintain Indore area surcharges, SEO title, meta configs" }
            };

            if (viewTitles[tabId]) {
                if (headerTitle) headerTitle.textContent = viewTitles[tabId].title;
                if (headerSubtitle) headerSubtitle.textContent = viewTitles[tabId].desc;
            }

            // Close mobile sidebar on switch
            const sidebar = document.querySelector('aside');
            if (sidebar) sidebar.classList.remove('active');
        }

        // Toggle mobile menu helper
        function toggleMobileSidebar() {
            const sidebar = document.querySelector('aside');
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('w-full');
        }

        // 2. Mock Databases & Dynamic JSON Databases
        let bookingsDB = @json($bookings);
        
        let rawPackages = @json($packages);
        let packagesDB = [];
        Object.keys(rawPackages).forEach(catId => {
            let cat = rawPackages[catId];
            if (cat && cat.tiers) {
                cat.tiers.forEach((tier, idx) => {
                    packagesDB.push({
                        catId: catId,
                        tierIndex: idx,
                        categoryName: cat.title || 'Event Setup',
                        title: tier.name || tier.title || 'Event Service',
                        tierName: tier.badge || tier.tier || (tier.price > 15000 ? 'Luxury Setup' : (tier.price > 8000 ? 'Gold Setup' : 'Standard Setup')),
                        desc: tier.desc || tier.short_desc || '',
                        price: tier.price || 0,
                        originalPrice: tier.original_price || tier.originalPrice || null,
                        discountPct: tier.discount_pct || null,
                        image: tier.image || cat.image || '/assets/images/hero/1.jpg',
                        inclusions: tier.inclusions || [],
                        specs: tier.specifications || {},
                        status: tier.status || (cat.active !== false ? 'Published' : 'Draft'),
                        active: cat.active ?? true
                    });
                });
            }
        });

        let rawCategories = @json($categories);
        let categoriesDB = rawCategories.map(c => {
            let slug = c.slug;
            if (!slug) {
                const title = (c.title || '').toLowerCase();
                if (title.includes('kid') || title.includes('cozy')) slug = 'kids-cozy';
                else if (title.includes('proposal') || title.includes('anniversary')) slug = 'proposal-anniversary';
                else if (title.includes('dj') || title.includes('acoustic') || title.includes('sound')) slug = 'dj-acoustic';
                else if (title.includes('wedding') || title.includes('sangeet')) slug = 'weddings-sangeet';
                else if (title.includes('house') || title.includes('party')) slug = 'house-party';
                else if (title.includes('baby') || title.includes('corporate')) slug = 'baby-corporate';
                else slug = 'birthdays';
            }
            return {
                id: c.id,
                title: c.title,
                slug: slug,
                desc: c.desc || (c.title + ' celebration setups & event packages'),
                image: c.image || '/assets/images/hero/1.jpg',
                active: c.active !== false
            };
        });

        let currentCategorySearch = '';
        let currentCategoryStatusFilter = 'all';

        function handleCategorySearch(val) {
            currentCategorySearch = val.trim();
            renderCategories();
        }

        function handleCategoryStatusFilter(status) {
            currentCategoryStatusFilter = status;
            renderCategories();
        }

        function viewCategoryServices(slug) {
            switchTab('packages');
            setTimeout(() => {
                if (typeof handlePackageCategorySelect === 'function') {
                    handlePackageCategorySelect(slug);
                }
            }, 50);
        }
        let faqDB = @json($faqs);

        // -------------------------------------------------------------
        // ARTIZEN BOOKINGS PORTAL CONTROLLER (Category-Wise SaaS Engine)
        // -------------------------------------------------------------
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
        let currentBookingsPage = 1;
        const BOOKINGS_PAGE_SIZE = 20;
        let activeDrawerBookingId = null;

        function getBookingCategorySlug(b) {
            if (b.category_slug) return b.category_slug;
            const pkg = (b.package || '').toLowerCase();
            if (pkg.includes('kid') || pkg.includes('cozy') || pkg.includes('child')) return 'kids-cozy';
            if (pkg.includes('proposal') || pkg.includes('anniversary') || pkg.includes('romantic') || pkg.includes('couple')) return 'proposal-anniversary';
            if (pkg.includes('dj') || pkg.includes('acoustic') || pkg.includes('sound') || pkg.includes('music')) return 'dj-acoustic';
            if (pkg.includes('wedding') || pkg.includes('sangeet') || pkg.includes('haldi') || pkg.includes('mehendi') || pkg.includes('marriage')) return 'weddings-sangeet';
            if (pkg.includes('house') || pkg.includes('home') || pkg.includes('party')) return 'house-party';
            if (pkg.includes('baby') || pkg.includes('corporate') || pkg.includes('office')) return 'baby-corporate';
            if (pkg.includes('birthday') || pkg.includes('bday')) return 'birthdays';
            return 'birthdays';
        }

        function getBookingCategoryName(slug) {
            const cat = OFFICIAL_CATEGORIES.find(c => c.slug === slug);
            return cat ? cat.name : 'Birthdays';
        }

        function getStatusClass(status) {
            switch (status) {
                case 'Pending': return 'bg-amber-50 text-amber-800 border border-amber-200';
                case 'Contacted': return 'bg-blue-50 text-blue-800 border border-blue-200';
                case 'Confirmed': return 'bg-emerald-50 text-emerald-800 border border-emerald-200';
                case 'Completed': return 'bg-purple-50 text-purple-800 border border-purple-200';
                case 'Cancelled': return 'bg-red-50 text-red-800 border border-red-200';
                default: return 'bg-gray-100 text-gray-700 border border-gray-200';
            }
        }

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

        function handleBookingSearch(val) {
            currentBookingsSearch = val.trim();
            currentBookingsPage = 1;
            renderBookingsTables();
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
            currentBookingsPage = 1;
            
            const searchEl = document.getElementById('b-search-input');
            if (searchEl) searchEl.value = '';

            const sortEl = document.getElementById('b-sort-select');
            if (sortEl) sortEl.value = 'newest';

            renderBookingsTables();
        }

        function updateBookingStatus(bookingId, newStatus) {
            const booking = bookingsDB.find(b => b.id === bookingId);
            if (booking) {
                booking.status = newStatus;
                renderBookingsTables();
                updateOverviewMetrics();

                if (activeDrawerBookingId === bookingId) {
                    const badge = document.getElementById('drawer-status-badge');
                    if (badge) {
                        badge.className = `px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider ${getStatusClass(newStatus)}`;
                        badge.textContent = newStatus;
                    }
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
                        if (data.notify_url && (newStatus === 'Confirmed' || newStatus === 'Cancelled')) {
                            if (confirm(`Booking ${bookingId} status updated to ${newStatus}. Send WhatsApp notification to customer?`)) {
                                window.open(data.notify_url, '_blank');
                            }
                        }
                    } else {
                        alert("Error saving status change to backend!");
                    }
                });
            }
        }

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

        function renderBookingsTables() {
            // A. Update Overview Recent Table
            const recentTbody = document.getElementById('recent-bookings-tbody');
            if (recentTbody) {
                recentTbody.innerHTML = '';
                bookingsDB.slice(0, 4).forEach(b => {
                    const row = document.createElement('tr');
                    row.className = "hover:bg-gray-50 transition-colors border-b border-gray-100 text-gray-800";
                    row.innerHTML = `
                        <td class="py-3 px-4 font-mono font-bold text-gray-900">${b.id}</td>
                        <td class="py-3 px-4">
                            <span class="font-bold text-gray-900 block">${b.name}</span>
                            <a href="https://wa.me/91${b.mobile || b.whatsapp || ''}" target="_blank" class="text-[11px] text-emerald-600 font-semibold hover:underline inline-flex items-center gap-1 mt-0.5">
                                <i class="fa-brands fa-whatsapp"></i> +91 ${b.mobile || b.whatsapp || ''}
                            </a>
                        </td>
                        <td class="py-3 px-4 font-medium text-gray-900">${b.package}</td>
                        <td class="py-3 px-4 text-gray-600 font-medium">${b.date}</td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold ${getStatusClass(b.status)}">${b.status}</span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button onclick="openBookingDrawer('${b.id}')" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md text-[11px] font-semibold transition-colors">View</button>
                                <button onclick="updateBookingStatus('${b.id}', 'Confirmed')" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 rounded-md text-[11px] font-semibold transition-all">Confirm</button>
                            </div>
                        </td>
                    `;
                    recentTbody.appendChild(row);
                });
            }

            // B. Top Metric Summary Cards Calculation
            const totalCount = bookingsDB.length;
            const pendingCount = bookingsDB.filter(b => b.status === 'Pending').length;
            const confirmedCount = bookingsDB.filter(b => b.status === 'Confirmed').length;
            const pipelineVal = bookingsDB.reduce((sum, b) => sum + (parseInt(b.total) || 0), 0);

            const metricTotalEl = document.getElementById('b-metric-total');
            const metricPendingEl = document.getElementById('b-metric-pending');
            const metricConfirmedEl = document.getElementById('b-metric-confirmed');
            const metricPipelineEl = document.getElementById('b-metric-pipeline');

            if (metricTotalEl) metricTotalEl.textContent = totalCount;
            if (metricPendingEl) metricPendingEl.textContent = pendingCount;
            if (metricConfirmedEl) metricConfirmedEl.textContent = confirmedCount;
            if (metricPipelineEl) metricPipelineEl.textContent = `₹${pipelineVal.toLocaleString('en-IN')}`;

            // C. Category Distribution Progress Bar & Legend
            const distBarEl = document.getElementById('b-distribution-bar');
            const distLegendEl = document.getElementById('b-distribution-legend');
            const distLabelEl = document.getElementById('b-distribution-total-label');

            const colorPalette = [
                { bg: 'bg-amber-500', hex: '#F59E0B' },
                { bg: 'bg-blue-500', hex: '#3B82F6' },
                { bg: 'bg-emerald-500', hex: '#10B981' },
                { bg: 'bg-purple-500', hex: '#8B5CF6' },
                { bg: 'bg-rose-500', hex: '#F43F5E' },
                { bg: 'bg-indigo-500', hex: '#6366F1' },
                { bg: 'bg-cyan-500', hex: '#06B6D4' }
            ];

            if (distBarEl && distLegendEl) {
                distBarEl.innerHTML = '';
                distLegendEl.innerHTML = '';

                const catCounts = {};
                OFFICIAL_CATEGORIES.filter(c => c.slug !== 'all').forEach(c => {
                    catCounts[c.slug] = bookingsDB.filter(b => getBookingCategorySlug(b) === c.slug).length;
                });

                if (distLabelEl) distLabelEl.textContent = `${totalCount} Total Bookings`;

                OFFICIAL_CATEGORIES.filter(c => c.slug !== 'all').forEach((cat, idx) => {
                    const cnt = catCounts[cat.slug] || 0;
                    if (cnt > 0 && totalCount > 0) {
                        const pct = Math.round((cnt / totalCount) * 100);
                        const palette = colorPalette[idx % colorPalette.length];

                        // Bar Segment
                        const seg = document.createElement('div');
                        seg.className = `${palette.bg} h-full transition-all duration-300`;
                        seg.style.width = `${pct}%`;
                        seg.title = `${cat.name}: ${cnt} (${pct}%)`;
                        distBarEl.appendChild(seg);

                        // Legend Item
                        const leg = document.createElement('div');
                        leg.className = "flex items-center gap-1.5 cursor-pointer hover:underline";
                        leg.onclick = () => selectCategoryTab(cat.slug);
                        leg.innerHTML = `
                            <span class="w-2.5 h-2.5 rounded-full ${palette.bg}"></span>
                            <span>${cat.name}</span>
                            <span class="font-bold text-gray-900">(${cnt})</span>
                        `;
                        distLegendEl.appendChild(leg);
                    }
                });
            }

            // D. Primary Category Navigation Tabs Render
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
                        ? "px-3.5 py-2 rounded-lg text-xs font-bold shrink-0 transition-all flex items-center gap-2 bg-gray-900 text-white shadow-sm border border-gray-900"
                        : "px-3.5 py-2 rounded-lg text-xs font-semibold shrink-0 transition-all flex items-center gap-2 bg-white text-gray-700 hover:text-gray-900 border border-gray-200 hover:bg-gray-50";

                    btn.innerHTML = `
                        <i class="${cat.icon} text-xs"></i>
                        <span>${cat.name}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${isActive ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600'}">${count}</span>
                    `;
                    tabsContainer.appendChild(btn);
                });
            }

            // E. Selected Category Header Text
            const catTitleEl = document.getElementById('b-selected-cat-title');
            const catDescEl = document.getElementById('b-selected-cat-desc');

            const filteredByCat = (currentBookingsCategory === 'all')
                ? bookingsDB
                : bookingsDB.filter(b => getBookingCategorySlug(b) === currentBookingsCategory);

            if (catTitleEl) {
                catTitleEl.textContent = currentBookingsCategory === 'all'
                    ? 'All Bookings'
                    : `${getBookingCategoryName(currentBookingsCategory)} Bookings`;
            }
            if (catDescEl) {
                catDescEl.textContent = `${filteredByCat.length} bookings found in this view`;
            }

            // F. Secondary Status Filter Pills Render (Scoped to selected category)
            const statusPillsContainer = document.getElementById('b-status-pills-container');
            if (statusPillsContainer) {
                statusPillsContainer.innerHTML = '';
                const statuses = ['all', 'Pending', 'Contacted', 'Confirmed', 'Completed', 'Cancelled'];

                statuses.forEach(st => {
                    const count = (st === 'all')
                        ? filteredByCat.length
                        : filteredByCat.filter(b => b.status === st).length;

                    const isActive = (currentBookingsStatus === st);
                    const pill = document.createElement('button');
                    pill.type = 'button';
                    pill.onclick = () => filterBookingStatus(st);
                    pill.className = isActive
                        ? "px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1.5 bg-amber-100 text-amber-900 border border-amber-300"
                        : "px-3 py-1.5 rounded-lg text-xs font-medium uppercase tracking-wider transition-all flex items-center gap-1.5 bg-gray-100 text-gray-600 hover:text-gray-900 hover:bg-gray-200 border border-gray-200";

                    pill.innerHTML = `
                        <span>${st === 'all' ? 'All Status' : st}</span>
                        <span class="font-bold">(${count})</span>
                    `;
                    statusPillsContainer.appendChild(pill);
                });
            }

            // G. Active Filters Indicator Bar
            const filtersBar = document.getElementById('b-active-filters-bar');
            const filtersText = document.getElementById('b-active-filters-text');
            const isFilterActive = (currentBookingsCategory !== 'all' || currentBookingsStatus !== 'all' || currentBookingsSearch !== '');

            if (filtersBar) {
                if (isFilterActive) {
                    filtersBar.classList.remove('hidden');
                    filtersBar.classList.add('flex');
                    let activeParts = [];
                    if (currentBookingsCategory !== 'all') activeParts.push(`Category: ${getBookingCategoryName(currentBookingsCategory)}`);
                    if (currentBookingsStatus !== 'all') activeParts.push(`Status: ${currentBookingsStatus}`);
                    if (currentBookingsSearch !== '') activeParts.push(`Search: "${currentBookingsSearch}"`);
                    if (filtersText) filtersText.textContent = `Active Filters: ${activeParts.join(' | ')}`;
                } else {
                    filtersBar.classList.add('hidden');
                    filtersBar.classList.remove('flex');
                }
            }

            // H. Apply Status & Search Filtering
            let resultData = filteredByCat;
            if (currentBookingsStatus !== 'all') {
                resultData = resultData.filter(b => b.status === currentBookingsStatus);
            }
            if (currentBookingsSearch !== '') {
                const lc = currentBookingsSearch.toLowerCase();
                resultData = resultData.filter(b =>
                    (b.name && b.name.toLowerCase().includes(lc)) ||
                    (b.id && b.id.toLowerCase().includes(lc)) ||
                    (b.mobile && b.mobile.includes(lc)) ||
                    (b.email && b.email.toLowerCase().includes(lc)) ||
                    (b.package && b.package.toLowerCase().includes(lc)) ||
                    (b.zone && b.zone.toLowerCase().includes(lc))
                );
            }

            // I. Apply Sorting
            resultData.sort((a, b) => {
                if (currentBookingsSort === 'oldest') {
                    return (a.id || '').localeCompare(b.id || '');
                } else if (currentBookingsSort === 'highest_val') {
                    return (parseInt(b.total) || 0) - (parseInt(a.total) || 0);
                } else if (currentBookingsSort === 'lowest_val') {
                    return (parseInt(a.total) || 0) - (parseInt(b.total) || 0);
                } else {
                    // Default: newest
                    return (b.id || '').localeCompare(a.id || '');
                }
            });

            // J. Pagination Calculations
            const totalItems = resultData.length;
            const totalPages = Math.ceil(totalItems / BOOKINGS_PAGE_SIZE) || 1;
            if (currentBookingsPage > totalPages) currentBookingsPage = totalPages;

            const startIndex = (currentBookingsPage - 1) * BOOKINGS_PAGE_SIZE;
            const pageData = resultData.slice(startIndex, startIndex + BOOKINGS_PAGE_SIZE);

            // K. Handle Empty States vs Table Render
            const mainTbody = document.getElementById('bookings-main-tbody');
            const mobileContainer = document.getElementById('bookings-mobile-container');
            const emptyStateEl = document.getElementById('b-empty-state');

            if (totalItems === 0) {
                if (mainTbody) mainTbody.innerHTML = '';
                if (mobileContainer) mobileContainer.innerHTML = '';
                if (emptyStateEl) emptyStateEl.classList.remove('hidden'), emptyStateEl.classList.add('flex');
            } else {
                if (emptyStateEl) emptyStateEl.classList.add('hidden'), emptyStateEl.classList.remove('flex');

                // Render Desktop Table Rows
                if (mainTbody) {
                    mainTbody.innerHTML = '';
                    pageData.forEach(b => {
                        const catSlug = getBookingCategorySlug(b);
                        const catName = getBookingCategoryName(catSlug);
                        const advanceAmt = b.advance ? `Advance ₹${parseInt(b.advance).toLocaleString('en-IN')}` : 'Advance Pending';
                        const totalFormatted = `₹${(parseInt(b.total) || 0).toLocaleString('en-IN')}`;

                        const row = document.createElement('tr');
                        row.className = "hover:bg-gray-50 transition-colors border-b border-gray-200 text-gray-800";
                        row.innerHTML = `
                            <td class="py-3.5 px-4 font-mono font-bold text-gray-900">
                                <span>${b.id}</span>
                                <span class="block text-[11px] text-gray-400 font-sans font-normal mt-0.5">${b.created_at || b.date}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-gray-900 block">${b.name}</span>
                                <a href="https://wa.me/91${b.mobile || b.whatsapp || ''}" target="_blank" class="text-[11px] text-emerald-600 font-semibold hover:underline inline-flex items-center gap-1 mt-0.5">
                                    <i class="fa-brands fa-whatsapp"></i> +91 ${b.mobile || b.whatsapp || ''}
                                </a>
                                ${b.email ? `<span class="block text-[11px] text-gray-400 font-normal">${b.email}</span>` : ''}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-gray-900 block leading-snug">${b.package}</span>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-0.5 rounded bg-gray-100 border border-gray-200 text-[10px] font-semibold text-gray-700">${catName}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-gray-900 block">${b.date}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-gray-900 block">${b.zone || 'Indore'}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-gray-900 block">${totalFormatted}</span>
                                <span class="text-[11px] text-gray-500 font-medium block mt-0.5">${advanceAmt}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold ${getStatusClass(b.status)}">${b.status}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button onclick="openBookingDrawer('${b.id}')" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-md text-[11px] font-semibold transition-colors cursor-pointer">View</button>
                                    <a href="https://wa.me/91${b.mobile || b.whatsapp || ''}" target="_blank" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 rounded-md text-[11px] font-semibold transition-all">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </a>
                                </div>
                            </td>
                        `;
                        mainTbody.appendChild(row);
                    });
                }

                // Render Mobile Cards
                if (mobileContainer) {
                    mobileContainer.innerHTML = '';
                    pageData.forEach(b => {
                        const catSlug = getBookingCategorySlug(b);
                        const catName = getBookingCategoryName(catSlug);
                        const totalFormatted = `₹${(parseInt(b.total) || 0).toLocaleString('en-IN')}`;

                        const card = document.createElement('div');
                        card.className = "bg-white border border-gray-200 p-4 rounded-xl shadow-sm flex flex-col gap-3 text-left text-xs";
                        card.innerHTML = `
                            <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                                <span class="font-mono font-bold text-amber-700">${b.id}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold ${getStatusClass(b.status)}">${b.status}</span>
                            </div>
                            <div>
                                <span class="text-sm font-bold text-gray-900 block">${b.name}</span>
                                <a href="https://wa.me/91${b.mobile || b.whatsapp || ''}" target="_blank" class="text-xs text-emerald-600 font-semibold hover:underline inline-flex items-center gap-1 mt-0.5">
                                    <i class="fa-brands fa-whatsapp"></i> +91 ${b.mobile || b.whatsapp || ''}
                                </a>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 p-2.5 rounded-lg flex flex-col gap-1">
                                <span class="font-bold text-gray-900">${b.package}</span>
                                <div class="flex items-center justify-between text-[11px] text-gray-500 font-medium mt-1">
                                    <span>${catName}</span>
                                    <span>${b.date}</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-bold text-gray-900 block">${totalFormatted}</span>
                                    <span class="text-[10px] text-gray-500">${b.zone || 'Indore'}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button onclick="openBookingDrawer('${b.id}')" class="px-3 py-1.5 bg-gray-900 text-white rounded-lg text-xs font-semibold shadow-sm cursor-pointer">View Details</button>
                                </div>
                            </div>
                        `;
                        mobileContainer.appendChild(card);
                    });
                }
            }

            // L. Update Pagination Controls UI
            const pagInfoEl = document.getElementById('b-pagination-info');
            const pagControlsEl = document.getElementById('b-pagination-controls');

            if (pagInfoEl) {
                const endItem = Math.min(startIndex + BOOKINGS_PAGE_SIZE, totalItems);
                pagInfoEl.textContent = totalItems === 0 ? 'Showing 0 of 0 bookings' : `Showing ${startIndex + 1} to ${endItem} of ${totalItems} bookings`;
            }

            if (pagControlsEl) {
                pagControlsEl.innerHTML = '';
                if (totalPages > 1) {
                    // Prev button
                    const prevBtn = document.createElement('button');
                    prevBtn.type = 'button';
                    prevBtn.disabled = (currentBookingsPage === 1);
                    prevBtn.onclick = () => { if (currentBookingsPage > 1) { currentBookingsPage--; renderBookingsTables(); } };
                    prevBtn.className = "px-2.5 py-1 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed text-xs font-semibold";
                    prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
                    pagControlsEl.appendChild(prevBtn);

                    // Page number buttons
                    for (let p = 1; p <= totalPages; p++) {
                        const pageBtn = document.createElement('button');
                        pageBtn.type = 'button';
                        pageBtn.onclick = () => { currentBookingsPage = p; renderBookingsTables(); };
                        pageBtn.className = p === currentBookingsPage
                            ? "px-2.5 py-1 rounded-md bg-gray-900 text-white font-bold text-xs"
                            : "px-2.5 py-1 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-xs font-semibold";
                        pageBtn.textContent = p;
                        pagControlsEl.appendChild(pageBtn);
                    }

                    // Next button
                    const nextBtn = document.createElement('button');
                    nextBtn.type = 'button';
                    nextBtn.disabled = (currentBookingsPage === totalPages);
                    nextBtn.onclick = () => { if (currentBookingsPage < totalPages) { currentBookingsPage++; renderBookingsTables(); } };
                    nextBtn.className = "px-2.5 py-1 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed text-xs font-semibold";
                    nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
                    pagControlsEl.appendChild(nextBtn);
                }
            }
        }

        // Drawer Handlers
        function openBookingDrawer(bookingId) {
            const b = bookingsDB.find(item => item.id === bookingId);
            if (!b) return;

            activeDrawerBookingId = bookingId;

            const drawer = document.getElementById('booking-detail-drawer');
            const overlay = document.getElementById('booking-drawer-overlay');

            document.getElementById('drawer-booking-id').textContent = b.id;
            document.getElementById('drawer-customer-title').textContent = b.name;
            document.getElementById('drawer-customer-name').textContent = b.name;
            document.getElementById('drawer-customer-mobile').textContent = `+91 ${b.mobile || b.whatsapp || '--'}`;
            document.getElementById('drawer-customer-email').textContent = b.email || 'Not provided';
            
            const waLink = document.getElementById('drawer-whatsapp-link');
            if (waLink) waLink.href = `https://wa.me/91${b.mobile || b.whatsapp || ''}`;

            document.getElementById('drawer-package-title').textContent = b.package;
            document.getElementById('drawer-package-tier').textContent = `${b.tier || 'Standard'} (${getBookingCategoryName(getBookingCategorySlug(b))})`;

            document.getElementById('drawer-event-date').textContent = b.date || 'TBD';
            document.getElementById('drawer-venue-zone').textContent = b.zone || 'Indore';

            document.getElementById('drawer-price-total').textContent = `₹${(parseInt(b.total) || 0).toLocaleString('en-IN')}`;
            document.getElementById('drawer-price-surcharge').textContent = `₹${(parseInt(b.surcharge) || 0).toLocaleString('en-IN')}`;

            const badge = document.getElementById('drawer-status-badge');
            if (badge) {
                badge.className = `px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider ${getStatusClass(b.status)}`;
                badge.textContent = b.status;
            }

            // Action buttons inside drawer
            const actionsBox = document.getElementById('drawer-action-buttons');
            if (actionsBox) {
                actionsBox.innerHTML = `
                    <button onclick="updateBookingStatus('${b.id}', 'Contacted')" class="px-3 py-2 bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white border border-blue-200 rounded-lg font-bold transition-all text-xs cursor-pointer">Mark Contacted</button>
                    <button onclick="updateBookingStatus('${b.id}', 'Confirmed')" class="px-3 py-2 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 rounded-lg font-bold transition-all text-xs cursor-pointer">Confirm Event</button>
                    <button onclick="updateBookingStatus('${b.id}', 'Completed')" class="px-3 py-2 bg-purple-50 hover:bg-purple-600 text-purple-700 hover:text-white border border-purple-200 rounded-lg font-bold transition-all text-xs cursor-pointer">Mark Completed</button>
                    <button onclick="updateBookingStatus('${b.id}', 'Cancelled')" class="px-3 py-2 bg-red-50 hover:bg-red-600 text-red-700 hover:text-white border border-red-200 rounded-lg font-bold transition-all text-xs cursor-pointer">Cancel Request</button>
                `;
            }

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

        // -------------------------------------------------------------
        // ARTIZEN PACKAGES INDEX CONTROLLER (Category-Wise SaaS Engine)
        // -------------------------------------------------------------
        const PACKAGE_OFFICIAL_CATEGORIES = [
            { slug: 'all', name: 'All Services', desc: 'All Artizen celebration setups & packages', icon: 'fa-solid fa-layer-group' },
            { slug: 'birthdays', name: 'Birthdays', desc: 'Birthday celebration setups', icon: 'fa-solid fa-cake-candles' },
            { slug: 'kids-cozy', name: 'Kids & Cozy', desc: 'Kids and cozy event setups', icon: 'fa-solid fa-child-reaching' },
            { slug: 'house-party', name: 'House Party', desc: 'House party & private setups', icon: 'fa-solid fa-house-chimney-user' },
            { slug: 'proposal-anniversary', name: 'Proposal & Anniversary', desc: 'Proposal & anniversary romantic setups', icon: 'fa-solid fa-heart' },
            { slug: 'dj-acoustic', name: 'DJ & Acoustic', desc: 'DJ, sound & acoustic setups', icon: 'fa-solid fa-compact-disc' },
            { slug: 'weddings-sangeet', name: 'Weddings & Sangeet', desc: 'Wedding, sangeet & haldi setups', icon: 'fa-solid fa-ring' },
            { slug: 'baby-corporate', name: 'Baby & Corporate', desc: 'Baby shower & corporate event setups', icon: 'fa-solid fa-building-user' }
        ];

        let currentPackageCategory = 'all';
        let currentPackageStatus = 'all';
        let currentPackageSearch = '';
        let currentPackageSort = 'newest';

        function getPackageCategorySlug(p) {
            if (p.category_slug) return p.category_slug;
            const catName = (p.categoryName || '').toLowerCase();
            const title = (p.title || '').toLowerCase();

            if (catName.includes('kid') || catName.includes('cozy') || title.includes('kid') || title.includes('cozy')) return 'kids-cozy';
            if (catName.includes('proposal') || catName.includes('anniversary') || title.includes('proposal') || title.includes('anniversary')) return 'proposal-anniversary';
            if (catName.includes('dj') || catName.includes('sound') || catName.includes('acoustic') || title.includes('dj') || title.includes('acoustic')) return 'dj-acoustic';
            if (catName.includes('wedding') || catName.includes('sangeet') || catName.includes('haldi') || title.includes('wedding') || title.includes('sangeet')) return 'weddings-sangeet';
            if (catName.includes('house') || catName.includes('party') || title.includes('house')) return 'house-party';
            if (catName.includes('baby') || catName.includes('corporate') || title.includes('baby') || title.includes('corporate')) return 'baby-corporate';
            if (catName.includes('birthday') || title.includes('birthday') || title.includes('bday')) return 'birthdays';
            return 'birthdays';
        }

        function handlePackageCategorySelect(slug) {
            currentPackageCategory = slug;
            renderPackages();
        }

        function handlePackageStatusFilter(status) {
            currentPackageStatus = status;
            renderPackages();
        }

        function handlePackageSearch(val) {
            currentPackageSearch = val.trim();
            renderPackages();
        }

        function handlePackageSortChange(val) {
            currentPackageSort = val;
            renderPackages();
        }

        function resetAllPackageFilters() {
            currentPackageCategory = 'all';
            currentPackageStatus = 'all';
            currentPackageSearch = '';
            currentPackageSort = 'newest';

            const searchEl = document.getElementById('p-search-input');
            if (searchEl) searchEl.value = '';

            const sortEl = document.getElementById('p-sort-select');
            if (sortEl) sortEl.value = 'newest';

            renderPackages();
        }

        function confirmDeletePackage(catId, tierIndex, serviceTitle) {
            const modal = document.getElementById('p-delete-modal');
            const form = document.getElementById('p-delete-form');
            const titleSpan = document.getElementById('p-delete-service-name');

            if (titleSpan) titleSpan.textContent = `"${serviceTitle}"`;
            if (form) form.action = `/admin/packages/delete/${catId}/${tierIndex}`;

            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.remove('pointer-events-none');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modal.querySelector('.transform').classList.remove('scale-95');
                }, 10);
            }
        }

        function closePackageDeleteModal() {
            const modal = document.getElementById('p-delete-modal');
            if (modal) {
                modal.classList.add('opacity-0');
                modal.querySelector('.transform').classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.add('pointer-events-none');
                }, 300);
            }
        }

        function renderPackages() {
            const container = document.getElementById('packages-sections-container');
            if (!container) return;

            // 1. Calculate Metrics Across All Packages
            const totalCount = packagesDB.length;
            const categoriesCoveredSet = new Set(packagesDB.map(p => getPackageCategorySlug(p)));
            const publishedCount = packagesDB.filter(p => p.status === 'Published' || p.active !== false).length;
            const avgPriceVal = totalCount > 0 ? Math.round(packagesDB.reduce((sum, p) => sum + (p.price || 0), 0) / totalCount) : 0;

            const mTotal = document.getElementById('p-metric-total');
            const mCat = document.getElementById('p-metric-categories');
            const mAvg = document.getElementById('p-metric-avg-price');
            const mPub = document.getElementById('p-metric-published');

            if (mTotal) mTotal.textContent = totalCount;
            if (mCat) mCat.textContent = categoriesCoveredSet.size;
            if (mAvg) mAvg.textContent = `₹${avgPriceVal.toLocaleString('en-IN')}`;
            if (mPub) mPub.textContent = publishedCount;

            // 2. Render Category Navigation Tabs with dynamic counts
            const tabsContainer = document.getElementById('p-category-tabs-container');
            if (tabsContainer) {
                tabsContainer.innerHTML = '';
                PACKAGE_OFFICIAL_CATEGORIES.forEach(cat => {
                    let catCount = 0;
                    if (cat.slug === 'all') {
                        catCount = totalCount;
                    } else {
                        catCount = packagesDB.filter(p => getPackageCategorySlug(p) === cat.slug).length;
                    }

                    const isSelected = (currentPackageCategory === cat.slug);
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.onclick = () => handlePackageCategorySelect(cat.slug);
                    btn.className = `px-3.5 py-2 rounded-lg text-xs font-semibold shrink-0 transition-all flex items-center gap-2 cursor-pointer ${
                        isSelected 
                            ? 'bg-gray-900 text-white shadow-sm font-bold' 
                            : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
                    }`;
                    btn.innerHTML = `
                        <i class="${cat.icon} text-xs"></i>
                        <span>${cat.name}</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] ${isSelected ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600'} font-bold">${catCount}</span>
                    `;
                    tabsContainer.appendChild(btn);
                });
            }

            // 3. Render Status Filter Pills
            const statusPillsContainer = document.getElementById('p-status-pills-container');
            if (statusPillsContainer) {
                statusPillsContainer.innerHTML = '';
                const statuses = [
                    { id: 'all', label: 'All' },
                    { id: 'Published', label: 'Published' },
                    { id: 'Draft', label: 'Draft' },
                    { id: 'Archived', label: 'Archived' }
                ];
                statuses.forEach(st => {
                    const isSel = (currentPackageStatus === st.id);
                    const pill = document.createElement('button');
                    pill.type = 'button';
                    pill.onclick = () => handlePackageStatusFilter(st.id);
                    pill.className = `px-2.5 py-1 rounded-md text-xs font-semibold transition-all cursor-pointer ${
                        isSel ? 'bg-white text-gray-900 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900'
                    }`;
                    pill.textContent = st.label;
                    statusPillsContainer.appendChild(pill);
                });
            }

            // 4. Update Active Filters Bar Indicator
            const activeFiltersBar = document.getElementById('p-active-filters-bar');
            const activeFiltersText = document.getElementById('p-active-filters-text');
            const isFiltered = (currentPackageCategory !== 'all' || currentPackageStatus !== 'all' || currentPackageSearch !== '');
            if (activeFiltersBar) {
                if (isFiltered) {
                    activeFiltersBar.classList.remove('hidden');
                    activeFiltersBar.classList.add('flex');
                    let filterParts = [];
                    if (currentPackageCategory !== 'all') {
                        const cObj = PACKAGE_OFFICIAL_CATEGORIES.find(c => c.slug === currentPackageCategory);
                        filterParts.push(`Category: ${cObj ? cObj.name : currentPackageCategory}`);
                    }
                    if (currentPackageStatus !== 'all') filterParts.push(`Status: ${currentPackageStatus}`);
                    if (currentPackageSearch) filterParts.push(`Search: "${currentPackageSearch}"`);
                    if (activeFiltersText) activeFiltersText.textContent = `Filtered by ${filterParts.join(' • ')}`;
                } else {
                    activeFiltersBar.classList.add('hidden');
                    activeFiltersBar.classList.remove('flex');
                }
            }

            // 5. Filter & Sort Packages Data
            let filtered = packagesDB.filter(p => {
                const catSlug = getPackageCategorySlug(p);
                // Category Filter
                if (currentPackageCategory !== 'all' && catSlug !== currentPackageCategory) {
                    return false;
                }
                // Status Filter
                if (currentPackageStatus !== 'all') {
                    const st = p.status || (p.active !== false ? 'Published' : 'Draft');
                    if (st !== currentPackageStatus) return false;
                }
                // Search Filter
                if (currentPackageSearch) {
                    const query = currentPackageSearch.toLowerCase();
                    const title = (p.title || '').toLowerCase();
                    const catName = (p.categoryName || '').toLowerCase();
                    const desc = (p.desc || '').toLowerCase();
                    const tierName = (p.tierName || '').toLowerCase();
                    const priceStr = (p.price || '').toString();
                    const incsStr = (p.inclusions || []).join(' ').toLowerCase();

                    const matches = title.includes(query) || catName.includes(query) || desc.includes(query) || tierName.includes(query) || priceStr.includes(query) || incsStr.includes(query);
                    if (!matches) return false;
                }
                return true;
            });

            // Apply Sorting
            filtered.sort((a, b) => {
                if (currentPackageSort === 'newest') return (b.tierIndex || 0) - (a.tierIndex || 0);
                if (currentPackageSort === 'oldest') return (a.tierIndex || 0) - (b.tierIndex || 0);
                if (currentPackageSort === 'price_low') return (a.price || 0) - (b.price || 0);
                if (currentPackageSort === 'price_high') return (b.price || 0) - (a.price || 0);
                if (currentPackageSort === 'name_asc') return (a.title || '').localeCompare(b.title || '');
                if (currentPackageSort === 'name_desc') return (b.title || '').localeCompare(a.title || '');
                return 0;
            });

            // 6. Group Packages by Official Categories
            const categoriesToRender = (currentPackageCategory === 'all')
                ? PACKAGE_OFFICIAL_CATEGORIES.filter(c => c.slug !== 'all')
                : PACKAGE_OFFICIAL_CATEGORIES.filter(c => c.slug === currentPackageCategory);

            container.innerHTML = '';

            if (filtered.length === 0) {
                container.innerHTML = `
                    <div class="bg-white border border-gray-200 p-12 rounded-xl text-center flex flex-col items-center justify-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">No Services Found</h3>
                        <p class="text-xs text-gray-500 max-w-sm">No packages match your search criteria or active filters.</p>
                        <button type="button" onclick="resetAllPackageFilters()" class="mt-2 px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg shadow-sm">
                            Clear Filters
                        </button>
                    </div>
                `;
                return;
            }

            // Render each category section independently
            categoriesToRender.forEach(cat => {
                const catPackages = filtered.filter(p => getPackageCategorySlug(p) === cat.slug);
                if (catPackages.length === 0 && currentPackageCategory === 'all') {
                    return; // Skip empty categories when viewing All Services
                }

                const section = document.createElement('div');
                section.className = "flex flex-col gap-4";

                // Section Header
                section.innerHTML = `
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-200 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-800 text-xs shrink-0">
                                <i class="${cat.icon}"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 leading-tight">${cat.name}</h3>
                                <p class="text-xs text-gray-500 font-normal mt-0.5">${cat.desc}</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full w-max">${catPackages.length} ${catPackages.length === 1 ? 'Service' : 'Services'}</span>
                    </div>
                `;

                // Section Cards Grid
                const grid = document.createElement('div');
                grid.className = "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6";

                catPackages.forEach(p => {
                    const priceFormatted = `₹${(p.price || 0).toLocaleString('en-IN')}`;
                    const mrpFormatted = p.originalPrice ? `₹${p.originalPrice.toLocaleString('en-IN')}` : null;
                    const statusVal = p.status || (p.active !== false ? 'Published' : 'Draft');

                    let statusBadgeClass = 'bg-gray-100 text-gray-700 border-gray-200';
                    if (statusVal === 'Published') statusBadgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                    else if (statusVal === 'Draft') statusBadgeClass = 'bg-amber-50 text-amber-800 border-amber-200';
                    else if (statusVal === 'Archived') statusBadgeClass = 'bg-gray-100 text-gray-600 border-gray-200';

                    const displayInclusions = (p.inclusions || []).slice(0, 4);
                    const overflowCount = (p.inclusions || []).length - displayInclusions.length;

                    // Build specifications list
                    let specsText = '';
                    if (p.specs && typeof p.specs === 'object') {
                        let parts = [];
                        if (p.specs.guest_capacity) parts.push(`${p.specs.guest_capacity} Guests`);
                        if (p.specs.setup_time) parts.push(`${p.specs.setup_time} Setup`);
                        if (p.specs.duration) parts.push(`${p.specs.duration} Event`);
                        if (parts.length > 0) specsText = parts.join(' • ');
                    }

                    const card = document.createElement('div');
                    card.className = "bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm flex flex-col justify-between hover:border-gray-400 transition-all text-left";
                    
                    card.innerHTML = `
                        <div>
                            <!-- Card Image Container -->
                            <div class="h-36 relative overflow-hidden bg-gray-100 border-b border-gray-100">
                                <img src="${p.image}" alt="${p.title}" class="w-full h-full object-cover" onerror="this.src='/assets/images/hero/1.jpg'">
                                <span class="absolute top-2.5 left-2.5 bg-black/70 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">
                                    ${p.categoryName}
                                </span>
                                <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border ${statusBadgeClass}">
                                    ${statusVal}
                                </span>
                            </div>

                            <!-- Card Body Content -->
                            <div class="p-4 flex flex-col gap-2">
                                <!-- Package Title & Tier -->
                                <h4 class="text-base font-bold text-gray-900 leading-tight block mb-0.5">${p.title}</h4>
                                ${p.tierName ? `<span class="text-xs font-semibold text-amber-700 block mb-2">${p.tierName}</span>` : ''}

                                <!-- Price Display -->
                                <div class="flex items-baseline gap-2 mb-2 bg-gray-50 border border-gray-200 p-2.5 rounded-lg">
                                    <span class="text-xl font-extrabold text-gray-900">${priceFormatted}</span>
                                    ${mrpFormatted ? `<span class="text-xs text-gray-400 font-medium line-through">MRP ${mrpFormatted}</span>` : ''}
                                </div>

                                <!-- Inclusions Preview -->
                                ${displayInclusions.length > 0 ? `
                                    <div class="border-t border-gray-100 pt-2 mb-2">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Inclusions</span>
                                        <ul class="flex flex-col gap-1 text-xs text-gray-700 font-medium">
                                            ${displayInclusions.map(inc => `<li class="flex items-start gap-1.5 text-gray-800"><i class="fa-solid fa-check text-emerald-600 text-xs mt-0.5 shrink-0"></i> <span>${inc}</span></li>`).join('')}
                                        </ul>
                                        ${overflowCount > 0 ? `<span class="text-[11px] font-semibold text-gray-500 block mt-1">+ ${overflowCount} more inclusions</span>` : ''}
                                    </div>
                                ` : ''}

                                <!-- Specifications Summary -->
                                ${specsText ? `
                                    <div class="bg-gray-50 border border-gray-200 px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-600 mb-1 flex items-center gap-1.5">
                                        <i class="fa-solid fa-sliders text-gray-400 text-xs shrink-0"></i>
                                        <span>${specsText}</span>
                                    </div>
                                ` : ''}
                            </div>
                        </div>

                        <!-- Card Footer Actions -->
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between mt-auto">
                            <a href="/admin/packages/edit/${p.catId}/${p.tierIndex}" class="px-3.5 py-1.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg shadow-sm transition-all flex items-center gap-1.5">
                                <i class="fa-solid fa-pen-to-square text-xs"></i> Edit
                            </a>
                            <button type="button" onclick="confirmDeletePackage('${p.catId}', ${p.tierIndex}, '${p.title.replace(/'/g, "\\'")}')" class="px-3.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-trash-can text-xs"></i> Delete
                            </button>
                        </div>
                    `;

                    grid.appendChild(card);
                });

                section.appendChild(grid);
                container.appendChild(section);
            });
        }

        function toggleCategoryStatus(catId) {
            const cat = categoriesDB.find(c => c.id === catId);
            if (cat) {
                cat.active = !cat.active;
                renderCategories();
            }
        }

        function renderCategories() {
            const grid = document.getElementById('categories-grid');
            if (!grid) return;

            // 1. Calculate Summary Metrics
            const totalCategories = categoriesDB.length;
            const activeCategories = categoriesDB.filter(c => c.active).length;
            const inactiveCategories = totalCategories - activeCategories;
            const totalServices = packagesDB.length;

            const mTot = document.getElementById('c-metric-total');
            const mAct = document.getElementById('c-metric-active');
            const mInact = document.getElementById('c-metric-inactive');
            const mServ = document.getElementById('c-metric-services');

            if (mTot) mTot.textContent = totalCategories;
            if (mAct) mAct.textContent = activeCategories;
            if (mInact) mInact.textContent = inactiveCategories;
            if (mServ) mServ.textContent = totalServices;

            // 2. Render Status Filter Pills
            const statusContainer = document.getElementById('c-status-pills-container');
            if (statusContainer) {
                statusContainer.innerHTML = '';
                const pills = [
                    { id: 'all', label: 'All' },
                    { id: 'active', label: 'Active' },
                    { id: 'inactive', label: 'Inactive' }
                ];
                pills.forEach(p => {
                    const isSel = (currentCategoryStatusFilter === p.id);
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.onclick = () => handleCategoryStatusFilter(p.id);
                    btn.className = `px-3 py-1 rounded-md text-xs font-semibold transition-all cursor-pointer ${
                        isSel ? 'bg-white text-gray-900 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900'
                    }`;
                    btn.textContent = p.label;
                    statusContainer.appendChild(btn);
                });
            }

            // 3. Filter Categories Data
            let filtered = categoriesDB.filter(c => {
                // Status Filter
                if (currentCategoryStatusFilter === 'active' && !c.active) return false;
                if (currentCategoryStatusFilter === 'inactive' && c.active) return false;

                // Search Filter
                if (currentCategorySearch) {
                    const q = currentCategorySearch.toLowerCase();
                    const matchTitle = (c.title || '').toLowerCase().includes(q);
                    const matchSlug = (c.slug || '').toLowerCase().includes(q);
                    const matchDesc = (c.desc || '').toLowerCase().includes(q);
                    if (!matchTitle && !matchSlug && !matchDesc) return false;
                }
                return true;
            });

            grid.innerHTML = '';

            if (filtered.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-full bg-white border border-gray-200 p-12 rounded-xl text-center flex flex-col items-center justify-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-tags"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">No Categories Found</h3>
                        <p class="text-xs text-gray-500 max-w-sm">No categories match your search criteria or active status filter.</p>
                        <button type="button" onclick="currentCategorySearch=''; currentCategoryStatusFilter='all'; const s=document.getElementById('c-search-input'); if(s)s.value=''; renderCategories();" class="mt-2 px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg shadow-sm">
                            Clear Filters
                        </button>
                    </div>
                `;
                return;
            }

            // 4. Render Category Cards
            filtered.forEach(c => {
                // Dynamically calculate actual package/service count from packagesDB
                const packageCount = packagesDB.filter(p => getPackageCategorySlug(p) === c.slug).length;

                const card = document.createElement('div');
                card.className = "bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm flex flex-col justify-between hover:border-gray-400 transition-all text-left";
                
                card.innerHTML = `
                    <div>
                        <!-- Cover Image Container (16:9) -->
                        <div class="aspect-[16/9] relative overflow-hidden bg-gray-100 border-b border-gray-100">
                            <img src="${c.image}" alt="${c.title}" class="w-full h-full object-cover" onerror="this.src='/assets/images/hero/1.jpg'">
                            
                            <!-- Top Left Badge: Service Count -->
                            <span class="absolute top-2.5 left-2.5 bg-black/70 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">
                                ${packageCount} ${packageCount === 1 ? 'Service' : 'Services'}
                            </span>

                            <!-- Top Right Badge: Status Pill -->
                            <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border ${
                                c.active ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-200'
                            }">
                                ${c.active ? '● Active' : '● Inactive'}
                            </span>
                        </div>

                        <!-- Card Body Content -->
                        <div class="p-4 flex flex-col gap-2">
                            <!-- Category Title -->
                            <h3 class="text-base font-bold text-gray-900 leading-tight block mb-0.5">${c.title}</h3>
                            
                            <!-- Description -->
                            <p class="text-xs text-gray-500 font-normal leading-relaxed">${c.desc}</p>

                            <!-- Monospace Slug Identifier -->
                            <div class="mt-1 font-mono text-[11px] text-gray-400 bg-gray-50 border border-gray-200 px-2 py-1 rounded w-max">
                                slug: <span class="text-gray-700 font-semibold">${c.slug}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-2 mt-auto">
                        <button type="button" onclick="viewCategoryServices('${c.slug}')" class="px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-box-open text-xs text-gray-500"></i>
                            <span>View Services (${packageCount})</span>
                        </button>

                        <div class="flex items-center gap-2">
                            <button type="button" onclick="openCategoryModal(${c.id})" class="px-3 py-1.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg shadow-sm transition-all flex items-center gap-1 cursor-pointer" title="Edit Category">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                <span>Edit</span>
                            </button>

                            <!-- Status Toggle Button -->
                            <button type="button" onclick="toggleCategoryStatus(${c.id})" class="w-8 h-4 rounded-full ${c.active ? 'bg-emerald-500' : 'bg-gray-200'} relative flex items-center px-0.5 cursor-pointer transition-all shrink-0" title="${c.active ? 'Deactivate Category' : 'Activate Category'}">
                                <span class="w-3 h-3 rounded-full bg-white absolute ${c.active ? 'right-0.5' : 'left-0.5'} transition-all shadow-sm"></span>
                            </button>
                        </div>
                    </div>
                `;

                grid.appendChild(card);
            });
        }

        function autoGenerateCategorySlug(val) {
            const slugInput = document.getElementById('cat-slug-input');
            const catIdInput = document.getElementById('cat-id-input');
            if (slugInput && (!catIdInput || !catIdInput.value)) {
                slugInput.value = val.toLowerCase().trim()
                    .replace(/&/g, '')
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
            }
        }

        function openCategoryModal(catId = null) {
            const modal = document.getElementById('category-modal');
            const titleEl = document.getElementById('cat-modal-title');
            const idInput = document.getElementById('cat-id-input');
            const nameInput = document.getElementById('cat-name-input');
            const slugInput = document.getElementById('cat-slug-input');
            const descInput = document.getElementById('cat-desc-input');
            const imageInput = document.getElementById('cat-image-input');

            if (catId) {
                const cat = categoriesDB.find(c => c.id === catId);
                if (cat) {
                    if (titleEl) titleEl.textContent = 'Edit Category';
                    if (idInput) idInput.value = cat.id;
                    if (nameInput) nameInput.value = cat.title || '';
                    if (slugInput) slugInput.value = cat.slug || '';
                    if (descInput) descInput.value = cat.desc || '';
                    if (imageInput) imageInput.value = cat.image || '';
                }
            } else {
                if (titleEl) titleEl.textContent = 'Add New Category';
                if (idInput) idInput.value = '';
                if (nameInput) nameInput.value = '';
                if (slugInput) slugInput.value = '';
                if (descInput) descInput.value = '';
                if (imageInput) imageInput.value = '';
            }

            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.remove('pointer-events-none');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modal.querySelector('.transform').classList.remove('scale-95');
                }, 10);
            }
        }

        function closeCategoryModal() {
            const modal = document.getElementById('category-modal');
            if (modal) {
                modal.classList.add('opacity-0');
                modal.querySelector('.transform').classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.add('pointer-events-none');
                }, 300);
            }
        }

        function saveCategoryForm(e) {
            e.preventDefault();
            const idInput = document.getElementById('cat-id-input');
            const nameInput = document.getElementById('cat-name-input');
            const slugInput = document.getElementById('cat-slug-input');
            const descInput = document.getElementById('cat-desc-input');
            const imageInput = document.getElementById('cat-image-input');

            const title = nameInput ? nameInput.value.trim() : '';
            let slug = slugInput ? slugInput.value.trim().toLowerCase() : '';
            const desc = descInput ? descInput.value.trim() : '';
            const image = (imageInput && imageInput.value.trim()) ? imageInput.value.trim() : '/assets/images/hero/1.jpg';

            if (!title) {
                alert('Please enter a category name.');
                return;
            }

            if (!slug) {
                slug = title.toLowerCase().replace(/&/g, '').replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-');
            }

            if (idInput && idInput.value) {
                // Edit existing category
                const catId = parseInt(idInput.value);
                const cat = categoriesDB.find(c => c.id === catId);
                if (cat) {
                    cat.title = title;
                    cat.slug = slug;
                    cat.desc = desc;
                    cat.image = image;
                }
            } else {
                // Add new category
                const newId = categoriesDB.length > 0 ? Math.max(...categoriesDB.map(c => c.id || 0)) + 1 : 1;
                categoriesDB.push({
                    id: newId,
                    title: title,
                    slug: slug,
                    desc: desc,
                    image: image,
                    active: true
                });
            }

            closeCategoryModal();
            renderCategories();
        }

        function renderCustomers() {
            const tbody = document.getElementById('customers-tbody');
            if (!tbody) return;

            tbody.innerHTML = '';
            // Generate list based on bookings
            const customerCounts = {};
            bookingsDB.forEach(b => {
                if (!customerCounts[b.name]) {
                    customerCounts[b.name] = { name: b.name, mobile: b.mobile, email: `${b.name.toLowerCase().replace(' ', '')}@gmail.com`, count: 0 };
                }
                customerCounts[b.name].count++;
            });

            Object.values(customerCounts).forEach(c => {
                const row = document.createElement('tr');
                row.className = "hover:bg-white/[0.01] transition-colors border-b border-border-color/30";
                row.innerHTML = `
                    <td class="py-4 px-6 text-white font-bold">${c.name}</td>
                    <td class="py-4 px-6 text-green-400 font-semibold">+91 ${c.mobile}</td>
                    <td class="py-4 px-6 text-muted-text font-semibold">${c.email}</td>
                    <td class="py-4 px-6 text-white font-bold">${c.count} Bookings</td>
                    <td class="py-4 px-6 text-right">
                        <a href="https://wa.me/91${c.mobile}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-[10px] font-extrabold uppercase tracking-widest transition-all">
                            <i class="fa-brands fa-whatsapp"></i> Chat Now
                        </a>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function renderFAQs() {
            const container = document.getElementById('faqs-admin-list');
            if (!container) return;

            container.innerHTML = '';
            faqDB.forEach(f => {
                const item = document.createElement('div');
                item.className = "p-4 bg-white/[0.01] border border-border-color/30 rounded-xl text-left flex justify-between items-start gap-6";
                item.innerHTML = `
                    <div class="flex-grow">
                        <span class="font-bold text-xs text-white uppercase tracking-wider block mb-1">Q: ${f.q}</span>
                        <p class="text-[11px] text-muted-text font-semibold leading-relaxed pl-4 border-l border-gold/45">A: ${f.a}</p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <button onclick="openFaqModal(${f.id})" class="text-[10px] text-muted-text hover:text-gold transition-colors cursor-pointer bg-transparent border-none outline-none"><i class="fa-solid fa-pen-to-square text-sm"></i></button>
                        <form action="/admin/faqs/delete/${f.id}" method="POST" onsubmit="return confirm('Are you sure you want to delete this FAQ?')" class="inline m-0">
                            @csrf
                            <button type="submit" class="text-[10px] text-red-400 hover:text-red-500 transition-colors cursor-pointer bg-transparent border-none outline-none"><i class="fa-solid fa-trash-can text-sm"></i></button>
                        </form>
                    </div>
                `;
                container.appendChild(item);
            });
        }

        // GCD helper to calculate aspect ratio
        function getAspectRatioLabel(width, height) {
            const gcd = (a, b) => b ? gcd(b, a % b) : a;
            const divisor = gcd(width, height);
            const wRatio = width / divisor;
            const hRatio = height / divisor;
            return `${wRatio}:${hRatio}`;
        }

        // Preview slide image and calculate aspect ratio
        function previewSlideImage(input, index) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const objectUrl = URL.createObjectURL(file);
                
                // Update preview image src
                const previewImg = document.getElementById(`slide-preview-${index}`);
                previewImg.src = objectUrl;

                // Load image to read dimensions
                const img = new Image();
                img.src = objectUrl;
                img.onload = function() {
                    const width = img.naturalWidth;
                    const height = img.naturalHeight;
                    const ratio = getAspectRatioLabel(width, height);
                    document.getElementById(`ratio-badge-${index}`).textContent = `${width}x${height} (${ratio})`;
                };
            }
        }

        // Initialize aspect ratios for existing images on load
        function initAspectRatios() {
            document.querySelectorAll('[id^="slide-preview-"]').forEach(previewImg => {
                const index = previewImg.id.replace('slide-preview-', '');
                if (previewImg && previewImg.src) {
                    const img = new Image();
                    img.src = previewImg.src;
                    img.onload = function() {
                        const width = img.naturalWidth;
                        const height = img.naturalHeight;
                        const ratio = getAspectRatioLabel(width, height);
                        const badge = document.getElementById(`ratio-badge-${index}`);
                        if (badge) {
                            badge.textContent = `${width}x${height} (${ratio})`;
                        }
                    };
                    // Trigger onload manually if the image is already cached/loaded
                    if (previewImg.complete) {
                        img.onload();
                    }
                }
            });
        }

        let slideCount = {{ count($cms['hero_slides']) }};

        function addNewSlide() {
            const container = document.getElementById('hero-slides-grid');
            if (!container) return;

            const idx = slideCount;
            slideCount++;

            const card = document.createElement('div');
            card.className = "slide-card-wrapper border border-border-color/60 rounded-xl p-4 bg-main-bg/30 flex flex-col gap-4";
            card.innerHTML = `
                <div class="flex justify-between items-center border-b border-border-color pb-2">
                    <span class="text-[10px] text-gold font-bold uppercase">New Slide</span>
                    <button type="button" onclick="removeSlide(this)" class="text-red-400 hover:text-red-500 text-[10px] font-bold uppercase tracking-wider transition-colors"><i class="fa-solid fa-trash-can mr-1"></i> Delete</button>
                </div>
                
                <!-- Dynamic Image Upload Zone with live preview and Aspect Ratio Calculations -->
                <div class="flex flex-col gap-2">
                    <label class="text-[9px] text-muted-text font-bold uppercase tracking-wider">Slide Image (Recommended 16:9)</label>
                    <div class="relative group aspect-[16/9] rounded-xl overflow-hidden border border-border-color hover:border-gold transition-all bg-main-bg flex flex-col items-center justify-center cursor-pointer shadow-sm" onclick="document.getElementById('slide-input-${idx}').click()">
                        <!-- Current image preview -->
                        <img id="slide-preview-${idx}" src="/assets/images/hero/1.jpg" class="absolute inset-0 w-full h-full object-cover group-hover:scale-102 transition-all">
                        <!-- Upload Overlay -->
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white text-[10px] font-bold gap-1 z-10">
                            <i class="fa-solid fa-cloud-arrow-up text-base"></i>
                            <span>Click to Upload</span>
                        </div>
                        <!-- Aspect Ratio Display overlay -->
                        <span id="ratio-badge-${idx}" class="absolute bottom-2 right-2 px-2 py-0.5 rounded bg-black/75 text-[8px] text-gold font-bold uppercase border border-white/5 font-mono shadow-md z-20">Detecting Ratio...</span>
                    </div>
                    <input type="file" id="slide-input-${idx}" name="hero_slides[${idx}][image_file]" class="hidden" accept="image/*" onchange="previewSlideImage(this, ${idx})">
                    <!-- Hidden input to keep current url path if no new file is uploaded -->
                    <input type="hidden" name="hero_slides[${idx}][image]" value="/assets/images/hero/1.jpg">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[9px] text-muted-text font-bold uppercase tracking-wider">Top Small Badge</label>
                    <input type="text" name="hero_slides[${idx}][badge]" value="NEW CELEBRATIONS" class="w-full bg-white border border-border-color p-2 text-xs font-semibold text-main-text rounded-lg outline-none focus:border-gold">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[9px] text-muted-text font-bold uppercase tracking-wider">Main Heading</label>
                    <input type="text" name="hero_slides[${idx}][title]" value="CUSTOM CELEBRATIONS" class="w-full bg-white border border-border-color p-2 text-xs font-semibold text-main-text rounded-lg outline-none focus:border-gold">
                </div>

                <div class="flex flex-col gap-1.5 text-left">
                    <label class="text-[9px] text-muted-text font-bold uppercase tracking-wider">Slide Description</label>
                    <textarea id="slide-desc-${idx}" name="hero_slides[${idx}][desc]" class="ck-editor-textarea">Book custom decors and lights instantly.</textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[9px] text-muted-text font-bold uppercase tracking-wider">Btn 1 Text</label>
                        <input type="text" name="hero_slides[${idx}][btn1Text]" value="Explore Packages" class="w-full bg-white border border-border-color p-2 text-xs font-semibold text-main-text rounded-lg outline-none focus:border-gold">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[9px] text-muted-text font-bold uppercase tracking-wider">Btn 1 Link</label>
                        <input type="text" name="hero_slides[${idx}][link1]" value="/events" class="w-full bg-white border border-border-color p-2 text-xs font-semibold text-main-text rounded-lg outline-none focus:border-gold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[9px] text-muted-text font-bold uppercase tracking-wider">Btn 2 Text</label>
                        <input type="text" name="hero_slides[${idx}][btn2Text]" value="Book Now" class="w-full bg-white border border-border-color p-2 text-xs font-semibold text-main-text rounded-lg outline-none focus:border-gold">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[9px] text-muted-text font-bold uppercase tracking-wider">Btn 2 Link</label>
                        <input type="text" name="hero_slides[${idx}][link2]" value="https://wa.me/919131668156" class="w-full bg-white border border-border-color p-2 text-xs font-semibold text-main-text rounded-lg outline-none focus:border-gold">
                    </div>
                </div>
            `;

            container.appendChild(card);

            // Initialize CKEditor on the new description textarea
            ClassicEditor
                .create(document.getElementById(`slide-desc-${idx}`), {
                    toolbar: [ 'undo', 'redo', '|', 'bold', 'italic', '|', 'link', 'bulletedList', 'numberedList', '|', 'blockQuote' ]
                })
                .catch(error => {
                    console.error(error);
                });

            // Initialize aspect ratio details for this new card preview
            const previewImg = document.getElementById(`slide-preview-${idx}`);
            if (previewImg) {
                const img = new Image();
                img.src = previewImg.src;
                img.onload = function() {
                    const width = img.naturalWidth;
                    const height = img.naturalHeight;
                    const ratio = getAspectRatioLabel(width, height);
                    document.getElementById(`ratio-badge-${idx}`).textContent = `${width}x${height} (${ratio})`;
                };
            }
        }

        function removeSlide(btn) {
            if (confirm('Are you sure you want to remove this slide?')) {
                const card = btn.closest('.slide-card-wrapper');
                if (card) {
                    card.remove();
                }
            }
        }

        // About Images Gallery upload and aspect ratio calculators
        let aboutImageCount = {{ count($cms['about']['images'] ?? [ $cms['about']['image'] ?? '/assets/images/hero/1.jpg' ]) }};

        function previewAboutGalleryImage(input, index) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const objectUrl = URL.createObjectURL(file);
                
                // Update preview image src
                const previewImg = document.getElementById(`about-img-preview-${index}`);
                previewImg.src = objectUrl;

                // Load image to read dimensions
                const img = new Image();
                img.src = objectUrl;
                img.onload = function() {
                    const width = img.naturalWidth;
                    const height = img.naturalHeight;
                    const ratio = getAspectRatioLabel(width, height);
                    document.getElementById(`about-img-ratio-badge-${index}`).textContent = `${width}x${height} (${ratio})`;
                };
            }
        }

        function initAboutGalleryAspectRatios() {
            document.querySelectorAll('[id^="about-img-preview-"]').forEach(previewImg => {
                const index = previewImg.id.replace('about-img-preview-', '');
                if (previewImg && previewImg.src) {
                    const img = new Image();
                    img.src = previewImg.src;
                    img.onload = function() {
                        const width = img.naturalWidth;
                        const height = img.naturalHeight;
                        const ratio = getAspectRatioLabel(width, height);
                        const badge = document.getElementById(`about-img-ratio-badge-${index}`);
                        if (badge) {
                            badge.textContent = `${width}x${height} (${ratio})`;
                        }
                    };
                    if (previewImg.complete) {
                        img.onload();
                    }
                }
            });
        }

        function addNewAboutImage() {
            const container = document.getElementById('about-images-grid');
            if (!container) return;

            const idx = aboutImageCount;
            aboutImageCount++;

            const card = document.createElement('div');
            card.className = "about-image-wrapper border border-border-color/60 rounded-xl p-3 bg-main-bg/30 flex flex-col gap-3 relative";
            card.innerHTML = `
                <button type="button" onclick="removeAboutImage(this)" class="absolute top-2 right-2 bg-red-500/80 hover:bg-red-500 text-white w-6 h-6 rounded-lg flex items-center justify-center transition-all z-20 shadow-md border border-white/10" title="Delete Image">
                    <i class="fa-solid fa-trash-can text-[10px]"></i>
                </button>
                
                <div class="relative group aspect-[4/3] rounded-lg overflow-hidden border border-border-color hover:border-gold transition-all bg-main-bg flex flex-col items-center justify-center cursor-pointer shadow-sm" onclick="document.getElementById('about-img-input-${idx}').click()">
                    <img id="about-img-preview-${idx}" src="/assets/images/hero/1.jpg" class="absolute inset-0 w-full h-full object-cover group-hover:scale-102 transition-all">
                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white text-[9px] font-bold gap-1 z-10">
                        <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                        <span>Click to Change</span>
                    </div>
                    <span id="about-img-ratio-badge-${idx}" class="absolute bottom-2 right-2 px-2 py-0.5 rounded bg-black/75 text-[7px] text-gold font-bold uppercase border border-white/5 font-mono shadow-md z-20">Detecting Ratio...</span>
                </div>
                <input type="file" id="about-img-input-${idx}" name="about[images][${idx}][image_file]" class="hidden" accept="image/*" onchange="previewAboutGalleryImage(this, ${idx})">
                <input type="hidden" name="about[images][${idx}][image]" value="/assets/images/hero/1.jpg">
            `;

            container.appendChild(card);

            // Initialize ratio loader
            const previewImg = document.getElementById(`about-img-preview-${idx}`);
            if (previewImg) {
                const img = new Image();
                img.src = previewImg.src;
                img.onload = function() {
                    const width = img.naturalWidth;
                    const height = img.naturalHeight;
                    const ratio = getAspectRatioLabel(width, height);
                    document.getElementById(`about-img-ratio-badge-${idx}`).textContent = `${width}x${height} (${ratio})`;
                };
            }
        }

        function removeAboutImage(btn) {
            if (confirm('Are you sure you want to remove this image from the gallery?')) {
                const wrapper = btn.closest('.about-image-wrapper');
                if (wrapper) {
                    wrapper.remove();
                }
            }
        }

        // Dynamic About Cards management
        let aboutCardCount = {{ count($cms['about']['cards']) }};

        function addNewAboutCard() {
            const container = document.getElementById('about-cards-grid');
            if (!container) return;

            const idx = aboutCardCount;
            aboutCardCount++;

            const card = document.createElement('div');
            card.className = "about-card-wrapper border border-border-color/60 rounded-xl p-4 bg-main-bg/30 flex flex-col gap-4";
            card.innerHTML = `
                <div class="flex justify-between items-center border-b border-border-color pb-2">
                    <span class="text-[10px] text-gold font-bold uppercase font-heading font-semibold">Grid Card</span>
                    <button type="button" onclick="removeAboutCard(this)" class="text-red-400 hover:text-red-500 text-[10px] font-bold uppercase tracking-wider transition-colors"><i class="fa-solid fa-trash-can mr-1"></i> Delete</button>
                </div>
                
                <div class="flex flex-col gap-1.5">
                    <label class="text-[9px] text-muted-text font-bold uppercase tracking-wider font-heading">Card Icon</label>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gold/10 border border-gold/30 flex items-center justify-center text-gold text-lg shrink-0">
                            <i id="card-icon-preview-${idx}" class="fa-solid fa-star"></i>
                        </div>
                        <button type="button" onclick="openIconPicker('card-icon-preview-${idx}', 'card-icon-input-${idx}')" class="border border-border-color text-main-text hover:border-gold hover:text-gold px-3 py-2 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-colors cursor-pointer bg-white">
                            Choose Icon
                        </button>
                        <input type="hidden" id="card-icon-input-${idx}" name="about[cards][${idx}][icon]" value="fa-solid fa-star">
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[9px] text-muted-text font-bold uppercase tracking-wider font-heading">Card Title</label>
                    <input type="text" name="about[cards][${idx}][title]" value="PREMIUM SERVICES" class="w-full bg-white border border-border-color p-2 text-xs font-semibold text-main-text rounded-lg outline-none focus:border-gold">
                </div>

                <div class="flex flex-col gap-1.5 text-left">
                    <label class="text-[9px] text-muted-text font-bold uppercase tracking-wider font-heading">Card Description</label>
                    <textarea id="about-card-desc-${idx}" name="about[cards][${idx}][desc]" class="ck-editor-textarea">Enter card features and logistics detail.</textarea>
                </div>
            `;

            container.appendChild(card);

            // Initialize CKEditor on the new description textarea
            ClassicEditor
                .create(document.getElementById(`about-card-desc-${idx}`), {
                    toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ]
                })
                .catch(error => {
                    console.error(error);
                });
        }

        function removeAboutCard(btn) {
            if (confirm('Are you sure you want to remove this about page card?')) {
                const card = btn.closest('.about-card-wrapper');
                if (card) {
                    card.remove();
                }
            }
        }

        // Reusable Global Icon Picker functions
        let targetPreviewId = null;
        let targetInputId = null;

        const iconLibrary = [
            // 👑 Luxury & Tier Badges
            { class: "fa-solid fa-crown", name: "crown luxury premium king platinum vip badge royal icon" },
            { class: "fa-solid fa-gem", name: "gem diamond luxury expensive premium tier custom jewelry crystal" },
            { class: "fa-solid fa-trophy", name: "trophy winner best seller award first prize victory champion" },
            { class: "fa-solid fa-award", name: "award trophy prize reward winner badge medal certificate ribbon" },
            { class: "fa-solid fa-medal", name: "medal award badge honor winner gold star" },
            { class: "fa-solid fa-shield-halved", name: "shield security guarantee verified safe protection trusted" },
            { class: "fa-solid fa-certificate", name: "certificate badge verified authorized quality star" },

            // ⭐ Popularity & Ratings
            { class: "fa-solid fa-star", name: "star featured rating favorite popular review highlight recommend" },
            { class: "fa-solid fa-star-half-stroke", name: "star half rating review score" },
            { class: "fa-solid fa-thumbs-up", name: "thumbs up recommend like approved top rated quality" },
            { class: "fa-solid fa-fire", name: "fire sparks hot popular trending sangeet pyro flame sparklers" },
            { class: "fa-solid fa-chart-line", name: "chart trending growth popular rank performance sales" },
            { class: "fa-solid fa-bolt", name: "bolt flash instant fast express lightning quick speed" },
            { class: "fa-solid fa-sparkles", name: "sparkles magic glow fairy lights entrance shiny decor" },
            { class: "fa-solid fa-wand-magic-sparkles", name: "magic wand sparkles entrance surprise pyro glow" },

            // 🎂 Parties & Celebrations
            { class: "fa-solid fa-cake-candles", name: "cake birthday candles party celebration dessert sweet anniversary" },
            { class: "fa-solid fa-heart", name: "heart love romantic proposal wedding anniversary couple" },
            { class: "fa-solid fa-champagne-glasses", name: "champagne wine toast party celebration drinks cheers glass" },
            { class: "fa-solid fa-ring", name: "ring engagement proposal wedding marriage diamond luxury couple" },
            { class: "fa-solid fa-gift", name: "gift surprise present box birthday celebration hamper offer" },
            { class: "fa-solid fa-masks-theater", name: "masks drama party entertainment stage show event drama costume" },
            { class: "fa-solid fa-ribbon", name: "ribbon gift bow decoration package grand opening" },
            { class: "fa-solid fa-glass-water-droplet", name: "glass drink beverage cocktail mocktail welcome bar" },

            // 🎶 Music, Sound & Stage
            { class: "fa-solid fa-music", name: "music dj song notes audio dance sangeet bass truss sound" },
            { class: "fa-solid fa-microphone", name: "microphone host mic anchor speech speaker singer live emcee" },
            { class: "fa-solid fa-microphone-lines", name: "microphone mic concert performance live show sound" },
            { class: "fa-solid fa-volume-high", name: "volume speaker sound music highbass truss systems box audio" },
            { class: "fa-solid fa-compact-disc", name: "disc cd dj vinyl player media record turntable mix" },
            { class: "fa-solid fa-headphones", name: "headphones audio sound listening monitor dj studio pro" },
            { class: "fa-solid fa-guitar", name: "guitar music band live acoustic performance instrument rock" },
            { class: "fa-solid fa-drum", name: "drum dhol band music rhythm sangeet baraat wedding beat" },
            { class: "fa-solid fa-sliders", name: "sliders mixer sound equalizer console DJ control" },

            // 💡 Lighting, Special Effects & Decor
            { class: "fa-solid fa-lightbulb", name: "light bulb lighting strobe concert led decor spotlight beam" },
            { class: "fa-solid fa-sun", name: "sun bright outdoor warm ambient mood yellow day" },
            { class: "fa-solid fa-moon", name: "moon night rooftop dusk romantic ambient star LED" },
            { class: "fa-solid fa-icons", name: "icons design decor elements theme props backdrop balloon setup" },
            { class: "fa-solid fa-spray-can-sparkles", name: "spray snow aerosol fog smoke machine cold pyro effect" },
            { class: "fa-solid fa-palette", name: "palette theme color custom decoration design art artist" },
            { class: "fa-solid fa-pen-ruler", name: "ruler blueprint design floor plan setup stage architecture" },

            // 📸 Photography, Video & Memories
            { class: "fa-solid fa-camera", name: "camera photo shoot gallery video reels memories capture DSLR" },
            { class: "fa-solid fa-camera-rotate", name: "camera 360 spin photo booth video booth rotation reel" },
            { class: "fa-solid fa-video", name: "video camera movie cinematic teaser trailer shooting clip" },
            { class: "fa-solid fa-film", name: "film cinema video reel production highlights movie" },
            { class: "fa-solid fa-image", name: "image photo portrait backdrop photo frame memory" },
            { class: "fa-solid fa-images", name: "images photo gallery slideshow portfolio album" },
            { class: "fa-solid fa-vr-cardboard", name: "vr 360 tour virtual walkthrough 3d view spatial" },

            // 🏛️ Venues & Space
            { class: "fa-solid fa-building", name: "building hotel banquet hall venue indoor resort hall" },
            { class: "fa-solid fa-tree", name: "tree garden lawn outdoor nature open air resort" },
            { class: "fa-solid fa-house", name: "house home private house party terrace lawn indoor" },
            { class: "fa-solid fa-warehouse", name: "warehouse storage props inventory stock supplier" },
            { class: "fa-solid fa-location-dot", name: "location address gps maps indore map venue pin spot city" },
            { class: "fa-solid fa-map-location-dot", name: "map navigation outstation city location destination route" },
            { class: "fa-solid fa-shop", name: "shop stall booth counter corporate exhibition kiosk" },

            // 🍔 Food, Catering & Refreshments
            { class: "fa-solid fa-utensils", name: "utensils food catering dinner buffet restaurant feast meal" },
            { class: "fa-solid fa-bowl-food", name: "bowl food dish starter snacks Indian cuisine chat" },
            { class: "fa-solid fa-mug-hot", name: "mug tea coffee welcome drink hot beverage station" },
            { class: "fa-solid fa-wine-glass", name: "wine glass bar cocktail lounge luxury drink welcome" },
            { class: "fa-solid fa-ice-cream", name: "ice cream dessert stall sweet counter kids treat" },
            { class: "fa-solid fa-cookie-bite", name: "cookie bakery cake dessert bakery snacks" },

            // 👥 People, Team & Guests
            { class: "fa-solid fa-user", name: "user person manager client customer contact profile" },
            { class: "fa-solid fa-users", name: "users guests audience group capacity attendance crowd" },
            { class: "fa-solid fa-people-group", name: "group people guests audience crowd corporate sangeet meet team" },
            { class: "fa-solid fa-handshake", name: "handshake partner agreement trust client corporate deal contract" },
            { class: "fa-solid fa-user-tie", name: "tie manager supervisor coordinator team lead host" },
            { class: "fa-solid fa-user-ninja", name: "ninja crew decorator setup staff technician artist" },
            { class: "fa-solid fa-child-reaching", name: "child kids birthday theme games fun inflatable bouncy" },

            // 🚚 Logistics, Vehicles & Support
            { class: "fa-solid fa-truck", name: "truck delivery vehicle dispatch travel transport cargo van logistics" },
            { class: "fa-solid fa-truck-fast", name: "truck fast express same day delivery priority setup" },
            { class: "fa-solid fa-car", name: "car luxury entry groom entry baraat vehicle vintage" },
            { class: "fa-solid fa-clock", name: "clock time hours duration timing lead setup notice" },
            { class: "fa-solid fa-hourglass-half", name: "hourglass time remaining countdown lead time notice" },
            { class: "fa-solid fa-calendar-check", name: "calendar date booking available schedule reserved slot" },

            // 💰 Offers, Price & Finance
            { class: "fa-solid fa-indian-rupee-sign", name: "rupee price cost money invoice payment cash budget affordable" },
            { class: "fa-solid fa-tags", name: "tags deals pricing discount offer coupon ticket sales promo" },
            { class: "fa-solid fa-percent", name: "percent discount offer off savings sale promo rate" },
            { class: "fa-solid fa-wallet", name: "wallet payment advance deposit booking money cash refund" },
            { class: "fa-solid fa-credit-card", name: "card payment digital receipt bill transaction invoice" },

            // 📞 Communication & Social
            { class: "fa-solid fa-phone", name: "phone call contact support hotline mobile whatsapp" },
            { class: "fa-brands fa-whatsapp", name: "whatsapp chat message mobile alert notification reminder" },
            { class: "fa-solid fa-envelope", name: "envelope email mail newsletter contact inbox inquiry" },
            { class: "fa-solid fa-comments", name: "comments chat reviews feedback testimonials discussion" },
            { class: "fa-solid fa-bell", name: "bell alert notification reminder notice ping updates" },
            { class: "fa-brands fa-youtube", name: "youtube video teaser trailer clip media channel" },
            { class: "fa-brands fa-instagram", name: "instagram photos reels social post story media" },
            { class: "fa-solid fa-share-nodes", name: "share social viral link connect reach" },

            // 🎈 Fun, Props & Miscellaneous
            { class: "fa-solid fa-face-smile", name: "smile face happy customer guests feedback joy fun" },
            { class: "fa-solid fa-gamepad", name: "gamepad games arcade VR fun activity kids entertainment" },
            { class: "fa-solid fa-puzzle-piece", name: "puzzle activity engagement team building games" },
            { class: "fa-solid fa-wand-magic", name: "magic magician show entertainment artist performance" },
            { class: "fa-solid fa-couch", name: "couch sofa lounge seating arrangement VIP stage furniture" },
            { class: "fa-solid fa-table", name: "table cake table dining table seating setup furniture" },
            { class: "fa-solid fa-plug", name: "plug power socket electrical 220V voltage generator electricity" },
            { class: "fa-solid fa-box", name: "box package tier bundle all in one decor items" },
            { class: "fa-solid fa-file-pdf", name: "pdf document brochure catalog guidelines sheet" },
            { class: "fa-solid fa-circle-info", name: "info help details instructions guide notes operational" },
            { class: "fa-solid fa-circle-check", name: "check success included verified done complete ok" },
            { class: "fa-solid fa-circle-xmark", name: "xmark excluded missing cancel not included prohibited" }
        ];

        function openIconPicker(previewId, inputId) {
            targetPreviewId = previewId;
            targetInputId = inputId;

            // Clear search field
            const search = document.getElementById('icon-search-input');
            if (search) search.value = '';

            // Render all icons
            filterIconLibrary('');

            // Display modal with animation
            const modal = document.getElementById('global-icon-picker-modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.remove('pointer-events-none');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modal.querySelector('.transform').classList.remove('scale-95');
                }, 10);
            }
        }

        function closeIconPicker() {
            const modal = document.getElementById('global-icon-picker-modal');
            if (modal) {
                modal.classList.add('opacity-0');
                modal.querySelector('.transform').classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.add('pointer-events-none');
                }, 300);
            }
        }

        function selectIcon(iconClass) {
            if (targetPreviewId && targetInputId) {
                // Update preview element class list
                const previewEl = document.getElementById(targetPreviewId);
                if (previewEl) {
                    previewEl.className = iconClass;
                }
                // Update hidden input element value
                const inputEl = document.getElementById(targetInputId);
                if (inputEl) {
                    inputEl.value = iconClass;
                }
            }
            closeIconPicker();
        }

        // Dynamic Tab Switching Handler for Sidebar Navigation
        window.switchTab = function (tabId) {
            if (!tabId) tabId = 'overview';

            // Hide all tab panels
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.add('hidden');
            });

            // Target section ID
            const targetId = 'panel-' + tabId;
            const targetPanel = document.getElementById(targetId);

            if (targetPanel) {
                targetPanel.classList.remove('hidden');
            } else {
                const overviewPanel = document.getElementById('panel-overview');
                if (overviewPanel) overviewPanel.classList.remove('hidden');
            }

            // Update sidebar button active styling
            const navLinks = document.querySelectorAll('#sidebar-nav a[data-tab-btn]');
            navLinks.forEach(link => {
                const btnTab = link.getAttribute('data-tab-btn');
                if (btnTab === tabId) {
                    link.className = 'flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-150 text-gray-900 bg-gray-100 text-left w-full';
                } else {
                    link.className = 'flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 text-gray-600 hover:text-gray-900 hover:bg-gray-50 text-left w-full';
                }
            });

            // Update Header Title & Subtitle
            const titleMap = {
                'overview': { title: 'Overview', desc: 'Live bookings & performance insights' },
                'bookings': { title: 'Bookings', desc: 'Manage customer bookings, event schedules and booking status.' },
                'packages': { title: 'Package Management', desc: 'Configure celebration packages, tiers, pricing and inclusions.' },
                'categories': { title: 'Category Management', desc: 'Manage event categories, titles, cover images and active statuses.' },
                'customers': { title: 'Customer Directory', desc: 'View customer contacts, order history and total celebration spend.' },
                'testimonials': { title: 'FAQ & Customer Reviews', desc: 'Manage dynamic FAQ statements, answers and customer feedback.' },
                'hero-manager': { title: 'Hero Slider Manager', desc: 'Customize main homepage hero swiper slides, badges, images and links.' },
                'about-manager': { title: 'About Us Content', desc: 'Update About Us page badges, guarantee statements, gallery photos and cards.' },
                'settings': { title: 'Website Settings & SEO', desc: 'Manage store details, contact numbers, branding and SEO tags.' }
            };

            const headerTitle = document.getElementById('current-view-title');
            const headerDesc = document.getElementById('current-view-subtitle');

            if (titleMap[tabId]) {
                if (headerTitle) headerTitle.innerText = titleMap[tabId].title;
                if (headerDesc) headerDesc.innerText = titleMap[tabId].desc;
            }

            // Update browser URL query string without page reload
            if (window.history && window.history.pushState) {
                const newUrl = window.location.pathname + '?tab=' + tabId;
                window.history.pushState({ tab: tabId }, '', newUrl);
            }
        };

        function filterIconLibrary(query) {
            const grid = document.getElementById('icon-picker-grid');
            if (!grid) return;

            grid.innerHTML = '';
            const lowerQuery = query.toLowerCase();

            const filtered = iconLibrary.filter(icon => {
                return icon.name.includes(lowerQuery) || icon.class.includes(lowerQuery);
            });

            filtered.forEach(icon => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = "p-3 rounded-xl border border-border-color/30 hover:border-gold bg-main-bg/30 text-[#1E1E24] hover:text-gold flex flex-col items-center justify-center gap-1.5 transition-all text-sm cursor-pointer";
                btn.onclick = () => selectIcon(icon.class);
                btn.innerHTML = `<i class="${icon.class} text-lg"></i>`;
                grid.appendChild(btn);
            });

            if (filtered.length === 0) {
                grid.innerHTML = `<div class="col-span-5 text-center text-muted-text text-[10px] py-4">No icons found matching "${query}"</div>`;
            }
        }

        // Modals Management Handlers
        window.openCategoryModal = function (catId = null) {
            const modal = document.getElementById('category-editor-modal');
            const form = document.getElementById('category-editor-form');
            const titleEl = document.getElementById('cat-modal-title');
            
            form.reset();
            document.getElementById('cat-id-input').value = '';
            document.getElementById('cat-image-preview').src = 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?q=80&w=300&auto=format&fit=crop';
            document.getElementById('cat-image-url').value = '';

            if (catId) {
                const cat = categoriesDB.find(c => c.id === catId);
                if (cat) {
                    titleEl.innerText = "Edit Event Category";
                    document.getElementById('cat-id-input').value = cat.id;
                    document.getElementById('cat-title-input').value = cat.title;
                    document.getElementById('cat-active-checkbox').checked = cat.active;
                    document.getElementById('cat-image-preview').src = cat.image;
                    document.getElementById('cat-image-url').value = cat.image;
                }
            } else {
                titleEl.innerText = "Create New Category";
            }
            
            modal.classList.remove('hidden');
            modal.classList.remove('opacity-0');
            modal.classList.remove('pointer-events-none');
            modal.querySelector('.transform').classList.remove('scale-95');
        };

        window.closeCategoryModal = function () {
            const modal = document.getElementById('category-editor-modal');
            modal.classList.add('hidden');
            modal.classList.add('opacity-0');
            modal.classList.add('pointer-events-none');
            modal.querySelector('.transform').classList.add('scale-95');
        };


        window.openFaqModal = function (faqId = null) {
            const modal = document.getElementById('faq-editor-modal');
            const form = document.getElementById('faq-editor-form');
            const titleEl = document.getElementById('faq-modal-title');
            
            form.reset();
            document.getElementById('faq-id-input').value = '';
            
            if (window.ckEditors && window.ckEditors['faq-a-input']) {
                window.ckEditors['faq-a-input'].setData('');
            } else {
                document.getElementById('faq-a-input').value = '';
            }

            if (faqId) {
                const f = faqDB.find(faq => faq.id === faqId);
                if (f) {
                    titleEl.innerText = "Edit Help FAQ Details";
                    document.getElementById('faq-id-input').value = f.id;
                    document.getElementById('faq-q-input').value = f.q;
                    
                    if (window.ckEditors && window.ckEditors['faq-a-input']) {
                        window.ckEditors['faq-a-input'].setData(f.a || '');
                    } else {
                        document.getElementById('faq-a-input').value = f.a || '';
                    }
                }
            } else {
                titleEl.innerText = "Add New FAQ Help Item";
            }

            modal.classList.remove('hidden');
            modal.classList.remove('opacity-0');
            modal.classList.remove('pointer-events-none');
            modal.querySelector('.transform').classList.remove('scale-95');
        };

        window.closeFaqModal = function () {
            const modal = document.getElementById('faq-editor-modal');
            modal.classList.add('hidden');
            modal.classList.add('opacity-0');
            modal.classList.add('pointer-events-none');
            modal.querySelector('.transform').classList.add('scale-95');
        };

        // Persistent status toggles
        window.toggleCategoryStatus = function (catId) {
            const cat = categoriesDB.find(c => c.id === catId);
            if (cat) {
                cat.active = !cat.active;
                renderCategories();
                
                const formData = new FormData();
                formData.append('id', cat.id);
                formData.append('title', cat.title);
                formData.append('image', cat.image);
                if (cat.active) formData.append('active', '1');
                
                fetch("{{ route('admin.categories.save') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                }).then(() => {
                    showToast('Category catalog status updated!', 'success');
                });
            }
        };

        window.togglePackageStatus = function (pkgId, tierIdx) {
            // Find tier from rawPackages representation
            const cat = rawPackages[pkgId];
            if (cat && cat.tiers[tierIdx]) {
                const tier = cat.tiers[tierIdx];
                cat.active = !(cat.active ?? true);
                renderPackages();
                
                const formData = new FormData();
                formData.append('category_id', pkgId);
                formData.append('tier_index', tierIdx);
                formData.append('name', tier.name);
                formData.append('price', tier.price);
                formData.append('desc', tier.desc);
                formData.append('inclusions', (tier.inclusions ?? []).join('\n'));
                formData.append('image', tier.image ?? cat.image);
                if (cat.active) formData.append('active', '1');
                
                fetch("{{ route('admin.packages.save') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                }).then(() => {
                    showToast('Package display status updated!', 'success');
                });
            }
        };

        window.showToast = function (message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;
            
            const toast = document.createElement('div');
            toast.className = `p-4 rounded-xl border border-white/10 backdrop-blur-md shadow-2xl transition-all duration-300 transform translate-y-4 opacity-0 flex items-center gap-3 text-[10px] font-heading uppercase font-bold tracking-wider ${
                type === 'success' 
                ? 'bg-green-500/10 border-green-500/30 text-green-400' 
                : 'bg-red-500/10 border-red-500/30 text-red-400'
            }`;
            
            const icon = type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';
            toast.innerHTML = `<i class="fa-solid ${icon} text-sm"></i><span>${message}</span>`;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('translate-y-4', 'opacity-0');
            }, 50);
            
            setTimeout(() => {
                toast.classList.add('translate-y-4', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        };

        // Initialize lists on load
        window.addEventListener('DOMContentLoaded', () => {
            // Read tab query parameter from URL
            const urlParams = new URLSearchParams(window.location.search);
            const activeTabParam = urlParams.get('tab');
            if (activeTabParam) {
                switchTab(activeTabParam);
            }

            // Handle browser back/forward buttons
            window.addEventListener('popstate', () => {
                const params = new URLSearchParams(window.location.search);
                const tab = params.get('tab') || 'overview';
                switchTab(tab);
            });

            updateOverviewMetrics();
            renderBookingsTables();
            renderPackages();
            renderCategories();
            renderCustomers();
            renderFAQs();

            // Initialize aspect ratios for hero slides and about gallery images
            initAspectRatios();
            initAboutGalleryAspectRatios();

            // Initialize classic editors with live-preview CSS matching the public legal pages
            window.ckEditors = {};

            document.querySelectorAll('.ck-editor-textarea').forEach(textarea => {
                ClassicEditor
                    .create(textarea, {
                        toolbar: [
                            'undo', 'redo', '|',
                            'heading', '|',
                            'bold', 'italic', '|',
                            'link', '|',
                            'bulletedList', 'numberedList', '|',
                            'blockQuote'
                        ],
                        heading: {
                            options: [
                                { model: 'paragraph',  title: 'Paragraph',  class: 'ck-heading_paragraph' },
                                { model: 'heading2',   view: 'h2', title: 'Section Heading (H2)', class: 'ck-heading_heading2' },
                                { model: 'heading3',   view: 'h3', title: 'Sub Heading (H3)',     class: 'ck-heading_heading3' }
                            ]
                        }
                    })
                    .then(editor => {
                        const id = textarea.id;
                        if (id) { window.ckEditors[id] = editor; }
                    })
                    .catch(error => { console.error(error); });
            });

            // Initialize Real-Time Dynamic Chart.js Analytics Graphs
            // 1. Category-Wise Revenue Bar Chart
                const catCanvas = document.getElementById('categoryRevenueChart');
                if (catCanvas) {
                    const catCtx = catCanvas.getContext('2d');
                    const catStats = @json($analytics['category_stats'] ?? []);
                    const labels = Object.keys(catStats);
                    const revenues = labels.map(k => catStats[k].revenue || 0);
                    const bookingCounts = labels.map(k => catStats[k].count || 0);

                    new Chart(catCtx, {
                        type: 'bar',
                        data: {
                            labels: labels.length ? labels : ['Birthday Party', 'Proposal', 'Anniversary', 'Wedding', 'DJ Night'],
                            datasets: [
                                {
                                    label: 'Confirmed Revenue (₹)',
                                    data: revenues.length ? revenues : [0, 0, 0, 0, 0],
                                    backgroundColor: 'rgba(212, 163, 115, 0.85)',
                                    borderColor: '#EA741D',
                                    borderWidth: 1.5,
                                    borderRadius: 6,
                                    yAxisID: 'y'
                                },
                                {
                                    label: 'Bookings Count',
                                    data: bookingCounts.length ? bookingCounts : [0, 0, 0, 0, 0],
                                    backgroundColor: 'rgba(30, 30, 36, 0.7)',
                                    borderColor: '#1E1E24',
                                    borderWidth: 1.5,
                                    borderRadius: 6,
                                    yAxisID: 'y1'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top', labels: { font: { size: 10, weight: 'bold' } } }
                            },
                            scales: {
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    ticks: { font: { size: 9 } }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    grid: { drawOnChartArea: false },
                                    ticks: { font: { size: 9 }, precision: 0 }
                                }
                            }
                        }
                    });
                }

                // 2. Booking Status Distribution Doughnut Chart
                const statusCanvas = document.getElementById('statusDistributionChart');
                if (statusCanvas) {
                    const statusCtx = statusCanvas.getContext('2d');
                    const pending = {{ $analytics['pending_count'] ?? 0 }};
                    const contacted = {{ $analytics['contacted_count'] ?? 0 }};
                    const confirmed = {{ $analytics['confirmed_count'] ?? 0 }};
                    const completed = {{ $analytics['completed_count'] ?? 0 }};
                    const cancelled = {{ $analytics['cancelled_count'] ?? 0 }};

                    new Chart(statusCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Pending', 'Contacted', 'Confirmed', 'Completed', 'Cancelled'],
                            datasets: [{
                                data: [pending, contacted, confirmed, completed, cancelled],
                                backgroundColor: ['#F59E0B', '#3B82F6', '#10B981', '#A855F7', '#EF4444'],
                                borderWidth: 2,
                                borderColor: '#FFFFFF'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'right', labels: { font: { size: 10, weight: 'bold' } } }
                            }
                        }
                    });
                }
            });
    </script>
@endpush

@props(['active' => null])

<!-- ===================== SIDEBAR STYLES ===================== -->
<style>
    /* Desktop Sidebar Collapsible Width & Smooth Transitions */
    #admin-sidebar {
        width: 16rem; /* 256px */
        transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Collapsed state styles */
    html.sidebar-collapsed #admin-sidebar,
    body.sidebar-collapsed #admin-sidebar {
        width: 4.5rem !important; /* 72px */
    }

    html.sidebar-collapsed .sidebar-text,
    body.sidebar-collapsed .sidebar-text,
    html.sidebar-collapsed .sidebar-section-title,
    body.sidebar-collapsed .sidebar-section-title {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }

    html.sidebar-collapsed #sidebar-toggle-icon,
    body.sidebar-collapsed #sidebar-toggle-icon {
        transform: rotate(180deg) !important;
    }

    /* When collapsed: completely remove the logo & brand wrapper */
    html.sidebar-collapsed .sidebar-brand-wrapper,
    body.sidebar-collapsed .sidebar-brand-wrapper,
    html.sidebar-collapsed .sidebar-brand-logo,
    body.sidebar-collapsed .sidebar-brand-logo {
        display: none !important;
    }

    /* When collapsed: center the toggle button in the header */
    html.sidebar-collapsed .sidebar-header-bar,
    body.sidebar-collapsed .sidebar-header-bar {
        justify-content: center !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    html.sidebar-collapsed .sidebar-toggle-wrapper,
    body.sidebar-collapsed .sidebar-toggle-wrapper {
        width: 100% !important;
        justify-content: center !important;
    }

    html.sidebar-collapsed #sidebar-desktop-toggle-btn,
    body.sidebar-collapsed #sidebar-desktop-toggle-btn {
        width: 2.25rem !important;
        height: 2.25rem !important;
        margin: 0 auto !important;
    }

    html.sidebar-collapsed .sidebar-nav-item,
    body.sidebar-collapsed .sidebar-nav-item {
        justify-content: center !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        width: 2.75rem !important;
        margin-left: auto !important;
        margin-right: auto !important;
        position: relative !important;
    }

    html.sidebar-collapsed .sidebar-nav-item i,
    body.sidebar-collapsed .sidebar-nav-item i {
        margin: 0 !important;
        font-size: 0.95rem !important;
    }

    html.sidebar-collapsed .sidebar-badge-pill,
    body.sidebar-collapsed .sidebar-badge-pill {
        position: absolute !important;
        top: 4px !important;
        right: 4px !important;
        padding: 0 !important;
        width: 8px !important;
        height: 8px !important;
        border-radius: 9999px !important;
        font-size: 0 !important;
        border: 2px solid #ffffff !important;
        background-color: #ea741d !important;
        margin: 0 !important;
    }

    html.sidebar-collapsed .sidebar-footer-btn,
    body.sidebar-collapsed .sidebar-footer-btn {
        justify-content: center !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        width: 2.75rem !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    /* Mobile drawer open state */
    body.mobile-sidebar-open #admin-sidebar {
        transform: translateX(0) !important;
    }
    body.mobile-sidebar-open #sidebar-mobile-backdrop {
        display: block !important;
        opacity: 1 !important;
    }

    /* Prevent focus outline / border on admin user trigger button */
    #admin-dropdown-trigger,
    #admin-dropdown-trigger:focus,
    #admin-dropdown-trigger:active,
    #admin-dropdown-trigger:focus-visible {
        outline: none !important;
        box-shadow: none !important;
        border: none !important;
    }
</style>

<!-- Early storage check script to prevent layout flash -->
<script>
    (function() {
        try {
            if (localStorage.getItem('artizen_admin_sidebar_collapsed') === 'true' && window.innerWidth >= 768) {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        } catch(e) {}
    })();
</script>

<!-- Mobile Backdrop Overlay -->
<div id="sidebar-mobile-backdrop" 
     onclick="closeAdminSidebarMobile()" 
     class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs z-40 hidden transition-opacity duration-300 md:hidden"
     aria-hidden="true"></div>

<!-- ===================== COMMON ADMIN SIDEBAR ===================== -->
<aside id="admin-sidebar" 
       class="fixed md:sticky top-0 left-0 h-screen z-50 md:z-40 bg-white border-r border-gray-200/90 flex flex-col shrink-0 -translate-x-full md:translate-x-0 shadow-2xl md:shadow-none select-none">
    
    <!-- Brand & Controls Header -->
    <div class="sidebar-header-bar h-16 border-b border-gray-200/80 flex items-center px-4 justify-between bg-white shrink-0">
        <!-- Logo & Title -->
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand-wrapper flex items-center gap-3 overflow-hidden group">
            <img src="{{ asset('assets/images/logo/artizen.png') }}" 
                 alt="Artizen Logo" 
                 class="sidebar-brand-logo h-9 w-9 object-contain rounded-lg shrink-0 transition-transform duration-200 group-hover:scale-105 shadow-2xs">
            <div class="sidebar-text overflow-hidden transition-opacity duration-200">
                <div class="flex items-center gap-1.5">
                    <span class="font-extrabold text-[15px] text-gray-900 leading-tight tracking-tight">Artizen</span>
                    <span class="text-[9px] uppercase tracking-wider font-extrabold px-1.5 py-0.5 rounded bg-amber-100 text-amber-800">Admin</span>
                </div>
                <span class="block text-[11px] text-gray-400 font-medium">Control Center</span>
            </div>
        </a>

        <!-- Open / Close Toggle Controls -->
        <div class="sidebar-toggle-wrapper flex items-center gap-1">
            <!-- Desktop Collapse/Expand Toggle Icon -->
            <button type="button" 
                    id="sidebar-desktop-toggle-btn"
                    onclick="toggleAdminSidebarDesktop()" 
                    class="hidden md:inline-flex w-8 h-8 rounded-xl items-center justify-center text-gray-400 hover:text-gray-800 hover:bg-gray-100 transition-colors border border-gray-200/80 cursor-pointer shadow-2xs" 
                    title="Collapse / Expand Sidebar"
                    aria-label="Toggle Sidebar">
                <i id="sidebar-toggle-icon" class="fa-solid fa-chevron-left text-xs transition-transform duration-300"></i>
            </button>

            <!-- Mobile Close (X) Icon -->
            <button type="button" 
                    onclick="closeAdminSidebarMobile()" 
                    class="md:hidden w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors cursor-pointer"
                    title="Close Sidebar"
                    aria-label="Close Sidebar">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="flex-1 px-3 py-4 flex flex-col gap-0.5 overflow-y-auto" id="sidebar-nav">
        @php
            $sections = [
                'Main' => [
                    ['id' => 'overview',       'label' => 'Overview',      'icon' => 'fa-solid fa-chart-line',      'route' => 'admin.dashboard'],
                    ['id' => 'bookings',       'label' => 'Bookings',      'icon' => 'fa-solid fa-calendar-check',  'route' => 'admin.bookings',    'badge' => true],
                ],
                'Catalog' => [
                    ['id' => 'packages',       'label' => 'Packages',      'icon' => 'fa-solid fa-box',             'route' => 'admin.packages'],
                    ['id' => 'categories',     'label' => 'Categories',    'icon' => 'fa-solid fa-tags',            'route' => 'admin.categories'],
                    ['id' => 'recipients',     'label' => 'For Everyone',  'icon' => 'fa-solid fa-users-viewfinder', 'route' => 'admin.recipients'],
                ],
                'Engagement' => [
                    ['id' => 'customers',      'label' => 'Customers',     'icon' => 'fa-solid fa-users',           'route' => 'admin.customers'],
                    ['id' => 'reviews',        'label' => 'Reviews',       'icon' => 'fa-solid fa-star',            'route' => 'admin.reviews'],
                    ['id' => 'testimonials',   'label' => 'FAQs',          'icon' => 'fa-solid fa-circle-question', 'route' => 'admin.faqs'],
                ],
                'Website CMS' => [
                    ['id' => 'hero-manager',   'label' => 'Hero Slider',   'icon' => 'fa-solid fa-images',          'route' => 'admin.hero'],
                    ['id' => 'about-manager',  'label' => 'About Page',    'icon' => 'fa-solid fa-address-card',    'route' => 'admin.about'],
                    ['id' => 'legal-manager',  'label' => 'Legal Pages',   'icon' => 'fa-solid fa-scale-balanced',  'route' => 'admin.legal'],
                    ['id' => 'settings',       'label' => 'Settings',      'icon' => 'fa-solid fa-sliders',         'route' => 'admin.settings'],
                ]
            ];

            // Count pending bookings for badge
            $pendingBookingsCount = 0;
            try {
                $allBookings = \App\Services\JsonStorageService::read('bookings.json', []);
                $pendingBookingsCount = count(array_filter($allBookings, fn($b) => ($b['status'] ?? '') === 'pending'));
            } catch (\Exception $e) {}
        @endphp

        @foreach($sections as $sectionName => $items)
            <div class="sidebar-section-title px-3 pt-3 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider select-none">
                {{ $sectionName }}
            </div>

            @foreach($items as $item)
                @php
                    $isCurrentRoute = false;
                    try {
                        $isCurrentRoute = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*');
                    } catch(\Exception $e) {}

                    $isActive = false;
                    if (!empty($active)) {
                        $isActive = ($active === $item['id']);
                    } else {
                        $isActive = $isCurrentRoute;
                    }

                    // Default to overview ONLY when actually on admin.dashboard
                    if (!$isActive && empty($active) && request()->routeIs('admin.dashboard') && $item['id'] === 'overview') {
                        $isActive = true;
                    }

                    $routeUrl = '#';
                    try { $routeUrl = route($item['route']); } catch(\Exception $e) {}
                @endphp
                <a href="{{ $routeUrl }}"
                   data-tab-btn="{{ $item['id'] }}"
                   title="{{ $item['label'] }}"
                   class="sidebar-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ $isActive ? 'is-active text-gray-900 bg-gray-100 font-bold border border-gray-200/70 shadow-2xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/70' }}">
                    <i class="{{ $item['icon'] }} text-xs w-4 text-center shrink-0 {{ $isActive ? 'text-gray-900' : 'text-gray-400' }}"></i>
                    <span class="sidebar-text flex-grow truncate">{{ $item['label'] }}</span>
                    @if(isset($item['badge']) && $item['badge'] && $pendingBookingsCount > 0)
                        <span class="sidebar-badge-pill ml-auto bg-amber-500 text-white font-extrabold text-[10px] px-2 py-0.5 rounded-full leading-none shrink-0 shadow-2xs">
                            {{ $pendingBookingsCount }}
                        </span>
                    @endif
                </a>
            @endforeach
        @endforeach
    </nav>

    <!-- Footer Controls -->
    <div class="p-3 border-t border-gray-200/80 bg-white space-y-2 shrink-0">
        <!-- Live Site Link -->
        <a href="/" 
           target="_blank" 
           title="View Live Site"
           class="sidebar-footer-btn flex items-center justify-center gap-2 w-full bg-white hover:bg-gray-50 border border-gray-200/90 text-gray-700 py-2.5 px-3 rounded-xl text-xs font-semibold transition-colors shadow-2xs cursor-pointer">
            <i class="fa-solid fa-arrow-up-right-from-square text-xs text-gray-400 shrink-0"></i>
            <span class="sidebar-text truncate">View Live Site</span>
        </a>

        <!-- Logout Form -->
        <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" 
                    title="Logout"
                    class="sidebar-footer-btn flex items-center justify-center gap-2 w-full bg-rose-50 hover:bg-rose-100 border border-rose-200/80 text-rose-600 py-2 px-3 rounded-xl text-xs font-bold transition-colors cursor-pointer shadow-2xs">
                <i class="fa-solid fa-right-from-bracket text-xs shrink-0"></i>
                <span class="sidebar-text truncate">Logout</span>
            </button>
        </form>
    </div>
</aside>

<!-- ===================== SIDEBAR CONTROLLER SCRIPTS ===================== -->
<script>
    function toggleAdminSidebar() {
        if (window.innerWidth < 768) {
            toggleAdminSidebarMobile();
        } else {
            toggleAdminSidebarDesktop();
        }
    }

    function toggleAdminSidebarDesktop() {
        const isCollapsed = document.documentElement.classList.toggle('sidebar-collapsed');
        document.body.classList.toggle('sidebar-collapsed', isCollapsed);
        try {
            localStorage.setItem('artizen_admin_sidebar_collapsed', isCollapsed ? 'true' : 'false');
        } catch(e) {}
        
        const btn = document.getElementById('sidebar-desktop-toggle-btn');
        if (btn) {
            btn.setAttribute('title', isCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar');
        }
    }

    function openAdminSidebarMobile() {
        document.body.classList.add('mobile-sidebar-open');
        const backdrop = document.getElementById('sidebar-mobile-backdrop');
        if (backdrop) backdrop.classList.remove('hidden');
    }

    function closeAdminSidebarMobile() {
        document.body.classList.remove('mobile-sidebar-open');
        const backdrop = document.getElementById('sidebar-mobile-backdrop');
        if (backdrop) backdrop.classList.add('hidden');
    }

    function toggleAdminSidebarMobile() {
        if (document.body.classList.contains('mobile-sidebar-open')) {
            closeAdminSidebarMobile();
        } else {
            openAdminSidebarMobile();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (document.documentElement.classList.contains('sidebar-collapsed')) {
            document.body.classList.add('sidebar-collapsed');
            const btn = document.getElementById('sidebar-desktop-toggle-btn');
            if (btn) btn.setAttribute('title', 'Expand Sidebar');
        }
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            closeAdminSidebarMobile();
        }
    });

    /* Universal Admin User Dropdown Toggle */
    function toggleAdminUserDropdown(event) {
        if (event) event.stopPropagation();
        const menu = document.getElementById('admin-user-dropdown-menu');
        const chevron = document.getElementById('admin-dropdown-chevron');
        const trigger = document.getElementById('admin-dropdown-trigger');
        if (!menu) return;
        
        const isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            menu.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');
            if (trigger) trigger.setAttribute('aria-expanded', 'true');
        } else {
            menu.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
        }
    }

    document.addEventListener('click', function(event) {
        const container = document.getElementById('admin-user-dropdown-container');
        const menu = document.getElementById('admin-user-dropdown-menu');
        const chevron = document.getElementById('admin-dropdown-chevron');
        const trigger = document.getElementById('admin-dropdown-trigger');
        if (container && !container.contains(event.target)) {
            if (menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
                if (trigger) trigger.setAttribute('aria-expanded', 'false');
            }
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const menu = document.getElementById('admin-user-dropdown-menu');
            const chevron = document.getElementById('admin-dropdown-chevron');
            const trigger = document.getElementById('admin-dropdown-trigger');
            if (menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
                if (trigger) trigger.setAttribute('aria-expanded', 'false');
            }
        }
    });
</script>

@props(['active' => 'overview'])

<!-- ===================== COMMON ADMIN SIDEBAR ===================== -->
<aside class="w-full md:w-64 bg-white border-r border-gray-200 flex flex-col shrink-0 md:sticky md:top-0 md:h-screen z-40">
    <!-- Brand Header -->
    <div class="h-16 border-b border-gray-200 flex items-center px-5 justify-between bg-white">
        <a href="/" class="flex items-center gap-3 group">
            <img src="{{ asset('assets/images/logo/artizen.png') }}" alt="Artizen Logo" class="h-9 w-auto object-contain transition-transform duration-300 group-hover:scale-105 rounded">
            <div>
                <span class="font-bold text-sm text-gray-900 block leading-tight">Artizen</span>
                <span class="block text-[11px] text-gray-500 font-medium">Admin Dashboard</span>
            </div>
        </a>
        <button onclick="toggleMobileSidebar()" class="md:hidden text-gray-500 hover:text-gray-900 transition-colors">
            <i class="fa-solid fa-bars text-base"></i>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="flex-1 px-3 py-4 flex flex-col gap-1 overflow-y-auto" id="sidebar-nav">
        @php
            $navItems = [
                ['id' => 'overview', 'label' => 'Overview', 'icon' => 'fa-solid fa-chart-line'],
                ['id' => 'bookings', 'label' => 'Bookings', 'icon' => 'fa-solid fa-calendar-check', 'badge' => '4'],
                ['id' => 'packages', 'label' => 'Packages', 'icon' => 'fa-solid fa-box'],
                ['id' => 'categories', 'label' => 'Categories', 'icon' => 'fa-solid fa-tags'],
                ['id' => 'customers', 'label' => 'Customers', 'icon' => 'fa-solid fa-users'],
                ['id' => 'testimonials', 'label' => 'FAQ & Reviews', 'icon' => 'fa-solid fa-comments'],
                ['id' => 'hero-manager', 'label' => 'Hero Slider', 'icon' => 'fa-solid fa-images'],
                ['id' => 'about-manager', 'label' => 'About Page', 'icon' => 'fa-solid fa-address-card'],
                ['id' => 'settings', 'label' => 'Settings', 'icon' => 'fa-solid fa-sliders'],
            ];
        @endphp

        @foreach($navItems as $item)
            @php
                $isActive = ($active === $item['id']);
                $btnClasses = $isActive 
                    ? 'flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-150 text-gray-900 bg-gray-100 text-left w-full' 
                    : 'flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 text-gray-600 hover:text-gray-900 hover:bg-gray-50 text-left w-full';
            @endphp
            <a href="/admin/dashboard?tab={{ $item['id'] }}" 
               onclick="if(typeof switchTab === 'function'){ switchTab('{{ $item['id'] }}'); return false; }" 
               data-tab-btn="{{ $item['id'] }}" 
               class="{{ $btnClasses }}">
                <i class="{{ $item['icon'] }} text-xs w-4 text-center shrink-0 text-gray-500"></i>
                <span class="flex-grow">{{ $item['label'] }}</span>
                @if(isset($item['badge']))
                    <span class="ml-auto bg-gray-900 text-white font-semibold text-xs px-2 py-0.5 rounded-full leading-none" id="sidebar-pending-badge">{{ $item['badge'] }}</span>
                @endif
            </a>
        @endforeach
    </nav>

    <!-- Footer Logout & Website Quick View -->
    <div class="p-4 border-t border-gray-200 flex flex-col gap-2 bg-white">
        <a href="/" target="_blank" class="flex items-center justify-center gap-2 w-full bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 py-2 px-3 rounded-lg text-xs font-semibold transition-colors shadow-sm">
            <i class="fa-solid fa-globe text-xs"></i> View Live Site
        </a>
    </div>
</aside>

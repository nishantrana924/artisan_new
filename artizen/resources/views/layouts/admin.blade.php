<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'Admin') | Artizen Admin Panel</title>

    <!-- Tailwind Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    @stack('head_scripts')
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'main-bg':      '#FFFFFF',
                        'card-bg':      '#FFFFFF',
                        'surface-bg':   '#F4F1DE',
                        'gold':         '#EA741D',
                        'gold-light':   '#F5A25D',
                        'border-color': '#E5E7EB',
                        'muted-text':   '#71717A',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        html {
            background-color: #F9FAFB !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        body {
            background-color: #F9FAFB !important;
            color: #111827 !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14.5px;
            overflow-x: hidden;
            margin: 0 !important;
            padding: 0 !important;
        }
        label {
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #374151 !important;
            margin-bottom: 0.25rem !important;
            display: block !important;
        }
        input[type="text"], input[type="number"], input[type="url"], input[type="email"], select, textarea {
            font-size: 14px !important;
            color: #111827 !important;
            background-color: #FFFFFF !important;
            border: 1px solid #D1D5DB !important;
            border-radius: 8px !important;
            padding: 9px 13px !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
        }
        input[type="text"]:focus, input[type="number"]:focus, input[type="url"]:focus, select:focus, textarea:focus {
            border-color: #111827 !important;
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(17, 24, 39, 0.08) !important;
        }
        input.pl-10, input[class*="pl-10"] { padding-left: 2.5rem !important; }
        input.pl-8,  input[class*="pl-8"]  { padding-left: 2.25rem !important; }
        table th {
            font-size: 12px !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            color: #6B7280 !important;
            background-color: #F9FAFB !important;
        }
        table td { font-size: 14px !important; color: #111827 !important; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track  { background: #F9FAFB !important; }
        ::-webkit-scrollbar-thumb  { background: #D1D5DB !important; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #9CA3AF !important; }
        button.bg-gold, a.bg-gold, .bg-gold { color: #000000 !important; }
        #sidebar-nav a { color: #4B5563; }
        #sidebar-nav a:hover { color: #111827; background-color: #F3F4F6; }
        #sidebar-nav a.is-active {
            color: #111827 !important;
            background-color: #F3F4F6 !important;
            font-weight: 700 !important;
        }

        /* Prevent any focus ring / outline on admin user button */
        #admin-dropdown-trigger,
        #admin-dropdown-trigger:focus,
        #admin-dropdown-trigger:active,
        #admin-dropdown-trigger:focus-visible {
            outline: none !important;
            box-shadow: none !important;
            border: none !important;
        }

        /* CKEditor — general */
        .ck-editor__editable {
            min-height: 120px !important;
            font-size: 13px !important;
            color: #1E1E24 !important;
            background-color: #FFFFFF !important;
            text-align: left !important;
        }

        /* Legal page editors — tall with live-preview styles */
        .legal-ck-wrapper .ck-editor__editable {
            min-height: 420px !important;
            font-size: 14px !important;
            line-height: 1.75 !important;
            padding: 20px 24px !important;
        }
        .legal-ck-wrapper .ck-editor__editable h2 {
            font-weight: 800 !important; font-size: 1rem !important;
            color: #030712 !important; margin-top: 1.4rem !important;
            margin-bottom: 0.6rem !important; display: flex !important;
            align-items: center !important; gap: 0.5rem !important;
        }
        .legal-ck-wrapper .ck-editor__editable h3 {
            font-weight: 700 !important; font-size: 0.9rem !important;
            color: #111827 !important; margin-top: 1.1rem !important; margin-bottom: 0.4rem !important;
        }
        .legal-ck-wrapper .ck-editor__editable p  { margin-bottom: 0.9rem !important; color: #374151 !important; line-height: 1.75 !important; }
        .legal-ck-wrapper .ck-editor__editable ul,
        .legal-ck-wrapper .ck-editor__editable ol  { padding-left: 1.5rem !important; margin-bottom: 0.9rem !important; }
        .legal-ck-wrapper .ck-editor__editable li  { margin-bottom: 0.3rem !important; color: #4b5563 !important; }
        .legal-ck-wrapper .ck-editor__editable blockquote {
            border-left: 4px solid #e8dfc8 !important; background-color: #faf7f2 !important;
            padding: 0.7rem 1rem !important; border-radius: 0.4rem !important;
            font-style: italic !important; color: #6b7280 !important; margin-bottom: 0.9rem !important;
        }
    </style>

    {{-- Chart.js (needed for overview analytics used in all pages) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Page-specific head scripts (e.g. CKEditor) --}}
    @stack('head_scripts')
</head>

<body class="md:h-screen md:overflow-hidden flex flex-col md:flex-row bg-main-bg font-sans antialiased text-[#1E1E24]">

    <!-- ===================== COMMON ADMIN SIDEBAR ===================== -->
    @include('layouts.admin-sidebar', ['active' => $adminActivePage ?? null])

    <!-- ===================== MAIN HEADER & VIEW WRAPPER ===================== -->
    <main class="flex-grow flex flex-col min-w-0 md:h-screen md:overflow-y-auto">

        <!-- Top Navigation / Modern Header Bar -->
        <header class="py-2.5 px-4 md:px-8 border-b border-gray-200/80 bg-white/95 backdrop-blur-sm shrink-0 sticky top-0 z-30">
            <div class="max-w-[1600px] mx-auto flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <!-- Mobile-only drawer toggle (hidden on desktop) -->
                    <button type="button" 
                            onclick="toggleAdminSidebarMobile()" 
                            class="md:hidden w-9 h-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-100 flex items-center justify-center text-gray-600 hover:text-gray-900 transition-colors shadow-2xs cursor-pointer shrink-0" 
                            title="Open Menu">
                        <i class="fa-solid fa-bars-staggered text-sm"></i>
                    </button>
                    <span class="text-base font-extrabold text-gray-900 tracking-tight select-none">Artizen</span>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Modern Notification Bell -->
                    <div class="w-9 h-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center text-gray-600 hover:text-gray-900 transition-colors relative cursor-pointer shadow-2xs" title="Notifications">
                        <i class="fa-solid fa-bell text-xs"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-amber-500 ring-2 ring-white"></span>
                    </div>

                    <!-- Administrator Avatar & Dropdown Menu -->
                    <div class="relative pl-1 border-l border-gray-200" id="admin-user-dropdown-container">
                        <!-- Dropdown Trigger Button -->
                        <button type="button" 
                                id="admin-dropdown-trigger" 
                                onclick="toggleAdminUserDropdown(event)" 
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl hover:bg-gray-100/70 transition-colors cursor-pointer group border-0 outline-none focus:outline-none ring-0 focus:ring-0 active:outline-none"
                                aria-expanded="false" 
                                aria-haspopup="true">
                            <div class="w-8 h-8 rounded-xl bg-gray-900 text-white flex items-center justify-center text-xs font-black shadow-2xs shrink-0 group-hover:scale-105 transition-transform">
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="hidden md:block text-left select-none">
                                <span class="block text-xs font-bold text-gray-900 leading-tight group-hover:text-gray-950">{{ Auth::user()->name ?? 'Artizen Admin' }}</span>
                                <span class="block text-[10px] text-gray-400 font-medium">Administrator</span>
                            </div>
                            <i id="admin-dropdown-chevron" class="fa-solid fa-chevron-down text-[10px] text-gray-400 group-hover:text-gray-600 transition-transform duration-200 ml-0.5"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="admin-user-dropdown-menu" 
                             class="hidden absolute right-0 mt-2 w-56 bg-white rounded-2xl border border-gray-200 shadow-xl py-1.5 z-50 transition-all duration-150 origin-top-right select-none">
                            
                            <!-- User info snippet -->
                            <div class="px-4 py-2.5 border-b border-gray-100">
                                <p class="text-xs font-bold text-gray-900 truncate">{{ Auth::user()->name ?? 'Artizen Admin' }}</p>
                                <p class="text-[11px] text-gray-400 truncate">{{ Auth::user()->email ?? 'admin@artizen.com' }}</p>
                            </div>

                            <div class="py-1">
                                <!-- Profile / Settings link -->
                                <a href="{{ route('admin.settings') }}" 
                                   class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-gray-700 hover:text-gray-900 hover:bg-gray-50 transition-colors">
                                    <i class="fa-solid fa-user-gear text-gray-400 w-4 text-center"></i>
                                    <span>Profile & Settings</span>
                                </a>

                                <!-- View Live Site link -->
                                <a href="/" target="_blank"
                                   class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-gray-700 hover:text-gray-900 hover:bg-gray-50 transition-colors">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-gray-400 w-4 text-center"></i>
                                    <span>View Live Site</span>
                                </a>
                            </div>

                            <div class="border-t border-gray-100 pt-1">
                                <!-- Logout Action -->
                                <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" 
                                            class="flex items-center gap-2.5 w-full text-left px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50/80 transition-colors cursor-pointer">
                                        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Page Content Area -->
        <div class="flex-grow p-6 md:p-8 overflow-y-auto">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm font-semibold mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm font-semibold mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-red-500"></i> {{ session('error') }}
                </div>
            @endif

            @yield('content')

        </div>
    </main>

    @stack('modals')

    {{-- Global Delete Confirmation Modal --}}
    @include('admin.components.delete-modal')

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div id="toast-container" class="fixed bottom-6 right-6 z-[100] flex flex-col gap-3"></div>

    <script>
        function showToast(message, type = 'success') {
            const colors = {
                success: 'bg-emerald-600 text-white',
                error:   'bg-red-600 text-white',
                info:    'bg-blue-600 text-white',
            };
            const icons = { success: 'fa-circle-check', error: 'fa-circle-exclamation', info: 'fa-circle-info' };
            const toast = document.createElement('div');
            toast.className = `flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl text-sm font-semibold min-w-[260px] max-w-sm transition-all duration-300 opacity-0 translate-y-2 ${colors[type] || colors.success}`;
            toast.innerHTML = `<i class="fa-solid ${icons[type] || icons.success} shrink-0"></i><span>${message}</span>`;
            document.getElementById('toast-container').prepend(toast);
            requestAnimationFrame(() => { toast.classList.remove('opacity-0', 'translate-y-2'); });
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 400);
            }, 4000);
        }

        /* Admin User Dropdown Toggle */
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

    @stack('page_scripts')

</body>
</html>

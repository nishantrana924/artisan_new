<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, interactive-widget=resizes-content">
    <title>@yield('page_title', 'Admin') | Artizen Admin Panel</title>

    <!-- Favicons & App Icons (Matching Public Site) -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/logo/artizen.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/logo/artizen.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/logo/artizen.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

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
            font-size: 14px;
            color: #111827 !important;
            background-color: #FFFFFF !important;
            border: 1px solid #D1D5DB !important;
            border-radius: 8px !important;
            padding: 9px 13px !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
        }

        /* Prevent automatic zoom-in on iPhone/iOS mobile inputs & disable double-tap/pinch zoom */
        @media screen and (max-width: 899px) {
            html body input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]):not([type="button"]):not([type="submit"]):not([type="reset"]),
            html body select,
            html body textarea,
            html body input[class],
            html body select[class],
            html body textarea[class],
            input.text-xs,
            select.text-xs,
            textarea.text-xs,
            input.text-sm,
            select.text-sm,
            textarea.text-sm {
                font-size: 16px !important;
            }
        }

        html, body, button, input, select, textarea, a {
            touch-action: manipulation;
        }
        input[type="text"]:focus, input[type="number"]:focus, input[type="url"]:focus, select:focus, textarea:focus {
            border-color: #111827 !important;
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(17, 24, 39, 0.08) !important;
        }
        input.pl-10, input[class*="pl-10"], input.admin-input-icon { padding-left: 2.6rem !important; }
        input.pl-8,  input[class*="pl-8"]  { padding-left: 2.25rem !important; }
        input.pr-10, input[class*="pr-10"], input.admin-input-eye { padding-right: 2.6rem !important; }
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
                    <!-- Modern Notification Bell & Real-time Dropdown -->
                    <div class="relative" id="admin-notifications-container">
                        <button type="button" 
                                id="admin-notifications-trigger" 
                                onclick="toggleAdminNotificationsDropdown(event)" 
                                class="w-9 h-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center text-gray-600 hover:text-gray-900 transition-colors relative cursor-pointer shadow-2xs group outline-none" 
                                title="Notifications"
                                aria-expanded="false"
                                aria-haspopup="true">
                            <i class="fa-solid fa-bell text-xs group-hover:scale-110 transition-transform"></i>
                            <span id="admin-bell-badge" class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[10px] font-extrabold flex items-center justify-center ring-2 ring-white shadow-xs">0</span>
                        </button>

                        <!-- Notifications Dropdown Menu -->
                        <div id="admin-notifications-menu" 
                             class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl border border-gray-200 shadow-2xl py-2 z-50 transition-all duration-150 origin-top-right">
                            <div class="px-4 py-2.5 border-b border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-extrabold text-gray-900">Live Notifications</span>
                                    <span id="admin-notif-count-pill" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">0 Pending</span>
                                </div>
                                <span class="text-[10px] text-gray-400 font-medium">Real-time alerts</span>
                            </div>
                            
                            <div id="admin-notifications-list" class="max-h-72 overflow-y-auto divide-y divide-gray-50 text-xs">
                                <div class="p-6 text-center text-gray-400" id="admin-notif-empty">
                                    <i class="fa-regular fa-bell text-gray-300 text-2xl mb-2 block"></i>
                                    <p class="text-xs font-semibold text-gray-600">No new notifications</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">New bookings and requests will appear here</p>
                                </div>
                            </div>

                            <div class="p-2 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                                <a href="{{ route('admin.bookings') }}" class="block w-full py-2 text-center text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:bg-white rounded-xl transition-colors">
                                    View All Bookings &rarr;
                                </a>
                            </div>
                        </div>
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
                            <div class="w-8 h-8 rounded-xl bg-gray-900 text-white flex items-center justify-center text-xs font-black shadow-2xs shrink-0 group-hover:scale-105 transition-transform overflow-hidden">
                                @if(!empty(Auth::user()->avatar))
                                    <img src="{{ Auth::user()->avatar }}" alt="Admin" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                @endif
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

            @yield('content')

        </div>
    </main>

    @stack('modals')

    {{-- Global Delete Confirmation Modal --}}
    @include('admin.components.delete-modal')

    <!-- MODERN FLOATING TOASTER NOTIFICATION CONTAINER (Top Right) -->
    <div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none max-w-sm w-full px-4 sm:px-0"></div>

    <script>
        /**
         * Global Premium Interactive Toaster Notification Engine
         */
        window.showToast = function(message, type = 'success', duration = 4500, actionUrl = null) {
            const configs = {
                success: {
                    title: 'Success',
                    iconBg: 'bg-emerald-50 border border-emerald-200/80',
                    iconColor: 'text-emerald-600',
                    icon: 'fa-circle-check',
                    accentBorder: 'border-l-4 border-l-emerald-500',
                },
                error: {
                    title: 'Error',
                    iconBg: 'bg-rose-50 border border-rose-200/80',
                    iconColor: 'text-rose-600',
                    icon: 'fa-triangle-exclamation',
                    accentBorder: 'border-l-4 border-l-rose-500',
                },
                info: {
                    title: 'Notice',
                    iconBg: 'bg-indigo-50 border border-indigo-200/80',
                    iconColor: 'text-indigo-600',
                    icon: 'fa-bell',
                    accentBorder: 'border-l-4 border-l-indigo-500',
                },
                warning: {
                    title: 'Warning',
                    iconBg: 'bg-amber-50 border border-amber-200/80',
                    iconColor: 'text-amber-600',
                    icon: 'fa-circle-exclamation',
                    accentBorder: 'border-l-4 border-l-amber-500',
                },
            };

            const cfg = configs[type] || configs.success;
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto bg-white/95 backdrop-blur-md border border-gray-200/90 shadow-2xl rounded-2xl p-4 flex items-start gap-3.5 transition-all duration-300 transform translate-x-8 opacity-0 ${cfg.accentBorder} relative overflow-hidden select-none cursor-pointer group`;
            
            const linkBtn = actionUrl ? `<a href="${actionUrl}" class="inline-flex items-center gap-1.5 text-[11px] font-extrabold text-indigo-600 hover:text-indigo-800 mt-2 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg transition-colors"><span>View Details</span> <i class="fa-solid fa-arrow-right text-[9px]"></i></a>` : '';

            toast.innerHTML = `
                <div class="w-8 h-8 rounded-xl ${cfg.iconBg} ${cfg.iconColor} flex items-center justify-center shrink-0 text-xs font-bold mt-0.5 shadow-2xs">
                    <i class="fa-solid ${cfg.icon}"></i>
                </div>
                <div class="flex-grow min-w-0 pr-1">
                    <div class="flex items-center justify-between gap-2">
                        <h4 class="text-xs font-extrabold text-gray-900 tracking-tight">${cfg.title}</h4>
                        <span class="text-[10px] text-gray-400 font-medium">Just now</span>
                    </div>
                    <p class="text-xs text-gray-600 font-semibold mt-0.5 leading-relaxed break-words">${message}</p>
                    ${linkBtn}
                </div>
                <button type="button" class="w-6 h-6 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-400 hover:text-gray-700 flex items-center justify-center shrink-0 cursor-pointer transition-colors" title="Dismiss">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            `;

            const closeBtn = toast.querySelector('button');
            const dismiss = (e) => {
                if (e) e.stopPropagation();
                toast.classList.add('translate-x-8', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            };
            if (closeBtn) closeBtn.onclick = dismiss;

            if (actionUrl) {
                toast.addEventListener('click', (e) => {
                    if (e.target !== closeBtn && !closeBtn.contains(e.target)) {
                        window.location.href = actionUrl;
                    }
                });
            }

            container.prepend(toast);
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-8', 'opacity-0');
            });

            if (duration > 0) {
                setTimeout(dismiss, duration);
            }
        };

        // Flash Session Toaster Auto-Triggers (Runs automatically on redirect or load)
        function triggerSessionToasts() {
            @if(session('success'))
                showToast(@json(session('success')), 'success');
            @endif
            @if(session('error'))
                showToast(@json(session('error')), 'error');
            @endif
            @if(session('info'))
                showToast(@json(session('info')), 'info');
            @endif
            @if(session('warning'))
                showToast(@json(session('warning')), 'warning');
            @endif
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', triggerSessionToasts);
        } else {
            triggerSessionToasts();
        }


        /**
         * Web Audio Notification Chime
         */
        function playNotificationChime() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5 note
                osc.frequency.setValueAtTime(880, ctx.currentTime + 0.1); // A5 note
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.45);
                osc.start();
                osc.stop(ctx.currentTime + 0.45);
            } catch (e) {
                // AudioContext not allowed before user gesture or unavailable
            }
        }

        /**
         * Real-time Notifications Polling Engine
         */
        let lastNotificationCheckTime = Math.floor(Date.now() / 1000);
        let lastKnownBookingId = null;
        let isInitialPoll = true;

        async function pollAdminNotifications() {
            try {
                let url = '{{ route("admin.notifications.poll") }}';
                const params = new URLSearchParams();
                if (!isInitialPoll && lastNotificationCheckTime) {
                    params.append('since', lastNotificationCheckTime);
                }
                if (lastKnownBookingId) {
                    params.append('last_id', lastKnownBookingId);
                }
                const fullUrl = url + (params.toString() ? '?' + params.toString() : '');
                const res = await fetch(fullUrl, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) return;
                const data = await res.json();
                
                if (data.server_time) {
                    lastNotificationCheckTime = data.server_time;
                }

                // Update bell badge
                const badge = document.getElementById('admin-bell-badge');
                const pill  = document.getElementById('admin-notif-count-pill');
                if (badge) {
                    if (data.pending_count > 0) {
                        badge.textContent = data.pending_count > 99 ? '99+' : data.pending_count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
                if (pill) {
                    pill.textContent = `${data.pending_count} Pending`;
                }

                // Render list in dropdown
                if (data.latest_bookings && data.latest_bookings.length > 0) {
                    renderNotificationsList(data.latest_bookings);
                    if (isInitialPoll && data.latest_id) {
                        lastKnownBookingId = data.latest_id;
                    }
                }

                // Show live toaster alert on new bookings
                if (!isInitialPoll && data.new_bookings && data.new_bookings.length > 0) {
                    data.new_bookings.forEach(b => {
                        playNotificationChime();
                        showToast(`🎉 New Booking: ${b.customer_name} booked ${b.package_name} (₹${Number(b.total_amount).toLocaleString('en-IN')})`, 'info', 8000, `{{ route('admin.bookings') }}?search=${encodeURIComponent(b.id)}`);
                    });
                    if (data.latest_id) {
                        lastKnownBookingId = data.latest_id;
                    }
                }

                isInitialPoll = false;
            } catch (err) {
                // Ignore transient network errors
            }
        }

        function renderNotificationsList(bookings) {
            const list = document.getElementById('admin-notifications-list');
            if (!list) return;
            if (!bookings || bookings.length === 0) {
                list.innerHTML = `
                    <div class="p-6 text-center text-gray-400">
                        <i class="fa-regular fa-bell text-gray-300 text-2xl mb-2 block"></i>
                        <p class="text-xs font-semibold text-gray-600">No new notifications</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">New bookings and requests will appear here</p>
                    </div>
                `;
                return;
            }

            const statusColors = {
                'Pending': 'bg-amber-50 text-amber-700 border-amber-200',
                'Confirmed': 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'Completed': 'bg-blue-50 text-blue-700 border-blue-200',
                'Cancelled': 'bg-rose-50 text-rose-700 border-rose-200',
            };

            list.innerHTML = bookings.map(b => {
                const badgeClass = statusColors[b.status] || 'bg-gray-50 text-gray-700 border-gray-200';
                return `
                    <a href="{{ route('admin.bookings') }}?search=${encodeURIComponent(b.id)}" class="p-3.5 hover:bg-gray-50/80 transition-colors flex items-start gap-3 block border-b border-gray-50 last:border-b-0">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 text-xs font-bold mt-0.5 shadow-2xs">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <p class="font-extrabold text-gray-900 truncate text-xs">${b.customer_name}</p>
                                <span class="text-[10px] text-gray-400 shrink-0 font-medium">${b.created_diff}</span>
                            </div>
                            <p class="text-[11px] text-gray-500 truncate mt-0.5">${b.package_name} • ₹${Number(b.total_amount).toLocaleString('en-IN')}</p>
                            <div class="flex items-center gap-1.5 mt-1.5">
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold border ${badgeClass}">${b.status}</span>
                                <span class="text-[9px] font-mono text-gray-400 font-bold">${b.id}</span>
                            </div>
                        </div>
                    </a>
                `;
            }).join('');
        }

        // Start polling on load and repeat every 15 seconds
        document.addEventListener('DOMContentLoaded', function() {
            pollAdminNotifications();
            setInterval(pollAdminNotifications, 15000);
        });

        /* Admin Notification Dropdown Toggle */
        function toggleAdminNotificationsDropdown(event) {
            if (event) event.stopPropagation();
            const menu = document.getElementById('admin-notifications-menu');
            const trigger = document.getElementById('admin-notifications-trigger');
            if (!menu) return;

            // Close user dropdown if open
            const userMenu = document.getElementById('admin-user-dropdown-menu');
            const userChevron = document.getElementById('admin-dropdown-chevron');
            const userTrigger = document.getElementById('admin-dropdown-trigger');
            if (userMenu && !userMenu.classList.contains('hidden')) {
                userMenu.classList.add('hidden');
                if (userChevron) userChevron.classList.remove('rotate-180');
                if (userTrigger) userTrigger.setAttribute('aria-expanded', 'false');
            }
            
            const isHidden = menu.classList.contains('hidden');
            if (isHidden) {
                menu.classList.remove('hidden');
                if (trigger) trigger.setAttribute('aria-expanded', 'true');
            } else {
                menu.classList.add('hidden');
                if (trigger) trigger.setAttribute('aria-expanded', 'false');
            }
        }

        /* Admin User Dropdown Toggle */
        function toggleAdminUserDropdown(event) {
            if (event) event.stopPropagation();
            const menu = document.getElementById('admin-user-dropdown-menu');
            const chevron = document.getElementById('admin-dropdown-chevron');
            const trigger = document.getElementById('admin-dropdown-trigger');
            if (!menu) return;

            // Close notification menu if open
            const notifMenu = document.getElementById('admin-notifications-menu');
            const notifTrigger = document.getElementById('admin-notifications-trigger');
            if (notifMenu && !notifMenu.classList.contains('hidden')) {
                notifMenu.classList.add('hidden');
                if (notifTrigger) notifTrigger.setAttribute('aria-expanded', 'false');
            }
            
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
            const notifContainer = document.getElementById('admin-notifications-container');
            const notifMenu = document.getElementById('admin-notifications-menu');
            const notifTrigger = document.getElementById('admin-notifications-trigger');
            if (notifContainer && !notifContainer.contains(event.target)) {
                if (notifMenu && !notifMenu.classList.contains('hidden')) {
                    notifMenu.classList.add('hidden');
                    if (notifTrigger) notifTrigger.setAttribute('aria-expanded', 'false');
                }
            }

            const userContainer = document.getElementById('admin-user-dropdown-container');
            const userMenu = document.getElementById('admin-user-dropdown-menu');
            const chevron = document.getElementById('admin-dropdown-chevron');
            const trigger = document.getElementById('admin-dropdown-trigger');
            if (userContainer && !userContainer.contains(event.target)) {
                if (userMenu && !userMenu.classList.contains('hidden')) {
                    userMenu.classList.add('hidden');
                    if (chevron) chevron.classList.remove('rotate-180');
                    if (trigger) trigger.setAttribute('aria-expanded', 'false');
                }
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const notifMenu = document.getElementById('admin-notifications-menu');
                const notifTrigger = document.getElementById('admin-notifications-trigger');
                if (notifMenu && !notifMenu.classList.contains('hidden')) {
                    notifMenu.classList.add('hidden');
                    if (notifTrigger) notifTrigger.setAttribute('aria-expanded', 'false');
                }

                const userMenu = document.getElementById('admin-user-dropdown-menu');
                const chevron = document.getElementById('admin-dropdown-chevron');
                const trigger = document.getElementById('admin-dropdown-trigger');
                if (userMenu && !userMenu.classList.contains('hidden')) {
                    userMenu.classList.add('hidden');
                    if (chevron) chevron.classList.remove('rotate-180');
                    if (trigger) trigger.setAttribute('aria-expanded', 'false');
                }
            }
        });
    </script>

    @stack('page_scripts')

</body>
</html>

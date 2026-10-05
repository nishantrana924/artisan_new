<!DOCTYPE html>
<!-- Artizen Admin Package Form - Updated View -->
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isEdit ? 'Edit Package Tier' : 'Add Package Tier' }} - Artizen Admin</title>

    <!-- Tailwind Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <!-- Classic CKEditor CDN -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>

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
                        'main-bg': '#FFFFFF',
                        'card-bg': '#FFFFFF',
                        'surface-bg': '#F4F1DE',
                        'gold': '#EA741D',
                        'gold-light': '#F5A25D',
                        'border-color': '#E5E7EB',
                        'muted-text': '#71717A',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #F9FAFB !important;
            color: #111827 !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Clean SaaS Input & Form Overrides */
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

        /* Fix left padding for inputs with left-aligned icons */
        input.pl-10, input[class*="pl-10"] {
            padding-left: 2.5rem !important; /* 40px left padding */
        }
        input.pl-8, input[class*="pl-8"] {
            padding-left: 2.25rem !important; /* 36px left padding */
        }

        /* Custom Modern Select Dropdown Styling */
        select {
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 0.75rem center !important;
            background-size: 1.1em 1.1em !important;
            padding-right: 2.5rem !important;
            cursor: pointer !important;
        }

        select:hover {
            border-color: #9CA3AF !important;
            background-color: #FAFAFA !important;
        }

        select:focus {
            border-color: #111827 !important;
            box-shadow: 0 0 0 3px rgba(17, 24, 39, 0.08) !important;
            background-color: #FFFFFF !important;
        }

        select option {
            padding: 10px 14px !important;
            font-size: 13.5px !important;
            color: #111827 !important;
            background-color: #FFFFFF !important;
        }

        select option[value="__add_new__"] {
            font-weight: 700 !important;
            color: #D97706 !important;
            background-color: #FFFBEB !important;
        }

        /* CKEditor Custom Styling & Bullet List Fix for Light SaaS Theme */
        .ck-editor__editable {
            min-height: 120px !important;
            font-size: 14px !important;
            color: #111827 !important;
            background-color: #FFFFFF !important;
            border-radius: 0 0 8px 8px !important;
            text-align: left !important;
            padding: 12px 16px !important;
        }

        .ck-editor__main .ck-content {
            background-color: #FFFFFF !important;
            border-color: #D1D5DB !important;
        }

        /* Bullet & Numbered List Styling Fix for Tailwind Reset */
        .ck-content ul, 
        .ck-editor__editable ul {
            list-style-type: disc !important;
            list-style-position: inside !important;
            padding-left: 0.75rem !important;
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }

        .ck-content ol, 
        .ck-editor__editable ol {
            list-style-type: decimal !important;
            list-style-position: inside !important;
            padding-left: 0.75rem !important;
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }

        .ck-content li, 
        .ck-editor__editable li {
            display: list-item !important;
            margin-bottom: 0.35rem !important;
            color: #111827 !important;
            line-height: 1.5 !important;
        }

        .ck-toolbar {
            background-color: #F9FAFB !important;
            border-color: #D1D5DB !important;
            border-radius: 8px 8px 0 0 !important;
        }

        .ck-toolbar__items button,
        .ck-dropdown__button {
            color: #4B5563 !important;
        }

        .ck-toolbar__items button:hover {
            background-color: #E5E7EB !important;
            color: #111827 !important;
        }
    </style>
</head>

<body class="md:h-screen md:overflow-hidden flex flex-col md:flex-row bg-[#F9FAFB] font-sans antialiased text-gray-900">

    <!-- ===================== COMMON ADMIN SIDEBAR ===================== -->
    @include('layouts.admin-sidebar', ['active' => 'packages'])

    <!-- ===================== MAIN HEADER & VIEW WRAPPER ===================== -->
    <div class="flex-grow flex flex-col min-w-0 md:h-screen md:overflow-y-auto">
        <!-- Top Navigation / Header Bar -->
        <header class="py-5 px-6 md:px-8 border-b border-gray-200 bg-white shrink-0">
            <div class="max-w-[1200px] mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex flex-col gap-1 text-left">
                    <a href="{{ route('admin.packages') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-900 inline-flex items-center gap-1.5 transition-colors mb-1">
                        <i class="fa-solid fa-arrow-left text-[11px]"></i> Back to Packages
                    </a>
                    <div class="flex items-center gap-3">
                        <button type="button" 
                                onclick="toggleAdminSidebarMobile()" 
                                class="md:hidden w-9 h-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-100 flex items-center justify-center text-gray-600 hover:text-gray-900 transition-colors shadow-2xs cursor-pointer shrink-0" 
                                title="Open Menu">
                            <i class="fa-solid fa-bars-staggered text-sm"></i>
                        </button>
                        <h1 class="text-xl md:text-2xl font-bold text-gray-900 tracking-tight">
                            {{ $isEdit ? 'Edit Package' : 'Create New Package' }}
                        </h1>
                        @php $statusVal = $tier['status'] ?? 'published'; @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusVal === 'published' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $statusVal === 'published' ? 'bg-emerald-500' : 'bg-amber-500' }} mr-1.5"></span>
                            {{ ucfirst($statusVal) }}
                        </span>
                    </div>
                    <p class="text-xs md:text-sm text-gray-500 font-normal">
                        {{ $isEdit ? 'Update package information, pricing, media and event details.' : 'Add package information, pricing, media and event details.' }}
                    </p>
                </div>
            </div>
        </header>

        <!-- Content Form Page Container -->
        <main class="flex-grow p-4 md:p-6 lg:p-8 overflow-y-auto w-full">
            <form id="package-main-form" action="{{ route('admin.packages.save') }}" method="POST" enctype="multipart/form-data" class="max-w-[1200px] w-full mx-auto flex flex-col gap-6 text-left pb-24">
                @csrf
                <input type="hidden" name="tier_index" value="{{ $tierIdx }}">

                <!-- Phase 1: Basic Information Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 flex flex-col gap-5 shadow-sm text-left">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-700 font-bold text-xs flex items-center justify-center border border-gray-200">01</span>
                            <div>
                                <h3 class="text-gray-900 font-bold text-base font-heading">Basic Information</h3>
                                <p class="text-xs text-gray-500 font-normal mt-0.5">Configure package identity, category, title & URL slug</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Row 1: Category Selection -->
                    <div class="flex flex-col gap-1 min-w-0">
                        <div class="flex justify-between items-center">
                            <label class="text-sm font-semibold text-gray-700">Event Category *</label>
                            <button type="button" onclick="addNewOptionToSelect('select-category-id', 'Category')" class="text-xs text-amber-700 hover:text-amber-800 hover:underline font-semibold cursor-pointer">
                                <i class="fa-solid fa-plus text-[10px] mr-0.5"></i> Add Custom Category
                            </button>
                        </div>
                        <select id="select-category-id" name="category_id" required data-field-name="Category" onchange="handleCustomSelectOption(this)" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900 cursor-pointer">
                            <option value="">Select Category</option>
                            @foreach($categories as $c)
                                <option value="{{ $c['id'] }}" {{ $catId == $c['id'] ? 'selected' : '' }}>{{ $c['title'] }}</option>
                            @endforeach
                            <option value="__add_new__" class="font-bold text-amber-600">Add New Custom Option...</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-0.5">Shows in the breadcrumb on the Event Details page.</p>
                    </div>

                    <!-- Row 2: Package Name & Auto Slug -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Package Title *</label>
                            <input type="text" id="package-name-input" name="name" value="{{ $tier['name'] }}" oninput="generateSlug(this.value)" required placeholder="e.g. Premium Birthday Setup" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                            <p class="text-xs text-gray-500 mt-0.5">Main heading displayed on the Event Details page.</p>
                        </div>

                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Page URL Slug</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3 text-xs text-gray-500 font-mono font-medium">/events/</span>
                                <input type="text" id="package-slug-input" name="slug" value="{{ $tier['slug'] }}" placeholder="premium-birthday-setup" class="w-full bg-white border border-gray-300 p-2.5 pl-16 text-sm font-mono font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">Auto-generated URL identifier.</p>
                        </div>
                    </div>

                    <!-- Row 3: Tier Badge Level, Package Icon, Sub-category Tag, Display Order -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
                        <div class="flex flex-col gap-1 min-w-0">
                            <div class="flex justify-between items-center">
                                <label class="text-sm font-semibold text-gray-700">Badge Tag *</label>
                                <button type="button" onclick="addNewOptionToSelect('select-tier-badge', 'Tier Badge')" class="text-xs text-amber-700 hover:underline font-semibold cursor-pointer">
                                    <i class="fa-solid fa-plus text-[10px] mr-0.5"></i> Custom
                                </button>
                            </div>
                            @php
                                $currentBadge = $tier['badge'] ?? 'PREMIUM PACKAGE';
                                $defaultBadges = ['BASIC PACKAGE', 'PREMIUM PACKAGE', 'LUXURY PACKAGE', 'PLATINUM PACKAGE', 'MOST POPULAR', 'BEST SELLER', 'CUSTOM PACKAGE'];
                                $isCustomBadge = !in_array($currentBadge, $defaultBadges) && !empty($currentBadge);
                            @endphp
                            <select id="select-tier-badge" name="badge" required data-field-name="Tier Badge" onchange="handleCustomSelectOption(this)" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900 cursor-pointer">
                                <option value="BASIC PACKAGE" {{ $currentBadge == 'BASIC PACKAGE' ? 'selected' : '' }}>BASIC PACKAGE</option>
                                <option value="PREMIUM PACKAGE" {{ $currentBadge == 'PREMIUM PACKAGE' ? 'selected' : '' }}>PREMIUM PACKAGE</option>
                                <option value="LUXURY PACKAGE" {{ $currentBadge == 'LUXURY PACKAGE' ? 'selected' : '' }}>LUXURY PACKAGE</option>
                                <option value="PLATINUM PACKAGE" {{ $currentBadge == 'PLATINUM PACKAGE' ? 'selected' : '' }}>PLATINUM PACKAGE</option>
                                <option value="MOST POPULAR" {{ $currentBadge == 'MOST POPULAR' ? 'selected' : '' }}>MOST POPULAR</option>
                                <option value="BEST SELLER" {{ $currentBadge == 'BEST SELLER' ? 'selected' : '' }}>BEST SELLER</option>
                                <option value="CUSTOM PACKAGE" {{ $currentBadge == 'CUSTOM PACKAGE' ? 'selected' : '' }}>CUSTOM PACKAGE</option>
                                @if($isCustomBadge)
                                    <option value="{{ $currentBadge }}" selected>{{ $currentBadge }}</option>
                                @endif
                                <option value="__add_new__" class="font-bold text-amber-600">Add New Custom Option...</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Package Icon</label>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="openIconPicker('package-icon-preview', 'package-icon-input')" class="w-full bg-white border border-gray-300 p-2 rounded-lg flex items-center justify-between hover:border-gray-900 transition-colors cursor-pointer">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded bg-gray-100 flex items-center justify-center text-gray-700 text-xs border border-gray-200">
                                            <i id="package-icon-preview" class="{{ $tier['icon'] ?? 'fa-solid fa-crown' }}"></i>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-800">Change Icon</span>
                                    </div>
                                    <i class="fa-solid fa-icons text-xs text-gray-400"></i>
                                </button>
                                <input type="hidden" name="icon" id="package-icon-input" value="{{ $tier['icon'] ?? 'fa-solid fa-crown' }}">
                            </div>
                        </div>

                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Sub-Category Tag</label>
                            <input type="text" name="sub_category" value="{{ $tier['sub_category'] ?? 'decor' }}" placeholder="e.g. decor, live, lights" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                        </div>

                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Display Order</label>
                            <input type="number" name="display_order" value="{{ $tier['display_order'] ?? 1 }}" min="1" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                        </div>
                    </div>

                    <!-- Row 4: Feature Flags & Badges Grid with + Add Custom Badge -->
                    <div class="border-t border-gray-100 pt-4 flex flex-col gap-2 min-w-0">
                        <div class="flex justify-between items-center">
                            <label class="text-sm font-semibold text-gray-700">Marketing & Feature Badges</label>
                            <button type="button" onclick="addCustomBadgeRow()" class="text-xs text-amber-700 hover:text-amber-800 hover:underline font-semibold cursor-pointer flex items-center gap-1">
                                <i class="fa-solid fa-plus text-[10px]"></i> Add Custom Badge
                            </button>
                        </div>
                        <div id="badges-grid-container" class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3 bg-gray-50 p-3.5 rounded-lg border border-gray-200">
                            <!-- Standard Badges -->
                            <label class="flex items-center gap-2 cursor-pointer select-none bg-white p-2 rounded-lg border border-gray-200">
                                <input type="checkbox" name="switches[featured]" {{ !empty($tier['switches']['featured']) ? 'checked' : '' }} class="w-4 h-4 text-gray-900 rounded accent-gray-900">
                                <span class="text-xs font-semibold text-gray-800 flex items-center gap-1.5">
                                    <button type="button" onclick="openIconPicker('switch-icon-prev-featured', 'switch-icon-input-featured')" class="hover:scale-110 transition-transform cursor-pointer" title="Click icon to change">
                                        <i id="switch-icon-prev-featured" class="{{ $tier['switch_icons']['featured'] ?? 'fa-solid fa-star' }} text-amber-500 text-xs"></i>
                                    </button>
                                    <input type="hidden" name="switch_icons[featured]" id="switch-icon-input-featured" value="{{ $tier['switch_icons']['featured'] ?? 'fa-solid fa-star' }}">
                                    Featured
                                </span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer select-none bg-white p-2 rounded-lg border border-gray-200">
                                <input type="checkbox" name="switches[recommended]" {{ !empty($tier['switches']['recommended']) ? 'checked' : '' }} class="w-4 h-4 text-gray-900 rounded accent-gray-900">
                                <span class="text-xs font-semibold text-gray-800 flex items-center gap-1.5">
                                    <button type="button" onclick="openIconPicker('switch-icon-prev-recommended', 'switch-icon-input-recommended')" class="hover:scale-110 transition-transform cursor-pointer" title="Click icon to change">
                                        <i id="switch-icon-prev-recommended" class="{{ $tier['switch_icons']['recommended'] ?? 'fa-solid fa-thumbs-up' }} text-amber-500 text-xs"></i>
                                    </button>
                                    <input type="hidden" name="switch_icons[recommended]" id="switch-icon-input-recommended" value="{{ $tier['switch_icons']['recommended'] ?? 'fa-solid fa-thumbs-up' }}">
                                    Recommended
                                </span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer select-none bg-white p-2 rounded-lg border border-gray-200">
                                <input type="checkbox" name="switches[popular]" {{ !empty($tier['switches']['popular']) ? 'checked' : '' }} class="w-4 h-4 text-gray-900 rounded accent-gray-900">
                                <span class="text-xs font-semibold text-gray-800 flex items-center gap-1.5">
                                    <button type="button" onclick="openIconPicker('switch-icon-prev-popular', 'switch-icon-input-popular')" class="hover:scale-110 transition-transform cursor-pointer" title="Click icon to change">
                                        <i id="switch-icon-prev-popular" class="{{ $tier['switch_icons']['popular'] ?? 'fa-solid fa-fire' }} text-amber-500 text-xs"></i>
                                    </button>
                                    <input type="hidden" name="switch_icons[popular]" id="switch-icon-input-popular" value="{{ $tier['switch_icons']['popular'] ?? 'fa-solid fa-fire' }}">
                                    Popular
                                </span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer select-none bg-white p-2 rounded-lg border border-gray-200">
                                <input type="checkbox" name="switches[best_seller]" {{ !empty($tier['switches']['best_seller']) ? 'checked' : '' }} class="w-4 h-4 text-gray-900 rounded accent-gray-900">
                                <span class="text-xs font-semibold text-gray-800 flex items-center gap-1.5">
                                    <button type="button" onclick="openIconPicker('switch-icon-prev-bestseller', 'switch-icon-input-bestseller')" class="hover:scale-110 transition-transform cursor-pointer" title="Click icon to change">
                                        <i id="switch-icon-prev-bestseller" class="{{ $tier['switch_icons']['best_seller'] ?? 'fa-solid fa-trophy' }} text-amber-500 text-xs"></i>
                                    </button>
                                    <input type="hidden" name="switch_icons[best_seller]" id="switch-icon-input-bestseller" value="{{ $tier['switch_icons']['best_seller'] ?? 'fa-solid fa-trophy' }}">
                                    Best Seller
                                </span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer select-none bg-white p-2 rounded-lg border border-gray-200">
                                <input type="checkbox" name="switches[trending]" {{ !empty($tier['switches']['trending']) ? 'checked' : '' }} class="w-4 h-4 text-gray-900 rounded accent-gray-900">
                                <span class="text-xs font-semibold text-gray-800 flex items-center gap-1.5">
                                    <button type="button" onclick="openIconPicker('switch-icon-prev-trending', 'switch-icon-input-trending')" class="hover:scale-110 transition-transform cursor-pointer" title="Click icon to change">
                                        <i id="switch-icon-prev-trending" class="{{ $tier['switch_icons']['trending'] ?? 'fa-solid fa-chart-line' }} text-amber-500 text-xs"></i>
                                    </button>
                                    <input type="hidden" name="switch_icons[trending]" id="switch-icon-input-trending" value="{{ $tier['switch_icons']['trending'] ?? 'fa-solid fa-chart-line' }}">
                                    Trending
                                </span>
                            </label>

                            <!-- Saved Custom Badges -->
                            @php $customBadges = $tier['custom_badges'] ?? []; @endphp
                            @foreach($customBadges as $cbIdx => $cb)
                                <div class="flex items-center gap-1.5 bg-white border border-gray-300 p-2 rounded-lg custom-badge-item min-w-0 shadow-sm">
                                    <input type="checkbox" name="custom_badges[{{ $cbIdx }}][active]" value="1" {{ !empty($cb['active']) ? 'checked' : '' }} class="w-4 h-4 text-gray-900 rounded accent-gray-900 shrink-0">
                                    <button type="button" onclick="openIconPicker('cb-icon-prev-{{ $cbIdx }}', 'cb-icon-input-{{ $cbIdx }}')" class="hover:scale-110 transition-transform cursor-pointer shrink-0" title="Click icon to change">
                                        <i id="cb-icon-prev-{{ $cbIdx }}" class="{{ $cb['icon'] ?? 'fa-solid fa-tag' }} text-amber-500 text-xs"></i>
                                    </button>
                                    <input type="hidden" name="custom_badges[{{ $cbIdx }}][icon]" id="cb-icon-input-{{ $cbIdx }}" value="{{ $cb['icon'] ?? 'fa-solid fa-tag' }}">
                                    <input type="text" name="custom_badges[{{ $cbIdx }}][label]" value="{{ $cb['label'] ?? '' }}" placeholder="Badge Name" class="w-full text-xs font-semibold text-gray-900 border-none outline-none bg-transparent p-0 min-w-0">
                                    <button type="button" onclick="this.closest('.custom-badge-item').remove()" class="text-red-500 hover:text-red-700 text-xs p-1 cursor-pointer shrink-0" title="Remove badge"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Row 5: Internal Admin Notes -->
                    <div class="flex flex-col gap-1 border-t border-gray-100 pt-4 min-w-0">
                        <label class="text-sm font-semibold text-gray-700">Internal Admin Notes</label>
                        <textarea name="internal_notes" rows="2" placeholder="Private operational notes (e.g. Special vendor coordination required for cold pyro...)" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">{!! $tier['internal_notes'] ?? '' !!}</textarea>
                        <p class="text-xs text-gray-500 mt-0.5">Private operational notes visible only in admin dashboard.</p>
                    </div>
                </div>

                <!-- Phase 2: Complete Pricing Matrix Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 flex flex-col gap-5 shadow-sm text-left">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-700 font-bold text-xs flex items-center justify-center border border-gray-200">02</span>
                            <div>
                                <h3 class="text-gray-900 font-bold text-base font-heading">Pricing</h3>
                                <p class="text-xs text-gray-500 font-normal mt-0.5">Set original MRP price, deal selling price & booking deposit</p>
                            </div>
                        </div>
                    </div>

                    <!-- Row 1: Original MRP Price, Sale Price, Auto Discount % -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Original MRP (₹)</label>
                            <input type="number" id="mrp-price-input" name="original_price" value="{{ $tier['original_price'] ?? '' }}" oninput="calculatePricingDetails()" placeholder="e.g. 24999" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                            <p class="text-xs text-gray-500 mt-0.5">Strike-through price displayed on Event Details page.</p>
                        </div>

                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Selling Price (₹) *</label>
                            <input type="number" id="sale-price-input" name="price" value="{{ $tier['price'] }}" oninput="calculatePricingDetails()" required placeholder="e.g. 19999" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                            <p class="text-xs text-gray-500 mt-0.5">Main deal price displayed on Event Details page.</p>
                        </div>

                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Discount Badge</label>
                            <div class="bg-gray-50 border border-gray-200 p-2.5 rounded-lg flex items-center justify-between h-[42px]">
                                <span id="discount-badge-display" class="text-sm font-bold text-emerald-700 font-mono">{{ $tier['discount_pct'] ?? 0 }}% OFF</span>
                                <span class="text-xs text-gray-500 font-semibold">Auto Calculated</span>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Advance Deposit, Setup Charge, Travel Charge, Extra Guest Rate -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Advance Deposit (₹)</label>
                            <input type="number" id="advance-deposit-input" name="advance_deposit" value="{{ $tier['advance_deposit'] ?? 2000 }}" oninput="calculatePricingDetails()" placeholder="2000" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                            <p class="text-xs text-gray-500 mt-0.5">Required deposit to submit booking.</p>
                        </div>

                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Setup Charge (₹)</label>
                            <input type="number" id="setup-charge-input" name="setup_charge" value="{{ $tier['setup_charge'] ?? 0 }}" oninput="calculatePricingDetails()" placeholder="0" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                        </div>

                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Base Travel Fee (₹)</label>
                            <input type="number" id="travel-charge-input" name="travel_charge" value="{{ $tier['travel_charge'] ?? 0 }}" oninput="calculatePricingDetails()" placeholder="0" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                        </div>

                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Extra Guest Rate (₹/head)</label>
                            <input type="number" name="extra_guest_rate" value="{{ $tier['extra_guest_rate'] ?? 150 }}" placeholder="150" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                        </div>
                    </div>

                    <!-- Row 3: GST Tax Settings & Live Calculation Breakdown Box -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-center border-t border-gray-100 pt-4">
                        <div class="md:col-span-4 flex flex-col gap-3 min-w-0">
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-semibold text-gray-700">GST Tax Percentage (%)</label>
                                <input type="number" id="gst-pct-input" name="gst_pct" value="{{ $tier['gst_pct'] ?? 18 }}" oninput="calculatePricingDetails()" placeholder="18" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer select-none bg-gray-50 p-2.5 rounded-lg border border-gray-200">
                                <input type="checkbox" name="gst_inclusive" id="gst-inclusive-input" onchange="calculatePricingDetails()" {{ !empty($tier['gst_inclusive']) ? 'checked' : '' }} class="w-4 h-4 text-gray-900 rounded accent-gray-900">
                                <span class="text-xs font-semibold text-gray-800">Prices include GST Tax</span>
                            </label>
                        </div>

                        <!-- Live Pricing Summary Calculator Box -->
                        <div class="md:col-span-8 bg-gray-50 p-4 rounded-xl border border-gray-200 flex flex-col gap-2 min-w-0">
                            <div class="flex justify-between items-center text-xs font-bold text-gray-600 border-b border-gray-200 pb-2">
                                <span>Pricing Breakdown Preview</span>
                                <span id="price-savings-summary" class="text-emerald-600 font-bold">Save ₹0</span>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-left pt-1">
                                <div>
                                    <span class="text-xs text-gray-500 font-medium block">Original MRP</span>
                                    <span id="preview-mrp-display" class="text-sm font-semibold text-gray-400 line-through">₹0</span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 font-medium block">Selling Price</span>
                                    <span id="preview-sale-display" class="text-base font-bold text-gray-900">₹0</span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 font-medium block">Advance Deposit</span>
                                    <span id="preview-advance-display" class="text-sm font-semibold text-amber-700">₹0</span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 font-medium block">Estimated Total</span>
                                    <span id="preview-final-total-display" class="text-base font-bold text-emerald-700">₹0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Phase 3: Rich Package Descriptions & Guidelines Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 flex flex-col gap-5 shadow-sm text-left">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-700 font-bold text-xs flex items-center justify-center border border-gray-200">03</span>
                            <div>
                                <h3 class="text-gray-900 font-bold text-base font-heading">Package Description</h3>
                                <p class="text-xs text-gray-500 font-normal mt-0.5">Intro summary, rich package story, USPs & guidelines</p>
                            </div>
                        </div>
                    </div>

                    <!-- 1. Short Intro Description -->
                    <div class="flex flex-col gap-1 min-w-0">
                        <div class="flex justify-between items-center">
                            <label class="text-sm font-semibold text-gray-700">Short Intro Summary *</label>
                            <span class="text-xs text-gray-500 font-normal">1-2 Lines</span>
                        </div>
                        <textarea name="short_desc" rows="2" placeholder="Brief summary displayed under package title on Event Details page..." class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">{!! $tier['short_desc'] ?? ($tier['desc'] ?? '') !!}</textarea>
                        <p class="text-xs text-gray-500 mt-0.5">Sub-description text right below the title on Event Details page.</p>
                    </div>

                    <!-- 2. Detailed About Package Description (CKEditor) -->
                    <div class="flex flex-col gap-1 min-w-0">
                        <label class="text-sm font-semibold text-gray-700">Detailed Package Story</label>
                        <textarea name="detailed_desc" id="editor-desc" class="ck-editor-textarea">{!! $tier['detailed_desc'] ?? ($tier['desc'] ?? '') !!}</textarea>
                    </div>

                    <!-- 3. Key Highlights Builder -->
                    <div class="border-t border-gray-100 pt-4 flex flex-col gap-3 min-w-0">
                        <div class="flex justify-between items-center">
                            <label class="text-sm font-semibold text-gray-700">Key Highlights & USPs</label>
                            <button type="button" onclick="addHighlightRow()" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-800 border border-gray-300 rounded-lg text-xs font-semibold transition-all cursor-pointer">
                                <i class="fa-solid fa-plus text-[10px] mr-1"></i> Add Highlight
                            </button>
                        </div>
                        <div id="highlights-container" class="flex flex-col gap-2">
                            @php $hlList = $tier['highlights'] ?? []; @endphp
                            @if(count($hlList) > 0)
                                @foreach($hlList as $hlIdx => $hlVal)
                                    <div class="flex items-center gap-2 highlight-row min-w-0">
                                        <button type="button" onclick="openIconPicker('hl-icon-prev-{{ $hlIdx }}', 'hl-icon-input-{{ $hlIdx }}')" class="p-2 bg-gray-50 border border-gray-300 rounded-lg hover:border-gray-900 cursor-pointer shrink-0" title="Click icon to change">
                                            <i id="hl-icon-prev-{{ $hlIdx }}" class="{{ $tier['highlight_icons'][$hlIdx] ?? 'fa-solid fa-star' }} text-amber-500 text-xs"></i>
                                        </button>
                                        <input type="hidden" name="highlight_icons[]" id="hl-icon-input-{{ $hlIdx }}" value="{{ $tier['highlight_icons'][$hlIdx] ?? 'fa-solid fa-star' }}">
                                        <input type="text" name="highlights[]" value="{{ $hlVal }}" placeholder="e.g. Concert-grade high bass PA sound rig" class="flex-grow bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                                        <button type="button" onclick="this.closest('.highlight-row').remove()" class="text-red-500 hover:text-red-700 p-2 text-xs cursor-pointer">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex items-center gap-2 highlight-row min-w-0">
                                    <button type="button" onclick="openIconPicker('hl-icon-prev-0', 'hl-icon-input-0')" class="p-2 bg-gray-50 border border-gray-300 rounded-lg hover:border-gray-900 cursor-pointer shrink-0" title="Click icon to change">
                                        <i id="hl-icon-prev-0" class="fa-solid fa-star text-amber-500 text-xs"></i>
                                    </button>
                                    <input type="hidden" name="highlight_icons[]" id="hl-icon-input-0" value="fa-solid fa-star">
                                    <input type="text" name="highlights[]" placeholder="e.g. 1 Hour DSLR Photography Coverage" class="flex-grow bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                                    <button type="button" onclick="this.closest('.highlight-row').remove()" class="text-red-500 hover:text-red-700 p-2 text-xs cursor-pointer">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 4. Important Operational Notes & Terms -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 border-t border-gray-100 pt-4">
                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Important Guidelines</label>
                            <textarea name="important_notes" rows="2" placeholder="e.g. Continuous 220V power supply required. Cold pyro setup requires venue permission." class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">{!! $tier['important_notes'] ?? '' !!}</textarea>
                        </div>

                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Cancellation Policy & Terms</label>
                            <textarea name="terms" rows="2" placeholder="e.g. Full advance refund if cancelled 48 hours prior to event." class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">{!! $tier['terms'] ?? '' !!}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Phase 4: Event Specifications Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 flex flex-col gap-5 shadow-sm text-left">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-700 font-bold text-xs flex items-center justify-center border border-gray-200">04</span>
                            <div>
                                <h3 class="text-gray-900 font-bold text-base font-heading">Event Specifications</h3>
                                <p class="text-xs text-gray-500 font-normal mt-0.5">Guest capacity, setup prep time, event duration & venue requirements</p>
                            </div>
                        </div>
                    </div>

                    <!-- Row 1: Guest Capacity | Setup Prep Time | Event Duration -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Guest Capacity *</label>
                            <input type="text" name="specifications[guest_capacity]" value="{{ $tier['specifications']['guest_capacity'] ?? '50 - 100 Guests' }}" placeholder="e.g. 50 - 100 Guests" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                            <p class="text-xs text-gray-500 mt-0.5">Shows in 'Ideal For' box.</p>
                        </div>

                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Setup Prep Time *</label>
                            <input type="text" name="specifications[setup_time]" value="{{ $tier['specifications']['setup_time'] ?? '3-4 Hours' }}" placeholder="e.g. 3-4 Hours before event" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                            <p class="text-xs text-gray-500 mt-0.5">Shows in 'Setup Time' box.</p>
                        </div>

                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Event Duration *</label>
                            <input type="text" name="specifications[duration]" value="{{ $tier['specifications']['duration'] ?? '4-5 Hours' }}" placeholder="e.g. 4-5 Hours active" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                            <p class="text-xs text-gray-500 mt-0.5">Shows in 'Duration' box.</p>
                        </div>
                    </div>

                    <!-- Row 2: Venue Location Type | Venue Stage Space | Power Needs -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="flex flex-col gap-1 min-w-0">
                            <div class="flex justify-between items-center">
                                <label class="text-sm font-semibold text-gray-700">Venue Location Type *</label>
                                <button type="button" onclick="addNewOptionToSelect('select-location-type', 'Location Type')" class="text-xs text-amber-700 hover:underline font-semibold cursor-pointer">
                                    <i class="fa-solid fa-plus text-[10px] mr-0.5"></i> Custom
                                </button>
                            </div>
                            @php
                                $currentLoc = $tier['specifications']['location_type'] ?? 'Indoor & Outdoor';
                                $defaultLocs = ['Indoor & Outdoor', 'Indoor Only', 'Outdoor Lawn Only', 'Rooftop / Private Venue', 'Resort Banquet', 'Poolside Deck'];
                                $isCustomLoc = !in_array($currentLoc, $defaultLocs) && !empty($currentLoc);
                            @endphp
                            <select id="select-location-type" name="specifications[location_type]" data-field-name="Location Type" onchange="handleCustomSelectOption(this)" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900 cursor-pointer">
                                <option value="Indoor & Outdoor" {{ $currentLoc == 'Indoor & Outdoor' ? 'selected' : '' }}>Indoor & Outdoor</option>
                                <option value="Indoor Only" {{ $currentLoc == 'Indoor Only' ? 'selected' : '' }}>Indoor Only</option>
                                <option value="Outdoor Lawn Only" {{ $currentLoc == 'Outdoor Lawn Only' ? 'selected' : '' }}>Outdoor Lawn Only</option>
                                <option value="Rooftop / Private Venue" {{ $currentLoc == 'Rooftop / Private Venue' ? 'selected' : '' }}>Rooftop / Private Venue</option>
                                <option value="Resort Banquet" {{ $currentLoc == 'Resort Banquet' ? 'selected' : '' }}>Resort Banquet</option>
                                <option value="Poolside Deck" {{ $currentLoc == 'Poolside Deck' ? 'selected' : '' }}>Poolside Deck</option>
                                @if($isCustomLoc)
                                    <option value="{{ $currentLoc }}" selected>{{ $currentLoc }}</option>
                                @endif
                                <option value="__add_new__" class="font-bold text-amber-600">Add New Custom Option...</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Venue Stage Space</label>
                            <input type="text" name="specifications[space_req]" value="{{ $tier['specifications']['space_req'] ?? '12ft x 8ft Stage' }}" placeholder="e.g. 12ft x 8ft Stage" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                        </div>

                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Power Needs</label>
                            <input type="text" name="specifications[power_req]" value="{{ $tier['specifications']['power_req'] ?? '220V 15A Power Socket' }}" placeholder="e.g. 220V 15A Power Socket" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                        </div>
                    </div>

                    <!-- Row 3: On-Site Crew Size -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">On-Site Crew Size</label>
                            <input type="text" name="specifications[crew_count]" value="{{ $tier['specifications']['crew_count'] ?? '3 Members' }}" placeholder="e.g. 3 Members" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                        </div>
                    </div>
                </div>

                <!-- Phase 05: Photos & Gallery Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 flex flex-col gap-5 shadow-sm text-left">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-700 font-bold text-xs flex items-center justify-center border border-gray-200">05</span>
                            <div>
                                <h3 class="text-gray-900 font-bold text-base font-heading">Photos & Gallery</h3>
                                <p class="text-xs text-gray-500 font-normal mt-0.5">Primary cover photo, showcase gallery thumbnails, video & virtual tour</p>
                            </div>
                        </div>
                    </div>

                    <!-- 1:1 Image Size Specification Info Callout -->
                    <div class="flex items-center gap-3 p-3.5 bg-amber-50 border border-amber-200 rounded-lg text-gray-800 text-xs font-medium">
                        <div class="w-7 h-7 rounded-md bg-amber-100 border border-amber-300 flex items-center justify-center shrink-0 text-amber-700 font-bold">
                            1:1
                        </div>
                        <div>
                            <span class="font-bold text-amber-900 block text-xs">Recommended Image Dimensions: 1:1 Square Ratio</span>
                            <span class="text-gray-600">Please upload square images (e.g., <strong>800 × 800 px</strong> or <strong>1000 × 1000 px</strong>). Supported Formats: JPG, PNG, WEBP (Max 5MB per image).</span>
                        </div>
                    </div>

                    <!-- 4 Image Upload Slots Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        @php
                            $galleryList = $tier['gallery'] ?? [];
                            if(!is_array($galleryList)) $galleryList = [$tier['image'] ?? '/assets/images/hero/1.jpg'];
                            while(count($galleryList) < 4) { $galleryList[] = $tier['image'] ?? '/assets/images/hero/1.jpg'; }
                        @endphp

                        @for($i = 0; $i < 4; $i++)
                            <div class="flex flex-col gap-1.5 border border-gray-200 p-2.5 rounded-lg bg-gray-50 relative group min-w-0">
                                <span class="text-xs font-semibold text-gray-600 flex items-center justify-between">
                                    <span>{{ $i === 0 ? '★ Main Cover' : 'Gallery ' . ($i + 1) }}</span>
                                    <span class="text-[10px] text-amber-700 font-bold bg-amber-100 px-1.5 py-0.5 rounded">1:1 Ratio</span>
                                </span>
                                <div class="aspect-square rounded overflow-hidden border border-gray-200 bg-gray-100 relative">
                                    <img id="gallery-preview-{{ $i }}" src="{{ $galleryList[$i] ?? '/assets/images/hero/1.jpg' }}" class="w-full h-full object-cover">
                                </div>
                                <div class="grid grid-cols-2 gap-1.5 mt-1">
                                    <label class="block cursor-pointer bg-white hover:bg-gray-100 border border-gray-300 text-gray-700 text-xs font-semibold py-1.5 text-center rounded transition-all select-none">
                                        <i class="fa-solid fa-upload text-[10px]"></i> Upload
                                        <input type="file" id="gallery-file-{{ $i }}" name="gallery_files[{{ $i }}]" onchange="previewGallerySlot(this, {{ $i }})" class="hidden">
                                    </label>
                                    <button type="button" onclick="removeGallerySlot({{ $i }})" class="bg-white hover:bg-red-50 border border-gray-300 text-red-600 text-xs font-semibold py-1.5 text-center rounded transition-all cursor-pointer select-none">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i> Remove
                                    </button>
                                </div>
                                <input type="hidden" name="gallery[{{ $i }}]" id="gallery-input-{{ $i }}" value="{{ $galleryList[$i] ?? '' }}">
                            </div>
                        @endfor
                    </div>

                    <!-- Primary Cover Reference -->
                    <input type="hidden" name="image" value="{{ $tier['image'] ?? '' }}">
                    <p class="text-xs text-gray-600 font-semibold flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info text-amber-600"></i> Recommended: 1:1 Aspect Ratio (Square: 800×800px / 1000×1000px) | JPG, PNG, WEBP
                    </p>

                    <!-- Phase 5: Video Teaser & PDF Brochure Media Section -->
                    <div class="border-t border-gray-100 pt-4 grid grid-cols-1 md:grid-cols-2 gap-5 text-left">
                        <!-- Video Teaser URL -->
                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">Video Teaser URL</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3 text-xs text-gray-400"><i class="fa-brands fa-youtube"></i></span>
                                <input type="url" name="video_url" value="{{ $tier['video_url'] ?? '' }}" placeholder="https://youtube.com/watch?v=..." class="w-full bg-white border border-gray-300 p-2.5 pl-9 text-xs font-mono font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">YouTube / Vimeo / Reels video link.</p>
                        </div>

                        <!-- PDF Brochure Upload / Link -->
                        <div class="flex flex-col gap-1 min-w-0">
                            <label class="text-sm font-semibold text-gray-700">PDF Package Brochure</label>
                            <div class="flex items-center gap-2">
                                <label class="block cursor-pointer bg-gray-100 hover:bg-gray-200 border border-gray-300 text-gray-700 text-xs font-semibold px-3 py-2.5 text-center rounded-lg transition-all select-none shrink-0">
                                    <i class="fa-solid fa-file-pdf"></i> Upload PDF
                                    <input type="file" name="pdf_brochure_file" accept=".pdf" class="hidden">
                                </label>
                                <input type="text" name="pdf_brochure" value="{{ $tier['pdf_brochure'] ?? '' }}" placeholder="/uploads/brochure_..." class="flex-grow bg-white border border-gray-300 p-2.5 text-xs font-mono font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">Upload PDF or paste brochure URL.</p>
                        </div>
                    </div>
                </div>

                <!-- Phase 06: Included Services Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 flex flex-col gap-5 shadow-sm text-left">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-700 font-bold text-xs flex items-center justify-center border border-gray-200">06</span>
                            <div>
                                <h3 class="text-gray-900 font-bold text-base font-heading">Included Services</h3>
                                <p class="text-xs text-gray-500 font-normal mt-0.5">Services and deliverables included in this package tier</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tip Banner -->
                    <div class="flex items-center gap-2.5 p-3 bg-blue-50 border border-blue-200 rounded-lg text-blue-900 text-xs font-medium">
                        <i class="fa-solid fa-circle-check text-blue-600 text-sm shrink-0"></i>
                        <span><strong>How it works:</strong> Add each item as a bullet point in the list below. These items will be displayed as green checkmark benefits (✓) on the customer Event Details page.</span>
                    </div>

                    <div class="flex flex-col gap-1 w-full min-w-0">
                        <label class="text-sm font-semibold text-gray-700">Included Services Checklist *</label>
                        <textarea name="inclusions" id="editor-inclusions" class="ck-editor-textarea">
                            @if(isset($tier['inclusions']) && count($tier['inclusions']) > 0)
                                <ul>
                                    @foreach($tier['inclusions'] as $inc)
                                        <li>{{ $inc }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <ul>
                                    <li>Grand Balloon Arch & Backdrop Stage Decor Setup</li>
                                    <li>Concert-Grade Dual PA Speaker Sound System with Wireless Mics</li>
                                    <li>Customized Acrylic Name Signage & Entrance Welcome Board</li>
                                    <li>Warm Ambient LED Par Lights & Cold Pyro Sparkler Entry (2 Boxes)</li>
                                    <li>On-Site Dedicated Event Coordinator & Setup Crew</li>
                                </ul>
                            @endif
                        </textarea>
                        <p class="text-xs text-gray-500 mt-1">Displayed as green checkmark items on Event Details page.</p>
                    </div>
                </div>

                <!-- Phase 07: Package Exclusions Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 flex flex-col gap-5 shadow-sm text-left">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-700 font-bold text-xs flex items-center justify-center border border-gray-200">07</span>
                            <div>
                                <h3 class="text-gray-900 font-bold text-base font-heading">Exclusions</h3>
                                <p class="text-xs text-gray-500 font-normal mt-0.5">Explicitly clarify services NOT included in this package tier</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tip Banner -->
                    <div class="flex items-center gap-2.5 p-3 bg-red-50 border border-red-200 rounded-lg text-red-900 text-xs font-medium">
                        <i class="fa-solid fa-circle-xmark text-red-600 text-sm shrink-0"></i>
                        <span><strong>How it works:</strong> Add excluded items as bullet points below. These will be displayed as red crossmark items (✗) on the customer Event Details page.</span>
                    </div>

                    <div class="flex flex-col gap-1 w-full min-w-0">
                        <label class="text-sm font-semibold text-gray-700">Services NOT Included *</label>
                        <textarea name="exclusions" id="editor-exclusions" class="ck-editor-textarea">
                            @if(isset($tier['exclusions']) && count($tier['exclusions']) > 0)
                                <ul>
                                    @foreach($tier['exclusions'] as $ex)
                                        <li>{{ $ex }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <ul>
                                    <li>Venue booking charges & local authority permission fees</li>
                                    <li>Heavy-duty generator diesel fuel charges (if power cuts occur)</li>
                                    <li>Personal catering, food & beverage services</li>
                                    <li>Outstation travel allowance outside Indore municipal limits</li>
                                </ul>
                            @endif
                        </textarea>
                        <p class="text-xs text-gray-500 mt-1">Displayed as red crossmark items (✗) on Event Details page.</p>
                    </div>
                </div>

                <!-- Phase 08: Package Add-ons Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 flex flex-col gap-5 shadow-sm text-left">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-700 font-bold text-xs flex items-center justify-center border border-gray-200">08</span>
                            <div>
                                <h3 class="text-gray-900 font-bold text-base font-heading">Add-ons & Upgrades</h3>
                                <p class="text-xs text-gray-500 font-normal mt-0.5">Optional extra services customers can select during booking</p>
                            </div>
                        </div>
                        <button type="button" onclick="addAddonRow()" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-800 border border-gray-300 rounded-lg text-xs font-semibold transition-all cursor-pointer">
                            <i class="fa-solid fa-plus text-[10px] mr-1"></i> Add Add-on
                        </button>
                    </div>

                    <div id="addons-container" class="flex flex-col gap-3 min-w-0">
                        @php $addonsList = $tier['addons'] ?? []; @endphp
                        @if(count($addonsList) > 0)
                            @foreach($addonsList as $aIdx => $aVal)
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center bg-gray-50 p-3 rounded-lg border border-gray-200 addon-row min-w-0">
                                    <div class="md:col-span-5 flex items-center gap-2 min-w-0">
                                        <button type="button" onclick="openIconPicker('addon-icon-prev-{{ $aIdx }}', 'addon-icon-input-{{ $aIdx }}')" class="p-2 bg-white border border-gray-300 rounded-lg hover:border-gray-900 cursor-pointer shrink-0" title="Click icon to change">
                                            <i id="addon-icon-prev-{{ $aIdx }}" class="{{ $aVal['icon'] ?? 'fa-solid fa-fire' }} text-amber-500 text-xs"></i>
                                        </button>
                                        <input type="hidden" name="addons[{{ $aIdx }}][icon]" id="addon-icon-input-{{ $aIdx }}" value="{{ $aVal['icon'] ?? 'fa-solid fa-fire' }}">
                                        <input type="text" name="addons[{{ $aIdx }}][title]" value="{{ $aVal['title'] ?? '' }}" placeholder="Add-on Title (e.g. Cold Pyro Entry)" class="w-full bg-white border border-gray-300 p-2 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                                    </div>
                                    <div class="md:col-span-3 min-w-0">
                                        <input type="number" name="addons[{{ $aIdx }}][price]" value="{{ $aVal['price'] ?? 0 }}" placeholder="Price (₹)" class="w-full bg-white border border-gray-300 p-2 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                                    </div>
                                    <div class="md:col-span-3 min-w-0">
                                        <input type="text" name="addons[{{ $aIdx }}][desc]" value="{{ $aVal['desc'] ?? '' }}" placeholder="Short note" class="w-full bg-white border border-gray-300 p-2 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                                    </div>
                                    <div class="md:col-span-1 text-right">
                                        <button type="button" onclick="this.closest('.addon-row').remove()" class="text-red-500 hover:text-red-700 p-1.5 text-xs cursor-pointer">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center bg-gray-50 p-3 rounded-lg border border-gray-200 addon-row min-w-0">
                                <div class="md:col-span-5 flex items-center gap-2 min-w-0">
                                    <button type="button" onclick="openIconPicker('addon-icon-prev-0', 'addon-icon-input-0')" class="p-2 bg-white border border-gray-300 rounded-lg hover:border-gray-900 cursor-pointer shrink-0" title="Click icon to change">
                                        <i id="addon-icon-prev-0" class="fa-solid fa-fire text-amber-500 text-xs"></i>
                                    </button>
                                    <input type="hidden" name="addons[0][icon]" id="addon-icon-input-0" value="fa-solid fa-fire">
                                    <input type="text" name="addons[0][title]" placeholder="e.g. 2 Cold Pyro Boxes" class="w-full bg-white border border-gray-300 p-2 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                                </div>
                                <div class="md:col-span-3 min-w-0">
                                    <input type="number" name="addons[0][price]" placeholder="1500" class="w-full bg-white border border-gray-300 p-2 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                                </div>
                                <div class="md:col-span-3 min-w-0">
                                    <input type="text" name="addons[0][desc]" placeholder="Includes safety setup" class="w-full bg-white border border-gray-300 p-2 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                                </div>
                                <div class="md:col-span-1 text-right">
                                    <button type="button" onclick="this.closest('.addon-row').remove()" class="text-red-500 hover:text-red-700 p-1.5 text-xs cursor-pointer">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Phase 09: Package FAQs Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 flex flex-col gap-5 shadow-sm text-left">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-700 font-bold text-xs flex items-center justify-center border border-gray-200">09</span>
                            <div>
                                <h3 class="text-gray-900 font-bold text-base font-heading">FAQs</h3>
                                <p class="text-xs text-gray-500 font-normal mt-0.5">Common questions and answers regarding this setup</p>
                            </div>
                        </div>
                        <button type="button" onclick="addFaqRow()" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-800 border border-gray-300 rounded-lg text-xs font-semibold transition-all cursor-pointer">
                            <i class="fa-solid fa-plus text-[10px] mr-1"></i> Add FAQ
                        </button>
                    </div>

                    <div id="faqs-container" class="flex flex-col gap-3 min-w-0">
                        @php $faqsList = $tier['faqs'] ?? []; @endphp
                        @if(count($faqsList) > 0)
                            @foreach($faqsList as $fIdx => $fVal)
                                <div class="flex flex-col gap-2 bg-gray-50 p-3.5 rounded-lg border border-gray-200 faq-row relative min-w-0">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-gray-700">FAQ Item #{{ $fIdx + 1 }}</span>
                                        <button type="button" onclick="this.closest('.faq-row').remove()" class="text-red-500 hover:text-red-700 text-xs cursor-pointer">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                    <input type="text" name="faqs[{{ $fIdx }}][question]" value="{{ $fVal['question'] ?? '' }}" placeholder="Question: e.g. How long does setup take?" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                                    <textarea name="faqs[{{ $fIdx }}][answer]" rows="2" placeholder="Answer: e.g. Setup takes approximately 3 hours before event start." class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">{!! $fVal['answer'] ?? '' !!}</textarea>
                                </div>
                            @endforeach
                        @else
                            <div class="flex flex-col gap-2 bg-gray-50 p-3.5 rounded-lg border border-gray-200 faq-row relative min-w-0">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-700">FAQ Item #1</span>
                                    <button type="button" onclick="this.closest('.faq-row').remove()" class="text-red-500 hover:text-red-700 text-xs cursor-pointer">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                                <input type="text" name="faqs[0][question]" placeholder="Question: e.g. Can we customize balloon color theme?" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900">
                                <textarea name="faqs[0][answer]" rows="2" placeholder="Answer: e.g. Yes, balloon color themes can be customized at zero extra charge." class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900"></textarea>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Phase 10: Publication Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 flex flex-col gap-5 shadow-sm text-left">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-700 font-bold text-xs flex items-center justify-center border border-gray-200">10</span>
                            <div>
                                <h3 class="text-gray-900 font-bold text-base font-heading">Publication</h3>
                                <p class="text-xs text-gray-500 font-normal mt-0.5">Control live visibility on customer website</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1 min-w-0">
                        <label class="text-sm font-semibold text-gray-700">Publication Status</label>
                        <select name="status" class="w-full bg-white border border-gray-300 p-2.5 text-sm font-medium text-gray-900 rounded-lg outline-none focus:border-gray-900 cursor-pointer">
                            <option value="published" {{ ($tier['status'] ?? 'published') == 'published' ? 'selected' : '' }}>Published (Live on Website)</option>
                            <option value="draft" {{ ($tier['status'] ?? '') == 'draft' ? 'selected' : '' }}>Draft (Hidden from Customers)</option>
                            <option value="archived" {{ ($tier['status'] ?? '') == 'archived' ? 'selected' : '' }}>Archived (Disabled)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Published packages are visible on customer website.</p>
                    </div>
                </div>
            </form>
        </main>

        <!-- Sticky Global Action Bar -->
        <div class="bg-white/95 backdrop-blur-md border-t border-gray-200 py-3.5 px-4 sm:px-6 md:px-8 z-40 shadow-lg shrink-0">
            <div class="max-w-[1200px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
                <div class="flex items-center gap-2 text-xs text-gray-500 font-medium text-center sm:text-left">
                    <i class="fa-solid fa-circle-info text-gray-400 text-sm"></i>
                    <span>Changes are saved when you click Save Package.</span>
                </div>
                <div class="flex items-center justify-end gap-2.5 sm:gap-3 w-full sm:w-auto">
                    <a href="{{ route('admin.packages') }}" class="flex-1 sm:flex-none px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold transition-all shadow-sm text-center">
                        Cancel
                    </a>
                    <button type="submit" form="package-main-form" name="save_draft" value="1" onclick="document.querySelector('select[name=status]').value = 'draft';" class="flex-1 sm:flex-none px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-sm font-semibold transition-all border border-gray-300 cursor-pointer text-center">
                        Save Draft
                    </button>
                    <button type="submit" form="package-main-form" class="flex-1 sm:flex-none px-5 py-2 bg-gray-900 hover:bg-black text-white rounded-lg text-sm font-semibold transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-check text-xs"></i> Save Package
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- GLOBAL CUSTOM OPTION MODAL -->
    <div id="custom-option-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[60] hidden opacity-0 transition-opacity duration-300 pointer-events-none">
        <div class="bg-white border border-border-color w-full max-w-md rounded-2xl shadow-2xl p-6 flex flex-col gap-4 text-left pointer-events-auto transform scale-95 transition-transform duration-300">
            <div class="flex justify-between items-center border-b border-border-color pb-3">
                <div>
                    <h3 id="custom-option-modal-title" class="text-[#1E1E24] font-bold text-sm uppercase tracking-wider font-heading">Add Custom Option</h3>
                    <span class="text-[9px] text-muted-text mt-0.5 block font-body">Type a new custom value to add to this dropdown selector</span>
                </div>
                <button type="button" onclick="closeCustomOptionModal()" class="text-muted-text hover:text-[#1E1E24] transition-colors text-base"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <!-- Input Box -->
            <div class="flex flex-col gap-1.5">
                <label id="custom-option-modal-label" class="text-[9px] text-muted-text font-bold uppercase tracking-wider font-heading">Custom Value *</label>
                <input type="text" id="custom-option-modal-input" placeholder="e.g. Poolside Lawn & Deck" class="w-full bg-main-bg border border-border-color p-3 text-xs font-semibold text-[#1E1E24] rounded-xl outline-none focus:border-gold">
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="submitCustomOptionModal()" class="flex-grow bg-gold hover:bg-gold-light text-[#1E1E24] py-2.5 text-center text-xs font-heading font-extrabold uppercase tracking-widest rounded-xl transition-colors cursor-pointer border-none shadow-md">
                    <i class="fa-solid fa-plus mr-1"></i> Add Custom Option
                </button>
                <button type="button" onclick="closeCustomOptionModal()" class="bg-main-bg hover:bg-surface-bg border border-border-color text-muted-text hover:text-[#1E1E24] py-2.5 px-4 text-center text-xs font-heading font-bold uppercase tracking-wider rounded-xl transition-colors cursor-pointer">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- GLOBAL ICON PICKER MODAL -->
    <div id="global-icon-picker-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 hidden opacity-0 transition-opacity duration-300 pointer-events-none">
        <div class="bg-white border border-border-color w-full max-w-lg rounded-2xl shadow-2xl p-6 flex flex-col gap-4 text-left pointer-events-auto transform scale-95 transition-transform duration-300">
            <div class="flex justify-between items-center border-b border-border-color pb-3">
                <div>
                    <h3 class="text-[#1E1E24] font-bold text-sm uppercase tracking-wider font-heading">Select Package Icon</h3>
                    <span class="text-[9px] text-muted-text mt-0.5 block font-body">Choose from 100+ FontAwesome icons for tiers, badges, highlights & add-ons</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[9px] bg-gold/10 text-gold border border-gold/30 px-2 py-0.5 rounded font-extrabold uppercase">100+ Icons</span>
                    <button type="button" onclick="closeIconPicker()" class="text-muted-text hover:text-[#1E1E24] transition-colors text-base"><i class="fa-solid fa-xmark"></i></button>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-muted-text pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" id="icon-search-input" placeholder="Search 100+ icons (e.g. crown, cake, star, pyro, food, music, dj, camera)..." oninput="filterIconLibrary(this.value)" class="w-full bg-white border border-border-color pl-9 pr-4 py-2.5 text-xs rounded-xl font-semibold text-[#1E1E24] outline-none focus:border-gold">
            </div>

            <!-- Icons Grid (Scrollable) -->
            <div class="grid grid-cols-6 gap-2.5 max-h-80 overflow-y-auto p-1" id="icon-picker-grid">
                <!-- Populated dynamically by JavaScript -->
            </div>
        </div>
    </div>

    <script>
        // Global Icon Library Data (100+ Curated Event Icons) & Picker Logic
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

            const search = document.getElementById('icon-search-input');
            if (search) search.value = '';

            filterIconLibrary('');

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
                const previewEl = document.getElementById(targetPreviewId);
                if (previewEl) {
                    previewEl.className = iconClass;
                }
                const inputEl = document.getElementById(targetInputId);
                if (inputEl) {
                    inputEl.value = iconClass;
                }
            }
            closeIconPicker();
        }

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
                grid.innerHTML = `<div class="col-span-6 text-center text-muted-text text-[10px] py-4">No icons found matching "${query}"</div>`;
            }
        }

        // Dynamic Custom Option UI Modal Handler
        let activeSelectTargetId = null;

        function addNewOptionToSelect(selectId, fieldName) {
            activeSelectTargetId = selectId;
            const modal = document.getElementById('custom-option-modal');
            const titleEl = document.getElementById('custom-option-modal-title');
            const labelEl = document.getElementById('custom-option-modal-label');
            const inputEl = document.getElementById('custom-option-modal-input');

            if (titleEl) titleEl.innerText = `Add Custom ${fieldName}`;
            if (labelEl) labelEl.innerText = `${fieldName} Value *`;
            if (inputEl) {
                inputEl.value = '';
                inputEl.placeholder = `Enter new custom ${fieldName}...`;
            }

            if (modal) {
                modal.classList.remove('hidden', 'pointer-events-none', 'opacity-0');
                modal.classList.add('opacity-100', 'pointer-events-auto');
                const inner = modal.querySelector('div');
                if (inner) inner.classList.replace('scale-95', 'scale-100');
                setTimeout(() => inputEl && inputEl.focus(), 100);
            }
        }

        function handleCustomSelectOption(selectEl) {
            if (selectEl.value === '__add_new__') {
                const fieldName = selectEl.getAttribute('data-field-name') || 'Option';
                if (!selectEl.id) {
                    selectEl.id = 'select_dyn_' + Date.now();
                }
                addNewOptionToSelect(selectEl.id, fieldName);
            }
        }

        function closeCustomOptionModal() {
            const modal = document.getElementById('custom-option-modal');
            if (modal) {
                modal.classList.remove('opacity-100', 'pointer-events-auto');
                modal.classList.add('opacity-0', 'pointer-events-none');
                const inner = modal.querySelector('div');
                if (inner) inner.classList.replace('scale-100', 'scale-95');
                setTimeout(() => modal.classList.add('hidden'), 300);
            }
            if (activeSelectTargetId) {
                const selectEl = document.getElementById(activeSelectTargetId);
                if (selectEl && selectEl.value === '__add_new__') {
                    selectEl.selectedIndex = 0;
                }
            }
        }

        function submitCustomOptionModal() {
            const inputEl = document.getElementById('custom-option-modal-input');
            const val = inputEl ? inputEl.value.trim() : '';

            if (!val) {
                alert('Please enter a valid custom option name!');
                return;
            }

            if (activeSelectTargetId) {
                const selectEl = document.getElementById(activeSelectTargetId);
                if (selectEl) {
                    let existingOption = Array.from(selectEl.options).find(opt => opt.value.toLowerCase() === val.toLowerCase());
                    if (existingOption) {
                        selectEl.value = existingOption.value;
                    } else {
                        const newOpt = document.createElement('option');
                        newOpt.value = val;
                        newOpt.innerText = val;
                        newOpt.selected = true;

                        const addNewOpt = selectEl.querySelector('option[value="__add_new__"]');
                        if (addNewOpt) {
                            selectEl.insertBefore(newOpt, addNewOpt);
                        } else {
                            selectEl.appendChild(newOpt);
                        }
                    }
                }
            }

            closeCustomOptionModal();
        }

        // Real-time URL Slug Generator
        function generateSlug(text) {
            const slugInput = document.getElementById('package-slug-input');
            if (!slugInput) return;
            const slug = text.toString().toLowerCase().trim()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-');        // Replace multiple - with single -
            slugInput.value = slug;
        }

        // Multi-banner slot preview loader
        function previewGallerySlot(input, index) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const imgEl = document.getElementById('gallery-preview-' + index);
                    if (imgEl) imgEl.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Remove gallery image slot
        function removeGallerySlot(index) {
            const preview = document.getElementById('gallery-preview-' + index);
            const hiddenInput = document.getElementById('gallery-input-' + index);
            const fileInput = document.getElementById('gallery-file-' + index);
            
            if (preview) {
                preview.src = 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?q=80&w=300&auto=format&fit=crop';
            }
            if (hiddenInput) {
                hiddenInput.value = '';
            }
            if (fileInput) {
                fileInput.value = '';
            }
        }

        // Phase 2: Live Pricing Matrix & Discount Calculator
        function calculatePricingDetails() {
            const mrpVal = parseFloat(document.getElementById('mrp-price-input')?.value) || 0;
            const saleVal = parseFloat(document.getElementById('sale-price-input')?.value) || 0;
            const advanceVal = parseFloat(document.getElementById('advance-deposit-input')?.value) || 0;
            const setupVal = parseFloat(document.getElementById('setup-charge-input')?.value) || 0;
            const travelVal = parseFloat(document.getElementById('travel-charge-input')?.value) || 0;
            const gstPct = parseFloat(document.getElementById('gst-pct-input')?.value) || 0;
            const gstInclusive = document.getElementById('gst-inclusive-input')?.checked;

            // 1. Calculate discount percentage
            let discountPct = 0;
            let savingsAmt = 0;
            if (mrpVal > 0 && saleVal > 0 && saleVal < mrpVal) {
                discountPct = Math.round(((mrpVal - saleVal) / mrpVal) * 100);
                savingsAmt = mrpVal - saleVal;
            }

            const discountBadge = document.getElementById('discount-badge-display');
            if (discountBadge) discountBadge.innerText = discountPct > 0 ? `${discountPct}% OFF` : '0% OFF';

            const savingsSummary = document.getElementById('price-savings-summary');
            if (savingsSummary) savingsSummary.innerText = savingsAmt > 0 ? `Save ₹${savingsAmt.toLocaleString('en-IN')}` : 'Save ₹0';

            // 2. Update Preview Summaries
            const mrpPreview = document.getElementById('preview-mrp-display');
            if (mrpPreview) mrpPreview.innerText = mrpVal > 0 ? `₹${mrpVal.toLocaleString('en-IN')}` : `₹${saleVal.toLocaleString('en-IN')}`;

            const salePreview = document.getElementById('preview-sale-display');
            if (salePreview) salePreview.innerText = `₹${saleVal.toLocaleString('en-IN')}`;

            const advancePreview = document.getElementById('preview-advance-display');
            if (advancePreview) advancePreview.innerText = `₹${advanceVal.toLocaleString('en-IN')}`;

            // Calculate final total
            let total = saleVal + setupVal + travelVal;
            if (!gstInclusive && gstPct > 0) {
                total += Math.round(total * (gstPct / 100));
            }

            const totalPreview = document.getElementById('preview-final-total-display');
            if (totalPreview) totalPreview.innerText = `₹${total.toLocaleString('en-IN')}`;
        }

        // Phase 3: Add Highlight Row Builder
        function addHighlightRow() {
            const container = document.getElementById('highlights-container');
            if (!container) return;
            const uid = 'hl_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

            const div = document.createElement('div');
            div.className = 'flex items-center gap-2 highlight-row';
            div.innerHTML = `
                <button type="button" onclick="openIconPicker('hl-icon-prev-${uid}', 'hl-icon-input-${uid}')" class="p-2 bg-main-bg border border-border-color rounded-lg hover:border-gold cursor-pointer shrink-0" title="Click icon to change">
                    <i id="hl-icon-prev-${uid}" class="fa-solid fa-star text-gold text-xs"></i>
                </button>
                <input type="hidden" name="highlight_icons[]" id="hl-icon-input-${uid}" value="fa-solid fa-star">
                <input type="text" name="highlights[]" placeholder="e.g. Premium Cold Pyro Entry Box" class="flex-grow bg-main-bg border border-border-color p-2.5 text-xs font-semibold text-[#1E1E24] rounded-lg outline-none focus:border-gold">
                <button type="button" onclick="this.closest('.highlight-row').remove()" class="text-red-500 hover:text-red-700 p-2 text-xs cursor-pointer">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            `;
            container.appendChild(div);
        }

        // Phase 1: Dynamic Custom Badge Builder
        function addCustomBadgeRow() {
            const container = document.getElementById('badges-grid-container');
            if (!container) return;
            const uid = 'cb_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            const index = container.children.length;

            const div = document.createElement('div');
            div.className = 'flex items-center gap-1.5 bg-white border border-gray-300 p-2 rounded-lg custom-badge-item min-w-0 shadow-sm';
            div.innerHTML = `
                <input type="checkbox" name="custom_badges[${index}][active]" value="1" checked class="w-4 h-4 text-gray-900 rounded accent-gray-900 shrink-0">
                <button type="button" onclick="openIconPicker('cb-icon-prev-${uid}', 'cb-icon-input-${uid}')" class="hover:scale-110 transition-transform cursor-pointer shrink-0" title="Click icon to change">
                    <i id="cb-icon-prev-${uid}" class="fa-solid fa-tag text-amber-500 text-xs"></i>
                </button>
                <input type="hidden" name="custom_badges[${index}][icon]" id="cb-icon-input-${uid}" value="fa-solid fa-tag">
                <input type="text" name="custom_badges[${index}][label]" value="" placeholder="e.g. VIP Setup" class="w-full text-xs font-semibold text-gray-900 border-none outline-none bg-transparent p-0 min-w-0">
                <button type="button" onclick="this.closest('.custom-badge-item').remove()" class="text-red-500 hover:text-red-700 text-xs p-1 cursor-pointer shrink-0" title="Remove badge">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            container.appendChild(div);
            const textInput = div.querySelector('input[type="text"]');
            if (textInput) textInput.focus();
        }

        // Phase 8: Dynamic Add-on Row Builder
        function addAddonRow() {
            const container = document.getElementById('addons-container');
            if (!container) return;
            const index = container.children.length;
            const uid = 'ad_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

            const div = document.createElement('div');
            div.className = 'grid grid-cols-1 md:grid-cols-12 gap-2 items-center bg-main-bg p-3 rounded-xl border border-border-color addon-row';
            div.innerHTML = `
                <div class="md:col-span-5 flex items-center gap-2">
                    <button type="button" onclick="openIconPicker('addon-icon-prev-${uid}', 'addon-icon-input-${uid}')" class="p-2 bg-card-bg border border-border-color rounded-lg hover:border-gold cursor-pointer shrink-0" title="Click icon to change">
                        <i id="addon-icon-prev-${uid}" class="fa-solid fa-fire text-gold text-xs"></i>
                    </button>
                    <input type="hidden" name="addons[${index}][icon]" id="addon-icon-input-${uid}" value="fa-solid fa-fire">
                    <input type="text" name="addons[${index}][title]" placeholder="Add-on Title (e.g. LED Number Sign)" class="w-full bg-card-bg border border-border-color p-2 text-xs font-semibold text-[#1E1E24] rounded-lg outline-none focus:border-gold">
                </div>
                <div class="md:col-span-3">
                    <input type="number" name="addons[${index}][price]" placeholder="1200" class="w-full bg-card-bg border border-border-color p-2 text-xs font-semibold text-[#1E1E24] rounded-lg outline-none focus:border-gold">
                </div>
                <div class="md:col-span-3">
                    <input type="text" name="addons[${index}][desc]" placeholder="Short note" class="w-full bg-card-bg border border-border-color p-2 text-xs font-semibold text-[#1E1E24] rounded-lg outline-none focus:border-gold">
                </div>
                <div class="md:col-span-1 text-right">
                    <button type="button" onclick="this.closest('.addon-row').remove()" class="text-red-500 hover:text-red-700 p-1.5 text-xs cursor-pointer">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            `;
            container.appendChild(div);
        }

        // Phase 9: Dynamic FAQ Row Builder
        function addFaqRow() {
            const container = document.getElementById('faqs-container');
            if (!container) return;
            const index = container.children.length;

            const div = document.createElement('div');
            div.className = 'flex flex-col gap-2 bg-main-bg p-3.5 rounded-xl border border-border-color faq-row relative';
            div.innerHTML = `
                <div class="flex justify-between items-center">
                    <span class="text-[8px] font-bold uppercase text-gold">FAQ Item #${index + 1}</span>
                    <button type="button" onclick="this.closest('.faq-row').remove()" class="text-red-500 hover:text-red-700 text-xs cursor-pointer">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
                <input type="text" name="faqs[${index}][question]" placeholder="Question: e.g. What are the venue electrical requirements?" class="w-full bg-card-bg border border-border-color p-2.5 text-xs font-semibold text-[#1E1E24] rounded-lg outline-none focus:border-gold">
                <textarea name="faqs[${index}][answer]" rows="2" placeholder="Answer: e.g. Requires a standard 220V 15A socket within 10 meters." class="w-full bg-card-bg border border-border-color p-2.5 text-xs font-semibold text-[#1E1E24] rounded-lg outline-none focus:border-gold"></textarea>
            `;
            container.appendChild(div);
        }

        // Image preview loader
        function previewFile(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('banner-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Initialize classic editors & pricing calculations
        window.addEventListener('DOMContentLoaded', () => {
            calculatePricingDetails();
            document.querySelectorAll('.ck-editor-textarea').forEach(textarea => {
                ClassicEditor
                    .create(textarea, {
                        toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ]
                    })
                    .catch(error => {
                        console.error(error);
                    });
            });

            // Enter key trigger for Custom Option Modal Input
            document.getElementById('custom-option-modal-input')?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitCustomOptionModal();
                }
            });
        });
    </script>
</body>

</html>

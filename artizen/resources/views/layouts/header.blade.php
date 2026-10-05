<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $settings = \App\Services\JsonStorageService::read('settings.json');
        $siteName = 'ARTIZEN';
        $currentUrl = url()->current();
        
        $defaultTitle = $seoTitle ?? ($settings['meta_title'] ?? 'ARTIZEN - Instant Event Booking Indore');
        $defaultDescription = $metaDescription ?? ($settings['meta_description'] ?? 'Book complete ready-to-go event setups, sound systems, lights, decor, and artists instantly at affordable package prices for Indore celebrations.');
        $defaultKeywords = $metaKeywords ?? ($settings['meta_keywords'] ?? 'event booking indore, birthday decoration, house party sound rig, proposal setup, wedding decorator indore, sound system rental indore, artizen events');
        $defaultOgImage = $seoImage ?? asset('assets/images/logo/Artizen_logo.png');
    @endphp

    <!-- Primary Meta Tags -->
    <title>@yield('title', $defaultTitle)</title>
    <meta name="title" content="@yield('title', $defaultTitle)">
    <meta name="description" content="@yield('meta_description', $defaultDescription)">
    <meta name="keywords" content="@yield('meta_keywords', $defaultKeywords)">
    <meta name="author" content="ARTIZEN Events">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="{{ $currentUrl }}">

    <!-- Geo & Local SEO Meta Tags (Indore, Madhya Pradesh) -->
    <meta name="geo.region" content="IN-MP">
    <meta name="geo.placename" content="Indore">
    <meta name="geo.position" content="22.7196;75.8577">
    <meta name="ICBM" content="22.7196, 75.8577">

    <!-- Mobile & PWA Theme Colors -->
    <meta name="theme-color" content="#111111">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ARTIZEN">
    <meta name="application-name" content="ARTIZEN">
    <meta name="format-detection" content="telephone=no">

    <!-- Open Graph / Facebook / WhatsApp / LinkedIn -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:url" content="{{ $currentUrl }}">
    <meta property="og:title" content="@yield('title', $defaultTitle)">
    <meta property="og:description" content="@yield('meta_description', $defaultDescription)">
    <meta property="og:image" content="@yield('og_image', $defaultOgImage)">
    <meta property="og:image:secure_url" content="@yield('og_image', $defaultOgImage)">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="ARTIZEN Event Booking Platform Indore">
    <meta property="og:locale" content="en_IN">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@artizenevents">
    <meta name="twitter:creator" content="@artizenevents">
    <meta name="twitter:url" content="{{ $currentUrl }}">
    <meta name="twitter:title" content="@yield('title', $defaultTitle)">
    <meta name="twitter:description" content="@yield('meta_description', $defaultDescription)">
    <meta name="twitter:image" content="@yield('og_image', $defaultOgImage)">
    <meta name="twitter:image:alt" content="ARTIZEN Event Booking Platform">

    <!-- Favicons & App Icons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/logo/artizen.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/logo/artizen.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/logo/artizen.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Schema.org JSON-LD Structured Data for Local Business & Event Service -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => 'ARTIZEN Events',
        'alternateName' => 'Artizen Event Booking Platform Indore',
        'url' => url('/'),
        'logo' => asset('assets/images/logo/artizen.png'),
        'image' => asset('assets/images/logo/Artizen_logo.png'),
        'description' => 'Book complete ready-to-go event setups, sound systems, lights, decor, and artists instantly at affordable package prices for Indore celebrations.',
        'telephone' => '+919131668156',
        'priceRange' => '₹₹',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Vijay Nagar',
            'addressLocality' => 'Indore',
            'addressRegion' => 'Madhya Pradesh',
            'postalCode' => '452010',
            'addressCountry' => 'IN'
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => 22.7196,
            'longitude' => 75.8577
        ],
        'openingHoursSpecification' => [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => [
                'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
            ],
            'opens' => '00:00',
            'closes' => '23:59'
        ],
        'sameAs' => [
            'https://wa.me/919131668156'
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <!-- Production Compiled Tailwind CSS & App Bundle via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts: Plus Jakarta Sans (600, 700, 800) & Inter (400, 500, 600) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Swiper JS & CSS (Locally hosted for instantaneous load without CDN latency) -->
    <link rel="stylesheet" href="{{ asset('vendor/swiper/swiper-bundle.min.css') }}" />
    <script src="{{ asset('vendor/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <!-- GSAP for Dropdowns & Animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

    <!-- Custom Stylesheet with CSS Custom Variables -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">

    <!-- Category icons and other inline scripts moved to ./js/main.js -->

    <!-- Main site JS -->
    <script src="{{ asset('js/main.js') }}"></script>

    <style>
        /* Smooth native scrolling without jump on refresh */
        html {
            scroll-behavior: smooth;
        }

        /* Direct adjustments allowed by editing the variables in :root inside style.css */
        /* CRT Scanlines / Noise Overlay for concert vibes */
        .noise-overlay {
            background-image: radial-gradient(rgba(255, 255, 255, 0.12) 1px, transparent 0), radial-gradient(rgba(255, 255, 255, 0.12) 1px, transparent 0);
            background-size: 10px 10px;
            background-position: 0 0, 5px 5px;
            pointer-events: none;
            opacity: 0.14;
        }

        .text-stroke {
            -webkit-text-stroke: 1px var(--text-main);
            color: transparent;
        }
    </style>
    <script>
        window.applyTheme = function(theme) {
            const isDark = theme === 'dark';
            document.documentElement.setAttribute('data-theme', theme);
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            try { localStorage.setItem('theme', theme); } catch(e){}

            // Update single icon on desktop
            const headerIcon = document.getElementById('theme-toggle-icon');
            if (headerIcon) {
                headerIcon.className = isDark 
                    ? 'fa-solid fa-sun text-xs text-amber-400 transition-all duration-300 group-hover:scale-110'
                    : 'fa-solid fa-moon text-xs text-gray-700 transition-all duration-300 group-hover:scale-110';
            }

            // Update single icon on mobile
            const mobileIcon = document.getElementById('mobile-theme-icon');
            if (mobileIcon) {
                mobileIcon.className = isDark 
                    ? 'fa-solid fa-sun text-xs text-amber-400'
                    : 'fa-solid fa-moon text-xs text-gray-400';
            }
            const mobileText = document.getElementById('mobile-theme-text');
            if (mobileText) {
                mobileText.textContent = isDark ? 'Switch to Light' : 'Switch to Dark';
            }
        };

        window.toggleTheme = function() {
            const isDark = document.documentElement.classList.contains('dark') || document.documentElement.getAttribute('data-theme') === 'dark';
            window.applyTheme(isDark ? 'light' : 'dark');
        };

        // Immediately set HTML theme attribute & class to prevent flicker
        (function() {
            try {
                const savedTheme = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-theme', savedTheme);
                if (savedTheme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch(e){}
        })();

        // Synchronize icon as soon as DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            window.applyTheme(savedTheme);
        });
    </script>

    <script>
        @php
            $artists = \App\Services\JsonStorageService::read('artists.json');
            $packages = \App\Services\JsonStorageService::read('packages.json');
            $categories = \App\Services\JsonStorageService::read('categories.json');
            $settings = \App\Services\JsonStorageService::read('settings.json');
        @endphp

        window.dynamicArtists = @json($artists);
        window.eventDatabase = @json($packages);
        window.eventCategories = @json($categories);
        window.hubSettings = @json($settings);
    </script>
</head>

<body class="bg-main-bg text-main-text font-body selection:bg-main-text selection:text-main-bg">

    <!-- Noise Background Layer (placed behind content) -->
    <div class="fixed inset-0 noise-overlay z-0 pointer-events-none"></div>

    
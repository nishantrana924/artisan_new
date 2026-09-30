<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

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

    <!-- Tailwind CSS v3 Play CDN suppressor -->
    <script>
        (function () {
            const origWarn = console.warn;
            console.warn = function (...args) {
                if (args[0] && typeof args[0] === 'string' && args[0].includes('cdn.tailwindcss.com')) return;
                origWarn.apply(console, args);
            };
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts: Plus Jakarta Sans (600, 700, 800) & Inter (400, 500, 600) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Swiper JS & CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

    <!-- Lenis Smooth Scroll CSS -->
    <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.18/dist/lenis.css" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <!-- GSAP & Lenis Smooth Scroll JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>

    <!-- Custom Stylesheet with CSS Custom Variables -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <script>
        // Bind Tailwind custom utilities to our Black + Gold + Warm White Design System
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'primary': '#FFD600',
                        'primary-hover': '#E6C200',
                        'primary-soft': '#FFF4B8',
                        'brand-gold': '#FFD600',
                        'brand-gold-hover': '#E6C200',
                        'brand-gold-soft': '#FFF4B8',
                        'brand-dark': '#171719',
                        'brand-dark-deep': '#080808',
                        'brand-dark-soft': '#292929',
                        'brand-bg': '#FAF9F6',
                        'brand-surface': '#FFFFFF',
                        'brand-surface-soft': '#F1EEE7',
                        'brand-border': '#E6E2D8',
                        'gold': '#FFD600',
                        'gold-hover': '#E6C200',
                        'gold-soft': '#FFF4B8',
                        'artizen-orange': '#FFD600',
                        'artizen-orange-hover': '#E6C200',
                        'artizen-orange-soft': '#FFF4B8',
                        'artizen-black': '#171719',
                        'main-bg': 'var(--bg-main)',
                        'surface-bg': 'var(--bg-surface)',
                        'card-bg': 'var(--bg-card)',
                        'main-text': 'var(--text-main)',
                        'muted-text': 'var(--text-muted)',
                        'primary-border': 'var(--border-color)',
                        'hover-border': 'var(--border-hover)',
                        'accent-bg': 'var(--accent)',
                        'accent-fg': 'var(--accent-text)',
                    },
                    fontFamily: {
                        heading: 'var(--font-heading)',
                        body: 'var(--font-body)',
                    }
                }
            }
        }
    </script>

    <!-- Category icons and other inline scripts moved to ./js/main.js -->

    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
    <!-- Main site JS -->
    <script src="{{ asset('js/main.js') }}"></script>
    <!-- Reels JS -->
    <script src="{{ asset('js/reels.js') }}"></script>

    <style>
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
        // Set default theme to dark mode (black) unless user has explicitly saved light mode
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
        if (savedTheme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
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

    
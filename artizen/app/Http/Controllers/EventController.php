<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Display the dynamic events catalog feed.
     */
    public function index(Request $request)
    {
        // Load categories and packages via JsonStorageService (database/json driver)
        $categoriesList = \App\Services\JsonStorageService::read('categories.json');

        // Map to expected route filters categories list (slug => display name)
        $categories = [];
        foreach ($categoriesList as $cat) {
            if ($cat['active'] ?? true) {
                $slug = 'cat-' . Str::slug($cat['title']);
                $categories[$slug] = $cat['title'];
            }
        }

        // Load packages via JsonStorageService
        $packagesDb = \App\Services\JsonStorageService::read('packages.json');

        /**
         * Slug normalization map:
         * packages.json category titles → matching categories.json slug
         * This ensures filter dropdowns (built from categories.json) match
         * card data-category values (built from packages.json).
         */
        $slugNormMap = [
            'adult-birthdays'    => 'birthdays',
            'birthdays'          => 'birthdays',
            'house-party-rigs'   => 'house-party',
            'house-party'        => 'house-party',
            'proposal-setups'    => 'proposal-anniversary',
            'proposal-anniversary' => 'proposal-anniversary',
            'wedding-sangeet'    => 'weddings-sangeet',
            'weddings-sangeet'   => 'weddings-sangeet',
            'corporate-events'   => 'baby-corporate',
            'baby-corporate'     => 'baby-corporate',
            'dj-acoustic'        => 'dj-acoustic',
            'kids-cozy'          => 'kids-cozy',
        ];

        $packages = [];
        $idCounter = 1;

        foreach ($packagesDb as $catId => $cat) {
            if (!($cat['active'] ?? true)) continue;

            $rawSlug = Str::slug($cat['title']);
            // Apply normalization map; fall back to rawSlug if not mapped
            $normalizedSlug = $slugNormMap[$rawSlug] ?? $rawSlug;
            $slug = 'cat-' . $normalizedSlug;

            foreach ($cat['tiers'] ?? [] as $tierIdx => $tier) {
                // Use tier's own badge if set, otherwise use smart tier labels
                $tierLabels = ['ESSENTIAL', 'BEST SELLER', 'PREMIUM', 'LUXURY', 'SIGNATURE'];
                $smartBadge = $tier['badge'] ?? ($tierLabels[$tierIdx] ?? ('TIER ' . ($tierIdx + 1)));

                $catGallery = $cat['gallery'] ?? [];
                $allCatTiers = $cat['tiers'] ?? [];
                $nextCatTier = $allCatTiers[($tierIdx + 1) % max(1, count($allCatTiers))] ?? [];
                $primaryImg = $tier['image'] ?? ($cat['image'] ?? '');
                $secondaryImg = !empty($catGallery[$tierIdx]) 
                    ? $catGallery[$tierIdx] 
                    : (!empty($catGallery[0]) && $catGallery[0] !== $primaryImg 
                        ? $catGallery[0] 
                        : (!empty($nextCatTier['image']) && $nextCatTier['image'] !== $primaryImg 
                            ? $nextCatTier['image'] 
                            : ($cat['image'] ?? $primaryImg)));

                $allPkgImages = array_values(array_filter(array_unique([
                    $primaryImg,
                    $secondaryImg,
                    $catGallery[0] ?? '',
                    $catGallery[1] ?? '',
                    $catGallery[2] ?? ''
                ])));

                $packages[] = [
                    'id'              => $idCounter++,
                    'event_id'        => (int)$catId,
                    'tier_index'      => (int)$tierIdx,
                    'category_id'     => $slug,
                    'category_name'   => $cat['title'],
                    'title'           => $tier['name'],
                    'price'           => (int)($tier['price'] ?? 0),
                    'rating'          => 4.9,
                    'desc'            => $tier['desc'] ?? '',
                    'image'           => $primaryImg,
                    'secondary_image' => $secondaryImg,
                    'all_images'      => $allPkgImages,
                    'badge'           => $smartBadge,
                    'inclusions'      => $tier['inclusions'] ?? []
                ];
            }
        }

        $selectedCategory = $request->query('category', $request->query('cat', 'all'));
        $selectedCategoryName = 'All Categories';

        if ($selectedCategory !== 'all') {
            $cleanSelected = str_replace('cat-', '', $selectedCategory);
            $cleanSelected = $slugNormMap[$cleanSelected] ?? $cleanSelected;
            $selectedCategory = 'cat-' . $cleanSelected;

            foreach ($categories as $catSlug => $catTitle) {
                $cleanCatSlug = str_replace('cat-', '', $catSlug);
                $cleanCatSlug = $slugNormMap[$cleanCatSlug] ?? $cleanCatSlug;
                if ($cleanCatSlug === $cleanSelected || Str::slug($catTitle) === $cleanSelected || $catSlug === $selectedCategory) {
                    $selectedCategory = $catSlug;
                    $selectedCategoryName = $catTitle;
                    break;
                }
            }

            // Prioritize matching category packages at the top
            usort($packages, function ($a, $b) use ($selectedCategory, $cleanSelected, $slugNormMap) {
                $cleanA = str_replace('cat-', '', $a['category_id'] ?? '');
                $cleanA = $slugNormMap[$cleanA] ?? $cleanA;
                $cleanB = str_replace('cat-', '', $b['category_id'] ?? '');
                $cleanB = $slugNormMap[$cleanB] ?? $cleanB;

                $aMatches = ($a['category_id'] === $selectedCategory || $cleanA === $cleanSelected || str_contains($cleanA, $cleanSelected) || str_contains($cleanSelected, $cleanA));
                $bMatches = ($b['category_id'] === $selectedCategory || $cleanB === $cleanSelected || str_contains($cleanB, $cleanSelected) || str_contains($cleanSelected, $cleanB));

                if ($aMatches && !$bMatches) return -1;
                if (!$aMatches && $bMatches) return 1;
                return $a['id'] <=> $b['id'];
            });
        }

        return view('events.index', compact('categories', 'packages', 'selectedCategory', 'selectedCategoryName'));
    }

    /**
     * Display an individual category page with its packages.
     */
    public function categoryShow($slug)
    {
        $categoriesList = \App\Services\JsonStorageService::read('categories.json');

        $cleanSlug = Str::slug(str_replace('cat-', '', $slug));
        if ($cleanSlug === 'adult-birthdays') $cleanSlug = 'birthdays';

        // Find current category
        $currentCategory = null;
        foreach ($categoriesList as $cat) {
            $catSlug = $cat['slug'] ?? Str::slug($cat['title'] ?? '');
            if ($catSlug === 'adult-birthdays') $catSlug = 'birthdays';

            if ($catSlug === $cleanSlug || Str::slug($cat['title'] ?? '') === $cleanSlug || ($cat['id'] ?? '') == $slug) {
                $currentCategory = $cat;
                break;
            }
        }

        if (!$currentCategory) {
            $currentCategory = [
                'id' => 1,
                'title' => ucwords(str_replace('-', ' ', $cleanSlug)),
                'slug' => $cleanSlug,
                'desc' => 'Explore ready-to-book setup packages for ' . ucwords(str_replace('-', ' ', $cleanSlug)) . ' in Indore.',
                'image' => asset('assets/images/hero/1.jpg'),
                'active' => true
            ];
        }

        // Load packages via JsonStorageService
        $packagesDb = \App\Services\JsonStorageService::read('packages.json');

        $categoryPackages = [];
        foreach ($packagesDb as $catId => $catData) {
            $dataSlug = Str::slug($catData['title'] ?? '');
            if ($dataSlug === 'adult-birthdays') $dataSlug = 'birthdays';

            if ($dataSlug === $cleanSlug || ($catData['id'] ?? '') == ($currentCategory['id'] ?? '')) {
                $catGallery = $catData['gallery'] ?? [];
                $allCatTiers = $catData['tiers'] ?? [];
                foreach ($catData['tiers'] ?? [] as $tierIdx => $tier) {
                    $nextCatTier = $allCatTiers[($tierIdx + 1) % max(1, count($allCatTiers))] ?? [];
                    $primaryImg = $tier['image'] ?? ($catData['image'] ?? $currentCategory['image']);
                    $secondaryImg = !empty($catGallery[$tierIdx]) 
                        ? $catGallery[$tierIdx] 
                        : (!empty($catGallery[0]) && $catGallery[0] !== $primaryImg 
                            ? $catGallery[0] 
                            : (!empty($nextCatTier['image']) && $nextCatTier['image'] !== $primaryImg 
                                ? $nextCatTier['image'] 
                                : ($catData['image'] ?? $primaryImg)));

                    $allPkgImages = array_values(array_filter(array_unique([
                        $primaryImg,
                        $secondaryImg,
                        $catGallery[0] ?? '',
                        $catGallery[1] ?? '',
                        $catGallery[2] ?? ''
                    ])));

                    $categoryPackages[] = [
                        'catId' => $catId,
                        'tierIndex' => $tierIdx,
                        'name' => $tier['name'] ?? $catData['title'],
                        'price' => $tier['price'] ?? 0,
                        'original_price' => $tier['original_price'] ?? null,
                        'desc' => $tier['desc'] ?? '',
                        'image' => $primaryImg,
                        'secondary_image' => $secondaryImg,
                        'all_images' => $allPkgImages,
                        'inclusions' => $tier['inclusions'] ?? [],
                        'badge' => $tier['badge'] ?? null
                    ];
                }
            }
        }

        return view('events.category', compact('currentCategory', 'categoryPackages', 'categoriesList'));
    }

    /**
     * Display single event details page.
     */
    public function show($slug)
    {
        $packagesDb = \App\Services\JsonStorageService::read('packages.json');

        $cleanSlug = Str::slug(str_replace('cat-', '', $slug));
        if ($cleanSlug === 'adult-birthdays') $cleanSlug = 'birthdays';

        // Search for matching category/package in packagesDb
        $matchedId = null;
        $matchedPackage = null;
        $matchedTierIdx = 0;

        foreach ($packagesDb as $catId => $catData) {
            $catSlugStr = Str::slug($catData['title'] ?? '');
            if ($catSlugStr === 'adult-birthdays') $catSlugStr = 'birthdays';

            // Check if slug matches category title slug or ID
            if ($catSlugStr === $cleanSlug || (string)$catId === (string)$slug || (string)($catData['id'] ?? '') === (string)$slug) {
                $matchedId = $catId;
                $matchedPackage = $catData;
                break;
            }

            // Check if slug matches any specific tier name/slug inside category
            foreach ($catData['tiers'] ?? [] as $tierIdx => $tier) {
                $tierSlugStr = $tier['slug'] ?? Str::slug($tier['name'] ?? '');
                if ($tierSlugStr === $cleanSlug) {
                    $matchedId = $catId;
                    $matchedPackage = $catData;
                    $matchedTierIdx = $tierIdx;
                    break 2;
                }
            }
        }

        // Fallback for numeric IDs or first available package
        if (!$matchedPackage) {
            if (is_numeric($slug) && isset($packagesDb[$slug])) {
                $matchedId = $slug;
                $matchedPackage = $packagesDb[$slug];
            } else if (!empty($packagesDb)) {
                // If not found at all, return 404
                abort(404, 'Event Package Not Found');
            }
        }

        return view('events.details', [
            'event' => $matchedPackage,
            'eventId' => $matchedId,
            'selectedTierIdx' => $matchedTierIdx,
            'packagesDb' => $packagesDb
        ]);
    }

    /**
     * Legacy booking save handler - delegates to canonical BookingController.
     */
    public function saveBooking(Request $request)
    {
        return app(BookingController::class)->store($request);
    }
}

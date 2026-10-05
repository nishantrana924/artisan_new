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
        // Load all celebration packages from the master catalog service
        $allPackages = \App\Services\CelebrationCatalogService::getAllPackages();
        $categoriesTaxonomy = \App\Services\CelebrationCatalogService::getCategories();

        // Calculate counts per category
        $categoryCounts = [
            'all' => count($allPackages)
        ];
        foreach ($allPackages as $pkg) {
            $cId = $pkg['category_id'] ?? 'other';
            $categoryCounts[$cId] = ($categoryCounts[$cId] ?? 0) + 1;
        }

        // Slug normalization map
        $slugNormMap = [
            'adult-birthdays'             => 'cat-birthdays',
            'birthdays'                   => 'cat-birthdays',
            'cat-birthdays'               => 'cat-birthdays',

            'kids-cozy'                   => 'cat-kids-cozy',
            'baby-shower-kids'            => 'cat-kids-cozy',
            'cat-baby-shower-kids'        => 'cat-kids-cozy',
            'cat-kids-cozy'               => 'cat-kids-cozy',

            'house-party-rigs'            => 'cat-house-party',
            'house-party'                 => 'cat-house-party',
            'cat-house-party'             => 'cat-house-party',

            'proposals'                   => 'cat-proposal-anniversary',
            'cat-proposals'               => 'cat-proposal-anniversary',
            'proposal-setups'             => 'cat-proposal-anniversary',
            'proposal-anniversary'        => 'cat-proposal-anniversary',
            'cat-proposal-anniversary'    => 'cat-proposal-anniversary',
            'anniversaries-proposals'     => 'cat-proposal-anniversary',
            'cat-anniversaries-proposals' => 'cat-proposal-anniversary',

            'wedding-sangeet'             => 'cat-weddings-sangeet',
            'weddings-sangeet'            => 'cat-weddings-sangeet',
            'cat-weddings-sangeet'        => 'cat-weddings-sangeet',
            'weddings-dj-sound'           => 'cat-weddings-sangeet',
            'cat-weddings-dj-sound'       => 'cat-weddings-sangeet',

            'dj-acoustic'                 => 'cat-dj-acoustic',
            'cat-dj-acoustic'             => 'cat-dj-acoustic',

            'corporate-events'            => 'cat-baby-corporate',
            'baby-corporate'              => 'cat-baby-corporate',
            'cat-baby-corporate'          => 'cat-baby-corporate',
        ];

        // Parse query filters
        $rawCategory = $request->query('category', $request->query('cat', 'all'));
        $selectedCategory = $slugNormMap[$rawCategory] ?? ($slugNormMap['cat-' . $rawCategory] ?? ($rawCategory ?: 'all'));
        if ($selectedCategory !== 'all' && !isset($categoriesTaxonomy[$selectedCategory])) {
            $selectedCategory = 'all';
        }

        $searchQuery = trim($request->query('q', $request->query('search', '')));
        $budgetFilter = $request->query('budget', 'all');
        $sortFilter = $request->query('sort', 'default');

        // Apply filters
        $filteredPackages = $allPackages;

        // 1. Category Filter
        if ($selectedCategory !== 'all') {
            $filteredPackages = array_values(array_filter($filteredPackages, function ($p) use ($selectedCategory) {
                return ($p['category_id'] ?? '') === $selectedCategory;
            }));
        }

        // 1.b Subcategory Filter (From Navbar mega menu or direct sub category)
        $subQuery = trim($request->query('sub', ''));
        if (!empty($subQuery)) {
            $lowerSub = strtolower(str_replace('-', ' ', $subQuery));
            $filteredPackages = array_values(array_filter($filteredPackages, function ($p) use ($subQuery, $lowerSub) {
                $subSlug = \Illuminate\Support\Str::slug($p['subcategory'] ?? '');
                $tagsStr = strtolower(implode(' ', $p['tags'] ?? []));
                return $subSlug === $subQuery ||
                       str_contains(strtolower($p['subcategory'] ?? ''), $lowerSub) ||
                       str_contains($tagsStr, $lowerSub);
            }));
        }

        // 1.c Recipient / Celebration For Filter ("Celebrations for Everyone" carousel / Storefront Filter)
        $forFilter = trim($request->query('for', $request->query('recipient', '')));
        if (!empty($forFilter)) {
            $lowerFor = strtolower($forFilter);
            $matchedByFor = array_values(array_filter($filteredPackages, function ($p) use ($lowerFor) {
                // 1. First check explicit database package recipients assigned in admin
                $pkgRecipients = array_map('strtolower', (array)($p['recipients'] ?? []));
                if (in_array($lowerFor, $pkgRecipients)) {
                    return true;
                }

                // 2. Intelligent keyword match fallback for older/unassigned packages
                $tagsStr = strtolower(implode(' ', $p['tags'] ?? []));
                $titleStr = strtolower($p['title'] ?? '');
                $subStr = strtolower($p['subcategory'] ?? '');

                return match($lowerFor) {
                    'him' => str_contains($titleStr, 'him') || str_contains($subStr, 'him') || str_contains($tagsStr, 'him') || str_contains($tagsStr, 'men') || str_contains($titleStr, 'birthday'),
                    'her' => str_contains($titleStr, 'her') || str_contains($subStr, 'her') || str_contains($tagsStr, 'her') || str_contains($tagsStr, 'women') || str_contains($tagsStr, 'princess') || str_contains($titleStr, 'proposal'),
                    'kids' => str_contains($titleStr, 'kid') || str_contains($subStr, 'kid') || str_contains($titleStr, 'baby') || str_contains($tagsStr, 'kids') || str_contains($tagsStr, 'baby'),
                    'friend' => str_contains($titleStr, 'party') || str_contains($titleStr, 'club') || str_contains($tagsStr, 'party') || str_contains($tagsStr, 'dj'),
                    'wife' => str_contains($titleStr, 'proposal') || str_contains($titleStr, 'cabana') || str_contains($titleStr, 'anniversary') || str_contains($tagsStr, 'romantic'),
                    'husband' => str_contains($titleStr, 'anniversary') || str_contains($titleStr, 'party') || str_contains($tagsStr, 'anniversary') || str_contains($tagsStr, 'romantic'),
                    'parents' => str_contains($titleStr, 'parent') || str_contains($titleStr, 'jubilee') || str_contains($subStr, 'parent') || str_contains($tagsStr, 'parents'),
                    default => str_contains($titleStr, $lowerFor) || str_contains($subStr, $lowerFor) || str_contains($tagsStr, $lowerFor)
                };
            }));
            if (!empty($matchedByFor)) {
                $filteredPackages = $matchedByFor;
            }
        }

        // 2. Search Filter
        if (!empty($searchQuery)) {
            $lowerQ = strtolower($searchQuery);
            $filteredPackages = array_values(array_filter($filteredPackages, function ($p) use ($lowerQ) {
                $tagsStr = implode(' ', $p['tags'] ?? []);
                return str_contains(strtolower($p['title'] ?? ''), $lowerQ) ||
                       str_contains(strtolower($p['subcategory'] ?? ''), $lowerQ) ||
                       str_contains(strtolower($p['category_name'] ?? ''), $lowerQ) ||
                       str_contains(strtolower($p['desc'] ?? ''), $lowerQ) ||
                       str_contains(strtolower($tagsStr), $lowerQ);
            }));
        }

        // 3. Budget Filter
        if ($budgetFilter !== 'all') {
            $filteredPackages = array_values(array_filter($filteredPackages, function ($p) use ($budgetFilter) {
                $price = (int)($p['price'] ?? 0);
                return match($budgetFilter) {
                    'under-5k' => $price < 5000,
                    '5k-7k'    => $price >= 5000 && $price <= 7000,
                    '7k-9k'    => $price > 7000 && $price <= 9000,
                    'above-9k' => $price > 9000,
                    default    => true
                };
            }));
        }

        // 4. Sort Filter
        usort($filteredPackages, function ($a, $b) use ($sortFilter) {
            return match($sortFilter) {
                'price-low'  => ($a['price'] ?? 0) <=> ($b['price'] ?? 0),
                'price-high' => ($b['price'] ?? 0) <=> ($a['price'] ?? 0),
                'name-az'    => strcasecmp($a['title'] ?? '', $b['title'] ?? ''),
                'rating'     => (($b['rating'] ?? 4.9) <=> ($a['rating'] ?? 4.9)) ?: (($b['reviews_count'] ?? 0) <=> ($a['reviews_count'] ?? 0)),
                default      => 0 // Default curation order
            };
        });

        // Categories map for simple dropdowns
        $categories = [];
        foreach ($categoriesTaxonomy as $slug => $cat) {
            if ($slug !== 'all') {
                $categories[$slug] = $cat['name'];
            }
        }

        // Subcategories grouped by category
        $subcategoriesByCategory = [];
        foreach ($allPackages as $pkg) {
            $catId = $pkg['category_id'];
            $sub = $pkg['subcategory'];
            if (!isset($subcategoriesByCategory[$catId])) {
                $subcategoriesByCategory[$catId] = [];
            }
            if (!in_array($sub, $subcategoriesByCategory[$catId])) {
                $subcategoriesByCategory[$catId][] = $sub;
            }
        }

        $selectedCategoryName = $categoriesTaxonomy[$selectedCategory]['name'] ?? 'All Celebrations';

        return view('events.index', [
            'packages' => $filteredPackages,
            'allPackages' => $allPackages,
            'categoriesTaxonomy' => $categoriesTaxonomy,
            'categoryCounts' => $categoryCounts,
            'subcategoriesByCategory' => $subcategoriesByCategory,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'selectedCategoryName' => $selectedCategoryName,
            'searchQuery' => $searchQuery,
            'budgetFilter' => $budgetFilter,
            'sortFilter' => $sortFilter,
            'personas' => \App\Models\CelebrationRecipient::active()->ordered()->get(),
            'forFilter' => $forFilter,
            'totalCount' => count($allPackages),
            'filteredCount' => count($filteredPackages),
        ]);
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
    /**
     * Display a specific event detail page by SEO slug.
     */
    public function show($slug)
    {
        $allCelebrationPackages = \App\Services\CelebrationCatalogService::getAllPackages();
        $packagesDb = \App\Services\JsonStorageService::read('packages.json');

        $rawSlug = strtolower(trim((string)$slug));
        $cleanSlug = Str::slug(str_replace(['cat-', 'pkg-'], '', $rawSlug));
        if ($cleanSlug === 'adult-birthdays') $cleanSlug = 'birthdays';

        $matchedPackage = null;
        $matchedId = null;
        $selectedTierIdx = (int)request()->query('tier', 0);

        // Map numeric IDs or short IDs directly to their canonical slug
        $legacyIdMap = [
            '1' => '1st-birthday-wonderland',
            '2' => 'rooftop-neon-dj-rig',
            '3' => 'marry-me-proposal',
            '4' => 'sangeet-dance-stage',
            '5' => 'live-acoustic-guitarist',
            '6' => 'sweet-16-club-bash',
            '7' => 'fairy-light-cabana',
            '8' => 'cozy-teepee-tent-village',
            '9' => 'corporate-gala-stage',
            '10' => 'haldi-brass-urli',
            '11' => 'punjabi-dhol-players',
            '12' => 'high-bass-speakers-mics',
            '14' => 'oh-baby-neon-throne',
            '15' => 'neon-sign-ring-arch'
        ];

        // 0. Direct Database lookup by slug or ID first - ensures any newly created admin package immediately loads
        $dbPkgModel = \App\Models\Package::with([
            'category',
            'subcategories',
            'tiers' => function ($q) {
                $q->where('active', true)->orderBy('display_order');
            }
        ])->where('active', true)->where(function ($q) use ($slug, $cleanSlug, $rawSlug) {
            $q->where('slug', $slug)
              ->orWhere('slug', $cleanSlug)
              ->orWhere('slug', $rawSlug);
            if (is_numeric($rawSlug)) {
                $q->orWhere('id', (int)$rawSlug);
            }
        })->first();

        if ($dbPkgModel) {
            $catNavSlug = $dbPkgModel->category?->nav_slug ?? ('cat-' . Str::slug($dbPkgModel->category?->title ?? 'celebrations'));
            $catName = $dbPkgModel->category?->title ?? 'Celebrations';
            $subcat = $dbPkgModel->subcategories->first()?->name ?? ($dbPkgModel->tag ?: 'Curated Setup');
            $inclusions = [];
            $tiersList = [];
            foreach ($dbPkgModel->tiers as $t) {
                $tInclusions = is_array($t->inclusions) ? $t->inclusions : [];
                if (empty($inclusions) && !empty($tInclusions)) {
                    $inclusions = $tInclusions;
                }
                $tiersList[] = [
                    'id'             => $t->id,
                    'name'           => $t->name,
                    'badge'          => $t->badge ?? null,
                    'price'          => (float)$t->price,
                    'original_price' => (float)($dbPkgModel->original_price ?: round($t->price * 1.15)),
                    'desc'           => $t->description ?: $dbPkgModel->description,
                    'image'          => $t->image ?: $dbPkgModel->image,
                    'inclusions'     => $tInclusions,
                ];
            }
            $tags = array_values(array_filter(array_unique(array_merge(
                [$dbPkgModel->tag ?: 'decor'],
                $dbPkgModel->subcategories->pluck('name')->toArray(),
                explode(' ', strtolower($dbPkgModel->title))
            ))));
            $dbPkgData = [
                'id'             => 'pkg-' . $dbPkgModel->id,
                'db_id'          => $dbPkgModel->id,
                'slug'           => $dbPkgModel->slug,
                'title'          => $dbPkgModel->title,
                'subcategory'    => $subcat,
                'category_id'    => $catNavSlug,
                'category_name'  => $catName,
                'price'          => (float)$dbPkgModel->price,
                'original_price' => (float)($dbPkgModel->original_price ?: round($dbPkgModel->price * 1.15)),
                'rating'         => 4.9,
                'reviews_count'  => 65,
                'image'          => $dbPkgModel->image ?: asset('assets/images/hero/1.jpg'),
                'gallery'        => is_array($dbPkgModel->gallery) && !empty($dbPkgModel->gallery) ? $dbPkgModel->gallery : [$dbPkgModel->image],
                'desc'           => $dbPkgModel->description ?? '',
                'tags'           => $tags,
                'inclusions'     => $inclusions,
                'badge'          => $dbPkgModel->badge ?: '15% OFF',
                'tiers'          => $tiersList,
            ];
            $matchedPackage = $this->buildCelebrationPackagePayload($dbPkgData);
            $matchedId = $dbPkgData['id'];
        }

        // 1. Direct slug match in CelebrationCatalogService (Priority #1 for clean SEO slugs)
        if (!$matchedPackage) {
            foreach ($allCelebrationPackages as $pkg) {
                $pSlug = strtolower($pkg['slug'] ?? '');
                $pTitleSlug = Str::slug($pkg['title'] ?? '');
                if ($pSlug === $cleanSlug || $pSlug === $rawSlug || $pTitleSlug === $cleanSlug) {
                    $matchedPackage = $this->buildCelebrationPackagePayload($pkg);
                    $matchedId = $pkg['id'];
                    break;
                }
            }
        }

        // 2. Legacy / Numeric ID mapping
        if (!$matchedPackage && isset($legacyIdMap[$rawSlug])) {
            $targetSlug = $legacyIdMap[$rawSlug];
            foreach ($allCelebrationPackages as $pkg) {
                if (strtolower($pkg['slug'] ?? '') === $targetSlug) {
                    $matchedPackage = $this->buildCelebrationPackagePayload($pkg);
                    $matchedId = $pkg['id'];
                    break;
                }
            }
        }

        // 3. ID match by pkg-XXX
        if (!$matchedPackage) {
            foreach ($allCelebrationPackages as $pkg) {
                $pId = strtolower($pkg['id'] ?? '');
                if ($pId === $rawSlug || $pId === 'pkg-' . $rawSlug) {
                    $matchedPackage = $this->buildCelebrationPackagePayload($pkg);
                    $matchedId = $pkg['id'];
                    break;
                }
            }
        }

        // 4. Legacy packagesDb match
        if (!$matchedPackage) {
            foreach ($packagesDb as $catId => $catData) {
                $catSlugStr = Str::slug($catData['title'] ?? '');
                if ($catSlugStr === 'adult-birthdays') $catSlugStr = 'birthdays';

                if ($catSlugStr === $cleanSlug || (string)$catId === (string)$rawSlug || (string)($catData['id'] ?? '') === (string)$rawSlug) {
                    $matchedId = $catId;
                    $matchedPackage = $catData;
                    if (!isset($matchedPackage['slug'])) {
                        $matchedPackage['slug'] = $catSlugStr;
                    }
                    break;
                }

                foreach ($catData['tiers'] ?? [] as $tierIdx => $tier) {
                    $tierSlugStr = $tier['slug'] ?? Str::slug($tier['name'] ?? '');
                    if ($tierSlugStr === $cleanSlug) {
                        $matchedId = $catId;
                        $matchedPackage = $catData;
                        $selectedTierIdx = $tierIdx;
                        if (!isset($matchedPackage['slug'])) {
                            $matchedPackage['slug'] = $tierSlugStr;
                        }
                        break 2;
                    }
                }
            }
        }

        // 5. Fallback if still not matched
        if (!$matchedPackage) {
            if (!empty($allCelebrationPackages)) {
                $first = $allCelebrationPackages[0];
                return redirect()->route('events.show', ['slug' => $first['slug']], 301);
            } else {
                abort(404, 'Event Package Not Found');
            }
        }

        // 6. Canonical Slug 301 Redirect: Ensure URL in browser is ALWAYS the SEO slug
        $canonicalSlug = $matchedPackage['slug'] ?? Str::slug($matchedPackage['title'] ?? '');
        if (!empty($canonicalSlug) && strtolower(trim((string)$slug)) !== strtolower($canonicalSlug)) {
            $queryParams = request()->query();
            return redirect()->route('events.show', array_merge(['slug' => $canonicalSlug], $queryParams), 301);
        }

        // 7. Build comprehensive combined DB for frontend JS (thumbnails, similar packages, tier switching)
        $combinedDb = $packagesDb;
        foreach ($allCelebrationPackages as $cPkg) {
            $cId = $cPkg['id'];
            if (!isset($combinedDb[$cId])) {
                $combinedDb[$cId] = [
                    'id' => $cPkg['id'],
                    'slug' => $cPkg['slug'],
                    'title' => $cPkg['title'],
                    'category' => $cPkg['category_name'] ?? 'Celebrations',
                    'desc' => $cPkg['desc'],
                    'image' => $cPkg['image'],
                    'gallery' => [ $cPkg['image'] ],
                    'tiers' => [
                        [
                            'name' => $cPkg['title'],
                            'price' => (int)$cPkg['price'],
                            'badge' => 'ESSENTIAL',
                            'desc' => $cPkg['desc'],
                            'image' => $cPkg['image'],
                            'inclusions' => $cPkg['inclusions'] ?? []
                        ]
                    ]
                ];
            }
        }

        if ($matchedId && !isset($combinedDb[$matchedId])) {
            $combinedDb[$matchedId] = $matchedPackage;
        }

        // Filter similar/related celebrations from master CelebrationCatalogService
        $currentCatId = $matchedPackage['category_id'] ?? null;
        $currentSlug = $matchedPackage['slug'] ?? '';
        
        $similarCelebrations = [];
        $otherCelebrations = [];
        foreach ($allCelebrationPackages as $cPkg) {
            if ($cPkg['slug'] === $currentSlug || $cPkg['id'] === $matchedId) {
                continue;
            }
            if (!empty($currentCatId) && ($cPkg['category_id'] ?? '') === $currentCatId) {
                $similarCelebrations[] = $cPkg;
            } else {
                $otherCelebrations[] = $cPkg;
            }
        }
        $similarCelebrations = array_slice(array_merge($similarCelebrations, $otherCelebrations), 0, 4);

        // Fetch package-particular reviews & dynamic rating metrics
        $packageReviewsData = \App\Http\Controllers\ReviewController::getReviewsForPackage(
            $matchedPackage['slug'] ?? $slug,
            $matchedPackage['title'] ?? '',
            $matchedPackage['category_name'] ?? ''
        );

        return view('events.details', [
            'event' => $matchedPackage,
            'eventId' => $matchedId,
            'selectedTierIdx' => $selectedTierIdx,
            'packagesDb' => $combinedDb,
            'similarCelebrations' => $similarCelebrations,
            'packageReviewsData' => $packageReviewsData
        ]);
    }

    /**
     * Helper to format a celebration catalog package for the view.
     */
    private function buildCelebrationPackagePayload(array $pkg): array
    {
        $basePrice = (int)$pkg['price'];
        $origPrice = (int)($pkg['original_price'] ?? round($basePrice * 1.15));

        $inclusions = $pkg['inclusions'] ?? [
            'Full On-Site Venue Setup in Indore',
            'Curated Themed Backdrops & Illumination',
            'Dedicated Setup Stylist & Crew',
            'Post-Event Dismantling & Clean Up'
        ];

        $gallery = !empty($pkg['gallery']) && is_array($pkg['gallery']) ? array_values(array_filter($pkg['gallery'])) : [];
        if (empty($gallery)) {
            $gallery = [
                $pkg['image'],
                asset('images/banners/custom_gathering_lights.webp'),
                asset('images/banners/custom_romantic_table.webp'),
                asset('images/banners/custom_fairy_reception.webp')
            ];
        }

        $tiers = [];
        if (!empty($pkg['tiers']) && is_array($pkg['tiers'])) {
            $tierBadges = ['STANDARD', 'MOST POPULAR', 'LUXURY VIP'];
            foreach ($pkg['tiers'] as $idx => $t) {
                $tPrice = (int)$t['price'];
                $tOrigPrice = (int)($t['original_price'] ?? round($tPrice * 1.15));
                $tiers[] = [
                    'id'             => $t['id'] ?? null,
                    'name'           => $t['name'] ?? 'Tier ' . ($idx + 1),
                    'badge'          => $t['badge'] ?? ($tierBadges[$idx] ?? 'PREMIUM'),
                    'price'          => $tPrice,
                    'original_price' => $tOrigPrice,
                    'desc'           => $t['desc'] ?? ($pkg['desc'] ?? 'Ready-to-celebrate setup with core themed props, backdrop and lighting.'),
                    'image'          => !empty($t['image']) ? $t['image'] : $pkg['image'],
                    'inclusions'     => !empty($t['inclusions']) ? $t['inclusions'] : $inclusions,
                    'gallery'        => $gallery
                ];
            }
        }

        if (empty($tiers)) {
            $tiers = [
                [
                    'name' => 'Essential Setup',
                    'badge' => 'STANDARD',
                    'price' => $basePrice,
                    'original_price' => $origPrice,
                    'desc' => $pkg['desc'] ?? 'Complete ready-to-celebrate setup with core themed props, backdrop and basic ambient lighting.',
                    'image' => $pkg['image'],
                    'inclusions' => $inclusions,
                    'gallery' => $gallery
                ],
                [
                    'name' => 'Premium Luxe',
                    'badge' => 'MOST POPULAR',
                    'price' => $basePrice + 1999,
                    'original_price' => round(($basePrice + 1999) * 1.15),
                    'desc' => 'Enhanced celebration experience with additional fairy lights, floral accents, customized name signage, and upgraded pedestal.',
                    'image' => $pkg['image'],
                    'inclusions' => array_merge($inclusions, [
                        'Custom Acrylic / Neon Name Board',
                        'Extended Fairy Light Canopies',
                        'Dedicated Event Coordinator On-Site'
                    ]),
                    'gallery' => $gallery
                ],
                [
                    'name' => 'Grand VIP Celebration',
                    'badge' => 'LUXURY VIP',
                    'price' => $basePrice + 4499,
                    'original_price' => round(($basePrice + 4499) * 1.15),
                    'desc' => 'The ultimate VIP setup: includes premium sound column rig, intelligent ambient lighting, photo corner, and cold pyro entry sparklers.',
                    'image' => $pkg['image'],
                    'inclusions' => array_merge($inclusions, [
                        'Custom Acrylic / Neon Name Board',
                        'Column Sound Audio Rig & Mic',
                        'Cold Pyro Entry Sparklers (4 Shots)',
                        'Priority Same-Day Setup Guarantee'
                    ]),
                    'gallery' => $gallery
                ]
            ];
        }

        return [
            'id' => $pkg['id'],
            'slug' => $pkg['slug'],
            'title' => $pkg['title'],
            'category' => $pkg['category_name'] ?? 'Celebrations',
            'subcategory' => $pkg['subcategory'] ?? 'Curated Setup',
            'desc' => $pkg['desc'] ?? 'Experience a seamless celebration with our professionally curated event setup in Indore.',
            'image' => $pkg['image'],
            'gallery' => $gallery,
            'rating' => $pkg['rating'] ?? 4.9,
            'reviews_count' => $pkg['reviews_count'] ?? 65,
            'tiers' => $tiers
        ];
    }

    /**
     * Legacy booking save handler - delegates to canonical BookingController.
     */
    public function saveBooking(Request $request)
    {
        return app(BookingController::class)->store($request);
    }
}

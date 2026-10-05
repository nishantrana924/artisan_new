<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Package;
use App\Models\PackageTier;
use App\Models\Subcategory;
use App\Services\CelebrationCatalogService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CelebrationPackagesSeeder extends Seeder
{
    /**
     * Seeds all 33 celebration packages from CelebrationCatalogService into MySQL database,
     * links them to their Category and Subcategories, and creates tiered pricing.
     */
    public function run(): void
    {
        $allPackages = CelebrationCatalogService::getStaticPackagesFallback();

        // Map category keys to database category IDs
        // ID 1: Birthdays
        // ID 2: House Party & DJ
        // ID 3: Proposals
        // ID 4: Anniversaries
        // ID 5: Weddings & Sangeet
        // ID 6: Baby Shower & Kids
        // ID 7: Corporate Events
        // ID 8: Live DJ & Acoustic
        $proposalKeywords = ['marry-me', 'proposal', 'cabana', 'rose-petal', 'starlight-dinner', 'violinist', 'sunset-terrace'];
        $anniversaryKeywords = ['jubilee', 'silver', 'golden', 'parents', 'anniversary', 'candlelight', 'memory-wall'];

        // Remove initial 5 dummy placeholder records if they exist
        Package::whereIn('slug', [
            'adult-birthdays-1',
            'house-party-rigs-2',
            'proposal-setups-3',
            'wedding-sangeet-4',
            'corporate-events-5'
        ])->delete();

        foreach ($allPackages as $index => $pkgData) {
            $catKey = $pkgData['category_id'] ?? 'cat-birthdays';
            $slug   = $pkgData['slug'];
            $title  = $pkgData['title'];

            // 1. Determine Category ID
            // 1. Determine Category dynamically from database
            $category = match($catKey) {
                'cat-birthdays'        => Category::where('nav_slug', 'cat-birthdays')->orWhere('slug', 'like', 'birthdays%')->first(),
                'cat-house-party'      => Category::where('nav_slug', 'cat-house-party')->orWhere('slug', 'like', 'house-party%')->first(),
                'cat-weddings-sangeet' => Category::where('nav_slug', 'cat-weddings-sangeet')->orWhere('slug', 'like', 'weddings%')->first(),
                'cat-kids-cozy'        => Category::where('nav_slug', 'cat-kids-cozy')->orWhere('slug', 'like', 'baby-shower%')->first(),
                'cat-baby-corporate'   => Category::where('nav_slug', 'cat-baby-corporate')->orWhere('slug', 'like', 'corporate%')->first(),
                'cat-dj-acoustic'      => Category::where('nav_slug', 'cat-dj-acoustic')->orWhere('slug', 'like', 'live-dj%')->first(),
                'cat-proposal-anniversary' => (function() use ($slug, $anniversaryKeywords) {
                    foreach ($anniversaryKeywords as $kw) {
                        if (str_contains($slug, $kw)) {
                            return Category::where('slug', 'like', 'anniversaries%')->orWhere('title', 'like', 'Anniversaries%')->first();
                        }
                    }
                    return Category::where('slug', 'like', 'proposals%')->orWhere('title', 'like', 'Proposals%')->first();
                })(),
                default => Category::first(),
            } ?? Category::first();

            // 2. Resolve / Create Subcategory under this category
            $subcatName = trim($pkgData['subcategory'] ?? 'Special Setup');
            $subcatSlug = Str::slug($subcatName);

            $subcategory = Subcategory::firstOrCreate(
                [
                    'category_id' => $category->id,
                    'name'        => $subcatName,
                ],
                [
                    'slug'        => $subcatSlug,
                    'is_active'   => true,
                ]
            );

            // Collect all relevant subcategories for this package
            $subcatIds = [$subcategory->id];

            // Check if existing subcategories under this category match tags
            $existingSubcats = Subcategory::where('category_id', $category->id)->get();
            foreach ($existingSubcats as $existingSub) {
                foreach ($pkgData['tags'] ?? [] as $t) {
                    if (str_contains(strtolower($existingSub->name), strtolower($t)) || str_contains(strtolower($existingSub->slug), strtolower($t))) {
                        $subcatIds[] = $existingSub->id;
                    }
                }
            }
            $subcatIds = array_values(array_unique($subcatIds));

            // 3. Normalize Image Path
            $rawImg = $pkgData['image'] ?? '';
            if (str_contains($rawImg, 'images/celebrations/')) {
                $filename = basename(parse_url($rawImg, PHP_URL_PATH));
                $imagePath = '/images/celebrations/' . $filename;
            } else {
                $imagePath = $rawImg;
            }

            // 4. Calculate Promotional Badge (BESTSELLER or LUXURY for curated setups)
            $price = (float)($pkgData['price'] ?? 4999);
            $origPrice = (float)($pkgData['original_price'] ?? round($price * 1.15));

            $isBestseller = in_array($slug, [
                '1st-birthday-wonderland',
                'neon-sign-ring-arch',
                'sweet-16-club-bash',
                '21st-high-energy-party',
                'rooftop-neon-dj-rig',
                'high-bass-speakers-mics',
                'silver-jubilee-arch',
                'fairy-light-cabana',
                '100-candlelight-pathway',
                'candlelight-pathway',
                'rose-petal-heart',
                'parents-anniversary-arch',
                'haldi-brass-urli',
                'mehendi-colorful-drapes',
                'punjabi-dhol-players',
                'cold-pyro-sparklers',
                'kids-jungle-safari',
                'pastel-balloon-arch',
                'oh-baby-neon-throne',
                'live-acoustic-guitarist',
            ]);

            $isLuxury = in_array($slug, [
                '30th-to-50th-jubilee',
                'jubilee-celebration',
                'the-marry-me-proposal',
                'marry-me-proposal',
                'golden-jubilee-stage',
                'private-rooftop-starlight-dinner',
                'rooftop-starlight-dinner',
                'secret-sunset-terrace-proposal',
                'sunset-terrace-proposal',
                'sangeet-dance-stage',
                'cozy-teepee-tent-village',
                'corporate-gala-stage',
                'farmhouse-poolside-bash',
                'sufi-bollywood-unplugged',
            ]);

            $badge = $isBestseller ? 'BESTSELLER' : ($isLuxury ? 'LUXURY' : 'POPULAR');

            // Primary tag
            $tag = $pkgData['tags'][0] ?? 'decor';

            // 5. Create or Update Package in Database
            $package = Package::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id'    => $category->id,
                    'title'          => $title,
                    'badge'          => $badge,
                    'tag'            => $tag,
                    'price'          => $price,
                    'original_price' => $origPrice,
                    'description'    => $pkgData['desc'] ?? '',
                    'image'          => $imagePath,
                    'gallery'        => null,
                    'active'         => true,
                ]
            );

            // 6. Sync Subcategories Pivot Table
            $package->subcategories()->sync($subcatIds);

            // 7. Create Package Tiers
            // Tier 1: Standard Setup
            $tier1Name = 'Standard Setup';
            $tier1Inclusions = $pkgData['inclusions'] ?? [
                'Full theme backdrop & decoration setup',
                'Ambient LED lighting & focus spots',
                'Indore on-site delivery and coordination team'
            ];

            PackageTier::updateOrCreate(
                [
                    'package_id' => $package->id,
                    'slug'       => Str::slug($package->title) . '-standard',
                ],
                [
                    'name'          => $tier1Name,
                    'price'         => $price,
                    'description'   => $package->description,
                    'image'         => $imagePath,
                    'inclusions'    => $tier1Inclusions,
                    'active'        => true,
                    'display_order' => 1,
                ]
            );

            // Tier 2: Premium VIP Experience
            $tier2Price = round($price * 1.35);
            $tier2Inclusions = array_merge($tier1Inclusions, [
                'Upgraded Premium Decor Elements & Floral Accents',
                'Heavy Low-Fog & Sparkler Entry Effects',
                'Dedicated Senior Event Coordinator On-Site'
            ]);

            PackageTier::updateOrCreate(
                [
                    'package_id' => $package->id,
                    'slug'       => Str::slug($package->title) . '-premium',
                ],
                [
                    'name'          => 'Premium VIP Setup',
                    'price'         => $tier2Price,
                    'description'   => 'Enhanced ' . strtolower($package->title) . ' experience with extended stage coverage, luxury lighting, and coordinator.',
                    'image'         => $imagePath,
                    'inclusions'    => $tier2Inclusions,
                    'active'        => true,
                    'display_order' => 2,
                ]
            );
        }
    }
}

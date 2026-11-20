<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageTier;
use App\Models\Subcategory;
use App\Services\CelebrationCatalogService;
use App\Services\ImageOptimizationService;
use App\Services\JsonStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminPackageController extends Controller
{
    /**
     * Render the create package page.
     */
    public function createPackagePage()
    {
        $categories = JsonStorageService::read('categories.json');

        return view('admin.packages.edit', [
            'categories' => $categories,
            'isEdit' => false,
            'catId' => null,
            'tierIdx' => null,
            'tier' => [
                'name' => '',
                'slug' => '',
                'badge' => 'PREMIUM PACKAGE',
                'sub_category' => 'decor',
                'display_order' => 1,
                'switches' => [
                    'featured' => false,
                    'recommended' => false,
                    'popular' => false,
                    'best_seller' => false,
                    'trending' => false,
                ],
                'internal_notes' => '',
                'price' => '',
                'original_price' => '',
                'discount_pct' => 0,
                'advance_deposit' => 2000,
                'setup_charge' => 0,
                'travel_charge' => 0,
                'extra_guest_rate' => 150,
                'gst_pct' => 18,
                'gst_inclusive' => true,
                'short_desc' => '',
                'detailed_desc' => '',
                'highlights' => [],
                'important_notes' => '',
                'terms' => '',
                'specifications' => [
                    'guest_capacity' => '50 - 100 Guests',
                    'setup_time' => '3-4 Hours',
                    'duration' => '4-5 Hours',
                    'space_req' => '12ft x 8ft Stage',
                    'location_type' => 'Indoor & Outdoor',
                    'power_req' => '220V 15A Power Socket',
                    'crew_count' => '3 Members',
                ],
                'video_url' => '',
                'virtual_tour_url' => '',
                'pdf_brochure' => '',
                'exclusions' => [],
                'addons' => [],
                'faqs' => [],
                'seo' => [
                    'meta_title' => '',
                    'meta_description' => '',
                    'meta_keywords' => '',
                ],
                'availability' => [
                    'lead_time_hours' => 24,
                    'max_daily_bookings' => 3,
                ],
                'service_area' => [
                    'city' => 'Indore',
                    'outstation_allowed' => true,
                    'outstation_charge' => 1500,
                ],
                'status' => 'published',
                'desc' => '',
                'inclusions' => [],
                'image' => '/assets/images/hero/1.jpg',
            ],
        ]);
    }

    /**
     * Render the edit package page.
     */
    public function editPackagePage($catId, $tierIdx)
    {
        $categoriesPath = storage_path('app/categories.json');
        $categories = [];
        if (File::exists($categoriesPath)) {
            $categories = json_decode(File::get($categoriesPath), true) ?? [];
        }

        $packagesPath = storage_path('app/packages.json');
        $tier = null;
        if (File::exists($packagesPath)) {
            $packages = json_decode(File::get($packagesPath), true) ?? [];
            if (isset($packages[$catId]['tiers'][$tierIdx])) {
                $tier = $packages[$catId]['tiers'][$tierIdx];
            }
        }

        if (!$tier) {
            return redirect()->route('admin.packages')->with('error', 'Package tier plan not found!');
        }

        // Apply defaults for Phase 1 basic information fields if not set
        $tier['slug'] = $tier['slug'] ?? Str::slug($tier['name'] ?? '');
        $tier['badge'] = $tier['badge'] ?? 'PREMIUM PACKAGE';
        $tier['sub_category'] = $tier['sub_category'] ?? 'decor';
        $tier['display_order'] = $tier['display_order'] ?? 1;
        $tier['switches'] = $tier['switches'] ?? [
            'featured' => false,
            'recommended' => false,
            'popular' => false,
            'best_seller' => false,
            'trending' => false,
        ];
        $tier['internal_notes'] = $tier['internal_notes'] ?? '';

        // Apply defaults for Phase 2 Pricing matrix fields if not set
        $tier['original_price'] = $tier['original_price'] ?? ($tier['price'] ? (int)round($tier['price'] * 1.25) : '');
        $tier['discount_pct'] = $tier['discount_pct'] ?? (($tier['original_price'] && $tier['price']) ? (int)round((($tier['original_price'] - $tier['price']) / $tier['original_price']) * 100) : 0);
        $tier['advance_deposit'] = $tier['advance_deposit'] ?? (int)round(($tier['price'] ?? 0) * 0.2);
        $tier['setup_charge'] = $tier['setup_charge'] ?? 0;
        $tier['travel_charge'] = $tier['travel_charge'] ?? 0;
        $tier['extra_guest_rate'] = $tier['extra_guest_rate'] ?? 150;
        $tier['gst_pct'] = $tier['gst_pct'] ?? 18;
        $tier['gst_inclusive'] = $tier['gst_inclusive'] ?? true;

        // Apply defaults for Phase 3 Descriptions & Guidelines fields if not set
        $tier['short_desc'] = $tier['short_desc'] ?? ($tier['desc'] ?? '');
        $tier['detailed_desc'] = $tier['detailed_desc'] ?? ($tier['desc'] ?? '');
        $tier['highlights'] = $tier['highlights'] ?? [];
        $tier['important_notes'] = $tier['important_notes'] ?? '';
        $tier['terms'] = $tier['terms'] ?? '';

        // Apply defaults for Phase 4 Technical Specifications if not set
        $tier['specifications'] = array_merge([
            'guest_capacity' => '50 - 100 Guests',
            'setup_time' => '3-4 Hours',
            'duration' => '4-5 Hours',
            'space_req' => '12ft x 8ft Stage',
            'location_type' => 'Indoor & Outdoor',
            'power_req' => '220V 15A Power Socket',
            'crew_count' => '3 Members',
        ], $tier['specifications'] ?? []);

        // Apply defaults for Phase 5 Gallery & Media fields if not set
        $tier['video_url'] = $tier['video_url'] ?? '';
        $tier['virtual_tour_url'] = $tier['virtual_tour_url'] ?? '';
        $tier['pdf_brochure'] = $tier['pdf_brochure'] ?? '';

        // Apply defaults for remaining modules (exclusions, addons, faqs, seo, availability, service_area, status)
        $tier['exclusions'] = $tier['exclusions'] ?? [];
        $tier['addons'] = $tier['addons'] ?? [];
        $tier['faqs'] = $tier['faqs'] ?? [];
        $tier['seo'] = array_merge([
            'meta_title' => $tier['name'] ?? '',
            'meta_description' => $tier['short_desc'] ?? ($tier['desc'] ?? ''),
            'meta_keywords' => 'event decoration indore, birthday package',
        ], $tier['seo'] ?? []);
        $tier['availability'] = array_merge([
            'lead_time_hours' => 24,
            'max_daily_bookings' => 3,
        ], $tier['availability'] ?? []);
        $tier['service_area'] = array_merge([
            'city' => 'Indore',
            'outstation_allowed' => true,
            'outstation_charge' => 1500,
        ], $tier['service_area'] ?? []);
        $tier['status'] = $tier['status'] ?? 'published';

        // Ensure gallery array has at least 4 items for UI preview
        $gallery = $tier['gallery'] ?? [];
        if (!is_array($gallery)) {
            $gallery = [$tier['image'] ?? '/assets/images/hero/1.jpg'];
        }
        while (count($gallery) < 4) {
            $gallery[] = $tier['image'] ?? '/assets/images/hero/1.jpg';
        }
        $tier['gallery'] = array_slice($gallery, 0, 4);

        return view('admin.packages.edit', [
            'categories' => $categories,
            'isEdit' => true,
            'catId' => $catId,
            'tierIdx' => $tierIdx,
            'tier' => $tier,
        ]);
    }

    /**
     * Save/Create a package and its tiers in MySQL database and sync subcategories.
     */
    public function savePackage(Request $request)
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,id',
            'slug'            => 'nullable|string|max:255',
            'badge'           => 'nullable|string|max:50',
            'tag'             => 'nullable|string|max:50',
            'recipients'      => 'nullable',
            'price'           => 'nullable|numeric|min:0',
            'original_price'  => 'nullable|numeric|min:0',
            'description'     => 'nullable|string',
            'image'           => 'nullable|string|max:1000',
            'image_file'      => 'nullable|file|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'gallery_files'   => 'nullable|array|max:10',
            'gallery_files.*' => 'nullable|file|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        if ($request->filled('price') && $request->filled('original_price')) {
            if ((float)$request->input('original_price') < (float)$request->input('price')) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['original_price' => 'The original price must be greater than or equal to the package selling price.']);
            }
        }

        $id = $request->input('id') ?: $request->input('package_id');
        $title = trim($request->input('title'));
        $slug = $request->input('slug') ? Str::slug($request->input('slug')) : Str::slug($title);

        // Ensure unique slug
        $existingSlug = Package::where('slug', $slug)
            ->when($id, fn($q) => $q->where('id', '!=', $id))
            ->exists();
        if ($existingSlug) {
            $slug .= '-' . time();
        }

        $package = $id ? Package::findOrFail($id) : new Package();

        // 1. Process Main Cover Image
        if ($request->hasFile('image_file')) {
            try {
                $image = app(ImageOptimizationService::class)->optimize(
                    file: $request->file('image_file'),
                    targetSubdir: 'uploads/packages',
                    profile: 'package',
                    prefix: 'pkg',
                    quality: ImageOptimizationService::DEFAULT_QUALITY
                );
            } catch (\Throwable $e) {
                Log::warning('Package cover image optimization failed: ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Package image processing failed: ' . $e->getMessage());
            }
        } elseif ($request->has('image')) {
            $image = trim((string)$request->input('image')) ?: null;
        } else {
            $image = $package->image;
        }

        // 2. Process Gallery Banners
        $galleryImages = [];
        $galleryInputs = $request->input('gallery', []);
        $galleryFiles  = $request->file('gallery_files', []);

        for ($i = 0; $i < 4; $i++) {
            if (isset($galleryFiles[$i]) && $galleryFiles[$i]->isValid()) {
                try {
                    $galleryImages[] = app(ImageOptimizationService::class)->optimize(
                        file: $galleryFiles[$i],
                        targetSubdir: 'uploads/packages',
                        profile: 'gallery',
                        prefix: 'pkg_gal_' . $i,
                        quality: ImageOptimizationService::DEFAULT_QUALITY
                    );
                } catch (\Throwable $e) {
                    Log::warning("Package gallery image {$i} optimization failed: " . $e->getMessage());
                    if (!empty($galleryInputs[$i])) {
                        $galleryImages[] = trim($galleryInputs[$i]);
                    }
                }
            } elseif (!empty($galleryInputs[$i])) {
                $galleryImages[] = trim($galleryInputs[$i]);
            }
        }
        $galleryImages = array_values(array_filter($galleryImages));

        // 3. Populate and Save Package
        $package->category_id    = (int) $request->input('category_id');
        $package->title          = $title;
        $package->slug           = $slug;
        $package->description    = $request->input('description', '');
        $package->price          = $request->input('price') ? (float)$request->input('price') : null;
        $package->original_price = $request->input('original_price') ? (float)$request->input('original_price') : null;
        $package->badge          = $request->input('badge') ?: null;
        $package->tag            = $request->input('tag', 'decor');
        $recipientsInput         = $request->input('recipients', []);
        if (is_string($recipientsInput)) {
            $recipientsInput = json_decode($recipientsInput, true) ?? [];
        }
        $package->recipients     = !empty($recipientsInput) ? array_values(array_filter(array_map('trim', (array)$recipientsInput))) : null;
        $package->image          = $image;
        $package->gallery        = !empty($galleryImages) ? $galleryImages : null;
        $package->active         = $request->boolean('active', true);
        $package->save();

        // 4. Sync Subcategories Pivot
        $subcatIds = $request->input('subcategories', []);
        if (is_string($subcatIds)) {
            $subcatIds = json_decode($subcatIds, true) ?? [];
        }
        $validSubcatIds = Subcategory::whereIn('id', (array)$subcatIds)
            ->where('category_id', $package->category_id)
            ->pluck('id')
            ->toArray();
        $package->subcategories()->sync($validSubcatIds);

        // 5. Process Tiers
        $tiersInput = $request->input('tiers', []);
        if (is_string($tiersInput)) {
            $tiersInput = json_decode($tiersInput, true) ?? [];
        }

        if (!empty($tiersInput) && is_array($tiersInput)) {
            $existingTierIds = [];
            foreach ($tiersInput as $idx => $t) {
                if (empty($t['name'])) continue;
                $tierSlug = Str::slug($t['name']) . '-' . ($idx + 1);
                $tierInclusions = [];
                if (!empty($t['inclusions'])) {
                    if (is_array($t['inclusions'])) {
                        $tierInclusions = array_values(array_filter(array_map('trim', $t['inclusions'])));
                    } else {
                        $tierInclusions = array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", "", strip_tags($t['inclusions']))))));
                    }
                }

                $tierData = [
                    'package_id'    => $package->id,
                    'name'          => trim($t['name']),
                    'slug'          => $tierSlug,
                    'price'         => (float)($t['price'] ?? 0),
                    'description'   => trim($t['description'] ?? ''),
                    'image'         => !empty($t['image']) ? $t['image'] : $package->image,
                    'inclusions'    => $tierInclusions,
                    'active'        => true,
                    'display_order' => $idx,
                ];

                if (!empty($t['id'])) {
                    $tierModel = PackageTier::where('id', $t['id'])->where('package_id', $package->id)->first();
                    if ($tierModel) {
                        $tierModel->update($tierData);
                        $existingTierIds[] = $tierModel->id;
                        continue;
                    }
                }

                $newTier = PackageTier::create($tierData);
                $existingTierIds[] = $newTier->id;
            }

            // Remove tiers that were removed by user
            if (!empty($existingTierIds)) {
                PackageTier::where('package_id', $package->id)->whereNotIn('id', $existingTierIds)->delete();
            }

            // Update package base price to lowest tier price if not explicitly set
            $minTierPrice = PackageTier::where('package_id', $package->id)->min('price');
            if ($minTierPrice > 0 && (!$package->price || $package->price <= 0)) {
                $package->price = (float)$minTierPrice;
                $package->save();
            }
        } else {
            // Default Standard tier if no tiers submitted
            $standardTier = $package->tiers()->first();
            $tierPrice = $package->price ?: 4999;
            if (!$standardTier) {
                PackageTier::create([
                    'package_id'    => $package->id,
                    'name'          => 'Standard Setup',
                    'slug'          => Str::slug($package->title) . '-standard',
                    'price'         => $tierPrice,
                    'description'   => $package->description ?: 'Complete setup including on-site decor, lighting, and coordination.',
                    'image'         => $package->image,
                    'inclusions'    => ['On-site Setup & Decor', 'LED Ambient Lighting', 'Standard Logistics Fee Included'],
                    'active'        => true,
                    'display_order' => 0,
                ]);
            } else {
                $standardTier->update([
                    'price'       => $tierPrice,
                    'image'       => $package->image ?: $standardTier->image,
                    'description' => $package->description ?: $standardTier->description,
                ]);
            }
        }

        // 6. Sync legacy packages.json snapshot (mirror for backwards-compatibility)
        try {
            $jsonPath = storage_path('app/packages.json');
            $packagesJson = [];
            if (File::exists($jsonPath)) {
                $packagesJson = json_decode(File::get($jsonPath), true) ?? [];
            }
            $catId = $package->category_id;
            $catTitle = $package->category ? $package->category->title : 'Category';

            $legacyTiers = [];
            foreach ($package->tiers as $t) {
                $legacyTiers[] = [
                    'name'           => $t->name,
                    'slug'           => $t->slug,
                    'price'          => (int)$t->price,
                    'original_price' => (int)($package->original_price ?: round($t->price * 1.2)),
                    'desc'           => $t->description,
                    'image'          => $t->image ?: $package->image,
                    'inclusions'     => $t->inclusions ?: [],
                    'badge'          => $package->badge ?: 'POPULAR',
                ];
            }

            $packagesJson[$catId] = [
                'id'      => (int)$catId,
                'title'   => $catTitle,
                'desc'    => $package->description ?: ('Custom packages for ' . $catTitle),
                'image'   => $package->image ?: 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?q=80&w=800',
                'tag'     => $package->tag ?: 'decor',
                'active'  => $package->active,
                'gallery' => $package->gallery ?: [],
                'tiers'   => $legacyTiers,
            ];
            JsonStorageService::write('packages.json', $packagesJson);
        } catch (\Throwable $e) {
            Log::warning('packages.json sync warning: ' . $e->getMessage());
        }

        CelebrationCatalogService::clearCache();

        return redirect()->route('admin.packages')->with('success', 'Package saved successfully!');
    }

    /**
     * Delete legacy package tier from JSON store.
     */
    public function deletePackage($catId, $tierIdx)
    {
        $path = storage_path('app/packages.json');
        if (File::exists($path)) {
            $packages = json_decode(File::get($path), true) ?? [];
            if (isset($packages[$catId]['tiers'][$tierIdx])) {
                array_splice($packages[$catId]['tiers'], $tierIdx, 1);
                File::put($path, json_encode($packages, JSON_PRETTY_PRINT));
                CelebrationCatalogService::clearCache();
            }
        }
        return redirect()->route('admin.packages')->with('success', 'Package tier deleted successfully!');
    }

    /**
     * Toggle active/draft status of a database package.
     */
    public function togglePackageStatus($id)
    {
        $package = Package::findOrFail($id);
        $package->active = !$package->active;
        $package->save();
        CelebrationCatalogService::clearCache();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'active'  => $package->active,
                'message' => 'Package status updated to ' . ($package->active ? 'Published' : 'Draft') . '.',
            ]);
        }

        return redirect()->back()->with('success', 'Package status updated.');
    }

    /**
     * Delete a database package and its tiers.
     */
    public function destroyPackage($id)
    {
        $package = Package::findOrFail($id);
        $title = $package->title;

        // Disassociate related bookings safely
        \App\Models\Booking::where('package_id', $package->id)->update(['package_id' => null]);

        $package->tiers()->delete();
        $package->subcategories()->detach();
        $package->delete();
        CelebrationCatalogService::clearCache();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Package '{$title}' and its tiers deleted successfully.",
            ]);
        }

        return redirect()->route('admin.packages')->with('success', "Package '{$title}' and its tiers deleted successfully.");
    }
}

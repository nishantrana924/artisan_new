<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Services\JsonStorageService;

class DashboardController extends Controller
{
    /**
     * Display the Admin Dashboard.
     */
    public function index(Request $request)
    {
        // Purge compiled views cache to ensure latest template edits render instantly
        foreach (glob(storage_path('framework/views/*.php')) as $cachedFile) {
            @unlink($cachedFile);
        }

        $defaultCms = [
            'hero_slides' => [
                [
                    'image' => '/assets/images/hero/1.jpg',
                    'badge' => 'PLAN, BOOK & RELAX',
                    'title' => 'ELEVATE YOUR CELEBRATIONS',
                    'desc' => 'Book complete premium decor, sound, and lighting setups instantly for birthdays, house parties, and sangeet.',
                    'link1' => '/events',
                    'btn1Text' => 'Explore Packages',
                    'link2' => 'https://wa.me/919131668156',
                    'btn2Text' => 'WhatsApp Inquiry'
                ],
                [
                    'image' => '/assets/images/hero/2.jpg',
                    'badge' => 'SOUNDS OF ARTIZEN',
                    'title' => 'HIGH-BASS PARTY RIGS',
                    'desc' => 'Professional active speaker columns, live DJs, strobes, and concert fog delivered directly to your venue.',
                    'link1' => '/events',
                    'btn1Text' => 'View DJ Setups',
                    'link2' => 'https://wa.me/919131668156',
                    'btn2Text' => 'Book Instant'
                ],
                [
                    'image' => '/assets/images/hero/3.jpg',
                    'badge' => 'ROMANTIC MEMORIES',
                    'title' => 'ROYAL STAGES & DECORS',
                    'desc' => 'Exquisite wedding mandaps, traditional haldi Urli setups, and premium anniversary flower panels.',
                    'link1' => '/events',
                    'btn1Text' => 'View Weddings',
                    'link2' => 'https://wa.me/919131668156',
                    'btn2Text' => 'Contact Planner'
                ]
            ],
            'about' => [
                'badge' => 'INSTANT BOOKING GUARANTEE',
                'title' => 'BOOKING CONFIRMED IN SECONDS',
                'desc' => 'We engineered the fastest event booking flow in the industry. Forget calling multiple decorators, negotiating sound rentals, or coordinating with DJs separately. With Artizen, choose your setup, book instantly, and relax. No lag, no hassles.',
                'cards' => [
                    [
                        'icon' => 'clock',
                        'title' => '24/7 SUPPORT',
                        'desc' => 'Our support team is live 24/7 to resolve booking queries instantly.'
                    ],
                    [
                        'icon' => 'lightning',
                        'title' => 'INSTANT SETUP',
                        'desc' => 'Seamless delivery and basic setups installed on-site within hours.'
                    ],
                    [
                        'icon' => 'shield',
                        'title' => 'SECURE GATEWAY',
                        'desc' => 'Cryptographically signed QR codes and secure SSL payment channels.'
                    ]
                ]
            ]
        ];

        $cms = JsonStorageService::read('cms.json', $defaultCms);
        $artists = JsonStorageService::read('artists.json');
        $categories = JsonStorageService::read('categories.json');
        $packages = JsonStorageService::read('packages.json');
        $bookings = JsonStorageService::read('bookings.json');
        $faqs = JsonStorageService::read('faqs.json');
        $settings = JsonStorageService::read('settings.json');

        // Compute Dynamic Real-Time Analytics & Reporting Metrics
        $analytics = $this->calculateAnalytics($bookings, $request);

        return view('admin.dashboard', compact('cms', 'artists', 'categories', 'packages', 'bookings', 'faqs', 'settings', 'analytics'));
    }

    /**
     * Compute dynamic analytics & reporting metrics from real JSON data.
     */
    protected function calculateAnalytics(array $bookings, Request $request): array
    {
        $timeFilter = $request->query('time_filter', 'all');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $filteredBookings = [];
        $todayTimestamp = strtotime('today');

        foreach ($bookings as $b) {
            $createdTime = !empty($b['created_at']) ? strtotime($b['created_at']) : (!empty($b['date']) ? strtotime($b['date']) : time());
            $bookingDate = !empty($b['date']) ? strtotime($b['date']) : $createdTime;

            $include = match($timeFilter) {
                'today' => date('Y-m-d', $createdTime) === date('Y-m-d') || date('Y-m-d', $bookingDate) === date('Y-m-d'),
                '7_days' => $createdTime >= strtotime('-7 days') || $bookingDate >= strtotime('-7 days'),
                '30_days' => $createdTime >= strtotime('-30 days') || $bookingDate >= strtotime('-30 days'),
                'this_month' => date('Y-m', $createdTime) === date('Y-m') || date('Y-m', $bookingDate) === date('Y-m'),
                'custom' => (empty($startDate) || $createdTime >= strtotime($startDate) || $bookingDate >= strtotime($startDate)) &&
                            (empty($endDate) || $createdTime <= strtotime($endDate . ' 23:59:59') || $bookingDate <= strtotime($endDate . ' 23:59:59')),
                default => true
            };

            if ($include) {
                $filteredBookings[] = $b;
            }
        }

        $totalBookings = count($filteredBookings);
        $pendingCount = 0;
        $contactedCount = 0;
        $confirmedCount = 0;
        $completedCount = 0;
        $cancelledCount = 0;

        $pipelineValue = 0;
        $confirmedRevenue = 0;
        $nonCancelledCount = 0;
        $nonCancelledSum = 0;
        $upcomingCount = 0;

        $categoryStats = [];
        $packageStats = [];
        $upcomingEventsList = [];

        foreach ($filteredBookings as $b) {
            $status = $b['status'] ?? 'Pending';
            $price = (int)($b['total'] ?? $b['package_price'] ?? 0);
            $bDate = !empty($b['date']) ? strtotime($b['date']) : 0;
            $catName = $b['category'] ?? 'Event Setup';
            $pkgName = $b['package'] ?? 'Custom Package';

            if (strcasecmp($status, 'Pending') === 0) {
                $pendingCount++;
                $pipelineValue += $price;
                $nonCancelledCount++;
                $nonCancelledSum += $price;
            } elseif (strcasecmp($status, 'Contacted') === 0) {
                $contactedCount++;
                $pipelineValue += $price;
                $nonCancelledCount++;
                $nonCancelledSum += $price;
            } elseif (strcasecmp($status, 'Confirmed') === 0) {
                $confirmedCount++;
                $pipelineValue += $price;
                $confirmedRevenue += $price;
                $nonCancelledCount++;
                $nonCancelledSum += $price;
            } elseif (strcasecmp($status, 'Completed') === 0) {
                $completedCount++;
                $confirmedRevenue += $price;
                $nonCancelledCount++;
                $nonCancelledSum += $price;
            } elseif (strcasecmp($status, 'Cancelled') === 0) {
                $cancelledCount++;
            } else {
                $pendingCount++;
                $pipelineValue += $price;
                $nonCancelledCount++;
                $nonCancelledSum += $price;
            }

            if (strcasecmp($status, 'Cancelled') !== 0 && $bDate >= $todayTimestamp) {
                $upcomingCount++;
                $upcomingEventsList[] = $b;
            }

            // Group Category Stats (excluding cancelled)
            if (strcasecmp($status, 'Cancelled') !== 0) {
                if (!isset($categoryStats[$catName])) {
                    $categoryStats[$catName] = ['count' => 0, 'revenue' => 0];
                }
                $categoryStats[$catName]['count']++;
                $categoryStats[$catName]['revenue'] += $price;

                // Group Package Stats (excluding cancelled)
                if (!isset($packageStats[$pkgName])) {
                    $packageStats[$pkgName] = ['count' => 0, 'revenue' => 0];
                }
                $packageStats[$pkgName]['count']++;
                $packageStats[$pkgName]['revenue'] += $price;
            }
        }

        // Average Booking Value: valid non-cancelled bookings sum / count
        $avgBookingValue = $nonCancelledCount > 0 ? (int)round($nonCancelledSum / $nonCancelledCount) : 0;

        // Sort Top 5 Packages by count descending
        uasort($packageStats, fn($a, $b) => $b['count'] <=> $a['count']);
        $topPackages = array_slice($packageStats, 0, 5, true);

        // Sort Upcoming Events by date ascending
        usort($upcomingEventsList, fn($a, $b) => strtotime($a['date'] ?? '') <=> strtotime($b['date'] ?? ''));
        $upcomingEventsList = array_slice($upcomingEventsList, 0, 10);

        // Recent Activity sorted by creation timestamp descending
        $recentActivity = $filteredBookings;
        usort($recentActivity, fn($a, $b) => strtotime($b['created_at'] ?? $b['date'] ?? '') <=> strtotime($a['created_at'] ?? $a['date'] ?? ''));
        $recentActivity = array_slice($recentActivity, 0, 8);

        return [
            'time_filter' => $timeFilter,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_bookings' => $totalBookings,
            'pending_count' => $pendingCount,
            'contacted_count' => $contactedCount,
            'confirmed_count' => $confirmedCount,
            'completed_count' => $completedCount,
            'cancelled_count' => $cancelledCount,
            'pipeline_value' => $pipelineValue,
            'confirmed_revenue' => $confirmedRevenue,
            'avg_booking_value' => $avgBookingValue,
            'upcoming_count' => $upcomingCount,
            'category_stats' => $categoryStats,
            'top_packages' => $topPackages,
            'upcoming_events' => $upcomingEventsList,
            'recent_activity' => $recentActivity,
        ];
    }

    /**
     * Save dynamic CMS settings.
     */
    public function saveCms(Request $request)
    {
        $cmsPath = storage_path('app/cms.json');
        
        // Read current CMS layout to preserve existing image paths if no new image is uploaded
        $currentCms = [];
        if (File::exists($cmsPath)) {
            $currentCms = json_decode(File::get($cmsPath), true);
        }

        $heroSlidesInput = $request->input('hero_slides', []);
        $heroSlides = [];

        foreach ($heroSlidesInput as $idx => $slideData) {
            // Check if a new file upload is present for this slide
            if ($request->hasFile("hero_slides.{$idx}.image_file")) {
                $file = $request->file("hero_slides.{$idx}.image_file");
                $filename = 'hero_' . $idx . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Ensure the public uploads directory exists
                if (!File::exists(public_path('uploads'))) {
                    File::makeDirectory(public_path('uploads'), 0755, true);
                }
                
                $file->move(public_path('uploads'), $filename);
                $slideData['image'] = '/uploads/' . $filename;
            } else {
                // Preserve existing image if available
                $slideData['image'] = $slideData['image'] ?? ($currentCms['hero_slides'][$idx]['image'] ?? '/assets/images/hero/1.jpg');
            }
            unset($slideData['image_file']);
            $heroSlides[] = $slideData;
        }

        $aboutInput = $request->input('about', []);
        
        // Handle About Gallery Images uploads
        $aboutImagesInput = $request->input('about.images', []);
        $aboutImages = [];

        foreach ($aboutImagesInput as $idx => $imgData) {
            // Check if file upload is present for this gallery item
            if ($request->hasFile("about.images.{$idx}.image_file")) {
                $file = $request->file("about.images.{$idx}.image_file");
                $filename = 'about_gallery_' . $idx . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                if (!File::exists(public_path('uploads'))) {
                    File::makeDirectory(public_path('uploads'), 0755, true);
                }
                
                $file->move(public_path('uploads'), $filename);
                $imgData['image'] = '/uploads/' . $filename;
            } else {
                // Keep the current hidden input image path
                $imgData['image'] = $imgData['image'] ?? ($currentCms['about']['images'][$idx] ?? '/assets/images/hero/1.jpg');
            }
            $aboutImages[] = $imgData['image'];
        }

        // If no images uploaded, fallback to current or default
        if (empty($aboutImages)) {
            $aboutImages = $currentCms['about']['images'] ?? [ $currentCms['about']['image'] ?? '/assets/images/hero/1.jpg' ];
        }

        // Re-index cards array to remove any deleted card index gaps
        $aboutCards = [];
        if (isset($aboutInput['cards']) && is_array($aboutInput['cards'])) {
            $aboutCards = array_values($aboutInput['cards']);
        } else {
            $aboutCards = $currentCms['about']['cards'] ?? [];
        }
        
        $about = [
            'badge' => $aboutInput['badge'] ?? ($currentCms['about']['badge'] ?? 'INSTANT BOOKING GUARANTEE'),
            'title' => $aboutInput['title'] ?? ($currentCms['about']['title'] ?? 'BOOKING CONFIRMED IN SECONDS'),
            'desc' => $aboutInput['desc'] ?? ($currentCms['about']['desc'] ?? ''),
            'image' => $aboutImages[0] ?? '/assets/images/hero/1.jpg', // Keep for backward compatibility
            'images' => $aboutImages,
            'cards' => $aboutCards
        ];

        $cms = [
            'hero_slides' => $heroSlides,
            'about' => $about
        ];

        JsonStorageService::write('cms.json', $cms);
        return redirect()->route('admin.dashboard')->with('success', 'CMS Content saved successfully!');
    }

    /**
     * Save/Create an Artist Profile.
     */
    public function saveArtist(Request $request)
    {
        $artistsPath = storage_path('app/artists.json');
        $artists = [];
        if (File::exists($artistsPath)) {
            $artists = json_decode(File::get($artistsPath), true) ?? [];
        }

        $id = $request->input('id');
        $artistData = [];

        // If ID exists, find and update
        $existingIndex = -1;
        if ($id) {
            foreach ($artists as $index => $art) {
                if ($art['id'] == $id) {
                    $existingIndex = $index;
                    $artistData = $art;
                    break;
                }
            }
        }

        // Assign basic details
        $artistData['name'] = $request->input('name');
        $artistData['category'] = $request->input('category', 'DJ');
        $artistData['price'] = $request->input('price', '₹10,000 onwards');
        $artistData['experience'] = $request->input('experience', '5+ Years');
        $artistData['rating'] = $request->input('rating', '4.9');
        $artistData['description'] = $request->input('description', '');
        $artistData['active'] = $request->has('active');
        $artistData['show_on_homepage'] = $request->has('show_on_homepage');
        $artistData['visible_location'] = $request->input('visible_location', 'all');

        // Handle Packages linked
        $packagesInput = $request->input('packages', []);
        $artistData['packages'] = array_map('intval', $packagesInput);

        // Handle Profile Image upload
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'artist_' . time() . '.' . $file->getClientOriginalExtension();
            
            if (!File::exists(public_path('uploads'))) {
                File::makeDirectory(public_path('uploads'), 0755, true);
            }
            
            $file->move(public_path('uploads'), $filename);
            $artistData['image'] = '/uploads/' . $filename;
        } else {
            $artistData['image'] = $request->input('image', $artistData['image'] ?? '/assets/images/hero/2.jpg');
        }

        if ($existingIndex !== -1) {
            $artists[$existingIndex] = $artistData;
        } else {
            // Create a new unique ID
            $newId = 1;
            if (!empty($artists)) {
                $ids = array_column($artists, 'id');
                $newId = max($ids) + 1;
            }
            $artistData['id'] = $newId;
            $artists[] = $artistData;
        }

        File::put($artistsPath, json_encode($artists, JSON_PRETTY_PRINT));

        return redirect()->route('admin.dashboard')->with('success', 'Artist profile successfully saved!');
    }

    /**
     * Delete an Artist Profile.
     */
    public function deleteArtist($id)
    {
        $artistsPath = storage_path('app/artists.json');
        if (File::exists($artistsPath)) {
            $artists = json_decode(File::get($artistsPath), true) ?? [];
            $filteredArtists = array_filter($artists, function($art) use ($id) {
                return $art['id'] != $id;
            });
            // Re-index array
            $filteredArtists = array_values($filteredArtists);
            File::put($artistsPath, json_encode($filteredArtists, JSON_PRETTY_PRINT));
        }

        return redirect()->route('admin.dashboard')->with('success', 'Artist profile successfully deleted!');
    }

    /**
     * Save dynamic categories.
     */
    public function saveCategory(Request $request)
    {
        $path = storage_path('app/categories.json');
        $categories = [];
        if (File::exists($path)) {
            $categories = json_decode(File::get($path), true) ?? [];
        }

        $id = $request->input('id');
        $catData = [];

        $existingIndex = -1;
        if ($id) {
            foreach ($categories as $index => $c) {
                if ($c['id'] == $id) {
                    $existingIndex = $index;
                    $catData = $c;
                    break;
                }
            }
        }

        $catData['title'] = $request->input('title');
        $catData['active'] = $request->has('active');

        // Handle Category image upload
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                return redirect()->back()->with('error', 'Unsafe file format rejected. Only JPG, PNG, WEBP, and GIF images are allowed.');
            }
            $filename = 'category_' . time() . '_' . rand(100, 999) . '.' . $ext;
            if (!File::exists(public_path('uploads'))) {
                File::makeDirectory(public_path('uploads'), 0755, true);
            }
            $file->move(public_path('uploads'), $filename);
            $catData['image'] = '/uploads/' . $filename;
        } else {
            $catData['image'] = $request->input('image', $catData['image'] ?? 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?q=80&w=300&auto=format&fit=crop');
        }

        if ($existingIndex !== -1) {
            $categories[$existingIndex] = $catData;
        } else {
            $newId = 1;
            if (!empty($categories)) {
                $newId = max(array_column($categories, 'id')) + 1;
            }
            $catData['id'] = $newId;
            $catData['count'] = 0;
            $categories[] = $catData;
        }

        JsonStorageService::write('categories.json', $categories);
        return redirect()->route('admin.dashboard')->with('success', 'Category saved successfully!');
    }

    /**
     * Render the create package page.
     */
    public function createPackagePage()
    {
        // Purge stale compiled Blade views to ensure fresh layout rendering
        foreach (glob(storage_path('framework/views/*.php')) as $cachedFile) {
            if (is_file($cachedFile)) {
                @unlink($cachedFile);
            }
        }

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
                    'trending' => false
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
                    'crew_count' => '3 Members'
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
                    'meta_keywords' => ''
                ],
                'availability' => [
                    'lead_time_hours' => 24,
                    'max_daily_bookings' => 3
                ],
                'service_area' => [
                    'city' => 'Indore',
                    'outstation_allowed' => true,
                    'outstation_charge' => 1500
                ],
                'status' => 'published',
                'desc' => '',
                'inclusions' => [],
                'image' => '/assets/images/hero/1.jpg'
            ]
        ]);
    }

    /**
     * Render the edit package page.
     */
    public function editPackagePage($catId, $tierIdx)
    {
        // Purge stale compiled Blade views to ensure fresh layout rendering
        foreach (glob(storage_path('framework/views/*.php')) as $cachedFile) {
            if (is_file($cachedFile)) {
                @unlink($cachedFile);
            }
        }

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
            return redirect()->route('admin.dashboard')->with('error', 'Package tier plan not found!');
        }

        // Apply defaults for Phase 1 basic information fields if not set
        $tier['slug'] = $tier['slug'] ?? \Illuminate\Support\Str::slug($tier['name'] ?? '');
        $tier['badge'] = $tier['badge'] ?? 'PREMIUM PACKAGE';
        $tier['sub_category'] = $tier['sub_category'] ?? 'decor';
        $tier['display_order'] = $tier['display_order'] ?? 1;
        $tier['switches'] = $tier['switches'] ?? [
            'featured' => false,
            'recommended' => false,
            'popular' => false,
            'best_seller' => false,
            'trending' => false
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
            'crew_count' => '3 Members'
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
            'meta_keywords' => 'event decoration indore, birthday package'
        ], $tier['seo'] ?? []);
        $tier['availability'] = array_merge([
            'lead_time_hours' => 24,
            'max_daily_bookings' => 3
        ], $tier['availability'] ?? []);
        $tier['service_area'] = array_merge([
            'city' => 'Indore',
            'outstation_allowed' => true,
            'outstation_charge' => 1500
        ], $tier['service_area'] ?? []);
        $tier['status'] = $tier['status'] ?? 'published';

        // Ensure gallery array has at least 4 items for UI preview
        $gallery = $tier['gallery'] ?? [];
        if (!is_array($gallery)) $gallery = [$tier['image'] ?? '/assets/images/hero/1.jpg'];
        while (count($gallery) < 4) {
            $gallery[] = $tier['image'] ?? '/assets/images/hero/1.jpg';
        }
        $tier['gallery'] = array_slice($gallery, 0, 4);

        return view('admin.packages.edit', [
            'categories' => $categories,
            'isEdit' => true,
            'catId' => $catId,
            'tierIdx' => $tierIdx,
            'tier' => $tier
        ]);
    }

    /**
     * Save/Create a tiered package under a category.
     */
    public function savePackage(Request $request)
    {
        $path = storage_path('app/packages.json');
        $packages = [];
        if (File::exists($path)) {
            $packages = json_decode(File::get($path), true) ?? [];
        }

        $catId = $request->input('category_id');
        
        if (!isset($packages[$catId])) {
            $categoriesPath = storage_path('app/categories.json');
            $categories = json_decode(File::get($categoriesPath), true) ?? [];
            $catTitle = 'Category';
            foreach ($categories as $c) {
                if ($c['id'] == $catId) {
                    $catTitle = $c['title'];
                    break;
                }
            }

            $packages[$catId] = [
                'id' => (int)$catId,
                'title' => $catTitle,
                'desc' => 'Custom packages for ' . $catTitle,
                'image' => 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?q=80&w=800&auto=format&fit=crop',
                'tag' => 'decor',
                'active' => true,
                'gallery' => [],
                'tiers' => []
            ];
        }

        $tierIndex = $request->input('tier_index');
        $name = $request->input('name');
        $slug = $request->input('slug') ? \Illuminate\Support\Str::slug($request->input('slug')) : \Illuminate\Support\Str::slug($name);
        
        $price = (int)$request->input('price', 0);
        $originalPrice = (int)$request->input('original_price', $price);
        $discountPct = ($originalPrice > 0 && $price < $originalPrice) ? (int)round((($originalPrice - $price) / $originalPrice) * 100) : 0;

        $existingTier = (is_numeric($tierIndex) && isset($packages[$catId]['tiers'][$tierIndex])) ? $packages[$catId]['tiers'][$tierIndex] : [];

        $highlightsInput = $request->input('highlights', []);
        $cleanHighlights = array_values(array_filter(array_map('trim', (array)$highlightsInput)));
        $specsInput = $request->input('specifications', []);

        // Process Addons
        $addonsRaw = $request->input('addons', []);
        $cleanAddons = [];
        if (is_array($addonsRaw)) {
            foreach ($addonsRaw as $item) {
                if (!empty($item['title'])) {
                    $cleanAddons[] = [
                        'title' => trim($item['title']),
                        'price' => (int)($item['price'] ?? 0),
                        'desc' => trim($item['desc'] ?? ''),
                        'icon' => trim($item['icon'] ?? 'fa-solid fa-fire')
                    ];
                }
            }
        }

        // Process FAQs
        $faqsRaw = $request->input('faqs', []);
        $cleanFaqs = [];
        if (is_array($faqsRaw)) {
            foreach ($faqsRaw as $item) {
                if (!empty($item['question'])) {
                    $cleanFaqs[] = [
                        'question' => trim($item['question']),
                        'answer' => trim($item['answer'] ?? '')
                    ];
                }
            }
        }

        // Process Exclusions
        $exclusionsRaw = $request->input('exclusions', '');
        $cleanExclusions = [];
        if (is_array($exclusionsRaw)) {
            $cleanExclusions = array_values(array_filter(array_map('trim', $exclusionsRaw)));
        } else {
            if (strpos($exclusionsRaw, '<li') !== false) {
                preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $exclusionsRaw, $matches);
                $cleanExclusions = array_values(array_filter(array_map('trim', array_map('strip_tags', $matches[1] ?? []))));
            } else {
                $cleanExclusions = array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", "", strip_tags($exclusionsRaw))))));
            }
        }

        // Process SEO Input
        $seoInput = $request->input('seo', []);

        // Process Availability Input
        $availInput = $request->input('availability', []);

        // Process Service Area Input
        $areaInput = $request->input('service_area', []);

        // Process PDF Brochure Upload
        $pdfPath = $request->input('pdf_brochure', $existingTier['pdf_brochure'] ?? '');
        if ($request->hasFile('pdf_brochure_file')) {
            $pdfFile = $request->file('pdf_brochure_file');
            $pdfName = 'brochure_' . time() . '.' . $pdfFile->getClientOriginalExtension();
            if (!File::exists(public_path('uploads'))) {
                File::makeDirectory(public_path('uploads'), 0755, true);
            }
            $pdfFile->move(public_path('uploads'), $pdfName);
            $pdfPath = '/uploads/' . $pdfName;
        }

        $tierData = array_merge($existingTier, [
            'name' => $name,
            'slug' => $slug,
            'badge' => $request->input('badge', 'PREMIUM PACKAGE'),
            'icon' => $request->input('icon', 'fa-solid fa-crown'),
            'sub_category' => $request->input('sub_category', 'decor'),
            'display_order' => (int)$request->input('display_order', 1),
            'switches' => [
                'featured' => $request->has('switches.featured'),
                'recommended' => $request->has('switches.recommended'),
                'popular' => $request->has('switches.popular'),
                'best_seller' => $request->has('switches.best_seller'),
                'trending' => $request->has('switches.trending'),
            ],
            'switch_icons' => $request->input('switch_icons', []),
            'custom_badges' => (function() use ($request) {
                $customBadgesRaw = $request->input('custom_badges', []);
                $clean = [];
                if (is_array($customBadgesRaw)) {
                    foreach ($customBadgesRaw as $cb) {
                        if (!empty($cb['label']) && !empty($cb['active'])) {
                            $clean[] = [
                                'label' => trim($cb['label']),
                                'icon' => trim($cb['icon'] ?? 'fa-solid fa-tag'),
                                'active' => true
                            ];
                        }
                    }
                }
                return $clean;
            })(),
            'internal_notes' => $request->input('internal_notes', ''),
            'price' => $price,
            'original_price' => $originalPrice,
            'discount_pct' => $discountPct,
            'advance_deposit' => (int)$request->input('advance_deposit', 0),
            'setup_charge' => (int)$request->input('setup_charge', 0),
            'travel_charge' => (int)$request->input('travel_charge', 0),
            'extra_guest_rate' => (int)$request->input('extra_guest_rate', 0),
            'gst_pct' => (int)$request->input('gst_pct', 18),
            'gst_inclusive' => $request->has('gst_inclusive'),
            'short_desc' => $request->input('short_desc', ''),
            'detailed_desc' => $request->input('detailed_desc', $request->input('desc', '')),
            'highlights' => $cleanHighlights,
            'important_notes' => $request->input('important_notes', ''),
            'terms' => $request->input('terms', ''),
            'specifications' => [
                'guest_capacity' => $specsInput['guest_capacity'] ?? '50 - 100 Guests',
                'setup_time' => $specsInput['setup_time'] ?? '3-4 Hours',
                'duration' => $specsInput['duration'] ?? '4-5 Hours',
                'space_req' => $specsInput['space_req'] ?? '12ft x 8ft Stage',
                'location_type' => $specsInput['location_type'] ?? 'Indoor & Outdoor',
                'power_req' => $specsInput['power_req'] ?? '220V 15A Power Socket',
                'crew_count' => $specsInput['crew_count'] ?? '3 Members'
            ],
            'video_url' => $request->input('video_url', ''),
            'virtual_tour_url' => $request->input('virtual_tour_url', ''),
            'pdf_brochure' => $pdfPath,
            'exclusions' => $cleanExclusions,
            'addons' => $cleanAddons,
            'faqs' => $cleanFaqs,
            'seo' => [
                'meta_title' => $seoInput['meta_title'] ?? $name,
                'meta_description' => $seoInput['meta_description'] ?? $request->input('short_desc', ''),
                'meta_keywords' => $seoInput['meta_keywords'] ?? 'event package'
            ],
            'availability' => [
                'lead_time_hours' => (int)($availInput['lead_time_hours'] ?? 24),
                'max_daily_bookings' => (int)($availInput['max_daily_bookings'] ?? 3)
            ],
            'service_area' => [
                'city' => $areaInput['city'] ?? 'Indore',
                'outstation_allowed' => isset($areaInput['outstation_allowed']),
                'outstation_charge' => (int)($areaInput['outstation_charge'] ?? 1500)
            ],
            'status' => $request->input('status', 'published'),
            'vendor_partner' => $request->input('vendor_partner', 'Artizen Internal Team'),
            'inventory_sku' => $request->input('inventory_sku', 'ART-DEC-01'),
            'vendor_cost' => (int)$request->input('vendor_cost', 0),
            'profit_margin_pct' => (int)$request->input('profit_margin_pct', 35),
            'props_checklist' => $request->input('props_checklist', ''),
            'featured_rating' => (float)$request->input('featured_rating', 4.9),
            'seasonal_tag' => $request->input('seasonal_tag', 'TRENDING NOW'),
            'whatsapp_template' => $request->input('whatsapp_template', 'ARTIZEN_BOOKING_CONFIRM'),
            'safety_guidelines' => $request->input('safety_guidelines', ''),
            'inclusions' => (function() use ($request) {
                $raw = $request->input('inclusions', '');
                if (strpos($raw, '<li') !== false) {
                    preg_match_all('/<li>(.*?)<\/li>/i', $raw, $matches);
                    return array_values(array_filter(array_map(function($val) {
                        return trim(html_entity_decode(strip_tags($val)));
                    }, $matches[1])));
                }
                return array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", "", $raw)))));
            })()
        ]);

        // Process Main Cover Image
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'package_tier_' . time() . '.' . $file->getClientOriginalExtension();
            if (!File::exists(public_path('uploads'))) {
                File::makeDirectory(public_path('uploads'), 0755, true);
            }
            $file->move(public_path('uploads'), $filename);
            $tierData['image'] = '/uploads/' . $filename;
        } else {
            $tierData['image'] = $request->input('image', $existingTier['image'] ?? '/assets/images/hero/1.jpg');
        }

        // Process 4 Multiple Gallery Banners
        $galleryImages = [];
        $galleryInputs = $request->input('gallery', []);
        $galleryFiles = $request->file('gallery_files', []);

        for ($i = 0; $i < 4; $i++) {
            if (isset($galleryFiles[$i]) && $galleryFiles[$i]->isValid()) {
                $file = $galleryFiles[$i];
                $filename = 'package_gal_' . $i . '_' . time() . '.' . $file->getClientOriginalExtension();
                if (!File::exists(public_path('uploads'))) {
                    File::makeDirectory(public_path('uploads'), 0755, true);
                }
                $file->move(public_path('uploads'), $filename);
                $galleryImages[] = '/uploads/' . $filename;
            } elseif (!empty($galleryInputs[$i])) {
                $galleryImages[] = $galleryInputs[$i];
            } else {
                $galleryImages[] = $tierData['image'];
            }
        }
        $tierData['gallery'] = array_values(array_filter($galleryImages));

        if (is_numeric($tierIndex) && isset($packages[$catId]['tiers'][$tierIndex])) {
            $packages[$catId]['tiers'][$tierIndex] = $tierData;
        } else {
            $packages[$catId]['tiers'][] = $tierData;
        }

        JsonStorageService::write('packages.json', $packages);
        return redirect()->route('admin.dashboard')->with('success', 'Package tier saved successfully!');
    }

    /**
     * Save dynamic FAQs.
     */
    public function saveFaq(Request $request)
    {
        $faqs = JsonStorageService::read('faqs.json');

        $id = $request->input('id');
        $faqData = [
            'q' => $request->input('q'),
            'a' => $request->input('a')
        ];

        $existingIndex = -1;
        if ($id) {
            foreach ($faqs as $index => $f) {
                if ($f['id'] == $id) {
                    $existingIndex = $index;
                    break;
                }
            }
        }

        if ($existingIndex !== -1) {
            $faqs[$existingIndex]['q'] = $faqData['q'];
            $faqs[$existingIndex]['a'] = $faqData['a'];
        } else {
            $newId = 1;
            if (!empty($faqs)) {
                $newId = max(array_column($faqs, 'id')) + 1;
            }
            $faqData['id'] = $newId;
            $faqs[] = $faqData;
        }

        JsonStorageService::write('faqs.json', $faqs);
        return redirect()->route('admin.dashboard')->with('success', 'FAQ saved successfully!');
    }

    /**
     * Delete dynamic FAQ.
     */
    public function deleteFaq($id)
    {
        $faqs = JsonStorageService::read('faqs.json');
        $faqs = array_values(array_filter($faqs, function($f) use ($id) {
            return $f['id'] != $id;
        }));
        JsonStorageService::write('faqs.json', $faqs);
        return redirect()->route('admin.dashboard')->with('success', 'FAQ deleted successfully!');
    }

    /**
     * Update booking status with atomic JSON writes and notification compilation.
     */
    public function updateBookingStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');

        if (empty($id) || empty($status)) {
            return response()->json(['success' => false, 'message' => 'Invalid parameters'], 422);
        }

        $bookings = \App\Services\JsonStorageService::read('bookings.json');
        $updatedBooking = null;

        foreach ($bookings as &$b) {
            if (($b['id'] ?? '') === $id) {
                $previousStatus = $b['status'] ?? 'Pending';
                $b['status'] = $status;
                $b['updated_at'] = date('d M Y, h:i A');

                $sentList = $b['notifications_sent'] ?? [];

                // Duplicate Notification Protection: Only send if status actually changed or notification was not sent yet
                if ($previousStatus !== $status || !in_array(strtolower($status), $sentList)) {
                    try {
                        if (!empty($b['email'])) {
                            if (strcasecmp($status, 'Confirmed') === 0) {
                                \Illuminate\Support\Facades\Notification::route('mail', $b['email'])
                                    ->notify(new \App\Notifications\BookingConfirmedNotification($b));
                                $sentList[] = 'confirmed';
                            } else if (strcasecmp($status, 'Cancelled') === 0) {
                                \Illuminate\Support\Facades\Notification::route('mail', $b['email'])
                                    ->notify(new \App\Notifications\BookingCancelledNotification($b));
                                $sentList[] = 'cancelled';
                            }
                        }
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error('Status update mail notification error: ' . $e->getMessage());
                    }

                    try {
                        $waSmsService = new \App\Services\WhatsAppSmsService();
                        if (strcasecmp($status, 'Confirmed') === 0 && !in_array('confirmed_wa', $sentList)) {
                            $waSmsService->sendCustomerBookingConfirmed($b);
                            $sentList[] = 'confirmed_wa';
                        } else if (strcasecmp($status, 'Cancelled') === 0 && !in_array('cancelled_wa', $sentList)) {
                            $waSmsService->sendCustomerBookingCancelled($b);
                            $sentList[] = 'cancelled_wa';
                        }
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error('Status update WhatsApp/SMS notification error: ' . $e->getMessage());
                    }
                }

                $b['notifications_sent'] = array_unique($sentList);
                $updatedBooking = $b;
                break;
            }
        }

        if ($updatedBooking) {
            \App\Services\JsonStorageService::write('bookings.json', $bookings);

            // Compile customer notification WhatsApp message link
            $custPhone = preg_replace('/[^0-9]/', '', $updatedBooking['whatsapp'] ?? $updatedBooking['mobile'] ?? '');
            if (!str_starts_with($custPhone, '91') && strlen($custPhone) === 10) {
                $custPhone = '91' . $custPhone;
            }

            $imgUrl = $updatedBooking['image'] ?? '/assets/images/hero/1.jpg';
            if (!str_starts_with($imgUrl, 'http')) {
                $imgUrl = url($imgUrl);
            }

            $msg = "";
            if (strcasecmp($status, 'Confirmed') === 0) {
                $msg = "Hello " . ($updatedBooking['name'] ?? 'Customer') . "! Your Artizen event booking request (" . $id . ") for " . ($updatedBooking['package'] ?? 'Setup') . " on " . ($updatedBooking['date'] ?? '') . " has been CONFIRMED. Our team will reach your venue on time.\n\n📷 Setup Photo: " . $imgUrl;
            } else if (strcasecmp($status, 'Cancelled') === 0) {
                $msg = "Hello " . ($updatedBooking['name'] ?? 'Customer') . ". Your Artizen event booking request (" . $id . ") has been CANCELLED. Please contact us on +91 9131668156 for assistance.";
            } else {
                $msg = "Hello " . ($updatedBooking['name'] ?? 'Customer') . "! Your Artizen booking (" . $id . ") status is now " . strtoupper($status) . ".\n\n📷 Setup Photo: " . $imgUrl;
            }

            $notifyUrl = !empty($custPhone) ? ("https://wa.me/" . $custPhone . "?text=" . urlencode($msg)) : null;

            return response()->json([
                'success' => true,
                'status' => $status,
                'notify_url' => $notifyUrl,
                'message' => 'Booking ' . $id . ' status updated to ' . $status
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Booking ID not found'], 404);
    }

    /**
     * Save dynamic meta and delivery settings.
     */
    public function saveSettings(Request $request)
    {
        $path = storage_path('app/settings.json');
        $settings = [];
        if (File::exists($path)) {
            $settings = json_decode(File::get($path), true) ?? [];
        }

        $settings['meta_title'] = $request->input('meta_title');
        $settings['meta_description'] = $request->input('meta_description');
        $settings['meta_keywords'] = $request->input('meta_keywords');

        $surcharges = $request->input('surcharges', []);
        foreach ($surcharges as $zone => $val) {
            $settings['surcharges'][$zone] = (int)str_replace('₹', '', $val);
        }

        File::put($path, json_encode($settings, JSON_PRETTY_PRINT));
        return redirect()->route('admin.dashboard')->with('success', 'Settings saved successfully!');
    }

    /**
     * Delete a package tier.
     */
    public function deletePackage($catId, $tierIdx)
    {
        $path = storage_path('app/packages.json');
        if (File::exists($path)) {
            $packages = json_decode(File::get($path), true) ?? [];
            if (isset($packages[$catId]['tiers'][$tierIdx])) {
                // Remove tier from index
                array_splice($packages[$catId]['tiers'], $tierIdx, 1);
                File::put($path, json_encode($packages, JSON_PRETTY_PRINT));
            }
        }
        return redirect()->route('admin.dashboard')->with('success', 'Package tier plan successfully deleted!');
    }

    /**
     * Delete Category.
     */
    public function deleteCategory($id)
    {
        $cats = JsonStorageService::read('categories.json', []);
        $updatedCats = array_values(array_filter($cats, fn($c) => (string)($c['id'] ?? '') !== (string)$id));
        JsonStorageService::write('categories.json', $updatedCats);

        return redirect()->route('admin.dashboard')->with('success', 'Category successfully deleted!');
    }

    /**
     * Save/Update Testimonial.
     */
    public function saveTestimonial(Request $request)
    {
        $request->validate([
            'author' => 'required|string|max:255',
            'review' => 'required|string|max:2000',
            'rating' => 'nullable|integer|min:1|max:5',
            'event_type' => 'nullable|string|max:255'
        ]);

        $testimonials = JsonStorageService::read('testimonials.json', []);
        $id = $request->input('id');

        if (empty($id)) {
            $id = 'TST-' . rand(1000, 9999);
            $testimonial = [
                'id' => $id,
                'author' => trim($request->input('author')),
                'location' => trim($request->input('location', 'Vijay Nagar, Indore')),
                'event_type' => trim($request->input('event_type', 'Birthday Event')),
                'review' => trim($request->input('review')),
                'rating' => (int)$request->input('rating', 5),
                'avatar' => trim($request->input('avatar', '/assets/images/reviews/1.jpg')),
                'created_at' => date('d M Y')
            ];
            $testimonials[] = $testimonial;
        } else {
            foreach ($testimonials as &$t) {
                if (($t['id'] ?? '') === $id) {
                    $t['author'] = trim($request->input('author'));
                    $t['location'] = trim($request->input('location', $t['location'] ?? 'Indore'));
                    $t['event_type'] = trim($request->input('event_type', $t['event_type'] ?? 'Celebration'));
                    $t['review'] = trim($request->input('review'));
                    $t['rating'] = (int)$request->input('rating', 5);
                    if ($request->filled('avatar')) {
                        $t['avatar'] = trim($request->input('avatar'));
                    }
                    break;
                }
            }
        }

        JsonStorageService::write('testimonials.json', $testimonials);
        return redirect()->route('admin.dashboard')->with('success', 'Testimonial successfully saved!');
    }

    /**
     * Delete Testimonial.
     */
    public function deleteTestimonial($id)
    {
        $testimonials = JsonStorageService::read('testimonials.json', []);
        $updated = array_values(array_filter($testimonials, fn($t) => ($t['id'] ?? '') !== $id));
        JsonStorageService::write('testimonials.json', $updated);

        return redirect()->route('admin.dashboard')->with('success', 'Testimonial successfully deleted!');
    }

    /**
     * Save/Update Gallery Item.
     */
    public function saveGalleryItem(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255'
        ]);

        $gallery = JsonStorageService::read('gallery.json', []);
        $id = $request->input('id');

        $imagePath = $request->input('image', '/assets/images/hero/1.jpg');
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'gallery_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            if (!File::exists(public_path('uploads'))) {
                File::makeDirectory(public_path('uploads'), 0755, true);
            }
            $file->move(public_path('uploads'), $filename);
            $imagePath = '/uploads/' . $filename;
        }

        if (empty($id)) {
            $id = 'GAL-' . rand(1000, 9999);
            $item = [
                'id' => $id,
                'title' => trim($request->input('title')),
                'category' => trim($request->input('category')),
                'image' => $imagePath,
                'active' => (bool)$request->input('active', true),
                'created_at' => date('d M Y')
            ];
            $gallery[] = $item;
        } else {
            foreach ($gallery as &$g) {
                if (($g['id'] ?? '') === $id) {
                    $g['title'] = trim($request->input('title'));
                    $g['category'] = trim($request->input('category'));
                    $g['image'] = $imagePath;
                    $g['active'] = (bool)$request->input('active', true);
                    break;
                }
            }
        }

        JsonStorageService::write('gallery.json', $gallery);
        return redirect()->route('admin.dashboard')->with('success', 'Gallery item successfully saved!');
    }

    /**
     * Delete Gallery Item.
     */
    public function deleteGalleryItem($id)
    {
        $gallery = JsonStorageService::read('gallery.json', []);
        $updated = array_values(array_filter($gallery, fn($g) => ($g['id'] ?? '') !== $id));
        JsonStorageService::write('gallery.json', $updated);

        return redirect()->route('admin.dashboard')->with('success', 'Gallery item successfully deleted!');
    }

    /**
     * Delete Contact Enquiry.
     */
    public function deleteEnquiry($id)
    {
        $enquiries = JsonStorageService::read('enquiries.json', []);
        $updated = array_values(array_filter($enquiries, fn($e) => ($e['id'] ?? '') !== $id));
        JsonStorageService::write('enquiries.json', $updated);

        return redirect()->route('admin.dashboard')->with('success', 'Contact enquiry deleted!');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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

        // Redirect legacy tab query params to their dedicated standalone pages
        if ($request->has('tab')) {
            $tab = $request->query('tab');
            $tabRedirects = [
                'packages'      => 'admin.packages',
                'categories'    => 'admin.categories',
                'bookings'      => 'admin.bookings',
                'customers'     => 'admin.customers',
                'reviews'       => 'admin.reviews',
                'testimonials'  => 'admin.faqs',
                'faqs'          => 'admin.faqs',
                'hero-manager'  => 'admin.hero',
                'hero'          => 'admin.hero',
                'about-manager' => 'admin.about',
                'about'         => 'admin.about',
                'legal-manager' => 'admin.legal',
                'legal'         => 'admin.legal',
                'settings'      => 'admin.settings',
            ];

            if (isset($tabRedirects[$tab])) {
                return redirect()->route($tabRedirects[$tab]);
            }
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

        $defaultLegalPages = app(\App\Http\Controllers\LegalController::class)->getDefaultLegalPages();
        $legalPages = JsonStorageService::read('legal_pages.json', $defaultLegalPages);

        // Compute Dynamic Real-Time Analytics & Reporting Metrics
        $analytics = $this->calculateAnalytics($bookings, $request);

        return view('admin.dashboard', compact('cms', 'artists', 'categories', 'packages', 'bookings', 'faqs', 'settings', 'legalPages', 'analytics'));
    }

    /**
     * Compute dynamic analytics & reporting metrics from real JSON data.
     * Made public so it can be called from AdminPageController.
     */
    public function calculateAnalytics(array $bookings, Request $request): array
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
        return redirect()->route('admin.hero')->with('success', 'CMS Content saved successfully!');
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

        return redirect()->route('admin.testimonials')->with('success', 'Artist profile saved successfully!');
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

        return redirect()->route('admin.testimonials')->with('success', 'Artist profile deleted successfully!');
    }

    /**
     * Save dynamic categories.
     */
    public function saveCategory(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'nav_slug'       => 'nullable|string|max:100',
            'icon'           => 'nullable|string|max:100',
            'bg_color'       => 'nullable|string|max:20',
            'dropdown_badge' => 'nullable|string|max:100',
            'display_order'  => 'nullable|integer',
        ]);

        $allowedImageExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $id = $request->input('id');
        $targetOrder = max(1, (int) $request->input('display_order', 1));
        $dbCat = null;

        // ------------------------------------------------------------------
        // Shift existing category display orders
        // ------------------------------------------------------------------
        if (!$id) {
            // New category: shift any existing categories at or after $targetOrder by +1
            \App\Models\Category::where('display_order', '>=', $targetOrder)
                ->increment('display_order');
        } else {
            // Existing category: shift intermediate categories
            $dbCat = \App\Models\Category::find($id);
            if ($dbCat) {
                $oldOrder = (int) $dbCat->display_order;
                if ($oldOrder !== $targetOrder) {
                    if ($targetOrder < $oldOrder) {
                        // Moving up (e.g. from 5 to 1): shift intermediate items down (+1)
                        \App\Models\Category::where('id', '!=', $dbCat->id)
                            ->where('display_order', '>=', $targetOrder)
                            ->where('display_order', '<', $oldOrder)
                            ->increment('display_order');
                    } else {
                        // Moving down (e.g. from 1 to 5): shift intermediate items up (-1)
                        \App\Models\Category::where('id', '!=', $dbCat->id)
                            ->where('display_order', '>', $oldOrder)
                            ->where('display_order', '<=', $targetOrder)
                            ->decrement('display_order');
                    }
                }
            }
        }

        // ------------------------------------------------------------------
        // Build DB payload
        // ------------------------------------------------------------------
        $dbPayload = [
            'title'          => trim($request->input('title')),
            'icon'           => $request->input('icon', null),
            'bg_color'       => $request->input('bg_color', '#F6CFB2'),
            'nav_slug'       => $request->input('nav_slug', null),
            'dropdown_badge' => $request->input('dropdown_badge', null),
            'display_order'  => $targetOrder,
            'active'         => $request->boolean('active', true),
        ];

        // Slider Image upload or URL
        if ($request->hasFile('slider_image_file')) {
            $file = $request->file('slider_image_file');
            $ext  = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, $allowedImageExts)) {
                return redirect()->back()->with('error', 'Invalid slider image format. Use JPG, PNG, WEBP or GIF.');
            }
            if (!File::exists(public_path('uploads/categories'))) {
                File::makeDirectory(public_path('uploads/categories'), 0755, true);
            }
            $fname = 'slider_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $file->move(public_path('uploads/categories'), $fname);
            $dbPayload['slider_image'] = '/uploads/categories/' . $fname;
        } else {
            $dbPayload['slider_image'] = $request->input('slider_image') ?: null;
        }

        // Dropdown Image upload or URL
        if ($request->hasFile('dropdown_image_file')) {
            $file = $request->file('dropdown_image_file');
            $ext  = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, $allowedImageExts)) {
                return redirect()->back()->with('error', 'Invalid dropdown image format. Use JPG, PNG, WEBP or GIF.');
            }
            if (!File::exists(public_path('uploads/categories'))) {
                File::makeDirectory(public_path('uploads/categories'), 0755, true);
            }
            $fname = 'dropdown_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $file->move(public_path('uploads/categories'), $fname);
            $dbPayload['dropdown_image'] = '/uploads/categories/' . $fname;
        } else {
            $dbPayload['dropdown_image'] = $request->input('dropdown_image') ?: null;
        }

        // ------------------------------------------------------------------
        // Persist to DB
        // ------------------------------------------------------------------
        if ($id) {
            if ($dbCat) {
                $dbCat->update($dbPayload);
            } else {
                $dbCat = \App\Models\Category::find($id);
                if ($dbCat) $dbCat->update($dbPayload);
            }
        } else {
            $dbPayload['slug'] = \Illuminate\Support\Str::slug($dbPayload['title']) . '-' . time();
            $dbPayload['count'] = 0;
            $dbCat = \App\Models\Category::create($dbPayload);
        }

        // Normalize full sequence to guarantee contiguous 1, 2, 3, ... N
        $allCategories = \App\Models\Category::orderBy('display_order')->orderBy('updated_at', 'desc')->get();
        foreach ($allCategories as $index => $catItem) {
            $expected = $index + 1;
            if ($catItem->display_order != $expected) {
                $catItem->update(['display_order' => $expected]);
            }
        }

        // ------------------------------------------------------------------
        // Handle Subcategories JSON (Strictly capped to 18 for mega-menu balance)
        // ------------------------------------------------------------------
        $subcatsJson = $request->input('subcategories_json', null);
        if ($subcatsJson && $dbCat) {
            $subcats = json_decode($subcatsJson, true);
            if (is_array($subcats)) {
                // Strict limit: maximum 18 subcategories (3 columns x 6 links max) to prevent navbar overflow
                $subcats = array_slice($subcats, 0, 18);
                $existingIds = [];
                foreach ($subcats as $idx => $sub) {
                    $name = trim($sub['name'] ?? '');
                    if (!$name) continue;
                    $group = trim($sub['group_name'] ?? '');
                    if (!$group) {
                        $group = 'Setup Themes'; // Always guarantee a valid group name
                    }
                    $subModel = \App\Models\Subcategory::updateOrCreate(
                        ['category_id' => $dbCat->id, 'name' => $name],
                        [
                            'slug'          => \Illuminate\Support\Str::slug($name),
                            'group_name'    => $group,
                            'badge'         => trim($sub['badge'] ?? '') ?: null,
                            'display_order' => $idx + 1,
                            'is_active'     => true,
                        ]
                    );
                    $existingIds[] = $subModel->id;
                }
                // Remove subcategories not in the updated list
                if (!empty($existingIds)) {
                    \App\Models\Subcategory::where('category_id', $dbCat->id)
                        ->whereNotIn('id', $existingIds)
                        ->delete();
                }
            }
        }

        // ------------------------------------------------------------------
        // Also maintain JSON legacy fallback (for JSON storage driver mode)
        // ------------------------------------------------------------------
        $path = storage_path('app/categories.json');
        $categories = [];
        if (File::exists($path)) {
            $categories = json_decode(File::get($path), true) ?? [];
        }

        $catData = [
            'id'     => $dbCat ? $dbCat->id : time(),
            'title'  => $dbPayload['title'],
            'active' => $dbPayload['active'],
            'image'  => $dbPayload['slider_image'] ?? ($categories[0]['image'] ?? ''),
        ];

        $existingIndex = -1;
        foreach ($categories as $index => $c) {
            if ((string)($c['id'] ?? '') === (string)($id ?? '')) {
                $existingIndex = $index;
                break;
            }
        }
        if ($existingIndex !== -1) {
            $categories[$existingIndex] = array_merge($categories[$existingIndex], $catData);
        } else {
            $catData['count'] = 0;
            $categories[] = $catData;
        }
        JsonStorageService::write('categories.json', $categories);

        return redirect()->route('admin.categories')->with('success', 'Category saved successfully!');
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
            return redirect()->route('admin.packages')->with('error', 'Package tier plan not found!');
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
     * Save/Create a package and its tiers in MySQL database and sync subcategories.
     */
    public function savePackage(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'slug'           => 'nullable|string|max:255',
            'badge'          => 'nullable|string|max:50',
            'tag'            => 'nullable|string|max:50',
            'recipients'     => 'nullable',
            'price'          => 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'description'    => 'nullable|string',
            'image_file'     => 'nullable|file|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $id = $request->input('id') ?: $request->input('package_id');
        $title = trim($request->input('title'));
        $slug = $request->input('slug') ? Str::slug($request->input('slug')) : Str::slug($title);

        // Ensure unique slug
        $existingSlug = \App\Models\Package::where('slug', $slug)
            ->when($id, fn($q) => $q->where('id', '!=', $id))
            ->exists();
        if ($existingSlug) {
            $slug .= '-' . time();
        }

        $package = $id ? \App\Models\Package::findOrFail($id) : new \App\Models\Package();

        // 1. Process Main Cover Image
        $image = $request->input('image') ?: null;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $ext  = strtolower($file->getClientOriginalExtension());
            if (!File::exists(public_path('uploads/packages'))) {
                File::makeDirectory(public_path('uploads/packages'), 0755, true);
            }
            $filename = 'pkg_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $file->move(public_path('uploads/packages'), $filename);
            $image = '/uploads/packages/' . $filename;
        }

        // 2. Process Gallery Banners
        $galleryImages = [];
        $galleryInputs = $request->input('gallery', []);
        $galleryFiles  = $request->file('gallery_files', []);

        for ($i = 0; $i < 4; $i++) {
            if (isset($galleryFiles[$i]) && $galleryFiles[$i]->isValid()) {
                $file = $galleryFiles[$i];
                $ext  = strtolower($file->getClientOriginalExtension());
                if (!File::exists(public_path('uploads/packages'))) {
                    File::makeDirectory(public_path('uploads/packages'), 0755, true);
                }
                $filename = 'pkg_gal_' . $i . '_' . time() . '_' . rand(100, 999) . '.' . $ext;
                $file->move(public_path('uploads/packages'), $filename);
                $galleryImages[] = '/uploads/packages/' . $filename;
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
        $validSubcatIds = \App\Models\Subcategory::whereIn('id', (array)$subcatIds)
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
                    $tierModel = \App\Models\PackageTier::where('id', $t['id'])->where('package_id', $package->id)->first();
                    if ($tierModel) {
                        $tierModel->update($tierData);
                        $existingTierIds[] = $tierModel->id;
                        continue;
                    }
                }

                $newTier = \App\Models\PackageTier::create($tierData);
                $existingTierIds[] = $newTier->id;
            }

            // Remove tiers that were removed by user
            if (!empty($existingTierIds)) {
                \App\Models\PackageTier::where('package_id', $package->id)->whereNotIn('id', $existingTierIds)->delete();
            }

            // Update package base price to lowest tier price if not explicitly set
            $minTierPrice = \App\Models\PackageTier::where('package_id', $package->id)->min('price');
            if ($minTierPrice > 0 && (!$package->price || $package->price <= 0)) {
                $package->price = (float)$minTierPrice;
                $package->save();
            }
        } else {
            // Default Standard tier if no tiers submitted
            $standardTier = $package->tiers()->first();
            $tierPrice = $package->price ?: 4999;
            if (!$standardTier) {
                \App\Models\PackageTier::create([
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
            \Illuminate\Support\Facades\Log::warning('packages.json sync warning: ' . $e->getMessage());
        }

        return redirect()->route('admin.packages')->with('success', 'Package saved successfully!');
    }

    /**
     * Save dynamic FAQs.
     */
    public function saveFaq(Request $request)
    {
        $request->validate([
            'q'                => 'required_without:question|nullable|string',
            'question'         => 'required_without:q|nullable|string',
            'a'                => 'required_without:answer|nullable|string',
            'answer'           => 'required_without:a|nullable|string',
            'category'         => 'nullable|string',
            'id'               => 'nullable|integer',
            'sort_order'       => 'nullable|integer',
            'display_order'    => 'nullable|integer',
            'show_on_homepage' => 'nullable',
            'show_on_home'     => 'nullable',
            'is_active'        => 'nullable',
            'active'           => 'nullable',
        ]);

        $id = $request->input('id');
        $q = trim($request->input('q', $request->input('question', '')));
        $a = trim($request->input('a', $request->input('answer', '')));
        $category = strtolower(trim($request->input('category', 'general')));
        if (empty($category)) {
            $category = 'general';
        }

        if ($request->has('is_active')) {
            $isActive = $request->boolean('is_active');
        } elseif ($request->has('active')) {
            $isActive = $request->boolean('active');
        } else {
            $isActive = true;
        }

        if ($request->has('show_on_homepage')) {
            $showOnHomepage = $request->boolean('show_on_homepage');
        } elseif ($request->has('show_on_home')) {
            $showOnHomepage = $request->boolean('show_on_home');
        } else {
            $showOnHomepage = false;
        }

        $sortOrder = (int)$request->input('sort_order', $request->input('display_order', 0));

        $data = [
            'question'         => $q,
            'answer'           => $a,
            'category'         => $category,
            'is_active'        => $isActive,
            'show_on_homepage' => $showOnHomepage,
            'sort_order'       => $sortOrder,
        ];

        if ($id) {
            $faq = \App\Models\Faq::find($id);
            if ($faq) {
                $faq->update($data);
            } else {
                $data['id'] = $id;
                $faq = \App\Models\Faq::create($data);
            }
        } else {
            if ($sortOrder === 0) {
                $data['sort_order'] = ((\App\Models\Faq::max('sort_order') ?? 0) + 1);
            }
            $faq = \App\Models\Faq::create($data);
        }

        // Re-read and sync faqs.json
        $allFaqs = \App\Models\Faq::ordered()->get()->map(function($f) {
            return [
                'id'               => $f->id,
                'q'                => $f->question,
                'question'         => $f->question,
                'a'                => $f->answer,
                'answer'           => $f->answer,
                'category'         => $f->category,
                'active'           => (bool)$f->is_active,
                'is_active'        => (bool)$f->is_active,
                'show_on_home'     => (bool)$f->show_on_homepage,
                'show_on_homepage' => (bool)$f->show_on_homepage,
                'sort_order'       => (int)$f->sort_order,
                'display_order'    => (int)$f->sort_order,
            ];
        })->toArray();

        JsonStorageService::write('faqs.json', $allFaqs);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'FAQ saved successfully!',
                'faq' => $faq,
            ]);
        }

        return redirect()->back()->with('success', 'FAQ saved successfully!');
    }

    /**
     * Delete dynamic FAQ.
     */
    public function deleteFaq($id)
    {
        \App\Models\Faq::where('id', $id)->delete();

        $allFaqs = \App\Models\Faq::ordered()->get()->map(function($f) {
            return [
                'id'               => $f->id,
                'q'                => $f->question,
                'question'         => $f->question,
                'a'                => $f->answer,
                'answer'           => $f->answer,
                'category'         => $f->category,
                'active'           => (bool)$f->is_active,
                'is_active'        => (bool)$f->is_active,
                'show_on_home'     => (bool)$f->show_on_homepage,
                'show_on_homepage' => (bool)$f->show_on_homepage,
                'sort_order'       => (int)$f->sort_order,
                'display_order'    => (int)$f->sort_order,
            ];
        })->toArray();

        JsonStorageService::write('faqs.json', $allFaqs);
        return redirect()->back()->with('success', 'FAQ deleted successfully!');
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
        return redirect()->route('admin.settings')->with('success', 'Settings saved successfully!');
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
        return redirect()->route('admin.packages')->with('success', 'Package tier deleted successfully!');
    }

    /**
     * Delete Category.
     */
    public function deleteCategory($id)
    {
        $dbCat = \App\Models\Category::find($id);
        if ($dbCat) {
            \App\Models\Subcategory::where('category_id', $dbCat->id)->delete();
            $dbCat->delete();

            // Re-normalize display_order sequence after deletion
            $remaining = \App\Models\Category::orderBy('display_order')->orderBy('id')->get();
            foreach ($remaining as $idx => $item) {
                $expected = $idx + 1;
                if ($item->display_order != $expected) {
                    $item->update(['display_order' => $expected]);
                }
            }
        }

        $cats = JsonStorageService::read('categories.json', []);
        $updatedCats = array_values(array_filter($cats, fn($c) => (string)($c['id'] ?? '') !== (string)$id));
        JsonStorageService::write('categories.json', $updatedCats);

        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully!');
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
        return redirect()->route('admin.testimonials')->with('success', 'Testimonial saved successfully!');
    }

    /**
     * Delete Testimonial.
     */
    public function deleteTestimonial($id)
    {
        $testimonials = JsonStorageService::read('testimonials.json', []);
        $updated = array_values(array_filter($testimonials, fn($t) => ($t['id'] ?? '') !== $id));
        JsonStorageService::write('testimonials.json', $updated);

        return redirect()->route('admin.testimonials')->with('success', 'Testimonial deleted successfully!');
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
        return redirect()->route('admin.testimonials')->with('success', 'Gallery item saved successfully!');
    }

    /**
     * Delete Gallery Item.
     */
    public function deleteGalleryItem($id)
    {
        $gallery = JsonStorageService::read('gallery.json', []);
        $updated = array_values(array_filter($gallery, fn($g) => ($g['id'] ?? '') !== $id));
        JsonStorageService::write('gallery.json', $updated);

        return redirect()->route('admin.testimonials')->with('success', 'Gallery item deleted successfully!');
    }

    /**
     * Delete Contact Enquiry.
     */
    public function deleteEnquiry($id)
    {
        $enquiries = JsonStorageService::read('enquiries.json', []);
        $updated = array_values(array_filter($enquiries, fn($e) => ($e['id'] ?? '') !== $id));
        JsonStorageService::write('enquiries.json', $updated);

        return redirect()->route('admin.bookings')->with('success', 'Contact enquiry deleted!');
    }

    /**
     * Save dynamic Legal Policy Pages.
     */
    public function saveLegalPages(Request $request)
    {
        $defaultLegalPages = app(\App\Http\Controllers\LegalController::class)->getDefaultLegalPages();

        $legalPages = [
            'privacy' => [
                'title' => $request->input('privacy_title', $defaultLegalPages['privacy']['title']),
                'subtitle' => $request->input('privacy_subtitle', $defaultLegalPages['privacy']['subtitle']),
                'content' => $request->input('privacy_content', $defaultLegalPages['privacy']['content']),
            ],
            'terms' => [
                'title' => $request->input('terms_title', $defaultLegalPages['terms']['title']),
                'subtitle' => $request->input('terms_subtitle', $defaultLegalPages['terms']['subtitle']),
                'content' => $request->input('terms_content', $defaultLegalPages['terms']['content']),
            ],
            'cancellation' => [
                'title' => $request->input('cancellation_title', $defaultLegalPages['cancellation']['title']),
                'subtitle' => $request->input('cancellation_subtitle', $defaultLegalPages['cancellation']['subtitle']),
                'content' => $request->input('cancellation_content', $defaultLegalPages['cancellation']['content']),
            ],
            'booking_policy' => [
                'title' => $request->input('booking_policy_title', $defaultLegalPages['booking_policy']['title']),
                'subtitle' => $request->input('booking_policy_subtitle', $defaultLegalPages['booking_policy']['subtitle']),
                'content' => $request->input('booking_policy_content', $defaultLegalPages['booking_policy']['content']),
            ],
        ];

        JsonStorageService::write('legal_pages.json', $legalPages);

        return redirect()->route('admin.legal')->with('success', 'Legal Policy pages updated successfully!');
    }

    /**
     * Toggle active/draft status of a database package.
     */
    public function togglePackageStatus($id)
    {
        $package = \App\Models\Package::findOrFail($id);
        $package->active = !$package->active;
        $package->save();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'active'  => $package->active,
                'message' => 'Package status updated to ' . ($package->active ? 'Published' : 'Draft') . '.'
            ]);
        }

        return redirect()->back()->with('success', 'Package status updated.');
    }

    /**
     * Delete a database package and its tiers.
     */
    public function destroyPackage($id)
    {
        $package = \App\Models\Package::findOrFail($id);
        $title = $package->title;
        $package->tiers()->delete();
        $package->subcategories()->detach();
        $package->delete();

        return redirect()->route('admin.packages')->with('success', "Package '{$title}' and its tiers deleted successfully.");
    }
}

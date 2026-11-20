<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\JsonStorageService;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminPackageController;
use App\Http\Controllers\Admin\AdminCmsController;

class DashboardController extends Controller
{
    /**
     * Display the Admin Dashboard.
     */
    public function index(Request $request)
    {
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
                'hero-manager'           => 'admin.hero',
                'hero'                   => 'admin.hero',
                'offers-manager'         => 'admin.offers',
                'offers'                 => 'admin.offers',
                'promise-banner-manager' => 'admin.promise-banner',
                'promise-banner'         => 'admin.promise-banner',
                'legal-manager'          => 'admin.legal',
                'legal'                  => 'admin.legal',
                'settings'               => 'admin.settings',
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

        $rawCms = JsonStorageService::read('cms.json', []) ?? [];
        $cms = array_replace_recursive($defaultCms, $rawCms);
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
        $activePipelineEvents = [];

        foreach ($filteredBookings as $b) {
            $status = $b['status'] ?? 'Pending';
            $price = (int)($b['total'] ?? $b['total_amount'] ?? $b['package_price'] ?? 0);
            $dateStr = $b['date'] ?? $b['event_date'] ?? null;
            $bDate = !empty($dateStr) ? strtotime($dateStr) : 0;
            $catName = $b['category_name'] ?? $b['category'] ?? 'Event Setup';
            $pkgName = $b['package_name'] ?? $b['package'] ?? 'Celebration Package';
            $custName = $b['customer_name'] ?? $b['name'] ?? 'Client';
            $custMobile = $b['customer_mobile'] ?? $b['mobile'] ?? '';
            $custWa = $b['customer_whatsapp'] ?? $b['whatsapp'] ?? $custMobile;
            $timeStr = $b['event_time'] ?? $b['time'] ?? 'Flexible';

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

            // Calculate timing badge
            $daysDiff = $bDate > 0 ? (int)round(($bDate - $todayTimestamp) / 86400) : null;
            $timingBadge = 'Scheduled';
            if ($daysDiff !== null) {
                if ($daysDiff === 0) {
                    $timingBadge = 'TODAY';
                } elseif ($daysDiff === 1) {
                    $timingBadge = 'TOMORROW';
                } elseif ($daysDiff > 1 && $daysDiff <= 7) {
                    $timingBadge = "In {$daysDiff} days";
                } elseif ($daysDiff > 7) {
                    $timingBadge = date('d M', $bDate);
                } elseif ($daysDiff === -1) {
                    $timingBadge = 'Yesterday';
                } else {
                    $timingBadge = 'Past Event';
                }
            }

            $normalizedEvent = array_merge($b, [
                'id' => $b['id'] ?? '',
                'name' => $custName,
                'customer_name' => $custName,
                'mobile' => $custMobile,
                'customer_mobile' => $custMobile,
                'whatsapp' => $custWa,
                'customer_whatsapp' => $custWa,
                'package' => $pkgName,
                'package_name' => $pkgName,
                'category' => $catName,
                'category_name' => $catName,
                'date' => $dateStr ?? 'TBD',
                'event_date' => $dateStr ?? 'TBD',
                'time' => $timeStr,
                'event_time' => $timeStr,
                'total' => $price,
                'total_amount' => $price,
                'status' => $status,
                'timestamp' => $bDate,
                'days_diff' => $daysDiff,
                'timing_badge' => $timingBadge,
                'is_future' => ($bDate >= $todayTimestamp),
            ]);

            if (strcasecmp($status, 'Cancelled') !== 0) {
                $activePipelineEvents[] = $normalizedEvent;

                if ($bDate >= $todayTimestamp) {
                    $upcomingCount++;
                    $upcomingEventsList[] = $normalizedEvent;
                }
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
                    $packageStats[$pkgName] = [
                        'count' => 0,
                        'revenue' => 0,
                        'category' => $catName,
                    ];
                }
                $packageStats[$pkgName]['count']++;
                $packageStats[$pkgName]['revenue'] += $price;
                if (!empty($catName) && $catName !== 'Event Setup') {
                    $packageStats[$pkgName]['category'] = $catName;
                }
            }
        }

        // Average Booking Value: valid non-cancelled bookings sum / count
        $avgBookingValue = $nonCancelledCount > 0 ? (int)round($nonCancelledSum / $nonCancelledCount) : 0;

        // Sort Top 5 Packages by count descending, then revenue descending
        uasort($packageStats, function($a, $b) {
            if ($b['count'] === $a['count']) {
                return $b['revenue'] <=> $a['revenue'];
            }
            return $b['count'] <=> $a['count'];
        });
        $topPackages = array_slice($packageStats, 0, 5, true);

        // Calculate share percentage for each top package
        $maxPackageBookings = 0;
        foreach ($topPackages as $pkg) {
            if ($pkg['count'] > $maxPackageBookings) {
                $maxPackageBookings = $pkg['count'];
            }
        }
        foreach ($topPackages as $name => &$pkgData) {
            $pkgData['percentage'] = $totalBookings > 0 ? round(($pkgData['count'] / $totalBookings) * 100, 1) : 0;
            $pkgData['bar_width'] = $maxPackageBookings > 0 ? round(($pkgData['count'] / $maxPackageBookings) * 100) : 0;
            $pkgData['avg_price'] = $pkgData['count'] > 0 ? (int)round($pkgData['revenue'] / $pkgData['count']) : 0;
        }
        unset($pkgData);

        // Sort Upcoming Events by date ascending (closest first)
        usort($upcomingEventsList, fn($a, $b) => ($a['timestamp'] ?? 0) <=> ($b['timestamp'] ?? 0));
        $trueUpcomingCount = count($upcomingEventsList);

        $isPipelineFallback = false;
        if (!empty($upcomingEventsList)) {
            $displayUpcomingEvents = array_slice($upcomingEventsList, 0, 10);
        } elseif (!empty($activePipelineEvents)) {
            // Graceful fallback to recent active bookings so the schedule view is never a dead empty screen
            usort($activePipelineEvents, fn($a, $b) => ($b['timestamp'] ?? 0) <=> ($a['timestamp'] ?? 0));
            $displayUpcomingEvents = array_slice($activePipelineEvents, 0, 10);
            $isPipelineFallback = true;
        } else {
            $displayUpcomingEvents = [];
        }

        // Recent Activity sorted by creation timestamp descending
        $recentActivity = $filteredBookings;
        usort($recentActivity, fn($a, $b) => strtotime($b['created_at'] ?? $b['date'] ?? '') <=> strtotime($a['created_at'] ?? $a['date'] ?? ''));
        $recentActivity = array_slice($recentActivity, 0, 8);

        // Compute Status Percentages
        $statusPercentages = [
            'pending' => $totalBookings > 0 ? round(($pendingCount / $totalBookings) * 100, 1) : 0,
            'contacted' => $totalBookings > 0 ? round(($contactedCount / $totalBookings) * 100, 1) : 0,
            'confirmed' => $totalBookings > 0 ? round(($confirmedCount / $totalBookings) * 100, 1) : 0,
            'completed' => $totalBookings > 0 ? round(($completedCount / $totalBookings) * 100, 1) : 0,
            'cancelled' => $totalBookings > 0 ? round(($cancelledCount / $totalBookings) * 100, 1) : 0,
        ];

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
            'status_percentages' => $statusPercentages,
            'pipeline_value' => $pipelineValue,
            'confirmed_revenue' => $confirmedRevenue,
            'avg_booking_value' => $avgBookingValue,
            'upcoming_count' => $upcomingCount,
            'true_upcoming_count' => $trueUpcomingCount,
            'is_pipeline_fallback' => $isPipelineFallback,
            'category_stats' => $categoryStats,
            'top_packages' => $topPackages,
            'upcoming_events' => $displayUpcomingEvents,
            'recent_activity' => $recentActivity,
            'total_customers' => \App\Models\Customer::count(),
        ];
    }

    /**
     * Save dynamic CMS settings.
    /**
     * Save Hero Banners (delegated to AdminCmsController).
     */
    public function saveHeroSlides(Request $request)
    {
        return app(AdminCmsController::class)->saveHeroSlides($request);
    }

    /**
     * Save Offers Section.
     */
    public function saveOffers(Request $request)
    {
        return app(AdminCmsController::class)->saveOffers($request);
    }

    /**
     * Save Promise Banner (delegated to AdminCmsController).
     */
    public function savePromiseBanner(Request $request)
    {
        return app(AdminCmsController::class)->savePromiseBanner($request);
    }

    /**
     * Save dynamic CMS settings (delegated to AdminCmsController).
     */
    public function saveCms(Request $request)
    {
        return app(AdminCmsController::class)->saveCms($request);
    }

    /**
     * Save/Create an Artist Profile.
     */
    public function saveArtist(Request $request)
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'category'   => ['nullable', 'string', 'max:100'],
            'image_file' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
        ]);

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
        $artistData['name'] = trim((string)$request->input('name'));
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
            try {
                $artistData['image'] = app(ImageOptimizationService::class)->optimize(
                    file: $request->file('image_file'),
                    targetSubdir: 'uploads',
                    profile: 'artist',
                    prefix: 'artist',
                    quality: ImageOptimizationService::DEFAULT_QUALITY
                );
            } catch (\Throwable $e) {
                Log::warning('Artist profile image optimization failed: ' . $e->getMessage());
                $artistData['image'] = $request->input('image', $artistData['image'] ?? '/assets/images/hero/2.jpg');
            }
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
     * Save dynamic categories (delegated to AdminCategoryController).
     */
    public function saveCategory(Request $request)
    {
        return app(AdminCategoryController::class)->saveCategory($request);
    }

    /**
     * Render the create package page (delegated to AdminPackageController).
     */
    public function createPackagePage()
    {
        return app(AdminPackageController::class)->createPackagePage();
    }

    /**
     * Render the edit package page (delegated to AdminPackageController).
     */
    public function editPackagePage($catId, $tierIdx)
    {
        return app(AdminPackageController::class)->editPackagePage($catId, $tierIdx);
    }

    /**
     * Save/Create a package (delegated to AdminPackageController).
     */
    public function savePackage(Request $request)
    {
        return app(AdminPackageController::class)->savePackage($request);
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
        $validated = $request->validate([
            'id'     => 'required|string',
            'status' => 'required|string|in:Pending,Contacted,Approved,Confirmed,Completed,Cancelled,Rejected,Under Review',
        ]);

        $id = $validated['id'];
        $status = $validated['status'];

        $result = DB::transaction(function() use ($id, $status) {
            // 1. Fetch and lock targeted booking row in database
            $bookingModel = Booking::where('id', $id)->lockForUpdate()->first();
            $previousStatus = $bookingModel ? $bookingModel->status : 'Pending';
            $sentList = $bookingModel ? ($bookingModel->notifications_sent ?? []) : [];

            if ($bookingModel) {
                $bookingModel->status = $status;
                $bookingModel->updated_at = now();
                $bookingModel->save();
            }

            // 2. Synchronize single targeted record in JSON fallback under exclusive flock
            $updatedJsonBooking = JsonStorageService::updateBookingRecord($id, [
                'status' => $status,
                'updated_at' => date('d M Y, h:i A')
            ]);

            if (!$bookingModel && !$updatedJsonBooking) {
                return null;
            }

            $bookingData = $updatedJsonBooking ?? [];
            if ($bookingModel) {
                $bookingData['id'] = $bookingData['id'] ?? $bookingModel->id;
                $bookingData['name'] = $bookingData['name'] ?? $bookingModel->customer_name;
                $bookingData['mobile'] = $bookingData['mobile'] ?? $bookingModel->customer_mobile;
                $bookingData['whatsapp'] = $bookingData['whatsapp'] ?? $bookingModel->customer_whatsapp;
                $bookingData['email'] = $bookingData['email'] ?? $bookingModel->customer_email;
                $bookingData['package'] = $bookingData['package'] ?? $bookingModel->package_name;
                $bookingData['category'] = $bookingData['category'] ?? $bookingModel->category_name;
                $bookingData['tier'] = $bookingData['tier'] ?? $bookingModel->tier_name;
                $bookingData['date'] = $bookingData['date'] ?? $bookingModel->event_date;
                $bookingData['time'] = $bookingData['time'] ?? $bookingModel->event_time;
                $bookingData['guest_count'] = $bookingData['guest_count'] ?? $bookingModel->guest_count;
                $bookingData['address'] = $bookingData['address'] ?? $bookingModel->address;
                $bookingData['area'] = $bookingData['area'] ?? $bookingModel->area;
                $bookingData['city'] = $bookingData['city'] ?? $bookingModel->city;
                $bookingData['landmark'] = $bookingData['landmark'] ?? $bookingModel->landmark;
                $bookingData['pincode'] = $bookingData['pincode'] ?? $bookingModel->pincode;
                $bookingData['total'] = $bookingData['total'] ?? $bookingModel->total_amount;
                $bookingData['package_price'] = $bookingData['package_price'] ?? $bookingModel->package_price;
                $bookingData['surcharge'] = $bookingData['surcharge'] ?? $bookingModel->surcharge_amount;
                $bookingData['status'] = $status;
                $bookingData['notifications_sent'] = $sentList;
            }

            return [
                'booking' => $bookingData,
                'previous_status' => $previousStatus,
                'model' => $bookingModel
            ];
        });

        if (!$result) {
            return response()->json(['success' => false, 'message' => 'Booking ID not found'], 404);
        }

        // Invalidate pending bookings counter badge cache on any successful status update
        \Illuminate\Support\Facades\Cache::forget('admin_pending_bookings_count');

        $updatedBooking = $result['booking'];
        $previousStatus = $result['previous_status'];
        $bookingModel = $result['model'];
        $sentList = is_array($updatedBooking['notifications_sent'] ?? null) ? $updatedBooking['notifications_sent'] : [];

        // Duplicate Notification Protection & Rate Limiting: 60-second debounce per booking status notification
        $notifDebounceKey = 'booking_status_notif_cooldown_' . $id . '_' . strtolower($status);
        $isDebounced = \Illuminate\Support\Facades\Cache::has($notifDebounceKey);

        if (!$isDebounced && ($previousStatus !== $status || !in_array(strtolower($status), $sentList))) {
            \Illuminate\Support\Facades\Cache::put($notifDebounceKey, true, 60);
            try {
                if (!empty($updatedBooking['email'])) {
                    if (strcasecmp($status, 'Confirmed') === 0) {
                        \Illuminate\Support\Facades\Notification::route('mail', $updatedBooking['email'])
                            ->notify(new \App\Notifications\BookingConfirmedNotification($updatedBooking));
                        $sentList[] = 'confirmed';
                    } else if (strcasecmp($status, 'Cancelled') === 0) {
                        \Illuminate\Support\Facades\Notification::route('mail', $updatedBooking['email'])
                            ->notify(new \App\Notifications\BookingCancelledNotification($updatedBooking));
                        $sentList[] = 'cancelled';
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Status update mail notification error: ' . $e->getMessage());
            }

            try {
                if (strcasecmp($status, 'Confirmed') === 0 && !in_array('confirmed_wa', $sentList)) {
                    \App\Jobs\SendWhatsAppBookingNotificationJob::dispatch($id, 'customer_confirmed', $updatedBooking);
                    $sentList[] = 'confirmed_wa';
                } else if (strcasecmp($status, 'Cancelled') === 0 && !in_array('cancelled_wa', $sentList)) {
                    \App\Jobs\SendWhatsAppBookingNotificationJob::dispatch($id, 'customer_cancelled', $updatedBooking);
                    $sentList[] = 'cancelled_wa';
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Status update WhatsApp/SMS notification error: ' . $e->getMessage());
            }

            // Update sent list if changed
            $sentList = array_unique($sentList);
            if ($bookingModel) {
                $bookingModel->notifications_sent = $sentList;
                $bookingModel->save();
            }
            JsonStorageService::updateBookingRecord($id, ['notifications_sent' => $sentList]);
        }

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
            $msg = "🎉 Great news, " . ($updatedBooking['name'] ?? 'Customer') . "! Your Artizen event booking #" . $id . " for " . ($updatedBooking['package'] ?? 'Setup') . " on " . ($updatedBooking['date'] ?? '') . " has been officially CONFIRMED! ✨ Our setup crew is reserved and will reach your venue on time. Settle payment offline post-setup. Thank you for celebrating with us!\n\n📷 Setup Photo: " . $imgUrl;
        } else if (strcasecmp($status, 'Cancelled') === 0) {
            $msg = "Hello " . ($updatedBooking['name'] ?? 'Customer') . ". We wanted to let you know that your Artizen booking request #" . $id . " has been cancelled. If you wish to reschedule or need any custom celebration assistance, please feel free to message us here anytime. Warm regards, Artizen Events.";
        } else {
            $msg = "Hello " . ($updatedBooking['name'] ?? 'Customer') . "! Your Artizen celebration request #" . $id . " status has been updated to " . strtoupper($status) . ". Our team is at your service.\n\n📷 Setup Photo: " . $imgUrl;
        }

        $notifyUrl = !empty($custPhone) ? ("https://wa.me/" . $custPhone . "?text=" . urlencode($msg)) : null;

        return response()->json([
            'success' => true,
            'status' => $status,
            'notify_url' => $notifyUrl,
            'message' => 'Booking ' . $id . ' status updated to ' . $status
        ]);
    }

    /**
     * Bulk delete bookings from Database and JSON storage.
     */
    public function bulkDeleteBookings(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'required|string',
        ]);

        $ids = $validated['ids'];

        DB::transaction(function() use ($ids) {
            \App\Models\WhatsAppNotification::whereIn('booking_id', $ids)->delete();
            Booking::whereIn('id', $ids)->delete();
            JsonStorageService::deleteBookingRecords($ids);
        });

        return response()->json([
            'success' => true,
            'message' => count($ids) . ' booking(s) deleted successfully!',
            'deleted_ids' => $ids
        ]);
    }

    /**
     * Delete an individual booking.
     */
    public function deleteBooking($id)
    {
        $ids = [$id];
        DB::transaction(function() use ($ids) {
            \App\Models\WhatsAppNotification::whereIn('booking_id', $ids)->delete();
            Booking::whereIn('id', $ids)->delete();
            JsonStorageService::deleteBookingRecords($ids);
        });

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Booking ' . $id . ' deleted successfully!'
            ]);
        }

        return redirect()->route('admin.bookings')->with('success', 'Booking ' . $id . ' deleted successfully!');
    }

    /**
     * Save admin settings or delegate to profile update.
     */
    public function saveSettings(Request $request)
    {
        if ($request->has('name') || $request->has('email')) {
            return app(AdminPageController::class)->updateProfile($request);
        }

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully!');
    }

    /**
     * Delete a package tier (delegated to AdminPackageController).
     */
    public function deletePackage($catId, $tierIdx)
    {
        return app(AdminPackageController::class)->deletePackage($catId, $tierIdx);
    }

    /**
     * Delete Category (delegated to AdminCategoryController).
     */
    public function deleteCategory($id)
    {
        return app(AdminCategoryController::class)->deleteCategory($id);
    }

    /**
     * Save/Update Testimonial (Review).
     */
    public function saveTestimonial(Request $request)
    {
        $request->validate([
            'author'      => 'required|string|max:255',
            'review'      => 'required|string|max:2000',
            'rating'      => 'nullable|integer|min:1|max:5',
            'event_type'  => 'nullable|string|max:255',
            'avatar_file' => 'nullable|file|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar_file')) {
            try {
                $avatarPath = app(ImageOptimizationService::class)->optimize(
                    file: $request->file('avatar_file'),
                    targetSubdir: 'uploads',
                    profile: 'review',
                    prefix: 'review_avatar',
                    quality: ImageOptimizationService::DEFAULT_QUALITY
                );
            } catch (\Throwable $e) {
                Log::warning('Testimonial avatar optimization failed: ' . $e->getMessage());
            }
        }

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
                'avatar' => $avatarPath ?: trim($request->input('avatar', '/assets/images/reviews/1.jpg')),
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
                    if ($avatarPath) {
                        $t['avatar'] = $avatarPath;
                    } elseif ($request->filled('avatar')) {
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
     * Alias for saveTestimonial to maintain naming convention with reviews.
     */
    public function saveReview(Request $request)
    {
        return $this->saveTestimonial($request);
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
            'title'      => 'required|string|max:255',
            'category'   => 'required|string|max:255',
            'image_file' => 'nullable|file|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $gallery = JsonStorageService::read('gallery.json', []);
        $id = $request->input('id');

        $imagePath = $request->input('image', '/assets/images/hero/1.jpg');
        if ($request->hasFile('image_file')) {
            try {
                $imagePath = app(ImageOptimizationService::class)->optimize(
                    file: $request->file('image_file'),
                    targetSubdir: 'uploads',
                    profile: 'gallery',
                    prefix: 'gallery',
                    quality: ImageOptimizationService::DEFAULT_QUALITY
                );
            } catch (\Throwable $e) {
                Log::warning('Gallery item image optimization failed: ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Gallery image processing failed: ' . $e->getMessage());
            }
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
     * Save dynamic Legal Policy Pages (delegated to AdminCmsController).
     */
    public function saveLegalPages(Request $request)
    {
        return app(AdminCmsController::class)->saveLegalPages($request);
    }

    /**
     * Toggle active/draft status of a database package (delegated to AdminPackageController).
     */
    public function togglePackageStatus($id)
    {
        return app(AdminPackageController::class)->togglePackageStatus($id);
    }

    /**
     * Delete a database package and its tiers (delegated to AdminPackageController).
     */
    public function destroyPackage($id)
    {
        return app(AdminPackageController::class)->destroyPackage($id);
    }
}

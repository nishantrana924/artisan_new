<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JsonStorageService;
use App\Services\CelebrationCatalogService;
use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Display the public booking request checkout form.
     */
    public function index(Request $request)
    {
        $categoriesList = JsonStorageService::read('categories.json', []);
        $allPackages = \App\Services\CelebrationCatalogService::getAllPackages();
        $masterAddons = \App\Services\CelebrationCatalogService::getMasterAddons();

        // 1. Resolve requested package by slug or name
        $slug = trim((string)$request->query('slug', ''));
        $requestedPkgName = trim((string)$request->query('package', ''));
        $requestedTier = $request->query('tier', 0);
        $rawAddons = $request->query('addons', '');
        $preselectedAddonIds = array_values(array_filter(explode(',', (string)$rawAddons)));

        $matchedPackage = null;

        // Try direct slug match
        if (!empty($slug)) {
            $cleanSlug = Str::slug($slug);
            foreach ($allPackages as $p) {
                if (strtolower($p['slug'] ?? '') === strtolower($slug) || Str::slug($p['title'] ?? '') === $cleanSlug) {
                    $matchedPackage = $p;
                    break;
                }
            }
        }

        // Try title match
        if (!$matchedPackage && !empty($requestedPkgName)) {
            foreach ($allPackages as $p) {
                if (strcasecmp($p['title'] ?? '', $requestedPkgName) === 0 || str_contains(strtolower($p['title'] ?? ''), strtolower($requestedPkgName))) {
                    $matchedPackage = $p;
                    break;
                }
            }
        }

        // Try lookup in packages.json if not found in catalog service
        if (!$matchedPackage && (!empty($slug) || !empty($requestedPkgName))) {
            $packagesDb = JsonStorageService::read('packages.json', []);
            foreach ($packagesDb as $pDb) {
                $pTitle = $pDb['title'] ?? '';
                $pSlug = Str::slug($pTitle);
                if ((!empty($slug) && $pSlug === Str::slug($slug)) || 
                    (!empty($requestedPkgName) && (strcasecmp($pTitle, $requestedPkgName) === 0 || str_contains(strtolower($pTitle), strtolower($requestedPkgName))))) {
                    $matchedPackage = [
                        'id' => $pDb['id'] ?? 'pkg-' . rand(100, 999),
                        'slug' => $pSlug,
                        'title' => $pTitle,
                        'category_name' => $pDb['tag'] ?? 'Celebrations',
                        'price' => $pDb['tiers'][0]['price'] ?? 4999,
                        'image' => $pDb['image'] ?? asset('assets/images/hero/1.jpg'),
                        'tiers' => $pDb['tiers'] ?? [],
                        'inclusions' => $pDb['tiers'][0]['inclusions'] ?? []
                    ];
                    break;
                }
            }
        }

        // Strictly enforce package selection: do not open booking page if no package is selected
        if (!$matchedPackage) {
            return redirect()->route('events.index')
                ->with('info', 'Please browse and select an event package first to proceed with booking.');
        }

        // Enforce Authentication: Customer must be logged in to book a package
        $user = \Illuminate\Support\Facades\Auth::user();
        $isLoggedIn = $user || session('user_logged_in');
        if (!$isLoggedIn) {
            return redirect()->route('login', ['redirect' => $request->getRequestUri()])
                ->with('info', 'Please log in to your Artizen account to proceed with your booking request.');
        }

        // Ensure tiers exist on matched package
        if (empty($matchedPackage['tiers'])) {
            $baseP = (int)($matchedPackage['price'] ?? 4999);
            $matchedPackage['tiers'] = [
                ['name' => 'Standard Setup', 'price' => $baseP, 'inclusions' => $matchedPackage['inclusions'] ?? []],
                ['name' => 'Premium Setup', 'price' => (int)round($baseP * 1.35), 'inclusions' => array_merge($matchedPackage['inclusions'] ?? [], ['Premium Ambient Lighting', 'Priority 2-Hour Setup'])],
                ['name' => 'Luxury VIP Setup', 'price' => (int)round($baseP * 1.8), 'inclusions' => array_merge($matchedPackage['inclusions'] ?? [], ['Full Venue Canopy', 'Dedicated Setup Manager', 'Cold Spark Machine'])]
            ];
        }

        // Resolve tier index
        $selectedTierIndex = 0;
        $requestedTierName = trim((string)$request->query('tier_name', ''));
        if (!empty($requestedTierName) && !empty($matchedPackage['tiers'])) {
            foreach ($matchedPackage['tiers'] as $idx => $t) {
                if (strcasecmp($t['name'] ?? '', $requestedTierName) === 0) {
                    $selectedTierIndex = $idx;
                    break;
                }
            }
        } elseif (is_numeric($requestedTier)) {
            $selectedTierIndex = (int)$requestedTier;
        } elseif (!empty($requestedTier) && !empty($matchedPackage['tiers'])) {
            foreach ($matchedPackage['tiers'] as $idx => $t) {
                if (strcasecmp($t['name'] ?? '', $requestedTier) === 0) {
                    $selectedTierIndex = $idx;
                    break;
                }
            }
        }

        if (empty($matchedPackage['tiers']) || !isset($matchedPackage['tiers'][$selectedTierIndex])) {
            $selectedTierIndex = 0;
        }

        return view('booking.create', compact(
            'categoriesList',
            'allPackages',
            'matchedPackage',
            'selectedTierIndex',
            'masterAddons',
            'preselectedAddonIds',
            'user'
        ));
    }

    /**
     * Save customer booking request with backend price verification & atomic storage.
     */
    public function store(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $isLoggedIn = $user || session('user_logged_in');

        if (!$isLoggedIn) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in to your account to complete your booking request.',
                    'require_login' => true,
                    'login_url' => route('login')
                ], 401);
            }
            return redirect()->route('login', ['redirect' => $request->getRequestUri()])
                ->with('error', 'Please log in to submit your booking request.');
        }

        $userId = $user ? $user->id : session('user_id');
        $userEmail = $request->input('email') ?: ($user ? $user->email : session('user_email'));

        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'whatsapp' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'event_date' => 'required|date|after_or_equal:today',
            'event_time' => 'required|string|max:255',
            'guest_count' => 'required|integer|min:1|max:5000',
            'address' => 'required|string|max:500',
            'area' => 'required|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'pincode' => 'required|string|max:10',
            'package' => 'required|string|max:255',
            'tier' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $allPackages = \App\Services\CelebrationCatalogService::getAllPackages();
        $masterAddons = \App\Services\CelebrationCatalogService::getMasterAddons();

        // 1. Backend Price & Package Verification
        $submittedPkgTitle = trim($request->input('package'));
        $submittedTierName = trim($request->input('tier', 'Standard'));
        $verifiedBasePrice = 0;
        $matchedCategoryTitle = trim($request->input('category', 'Event Setup'));
        $matchedImage = '/assets/images/hero/1.jpg';
        $matchedPackageId = null;

        foreach ($allPackages as $p) {
            if (strcasecmp($p['title'] ?? '', $submittedPkgTitle) === 0 || str_contains(strtolower($submittedPkgTitle), strtolower($p['title'] ?? ''))) {
                $rawPkgId = $p['db_id'] ?? $p['id'] ?? null;
                if (is_numeric($rawPkgId)) {
                    $matchedPackageId = (int)$rawPkgId;
                } elseif (is_string($rawPkgId) && str_starts_with($rawPkgId, 'pkg-')) {
                    $matchedPackageId = (int)substr($rawPkgId, 4);
                }
                if ($matchedPackageId && !\App\Models\Package::where('id', $matchedPackageId)->exists()) {
                    $matchedPackageId = null;
                }
                $matchedCategoryTitle = $p['category_name'] ?? $matchedCategoryTitle;
                $matchedImage = $p['image'] ?? $matchedImage;

                // Match Tier
                foreach ($p['tiers'] ?? [] as $tier) {
                    if (strcasecmp($tier['name'] ?? '', $submittedTierName) === 0 || empty($submittedTierName)) {
                        $verifiedBasePrice = (int)($tier['price'] ?? 0);
                        $submittedTierName = $tier['name'] ?? $submittedTierName;
                        $matchedImage = $tier['image'] ?? $matchedImage;
                        break;
                    }
                }
                break;
            }
        }

        // Fallback to submitted price only if custom quote
        if ($verifiedBasePrice <= 0) {
            $verifiedBasePrice = (int)$request->input('price', 4999);
            if ($verifiedBasePrice <= 0) $verifiedBasePrice = 4999;
        }

        // 2. Add-ons Price Verification
        $submittedAddonIds = $request->input('addons', []);
        if (is_string($submittedAddonIds)) {
            $decoded = json_decode($submittedAddonIds, true);
            $submittedAddonIds = is_array($decoded) ? $decoded : array_filter(explode(',', $submittedAddonIds));
        }

        $verifiedAddonsList = [];
        $verifiedAddonsTotal = 0;
        $addonsSummaryLines = [];

        foreach ($submittedAddonIds as $rawAddon) {
            $addonId = '';
            $addonName = '';
            $addonPrice = 0;

            if (is_array($rawAddon)) {
                $addonId = trim($rawAddon['id'] ?? '');
                $addonName = trim($rawAddon['name'] ?? '');
                $addonPrice = (int)($rawAddon['price'] ?? 0);
            } else {
                $addonId = trim((string)$rawAddon);
            }

            if (empty($addonId) && empty($addonName)) continue;

            $matched = null;
            foreach ($masterAddons as $ma) {
                $aliases = $ma['aliases'] ?? [];
                if ($ma['id'] === $addonId || in_array($addonId, $aliases)) {
                    $matched = $ma;
                    break;
                }
                if (!empty($addonName) && strcasecmp($ma['name'], $addonName) === 0) {
                    $matched = $ma;
                    break;
                }
                if (!empty($addonId) && (str_contains(strtolower($ma['id']), strtolower($addonId)) || str_contains(strtolower($ma['name']), strtolower($addonId)))) {
                    $matched = $ma;
                    break;
                }
            }

            if ($matched) {
                $verifiedAddonsList[] = [
                    'id' => $matched['id'],
                    'name' => $matched['name'],
                    'price' => (int)$matched['price']
                ];
                $verifiedAddonsTotal += (int)$matched['price'];
                $addonsSummaryLines[] = $matched['name'] . ' (₹' . number_format($matched['price']) . ')';
            } elseif (!empty($addonName) && $addonPrice > 0 && $addonPrice <= 15000) {
                $verifiedAddonsList[] = [
                    'id' => $addonId ?: ('custom-' . md5($addonName)),
                    'name' => $addonName,
                    'price' => $addonPrice
                ];
                $verifiedAddonsTotal += $addonPrice;
                $addonsSummaryLines[] = $addonName . ' (₹' . number_format($addonPrice) . ')';
            }
        }

        $grandTotal = $verifiedBasePrice + $verifiedAddonsTotal;

        // 3. Construct Combined Notes (Add-ons breakdown + Customer Instructions)
        $customerNotes = trim($request->input('notes', ''));
        $combinedNotesParts = [];
        if (!empty($addonsSummaryLines)) {
            $combinedNotesParts[] = "Selected Add-ons:\n- " . implode("\n- ", $addonsSummaryLines);
        }
        if (!empty($customerNotes)) {
            $combinedNotesParts[] = "Customer Notes: " . $customerNotes;
        }
        $finalNotes = implode("\n\n", $combinedNotesParts);

        // 4. Atomic Database Transaction: Save booking
        $booking = \Illuminate\Support\Facades\DB::transaction(function() use (
            $request, $submittedPkgTitle, $matchedCategoryTitle, $submittedTierName, 
            $verifiedBasePrice, $verifiedAddonsTotal, $grandTotal, $matchedImage, 
            $verifiedAddonsList, $finalNotes, $userId, $userEmail, $matchedPackageId
        ) {
            // Find or create customer atomically (enforcing mobile uniqueness)
            $phone = preg_replace('/[^0-9]/', '', $request->input('mobile') ?? $request->input('whatsapp') ?? '');
            if (empty($phone)) $phone = '9131668156';

            $customer = Customer::firstOrCreate(
                ['mobile' => $phone],
                [
                    'name' => trim($request->input('name')),
                    'whatsapp' => trim($request->input('whatsapp', $phone)),
                    'email' => $userEmail ?: null,
                    'address' => trim($request->input('address')),
                    'area' => trim($request->input('area')),
                    'city' => trim($request->input('city', 'Indore')),
                    'state' => trim($request->input('state', 'Madhya Pradesh')),
                    'pincode' => trim($request->input('pincode')),
                ]
            );

            // Generate collision-resistant unique Booking ID (2+ billion combinations)
            do {
                $newId = 'BK-' . strtoupper(Str::random(6));
            } while (Booking::where('id', $newId)->exists());

            $newBooking = [
                'id' => $newId,
                'user_id' => $userId,
                'customer_id' => $customer->id,
                'package_id' => $matchedPackageId,
                'name' => trim($request->input('name')),
                'mobile' => $phone,
                'whatsapp' => trim($request->input('whatsapp', $phone)),
                'email' => trim($userEmail),
                'package' => $submittedPkgTitle,
                'category' => $matchedCategoryTitle,
                'tier' => $submittedTierName,
                'date' => $request->input('event_date'),
                'time' => $request->input('event_time'),
                'guest_count' => (int)$request->input('guest_count', 25),
                'address' => trim($request->input('address')),
                'landmark' => trim($request->input('landmark', '')),
                'area' => trim($request->input('area')),
                'city' => trim($request->input('city', 'Indore')),
                'state' => trim($request->input('state', 'Madhya Pradesh')),
                'pincode' => trim($request->input('pincode')),
                'notes' => $finalNotes,
                'addons' => $verifiedAddonsList,
                'package_price' => $verifiedBasePrice,
                'surcharge' => $verifiedAddonsTotal,
                'total' => $grandTotal,
                'status' => 'Pending',
                'notifications_sent' => ['creation'],
                'created_at' => date('d M Y, h:i A')
            ];

            // 1. Direct Eloquent insert: Single record, zero full-table rewrites
            Booking::create([
                'id' => $newId,
                'user_id' => $userId,
                'customer_id' => $customer->id,
                'package_id' => $matchedPackageId,
                'customer_name' => trim($request->input('name')),
                'customer_mobile' => $phone,
                'customer_whatsapp' => trim($request->input('whatsapp', $phone)),
                'customer_email' => $userEmail ?: null,
                'package_name' => $submittedPkgTitle,
                'category_name' => $matchedCategoryTitle,
                'tier_name' => $submittedTierName,
                'event_date' => $request->input('event_date'),
                'event_time' => $request->input('event_time'),
                'guest_count' => (int)$request->input('guest_count', 25),
                'address' => trim($request->input('address')),
                'landmark' => trim($request->input('landmark', '')),
                'area' => trim($request->input('area')),
                'city' => trim($request->input('city', 'Indore')),
                'state' => trim($request->input('state', 'Madhya Pradesh')),
                'pincode' => trim($request->input('pincode')),
                'notes' => $finalNotes,
                'addons' => $verifiedAddonsList,
                'package_price' => $verifiedBasePrice,
                'surcharge_amount' => $verifiedAddonsTotal,
                'total_amount' => $grandTotal,
                'status' => 'Pending',
                'notifications_sent' => ['creation'],
            ]);

            // Update customer totals atomically
            $customer->increment('total_bookings');
            $customer->increment('total_spend', $grandTotal);

            // 2. Synchronize single booking to JSON fallback without rewriting all DB rows
            JsonStorageService::appendBookingRecord($newBooking);

            return $newBooking;
        });

        $newId = $booking['id'];
        \Illuminate\Support\Facades\Cache::forget('admin_pending_bookings_count');

        // Store in session for instant order history lookup
        session([
            'last_booking_id' => $newId,
            'last_booking_phone' => $booking['mobile'] ?? '',
        ]);

        // 5. Dispatch notifications with failure protection
        try {
            if (!empty($booking['email'])) {
                \Illuminate\Support\Facades\Notification::route('mail', $booking['email'])
                    ->notify(new \App\Notifications\BookingReceivedNotification($booking));
            }
            \Illuminate\Support\Facades\Notification::route('mail', config('mail.from.address', 'admin@artizenevents.com'))
                ->notify(new \App\Notifications\NewBookingAdminNotification($booking));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Booking creation email notification error: ' . $e->getMessage());
        }

        try {
            $waSmsService = new \App\Services\WhatsAppSmsService();
            $waSmsService->sendCustomerBookingReceived($booking);
            $waSmsService->sendAdminNewBooking($booking);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Booking creation WhatsApp/SMS notification error: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'booking_id' => $newId,
                'total' => $grandTotal,
                'redirect' => route('booking.success', ['id' => $newId])
            ]);
        }

        return redirect()->route('booking.success', ['id' => $newId]);
    }

    /**
     * Display booking confirmation success page.
     */
    public function success(Request $request)
    {
        $id = $request->query('id', '');
        $booking = null;

        if (!empty($id)) {
            $bookings = JsonStorageService::read('bookings.json');
            foreach ($bookings as $b) {
                if (($b['id'] ?? '') === $id) {
                    $booking = $b;
                    break;
                }
            }
        }

        if (!$booking && !empty($id)) {
            abort(404, 'Booking Reference ID Not Found');
        }

        if (!$booking) {
            $booking = [
                'id' => 'BK-8844',
                'name' => 'Valued Customer',
                'package' => 'Celebration Package',
                'category' => 'Event Setup',
                'date' => date('Y-m-d', strtotime('+3 days')),
                'time' => '18:00',
                'city' => 'Indore',
                'area' => 'Vijay Nagar',
                'address' => 'Indore, Madhya Pradesh',
                'total' => 4999,
                'status' => 'Pending',
                'created_at' => date('d M Y')
            ];
        }

        if (empty($booking['image'])) {
            $allPackages = CelebrationCatalogService::getAllPackages();
            foreach ($allPackages as $p) {
                if (strcasecmp($p['title'] ?? '', $booking['package'] ?? '') === 0 || str_contains(strtolower($booking['package'] ?? ''), strtolower($p['title'] ?? ''))) {
                    $booking['image'] = $p['image'] ?? '/assets/images/hero/1.jpg';
                    break;
                }
            }
            if (empty($booking['image'])) {
                $booking['image'] = '/assets/images/hero/1.jpg';
            }
        }

        return view('booking.success', compact('booking'));
    }
}

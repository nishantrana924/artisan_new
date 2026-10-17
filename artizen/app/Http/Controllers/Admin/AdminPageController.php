<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\JsonStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Services\ImageOptimizationService;

/**
 * AdminPageController
 * Handles individual admin section pages.
 * Each page gets a full data payload so the shared JS works everywhere.
 */
class AdminPageController extends Controller
{
    /**
     * Base empty payload structure to ensure shared layout and legacy scripts have safe defaults.
     */
    protected function baseData(): array
    {
        return [
            'bookings'   => [],
            'packages'   => [],
            'categories' => [],
            'faqs'       => [],
            'artists'    => [],
            'cms'        => [
                'hero_slides' => [],
                'about'       => ['badge' => '', 'title' => '', 'desc' => '', 'cards' => [], 'images' => []],
            ],
            'settings'   => [],
            'legalPages' => [],
            'analytics'  => [],
        ];
    }

    /**
     * Legacy helper kept for backward compatibility with tests/external callers.
     * Analytics computation is intentionally disabled here to prevent slow execution on non-dashboard routes.
     */
    protected function allData(Request $request): array
    {
        $bookings   = JsonStorageService::read('bookings.json', []);
        $packages   = JsonStorageService::read('packages.json', []);
        $categories = JsonStorageService::read('categories.json', []);
        $faqs       = JsonStorageService::read('faqs.json', []);
        $artists    = JsonStorageService::read('artists.json', []);
        $cms        = JsonStorageService::read('cms.json', []);
        if (empty($cms['hero_slides'])) {
            $cms['hero_slides'] = [
                [
                    'image' => '/assets/images/hero/1.jpg',
                    'badge' => 'PLAN, BOOK & RELAX',
                    'title' => 'ELEVATE YOUR CELEBRATIONS',
                    'desc' => 'Book complete premium decor, sound, and lighting setups instantly for birthdays, house parties, and sangeet.',
                    'link1' => '/events',
                    'btn1Text' => 'Explore Packages',
                    'link2' => 'https://wa.me/919131668156',
                    'btn2Text' => 'WhatsApp Inquiry'
                ]
            ];
        }
        if (empty($cms['about'])) {
            $cms['about'] = [
                'badge'  => '',
                'title'  => '',
                'desc'   => '',
                'cards'  => [],
                'images' => [],
            ];
        }
        $settings   = JsonStorageService::read('settings.json', []);

        $defaultLegalPages = app(\App\Http\Controllers\LegalController::class)->getDefaultLegalPages();
        $legalPages        = JsonStorageService::read('legal_pages.json', $defaultLegalPages);

        $analytics = []; // Kept on dashboard routes only

        return compact(
            'bookings', 'packages', 'categories', 'faqs',
            'artists', 'cms', 'settings', 'legalPages', 'analytics'
        );
    }

    // ---------------------------------------------------------------------------
    // Bookings page
    // ---------------------------------------------------------------------------
    public function bookings(Request $request)
    {
        $bookings   = JsonStorageService::read('bookings.json', []);
        $packages   = JsonStorageService::read('packages.json', []);
        $categories = JsonStorageService::read('categories.json', []);
        $faqs       = JsonStorageService::read('faqs.json', []);

        return view('admin.pages.bookings', array_merge(
            $this->baseData(),
            [
                'adminActivePage' => 'bookings',
                'bookings'        => $bookings,
                'packages'        => $packages,
                'categories'      => $categories,
                'faqs'            => $faqs,
            ]
        ));
    }

    // ---------------------------------------------------------------------------
    // Packages page
    // ---------------------------------------------------------------------------
    public function packages(Request $request)
    {
        $dbPackages = \App\Models\Package::with(['category', 'subcategories', 'tiers'])->orderBy('id')->get();
        $dbCategories = \App\Models\Category::with(['activeSubcategories'])->ordered()->get();

        $totalPackages = $dbPackages->count();
        $publishedCount = $dbPackages->where('active', true)->count();
        $draftCount = $dbPackages->where('active', false)->count();
        $categoriesCount = $dbPackages->pluck('category_id')->filter()->unique()->count();

        $allPrices = [];
        foreach ($dbPackages as $p) {
            foreach ($p->tiers as $t) {
                if ((float)$t->price > 0) {
                    $allPrices[] = (float)$t->price;
                }
            }
        }
        $avgPrice = !empty($allPrices) ? (int)round(array_sum($allPrices) / count($allPrices)) : 0;
        $minPrice = !empty($allPrices) ? (int)min($allPrices) : 0;

        return view('admin.pages.packages', array_merge(
            $this->baseData(),
            [
                'adminActivePage' => 'packages',
                'dbPackages'      => $dbPackages,
                'dbCategories'    => $dbCategories,
                'totalPackages'   => $totalPackages,
                'publishedCount'  => $publishedCount,
                'draftCount'      => $draftCount,
                'categoriesCount' => $categoriesCount,
                'avgPrice'        => $avgPrice,
                'minPrice'        => $minPrice,
            ]
        ));
    }

    // ---------------------------------------------------------------------------
    // Package Create page
    // ---------------------------------------------------------------------------
    public function packageCreate(Request $request)
    {
        $categories = \App\Models\Category::with(['activeSubcategories'])->ordered()->get();
        $recipientsList = \App\Models\CelebrationRecipient::active()->ordered()->get();
        $package = new \App\Models\Package([
            'active' => true,
            'tag'    => 'decor',
        ]);

        return view('admin.pages.package-form', array_merge(
            $this->baseData(),
            [
                'adminActivePage'    => 'packages',
                'categories'         => $categories,
                'recipientsList'     => $recipientsList,
                'package'            => $package,
                'isEdit'             => false,
                'selectedSubcatIds'  => [],
                'selectedRecipients' => [],
            ]
        ));
    }

    // ---------------------------------------------------------------------------
    // Package Edit page
    // ---------------------------------------------------------------------------
    public function packageEdit(Request $request, $id)
    {
        $categories = \App\Models\Category::with(['activeSubcategories'])->ordered()->get();
        $recipientsList = \App\Models\CelebrationRecipient::active()->ordered()->get();
        $package = \App\Models\Package::with(['category', 'subcategories', 'tiers'])->findOrFail($id);

        return view('admin.pages.package-form', array_merge(
            $this->baseData(),
            [
                'adminActivePage'    => 'packages',
                'categories'         => $categories,
                'recipientsList'     => $recipientsList,
                'package'            => $package,
                'isEdit'             => true,
                'selectedSubcatIds'  => $package->subcategories->pluck('id')->toArray(),
                'selectedRecipients' => (array)($package->recipients ?? []),
            ]
        ));
    }

    // ---------------------------------------------------------------------------
    // Package Edit Legacy redirect
    // ---------------------------------------------------------------------------
    public function packageEditLegacy(Request $request, $catId, $tierIdx)
    {
        $package = \App\Models\Package::where('category_id', $catId)->first();
        if ($package) {
            return redirect()->route('admin.packages.edit', $package->id);
        }
        return redirect()->route('admin.packages');
    }

    // ---------------------------------------------------------------------------
    // Categories page
    // ---------------------------------------------------------------------------
    public function categories(Request $request)
    {
        // Load categories with eager-loaded subcategories for the admin redesign
        $categoriesWithSubs = \App\Models\Category::with(['subcategories'])->orderBy('display_order')->orderBy('id')->get();
        return view('admin.pages.categories', array_merge(
            $this->baseData(),
            [
                'adminActivePage'    => 'categories',
                'categoriesWithSubs' => $categoriesWithSubs,
            ]
        ));
    }

    // ---------------------------------------------------------------------------
    // Category Create page
    // ---------------------------------------------------------------------------
    public function categoryCreate(Request $request)
    {
        $maxOrder = (int) \App\Models\Category::max('display_order');
        $category = new \App\Models\Category([
            'bg_color'      => '#F6CFB2',
            'display_order' => $maxOrder + 1,
            'active'        => true,
            'icon'          => 'fa-solid fa-cake-candles',
        ]);

        return view('admin.pages.category-form', array_merge(
            $this->baseData(),
            [
                'adminActivePage' => 'categories',
                'category'        => $category,
                'isEdit'          => false,
            ]
        ));
    }

    // ---------------------------------------------------------------------------
    // Category Edit page
    // ---------------------------------------------------------------------------
    public function categoryEdit(Request $request, $id)
    {
        $category = \App\Models\Category::with(['subcategories'])->findOrFail($id);

        return view('admin.pages.category-form', array_merge(
            $this->baseData(),
            [
                'adminActivePage' => 'categories',
                'category'        => $category,
                'isEdit'          => true,
            ]
        ));
    }

    // ---------------------------------------------------------------------------
    // Customers page
    // ---------------------------------------------------------------------------
    public function customers(Request $request)
    {
        $dbCustomers = \App\Models\Customer::with(['bookings' => function($q) {
            $q->orderBy('created_at', 'desc');
        }])->orderBy('id', 'desc')->get();

        $bookings   = JsonStorageService::read('bookings.json', []);
        $packages   = JsonStorageService::read('packages.json', []);
        $categories = JsonStorageService::read('categories.json', []);
        $faqs       = JsonStorageService::read('faqs.json', []);

        $customersList = $dbCustomers->map(function($c) {
            $realBookings = $c->bookings->map(function($b) {
                return [
                    'id'            => $b->id,
                    'package_name'  => $b->package_name,
                    'category_name' => $b->category_name,
                    'tier_name'     => $b->tier_name,
                    'event_date'    => $b->event_date,
                    'event_time'    => $b->event_time,
                    'guest_count'   => $b->guest_count,
                    'address'       => $b->address,
                    'area'          => $b->area,
                    'city'          => $b->city,
                    'total_amount'  => (float)$b->total_amount,
                    'status'        => $b->status,
                    'created_at'    => $b->created_at ? $b->created_at->format('d M Y, h:i A') : '',
                ];
            });

            $totalSpend = $c->bookings->whereNotIn('status', ['Cancelled'])->sum('total_amount');

            return [
                'id'             => $c->id,
                'name'           => $c->name,
                'mobile'         => $c->mobile,
                'whatsapp'       => $c->whatsapp ?: $c->mobile,
                'email'          => $c->email ?: null, // real email or null - NEVER fabricated!
                'address'        => $c->address ?: '',
                'area'           => $c->area ?: '',
                'city'           => $c->city ?: 'Indore',
                'state'          => $c->state ?: 'Madhya Pradesh',
                'pincode'        => $c->pincode ?: '',
                'total_bookings' => $c->bookings->count(),
                'total_spend'    => (float)$totalSpend,
                'created_at'     => $c->created_at ? $c->created_at->format('d M Y, h:i A') : '',
                'bookings'       => $realBookings,
            ];
        })->toArray();

        return view('admin.pages.customers', array_merge(
            $this->baseData(),
            [
                'adminActivePage' => 'customers',
                'dbCustomers'     => $dbCustomers,
                'customersList'   => $customersList,
                'bookings'        => $bookings,
                'packages'        => $packages,
                'categories'      => $categories,
                'faqs'            => $faqs,
            ]
        ));
    }

    public function customerDetail(Request $request, $id)
    {
        $customer = \App\Models\Customer::with(['bookings' => function($q) {
            $q->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'customer' => $customer,
            ]);
        }

        return redirect()->route('admin.customers', ['id' => $customer->id]);
    }

    // ---------------------------------------------------------------------------
    // FAQ & Reviews page
    // ---------------------------------------------------------------------------
    public function testimonials(Request $request)
    {
        $faqs       = JsonStorageService::read('faqs.json', []);
        $categories = JsonStorageService::read('categories.json', []);
        $packages   = JsonStorageService::read('packages.json', []);
        $bookings   = JsonStorageService::read('bookings.json', []);

        return view('admin.pages.testimonials', array_merge(
            $this->baseData(),
            [
                'adminActivePage' => 'testimonials',
                'faqs'            => $faqs,
                'categories'      => $categories,
                'packages'        => $packages,
                'bookings'        => $bookings,
            ]
        ));
    }

    // ---------------------------------------------------------------------------
    // Hero Banners page
    // ---------------------------------------------------------------------------
    public function hero(Request $request)
    {
        $cms = JsonStorageService::read('cms.json', []);
        $slides = $cms['hero_slides'] ?? [];
        if (empty($slides)) {
            $slides = [
                [
                    'id'           => 1,
                    'image'        => '/assets/images/hero/1.jpg',
                    'badge'        => 'PLAN, BOOK & RELAX',
                    'title'        => 'ELEVATE YOUR CELEBRATIONS',
                    'desc'         => 'Book complete premium decor, sound, and lighting setups instantly for birthdays, house parties, and sangeet.',
                    'link1'        => '/events',
                    'btn1Text'     => 'Explore Packages',
                    'link2'        => 'https://wa.me/919131668156',
                    'btn2Text'     => 'WhatsApp Inquiry',
                    'display_order'=> 0,
                    'is_active'    => true,
                ]
            ];
        }

        return view('admin.pages.hero', array_merge(
            $this->baseData(),
            [
                'adminActivePage' => 'hero-manager',
                'slides'          => $slides,
                'cms'             => $cms,
            ]
        ));
    }

    // ---------------------------------------------------------------------------
    // Offers Section page
    // ---------------------------------------------------------------------------
    public function offers(Request $request)
    {
        $cms = JsonStorageService::read('cms.json', []);
        $offersConfig = $cms['offers'] ?? [
            'section_title' => 'Save More with Offers',
            'is_active'     => true,
            'items'         => [],
        ];

        return view('admin.pages.offers', array_merge(
            $this->baseData(),
            [
                'adminActivePage' => 'offers-manager',
                'offersConfig'    => $offersConfig,
                'cms'             => $cms,
            ]
        ));
    }

    // ---------------------------------------------------------------------------
    // Promise Banner page
    // ---------------------------------------------------------------------------
    public function promiseBanner(Request $request)
    {
        $cms = JsonStorageService::read('cms.json', []);
        $promiseBanner = $cms['promise_banner'] ?? [
            'is_active' => true,
            'image'     => '/images/banners/artizen-promise-banner.webp',
            'title'     => 'Artizen — Curating Unforgettable Moments, Luxury Weddings & Celebrations',
            'link'      => '/events',
        ];

        return view('admin.pages.promise-banner', array_merge(
            $this->baseData(),
            [
                'adminActivePage' => 'promise-banner-manager',
                'promiseBanner'   => $promiseBanner,
                'cms'             => $cms,
            ]
        ));
    }

    // ---------------------------------------------------------------------------
    // Legal Pages
    // ---------------------------------------------------------------------------
    public function legal(Request $request)
    {
        $defaultLegalPages = app(\App\Http\Controllers\LegalController::class)->getDefaultLegalPages();
        $legalPages        = JsonStorageService::read('legal_pages.json', $defaultLegalPages);

        return view('admin.pages.legal', array_merge(
            $this->baseData(),
            [
                'adminActivePage' => 'legal-manager',
                'legalPages'      => $legalPages,
            ]
        ));
    }

    /**
     * Resolve the current authenticated administrator user.
     */
    public function getAdminUser(): ?User
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return Auth::user();
        }

        $sessionEmail = session('admin_email');
        if (session('admin_logged_in') === true && $sessionEmail) {
            $user = User::where('email', $sessionEmail)->first();
            if ($user && $user->isAdmin()) {
                Auth::login($user);
                return $user;
            }
        }

        return null;
    }

    // ---------------------------------------------------------------------------
    // Settings page
    // ---------------------------------------------------------------------------
    public function settings(Request $request)
    {
        // Purge legacy SEO / surcharges data from settings database table
        try {
            DB::table('settings')->whereIn('key', ['meta_title', 'meta_description', 'meta_keywords', 'surcharges', 'seo'])->delete();
        } catch (\Throwable $e) {}

        $adminUser = $this->getAdminUser();
        if (!$adminUser) {
            return redirect()->route('login', ['redirect' => route('admin.settings')])
                ->with('error', 'Administrator authentication required.');
        }

        $settings = JsonStorageService::read('settings.json', []);

        return view('admin.pages.settings', array_merge(
            $this->baseData(),
            [
                'adminActivePage' => 'settings',
                'adminUser'       => $adminUser,
                'settings'        => $settings,
            ]
        ));
    }

    /**
     * Update administrator profile details (Name, Email, Avatar Image Upload).
     */
     public function updateProfile(Request $request)
     {
         $admin = $this->getAdminUser();
         if (!$admin) {
             return redirect()->route('admin.settings')->with('error', 'Administrator account not found.');
         }
 
         $validated = $request->validate([
             'name'          => ['required', 'string', 'max:100'],
             'email'         => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin->id)],
             'avatar_file'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
             'remove_avatar' => ['nullable'],
             'avatar'        => ['nullable', 'string', 'max:500'],
         ], [
             'name.required'     => 'Administrator name is required.',
             'email.required'    => 'Email address is required.',
             'email.unique'      => 'This email address is already in use by another user.',
             'avatar_file.image' => 'The uploaded file must be an image.',
             'avatar_file.mimes' => 'Avatar must be a valid image format (JPEG, PNG, WEBP, or GIF).',
             'avatar_file.max'   => 'Avatar image size cannot exceed 5MB.',
         ]);
 
         $admin->name = trim($validated['name']);
         $admin->email = strtolower(trim($validated['email']));
 
         // 1. Check if user requested to remove avatar
         if ($request->filled('remove_avatar') && $request->input('remove_avatar') == '1') {
             if (!empty($admin->avatar) && str_starts_with($admin->avatar, '/uploads/') && file_exists(public_path(ltrim($admin->avatar, '/')))) {
                 @unlink(public_path(ltrim($admin->avatar, '/')));
             }
             $admin->avatar = null;
         }
 
         // 2. Handle new avatar upload with WebP auto-compression and optimization
         if ($request->hasFile('avatar_file')) {
             $webpAvatar = $this->compressAndSaveWebpAvatar($request->file('avatar_file'), $admin->id);
             if ($webpAvatar) {
                 if (!empty($admin->avatar) && str_starts_with($admin->avatar, '/uploads/') && file_exists(public_path(ltrim($admin->avatar, '/')))) {
                     @unlink(public_path(ltrim($admin->avatar, '/')));
                 }
                 $admin->avatar = $webpAvatar;
             }
         } elseif ($request->filled('avatar') && (!$request->filled('remove_avatar') || $request->input('remove_avatar') != '1')) {
             $admin->avatar = trim($validated['avatar']);
         }
 
         $admin->save();
 
         session(['admin_email' => $admin->email]);
 
         return redirect()->route('admin.settings')->with('success', 'Profile details updated successfully!');
     }
 
     /**
      * Process, resize, and compress an uploaded image into an optimized WebP file in public/uploads.
      */
     protected function compressAndSaveWebpAvatar($file, int|string $userId): ?string
     {
         try {
             return app(ImageOptimizationService::class)->optimize(
                 file: $file,
                 targetSubdir: 'uploads',
                 profile: 'avatar',
                 prefix: 'avatar_' . $userId,
                 quality: ImageOptimizationService::DEFAULT_QUALITY
             );
         } catch (\Throwable $e) {
             Log::warning('Avatar optimization failed: ' . $e->getMessage());
             return null;
         }
     }

    /**
     * Update administrator password with current password verification.
     */
    public function updatePassword(Request $request)
    {
        $admin = $this->getAdminUser();
        if (!$admin) {
            return redirect()->route('admin.settings')->with('error', 'Administrator account not found.');
        }

        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Current password is required.',
            'password.required'         => 'New password is required.',
            'password.min'              => 'New password must be at least 8 characters.',
            'password.confirmed'        => 'Password confirmation does not match.',
        ]);

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors([
                'current_password' => 'The provided current password does not match your existing password.',
            ])->withInput();
        }

        $admin->password = Hash::make($request->password);
        $admin->save();

        return redirect()->route('admin.settings')->with('success', 'Password updated successfully! Keep your credentials safe.');
    }

    /**
     * Send OTP verification code to the connected administrator email.
     */
    public function sendResetOtp(Request $request)
    {
        $admin = $this->getAdminUser();
        if (!$admin) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Administrator account not found.'], 404);
            }
            return redirect()->route('admin.settings')->with('error', 'Administrator account not found.');
        }

        // Rapid dispatch cooldown: prevent sending OTP more than once every 60 seconds per admin
        $cooldownKey = 'admin_panel_otp_send_cooldown_' . md5($admin->email);
        if (\Illuminate\Support\Facades\Cache::has($cooldownKey)) {
            $msg = 'Please wait 60 seconds before requesting another verification code.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => $msg], 429);
            }
            return redirect()->route('admin.settings', ['#forgot'])->withErrors(['email' => $msg]);
        }

        $otp = (string) random_int(100000, 999999);

        session([
            'admin_reset_otp'            => $otp,
            'admin_reset_otp_expires_at' => now()->addMinutes(10)->timestamp,
            'admin_reset_otp_email'      => $admin->email,
            'admin_reset_otp_verified'   => false,
        ]);

        \Illuminate\Support\Facades\Cache::put($cooldownKey, true, 60);

        try {
            Mail::raw(
                "Hello {$admin->name},\n\nYour Artizen administrator password reset verification code (OTP) is: {$otp}\n\nThis verification code is valid for 10 minutes.\n\nIf you did not request this verification code, please ignore this email.\n\nArtizen Platform Security",
                function ($message) use ($admin) {
                    $message->to($admin->email)
                        ->subject("Artizen Admin: Password Reset Code");
                }
            );
        } catch (\Throwable $e) {
            Log::error('Admin Reset OTP mail delivery error: ' . $e->getMessage());
        }

        Log::info("Admin Password Reset OTP generated and dispatched for {$admin->email}.");

        $message = "A 6-digit verification code has been sent to {$admin->email}.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => $message,
                'email'   => $admin->email,
            ]);
        }

        return redirect()->route('admin.settings', ['#forgot'])
            ->with('success', $message)
            ->with('otp_sent', true);
    }

    /**
     * Verify the 6-digit OTP code before allowing the new password form to be filled.
     */
    public function verifyResetOtp(Request $request)
    {
        $admin = $this->getAdminUser();
        if (!$admin) {
            return response()->json(['status' => 'error', 'message' => 'Administrator account not found.'], 404);
        }

        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'The 6-digit OTP code is required.',
            'otp.size'     => 'The OTP code must be exactly 6 digits.',
        ]);

        $storedOtp   = session('admin_reset_otp');
        $expiresAt   = session('admin_reset_otp_expires_at');
        $storedEmail = session('admin_reset_otp_email');

        // Check verification brute-force attempt counter (max 5 failed attempts)
        $failsKey = 'admin_panel_otp_verify_fails_' . md5($admin->email);
        $failedAttempts = (int) \Illuminate\Support\Facades\Cache::get($failsKey, 0);

        if ($failedAttempts >= 5) {
            session()->forget([
                'admin_reset_otp',
                'admin_reset_otp_expires_at',
                'admin_reset_otp_email',
                'admin_reset_otp_verified',
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Too many failed verification attempts. This verification code has been invalidated. Please request a new code.',
            ], 429);
        }

        if (!$storedOtp || !$expiresAt || now()->timestamp > $expiresAt || $storedEmail !== $admin->email) {
            return response()->json([
                'status'  => 'error',
                'message' => 'The verification code has expired or has not been requested yet. Please click "Send OTP Code".',
            ], 422);
        }

        if (!hash_equals((string) $storedOtp, trim($request->otp))) {
            $failedAttempts++;
            \Illuminate\Support\Facades\Cache::put($failsKey, $failedAttempts, 600); // 10 minutes
            if ($failedAttempts >= 5) {
                session()->forget([
                    'admin_reset_otp',
                    'admin_reset_otp_expires_at',
                    'admin_reset_otp_email',
                    'admin_reset_otp_verified',
                ]);
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Too many failed verification attempts. This verification code has been invalidated. Please request a new code.',
                ], 429);
            }
            $remaining = 5 - $failedAttempts;
            return response()->json([
                'status'  => 'error',
                'message' => "Incorrect verification code. ({$remaining} " . ($remaining === 1 ? 'attempt' : 'attempts') . " remaining).",
            ], 422);
        }

        \Illuminate\Support\Facades\Cache::forget($failsKey);
        session(['admin_reset_otp_verified' => true]);

        return response()->json([
            'status'  => 'success',
            'message' => 'OTP verified successfully! You can now set your new password.',
        ]);
    }

    /**
     * Emergency in-session password reset verifying OTP sent to connected email.
     */
    public function emergencyPasswordReset(Request $request)
    {
        $admin = $this->getAdminUser();
        if (!$admin) {
            return redirect()->route('admin.settings')->with('error', 'Administrator account not found.');
        }

        $request->validate([
            'otp'          => ['nullable', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'new_password.required'  => 'New password is required.',
            'new_password.min'       => 'New password must be at least 8 characters.',
            'new_password.confirmed' => 'Password confirmation does not match.',
        ]);

        $isVerified  = session('admin_reset_otp_verified') === true;
        $storedOtp   = session('admin_reset_otp');
        $expiresAt   = session('admin_reset_otp_expires_at');
        $storedEmail = session('admin_reset_otp_email');

        if (!$isVerified) {
            if (!$storedOtp || !$expiresAt || now()->timestamp > $expiresAt || $storedEmail !== $admin->email) {
                return back()->withErrors([
                    'otp' => 'The verification code has expired or has not been requested yet. Please click "Send OTP Code".',
                ])->withInput();
            }

            if (!$request->filled('otp') || !hash_equals((string) $storedOtp, trim($request->otp))) {
                return back()->withErrors([
                    'otp' => 'Please verify your OTP code before setting your new password.',
                ])->withInput();
            }
        }

        // Clear all OTP session variables
        session()->forget([
            'admin_reset_otp',
            'admin_reset_otp_expires_at',
            'admin_reset_otp_email',
            'admin_reset_otp_verified',
        ]);

        $admin->password = Hash::make($request->new_password);
        $admin->save();

        return redirect()->route('admin.settings', ['#security'])
            ->with('success', 'Password reset successfully with OTP verification! Your new password is now active.');
    }
}

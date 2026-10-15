<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\JsonStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * AdminPageController
 * Handles individual admin section pages.
 * Each page gets a full data payload so the shared JS works everywhere.
 */
class AdminPageController extends Controller
{
    /**
     * Load all shared data required by the JS in every page.
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
        $settings   = JsonStorageService::read('settings.json', []);

        $defaultLegalPages = app(\App\Http\Controllers\LegalController::class)->getDefaultLegalPages();
        $legalPages        = JsonStorageService::read('legal_pages.json', $defaultLegalPages);

        $analytics = app(DashboardController::class)->calculateAnalytics($bookings, $request);

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
        return view('admin.pages.bookings', array_merge(
            $this->allData($request),
            ['adminActivePage' => 'bookings']
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
            $this->allData($request),
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
            $this->allData($request),
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
            $this->allData($request),
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
            $this->allData($request),
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
            $this->allData($request),
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
            $this->allData($request),
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
        return view('admin.pages.customers', array_merge(
            $this->allData($request),
            ['adminActivePage' => 'customers']
        ));
    }

    // ---------------------------------------------------------------------------
    // FAQ & Reviews page
    // ---------------------------------------------------------------------------
    public function testimonials(Request $request)
    {
        return view('admin.pages.testimonials', array_merge(
            $this->allData($request),
            ['adminActivePage' => 'testimonials']
        ));
    }

    // ---------------------------------------------------------------------------
    // Hero Slider page
    // ---------------------------------------------------------------------------
    public function hero(Request $request)
    {
        return view('admin.pages.hero', array_merge(
            $this->allData($request),
            ['adminActivePage' => 'hero-manager']
        ));
    }



    // ---------------------------------------------------------------------------
    // Legal Pages
    // ---------------------------------------------------------------------------
    public function legal(Request $request)
    {
        return view('admin.pages.legal', array_merge(
            $this->allData($request),
            ['adminActivePage' => 'legal-manager']
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
        if ($sessionEmail) {
            $user = User::where('email', $sessionEmail)->first();
            if ($user) {
                Auth::login($user);
                return $user;
            }
        }

        $admin = User::where('is_admin', true)->first();
        if ($admin) {
            Auth::login($admin);
            return $admin;
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

        return view('admin.pages.settings', array_merge(
            $this->allData($request),
            [
                'adminActivePage' => 'settings',
                'adminUser'       => $adminUser,
            ]
        ));
    }

    /**
     * Update administrator profile details (Name, Email, Avatar).
     */
    public function updateProfile(Request $request)
    {
        $admin = $this->getAdminUser();
        if (!$admin) {
            return redirect()->route('admin.settings')->with('error', 'Administrator account not found.');
        }

        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:100'],
            'email'  => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin->id)],
            'avatar' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required'  => 'Administrator name is required.',
            'email.required' => 'Email address is required.',
            'email.unique'   => 'This email address is already in use by another user.',
        ]);

        $admin->name = trim($validated['name']);
        $admin->email = strtolower(trim($validated['email']));
        if (array_key_exists('avatar', $validated)) {
            $admin->avatar = $validated['avatar'] ? trim($validated['avatar']) : null;
        }
        $admin->save();

        session(['admin_email' => $admin->email]);

        return redirect()->route('admin.settings')->with('success', 'Profile details updated successfully!');
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
     * Emergency in-session password reset when admin forgets existing password.
     */
    public function emergencyPasswordReset(Request $request)
    {
        $admin = $this->getAdminUser();
        if (!$admin) {
            return redirect()->route('admin.settings')->with('error', 'Administrator account not found.');
        }

        $request->validate([
            'admin_confirmation' => ['required', 'string'],
            'new_password'       => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'admin_confirmation.required' => 'Please confirm your administrator email or type RESET.',
            'new_password.required'       => 'New password is required.',
            'new_password.min'            => 'New password must be at least 8 characters.',
            'new_password.confirmed'      => 'Password confirmation does not match.',
        ]);

        $confirmation = trim($request->admin_confirmation);
        $expectedEmail = trim($admin->email);

        if (strcasecmp($confirmation, $expectedEmail) !== 0 && strtoupper($confirmation) !== 'RESET') {
            return back()->withErrors([
                'admin_confirmation' => "Security verification failed. Please enter \"{$expectedEmail}\" or \"RESET\" to confirm authorization.",
            ])->withInput();
        }

        $admin->password = Hash::make($request->new_password);
        $admin->save();

        return redirect()->route('admin.settings')->with('success', 'Emergency password reset completed! Your new password has been set.');
    }
}

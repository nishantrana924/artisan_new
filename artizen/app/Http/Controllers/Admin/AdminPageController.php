<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\JsonStorageService;
use Illuminate\Http\Request;

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
    // About Page
    // ---------------------------------------------------------------------------
    public function about(Request $request)
    {
        return view('admin.pages.about', array_merge(
            $this->allData($request),
            ['adminActivePage' => 'about-manager']
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

    // ---------------------------------------------------------------------------
    // Settings page
    // ---------------------------------------------------------------------------
    public function settings(Request $request)
    {
        return view('admin.pages.settings', array_merge(
            $this->allData($request),
            ['adminActivePage' => 'settings']
        ));
    }
}

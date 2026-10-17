<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Package;
use App\Models\PackageTier;
use App\Models\Subcategory;
use App\Models\User;
use App\Services\CelebrationCatalogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PerformancePhase4Step2Test extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customerUser;
    protected Category $category;
    protected Package $package;
    protected PackageTier $tier;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        CelebrationCatalogService::clearCache();

        $this->admin = User::create([
            'name'     => 'Artizen Perf Admin',
            'email'    => 'admin@artizen.com',
            'password' => Hash::make('AdminPass123!'),
        ]);
        $this->admin->is_admin = true;
        $this->admin->save();

        $this->customerUser = User::create([
            'name'     => 'Perf Customer',
            'email'    => 'perf.customer@example.com',
            'password' => Hash::make('CustomerPass123!'),
        ]);

        $this->category = Category::create([
            'title'         => 'Birthday Celebrations',
            'slug'          => 'birthday-celebrations',
            'nav_slug'      => 'cat-birthdays',
            'display_order' => 1,
            'active'        => true,
        ]);

        $this->package = Package::create([
            'category_id'    => $this->category->id,
            'title'          => 'Golden Birthday Cabana',
            'slug'           => 'golden-birthday-cabana',
            'description'    => 'Luxury birthday celebration setup.',
            'price'          => 4999,
            'original_price' => 5999,
            'badge'          => 'BESTSELLER',
            'tag'            => 'decor',
            'active'         => true,
        ]);

        $this->tier = PackageTier::create([
            'package_id'    => $this->package->id,
            'name'          => 'Standard Setup',
            'slug'          => 'golden-birthday-cabana-standard',
            'price'         => 4999,
            'description'   => 'Complete setup with balloons and fairy lights.',
            'inclusions'    => ['Balloons', 'Fairy Lights'],
            'active'        => true,
            'display_order' => 1,
        ]);
    }

    /**
     * Test that CelebrationCatalogService memoizes and caches packages to eliminate repeated queries.
     */
    public function test_catalog_packages_are_cached_and_memoized_in_memory(): void
    {
        // 1. Initial fetch warms the cache
        $packages1 = CelebrationCatalogService::getAllPackages();
        $this->assertNotEmpty($packages1);
        $this->assertEquals('Golden Birthday Cabana', $packages1[0]['title']);

        // 2. Direct database update that does not trigger Eloquent events
        DB::table('packages')->where('id', $this->package->id)->update(['title' => 'Bypassed Title']);

        // 3. Second fetch must return the cached/memoized data instantly without re-querying
        $packages2 = CelebrationCatalogService::getAllPackages();
        $this->assertEquals('Golden Birthday Cabana', $packages2[0]['title'], 'Must return memoized/cached package without querying DB');

        // 4. Explicit clearCache forces fresh read
        CelebrationCatalogService::clearCache();
        $packages3 = CelebrationCatalogService::getAllPackages();
        $this->assertEquals('Bypassed Title', $packages3[0]['title'], 'After clearCache, must query fresh data');
    }

    /**
     * Test that saving a package automatically invalidates the catalog cache.
     */
    public function test_catalog_cache_is_invalidated_when_package_is_saved(): void
    {
        // Warm cache
        CelebrationCatalogService::getAllPackages();

        // Admin updates package via Eloquent
        $this->package->title = 'Diamond Luxury Cabana';
        $this->package->save();

        // Catalog must immediately reflect the new title
        $freshPackages = CelebrationCatalogService::getAllPackages();
        $this->assertEquals('Diamond Luxury Cabana', $freshPackages[0]['title'], 'Cache must be invalidated upon package save');
    }

    /**
     * Test that saving a category automatically invalidates the catalog cache.
     */
    public function test_catalog_cache_is_invalidated_when_category_is_saved(): void
    {
        // Warm categories cache
        $cats = CelebrationCatalogService::getCategories();
        $this->assertEquals('Birthday Celebrations', $cats['cat-birthdays']['name']);

        // Update category via Eloquent
        $this->category->title = 'Royal Birthday Celebrations';
        $this->category->save();

        // Categories must immediately reflect the update
        $freshCats = CelebrationCatalogService::getCategories();
        $this->assertEquals('Royal Birthday Celebrations', $freshCats['cat-birthdays']['name'], 'Cache must be invalidated upon category save');
    }

    /**
     * Test that saving a package tier automatically invalidates the catalog cache.
     */
    public function test_catalog_cache_is_invalidated_when_package_tier_is_saved(): void
    {
        // Warm cache
        $packages = CelebrationCatalogService::getAllPackages();
        $this->assertEquals(4999, $packages[0]['tiers'][0]['price']);

        // Update tier price
        $this->tier->price = 7999;
        $this->tier->save();

        // Catalog must immediately reflect the new tier price
        $freshPackages = CelebrationCatalogService::getAllPackages();
        $this->assertEquals(7999, $freshPackages[0]['tiers'][0]['price'], 'Cache must be invalidated upon tier save');
    }

    /**
     * Test that saving or deleting a subcategory automatically invalidates the catalog cache.
     */
    public function test_catalog_cache_is_invalidated_when_subcategory_is_saved_or_deleted(): void
    {
        // Create subcategory and associate with package
        $subcat = Subcategory::create([
            'category_id'   => $this->category->id,
            'name'          => 'Initial Subcat',
            'slug'          => 'initial-subcat',
            'group_name'    => 'Setup Themes',
            'display_order' => 1,
            'is_active'     => true,
        ]);
        $this->package->subcategories()->attach($subcat->id);

        // Reset and warm cache
        CelebrationCatalogService::clearCache();
        $pkgs1 = CelebrationCatalogService::getAllPackages();
        $this->assertEquals('Initial Subcat', $pkgs1[0]['subcategory']);

        // Update subcategory name
        $subcat->name = 'Royal Canopy Setup';
        $subcat->save();

        // Fresh catalog read must reflect updated subcategory
        $pkgs2 = CelebrationCatalogService::getAllPackages();
        $this->assertEquals('Royal Canopy Setup', $pkgs2[0]['subcategory'], 'Cache must be invalidated upon subcategory save');

        // Delete subcategory
        $subcat->delete();

        // Fresh catalog read must not fail and reflect fallback or absence
        $pkgs3 = CelebrationCatalogService::getAllPackages();
        $this->assertNotEquals('Royal Canopy Setup', $pkgs3[0]['subcategory'], 'Cache must be invalidated upon subcategory delete');
    }

    /**
     * Test admin standalone pages load successfully with targeted data.
     */
    public function test_admin_pages_load_successfully_with_targeted_data(): void
    {
        $session = [
            'admin_logged_in' => true,
            'admin_email'     => $this->admin->email,
        ];

        // 1. Packages Page
        $resPackages = $this->actingAs($this->admin)->withSession($session)->get('/admin/packages');
        $resPackages->assertStatus(200);
        $resPackages->assertSee('Golden Birthday Cabana');

        // 2. Categories Page
        $resCategories = $this->actingAs($this->admin)->withSession($session)->get('/admin/categories');
        $resCategories->assertStatus(200);
        $resCategories->assertSee('Birthday Celebrations');

        // 3. Hero Manager Page
        $resHero = $this->actingAs($this->admin)->withSession($session)->get('/admin/hero-slider');
        $resHero->assertStatus(200);

        // 4. Legal Pages
        $resLegal = $this->actingAs($this->admin)->withSession($session)->get('/admin/legal-pages');
        $resLegal->assertStatus(200);

        // 5. Settings Page
        $resSettings = $this->actingAs($this->admin)->withSession($session)->get('/admin/settings');
        $resSettings->assertStatus(200);
    }

    /**
     * Test dashboard route computes full analytics metrics for charts.
     */
    public function test_dashboard_route_computes_analytics(): void
    {
        $customer = Customer::create([
            'name'   => 'Analytics Test Customer',
            'mobile' => '9826011111',
        ]);

        Booking::create([
            'id'              => 'BK-ANALYTIC1',
            'customer_id'     => $customer->id,
            'customer_name'   => 'Analytics Test Customer',
            'customer_mobile' => '9826011111',
            'package_name'    => 'Golden Birthday Cabana',
            'category_name'   => 'Birthday Celebrations',
            'event_date'      => date('Y-m-d'),
            'address'         => 'Scheme 54, Vijay Nagar',
            'area'            => 'Vijay Nagar',
            'total_amount'    => 5000,
            'status'          => 'Confirmed',
        ]);

        $response = $this->actingAs($this->admin)->withSession([
            'admin_logged_in' => true,
            'admin_email'     => $this->admin->email,
        ])->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('analytics');
        $analytics = $response->viewData('analytics');
        $this->assertIsArray($analytics);
        $this->assertArrayHasKey('confirmed_revenue', $analytics);
        $this->assertArrayHasKey('confirmed_count', $analytics);
    }

    /**
     * Test public catalog listings and event details continue to work seamlessly with cached catalog.
     */
    public function test_public_event_listings_and_details_render_seamlessly(): void
    {
        // 1. Events Index
        $resList = $this->get('/events');
        $resList->assertStatus(200);
        $resList->assertSee('Golden Birthday Cabana');

        // 2. Event Details
        $resDetails = $this->get('/events/golden-birthday-cabana');
        $resDetails->assertStatus(200);
        $resDetails->assertSee('Golden Birthday Cabana');
        $resDetails->assertSee('4,999');

        // 3. Booking Form for Authenticated Customer
        $resBooking = $this->actingAs($this->customerUser)->get('/booking?slug=golden-birthday-cabana');
        $resBooking->assertStatus(200);
        $resBooking->assertSee('Golden Birthday Cabana');
    }

    /**
     * Test pending bookings count is cached and refreshed when a booking is created or updated.
     */
    public function test_pending_bookings_count_is_cached_and_refreshed(): void
    {
        $customer = Customer::create([
            'name'   => 'Badge Customer',
            'mobile' => '9826099999',
        ]);

        // Create a pending booking
        Booking::create([
            'id'              => 'BK-BADGE01',
            'customer_id'     => $customer->id,
            'customer_name'   => 'Badge Customer',
            'customer_mobile' => '9826099999',
            'package_name'    => 'Golden Birthday Cabana',
            'category_name'   => 'Birthday Celebrations',
            'event_date'      => date('Y-m-d'),
            'address'         => 'Scheme 54, Vijay Nagar',
            'area'            => 'Vijay Nagar',
            'total_amount'    => 4000,
            'status'          => 'Pending',
        ]);

        Cache::forget('admin_pending_bookings_count');

        // Verify count is 1
        $count1 = Cache::remember('admin_pending_bookings_count', 60, function() {
            return Booking::whereRaw('LOWER(status) = ?', ['pending'])->count();
        });
        $this->assertEquals(1, $count1);

        // Update status to Confirmed via admin action
        $this->actingAs($this->admin)->withSession([
            'admin_logged_in' => true,
            'admin_email'     => $this->admin->email,
        ])->postJson('/admin/bookings/update-status', [
            'id'     => 'BK-BADGE01',
            'status' => 'Confirmed',
        ])->assertStatus(200);

        // Cache must have been forgotten
        $this->assertFalse(Cache::has('admin_pending_bookings_count'));

        // Fresh count is 0
        $count2 = Cache::remember('admin_pending_bookings_count', 60, function() {
            return Booking::whereRaw('LOWER(status) = ?', ['pending'])->count();
        });
        $this->assertEquals(0, $count2);
    }

    /**
     * Test that pending booking counter is invalidated even when status notification is debounced,
     * and is NOT invalidated when status update fails.
     */
    public function test_pending_bookings_count_invalidated_even_when_notification_is_debounced_and_not_on_failure(): void
    {
        $customer = Customer::create([
            'name'   => 'Debounce Badge Customer',
            'mobile' => '9826088888',
        ]);

        Booking::create([
            'id'              => 'BK-DEBOUNCE1',
            'customer_id'     => $customer->id,
            'customer_name'   => 'Debounce Badge Customer',
            'customer_mobile' => '9826088888',
            'package_name'    => 'Golden Birthday Cabana',
            'category_name'   => 'Birthday Celebrations',
            'event_date'      => date('Y-m-d'),
            'address'         => 'Scheme 54, Vijay Nagar',
            'area'            => 'Vijay Nagar',
            'total_amount'    => 4000,
            'status'          => 'Pending',
        ]);

        // 1. Warm badge cache
        Cache::put('admin_pending_bookings_count', 1, 60);
        $this->assertTrue(Cache::has('admin_pending_bookings_count'));

        // 2. Pre-set debounce cooldown key so that notifications will be suppressed
        $debounceKey = 'booking_status_notif_cooldown_BK-DEBOUNCE1_confirmed';
        Cache::put($debounceKey, true, 60);
        $this->assertTrue(Cache::has($debounceKey));

        // 3. Admin updates status to Confirmed
        $response = $this->actingAs($this->admin)->withSession([
            'admin_logged_in' => true,
            'admin_email'     => $this->admin->email,
        ])->postJson('/admin/bookings/update-status', [
            'id'     => 'BK-DEBOUNCE1',
            'status' => 'Confirmed',
        ]);
        $response->assertStatus(200);

        // 4. Pending bookings cache MUST be invalidated despite the debounce suppression
        $this->assertFalse(Cache::has('admin_pending_bookings_count'), 'Pending count cache must be invalidated even when notification is debounced');

        // 5. Test failed update: Warm cache again
        Cache::put('admin_pending_bookings_count', 5, 60);

        // Attempt to update a non-existent booking ID
        $failedResponse = $this->actingAs($this->admin)->withSession([
            'admin_logged_in' => true,
            'admin_email'     => $this->admin->email,
        ])->postJson('/admin/bookings/update-status', [
            'id'     => 'BK-NONEXISTENT999',
            'status' => 'Confirmed',
        ]);
        $failedResponse->assertStatus(404);

        // Cache must NOT be invalidated for a failed update
        $this->assertTrue(Cache::has('admin_pending_bookings_count'), 'Pending count cache must NOT be invalidated on failed update');
    }
}


<?php

namespace Tests\Feature;

use App\Models\CmsOffer;
use App\Models\CmsOfferSetting;
use App\Models\CmsPromiseBanner;
use App\Models\CmsSlide;
use App\Models\User;
use App\Services\JsonStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class Phase2BSeparateCmsPagesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected string $originalCmsJsonRaw = '';

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Backup current cms.json on disk to prevent test pollution
        $cmsPath = storage_path('app/cms.json');
        if (File::exists($cmsPath)) {
            $this->originalCmsJsonRaw = File::get($cmsPath);
        }

        // 2. Create authenticated admin
        $this->admin = new User([
            'name'     => 'Artizen Admin',
            'email'    => 'admin@artizen.com',
            'password' => bcrypt('password123'),
        ]);
        $this->admin->is_admin = true;
        $this->admin->save();

        // 3. Seed baseline CmsSlide records
        CmsSlide::create([
            'id'            => 1,
            'title'         => 'ELEVATE YOUR CELEBRATIONS',
            'description'   => 'Book complete premium decor, sound, and lighting setups.',
            'image'         => '/assets/images/hero/1.jpg',
            'button1_text'  => 'Explore Packages',
            'button1_link'  => '/events',
            'button2_text'  => 'WhatsApp Inquiry',
            'button2_link'  => 'https://wa.me/919131668156',
            'display_order' => 0,
            'is_active'     => true,
        ]);

        CmsSlide::create([
            'id'            => 2,
            'title'         => 'HIGH-BASS PARTY RIGS',
            'description'   => 'Professional active speaker columns and live DJs.',
            'image'         => '/assets/images/hero/2.jpg',
            'button1_text'  => 'View DJ Setups',
            'button1_link'  => '/events',
            'button2_text'  => 'Book Instant',
            'button2_link'  => 'https://wa.me/919131668156',
            'display_order' => 1,
            'is_active'     => true,
        ]);

        // 4. Seed baseline CmsOffer and CmsOfferSetting records
        CmsOfferSetting::create([
            'id'            => 1,
            'section_title' => 'Save More with Offers',
            'is_active'     => true,
        ]);

        CmsOffer::create([
            'id'             => 1,
            'category_name'  => 'Birthdays & House Parties',
            'icon'           => 'fa-solid fa-cake-candles',
            'icon_color'     => 'text-amber-600',
            'subheading'     => 'FLAT ₹500 OFF',
            'discount_title' => 'Flat ₹500 Off on Theme Decor',
            'terms'          => 'Min. booking ₹4,999 • Code: ARTIZEN500',
            'link'           => '/events?category=birthdays',
            'display_order'  => 1,
            'is_active'      => true,
        ]);

        CmsOffer::create([
            'id'             => 2,
            'category_name'  => 'Weddings & DJ Sound',
            'icon'           => 'fa-solid fa-compact-disc',
            'icon_color'     => 'text-purple-600',
            'subheading'     => 'COMPLIMENTARY FOG RIG',
            'discount_title' => 'Free Concert Fog Rig',
            'terms'          => 'With all 4000W+ rigs',
            'link'           => '/events?category=dj',
            'display_order'  => 2,
            'is_active'      => true,
        ]);

        // 5. Seed baseline CmsPromiseBanner record
        CmsPromiseBanner::create([
            'id'        => 1,
            'image'     => '/images/banners/artizen-promise-banner.webp',
            'title'     => 'Artizen — Curating Unforgettable Moments, Luxury Weddings & Celebrations',
            'link'      => '/events',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        $cmsPath = storage_path('app/cms.json');
        if (!empty($this->originalCmsJsonRaw)) {
            File::put($cmsPath, $this->originalCmsJsonRaw);
        }

        parent::tearDown();
    }

    protected function actingAsAdmin()
    {
        return $this->actingAs($this->admin)
            ->withSession([
                'admin_logged_in' => true,
                'admin_email'     => $this->admin->email,
            ]);
    }

    /**
     * 1. Test guest authorization: unauthorized visitors are redirected to admin login
     * for all 3 GET pages and all 3 POST save endpoints.
     */
    public function test_guest_cannot_access_or_save_any_of_the_three_cms_pages(): void
    {
        // GET routes redirect to login
        $this->get(route('admin.hero'))->assertRedirect(route('login', ['redirect' => url('/admin/hero-slider')]));
        $this->get(route('admin.offers'))->assertRedirect(route('login', ['redirect' => url('/admin/offers')]));
        $this->get(route('admin.promise-banner'))->assertRedirect(route('login', ['redirect' => url('/admin/promise-banner')]));

        // POST routes redirect to login
        $this->post(route('admin.hero.save'), [])->assertRedirect(route('login', ['redirect' => url('/admin/hero-slider/save')]));
        $this->post(route('admin.offers.save'), [])->assertRedirect(route('login', ['redirect' => url('/admin/offers/save')]));
        $this->post(route('admin.promise-banner.save'), [])->assertRedirect(route('login', ['redirect' => url('/admin/promise-banner/save')]));
    }

    /**
     * 2. Test authenticated admin can view each independent page with its own specific UI.
     */
    public function test_admin_can_view_three_independent_cms_pages(): void
    {
        // 1. Hero Banners Page
        $heroResponse = $this->actingAsAdmin()->get(route('admin.hero'));
        $heroResponse->assertStatus(200);
        $heroResponse->assertSee('Hero Banners Manager');
        $heroResponse->assertSee('ELEVATE YOUR CELEBRATIONS');
        $heroResponse->assertSee('HIGH-BASS PARTY RIGS');
        // Ensure Offers and Promise Banner controls are NOT in Hero page
        $heroResponse->assertDontSee('Offers Section Manager');
        $heroResponse->assertDontSee('Promise Banner Manager');

        // 2. Offers Section Page
        $offersResponse = $this->actingAsAdmin()->get(route('admin.offers'));
        $offersResponse->assertStatus(200);
        $offersResponse->assertSee('Offers Section Manager');
        $offersResponse->assertSee('Save More with Offers');
        $offersResponse->assertSee('Flat ₹500 Off on Theme Decor');
        $offersResponse->assertSee('Select Celebration Icon'); // Icon picker present
        // Ensure Hero Banner controls are NOT in Offers page
        $offersResponse->assertDontSee('Hero Banners Manager');
        $offersResponse->assertDontSee('Panoramic Promise Banner');

        // 3. Promise Banner Page
        $pbResponse = $this->actingAsAdmin()->get(route('admin.promise-banner'));
        $pbResponse->assertStatus(200);
        $pbResponse->assertSee('Promise Banner Manager');
        $pbResponse->assertSee('Panoramic Promise Banner');
        $pbResponse->assertSee('Artizen — Curating Unforgettable Moments');
        // Ensure Hero and Offers controls are NOT in Promise Banner page
        $pbResponse->assertDontSee('Hero Banners Manager');
        $pbResponse->assertDontSee('Offers Section Manager');
    }

    /**
     * 3. Test Hero Banners save: preserves slide IDs, prevents duplicate rows when titles change,
     * supports reordering, activating/deactivating, and creating new slides.
     */
    public function test_hero_banners_save_updates_same_record_without_duplicate_rows(): void
    {
        $this->assertEquals(2, CmsSlide::count());

        $newTitle = 'NEW SPECTACULAR GALA NIGHT ' . rand(1000, 9999);
        $payload = [
            'hero_slides' => [
                [
                    'id'            => 1,
                    'title'         => $newTitle,
                    'badge'         => 'UPDATED BADGE',
                    'desc'          => 'Updated description for slide 1',
                    'btn1Text'      => 'Explore Now',
                    'link1'         => '/events',
                    'btn2Text'      => 'WhatsApp',
                    'link2'         => 'https://wa.me/919131668156',
                    'image'         => '/assets/images/hero/1.jpg',
                    'is_active'     => '1',
                    'display_order' => 1, // swapped order
                ],
                [
                    'id'            => 2,
                    'title'         => 'HIGH-BASS PARTY RIGS',
                    'badge'         => 'SOUNDS OF ARTIZEN',
                    'desc'          => 'Professional active speaker columns and live DJs.',
                    'btn1Text'      => 'View DJ Setups',
                    'link1'         => '/events',
                    'btn2Text'      => 'Book Instant',
                    'link2'         => 'https://wa.me/919131668156',
                    'image'         => '/assets/images/hero/2.jpg',
                    'is_active'     => '1',
                    'display_order' => 0, // swapped order
                ],
                [
                    'id'            => '', // Brand new 3rd slide
                    'title'         => 'BRAND NEW THIRD SLIDE',
                    'badge'         => 'NEW LAUNCH',
                    'desc'          => 'Brand new 3rd slide description',
                    'btn1Text'      => 'Book 3rd',
                    'link1'         => '/events',
                    'btn2Text'      => 'Inquire',
                    'link2'         => 'https://wa.me/919131668156',
                    'image'         => '/assets/images/hero/3.jpg',
                    'is_active'     => '1',
                    'display_order' => 2,
                ]
            ]
        ];

        $response = $this->actingAsAdmin()->post(route('admin.hero.save'), $payload);
        $response->assertRedirect(route('admin.hero'));
        $response->assertSessionHas('success');

        // Total count must now be 3
        $this->assertEquals(3, CmsSlide::count());

        // Slide 1 must preserve its exact ID 1 with updated title
        $slide1 = CmsSlide::find(1);
        $this->assertNotNull($slide1);
        $this->assertEquals($newTitle, $slide1->title);
        $this->assertEquals('Updated description for slide 1', $slide1->description);
        $this->assertEquals(1, $slide1->display_order);

        // Slide 2 must have display order 0
        $slide2 = CmsSlide::find(2);
        $this->assertEquals(0, $slide2->display_order);

        // Third slide was created
        $slide3 = CmsSlide::where('title', 'BRAND NEW THIRD SLIDE')->first();
        $this->assertNotNull($slide3);
        $this->assertEquals('Brand new 3rd slide description', $slide3->description);
    }

    /**
     * 4. Test Hero Banners delete: removing a slide from the payload deletes it from the database.
     */
    public function test_hero_banners_delete_removes_record_from_database(): void
    {
        $this->assertEquals(2, CmsSlide::count());

        // Submit payload with ONLY slide 1 (slide 2 omitted, i.e., deleted)
        $payload = [
            'hero_slides' => [
                [
                    'id'            => 1,
                    'title'         => 'ELEVATE YOUR CELEBRATIONS',
                    'image'         => '/assets/images/hero/1.jpg',
                    'is_active'     => '1',
                    'display_order' => 0,
                ]
            ]
        ];

        $response = $this->actingAsAdmin()->post(route('admin.hero.save'), $payload);
        $response->assertRedirect(route('admin.hero'));

        $this->assertEquals(1, CmsSlide::count());
        $this->assertNull(CmsSlide::find(2));
        $this->assertNotNull(CmsSlide::find(1));
    }

    /**
     * 5. Test Offers Section save: updates section heading, visibility, adds new offers,
     * updates existing offers with icon picker identifiers, and deletes removed offers.
     */
    public function test_offers_section_save_updates_settings_items_and_icon_identifiers(): void
    {
        $payload = [
            'offers' => [
                'section_title' => 'INDORE FESTIVE SAVINGS 2026',
                'is_active'     => '1',
                'items'         => [
                    [
                        'id'             => 1,
                        'category_name'  => 'Birthdays & Private Parties',
                        'icon'           => 'fa-solid fa-cake-candles',
                        'icon_color'     => 'text-amber-600',
                        'subheading'     => 'FLAT ₹1,000 OFF',
                        'discount_title' => 'Flat ₹1,000 Off on Grand Birthday Setups',
                        'terms'          => 'Min. booking ₹9,999 • Code: BDAY1000',
                        'link'           => '/events?category=birthdays',
                        'is_active'      => '1',
                        'display_order'  => 1,
                    ],
                    [
                        'id'             => '', // Brand new offer ticket
                        'category_name'  => 'Royal Weddings & Haldi',
                        'icon'           => 'fa-solid fa-crown',
                        'icon_color'     => 'text-rose-600',
                        'subheading'     => 'EARLY BIRD 20% OFF',
                        'discount_title' => 'Flat 20% Off Stage Mandap',
                        'terms'          => 'Book 45 days prior',
                        'link'           => '/events?category=weddings',
                        'is_active'      => '1',
                        'display_order'  => 2,
                    ]
                ]
            ]
        ];

        $response = $this->actingAsAdmin()->post(route('admin.offers.save'), $payload);
        $response->assertRedirect(route('admin.offers'));
        $response->assertSessionHas('success');

        // Check CmsOfferSetting
        $setting = CmsOfferSetting::first();
        $this->assertNotNull($setting);
        $this->assertEquals('INDORE FESTIVE SAVINGS 2026', $setting->section_title);
        $this->assertTrue($setting->is_active);

        // Check CmsOffer items count (offer 2 was deleted, new offer added -> total 2)
        $this->assertEquals(2, CmsOffer::count());

        $offer1 = CmsOffer::find(1);
        $this->assertEquals('Flat ₹1,000 Off on Grand Birthday Setups', $offer1->discount_title);
        $this->assertEquals('fa-solid fa-cake-candles', $offer1->icon);

        $newOffer = CmsOffer::where('icon', 'fa-solid fa-crown')->first();
        $this->assertNotNull($newOffer);
        $this->assertEquals('Royal Weddings & Haldi', $newOffer->category_name);
        $this->assertEquals('EARLY BIRD 20% OFF', $newOffer->subheading);
    }

    /**
     * 6. Test Promise Banner save: updates banner title, image, destination link, and toggle.
     */
    public function test_promise_banner_save_updates_title_link_and_active_status(): void
    {
        $payload = [
            'promise_banner' => [
                'title'     => '100% On-Time Execution Guarantee Across All Indore Venues',
                'link'      => '/events?category=all',
                'image'     => '/images/banners/custom-promise.webp',
                'is_active' => '1',
            ]
        ];

        $response = $this->actingAsAdmin()->post(route('admin.promise-banner.save'), $payload);
        $response->assertRedirect(route('admin.promise-banner'));
        $response->assertSessionHas('success');

        $pb = CmsPromiseBanner::first();
        $this->assertNotNull($pb);
        $this->assertEquals('100% On-Time Execution Guarantee Across All Indore Venues', $pb->title);
        $this->assertEquals('/events?category=all', $pb->link);
        $this->assertEquals('/images/banners/custom-promise.webp', $pb->image);
        $this->assertTrue($pb->is_active);
    }

    /**
     * 7. Test Image Validation on Hero and Promise Banner: rejects non-image files,
     * accepts valid images.
     */
    public function test_image_upload_validation_rejects_invalid_files_on_hero_and_promise_banner(): void
    {
        $invalidFile = UploadedFile::fake()->create('script.php', 100, 'text/x-php');

        // Hero upload validation
        $badHeroPayload = [
            'hero_slides' => [
                [
                    'id'         => 1,
                    'title'      => 'Test Slide',
                    'image_file' => $invalidFile,
                ]
            ]
        ];
        $responseHero = $this->actingAsAdmin()->post(route('admin.hero.save'), $badHeroPayload);
        $responseHero->assertSessionHasErrors(['hero_slides.0.image_file']);

        // Promise banner upload validation
        $badPbPayload = [
            'promise_banner' => [
                'title'      => 'Test Banner',
                'image_file' => $invalidFile,
            ]
        ];
        $responsePb = $this->actingAsAdmin()->post(route('admin.promise-banner.save'), $badPbPayload);
        $responsePb->assertSessionHasErrors(['promise_banner.image_file']);
    }

    /**
     * 8. Test public homepage reflects changes across Hero, Offers, and Promise Banner,
     * and cleanly hides disabled sections.
     */
    public function test_public_homepage_renders_all_three_sections_and_respects_inactive_toggles(): void
    {
        $uniqueSlide = 'UNIQUE INDORE HERO ' . rand(1000, 9999);
        $uniqueOffer = 'MEGA SAVINGS ' . rand(1000, 9999);
        $uniqueBanner = 'PANORAMIC TRUST ' . rand(1000, 9999);

        // Update records in DB
        CmsSlide::find(1)->update(['title' => $uniqueSlide, 'is_active' => true]);
        CmsOfferSetting::first()->update(['is_active' => true]);
        CmsOffer::find(1)->update(['discount_title' => $uniqueOffer, 'is_active' => true]);
        CmsPromiseBanner::first()->update(['title' => $uniqueBanner, 'is_active' => true]);

        // Homepage GET
        $res = $this->get('/');
        $res->assertStatus(200);
        $res->assertSee($uniqueSlide);
        $res->assertSee($uniqueOffer);
        $res->assertSee($uniqueBanner);

        // Now toggle Offers and Promise Banner OFF
        CmsOfferSetting::first()->update(['is_active' => false]);
        CmsPromiseBanner::first()->update(['is_active' => false]);

        $resDisabled = $this->get('/');
        $resDisabled->assertStatus(200);
        $resDisabled->assertSee($uniqueSlide); // Hero remains
        $resDisabled->assertDontSee($uniqueOffer); // Offers hidden
        $resDisabled->assertDontSee($uniqueBanner); // Banner hidden
    }

    /**
     * 9. Test cross-section isolation: saving Hero Banners NEVER overwrites Offers,
     * Promise Banner, or About sections.
     */
    public function test_saving_hero_banners_never_touches_unrelated_cms_sections(): void
    {
        // Seed unique values in offers, promise banner, and about
        $cms = JsonStorageService::read('cms.json', []);
        $cms['about'] = [
            'badge' => 'PRESERVED ABOUT BADGE 2B',
            'title' => 'Preserved About Title 2B',
            'desc'  => 'Preserved About Description 2B',
            'cards' => [],
        ];
        JsonStorageService::write('cms.json', $cms);

        $initialOfferTitle = CmsOffer::find(1)->discount_title;
        $initialBannerTitle = CmsPromiseBanner::first()->title;

        // Post ONLY to hero save
        $payload = [
            'hero_slides' => [
                [
                    'id'            => 1,
                    'title'         => 'SLIDE AFTER ISOLATION TEST',
                    'image'         => '/assets/images/hero/1.jpg',
                    'is_active'     => '1',
                    'display_order' => 0,
                ]
            ]
        ];

        $response = $this->actingAsAdmin()->post(route('admin.hero.save'), $payload);
        $response->assertRedirect(route('admin.hero'));

        // Verify offers and promise banner in DB are 100% identical
        $this->assertEquals($initialOfferTitle, CmsOffer::find(1)->discount_title);
        $this->assertEquals($initialBannerTitle, CmsPromiseBanner::first()->title);

        // Verify about in cms.json is untouched
        $persistedCms = JsonStorageService::read('cms.json', []);
        $this->assertEquals('PRESERVED ABOUT BADGE 2B', $persistedCms['about']['badge'] ?? null);
    }
}

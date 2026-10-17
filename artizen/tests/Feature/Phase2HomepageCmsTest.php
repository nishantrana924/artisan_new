<?php

namespace Tests\Feature;

use App\Models\CmsSlide;
use App\Models\User;
use App\Services\JsonStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class Phase2HomepageCmsTest extends TestCase
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

        // 2. Create authenticated admin in testing database
        $this->admin = new User([
            'name'     => 'Artizen Admin',
            'email'    => 'admin@artizen.com',
            'password' => bcrypt('password123'),
        ]);
        $this->admin->is_admin = true;
        $this->admin->save();

        // 3. Seed baseline CmsSlide records into the testing database
        CmsSlide::create([
            'id'           => 1,
            'title'        => 'ELEVATE YOUR CELEBRATIONS',
            'description'  => 'Book complete premium decor, sound, and lighting setups instantly.',
            'image'        => '/assets/images/hero/1.jpg',
            'button1_text' => 'Explore Packages',
            'button1_link' => '/events',
            'button2_text' => 'WhatsApp Inquiry',
            'button2_link' => 'https://wa.me/919131668156',
            'display_order'=> 0,
            'is_active'    => true,
        ]);

        CmsSlide::create([
            'id'           => 2,
            'title'        => 'HIGH-BASS PARTY RIGS',
            'description'  => 'Professional active speaker columns and live DJs.',
            'image'        => '/assets/images/hero/2.jpg',
            'button1_text' => 'View DJ Setups',
            'button1_link' => '/events',
            'button2_text' => 'Book Instant',
            'button2_link' => 'https://wa.me/919131668156',
            'display_order'=> 1,
            'is_active'    => true,
        ]);
    }

    protected function tearDown(): void
    {
        // Restore disk cms.json to exact pre-test state
        $cmsPath = storage_path('app/cms.json');
        if (!empty($this->originalCmsJsonRaw)) {
            File::put($cmsPath, $this->originalCmsJsonRaw);
        }

        parent::tearDown();
    }

    /**
     * Helper to authenticate as admin
     */
    protected function actingAsAdmin()
    {
        return $this->actingAs($this->admin)
            ->withSession([
                'admin_logged_in' => true,
                'admin_email'     => $this->admin->email,
            ]);
    }

    /**
     * 1. Test saving hero slides preserves slide IDs and updates existing database rows
     * without creating duplicate records when slide titles change.
     */
    public function test_admin_hero_slide_saving_preserves_slide_ids_and_updates_db_without_duplicate_rows(): void
    {
        $this->assertEquals(2, CmsSlide::count());

        $firstSlide = CmsSlide::find(1);
        $this->assertNotNull($firstSlide);
        $newTitle = 'NEW LUXURY WEDDINGS & CELEBRATIONS ' . time();

        $payload = [
            'hero_slides' => [
                [
                    'id'            => 1,
                    'title'         => $newTitle,
                    'badge'         => 'ROYAL MOMENTS',
                    'desc'          => 'Updated dynamic description for test.',
                    'btn1Text'      => 'Explore Now',
                    'link1'         => '/events',
                    'btn2Text'      => 'Contact Us',
                    'link2'         => '/contact',
                    'image'         => '/assets/images/hero/1.jpg',
                    'is_active'     => '1',
                    'display_order' => 0,
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
                    'display_order' => 1,
                ]
            ]
        ];

        $response = $this->actingAsAdmin()->post(route('admin.cms.save'), $payload);
        $response->assertRedirect(route('admin.hero'));
        $response->assertSessionHas('success');

        // Total count must remain 2 (no duplicate rows created)
        $this->assertEquals(2, CmsSlide::count(), "Database row count must remain 2 after title change");

        // Slide 1 must retain its ID and reflect updated title
        $updatedSlide = CmsSlide::find(1);
        $this->assertNotNull($updatedSlide, "Original slide ID 1 must be preserved in DB");
        $this->assertEquals($newTitle, $updatedSlide->title, "Slide title must be updated");
        $this->assertEquals('Updated dynamic description for test.', $updatedSlide->description);
    }

    /**
     * 2. Test saving slides never overwrites unrelated CMS sections (about, offers, promise_banner).
     */
    public function test_saving_slides_preserves_unrelated_cms_sections_in_storage(): void
    {
        // Guarantee unrelated sections exist in storage prior to test
        $baselineCms = JsonStorageService::read('cms.json', []) ?? [];
        $baselineCms['about'] = [
            'badge' => 'PRESERVED ABOUT BADGE',
            'title' => 'Preserved About Title',
            'desc'  => 'Preserved About Description',
            'cards' => [
                ['icon' => 'fa-solid fa-star', 'title' => 'VIP Service', 'desc' => 'VIP Description']
            ]
        ];
        $baselineCms['offers'] = [
            'section_title' => 'PRESERVED OFFERS SECTION',
            'is_active'     => true,
            'items'         => [
                ['id' => 1, 'category_name' => 'Preserved Category', 'discount_title' => '10% OFF', 'is_active' => true]
            ]
        ];
        $baselineCms['promise_banner'] = [
            'image'     => '/images/banners/preserved.webp',
            'title'     => 'PRESERVED PROMISE TITLE',
            'link'      => '/preserved-link',
            'is_active' => true,
        ];
        JsonStorageService::write('cms.json', $baselineCms);

        // Submit ONLY hero slides
        $payload = [
            'hero_slides' => [
                [
                    'id'            => 1,
                    'title'         => 'HERO TITLE FOR PRESERVATION TEST',
                    'badge'         => 'TEST BADGE',
                    'desc'          => 'Test slide desc',
                    'btn1Text'      => 'Explore',
                    'link1'         => '/events',
                    'btn2Text'      => 'Contact',
                    'link2'         => '/contact',
                    'image'         => '/assets/images/hero/1.jpg',
                    'is_active'     => '1',
                    'display_order' => 0,
                ]
            ]
        ];

        $response = $this->actingAsAdmin()->post(route('admin.cms.save'), $payload);
        $response->assertRedirect(route('admin.hero'));

        // Verify storage still contains about, offers, and promise_banner untouched
        $persistedCms = JsonStorageService::read('cms.json', []);
        $this->assertEquals('PRESERVED ABOUT BADGE', $persistedCms['about']['badge'] ?? null);
        $this->assertEquals('PRESERVED OFFERS SECTION', $persistedCms['offers']['section_title'] ?? null);
        $this->assertEquals('PRESERVED PROMISE TITLE', $persistedCms['promise_banner']['title'] ?? null);
    }

    /**
     * 3. Test admin can update "Save More with Offers" and the full-width promise banner.
     */
    public function test_admin_can_update_offers_and_promise_banner_sections(): void
    {
        $payload = [
            'offers' => [
                'section_title' => 'EXCLUSIVE WEEKEND OFFERS',
                'is_active'     => '1',
                'items' => [
                    [
                        'id'             => 1,
                        'category_name'  => 'Terrace Birthdays',
                        'icon'           => 'fa-solid fa-cake-candles',
                        'icon_color'     => 'text-amber-500',
                        'subheading'     => 'SPECIAL TERRACE DEAL',
                        'discount_title' => 'Flat ₹750 Off on Setup',
                        'terms'          => 'Valid till Sunday • Code: TERRACE750',
                        'link'           => '/events?category=birthdays',
                        'is_active'      => '1',
                        'display_order'  => 1,
                    ]
                ]
            ],
            'promise_banner' => [
                'title'     => 'Indore 100% Guaranteed Setup Execution',
                'link'      => '/guarantee',
                'image'     => '/images/banners/artizen-promise-banner.webp',
                'is_active' => '1',
            ]
        ];

        $response = $this->actingAsAdmin()->post(route('admin.cms.save'), $payload);
        $response->assertRedirect(route('admin.hero'));
        $response->assertSessionHas('success');

        $savedCms = JsonStorageService::read('cms.json', []);
        $this->assertEquals('EXCLUSIVE WEEKEND OFFERS', $savedCms['offers']['section_title']);
        $this->assertTrue($savedCms['offers']['is_active']);
        $this->assertEquals('Terrace Birthdays', $savedCms['offers']['items'][0]['category_name']);
        $this->assertEquals('Flat ₹750 Off on Setup', $savedCms['offers']['items'][0]['discount_title']);

        $this->assertEquals('Indore 100% Guaranteed Setup Execution', $savedCms['promise_banner']['title']);
        $this->assertEquals('/guarantee', $savedCms['promise_banner']['link']);
        $this->assertTrue($savedCms['promise_banner']['is_active']);
    }

    /**
     * 4. Test homepage renders admin-managed hero slides with Swiper layout intact.
     */
    public function test_homepage_renders_admin_managed_hero_slides_with_swiper_markup(): void
    {
        $uniqueTitle = 'SPECTACULAR INDORE CELEBRATIONS ' . rand(1000, 9999);

        // Update DB slide
        $firstSlide = CmsSlide::find(1);
        $firstSlide->update([
            'title'        => $uniqueTitle,
            'is_active'    => true,
            'image'        => '/assets/images/hero/1.jpg',
            'button1_text' => 'Book My VIP Setup',
            'button1_link' => '/events',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // Assert Swiper container & structure
        $response->assertSee('hero-banner-swiper');
        $response->assertSee('swiper-slide');
        $response->assertSee($uniqueTitle);
        $response->assertSee('Book My VIP Setup');
    }

    /**
     * 5. Test homepage renders admin-managed offers and cleanly hides when toggled off.
     */
    public function test_homepage_renders_admin_managed_offers_and_respects_is_active_toggle(): void
    {
        $uniqueOfferTitle = 'FLASH MEGA DISCOUNT ' . rand(1000, 9999);
        $uniqueCategory = 'VIP Sangeet Night ' . rand(100, 999);

        // 1. Enable offers
        $cms = JsonStorageService::read('cms.json', []);
        $cms['offers'] = [
            'section_title' => 'SUPER SAVER OFFERS',
            'is_active'     => true,
            'items'         => [
                [
                    'id'             => 1,
                    'category_name'  => $uniqueCategory,
                    'icon'           => 'fa-solid fa-ring',
                    'icon_color'     => 'text-rose-600',
                    'subheading'     => 'FLAT 35% OFF',
                    'discount_title' => $uniqueOfferTitle,
                    'terms'          => 'Limited to first 10 bookings',
                    'link'           => '/events?category=weddings',
                    'is_active'      => true,
                ]
            ]
        ];
        JsonStorageService::write('cms.json', $cms);

        $responseActive = $this->get('/');
        $responseActive->assertStatus(200);
        $responseActive->assertSee('SUPER SAVER OFFERS');
        $responseActive->assertSee($uniqueCategory);
        $responseActive->assertSee($uniqueOfferTitle);

        // 2. Disable offers
        $cms['offers']['is_active'] = false;
        JsonStorageService::write('cms.json', $cms);

        $responseDisabled = $this->get('/');
        $responseDisabled->assertStatus(200);
        $responseDisabled->assertDontSee($uniqueOfferTitle);
        $responseDisabled->assertDontSee('SUPER SAVER OFFERS');
    }

    /**
     * 6. Test homepage renders admin-managed promise banner and cleanly hides when toggled off.
     */
    public function test_homepage_renders_admin_managed_promise_banner_and_respects_is_active_toggle(): void
    {
        $uniqueBannerTitle = 'Pan-Indore Event Execution Standard ' . rand(1000, 9999);
        $uniqueLink = '/custom-trust-link-' . rand(100, 999);

        // 1. Enable banner
        $cms = JsonStorageService::read('cms.json', []);
        $cms['promise_banner'] = [
            'image'     => '/images/banners/artizen-promise-banner.webp',
            'title'     => $uniqueBannerTitle,
            'link'      => $uniqueLink,
            'is_active' => true,
        ];
        JsonStorageService::write('cms.json', $cms);

        $responseActive = $this->get('/');
        $responseActive->assertStatus(200);
        $responseActive->assertSee($uniqueBannerTitle);
        $responseActive->assertSee($uniqueLink);

        // 2. Disable banner
        $cms['promise_banner']['is_active'] = false;
        JsonStorageService::write('cms.json', $cms);

        $responseDisabled = $this->get('/');
        $responseDisabled->assertStatus(200);
        $responseDisabled->assertDontSee($uniqueBannerTitle);
        $responseDisabled->assertDontSee($uniqueLink);
    }

    /**
     * 7. Test CMS image upload validation rejects invalid file types and accepts valid images.
     */
    public function test_cms_image_upload_validation_rejects_invalid_files_and_accepts_valid_images(): void
    {
        // 1. Invalid file rejection (text file masquerading as image)
        $invalidFile = UploadedFile::fake()->create('malicious.txt', 100, 'text/plain');
        $badPayload = [
            'promise_banner' => [
                'title'      => 'Test Banner',
                'image_file' => $invalidFile,
            ]
        ];

        $badResponse = $this->actingAsAdmin()->post(route('admin.cms.save'), $badPayload);
        $badResponse->assertSessionHasErrors(['promise_banner.image_file']);

        // 2. Valid image acceptance
        $validImage = UploadedFile::fake()->image('test_hero_slide.jpg', 1920, 1080);
        $goodPayload = [
            'hero_slides' => [
                [
                    'id'            => 1,
                    'title'         => 'VALID UPLOAD SLIDE',
                    'image_file'    => $validImage,
                    'is_active'     => '1',
                    'display_order' => 0,
                ]
            ]
        ];

        $goodResponse = $this->actingAsAdmin()->post(route('admin.cms.save'), $goodPayload);
        $goodResponse->assertRedirect(route('admin.hero'));
        $goodResponse->assertSessionHasNoErrors();
    }

    /**
     * 8. Test homepage safe defaults and fallbacks when CMS content is empty or null.
     */
    public function test_homepage_safe_fallbacks_when_cms_content_is_missing_or_empty(): void
    {
        // Empty CMS storage and slide records
        JsonStorageService::write('cms.json', []);
        CmsSlide::truncate();

        $response = $this->get('/');
        $response->assertStatus(200);
        // Page must still render without 500 error or exception
        $response->assertSee('hero-banner-swiper');
    }
}

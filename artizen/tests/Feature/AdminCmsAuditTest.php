<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\JsonStorageService;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCmsAuditTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'email' => 'cmsauditadmin@artizen.com',
            'password' => Hash::make('Password123!'),
            'is_admin' => true,
        ]);
    }

    /**
     * 1. Overview module access test
     */
    public function test_overview_module_renders_real_metrics(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('analytics');
    }

    /**
     * 2. Bookings status transition test
     */
    public function test_bookings_status_update_and_persistence(): void
    {
        $originalBookings = JsonStorageService::read('bookings.json', []);
        $testId = 'BK-AUDIT10';

        $mockBooking = [
            'id' => $testId,
            'name' => 'Audit Test Customer',
            'mobile' => '9131668156',
            'package' => 'Audit Package',
            'total' => 5000,
            'status' => 'Pending',
            'created_at' => date('d M Y, h:i A')
        ];

        JsonStorageService::write('bookings.json', [$mockBooking]);

        try {
            $response = $this->actingAs($this->admin)->postJson('/admin/bookings/update-status', [
                'id' => $testId,
                'status' => 'Confirmed'
            ]);

            $response->assertStatus(200);
            $response->assertJson(['success' => true]);

            $updatedBookings = JsonStorageService::read('bookings.json', []);
            $this->assertEquals('Confirmed', $updatedBookings[0]['status']);
        } finally {
            JsonStorageService::write('bookings.json', $originalBookings);
        }
    }

    /**
     * 3 & 4. Categories & Packages CRUD test
     */
    public function test_category_and_package_saving_and_deletion(): void
    {
        $originalCats = JsonStorageService::read('categories.json', []);

        try {
            // Save category
            $response = $this->actingAs($this->admin)->post('/admin/categories/save', [
                'title' => 'Audit Category',
                'active' => 1
            ]);

            $response->assertRedirect();

            $cats = JsonStorageService::read('categories.json', []);
            $catId = null;
            foreach ($cats as $c) {
                if (($c['title'] ?? '') === 'Audit Category') {
                    $catId = $c['id'];
                    break;
                }
            }

            $this->assertNotNull($catId);

            // Delete category
            $delResponse = $this->actingAs($this->admin)->post("/admin/categories/delete/{$catId}");
            $delResponse->assertRedirect();

            $finalCats = JsonStorageService::read('categories.json', []);
            $found = array_filter($finalCats, fn($c) => ($c['id'] ?? '') == $catId);
            $this->assertEmpty($found);

        } finally {
            JsonStorageService::write('categories.json', $originalCats);
        }
    }

    /**
     * 6. FAQs CRUD test
     */
    public function test_faq_crud_persistence(): void
    {
        $originalFaqs = JsonStorageService::read('faqs.json', []);

        try {
            $response = $this->actingAs($this->admin)->post('/admin/faqs/save', [
                'q' => 'Is venue setup included?',
                'a' => 'Yes, our on-site team handles full setup and teardown.'
            ]);

            $response->assertRedirect();

            $faqs = JsonStorageService::read('faqs.json', []);
            $faqId = $faqs[count($faqs) - 1]['id'] ?? null;
            $this->assertNotNull($faqId);

            // Delete FAQ
            $delResponse = $this->actingAs($this->admin)->post("/admin/faqs/delete/{$faqId}");
            $delResponse->assertRedirect();

        } finally {
            JsonStorageService::write('faqs.json', $originalFaqs);
        }
    }

    /**
     * 7. Testimonials CRUD test
     */
    public function test_testimonial_crud_persistence(): void
    {
        $originalTestimonials = JsonStorageService::read('testimonials.json', []);

        try {
            $response = $this->actingAs($this->admin)->post('/admin/testimonials/save', [
                'author' => 'Rahul Sharma',
                'review' => 'Stunning birthday decor setup!',
                'rating' => 5,
                'event_type' => 'Birthday'
            ]);

            $response->assertRedirect();

            $testimonials = JsonStorageService::read('testimonials.json', []);
            $tId = $testimonials[count($testimonials) - 1]['id'] ?? null;
            $this->assertNotNull($tId);

            // Delete Testimonial
            $delResponse = $this->actingAs($this->admin)->post("/admin/testimonials/delete/{$tId}");
            $delResponse->assertRedirect();

        } finally {
            JsonStorageService::write('testimonials.json', $originalTestimonials);
        }
    }

    /**
     * 8. Gallery CRUD test
     */
    public function test_gallery_crud_persistence(): void
    {
        $originalGallery = JsonStorageService::read('gallery.json', []);

        try {
            $response = $this->actingAs($this->admin)->post('/admin/gallery/save', [
                'title' => 'Mandap Floral Setup',
                'category' => 'Wedding',
                'image' => '/assets/images/hero/3.jpg',
                'active' => 1
            ]);

            $response->assertRedirect();

            $gallery = JsonStorageService::read('gallery.json', []);
            $gId = $gallery[count($gallery) - 1]['id'] ?? null;
            $this->assertNotNull($gId);

            // Delete Gallery item
            $delResponse = $this->actingAs($this->admin)->post("/admin/gallery/delete/{$gId}");
            $delResponse->assertRedirect();

        } finally {
            JsonStorageService::write('gallery.json', $originalGallery);
        }
    }

    /**
     * 9 & 10. Hero Slider & About CMS save test
     */
    public function test_cms_hero_and_about_saving(): void
    {
        $originalCms = JsonStorageService::read('cms.json', []);

        try {
            $response = $this->actingAs($this->admin)->post('/admin/cms/save', [
                'hero_slides' => [
                    [
                        'badge' => 'TEST BADGE',
                        'title' => 'TEST TITLE',
                        'desc' => 'Test Slide Description',
                        'link1' => '/events',
                        'btn1Text' => 'Explore',
                        'link2' => 'https://wa.me/919131668156',
                        'btn2Text' => 'WhatsApp',
                        'image' => '/assets/images/hero/1.jpg'
                    ]
                ],
                'about' => [
                    'badge' => 'ABOUT BADGE',
                    'title' => 'ABOUT TITLE',
                    'desc' => 'About description content',
                    'image' => '/assets/images/hero/1.jpg'
                ]
            ]);

            $response->assertRedirect();

            $updatedCms = JsonStorageService::read('cms.json', []);
            $this::assertEquals('TEST BADGE', $updatedCms['hero_slides'][0]['badge']);
            $this::assertEquals('ABOUT BADGE', $updatedCms['about']['badge']);

        } finally {
            JsonStorageService::write('cms.json', $originalCms);
        }
    }

    /**
     * 12 & 13. Settings & SEO save test
     */
    public function test_settings_and_seo_saving(): void
    {
        $originalSettings = JsonStorageService::read('settings.json', []);

        try {
            $response = $this->actingAs($this->admin)->post('/admin/settings/save', [
                'phone' => '+91 9131668156',
                'whatsapp' => '919131668156',
                'email' => 'info@artizenevents.com',
                'meta_title' => 'ARTIZEN SEO Meta Title Test',
                'meta_description' => 'ARTIZEN SEO Meta Description Test',
                'meta_keywords' => 'event decor, Indore packages'
            ]);

            $response->assertRedirect();

            $updatedSettings = JsonStorageService::read('settings.json', []);
            $this::assertEquals('ARTIZEN SEO Meta Title Test', $updatedSettings['meta_title']);
            $this::assertEquals('ARTIZEN SEO Meta Description Test', $updatedSettings['meta_description']);

        } finally {
            JsonStorageService::write('settings.json', $originalSettings);
        }
    }

    /**
     * Guest access security test
     */
    public function test_guest_blocked_from_all_admin_cms_routes(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
        $this->post('/admin/cms/save')->assertRedirect('/admin/login');
        $this->post('/admin/categories/save')->assertRedirect('/admin/login');
        $this->post('/admin/faqs/save')->assertRedirect('/admin/login');
        $this->post('/admin/settings/save')->assertRedirect('/admin/login');
    }
}

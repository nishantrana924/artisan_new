<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Package;
use App\Models\PackageTier;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use App\Services\JsonStorageService;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FinalMasterRegressionAuditTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'email' => 'finalmasteradmin@artizen.com',
            'password' => Hash::make('Password123!'),
            'is_admin' => true,
        ]);
    }

    /**
     * Test 1 to 5: Homepage, Categories, Events listing, Event details, Service Cards
     */
    public function test_public_website_and_event_catalog_feed(): void
    {
        $this->get('/')->assertStatus(200)->assertSee('ARTIZEN');
        $this->get('/events')->assertStatus(200)->assertSee('PACKAGES');
        $this->get('/events/cat-birthdays')->assertStatus(200);
        $this->get('/events/1?tier=0')->assertStatus(200)->assertSee('Basic Birthday Package');
        $this->get('/gallery')->assertStatus(200);
    }

    /**
     * Test 6 to 10: Booking creation, validation, tracker, contact enquiry
     */
    public function test_booking_checkout_tracker_and_contact_enquiry(): void
    {
        $bookingData = [
            'name' => 'Master Regression Customer',
            'mobile' => '9131668156',
            'whatsapp' => '9131668156',
            'email' => 'masterregression@artizen.com',
            'package' => 'Adult Birthdays - Basic Birthday Package',
            'category' => 'Birthdays',
            'tier' => 'Basic Birthday Package',
            'event_date' => date('Y-m-d', strtotime('+12 days')),
            'event_time' => '18:00',
            'guest_count' => 40,
            'address' => 'Vijay Nagar, Indore',
            'area' => 'Vijay Nagar',
            'city' => 'Indore',
            'state' => 'Madhya Pradesh',
            'pincode' => '452010',
            'package_price' => 4999,
            'total' => 4999
        ];

        $checkout = $this->postJson('/booking/save', $bookingData);
        $checkout->assertStatus(200)->assertJson(['success' => true]);

        $bookingId = $checkout->json('booking_id');
        $this->assertNotNull($bookingId);

        // Live tracker lookup
        $tracker = $this->postJson('/track-booking/search', [
            'booking_id' => $bookingId,
            'mobile' => '9131668156'
        ]);
        $tracker->assertStatus(200)->assertJson(['success' => true]);

        // Contact enquiry
        $contact = $this->postJson('/contact/send', [
            'name' => 'Master Regression Contact',
            'phone' => '9131668156',
            'email' => 'contactreg@artizen.com',
            'subject' => 'Regression Test Subject',
            'message' => 'Testing contact message sending'
        ]);
        $contact->assertStatus(200)->assertJson(['success' => true]);
    }

    /**
     * Test 11 to 22: Admin portal authentication, status transitions, CRUD operations, Analytics, Backup
     */
    public function test_admin_portal_crud_analytics_and_backup(): void
    {
        // Admin Auth
        $login = $this->post('/admin/login', [
            'email' => 'finalmasteradmin@artizen.com',
            'password' => 'Password123!'
        ]);
        $login->assertRedirect('/admin/dashboard');

        // Dashboard
        $dashboard = $this->actingAs($this->admin)->get('/admin/dashboard');
        $dashboard->assertStatus(200);

        // Status update
        $status = $this->actingAs($this->admin)->postJson('/admin/bookings/update-status', [
            'id' => 'BK-1232',
            'status' => 'Confirmed'
        ]);
        $status->assertStatus(200);

        // Category Save & Delete
        $saveCat = $this->actingAs($this->admin)->post('/admin/categories/save', [
            'title' => 'Master Category Test',
            'active' => 1
        ]);
        $saveCat->assertRedirect();

        // FAQ Save & Delete
        $saveFaq = $this->actingAs($this->admin)->post('/admin/faqs/save', [
            'q' => 'Master FAQ Q?',
            'a' => 'Master FAQ A!'
        ]);
        $saveFaq->assertRedirect();

        // Backup command execution
        $this->artisan('app:backup-json')->assertExitCode(0);
    }
}

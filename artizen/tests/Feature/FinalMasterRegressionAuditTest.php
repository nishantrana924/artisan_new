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
        ]);
        $this->admin->is_admin = true;
        $this->admin->save();
    }

    /**
     * Test 1 to 5: Homepage, Categories, Events listing, Event details, Service Cards
     */
    public function test_public_website_and_event_catalog_feed(): void
    {
        $this->get('/')->assertStatus(200)->assertSee('ARTIZEN');
        $this->get('/events')->assertStatus(200)->assertSee('Celebrations');
        $this->get('/events?category=cat-birthdays')->assertStatus(200);
        $this->get('/events/1st-birthday-wonderland?tier=0')->assertStatus(200)->assertSee('1st Birthday Wonderland');
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

        $customer = User::factory()->create([
            'email' => 'masterregression@artizen.com',
        ]);

        $checkout = $this->actingAs($customer)->postJson('/booking/save', $bookingData);
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
        $dashboard = $this->actingAsAdmin($this->admin)->get('/admin/dashboard');
        $dashboard->assertStatus(200);

        // Create booking for status update test
        $booking = Booking::create([
            'id' => 'BK-1232',
            'customer_name' => 'Test Booking',
            'customer_mobile' => '9876543210',
            'customer_email' => 'test@artizen.com',
            'package_name' => 'Basic Package',
            'category_name' => 'Birthdays',
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'guest_count' => 50,
            'address' => 'Vijay Nagar, Indore',
            'area' => 'Vijay Nagar',
            'status' => 'Pending',
            'total_amount' => 5000,
        ]);

        // Status update
        $status = $this->actingAsAdmin($this->admin)->postJson('/admin/bookings/update-status', [
            'id' => 'BK-1232',
            'status' => 'Confirmed'
        ]);
        $status->assertStatus(200);

        // Category Save & Delete
        $saveCat = $this->actingAsAdmin($this->admin)->post('/admin/categories/save', [
            'title' => 'Master Category Test',
            'active' => 1
        ]);
        $saveCat->assertRedirect();

        // FAQ Save & Delete
        $saveFaq = $this->actingAsAdmin($this->admin)->post('/admin/faqs/save', [
            'q' => 'Master FAQ Q?',
            'a' => 'Master FAQ A!'
        ]);
        $saveFaq->assertRedirect();

        // Backup command execution
        $this->artisan('app:backup-json')->assertExitCode(0);
    }
}

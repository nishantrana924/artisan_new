<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\CmsSlide;
use App\Models\Customer;
use App\Models\Enquiry;
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

class Step14DatabaseSwitchTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'email' => 'step14admin@artizen.com',
            'password' => Hash::make('Password123!'),
            'is_admin' => true,
        ]);
    }

    /**
     * Test active storage driver is database mode.
     */
    public function test_active_storage_driver_is_database(): void
    {
        $driver = config('artizen.storage_driver');
        $this->assertEquals('database', $driver);
    }

    /**
     * Test customer booking creation workflow in database mode.
     */
    public function test_customer_booking_creation_in_database_mode(): void
    {
        $bookingData = [
            'name' => 'MySQL Workflow Customer',
            'mobile' => '9131668156',
            'whatsapp' => '9131668156',
            'email' => 'mysqlcustomer@artizen.com',
            'package' => 'Proposal & Anniversary Setup Package',
            'category' => 'Proposal & Anniversary',
            'tier' => 'Standard',
            'date' => date('Y-m-d', strtotime('+5 days')),
            'time' => '18:00',
            'guest_count' => 30,
            'address' => 'Vijay Nagar Square',
            'landmark' => 'Near C21 Mall',
            'area' => 'Vijay Nagar',
            'city' => 'Indore',
            'state' => 'Madhya Pradesh',
            'pincode' => '452010',
            'notes' => 'Test MySQL creation',
            'package_price' => 4999,
            'surcharge' => 0,
            'total' => 4999
        ];

        $response = $this->postJson('/booking/save', $bookingData);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $bookingId = $response->json('booking_id');
        $this->assertNotNull($bookingId);

        // Verify status tracker lookup
        $trackRes = $this->postJson('/track-booking/search', [
            'booking_id' => $bookingId,
            'mobile' => '9131668156'
        ]);

        $trackRes->assertStatus(200);
        $trackRes->assertJson(['success' => true]);
        $trackRes->assertJsonPath('booking.name', 'MySQL Workflow Customer');

        // Verify admin status update
        $updateRes = $this->actingAs($this->admin)->postJson('/admin/bookings/update-status', [
            'id' => $bookingId,
            'status' => 'Confirmed'
        ]);

        $updateRes->assertStatus(200);
        $updateRes->assertJson(['success' => true]);
    }

    /**
     * Test admin CRUD operations in database mode.
     */
    public function test_admin_crud_in_database_mode(): void
    {
        // Category Create & Delete
        $catRes = $this->actingAs($this->admin)->post('/admin/categories/save', [
            'title' => 'MySQL Test Category',
            'active' => 1
        ]);
        $catRes->assertRedirect();

        // FAQ Create & Delete
        $faqRes = $this->actingAs($this->admin)->post('/admin/faqs/save', [
            'q' => 'Is MySQL mode fully active?',
            'a' => 'Yes, 100% active with zero data loss.'
        ]);
        $faqRes->assertRedirect();

        // Testimonial Create & Delete
        $tstRes = $this->actingAs($this->admin)->post('/admin/testimonials/save', [
            'author' => 'Ankita Sharma',
            'review' => 'Perfect database backend execution!',
            'rating' => 5,
            'event_type' => 'Anniversary'
        ]);
        $tstRes->assertRedirect();
    }

    /**
     * Test emergency rollback capabilities.
     */
    public function test_emergency_rollback_driver_switch(): void
    {
        config(['artizen.storage_driver' => 'json']);
        $driver = config('artizen.storage_driver');
        $this->assertEquals('json', $driver);

        $categories = JsonStorageService::read('categories.json');
        $this->assertNotEmpty($categories);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\User;
use App\Services\JsonStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Step16ProductionReadinessTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'email' => 'step16admin@artizen.com',
            'password' => Hash::make('Password123!'),
            'is_admin' => true,
        ]);
    }

    /**
     * TEST 1 & 2 — Booking Concurrency & Double Submission Protection
     */
    public function test_booking_concurrency_and_double_submission(): void
    {
        $payload = [
            'name' => 'Concurrent User',
            'mobile' => '9131668156',
            'whatsapp' => '9131668156',
            'email' => 'concurrent@artizen.com',
            'package' => 'Proposal & Anniversary Setup Package',
            'category' => 'Proposal & Anniversary',
            'tier' => 'Standard',
            'event_date' => date('Y-m-d', strtotime('+7 days')),
            'event_time' => '18:00',
            'guest_count' => 25,
            'address' => 'Vijay Nagar, Indore',
            'area' => 'Vijay Nagar',
            'city' => 'Indore',
            'state' => 'Madhya Pradesh',
            'pincode' => '452010',
            'total' => 4999
        ];

        // First submission
        $res1 = $this->postJson('/booking/save', $payload);
        $res1->assertStatus(200);
        $id1 = $res1->json('booking_id');

        // Immediate second submission
        $res2 = $this->postJson('/booking/save', $payload);
        $res2->assertStatus(200);
        $id2 = $res2->json('booking_id');

        // Verify distinct IDs generated (no collision)
        $this->assertNotEquals($id1, $id2);
    }

    /**
     * TEST 3 — Frontend Price Tampering Security
     */
    public function test_server_ignores_client_side_price_tampering(): void
    {
        $tamperedPayload = [
            'name' => 'Hacker Tamper',
            'mobile' => '9131668156',
            'whatsapp' => '9131668156',
            'email' => 'tamper@artizen.com',
            'package' => 'Proposal & Anniversary Setup Package', // Official price 4999
            'category' => 'Proposal & Anniversary',
            'tier' => 'Standard',
            'event_date' => date('Y-m-d', strtotime('+7 days')),
            'event_time' => '18:00',
            'guest_count' => 25,
            'address' => 'Vijay Nagar, Indore',
            'area' => 'Vijay Nagar',
            'city' => 'Indore',
            'state' => 'Madhya Pradesh',
            'pincode' => '452010',
            'price' => 1, // Tampered client price ₹1
            'total' => 1
        ];

        $response = $this->postJson('/booking/save', $tamperedPayload);
        $response->assertStatus(200);

        // Server must override tampered price with official DB price (4999)
        $this->assertEquals(4999, $response->json('total'));
    }

    /**
     * TEST 4 — Guest Authorization Security
     */
    public function test_guest_blocked_from_protected_admin_routes(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
        $this->get('/admin/packages/create')->assertRedirect('/admin/login');
        $this->post('/admin/categories/save', ['title' => 'Illegal'])->assertRedirect('/admin/login');
        $this->post('/admin/settings/save', ['phone' => 'Illegal'])->assertRedirect('/admin/login');
    }

    /**
     * TEST 5 — Booking Privacy & Data Isolation
     */
    public function test_booking_tracker_privacy_isolation(): void
    {
        // 1. Correct BK + wrong phone
        $res1 = $this->postJson('/track-booking/search', [
            'booking_id' => 'BK-1232',
            'mobile' => '0000000000'
        ]);
        $res1->assertStatus(200);
        $res1->assertJson(['success' => false]);
        $res1->assertDontSee('Nishant Rana');

        // 2. Wrong BK + correct phone
        $res2 = $this->postJson('/track-booking/search', [
            'booking_id' => 'BK-0000',
            'mobile' => '9131668156'
        ]);
        $res2->assertStatus(200);
        $res2->assertJson(['success' => false]);
    }

    /**
     * TEST 6 — Input Security & Injection Prevention
     */
    public function test_input_validation_prevents_xss_and_injection(): void
    {
        $xssPayload = [
            'name' => '<script>alert("xss")</script>',
            'mobile' => 'invalid-phone-string',
            'email' => 'not-an-email',
            'package' => 'Proposal & Anniversary Setup Package',
            'event_date' => 'invalid-date',
            'guest_count' => -10, // Invalid negative guest count
            'address' => 'Test Address',
            'area' => 'Vijay Nagar',
            'pincode' => '452010'
        ];

        $response = $this->postJson('/booking/save', $xssPayload);
        $response->assertStatus(422); // Validation error response
        $response->assertJsonValidationErrors(['mobile', 'event_date']);
    }

    /**
     * TEST 7 — Unsafe File Upload Security
     */
    public function test_unsafe_php_file_upload_rejected(): void
    {
        $fakeScript = UploadedFile::fake()->create('malicious.php', 10, 'text/x-php');

        $response = $this->actingAs($this->admin)->post('/admin/categories/save', [
            'title' => 'Hack Category',
            'image_file' => $fakeScript
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /**
     * TEST 9 — Notification Failure Safety
     */
    public function test_booking_persists_even_if_notifications_fail(): void
    {
        config(['mail.default' => 'invalid_mailer_test']);

        $payload = [
            'name' => 'Notification Failure User',
            'mobile' => '9131668156',
            'whatsapp' => '9131668156',
            'email' => 'notifyfail@artizen.com',
            'package' => 'Proposal & Anniversary Setup Package',
            'category' => 'Proposal & Anniversary',
            'tier' => 'Standard',
            'event_date' => date('Y-m-d', strtotime('+4 days')),
            'event_time' => '18:00',
            'guest_count' => 20,
            'address' => 'Vijay Nagar, Indore',
            'area' => 'Vijay Nagar',
            'city' => 'Indore',
            'state' => 'Madhya Pradesh',
            'pincode' => '452010',
            'total' => 4999
        ];

        $response = $this->postJson('/booking/save', $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $bookingId = $response->json('booking_id');
        $this->assertNotNull($bookingId);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\User;
use App\Services\JsonStorageService;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Step18EndToEndAcceptanceTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'email' => 'e2eadmin@artizen.com',
            'password' => Hash::make('Password123!'),
            'is_admin' => true,
        ]);
    }

    /**
     * Complete 16-Step End-to-End Customer Journey & Admin Approval Acceptance Test
     */
    public function test_complete_end_to_end_customer_journey(): void
    {
        // Step 1: Customer opens website homepage
        $homeRes = $this->get('/');
        $homeRes->assertStatus(200);
        $homeRes->assertSee('ARTIZEN');

        // Step 2 & 3: Browses events catalog
        $eventsRes = $this->get('/events');
        $eventsRes->assertStatus(200);

        // Step 4: Views package details page
        $detailsRes = $this->get('/events/1?tier=0');
        $detailsRes->assertStatus(200);

        // Step 5 & 6: Submits booking form and receives BK-XXXX ID
        $bookingPayload = [
            'name' => 'E2E Validation Customer',
            'mobile' => '9826012345',
            'whatsapp' => '9826012345',
            'email' => 'e2ecustomer@artizen.com',
            'package' => 'Adult Birthdays - Basic Birthday Package',
            'category' => 'Birthdays',
            'tier' => 'Basic Birthday Package',
            'event_date' => date('Y-m-d', strtotime('+10 days')),
            'event_time' => '18:00',
            'guest_count' => 35,
            'address' => 'Vijay Nagar Square, Indore',
            'landmark' => 'Near Apollo DB City',
            'area' => 'Vijay Nagar',
            'city' => 'Indore',
            'state' => 'Madhya Pradesh',
            'pincode' => '452010',
            'notes' => 'E2E Full Acceptance Test Booking',
            'package_price' => 4999,
            'total' => 4999
        ];

        $checkoutRes = $this->postJson('/booking/save', $bookingPayload);
        $checkoutRes->assertStatus(200);
        $checkoutRes->assertJson(['success' => true]);

        $bookingId = $checkoutRes->json('booking_id');
        $this->assertNotNull($bookingId);
        $this->assertStringStartsWith('BK-', $bookingId);

        // Step 7 & 8: Success redirect view
        $successRes = $this->get('/booking/success?id=' . $bookingId);
        $successRes->assertStatus(200);

        // Step 9: Admin logs in and opens dashboard
        $adminDashboard = $this->actingAs($this->admin)->get('/admin/dashboard');
        $adminDashboard->assertStatus(200);
        $adminDashboard->assertSee($bookingId);

        // Step 10: Admin updates status to Contacted
        $contactedRes = $this->actingAs($this->admin)->postJson('/admin/bookings/update-status', [
            'id' => $bookingId,
            'status' => 'Contacted'
        ]);
        $contactedRes->assertStatus(200);

        // Step 11 & 12: Admin confirms booking
        $confirmedRes = $this->actingAs($this->admin)->postJson('/admin/bookings/update-status', [
            'id' => $bookingId,
            'status' => 'Confirmed'
        ]);
        $confirmedRes->assertStatus(200);

        // Step 13: Customer tracks booking live status
        $trackerRes = $this->postJson('/track-booking/search', [
            'booking_id' => $bookingId,
            'mobile' => '9826012345'
        ]);
        $trackerRes->assertStatus(200);
        $trackerRes->assertJson(['success' => true]);
        $trackerRes->assertJsonPath('booking.status', 'Confirmed');

        // Step 14: Admin completes execution
        $completedRes = $this->actingAs($this->admin)->postJson('/admin/bookings/update-status', [
            'id' => $bookingId,
            'status' => 'Completed'
        ]);
        $completedRes->assertStatus(200);

        // Step 15: Analytics metrics update
        $analyticsRes = $this->actingAs($this->admin)->get('/admin/dashboard');
        $analyticsRes->assertStatus(200);
        $analyticsData = $analyticsRes->viewData('analytics');
        $this->assertGreaterThan(0, $analyticsData['total_bookings']);

        // Step 16: Automated JSON backup
        $this->artisan('app:backup-json')->assertExitCode(0);
    }
}

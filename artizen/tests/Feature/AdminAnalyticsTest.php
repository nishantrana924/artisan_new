<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\JsonStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAnalyticsTest extends TestCase
{
    /**
     * Test guest cannot access analytics dashboard.
     */
    public function test_guest_cannot_access_analytics(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test authenticated admin can access analytics metrics.
     */
    public function test_admin_can_access_analytics_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'analyticsadmin@artizen.com',
            'password' => Hash::make('Password123!'),
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('analytics');
    }

    /**
     * Test metrics calculation logic and cancelled booking exclusion from revenue.
     */
    public function test_analytics_revenue_and_pipeline_calculation(): void
    {
        $admin = User::factory()->create([
            'email' => 'calcadmin@artizen.com',
            'password' => Hash::make('Password123!'),
            'is_admin' => true,
        ]);

        // Backup current bookings
        $originalBookings = JsonStorageService::read('bookings.json', []);

        // Mock test bookings
        $mockBookings = [
            [
                'id' => 'BK-TEST1',
                'name' => 'Customer One',
                'status' => 'Pending',
                'total' => 5000,
                'category' => 'Birthday Party',
                'package' => 'Gold Tier',
                'date' => date('Y-m-d', strtotime('+2 days')),
                'created_at' => date('d M Y, h:i A')
            ],
            [
                'id' => 'BK-TEST2',
                'name' => 'Customer Two',
                'status' => 'Confirmed',
                'total' => 10000,
                'category' => 'Birthday Party',
                'package' => 'Gold Tier',
                'date' => date('Y-m-d', strtotime('+3 days')),
                'created_at' => date('d M Y, h:i A')
            ],
            [
                'id' => 'BK-TEST3',
                'name' => 'Customer Three',
                'status' => 'Completed',
                'total' => 15000,
                'category' => 'Wedding',
                'package' => 'Royal Stage',
                'date' => date('Y-m-d', strtotime('-5 days')),
                'created_at' => date('d M Y, h:i A')
            ],
            [
                'id' => 'BK-TEST4',
                'name' => 'Customer Four',
                'status' => 'Cancelled',
                'total' => 20000,
                'category' => 'Wedding',
                'package' => 'Royal Stage',
                'date' => date('Y-m-d', strtotime('+1 day')),
                'created_at' => date('d M Y, h:i A')
            ],
        ];

        JsonStorageService::write('bookings.json', $mockBookings);

        try {
            $response = $this->actingAs($admin)->get('/admin/dashboard');
            $response->assertStatus(200);

            $analytics = $response->viewData('analytics');

            $this::assertEquals(4, $analytics['total_bookings']);
            $this::assertEquals(1, $analytics['pending_count']);
            $this::assertEquals(1, $analytics['confirmed_count']);
            $this::assertEquals(1, $analytics['completed_count']);
            $this::assertEquals(1, $analytics['cancelled_count']);

            // Pipeline = Pending (5000) + Confirmed (10000) = 15000
            $this::assertEquals(15000, $analytics['pipeline_value']);

            // Confirmed Revenue = Confirmed (10000) + Completed (15000) = 25000 (Cancelled NOT included!)
            $this::assertEquals(25000, $analytics['confirmed_revenue']);

            // ABV = (5000 + 10000 + 15000) / 3 non-cancelled = 10000
            $this::assertEquals(10000, $analytics['avg_booking_value']);

            // Upcoming events = TEST1 + TEST2 (TEST4 is cancelled) = 2
            $this::assertEquals(2, $analytics['upcoming_count']);

        } finally {
            // Restore original bookings
            JsonStorageService::write('bookings.json', $originalBookings);
        }
    }

    /**
     * Test zero division safety when no bookings exist.
     */
    public function test_analytics_handles_empty_bookings_without_zero_division(): void
    {
        $admin = User::factory()->create([
            'email' => 'zerodivadmin@artizen.com',
            'password' => Hash::make('Password123!'),
            'is_admin' => true,
        ]);

        $originalBookings = JsonStorageService::read('bookings.json', []);
        JsonStorageService::write('bookings.json', []);

        try {
            $response = $this->actingAs($admin)->get('/admin/dashboard?time_filter=7_days');
            $response->assertStatus(200);

            $analytics = $response->viewData('analytics');
            $this::assertEquals(0, $analytics['total_bookings']);
            $this::assertEquals(0, $analytics['avg_booking_value']);
            $this::assertEquals(0, $analytics['confirmed_revenue']);
            $this::assertEquals(0, $analytics['pipeline_value']);
        } finally {
            JsonStorageService::write('bookings.json', $originalBookings);
        }
    }
}

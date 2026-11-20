<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationPollingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['session.driver' => 'array']);
        config(['auth.defaults.guard' => 'web']);
    }

    public function test_guest_cannot_access_notifications_poll(): void
    {
        $response = $this->getJson(route('admin.notifications.poll'));
        $response->assertStatus(401);
    }

    public function test_admin_can_poll_notifications_and_receive_valid_payload(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'email'    => 'admin@artizens.in',
            'password' => bcrypt('Admin@123456'),
        ]);

        $customer = Customer::create([
            'name'     => 'Ankit Verma',
            'mobile'   => '9876543210',
            'whatsapp' => '9876543210',
            'email'    => 'ankit@example.com',
            'city'     => 'Indore',
            'state'    => 'Madhya Pradesh',
        ]);

        $booking = Booking::create([
            'id'              => 'BK-9999',
            'customer_id'     => $customer->id,
            'customer_name'   => 'Ankit Verma',
            'customer_mobile' => '9876543210',
            'package_name'    => 'Birthday Celebration Setup',
            'category_name'   => 'Birthdays',
            'event_date'      => '2026-11-15',
            'event_time'      => '18:00',
            'address'         => '123 MG Road',
            'area'            => 'Vijay Nagar',
            'city'            => 'Indore',
            'total_amount'    => 5499.00,
            'status'          => 'Pending',
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['admin_logged_in' => true, 'admin_email' => $admin->email])
            ->getJson(route('admin.notifications.poll'));

        $response->assertStatus(200)
            ->assertJson([
                'success'       => true,
                'pending_count' => 1,
            ])
            ->assertJsonStructure([
                'success',
                'pending_count',
                'latest_id',
                'server_time',
                'latest_bookings',
                'new_bookings',
            ]);
    }

    public function test_admin_poll_detects_new_bookings_since_timestamp(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'email'    => 'admin@artizens.in',
            'password' => bcrypt('Admin@123456'),
        ]);

        $pastTime = time() - 60;

        $booking = Booking::create([
            'id'              => 'BK-9998',
            'customer_name'   => 'Pooja Jain',
            'customer_mobile' => '9876500000',
            'package_name'    => 'Anniversary Candlelight Dinner',
            'category_name'   => 'Anniversary',
            'event_date'      => '2026-11-20',
            'event_time'      => '20:00',
            'address'         => '456 Palasia',
            'area'            => 'Palasia',
            'city'            => 'Indore',
            'total_amount'    => 3999.00,
            'status'          => 'Pending',
        ]);



        $response = $this->actingAs($admin)
            ->withSession(['admin_logged_in' => true, 'admin_email' => $admin->email])
            ->getJson(route('admin.notifications.poll', ['since' => $pastTime]));

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertNotEmpty($data['new_bookings']);
        $this->assertEquals('BK-9998', $data['new_bookings'][0]['id']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\WhatsAppSmsService;
use App\Services\JsonStorageService;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WhatsAppSmsNotificationTest extends TestCase
{
    /**
     * Test provider configuration detector returns false when API keys are unconfigured.
     */
    public function test_provider_is_not_configured_by_default(): void
    {
        $this->assertFalse(WhatsAppSmsService::isConfigured());
    }

    /**
     * Test message formatters generate clean, professional content with required fields.
     */
    public function test_message_formatters_contain_required_booking_details(): void
    {
        $service = new WhatsAppSmsService();
        $dummyBooking = [
            'id' => 'BK-9999',
            'name' => 'John Doe',
            'mobile' => '9131668156',
            'package' => 'Gold Party Package',
            'category' => 'Birthday Party',
            'date' => '2026-09-01',
            'time' => '19:00',
            'guest_count' => 50,
            'address' => '123 Vijay Nagar',
            'area' => 'Vijay Nagar',
            'city' => 'Indore',
            'total' => 9999,
            'status' => 'Pending'
        ];

        $receivedMsg = $service->buildBookingReceivedMessage($dummyBooking);
        $this->assertStringContainsString('BK-9999', $receivedMsg);
        $this->assertStringContainsString('John Doe', $receivedMsg);
        $this->assertStringContainsString('Gold Party Package', $receivedMsg);
        $this->assertStringContainsString('Pending', $receivedMsg);

        $adminMsg = $service->buildAdminNewBookingMessage($dummyBooking);
        $this->assertStringContainsString('NEW ARTIZEN BOOKING', $adminMsg);
        $this->assertStringContainsString('9131668156', $adminMsg);

        $confirmedMsg = $service->buildBookingConfirmedMessage($dummyBooking);
        $this->assertStringContainsString('CONFIRMED', $confirmedMsg);
        $this->assertStringContainsString('Balance:', $confirmedMsg);

        $cancelledMsg = $service->buildBookingCancelledMessage($dummyBooking);
        $this->assertStringContainsString('CANCELLED', $cancelledMsg);
    }

    /**
     * Test unconfigured provider handles dispatch gracefully without throwing HTTP 500 error.
     */
    public function test_unconfigured_provider_returns_not_configured_status(): void
    {
        $service = new WhatsAppSmsService();
        $dummyBooking = [
            'id' => 'BK-9999',
            'name' => 'John Doe',
            'mobile' => '9131668156',
            'package' => 'Gold Package',
            'date' => '2026-09-01',
            'time' => '19:00',
            'total' => 9999,
            'status' => 'Pending'
        ];

        $res = $service->sendCustomerBookingReceived($dummyBooking);
        $this->assertFalse($res['success']);
        $this->assertEquals('NOT_CONFIGURED', $res['status']);
    }

    /**
     * Test duplicate protection prevents redundant status notifications.
     */
    public function test_duplicate_notification_protection(): void
    {
        $admin = User::factory()->create([
            'email' => 'wadmin@artizen.com',
            'password' => Hash::make('Password123!'),
            'is_admin' => true,
        ]);

        $originalBookings = JsonStorageService::read('bookings.json', []);
        $testId = 'BK-DUPTEST';

        $mockBooking = [
            'id' => $testId,
            'name' => 'Dup Test User',
            'mobile' => '9131668156',
            'package' => 'Test Pkg',
            'total' => 4999,
            'status' => 'Confirmed',
            'notifications_sent' => ['confirmed', 'confirmed_wa']
        ];

        JsonStorageService::write('bookings.json', [$mockBooking]);

        try {
            // Re-submitting same confirmed status
            $response = $this->actingAs($admin)->postJson('/admin/bookings/update-status', [
                'id' => $testId,
                'status' => 'Confirmed'
            ]);

            $response->assertStatus(200);
            $response->assertJson(['success' => true]);

            $updatedBookings = JsonStorageService::read('bookings.json', []);
            $updatedItem = $updatedBookings[0];

            // Count of confirmed_wa should remain 1 (no duplicate added)
            $waCount = array_count_values($updatedItem['notifications_sent'])['confirmed_wa'] ?? 0;
            $this->assertEquals(1, $waCount);

        } finally {
            JsonStorageService::write('bookings.json', $originalBookings);
        }
    }
}

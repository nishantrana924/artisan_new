<?php

namespace Tests\Feature;

use App\Jobs\SendWhatsAppBookingNotificationJob;
use App\Models\Booking;
use App\Models\User;
use App\Models\WhatsAppNotification;
use App\Services\WhatsAppSmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MetaWhatsAppCloudApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test phone number formatting and Indian phone number validation rules.
     */
    public function test_phone_number_formatting_and_validation(): void
    {
        // Formatting standard 10-digit
        $this->assertEquals('919131668156', WhatsAppSmsService::formatPhoneNumber('9131668156'));
        $this->assertTrue(WhatsAppSmsService::isValidIndianPhoneNumber('9131668156'));

        // Formatting with +91 and spaces/dashes
        $this->assertEquals('919131668156', WhatsAppSmsService::formatPhoneNumber('+91 91316-68156'));
        $this->assertTrue(WhatsAppSmsService::isValidIndianPhoneNumber('+91 91316-68156'));

        // Leading zero
        $this->assertEquals('919131668156', WhatsAppSmsService::formatPhoneNumber('09131668156'));
        $this->assertTrue(WhatsAppSmsService::isValidIndianPhoneNumber('09131668156'));

        // Already 12-digit format
        $this->assertEquals('919131668156', WhatsAppSmsService::formatPhoneNumber('919131668156'));
        $this->assertTrue(WhatsAppSmsService::isValidIndianPhoneNumber('919131668156'));

        // Invalid numbers (starting with 1-5, short numbers, empty)
        $this->assertFalse(WhatsAppSmsService::isValidIndianPhoneNumber('1234567890'));
        $this->assertFalse(WhatsAppSmsService::isValidIndianPhoneNumber('91316'));
        $this->assertFalse(WhatsAppSmsService::isValidIndianPhoneNumber(''));
    }

    /**
     * Test admin new booking notification message sanitizes inputs and bounds customer notes.
     */
    public function test_admin_new_booking_message_sanitizes_html_and_bounds_notes(): void
    {
        $service = new WhatsAppSmsService();

        $excessiveNotes = str_repeat('Extra fairy lights requested. ', 30); // ~900 characters

        $booking = [
            'id' => 'BK-WA9900',
            'name' => '<script>alert("xss")</script>Priya Patel',
            'mobile' => '9826011223',
            'whatsapp' => '9826011223',
            'package' => '<b>Adult Birthdays</b> - Premium Setup',
            'category' => 'Birthdays',
            'tier' => 'Premium Setup',
            'date' => '2026-11-25',
            'time' => '07:00 PM',
            'guest_count' => 50,
            'address' => 'Flat 301, <i>Shalimar Palms</i>',
            'landmark' => 'Near Bombay Hospital',
            'area' => 'Vijay Nagar',
            'city' => 'Indore',
            'pincode' => '452010',
            'total' => 14999,
            'notes' => $excessiveNotes,
        ];

        $message = $service->buildAdminNewBookingMessage($booking);

        // Assert XSS tags stripped
        $this->assertStringNotContainsString('<script>', $message);
        $this->assertStringNotContainsString('<b>', $message);
        $this->assertStringNotContainsString('<i>', $message);
        $this->assertStringContainsString('Priya Patel', $message);

        // Assert notes bounded to 300 chars
        preg_match('/Customer Notes:\s*(.*?)\n/s', $message, $m);
        $this->assertLessThanOrEqual(305, strlen(trim($m[1] ?? '')));

        // Assert links present and properly formatted
        $this->assertStringContainsString('https://wa.me/919826011223', $message);
        $this->assertStringContainsString('/admin/dashboard', $message);
        $this->assertStringContainsString('100% Offline Payment', $message);
    }

    /**
     * Test Meta Cloud API successfully dispatches HTTP POST payload to Meta Graph API.
     */
    public function test_meta_cloud_api_dispatches_http_post_to_meta_graph_api_when_configured(): void
    {
        Config::set('services.whatsapp.provider', 'meta');
        Config::set('services.whatsapp.access_token', 'EAAG_TEST_TOKEN_SECRET_XYZ');
        Config::set('services.whatsapp.phone_number_id', '109283746592817');
        Config::set('services.whatsapp.admin_number', '919131668156');
        Config::set('services.whatsapp.api_version', 'v21.0');

        $this->assertTrue(WhatsAppSmsService::isConfigured());

        Http::fake([
            'https://graph.facebook.com/v21.0/109283746592817/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'contacts' => [
                    ['input' => '919131668156', 'wa_id' => '919131668156']
                ],
                'messages' => [
                    ['id' => 'wamid.HBgTEST1234567890ABC']
                ]
            ], 200)
        ]);

        $service = new WhatsAppSmsService();
        $dummyBooking = [
            'id' => 'BK-META99',
            'name' => 'Rahul Verma',
            'mobile' => '9131668156',
            'whatsapp' => '9131668156',
            'package' => 'Proposal Setup',
            'date' => '2026-12-05',
            'time' => '18:00',
            'total' => 8999,
        ];

        $response = $service->sendAdminNewBooking($dummyBooking);

        $this->assertTrue($response['success']);
        $this->assertEquals('SENT', $response['status']);
        $this->assertEquals('meta', $response['provider']);
        $this->assertEquals('wamid.HBgTEST1234567890ABC', $response['message_id']);

        // Verify HTTP call details
        Http::assertSent(function ($request) {
            return $request->url() === 'https://graph.facebook.com/v21.0/109283746592817/messages'
                && $request->hasHeader('Authorization', 'Bearer EAAG_TEST_TOKEN_SECRET_XYZ')
                && $request['messaging_product'] === 'whatsapp'
                && $request['to'] === '919131668156'
                && $request['type'] === 'text'
                && str_contains($request['text']['body'], 'BK-META99');
        });
    }

    /**
     * Test Meta Cloud API error categorization (OAuth expiration, permissions, rate limits, service window).
     */
    public function test_meta_cloud_api_error_categorization(): void
    {
        Config::set('services.whatsapp.provider', 'meta');
        Config::set('services.whatsapp.access_token', 'TEST_TOKEN');
        Config::set('services.whatsapp.phone_number_id', '109283746592817');
        Config::set('services.whatsapp.admin_number', '919131668156');

        Http::fakeSequence()
            // Response 1: 401 OAuth token expired
            ->push(['error' => ['message' => 'Token expired', 'code' => 190]], 401)
            // Response 2: 400 Outside 24-hr service window
            ->push(['error' => ['message' => 'Service window exceeded', 'code' => 131030]], 400)
            // Response 3: 429 Rate limit
            ->push(['error' => ['message' => 'Rate limit exceeded', 'code' => 429]], 429);

        $service = new WhatsAppSmsService();
        $dummy = ['id' => 'BK-ERR01', 'name' => 'Error Test', 'mobile' => '9131668156'];

        $res1 = $service->sendAdminNewBooking($dummy);
        $this->assertFalse($res1['success']);
        $this->assertEquals('AUTHENTICATION_ERROR', $res1['error_category']);
        $this->assertEquals(190, $res1['error_code']);

        $res2 = $service->sendAdminNewBooking($dummy);
        $this->assertFalse($res2['success']);
        $this->assertEquals('SERVICE_WINDOW_ERROR', $res2['error_category']);
        $this->assertEquals(131030, $res2['error_code']);

        $res3 = $service->sendAdminNewBooking($dummy);
        $this->assertFalse($res3['success']);
        $this->assertEquals('RATE_LIMIT_ERROR', $res3['error_category']);
        $this->assertEquals(429, $res3['error_code']);
    }

    /**
     * Test booking creation dispatches queued job after commit and persists outbox entry.
     */
    public function test_booking_creation_dispatches_queued_job_and_saves_outbox(): void
    {
        Config::set('services.whatsapp.provider', 'meta');
        Config::set('services.whatsapp.access_token', 'EAAG_LIVE_TEST_TOKEN');
        Config::set('services.whatsapp.phone_number_id', '109283746592817');
        Config::set('services.whatsapp.admin_number', '919131668156');

        Http::fake([
            'https://graph.facebook.com/v21.0/109283746592817/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.HBgENDTOEND12345']]
            ], 200)
        ]);

        $customer = User::factory()->create([
            'email' => 'metacustomer@artizen.com',
        ]);

        $bookingData = [
            'name' => 'Pooja Verma',
            'mobile' => '9826012345',
            'whatsapp' => '9826012345',
            'email' => 'metacustomer@artizen.com',
            'package' => 'Adult Birthdays - Basic Birthday Package',
            'category' => 'Birthdays',
            'tier' => 'Basic Birthday Package',
            'event_date' => date('Y-m-d', strtotime('+7 days')),
            'event_time' => '19:00',
            'guest_count' => 30,
            'address' => 'Scheme 54, Near Meghdoot Garden',
            'area' => 'Vijay Nagar',
            'city' => 'Indore',
            'state' => 'Madhya Pradesh',
            'pincode' => '452010',
            'package_price' => 4999,
            'total' => 4999,
        ];

        $response = $this->actingAs($customer)->postJson('/booking/save', $bookingData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $bookingId = $response->json('booking_id');
        $this->assertNotNull($bookingId);

        // Booking must persist in database
        $this->assertDatabaseHas('bookings', [
            'id' => $bookingId,
            'customer_name' => 'Pooja Verma',
            'status' => 'Pending',
        ]);

        // Outbox record must be created and marked as sent
        $this->assertDatabaseHas('whatsapp_notifications', [
            'booking_id' => $bookingId,
            'recipient_type' => 'admin',
            'message_type' => 'admin_new_booking',
            'status' => 'sent',
            'wamid' => 'wamid.HBgENDTOEND12345',
        ]);
    }

    /**
     * Test idempotency: re-running job does not dispatch duplicate external WhatsApp API calls.
     */
    public function test_idempotency_prevents_duplicate_whatsapp_dispatches(): void
    {
        Config::set('services.whatsapp.provider', 'meta');
        Config::set('services.whatsapp.access_token', 'EAAG_LIVE_TEST_TOKEN');
        Config::set('services.whatsapp.phone_number_id', '109283746592817');

        Http::fake();

        // Create an already sent notification
        $testBookingId = 'BK-IDEMPOTENT1';
        WhatsAppNotification::create([
            'booking_id' => $testBookingId,
            'recipient_type' => 'admin',
            'recipient_phone' => '919131668156',
            'message_type' => 'admin_new_booking',
            'provider' => 'meta',
            'wamid' => 'wamid.ALREADY_SENT_123',
            'status' => 'sent',
            'sent_at' => now(),
            'attempts' => 1,
        ]);

        // Attempt running the job again
        $job = new SendWhatsAppBookingNotificationJob($testBookingId, 'admin_new_booking');
        $job->handle(new WhatsAppSmsService());

        // Zero HTTP calls must be made because notification is already sent
        Http::assertNothingSent();
    }

    /**
     * Test Meta Webhook verification handshake (GET /webhook/whatsapp).
     */
    public function test_webhook_verification_handshake(): void
    {
        Config::set('services.whatsapp.webhook_verify_token', 'artizen_secret_webhook_verify_token_123');

        // Valid verification request
        $response = $this->get('/webhook/whatsapp?hub_mode=subscribe&hub_verify_token=artizen_secret_webhook_verify_token_123&hub_challenge=1158201244');
        $response->assertStatus(200);
        $this->assertEquals('1158201244', $response->getContent());

        // Invalid verification token
        $badResponse = $this->get('/webhook/whatsapp?hub_mode=subscribe&hub_verify_token=wrong_token&hub_challenge=1158201244');
        $badResponse->assertStatus(403);
    }

    /**
     * Test Meta Webhook signature validation and status ingestion (delivered, read, failed).
     */
    public function test_webhook_signature_validation_and_status_ingestion(): void
    {
        $appSecret = 'artizen_meta_app_secret_xyz';
        Config::set('services.whatsapp.app_secret', $appSecret);

        $testWamid = 'wamid.HBgTESTDELIVERY99';
        $notification = WhatsAppNotification::create([
            'booking_id' => 'BK-WEBHOOKTEST',
            'recipient_type' => 'admin',
            'recipient_phone' => '919131668156',
            'message_type' => 'admin_new_booking',
            'provider' => 'meta',
            'wamid' => $testWamid,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $payload = [
            'object' => 'whatsapp_business_account',
            'entry' => [
                [
                    'id' => 'WABA_12345',
                    'changes' => [
                        [
                            'field' => 'messages',
                            'value' => [
                                'messaging_product' => 'whatsapp',
                                'statuses' => [
                                    [
                                        'id' => $testWamid,
                                        'status' => 'delivered',
                                        'timestamp' => '1730000000',
                                        'recipient_id' => '919131668156'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $rawBody = json_encode($payload);
        $validSignature = 'sha256=' . hash_hmac('sha256', $rawBody, $appSecret);

        // 1. Invalid signature should return 401
        $badResponse = $this->call(
            'POST',
            '/webhook/whatsapp',
            [],
            [],
            [],
            ['HTTP_X_HUB_SIGNATURE_256' => 'sha256=invalid_hash', 'CONTENT_TYPE' => 'application/json'],
            $rawBody
        );
        $badResponse->assertStatus(401);

        // 2. Valid signature should process successfully and update status to delivered
        $goodResponse = $this->call(
            'POST',
            '/webhook/whatsapp',
            [],
            [],
            [],
            ['HTTP_X_HUB_SIGNATURE_256' => $validSignature, 'CONTENT_TYPE' => 'application/json'],
            $rawBody
        );
        $goodResponse->assertStatus(200);
        $this->assertEquals('EVENT_RECEIVED', $goodResponse->getContent());

        $notification->refresh();
        $this->assertEquals('delivered', $notification->status);
        $this->assertNotNull($notification->delivered_at);
    }

    /**
     * Test that WhatsApp API failure never rolls back an already committed booking.
     */
    public function test_whatsapp_failure_does_not_rollback_booking(): void
    {
        Config::set('services.whatsapp.provider', 'meta');
        Config::set('services.whatsapp.access_token', 'EAAG_LIVE_TEST_TOKEN');
        Config::set('services.whatsapp.phone_number_id', '109283746592817');

        // Force Meta API to return 500 Internal Server Error
        Http::fake([
            'https://graph.facebook.com/v21.0/109283746592817/messages' => Http::response([
                'error' => ['message' => 'Internal Meta Error', 'code' => 500]
            ], 500)
        ]);

        $customer = User::factory()->create([
            'email' => 'failsafecustomer@artizen.com',
        ]);

        $bookingData = [
            'name' => 'Failsafe Test Customer',
            'mobile' => '9826055555',
            'whatsapp' => '9826055555',
            'email' => 'failsafecustomer@artizen.com',
            'package' => 'Adult Birthdays - Basic Birthday Package',
            'category' => 'Birthdays',
            'tier' => 'Basic Birthday Package',
            'event_date' => date('Y-m-d', strtotime('+5 days')),
            'event_time' => '18:00',
            'guest_count' => 25,
            'address' => 'Palasia Square',
            'area' => 'Palasia',
            'city' => 'Indore',
            'state' => 'Madhya Pradesh',
            'pincode' => '452001',
            'package_price' => 4999,
            'total' => 4999,
        ];

        $response = $this->actingAs($customer)->postJson('/booking/save', $bookingData);

        // Booking MUST succeed regardless of external WhatsApp API failure
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $bookingId = $response->json('booking_id');
        $this->assertDatabaseHas('bookings', [
            'id' => $bookingId,
            'customer_name' => 'Failsafe Test Customer',
            'status' => 'Pending',
        ]);
    }
}

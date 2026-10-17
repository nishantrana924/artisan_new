<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Package;
use App\Models\User;
use App\Services\JsonStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityPhase3AuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear rate limiter cache between tests to ensure test isolation
        RateLimiter::clear('login');
        RateLimiter::clear('register');
        RateLimiter::clear('admin_otp_send');
        RateLimiter::clear('admin_otp_verify');
        RateLimiter::clear('booking_submit');
        RateLimiter::clear('booking_track');
        RateLimiter::clear('contact_enquiry');
        RateLimiter::clear('reviews');
        RateLimiter::clear('oauth');
        RateLimiter::clear('admin_booking_status');
        Cache::flush();
    }

    protected function createAdmin(array $attributes = []): User
    {
        $user = new User(array_merge([
            'name'     => 'Artizen Admin',
            'email'    => 'admin@artizen.com',
            'password' => Hash::make('AdminPass123!'),
        ], $attributes));
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    protected function createCustomer(array $attributes = []): User
    {
        $user = new User(array_merge([
            'name'     => 'Customer Test',
            'email'    => 'customer@example.com',
            'password' => Hash::make('CustomerPass123!'),
        ], $attributes));
        $user->is_admin = false;
        $user->save();

        return $user;
    }

    // =========================================================================
    // 1. Login and Registration Rate Limiting Tests
    // =========================================================================

    public function test_login_endpoint_enforces_rate_limiting_under_excessive_attempts(): void
    {
        // 10 attempts allowed per minute per IP
        for ($i = 0; $i < 10; $i++) {
            $response = $this->post('/login', [
                'email'    => "random_{$i}@example.com",
                'password' => 'wrongpass',
            ]);
            // Either failed login (302/redirect) or internal lockout
            $this->assertNotEquals(429, $response->getStatusCode(), "Attempt {$i} should not be 429 yet.");
        }

        // 11th attempt must be throttled with HTTP 429
        $throttledResponse = $this->post('/login', [
            'email'    => 'another@example.com',
            'password' => 'wrongpass',
        ]);

        $throttledResponse->assertStatus(429);
    }

    public function test_registration_endpoint_enforces_rate_limiting(): void
    {
        // 5 registrations allowed per minute
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/register', [
                'name'                  => "Test User {$i}",
                'email'                 => "user{$i}@example.com",
                'password'              => 'SecurePass123!',
                'password_confirmation' => 'SecurePass123!',
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 6th attempt must be throttled with HTTP 429
        $throttledResponse = $this->post('/register', [
            'name'                  => 'Test User 6',
            'email'                 => 'user6@example.com',
            'password'              => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $throttledResponse->assertStatus(429);
    }

    // =========================================================================
    // 2. Admin Password-Reset OTP Sending and Verification Tests
    // =========================================================================

    public function test_admin_password_reset_otp_sending_throttles_rapid_requests(): void
    {
        $admin = $this->createAdmin();

        // 1st OTP send succeeds
        $first = $this->postJson('/admin/forgot-password/send-otp', [
            'email' => $admin->email,
        ]);
        $first->assertStatus(200);
        $first->assertJson(['status' => 'success']);

        // 2nd immediate OTP send for same email hits 60s cooldown (HTTP 429)
        $second = $this->postJson('/admin/forgot-password/send-otp', [
            'email' => $admin->email,
        ]);
        $second->assertStatus(429);
        $this->assertStringContainsString('60 seconds', $second->json('message'));
    }

    public function test_admin_password_reset_otp_verification_locks_out_after_repeated_failures(): void
    {
        $admin = $this->createAdmin();

        $sessionData = [
            'admin_public_reset_otp'        => '123456',
            'admin_public_reset_expires_at' => now()->addMinutes(10)->timestamp,
            'admin_public_reset_email'      => $admin->email,
        ];

        // 4 failed attempts return 422 with remaining count
        for ($i = 1; $i <= 4; $i++) {
            $response = $this->withSession($sessionData)->postJson('/admin/forgot-password/verify-otp', [
                'email' => $admin->email,
                'otp'   => '999999',
            ]);
            $response->assertStatus(422);
            $this->assertStringContainsString('remaining', $response->json('message'));
        }

        // 5th failed attempt invalidates the OTP and locks out with 429
        $response5 = $this->withSession($sessionData)->postJson('/admin/forgot-password/verify-otp', [
            'email' => $admin->email,
            'otp'   => '999999',
        ]);
        $response5->assertStatus(429);
        $this->assertStringContainsString('invalidated', $response5->json('message'));
    }

    // =========================================================================
    // 3. Booking Submissions and Tracking Rate Limiting Tests
    // =========================================================================

    public function test_booking_tracking_search_rate_limiting(): void
    {
        // 10 searches per minute allowed
        for ($i = 0; $i < 10; $i++) {
            $response = $this->post('/track-booking/search', [
                'booking_id' => 'BK-1234',
                'mobile'     => '9876543210',
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 11th search receives 429
        $throttled = $this->post('/track-booking/search', [
            'booking_id' => 'BK-1234',
            'mobile'     => '9876543210',
        ]);
        $throttled->assertStatus(429);
    }

    // =========================================================================
    // 4. Contact and Enquiry Forms Rate Limiting & Debouncing
    // =========================================================================

    public function test_contact_form_enforces_debounce_and_rate_limiting(): void
    {
        $contactData = [
            'name'    => 'Inquiry Tester',
            'phone'   => '9131668156',
            'email'   => 'inquiry@example.com',
            'subject' => 'Corporate Event Query',
            'message' => 'Looking for full stage lighting setup for 100 guests in Indore.',
        ];

        // 1st submission succeeds
        $first = $this->postJson('/contact/send', $contactData);
        $first->assertStatus(200);

        // Immediate identical 2nd submission is debounced gracefully without duplicate storage
        $second = $this->postJson('/contact/send', $contactData);
        $second->assertStatus(200);
        $this->assertStringContainsString('already been received', $second->json('message'));

        // Submitting differing messages up to limit
        for ($i = 1; $i <= 4; $i++) {
            $this->postJson('/contact/send', array_merge($contactData, [
                'message' => "Different query message {$i} for pricing details.",
            ]));
        }

        // Exceeding the 5 per minute limit receives HTTP 429
        $throttled = $this->postJson('/contact/send', array_merge($contactData, [
            'message' => 'Excessive submission beyond allowed limit.',
        ]));
        $throttled->assertStatus(429);
    }

    // =========================================================================
    // 5. Customer Review Submissions Rate Limiting
    // =========================================================================

    public function test_review_submissions_enforce_rate_limiting(): void
    {
        $customer = $this->createCustomer();

        // 5 review submissions per minute allowed
        for ($i = 0; $i < 5; $i++) {
            $response = $this->actingAs($customer)->postJson('/reviews', [
                'rating'       => 5,
                'review'       => "Great experience test review variation {$i}!",
                'package_slug' => "test-pkg-{$i}",
                'event_title'  => "Setup {$i}",
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 6th review submission receives HTTP 429
        $throttled = $this->actingAs($customer)->postJson('/reviews', [
            'rating'       => 5,
            'review'       => 'Exceeding limit review test content!',
            'package_slug' => 'test-pkg-6',
            'event_title'  => 'Setup 6',
        ]);
        $throttled->assertStatus(429);
    }

    // =========================================================================
    // 6. Google OAuth Rate Limiting
    // =========================================================================

    public function test_google_oauth_endpoints_enforce_rate_limiting(): void
    {
        // 10 requests allowed per minute
        for ($i = 0; $i < 10; $i++) {
            $response = $this->get('/auth/google');
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 11th request receives HTTP 429
        $throttled = $this->get('/auth/google');
        $throttled->assertStatus(429);
    }

    // =========================================================================
    // 7. Notification Debouncing and Recovery Tests
    // =========================================================================

    public function test_booking_status_notification_debouncing_prevents_duplicate_dispatches(): void
    {
        Notification::fake();
        $admin = $this->createAdmin();

        $bookingId = 'BK-DEBOUNCE-99';
        JsonStorageService::write('bookings.json', [
            [
                'id'                 => $bookingId,
                'name'               => 'Test Client',
                'email'              => 'client@example.com',
                'mobile'             => '9131668156',
                'package'            => 'Luxury Neon Setup',
                'date'               => '2026-11-01',
                'status'             => 'Pending',
                'notifications_sent' => [],
            ]
        ]);

        // 1. First status update to Confirmed triggers notification
        $response1 = $this->actingAs($admin)
            ->withSession([
                'admin_logged_in' => true,
                'admin_email'     => $admin->email,
            ])
            ->postJson('/admin/bookings/update-status', [
                'id'     => $bookingId,
                'status' => 'Confirmed',
            ]);
        $response1->assertStatus(200);

        // Notification must have been sent once
        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable(),
            \App\Notifications\BookingConfirmedNotification::class,
            1
        );

        // 2. Second status update to Confirmed within 60 seconds is debounced
        $response2 = $this->actingAs($admin)
            ->withSession([
                'admin_logged_in' => true,
                'admin_email'     => $admin->email,
            ])
            ->postJson('/admin/bookings/update-status', [
                'id'     => $bookingId,
                'status' => 'Confirmed',
            ]);
        $response2->assertStatus(200);

        // Must still have been sent only once (not duplicated)
        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable(),
            \App\Notifications\BookingConfirmedNotification::class,
            1
        );
    }

    public function test_legitimate_requests_recover_after_rate_limit_expires(): void
    {
        // Exhaust registration limiter
        for ($i = 0; $i < 5; $i++) {
            $this->post('/register', [
                'name'                  => "Recovery User {$i}",
                'email'                 => "recov{$i}@example.com",
                'password'              => 'SecurePass123!',
                'password_confirmation' => 'SecurePass123!',
            ]);
        }

        // Verify throttled
        $throttled = $this->post('/register', [
            'name'                  => 'Recovery User Blocked',
            'email'                 => 'blocked@example.com',
            'password'              => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);
        $throttled->assertStatus(429);

        // Simulate rate limit window expiry
        Cache::flush();
        $this->travel(65)->seconds();

        // Next legitimate request recovers cleanly
        $recovered = $this->post('/register', [
            'name'                  => 'Recovery User Allowed',
            'email'                 => 'allowed@example.com',
            'password'              => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);
        $this->assertNotEquals(429, $recovered->getStatusCode());
    }
}

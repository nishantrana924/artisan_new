<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\JsonStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class SecurityPhase1AuditTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Helper to create an admin user.
     */
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

    // =========================================================================
    // 1. Unauthenticated administrator password reset in AdminAuthController
    // =========================================================================

    public function test_admin_password_reset_requires_otp_and_fails_without_it(): void
    {
        $admin = $this->createAdmin();

        $response = $this->post('/admin/forgot-password', [
            'email'                 => $admin->email,
            'password'              => 'NewHackedPass123!',
            'password_confirmation' => 'NewHackedPass123!',
        ]);

        $response->assertSessionHasErrors(['otp']);
        $admin->refresh();
        $this->assertFalse(Hash::check('NewHackedPass123!', $admin->password));
    }

    public function test_admin_password_reset_fails_with_invalid_otp(): void
    {
        $admin = $this->createAdmin();

        $response = $this->withSession([
            'admin_public_reset_otp'        => '654321',
            'admin_public_reset_expires_at' => now()->addMinutes(10)->timestamp,
            'admin_public_reset_email'      => $admin->email,
        ])->post('/admin/forgot-password', [
            'email'                 => $admin->email,
            'otp'                   => '000000', // Invalid OTP
            'password'              => 'NewSecurePass123!',
            'password_confirmation' => 'NewSecurePass123!',
        ]);

        $response->assertSessionHasErrors(['otp']);
        $admin->refresh();
        $this->assertFalse(Hash::check('NewSecurePass123!', $admin->password));
    }

    public function test_admin_password_reset_fails_with_expired_otp(): void
    {
        $admin = $this->createAdmin();

        $response = $this->withSession([
            'admin_public_reset_otp'        => '654321',
            'admin_public_reset_expires_at' => now()->subMinute()->timestamp, // Expired
            'admin_public_reset_email'      => $admin->email,
        ])->post('/admin/forgot-password', [
            'email'                 => $admin->email,
            'otp'                   => '654321',
            'password'              => 'NewSecurePass123!',
            'password_confirmation' => 'NewSecurePass123!',
        ]);

        $response->assertSessionHasErrors(['otp']);
        $admin->refresh();
        $this->assertFalse(Hash::check('NewSecurePass123!', $admin->password));
    }

    public function test_admin_password_reset_succeeds_with_valid_otp_and_invalidates_otp(): void
    {
        $admin = $this->createAdmin();
        $validOtp = '789123';

        $response = $this->withSession([
            'admin_public_reset_otp'        => $validOtp,
            'admin_public_reset_expires_at' => now()->addMinutes(10)->timestamp,
            'admin_public_reset_email'      => $admin->email,
        ])->post('/admin/forgot-password', [
            'email'                 => $admin->email,
            'otp'                   => $validOtp,
            'password'              => 'NewValidPass123!',
            'password_confirmation' => 'NewValidPass123!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success');

        $admin->refresh();
        $this->assertTrue(Hash::check('NewValidPass123!', $admin->password));

        // Ensure session OTP was forgotten to prevent reuse
        $this->assertNull(session('admin_public_reset_otp'));
    }

    public function test_admin_can_request_public_reset_otp(): void
    {
        Mail::fake();
        $admin = $this->createAdmin();

        $response = $this->postJson('/admin/forgot-password/send-otp', [
            'email' => $admin->email,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $storedOtp = session('admin_public_reset_otp');
        $this->assertNotNull($storedOtp);
        $this->assertEquals(6, strlen($storedOtp));
        $this->assertEquals($admin->email, session('admin_public_reset_email'));
    }

    // =========================================================================
    // 2. Mass-assignment privilege escalation involving is_admin in User model
    // =========================================================================

    public function test_is_admin_is_not_mass_assignable_on_user_model(): void
    {
        $user = new User();
        $this->assertNotContains('is_admin', $user->getFillable());

        // Attempt mass assignment via create
        $created = User::create([
            'name'     => 'Malicious User',
            'email'    => 'malicious@example.com',
            'password' => Hash::make('Password123!'),
            'is_admin' => true,
        ]);

        $this->assertFalse((bool) $created->is_admin);
        $this->assertFalse($created->isAdmin());
    }

    public function test_registration_endpoint_cannot_assign_admin_privilege(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Infiltrator',
            'email'                 => 'infiltrator@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
            'is_admin'              => 1,
            'role'                  => 'admin',
        ]);

        $user = User::where('email', 'infiltrator@example.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse((bool) $user->is_admin);
        $this->assertFalse($user->isAdmin());
    }

    // =========================================================================
    // 3. Unauthenticated booking lookup exposing customer PII in BookingTracker
    // =========================================================================

    public function test_unauthenticated_request_by_id_alone_does_not_reveal_booking_pii(): void
    {
        // Setup a dummy booking in JSON storage
        $dummyBooking = [
            'id'            => 'BK-9999',
            'name'          => 'Confidential Customer',
            'email'         => 'confidential@example.com',
            'mobile'        => '9876543210',
            'whatsapp'      => '9876543210',
            'package'       => 'Secret VIP Celebration',
            'tier'          => 'Premium',
            'total'         => 19999,
            'address'       => 'Private Bungalow, Indore',
            'status'        => 'confirmed',
            'created_at'    => '2026-10-16 10:00:00',
        ];
        JsonStorageService::write('bookings.json', [$dummyBooking]);

        // Access via GET without mobile
        $response = $this->get('/track-booking?id=BK-9999');

        $response->assertStatus(200);
        // The booking details must NOT be rendered in receipt format
        $response->assertDontSee('Confidential Customer');
        $response->assertDontSee('Private Bungalow, Indore');
        $response->assertDontSee('confidential@example.com');
        // Search form should be visible with prefilled ID
        $response->assertSee('Find Your Booking by Order ID &amp; Phone', false);
        $response->assertSee('BK-9999');
    }

    public function test_request_with_both_id_and_mobile_reveals_booking(): void
    {
        $dummyBooking = [
            'id'            => 'BK-9999',
            'name'          => 'Legitimate Customer',
            'email'         => 'legit@example.com',
            'mobile'        => '9876543210',
            'package'       => 'Celebration Package',
            'status'        => 'confirmed',
        ];
        JsonStorageService::write('bookings.json', [$dummyBooking]);

        // 1. With correct phone in query
        $response = $this->get('/track-booking?id=BK-9999&mobile=9876543210');
        $response->assertStatus(200);
        $response->assertSee('Legitimate Customer');

        // 2. Via POST search with correct phone
        $postResponse = $this->postJson('/track-booking/search', [
            'booking_id' => 'BK-9999',
            'mobile'     => '9876543210',
        ]);
        $postResponse->assertStatus(200);
        $postResponse->assertJson(['success' => true]);

        // 3. Via POST search with wrong phone
        $failResponse = $this->postJson('/track-booking/search', [
            'booking_id' => 'BK-9999',
            'mobile'     => '1111111111',
        ]);
        $failResponse->assertStatus(200);
        $failResponse->assertJson(['success' => false]);
    }

    public function test_session_owner_can_view_their_own_recent_booking(): void
    {
        $dummyBooking = [
            'id'      => 'BK-7777',
            'name'    => 'Recent Booker',
            'email'   => 'recent@example.com',
            'mobile'  => '9876543210',
            'package' => 'Recent Package',
            'status'  => 'pending',
        ];
        JsonStorageService::write('bookings.json', [$dummyBooking]);

        $response = $this->withSession(['last_booking_id' => 'BK-7777'])
            ->get('/track-booking?id=BK-7777');

        $response->assertStatus(200);
        $response->assertSee('Recent Booker');
    }

    // =========================================================================
    // 4. Open redirect in GoogleAuthController
    // =========================================================================

    public function test_google_oauth_redirect_sanitizes_external_urls(): void
    {
        Config::set('services.google.client_id', 'dummy-client-id');
        Config::set('services.google.client_secret', 'dummy-client-secret');

        $driver = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $driver->shouldReceive('redirectUrl')->andReturnSelf();
        $driver->shouldReceive('redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2/v2/auth'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($driver);

        // Attempt open redirect to evil domain
        $response = $this->get('/auth/google?redirect=https://evil.com/phishing');
        $this->assertEquals(route('home'), session('google_auth_redirect'));

        // Attempt protocol-relative open redirect
        $response2 = $this->get('/auth/google?redirect=//evil.com');
        $this->assertEquals(route('home'), session('google_auth_redirect'));

        // Legitimate relative local path is preserved
        $response3 = $this->get('/auth/google?redirect=/events');
        $this->assertEquals('/events', session('google_auth_redirect'));
    }

    // =========================================================================
    // 5. Automatic administrator login fallback in AdminPageController
    // =========================================================================

    public function test_unauthenticated_guest_is_not_auto_logged_in_as_admin(): void
    {
        $admin = $this->createAdmin();

        // Guest accesses settings route
        $response = $this->get('/admin/settings');

        // AdminMiddleware rejects guest
        $response->assertRedirect();
        $this->assertGuest();
        $this->assertFalse(session('admin_logged_in') === true);
    }

    public function test_admin_controller_rejects_missing_admin_and_never_auto_promotes(): void
    {
        $admin = $this->createAdmin();
        $regularUser = User::create([
            'name'     => 'Regular Customer',
            'email'    => 'regular@example.com',
            'password' => Hash::make('CustomerPass123!'),
        ]);

        $controller = new \App\Http\Controllers\Admin\AdminPageController(
            app(\App\Services\JsonStorageService::class),
            app(\App\Services\CelebrationCatalogService::class)
        );

        // Without authentication
        $this->assertNull($controller->getAdminUser());

        // Acting as regular user
        $this->actingAs($regularUser);
        $this->assertNull($controller->getAdminUser());
        $this->assertFalse(\Illuminate\Support\Facades\Auth::user()->isAdmin());
    }

    // =========================================================================
    // 6. Plain-text OTP logging in AdminPageController
    // =========================================================================

    public function test_otp_generation_does_not_log_raw_otp_code(): void
    {
        Mail::fake();
        $loggedMessages = [];
        Log::shouldReceive('error')->andReturnNull();
        Log::shouldReceive('warning')->andReturnNull();
        Log::shouldReceive('info')
            ->andReturnUsing(function ($message) use (&$loggedMessages) {
                $loggedMessages[] = $message;
                return null;
            });

        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->withSession(['admin_logged_in' => true, 'admin_email' => $admin->email])
            ->postJson('/admin/settings/send-otp');

        $response->assertStatus(200);
        $this->assertNotEmpty($loggedMessages);

        foreach ($loggedMessages as $msg) {
            // Assert that none of the log messages contain an exposed OTP pattern
            $this->assertDoesNotMatchRegularExpression('/OTP generated for [^:]+:\s*\d{6}/i', $msg);
        }
    }
}

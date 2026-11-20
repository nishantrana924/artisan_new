<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailVerificationAndStrongPasswordTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /**
     * Test registration rejects weak passwords missing special characters or numbers.
     */
    public function test_registration_requires_strong_password_with_special_characters(): void
    {
        // Missing special character
        $response1 = $this->post('/register', [
            'name'                  => 'Weak Pass User',
            'email'                 => 'weak1@example.com',
            'password'              => 'WeakPassword123',
            'password_confirmation' => 'WeakPassword123',
        ]);
        $response1->assertSessionHasErrors(['password']);

        // Missing number
        $response2 = $this->post('/register', [
            'name'                  => 'Weak Pass User',
            'email'                 => 'weak2@example.com',
            'password'              => 'WeakPassword!',
            'password_confirmation' => 'WeakPassword!',
        ]);
        $response2->assertSessionHasErrors(['password']);

        // Missing uppercase
        $response3 = $this->post('/register', [
            'name'                  => 'Weak Pass User',
            'email'                 => 'weak3@example.com',
            'password'              => 'weakpass123!',
            'password_confirmation' => 'weakpass123!',
        ]);
        $response3->assertSessionHasErrors(['password']);
    }

    /**
     * Test successful registration creates unverified user and redirects to OTP verification.
     */
    public function test_registration_succeeds_and_dispatches_otp_code(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'John Verified',
            'email'                 => 'john.verified@example.com',
            'password'              => 'ArtizenSecure2026@',
            'password_confirmation' => 'ArtizenSecure2026@',
        ]);

        $response->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'john.verified@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);

        $cacheKey = 'email_verify_otp_' . md5('john.verified@example.com');
        $this->assertTrue(Cache::has($cacheKey));

        $record = Cache::get($cacheKey);
        $this->assertIsArray($record);
        $this->assertEquals(6, strlen($record['otp']));
    }

    /**
     * Test invalid OTP fails verification.
     */
    public function test_invalid_otp_fails_verification(): void
    {
        $user = User::create([
            'name'              => 'Jane Unverified',
            'email'             => 'jane.unverified@example.com',
            'password'          => Hash::make('ArtizenSecure2026@'),
            'email_verified_at' => null,
        ]);

        Cache::put('email_verify_otp_' . md5($user->email), [
            'otp'        => '123456',
            'user_id'    => $user->id,
            'expires_at' => now()->addMinutes(10)->timestamp,
            'attempts'   => 0,
        ], now()->addMinutes(10));

        $response = $this->post('/verify-email', [
            'email' => $user->email,
            'otp'   => '999999', // Incorrect OTP
        ]);

        $response->assertSessionHasErrors(['otp']);
        $this->assertNull($user->fresh()->email_verified_at);
        $this->assertGuest();
    }

    /**
     * Test correct OTP verifies user email and authenticates.
     */
    public function test_correct_otp_verifies_email_and_logs_in(): void
    {
        $user = User::create([
            'name'              => 'Verified Customer',
            'email'             => 'verified.cust@example.com',
            'password'          => Hash::make('ArtizenSecure2026@'),
            'email_verified_at' => null,
        ]);

        $correctOtp = '582914';
        Cache::put('email_verify_otp_' . md5($user->email), [
            'otp'        => $correctOtp,
            'user_id'    => $user->id,
            'expires_at' => now()->addMinutes(10)->timestamp,
            'attempts'   => 0,
        ], now()->addMinutes(10));

        $response = $this->post('/verify-email', [
            'email' => $user->email,
            'otp'   => $correctOtp,
        ]);

        $response->assertRedirect(route('events.index'));
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test unverified customer logging in is redirected to OTP verification.
     */
    public function test_unverified_customer_login_redirects_to_otp_verification(): void
    {
        $user = User::create([
            'name'              => 'Pending Customer',
            'email'             => 'pending.cust@example.com',
            'password'          => Hash::make('ArtizenSecure2026@'),
            'email_verified_at' => null,
            'is_admin'          => false,
        ]);

        $response = $this->post('/login', [
            'email'    => 'pending.cust@example.com',
            'password' => 'ArtizenSecure2026@',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertGuest();

        $cacheKey = 'email_verify_otp_' . md5($user->email);
        $this->assertTrue(Cache::has($cacheKey));
    }
}


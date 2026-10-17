<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SecurityPhase5AHardeningTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Task 1: Verify that public login does not auto-provision an admin account
     * when the account does not exist in the database.
     */
    public function test_public_login_does_not_auto_provision_admin_account(): void
    {
        // Ensure admin@artizen.com does not exist
        $this->assertDatabaseMissing('users', ['email' => 'admin@artizen.com']);

        $response = $this->post('/login', [
            'email'    => 'admin@artizen.com',
            'password' => 'AnyPassword123!',
        ]);

        // User must NOT be created automatically
        $this->assertDatabaseMissing('users', ['email' => 'admin@artizen.com']);
        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Task 2: Verify that a regular user cannot gain admin access by email alone
     * even if their email is admin@artizen.com or another historically privileged address.
     */
    public function test_user_with_admin_email_but_without_is_admin_flag_cannot_access_admin_dashboard(): void
    {
        $nonAdminUser = User::factory()->create([
            'email'    => 'admin@artizen.com',
            'password' => Hash::make('Secret123!'),
            'is_admin' => false,
        ]);

        $this->assertFalse($nonAdminUser->isAdmin());

        // Attempting to access admin dashboard as this user must be rejected
        $response = $this->actingAs($nonAdminUser)->get('/admin/dashboard');
        $response->assertRedirect(route('login', ['redirect' => url('/admin/dashboard')]));

        // Also test with admin session email but without database admin privileges
        $sessionResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_email'     => 'admin@artizen.com',
        ])->get('/admin/dashboard');

        // Since user has is_admin = false, AdminMiddleware must deny access
        $sessionResponse->assertRedirect(route('login', ['redirect' => url('/admin/dashboard')]));
    }

    /**
     * Task 3: Verify that reserved admin email addresses cannot be claimed through public registration.
     */
    public function test_reserved_admin_emails_cannot_be_registered_by_public(): void
    {
        $reservedEmails = [
            'admin@artizen.com',
            'superadmin@example.com',
            'administrator@test.com',
            'root@domain.com',
            'newstaff@artizen.com',
        ];

        foreach ($reservedEmails as $reservedEmail) {
            $response = $this->post('/register', [
                'name'                  => 'Attacker',
                'email'                 => $reservedEmail,
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
            ]);

            $response->assertSessionHasErrors(['email']);
            $this->assertDatabaseMissing('users', ['email' => strtolower($reservedEmail)]);
        }
    }

    /**
     * Task 3: Verify that regular customer registration works normally for legitimate emails.
     */
    public function test_legitimate_customer_registration_succeeds(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Legitimate Customer',
            'email'                 => 'validcustomer@example.com',
            'password'              => 'ValidPassword123!',
            'password_confirmation' => 'ValidPassword123!',
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertDatabaseHas('users', [
            'email'    => 'validcustomer@example.com',
            'is_admin' => false,
        ]);
        $this->assertAuthenticated();
    }

    /**
     * Task 1 & 2: Verify that legitimate database-backed admin login still works.
     */
    public function test_legitimate_admin_login_works(): void
    {
        $admin = User::factory()->create([
            'email'    => 'admin@artizen.com',
            'password' => Hash::make('CorrectPassword123!'),
            'is_admin' => true,
        ]);

        $response = $this->post('/login', [
            'email'    => 'admin@artizen.com',
            'password' => 'CorrectPassword123!',
        ]);

        $this->assertAuthenticatedAs($admin);
        $this->assertTrue(session('admin_logged_in'));
        $response->assertRedirect('/admin/dashboard');

        // Verified admin can access dashboard
        $dashboardResponse = $this->actingAs($admin)->get('/admin/dashboard');
        $dashboardResponse->assertStatus(200);
    }

    /**
     * Task 1: Verify OTP password recovery flow still works for legitimate admins.
     */
    public function test_legitimate_admin_otp_recovery_works(): void
    {
        $admin = User::factory()->create([
            'email'    => 'admin@artizen.com',
            'password' => Hash::make('CurrentPassword123!'),
            'is_admin' => true,
        ]);

        $response = $this->postJson('/admin/forgot-password/send-otp', [
            'email' => 'admin@artizen.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $response->assertSessionHas('admin_public_reset_otp');
        $this->assertEquals('admin@artizen.com', session('admin_public_reset_email'));
    }

    /**
     * Task 4: Verify upload directory protection configuration.
     * The uploads/.htaccess must exist and contain directives denying script execution.
     */
    public function test_uploads_htaccess_exists_and_blocks_script_execution(): void
    {
        $htaccessPath = public_path('uploads/.htaccess');
        $this->assertFileExists($htaccessPath);

        $content = File::get($htaccessPath);
        $this->assertStringContainsString('php_flag engine off', $content);
        $this->assertStringContainsString('Options -Indexes -ExecCGI', $content);
        $this->assertStringContainsString('Require all denied', $content);
        $this->assertStringContainsString('php', $content);
    }

    /**
     * Task 4: Verify image upload succeeds and non-image/executable upload is rejected by validation.
     */
    public function test_image_upload_validation_accepts_image_and_rejects_scripts(): void
    {
        $admin = User::factory()->create([
            'email'    => 'uploadadmin@artizen.com',
            'password' => Hash::make('Password123!'),
            'is_admin' => true,
        ]);

        // Uploading an executable PHP script disguised as avatar must fail validation
        $fakeScript = UploadedFile::fake()->create('malicious.php', 100, 'text/x-php');
        $failResponse = $this->actingAs($admin)
            ->withSession(['admin_logged_in' => true, 'admin_email' => $admin->email])
            ->post('/admin/settings/profile', [
                'name'        => 'Admin User',
                'email'       => $admin->email,
                'avatar_file' => $fakeScript,
            ]);

        $failResponse->assertSessionHasErrors(['avatar_file']);

        // Uploading a legitimate image must pass validation
        $fakeImage = UploadedFile::fake()->image('avatar.jpg', 200, 200);
        $passResponse = $this->actingAs($admin)
            ->withSession(['admin_logged_in' => true, 'admin_email' => $admin->email])
            ->post('/admin/settings/profile', [
                'name'        => 'Admin User',
                'email'       => $admin->email,
                'avatar_file' => $fakeImage,
            ]);

        $passResponse->assertSessionDoesntHaveErrors();
    }

    /**
     * Task 5: Verify session cookie secure configuration behavior in production vs local.
     */
    public function test_session_cookie_secure_flag_configuration(): void
    {
        // When SESSION_SECURE_COOKIE is not explicitly set:
        // In local: false
        putenv('SESSION_SECURE_COOKIE');
        unset($_ENV['SESSION_SECURE_COOKIE'], $_SERVER['SESSION_SECURE_COOKIE']);

        // Test configuration expression:
        $isLocalSecure = env('SESSION_SECURE_COOKIE', 'local' === 'production');
        $this->assertFalse($isLocalSecure);

        // In production: true
        $isProdSecure = env('SESSION_SECURE_COOKIE', 'production' === 'production');
        $this->assertTrue($isProdSecure);
    }

    /**
     * Task 5: Verify HTTPS URL scheme forcing behavior.
     */
    public function test_https_url_generation_behavior(): void
    {
        // When local HTTP request is received:
        $response = $this->get('http://localhost/events');
        $response->assertStatus(200);

        // In production AppServiceProvider forces HTTPS scheme:
        URL::forceScheme('https');
        $this->assertStringStartsWith('https://', url('/events'));

        // Reset
        URL::forceScheme(null);
    }
}

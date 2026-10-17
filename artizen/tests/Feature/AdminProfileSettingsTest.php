<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminProfileSettingsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Create an admin user for testing.
     */
    protected function makeAdmin(array $attributes = []): User
    {
        $user = new User();
        $user->name = $attributes['name'] ?? 'Artizen Admin';
        $user->email = $attributes['email'] ?? 'testadmin_' . uniqid() . '@artizen.com';
        $user->password = Hash::make($attributes['password'] ?? 'CorrectPass123!');
        $user->is_admin = true;
        return $user;
    }

    /**
     * Test admin can view settings page and SEO config has been removed.
     */
    public function test_settings_page_renders_profile_and_removes_seo_config(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)
            ->withSession(['admin_logged_in' => true, 'admin_email' => $admin->email])
            ->get('/admin/settings');

        $response->assertStatus(200);
        $response->assertSee('Profile & Security Settings');
        $response->assertSee('Change Account Password');
        $response->assertSee('Forgot Password? Emergency Reset');
        
        // Assert old deprecated SEO/Surcharges config is removed
        $response->assertDontSee('Website & SEO Config');
        $response->assertDontSee('Meta Details');
        $response->assertDontSee('Meta Title Template');
        $response->assertDontSee('Delivery Surcharges');
    }

    /**
     * Test guest cannot access admin settings.
     */
    public function test_guest_cannot_access_settings(): void
    {
        $response = $this->get('/admin/settings');
        $response->assertRedirect();
        $this->assertStringContainsString('/login', $response->headers->get('Location'));

        $response = $this->post('/admin/settings/profile', ['name' => 'Hacker', 'email' => 'h@artizen.com']);
        $response->assertRedirect();
        $this->assertStringContainsString('/login', $response->headers->get('Location'));

        $response = $this->post('/admin/settings/password', ['current_password' => 'abc', 'password' => 'newpass123', 'password_confirmation' => 'newpass123']);
        $response->assertRedirect();
        $this->assertStringContainsString('/login', $response->headers->get('Location'));
    }

    /**
     * Test public forgot password page renders for guests.
     */
    public function test_public_forgot_password_page_renders_for_guest(): void
    {
        $response = $this->get('/admin/forgot-password');
        $response->assertStatus(200);
        $response->assertSee('Forgot Password?');
        $response->assertSee('Reset & Update Password', false);
    }

    /**
     * Test admin can update profile with avatar auto-compressed to WebP in public/uploads.
     */
    public function test_admin_can_update_profile_and_upload_avatar_auto_compressed_to_webp(): void
    {
        $admin = $this->makeAdmin();
        $admin->save();

        $uploadedFile = \Illuminate\Http\UploadedFile::fake()->image('my_photo.png', 800, 800);

        $response = $this->actingAs($admin)
            ->withSession(['admin_logged_in' => true, 'admin_email' => $admin->email])
            ->post('/admin/settings/profile', [
                'name'        => 'Updated Admin Name',
                'email'       => $admin->email,
                'avatar_file' => $uploadedFile,
            ]);

        $response->assertRedirect('/admin/settings');
        $response->assertSessionHas('success');

        $admin->refresh();
        $this->assertEquals('Updated Admin Name', $admin->name);
        $this->assertNotNull($admin->avatar);
        $this->assertStringStartsWith('/uploads/avatar_', $admin->avatar);
        $this->assertStringEndsWith('.webp', $admin->avatar);

        // Verify the file actually exists on disk in public/uploads
        $diskPath = public_path(ltrim($admin->avatar, '/'));
        $this->assertFileExists($diskPath);

        // Verify it is a valid image
        $imageInfo = @getimagesize($diskPath);
        $this->assertNotEmpty($imageInfo);

        // Clean up test file
        if (file_exists($diskPath)) {
            @unlink($diskPath);
        }
    }

    /**
     * Test admin can remove avatar and delete file from public/uploads.
     */
    public function test_admin_can_remove_avatar(): void
    {
        $admin = $this->makeAdmin();
        $admin->avatar = '/uploads/sample_dummy_avatar.webp';
        $admin->save();

        // Create a dummy file in public/uploads to test file cleanup
        $dummyPath = public_path('uploads/sample_dummy_avatar.webp');
        file_put_contents($dummyPath, 'dummy data');

        $response = $this->actingAs($admin)
            ->withSession(['admin_logged_in' => true, 'admin_email' => $admin->email])
            ->post('/admin/settings/profile', [
                'name'          => 'Admin Name',
                'email'         => $admin->email,
                'remove_avatar' => '1',
            ]);

        $response->assertRedirect('/admin/settings');
        $admin->refresh();
        $this->assertNull($admin->avatar);
        $this->assertFileDoesNotExist($dummyPath);
    }

    /**
     * Test admin can send reset OTP to their connected email.
     */
    public function test_admin_can_send_reset_otp_to_connected_email(): void
    {
        $admin = $this->makeAdmin();
        $admin->save();

        $response = $this->actingAs($admin)
            ->withSession(['admin_logged_in' => true, 'admin_email' => $admin->email])
            ->postJson('/admin/settings/send-otp');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'email'  => $admin->email,
        ]);

        $this->assertNotNull(session('admin_reset_otp'));
        $this->assertEquals(6, strlen(session('admin_reset_otp')));
        $this->assertEquals($admin->email, session('admin_reset_otp_email'));
    }

    /**
     * Test admin cannot reset password with invalid OTP.
     */
    public function test_admin_cannot_reset_password_with_invalid_otp(): void
    {
        $admin = $this->makeAdmin();
        $admin->save();

        $response = $this->actingAs($admin)
            ->withSession([
                'admin_logged_in'            => true,
                'admin_email'                => $admin->email,
                'admin_reset_otp'            => '123456',
                'admin_reset_otp_expires_at' => now()->addMinutes(10)->timestamp,
                'admin_reset_otp_email'      => $admin->email,
            ])
            ->post('/admin/settings/emergency-reset', [
                'otp'                       => '999999', // Wrong OTP
                'new_password'              => 'BrandNewPass123!',
                'new_password_confirmation' => 'BrandNewPass123!',
            ]);

        $response->assertSessionHasErrors(['otp']);
        $admin->refresh();
        $this->assertFalse(Hash::check('BrandNewPass123!', $admin->password));
    }

    /**
     * Test admin can reset password with valid OTP.
     */
    public function test_admin_can_reset_password_with_valid_otp(): void
    {
        $admin = $this->makeAdmin();
        $admin->save();

        $correctOtp = '849201';

        $response = $this->actingAs($admin)
            ->withSession([
                'admin_logged_in'            => true,
                'admin_email'                => $admin->email,
                'admin_reset_otp'            => $correctOtp,
                'admin_reset_otp_expires_at' => now()->addMinutes(10)->timestamp,
                'admin_reset_otp_email'      => $admin->email,
            ])
            ->post('/admin/settings/emergency-reset', [
                'otp'                       => $correctOtp,
                'new_password'              => 'BrandNewPass123!',
                'new_password_confirmation' => 'BrandNewPass123!',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $admin->refresh();
        $this->assertTrue(Hash::check('BrandNewPass123!', $admin->password));
        $this->assertNull(session('admin_reset_otp'));
    }

    /**
     * Test admin can verify OTP via API before setting password.
     */
    public function test_admin_can_verify_otp_before_setting_new_password(): void
    {
        $admin = $this->makeAdmin();
        $admin->save();

        $correctOtp = '123456';

        // 1. Verify invalid OTP fails
        $failResponse = $this->actingAs($admin)
            ->withSession([
                'admin_logged_in'            => true,
                'admin_email'                => $admin->email,
                'admin_reset_otp'            => $correctOtp,
                'admin_reset_otp_expires_at' => now()->addMinutes(10)->timestamp,
                'admin_reset_otp_email'      => $admin->email,
            ])
            ->postJson('/admin/settings/verify-otp', ['otp' => '000000']);

        $failResponse->assertStatus(422);
        $failResponse->assertJson(['status' => 'error']);

        // 2. Verify valid OTP succeeds
        $passResponse = $this->actingAs($admin)
            ->withSession([
                'admin_logged_in'            => true,
                'admin_email'                => $admin->email,
                'admin_reset_otp'            => $correctOtp,
                'admin_reset_otp_expires_at' => now()->addMinutes(10)->timestamp,
                'admin_reset_otp_email'      => $admin->email,
            ])
            ->postJson('/admin/settings/verify-otp', ['otp' => $correctOtp]);

        $passResponse->assertStatus(200);
        $passResponse->assertJson(['status' => 'success']);
        $this->assertTrue(session('admin_reset_otp_verified'));

        // 3. Now submit new password without OTP input because session is verified
        $resetResponse = $this->actingAs($admin)
            ->withSession([
                'admin_logged_in'          => true,
                'admin_email'              => $admin->email,
                'admin_reset_otp_verified' => true,
            ])
            ->post('/admin/settings/emergency-reset', [
                'new_password'              => 'VerifiedPass123!',
                'new_password_confirmation' => 'VerifiedPass123!',
            ]);

        $resetResponse->assertRedirect();
        $admin->refresh();
        $this->assertTrue(Hash::check('VerifiedPass123!', $admin->password));
    }
}

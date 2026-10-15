<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminProfileSettingsTest extends TestCase
{
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
        $response->assertRedirect('/admin/login');

        $response = $this->post('/admin/settings/profile', ['name' => 'Hacker', 'email' => 'h@artizen.com']);
        $response->assertRedirect('/admin/login');

        $response = $this->post('/admin/settings/password', ['current_password' => 'abc', 'password' => 'newpass123', 'password_confirmation' => 'newpass123']);
        $response->assertRedirect('/admin/login');
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
}

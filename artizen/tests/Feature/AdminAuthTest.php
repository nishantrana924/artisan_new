<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    /**
     * Test admin login view is accessible.
     */
    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('Admin Sign In');
    }

    /**
     * Test unauthenticated guest cannot access admin dashboard.
     */
    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test empty fields produce validation errors.
     */
    public function test_validation_errors_for_empty_login_request(): void
    {
        $response = $this->post('/admin/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    /**
     * Test invalid email format produces validation error.
     */
    public function test_invalid_email_format_validation(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'invalid-email-string',
            'password' => 'somepassword',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test wrong password returns generic error message.
     */
    public function test_admin_cannot_authenticate_with_invalid_password(): void
    {
        $admin = User::factory()->create([
            'email' => 'testadmin@artizen.com',
            'password' => Hash::make('CorrectPassword123!'),
            'is_admin' => true,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'testadmin@artizen.com',
            'password' => 'WrongPassword123!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test non-admin user cannot access admin dashboard.
     */
    public function test_non_admin_user_cannot_access_admin_dashboard(): void
    {
        $regularUser = User::factory()->create([
            'email' => 'regular@example.com',
            'password' => Hash::make('Password123!'),
            'is_admin' => false,
        ]);

        $response = $this->actingAs($regularUser)->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test admin can authenticate with valid credentials and session is regenerated.
     */
    public function test_admin_can_authenticate_with_valid_credentials(): void
    {
        $admin = User::factory()->create([
            'email' => 'authadmin@artizen.com',
            'password' => Hash::make('ValidPassword123!'),
            'is_admin' => true,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'authadmin@artizen.com',
            'password' => 'ValidPassword123!',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect('/admin/dashboard');
    }

    /**
     * Test admin can logout.
     */
    public function test_admin_can_logout(): void
    {
        $admin = User::factory()->create([
            'email' => 'logoutadmin@artizen.com',
            'password' => Hash::make('ValidPassword123!'),
            'is_admin' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->post('/admin/logout');

        $this->assertGuest();
        $response->assertRedirect('/admin/login');
    }
}

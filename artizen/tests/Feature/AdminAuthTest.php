<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin login route redirects to unified /login page.
     */
    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');
        $response->assertRedirect('/login');

        $unifiedLogin = $this->get('/login');
        $unifiedLogin->assertStatus(200);
        $unifiedLogin->assertSee('Sign In');
    }

    /**
     * Test unauthenticated guest cannot access admin dashboard and is redirected to /login.
     */
    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect(route('login', ['redirect' => url('/admin/dashboard')]));
    }

    /**
     * Test empty fields produce validation errors.
     */
    public function test_validation_errors_for_empty_login_request(): void
    {
        $response = $this->post('/login', [
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
        $response = $this->post('/login', [
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

        $response = $this->post('/login', [
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
        $response->assertRedirect(route('login', ['redirect' => url('/admin/dashboard')]));
    }

    /**
     * Test admin can authenticate via unified login and is redirected to /admin/dashboard.
     */
    public function test_admin_can_authenticate_with_valid_credentials(): void
    {
        $admin = User::factory()->create([
            'email' => 'authadmin@artizen.com',
            'password' => Hash::make('ValidPassword123!'),
            'is_admin' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'authadmin@artizen.com',
            'password' => 'ValidPassword123!',
        ]);

        $this->assertAuthenticatedAs($admin);
        $this->assertTrue(session('admin_logged_in'));
        $response->assertRedirect('/admin/dashboard');

        $dashboardResponse = $this->get('/admin/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Dashboard');
    }

    /**
     * Test regular customer user authenticating via unified login is redirected to home.
     */
    public function test_customer_authenticating_is_redirected_to_home(): void
    {
        $user = User::factory()->create([
            'email' => 'customer@example.com',
            'password' => Hash::make('CustomerPass123!'),
            'is_admin' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'customer@example.com',
            'password' => 'CustomerPass123!',
        ]);

        $this->assertAuthenticatedAs($user);
        $this->assertNull(session('admin_logged_in'));
        $response->assertRedirect(route('home'));
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
        $response->assertRedirect(route('home'));
    }

    /**
     * Test login and register pages render with improved placeholders and without input labels.
     */
    public function test_auth_pages_have_improved_placeholders_and_no_field_labels(): void
    {
        // Login Page checks
        $loginRes = $this->get('/login');
        $loginRes->assertStatus(200);
        $loginRes->assertSee('placeholder="Enter your email address"', false);
        $loginRes->assertSee('placeholder="Enter your password"', false);
        $loginRes->assertDontSee('<label for="login-email">', false);
        $loginRes->assertDontSee('<label for="login-password">', false);

        // Register Page checks
        $regRes = $this->get('/register');
        $regRes->assertStatus(200);
        $regRes->assertSee('placeholder="Enter your full name"', false);
        $regRes->assertSee('placeholder="Enter your email address"', false);
        $regRes->assertSee('placeholder="Create a password (min. 8 characters)"', false);
        $regRes->assertSee('placeholder="Confirm your password"', false);
        $regRes->assertDontSee('<label for="reg-name">', false);
        $regRes->assertDontSee('<label for="reg-email">', false);
        $regRes->assertDontSee('<label for="reg-password">', false);
        $regRes->assertDontSee('<label for="reg-password-confirm">', false);
    }
}


<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test redirect without credentials warns user instead of logging in static account.
     */
    public function test_redirect_without_credentials_redirects_to_login_with_message(): void
    {
        Config::set('services.google.client_id', null);
        Config::set('services.google.client_secret', null);

        $response = $this->get('/auth/google');
        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Google Sign-In is temporarily unavailable. Please sign in with your email and password.');
        $errorMessage = session('error');
        $this->assertStringNotContainsString('.env', $errorMessage);
        $this->assertStringNotContainsString('GOOGLE_CLIENT_ID', $errorMessage);
    }

    /**
     * Test redirect with credentials calls Socialite driver.
     */
    public function test_redirect_with_credentials_initiates_oauth(): void
    {
        Config::set('services.google.client_id', 'dummy-client-id-12345');
        Config::set('services.google.client_secret', 'dummy-client-secret-12345');

        $driver = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $driver->shouldReceive('redirectUrl')->once()->andReturnSelf();
        $driver->shouldReceive('redirect')->once()->andReturn(redirect('https://accounts.google.com/o/oauth2/v2/auth?test=1'));

        Socialite::shouldReceive('driver')->with('google')->andReturn($driver);

        $response = $this->get('/auth/google');
        $response->assertRedirect('https://accounts.google.com/o/oauth2/v2/auth?test=1');
    }

    /**
     * Test callback authenticates real customer and creates database record.
     */
    public function test_callback_authenticates_customer(): void
    {
        Config::set('services.google.client_id', 'dummy-client-id-12345');
        Config::set('services.google.client_secret', 'dummy-client-secret-12345');

        $socialiteUser = new SocialiteUser();
        $socialiteUser->id = 'google-real-id-998877';
        $socialiteUser->name = 'Prashant Customer';
        $socialiteUser->email = 'realcustomer@gmail.com';
        $socialiteUser->avatar = 'https://lh3.googleusercontent.com/a/sample=s96-c';

        $driver = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $driver->shouldReceive('redirectUrl')->once()->andReturnSelf();
        $driver->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($driver);

        $response = $this->get('/auth/google/callback');

        $this->assertDatabaseHas('users', [
            'email' => 'realcustomer@gmail.com',
            'google_id' => 'google-real-id-998877',
            'name' => 'Prashant Customer',
        ]);

        $this->assertAuthenticated();
        $this->assertEquals('realcustomer@gmail.com', auth()->user()->email);
        $this->assertNull(session('admin_logged_in'));
        $response->assertRedirect(route('home'));
    }

    /**
     * Test callback authenticates admin user and sets admin session flags.
     */
    public function test_callback_authenticates_admin(): void
    {
        Config::set('services.google.client_id', 'dummy-client-id-12345');
        Config::set('services.google.client_secret', 'dummy-client-secret-12345');

        $socialiteUser = new SocialiteUser();
        $socialiteUser->id = 'google-admin-id-112233';
        $socialiteUser->name = 'Artizen Admin';
        $socialiteUser->email = 'admin@artizen.com';
        $socialiteUser->avatar = 'https://lh3.googleusercontent.com/a/admin=s96-c';

        $driver = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $driver->shouldReceive('redirectUrl')->once()->andReturnSelf();
        $driver->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($driver);

        $response = $this->get('/auth/google/callback');

        $this->assertDatabaseHas('users', [
            'email' => 'admin@artizen.com',
            'google_id' => 'google-admin-id-112233',
            'is_admin' => 1,
        ]);

        $this->assertAuthenticated();
        $this->assertTrue(session('admin_logged_in'));
        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Test user cancelling Google login redirects back to login with notice.
     */
    public function test_callback_handles_user_cancellation(): void
    {
        $response = $this->get('/auth/google/callback?error=access_denied&error_description=The+user+denied+the+request');
        $response->assertRedirect('/login');
        $response->assertSessionHas('error');
    }
}

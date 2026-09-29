<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\WhatsAppSmsService;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Step17ProductionDeploymentTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'email' => 'proddeployadmin@artizen.com',
            'password' => Hash::make('Password123!'),
            'is_admin' => true,
        ]);
    }

    /**
     * Test sensitive files are protected from HTTP access.
     */
    public function test_sensitive_files_are_not_in_public_directory(): void
    {
        $this->assertFalse(file_exists(public_path('.env')));
        $this->assertFalse(file_exists(public_path('storage/app/bookings.json')));
        $this->assertFalse(file_exists(public_path('storage/backups')));
        $this->assertFalse(file_exists(public_path('.git')));
    }

    /**
     * Test security headers are applied to web responses.
     */
    public function test_security_headers_applied(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * Test production sitemap and robots.txt accessibility.
     */
    public function test_seo_robots_and_sitemap_exist(): void
    {
        $this->assertTrue(file_exists(public_path('robots.txt')));
        $this->assertTrue(file_exists(public_path('sitemap.xml')));
    }

    /**
     * Test WhatsApp provider configuration audit status.
     */
    public function test_whatsapp_provider_status_check(): void
    {
        $isConfigured = WhatsAppSmsService::isConfigured();
        $this->assertFalse($isConfigured); // Confirms safe NOT CONFIGURED state
    }
}

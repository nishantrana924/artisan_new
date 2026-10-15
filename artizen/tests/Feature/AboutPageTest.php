<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    /**
     * Test public /about and /about-us pages render successfully with rich Indore content.
     */
    public function test_public_about_pages_render_successfully(): void
    {
        $responseUs = $this->get('/about-us');
        $responseUs->assertStatus(200);
        $responseUs->assertSee('Curated Celebrations Across Indore');
        $responseUs->assertSee('1,200+');
        $responseUs->assertSee('4.9 ★');
        $responseUs->assertSee('A Better Way to Celebrate');
        $responseUs->assertSee('Vijay Nagar');
        $responseUs->assertDontSee('Frequently Asked Questions');

        $responseShort = $this->get('/about');
        $responseShort->assertStatus(200);
        $responseShort->assertSee('Curated Celebrations Across Indore');
    }

    /**
     * Test settings table is absent from the database.
     */
    public function test_settings_database_table_is_dropped(): void
    {
        $this->assertFalse(Schema::hasTable('settings'), 'The settings table should be completely dropped from the database.');
    }

    /**
     * Test admin about management route is removed and redirects to dashboard.
     */
    public function test_admin_about_route_redirects(): void
    {
        $admin = new User([
            'name' => 'Artizen Admin',
            'email' => 'admin_about_test@artizen.com',
            'is_admin' => true,
        ]);
        $admin->id = 99;

        $response = $this->actingAs($admin)->get('/admin/about');
        $response->assertRedirect('/admin/dashboard');
    }

    /**
     * Test admin sidebar does not render the about page link.
     */
    public function test_admin_sidebar_does_not_contain_about_page_link(): void
    {
        $admin = new User([
            'name' => 'Artizen Admin',
            'email' => 'admin_about_test2@artizen.com',
            'is_admin' => true,
        ]);
        $admin->id = 100;

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertDontSee('admin/about');
        $response->assertDontSee('About Page');
    }
}

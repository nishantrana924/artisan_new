<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \App\Services\CelebrationCatalogService::clearCache();
    }

    /**
     * Helper to authenticate as an administrator in tests.
     */
    protected function actingAsAdmin($admin = null)
    {
        if (!$admin) {
            $admin = User::firstOrCreate(
                ['email' => 'admin@artizen.com'],
                [
                    'name' => 'Admin User',
                    'password' => Hash::make('Password123!'),
                ]
            );
        }

        if (!$admin->is_admin) {
            $admin->is_admin = 1;
            $admin->save();
        }

        return $this->actingAs($admin)->withSession([
            'admin_logged_in' => true,
            'admin_email'     => $admin->email,
            'admin_name'      => $admin->name,
            'admin_user_id'   => $admin->id,
        ]);
    }
}

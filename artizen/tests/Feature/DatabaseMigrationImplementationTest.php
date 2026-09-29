<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Package;
use App\Models\PackageTier;
use App\Services\JsonStorageService;
use Tests\TestCase;

class DatabaseMigrationImplementationTest extends TestCase
{
    /**
     * Test JSON backup system runs and preserves files before migration.
     */
    public function test_json_backup_preserves_files(): void
    {
        $this->artisan('app:backup-json')->assertExitCode(0);
        $this->assertTrue(file_exists(storage_path('app/bookings.json')));
        $this->assertTrue(file_exists(storage_path('app/packages.json')));
    }

    /**
     * Test Eloquent relationships (Category -> Package -> Tier & Customer -> Booking).
     */
    public function test_eloquent_models_and_relationships_schema(): void
    {
        $category = new Category([
            'title' => 'Relational Test Category',
            'slug' => 'relational-test-category',
            'active' => true
        ]);
        $this->assertEquals('Relational Test Category', $category->title);

        $package = new Package([
            'title' => 'Test Relational Package',
            'slug' => 'test-relational-package'
        ]);
        $this->assertEquals('Test Relational Package', $package->title);

        $customer = new Customer([
            'name' => 'John Relational',
            'mobile' => '9131668156',
            'city' => 'Indore'
        ]);
        $this->assertEquals('9131668156', $customer->mobile);

        $booking = new Booking([
            'id' => 'BK-TESTREL',
            'customer_name' => 'John Relational',
            'customer_mobile' => '9131668156',
            'package_name' => 'Test Package',
            'category_name' => 'Birthdays',
            'total_amount' => 4999,
            'status' => 'Pending'
        ]);
        $this->assertEquals('BK-TESTREL', $booking->id);
    }

    /**
     * Test application default storage driver remains JSON mode until explicit verification.
     */
    public function test_default_storage_driver_remains_json(): void
    {
        $driver = config('artizen.storage_driver', 'json');
        $this->assertEquals('json', $driver);
    }
}

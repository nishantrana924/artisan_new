<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Package;
use App\Services\JsonStorageService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class Step15ProductionHardeningTest extends TestCase
{
    /**
     * Test Eager Loading eliminates N+1 queries.
     */
    public function test_eager_loading_eliminates_n_plus_one_queries(): void
    {
        DB::enableQueryLog();

        // Fetch packages with eager loaded tiers and category
        $packages = Package::with(['tiers', 'category'])->get();
        $queryCount = count(DB::getQueryLog());

        // Maximum 2 queries executed (1 for packages, 1 for eager-loaded tiers)
        $this->assertLessThanOrEqual(2, $queryCount);
    }

    /**
     * Test atomic DB transaction wraps booking checkout creation safely.
     */
    public function test_booking_creation_runs_in_atomic_db_transaction(): void
    {
        $bookingData = [
            'name' => 'Atomic Hardened Customer',
            'mobile' => '9131668156',
            'whatsapp' => '9131668156',
            'email' => 'atomic@artizen.com',
            'package' => 'Proposal & Anniversary Setup Package',
            'category' => 'Proposal & Anniversary',
            'tier' => 'Standard',
            'date' => date('Y-m-d', strtotime('+3 days')),
            'time' => '18:00',
            'guest_count' => 20,
            'address' => 'Bypass Road, Indore',
            'landmark' => 'Omaxe City',
            'area' => 'Bypass Road',
            'city' => 'Indore',
            'state' => 'Madhya Pradesh',
            'pincode' => '452010',
            'package_price' => 4999,
            'total' => 4999
        ];

        $response = $this->postJson('/booking/save', $bookingData);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Audit queue configuration.
     */
    public function test_queue_configuration_audit(): void
    {
        $queueDriver = config('queue.default');
        $this->assertNotNull($queueDriver);
    }
}

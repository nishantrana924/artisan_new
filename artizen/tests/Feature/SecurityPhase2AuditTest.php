<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use App\Models\User;
use App\Services\JsonStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityPhase2AuditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper to create an admin user.
     */
    protected function createAdmin(array $attributes = []): User
    {
        $user = new User(array_merge([
            'name'     => 'Artizen Admin',
            'email'    => 'admin@artizen.com',
            'password' => Hash::make('AdminPass123!'),
        ], $attributes));
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    /**
     * Helper to create a regular customer user.
     */
    protected function createCustomer(array $attributes = []): User
    {
        $user = new User(array_merge([
            'name'     => 'Customer Test',
            'email'    => 'customer@example.com',
            'password' => Hash::make('CustomerPass123!'),
        ], $attributes));
        $user->is_admin = false;
        $user->save();

        return $user;
    }

    // =========================================================================
    // 1. Review Moderation Tests
    // =========================================================================

    public function test_newly_submitted_customer_review_is_pending_moderation_by_default(): void
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)
            ->postJson('/reviews', [
                'rating'       => 5,
                'review'       => 'Antigravity test review: exceptional styling and prompt setup!',
                'package_slug' => 'test-phase2-celebration',
                'event_title'  => 'Test Phase 2 Celebration',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertStringContainsString('moderation', strtolower($response->json('message')));

        // Verify database state: is_active must be false
        $review = Testimonial::where('email', $customer->email)->first();
        $this->assertNotNull($review);
        $this->assertFalse((bool)$review->is_active);

        // Verify JSON storage synchronization: is_active must be false
        $storedReviews = JsonStorageService::read('testimonials.json', []);
        $found = collect($storedReviews)->firstWhere('id', $review->id);
        $this->assertNotNull($found);
        $this->assertFalse((bool)($found['is_active'] ?? false));
    }

    public function test_unapproved_review_is_not_visible_on_public_reviews_page(): void
    {
        // 1. Active review
        $approved = Testimonial::create([
            'id'                   => 'TST-APP-1',
            'author'               => 'Approved Customer',
            'email'                => 'approved@example.com',
            'location'             => 'Indore, MP',
            'event_type'           => 'Golden Gala',
            'review'               => 'VISIBLE_APPROVED_REVIEW_CONTENT_ABC',
            'rating'               => 5,
            'is_verified'          => true,
            'is_active'            => true,
            'show_on_reviews_page' => true,
            'sort_order'           => 1,
        ]);

        // 2. Pending unapproved review
        $pending = Testimonial::create([
            'id'                   => 'TST-PEN-2',
            'author'               => 'Pending Customer',
            'email'                => 'pending@example.com',
            'location'             => 'Indore, MP',
            'event_type'           => 'Secret Party',
            'review'               => 'HIDDEN_PENDING_REVIEW_CONTENT_XYZ',
            'rating'               => 5,
            'is_verified'          => true,
            'is_active'            => false,
            'show_on_reviews_page' => true,
            'sort_order'           => 2,
        ]);

        $response = $this->get('/reviews');
        $response->assertStatus(200);
        $response->assertSee('VISIBLE_APPROVED_REVIEW_CONTENT_ABC');
        $response->assertDontSee('HIDDEN_PENDING_REVIEW_CONTENT_XYZ');
    }

    public function test_admin_can_approve_pending_review_via_toggle_visibility(): void
    {
        $admin = $this->createAdmin();

        $pending = Testimonial::create([
            'id'                   => 'TST-PEN-3',
            'author'               => 'Awaiting Approval',
            'email'                => 'awaiting@example.com',
            'location'             => 'Indore, MP',
            'event_type'           => 'Birthday Spark',
            'review'               => 'Review ready for admin moderation toggle',
            'rating'               => 5,
            'is_verified'          => true,
            'is_active'            => false,
            'show_on_reviews_page' => true,
            'sort_order'           => 1,
        ]);

        $response = $this->actingAs($admin)
            ->withSession([
                'admin_logged_in' => true,
                'admin_email'     => $admin->email,
            ])
            ->postJson('/admin/reviews/toggle-visibility', [
                'id'    => $pending->id,
                'field' => 'is_active',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'   => true,
            'field'     => 'is_active',
            'new_value' => true,
        ]);

        $pending->refresh();
        $this->assertTrue((bool)$pending->is_active);
    }

    // =========================================================================
    // 2. Authorization and Admin Middleware Hardening Tests
    // =========================================================================

    public function test_admin_middleware_rejects_session_spoofing_without_valid_admin_user(): void
    {
        // Attacker creates a session claiming admin_logged_in, but email is not a valid admin user in the DB
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_email'     => 'attacker@fake-domain.com',
        ])->get('/admin/dashboard');

        // Must reject and redirect to login
        $this->assertTrue($response->isRedirect());
        $this->assertStringContainsString('/login', (string)$response->headers->get('Location'));
    }

    public function test_admin_middleware_allows_verified_admin_user(): void
    {
        $admin = $this->createAdmin();

        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_email'     => $admin->email,
        ])->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_admin_booking_status_update_enforces_strict_allowlist(): void
    {
        $admin = $this->createAdmin();

        JsonStorageService::write('bookings.json', [
            [
                'id'     => 'ART-TEST-1234',
                'status' => 'Pending',
                'name'   => 'Test Customer',
            ]
        ]);

        // 1. Rejects arbitrary invalid status
        $badResponse = $this->actingAs($admin)
            ->withSession([
                'admin_logged_in' => true,
                'admin_email'     => $admin->email,
            ])
            ->postJson('/admin/bookings/update-status', [
                'id'     => 'ART-TEST-1234',
                'status' => 'ArbitraryUnapprovedStatus',
            ]);

        $badResponse->assertStatus(422);

        // 2. Accepts valid status from allowlist
        $goodResponse = $this->actingAs($admin)
            ->withSession([
                'admin_logged_in' => true,
                'admin_email'     => $admin->email,
            ])
            ->postJson('/admin/bookings/update-status', [
                'id'     => 'ART-TEST-1234',
                'status' => 'Confirmed',
            ]);

        $goodResponse->assertStatus(200);
    }

    public function test_customer_orders_verifies_ownership_of_session_last_booking(): void
    {
        $customerA = $this->createCustomer(['email' => 'customerA@example.com']);
        $customerB = $this->createCustomer(['email' => 'customerB@example.com']);

        // Store a booking belonging to Customer A
        $bookingA = [
            'id'             => 'ART-BOOKING-A99',
            'user_id'        => $customerA->id,
            'email'          => $customerA->email,
            'customer_name'  => 'Customer A',
            'package_title'  => 'Customer A Secret Package',
            'status'         => 'Pending',
            'created_at'     => date('d M Y, h:i A'),
        ];
        JsonStorageService::write('bookings.json', [$bookingA]);

        // Customer B logs in, but session has last_booking_id pointing to Customer A's booking
        $response = $this->actingAs($customerB)
            ->withSession([
                'last_booking_id' => 'ART-BOOKING-A99',
            ])
            ->get('/orders');

        $response->assertStatus(200);
        // Customer B must NOT see Customer A's booking reference or details
        $response->assertDontSee('ART-BOOKING-A99');
        $response->assertDontSee('Customer A Secret Package');
    }

    // =========================================================================
    // 3. File Upload MIME and Extension Hardening Tests
    // =========================================================================

    public function test_avatar_upload_rejects_non_image_files(): void
    {
        $admin = $this->createAdmin();

        $fakeScript = UploadedFile::fake()->create('malicious.php', 10, 'application/x-php');

        $response = $this->actingAs($admin)
            ->withSession([
                'admin_logged_in' => true,
                'admin_email'     => $admin->email,
            ])
            ->post('/admin/settings/profile', [
                'name'        => 'Admin Name',
                'email'       => $admin->email,
                'avatar_file' => $fakeScript,
            ]);

        $response->assertSessionHasErrors(['avatar_file']);
    }

    public function test_package_upload_rejects_non_image_files(): void
    {
        $admin = $this->createAdmin();

        // Create category required for package validation
        $cat = \App\Models\Category::create([
            'title' => 'Test Category',
            'slug'  => 'test-category',
        ]);

        $fakeScript = UploadedFile::fake()->create('backdoor.sh', 15, 'text/x-shellscript');

        $response = $this->actingAs($admin)
            ->withSession([
                'admin_logged_in' => true,
                'admin_email'     => $admin->email,
            ])
            ->post('/admin/packages/save', [
                'title'       => 'Test Package Upload',
                'category_id' => $cat->id,
                'image_file'  => $fakeScript,
            ]);

        $response->assertSessionHasErrors(['image_file']);
    }
}

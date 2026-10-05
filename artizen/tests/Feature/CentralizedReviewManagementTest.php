<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\ReviewSeeder;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CentralizedReviewManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::firstOrCreate(
            ['email' => 'reviewtestadmin@artizen.com'],
            [
                'name' => 'Review Test Admin',
                'password' => bcrypt('password123'),
                'is_admin' => true,
            ]
        );

        $this->seed(ReviewSeeder::class);
    }

    /**
     * Test 1: Admin can access the dedicated review management page.
     */
    public function test_admin_can_access_reviews_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reviews');

        $response->assertStatus(200);
        $response->assertSee('Total Stories');
        $response->assertSee('Add Review');
        $response->assertSee(route('admin.reviews.create'));
        $response->assertSee('Search customer, review, event...');
    }

    /**
     * Test: Admin can access the separate create review page.
     */
    public function test_admin_can_access_create_review_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reviews/create');

        $response->assertStatus(200);
        $response->assertSee('Add New Review');
        $response->assertSee('Customer Full Name');
        $response->assertSee('Customer Rating');
        $response->assertSee('page-input-is_active');
        $response->assertSee('page-input-show_on_home');
        $response->assertSee('page-input-show_on_reviews_page');
        $response->assertSee('page-input-show_on_event_details');
    }

    /**
     * Test: Admin can access the separate edit review page.
     */
    public function test_admin_can_access_edit_review_page(): void
    {
        $review = Testimonial::first();
        $response = $this->actingAs($this->admin)->get("/admin/reviews/{$review->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('Edit Review');
        $response->assertSee($review->author);
        $response->assertSee('page-input-is_active');
        $response->assertSee('page-input-show_on_home');
    }

    /**
     * Test 2: Admin can save a new review with full independent visibility flags.
     */
    public function test_admin_can_save_new_review_with_independent_visibility_flags(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/reviews/save', [
            'author'               => 'Vikram Singhania',
            'email'                => 'vikram.singhania@example.com',
            'location'             => 'Indore, MP',
            'package_slug'         => 'marry-me-proposal',
            'rating'               => 5,
            'review'               => 'Unforgettable candlelight ambiance and flower arch for my proposal.',
            'is_active'            => 1,
            'show_on_home'         => 1,
            'show_on_reviews_page' => 1,
            'show_on_event_details'=> 1,
            'sort_order'           => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $saved = Testimonial::where('author', 'Vikram Singhania')->first();
        $this->assertNotNull($saved);
        $this->assertEquals('marry-me-proposal', $saved->package_slug);
        $this->assertEquals(5, $saved->rating);
        $this->assertTrue((bool)$saved->is_active);
        $this->assertTrue((bool)$saved->show_on_home);
        $this->assertTrue((bool)$saved->show_on_reviews_page);
        $this->assertTrue((bool)$saved->show_on_event_details);

        // Clean up
        $saved->delete();
    }

    /**
     * Test 3: Admin quick toggle endpoint toggles each visibility flag via AJAX.
     */
    public function test_admin_quick_toggle_endpoint_updates_visibility(): void
    {
        $review = Testimonial::first();
        $this->assertNotNull($review);

        // Toggle show_on_home
        $initialHome = (bool)$review->show_on_home;
        $response = $this->actingAs($this->admin)->postJson('/admin/reviews/toggle-visibility', [
            'id'    => $review->id,
            'field' => 'show_on_home',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $review->refresh();
        $this->assertEquals(!$initialHome, (bool)$review->show_on_home);

        // Revert toggle
        $this->actingAs($this->admin)->postJson('/admin/reviews/toggle-visibility', [
            'id'    => $review->id,
            'field' => 'show_on_home',
        ]);
        $review->refresh();
        $this->assertEquals($initialHome, (bool)$review->show_on_home);
    }

    /**
     * Test 4: Independent Visibility Matrix:
     * - Inactive review is NEVER displayed publicly.
     * - show_on_home=1 only displays on home carousel.
     * - show_on_reviews_page=1 only displays on /reviews.
     * - show_on_event_details=1 only displays on /event/{slug}.
     */
    public function test_independent_visibility_matrix(): void
    {
        // 1. Inactive review
        $inactiveReview = Testimonial::create([
            'id'                   => 'TST-TEST-INACTIVE',
            'author'               => 'Ghost Reviewer',
            'location'             => 'Indore',
            'package_slug'         => 'marry-me-proposal',
            'rating'               => 5,
            'review'               => 'This hidden text should not appear anywhere publicly.',
            'is_active'            => 0,
            'show_on_home'         => 1,
            'show_on_reviews_page' => 1,
            'show_on_event_details'=> 1,
        ]);

        // Assert not on home
        $homeResp = $this->get('/');
        $homeResp->assertDontSee('Secret Private Review');

        // Assert not on /reviews
        $reviewsResp = $this->get('/reviews');
        $reviewsResp->assertDontSee('Secret Private Review');

        // Assert not on event page
        $eventResp = $this->get('/event/marry-me-proposal');
        $eventResp->assertDontSee('Secret Private Review');

        // 2. Home Only Review
        $homeOnly = Testimonial::create([
            'id'                   => 'TST-TEST-HOMEONLY',
            'author'               => 'Home Champion',
            'location'             => 'Indore',
            'package_slug'         => 'marry-me-proposal',
            'rating'               => 5,
            'review'               => 'Home-exclusive testimonial shining bright.',
            'is_active'            => 1,
            'show_on_home'         => 1,
            'show_on_reviews_page' => 0,
            'show_on_event_details'=> 0,
        ]);

        $homeResp = $this->get('/');
        $homeResp->assertSee('Home-exclusive testimonial shining bright.');

        $reviewsResp = $this->get('/reviews');
        $reviewsResp->assertDontSee('Home-exclusive testimonial shining bright.');

        $eventResp = $this->get('/event/marry-me-proposal');
        $eventResp->assertDontSee('Home-exclusive testimonial shining bright.');

        // Clean up temporary records
        $inactiveReview->delete();
        $homeOnly->delete();
    }

    /**
     * Test 5: Event Isolation - Event A review does NOT leak onto Event B.
     */
    public function test_event_isolation_review_does_not_leak(): void
    {
        $proposalReview = Testimonial::create([
            'id'                   => 'TST-TEST-PROPOSAL-ONLY',
            'author'               => 'Proposal King',
            'location'             => 'Indore',
            'package_slug'         => 'marry-me-proposal',
            'rating'               => 5,
            'review'               => 'This proposal review must strictly stay on the proposal package page.',
            'is_active'            => 1,
            'show_on_home'         => 0,
            'show_on_reviews_page' => 1,
            'show_on_event_details'=> 1,
        ]);

        // Proposal page MUST see it
        $proposalPage = $this->get('/event/marry-me-proposal');
        $proposalPage->assertStatus(200);
        $proposalPage->assertSee('This proposal review must strictly stay on the proposal package page.');

        // Another package (e.g., 1st birthday) MUST NOT see it
        $birthdayPage = $this->get('/event/1st-birthday-wonderland');
        $birthdayPage->assertStatus(200);
        $birthdayPage->assertDontSee('This proposal review must strictly stay on the proposal package page.');

        // Clean up
        $proposalReview->delete();
    }

    /**
     * Test 6: Admin can delete a review.
     */
    public function test_admin_can_delete_review(): void
    {
        $review = Testimonial::create([
            'id'                   => 'TST-TO-DELETE',
            'author'               => 'Disposable Reviewer',
            'location'             => 'Indore',
            'rating'               => 4,
            'review'               => 'Temporary review to be purged.',
            'is_active'            => 1,
        ]);

        $deleteResp = $this->actingAs($this->admin)->post("/admin/reviews/delete/{$review->id}");
        $deleteResp->assertRedirect();

        $this->assertNull(Testimonial::find('TST-TO-DELETE'));
    }

    /**
     * Test 7: Recent reviews appear at the top and pagination activates after 16 reviews.
     */
    public function test_admin_recent_review_shown_at_top_and_paginated(): void
    {
        // Create a distinct recent review
        $brandNew = Testimonial::create([
            'id'                   => 'TST-BRAND-NEW-TOP',
            'author'               => 'Brand New VIP Customer',
            'location'             => 'Indore, MP',
            'package_slug'         => 'marry-me-proposal',
            'rating'               => 5,
            'review'               => 'This review was just posted moments ago and must be at the very top.',
            'is_active'            => 1,
            'show_on_home'         => 1,
            'show_on_reviews_page' => 1,
            'show_on_event_details'=> 1,
            'created_at'           => now()->addHour(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/reviews');
        $response->assertStatus(200);
        $response->assertSee('Brand New VIP Customer');

        // Verify it is on the first page
        $reviewsOnPage = $response->viewData('reviews');
        $this->assertEquals('TST-BRAND-NEW-TOP', $reviewsOnPage->first()->id);

        // Ensure pagination triggers after 16 reviews
        $createdIds = ['TST-BRAND-NEW-TOP'];
        $currentTotal = Testimonial::count();
        $needed = max(0, 18 - $currentTotal);
        for ($i = 1; $i <= $needed; $i++) {
            $t = Testimonial::create([
                'id'         => "TST-PAGINATE-FILL-$i",
                'author'     => "Pagination Test Customer $i",
                'rating'     => 5,
                'review'     => "Review content filler $i",
                'created_at' => now()->subDays($i + 10),
            ]);
            $createdIds[] = $t->id;
        }

        $paginatedResp = $this->actingAs($this->admin)->get('/admin/reviews');
        $paginatedResp->assertStatus(200);
        $paginatedReviews = $paginatedResp->viewData('reviews');

        // Must paginate at 16 per page
        $this->assertEquals(16, $paginatedReviews->perPage());
        $this->assertTrue($paginatedReviews->total() >= 17);
        $this->assertTrue($paginatedReviews->hasPages());

        // Verify page 2 is accessible
        $page2Resp = $this->actingAs($this->admin)->get('/admin/reviews?page=2');
        $page2Resp->assertStatus(200);

        // Clean up
        Testimonial::whereIn('id', $createdIds)->delete();
    }
}

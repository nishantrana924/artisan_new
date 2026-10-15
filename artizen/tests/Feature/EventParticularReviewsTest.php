<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Http\Controllers\ReviewController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventParticularReviewsTest extends TestCase
{
    use RefreshDatabase;
    public function test_package_reviews_are_particular_to_event(): void
    {
        $birthdayReviews = ReviewController::getReviewsForPackage(
            '1st-birthday-wonderland',
            '1st Birthday Wonderland',
            'Birthdays'
        );

        $this->assertNotEmpty($birthdayReviews['reviews']);
        $this->assertGreaterThanOrEqual(1, $birthdayReviews['total_count']);
        $this->assertEquals('5.0', $birthdayReviews['avg_rating']);

        $proposalReviews = ReviewController::getReviewsForPackage(
            'marry-me-proposal',
            'The Marry Me Proposal',
            'Proposals'
        );

        $this->assertNotEmpty($proposalReviews['reviews']);
        $this->assertGreaterThanOrEqual(1, $proposalReviews['total_count']);
    }

    public function test_guest_cannot_submit_review(): void
    {
        $payload = [
            'rating' => 5,
            'review' => 'Great setup for my birthday celebration in Indore!',
            'package_slug' => 'test-celebration-slug',
            'event_title' => 'Test Celebration'
        ];

        $response = $this->postJson('/reviews', $payload);
        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'require_login' => true
        ]);
    }

    public function test_authenticated_user_can_submit_review_with_only_rating_and_description(): void
    {
        $user = new User([
            'id' => 999,
            'name' => 'Vikram Sharma',
            'email' => 'vikram@example.com'
        ]);

        $payload = [
            'rating' => 5,
            'review' => 'Spectacular setup and polite team. Offline payment post verification was smooth.',
            'package_slug' => 'test-celebration-slug',
            'event_title' => 'Test Celebration'
        ];

        $response = $this->actingAs($user)->postJson('/reviews', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'review' => [
                'author' => 'Vikram Sharma',
                'email' => 'vikram@example.com',
                'rating' => 5,
                'review' => 'Spectacular setup and polite team. Offline payment post verification was smooth.'
            ]
        ]);
    }

    public function test_reviews_page_filters_by_particular_package(): void
    {
        $response = $this->get('/reviews?package=1st-birthday-wonderland&title=1st+Birthday+Wonderland');
        $response->assertStatus(200);
        $response->assertSee('Filtered by Package:');
        $response->assertSee('1st Birthday Wonderland');
        $response->assertSee('Clear Filter (View All)');
    }

    public function test_event_details_view_all_links_to_filtered_reviews_page(): void
    {
        $response = $this->get('/event/1st-birthday-wonderland');
        $response->assertStatus(200);
        $response->assertSee('/reviews?package=1st-birthday-wonderland');
    }

    public function test_user_who_already_reviewed_package_sees_edit_your_review_button(): void
    {
        $user = new User([
            'id' => 888,
            'name' => 'Aditi Sharma',
            'email' => 'aditi@example.com'
        ]);

        // Submit initial review
        $payload = [
            'rating' => 5,
            'review' => 'First review for this lovely event package!',
            'package_slug' => '1st-birthday-wonderland',
            'event_title' => '1st Birthday Wonderland'
        ];
        $this->actingAs($user)->postJson('/reviews', $payload);

        // Visit the event details page as this user
        $response = $this->actingAs($user)->get('/event/1st-birthday-wonderland');
        $response->assertStatus(200);
        $response->assertSee('Edit Your Review');
        $response->assertSee('First review for this lovely event package!');
    }

    public function test_user_cannot_create_multiple_reviews_for_same_package_and_submission_updates_existing_review(): void
    {
        $user = new User([
            'id' => 777,
            'name' => 'Rahul Khanna',
            'email' => 'rahul.k@example.com'
        ]);

        // First review
        $payload1 = [
            'rating' => 4,
            'review' => 'Initial 4-star experience with this celebration decor.',
            'package_slug' => 'marry-me-proposal',
            'event_title' => 'The Marry Me Proposal'
        ];
        $res1 = $this->actingAs($user)->postJson('/reviews', $payload1);
        $res1->assertStatus(200);
        $res1->assertJson([
            'success' => true,
            'is_edit' => false
        ]);

        // Attempting to post a second review for the same package
        $payload2 = [
            'rating' => 5,
            'review' => 'Updated review: Truly 5-star experience after final setup!',
            'package_slug' => 'marry-me-proposal',
            'event_title' => 'The Marry Me Proposal'
        ];
        $res2 = $this->actingAs($user)->postJson('/reviews', $payload2);
        $res2->assertStatus(200);
        $res2->assertJson([
            'success' => true,
            'is_edit' => true,
            'review' => [
                'rating' => 5,
                'review' => 'Updated review: Truly 5-star experience after final setup!'
            ]
        ]);

        // Ensure there is only 1 review for this user and package in the database
        $userReviewsCount = \App\Models\Testimonial::where('package_slug', 'marry-me-proposal')
            ->where(function($q) use ($user) {
                $q->where('email', $user->email)->orWhere('author', $user->name);
            })
            ->count();
        $this->assertEquals(1, $userReviewsCount);
    }

    public function test_reviews_page_orders_by_rating_descending_and_newest_on_top(): void
    {
        // 1. Create older 5-star review
        \App\Models\Testimonial::create([
            'id' => 'TST-OLD-5',
            'author' => 'Older 5 Star Author',
            'rating' => 5,
            'review' => 'Great experience 5 star.',
            'event_type' => 'Birthday Party',
            'is_active' => true,
            'show_on_reviews_page' => true,
            'created_at' => now()->subDays(5),
        ]);

        // 2. Create newer 5-star review
        \App\Models\Testimonial::create([
            'id' => 'TST-NEW-5',
            'author' => 'Newer 5 Star Author',
            'rating' => 5,
            'review' => 'Brand new 5 star review.',
            'event_type' => 'Birthday Party',
            'is_active' => true,
            'show_on_reviews_page' => true,
            'created_at' => now(),
        ]);

        // 3. Create a 4-star review
        \App\Models\Testimonial::create([
            'id' => 'TST-NEW-4',
            'author' => '4 Star Author',
            'rating' => 4,
            'review' => 'Good 4 star review.',
            'event_type' => 'Corporate Event',
            'is_active' => true,
            'show_on_reviews_page' => true,
            'created_at' => now(),
        ]);

        $response = $this->get('/reviews');
        $response->assertStatus(200);

        $reviewList = $response->viewData('reviewList');
        $this->assertNotEmpty($reviewList);

        // Newer 5-star review must appear before older 5-star review
        $new5Index = null;
        $old5Index = null;
        $new4Index = null;

        foreach ($reviewList as $idx => $r) {
            if (($r['id'] ?? null) === 'TST-NEW-5') $new5Index = $idx;
            if (($r['id'] ?? null) === 'TST-OLD-5') $old5Index = $idx;
            if (($r['id'] ?? null) === 'TST-NEW-4') $new4Index = $idx;
        }

        $this->assertNotNull($new5Index);
        $this->assertNotNull($old5Index);
        $this->assertNotNull($new4Index);

        // Assert 5-star newest is before 5-star older
        $this->assertLessThan($old5Index, $new5Index);
        // Assert 5-star reviews are before 4-star reviews
        $this->assertLessThan($new4Index, $new5Index);
        $this->assertLessThan($new4Index, $old5Index);
    }
}

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
        $this->assertStringContainsString('proposal', strtolower($proposalReviews['reviews'][0]['text']));
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
}

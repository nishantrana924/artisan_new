<?php

namespace Tests\Feature;

use App\Models\CelebrationRecipient;
use App\Models\Category;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CelebrationRecipientManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        // Cleanup test images if created in public/images/for-everyone
        $testFiles = File::glob(public_path('images/for-everyone/recipient_test-*.*'));
        foreach ($testFiles as $file) {
            @unlink($file);
        }
        parent::tearDown();
    }

    public function test_admin_can_view_recipients_list(): void
    {
        CelebrationRecipient::create([
            'name'          => 'Test Recipient',
            'slug'          => 'test-recipient',
            'image'         => '/images/for-everyone/him.webp',
            'display_order' => 1,
            'is_active'     => true,
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.recipients'));

        $response->assertStatus(200);
        $response->assertSee('Test Recipient');
        $response->assertSee('Celebrations for Everyone');
        $response->assertSee('/events?for=test-recipient');
    }

    public function test_admin_can_view_create_and_edit_forms(): void
    {
        $recipient = CelebrationRecipient::create([
            'name'          => 'For Mother',
            'slug'          => 'for-mother',
            'image'         => '/images/for-everyone/her.webp',
            'display_order' => 1,
            'is_active'     => true,
        ]);

        // Create form
        $createResponse = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.recipients.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Persona Details');
        $createResponse->assertSee('Persona Name / Title');

        // Edit form
        $editResponse = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.recipients.edit', $recipient->id));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Edit Persona: For Mother');
        $editResponse->assertSee('for-mother');
    }

    public function test_admin_can_create_recipient_with_webp_conversion(): void
    {
        $image = UploadedFile::fake()->image('test-avatar.png', 400, 300);

        $payload = [
            'name'          => 'Brother',
            'slug'          => 'brother',
            'display_order' => 2,
            'is_active'     => '1',
            'image_file'    => $image,
        ];

        $response = $this->withSession(['admin_logged_in' => true])
            ->post(route('admin.recipients.save'), $payload);

        $response->assertRedirect(route('admin.recipients'));
        $this->assertDatabaseHas('celebration_recipients', [
            'name'          => 'Brother',
            'slug'          => 'brother',
            'is_active'     => true,
        ]);

        $saved = CelebrationRecipient::where('slug', 'brother')->first();
        $this->assertNotNull($saved);
        $this->assertStringEndsWith('.webp', $saved->image);
        $this->assertStringContainsString('/images/for-everyone/', $saved->image);
        $this->assertEquals(route('events.index', ['for' => 'brother']), $saved->target_url);

        // Clean up generated file
        if (File::exists(public_path($saved->image))) {
            @unlink(public_path($saved->image));
        }
    }

    public function test_admin_can_update_recipient_and_toggle_status(): void
    {
        $recipient = CelebrationRecipient::create([
            'name'          => 'Colleague',
            'slug'          => 'colleague',
            'image'         => '/images/for-everyone/friend.webp',
            'display_order' => 5,
            'is_active'     => true,
        ]);

        // Update
        $response = $this->withSession(['admin_logged_in' => true])
            ->post(route('admin.recipients.save'), [
                'id'            => $recipient->id,
                'name'          => 'Colleague Updated',
                'slug'          => 'colleague',
                'existing_image'=> '/images/for-everyone/friend.webp',
                'display_order' => 10,
                'is_active'     => '1',
            ]);

        $response->assertRedirect(route('admin.recipients'));
        $recipient->refresh();
        $this->assertEquals('Colleague Updated', $recipient->name);
        $this->assertEquals(route('events.index', ['for' => 'colleague']), $recipient->target_url);

        // Toggle Status via AJAX
        $ajaxResponse = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.recipients.toggle-status', $recipient->id));

        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJson(['is_active' => false]);
        $recipient->refresh();
        $this->assertFalse($recipient->is_active);
    }

    public function test_admin_can_delete_recipient(): void
    {
        $recipient = CelebrationRecipient::create([
            'name'          => 'Temp Persona',
            'slug'          => 'temp-persona',
            'image'         => '/images/for-everyone/kids.webp',
            'is_active'     => true,
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->delete(route('admin.recipients.delete', $recipient->id));

        $response->assertRedirect(route('admin.recipients'));
        $this->assertDatabaseMissing('celebration_recipients', ['id' => $recipient->id]);
    }

    public function test_storefront_renders_active_recipients_dynamically(): void
    {
        CelebrationRecipient::create([
            'name'          => 'Grandparents',
            'slug'          => 'grandparents',
            'image'         => '/images/for-everyone/parents.webp',
            'display_order' => 1,
            'is_active'     => true,
        ]);

        CelebrationRecipient::create([
            'name'          => 'Secret Hidden',
            'slug'          => 'secret-hidden',
            'image'         => '/images/for-everyone/him.webp',
            'is_active'     => false, // Inactive
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Grandparents');
        $response->assertDontSee('Secret Hidden');
    }

    public function test_admin_can_save_package_with_multiple_recipients(): void
    {
        $category = Category::create([
            'title'         => 'House Party',
            'slug'          => 'house-party',
            'nav_slug'      => 'cat-house-party',
            'display_order' => 1,
            'active'        => true,
        ]);

        $payload = [
            'title'       => 'Neon Rooftop Party',
            'category_id' => $category->id,
            'price'       => 7500,
            'recipients'  => ['him', 'friend', 'husband'],
            'active'      => '1',
        ];

        $response = $this->withSession(['admin_logged_in' => true])
            ->post(route('admin.packages.save'), $payload);

        $response->assertRedirect(route('admin.packages'));

        $package = Package::where('title', 'Neon Rooftop Party')->first();
        $this->assertNotNull($package);
        $this->assertEquals(['him', 'friend', 'husband'], $package->recipients);
    }

    public function test_events_page_filters_by_recipient_persona_across_categories(): void
    {
        $cat1 = Category::create([
            'title'         => 'Birthdays',
            'slug'          => 'birthdays',
            'nav_slug'      => 'cat-birthdays',
            'display_order' => 1,
            'active'        => true,
        ]);

        $cat2 = Category::create([
            'title'         => 'Proposals',
            'slug'          => 'proposals',
            'nav_slug'      => 'cat-proposals',
            'display_order' => 2,
            'active'        => true,
        ]);

        Package::create([
            'category_id' => $cat1->id,
            'title'       => 'Birthday Setup For Him',
            'slug'        => 'birthday-for-him',
            'price'       => 5000,
            'recipients'  => ['him'],
            'active'      => true,
        ]);

        Package::create([
            'category_id' => $cat2->id,
            'title'       => 'Proposal Setup For Him',
            'slug'        => 'proposal-for-him',
            'price'       => 8000,
            'recipients'  => ['him', 'husband'],
            'active'      => true,
        ]);

        Package::create([
            'category_id' => $cat1->id,
            'title'       => 'Princess Baby Shower',
            'slug'        => 'princess-baby-shower',
            'price'       => 6000,
            'recipients'  => ['kids'],
            'active'      => true,
        ]);

        $response = $this->get('/events?for=him');
        $response->assertStatus(200);
        $response->assertSee('Birthday Setup For Him');
        $response->assertSee('Proposal Setup For Him');
        $response->assertViewHas('packages', function ($pkgs) {
            $titles = array_column($pkgs, 'title');
            return in_array('Birthday Setup For Him', $titles)
                && in_array('Proposal Setup For Him', $titles)
                && !in_array('Princess Baby Shower', $titles);
        });
    }
}

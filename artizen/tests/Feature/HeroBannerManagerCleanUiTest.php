<?php

namespace Tests\Feature;

use App\Models\CmsSlide;
use App\Models\User;
use App\Services\JsonStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HeroBannerManagerCleanUiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = new User([
            'name'     => 'Artizen Admin',
            'email'    => 'admin@artizen.com',
            'password' => bcrypt('password123'),
        ]);
        $this->admin->is_admin = true;
        $this->admin->save();

        CmsSlide::create([
            'id'            => 1,
            'title'         => 'ELEVATE YOUR CELEBRATIONS',
            'description'   => 'Book complete premium decor, sound, and lighting setups.',
            'image'         => '/assets/images/hero/1.jpg',
            'button1_text'  => 'Explore Packages',
            'button1_link'  => '/events',
            'display_order' => 0,
            'is_active'     => true,
        ]);
    }

    public function test_badge_column_is_not_present_in_cms_slides_table(): void
    {
        $this->assertFalse(Schema::hasColumn('cms_slides', 'badge'));
    }

    public function test_admin_hero_manager_page_has_no_top_metric_cards_and_no_badge_fields(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.hero'));
        $response->assertStatus(200);

        // Metric cards removed
        $response->assertDontSee('TOTAL SLIDES');
        $response->assertDontSee('DRAFT / HIDDEN');
        $response->assertDontSee('16:9 WebP');
        $response->assertDontSee('Auto-compressed images');

        // Badge input removed
        $response->assertDontSee('Top Eyebrow Badge');
        $response->assertDontSee('name="hero_slides[0][badge]"', false);

        // CKEditor removed
        $response->assertDontSee('ckeditor.js');
        $response->assertDontSee('ck-editor-textarea');

        // Required streamlined fields are present
        $response->assertSee('Slide Image');
        $response->assertSee('Main Headline');
        $response->assertSee('Description');
        $response->assertSee('Button Text');
        $response->assertSee('Button Link URL');
        $response->assertSee('50 words');
        $response->assertSee('Visible on Homepage');
    }

    public function test_hero_banner_description_rejects_more_than_50_words(): void
    {
        // 55-word description
        $longDescription = str_repeat('celebration ', 55);

        $payload = [
            'hero_slides' => [
                [
                    'id'            => 1,
                    'title'         => 'VALID TITLE',
                    'desc'          => $longDescription,
                    'btn1Text'      => 'Explore',
                    'link1'         => '/events',
                    'image'         => '/assets/images/hero/1.jpg',
                    'is_active'     => '1',
                    'display_order' => 0,
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.hero.save'), $payload);
        $response->assertSessionHasErrors(['hero_slides.0.desc']);
    }

    public function test_hero_banner_saves_cleanly_with_50_words_or_less_and_no_badge(): void
    {
        $validDescription = 'Book complete premium decor, sound, and lighting setups instantly for birthdays, house parties, and weddings.';

        $payload = [
            'hero_slides' => [
                [
                    'id'            => 1,
                    'title'         => 'CLEAN CELEBRATIONS INDORE',
                    'desc'          => $validDescription,
                    'btn1Text'      => 'Book Packages',
                    'link1'         => '/events',
                    'image'         => '/assets/images/hero/1.jpg',
                    'is_active'     => '1',
                    'display_order' => 0,
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.hero.save'), $payload);
        $response->assertRedirect(route('admin.hero'));
        $response->assertSessionHas('success');

        $slide = CmsSlide::find(1);
        $this->assertEquals('CLEAN CELEBRATIONS INDORE', $slide->title);
        $this->assertEquals($validDescription, $slide->description);
        $this->assertEquals('Book Packages', $slide->button1_text);
        $this->assertEquals('/events', $slide->button1_link);
        $this->assertTrue($slide->is_active);
    }
}

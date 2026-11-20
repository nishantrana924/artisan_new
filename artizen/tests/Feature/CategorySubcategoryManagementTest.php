<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Package;
use App\Models\Subcategory;
use App\Services\CelebrationCatalogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorySubcategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_save_category_with_subcategories(): void
    {
        $payload = [
            'title'              => 'Luxury Weddings',
            'nav_slug'           => 'cat-weddings',
            'icon'               => 'fa-solid fa-ring',
            'bg_color'           => '#FFE4E6',
            'slider_image'       => '/images/occasions/weddings.webp',
            'dropdown_image'     => '/images/dropdowns/weddings.webp',
            'dropdown_badge'     => 'Royal',
            'display_order'      => 1,
            'active'             => '1',
            'subcategories_json' => json_encode([
                [
                    'name'       => 'Mandap Setups',
                    'group_name' => 'Stage & Decor',
                    'badge'      => 'Popular',
                ],
                [
                    'name'       => 'Sangeet DJ Rigs',
                    'group_name' => 'Music & FX',
                    'badge'      => 'Top',
                ],
            ]),
        ];

        $response = $this->actingAsAdmin()
            ->post(route('admin.categories.save'), $payload);

        $response->assertRedirect(route('admin.categories'));

        $category = Category::where('title', 'Luxury Weddings')->first();
        $this->assertNotNull($category);
        $this->assertEquals('cat-weddings', $category->nav_slug);
        $this->assertEquals('/images/occasions/weddings.webp', $category->slider_image);
        $this->assertEquals('/images/dropdowns/weddings.webp', $category->dropdown_image);
        $this->assertEquals('Royal', $category->dropdown_badge);

        $this->assertCount(2, $category->subcategories);
        $this->assertEquals('Mandap Setups', $category->subcategories[0]->name);
        $this->assertEquals('Stage & Decor', $category->subcategories[0]->group_name);
        $this->assertEquals('Popular', $category->subcategories[0]->badge);
    }

    public function test_can_delete_category_and_its_subcategories(): void
    {
        $cat = Category::create([
            'title' => 'House Party Test',
            'slug'  => 'house-party-test',
        ]);

        Subcategory::create([
            'category_id' => $cat->id,
            'name'        => 'Neon Lights',
            'slug'        => 'neon-lights',
        ]);

        $this->assertDatabaseHas('categories', ['id' => $cat->id]);
        $this->assertDatabaseHas('subcategories', ['category_id' => $cat->id]);

        $response = $this->actingAsAdmin()
            ->post(route('admin.categories.delete', $cat->id));

        $response->assertRedirect(route('admin.categories'));
        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
        $this->assertDatabaseMissing('subcategories', ['category_id' => $cat->id]);
    }

    public function test_adding_category_at_order_1_shifts_existing_orders_automatically(): void
    {
        // Setup initial categories 1, 2, 3
        $cat1 = Category::create(['title' => 'Cat 1', 'slug' => 'cat-1', 'display_order' => 1]);
        $cat2 = Category::create(['title' => 'Cat 2', 'slug' => 'cat-2', 'display_order' => 2]);
        $cat3 = Category::create(['title' => 'Cat 3', 'slug' => 'cat-3', 'display_order' => 3]);

        // Add new category with display_order = 1
        $this->actingAsAdmin()
            ->post(route('admin.categories.save'), [
                'title' => 'New Prime Cat',
                'display_order' => 1,
            ]);

        $this->assertEquals(1, Category::where('title', 'New Prime Cat')->value('display_order'));
        $this->assertEquals(2, $cat1->fresh()->display_order);
        $this->assertEquals(3, $cat2->fresh()->display_order);
        $this->assertEquals(4, $cat3->fresh()->display_order);
    }

    public function test_updating_category_order_reorders_all_categories_properly(): void
    {
        $cat1 = Category::create(['title' => 'Cat 1', 'slug' => 'cat-1', 'display_order' => 1]);
        $cat2 = Category::create(['title' => 'Cat 2', 'slug' => 'cat-2', 'display_order' => 2]);
        $cat3 = Category::create(['title' => 'Cat 3', 'slug' => 'cat-3', 'display_order' => 3]);
        $cat4 = Category::create(['title' => 'Cat 4', 'slug' => 'cat-4', 'display_order' => 4]);

        // Move Cat 4 to position 1
        $this->actingAsAdmin()
            ->post(route('admin.categories.save'), [
                'id' => $cat4->id,
                'title' => 'Cat 4',
                'display_order' => 1,
            ]);

        $this->assertEquals(1, $cat4->fresh()->display_order);
        $this->assertEquals(2, $cat1->fresh()->display_order);
        $this->assertEquals(3, $cat2->fresh()->display_order);
        $this->assertEquals(4, $cat3->fresh()->display_order);
    }

    public function test_category_has_no_default_images_when_not_provided(): void
    {
        $payload = [
            'title'              => 'No Image Category',
            'nav_slug'           => 'cat-no-image',
            'icon'               => 'fa-solid fa-star',
            'bg_color'           => '#F6CFB2',
            'slider_image'       => '',
            'dropdown_image'     => '',
            'display_order'      => 1,
            'active'             => '1',
            'subcategories_json' => '[]',
        ];

        $response = $this->actingAsAdmin()
            ->post(route('admin.categories.save'), $payload);

        $response->assertRedirect(route('admin.categories'));

        $category = Category::where('title', 'No Image Category')->first();
        $this->assertNotNull($category);
        $this->assertNull($category->slider_image);
        $this->assertNull($category->dropdown_image);
    }

    public function test_can_render_admin_categories_index_page(): void
    {
        $cat = Category::create([
            'title'         => 'Birthday Celebrations',
            'slug'          => 'birthday-celebrations',
            'nav_slug'      => 'cat-birthdays',
            'icon'          => 'fa-solid fa-cake-candles',
            'bg_color'      => '#F6CFB2',
            'display_order' => 1,
            'active'        => true,
        ]);

        Subcategory::create([
            'category_id' => $cat->id,
            'name'        => 'Neon Theme Backdrops',
            'slug'        => 'neon-theme-backdrops',
            'group_name'  => 'Setup Themes',
        ]);

        $response = $this->actingAsAdmin()
            ->get(route('admin.categories'));

        $response->assertStatus(200);
        $response->assertSee('Event Categories');
        $response->assertSee('Birthday Celebrations');
        $response->assertSee('cat-birthdays');
        $response->assertSee('Neon Theme Backdrops');
        $response->assertSee('Search categories');
    }

    public function test_can_render_category_create_page(): void
    {
        $response = $this->actingAsAdmin()
            ->get(route('admin.categories.create'));

        $response->assertStatus(200);
        $response->assertSee('Create Category');
        $response->assertSee('Category Title');
        $response->assertSee('FontAwesome Icon');
        $response->assertSee('Slider Capsule Preview');
    }

    public function test_can_render_category_edit_page(): void
    {
        $cat = Category::create([
            'title'          => 'Corporate Events',
            'slug'           => 'corporate-events',
            'nav_slug'       => 'cat-corporate',
            'icon'           => 'fa-solid fa-briefcase',
            'bg_color'       => '#D7EAF3',
            'dropdown_badge' => 'Executive Choice',
            'display_order'  => 3,
            'active'         => true,
        ]);

        $sub = Subcategory::create([
            'category_id' => $cat->id,
            'name'        => 'Annual General Meeting Setups',
            'slug'        => 'agm-setups',
            'group_name'  => 'Conferences',
            'badge'       => 'Popular',
        ]);

        $response = $this->actingAsAdmin()
            ->get(route('admin.categories.edit', $cat->id));

        $response->assertStatus(200);
        $response->assertSee('Edit Category');
        $response->assertSee('Corporate Events');
        $response->assertSee('Executive Choice');
        $response->assertSee('Annual General Meeting Setups');
    }

    public function test_category_validation_fails_when_title_missing(): void
    {
        $response = $this->actingAsAdmin()
            ->post(route('admin.categories.save'), [
                'title' => '',
            ]);

        $response->assertSessionHasErrors(['title']);
    }

    public function test_category_slug_collision_resolves_cleanly(): void
    {
        $existing = Category::create([
            'title' => 'House Party & DJ',
            'slug'  => 'house-party-dj',
        ]);

        $this->actingAsAdmin()
            ->post(route('admin.categories.save'), [
                'title' => 'House Party & DJ', // Same title, triggers collision check
            ]);

        $newCat = Category::where('id', '!=', $existing->id)->first();
        $this->assertNotNull($newCat);
        $this->assertStringStartsWith('house-party-dj-', $newCat->slug);
    }

    public function test_can_toggle_category_status_via_ajax(): void
    {
        $cat = Category::create([
            'title'  => 'Baby Shower Occasions',
            'slug'   => 'baby-shower-occasions',
            'active' => true,
        ]);

        $response = $this->actingAsAdmin()
            ->postJson(route('admin.categories.toggle-status', $cat->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'active'  => false,
        ]);

        $this->assertFalse($cat->fresh()->active);
    }

    public function test_category_update_preserves_existing_images_when_no_new_file_uploaded(): void
    {
        $cat = Category::create([
            'title'          => 'Romantic Proposals',
            'slug'           => 'romantic-proposals',
            'slider_image'   => 'https://example.com/proposal-slider.webp',
            'dropdown_image' => 'https://example.com/proposal-dropdown.webp',
        ]);

        $this->actingAsAdmin()
            ->post(route('admin.categories.save'), [
                'id'    => $cat->id,
                'title' => 'Romantic Proposals Updated',
            ]);

        $cat->refresh();
        $this->assertEquals('Romantic Proposals Updated', $cat->title);
        $this->assertEquals('https://example.com/proposal-slider.webp', $cat->slider_image);
        $this->assertEquals('https://example.com/proposal-dropdown.webp', $cat->dropdown_image);
    }

    public function test_deleting_category_safely_decouples_attached_packages_without_deleting_packages(): void
    {
        $cat = Category::create([
            'title' => 'Anniversary Gala',
            'slug'  => 'anniversary-gala',
        ]);

        $package = Package::create([
            'category_id' => $cat->id,
            'title'       => 'Silver Jubilee Package',
            'slug'        => 'silver-jubilee-package',
            'active'      => true,
        ]);

        $this->assertEquals($cat->id, $package->category_id);

        $response = $this->actingAsAdmin()
            ->post(route('admin.categories.delete', $cat->id));

        $response->assertRedirect(route('admin.categories'));
        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);

        $package->refresh();
        $this->assertNull($package->category_id);
    }

    public function test_clearing_all_subcategories_in_editor_deletes_all_existing_subcategories(): void
    {
        $cat = Category::create([
            'title' => 'Live Music & Acoustic',
            'slug'  => 'live-music-acoustic',
        ]);

        Subcategory::create([
            'category_id' => $cat->id,
            'name'        => 'Acoustic Guitarists',
            'slug'        => 'acoustic-guitarists',
        ]);

        Subcategory::create([
            'category_id' => $cat->id,
            'name'        => 'Live Drummers',
            'slug'        => 'live-drummers',
        ]);

        $this->assertCount(2, $cat->subcategories);

        // Submit with empty subcategories list
        $this->actingAsAdmin()
            ->post(route('admin.categories.save'), [
                'id'                 => $cat->id,
                'title'              => 'Live Music & Acoustic',
                'subcategories_json' => '[]',
            ]);

        $this->assertCount(0, $cat->fresh()->subcategories);
    }

    public function test_subcategories_limit_capped_at_eighteen(): void
    {
        $cat = Category::create([
            'title' => 'Grand Banquets',
            'slug'  => 'grand-banquets',
        ]);

        $twentySubcats = [];
        for ($i = 1; $i <= 20; $i++) {
            $twentySubcats[] = [
                'name'       => "Subcat Item {$i}",
                'group_name' => 'Themes',
            ];
        }

        $this->actingAsAdmin()
            ->post(route('admin.categories.save'), [
                'id'                 => $cat->id,
                'title'              => 'Grand Banquets',
                'subcategories_json' => json_encode($twentySubcats),
            ]);

        // Must cap at 18
        $this->assertCount(18, $cat->fresh()->subcategories);
    }

    public function test_public_website_reflects_active_categories(): void
    {
        $activeCat = Category::create([
            'title'         => 'Carnival Fairs',
            'slug'          => 'carnival-fairs',
            'nav_slug'      => 'cat-carnival',
            'display_order' => 10,
            'active'        => true,
        ]);

        $inactiveCat = Category::create([
            'title'         => 'Secret Vault Parties',
            'slug'          => 'secret-vault-parties',
            'nav_slug'      => 'cat-secret',
            'display_order' => 11,
            'active'        => false,
        ]);

        CelebrationCatalogService::clearCache();
        $frontendCategories = CelebrationCatalogService::getCategories();

        $this->assertArrayHasKey('cat-carnival', $frontendCategories);
        $this->assertArrayNotHasKey('cat-secret', $frontendCategories);
    }
}

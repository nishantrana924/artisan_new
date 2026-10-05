<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Subcategory;
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

        $response = $this->withSession(['admin_logged_in' => true])
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

        $response = $this->withSession(['admin_logged_in' => true])
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
        $this->withSession(['admin_logged_in' => true])
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
        $this->withSession(['admin_logged_in' => true])
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

        $response = $this->withSession(['admin_logged_in' => true])
            ->post(route('admin.categories.save'), $payload);

        $response->assertRedirect(route('admin.categories'));

        $category = Category::where('title', 'No Image Category')->first();
        $this->assertNotNull($category);
        $this->assertNull($category->slider_image);
        $this->assertNull($category->dropdown_image);
    }
}

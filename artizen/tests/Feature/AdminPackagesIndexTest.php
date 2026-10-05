<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Package;
use App\Models\PackageTier;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPackagesIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_admin_packages_index_page(): void
    {
        $category = Category::create([
            'title'    => 'Birthdays',
            'slug'     => 'birthdays',
            'bg_color' => '#F6CFB2',
            'active'   => true,
        ]);

        $subcat = Subcategory::create([
            'category_id' => $category->id,
            'name'        => 'Setup Themes',
            'slug'        => 'setup-themes',
            'is_active'   => true,
        ]);

        $package = Package::create([
            'category_id' => $category->id,
            'title'       => 'Luxury Birthday Party',
            'slug'        => 'luxury-birthday-party',
            'tag'         => 'decor',
            'active'      => true,
        ]);

        $package->subcategories()->attach($subcat->id);

        PackageTier::create([
            'package_id' => $package->id,
            'name'       => 'Standard Tier',
            'slug'       => 'standard-tier',
            'price'      => 4999,
            'active'     => true,
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.packages'));

        $response->assertStatus(200);
        $response->assertSee('Event Packages');
        $response->assertSee('Luxury Birthday Party');
        $response->assertSee('Birthdays');
        $response->assertSee('/events/luxury-birthday-party');
    }

    public function test_can_toggle_package_status_via_ajax(): void
    {
        $category = Category::create(['title' => 'House Party', 'slug' => 'house-party']);
        $package = Package::create([
            'category_id' => $category->id,
            'title'       => 'DJ Sound Rig',
            'slug'        => 'dj-sound-rig',
            'active'      => true,
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.packages.toggle-status', $package->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'active'  => false,
        ]);

        $this->assertFalse($package->fresh()->active);
    }

    public function test_can_destroy_database_package(): void
    {
        $category = Category::create(['title' => 'Weddings', 'slug' => 'weddings']);
        $package = Package::create([
            'category_id' => $category->id,
            'title'       => 'Traditional Mandap',
            'slug'        => 'traditional-mandap',
            'active'      => true,
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->post(route('admin.packages.destroy', $package->id));

        $response->assertRedirect(route('admin.packages'));
        $this->assertDatabaseMissing('packages', ['id' => $package->id]);
    }

    public function test_can_render_package_create_form(): void
    {
        $category = Category::create([
            'title'    => 'Anniversary',
            'slug'     => 'anniversary',
            'active'   => true,
        ]);

        Subcategory::create([
            'category_id' => $category->id,
            'name'        => 'Candlelight Dinner',
            'slug'        => 'candlelight-dinner',
            'is_active'   => true,
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.packages.create'));

        $response->assertStatus(200);
        $response->assertSee('Create Package');
        $response->assertSee('Anniversary');
        $response->assertSee('Candlelight Dinner');
    }

    public function test_can_render_package_edit_form_with_preselected_subcategories(): void
    {
        $category = Category::create([
            'title'    => 'Proposals',
            'slug'     => 'proposals',
            'active'   => true,
        ]);

        $subcat = Subcategory::create([
            'category_id' => $category->id,
            'name'        => 'Rooftop Cabana',
            'slug'        => 'rooftop-cabana',
            'is_active'   => true,
        ]);

        $package = Package::create([
            'category_id' => $category->id,
            'title'       => 'Marry Me Setup',
            'slug'        => 'marry-me-setup',
            'price'       => 8999,
            'active'      => true,
        ]);

        $package->subcategories()->attach($subcat->id);

        $response = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.packages.edit', $package->id));

        $response->assertStatus(200);
        $response->assertSee('Edit Package');
        $response->assertSee('Marry Me Setup');
        $response->assertSee('Rooftop Cabana');
    }

    public function test_can_save_package_with_subcategories_and_tiers(): void
    {
        $category = Category::create([
            'title'    => 'Baby Shower',
            'slug'     => 'baby-shower',
            'active'   => true,
        ]);

        $subcat1 = Subcategory::create([
            'category_id' => $category->id,
            'name'        => 'Pastel Balloon Ring',
            'slug'        => 'pastel-balloon-ring',
            'is_active'   => true,
        ]);

        $subcat2 = Subcategory::create([
            'category_id' => $category->id,
            'name'        => 'Teddy Bear Theme',
            'slug'        => 'teddy-bear-theme',
            'is_active'   => true,
        ]);

        $payload = [
            'title'          => 'Cloud 9 Baby Shower',
            'category_id'    => $category->id,
            'slug'           => 'cloud-9-baby-shower',
            'badge'          => 'TRENDING',
            'tag'            => 'decor',
            'price'          => 7499,
            'original_price' => 9999,
            'description'    => 'Dreamy pastel clouds and customized stage backdrop.',
            'active'         => 1,
            'subcategories'  => [$subcat1->id, $subcat2->id],
            'tiers'          => [
                [
                    'name'        => 'Basic Cloud Setup',
                    'price'       => 7499,
                    'inclusions'  => "Pastel Balloon Ring\nCustom Welcome Easel\nLED Spotlights",
                ],
                [
                    'name'        => 'Luxury Cloud Suite',
                    'price'       => 12499,
                    'inclusions'  => "Full Stage Backing\nTeddy Props\nFog Machine Entrance",
                ],
            ],
        ];

        $response = $this->withSession(['admin_logged_in' => true])
            ->post(route('admin.packages.save'), $payload);

        $response->assertRedirect(route('admin.packages'));

        $package = Package::where('slug', 'cloud-9-baby-shower')->first();
        $this->assertNotNull($package);
        $this->assertEquals($category->id, $package->category_id);
        $this->assertEquals('TRENDING', $package->badge);
        $this->assertCount(2, $package->subcategories);
        $this->assertTrue($package->subcategories->contains($subcat1->id));
        $this->assertTrue($package->subcategories->contains($subcat2->id));
        $this->assertCount(2, $package->tiers);
    }

    public function test_accessing_dashboard_tab_packages_redirects_to_packages_index(): void
    {
        $response = $this->withSession(['admin_logged_in' => true])
            ->get('/admin/dashboard?tab=packages');

        $response->assertRedirect(route('admin.packages'));
    }

    public function test_all_thirty_three_celebration_packages_seeded_into_database(): void
    {
        $this->seed(\Database\Seeders\CategorySubcategorySeeder::class);
        $this->seed(\Database\Seeders\CelebrationPackagesSeeder::class);

        $this->assertEquals(33, Package::count());
        $this->assertEquals(66, PackageTier::count());

        // Check 1st Birthday Wonderland
        $wonderland = Package::where('slug', '1st-birthday-wonderland')->with(['category', 'subcategories', 'tiers'])->first();
        $this->assertNotNull($wonderland);
        $this->assertEquals('Birthdays', $wonderland->category->title);
        $this->assertNotEmpty($wonderland->subcategories);
        $this->assertCount(2, $wonderland->tiers);
        $this->assertEquals(4999, (int)$wonderland->price);

        // Check Admin Packages index lists them
        $response = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.packages'));

        $response->assertStatus(200);
        $response->assertSee('33 Packages');
        $response->assertSee('1st Birthday Wonderland');
        $response->assertSee('Kids Jungle Safari Theme');
        $response->assertSee('Pastel Balloon Arch &amp; Teddy Bear', false);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\User;
use Database\Seeders\FaqSeeder;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CentralizedFaqIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Find or create admin user
        $this->admin = User::firstOrCreate(
            ['email' => 'faqtestadmin@artizen.com'],
            [
                'name' => 'FAQ Test Admin',
                'password' => bcrypt('password123'),
                'is_admin' => true,
            ]
        );
    }

    /**
     * Test 1: Verify initial seeding has 16 FAQs with exactly 6 on homepage.
     */
    public function test_initial_seeding_has_six_homepage_faqs(): void
    {
        $this->seed(FaqSeeder::class);

        $totalActive = Faq::active()->count();
        $homepageCount = Faq::active()->forHomepage()->count();

        $this->assertEquals(16, $totalActive);
        $this->assertEquals(6, $homepageCount);

        // Verify public pages load
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('How do I book an event package on Artizen?');

        $faqResponse = $this->get('/faq');
        $faqResponse->assertStatus(200);
        $faqResponse->assertSee('How fast is setup completed in Indore?');
    }

    /**
     * Test 2: Admin can create a new FAQ and control homepage visibility.
     */
    public function test_admin_can_create_faq_with_homepage_visibility(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/faqs/save', [
            'q'                => 'Can I request bespoke lighting?',
            'a'                => 'Yes, Artizen provides custom fairy light and neon installations.',
            'category'         => 'customization',
            'sort_order'       => 1,
            'is_active'        => 1,
            'show_on_homepage' => 1,
            'faq_form_submitted' => 1,
        ]);

        $response->assertRedirect();

        $created = Faq::where('question', 'Can I request bespoke lighting?')->first();
        $this->assertNotNull($created);
        $this->assertTrue($created->is_active);
        $this->assertTrue($created->show_on_homepage);
        $this->assertEquals(1, $created->sort_order);

        // Should appear on homepage and faq page
        $homeResponse = $this->get('/');
        $homeResponse->assertSee('Can I request bespoke lighting?');

        $faqResponse = $this->get('/faq');
        $faqResponse->assertSee('Can I request bespoke lighting?');

        // Cleanup
        $created->delete();
    }

    /**
     * Test 3: Disabling homepage visibility hides FAQ from home but keeps on /faq.
     */
    public function test_disabling_homepage_visibility_keeps_faq_on_dedicated_page(): void
    {
        $faq = Faq::create([
            'question'         => 'Is a sound check performed before the party?',
            'answer'           => 'Yes, our technicians conduct a full sound and decibel test.',
            'category'         => 'setup',
            'sort_order'       => 20,
            'is_active'        => true,
            'show_on_homepage' => false,
        ]);

        $homeResponse = $this->get('/');
        $homeResponse->assertDontSee('Is a sound check performed before the party?');

        $faqResponse = $this->get('/faq');
        $faqResponse->assertSee('Is a sound check performed before the party?');

        $faq->delete();
    }

    /**
     * Test 4: Deactivating FAQ hides it from BOTH homepage and /faq page.
     */
    public function test_deactivating_faq_hides_from_both_pages(): void
    {
        $faq = Faq::create([
            'question'         => 'Temporary unconfirmed policy question?',
            'answer'           => 'This is an inactive answer draft.',
            'category'         => 'general',
            'sort_order'       => 21,
            'is_active'        => false,
            'show_on_homepage' => true, // Notice: show_on_homepage is true, but is_active is false!
        ]);

        // Must NOT appear on homepage even though show_on_homepage is true
        $homeResponse = $this->get('/');
        $homeResponse->assertDontSee('Temporary unconfirmed policy question?');

        // Must NOT appear on /faq page
        $faqResponse = $this->get('/faq');
        $faqResponse->assertDontSee('Temporary unconfirmed policy question?');

        $faq->delete();
    }

    /**
     * Test 5: Admin can delete FAQ.
     */
    public function test_admin_can_delete_faq(): void
    {
        $faq = Faq::create([
            'question'         => 'FAQ To Be Deleted?',
            'answer'           => 'Answer to be deleted.',
            'category'         => 'general',
            'sort_order'       => 99,
            'is_active'        => true,
            'show_on_homepage' => false,
        ]);

        $delResponse = $this->actingAs($this->admin)->post("/admin/faqs/delete/{$faq->id}");
        $delResponse->assertRedirect();

        $this->assertNull(Faq::find($faq->id));
    }

    /**
     * Test 6: Running FaqSeeder multiple times is idempotent and prevents duplicates.
     */
    public function test_seeder_is_idempotent(): void
    {
        $this->seed(FaqSeeder::class);
        $countAfterFirst = Faq::count();

        $this->seed(FaqSeeder::class);
        $countAfterSecond = Faq::count();

        $this->assertEquals($countAfterFirst, $countAfterSecond);
    }

    /**
     * Test 7: Verify separate button logic:
     * - Inactive => hidden everywhere (neither homepage nor FAQ page)
     * - Active & Homepage OFF => shown only on /faq, NOT on homepage
     * - Active & Homepage ON => shown on both homepage and /faq
     */
    public function test_admin_separate_buttons_active_and_homepage_toggles(): void
    {
        $faq = Faq::create([
            'question'         => 'Button Toggle Behavior Test Question?',
            'answer'           => 'Verification of separate active and home toggles.',
            'category'         => 'policy',
            'sort_order'       => 50,
            'is_active'        => true,
            'show_on_homepage' => false,
        ]);

        // State 1: Active + Homepage OFF -> Visible on /faq, NOT on home
        $homeRes = $this->get('/');
        $homeRes->assertDontSee('Button Toggle Behavior Test Question?');
        $faqRes = $this->get('/faq');
        $faqRes->assertSee('Button Toggle Behavior Test Question?');

        // State 2: Toggle Homepage ON via admin JSON request
        $toggleHomeRes = $this->actingAs($this->admin)->postJson('/admin/faqs/save', [
            'id'               => $faq->id,
            'q'                => $faq->question,
            'a'                => $faq->answer,
            'category'         => $faq->category,
            'is_active'        => '1',
            'show_on_homepage' => '1',
        ]);
        $toggleHomeRes->assertStatus(200)->assertJson(['success' => true]);

        $this->assertTrue((bool)Faq::find($faq->id)->show_on_homepage);
        $this->get('/')->assertSee('Button Toggle Behavior Test Question?');
        $this->get('/faq')->assertSee('Button Toggle Behavior Test Question?');

        // State 3: Toggle Inactive via admin JSON request (is_active = 0)
        $toggleInactiveRes = $this->actingAs($this->admin)->postJson('/admin/faqs/save', [
            'id'               => $faq->id,
            'q'                => $faq->question,
            'a'                => $faq->answer,
            'category'         => $faq->category,
            'is_active'        => '0',
            'show_on_homepage' => '1',
        ]);
        $toggleInactiveRes->assertStatus(200)->assertJson(['success' => true]);

        $this->assertFalse((bool)Faq::find($faq->id)->is_active);
        // Inactive must NOT appear on homepage
        $this->get('/')->assertDontSee('Button Toggle Behavior Test Question?');
        // Inactive must NOT appear on /faq
        $this->get('/faq')->assertDontSee('Button Toggle Behavior Test Question?');

        $faq->delete();
    }
}

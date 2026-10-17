<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\User;
use App\Services\JsonStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class Phase3CustomerBookingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected string $originalBookingsJsonRaw = '';

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Backup bookings.json to preserve disk state
        $bookingsPath = storage_path('app/bookings.json');
        if (File::exists($bookingsPath)) {
            $this->originalBookingsJsonRaw = File::get($bookingsPath);
        }

        // 2. Create authenticated admin in testing database
        $this->admin = new User([
            'name'     => 'Artizen Admin',
            'email'    => 'admin@artizen.com',
            'password' => bcrypt('password123'),
        ]);
        $this->admin->is_admin = true;
        $this->admin->save();
    }

    protected function tearDown(): void
    {
        // Restore disk bookings.json
        $bookingsPath = storage_path('app/bookings.json');
        if (!empty($this->originalBookingsJsonRaw)) {
            File::put($bookingsPath, $this->originalBookingsJsonRaw);
        }

        parent::tearDown();
    }

    protected function actingAsAdmin()
    {
        return $this->actingAs($this->admin)
            ->withSession([
                'admin_logged_in' => true,
                'admin_email'     => $this->admin->email,
            ]);
    }

    /**
     * 1. Test real customer records are loaded from MySQL without fabricated emails.
     */
    public function test_admin_customers_page_loads_real_mysql_customers_without_fabricated_emails(): void
    {
        // Create real customer with actual verified email
        $customerWithEmail = Customer::create([
            'name'     => 'Vikramaditya Solanki',
            'mobile'   => '9826011111',
            'whatsapp' => '9826011111',
            'email'    => 'vikram.solanki@artizen.com',
            'address'  => '101 Silver Estate',
            'area'     => 'Vijay Nagar',
            'city'     => 'Indore',
            'state'    => 'Madhya Pradesh',
            'pincode'  => '452010',
        ]);

        // Create real customer WITHOUT an email (null/empty)
        $customerNoEmail = Customer::create([
            'name'     => 'Harshita Verma',
            'mobile'   => '9826022222',
            'whatsapp' => '9826022222',
            'email'    => null,
            'address'  => '402 Apollo DB City',
            'area'     => 'Nipania',
            'city'     => 'Indore',
            'state'    => 'Madhya Pradesh',
            'pincode'  => '452016',
        ]);

        $response = $this->actingAsAdmin()->get(route('admin.customers'));
        $response->assertStatus(200);

        // Real verified email must be rendered
        $response->assertSee('vikram.solanki@artizen.com');

        // Customer with no email must render "Not provided", never a fabricated email like harshitaverma@gmail.com
        $response->assertSee('Harshita Verma');
        $response->assertSee('Not provided');
        $response->assertDontSee('harshitaverma@gmail.com');
        $response->assertDontSee('harshita.verma@gmail.com');
    }

    /**
     * 2. Test customer detail view returns actual profile fields and linked booking history.
     */
    public function test_customer_detail_shows_profile_fields_and_linked_booking_history(): void
    {
        $customer = Customer::create([
            'name'     => 'Rajesh Agrawal',
            'mobile'   => '9826033333',
            'whatsapp' => '9826033333',
            'email'    => 'rajesh.agrawal@example.com',
            'address'  => '55 Saket Nagar',
            'area'     => 'Saket',
            'city'     => 'Indore',
            'state'    => 'Madhya Pradesh',
            'pincode'  => '452018',
        ]);

        // Create 2 linked bookings for Rajesh
        $booking1 = Booking::create([
            'id'                => 'BK-RAJESH-1',
            'customer_id'       => $customer->id,
            'customer_name'     => $customer->name,
            'customer_mobile'   => $customer->mobile,
            'customer_whatsapp' => $customer->whatsapp,
            'customer_email'    => $customer->email,
            'package_name'      => 'Grand Stage Wedding Setup',
            'category_name'     => 'Weddings & Sangeet',
            'tier_name'         => 'Royal Tier',
            'event_date'        => '2026-11-20',
            'event_time'        => '19:00',
            'guest_count'       => 200,
            'address'           => '55 Saket Nagar, Indore',
            'area'              => 'Saket',
            'city'              => 'Indore',
            'state'             => 'Madhya Pradesh',
            'pincode'           => '452018',
            'package_price'     => 15000.00,
            'surcharge_amount'  => 0.00,
            'total_amount'      => 15000.00,
            'status'            => 'Confirmed',
        ]);

        $booking2 = Booking::create([
            'id'                => 'BK-RAJESH-2',
            'customer_id'       => $customer->id,
            'customer_name'     => $customer->name,
            'customer_mobile'   => $customer->mobile,
            'customer_whatsapp' => $customer->whatsapp,
            'customer_email'    => $customer->email,
            'package_name'      => 'High-Bass DJ & Sound Rig',
            'category_name'     => 'DJ & Acoustic',
            'tier_name'         => 'Club Rig',
            'event_date'        => '2026-11-21',
            'event_time'        => '20:00',
            'guest_count'       => 150,
            'address'           => '55 Saket Nagar, Indore',
            'area'              => 'Saket',
            'city'              => 'Indore',
            'state'             => 'Madhya Pradesh',
            'pincode'           => '452018',
            'package_price'     => 8500.00,
            'surcharge_amount'  => 0.00,
            'total_amount'      => 8500.00,
            'status'            => 'Pending',
        ]);

        // Request detail via JSON API
        $responseJson = $this->actingAsAdmin()->getJson(route('admin.customers.show', $customer->id));
        $responseJson->assertStatus(200);
        $responseJson->assertJsonPath('customer.name', 'Rajesh Agrawal');
        $responseJson->assertJsonPath('customer.mobile', '9826033333');
        $responseJson->assertJsonPath('customer.city', 'Indore');
        $responseJson->assertJsonCount(2, 'customer.bookings');

        // Request detail page
        $responsePage = $this->actingAsAdmin()->get(route('admin.customers', ['id' => $customer->id]));
        $responsePage->assertStatus(200);
        $responsePage->assertSee('Rajesh Agrawal');
        $responsePage->assertSee('BK-RAJESH-1');
        $responsePage->assertSee('Grand Stage Wedding Setup');
        $responsePage->assertSee('BK-RAJESH-2');
    }

    /**
     * 3. Test admin booking list shows correct linked customer information.
     */
    public function test_admin_booking_list_shows_correct_linked_customer_information(): void
    {
        $customer = Customer::create([
            'name'     => 'Ananya Deshmukh',
            'mobile'   => '9826044444',
            'whatsapp' => '9826044444',
            'email'    => 'ananya.d@artizen.com',
            'address'  => 'Old Palasia',
            'area'     => 'Palasia',
            'city'     => 'Indore',
            'state'    => 'Madhya Pradesh',
            'pincode'  => '452001',
        ]);

        $booking = Booking::create([
            'id'                => 'BK-ANANYA-10',
            'customer_id'       => $customer->id,
            'customer_name'     => $customer->name,
            'customer_mobile'   => $customer->mobile,
            'customer_whatsapp' => $customer->whatsapp,
            'customer_email'    => $customer->email,
            'package_name'      => 'Pastel Balloon Theme Canopy',
            'category_name'     => 'Birthdays',
            'tier_name'         => 'Premium Decor',
            'event_date'        => '2026-10-25',
            'event_time'        => '17:00',
            'guest_count'       => 40,
            'address'           => 'Old Palasia, Indore',
            'area'              => 'Palasia',
            'city'              => 'Indore',
            'state'             => 'Madhya Pradesh',
            'pincode'           => '452001',
            'package_price'     => 6999.00,
            'surcharge_amount'  => 0.00,
            'total_amount'      => 6999.00,
            'status'            => 'Pending',
        ]);

        // 1. Verify JsonStorageService maps linked customer profile
        $storageBookings = JsonStorageService::read('bookings.json', []);
        $found = collect($storageBookings)->firstWhere('id', 'BK-ANANYA-10');
        $this->assertNotNull($found, "Booking must be readable via JsonStorageService");
        $this->assertEquals($customer->id, $found['customer_id']);
        $this->assertEquals('Ananya Deshmukh', $found['name']);
        $this->assertEquals('ananya.d@artizen.com', $found['email']);
        $this->assertNotNull($found['customer_profile']);
        $this->assertEquals($customer->id, $found['customer_profile']['id']);

        // 2. Verify admin bookings page renders booking with linked customer information
        $response = $this->actingAsAdmin()->get(route('admin.bookings'));
        $response->assertStatus(200);
        $response->assertSee('BK-ANANYA-10');
        $response->assertSee('Ananya Deshmukh');
        $response->assertSee('9826044444');
        $response->assertSee('Pastel Balloon Theme Canopy');
    }

    /**
     * 4. Test Contacted status validation succeeds and updates booking status in DB & JSON.
     */
    public function test_contacted_booking_status_validation_succeeds(): void
    {
        $booking = Booking::create([
            'id'                => 'BK-STATUS-TEST',
            'customer_name'     => 'Kavita Joshi',
            'customer_mobile'   => '9826055555',
            'customer_whatsapp' => '9826055555',
            'customer_email'    => 'kavita@artizen.com',
            'package_name'      => 'Acoustic Guitar Evening',
            'category_name'     => 'DJ & Acoustic',
            'tier_name'         => 'Solo Acoustic',
            'event_date'        => '2026-10-30',
            'event_time'        => '19:00',
            'guest_count'       => 20,
            'address'           => 'Annapurna Road, Indore',
            'area'              => 'Annapurna',
            'city'              => 'Indore',
            'state'             => 'Madhya Pradesh',
            'pincode'           => '452009',
            'package_price'     => 4500.00,
            'surcharge_amount'  => 0.00,
            'total_amount'      => 4500.00,
            'status'            => 'Pending',
        ]);

        // Attempt updating to "Contacted"
        $response = $this->actingAsAdmin()->postJson(route('admin.bookings.update-status'), [
            'id'     => 'BK-STATUS-TEST',
            'status' => 'Contacted',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify status in MySQL database
        $updatedBooking = Booking::find('BK-STATUS-TEST');
        $this->assertEquals('Contacted', $updatedBooking->status);

        // Verify invalid status is rejected by validation
        $badResponse = $this->actingAsAdmin()->postJson(route('admin.bookings.update-status'), [
            'id'     => 'BK-STATUS-TEST',
            'status' => 'InvalidStatusX',
        ]);
        $badResponse->assertStatus(422);
        $badResponse->assertJsonValidationErrors(['status']);
    }

    /**
     * 5. Test admin dashboard includes real customer count and quick navigation links.
     */
    public function test_admin_dashboard_includes_customer_count_and_quick_links(): void
    {
        Customer::create([
            'name'     => 'Pooja Tiwari',
            'mobile'   => '9826066666',
            'whatsapp' => '9826066666',
            'email'    => 'pooja@artizen.com',
            'city'     => 'Indore',
        ]);

        $response = $this->actingAsAdmin()->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Assert customer overview metric card
        $response->assertSee('Verified Customers');
        $response->assertSee(route('admin.customers'));
        $response->assertSee(route('admin.bookings'));
    }

    /**
     * 6. Test customer and booking management endpoints require admin authorization.
     */
    public function test_customer_and_booking_management_requires_admin_authorization(): void
    {
        // 1. Unauthenticated guest accessing customers directory
        $guestResponse = $this->get(route('admin.customers'));
        $guestResponse->assertRedirect(route('login', ['redirect' => url('/admin/customers')]));

        // 2. Unauthenticated guest updating booking status
        $guestUpdate = $this->postJson(route('admin.bookings.update-status'), [
            'id'     => 'BK-ANY',
            'status' => 'Confirmed',
        ]);
        $guestUpdate->assertStatus(401);

        // 3. Authenticated customer (non-admin) blocked from admin customer directory
        $regularUser = new User([
            'name'     => 'Regular Customer',
            'email'    => 'customer@artizen.com',
            'password' => bcrypt('password123'),
        ]);
        $regularUser->is_admin = false;
        $regularUser->save();

        $nonAdminResponse = $this->actingAs($regularUser)->get(route('admin.customers'));
        $nonAdminResponse->assertRedirect(route('login', ['redirect' => url('/admin/customers')]));
    }
}

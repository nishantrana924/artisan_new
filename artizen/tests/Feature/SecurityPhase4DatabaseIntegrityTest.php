<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\Package;
use App\Models\User;
use App\Services\JsonStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityPhase4DatabaseIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customerUser;
    protected Package $package;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('booking_submit');
        RateLimiter::clear('booking_track');
        RateLimiter::clear('contact_enquiry');
        RateLimiter::clear('admin_booking_status');
        Cache::flush();

        $this->admin = User::create([
            'name' => 'Artizen Audit Admin',
            'email' => 'admin@artizen.com',
            'password' => Hash::make('AdminPass123!'),
        ]);
        $this->admin->is_admin = true;
        $this->admin->save();

        $this->customerUser = User::create([
            'name' => 'Pooja Sharma',
            'email' => 'pooja.customer@example.com',
            'password' => Hash::make('CustomerPass123!'),
            'is_admin' => false,
        ]);

        $category = Category::create([
            'title' => 'Celebration Decor',
            'slug' => 'celebration-decor',
            'active' => true,
        ]);

        $this->package = Package::create([
            'title' => 'Royal Anniversary Cabana',
            'slug' => 'royal-anniversary-cabana',
            'category_id' => $category->id,
            'active' => true,
        ]);
    }

    /**
     * Test booking creation persists the correct booking and customer, linking user_id.
     */
    public function test_booking_creation_persists_correct_booking_and_customer_with_user_id(): void
    {
        $payload = [
            'name' => 'Pooja Sharma',
            'mobile' => '9826012345',
            'whatsapp' => '9826012345',
            'email' => 'pooja.customer@example.com',
            'package' => 'Royal Anniversary Cabana',
            'category' => 'Celebration Decor',
            'tier' => 'Standard',
            'event_date' => date('Y-m-d', strtotime('+7 days')),
            'event_time' => '19:00',
            'guest_count' => 20,
            'address' => 'Scheme 54, Vijay Nagar',
            'landmark' => 'Near Apollo Hospital',
            'area' => 'Vijay Nagar',
            'city' => 'Indore',
            'state' => 'Madhya Pradesh',
            'pincode' => '452010',
            'notes' => 'Romantic lighting requested',
            'price' => 5999,
        ];

        $response = $this->actingAs($this->customerUser)
            ->postJson('/booking/save', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $bookingId = $response->json('booking_id');
        $this->assertNotNull($bookingId);
        $this->assertStringStartsWith('BK-', $bookingId);

        // Verify Database Booking record
        $dbBooking = Booking::find($bookingId);
        $this->assertNotNull($dbBooking, 'Booking record must exist in database');
        $this->assertEquals($this->customerUser->id, $dbBooking->user_id, 'user_id must match authenticated customer');
        $this->assertEquals('9826012345', $dbBooking->customer_mobile);
        $this->assertEquals('Royal Anniversary Cabana', $dbBooking->package_name);
        $this->assertEquals($this->package->id, $dbBooking->package_id);
        $this->assertEquals('Pending', $dbBooking->status);

        // Verify Database Customer record
        $customer = Customer::where('mobile', '9826012345')->first();
        $this->assertNotNull($customer, 'Customer record must exist in database');
        $this->assertEquals('Pooja Sharma', $customer->name);
        $this->assertEquals(1, $customer->total_bookings);
        $this->assertEquals($dbBooking->customer_id, $customer->id);
    }

    /**
     * Test existing bookings are not lost or overwritten when a new booking is created.
     */
    public function test_existing_bookings_are_not_lost_when_new_booking_is_created(): void
    {
        // 1. Create first booking
        $payload1 = [
            'name' => 'Customer One',
            'mobile' => '9111111111',
            'whatsapp' => '9111111111',
            'package' => 'Royal Anniversary Cabana',
            'event_date' => date('Y-m-d', strtotime('+3 days')),
            'event_time' => '18:00',
            'guest_count' => 15,
            'address' => 'Address One',
            'area' => 'Vijay Nagar',
            'city' => 'Indore',
            'pincode' => '452010',
            'price' => 4999,
        ];
        $res1 = $this->actingAs($this->customerUser)->postJson('/booking/save', $payload1);
        $res1->assertStatus(200);
        $id1 = $res1->json('booking_id');

        RateLimiter::clear('booking_submit');

        // 2. Create second booking
        $payload2 = [
            'name' => 'Customer Two',
            'mobile' => '9222222222',
            'whatsapp' => '9222222222',
            'package' => 'Royal Anniversary Cabana',
            'event_date' => date('Y-m-d', strtotime('+4 days')),
            'event_time' => '20:00',
            'guest_count' => 25,
            'address' => 'Address Two',
            'area' => 'Palasia',
            'city' => 'Indore',
            'pincode' => '452001',
            'price' => 6999,
        ];
        $res2 = $this->actingAs($this->customerUser)->postJson('/booking/save', $payload2);
        $res2->assertStatus(200);
        $id2 = $res2->json('booking_id');

        $this->assertNotEquals($id1, $id2, 'Booking IDs must be distinct');

        // Both bookings must still exist in the database with their respective customer details
        $b1 = Booking::find($id1);
        $b2 = Booking::find($id2);

        $this->assertNotNull($b1, 'First booking must not be lost');
        $this->assertNotNull($b2, 'Second booking must not be lost');
        $this->assertEquals('Customer One', $b1->customer_name);
        $this->assertEquals('Customer Two', $b2->customer_name);
    }

    /**
     * Test booking IDs remain unique and collision-resistant across successive creations.
     */
    public function test_booking_ids_remain_unique(): void
    {
        $generatedIds = [];

        for ($i = 0; $i < 5; $i++) {
            RateLimiter::clear('booking_submit');
            $res = $this->actingAs($this->customerUser)->postJson('/booking/save', [
                'name' => 'Unique Test ' . $i,
                'mobile' => '980000000' . $i,
                'whatsapp' => '980000000' . $i,
                'package' => 'Royal Anniversary Cabana',
                'event_date' => date('Y-m-d', strtotime('+' . ($i + 1) . ' days')),
                'event_time' => '18:30',
                'guest_count' => 10,
                'address' => 'Indore Location',
                'area' => 'Vijay Nagar',
                'city' => 'Indore',
                'pincode' => '452010',
                'price' => 5000,
            ]);

            $res->assertStatus(200);
            $id = $res->json('booking_id');
            $this->assertStringStartsWith('BK-', $id);
            $this->assertFalse(in_array($id, $generatedIds), "Duplicate ID generated: {$id}");
            $generatedIds[] = $id;
        }

        $this->assertCount(5, array_unique($generatedIds));
    }

    /**
     * Test customer mobile uniqueness constraint ensures concurrent bookings from same customer reuse single record.
     */
    public function test_customer_mobile_reuses_single_customer_record(): void
    {
        $phone = '9893099999';

        // First booking by customer
        $this->actingAs($this->customerUser)->postJson('/booking/save', [
            'name' => 'Rahul Verma',
            'mobile' => $phone,
            'whatsapp' => $phone,
            'package' => 'Royal Anniversary Cabana',
            'event_date' => date('Y-m-d', strtotime('+5 days')),
            'event_time' => '18:00',
            'guest_count' => 15,
            'address' => 'Vijay Nagar',
            'area' => 'Vijay Nagar',
            'city' => 'Indore',
            'pincode' => '452010',
            'price' => 4000,
        ])->assertStatus(200);

        RateLimiter::clear('booking_submit');

        // Second booking by same customer with same phone
        $this->actingAs($this->customerUser)->postJson('/booking/save', [
            'name' => 'Rahul Verma',
            'mobile' => $phone,
            'whatsapp' => $phone,
            'package' => 'Royal Anniversary Cabana',
            'event_date' => date('Y-m-d', strtotime('+12 days')),
            'event_time' => '19:00',
            'guest_count' => 30,
            'address' => 'Vijay Nagar',
            'area' => 'Vijay Nagar',
            'city' => 'Indore',
            'pincode' => '452010',
            'price' => 6000,
        ])->assertStatus(200);

        // Verify only 1 Customer record exists for this mobile
        $customers = Customer::where('mobile', $phone)->get();
        $this->assertCount(1, $customers, 'Multiple bookings for same phone must not create duplicate Customer rows');
        $this->assertEquals(2, $customers->first()->total_bookings);
    }

    /**
     * Test booking status update modifies only the target booking and preserves unrelated records.
     */
    public function test_booking_status_changes_preserve_unrelated_records(): void
    {
        $customer = Customer::create([
            'name' => 'Status Test Customer',
            'mobile' => '9131668100',
        ]);

        $booking1 = Booking::create([
            'id' => 'BK-TARGET01',
            'customer_id' => $customer->id,
            'customer_name' => 'Status Test Customer',
            'customer_mobile' => '9131668100',
            'package_name' => 'Royal Anniversary Cabana',
            'category_name' => 'Celebration Decor',
            'event_date' => date('Y-m-d'),
            'address' => 'Indore',
            'area' => 'Vijay Nagar',
            'total_amount' => 5000,
            'status' => 'Pending',
        ]);

        $booking2 = Booking::create([
            'id' => 'BK-UNTOUCH02',
            'customer_id' => $customer->id,
            'customer_name' => 'Status Test Customer',
            'customer_mobile' => '9131668100',
            'package_name' => 'Royal Anniversary Cabana',
            'category_name' => 'Celebration Decor',
            'event_date' => date('Y-m-d'),
            'address' => 'Indore',
            'area' => 'Vijay Nagar',
            'total_amount' => 7000,
            'status' => 'Pending',
        ]);

        // Admin updates status of BK-TARGET01 to Confirmed
        $response = $this->actingAs($this->admin)
            ->withSession([
                'admin_logged_in' => true,
                'admin_email'     => $this->admin->email,
            ])
            ->postJson('/admin/bookings/update-status', [
                'id' => 'BK-TARGET01',
                'status' => 'Confirmed',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'status' => 'Confirmed']);

        // Verify BK-TARGET01 updated
        $this->assertEquals('Confirmed', Booking::find('BK-TARGET01')->status);

        // Verify BK-UNTOUCH02 is completely unchanged
        $untouched = Booking::find('BK-UNTOUCH02');
        $this->assertNotNull($untouched);
        $this->assertEquals('Pending', $untouched->status, 'Unrelated booking must not be modified');
    }

    /**
     * Test database write failures in JsonStorageService are detected, logged, and return false.
     */
    public function test_database_write_failures_are_detected_and_logged_safely(): void
    {
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'Database sync failed') && ($context['filename'] ?? '') === 'bookings.json';
            });

        config(['artizen.storage_driver' => 'database']);

        // Foreign key violation: package_id = 999999999 does not exist
        $invalidBookings = [
            [
                'id' => 'BK-CRASH-TEST',
                'name' => 'Crash Test Customer',
                'mobile' => '9999999999',
                'package_id' => 999999999,
            ]
        ];

        $result = JsonStorageService::write('bookings.json', $invalidBookings);
        $this->assertFalse($result, 'JsonStorageService::write must return false when database sync fails');
    }

    /**
     * Test authenticated customer order history retrieves bookings linked by user_id.
     */
    public function test_authenticated_customer_booking_history_retrieves_orders_by_user_id(): void
    {
        $customer = Customer::create([
            'name' => 'Pooja Sharma',
            'mobile' => '9826012345',
        ]);

        $booking = Booking::create([
            'id' => 'BK-USERAUTH01',
            'user_id' => $this->customerUser->id,
            'customer_id' => $customer->id,
            'customer_name' => 'Pooja Sharma',
            'customer_mobile' => '9826012345',
            'customer_email' => 'pooja.customer@example.com',
            'package_name' => 'Royal Anniversary Cabana',
            'category_name' => 'Celebration Decor',
            'event_date' => date('Y-m-d'),
            'address' => 'Scheme 54, Vijay Nagar',
            'area' => 'Vijay Nagar',
            'total_amount' => 5999,
            'status' => 'Confirmed',
        ]);

        $response = $this->actingAs($this->customerUser)
            ->get('/orders');

        $response->assertStatus(200);
        $response->assertSee('BK-USERAUTH01');
        $response->assertSee('Royal Anniversary Cabana');
    }

    /**
     * Test contact enquiry creation persists with unique collision-resistant ID and creates DB record.
     */
    public function test_contact_enquiry_persists_with_collision_resistant_id_and_db_record(): void
    {
        $payload = [
            'name' => 'Vikas Jain',
            'phone' => '9893012345',
            'email' => 'vikas@example.com',
            'subject' => 'Corporate Event Setup',
            'message' => 'Need lighting and PA system for 100 people.'
        ];

        $response = $this->postJson('/contact/send', $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify enquiry in DB
        $dbEnquiry = Enquiry::where('phone', '9893012345')->first();
        $this->assertNotNull($dbEnquiry, 'Enquiry must be stored in database');
        $this->assertStringStartsWith('ENQ-', $dbEnquiry->id);
        $this->assertEquals('Corporate Event Setup', $dbEnquiry->subject);
    }

    /**
     * Test booking tracker queries database directly using reference ID and mobile number.
     */
    public function test_booking_tracker_queries_database_directly_with_id_and_phone(): void
    {
        $customer = Customer::create([
            'name' => 'Tracking Test Customer',
            'mobile' => '9826198765',
        ]);

        Booking::create([
            'id' => 'BK-TRK001',
            'customer_id' => $customer->id,
            'customer_name' => 'Tracking Test Customer',
            'customer_mobile' => '9826198765',
            'customer_whatsapp' => '9826198765',
            'package_name' => 'Royal Anniversary Cabana',
            'category_name' => 'Celebration Decor',
            'event_date' => date('Y-m-d'),
            'address' => 'Indore Location',
            'area' => 'Vijay Nagar',
            'total_amount' => 4500,
            'status' => 'Confirmed',
        ]);

        // Search with matching phone
        $res = $this->postJson('/track-booking/search', [
            'booking_id' => 'BK-TRK001',
            'mobile' => '9826198765',
        ]);

        $res->assertStatus(200);
        $res->assertJson(['success' => true]);
        $res->assertJsonPath('booking.name', 'Tracking Test Customer');
        $res->assertJsonPath('booking.status', 'Confirmed');

        // Search with non-matching phone fails lookup
        RateLimiter::clear('booking_track');
        $failRes = $this->postJson('/track-booking/search', [
            'booking_id' => 'BK-TRK001',
            'mobile' => '9999900000',
        ]);

        $failRes->assertStatus(200);
        $failRes->assertJson(['success' => false]);
    }
}

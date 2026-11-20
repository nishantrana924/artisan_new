<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBookingBulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_bulk_delete_multiple_bookings(): void
    {
        $customer = Customer::create([
            'name'   => 'Test Customer',
            'mobile' => '9876543210',
            'email'  => 'test.cust@example.com',
            'city'   => 'Indore',
        ]);

        $b1 = Booking::create([
            'id'              => 'BK-DEL01',
            'customer_id'     => $customer->id,
            'customer_name'   => 'Test Customer',
            'customer_mobile' => '9876543210',
            'package_name'    => 'Test Package 1',
            'category_name'   => 'Birthdays',
            'address'         => '123 Test St, Indore',
            'area'            => 'Vijay Nagar',
            'event_date'      => '2026-11-20',
            'total_amount'    => 5000,
            'status'          => 'Pending',
        ]);

        $b2 = Booking::create([
            'id'              => 'BK-DEL02',
            'customer_id'     => $customer->id,
            'customer_name'   => 'Test Customer',
            'customer_mobile' => '9876543210',
            'package_name'    => 'Test Package 2',
            'category_name'   => 'House Party',
            'address'         => '456 Test Ave, Indore',
            'area'            => 'Vijay Nagar',
            'event_date'      => '2026-11-21',
            'total_amount'    => 8000,
            'status'          => 'Pending',
        ]);

        $b3 = Booking::create([
            'id'              => 'BK-KEEP01',
            'customer_id'     => $customer->id,
            'customer_name'   => 'Keep Customer',
            'customer_mobile' => '9876543210',
            'package_name'    => 'Test Package 3',
            'category_name'   => 'Weddings',
            'address'         => '789 Keep Rd, Indore',
            'area'            => 'Vijay Nagar',
            'event_date'      => '2026-11-22',
            'total_amount'    => 12000,
            'status'          => 'Confirmed',
        ]);

        $this->assertDatabaseHas('bookings', ['id' => 'BK-DEL01']);
        $this->assertDatabaseHas('bookings', ['id' => 'BK-DEL02']);
        $this->assertDatabaseHas('bookings', ['id' => 'BK-KEEP01']);

        $response = $this->actingAsAdmin()
                         ->postJson(route('admin.bookings.bulk-delete'), [
                             'ids' => ['BK-DEL01', 'BK-DEL02']
                         ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'deleted_ids' => ['BK-DEL01', 'BK-DEL02'],
                 ]);

        $this->assertDatabaseMissing('bookings', ['id' => 'BK-DEL01']);
        $this->assertDatabaseMissing('bookings', ['id' => 'BK-DEL02']);
        $this->assertDatabaseHas('bookings', ['id' => 'BK-KEEP01']);
    }

    public function test_bulk_delete_validation_requires_ids_array(): void
    {
        $response = $this->actingAsAdmin()
                         ->postJson(route('admin.bookings.bulk-delete'), [
                             'ids' => []
                         ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['ids']);
    }

    public function test_can_delete_single_booking(): void
    {
        $b = Booking::create([
            'id'              => 'BK-SINGLE01',
            'customer_name'   => 'Single Delete Cust',
            'customer_mobile' => '9876543210',
            'package_name'    => 'Single Package',
            'category_name'   => 'Birthdays',
            'address'         => 'Single Address, Indore',
            'area'            => 'Vijay Nagar',
            'event_date'      => '2026-11-25',
            'total_amount'    => 3500,
            'status'          => 'Pending',
        ]);

        $this->assertDatabaseHas('bookings', ['id' => 'BK-SINGLE01']);

        $response = $this->actingAsAdmin()
                         ->deleteJson(route('admin.bookings.delete', ['id' => 'BK-SINGLE01']));

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseMissing('bookings', ['id' => 'BK-SINGLE01']);
    }
}

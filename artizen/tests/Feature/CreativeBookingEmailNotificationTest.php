<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\User;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\BookingReceivedNotification;
use App\Notifications\BookingCancelledNotification;
use App\Notifications\NewBookingAdminNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CreativeBookingEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function createCustomerUser(): User
    {
        return User::create([
            'name' => 'Pooja Sharma',
            'email' => 'pooja.sharma@example.com',
            'password' => bcrypt('Artizen@2026'),
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);
    }

    protected function createAdminUser(): User
    {
        $admin = new User([
            'name' => 'Artizen Admin',
            'email' => 'admin@artizen.com',
            'password' => bcrypt('Artizen@2026'),
            'email_verified_at' => now(),
        ]);
        $admin->is_admin = true;
        $admin->save();

        return $admin;
    }

    public function test_booking_received_email_renders_creative_and_polite_content(): void
    {
        $booking = [
            'id' => 'BK-CR8888',
            'name' => 'Aarav Mehta',
            'mobile' => '9826011111',
            'whatsapp' => '9826011111',
            'email' => 'aarav@example.com',
            'package' => 'Romantic Proposal Setup',
            'tier' => 'Luxury VIP Setup',
            'date' => '2026-12-25',
            'time' => '07:00 PM',
            'guest_count' => 2,
            'address' => 'Flat 402, Lotus Pride',
            'area' => 'Vijay Nagar',
            'city' => 'Indore',
            'total' => 12999,
            'status' => 'Pending'
        ];

        $notification = new BookingReceivedNotification($booking);
        $mail = $notification->toMail(new \Illuminate\Notifications\AnonymousNotifiable());

        $this->assertEquals("✨ Celebration Request Received! Ref: #BK-CR8888 • Artizen", $mail->subject);
        $this->assertEquals('emails.booking-received', $mail->view);

        $rendered = $mail->render();
        $this->assertStringContainsString('Aarav Mehta', $rendered);
        $this->assertStringContainsString('BK-CR8888', $rendered);
        $this->assertStringContainsString('Romantic Proposal Setup', $rendered);
        $this->assertStringContainsString('100% Offline Payment Guarantee', $rendered);
        $this->assertStringContainsString('Warm Greetings from Artizen', $rendered);
    }

    public function test_booking_confirmed_email_renders_joyful_and_polite_content(): void
    {
        $booking = [
            'id' => 'BK-CF9999',
            'name' => 'Sneha Patel',
            'mobile' => '9826022222',
            'whatsapp' => '9826022222',
            'email' => 'sneha@example.com',
            'package' => 'Grand Birthday Canopy',
            'tier' => 'Premium Setup',
            'date' => '2026-11-20',
            'time' => '06:00 PM',
            'guest_count' => 30,
            'address' => 'Bunglow 12, Scheme 54',
            'area' => 'Vijay Nagar',
            'city' => 'Indore',
            'total' => 15999,
            'status' => 'Confirmed'
        ];

        $notification = new BookingConfirmedNotification($booking);
        $mail = $notification->toMail(new \Illuminate\Notifications\AnonymousNotifiable());

        $this->assertEquals("🎉 Your Celebration is Confirmed! Ref: #BK-CF9999 • Artizen", $mail->subject);
        $this->assertEquals('emails.booking-confirmed', $mail->view);

        $rendered = $mail->render();
        $this->assertStringContainsString('Sneha Patel', $rendered);
        $this->assertStringContainsString('BK-CF9999', $rendered);
        $this->assertStringContainsString('Grand Birthday Canopy', $rendered);
        $this->assertStringContainsString('Your Celebration is Confirmed!', $rendered);
        $this->assertStringContainsString('Punctual Arrival', $rendered);
    }

    public function test_booking_cancelled_email_renders_respectful_content(): void
    {
        $booking = [
            'id' => 'BK-CN5555',
            'name' => 'Kunal Joshi',
            'package' => 'House Party Lounge',
        ];

        $notification = new BookingCancelledNotification($booking);
        $mail = $notification->toMail(new \Illuminate\Notifications\AnonymousNotifiable());

        $this->assertStringContainsString('BK-CN5555', $mail->subject);
        $this->assertEquals('emails.booking-cancelled', $mail->view);

        $rendered = $mail->render();
        $this->assertStringContainsString('Kunal Joshi', $rendered);
        $this->assertStringContainsString('Would you like to reschedule', $rendered);
    }

    public function test_admin_new_booking_email_renders_clean_summary(): void
    {
        $booking = [
            'id' => 'BK-AD1111',
            'name' => 'Meera Sen',
            'mobile' => '9826033333',
            'email' => 'meera@example.com',
            'package' => 'Anniversary Cabana',
            'date' => '2026-11-18',
            'time' => '08:00 PM',
            'total' => 8499,
        ];

        $notification = new NewBookingAdminNotification($booking);
        $mail = $notification->toMail(new \Illuminate\Notifications\AnonymousNotifiable());

        $this->assertStringContainsString('BK-AD1111', $mail->subject);
        $this->assertEquals('emails.new-booking-admin', $mail->view);

        $rendered = $mail->render();
        $this->assertStringContainsString('Meera Sen', $rendered);
        $this->assertStringContainsString('Admin Fast-Track Booking Alert', $rendered);
    }

    public function test_admin_marking_confirmed_triggers_email_to_customer(): void
    {
        Notification::fake();

        $admin = $this->createAdminUser();

        // Create booking with customer email
        $booking = Booking::create([
            'id' => 'BK-E2E101',
            'customer_name' => 'Rohan Varma',
            'customer_mobile' => '9131668156',
            'customer_whatsapp' => '9131668156',
            'customer_email' => 'rohan.varma@example.com',
            'package_name' => 'Neon Glow Party',
            'category_name' => 'Birthdays',
            'tier_name' => 'Standard Setup',
            'event_date' => date('Y-m-d', strtotime('+3 days')),
            'event_time' => '07:00 PM',
            'guest_count' => 20,
            'address' => 'Palasia Main Road',
            'area' => 'Old Palasia',
            'city' => 'Indore',
            'state' => 'Madhya Pradesh',
            'pincode' => '452001',
            'total_amount' => 7999,
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($admin)
            ->withSession([
                'admin_logged_in' => true,
                'admin_email' => $admin->email,
            ])
            ->postJson('/admin/bookings/update-status', [
                'id' => 'BK-E2E101',
                'status' => 'Confirmed',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'status' => 'Confirmed']);

        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable(),
            BookingConfirmedNotification::class,
            function ($notification, $channels, $notifiable) {
                return $notifiable->routes['mail'] === 'rohan.varma@example.com'
                    && $notification->booking['id'] === 'BK-E2E101'
                    && $notification->booking['name'] === 'Rohan Varma';
            }
        );
    }
}


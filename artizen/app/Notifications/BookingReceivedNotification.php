<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingReceivedNotification extends Notification
{
    use Queueable;

    public array $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $id = $this->booking['id'] ?? 'BK-XXXX';
        $name = $this->booking['name'] ?? 'Valued Customer';
        $package = $this->booking['package'] ?? 'Event Package';
        $date = $this->booking['date'] ?? 'Upcoming';
        $time = $this->booking['time'] ?? 'Evening';
        $total = number_format((int)($this->booking['total'] ?? 4999));
        $venue = ($this->booking['area'] ?? '') . ', ' . ($this->booking['city'] ?? 'Indore');

        return (new MailMessage)
            ->subject("Artizen Booking Request Received — {$id}")
            ->greeting("Hello {$name},")
            ->line("Thank you for choosing Artizen! Your booking request has been received and registered under Reference ID: {$id}.")
            ->line("--- Event Request Details ---")
            ->line("• Package: {$package}")
            ->line("• Event Date & Time: {$date} ({$time})")
            ->line("• Venue Location: {$venue}")
            ->line("• Estimated Total Amount: ₹{$total}")
            ->line("• Current Status: PENDING REVIEW")
            ->line("--- Important Note ---")
            ->line("Our Artizen event manager will review your date availability and contact you on WhatsApp/Phone shortly to confirm your booking and offline payment.")
            ->action('View Booking Details', route('booking.success', ['id' => $id]))
            ->line('Thank you for celebrating with Artizen Event Platform!');
    }
}

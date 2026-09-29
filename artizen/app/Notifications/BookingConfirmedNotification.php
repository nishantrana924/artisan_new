<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification
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
        $package = $this->booking['package'] ?? 'Event Setup';
        $date = $this->booking['date'] ?? 'Upcoming';
        $time = $this->booking['time'] ?? 'Evening';
        $total = number_format((int)($this->booking['total'] ?? 4999));
        $venue = ($this->booking['address'] ?? '') . ', ' . ($this->booking['area'] ?? '') . ', ' . ($this->booking['city'] ?? 'Indore');

        return (new MailMessage)
            ->subject("Artizen Booking Confirmed — {$id}")
            ->greeting("Great News, {$name}!")
            ->line("Your event booking request (Ref ID: {$id}) has been officially CONFIRMED by the Artizen Operations Team.")
            ->line("--- Confirmed Booking Details ---")
            ->line("• Booking Reference ID: {$id}")
            ->line("• Package: {$package}")
            ->line("• Event Date & Time: {$date} ({$time})")
            ->line("• Venue Location: {$venue}")
            ->line("• Total Booking Amount: ₹{$total}")
            ->line("• Payment Method: Offline (Post-Setup)")
            ->line("• Status: CONFIRMED")
            ->line("Our setup crew will reach your venue on the event date at the scheduled time.")
            ->action('View Confirmed Booking', route('booking.success', ['id' => $id]))
            ->line('Thank you for choosing Artizen Event Platform!');
    }
}

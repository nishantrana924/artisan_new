<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification
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
        $name = $this->booking['name'] ?? 'Customer';
        $package = $this->booking['package'] ?? 'Event Setup';

        return (new MailMessage)
            ->subject("Artizen Booking Request Cancelled — {$id}")
            ->greeting("Hello {$name},")
            ->line("Your booking request (Ref ID: {$id}) for {$package} has been CANCELLED by the Artizen team.")
            ->line("--- Booking Summary ---")
            ->line("• Booking Reference ID: {$id}")
            ->line("• Status: CANCELLED")
            ->line("If you have any questions or wish to reschedule for a different date, please connect with our team on WhatsApp (+91 9131668156) or email info@artizenevents.com.")
            ->action('Browse Other Packages', route('events.index'))
            ->line('Thank you for considering Artizen.');
    }
}

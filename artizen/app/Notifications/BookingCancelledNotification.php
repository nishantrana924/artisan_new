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
        $id = $this->booking['id'] ?? $this->booking['booking_id'] ?? 'BK-XXXX';

        return (new MailMessage)
            ->subject("Update on your Booking Request #{$id} • Artizen")
            ->view('emails.booking-cancelled', ['booking' => $this->booking]);
    }
}

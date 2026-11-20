<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingAdminNotification extends Notification
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

        return (new MailMessage)
            ->subject("⚡ New Booking Request: #{$id} ({$name}) • Project Brief & Invoice PDF")
            ->view('emails.new-booking-admin', ['booking' => $this->booking]);
    }
}

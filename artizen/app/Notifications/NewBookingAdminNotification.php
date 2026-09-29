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
        $phone = $this->booking['mobile'] ?? ($this->booking['whatsapp'] ?? 'N/A');
        $email = $this->booking['email'] ?? 'N/A';
        $package = $this->booking['package'] ?? 'Package';
        $date = $this->booking['date'] ?? 'N/A';
        $time = $this->booking['time'] ?? 'N/A';
        $guests = $this->booking['guest_count'] ?? 25;
        $total = number_format((int)($this->booking['total'] ?? 4999));
        $address = ($this->booking['address'] ?? '') . ', ' . ($this->booking['area'] ?? '') . ', ' . ($this->booking['city'] ?? 'Indore') . ' - ' . ($this->booking['pincode'] ?? '');

        return (new MailMessage)
            ->subject("New Booking Received — {$id} ({$name})")
            ->greeting("Hello Artizen Admin,")
            ->line("A new event booking request has been submitted on the Artizen portal.")
            ->line("--- Booking Summary ---")
            ->line("• Booking Reference ID: {$id}")
            ->line("• Customer Name: {$name}")
            ->line("• Mobile / WhatsApp: {$phone}")
            ->line("• Customer Email: {$email}")
            ->line("• Selected Package: {$package}")
            ->line("• Event Date & Time: {$date} ({$time})")
            ->line("• Guest Count: {$guests} Guests")
            ->line("• Full Address: {$address}")
            ->line("• Total Amount: ₹{$total}")
            ->line("• Status: PENDING REVIEW")
            ->action('Review Booking in Dashboard', route('admin.dashboard'))
            ->line('Please review date availability and contact the customer to confirm or adjust booking details.');
    }
}

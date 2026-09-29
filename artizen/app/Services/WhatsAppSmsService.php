<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppSmsService
{
    /**
     * Check if a third-party WhatsApp/SMS gateway provider is configured.
     */
    public static function isConfigured(): bool
    {
        $waKey = config('services.whatsapp.api_key');
        $waProvider = config('services.whatsapp.provider');
        $smsKey = config('services.sms.api_key');

        return !empty($waKey) || !empty($waProvider) || !empty($smsKey);
    }

    /**
     * Send Customer Booking Received Notification.
     */
    public function sendCustomerBookingReceived(array $booking): array
    {
        $message = $this->buildBookingReceivedMessage($booking);
        $phone = $booking['mobile'] ?? $booking['whatsapp'] ?? '';

        return $this->dispatchMessage('Customer Booking Received', $booking['id'] ?? 'BK-XXXX', $phone, $message);
    }

    /**
     * Send Admin New Booking Notification.
     */
    public function sendAdminNewBooking(array $booking): array
    {
        $message = $this->buildAdminNewBookingMessage($booking);
        $adminPhone = config('services.whatsapp.from_number', '919131668156');

        return $this->dispatchMessage('Admin New Booking', $booking['id'] ?? 'BK-XXXX', $adminPhone, $message);
    }

    /**
     * Send Customer Booking Confirmed Notification.
     */
    public function sendCustomerBookingConfirmed(array $booking): array
    {
        $message = $this->buildBookingConfirmedMessage($booking);
        $phone = $booking['whatsapp'] ?? $booking['mobile'] ?? '';

        return $this->dispatchMessage('Customer Booking Confirmed', $booking['id'] ?? 'BK-XXXX', $phone, $message);
    }

    /**
     * Send Customer Booking Cancelled Notification.
     */
    public function sendCustomerBookingCancelled(array $booking): array
    {
        $message = $this->buildBookingCancelledMessage($booking);
        $phone = $booking['whatsapp'] ?? $booking['mobile'] ?? '';

        return $this->dispatchMessage('Customer Booking Cancelled', $booking['id'] ?? 'BK-XXXX', $phone, $message);
    }

    /**
     * Build Customer Booking Received Message.
     */
    public function buildBookingReceivedMessage(array $booking): string
    {
        $id = $booking['id'] ?? 'BK-XXXX';
        $name = $booking['name'] ?? 'Valued Customer';
        $package = $booking['package'] ?? 'Event Package';
        $date = $booking['date'] ?? '';
        $time = $booking['time'] ?? '';

        return "🎉 Artizen Booking Received!\n"
            . "Booking ID: {$id}\n"
            . "Customer Name: {$name}\n"
            . "Package: {$package}\n"
            . "Event Date: {$date}\n"
            . "Time: {$time}\n"
            . "Status: Pending\n\n"
            . "Our event team will contact you shortly to verify venue details.";
    }

    /**
     * Build Admin New Booking Message.
     */
    public function buildAdminNewBookingMessage(array $booking): string
    {
        $id = $booking['id'] ?? 'BK-XXXX';
        $name = $booking['name'] ?? 'Customer';
        $phone = $booking['mobile'] ?? '';
        $package = $booking['package'] ?? 'Event Package';
        $date = $booking['date'] ?? '';
        $guests = $booking['guest_count'] ?? 25;
        $venue = ($booking['area'] ?? '') . ', ' . ($booking['city'] ?? 'Indore');
        $total = number_format((int)($booking['total'] ?? $booking['package_price'] ?? 0));

        return "🔔 NEW ARTIZEN BOOKING REQUEST!\n"
            . "Booking ID: {$id}\n"
            . "Customer: {$name}\n"
            . "Phone: {$phone}\n"
            . "Package: {$package}\n"
            . "Date: {$date}\n"
            . "Guests: {$guests} Guests\n"
            . "Venue: {$venue}\n"
            . "Total: ₹{$total}\n"
            . "Status: Pending";
    }

    /**
     * Build Customer Booking Confirmed Message.
     */
    public function buildBookingConfirmedMessage(array $booking): string
    {
        $id = $booking['id'] ?? 'BK-XXXX';
        $package = $booking['package'] ?? 'Event Package';
        $date = $booking['date'] ?? '';
        $time = $booking['time'] ?? '';
        $venue = ($booking['address'] ?? '') . ', ' . ($booking['area'] ?? '') . ', ' . ($booking['city'] ?? 'Indore');
        $totalVal = (int)($booking['total'] ?? $booking['package_price'] ?? 0);
        $advanceVal = (int)round($totalVal * 0.2);
        $balanceVal = $totalVal - $advanceVal;

        $total = number_format($totalVal);
        $advance = number_format($advanceVal);
        $balance = number_format($balanceVal);

        return "✅ Artizen Booking CONFIRMED!\n"
            . "Booking ID: {$id}\n"
            . "Package: {$package}\n"
            . "Event Date: {$date}\n"
            . "Time: {$time}\n"
            . "Venue: {$venue}\n"
            . "Total: ₹{$total}\n"
            . "Advance Deposit: ₹{$advance}\n"
            . "Balance: ₹{$balance} (Payable Offline)\n"
            . "Status: Confirmed\n\n"
            . "Our setup team will arrive on time at your venue.";
    }

    /**
     * Build Customer Booking Cancelled Message.
     */
    public function buildBookingCancelledMessage(array $booking): string
    {
        $id = $booking['id'] ?? 'BK-XXXX';
        $package = $booking['package'] ?? 'Event Package';
        $date = $booking['date'] ?? '';

        return "❌ Artizen Booking CANCELLED\n"
            . "Booking ID: {$id}\n"
            . "Package: {$package}\n"
            . "Event Date: {$date}\n"
            . "Status: Cancelled\n\n"
            . "If you have any questions or wish to reschedule, please contact us on +91 9131668156.";
    }

    /**
     * Dispatch notification payload to configured provider API or handle NOT_CONFIGURED state safely.
     */
    protected function dispatchMessage(string $type, string $bookingId, string $phone, string $message): array
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        if (empty($cleanPhone) || strlen($cleanPhone) < 8) {
            Log::warning("WhatsApp/SMS notification [{$type}] skipped due to invalid phone number for Booking ID: {$bookingId}");
            return [
                'success' => false,
                'status' => 'INVALID_PHONE',
                'message' => "Invalid phone number provided for Booking ID {$bookingId}."
            ];
        }

        if (!self::isConfigured()) {
            Log::info("WhatsApp/SMS provider NOT CONFIGURED. Notification [{$type}] skipped for Booking ID: {$bookingId}");
            return [
                'success' => false,
                'status' => 'NOT_CONFIGURED',
                'message' => 'WhatsApp/SMS provider NOT CONFIGURED'
            ];
        }

        // Active Provider Integration Dispatch
        try {
            $provider = config('services.whatsapp.provider', 'twilio');
            $apiKey = config('services.whatsapp.api_key');
            $fromNumber = config('services.whatsapp.from_number');

            Log::info("WhatsApp/SMS sending [{$type}] via {$provider} to {$cleanPhone} for Booking ID: {$bookingId}");

            // Generic HTTP API POST payload template
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post("https://api.provider.com/v1/messages", [
                'to' => $cleanPhone,
                'from' => $fromNumber,
                'body' => $message
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp/SMS dispatch SUCCESS [{$type}] for Booking ID: {$bookingId}");
                return ['success' => true, 'status' => 'SENT', 'message' => 'Notification dispatched successfully.'];
            }

            Log::error("WhatsApp/SMS dispatch provider error [{$type}] for Booking ID {$bookingId}: " . $response->body());
            return ['success' => false, 'status' => 'PROVIDER_ERROR', 'message' => 'Gateway provider API returned error.'];

        } catch (\Throwable $e) {
            Log::error("WhatsApp/SMS dispatch exception [{$type}] for Booking ID {$bookingId}: " . $e->getMessage());
            return ['success' => false, 'status' => 'FAILED', 'message' => 'Provider request failed: ' . $e->getMessage()];
        }
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppSmsService
{
    /**
     * Check if a third-party WhatsApp gateway provider is configured.
     */
    public static function isConfigured(): bool
    {
        $provider = config('services.whatsapp.provider');
        $token = config('services.whatsapp.access_token') ?: config('services.whatsapp.api_key');
        $phoneId = config('services.whatsapp.phone_number_id');
        $smsKey = config('services.sms.api_key');

        if ($provider === 'meta' || (!empty($phoneId) && !empty($token))) {
            return !empty($token) && !empty($phoneId);
        }

        return !empty($token) || !empty($provider) || !empty($smsKey);
    }

    /**
     * Format phone number to clean E.164 international format without leading + or 0.
     * For 10-digit Indian numbers, automatically prepends the 91 country code.
     */
    public static function formatPhoneNumber(string $phone, string $defaultCountryCode = '91'): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (empty($clean)) {
            return '';
        }

        // Strip leading zeroes (e.g. 09131668156 -> 9131668156)
        $clean = ltrim($clean, '0');

        // If standard 10-digit Indian mobile number, prefix with 91
        if (strlen($clean) === 10) {
            return $defaultCountryCode . $clean;
        }

        return $clean;
    }

    /**
     * Validate whether a phone number matches standard Indian mobile format (10 digits starting with 6-9).
     */
    public static function isValidIndianPhoneNumber(string $phone): bool
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        $clean = ltrim($clean, '0');

        if (str_starts_with($clean, '91') && strlen($clean) === 12) {
            $national = substr($clean, 2);
            return (bool)preg_match('/^[6-9]\d{9}$/', $national);
        }

        if (strlen($clean) === 10) {
            return (bool)preg_match('/^[6-9]\d{9}$/', $clean);
        }

        return false;
    }

    /**
     * Send Customer Booking Received Notification.
     */
    public function sendCustomerBookingReceived(array $booking): array
    {
        $message = $this->buildBookingReceivedMessage($booking);
        $phone = $booking['whatsapp'] ?? $booking['mobile'] ?? '';

        return $this->dispatchMessage('Customer Booking Received', $booking['id'] ?? 'BK-XXXX', $phone, $message, $booking, false);
    }

    /**
     * Send Admin New Booking Notification immediately upon package request submission.
     */
    public function sendAdminNewBooking(array $booking): array
    {
        $message = $this->buildAdminNewBookingMessage($booking);
        $adminPhone = config('services.whatsapp.admin_number')
            ?: config('services.whatsapp.from_number', '919131668156');

        return $this->dispatchMessage('Admin New Booking', $booking['id'] ?? 'BK-XXXX', $adminPhone, $message, $booking, true);
    }

    /**
     * Send Customer Booking Confirmed Notification.
     */
    public function sendCustomerBookingConfirmed(array $booking): array
    {
        $message = $this->buildBookingConfirmedMessage($booking);
        $phone = $booking['whatsapp'] ?? $booking['mobile'] ?? '';

        return $this->dispatchMessage('Customer Booking Confirmed', $booking['id'] ?? 'BK-XXXX', $phone, $message, $booking, false);
    }

    /**
     * Send Customer Booking Cancelled Notification.
     */
    public function sendCustomerBookingCancelled(array $booking): array
    {
        $message = $this->buildBookingCancelledMessage($booking);
        $phone = $booking['whatsapp'] ?? $booking['mobile'] ?? '';

        return $this->dispatchMessage('Customer Booking Cancelled', $booking['id'] ?? 'BK-XXXX', $phone, $message, $booking, false);
    }

    /**
     * Build Customer Booking Received Message.
     */
    public function buildBookingReceivedMessage(array $booking): string
    {
        $id = $booking['id'] ?? 'BK-XXXX';
        $name = $booking['name'] ?? 'Valued Customer';
        $package = $booking['package'] ?? 'Event Package';
        $tier = !empty($booking['tier']) ? " (" . $booking['tier'] . ")" : '';
        $date = $booking['date'] ?? '';
        $time = $booking['time'] ?? '';
        $guests = $booking['guest_count'] ?? 25;
        $total = number_format((int)($booking['total'] ?? $booking['package_price'] ?? 0));
        $pdfUrl = url('/booking/success?id=' . $id . '&download_pdf=1');

        return "BOOKING RECEIVED - ARTIZEN CELEBRATIONS\n\n"
            . "Dear " . strip_tags($name) . ",\n"
            . "Thank you for choosing Artizen to be a part of your celebration! We have safely received your request.\n\n"
            . "PROJECT BRIEF SUMMARY:\n"
            . "- Reference ID: #{$id}\n"
            . "- Customer Name: {$name}\n"
            . "- Package: {$package}{$tier}\n"
            . "- Event Date: {$date}" . (!empty($time) ? " - {$time}" : "") . "\n"
            . "- Guest Count: {$guests} Guests\n"
            . "- Total Settlement: Rs. {$total} (100% Offline Payment)\n"
            . "- Status: Pending Review\n\n"
            . "DOWNLOAD YOUR PROJECT BRIEF & INVOICE PDF:\n"
            . "{$pdfUrl}\n\n"
            . "Our event team is reviewing venue schedule and decor inventory. We will connect with you on WhatsApp/Call shortly to finalize every detail.\n\n"
            . "Offline Payment Guarantee: No payment is required right now. Payment is settled offline only after your booking is confirmed.\n\n"
            . "Artizen Events - Indore";
    }

    /**
     * Build Admin New Booking Message for immediate WhatsApp alert.
     */
    public function buildAdminNewBookingMessage(array $booking): string
    {
        $id = $booking['id'] ?? 'BK-XXXX';
        $name = strip_tags($booking['name'] ?? 'Customer');
        $phone = $booking['mobile'] ?? '';
        $whatsapp = $booking['whatsapp'] ?? $phone;
        $package = strip_tags($booking['package'] ?? 'Event Package');
        $category = strip_tags($booking['category'] ?? 'Celebrations');
        $tier = !empty($booking['tier']) ? " (" . strip_tags($booking['tier']) . ")" : '';
        $date = $booking['date'] ?? '';
        $time = $booking['time'] ?? '';
        $guests = $booking['guest_count'] ?? 25;
        $address = strip_tags($booking['address'] ?? '');
        $area = strip_tags($booking['area'] ?? '');
        $city = strip_tags($booking['city'] ?? 'Indore');
        $landmark = !empty($booking['landmark']) ? "Near " . strip_tags($booking['landmark']) . ", " : '';
        $pincode = !empty($booking['pincode']) ? " - " . strip_tags($booking['pincode']) : '';
        $notes = !empty($booking['notes']) ? mb_substr(strip_tags((string)$booking['notes']), 0, 300) : 'None';
        $total = number_format((int)($booking['total'] ?? $booking['package_price'] ?? 0));

        $fullVenue = trim("{$address}, {$landmark}{$area}, {$city}{$pincode}");
        $cleanCustomerWa = self::formatPhoneNumber($whatsapp);

        $chatLink = !empty($cleanCustomerWa)
            ? "https://wa.me/{$cleanCustomerWa}?text=" . rawurlencode("Hi {$name}, thank you for choosing Artizen! We received your booking request #{$id} for {$package}. We are reviewing availability and will coordinate setup.")
            : "N/A";

        $adminDashboardUrl = url('/admin/dashboard');
        $pdfUrl = url('/booking/success?id=' . $id . '&download_pdf=1');

        return "NEW ARTIZEN BOOKING REQUEST\n"
            . "----------------------------------------\n"
            . "Booking ID: #{$id}\n"
            . "Customer Name: {$name}\n"
            . "Phone: {$phone}\n"
            . "WhatsApp: {$whatsapp}\n\n"
            . "EVENT & PACKAGE DETAILS:\n"
            . "- Package: {$package}{$tier}\n"
            . "- Category: {$category}\n"
            . "- Date: {$date}" . (!empty($time) ? " - {$time}" : "") . "\n"
            . "- Guests: {$guests} Guests\n"
            . "- Total Amount: Rs. {$total} (100% Offline Payment)\n\n"
            . "VENUE ADDRESS:\n"
            . "- {$fullVenue}\n\n"
            . "Special Notes: {$notes}\n"
            . "----------------------------------------\n"
            . "OFFICIAL INVOICE & PROJECT BRIEF PDF:\n"
            . "{$pdfUrl}\n"
            . "----------------------------------------\n"
            . "QUICK ACTIONS:\n"
            . "- Chat with Customer: {$chatLink}\n"
            . "- Admin Dashboard: {$adminDashboardUrl}\n"
            . "----------------------------------------\n"
            . "Artizen Event Booking Alert";
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

        return "BOOKING CONFIRMED - ARTIZEN CELEBRATIONS\n\n"
            . "We are thrilled to celebrate with you! Your event setup has been officially confirmed.\n\n"
            . "Confirmed Event Summary:\n"
            . "- Booking ID: #{$id}\n"
            . "- Package: {$package}\n"
            . "- Event Date: {$date}\n"
            . "- Setup Time: {$time}\n"
            . "- Venue: {$venue}\n\n"
            . "Payment Breakdown:\n"
            . "- Total: Rs. {$total}\n"
            . "- Advance Deposit: Rs. {$advance}\n"
            . "- Balance: Rs. {$balance} (Payable Offline at Venue)\n"
            . "- Status: Confirmed\n\n"
            . "Our decoration and setup crew will reach your venue on time with all curated props.\n"
            . "For any assistance or special surprise additions, reply directly to this message!\n\n"
            . "Artizen Events - Indore";
    }

    /**
     * Build Customer Booking Cancelled Message.
     */
    public function buildBookingCancelledMessage(array $booking): string
    {
        $id = $booking['id'] ?? 'BK-XXXX';
        $package = $booking['package'] ?? 'Event Package';
        $date = $booking['date'] ?? '';

        return "BOOKING UPDATE - ARTIZEN\n\n"
            . "Dear Customer, we are writing to update you that your booking request #{$id} for {$package} on {$date} has been CANCELLED.\n\n"
            . "If this was done by mistake or if you would like to reschedule for another date, we would be delighted to assist you. Simply reply to this chat anytime.\n\n"
            . "Warm regards,\nArtizen Events Concierge";
    }

    /**
     * Dispatch notification payload to configured provider API or handle NOT_CONFIGURED state safely.
     */
    protected function dispatchMessage(string $type, string $bookingId, string $phone, string $message, array $booking = [], bool $isForAdmin = false): array
    {
        $cleanPhone = self::formatPhoneNumber($phone);

        if (empty($cleanPhone) || strlen($cleanPhone) < 8) {
            Log::warning("WhatsApp/SMS notification [{$type}] skipped due to invalid phone number ({$phone}) for Booking ID: {$bookingId}");
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

        $provider = config('services.whatsapp.provider');
        $phoneId = config('services.whatsapp.phone_number_id');

        // If provider is meta or phone_number_id is present, route via Meta Cloud API
        if ($provider === 'meta' || (!empty($phoneId) && empty($provider))) {
            return $this->dispatchMetaCloudApi($type, $bookingId, $cleanPhone, $message, $booking, $isForAdmin);
        }

        return $this->dispatchGenericProvider($type, $bookingId, $cleanPhone, $message);
    }

    /**
     * Dispatch WhatsApp message using official Meta WhatsApp Business Cloud API (Graph API).
     */
    protected function dispatchMetaCloudApi(string $type, string $bookingId, string $cleanPhone, string $message, array $booking, bool $isForAdmin): array
    {
        try {
            $token = config('services.whatsapp.access_token') ?: config('services.whatsapp.api_key');
            $phoneId = config('services.whatsapp.phone_number_id');
            $version = config('services.whatsapp.api_version', 'v21.0');
            $templateName = $isForAdmin
                ? config('services.whatsapp.admin_template')
                : config('services.whatsapp.customer_template');

            if (empty($token) || empty($phoneId)) {
                Log::warning("Meta WhatsApp Cloud API missing token or phone_number_id. Notification [{$type}] skipped for Booking ID: {$bookingId}");
                return [
                    'success' => false,
                    'status' => 'MISSING_CREDENTIALS',
                    'message' => 'Meta WhatsApp Access Token or Phone Number ID is missing.'
                ];
            }

            $endpoint = "https://graph.facebook.com/{$version}/{$phoneId}/messages";

            // If a Meta pre-approved template name is configured, use template payload
            if (!empty($templateName)) {
                $payload = [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $cleanPhone,
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => [
                            'code' => config('services.whatsapp.template_language', 'en')
                        ],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => [
                                    ['type' => 'text', 'text' => $booking['id'] ?? $bookingId],
                                    ['type' => 'text', 'text' => $booking['name'] ?? 'Customer'],
                                    ['type' => 'text', 'text' => $booking['package'] ?? 'Package'],
                                    ['type' => 'text', 'text' => $booking['date'] ?? ''],
                                    ['type' => 'text', 'text' => (string)($booking['total'] ?? $booking['package_price'] ?? 0)],
                                ]
                            ]
                        ]
                    ]
                ];
            } else {
                // Standard Direct Formatted Text Message
                $payload = [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $cleanPhone,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => true,
                        'body' => $message
                    ]
                ];
            }

            Log::info("Meta WhatsApp Cloud API dispatching [{$type}] to {$cleanPhone} for Booking ID: {$bookingId}");

            // Configurable timeout (default 6 seconds for non-blocking execution)
            $timeoutSeconds = (int)config('services.whatsapp.timeout', 6);
            if ($timeoutSeconds <= 0) $timeoutSeconds = 6;

            $response = Http::timeout($timeoutSeconds)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, $payload);

            if ($response->successful()) {
                $msgId = $response->json('messages.0.id');
                Log::info("Meta WhatsApp Cloud API SUCCESS [{$type}] for Booking ID {$bookingId} (WAMID: {$msgId})");
                return [
                    'success' => true,
                    'status' => 'SENT',
                    'provider' => 'meta',
                    'message_id' => $msgId,
                    'message' => 'WhatsApp message dispatched successfully via Meta Cloud API.'
                ];
            }

            $error = $response->json('error', []);
            $errMessage = $error['message'] ?? $response->body();
            $errCode = $error['code'] ?? $response->status();
            $fbTrace = $error['fbtrace_id'] ?? 'N/A';

            $category = match (true) {
                in_array((int)$errCode, [190, 401]) => 'AUTHENTICATION_ERROR',
                in_array((int)$errCode, [403, 131026]) => 'PERMISSION_ERROR',
                (int)$errCode === 131030 => 'SERVICE_WINDOW_ERROR',
                in_array((int)$errCode, [429, 130429]) => 'RATE_LIMIT_ERROR',
                (int)$errCode >= 500 => 'SERVER_ERROR',
                default => 'CLIENT_ERROR'
            };

            Log::error("Meta WhatsApp Cloud API ERROR [{$type}] for Booking ID {$bookingId}: [{$errCode}] {$errMessage} (Trace: {$fbTrace})");

            return [
                'success' => false,
                'status' => 'PROVIDER_ERROR',
                'provider' => 'meta',
                'error_code' => $errCode,
                'error_category' => $category,
                'error_message' => $errMessage,
                'message' => "Meta WhatsApp API Error: {$errMessage} (Code: {$errCode})"
            ];

        } catch (\Throwable $e) {
            Log::error("Meta WhatsApp Cloud API EXCEPTION [{$type}] for Booking ID {$bookingId}: " . $e->getMessage());
            return [
                'success' => false,
                'status' => 'FAILED',
                'provider' => 'meta',
                'message' => 'Meta WhatsApp request failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generic/Twilio fallback dispatcher.
     */
    protected function dispatchGenericProvider(string $type, string $bookingId, string $cleanPhone, string $message): array
    {
        try {
            $provider = config('services.whatsapp.provider', 'twilio');
            $apiKey = config('services.whatsapp.api_key');
            $fromNumber = config('services.whatsapp.from_number');

            Log::info("WhatsApp/SMS sending [{$type}] via {$provider} to {$cleanPhone} for Booking ID: {$bookingId}");

            $response = Http::timeout(6)->withHeaders([
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

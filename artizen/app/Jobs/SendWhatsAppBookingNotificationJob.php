<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\WhatsAppNotification;
use App\Services\JsonStorageService;
use App\Services\WhatsAppSmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppBookingNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximum attempts before job is sent to failed_jobs.
     */
    public int $tries = 3;

    /**
     * Timeout for the entire job in seconds.
     */
    public int $timeout = 30;

    /**
     * Exponential backoff in seconds between retries.
     */
    public function backoff(): array
    {
        return [10, 60, 300];
    }

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $bookingId,
        public string $messageType = 'admin_new_booking',
        public ?array $bookingData = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WhatsAppSmsService $service): void
    {
        // 1. Idempotency Check: Prevent duplicate messages if already successfully sent or delivered
        $recipientType = str_starts_with($this->messageType, 'admin') ? 'admin' : 'customer';

        $existingNotification = WhatsAppNotification::where('booking_id', $this->bookingId)
            ->where('recipient_type', $recipientType)
            ->where('message_type', $this->messageType)
            ->whereIn('status', ['sent', 'delivered', 'read'])
            ->first();

        if ($existingNotification) {
            Log::info("SendWhatsAppBookingNotificationJob skipped: {$this->messageType} already successfully delivered for Booking ID {$this->bookingId} (WAMID: {$existingNotification->wamid})");
            return;
        }

        // 2. Fetch booking record
        $booking = $this->resolveBookingData();
        if (!$booking) {
            Log::warning("SendWhatsAppBookingNotificationJob aborted: Booking ID {$this->bookingId} not found.");
            return;
        }

        // 3. Resolve target phone number
        $phone = ($recipientType === 'admin')
            ? (config('services.whatsapp.admin_number') ?: config('services.whatsapp.from_number', '919131668156'))
            : ($booking['whatsapp'] ?? $booking['mobile'] ?? '');

        // 4. Create or fetch outbox tracking record
        $notificationRecord = WhatsAppNotification::firstOrCreate(
            [
                'booking_id' => $this->bookingId,
                'recipient_type' => $recipientType,
                'message_type' => $this->messageType,
            ],
            [
                'recipient_phone' => WhatsAppSmsService::formatPhoneNumber($phone),
                'provider' => config('services.whatsapp.provider', 'meta') ?: 'meta',
                'status' => 'queued',
                'attempts' => 0,
            ]
        );

        $notificationRecord->increment('attempts');
        $notificationRecord->update([
            'last_attempt_at' => now(),
            'status' => 'queued',
        ]);

        // 5. Dispatch via WhatsAppSmsService
        $result = match ($this->messageType) {
            'admin_new_booking' => $service->sendAdminNewBooking($booking),
            'customer_received' => $service->sendCustomerBookingReceived($booking),
            'customer_confirmed' => $service->sendCustomerBookingConfirmed($booking),
            'customer_cancelled' => $service->sendCustomerBookingCancelled($booking),
            default => $service->sendAdminNewBooking($booking),
        };

        // 6. Handle dispatch outcome
        if (!empty($result['success'])) {
            $notificationRecord->markAsSent($result['message_id'] ?? 'wamid.ARTIZEN_' . uniqid());
            Log::info("WhatsApp notification [{$this->messageType}] SUCCESS for Booking ID {$this->bookingId}");
            return;
        }

        // Handle failure
        $errorCode = (string)($result['error_code'] ?? ($result['status'] ?? 'UNKNOWN_ERROR'));
        $errorMessage = (string)($result['error_message'] ?? ($result['message'] ?? 'Notification dispatch failed'));

        $notificationRecord->markAsFailed($errorCode, $errorMessage);

        // Check if error is permanent (do not retry) vs transient (retry with backoff)
        $isPermanent = in_array($result['status'] ?? '', ['NOT_CONFIGURED', 'INVALID_PHONE', 'MISSING_CREDENTIALS'])
            || in_array((int)$errorCode, [190, 401, 403, 131026]); // OAuth or permission errors

        if ($isPermanent) {
            Log::warning("WhatsApp notification [{$this->messageType}] permanently failed for Booking ID {$this->bookingId}: {$errorMessage}. Will not retry.");
            return;
        }

        // For transient errors, if attempts remain, throw exception to trigger backoff
        if ($this->attempts() < $this->tries) {
            throw new \RuntimeException("WhatsApp dispatch transient failure for Booking {$this->bookingId} [{$errorCode}]: {$errorMessage}");
        }
    }

    /**
     * Resolve booking data array from payload or database or JSON storage.
     */
    protected function resolveBookingData(): ?array
    {
        if (!empty($this->bookingData)) {
            return $this->bookingData;
        }

        $model = Booking::find($this->bookingId);
        if ($model) {
            return [
                'id' => $model->id,
                'name' => $model->customer_name,
                'mobile' => $model->customer_mobile,
                'whatsapp' => $model->customer_whatsapp ?: $model->customer_mobile,
                'email' => $model->customer_email,
                'package' => $model->package_name,
                'category' => $model->category_name,
                'tier' => $model->tier_name,
                'date' => $model->event_date,
                'time' => $model->event_time,
                'guest_count' => $model->guest_count,
                'address' => $model->address,
                'landmark' => $model->landmark,
                'area' => $model->area,
                'city' => $model->city,
                'pincode' => $model->pincode,
                'notes' => $model->notes,
                'total' => $model->total_amount,
                'package_price' => $model->package_price,
                'status' => $model->status,
            ];
        }

        // Fallback to JSON storage
        $all = JsonStorageService::read('bookings.json', []);
        foreach ($all as $b) {
            if (($b['id'] ?? '') === $this->bookingId) {
                return $b;
            }
        }

        return null;
    }
}

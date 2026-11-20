<?php

namespace App\Console\Commands;

use App\Models\WhatsAppNotification;
use App\Services\WhatsAppSmsService;
use Illuminate\Console\Command;

class TestWhatsAppNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'artizen:test-whatsapp 
                            {phone? : Optional recipient phone number to test}
                            {--retry-failed : Re-dispatch all failed notifications in the outbox}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Meta WhatsApp Business Cloud API notification dispatch, webhooks, and audit outbox';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("=================================================");
        $this->info("  ARTIZEN - META WHATSAPP CLOUD API AUDIT & TEST ");
        $this->info("=================================================");

        $provider = config('services.whatsapp.provider', 'meta');
        $token = config('services.whatsapp.access_token') ?: config('services.whatsapp.api_key');
        $phoneId = config('services.whatsapp.phone_number_id');
        $adminPhone = config('services.whatsapp.admin_number') ?: config('services.whatsapp.from_number', '919131668156');
        $apiVersion = config('services.whatsapp.api_version', 'v21.0');
        $template = config('services.whatsapp.admin_template');
        $verifyToken = config('services.whatsapp.webhook_verify_token');
        $appSecret = config('services.whatsapp.app_secret');

        $maskedToken = !empty($token)
            ? substr($token, 0, 7) . '...' . substr($token, -6)
            : '<NOT CONFIGURED>';

        $maskedSecret = !empty($appSecret)
            ? substr($appSecret, 0, 4) . '...' . substr($appSecret, -4)
            : '<NOT CONFIGURED>';

        $this->table(
            ['Configuration Key', 'Current Setting'],
            [
                ['WHATSAPP_PROVIDER', $provider ?: '<none>'],
                ['WHATSAPP_PHONE_NUMBER_ID', $phoneId ?: '<NOT CONFIGURED>'],
                ['WHATSAPP_ACCESS_TOKEN', $maskedToken],
                ['WHATSAPP_ADMIN_NUMBER', $adminPhone],
                ['WHATSAPP_API_VERSION', $apiVersion],
                ['WHATSAPP_ADMIN_TEMPLATE', $template ?: '<Direct Formatted Text Message>'],
                ['WHATSAPP_WEBHOOK_VERIFY_TOKEN', $verifyToken ? '<CONFIGURED>' : '<NOT CONFIGURED>'],
                ['WHATSAPP_APP_SECRET', $maskedSecret],
                ['Webhook URL', url('/webhook/whatsapp')],
                ['Is Configured?', WhatsAppSmsService::isConfigured() ? 'YES (Active)' : 'NO (Inactive)'],
            ]
        );

        // Display Outbox Statistics if table exists
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('whatsapp_notifications')) {
                $total = WhatsAppNotification::count();
                $sent = WhatsAppNotification::where('status', 'sent')->count();
                $delivered = WhatsAppNotification::where('status', 'delivered')->count();
                $read = WhatsAppNotification::where('status', 'read')->count();
                $failed = WhatsAppNotification::where('status', 'failed')->count();
                $queued = WhatsAppNotification::where('status', 'queued')->count();

                $this->info("\n📊 WhatsApp Outbox & Delivery Statistics:");
                $this->table(
                    ['Total', 'Queued', 'Sent', 'Delivered', 'Read', 'Failed'],
                    [[$total, $queued, $sent, $delivered, $read, $failed]]
                );
            }
        } catch (\Throwable $e) {
            // Ignore database table queries in testing or offline
        }

        // Retry failed notifications if requested
        if ($this->option('retry-failed')) {
            return $this->retryFailedNotifications();
        }

        if (!WhatsAppSmsService::isConfigured()) {
            $this->warn("\n⚠️ Meta WhatsApp Cloud API is not configured yet in your .env file!");
            $this->line("To activate direct WhatsApp messages to Admin upon booking requests, add the following to your .env:\n");
            $this->line("WHATSAPP_PROVIDER=meta");
            $this->line("WHATSAPP_PHONE_NUMBER_ID=your_meta_phone_number_id");
            $this->line("WHATSAPP_ACCESS_TOKEN=your_meta_system_user_access_token");
            $this->line("WHATSAPP_ADMIN_NUMBER=919131668156");
            $this->line("WHATSAPP_WEBHOOK_VERIFY_TOKEN=artizen_secure_webhook_token_random");
            $this->line("WHATSAPP_APP_SECRET=your_meta_app_secret\n");
            return Command::FAILURE;
        }

        $targetPhone = $this->argument('phone') ?: $adminPhone;
        $cleanTarget = WhatsAppSmsService::formatPhoneNumber($targetPhone);

        $this->info("\n🚀 Dispatching Test Booking Notification to: {$cleanTarget}");

        $dummyBooking = [
            'id' => 'BK-' . strtoupper(substr(uniqid(), -6)),
            'name' => 'Demo Customer (Test)',
            'mobile' => '9131668156',
            'whatsapp' => '9131668156',
            'package' => 'Adult Birthdays - Premium Setup',
            'category' => 'Birthdays',
            'tier' => 'Premium Setup',
            'date' => date('d M Y', strtotime('+3 days')),
            'time' => '07:00 PM',
            'guest_count' => 35,
            'address' => 'Flat 204, Silver Arcade, Vijay Nagar',
            'area' => 'Vijay Nagar',
            'city' => 'Indore',
            'landmark' => 'Apollo Premier',
            'pincode' => '452010',
            'total' => 12999,
            'notes' => 'Testing Meta WhatsApp Business Cloud API notification.',
        ];

        $service = new WhatsAppSmsService();
        $response = $service->sendAdminNewBooking($dummyBooking);

        if (!empty($response['success'])) {
            $this->info("✅ SUCCESS! Test booking WhatsApp message dispatched successfully!");
            $this->line("Message ID (WAMID): " . ($response['message_id'] ?? 'N/A'));
            return Command::SUCCESS;
        }

        $this->error("❌ DISPATCH FAILED: " . ($response['message'] ?? 'Unknown Error'));
        if (!empty($response['error_code'])) {
            $this->line("Meta Error Code: " . $response['error_code']);
        }
        if (!empty($response['error_category'])) {
            $this->line("Error Category: " . $response['error_category']);
        }
        if (!empty($response['error_message'])) {
            $this->line("Details: " . $response['error_message']);
        }

        return Command::FAILURE;
    }

    /**
     * Retry failed notifications from outbox.
     */
    protected function retryFailedNotifications(): int
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('whatsapp_notifications')) {
            $this->error("whatsapp_notifications table not found.");
            return Command::FAILURE;
        }

        $failedList = WhatsAppNotification::where('status', 'failed')
            ->where('attempts', '<', 5)
            ->get();

        if ($failedList->isEmpty()) {
            $this->info("No retryable failed WhatsApp notifications found.");
            return Command::SUCCESS;
        }

        $this->info("Re-dispatching {$failedList->count()} failed notification(s)...");

        foreach ($failedList as $item) {
            \App\Jobs\SendWhatsAppBookingNotificationJob::dispatch(
                $item->booking_id,
                $item->message_type
            );
            $this->line("Queued retry for Booking ID: {$item->booking_id} ({$item->message_type})");
        }

        $this->info("All retry jobs dispatched successfully.");
        return Command::SUCCESS;
    }
}

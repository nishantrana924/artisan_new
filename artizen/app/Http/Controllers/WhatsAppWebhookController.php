<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Handle Meta Webhook Verification Handshake (GET /webhook/whatsapp).
     */
    public function verify(Request $request): Response
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $configuredToken = config('services.whatsapp.webhook_verify_token');

        if ($mode === 'subscribe' && !empty($configuredToken) && hash_equals($configuredToken, (string)$token)) {
            Log::info("Meta WhatsApp Webhook subscription verified successfully.");
            return response((string)$challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning("Meta WhatsApp Webhook verification failed. Invalid or missing token.");
        return response('Forbidden', 403);
    }

    /**
     * Ingest Meta Webhook Events (POST /webhook/whatsapp).
     */
    public function handle(Request $request): Response
    {
        $rawPayload = $request->getContent();

        // 1. Authenticate Payload with Meta X-Hub-Signature-256 HMAC if APP_SECRET is configured
        $appSecret = config('services.whatsapp.app_secret');
        if (!empty($appSecret)) {
            $hubSignature = $request->header('X-Hub-Signature-256');
            if (empty($hubSignature) || !str_starts_with($hubSignature, 'sha256=')) {
                Log::warning("WhatsApp Webhook rejected: Missing or malformed X-Hub-Signature-256 header.");
                return response('Unauthorized: Signature missing', 401);
            }

            $expectedSignature = hash_hmac('sha256', $rawPayload, $appSecret);
            $providedSignature = substr($hubSignature, 7);

            if (!hash_equals($expectedSignature, $providedSignature)) {
                Log::warning("WhatsApp Webhook rejected: Invalid HMAC signature.");
                return response('Unauthorized: Signature mismatch', 401);
            }
        }

        // 2. Decode incoming event payload
        $data = json_decode($rawPayload, true);
        if (!is_array($data)) {
            return response('EVENT_RECEIVED', 200)->header('Content-Type', 'text/plain');
        }

        // 3. Process Status Updates
        $entries = $data['entry'] ?? [];
        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];
            foreach ($changes as $change) {
                $value = $change['value'] ?? [];
                $statuses = $value['statuses'] ?? [];

                foreach ($statuses as $statusItem) {
                    $wamid = $statusItem['id'] ?? null;
                    $status = strtolower($statusItem['status'] ?? '');
                    $timestamp = $statusItem['timestamp'] ?? null;

                    if (empty($wamid)) {
                        continue;
                    }

                    $notification = WhatsAppNotification::where('wamid', $wamid)->first();
                    if ($notification) {
                        match ($status) {
                            'delivered' => $notification->markAsDelivered($timestamp),
                            'read' => $notification->markAsRead($timestamp),
                            'failed' => $notification->markAsFailed(
                                (string)($statusItem['errors'][0]['code'] ?? 'META_DELIVERY_FAILED'),
                                (string)($statusItem['errors'][0]['title'] ?? 'Message delivery failed at recipient device')
                            ),
                            default => null,
                        };

                        Log::info("WhatsApp delivery status updated to [{$status}] for WAMID: {$wamid}");
                    }
                }
            }
        }

        // Always return HTTP 200 within 2 seconds as required by Meta Cloud API
        return response('EVENT_RECEIVED', 200)->header('Content-Type', 'text/plain');
    }
}

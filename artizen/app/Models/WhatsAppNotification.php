<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppNotification extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_notifications';

    protected $fillable = [
        'booking_id',
        'recipient_type',
        'recipient_phone',
        'message_type',
        'provider',
        'wamid',
        'status',
        'error_code',
        'error_message',
        'attempts',
        'last_attempt_at',
        'sent_at',
        'delivered_at',
        'read_at',
        'payload_summary',
    ];

    protected $casts = [
        'payload_summary' => 'array',
        'attempts' => 'integer',
        'last_attempt_at' => 'datetime',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    /**
     * Associated Booking model.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }

    /**
     * Mark notification as successfully accepted/sent by Meta Cloud API.
     */
    public function markAsSent(string $wamid): self
    {
        $this->update([
            'wamid' => $wamid,
            'status' => 'sent',
            'sent_at' => now(),
            'error_code' => null,
            'error_message' => null,
        ]);

        return $this;
    }

    /**
     * Mark notification as delivered to the recipient's device via Meta Webhook.
     */
    public function markAsDelivered(?string $timestamp = null): self
    {
        $deliveredAt = $timestamp ? date('Y-m-d H:i:s', (int)$timestamp) : now();

        $this->update([
            'status' => 'delivered',
            'delivered_at' => $deliveredAt,
        ]);

        return $this;
    }

    /**
     * Mark notification as read by the recipient via Meta Webhook.
     */
    public function markAsRead(?string $timestamp = null): self
    {
        $readAt = $timestamp ? date('Y-m-d H:i:s', (int)$timestamp) : now();

        $this->update([
            'status' => 'read',
            'read_at' => $readAt,
        ]);

        return $this;
    }

    /**
     * Mark notification attempt as failed.
     */
    public function markAsFailed(string $errorCode, string $errorMessage): self
    {
        $this->update([
            'status' => 'failed',
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
        ]);

        return $this;
    }
}

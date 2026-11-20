<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('whatsapp_notifications')) {
            Schema::create('whatsapp_notifications', function (Blueprint $table) {
                $table->id();
                $table->string('booking_id', 50)->index();
                $table->string('recipient_type', 20)->default('admin'); // admin, customer
                $table->string('recipient_phone', 20)->index();
                $table->string('message_type', 50)->default('admin_new_booking'); // admin_new_booking, customer_received, customer_confirmed, customer_cancelled
                $table->string('provider', 30)->default('meta');
                $table->string('wamid', 100)->nullable()->unique();
                $table->string('status', 30)->default('pending')->index(); // pending, queued, sent, delivered, read, failed
                $table->string('error_code', 50)->nullable();
                $table->text('error_message')->nullable();
                $table->unsignedSmallInteger('attempts')->default(0);
                $table->timestamp('last_attempt_at')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->json('payload_summary')->nullable();
                $table->timestamps();

                $table->index(['booking_id', 'recipient_type', 'message_type'], 'idx_wa_booking_recip_msg');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_notifications');
    }
};

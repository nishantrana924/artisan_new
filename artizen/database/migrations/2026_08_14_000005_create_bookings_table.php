<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->string('id', 50)->primary(); // BK-XXXX
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');
            $table->string('customer_name');
            $table->string('customer_mobile', 20)->index();
            $table->string('customer_whatsapp', 20)->nullable();
            $table->string('customer_email')->nullable();
            $table->string('package_name');
            $table->string('category_name');
            $table->string('tier_name')->nullable();
            $table->string('event_date')->index();
            $table->string('event_time')->nullable();
            $table->integer('guest_count')->default(25);
            $table->text('address');
            $table->string('landmark')->nullable();
            $table->string('area')->index();
            $table->string('city', 100)->default('Indore');
            $table->string('state', 100)->default('Madhya Pradesh');
            $table->string('pincode', 10)->nullable();
            $table->text('notes')->nullable();
            $table->decimal('package_price', 10, 2)->default(0.00);
            $table->decimal('surcharge_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00)->index();
            $table->string('status', 50)->default('Pending')->index();
            $table->json('notifications_sent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

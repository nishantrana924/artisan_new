<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile', 20)->index();
            $table->string('whatsapp', 20)->nullable();
            $table->string('email')->nullable()->index();
            $table->text('address')->nullable();
            $table->string('area')->nullable();
            $table->string('city', 100)->default('Indore');
            $table->string('state', 100)->default('Madhya Pradesh');
            $table->string('pincode', 10)->nullable();
            $table->integer('total_bookings')->default(0);
            $table->decimal('total_spend', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

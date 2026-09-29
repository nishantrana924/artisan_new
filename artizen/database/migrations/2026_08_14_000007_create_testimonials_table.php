<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->string('id', 50)->primary(); // TST-XXXX
            $table->string('author');
            $table->string('location')->default('Vijay Nagar, Indore');
            $table->string('event_type')->default('Celebration');
            $table->text('review');
            $table->tinyInteger('rating')->default(5);
            $table->text('avatar')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};

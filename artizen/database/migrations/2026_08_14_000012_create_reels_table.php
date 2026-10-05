<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reels', function (Blueprint $table) {
            $table->string('id', 50)->primary(); // REEL-XXXX
            $table->string('title');
            $table->string('category')->index(); // birthday, proposal, wedding, house-party, dj-night, corporate
            $table->string('badge')->nullable(); // Most Viewed, Trending, Editor's Pick, Popular
            $table->string('badge_icon')->nullable(); // fa-fire, fa-arrow-trend-up, fa-award, fa-heart
            $table->string('views')->default('1.0K');
            $table->string('location')->default('Indore');
            $table->string('duration')->default('45s');
            $table->text('description')->nullable();
            $table->text('image');
            $table->text('video');
            $table->boolean('is_active')->default(true)->index();
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reels');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cms_offers')) {
            Schema::create('cms_offers', function (Blueprint $table) {
                $table->id();
                $table->string('category_name')->default('Celebration Offer');
                $table->string('icon')->default('fa-solid fa-cake-candles');
                $table->string('icon_color')->default('text-amber-600');
                $table->string('subheading')->nullable();
                $table->string('discount_title');
                $table->string('terms')->nullable();
                $table->string('link')->default('/events');
                $table->integer('display_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cms_offer_settings')) {
            Schema::create('cms_offer_settings', function (Blueprint $table) {
                $table->id();
                $table->string('section_title')->default('Save More with Offers');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cms_promise_banners')) {
            Schema::create('cms_promise_banners', function (Blueprint $table) {
                $table->id();
                $table->text('image');
                $table->string('title')->nullable();
                $table->string('link')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_promise_banners');
        Schema::dropIfExists('cms_offer_settings');
        Schema::dropIfExists('cms_offers');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Nav icon (FontAwesome class, e.g. "fa-solid fa-cake-candles")
            $table->string('icon')->nullable()->after('slug');
            // Pastel BG color for homepage occasion slider card (e.g. "#F6CFB2")
            $table->string('bg_color', 20)->default('#F6CFB2')->after('icon');
            // Separate image for homepage slider card (occasion capsule)
            $table->text('slider_image')->nullable()->after('bg_color');
            // Separate image for header mega-dropdown right panel
            $table->text('dropdown_image')->nullable()->after('slider_image');
            // Badge text on the dropdown panel image (e.g. "Popular Choice")
            $table->string('dropdown_badge', 100)->nullable()->after('dropdown_image');
            // Nav link slug used in URL (cat-birthdays, cat-house-party, etc.)
            $table->string('nav_slug', 100)->nullable()->after('dropdown_badge');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['icon', 'bg_color', 'slider_image', 'dropdown_image', 'dropdown_badge', 'nav_slug']);
        });
    }
};

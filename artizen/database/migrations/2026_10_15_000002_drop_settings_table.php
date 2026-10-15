<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drop the settings table as About page and other configurations are no longer managed via database settings.
     */
    public function up(): void
    {
        Schema::dropIfExists('settings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->longText('value')->nullable();
            $table->string('group', 100)->default('general')->index();
            $table->timestamps();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('package_subcategory')) {
            Schema::create('package_subcategory', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
                $table->foreignId('subcategory_id')->constrained('subcategories')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['package_id', 'subcategory_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('package_subcategory');
    }
};

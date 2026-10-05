<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'badge')) {
                $table->string('badge', 100)->nullable()->after('tag');
            }
            if (!Schema::hasColumn('packages', 'original_price')) {
                $table->decimal('original_price', 10, 2)->nullable()->after('description');
            }
            if (!Schema::hasColumn('packages', 'price')) {
                $table->decimal('price', 10, 2)->nullable()->after('original_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['badge', 'original_price', 'price']);
        });
    }
};

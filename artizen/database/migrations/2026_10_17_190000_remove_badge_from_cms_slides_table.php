<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cms_slides', 'badge')) {
            Schema::table('cms_slides', function (Blueprint $table) {
                $table->dropColumn('badge');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('cms_slides', 'badge')) {
            Schema::table('cms_slides', function (Blueprint $table) {
                $table->string('badge')->nullable()->after('id');
            });
        }
    }
};

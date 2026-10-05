<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            if (!Schema::hasColumn('testimonials', 'email')) {
                $table->string('email', 255)->nullable()->after('author');
            }
            if (!Schema::hasColumn('testimonials', 'package_slug')) {
                $table->string('package_slug', 150)->nullable()->after('event_type')->index();
            }
            if (!Schema::hasColumn('testimonials', 'title')) {
                $table->string('title', 255)->nullable()->after('package_slug');
            }
            if (!Schema::hasColumn('testimonials', 'show_on_home')) {
                $table->boolean('show_on_home')->default(true)->after('is_active')->index();
            }
            if (!Schema::hasColumn('testimonials', 'show_on_reviews_page')) {
                $table->boolean('show_on_reviews_page')->default(true)->after('show_on_home')->index();
            }
            if (!Schema::hasColumn('testimonials', 'show_on_event_details')) {
                $table->boolean('show_on_event_details')->default(true)->after('show_on_reviews_page')->index();
            }
            if (!Schema::hasColumn('testimonials', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('show_on_event_details')->index();
            }
            if (!Schema::hasColumn('testimonials', 'is_verified')) {
                $table->boolean('is_verified')->default(true)->after('avatar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach ([
                'email',
                'package_slug',
                'title',
                'show_on_home',
                'show_on_reviews_page',
                'show_on_event_details',
                'sort_order',
                'is_verified'
            ] as $col) {
                if (Schema::hasColumn('testimonials', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};

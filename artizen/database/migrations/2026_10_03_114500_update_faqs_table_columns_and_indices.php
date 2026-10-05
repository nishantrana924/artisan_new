<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            if (Schema::hasColumn('faqs', 'show_on_home') && !Schema::hasColumn('faqs', 'show_on_homepage')) {
                $table->renameColumn('show_on_home', 'show_on_homepage');
            } elseif (!Schema::hasColumn('faqs', 'show_on_homepage')) {
                $table->boolean('show_on_homepage')->default(false)->after('is_active');
            }

            if (Schema::hasColumn('faqs', 'display_order') && !Schema::hasColumn('faqs', 'sort_order')) {
                $table->renameColumn('display_order', 'sort_order');
            } elseif (!Schema::hasColumn('faqs', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('show_on_homepage');
            }
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->index(['is_active', 'show_on_homepage', 'sort_order'], 'faqs_visibility_order_idx');
        });
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropIndex('faqs_visibility_order_idx');

            if (Schema::hasColumn('faqs', 'show_on_homepage') && !Schema::hasColumn('faqs', 'show_on_home')) {
                $table->renameColumn('show_on_homepage', 'show_on_home');
            }

            if (Schema::hasColumn('faqs', 'sort_order') && !Schema::hasColumn('faqs', 'display_order')) {
                $table->renameColumn('sort_order', 'display_order');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Hardens database integrity: adds user_id FK and composite indexes to bookings,
     * enforces unique customer mobile numbers, and indexes enquiries.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('set null');
            }

            // Composite index for rapid tracking and customer lookups
            $table->index(['id', 'customer_mobile'], 'bookings_id_customer_mobile_index');
        });

        Schema::table('customers', function (Blueprint $table) {
            // Enforce database-level uniqueness on customer mobile numbers
            $table->unique('mobile', 'customers_mobile_unique');
        });

        Schema::table('enquiries', function (Blueprint $table) {
            if (Schema::hasTable('enquiries')) {
                $table->index('created_at', 'enquiries_created_at_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_id_customer_mobile_index');
            if (Schema::hasColumn('bookings', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique('customers_mobile_unique');
        });

        Schema::table('enquiries', function (Blueprint $table) {
            if (Schema::hasTable('enquiries')) {
                $table->dropIndex('enquiries_created_at_index');
            }
        });
    }
};

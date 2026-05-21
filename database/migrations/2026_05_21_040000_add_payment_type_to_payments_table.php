<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add payment_type column to payments table.
     * Values: 'credit' (money paid TO vendor) or 'debit' (money taken FROM vendor).
     * Existing records default to 'credit' to preserve current behavior.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_type', 10)->default('credit')->after('amount');
            $table->index('payment_type');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['payment_type']);
            $table->dropColumn('payment_type');
        });
    }
};

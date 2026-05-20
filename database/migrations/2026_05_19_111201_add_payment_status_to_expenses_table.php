<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add payment_status column to expenses table.
     * 0 = unpaid, 1 = partial, 2 = paid
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->smallInteger('payment_status')
                  ->default(0)
                  ->after('expense_date')
                  ->comment('0 = Unpaid, 1 = Partial, 2 = Paid');
        });
    }

    /**
     * Remove payment_status from expenses table.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
};

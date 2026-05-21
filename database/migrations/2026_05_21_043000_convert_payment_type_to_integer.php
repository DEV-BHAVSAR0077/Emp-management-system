<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Convert payment_type from string ('credit'/'debit') to tinyInteger (1=Credit, 0=Debit).
     */
    public function up(): void
    {
        // First add a temporary column
        Schema::table('payments', function (Blueprint $table) {
            $table->tinyInteger('payment_type_new')->default(1)->after('payment_type');
        });

        // Convert existing data: 'credit' -> 1, 'debit' -> 0
        DB::table('payments')->where('payment_type', 'credit')->update(['payment_type_new' => 1]);
        DB::table('payments')->where('payment_type', 'debit')->update(['payment_type_new' => 0]);

        // Drop old column and rename new one
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['payment_type']);
            $table->dropColumn('payment_type');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('payment_type_new', 'payment_type');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('payment_type');
        });
    }

    /**
     * Reverse: convert back to string.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['payment_type']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_type_temp', 10)->default('credit')->after('payment_type');
        });

        DB::table('payments')->where('payment_type', 1)->update(['payment_type_temp' => 'credit']);
        DB::table('payments')->where('payment_type', 0)->update(['payment_type_temp' => 'debit']);

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_type');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('payment_type_temp', 'payment_type');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('payment_type');
        });
    }
};

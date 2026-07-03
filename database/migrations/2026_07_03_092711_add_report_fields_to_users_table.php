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
        Schema::table('users', function (Blueprint $table) {
            $table->string('report_frequency')->nullable()->after('remember_token'); // 'daily', 'weekly', 'monthly'
            $table->timestamp('next_send_at')->nullable()->after('report_frequency');
            $table->timestamp('last_sent_at')->nullable()->after('next_send_at');
            
            $table->index('next_send_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['next_send_at']);
            $table->dropColumn(['report_frequency', 'next_send_at', 'last_sent_at']);
        });
    }
};

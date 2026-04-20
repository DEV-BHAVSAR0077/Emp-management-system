<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Widen the role column to hold any custom role name (up to 100 chars).
            // Previously it was only large enough for 'admin','hr','user'.
            $table->string('role', 100)->default('User')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('User')->change();
        });
    }
};

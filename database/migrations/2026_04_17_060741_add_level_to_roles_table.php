<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add level column to roles
        Schema::table('roles', function (Blueprint $table) {
            $table->enum('level', ['admin', 'hr', 'user'])->default('user')->after('color');
        });

        // 2. Set levels for the seeded default roles
        DB::table('roles')->where('name', 'Admin')->update(['level' => 'admin']);
        DB::table('roles')->where('name', 'HR')->update(['level' => 'hr']);
        DB::table('roles')->where('name', 'User')->update(['level' => 'user']);

        // 3. Normalize users.role to match roles.name casing
        //    (existing users were created with lowercase 'admin', 'hr', 'user')
        DB::table('users')->where('role', 'admin')->update(['role' => 'Admin']);
        DB::table('users')->where('role', 'hr')->update(['role' => 'HR']);
        DB::table('users')->where('role', 'user')->update(['role' => 'User']);
    }

    public function down(): void
    {
        // Undo the user role casing
        DB::table('users')->where('role', 'Admin')->update(['role' => 'admin']);
        DB::table('users')->where('role', 'HR')->update(['role' => 'hr']);
        DB::table('users')->where('role', 'User')->update(['role' => 'user']);

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add the role column
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->default('User')->after('email');
        });

        // 2. Migrate data from role_id to role
        DB::table('users')->whereNotNull('role_id')->orderBy('id')->chunk(100, function ($users) {
            foreach ($users as $user) {
                $roleName = DB::table('roles')->where('id', $user->role_id)->value('name') ?? 'User';
                DB::table('users')->where('id', $user->id)->update(['role' => $roleName]);
            }
        });

        // 3. Drop role_id column and its foreign key constraint
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_role_id_foreign');
            $table->dropColumn('role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
        });

        DB::table('users')->whereNotNull('role')->orderBy('id')->chunk(100, function ($users) {
            foreach ($users as $user) {
                $roleId = DB::table('roles')->where('name', $user->role)->value('id');
                if ($roleId) {
                    DB::table('users')->where('id', $user->id)->update(['role_id' => $roleId]);
                }
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};

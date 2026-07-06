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
        // 1. Add the new role_id column
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('role');
        });

        // 2. Map existing string roles to their corresponding role_ids
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            if ($user->role) {
                // Find the role ID
                $roleId = DB::table('roles')->where('name', $user->role)->value('id');
                if ($roleId) {
                    DB::table('users')->where('id', $user->id)->update(['role_id' => $roleId]);
                }
            }
        }

        // 3. Drop the old string column and setup the foreign key constraint
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add the role column back
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->nullable()->after('role_id');
        });

        // 2. Map existing role_ids to their corresponding string roles
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            if ($user->role_id) {
                $roleName = DB::table('roles')->where('id', $user->role_id)->value('name');
                if ($roleName) {
                    DB::table('users')->where('id', $user->id)->update(['role' => $roleName]);
                }
            }
        }

        // 3. Drop the foreign key and the role_id column
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};

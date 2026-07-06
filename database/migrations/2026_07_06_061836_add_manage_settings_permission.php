<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permission = Permission::updateOrCreate(
            ['name' => 'manage-settings'],
            ['display_name' => 'Manage Settings']
        );

        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            if (!$adminRole->permissions()->where('name', 'manage-settings')->exists()) {
                $adminRole->permissions()->attach($permission->id);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = Permission::where('name', 'manage-settings')->first();
        if ($permission) {
            DB::table('permission_role')->where('permission_id', $permission->id)->delete();
            $permission->delete();
        }
    }
};

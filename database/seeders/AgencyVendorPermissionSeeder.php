<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class AgencyVendorPermissionSeeder extends Seeder
{
    /**
     * Seed the Agency & Vendor permissions and assign to Admin role.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'view-agency-vendor',   'display_name' => 'View Agency/Vendor',   'group' => 'Agency & Vendors'],
            ['name' => 'create-agency-vendor', 'display_name' => 'Create Agency/Vendor', 'group' => 'Agency & Vendors'],
            ['name' => 'edit-agency-vendor',   'display_name' => 'Edit Agency/Vendor',   'group' => 'Agency & Vendors'],
            ['name' => 'delete-agency-vendor', 'display_name' => 'Delete Agency/Vendor', 'group' => 'Agency & Vendors'],
        ];

        $permissionIds = [];
        foreach ($permissions as $permission) {
            $perm = Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
            $permissionIds[] = $perm->id;
        }

        // Auto-assign all four permissions to the Admin role
        $adminRole = \App\Models\Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->syncWithoutDetaching($permissionIds);
        }
    }
}

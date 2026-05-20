<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PaymentPermissionSeeder extends Seeder
{
    /**
     * Seed the payment-related permissions and assign to Admin role.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'view-payment',   'display_name' => 'View Payment',   'group' => 'Payments'],
            ['name' => 'create-payment', 'display_name' => 'Create Payment', 'group' => 'Payments'],
            ['name' => 'edit-payment',   'display_name' => 'Edit Payment',   'group' => 'Payments'],
            ['name' => 'delete-payment', 'display_name' => 'Delete Payment', 'group' => 'Payments'],
        ];

        $permissionIds = [];
        foreach ($permissions as $permission) {
            $perm = Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
            $permissionIds[] = $perm->id;
        }

        // Auto-assign these permissions to the 'Admin' role if it exists
        $adminRole = \App\Models\Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->syncWithoutDetaching($permissionIds);
        }
    }
}

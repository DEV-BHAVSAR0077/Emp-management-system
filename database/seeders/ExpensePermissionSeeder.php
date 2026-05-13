<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class ExpensePermissionSeeder extends Seeder
{
    /**
     * Seed the expense-related permissions.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'view-expense',   'display_name' => 'View Expense',   'group' => 'Expenses'],
            ['name' => 'create-expense', 'display_name' => 'Create Expense', 'group' => 'Expenses'],
            ['name' => 'edit-expense',   'display_name' => 'Edit Expense',   'group' => 'Expenses'],
            ['name' => 'delete-expense', 'display_name' => 'Delete Expense', 'group' => 'Expenses'],
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

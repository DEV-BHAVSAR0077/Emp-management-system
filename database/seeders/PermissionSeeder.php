<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'create-user',      'display_name' => 'Create User'],
            ['name' => 'edit-user',        'display_name' => 'Edit User'],
            ['name' => 'delete-user',      'display_name' => 'Delete User'],
            ['name' => 'view-user',        'display_name' => 'View User'],

            ['name' => 'create-role',      'display_name' => 'Create Role'],
            ['name' => 'edit-role',        'display_name' => 'Edit Role'],
            ['name' => 'delete-role',      'display_name' => 'Delete Role'],
            ['name' => 'view-role',        'display_name' => 'View Role'],

            ['name'=> 'dashboard', 'display_name'=> 'Dashboard'],
        ];
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                ['display_name' => $permission['display_name']]
            );
        }
    }
}

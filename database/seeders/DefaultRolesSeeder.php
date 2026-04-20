<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class DefaultRolesSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['name' => 'Admin', 'description' => 'Full system access. Can manage users and roles.', 'color' => '#b45309'],
            ['name' => 'HR',    'description' => 'Can add, edit, and delete employee records.',      'color' => '#6d28d9'],
            ['name' => 'User',  'description' => 'Standard access. Can view users and edit own profile.', 'color' => '#374151'],
        ];

        foreach ($defaults as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}

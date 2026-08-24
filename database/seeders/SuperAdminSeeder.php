<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super-admin role if it doesn't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // Create permissions for super admin (optional)
        $permissions = [
            'manage-businesses',
            'manage-admins',
            'manage-system-settings',
            'view-all-reports',
            'manage-roles-permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to super-admin role
        $superAdminRole->syncPermissions(Permission::all());

        $this->command->info('Super-admin role and permissions seeded.');
        $this->command->line('Create an administrator explicitly with user:create-super-admin.');
    }
}

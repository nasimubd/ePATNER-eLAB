<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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

        // Create super admin users
        $superAdmins = [
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@epatner.com',
                'password' => Hash::make('SuperAdmin@123'),
                'email_verified_at' => now(),
                'business_id' => null, // Super admin is not tied to any specific business
            ],
            [
                'name' => 'System Admin',
                'email' => 'system@epatner.com',
                'password' => Hash::make('SystemAdmin@123'),
                'email_verified_at' => now(),
                'business_id' => null,
            ],
            [
                'name' => 'Root Admin',
                'email' => 'root@epatner.com',
                'password' => Hash::make('RootAdmin@123'),
                'email_verified_at' => now(),
                'business_id' => null,
            ],
        ];

        foreach ($superAdmins as $superAdminData) {
            // Check if user already exists
            $existingUser = User::where('email', $superAdminData['email'])->first();

            if (!$existingUser) {
                $superAdmin = User::create($superAdminData);
                $superAdmin->assignRole($superAdminRole);

                $this->command->info("Super Admin created: {$superAdminData['email']}");
            } else {
                // If user exists, just assign the role
                if (!$existingUser->hasRole('super-admin')) {
                    $existingUser->assignRole($superAdminRole);
                }
                $this->command->info("Super Admin role assigned to existing user: {$superAdminData['email']}");
            }
        }

        $this->command->info('Super Admin users created/updated successfully!');
        $this->command->line('');
        $this->command->line('Super Admin Credentials:');
        $this->command->line('Email: superadmin@epatner.com | Password: SuperAdmin@123');
        $this->command->line('Email: system@epatner.com | Password: SystemAdmin@123');
        $this->command->line('Email: root@epatner.com | Password: RootAdmin@123');
    }
}

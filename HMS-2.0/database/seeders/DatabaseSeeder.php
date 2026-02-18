<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SuperAdminSeeder::class,
        ]);

        // Create a super admin user
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
        ]);
        $superAdmin->assignRole('super-admin');




        $manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
        ]);
        $manager->assignRole('Manager');

        $doctor = User::factory()->create([
            'name' => 'Doctor User',
            'email' => 'doctor@example.com',
        ]);
        $doctor->assignRole('Doctor');

        $la = User::factory()->create([
            'name' => 'LA User',
            'email' => 'la@example.com',
        ]);
        $la->assignRole('LA');
    }
}

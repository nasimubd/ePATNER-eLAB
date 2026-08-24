<?php

namespace Database\Seeders;

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

        // Seed only roles and permissions. Administrator accounts must be created
        // explicitly with the user:create-super-admin command.
    }
}

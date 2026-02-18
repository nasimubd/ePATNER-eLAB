<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->insert([
            [
                'key' => 'monthly_fee',
                'value' => '500.00',
                'type' => 'decimal',
                'description' => 'Monthly subscription fee for businesses',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'payment_qr_path',
                'value' => 'images/Payment-QR.png',
                'type' => 'string',
                'description' => 'Path to payment QR code image',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'letterhead_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable letterhead functionality for invoices and lab reports',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

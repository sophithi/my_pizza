<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
            ]
        );

        // Call seeders
        $this->call([
            SettingSeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            OrderSeeder::class,
            InvoiceSeeder::class,
        ]);
    }
}

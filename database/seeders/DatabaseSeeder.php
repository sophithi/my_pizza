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
        // Create admin users
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin2@admin.com'],
            [
                'name' => 'Admin 2',
                'password' => bcrypt('admin234'),
                'role' => 'admin',
            ]
        );

        // Create user admin account
        User::firstOrCreate(
            ['email' => 'user_admin@admin.com'],
            [
                'name' => 'User Admin',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
            ]
        );

        // Call seeders
        $this->call([
            ProductSeeder::class,
            CustomerSeeder::class,
            OrderSeeder::class,
            InvoiceSeeder::class,
        ]);
    }
}

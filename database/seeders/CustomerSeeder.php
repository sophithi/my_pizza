<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Bella Italia Restaurant',
                'type' => 'facebook',
                'phone' => '555-0101',
                'address' => '123 Main Street',
                'city' => 'New York',
                'status' => 'active',
            ],
            [
                'name' => 'Pizza Heaven',
                'type' => 'telegram',
                'phone' => '555-0102',
                'address' => '456 Oak Avenue',
                'city' => 'Los Angeles',
                'status' => 'active',
            ],
            [
                'name' => 'Roma Kitchen',
                'type' => 'facebook',
                'phone' => '555-0103',
                'address' => '789 Park Lane',
                'city' => 'Chicago',
                'status' => 'active',
            ],
            [
                'name' => 'Quick Bite Cafe',
                'type' => 'telegram',
                'phone' => '555-0104',
                'address' => '321 Elm Street',
                'city' => 'Houston',
                'status' => 'active',
            ],
            [
                'name' => 'Gourmet Hub',
                'type' => 'facebook',
                'phone' => '555-0105',
                'address' => '654 Maple Drive',
                'city' => 'Phoenix',
                'status' => 'active',
            ],
            [
                'name' => 'Family Bistro',
                'type' => 'telegram',
                'phone' => '555-0106',
                'address' => '987 Pine Road',
                'city' => 'Philadelphia',
                'status' => 'active',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                ['phone' => $customer['phone']],
                $customer
            );
        }
    }
}

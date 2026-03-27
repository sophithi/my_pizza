<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'email' => 'bella@example.com',
                'phone' => '555-0101',
                'address' => '123 Main Street',
                'city' => 'New York',
                'postal_code' => '10001',
                'company_name' => 'Bella Italia Inc.',
                'contact_person' => 'Marco Rossi',
                'credit_limit' => 5000.00,
                'status' => 'active',
            ],
            [
                'name' => 'Pizza Heaven',
                'email' => 'heaven@example.com',
                'phone' => '555-0102',
                'address' => '456 Oak Avenue',
                'city' => 'Los Angeles',
                'postal_code' => '90001',
                'company_name' => 'Heaven Foods LLC',
                'contact_person' => 'Giovanni Verdi',
                'credit_limit' => 8000.00,
                'status' => 'active',
            ],
            [
                'name' => 'Roma Kitchen',
                'email' => 'roma@example.com',
                'phone' => '555-0103',
                'address' => '789 Park Lane',
                'city' => 'Chicago',
                'postal_code' => '60601',
                'company_name' => 'Roma Foods',
                'contact_person' => 'Antonio Bianchi',
                'credit_limit' => 6500.00,
                'status' => 'active',
            ],
            [
                'name' => 'Quick Bite Cafe',
                'email' => 'bite@example.com',
                'phone' => '555-0104',
                'address' => '321 Elm Street',
                'city' => 'Houston',
                'postal_code' => '77001',
                'company_name' => 'Quick Bite Co.',
                'contact_person' => 'Carlo Negri',
                'credit_limit' => 3500.00,
                'status' => 'active',
            ],
            [
                'name' => 'Gourmet Hub',
                'email' => 'gourmet@example.com',
                'phone' => '555-0105',
                'address' => '654 Maple Drive',
                'city' => 'Phoenix',
                'postal_code' => '85001',
                'company_name' => 'Gourmet Enterprises',
                'contact_person' => 'Sophia Romano',
                'credit_limit' => 7200.00,
                'status' => 'active',
            ],
            [
                'name' => 'Family Bistro',
                'email' => 'family@example.com',
                'phone' => '555-0106',
                'address' => '987 Pine Road',
                'city' => 'Philadelphia',
                'postal_code' => '19101',
                'company_name' => 'Family Dining Group',
                'contact_person' => 'Luigi Fermi',
                'credit_limit' => 4800.00,
                'status' => 'active',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}

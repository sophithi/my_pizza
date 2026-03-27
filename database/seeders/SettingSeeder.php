<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'business_name' => 'Pizza Happy Family',
            'business_email' => 'info@pizzahappyfamily.com',
            'business_phone' => '+63 (02) 1234 5678',
            'business_address' => '123 Main Street, Makati',
            'business_city' => 'Makati',
            'business_postal_code' => '1200',
            'business_description' => 'Premium Pizza Delivery & Restaurant Management System',
            'currency' => 'PHP',
            'tax_rate' => 10,
            'tax_name' => 'VAT',
            'invoice_prefix' => 'INV',
            'enable_notifications' => true,
            'enable_email_invoices' => true,
            'mail_driver' => 'smtp',
            'mail_host' => 'smtp.mailtrap.io',
            'mail_port' => 587,
            'mail_username' => 'your_username',
            'mail_password' => 'your_password',
            'mail_encryption' => 'tls',
            'timezone' => 'Asia/Manila',
            'date_format' => 'Y-m-d',
        ]);
    }
}

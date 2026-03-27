<?php

namespace Database\Seeders;

use App\Models\ExchangeRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use USD as base currency and provide KHR rate relative to USD
        $rates = [
            [
                'currency_code' => 'USD',
                'currency_name' => 'US Dollar',
                'symbol' => '$',
                'rate' => 1.0000, // Base currency (1 USD)
            ],
            [
                'currency_code' => 'KHR',
                'currency_name' => 'Cambodian Riel',
                'symbol' => '៛',
                'rate' => 4050.0000, // 1 USD ≈ 4050 KHR (adjust as needed)
            ],
        ];

        foreach ($rates as $rate) {
            ExchangeRate::create($rate);
        }
    }
}

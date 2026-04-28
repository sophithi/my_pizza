<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    public function usdToKhr(): array
    {
        return Cache::remember('live_exchange_rate_usd_khr', now()->addHours(6), function () {
            $fallbackRate = (float) ExchangeRate::getUSDtoKHRRate();

            try {
                $response = Http::timeout(3)->get(config('services.exchange_rate.url'));

                if ($response->successful()) {
                    $rate = (float) data_get($response->json(), 'rates.KHR');

                    if ($rate > 0) {
                        ExchangeRate::updateOrCreate(
                            ['currency_code' => 'KHR'],
                            [
                                'currency_name' => 'Cambodian Riel',
                                'symbol' => '៛',
                                'rate' => $rate,
                            ]
                        );

                        return [
                            'rate' => $rate,
                            'source' => 'live',
                            'updated_at' => now(),
                        ];
                    }
                }
            } catch (\Throwable $exception) {
                Log::warning('Unable to fetch live USD to KHR exchange rate.', [
                    'message' => $exception->getMessage(),
                ]);
            }

            return [
                'rate' => $fallbackRate,
                'source' => 'local',
                'updated_at' => optional(ExchangeRate::where('currency_code', 'KHR')->first())->updated_at,
            ];
        });
    }
}

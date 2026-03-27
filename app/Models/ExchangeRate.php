<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [
        'currency_code',
        'rate',
        'currency_name',
        'symbol',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
    ];

    /**
     * Get exchange rate for a specific currency (relative to USD which is base)
     */
    public static function getRate($currencyCode = 'USD')
    {
        $rate = self::where('currency_code', $currencyCode)->first();
        return $rate?->rate ?? 1;
    }

    /**
     * Convert USD to target currency
     */
    public static function convertToKHR($usdAmount)
    {
        $rate = self::getRate('KHR');
        return round($usdAmount * $rate, 2);
    }

    /**
     * Convert KHR to USD
     */
    public static function convertToUSD($khrAmount)
    {
        $rate = self::getRate('KHR');
        return round($khrAmount / $rate, 2);
    }

    /**
     * Get all rates as an array for easy access
     */
    public static function getAllRates()
    {
        return self::get()->mapWithKeys(fn($rate) => [
            $rate->currency_code => [
                'rate' => $rate->rate,
                'symbol' => $rate->symbol,
                'name' => $rate->currency_name,
            ]
        ])->toArray();
    }

    /**
     * Get USD to KHR rate for quick lookup
     */
    public static function getUSDtoKHRRate()
    {
        return self::getRate('KHR');
    }
}

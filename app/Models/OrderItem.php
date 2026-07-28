<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'delivery_id',
        'quantity',
        'unit_price',
        'unit_price_khr',
        'total_price',
        'discount_percent',
        'is_custom_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'unit_price_khr' => 'decimal:2',
        'total_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'is_custom_price' => 'boolean',
    ];

    /**
     * KHR unit price actually charged: the stored value if present,
     * otherwise the USD price converted at $rate.
     */
    public function displayUnitKhr(int $rate = 4000): float
    {
        if ($this->unit_price_khr !== null) {
            return (float) $this->unit_price_khr;
        }

        return (float) $this->unit_price * $rate;
    }

    /**
     * This line's KHR total actually charged: unit KHR price x quantity,
     * with this item's own discount_percent applied.
     */
    public function lineTotalKhr(): float
    {
        $discount = (float) ($this->discount_percent ?? 0);

        return $this->displayUnitKhr() * (float) $this->quantity * (1 - $discount / 100);
    }

    /**
     * Get the order associated with this item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product associated with this item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function delivery()
    {
        return $this->belongsTo(\App\Models\Delivery::class);
    }
}
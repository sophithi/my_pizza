<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
   protected $fillable = [
    'name',
    'sku',
    'description',
    'image',
    'category',
    'unit',
    'price_usd',
    'price_khr',
    'supplier',
];

    protected $casts = [
        'price_khr' => 'decimal:2',
        'price_usd' => 'decimal:2',
    ];

    /**
     * Get the inventory record associated with the product.
     */
    protected static function booted()
    {
        static::saving(function ($product) {
            // Auto-calculate KHR from USD
            $product->price_khr = $product->price_usd * 4030;
        });
    }
    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    /**
     * Get all orders that include this product.
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('quantity', 'unit_price', 'total_price')
            ->withTimestamps();
    }

    /**
     * Get the order items for this product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}

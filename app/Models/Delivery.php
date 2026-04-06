<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Delivery extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'delivery_address',
        'delivery_phone',
        'scheduled_delivery_at',
        'actual_delivery_at',
        'status',
        'delivery_type',
        'name_service',
        'price_of_delivery',
        'driver_name',
        'driver_phone',
        'delivery_fee',
        'notes',
        'rejection_reason',
    ];

    protected $casts = [
        'scheduled_delivery_at' => 'datetime',
        'actual_delivery_at' => 'datetime',
        'delivery_fee' => 'decimal:2',
        'price_of_delivery' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Status helpers
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isPreparing()
    {
        return $this->status === 'preparing';
    }

    public function isOutForDelivery()
    {
        return $this->status === 'out_for_delivery';
    }

    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    // Delivery type helpers
    public function isLogistic()
    {
        return $this->delivery_type === 'Logistic';
    }

    public function isTaxi()
    {
        return $this->delivery_type === 'Taxi';
    }

    public function isCustomerSelfPickup()
    {
        return $this->delivery_type === 'Customer Self Picking';
    }

    public function isUsDeliveryCompany()
    {
        return $this->delivery_type === 'Us Delivery Company';
    }

    /**
     * Get all available delivery types
     */
    public static function getDeliveryTypes()
    {
        return [
            'Logistic' => '🏢 Logistic',
            'Taxi' => '🚕 Taxi',
            'Customer Self Picking' => '🚶 Customer Self Picking',
            'Us Delivery Company' => '🚗 Us Delivery Company',
        ];
    }
}

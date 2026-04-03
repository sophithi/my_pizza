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
}

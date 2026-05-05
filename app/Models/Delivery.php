<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Delivery extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'delivery_name',

        'delivery_price_khr',
        'delivery_desc',
    ];

    protected $casts = [
        'delivery_price_khr' => 'decimal:0',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, Order::class, 'delivery_id', 'order_id', 'id', 'id');
    }
}

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
        'delivery_price_khr_big',
        'delivery_desc',
        'show_invoice_info',
    ];

    protected $casts = [
        'delivery_price_khr' => 'decimal:0',
        'delivery_price_khr_big' => 'decimal:0',
        'show_invoice_info' => 'boolean',
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

<?php

namespace App\Models;

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
}

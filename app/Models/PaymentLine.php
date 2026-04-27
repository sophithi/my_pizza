<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLine extends Model
{
    protected $fillable = [
        'payment_id',
        'method',
        'currency',
        'amount_original',
        'amount_usd',
        'exchange_rate',
    ];

    protected $casts = [
        'amount_original' => 'float',
        'amount_usd' => 'float',
        'exchange_rate' => 'float',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}

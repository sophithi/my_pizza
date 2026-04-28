<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'type',
        'phone',
        'address',
        'city',
        'status',
        'notes',
    ];

    protected $casts = [
        //
    ];

    /**
     * Get all orders for this customer.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the total amount spent by this customer.
     */
    public function getTotalSpentAttribute($value)
    {
        return $value ?? $this->orders()->sum('total_amount');
    }
}

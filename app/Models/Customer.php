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
        'salesperson_id',
        'notes',
    ];

    protected $casts = [
        //
    ];

    /**
     * Get the salesperson/agent associated with this customer.
     */
    public function salesperson()
    {
        return $this->belongsTo(Salesperson::class, 'salesperson_id');
    }

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

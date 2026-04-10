<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'type',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'company_name',
        'contact_person',
        'credit_limit',
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
    public function getTotalSpentAttribute()
    {
        return $this->orders()->where('status', 'completed')->sum('total_amount');
    }
}
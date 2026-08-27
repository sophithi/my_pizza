<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salesperson extends Model
{
    protected $table = 'salespersons';

    protected $fillable = [
        'name',
        'phone',
        'status',
    ];

    /**
     * Get all customers managed by this salesperson.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'salesperson_id');
    }
}


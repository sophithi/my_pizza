<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventories';

    protected $fillable = [
        'product_id',
        'quantity',
        'reorder_level',
        'reorder_quantity',
        'last_restocked',
        'cost_per_unit',
        'warehouse_location',
    ];

    protected $casts = [
        'last_restocked' => 'datetime',
        'cost_per_unit' => 'decimal:2',
    ];

    /**
     * Get the product associated with this inventory.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if the inventory needs restocking.
     */
    public function needsRestocking()
    {
        return $this->quantity <= $this->reorder_level;
    }

    /**
     * Get the inventory status.
     */
    public function getStatusAttribute()
    {
        if ($this->quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->quantity <= $this->reorder_level) {
            return 'low_stock';
        }
        return 'in_stock';
    }
}

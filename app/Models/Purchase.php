<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference_number',
        'supplier_name',
        'purchase_date',
        'total_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}

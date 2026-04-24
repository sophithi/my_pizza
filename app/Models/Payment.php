<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'order_id',
        'order_date',
        'total_amount',
        'paid_amount',
        'method',
        'status',   // paid | partial | pending
        'notes',
    ];

    protected $casts = [
        'order_date'   => 'date',
        'total_amount' => 'float',
        'paid_amount'  => 'float',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'pending');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getBalanceAttribute(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'paid'    => 'Paid',
            'partial' => 'Partial',
            'pending' => 'Unpaid',
            default   => ucfirst($this->status),
        };
    }
}
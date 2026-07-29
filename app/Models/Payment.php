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
        'total_amount_khr',
        'paid_amount',
        'paid_amount_khr',
        'exchange_rate',
        'exchange_rate_notes',
        'method',
        'status',   // paid | partial | pending
        'notes',
    ];

    protected $casts = [
        'order_date'   => 'date',
        'total_amount' => 'float',
        'total_amount_khr' => 'decimal:2',
        'paid_amount'  => 'float',
        'paid_amount_khr' => 'decimal:2',
        'exchange_rate' => 'float',
    ];

    public function lines()
    {
        return $this->hasMany(PaymentLine::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if payment overage is acceptable due to exchange rate differences
     * Allows up to 2% or $5 USD, whichever is smaller
     */
    public function isOverageAcceptable(float $overage): bool
    {
        $tolerance = min((float) $this->total_amount * 0.02, 5);
        return abs($overage) <= $tolerance;
    }

    /**
     * Get exchange rate notes safely
     */
    public function getExchangeRateNotesAttribute(): ?string
    {
        return $this->attributes['exchange_rate_notes'] ?? null;
    }

    /**
     * Get exchange rate safely with fallback
     */
    public function getExchangeRateAttribute(): float
    {
        return (float) ($this->attributes['exchange_rate'] ?? 4000);
    }

    /**
     * Check if has exchange rate variance
     */
    public function hasExchangeRateVariance(): bool
    {
        return !empty($this->attributes['exchange_rate_notes'] ?? null);
    }

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

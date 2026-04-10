<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'order_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the order for this invoice.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Mark invoice as sent.
     */
    public function markAsSent(): void
    {
        $this->update(['status' => 'sent']);
    }

    /**
     * Mark invoice as paid.
     */
    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid']);
    }

    /**
     * Mark invoice as cancelled.
     */
    public function markAsCancelled(): void
    {
        $this->update(['status' => 'cancelled']);
    }
}

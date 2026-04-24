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
   public function items()
{
    return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
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

    /**
     * Generate next invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $lastInvoice = self::orderByRaw("CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED) DESC")->first();
        $nextNumber = $lastInvoice ? (int) substr($lastInvoice->invoice_number, 4) + 1 : 1;
        return 'INV-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create invoice from order (auto-generate).
     */
    public static function createFromOrder(Order $order): self
    {
        return self::create([
            'order_id' => $order->id,
            'invoice_number' => self::generateInvoiceNumber(),
            'invoice_date' => now()->toDateString(),
            'subtotal' => $order->subtotal,
            'discount_amount' => $order->discount_amount,
            'total_amount' => $order->total_amount,
            'notes' => $order->notes ?? null,
        ]);
    }
}

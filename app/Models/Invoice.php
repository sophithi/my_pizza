<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'delivery_fee_khr',
        'delivery_fee_usd',
        'total_amount',
        'status',
        'packing_sent_at',
        'packing_completed_at',
        'printed_at',
        'notes',
        'deleted_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'delivery_fee_khr' => 'decimal:2',
        'delivery_fee_usd' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'packing_sent_at' => 'datetime',
        'packing_completed_at' => 'datetime',
        'printed_at' => 'datetime',
    ];

    /**
     * Get the order for this invoice. Always withTrashed() — an invoice and
     * its order are soft-deleted/restored together (see InvoiceController),
     * so a trashed invoice must still be able to show its (also trashed) order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class)->withTrashed();
    }

    /**
     * Get the user who deleted this invoice.
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
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
        $lastInvoice = self::withTrashed()->orderByRaw("CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED) DESC")->first();
        $nextNumber = $lastInvoice ? (int) substr($lastInvoice->invoice_number, 4) + 1 : 1;
        return 'INV-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create an invoice with a guaranteed-unique invoice_number. Two requests
     * can still read the same "next number" before either commits; if the
     * insert collides on the unique constraint, regenerate and retry rather
     * than surface a 500.
     */
    public static function createUnique(array $attributes): self
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            try {
                return self::create(['invoice_number' => self::generateInvoiceNumber()] + $attributes);
            } catch (\Illuminate\Database\QueryException $e) {
                if ($attempt === 5 || $e->getCode() !== '23000') {
                    throw $e;
                }
            }
        }
    }

    /**
     * Create invoice from order (auto-generate).
     */
    public static function createFromOrder(Order $order): self
    {
        return self::createUnique([
            'order_id' => $order->id,
            'invoice_date' => now()->toDateString(),
            'subtotal' => $order->subtotal,
            'discount_amount' => $order->discount_amount,
            'delivery_fee_khr' => $order->delivery_fee_khr,
            'delivery_fee_usd' => $order->delivery_fee_usd,
            'total_amount' => $order->total_amount,
            'notes' => $order->notes ?? null,
        ]);
    }
}

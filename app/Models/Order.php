<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'delivery_id',
        'taxi_phone',
        'box_qty',
        'small_pack_qty',
        'big_pack_qty',
        'user_id',
        'prepared_by',
        'prepared_at',
        'order_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'delivery_fee_khr',
        'delivery_fee_usd',
        'total_amount',
        'status',
        'stock_deducted',
        'payment_status',
        'notes',
        'delivery_date',
        'deleted_by',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'delivery_date' => 'datetime',
        'prepared_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'delivery_fee_khr' => 'decimal:2',
        'delivery_fee_usd' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'box_qty' => 'integer',
        'small_pack_qty' => 'integer',
        'big_pack_qty' => 'integer',
        'stock_deducted' => 'boolean',
    ];

    /**
     * Get the customer associated with the order.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user (staff) who created this order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all items in this order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get paid items in this order.
     */
    public function paidItems()
    {
        return $this->hasMany(OrderItem::class)->where('unit_price', '>', 0);
    }

    /**
     * Get free items in this order.
     */
    public function freeItems()
    {
        return $this->hasMany(OrderItem::class)->where('unit_price', '=', 0);
    }

    /**
     * Get all products in this order.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot('quantity', 'unit_price', 'total_price')
            ->withTimestamps();
    }

    /**
     * Get the invoice for this order.
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Get all payments for this order.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the delivery for this order.
     */
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    /**
     * Get the user who prepared this order.
     */
    public function preparer()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    /**
     * Get the user who deleted this order.
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Canonical price totals — every page (order, invoice, packing sticker)
     * should read totals through these instead of re-deriving its own
     * formula, mirroring how orders/create.blade.php computes them.
     */

    /**
     * Gross (pre-discount) USD subtotal, summed from items.
     */
    public function grossSubtotalUsd(): float
    {
        return (float) $this->items->sum(function ($item) {
            return (float) $item->unit_price * (float) $item->quantity;
        });
    }

    /**
     * Gross (pre-discount) KHR subtotal, summed from items.
     */
    public function grossSubtotalKhr(): float
    {
        return (float) $this->items->sum(function ($item) {
            return $item->displayUnitKhr() * (float) $item->quantity;
        });
    }

    /**
     * Total KHR discount, summed from each item's own discount_percent
     * applied to its own KHR price — never a flat-rate conversion of the
     * USD discount, which drifts whenever a custom KHR price is used.
     */
    public function itemDiscountKhr(): float
    {
        return (float) $this->items->sum(function ($item) {
            $discount = (float) ($item->discount_percent ?? 0);

            return $item->displayUnitKhr() * (float) $item->quantity * ($discount / 100);
        });
    }

    /**
     * Net KHR total actually charged: gross subtotal minus each item's own
     * discount, plus delivery.
     */
    public function totalKhr(): float
    {
        return $this->grossSubtotalKhr() - $this->itemDiscountKhr() + (float) $this->delivery_fee_khr;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'delivery_id',
        'box_qty',
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
}

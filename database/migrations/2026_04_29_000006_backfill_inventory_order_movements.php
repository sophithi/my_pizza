<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('inventory_movements')) {
            return;
        }

        $orderItems = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('inventories', 'inventories.product_id', '=', 'order_items.product_id')
            ->where('orders.stock_deducted', true)
            ->select(
                'orders.id as order_id',
                'orders.user_id',
                'orders.created_at as order_created_at',
                'inventories.id as inventory_id',
                'inventories.product_id',
                'inventories.quantity as current_quantity',
                DB::raw('SUM(order_items.quantity) as quantity')
            )
            ->groupBy(
                'orders.id',
                'orders.user_id',
                'orders.created_at',
                'inventories.id',
                'inventories.product_id',
                'inventories.quantity'
            )
            ->get();

        foreach ($orderItems as $item) {
            $quantity = (int) $item->quantity;
            $quantityAfter = (int) $item->current_quantity;

            DB::table('inventory_movements')->insert([
                'inventory_id' => $item->inventory_id,
                'product_id' => $item->product_id,
                'order_id' => $item->order_id,
                'user_id' => $item->user_id,
                'type' => 'order_deduct',
                'quantity_change' => -$quantity,
                'quantity_before' => $quantityAfter + $quantity,
                'quantity_after' => $quantityAfter,
                'note' => 'Backfilled stock deduction for order #' . $item->order_id,
                'created_at' => $item->order_created_at,
                'updated_at' => $item->order_created_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('inventory_movements')
            ->where('note', 'like', 'Backfilled stock deduction for order #%')
            ->delete();
    }
};

<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderStockService
{
    public function snapshot(Order $order): array
    {
        $result = [];
        foreach ($order->items()->get() as $item) {
            $kind = $item->item_type;
            $result[$item->product_id][$kind] = ($result[$item->product_id][$kind] ?? 0) + (int) $item->quantity;
        }
        return $result;
    }

    public function deduct(Order $order): array
    {
        return DB::transaction(function () use ($order) {
            $locked = Order::withTrashed()->lockForUpdate()->findOrFail($order->id);
            if ($locked->stock_deducted) {
                return [];
            }
            $this->apply($locked, [], $this->snapshot($locked), 'order_deduct');
            $locked->update(['stock_deducted' => true]);
            $order->stock_deducted = true;
            return [];
        });
    }

    public function restore(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $locked = Order::withTrashed()->lockForUpdate()->findOrFail($order->id);
            if (!$locked->stock_deducted) {
                return;
            }
            $this->apply($locked, $this->snapshot($locked), [], 'order_restore');
            $locked->update(['stock_deducted' => false]);
            $order->stock_deducted = false;
        });
    }

    // The caller holds the order lock and transaction while replacing its items.
    public function reconcile(Order $order, array $before): void
    {
        $this->apply($order, $before, $this->snapshot($order), 'order_adjust');
    }

    private function apply(Order $order, array $before, array $after, string $type): void
    {
        $productIds = array_unique(array_merge(array_keys($before), array_keys($after)));
        sort($productIds, SORT_NUMERIC);
        foreach ($productIds as $productId) {
            $deltas = [];
            foreach (['sale', 'free'] as $kind) {
                $deltas[$kind] = ($before[$productId][$kind] ?? 0) - ($after[$productId][$kind] ?? 0);
            }
            if ($deltas['sale'] === 0 && $deltas['free'] === 0) {
                continue;
            }
            $inventory = Inventory::where('product_id', $productId)->lockForUpdate()->first();
            if (!$inventory) {
                throw ValidationException::withMessages(['order_items' => "Product #{$productId} has no inventory record."]);
            }
            $quantity = (int) $inventory->quantity;
            // Orders may exceed recorded stock; preserve the signed balance and full ledger.
            // Apply returns first so a sale/free reclassification never creates a negative intermediate balance.
            arsort($deltas);
            foreach ($deltas as $kind => $change) {
                if ($change === 0) {
                    continue;
                }
                InventoryMovement::create([
                    'inventory_id' => $inventory->id,
                    'product_id' => $productId,
                    'order_id' => $order->id,
                    'user_id' => auth()->id(),
                    'type' => $type,
                    'item_type' => $kind,
                    'quantity_change' => $change,
                    'quantity_before' => $quantity,
                    'quantity_after' => $quantity + $change,
                    'note' => "{$type} ({$kind}) for order #{$order->id}",
                ]);
                $quantity += $change;
            }
            $inventory->update(['quantity' => $quantity]);
        }
    }
}

<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Support\Collection;

class InventoryReportService
{
    public function rows(?string $start, ?string $end, ?int $productId = null, ?string $category = null): Collection
    {
        $inventory = Inventory::with('product')
            ->when($productId, fn ($q) => $q->where('product_id', $productId))
            ->when($category, fn ($q) => $q->whereHas('product', fn ($p) => $p->where('category', $category)))
            ->orderBy('product_id')->get();
        $ids = $inventory->modelKeys();
        $flow = InventoryMovement::whereIn('inventory_id', $ids)
            ->when($start, fn ($q) => $q->whereDate('created_at', '>=', $start))
            ->when($end, fn ($q) => $q->whereDate('created_at', '<=', $end))
            ->selectRaw('inventory_id, SUM(quantity_change) as delta')
            ->selectRaw("SUM(CASE WHEN type IN ('stock_create', 'stock_restock') AND quantity_change > 0 THEN quantity_change ELSE 0 END) as received")
            ->selectRaw("SUM(CASE WHEN type IN ('order_deduct', 'order_adjust') AND item_type = 'sale' AND quantity_change < 0 THEN -quantity_change ELSE 0 END) as sold")
            ->selectRaw("SUM(CASE WHEN type IN ('order_deduct', 'order_adjust') AND item_type = 'free' AND quantity_change < 0 THEN -quantity_change ELSE 0 END) as free")
            ->selectRaw("SUM(CASE WHEN type IN ('order_deduct', 'order_adjust') AND (item_type IS NULL OR item_type NOT IN ('sale', 'free')) AND quantity_change < 0 THEN -quantity_change ELSE 0 END) as unclassified")
            ->selectRaw("SUM(CASE WHEN type = 'order_restore' AND quantity_change > 0 THEN quantity_change ELSE 0 END) as returned")
            ->groupBy('inventory_id')->get()->keyBy('inventory_id');
        $later = $end ? InventoryMovement::whereIn('inventory_id', $ids)
            ->whereDate('created_at', '>', $end)->selectRaw('inventory_id, SUM(quantity_change) as delta')
            ->groupBy('inventory_id')->get()->keyBy('inventory_id') : collect();

        return $inventory->map(function ($item) use ($flow, $later) {
            $movement = $flow->get($item->id);
            $received = (int) ($movement?->received ?? 0);
            $sold = (int) ($movement?->sold ?? 0);
            $free = (int) ($movement?->free ?? 0);
            $unclassified = (int) ($movement?->unclassified ?? 0);
            $returned = (int) ($movement?->returned ?? 0);
            $delta = (int) ($movement?->delta ?? 0);
            $closing = (int) $item->quantity - (int) ($later->get($item->id)?->delta ?? 0);
            // Remaining adjustments include stock corrections and positive order-edit reversals.
            $adjustment = $delta - ($received - $sold - $free - $unclassified + $returned);
            return (object) [
                'id' => $item->id, 'product_id' => $item->product_id,
                'name' => $item->product?->name ?? '—', 'unit' => $item->product?->unitLabel() ?? '',
                'opening' => $closing - $delta, 'received' => $received, 'sold' => $sold,
                'free' => $free, 'returned' => $returned, 'unclassified' => $unclassified,
                'adjustment' => $adjustment, 'closing' => $closing,
            ];
        });
    }
}

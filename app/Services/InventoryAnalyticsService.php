<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use Carbon\Carbon;

class InventoryAnalyticsService
{
    public function dashboard(string $start, string $end, ?int $productId = null): array
    {
        $items = Inventory::with('product')->get();
        $days = [];
        for ($day = Carbon::parse($start); $day->lte(Carbon::parse($end)); $day->addDay()) {
            $days[$day->toDateString()] = ['sold' => 0, 'free' => 0, 'other' => 0];
        }
        $chartItems = $productId ? $items->where('product_id', $productId) : $items;
        $movements = InventoryMovement::whereIn('inventory_id', $chartItems->modelKeys())
            ->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->where('quantity_change', '<', 0)
            ->selectRaw('DATE(created_at) as day')
            ->selectRaw("SUM(CASE WHEN type IN ('order_deduct', 'order_adjust') AND item_type = 'sale' THEN -quantity_change ELSE 0 END) as sold")
            ->selectRaw("SUM(CASE WHEN type IN ('order_deduct', 'order_adjust') AND item_type = 'free' THEN -quantity_change ELSE 0 END) as free")
            ->selectRaw("SUM(CASE WHEN type IN ('order_deduct', 'order_adjust') AND item_type IN ('sale', 'free') THEN 0 ELSE -quantity_change END) as other")
            ->groupByRaw('DATE(created_at)')->get();
        foreach ($movements as $movement) {
            $days[$movement->day] = [
                'sold' => (int) $movement->sold,
                'free' => (int) $movement->free,
                'other' => (int) $movement->other,
            ];
        }

        $granularity = count($days) <= 31 ? 'day' : (count($days) <= 120 ? 'week' : 'month');
        $buckets = [];
        $dayIndex = 0;
        foreach ($days as $date => $counts) {
            $key = match ($granularity) {
                'week' => (string) intdiv($dayIndex, 7),
                'month' => substr($date, 0, 7),
                default => $date,
            };
            if (!isset($buckets[$key])) {
                $buckets[$key] = ['from' => $date, 'to' => $date, 'sold' => 0, 'free' => 0, 'other' => 0];
            }
            $buckets[$key]['to'] = $date;
            foreach (['sold', 'free', 'other'] as $kind) {
                $buckets[$key][$kind] += $counts[$kind];
            }
            $dayIndex++;
        }

        return [
            'labels' => array_values(array_map(fn ($bucket) => match ($granularity) {
                'week' => Carbon::parse($bucket['from'])->format('d/m') . '–' . Carbon::parse($bucket['to'])->format('d/m'),
                'month' => Carbon::parse($bucket['from'])->format('m/Y'),
                default => $bucket['from'],
            }, $buckets)),
            'granularity' => $granularity,
            'sold' => array_column(array_values($buckets), 'sold'),
            'free' => array_column(array_values($buckets), 'free'),
            'other' => array_column(array_values($buckets), 'other'),
            'products' => $items->filter(fn ($item) => $item->product)->sortBy(fn ($item) => $item->product->name)->values(),
            'recentActivities' => InventoryMovement::with([
                'product',
                'order' => fn ($query) => $query->withTrashed()->with('items'),
            ])->orderByDesc('created_at')->orderByDesc('id')->limit(6)->get(),
            'lowStockAlerts' => $items->filter(fn ($item) => $item->quantity <= max(0, (int) $item->reorder_level))
                ->sortBy('quantity')->take(6)->values(),
        ];
    }
}

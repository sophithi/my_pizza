<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$request->has('period') && !$request->filled('date')) {
            $request->merge(['period' => 'today']);
        }

        $stats = [
            'total' => Inventory::count(),
            'in_stock' => Inventory::whereColumn('quantity', '>', 'reorder_level')->count(),
            'low_stock' => Inventory::where('quantity', '>', 0)
                ->whereColumn('quantity', '<=', 'reorder_level')
                ->count(),
            'out_stock' => Inventory::where('quantity', '<=', 0)->count(),
        ];

        $sort = $request->get('sort');
        $period = $request->get('period');
        $movementDate = $this->movementDateFromRequest($request);
        $query = Inventory::with('product');

        if ($movementDate) {
            $query->whereHas('movements', function ($movementQuery) use ($movementDate) {
                $movementQuery->whereDate('created_at', $movementDate);
            });
        } elseif ($period === 'month') {
            $query->whereHas('movements', function ($movementQuery) {
                $movementQuery->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
            });
        } elseif ($period === 'year') {
            $query->whereHas('movements', function ($movementQuery) {
                $movementQuery->whereYear('created_at', now()->year);
            });
        }

        if ($movementDate || in_array($period, ['month', 'year'], true) || $sort === 'day') {
            $query = $query->withMax('movements as last_movement_at', 'created_at')
                ->orderByDesc('last_movement_at');
        } else {
            $query = $query->orderByDesc('id');
        }

        $inventories = $query->paginate(15)->withQueryString();
        $movementSummary = $this->movementSummary($request);
        $movementsByInventory = $this->movementsByInventory($request);

        return view('inventory.index', compact('inventories', 'stats', 'movementDate', 'movementSummary', 'movementsByInventory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = \App\Models\Product::doesntHave('inventory')->get();
        return view('inventory.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventoryRequest $request)
    {
        $inventory = DB::transaction(function () use ($request) {
            $inventory = Inventory::create($request->validated());
            $this->recordManualMovement($inventory, 'stock_create', (int) $inventory->quantity, 0, (int) $inventory->quantity, 'Initial inventory created');

            return $inventory;
        });

        return redirect()->route('inventory.show', $inventory)->with('success', 'បានបង្កើតស្តុកទំនិញដោយជោគជ័យ។');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        $inventory->load('product');
        return view('inventory.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        return view('inventory.edit', compact('inventory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInventoryRequest $request, Inventory $inventory)
    {
        DB::transaction(function () use ($request, $inventory) {
            $validated = $request->validated();
            $before = (int) $inventory->quantity;
            $inventory->update($validated);
            $after = (int) $inventory->quantity;

            if ($before !== $after) {
                $this->recordManualMovement($inventory, 'manual_adjust', $after - $before, $before, $after, 'Inventory edited manually');
            }
        });

        return redirect()->route('inventory.show', $inventory)->with('success', 'បានកែប្រែស្តុកទំនិញដោយជោគជ័យ។');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('inventory.index')->with('success', 'បានលុបស្តុកទំនិញដោយជោគជ័យ។');
    }

    /**
     * Quick update the quantity of an inventory item.
     */
    public function quickUpdate($id)
    {
        $inventory = Inventory::findOrFail($id);
        $quantity = request()->validate(['quantity' => 'required|numeric|min:0'])['quantity'];

        DB::transaction(function () use ($inventory, $quantity) {
            $before = (int) $inventory->quantity;
            $after = (int) $quantity;
            $inventory->update(['quantity' => $after]);

            if ($before !== $after) {
                $this->recordManualMovement($inventory, 'quick_adjust', $after - $before, $before, $after, 'Quick stock update');
            }
        });

        return redirect()->route('inventory.index')->with('success', 'បានកែប្រែចំនួនស្តុកដោយជោគជ័យ។');
    }

    public function restock(Inventory $inventory)
    {
        $validated = request()->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $addedQuantity = (int) $validated['quantity'];

        DB::transaction(function () use ($inventory, $addedQuantity) {
            $before = (int) $inventory->quantity;
            $inventory->increment('quantity', $addedQuantity);
            $after = (int) $inventory->fresh()->quantity;

            $this->recordManualMovement($inventory, 'stock_restock', $addedQuantity, $before, $after, 'Stock added to inventory');
        });

        return redirect()
            ->route('inventory.index', ['period' => 'today'])
            ->with('success', 'បានបន្ថែមចំនួនទំនិញចូលស្តុកដោយជោគជ័យ។');
    }

    private function movementDateFromRequest(Request $request): ?string
    {
        if ($request->get('period') === 'today') {
            return today()->toDateString();
        }

        if ($request->get('period') === 'yesterday') {
            return today()->subDay()->toDateString();
        }

        if ($request->filled('date')) {
            return $request->date;
        }

        return null;
    }

    private function movementSummary(Request $request): array
    {
        $query = $this->visibleMovementGroups($request);

        return [
            'cut_out' => (int) (clone $query)->sum(DB::raw('CASE WHEN net_change < 0 THEN ABS(net_change) ELSE 0 END')),
            'added_back' => (int) (clone $query)->sum(DB::raw('CASE WHEN net_change > 0 THEN net_change ELSE 0 END')),
            'products' => (clone $query)->distinct('inventory_id')->count('inventory_id'),
        ];
    }

    private function movementsByInventory(Request $request)
    {
        $query = $this->visibleMovementGroups($request)
            ->selectRaw('inventory_id, SUM(CASE WHEN net_change < 0 THEN ABS(net_change) ELSE 0 END) as cut_out')
            ->selectRaw('SUM(CASE WHEN net_change > 0 THEN net_change ELSE 0 END) as added_back')
            ->selectRaw('MAX(last_movement_at) as last_movement_at')
            ->groupBy('inventory_id');

        return $query->get()->keyBy('inventory_id');
    }

    private function visibleMovementGroups(Request $request)
    {
        $groups = InventoryMovement::query()
            ->selectRaw('inventory_id')
            ->selectRaw('SUM(quantity_change) as net_change')
            ->selectRaw('MAX(created_at) as last_movement_at')
            ->groupBy('inventory_id')
            ->groupBy(DB::raw('COALESCE(order_id, id)'))
            ->groupBy('created_at');

        $this->applyMovementDateFilter($groups, $request);

        return DB::query()
            ->fromSub($groups, 'movement_groups')
            ->where('net_change', '<>', 0);
    }

    private function applyMovementDateFilter($query, Request $request): void
    {
        $period = $request->get('period');
        $movementDate = $this->movementDateFromRequest($request);

        if ($movementDate) {
            $query->whereDate('created_at', $movementDate);
        } elseif ($period === 'month') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } elseif ($period === 'year') {
            $query->whereYear('created_at', now()->year);
        }
    }

    private function recordManualMovement(Inventory $inventory, string $type, int $quantityChange, int $quantityBefore, int $quantityAfter, string $note): void
    {
        InventoryMovement::create([
            'inventory_id' => $inventory->id,
            'product_id' => $inventory->product_id,
            'user_id' => auth()->id(),
            'type' => $type,
            'quantity_change' => $quantityChange,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'note' => $note,
        ]);
    }
}

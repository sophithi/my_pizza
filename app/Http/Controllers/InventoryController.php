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
        $query = InventoryMovement::query();
        $this->applyMovementDateFilter($query, $request);

        return [
            'cut_out' => (int) (clone $query)->where('quantity_change', '<', 0)->sum(DB::raw('ABS(quantity_change)')),
            'added_back' => (int) (clone $query)->where('quantity_change', '>', 0)->sum('quantity_change'),
            'products' => (clone $query)->distinct('inventory_id')->count('inventory_id'),
        ];
    }

    private function movementsByInventory(Request $request)
    {
        $query = InventoryMovement::query()
            ->selectRaw('inventory_id, SUM(CASE WHEN quantity_change < 0 THEN ABS(quantity_change) ELSE 0 END) as cut_out')
            ->selectRaw('SUM(CASE WHEN quantity_change > 0 THEN quantity_change ELSE 0 END) as added_back')
            ->selectRaw('MAX(created_at) as last_movement_at')
            ->groupBy('inventory_id');

        $this->applyMovementDateFilter($query, $request);

        return $query->get()->keyBy('inventory_id');
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

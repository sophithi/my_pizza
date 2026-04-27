<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stats = [
            'total' => Inventory::count(),
            'in_stock' => Inventory::whereColumn('quantity', '>', 'reorder_level')->count(),
            'low_stock' => Inventory::where('quantity', '>', 0)
                ->whereColumn('quantity', '<=', 'reorder_level')
                ->count(),
            'out_stock' => Inventory::where('quantity', '<=', 0)->count(),
        ];

        $inventories = Inventory::with('product')->paginate(15);
        return view('inventory.index', compact('inventories', 'stats'));
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
        $inventory = Inventory::create($request->validated());
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
        $inventory->update($request->validated());
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
        $inventory->update(['quantity' => $quantity]);
        return redirect()->route('inventory.index')->with('success', 'បានកែប្រែចំនួនស្តុកដោយជោគជ័យ។');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::latest()->paginate(15);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        return view('purchases.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'reference_number' => 'nullable|unique:purchases',
            'supplier_name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0.01',
            'status' => 'required|in:pending,received,cancelled',
            'notes' => 'nullable|string',
        ]);

        Purchase::create($request->all());

        return redirect()->route('purchases.index')
            ->with('success', 'Purchase recorded successfully.');
    }

    public function show(Purchase $purchase)
    {
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        return view('purchases.edit', compact('purchase'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'reference_number' => 'nullable|unique:purchases,reference_number,' . $purchase->id,
            'supplier_name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0.01',
            'status' => 'required|in:pending,received,cancelled',
            'notes' => 'nullable|string',
        ]);

        $purchase->update($request->all());

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase updated successfully.');
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', 'Purchase deleted successfully.');
    }
}

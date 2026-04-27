<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    private const EXCHANGE_RATE_KHR = 4000;

    public function index(Request $request)
    {
        $query = Purchase::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('purchase_date', $request->date);
        }

        $statsQuery = clone $query;
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'amount' => (clone $statsQuery)->sum('total_amount'),
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
            'paid' => (clone $statsQuery)->where('status', 'received')->count(),
        ];

        $purchases = $query->latest('purchase_date')->latest()->paginate(15)->withQueryString();
        $exchangeRate = self::EXCHANGE_RATE_KHR;

        return view('purchases.index', compact('purchases', 'stats', 'exchangeRate'));
    }

    public function create()
    {
        return view('purchases.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'reference_number' => 'nullable|unique:purchases',
            'supplier_name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0.01',
            'amount_currency' => 'nullable|in:USD,KHR',
            'status' => 'required|in:pending,received,cancelled',
            'notes' => 'nullable|string',
        ]);

        $data['total_amount'] = $this->normalizeAmountToUsd($data['total_amount'], $data['amount_currency'] ?? 'USD');
        unset($data['amount_currency']);

        Purchase::create($data);

        return redirect()->route('purchases.index')
            ->with('success', 'Daily expense recorded successfully.');
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
        $data = $request->validate([
            'reference_number' => 'nullable|unique:purchases,reference_number,' . $purchase->id,
            'supplier_name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0.01',
            'amount_currency' => 'nullable|in:USD,KHR',
            'status' => 'required|in:pending,received,cancelled',
            'notes' => 'nullable|string',
        ]);

        $data['total_amount'] = $this->normalizeAmountToUsd($data['total_amount'], $data['amount_currency'] ?? 'USD');
        unset($data['amount_currency']);

        $purchase->update($data);

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Daily expense updated successfully.');
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', 'Daily expense deleted successfully.');
    }

    private function normalizeAmountToUsd(float $amount, string $currency): float
    {
        if ($currency === 'KHR') {
            return round($amount / self::EXCHANGE_RATE_KHR, 2);
        }

        return round($amount, 2);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Salesperson;
use Illuminate\Http\Request;

class SalespersonController extends Controller
{
    public function index(Request $request)
    {
        $query = Salesperson::withCount('customers');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $salespersons = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('salespersons.index', compact('salespersons'));
    }

    public function create()
    {
        return view('salespersons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|string|in:active,inactive',
        ]);

        Salesperson::create($validated);

        return redirect()->route('salespersons.index')
            ->with('success', 'ភ្នាក់ងារលក់ត្រូវបានបង្កើតដោយជោគជ័យ។');
    }

    public function show(Request $request, Salesperson $salesperson)
    {
        $period = $request->get('period', 'all');
        $start = null;
        $end = null;

        if ($period === 'today') {
            $start = \Carbon\Carbon::today()->startOfDay();
            $end = \Carbon\Carbon::today()->endOfDay();
        } elseif ($period === 'month') {
            $start = \Carbon\Carbon::today()->startOfMonth();
            $end = \Carbon\Carbon::today()->endOfMonth();
        } elseif ($period === 'custom') {
            if ($request->filled('date_from')) {
                $start = \Carbon\Carbon::parse($request->input('date_from'))->startOfDay();
            }
            if ($request->filled('date_to')) {
                $end = \Carbon\Carbon::parse($request->input('date_to'))->endOfDay();
            }
        }

        $customersQuery = $salesperson->customers()
            ->withCount(['orders' => function ($q) use ($start, $end) {
                if ($start) {
                    $q->where('order_date', '>=', $start);
                }
                if ($end) {
                    $q->where('order_date', '<=', $end);
                }
            }])
            ->withSum(['orders as total_spent' => function ($q) use ($start, $end) {
                if ($start) {
                    $q->where('order_date', '>=', $start);
                }
                if ($end) {
                    $q->where('order_date', '<=', $end);
                }
            }], 'total_amount');

        $customers = $customersQuery->orderBy('name')->paginate(15)->withQueryString();

        return view('salespersons.show', compact('salesperson', 'customers'));
    }

    public function edit(Salesperson $salesperson)
    {
        return view('salespersons.edit', compact('salesperson'));
    }

    public function update(Request $request, Salesperson $salesperson)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|string|in:active,inactive',
        ]);

        $salesperson->update($validated);

        return redirect()->route('salespersons.index')
            ->with('success', 'ភ្នាក់ងារលក់ត្រូវបានកែប្រែដោយជោគជ័យ។');
    }

    public function destroy(Salesperson $salesperson)
    {
        // salesperson_id in customers table will automatically set to NULL due to ON DELETE SET NULL migration constraint
        $salesperson->delete();

        return redirect()->route('salespersons.index')
            ->with('success', 'ភ្នាក់ងារលក់ត្រូវបានលុបដោយជោគជ័យ។');
    }
}


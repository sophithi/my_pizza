<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        [$filter, $startDate, $endDate] = $this->resolveDateFilter($request);

        $deliveries = Delivery::withCount([
            'orders as orders_count' => function ($query) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
                }
            }
        ])->latest()->paginate(15)->appends($request->query());

        return view('deliveries.index', compact('deliveries', 'startDate', 'endDate', 'filter'));
    }

    public function create()
    {
        return view('deliveries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'delivery_name' => 'required|string|max:255',
            'delivery_price_khr' => 'required|numeric|min:0',
            'delivery_desc' => 'nullable|string',
        ]);

        Delivery::create($request->only('delivery_name', 'delivery_price_khr', 'delivery_desc'));

        return redirect()->route('deliveries.index')
            ->with('success', 'Created successfully!');
    }

    public function show(Request $request, Delivery $delivery)
    {
        [$filter, $startDate, $endDate] = $this->resolveDateFilter($request);

        $delivery->load([
            'orders' => function ($query) use ($startDate, $endDate) {
                $query->with(['invoice', 'customer'])->latest();

                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($startDate)->startOfDay(),
                        Carbon::parse($endDate)->endOfDay(),
                    ]);
                }
            },
        ]);

        return view('deliveries.show', compact('delivery', 'startDate', 'endDate', 'filter'));
    }

    public function edit(Delivery $delivery)
    {
        return view('deliveries.edit', compact('delivery'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        $request->validate([
            'delivery_name' => 'required|string|max:255',
            'delivery_price_khr' => 'required|numeric|min:0',
            'delivery_desc' => 'nullable|string',
        ]);

        $delivery->update($request->only('delivery_name', 'delivery_price_khr', 'delivery_desc'));

        return redirect()->route('deliveries.index')
            ->with('success', 'Updated successfully!');
    }

    public function destroy(Delivery $delivery)
    {
        $delivery->delete();

        return redirect()->route('deliveries.index')
            ->with('success', 'Deleted successfully!');
    }

    private function resolveDateFilter(Request $request): array
    {
        $filter = $request->query('filter');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if ($filter === 'today') {
            $startDate = Carbon::today()->toDateString();
            $endDate = Carbon::today()->toDateString();
        } elseif ($filter === 'yesterday') {
            $startDate = Carbon::yesterday()->toDateString();
            $endDate = Carbon::yesterday()->toDateString();
        }

        return [$filter, $startDate, $endDate];
    }
}

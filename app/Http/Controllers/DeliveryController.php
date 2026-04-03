<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::with(['order.customer'])->latest()->paginate(15);
        return view('deliveries.index', compact('deliveries'));
    }

    public function create()
    {
        $orders = Order::with('customer')
            ->where('status', '!=', 'cancelled')
            ->doesntHave('delivery')
            ->latest()
            ->get();
        return view('deliveries.create', compact('orders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'delivery_address' => 'required|string|max:255',
            'delivery_phone' => 'nullable|string|max:20',
            'scheduled_delivery_at' => 'required|date',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:20',
            'delivery_fee' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        Delivery::create($request->all());

        return redirect()->route('deliveries.index')
            ->with('success', 'Delivery scheduled successfully.');
    }

    public function show(Delivery $delivery)
    {
        $delivery->load(['order.customer']);
        return view('deliveries.show', compact('delivery'));
    }

    public function edit(Delivery $delivery)
    {
        $delivery->load('order');
        return view('deliveries.edit', compact('delivery'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'delivery_address' => 'required|string|max:255',
            'delivery_phone' => 'nullable|string|max:20',
            'scheduled_delivery_at' => 'required|date',
            'status' => 'required|in:pending,preparing,out_for_delivery,delivered,cancelled',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:20',
            'delivery_fee' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'rejection_reason' => 'nullable|string',
        ]);

        $delivery->update($request->all());

        return redirect()->route('deliveries.show', $delivery)
            ->with('success', 'Delivery updated successfully.');
    }

    public function destroy(Delivery $delivery)
    {
        $delivery->delete();

        return redirect()->route('deliveries.index')
            ->with('success', 'Delivery deleted successfully.');
    }

    /**
     * Mark delivery as out for delivery
     */
    public function markOutForDelivery(Delivery $delivery)
    {
        $delivery->update(['status' => 'out_for_delivery']);

        return back()->with('success', 'Delivery marked as out for delivery.');
    }

    /**
     * Mark delivery as completed
     */
    public function markDelivered(Delivery $delivery)
    {
        $delivery->update([
            'status' => 'delivered',
            'actual_delivery_at' => now(),
        ]);

        return back()->with('success', 'Delivery marked as completed.');
    }

    /**
     * Cancel delivery
     */
    public function cancel(Request $request, Delivery $delivery)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $delivery->update([
            'status' => 'cancelled',
            'rejection_reason' => $request->input('rejection_reason', 'No reason provided'),
        ]);

        return back()->with('success', 'Delivery cancelled.');
    }

    /**
     * Get deliveries for a specific date (for dashboard/calendar)
     */
    public function byDate(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        
        $deliveries = Delivery::whereDate('scheduled_delivery_at', $date)
            ->with('order')
            ->orderBy('scheduled_delivery_at')
            ->get();

        return view('deliveries.by-date', compact('deliveries', 'date'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Order::with('customer')->latest();

        if (request('status')) {
            $query->where('status', request('status'));
        }

        $orders = $query->paginate(15)->withQueryString();
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = \App\Models\Customer::all();
        $products = \App\Models\Product::all();
        $deliveries = \App\Models\Delivery::all();
        return view('orders.create', compact('customers', 'products', 'deliveries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();
        
        // Create the order
        $order = Order::create([
            'customer_id' => $validated['customer_id'],
            'user_id' => auth()->id(),
            'order_date' => $validated['order_date'],
            'subtotal' => $validated['subtotal'],
            'discount_amount' => $validated['discount_amount'] ?? 0,
            'total_amount' => $validated['total_amount'],
            'status' => $validated['status'] ?? 'pending',
            'payment_status' => $validated['payment_status'] ?? 'unpaid',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Parse and create order items
        $orderItems = json_decode($validated['order_items'], true);
        foreach ($orderItems as $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'delivery_id' => $item['delivery_id'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price'],
            ]);

            // Decrement inventory quantity
            $inventory = \App\Models\Inventory::where('product_id', $item['product_id'])->first();
            if ($inventory) {
                $inventory->decrement('quantity', $item['quantity']);
            }
        }

        return redirect()->route('orders.show', $order)->with('success', 'Order created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load('customer', 'items.product', 'items.delivery', 'preparer');
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $customers = \App\Models\Customer::all();
        return view('orders.edit', compact('order', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->update($request->validated());
        return redirect()->route('orders.show', $order)->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        // Restore inventory quantities when order is deleted
        foreach ($order->items as $item) {
            $inventory = \App\Models\Inventory::where('product_id', $item->product_id)->first();
            if ($inventory) {
                $inventory->increment('quantity', $item->quantity);
            }
        }
        
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }

    /**
     * Mark order as processing (being prepared).
     */
    public function prepare(Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'មានតែការបញ្ជាទិញដែលកំពុងរង់ចាំប៉ុណ្ណោះដែលអាចរៀបចំបាន។');
        }

        $order->update([
            'status' => 'processing',
            'prepared_by' => auth()->id(),
            'prepared_at' => now(),
        ]);

        return back()->with('success', 'ការបញ្ជាទិញកំពុងរៀបចំ។');
    }

    /**
     * Mark order as completed (ready).
     */
    public function ready(Order $order)
    {
        if ($order->status !== 'processing') {
            return back()->with('error', 'មានតែការបញ្ជាទិញដែលកំពុងដំណើរការប៉ុណ្ណោះដែលអាចបញ្ចប់បាន។');
        }

        $order->update([
            'status' => 'completed',
        ]);

        return back()->with('success', 'ការបញ្ជាទិញបានបញ្ចប់។');
    }
}

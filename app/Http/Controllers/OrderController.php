<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Notifications\NewOrderNotification;
use App\Models\User;
use App\Models\Delivery;

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
        $selectedCustomerId = request('customer_id');
        return view('orders.create', compact('customers', 'products', 'deliveries', 'selectedCustomerId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();
        $delivery = !empty($validated['delivery_id']) ? Delivery::find($validated['delivery_id']) : null;
        $deliveryFeeKhr = $delivery ? (float) $delivery->delivery_price_khr : 0;
        $deliveryFeeUsd = round($deliveryFeeKhr / 4000, 2);
        $subtotal = (float) $validated['subtotal'];
        $discountAmount = (float) ($validated['discount_amount'] ?? 0);
        $totalAmount = round($subtotal + $deliveryFeeUsd, 2);
        
        // Create the order
        $order = Order::create([
            'customer_id' => $validated['customer_id'],
            'delivery_id' => $delivery?->id,
            'user_id' => auth()->id(),
            'order_date' => $validated['order_date'],
            'code' => 'ORD-' . rand(1000, 9999),
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'delivery_fee_khr' => $deliveryFeeKhr,
            'delivery_fee_usd' => $deliveryFeeUsd,
            'total_amount' => $totalAmount,
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
                'delivery_id' => $delivery?->id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price'],
            ]);
        }

        // Auto-create invoice
        $lastInvoice = \App\Models\Invoice::orderByRaw("CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED) DESC")->first();
        $nextNumber = $lastInvoice ? (int) substr($lastInvoice->invoice_number, 4) + 1 : 1;
        $invoiceNumber = 'INV-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        \App\Models\Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => now()->toDateString(),
            'subtotal' => $order->subtotal,
            'discount_amount' => $order->discount_amount,
            'delivery_fee_khr' => $order->delivery_fee_khr,
            'delivery_fee_usd' => $order->delivery_fee_usd,
            'total_amount' => $order->total_amount,
            'notes' => $order->notes ?? null,
        ]);

        // Send notification to inventory staff + admin
        $users = User::whereIn('role', ['inventory', 'admin'])->get();
        foreach ($users as $user) {
            $user->notify(new NewOrderNotification($order));
        }

        return redirect()->route('orders.show', $order)->with('success', 'Order created successfully with invoice ' . $invoiceNumber . '.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load('customer', 'delivery', 'items.product', 'items.delivery', 'preparer');
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
        // Only restore inventory if order was already prepared (stock was deducted)
        if (in_array($order->status, ['processing', 'completed'])) {
            foreach ($order->items as $item) {
                $inventory = \App\Models\Inventory::where('product_id', $item->product_id)->first();
                if ($inventory) {
                    $inventory->increment('quantity', $item->quantity);
                }
            }
        }
        
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }

    /**
     * Mark order as processing (being prepared) and deduct stock.
     */
    public function prepare(Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'មានតែការបញ្ជាទិញដែលកំពុងរង់ចាំប៉ុណ្ណោះដែលអាចរៀបចំបាន។');
        }

        $order->load('items.product');
        $warnings = [];

        // Deduct inventory for each order item
        foreach ($order->items as $item) {
            $inventory = \App\Models\Inventory::where('product_id', $item->product_id)->first();
            if ($inventory) {
                $inventory->decrement('quantity', $item->quantity);
                if ($inventory->fresh()->quantity < 0) {
                    $warnings[] = $item->product->name . ' ស្តុកអស់ (នៅសល់: ' . $inventory->fresh()->quantity . ')';
                } elseif ($inventory->fresh()->quantity <= 5) {
                    $warnings[] = $item->product->name . ' ស្តុកជិតអស់ (នៅសល់: ' . $inventory->fresh()->quantity . ')';
                }
            }
        }

        $order->update([
            'status' => 'processing',
            'prepared_by' => auth()->id(),
            'prepared_at' => now(),
        ]);

        $message = 'ការបញ្ជាទិញកំពុងរៀបចំ។ ស្តុកត្រូវបានកាត់រួចហើយ។';
        if (!empty($warnings)) {
            return back()->with('success', $message)->with('stockWarnings', $warnings);
        }

        return back()->with('success', $message);
    }

    /**
     * Mark order as completed (ready) and auto-create invoice.
     */
    public function ready(Order $order)
    {
        if ($order->status !== 'processing') {
            return back()->with('error', 'មានតែការបញ្ជាទិញដែលកំពុងដំណើរការប៉ុណ្ណោះដែលអាចបញ្ចប់បាន។');
        }

        $order->update([
            'status' => 'completed',
        ]);

        // Auto-create invoice if not already created
        if (!$order->invoice) {
            $lastInvoice = \App\Models\Invoice::orderByRaw("CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED) DESC")->first();
            $nextNumber = $lastInvoice ? (int) substr($lastInvoice->invoice_number, 4) + 1 : 1;
            $invoiceNumber = 'INV-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            \App\Models\Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => now()->toDateString(),
                'subtotal' => $order->subtotal,
                'discount_amount' => $order->discount_amount,
                'delivery_fee_khr' => $order->delivery_fee_khr,
                'delivery_fee_usd' => $order->delivery_fee_usd,
                'total_amount' => $order->total_amount,
                'notes' => $order->notes ?? null,
            ]);

            return back()->with('success', 'ការបញ្ជាទិញបានបញ្ចប់ និងវិក្ក័យបត្រ ' . $invoiceNumber . ' បានបង្កើត។');
        }

        return back()->with('success', 'ការបញ្ជាទិញបានបញ្ចប់។');
    }
}

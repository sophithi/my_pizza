<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Delivery;
use Illuminate\Support\Facades\DB;

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

        [$order, $invoiceNumber, $warnings] = DB::transaction(function () use ($validated, $delivery, $deliveryFeeKhr, $deliveryFeeUsd, $subtotal, $discountAmount, $totalAmount) {
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

            $warnings = $this->deductInventoryForOrder($order);

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

            return [$order, $invoiceNumber, $warnings];
        });

        $redirect = redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order created successfully with invoice ' . $invoiceNumber . '. Stock has been deducted.');

        if (!empty($warnings)) {
            $redirect->with('stockWarnings', $warnings);
        }

        return $redirect;
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
        $order->load('items.product', 'customer', 'delivery', 'invoice');
        $customers = \App\Models\Customer::all();
        $products = \App\Models\Product::all();
        $deliveries = \App\Models\Delivery::all();

        return view('orders.edit', compact('order', 'customers', 'products', 'deliveries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $validated = $request->validated();
        $delivery = !empty($validated['delivery_id']) ? Delivery::find($validated['delivery_id']) : null;
        $deliveryFeeKhr = $delivery ? (float) $delivery->delivery_price_khr : 0;
        $deliveryFeeUsd = round($deliveryFeeKhr / 4000, 2);
        $subtotal = (float) $validated['subtotal'];
        $discountAmount = (float) ($validated['discount_amount'] ?? 0);
        $totalAmount = round($subtotal + $deliveryFeeUsd, 2);

        DB::transaction(function () use ($order, $validated, $delivery, $deliveryFeeKhr, $deliveryFeeUsd, $subtotal, $discountAmount, $totalAmount) {
            $wasStockDeducted = (bool) $order->stock_deducted;

            if ($wasStockDeducted) {
                $this->restoreInventoryForOrder($order);
            }

            $order->update([
                'customer_id' => $validated['customer_id'],
                'delivery_id' => $delivery?->id,
                'order_date' => $validated['order_date'],
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'delivery_fee_khr' => $deliveryFeeKhr,
                'delivery_fee_usd' => $deliveryFeeUsd,
                'total_amount' => $totalAmount,
                'payment_status' => $validated['payment_status'] ?? $order->payment_status,
                'notes' => $validated['notes'] ?? null,
                'stock_deducted' => false,
            ]);

            $order->items()->delete();

            foreach (json_decode($validated['order_items'], true) as $item) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'delivery_id' => $delivery?->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);
            }

            if ($wasStockDeducted) {
                $this->deductInventoryForOrder($order->fresh('items.product'));
            }

            if ($order->invoice) {
                $order->invoice->update([
                    'subtotal' => $order->subtotal,
                    'discount_amount' => $order->discount_amount,
                    'delivery_fee_khr' => $order->delivery_fee_khr,
                    'delivery_fee_usd' => $order->delivery_fee_usd,
                    'total_amount' => $order->total_amount,
                    'packing_sent_at' => null,
                    'packing_completed_at' => null,
                    'notes' => $order->notes ?? null,
                ]);
            }
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order updated successfully. Please send it to packing again.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        DB::transaction(function () use ($order) {
            $this->restoreInventoryForOrder($order);
            $order->delete();
        });

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

        $warnings = DB::transaction(function () use ($order) {
            $warnings = $this->deductInventoryForOrder($order);

            $order->update([
                'status' => 'processing',
                'prepared_by' => auth()->id(),
                'prepared_at' => now(),
            ]);

            return $warnings;
        });

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

    private function deductInventoryForOrder(Order $order): array
    {
        if ($order->stock_deducted) {
            return [];
        }

        $order->loadMissing('items.product');
        $warnings = [];
        $quantitiesByProduct = $order->items
            ->groupBy('product_id')
            ->map(fn($items) => (int) $items->sum('quantity'));

        foreach ($quantitiesByProduct as $productId => $quantity) {
            $inventory = Inventory::where('product_id', $productId)->lockForUpdate()->first();
            if (!$inventory) {
                continue;
            }

            $before = (int) $inventory->quantity;
            $inventory->decrement('quantity', $quantity);
            $remaining = (int) $inventory->fresh()->quantity;
            $this->recordInventoryMovement($inventory, $order, 'order_deduct', -$quantity, $before, $remaining);
            $productName = $order->items->firstWhere('product_id', $productId)?->product?->name ?? 'Product';

            if ($remaining < 0) {
                $warnings[] = $productName . ' ស្តុកអស់ (នៅសល់: ' . $remaining . ')';
            } elseif ($remaining <= (int) $inventory->reorder_level) {
                $warnings[] = $productName . ' ស្តុកជិតអស់ (នៅសល់: ' . $remaining . ')';
            }
        }

        $order->update(['stock_deducted' => true]);

        return $warnings;
    }

    private function restoreInventoryForOrder(Order $order): void
    {
        if (!$order->stock_deducted) {
            return;
        }

        $order->loadMissing('items');
        $quantitiesByProduct = $order->items
            ->groupBy('product_id')
            ->map(fn($items) => (int) $items->sum('quantity'));

        foreach ($quantitiesByProduct as $productId => $quantity) {
            $inventory = Inventory::where('product_id', $productId)->lockForUpdate()->first();
            if ($inventory) {
                $before = (int) $inventory->quantity;
                $inventory->increment('quantity', $quantity);
                $after = (int) $inventory->fresh()->quantity;
                $this->recordInventoryMovement($inventory, $order, 'order_restore', $quantity, $before, $after);
            }
        }

        $order->update(['stock_deducted' => false]);
    }

    private function recordInventoryMovement(
        Inventory $inventory,
        Order $order,
        string $type,
        int $quantityChange,
        int $quantityBefore,
        int $quantityAfter
    ): void {
        InventoryMovement::create([
            'inventory_id' => $inventory->id,
            'product_id' => $inventory->product_id,
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'type' => $type,
            'quantity_change' => $quantityChange,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'note' => $type === 'order_deduct'
                ? 'Stock deducted by order #' . $order->id
                : 'Stock restored by order edit/delete #' . $order->id,
        ]);
    }
}

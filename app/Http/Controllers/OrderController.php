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
        return
            view(
                'orders.create',
                compact('customers', 'products', 'deliveries', 'selectedCustomerId')
            );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();
        $delivery = !empty($validated['delivery_id']) ? Delivery::find($validated['delivery_id']) : null;
        $smallPackQty = max((int) ($validated['small_pack_qty'] ?? 1), 0);
        $bigPackQty = max((int) ($validated['big_pack_qty'] ?? 0), 0);
        $boxQty = max($smallPackQty + $bigPackQty, 1);
        $deliveryFeeKhr = $delivery
            ? ($smallPackQty * (float) $delivery->delivery_price_khr) + ($bigPackQty * (float) $delivery->delivery_price_khr_big)
            : 0;
        $deliveryFeeUsd = round($deliveryFeeKhr / 4000, 2);
        $orderItems = json_decode($validated['order_items'], true) ?? [];
        ['gross' => $grossSubtotalKhr, 'discount' => $itemDiscountKhr] = $this->summarizeItemsKhr($orderItems);
        $netSubtotalKhr = $grossSubtotalKhr - $itemDiscountKhr;
        $totalKhr = $netSubtotalKhr + $deliveryFeeKhr;
        // USD figures are derived from the exact KHR totals with a single rounding
        // step at the end, rather than trusting/summing already-rounded per-item
        // USD amounts from the client — keeps every page's USD/KHR pair consistent.
        $subtotal = round($netSubtotalKhr / 4000, 2);
        $discountAmount = round($itemDiscountKhr / 4000, 2);
        $totalAmount = round($totalKhr / 4000, 2);

        [$order, $invoiceNumber, $warnings] = DB::transaction(function () use ($validated, $delivery, $boxQty, $smallPackQty, $bigPackQty, $deliveryFeeKhr, $deliveryFeeUsd, $subtotal, $discountAmount, $totalAmount, $orderItems) {
            // Create the order
            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'delivery_id' => $delivery?->id,
                'taxi_phone' => $validated['taxi_phone'] ?? null,
                'box_qty' => $boxQty,
                'small_pack_qty' => $smallPackQty,
                'big_pack_qty' => $bigPackQty,
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

            // Create order items
            foreach ($orderItems as $item) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'delivery_id' => $delivery?->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'unit_price_khr' => $item['unit_price_khr'] ?? null,
                    'total_price' => $item['total_price'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'is_custom_price' => $item['is_custom_price'] ?? false,
                ]);
            }

            foreach (($validated['free_products'] ?? []) as $freeProduct) {
                if (empty($freeProduct['product_id']) || empty($freeProduct['qty'])) {
                    continue;
                }

                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $freeProduct['product_id'],
                    'delivery_id' => $delivery?->id,
                    'quantity' => (int) $freeProduct['qty'],
                    'unit_price' => 0,
                    'total_price' => 0,
                ]);
            }

            $warnings = $this->deductInventoryForOrder($order);

            // Auto-create invoice
            $invoice = \App\Models\Invoice::createUnique([
                'order_id' => $order->id,
                'invoice_date' => now()->toDateString(),
                'subtotal' => $order->subtotal,
                'discount_amount' => $order->discount_amount,
                'delivery_fee_khr' => $order->delivery_fee_khr,
                'delivery_fee_usd' => $order->delivery_fee_usd,
                'total_amount' => $order->total_amount,
                'status' => $order->payment_status === 'paid' ? 'paid' : 'draft',
                'notes' => $order->notes ?? null,
            ]);
            $invoiceNumber = $invoice->invoice_number;

            // Create payment record if order is marked as paid
            if ($validated['payment_status'] === 'paid') {
                $methodStr = $validated['payment_method'] ?? 'Cash';
                $methods = array_values(array_filter(array_map('trim', explode('+', $methodStr ?: 'Cash'))));
                if (empty($methods)) {
                    $methods = ['Cash'];
                }

                $payment = \App\Models\Payment::create([
                    'order_id' => $order->id,
                    'customer_name' => $order->customer?->name ?? 'Walk-in Customer',
                    'order_date' => $order->order_date,
                    'total_amount' => $order->total_amount,
                    'total_amount_khr' => $order->totalKhr(),
                    'paid_amount' => $order->total_amount,
                    'paid_amount_khr' => $order->totalKhr(),
                    'method' => implode(' + ', $methods),
                    'status' => 'paid',
                ]);

                // Sync created_at to order_date
                if ($order->order_date) {
                    $payment->created_at = \Carbon\Carbon::parse($order->order_date);
                    $payment->save();
                }

                $splitCount = count($methods);
                $perMethodUsd = round($order->total_amount / $splitCount, 2);
                $lines = [];

                foreach ($methods as $i => $m) {
                    $isLast = ($i === $splitCount - 1);
                    $lineUsd = $isLast ? ($order->total_amount - ($perMethodUsd * ($splitCount - 1))) : $perMethodUsd;
                    $payment->lines()->create([
                        'method' => $m,
                        'currency' => 'USD',
                        'amount_original' => $lineUsd,
                        'amount_usd' => $lineUsd,
                        'exchange_rate' => 4000,
                    ]);
                    $lines[] = sprintf("%s: $%.2f USD", $m, $lineUsd);
                }

                $payment->update(['notes' => 'Paid: ' . implode(' + ', $lines)]);
            }

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
        $order->load('items.product', 'customer', 'delivery', 'invoice', 'payments');
        $customers = \App\Models\Customer::all();
        $products = \App\Models\Product::all();
        $deliveries = \App\Models\Delivery::all();

        // Existing payment method(s), e.g. "Cash + ABA", split back into an array
        // so the edit form can pre-check the same buttons shown at order creation.
        $existingPaymentMethod = optional($order->payments->first())->method ?? '';
        $existingPaymentMethods = $existingPaymentMethod && $existingPaymentMethod !== '—'
            ? array_values(array_filter(array_map('trim', explode('+', $existingPaymentMethod))))
            : [];

        // Prepare data for JSON encoding
        $existingOrderItems = $order->paidItems->map(fn($item) => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'unit_price_khr' => $item->unit_price_khr,
            'discount_percent' => $item->discount_percent ?? 0,
            'is_custom_price' => $item->is_custom_price,
        ])->values();

        $existingFreeProducts = $order->freeItems->map(fn($fp) => [
            'product_id' => $fp->product_id,
            'qty' => $fp->quantity ?? 1,
        ])->values();

        $deliveryOptions = $deliveries->map(fn($d) => [
            'id' => $d->id,
            'name' => $d->delivery_name,
            'price_khr' => $d->delivery_price_khr,
            'price_khr_big' => $d->delivery_price_khr_big,
        ])->values();

        // allProducts as keyed object (for lookup by ID)
        $allProducts = $products->keyBy('id')->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'price_usd' => $p->price_usd,
            'price_khr' => $p->price_khr,
            'image_url' => $p->imageUrl(),
        ]);

        // productsArray for dropdowns (regular array)
        $productsArray = $products->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
        ])->values();

        return view('orders.edit', compact(
            'order',
            'customers',
            'products',
            'deliveries',
            'existingOrderItems',
            'existingFreeProducts',
            'existingPaymentMethods',
            'deliveryOptions',
            'allProducts',
            'productsArray'
        ));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $validated = $request->validated();
        $delivery = !empty($validated['delivery_id']) ? Delivery::find($validated['delivery_id']) : null;
        $smallPackQty = max((int) ($validated['small_pack_qty'] ?? 1), 0);
        $bigPackQty = max((int) ($validated['big_pack_qty'] ?? 0), 0);
        $boxQty = max($smallPackQty + $bigPackQty, 1);
        $deliveryFeeKhr = $delivery
            ? ($smallPackQty * (float) $delivery->delivery_price_khr) + ($bigPackQty * (float) $delivery->delivery_price_khr_big)
            : 0;
        $deliveryFeeUsd = round($deliveryFeeKhr / 4000, 2);
        $orderItems = json_decode($validated['order_items'], true) ?? [];
        ['gross' => $grossSubtotalKhr, 'discount' => $itemDiscountKhr] = $this->summarizeItemsKhr($orderItems);
        $netSubtotalKhr = $grossSubtotalKhr - $itemDiscountKhr;
        $totalKhr = $netSubtotalKhr + $deliveryFeeKhr;
        $subtotal = round($netSubtotalKhr / 4000, 2);
        $discountAmount = round($itemDiscountKhr / 4000, 2);
        $totalAmount = round($totalKhr / 4000, 2);

        $packingRelevantChanged = false;

        DB::transaction(function () use ($order, $validated, $delivery, $boxQty, $smallPackQty, $bigPackQty, $deliveryFeeKhr, $deliveryFeeUsd, $subtotal, $discountAmount, $totalAmount, $totalKhr, $orderItems, &$packingRelevantChanged) {
            $wasStockDeducted = (bool) $order->stock_deducted;
            $originalItemsSignature = $this->itemsSignature($order->items()->get(['product_id', 'quantity', 'unit_price', 'discount_percent']));

            if ($wasStockDeducted) {
                $this->restoreInventoryForOrder($order);
            }

            $order->update([
                'customer_id' => $validated['customer_id'],
                'delivery_id' => $delivery?->id,
                'taxi_phone' => $validated['taxi_phone'] ?? null,
                'box_qty' => $boxQty,
                'small_pack_qty' => $smallPackQty,
                'big_pack_qty' => $bigPackQty,
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

            // Keep the payment record's method in sync with what was chosen on
            // this form, mirroring how store() records it when an order is first
            // marked paid — otherwise an edited Cash/ABA/etc. selection is silently
            // discarded and the edit form has nothing to show next time. Uses the
            // $totalKhr computed above (not $order->totalKhr()) because the items
            // relation may already be cached with the pre-edit items at this point
            // (loaded by restoreInventoryForOrder() above when stock was deducted).
            if (($validated['payment_status'] ?? $order->payment_status) === 'paid') {
                $methodStr = $validated['payment_method'] ?? 'Cash';
                $methods = array_values(array_filter(array_map('trim', explode('+', $methodStr ?: 'Cash'))));
                if (empty($methods)) {
                    $methods = ['Cash'];
                }

                $payment = \App\Models\Payment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'customer_name' => $order->customer?->name ?? 'Walk-in Customer',
                        'order_date' => $order->order_date,
                        'total_amount' => $totalAmount,
                        'total_amount_khr' => $totalKhr,
                        'paid_amount' => $totalAmount,
                        'paid_amount_khr' => $totalKhr,
                        'method' => implode(' + ', $methods),
                        'status' => 'paid',
                    ]
                );

                if ($order->order_date) {
                    $payment->created_at = \Carbon\Carbon::parse($order->order_date);
                    $payment->save();
                }

                // If lines don't exist or methods changed, sync lines
                $splitCount = count($methods);
                $perMethodUsd = round($totalAmount / $splitCount, 2);
                $payment->lines()->delete();
                $lines = [];

                foreach ($methods as $i => $m) {
                    $isLast = ($i === $splitCount - 1);
                    $lineUsd = $isLast ? ($totalAmount - ($perMethodUsd * ($splitCount - 1))) : $perMethodUsd;
                    $payment->lines()->create([
                        'method' => $m,
                        'currency' => 'USD',
                        'amount_original' => $lineUsd,
                        'amount_usd' => $lineUsd,
                        'exchange_rate' => 4000,
                    ]);
                    $lines[] = sprintf("%s: $%.2f USD", $m, $lineUsd);
                }

                $payment->update(['notes' => 'Paid: ' . implode(' + ', $lines)]);
            }

            $order->items()->delete();

            foreach ($orderItems as $item) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'delivery_id' => $delivery?->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'unit_price_khr' => $item['unit_price_khr'] ?? null,
                    'total_price' => $item['total_price'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'is_custom_price' => $item['is_custom_price'] ?? false,
                ]);
            }

            foreach (($validated['free_products'] ?? []) as $freeProduct) {
                if (empty($freeProduct['product_id']) || empty($freeProduct['qty'])) {
                    continue;
                }

                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $freeProduct['product_id'],
                    'delivery_id' => $delivery?->id,
                    'quantity' => (int) $freeProduct['qty'],
                    'unit_price' => 0,
                    'total_price' => 0,
                ]);
            }

            if ($wasStockDeducted) {
                $this->deductInventoryForOrder($order->fresh('items.product'));
            }

            $newItemsSignature = $this->itemsSignature($order->items()->get(['product_id', 'quantity', 'unit_price', 'discount_percent']));
            $packingRelevantChanged = $order->wasChanged(['customer_id', 'delivery_id', 'taxi_phone', 'small_pack_qty', 'big_pack_qty', 'notes'])
                || $originalItemsSignature !== $newItemsSignature;

            if ($order->invoice) {
                $invoiceUpdate = [
                    'subtotal' => $order->subtotal,
                    'discount_amount' => $order->discount_amount,
                    'delivery_fee_khr' => $order->delivery_fee_khr,
                    'delivery_fee_usd' => $order->delivery_fee_usd,
                    'total_amount' => $order->total_amount,
                    'status' => $order->invoice->status === 'cancelled'
                        ? 'cancelled'
                        : ($order->payment_status === 'paid' ? 'paid' : 'draft'),
                    'notes' => $order->notes ?? null,
                ];

                // Only pull the invoice out of the packing queue when something a
                // packer would actually see (items, delivery, pack qty, notes...)
                // changed — a payment-status or order-date-only edit shouldn't
                // silently drop an already-packed invoice off packing/index.
                if ($packingRelevantChanged) {
                    $invoiceUpdate['packing_sent_at'] = null;
                    $invoiceUpdate['packing_completed_at'] = null;
                }

                $order->invoice->update($invoiceUpdate);
            }
        });

        $message = 'Order updated successfully.';
        if ($order->invoice && $packingRelevantChanged) {
            $message .= ' Please send it to packing again.';
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', $message);
    }

    /**
     * Build an order-independent signature for a set of order items so we can
     * detect whether the packable contents of an order actually changed.
     */
    private function itemsSignature(\Illuminate\Support\Collection $items): string
    {
        return $items
            ->map(fn($item) => "{$item->product_id}:{$item->quantity}:{$item->unit_price}:{$item->discount_percent}")
            ->sort()
            ->values()
            ->implode('|');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        DB::transaction(function () use ($order) {
            $this->restoreInventoryForOrder($order);

            // Order and invoice are always trashed/restored as a pair (see
            // InvoiceController::destroy) — cascade here too so deleting from
            // the order side never leaves an active invoice pointing at a
            // trashed order.
            if ($invoice = $order->invoice) {
                $invoice->update(['deleted_by' => auth()->id()]);
                $invoice->delete();
            }

            $order->update(['deleted_by' => auth()->id()]);
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
            $invoice = \App\Models\Invoice::createUnique([
                'order_id' => $order->id,
                'invoice_date' => now()->toDateString(),
                'subtotal' => $order->subtotal,
                'discount_amount' => $order->discount_amount,
                'delivery_fee_khr' => $order->delivery_fee_khr,
                'delivery_fee_usd' => $order->delivery_fee_usd,
                'total_amount' => $order->total_amount,
                'status' => $order->payment_status === 'paid' ? 'paid' : 'draft',
                'notes' => $order->notes ?? null,
            ]);
            $invoiceNumber = $invoice->invoice_number;

            return back()->with('success', 'ការបញ្ជាទិញបានបញ្ចប់ និងវិក្ក័យបត្រ ' . $invoiceNumber . ' បានបង្កើត។');
        }

        return back()->with('success', 'ការបញ្ជាទិញបានបញ្ចប់។');
    }

    /**
     * Sum gross KHR value and KHR discount across a decoded order_items
     * payload, mirroring Order::grossSubtotalKhr()/itemDiscountKhr() so
     * the total computed at store/update time always agrees with the
     * canonical figure shown on every other page.
     */
    private function summarizeItemsKhr(array $orderItems): array
    {
        $gross = 0.0;
        $discount = 0.0;

        foreach ($orderItems as $item) {
            $unitKhr = isset($item['unit_price_khr']) && $item['unit_price_khr'] !== null
                ? (float) $item['unit_price_khr']
                : (float) ($item['unit_price'] ?? 0) * 4000;
            $lineGrossKhr = $unitKhr * (float) ($item['quantity'] ?? 0);
            $discPercent = (float) ($item['discount_percent'] ?? 0);

            $gross += $lineGrossKhr;
            $discount += $lineGrossKhr * ($discPercent / 100);
        }

        return ['gross' => $gross, 'discount' => $discount];
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

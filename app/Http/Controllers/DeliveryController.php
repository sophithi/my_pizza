<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
            'delivery_price_khr_big' => 'nullable|numeric|min:0',
            'delivery_desc' => 'nullable|string',
        ]);

        Delivery::create($request->only('delivery_name', 'delivery_price_khr', 'delivery_price_khr_big', 'delivery_desc')
            + ['show_invoice_info' => $request->boolean('show_invoice_info')]);

        return redirect()->route('deliveries.index')
            ->with('success', 'Created successfully!');
    }

    public function show(Request $request, Delivery $delivery)
    {
        [$filter, $startDate, $endDate] = $this->resolveDateFilter($request);

        $delivery->load([
            'orders' => function ($query) use ($startDate, $endDate) {
                $query->with(['invoice', 'customer', 'items'])->latest();

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
            'delivery_price_khr_big' => 'nullable|numeric|min:0',
            'delivery_desc' => 'nullable|string',
        ]);

        $delivery->update($request->only('delivery_name', 'delivery_price_khr', 'delivery_price_khr_big', 'delivery_desc')
            + ['show_invoice_info' => $request->boolean('show_invoice_info')]);

        return redirect()->route('deliveries.index')
            ->with('success', 'Updated successfully!');
    }

    public function destroy(Delivery $delivery)
    {
        $delivery->delete();

        return redirect()->route('deliveries.index')
            ->with('success', 'Deleted successfully!');
    }

    // ─── Inline packing qty update (delivery show page) ────────────────────────

    public function updateOrderPacking(Request $request, Delivery $delivery, Order $order)
    {
        abort_unless($order->delivery_id === $delivery->id, 404);

        $validated = $request->validate([
            'small_pack_qty' => 'required|integer|min:0',
            'big_pack_qty' => 'required|integer|min:0',
            'address' => 'nullable|string|max:500',
        ]);

        $deliveryFeeKhr = ($validated['small_pack_qty'] * (float) $delivery->delivery_price_khr)
            + ($validated['big_pack_qty'] * (float) $delivery->delivery_price_khr_big);
        $deliveryFeeUsd = round($deliveryFeeKhr / 4000, 2);

        DB::transaction(function () use ($order, $request, $validated, $deliveryFeeKhr, $deliveryFeeUsd) {
            $order->loadMissing('items');
            // KHR is the ground truth: total_amount must be derived the same way
            // store()/update() derive it (totalKhr() = gross - item discount +
            // delivery fee), otherwise this row's total silently drifts from what
            // the invoice/orders pages show.
            $totalKhr = $order->grossSubtotalKhr() - $order->itemDiscountKhr() + $deliveryFeeKhr;
            $totalAmount = round($totalKhr / 4000, 2);

            $order->update([
                'small_pack_qty' => $validated['small_pack_qty'],
                'big_pack_qty' => $validated['big_pack_qty'],
                'box_qty' => max($validated['small_pack_qty'] + $validated['big_pack_qty'], 1),
                'delivery_fee_khr' => $deliveryFeeKhr,
                'delivery_fee_usd' => $deliveryFeeUsd,
                'total_amount' => $totalAmount,
            ]);

            if ($order->customer && $request->has('address')) {
                $order->customer->update(['address' => $validated['address']]);
            }

            if ($order->invoice) {
                $order->invoice->update([
                    'delivery_fee_khr' => $deliveryFeeKhr,
                    'delivery_fee_usd' => $deliveryFeeUsd,
                    'total_amount' => $totalAmount,
                ]);
            }
        });

        return response()->json([
            'small_pack_qty' => $order->small_pack_qty,
            'big_pack_qty' => $order->big_pack_qty,
            'delivery_fee_khr' => (float) $order->delivery_fee_khr,
            'delivery_fee_usd' => (float) $order->delivery_fee_usd,
            'total_amount' => (float) $order->total_amount,
            'address' => $order->customer?->address,
            'customer_id' => $order->customer_id,
        ]);
    }

    // ─── Export ─────────────────────────────────────────────────────────────────

    public function exportExcel(Request $request, Delivery $delivery)
    {
        [, $startDate, $endDate] = $this->resolveDateFilter($request);

        $delivery->load(['orders' => function ($query) use ($startDate, $endDate) {
            $query->with(['invoice', 'customer'])->latest();

            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ]);
            }
        }]);

        return view('deliveries.export', compact('delivery', 'startDate', 'endDate'));
    }

    public function exportPdf(Request $request, Delivery $delivery)
    {
        [, $startDate, $endDate] = $this->resolveDateFilter($request);

        $delivery->load(['orders' => function ($query) use ($startDate, $endDate) {
            $query->with(['invoice', 'customer'])->latest();

            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ]);
            }
        }]);

        return view('deliveries.pdf', compact('delivery', 'startDate', 'endDate'));
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

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

    public function map(Request $request)
    {
        $depot = \App\Http\Controllers\CustomerLocationController::getStoreLocation();
        $deliveries = Delivery::orderBy('delivery_name')->get();
        
        $dateFilter = $request->get('date_filter', 'today');
        $date = $request->get('date', ($dateFilter === 'today' ? Carbon::today()->toDateString() : ''));
        $deliveryId = $request->get('delivery_id');

        $query = Order::with(['customer', 'delivery', 'items.product', 'invoice'])
            ->whereNull('deleted_at');

        if ($deliveryId) {
            $query->where('delivery_id', $deliveryId);
        }

        if ($dateFilter === 'active') {
            // All active/unfulfilled orders
            $query->whereIn('status', ['pending', 'processing', 'delivering']);
        } elseif ($dateFilter === 'today') {
            // Orders today OR any order currently out for delivery/processing or updated today
            $query->where(function ($q) {
                $q->whereDate('order_date', Carbon::today())
                  ->orWhereIn('status', ['delivering', 'processing'])
                  ->orWhereDate('updated_at', Carbon::today());
            });
        } elseif ($dateFilter === 'yesterday') {
            $query->where(function ($q) {
                $q->whereDate('order_date', Carbon::yesterday())
                  ->orWhereDate('updated_at', Carbon::yesterday());
            });
        } elseif ($dateFilter === 'this_week') {
            $query->where(function ($q) {
                $q->whereBetween('order_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                  ->orWhereIn('status', ['delivering', 'processing'])
                  ->orWhereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            });
        } elseif ($request->filled('date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('order_date', $request->date)
                  ->orWhere(function ($sub) use ($request) {
                      $sub->whereDate('updated_at', $request->date)
                          ->whereIn('status', ['delivering', 'processing']);
                  });
            });
        }

        $orders = $query->latest('id')->get();
        $activeDeliveries = $this->formatOrdersForMap($orders);

        return view('deliveries.map', compact('depot', 'deliveries', 'activeDeliveries', 'date', 'dateFilter', 'deliveryId'));
    }

    public function liveData(Request $request)
    {
        $dateFilter = $request->get('date_filter', 'today');
        $date = $request->get('date');
        $deliveryId = $request->get('delivery_id');

        $query = Order::with(['customer', 'delivery', 'items.product', 'invoice'])
            ->whereNull('deleted_at');

        if ($deliveryId) {
            $query->where('delivery_id', $deliveryId);
        }

        if ($dateFilter === 'active') {
            // All active/unfulfilled orders
            $query->whereIn('status', ['pending', 'processing', 'delivering']);
        } elseif ($dateFilter === 'today') {
            // Orders today OR any order currently out for delivery/processing or updated today
            $query->where(function ($q) {
                $q->whereDate('order_date', Carbon::today())
                  ->orWhereIn('status', ['delivering', 'processing'])
                  ->orWhereDate('updated_at', Carbon::today());
            });
        } elseif ($dateFilter === 'yesterday') {
            $query->where(function ($q) {
                $q->whereDate('order_date', Carbon::yesterday())
                  ->orWhereDate('updated_at', Carbon::yesterday());
            });
        } elseif ($dateFilter === 'this_week') {
            $query->where(function ($q) {
                $q->whereBetween('order_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                  ->orWhereIn('status', ['delivering', 'processing'])
                  ->orWhereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            });
        } elseif ($request->filled('date') && ($dateFilter === 'custom' || empty($dateFilter))) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('order_date', $request->date)
                  ->orWhere(function ($sub) use ($request) {
                      $sub->whereDate('updated_at', $request->date)
                          ->whereIn('status', ['delivering', 'processing']);
                  });
            });
        }

        $orders = $query->latest('id')->get();
        $activeDeliveries = $this->formatOrdersForMap($orders);

        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'formatted_time' => now()->format('h:i:s A'),
            'count' => count($activeDeliveries),
            'deliveries' => $activeDeliveries,
        ]);
    }

    /**
     * AJAX Quick Order Status Update from Map
     */
    public function updateOrderStatus(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:pending,processing,delivering,completed,cancelled',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $order->update(['status' => $validated['status']]);

        $statusKh = match ($validated['status']) {
            'completed' => 'ដឹកជញ្ជូនរួចរាល់',
            'delivering' => 'កំពុងចេញដឹក',
            'processing' => 'កំពុងរៀបចំ/ដុតនំ',
            'pending' => 'កំពុងរង់ចាំ',
            'cancelled' => 'បានបោះបង់',
            default => $validated['status'],
        };

        return response()->json([
            'status' => 'ok',
            'message' => "បានកែប្រែស្ថានភាព #ORD-{$order->id} ទៅជា \"{$statusKh}\" ជោគជ័យ!",
            'order_id' => $order->id,
            'new_status' => $order->status,
            'status_kh' => $statusKh,
        ]);
    }

    /**
     * Update Driver GPS Location (From Mobile Tracker)
     */
    public function updateDriverLocation(Request $request, $orderId = null)
    {
        $validated = $request->validate([
            'order_id' => 'nullable|integer',
            'delivery_id' => 'nullable|integer',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric',
        ]);

        $targetOrderId = $orderId ?: $request->order_id;
        $deliveryId = $request->delivery_id;

        $posData = [
            'lat' => (float)$validated['lat'],
            'lng' => (float)$validated['lng'],
            'speed' => (float)($validated['speed'] ?? 0),
            'heading' => (float)($validated['heading'] ?? 0),
            'accuracy' => (float)($validated['accuracy'] ?? 0),
            'updated_at' => now()->toIso8601String(),
            'updated_time' => now()->format('h:i:s A'),
        ];

        if ($targetOrderId) {
            cache()->put("order_driver_pos_{$targetOrderId}", $posData, 600);
        }

        if ($deliveryId) {
            cache()->put("driver_pos_{$deliveryId}", $posData, 600);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'បានកត់ត្រាទីតាំង GPS អ្នកដឹកជញ្ជូនជោគជ័យ!',
            'data' => $posData,
        ]);
    }

    /**
     * Mobile Page for Driver to Track and Complete Order
     */
    public function driverTrackView(Order $order)
    {
        $order->load(['customer', 'delivery', 'items.product']);
        $depot = \App\Http\Controllers\CustomerLocationController::getStoreLocation();
        $customer = $order->customer;

        $custLat = $customer?->latitude ? (float)$customer->latitude : null;
        $custLng = $customer?->longitude ? (float)$customer->longitude : null;

        return view('deliveries.driver_mobile', compact('order', 'customer', 'depot', 'custLat', 'custLng'));
    }

    /**
     * Mobile Complete Order Action
     */
    public function driverCompleteOrder(Request $request, Order $order)
    {
        $order->update(['status' => 'completed']);
        cache()->forget("order_driver_pos_{$order->id}");

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'ok',
                'message' => '🎉 បានដឹកជញ្ជូនភីហ្សាដល់ដៃអតិថិជនរួចរាល់!',
            ]);
        }

        return redirect()->back()->with('success', 'បានកត់ត្រាការដឹកជញ្ជូនរួចរាល់!');
    }

    /**
     * Mobile Delivery Portal for Drivers (Full Dispatch Web App)
     */
    public function driverPortal(Request $request, $delivery = null)
    {
        $deliveries = Delivery::orderBy('delivery_name')->get();
        $selectedDeliveryId = null;

        if ($delivery instanceof Delivery) {
            $selectedDeliveryId = $delivery->id;
        } elseif (is_numeric($delivery)) {
            $selectedDeliveryId = (int) $delivery;
        } elseif ($request->filled('delivery_id')) {
            $selectedDeliveryId = (int) $request->delivery_id;
        }

        $currentDelivery = $selectedDeliveryId ? Delivery::find($selectedDeliveryId) : null;

        $query = Order::with(['customer', 'delivery', 'items.product', 'invoice'])
            ->whereNull('deleted_at');

        if ($selectedDeliveryId) {
            $query->where('delivery_id', $selectedDeliveryId);
        }

        // 1. First priority: Orders created today for this driver
        $orders = (clone $query)->whereDate('order_date', Carbon::today())->latest('id')->get();

        // 2. If no orders today, get recent active orders for this driver
        if ($orders->isEmpty()) {
            $orders = (clone $query)->whereIn('status', ['pending', 'processing', 'delivering'])
                ->latest('id')
                ->take(15)
                ->get();
        }

        // 3. Fallback to recent orders for this driver
        if ($orders->isEmpty()) {
            $orders = (clone $query)->latest('id')->take(10)->get();
        }

        $depot = \App\Http\Controllers\CustomerLocationController::getStoreLocation();
        $formattedOrders = $this->formatOrdersForMap($orders);

        return view('deliveries.driver_portal', compact('deliveries', 'selectedDeliveryId', 'currentDelivery', 'formattedOrders', 'depot'));
    }

    /**
     * Driver changes order status from Mobile Portal
     */
    public function driverUpdateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:processing,delivering,completed',
        ]);

        $order->update(['status' => $validated['status']]);
        if ($validated['status'] === 'completed') {
            cache()->forget("order_driver_pos_{$order->id}");
        }

        $statusKh = match ($validated['status']) {
            'completed' => 'ដឹកជញ្ជូនរួចរាល់',
            'delivering' => 'កំពុងចេញដឹក',
            'processing' => 'កំពុងរៀបចំ/ដុតនំ',
            default => $validated['status'],
        };

        return response()->json([
            'status' => 'ok',
            'message' => "បានកែប្រែស្ថានភាព #ORD-{$order->id} ទៅជា \"{$statusKh}\" ជោគជ័យ!",
            'order_id' => $order->id,
            'new_status' => $order->status,
            'status_kh' => $statusKh,
        ]);
    }

    private function formatOrdersForMap($orders): array
    {
        return $orders->map(function ($order) {
            $customer = $order->customer;
            $hasGps = !empty($customer?->latitude) && !empty($customer?->longitude);
            if ($hasGps) {
                $lat = (float) $customer->latitude;
                $lng = (float) $customer->longitude;
                $areaName = $customer->landmark ?: ($customer->city ?: 'Phnom Penh');
            } else {
                [$lat, $lng, $areaName] = $this->resolveAreaCoordinates(
                    $customer?->address,
                    $customer?->city,
                    $order->id
                );
            }

            $statusKh = match ($order->status) {
                'completed' => 'ដឹកជញ្ជូនរួចរាល់',
                'delivering' => 'កំពុងចេញដឹក',
                'processing' => 'កំពុងរៀបចំ/ដុតនំ',
                'pending' => 'កំពុងរង់ចាំ',
                'cancelled' => 'បានបោះបង់',
                default => $order->status,
            };

            // Check if active driver live GPS exists in cache
            $driverLivePos = cache()->get("order_driver_pos_{$order->id}") ?? ($order->delivery_id ? cache()->get("driver_pos_{$order->delivery_id}") : null);

            return [
                'id' => $order->id,
                'order_code' => 'ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
                'order_date' => $order->order_date ? $order->order_date->format('Y-m-d H:i') : null,
                'time' => $order->order_date ? $order->order_date->format('h:i A') : '',
                'customer_id' => $customer?->id,
                'customer_name' => $customer?->name ?? 'Walk-in Customer',
                'customer_phone' => $customer?->phone ?? $order->taxi_phone ?? '—',
                'address' => $customer?->address ?? 'Phnom Penh',
                'landmark' => $customer?->landmark ?? '',
                'area' => $areaName,
                'lat' => $lat,
                'lng' => $lng,
                'has_gps' => $hasGps,
                'delivery_id' => $order->delivery_id,
                'delivery_name' => $order->delivery?->delivery_name ?? 'In-House Delivery',
                'total_amount' => (float) $order->total_amount,
                'total_amount_khr' => (float) $order->totalKhr(),
                'status' => $order->status,
                'status_kh' => $statusKh,
                'payment_status' => $order->payment_status,
                'box_qty' => (int) ($order->box_qty ?: 1),
                'items_summary' => $order->items->map(fn($it) => ($it->product?->name ?? 'Item') . ' x' . $it->quantity)->join(', '),
                'driver_live_pos' => $driverLivePos,
                'driver_track_url' => request()->root() . '/driver-track/' . $order->id,
            ];
        })->values()->all();
    }

    private function resolveAreaCoordinates(?string $address, ?string $city, int $id): array
    {
        $text = mb_strtolower(($address ?? '') . ' ' . ($city ?? ''));

        $areas = [
            'bkk'            => [11.5480, 104.9284, 'BKK1'],
            'boeng keng kang'=> [11.5480, 104.9284, 'BKK'],
            'toul kork'      => [11.5764, 104.8925, 'Toul Kork'],
            'chamkarmon'     => [11.5449, 104.9160, 'Chamkarmon'],
            'sen sok'        => [11.5989, 104.8797, 'Sen Sok'],
            'chroy changvar' => [11.5830, 104.9310, 'Chroy Changvar'],
            'meanchey'       => [11.5250, 104.9130, 'Meanchey'],
            'russey keo'     => [11.6050, 104.9120, 'Russey Keo'],
            'dangkao'        => [11.4890, 104.8750, 'Dangkao'],
            'por senchey'    => [11.5540, 104.8320, 'Por Senchey'],
            'daun penh'      => [11.5690, 104.9250, 'Daun Penh'],
            '7 makara'       => [11.5610, 104.9120, 'Prampir Makara'],
            'prampir makara' => [11.5610, 104.9120, 'Prampir Makara'],
            'toul tompoung'  => [11.5360, 104.9140, 'Toul Tompoung'],
            'takhmao'        => [11.4780, 104.9510, 'Ta Khmau'],
            'kandal'         => [11.4780, 104.9510, 'Kandal'],
        ];

        foreach ($areas as $keyword => $coord) {
            if (str_contains($text, $keyword)) {
                $offsetLat = (($id * 17) % 20 - 10) * 0.0007;
                $offsetLng = (($id * 23) % 20 - 10) * 0.0007;
                return [$coord[0] + $offsetLat, $coord[1] + $offsetLng, $coord[2]];
            }
        }

        $defaultLat = 11.5564 + ((($id * 19) % 30 - 15) * 0.0012);
        $defaultLng = 104.9282 + ((($id * 29) % 30 - 15) * 0.0012);
        return [$defaultLat, $defaultLng, $city ?: 'Phnom Penh'];
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

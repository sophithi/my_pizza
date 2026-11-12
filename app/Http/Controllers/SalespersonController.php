<?php

namespace App\Http\Controllers;

use App\Models\Salesperson;
use App\Models\OrderItem;
use App\Models\Order;
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

        // 3 core pizza products: S (13), M (14), L (15)
        $targetIds = [13, 14, 15];

        // 1. Overall Pizza Sales Summary for this Salesperson in the period
        $productSalesQuery = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('customers.salesperson_id', $salesperson->id)
            ->whereIn('order_items.product_id', $targetIds)
            ->where('orders.status', '!=', 'cancelled')
            ->whereNull('orders.deleted_at');

        if ($start) {
            $productSalesQuery->where('orders.order_date', '>=', $start);
        }
        if ($end) {
            $productSalesQuery->where('orders.order_date', '<=', $end);
        }

        $productSummary = (clone $productSalesQuery)
            ->groupBy('order_items.product_id', 'products.name', 'products.unit', 'products.image', 'products.sku')
            ->selectRaw('order_items.product_id as id, products.name, products.unit, products.image, products.sku, SUM(CASE WHEN order_items.item_type = \'sale\' THEN order_items.quantity ELSE 0 END) as total_qty, SUM(CASE WHEN order_items.item_type = \'free\' THEN order_items.quantity ELSE 0 END) as free_qty, COUNT(DISTINCT customers.id) as customer_count, SUM(order_items.total_price) as total_usd')
            ->orderBy('order_items.product_id')
            ->get();

        $totalPizzasFree = $productSummary->sum('free_qty');
        $totalPizzasSold = $productSummary->sum('total_qty');
        $totalPizzaSalesUsd = $productSummary->sum('total_usd');

        // Total orders in the period
        $ordersCountQuery = Order::query()
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('customers.salesperson_id', $salesperson->id)
            ->where('orders.status', '!=', 'cancelled')
            ->whereNull('orders.deleted_at');

        if ($start) {
            $ordersCountQuery->where('orders.order_date', '>=', $start);
        }
        if ($end) {
            $ordersCountQuery->where('orders.order_date', '<=', $end);
        }

        $totalPeriodOrders = $ordersCountQuery->count();

        // 2. Customers query
        $customersQuery = $salesperson->customers()
            ->withCount(['orders' => function ($q) use ($start, $end) {
                $q->where('status', '!=', 'cancelled');
                if ($start) {
                    $q->where('order_date', '>=', $start);
                }
                if ($end) {
                    $q->where('order_date', '<=', $end);
                }
            }])
            ->withSum(['orders as total_spent' => function ($q) use ($start, $end) {
                $q->where('status', '!=', 'cancelled');
                if ($start) {
                    $q->where('order_date', '>=', $start);
                }
                if ($end) {
                    $q->where('order_date', '<=', $end);
                }
            }], 'total_amount');

        $customers = $customersQuery->orderBy('name')->paginate(15)->withQueryString();

        // 3. For paginated customers, load S, M, L pizza quantities
        $customerIds = $customers->pluck('id');
        $customerPizzas = [];
        $customerFreePizzas = [];

        if ($customerIds->isNotEmpty()) {
            $itemsQuery = OrderItem::query()
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereIn('orders.customer_id', $customerIds)
                    ->whereIn('order_items.product_id', $targetIds)
                ->where('orders.status', '!=', 'cancelled')
                ->whereNull('orders.deleted_at');

            if ($start) {
                $itemsQuery->where('orders.order_date', '>=', $start);
            }
            if ($end) {
                $itemsQuery->where('orders.order_date', '<=', $end);
            }

            $items = $itemsQuery
                ->groupBy('orders.customer_id', 'order_items.product_id')
                ->selectRaw('orders.customer_id, order_items.product_id, SUM(CASE WHEN order_items.item_type = \'sale\' THEN order_items.quantity ELSE 0 END) as qty, SUM(CASE WHEN order_items.item_type = \'free\' THEN order_items.quantity ELSE 0 END) as free_qty')
                ->get();

            foreach ($items as $item) {
                $customerPizzas[$item->customer_id][$item->product_id] = (int) $item->qty;
                $customerFreePizzas[$item->customer_id][$item->product_id] = (int) $item->free_qty;
            }
        }

        return view('salespersons.show', compact(
            'salesperson',
            'customers',
            'productSummary',
            'totalPizzasSold',
            'totalPizzasFree',
            'customerFreePizzas',
            'totalPizzaSalesUsd',
            'totalPeriodOrders',
            'customerPizzas',
            'period'
        ));
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


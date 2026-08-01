<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    private const EXCHANGE_RATE = 4000;

    public function daily(Request $request)
    {
        $date = $request->input('date', today()->toDateString());
        $reportDate = Carbon::createFromFormat('Y-m-d', $date);

        $orders = Order::query()
            ->with('customer')
            ->whereDate('order_date', $reportDate)
            ->latest('order_date');

        $totalOrders = (clone $orders)->count();
        $grossSales = (clone $orders)->where('status', '!=', 'cancelled')->sum('total_amount');

        $dayOrders = (clone $orders)->where('status', '!=', 'cancelled')
            ->with(['items', 'payments'])
            ->get();
        $grossSalesKhr = $dayOrders->sum(fn($order) => $order->totalKhr());

        // Outstanding balance owed on today's orders specifically (not to be
        // confused with $income, which is payments recorded today for any order).
        $unpaid = 0.0;
        $unpaidKhr = 0.0;
        foreach ($dayOrders as $order) {
            $payment = $order->payments->first();
            $totalAmount = (float) $order->total_amount;
            $totalKhrAmount = $order->totalKhr();
            $paidAmount = $payment?->paid_amount ?? ($order->payment_status === 'paid' ? $totalAmount : 0);
            $paidKhrAmount = $payment?->paid_amount_khr ?? ($order->payment_status === 'paid' ? $totalKhrAmount : 0);

            $unpaid += max(0, $totalAmount - $paidAmount);
            $unpaidKhr += max(0, $totalKhrAmount - $paidKhrAmount);
        }

        $income = Payment::whereDate('created_at', $reportDate)->sum('paid_amount');
        $incomeKhr = Payment::whereDate('created_at', $reportDate)->sum('paid_amount_khr');
        $expenses = Purchase::whereDate('purchase_date', $reportDate)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
        // Purchase has no stored KHR figure, so this is a rate conversion (not a
        // ground-truth KHR value like the other totals above).
        $expensesKhr = $expenses * self::EXCHANGE_RATE;
        $netIncome = $income - $expenses;
        $netIncomeKhr = $incomeKhr - $expensesKhr;

        $soldItems = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereDate('orders.order_date', $reportDate)
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('order_items.product_id', 'products.name', 'products.unit')
            ->selectRaw('products.name, products.unit, SUM(order_items.quantity) as quantity, SUM(order_items.total_price) as total')
            ->selectRaw(
                'SUM(COALESCE(order_items.unit_price_khr, order_items.unit_price * ?) * order_items.quantity * (1 - COALESCE(order_items.discount_percent, 0) / 100)) as total_khr',
                [self::EXCHANGE_RATE]
            )
            ->orderByDesc('quantity')
            ->get();

        $dayPayments = Payment::with(['order.customer', 'lines'])
            ->whereDate('created_at', $reportDate)
            ->latest()
            ->get();
        $payments = $dayPayments->take(10);
        $paymentMethodBreakdown = $this->buildDailyMethodBreakdown($dayPayments);

        $purchases = Purchase::whereDate('purchase_date', $reportDate)
            ->latest()
            ->limit(10)
            ->get();

        $stockMovement = InventoryMovement::query()
            ->join('inventories', 'inventory_movements.inventory_id', '=', 'inventories.id')
            ->leftJoin('products', 'inventory_movements.product_id', '=', 'products.id')
            ->whereDate('inventory_movements.created_at', $reportDate)
            ->groupBy('inventory_movements.inventory_id', 'products.name', 'products.unit', 'inventories.quantity')
            ->selectRaw('products.name, products.unit, inventories.quantity as current_quantity')
            ->selectRaw('SUM(CASE WHEN inventory_movements.quantity_change < 0 THEN ABS(inventory_movements.quantity_change) ELSE 0 END) as stock_out')
            ->selectRaw('SUM(CASE WHEN inventory_movements.quantity_change > 0 THEN inventory_movements.quantity_change ELSE 0 END) as stock_in')
            ->selectRaw('MAX(inventory_movements.created_at) as last_movement_at')
            ->orderByDesc('last_movement_at')
            ->get();

        $lowStock = Inventory::with('product')
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->orderBy('quantity')
            ->limit(10)
            ->get();

        $exchangeRate = self::EXCHANGE_RATE;

        return view('reports.daily', compact(
            'date',
            'reportDate',
            'totalOrders',
            'grossSales',
            'grossSalesKhr',
            'unpaid',
            'unpaidKhr',
            'income',
            'incomeKhr',
            'expenses',
            'expensesKhr',
            'netIncome',
            'netIncomeKhr',
            'soldItems',
            'payments',
            'paymentMethodBreakdown',
            'purchases',
            'stockMovement',
            'lowStock',
            'exchangeRate'
        ));
    }

    /**
     * Break down a day's payments by method (Cash/ABA/ACLEDA/Wing/Other),
     * summing both KHR and USD. Mirrors PaymentController's breakdown but
     * works directly off Payment models rather than mapped order rows.
     */
    private function buildDailyMethodBreakdown($payments): array
    {
        $methodLabels = [
            'Cash'   => 'លុយក្រៅ',
            'ABA'    => 'ABA Bank',
            'ACLEDA' => 'ACLEDA Bank',
            'Wing'   => 'Wing',
        ];

        $breakdown = [];
        foreach ($methodLabels as $key => $label) {
            $breakdown[$key] = ['label' => $label, 'usd' => 0.0, 'khr' => 0.0];
        }
        $breakdown['Other'] = ['label' => 'ផ្សេងៗ', 'usd' => 0.0, 'khr' => 0.0];

        foreach ($payments as $payment) {
            $lines = $payment->lines;

            // Legacy payments recorded before the per-line breakdown existed have
            // no lines — fall back to the payment-level (possibly "+"-joined) method.
            if ($lines->isEmpty() && (float) $payment->paid_amount > 0) {
                $matchedKey = 'Other';
                foreach ($methodLabels as $key => $label) {
                    if (str_contains((string) $payment->method, $key)) {
                        $matchedKey = $key;
                        break;
                    }
                }

                $breakdown[$matchedKey]['usd'] += (float) $payment->paid_amount;
                $breakdown[$matchedKey]['khr'] += (float) $payment->paid_amount_khr;
                continue;
            }

            foreach ($lines as $line) {
                $key = array_key_exists($line->method, $methodLabels) ? $line->method : 'Other';
                $rate = (float) ($line->exchange_rate ?: self::EXCHANGE_RATE);
                $khr = $line->currency === 'KHR'
                    ? (float) $line->amount_original
                    : round((float) $line->amount_usd * $rate);

                $breakdown[$key]['usd'] += (float) $line->amount_usd;
                $breakdown[$key]['khr'] += $khr;
            }
        }

        return $breakdown;
    }

    /**
     * Display sales report.
     */
    public function sales(Request $request)
    {
        // Get filter parameters
        $period = $request->input('period', 'all'); // all, today, month, year, custom
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Build date range query
        $query = Order::query();
        $dateRange = $this->getDateRange($period, $startDate, $endDate);

        if ($dateRange['start']) {
            $query->whereDate('order_date', '>=', $dateRange['start']);
        }
        if ($dateRange['end']) {
            $query->whereDate('order_date', '<=', $dateRange['end']);
        }

        // Overall metrics
        $totalOrders = $query->count();
        $totalRevenue = (clone $query)->where('status', '!=', 'cancelled')->sum('total_amount');
        $totalRevenueKhr = (clone $query)->where('status', '!=', 'cancelled')
            ->with('items')
            ->get()
            ->sum(fn($order) => $order->totalKhr());
        $averageOrderValue = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0;
        $averageOrderValueKhr = $totalOrders > 0 ? ($totalRevenueKhr / $totalOrders) : 0;
        $completedOrders = (clone $query)->where('status', 'completed')->count();
        $pendingOrders = (clone $query)->where('status', 'pending')->count();

        // Revenue by product (cancelled orders don't count as sold)
        $productRevenue = Product::with(['orderItems' => function ($q) use ($dateRange) {
            $q->whereHas('order', fn($oq) => $oq->where('status', '!=', 'cancelled'));
            if ($dateRange['start']) $q->whereDate('created_at', '>=', $dateRange['start']);
            if ($dateRange['end']) $q->whereDate('created_at', '<=', $dateRange['end']);
        }])
            ->withCount(['orderItems' => function ($q) use ($dateRange) {
                $q->whereHas('order', fn($oq) => $oq->where('status', '!=', 'cancelled'));
                if ($dateRange['start']) $q->whereDate('created_at', '>=', $dateRange['start']);
                if ($dateRange['end']) $q->whereDate('created_at', '<=', $dateRange['end']);
            }])
            ->orderByDesc('order_items_count')
            ->limit(10)
            ->get();

        // Orders by status
        $ordersByStatus = (clone $query)
            ->groupBy('status')
            ->selectRaw('status, COUNT(*) as count, SUM(total_amount) as total')
            ->get();

        // Revenue by customer (top 10, cancelled orders excluded)
        $customerRevenue = Customer::withSum(['orders' => function ($q) use ($dateRange) {
            $q->where('status', '!=', 'cancelled');
            if ($dateRange['start']) $q->whereDate('order_date', '>=', $dateRange['start']);
            if ($dateRange['end']) $q->whereDate('order_date', '<=', $dateRange['end']);
        }], 'total_amount')
            ->orderByDesc('orders_sum_total_amount')
            ->limit(10)
            ->get();

        // Daily revenue data for chart
        $dailyRevenue = $this->getDailyRevenue($dateRange);
        $exchangeRate = self::EXCHANGE_RATE;

        return view('reports.sales', compact(
            'totalOrders',
            'totalRevenue',
            'totalRevenueKhr',
            'averageOrderValue',
            'averageOrderValueKhr',
            'completedOrders',
            'pendingOrders',
            'productRevenue',
            'ordersByStatus',
            'customerRevenue',
            'dailyRevenue',
            'period',
            'startDate',
            'endDate',
            'exchangeRate'
        ));
    }

    /**
     * Display inventory report.
     */
    public function inventory(Request $request)
    {
        $totalProducts = Product::count();
        $lowStockProducts = Inventory::whereRaw('quantity <= reorder_level')
            ->with('product')
            ->get();
        $outOfStockCount = Inventory::where('quantity', '<=', 0)->count();
        $totalInventoryValue = Inventory::query()
            ->join('products', 'products.id', '=', 'inventories.product_id')
            ->selectRaw('SUM(GREATEST(inventories.quantity, 0) * COALESCE(NULLIF(inventories.cost_per_unit, 0), products.price_usd, 0)) as total')
            ->value('total') ?? 0;
        // KHR ground truth mirrors the USD calc above, but reads products.price_khr
        // (the stored KHR catalog price) instead of deriving it from price_usd.
        $totalInventoryValueKhr = Inventory::query()
            ->join('products', 'products.id', '=', 'inventories.product_id')
            ->selectRaw(
                'SUM(GREATEST(inventories.quantity, 0) * COALESCE(NULLIF(inventories.cost_per_unit, 0) * ?, products.price_khr, 0)) as total',
                [self::EXCHANGE_RATE]
            )
            ->value('total') ?? 0;

        // All inventory with stock status
        $inventory = Inventory::with('product')
            ->orderBy('quantity', 'asc')
            ->paginate(20);

        // Get period for display, and use it to scope the stock flow below
        $period = $request->input('period', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $dateRange = $this->getDateRange($period, $startDate, $endDate);

        $stockMovementQuery = InventoryMovement::query()
            ->join('inventories', 'inventory_movements.inventory_id', '=', 'inventories.id')
            ->leftJoin('products', 'inventory_movements.product_id', '=', 'products.id')
            ->groupBy('inventory_movements.inventory_id', 'products.name', 'products.unit', 'inventories.quantity')
            ->selectRaw('products.name, products.unit, inventories.quantity as current_quantity')
            ->selectRaw('SUM(CASE WHEN inventory_movements.quantity_change < 0 THEN ABS(inventory_movements.quantity_change) ELSE 0 END) as stock_out')
            ->selectRaw('SUM(CASE WHEN inventory_movements.quantity_change > 0 THEN inventory_movements.quantity_change ELSE 0 END) as stock_in')
            ->selectRaw('MAX(inventory_movements.created_at) as last_movement_at');

        if ($dateRange['start']) {
            $stockMovementQuery->whereDate('inventory_movements.created_at', '>=', $dateRange['start']);
        }
        if ($dateRange['end']) {
            $stockMovementQuery->whereDate('inventory_movements.created_at', '<=', $dateRange['end']);
        }

        $stockMovement = $stockMovementQuery->orderByDesc('last_movement_at')->get();

        return view('reports.inventory', compact(
            'totalProducts',
            'lowStockProducts',
            'outOfStockCount',
            'totalInventoryValue',
            'totalInventoryValueKhr',
            'inventory',
            'stockMovement',
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display customer report.
     */
    public function customers(Request $request)
    {
        // Get filter parameters
        $period = $request->input('period', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Build date range query
        $dateRange = $this->getDateRange($period, $startDate, $endDate);

        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', 'active')->count();

        // Customer activity (orders placed) with date filtering
        $customerActivity = Customer::withCount(['orders' => function ($q) use ($dateRange) {
            if ($dateRange['start']) $q->whereDate('order_date', '>=', $dateRange['start']);
            if ($dateRange['end']) $q->whereDate('order_date', '<=', $dateRange['end']);
        }])
            ->withSum(['orders' => function ($q) use ($dateRange) {
                $q->where('status', '!=', 'cancelled');
                if ($dateRange['start']) $q->whereDate('order_date', '>=', $dateRange['start']);
                if ($dateRange['end']) $q->whereDate('order_date', '<=', $dateRange['end']);
            }], 'total_amount')
            // Eager-loaded alongside the aggregates above so the KHR ground-truth
            // total (Order::totalKhr(), computed from items) can be summed per
            // customer without re-querying — same filter as the withSum above.
            ->with(['orders' => function ($q) use ($dateRange) {
                $q->where('status', '!=', 'cancelled')->with('items');
                if ($dateRange['start']) $q->whereDate('order_date', '>=', $dateRange['start']);
                if ($dateRange['end']) $q->whereDate('order_date', '<=', $dateRange['end']);
            }])
            ->orderByDesc('orders_count')
            ->paginate(20);

        return view('reports.customers', compact(
            'totalCustomers',
            'activeCustomers',
            'customerActivity',
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display dashboard reports.
     */
    public function dashboard(Request $request)
    {
        // Get filter parameters
        $period = $request->input('period', 'month');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Build date range query
        $dateRange = $this->getDateRange($period, $startDate, $endDate);

        // Overall metrics
        $query = Order::query();
        if ($dateRange['start']) {
            $query->whereDate('order_date', '>=', $dateRange['start']);
        }
        if ($dateRange['end']) {
            $query->whereDate('order_date', '<=', $dateRange['end']);
        }

        $totalRevenue = (clone $query)->where('status', '!=', 'cancelled')->sum('total_amount');
        $totalRevenueKhr = (clone $query)->where('status', '!=', 'cancelled')
            ->with('items')
            ->get()
            ->sum(fn($order) => $order->totalKhr());
        $totalOrders = (clone $query)->count();
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();

        $recentOrders = (clone $query)->with(['customer', 'items'])->latest('order_date')->limit(5)->get();
        $lowStockAlerts = Inventory::whereRaw('quantity <= reorder_level')
            ->with('product')
            ->limit(5)
            ->get();

        // Revenue data based on period
        $chartData = $this->getChartData($period, $dateRange);

        $exchangeRate = self::EXCHANGE_RATE;

        return view('reports.dashboard', compact(
            'totalRevenue',
            'totalRevenueKhr',
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'recentOrders',
            'lowStockAlerts',
            'chartData',
            'period',
            'startDate',
            'endDate',
            'exchangeRate'
        ));
    }

    /**
     * Get chart data based on period selection.
     */
    private function getChartData($period, $dateRange)
    {
        $query = Order::selectRaw('DATE(order_date) as date, SUM(total_amount) as total, COUNT(*) as count')
            ->where('status', '!=', 'cancelled')
            ->groupByRaw('DATE(order_date)');

        if ($dateRange['start']) {
            $query->whereDate('order_date', '>=', $dateRange['start']);
        }
        if ($dateRange['end']) {
            $query->whereDate('order_date', '<=', $dateRange['end']);
        }

        return $query->orderBy('date', 'asc')->get();
    }

    /**
     * Get date range based on period selection.
     */
    private function getDateRange($period, $startDate = null, $endDate = null)
    {
        $start = null;
        $end = null;

        switch ($period) {
            case 'today':
                $start = Carbon::today();
                $end = Carbon::today();
                break;
            case 'yesterday':
                $start = Carbon::yesterday();
                $end = Carbon::yesterday();
                break;
            case 'week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $start = $startDate ? Carbon::createFromFormat('Y-m-d', $startDate) : null;
                $end = $endDate ? Carbon::createFromFormat('Y-m-d', $endDate) : null;
                break;
            case 'all':
            default:
                $start = null;
                $end = null;
                break;
        }

        return ['start' => $start, 'end' => $end];
    }

    /**
     * Get daily revenue data for chart.
     */
    private function getDailyRevenue($dateRange)
    {
        $query = Order::selectRaw(
            'DATE(order_date) as date, SUM(total_amount) as total, COUNT(*) as count'
        )->where('status', '!=', 'cancelled')
            ->groupByRaw('DATE(order_date)');

        if ($dateRange['start']) {
            $query->whereDate('order_date', '>=', $dateRange['start']);
        }
        if ($dateRange['end']) {
            $query->whereDate('order_date', '<=', $dateRange['end']);
        }

        return $query->orderBy('date', 'asc')->get();
    }
}

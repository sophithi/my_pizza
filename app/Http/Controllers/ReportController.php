<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
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
        $totalRevenue = $query->sum('total_amount');
        $averageOrderValue = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0;
        $completedOrders = (clone $query)->where('status', 'completed')->count();
        $pendingOrders = (clone $query)->where('status', 'pending')->count();

        // Revenue by product
        $productRevenue = Product::with(['orderItems' => function ($q) use ($dateRange) {
            if ($dateRange['start']) $q->whereDate('created_at', '>=', $dateRange['start']);
            if ($dateRange['end']) $q->whereDate('created_at', '<=', $dateRange['end']);
        }])
            ->withCount(['orderItems' => function ($q) use ($dateRange) {
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

        // Revenue by customer (top 10)
        $customerRevenue = Customer::withSum(['orders' => function ($q) use ($dateRange) {
            if ($dateRange['start']) $q->whereDate('order_date', '>=', $dateRange['start']);
            if ($dateRange['end']) $q->whereDate('order_date', '<=', $dateRange['end']);
        }], 'total_amount')
            ->orderByDesc('orders_sum_total_amount')
            ->limit(10)
            ->get();

        // Daily revenue data for chart
        $dailyRevenue = $this->getDailyRevenue($dateRange);

        return view('reports.sales', compact(
            'totalOrders',
            'totalRevenue',
            'averageOrderValue',
            'completedOrders',
            'pendingOrders',
            'productRevenue',
            'ordersByStatus',
            'customerRevenue',
            'dailyRevenue',
            'period',
            'startDate',
            'endDate'
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
        $outOfStockCount = Inventory::where('quantity', 0)->count();
        $totalInventoryValue = Inventory::selectRaw('SUM(quantity * COALESCE(cost_per_unit, 0)) as total')
            ->value('total') ?? 0;

        // All inventory with stock status
        $inventory = Inventory::with('product')
            ->orderBy('quantity', 'asc')
            ->paginate(20);

        // Get period for display
        $period = $request->input('period', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return view('reports.inventory', compact(
            'totalProducts',
            'lowStockProducts',
            'outOfStockCount',
            'totalInventoryValue',
            'inventory',
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
        $totalCreditLimit = Customer::sum('credit_limit');

        // Customer activity (orders placed) with date filtering
        $customerActivity = Customer::withCount(['orders' => function ($q) use ($dateRange) {
            if ($dateRange['start']) $q->whereDate('order_date', '>=', $dateRange['start']);
            if ($dateRange['end']) $q->whereDate('order_date', '<=', $dateRange['end']);
        }])
            ->withSum(['orders' => function ($q) use ($dateRange) {
                if ($dateRange['start']) $q->whereDate('order_date', '>=', $dateRange['start']);
                if ($dateRange['end']) $q->whereDate('order_date', '<=', $dateRange['end']);
            }], 'total_amount')
            ->orderByDesc('orders_count')
            ->paginate(20);

        return view('reports.customers', compact(
            'totalCustomers',
            'activeCustomers',
            'totalCreditLimit',
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

        $totalRevenue = (clone $query)->sum('total_amount');
        $totalOrders = (clone $query)->count();
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();

        $recentOrders = (clone $query)->with('customer')->latest('order_date')->limit(5)->get();
        $lowStockAlerts = Inventory::whereRaw('quantity <= reorder_level')
            ->with('product')
            ->limit(5)
            ->get();

        // Revenue data based on period
        $chartData = $this->getChartData($period, $dateRange);

        return view('reports.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'recentOrders',
            'lowStockAlerts',
            'chartData',
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get chart data based on period selection.
     */
    private function getChartData($period, $dateRange)
    {
        $query = Order::selectRaw('DATE(order_date) as date, SUM(total_amount) as total, COUNT(*) as count')
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
        )->groupByRaw('DATE(order_date)');

        if ($dateRange['start']) {
            $query->whereDate('order_date', '>=', $dateRange['start']);
        }
        if ($dateRange['end']) {
            $query->whereDate('order_date', '<=', $dateRange['end']);
        }

        return $query->orderBy('date', 'asc')->get();
    }
}

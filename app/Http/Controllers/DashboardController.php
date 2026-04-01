<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $today = Carbon::today();

        // Real database queries for statistics
        $stats = [
            'today_sales' => Order::whereDate('order_date', $today)
                ->where('status', 'completed')
                ->sum('total_amount') ?? 0,
            
            'total_orders' => Order::count(),
            
            'orders_today' => Order::whereDate('order_date', $today)->count(),
            
            'stock_alerts' => Inventory::whereColumn('quantity', '<=', 'reorder_level')
                ->count(),
            
            'overdue_invoices' => Invoice::where('status', '!=', 'paid')
                ->where('due_date', '<', $today)
                ->count(),
            
            'customers' => Customer::count(),
            
            'low_inventory_items' => Inventory::whereColumn('quantity', '<=', 'reorder_level')
                ->count(),
            
            'recovery_rate' => $this->calculateRecoveryRate(),
        ];

        // Recent orders from database (last 5)
        $recent_orders = Order::with('customer')
            ->latest('order_date')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer' => $order->customer?->name ?? 'Unknown',
                    'amount' => floatval($order->total_amount),
                    'status' => ucfirst($order->status),
                    'date' => $order->order_date->format('Y-m-d'),
                ];
            })
            ->toArray();

        // Top selling products (by quantity)
        $top_products = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('products.name, SUM(order_items.quantity) as quantity, SUM(order_items.total_price) as amount')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('quantity')
            ->limit(5)
            ->get()
            ->map(function ($product) {
                return [
                    'name' => $product->name,
                    'quantity' => (int) $product->quantity,
                    'amount' => floatval($product->amount),
                ];
            })
            ->toArray();

        // Inventory alerts (low stock items)
        $inventory_alerts = Inventory::with('product')
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->get()
            ->map(function ($inventory) {
                $status = $inventory->quantity <= 0 ? 'critical' : 'warning';
                return [
                    'item' => $inventory->product?->name ?? 'Unknown',
                    'current' => (int) $inventory->quantity,
                    'minimum' => (int) $inventory->reorder_level,
                    'status' => $status,
                ];
            })
            ->toArray();

        return view('dashboard', compact('stats', 'recent_orders', 'top_products', 'inventory_alerts'));
    }

    /**
     * Calculate recovery/completion rate
     */
    private function calculateRecoveryRate()
    {
        $total_orders = Order::count();
        
        if ($total_orders == 0) {
            return 0;
        }

        $completed_orders = Order::where('status', 'completed')->count();
        
        return round(($completed_orders / $total_orders) * 100, 1);
    }
}

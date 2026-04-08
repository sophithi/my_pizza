<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Delivery;
use App\Models\Payment;
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
            
        
            // Payment Stats
            'unpaid_orders' => Order::where('payment_status', '!=', 'paid')->count(),
            'pending_payments' => Payment::where('status', 'pending')->sum('amount') ?? 0,
            'today_payments' => Payment::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->sum('amount') ?? 0,
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
        $topProducts = Product::with('inventory')
            ->limit(5)
            ->get()
            ->sortByDesc(function ($product) {
                return $product->orderItems()->sum('quantity');
            })
            ->values();

        // Keep array format for recent recent_orders, top_products, etc
        $top_products_array = $topProducts->map(function ($product) {
            return [
                'name' => $product->name,
                'category' => $product->category,
                'price_usd' => floatval($product->price_usd),
                'quantity' => (int) ($product->inventory->quantity ?? 0),
                'sales' => (int) ($product->orderItems()->sum('quantity') ?? 0),
            ];
        })->toArray();

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
        // Pending Payments (Orders that need payment follow-up)
        $pending_payments = Order::with('customer')
            ->where('payment_status', '!=', 'paid')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer' => $order->customer?->name ?? 'Unknown',
                    'amount' => floatval($order->total_amount),
                    'payment_status' => $order->payment_status,
                    'date' => $order->order_date->format('d M'),
                ];
            })
            ->toArray();

        return view('dashboard', compact(
            'stats', 
            'recent_orders', 
            'topProducts',
            'inventory_alerts',
    
            'pending_payments'
        ));
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

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $today = Carbon::today('Asia/Phnom_Penh');
        $exchangeRate = 4000;
        $last7Start = $today->copy()->subDays(6);

        // Real database queries for statistics
        $stats = [
            'today_sales' => Order::whereDate('order_date', $today)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount') ?? 0,

            'total_orders' => Order::count(),

            'orders_today' => Order::whereDate('order_date', $today)->count(),

            'pending_orders' => Order::where('status', 'pending')->count(),

            'processing_orders' => Order::where('status', 'processing')->count(),

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
            'unpaid_amount' => Order::where('payment_status', '!=', 'paid')->sum('total_amount') ?? 0,
            'pending_payments' => Payment::whereIn('status', ['pending', 'partial'])->sum(DB::raw('GREATEST(total_amount - paid_amount, 0)')) ?? 0,
            'today_payments' => Payment::whereDate('created_at', $today)
                ->where('status', 'paid')
                ->sum('paid_amount') ?? 0,

            'today_expenses' => Purchase::whereDate('purchase_date', $today)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount') ?? 0,
        ];

        $stats['today_net'] = $stats['today_sales'] - $stats['today_expenses'];
        $stats['today_sales_khr'] = $stats['today_sales'] * $exchangeRate;
        $stats['today_expenses_khr'] = $stats['today_expenses'] * $exchangeRate;
        $stats['today_net_khr'] = $stats['today_net'] * $exchangeRate;

        $order_status = [
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        $payment_status = [
            'paid' => Order::where('payment_status', 'paid')->count(),
            'partial' => Order::where('payment_status', 'partial')->count(),
            'unpaid' => Order::where('payment_status', 'unpaid')->count(),
        ];

        $sales_by_day = collect(range(0, 6))->map(function ($offset) use ($last7Start) {
            $date = $last7Start->copy()->addDays($offset);
            $total = Order::whereDate('order_date', $date)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount') ?? 0;

            return [
                'label' => $date->format('D'),
                'date' => $date->format('M d'),
                'total' => (float) $total,
            ];
        });

        $maxSales = max(1, $sales_by_day->max('total'));

        // Recent orders from database (last 5)
        $recent_orders = Order::with('customer')
            ->latest('order_date')
            ->limit(6)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer' => $order->customer?->name ?? 'Unknown',
                    'amount' => floatval($order->total_amount),
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'date' => $order->order_date->format('d M'),
                ];
            })
            ->toArray();

        // Top selling products (by quantity)
        $topProducts = Product::withSum('orderItems as sold_quantity', 'quantity')
            ->with('inventory')
            ->orderByDesc('sold_quantity')
            ->limit(5)
            ->get();

        // Keep array format for recent recent_orders, top_products, etc
        $top_products_array = $topProducts->map(function ($product) {
            return [
                'name' => $product->name,
                'category' => $product->category,
                'price_usd' => floatval($product->price_usd),
                'quantity' => (int) ($product->inventory->quantity ?? 0),
                'sales' => (int) ($product->sold_quantity ?? 0),
            ];
        })->toArray();

        // Inventory alerts (low stock items)
        $inventory_alerts = Inventory::with('product')
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->orderBy('quantity')
            ->limit(5)
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
            ->limit(6)
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
            'exchangeRate',
            'recent_orders',
            'top_products_array',
            'inventory_alerts',
            'pending_payments',
            'order_status',
            'payment_status',
            'sales_by_day',
            'maxSales'
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Mock data - Replace with actual database queries
        $stats = [
            'today_sales' => 2450.00,
            'total_orders' => 127,
            'orders_today' => 34,
            'stock_alerts' => 5,
            'overdue_invoices' => 3,
            'customers' => 84,
            'low_inventory_items' => 8,
            'recovery_rate' => 98.5,
        ];

        $recent_orders = [
            ['id' => 'ORD-001', 'customer' => 'John Restaurant', 'amount' => 450.00, 'status' => 'Completed', 'date' => '2026-03-26'],
            ['id' => 'ORD-002', 'customer' => 'Pizza Italia', 'amount' => 320.50, 'status' => 'Pending', 'date' => '2026-03-26'],
            ['id' => 'ORD-003', 'customer' => 'Quick Bite Cafe', 'amount' => 280.75, 'status' => 'Processing', 'date' => '2026-03-25'],
            ['id' => 'ORD-004', 'customer' => 'Family Bistro', 'amount' => 560.00, 'status' => 'Completed', 'date' => '2026-03-25'],
            ['id' => 'ORD-005', 'customer' => 'Gourmet Hub', 'amount' => 410.25, 'status' => 'Pending', 'date' => '2026-03-25'],
        ];

        $top_products = [
            ['name' => 'Regular Pizza Dough', 'quantity' => 245, 'amount' => 1225.00],
            ['name' => 'Mozzarella Cheese 1kg', 'quantity' => 180, 'amount' => 900.00],
            ['name' => 'Tomato Sauce 5L', 'quantity' => 95, 'amount' => 475.00],
            ['name' => 'Olive Oil Premium', 'quantity' => 42, 'amount' => 525.00],
            ['name' => 'Fresh Basil Bundle', 'quantity' => 67, 'amount' => 335.00],
        ];

        $inventory_alerts = [
            ['item' => 'Buffalo Mozzarella', 'current' => 2, 'minimum' => 10, 'status' => 'critical'],
            ['item' => 'San Marzano Tomatoes', 'current' => 5, 'minimum' => 15, 'status' => 'warning'],
            ['item' => 'Extra Virgin Olive Oil', 'current' => 3, 'minimum' => 8, 'status' => 'critical'],
            ['item' => 'Fresh Basil', 'current' => 4, 'minimum' => 12, 'status' => 'warning'],
        ];

        return view('dashboard', compact('stats', 'recent_orders', 'top_products', 'inventory_alerts'));
    }
}

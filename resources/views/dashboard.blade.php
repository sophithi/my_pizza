@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<style>
    .stat-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 24px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #e85d24 0%, #d94a10 100%);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    }

    .stat-card.success::before {
        background: linear-gradient(180deg, #28a745 0%, #20c997 100%);
    }

    .stat-card.warning::before {
        background: linear-gradient(180deg, #ffc107 0%, #ff9800 100%);
    }

    .stat-card.danger::before {
        background: linear-gradient(180deg, #dc3545 0%, #e74c3c 100%);
    }

    .stat-icon {
        font-size: 28px;
        margin-bottom: 12px;
        opacity: 0.7;
    }

    .stat-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #1a1d29;
        margin: 0;
    }

    .stat-change {
        font-size: 12px;
        margin-top: 8px;
        color: #28a745;
    }

    .data-table {
        background: transparent;
    }

    .data-table thead {
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        border-bottom: 2px solid #e9ecef;
    }

    .data-table thead th {
        color: #1a1d29;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 16px 12px;
        border: none;
    }

    .data-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #e9ecef;
    }

    .data-table tbody tr:hover {
        background: #f8f9fa;
    }

    .data-table tbody td {
        padding: 14px 12px;
        vertical-align: middle;
        color: #1a1d29;
    }

    .badge-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-completed {
        background: #d4edda;
        color: #155724;
    }

    .badge-pending {
        background: #fff3cd;
        color: #856404;
    }

    .badge-processing {
        background: #d1ecf1;
        color: #0c5460;
    }

    .alert-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 10px;
        border-left: 4px solid;
    }

    .alert-critical {
        background: #f8d7da;
        border-left-color: #dc3545;
        color: #721c24;
    }

    .alert-warning {
        background: #fff3cd;
        border-left-color: #ffc107;
        color: #856404;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a1d29;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 12px;
    }

    /* .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 4px;
        height: 24px;
        background: linear-gradient(180deg, #e85d24 0%, #d94a10 100%);
        border-radius: 2px;
    } */

    .btn-action {
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%);
        color: #fff;
        border: none;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(232, 93, 36, 0.3);
        color: #fff;
    }

    .view-all-link {
        color: #e85d24;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .view-all-link:hover {
        color: #d94a10;
        text-decoration: underline;
    }
</style>

<!-- Statistics Cards Row -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card success">
            <div class="stat-icon">💰</div>
            <div class="stat-label">Today's Sales</div>
            <h3 class="stat-value">${{ number_format($stats['today_sales'], 2) }}</h3>
            <span class="stat-change">↑ 12.5% from yesterday</span>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-label">Total Orders</div>
            <h3 class="stat-value">{{ $stats['total_orders'] }}</h3>
            <span class="stat-change">{{ $stats['orders_today'] }} orders today</span>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card warning">
            <div class="stat-icon">⚠️</div>
            <div class="stat-label">Low Stock Items</div>
            <h3 class="stat-value">{{ $stats['low_inventory_items'] }}</h3>
            <span class="stat-change">Needs attention</span>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card danger">
            <div class="stat-icon">📋</div>
            <div class="stat-label">Overdue Invoices</div>
            <h3 class="stat-value">{{ $stats['overdue_invoices'] }}</h3>
            <span class="stat-change">Total due: $2,480</span>
        </div>
    </div>
</div>

<!-- Charts and Recent Orders Row -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body" style="padding: 24px;">
                <h6 class="section-title mb-3">Recent Orders</h6>
                <div class="table-responsive">
                    <table class="table table-hover data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_orders as $order)
                            <tr>
                                <td style="font-weight: 600; color: #e85d24;">{{ $order['id'] }}</td>
                                <td>{{ $order['customer'] }}</td>
                                <td>${{ number_format($order['amount'], 2) }}</td>
                                <td>
                                    <span class="badge-status badge-{{ strtolower($order['status']) }}">
                                        {{ $order['status'] }}
                                    </span>
                                </td>
                                <td style="font-size: 13px; color: #6c757d;">{{ $order['date'] }}</td>
                                <td>
                                    <a href="#" class="view-all-link" style="color: #6c757d; font-weight: normal;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="text-align: right; margin-top: 16px;">
                    <a href="/orders" class="view-all-link">View all orders →</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; margin-bottom: 20px;">
            <div class="card-body" style="padding: 24px;">
                <h6 class="section-title mb-3">Inventory Alerts</h6>
                <div>
                    @foreach($inventory_alerts as $alert)
                    <div class="alert-item alert-{{ $alert['status'] }}">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; margin-bottom: 4px;">{{ $alert['item'] }}</div>
                            <div style="font-size: 12px;">Stock: {{ $alert['current'] }}/{{ $alert['minimum'] }} units</div>
                        </div>
                        <div style="font-weight: 600; font-size: 12px;">{{ round(($alert['current']/$alert['minimum'])*100) }}%</div>
                    </div>
                    @endforeach
                </div>
                <a href="/inventory" class="btn-action btn-primary-custom w-100" style="margin-top: 16px; display: inline-block; text-align: center; text-decoration: none;">
                    Manage Inventory
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body" style="padding: 24px;">
                <h6 class="section-title mb-3">Quick Actions</h6>
                <div class="d-grid gap-2">
                    <a href="/orders/create" class="btn-action btn-primary-custom" style="display: block; text-align: center; text-decoration: none;">
                        <i class="fas fa-plus-circle"></i> New Order
                    </a>
                    <a href="/customers/create" class="btn-action" style="background: #f8f9fa; color: #1a1d29; text-decoration: none; border: 1px solid #e9ecef; display: block; text-align: center;">
                        <i class="fas fa-user-plus"></i> Add Customer
                    </a>
                    <a href="/reports" class="btn-action" style="background: #f8f9fa; color: #1a1d29; text-decoration: none; border: 1px solid #e9ecef; display: block; text-align: center;">
                        <i class="fas fa-chart-bar"></i> View Reports
                    </a>
                    <a href="/invoices" class="btn-action" style="background: #f8f9fa; color: #1a1d29; text-decoration: none; border: 1px solid #e9ecef; display: block; text-align: center;">
                        <i class="fas fa-file-invoice"></i> Invoices
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Products Row -->
<div class="row g-3">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body" style="padding: 24px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="section-title mb-0">Top Selling Items</h6>
                    <a href="/products" class="view-all-link">See all →</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover data-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Units Sold</th>
                                <th>Total Revenue</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($top_products as $product)
                            <tr>
                                <td>
                                    <div>{{ $product['name'] }}</div>
                                </td>
                                <td>{{ $product['quantity'] }} units</td>
                                <td style="font-weight: 600; color: #e85d24;">${{ number_format($product['amount'], 2) }}</td>
                                <td style="color: #28a745;">
                                    <i class="fas fa-arrow-up"></i> +8.5%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

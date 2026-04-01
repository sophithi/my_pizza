@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<style>
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }

    .dashboard-title {
        font-size: 28px;
        font-weight: 700;
        color: #1a1d29;
        margin: 0;
    }

    .dashboard-refresh {
        font-size: 12px;
        color: #6c757d;
    }

    .stat-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 24px;
        transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
        overflow: hidden;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        display: block;
        animation: fadeInScale 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) backwards;
    }

    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.92);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Stagger animation for stat cards */
    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.15s; }
    .stat-card:nth-child(4) { animation-delay: 0.2s; }

    .stat-card:hover {
        color: inherit;
        text-decoration: none;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #e85d24 0%, #d94a10 100%);
        transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at top-right, rgba(232, 93, 36, 0.05), transparent);
        opacity: 0;
        transition: opacity 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        pointer-events: none;
    }

    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15),
                    0 0 20px rgba(232, 93, 36, 0.1);
    }

    .stat-card:hover::before {
        width: 6px;
        box-shadow: 0 0 15px rgba(232, 93, 36, 0.3);
    }

    .stat-card:hover::after {
        opacity: 1;
    }

    .stat-card:active {
        transform: translateY(-6px);
    }

    .stat-card.success {
        border-top: 2px solid #28a745;
    }

    .stat-card.success::before {
        background: linear-gradient(180deg, #28a745 0%, #20c997 100%);
    }

    .stat-card.warning {
        border-top: 2px solid #ffc107;
    }

    .stat-card.warning::before {
        background: linear-gradient(180deg, #ffc107 0%, #ff9800 100%);
    }

    .stat-card.danger {
        border-top: 2px solid #dc3545;
    }

    .stat-card.danger::before {
        background: linear-gradient(180deg, #dc3545 0%, #e74c3c 100%);
    }

    .stat-card.info {
        border-top: 2px solid #17a2b8;
    }

    .stat-card.info::before {
        background: linear-gradient(180deg, #17a2b8 0%, #138496 100%);
    }

    .stat-icon {
        font-size: 32px;
        margin-bottom: 16px;
        display: inline-block;
        transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        animation: popIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes popIn {
        0% {
            transform: scale(0.5) rotate(-10deg);
            opacity: 0;
        }
        50% {
            transform: scale(1.1) rotate(5deg);
        }
        100% {
            transform: scale(1) rotate(0deg);
            opacity: 1;
        }
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.15) rotate(5deg);
    }

    .stat-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }

    .stat-card:hover .stat-label {
        color: #1a1d29;
    }

    .stat-value {
        font-size: 36px;
        font-weight: 800;
        color: #1a1d29;
        margin: 0 0 12px 0;
        display: flex;
        align-items: baseline;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .stat-card:hover .stat-value {
        color: #e85d24;
        transform: scale(1.05);
    }

    .stat-change {
        font-size: 13px;
        margin-top: 0;
        color: #28a745;
        font-weight: 500;
    }

    .stat-change.negative {
        color: #dc3545;
    }

    .stat-change.neutral {
        color: #6c757d;
    }

    .data-table {
        background: transparent;
    }

    .data-table thead {
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        border-bottom: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .data-table thead th {
        color: #1a1d29;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 16px 14px;
        border: none;
        transition: all 0.3s ease;
    }

    .data-table tbody tr {
        transition: all 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        border-bottom: 1px solid #e9ecef;
        animation: slideInUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) backwards;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .data-table tbody tr:nth-child(1) { animation-delay: 0.05s; }
    .data-table tbody tr:nth-child(2) { animation-delay: 0.1s; }
    .data-table tbody tr:nth-child(3) { animation-delay: 0.15s; }
    .data-table tbody tr:nth-child(4) { animation-delay: 0.2s; }
    .data-table tbody tr:nth-child(5) { animation-delay: 0.25s; }

    .data-table tbody tr:hover {
        background: #f8f9fa;
        box-shadow: inset 4px 0 0 #e85d24;
        transform: scale(1.01);
    }

    .data-table tbody tr:active {
        transform: scale(0.99);
    }

    .data-table tbody td {
        padding: 16px 14px;
        vertical-align: middle;
        color: #1a1d29;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .order-id {
        font-weight: 700;
        color: #e85d24;
        font-size: 13px;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .data-table tbody tr:hover .order-id {
        text-shadow: 0 0 10px rgba(232, 93, 36, 0.2);
        font-size: 14px;
    }

    .badge-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        animation: popIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .badge-status:hover {
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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

    .badge-sent {
        background: #e2e3e5;
        color: #383d41;
    }

    .badge-paid {
        background: #d4edda;
        color: #155724;
    }

    .alert-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        border-radius: 8px;
        margin-bottom: 12px;
        border-left: 4px solid;
        transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        animation: slideInRight 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) backwards;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .alert-item:nth-child(1) { animation-delay: 0.05s; }
    .alert-item:nth-child(2) { animation-delay: 0.1s; }
    .alert-item:nth-child(3) { animation-delay: 0.15s; }

    .alert-item:hover {
        transform: translateX(6px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
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

    .alert-content {
        flex: 1;
        transition: all 0.3s ease;
    }

    .alert-item-name {
        font-weight: 600;
        margin-bottom: 4px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .alert-item:hover .alert-item-name {
        color: #1a1d29;
        font-size: 15px;
    }

    .alert-item-stock {
        font-size: 12px;
        opacity: 0.8;
        transition: opacity 0.3s ease;
    }

    .alert-item:hover .alert-item-stock {
        opacity: 1;
    }

    .alert-percentage {
        font-weight: 600;
        font-size: 13px;
        min-width: 40px;
        text-align: right;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .alert-item:hover .alert-percentage {
        font-size: 14px;
        transform: scale(1.1);
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a1d29;
        margin-bottom: 20px;
        position: relative;
        animation: slideInDown 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .btn-action {
        padding: 10px 18px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        display: inline-block;
        text-align: center;
        text-decoration: none;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%);
        color: #fff;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(232, 93, 36, 0.3);
        color: #fff;
        text-decoration: none;
    }

    .btn-sm-custom {
        padding: 6px 12px;
        font-size: 12px;
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

    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: #6c757d;
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .empty-state-text {
        font-size: 16px;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .card-shadow {
        border: 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .product-trend {
        font-size: 13px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .trend-up {
        color: #28a745;
    }

    .trend-down {
        color: #dc3545;
    }

    @media (max-width: 768px) {
        .stat-card {
            padding: 18px;
        }

        .stat-value {
            font-size: 28px;
        }

        .dashboard-title {
            font-size: 24px;
        }

        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .breadcrumb-nav {
            gap: 6px;
            margin-bottom: 16px;
            font-size: 12px;
        }

        .breadcrumb-item {
            font-size: 11px;
        }

        .breadcrumb-separator {
            display: none;
        }

        .stat-icon {
            font-size: 24px;
            margin-bottom: 12px;
        }

        .stat-label {
            font-size: 11px;
        }

        .stat-value {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .stat-change {
            font-size: 11px;
        }

        .data-table thead th {
            font-size: 11px;
            padding: 12px 8px;
        }

        .data-table tbody td {
            padding: 12px 8px;
            font-size: 12px;
        }

        .order-id {
            font-size: 12px;
        }

        .badge-status {
            padding: 4px 8px;
            font-size: 10px;
        }

        .alert-item {
            padding: 10px 12px;
            margin-bottom: 10px;
        }

        .alert-item-name {
            font-size: 13px;
        }

        .alert-item-stock {
            font-size: 11px;
        }

        .alert-percentage {
            font-size: 12px;
        }

        .quick-nav {
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            margin-top: 16px;
            padding-top: 16px;
        }

        .quick-nav-btn {
            padding: 10px 6px;
            font-size: 11px;
            gap: 4px;
        }

        .quick-nav-icon {
            font-size: 18px;
        }

        .quick-actions {
            gap: 6px;
        }

        .btn-primary-custom,
        .btn-outline-custom {
            padding: 8px 12px;
            font-size: 11px;
        }

        .section-title {
            font-size: 16px;
            margin-bottom: 16px;
        }

        .product-trend {
            font-size: 12px;
        }

        .empty-state {
            padding: 32px 16px;
        }

        .empty-state-icon {
            font-size: 40px;
        }

        .empty-state-text {
            font-size: 14px;
        }

        .dashboard-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 20px;
        }

        .row {
            margin-left: -8px !important;
            margin-right: -8px !important;
        }

        .col-lg-8,
        .col-lg-4,
        .col-lg-3,
        .col-lg-12 {
            padding-left: 8px !important;
            padding-right: 8px !important;
        }

        .card {
            margin-bottom: 16px !important;
        }

        .card-body {
            padding: 16px !important;
        }

        .view-all-link {
            font-size: 12px;
        }
    }

    @media (max-width: 576px) {
        .dashboard-title {
            font-size: 20px;
        }

        .stat-card {
            padding: 14px;
        }

        .stat-value {
            font-size: 20px;
        }

        .stat-icon {
            font-size: 20px;
        }

        .stat-label {
            font-size: 10px;
        }

        .stat-change {
            font-size: 10px;
        }

        .section-title {
            font-size: 14px;
            margin-bottom: 12px;
        }

        .data-table {
            font-size: 11px;
        }

        .data-table thead th {
            font-size: 10px;
            padding: 8px 4px;
        }

        .data-table tbody td {
            padding: 8px 4px;
            font-size: 11px;
        }

        .badge-status {
            padding: 3px 6px;
            font-size: 9px;
        }

        .quick-nav {
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
        }

        .quick-nav-btn {
            padding: 8px 4px;
            font-size: 10px;
        }

        .quick-nav-icon {
            font-size: 16px;
        }

        .alert-item {
            padding: 8px 10px;
            margin-bottom: 8px;
            flex-direction: column;
            gap: 6px;
        }

        .alert-content {
            width: 100%;
        }

        .alert-percentage {
            text-align: left;
            font-size: 11px;
        }

        .breadcrumb-nav {
            gap: 4px;
            font-size: 10px;
        }

        .breadcrumb-item {
            font-size: 10px;
        }

        .btn-primary-custom,
        .btn-outline-custom {
            padding: 6px 10px;
            font-size: 10px;
            width: 100%;
            margin-bottom: 6px;
        }

        .quick-actions {
            flex-direction: column;
            gap: 4px;
        }

        .view-all-link {
            font-size: 11px;
        }

        .product-trend {
            font-size: 11px;
        }

        .empty-state {
            padding: 24px 12px;
        }

        .empty-state-icon {
            font-size: 36px;
            margin-bottom: 12px;
        }

        .empty-state-text {
            font-size: 13px;
            margin-bottom: 6px;
        }

        .card-body {
            padding: 12px !important;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Hide less important columns on mobile */
        .data-table tbody td:nth-child(5) {
            display: none;
        }

        .data-table thead th:nth-child(5) {
            display: none;
        }
    }

    .quick-actions {
        display: flex;
        gap: 8px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .btn-outline-custom {
        padding: 8px 14px;
        border: 1px solid #e9ecef;
        background: transparent;
        color: #e85d24;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .btn-outline-custom:hover {
        background: #f8f9fa;
        border-color: #e85d24;
        color: #d94a10;
    }

    .breadcrumb-nav {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .breadcrumb-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #6c757d;
    }

    .breadcrumb-item.active {
        color: #1a1d29;
        font-weight: 600;
    }

    .breadcrumb-separator {
        color: #dee2e6;
    }

    .quick-nav {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 8px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
    }

    .quick-nav-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 12px;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        background: transparent;
        color: #1a1d29;
        text-decoration: none;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .quick-nav-btn:hover {
        background: #f8f9fa;
        border-color: #e85d24;
        color: #e85d24;
        text-decoration: none;
    }

    .quick-nav-icon {
        font-size: 20px;
    }

</style>

<!-- Dashboard Header -->
<div class="dashboard-header">
    <div>
        <h1 class="dashboard-title">ទិន្នន័យទូទៅ</h1>
        <div class="dashboard-refresh" style="margin-top: 4px;">Last updated: just now</div>
    </div>
    <div>
        <a href="javascript:location.reload()" class="btn-outline-custom">
            <i class="fas fa-sync-alt"></i> Refresh
        </a>
    </div>
</div>


<!-- Statistics Cards Row -->
<div class="row g-2 g-md-3 mb-3 mb-md-4">
    <div class="col-6 col-md-6 col-lg-3">
        <a href="/orders?status=completed&date=today" class="stat-card success" title="View today's sales">
            <div class="stat-icon">💰</div>
            <div class="stat-label">ការលក់ថ្ងៃនេះ</div>
            <div class="stat-value">
                ${{ number_format($stats['today_sales'], 2) }}
            </div>
            <div>

            </div>
      
        </a>
    </div>
    <div class="col-6 col-md-6 col-lg-3">
        <a href="/orders" class="stat-card info" title="View all orders">
            <div class="stat-icon">📦</div>
            <div class="stat-label">ការកម្មង់ថ្ងៃនេះ</div>
            <div class="stat-value">
                {{ $stats['total_orders'] }}
            </div>
            <span class="stat-change neutral">
                <i class="fas fa-check-circle"></i> {{ $stats['orders_today'] }} Today
            </span>
        </a>
    </div>
    <div class="col-6 col-md-6 col-lg-3">
        <a href="/inventory?status=low" class="stat-card warning" title="View low stock items">
            <div class="stat-icon">⚠️</div>
            <div class="stat-label">Low Stock Items</div>
            <div class="stat-value">    
                {{ $stats['low_inventory_items'] }}
            </div>
            <span class="stat-change negative">
                <i class="fas fa-exclamation-triangle"></i> Attention
            </span>
        </a>
    </div>
    <div class="col-6 col-md-6 col-lg-3">
        <a href="/invoices?status=overdue" class="stat-card danger" title="View overdue invoices">
            <div class="stat-icon">📋</div>
            <div class="stat-label">Overdue Invoices</div>
            <div class="stat-value">
                {{ $stats['overdue_invoices'] }}
            </div>
            <span class="stat-change negative">
                <i class="fas fa-clock"></i> Action needed
            </span>
        </a>
    </div>
</div>

<!-- Additional Metrics Row -->
<div class="row g-2 g-md-3 mb-3 mb-md-4">
    <div class="col-6 col-md-6 col-lg-4">
        <a href="/customers" class="stat-card" title="View all customers">
            <div class="stat-icon">👥</div>
            <div class="stat-label">Total Customers</div>
            <div class="stat-value">{{ $stats['customers'] }}</div>
            <span class="stat-change neutral">
                <i class="fas fa-user-plus"></i> Active
            </span>
        </a>
    </div>
    <div class="col-6 col-md-6 col-lg-4">
        <a href="/orders?status=completed" class="stat-card success" title="View completed orders">
            <div class="stat-icon">✅</div>
            <div class="stat-label">Completion Rate</div>
            <div class="stat-value">{{ $stats['recovery_rate'] }}%</div>
            <span class="stat-change">
                <i class="fas fa-chart-line"></i> Completed
            </span>
        </a>
    </div>
    <div class="col-6 col-md-6 col-lg-4">
        <a href="/inventory?alert=yes" class="stat-card warning" title="View reorder items">
            <div class="stat-icon">📊</div>
            <div class="stat-label">Stock Alerts</div>
            <div class="stat-value">{{ $stats['stock_alerts'] }}</div>
            <span class="stat-change neutral">
                <i class="fas fa-bell"></i> Reorder
            </span>
        </a>
    </div>
</div>
<div class="row g-2 g-md-3 mb-3 mb-md-4">
    <div class="col-12 col-lg-8">
        <div class="card card-shadow" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body" style="padding: 24px;">
                <div class="section-header">
                    <h6 class="section-title mb-0">Recent Orders</h6>
                    <a href="/orders" class="view-all-link">View all →</a>
                </div>
                <div class="table-responsive">
                    @if(count($recent_orders) > 0)
                        <table class="table table-hover data-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th class="d-none d-md-table-cell">Customer</th>
                                    <th>Amount</th>
                                    <th class="d-none d-sm-table-cell">Status</th>
                                    <th class="d-none d-lg-table-cell">Date</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_orders as $order)
                                <tr style="cursor: pointer;" onclick="window.location.href='/orders/{{ $order['id'] }}'">
                                    <td>
                                        <a href="/orders/{{ $order['id'] }}" class="order-id" style="text-decoration: none; color: #e85d24;" title="View order details">#{{ $order['id'] }}</a>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <a href="/customers/search?name={{ urlencode($order['customer']) }}" style="text-decoration: none; color: inherit;" title="View customer">
                                            <strong>{{ $order['customer'] }}</strong>
                                        </a>
                                    </td>
                                    <td>
                                        <strong style="color: #28a745;">${{ number_format($order['amount'], 2) }}</strong>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <span class="badge-status badge-{{ strtolower(str_replace(' ', '_', $order['status'])) }}">
                                            {{ $order['status'] }}
                                        </span>
                                    </td>
                                    <td class="d-none d-lg-table-cell" style="font-size: 13px; color: #6c757d;">
                                        {{ $order['date'] }}
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="/orders/{{ $order['id'] }}" class="view-all-link" title="View details" onclick="event.stopPropagation();">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">📭</div>
                            <div class="empty-state-text">No orders yet</div>
                            <div style="font-size: 13px;">Orders will appear here once you create them</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card card-shadow" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body" style="padding: 24px;">
                <h6 class="section-title mb-3">Inventory Alerts</h6>
                @if(count($inventory_alerts) > 0)
                    <div>
                        @foreach($inventory_alerts as $alert)
                        <a href="/inventory?search={{ urlencode($alert['item']) }}" style="text-decoration: none; color: inherit;" title="View inventory">
                            <div class="alert-item alert-{{ $alert['status'] }}" style="cursor: pointer; transition: all 0.2s ease;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                <div class="alert-content">
                                    <div class="alert-item-name">{{ $alert['item'] }}</div>
                                    <div class="alert-item-stock">Stock: <strong>{{ $alert['current'] }}/{{ $alert['minimum'] }}</strong> units</div>
                                </div>
                                <div class="alert-percentage">{{ round(($alert['current']/$alert['minimum'])*100, 0) }}%</div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state" style="padding: 32px 16px;">
                        <div class="empty-state-icon">✅</div>
                        <div class="empty-state-text">All stock levels are good</div>
                        <div style="font-size: 13px;">No items below reorder level</div>
                    </div>
                @endif
                <div class="quick-actions">
                    <a href="/inventory" class="btn-action btn-primary-custom flex-grow-1" style="text-align: center; text-decoration: none;">
                        <i class="fas fa-warehouse"></i> <span class="d-none d-md-inline">Manage </span>Inventory
                    </a>
                    <a href="/purchasing" class="btn-outline-custom">
                        <i class="fas fa-shopping-cart"></i> <span class="d-none d-md-inline">Order </span>Stock
                    </a>
                </div>

             
            </div>
        </div>
    </div>
</div>

<!-- Top Products Row -->
<div class="row g-2 g-md-3">
    <div class="col-12">
        <div class="card card-shadow" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body" style="padding: 24px;">
                <div class="section-header">
                    <h6 class="section-title mb-0">📈 Top Selling Items</h6>
                    <a href="/products" class="view-all-link">See all →</a>
                </div>
                <div class="table-responsive">
                    @if(count($top_products) > 0)
                        <table class="table table-hover data-table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th class="d-none d-md-table-cell">Units Sold</th>
                                    <th>Revenue</th>
                                    <th class="d-none d-sm-table-cell" style="text-align: center;">Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($top_products as $product)
                                <tr style="cursor: pointer;" onclick="window.location.href='/products?search={{ urlencode($product['name']) }}'">
                                    <td>
                                        <div>
                                            <a href="/products?search={{ urlencode($product['name']) }}" style="text-decoration: none; color: inherit;" title="View product details">
                                                <strong>{{ $product['name'] }}</strong>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <span style="background: #f0f0f0; padding: 4px 8px; border-radius: 4px; font-weight: 500;">
                                            {{ $product['quantity'] }} units
                                        </span>
                                    </td>
                                    <td>
                                        <strong style="color: #28a745;">
                                            ${{ number_format($product['amount'], 2) }}
                                        </strong>
                                    </td>
                                    <td class="d-none d-sm-table-cell" style="text-align: center;">
                                        <span class="product-trend trend-up">
                                            <i class="fas fa-arrow-up"></i> <span class="d-none d-md-inline">+8.5%</span>
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">📦</div>
                            <div class="empty-state-text">No sales data yet</div>
                            <div style="font-size: 13px;">Top products will appear here once orders are completed</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

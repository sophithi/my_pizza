<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizza Happy Family</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom styles -->
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, #1a1d29 0%, #0f1117 100%);
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%);
            padding: 24px 20px;
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            text-align: center;
            box-shadow: 0 4px 15px rgba(232, 93, 36, 0.3);
            letter-spacing: 0.5px;
        }

        .sidebar nav {
            padding: 20px 0;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }

        .sidebar .nav-label {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.4);
            padding: 16px 24px 8px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
            margin-top: 12px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.65);
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            border-left-color: #e85d24;
            padding-left: 28px;
        }

        .sidebar .nav-link.active {
            color: #e85d24;
            background: rgba(232, 93, 36, 0.15);
            border-left-color: #e85d24;
            font-weight: 600;
        }

        /* Top bar Styling */
        .topbar {
            background: #fff;
            border-bottom: 2px solid #e9ecef;
            padding: 16px 32px;
            margin-left: 260px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 999;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar .page-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a1d29;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .topbar .user-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(232, 93, 36, 0.3);
        }

        .user-name {
            font-size: 14px;
            font-weight: 500;
            color: #1a1d29;
            margin: 0;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 32px;
            min-height: calc(100vh - 120px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                margin-left: -260px;
                transition: margin-left 0.3s ease;
            }

            .topbar,
            .main-content {
                margin-left: 0;
            }

            .sidebar.active {
                margin-left: 0;
            }
        }

        /* Scrollbar Styling */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(232, 93, 36, 0.5);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #e85d24;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
          
            Pizza Happy Family  
        </div>
        <nav class="mt-2">
            <div class="nav-label">Main</div>
            <a href="/" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="/orders" class="nav-link {{ request()->is('orders*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
            <a href="/customers" class="nav-link {{ request()->is('customers*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Customers
            </a>

            <div class="nav-label">Warehouse</div>
            <a href="/inventory" class="nav-link {{ request()->is('inventory*') ? 'active' : '' }}">
                <i class="fas fa-boxes"></i> Inventory
            </a>
            <a href="/purchasing" class="nav-link {{ request()->is('purchasing*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice"></i> Purchasing
            </a>
            <a href="/delivery" class="nav-link {{ request()->is('delivery*') ? 'active' : '' }}">
                <i class="fas fa-truck"></i> Delivery
            </a>

            <div class="nav-label">Finance</div>
            <a href="/invoices" class="nav-link {{ request()->is('invoices*') ? 'active' : '' }}">
                <i class="fas fa-receipt"></i> Invoices
            </a>
            <a href="/reports" class="nav-link {{ request()->is('reports*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> Reports
            </a>

            <div class="nav-label">System</div>
            <a href="/users" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i> Users
            </a>
            <a href="/settings" class="nav-link {{ request()->is('settings*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i> Settings
            </a>
        </nav>
    </div>

    <!-- Top bar -->
    <div class="topbar">
        <h1 class="page-title">@yield('title', 'Dashboard')</h1>
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <p class="user-name">{{ auth()->user()->name ?? 'Admin' }}</p>
        </div>
    </div>

    <!-- Page content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html>
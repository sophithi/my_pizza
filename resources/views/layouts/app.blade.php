<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pizza Happy Family</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Noto+Sans+Khmer&family=Hanuman&family=Battambang&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', 'Noto Sans Khmer', 'Hanuman', 'Battambang', 'Khmer OS', sans-serif;
        }

        body {
            background: #f5f7fa;
            min-height: 100vh;
            overflow-x: hidden;
        }

        html {
            background: #f5f7fa;
        }

        /* ===== SIDEBAR (Option 4: Pizza Brand Dark Theme with Live Badges & Counters) ===== */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: #0f172a;
            border-right: 1px solid #1e293b;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: width 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            overflow-y: auto;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.25);
        }

        .sidebar::-webkit-scrollbar,
        .sidebar nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track,
        .sidebar nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb,
        .sidebar nav::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 4px;
        }

        .sidebar:hover::-webkit-scrollbar-thumb,
        .sidebar nav:hover::-webkit-scrollbar-thumb {
            background: #334155;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-brand {
            background: #0f172a;
            border-bottom: 1px solid #1e293b;
            padding: 16px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 68px;
            gap: 8px;
        }

        .sidebar-brand .brand-inner {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            min-width: 0;
            flex: 1;
        }

        .sidebar-brand-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: linear-gradient(135deg, #ea580c, #c2410c);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.4);
            flex-shrink: 0;
        }

        .sidebar-brand-text {
            color: #f8fafc;
            font-size: 15px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: all 0.2s ease;
            text-decoration: none;
            letter-spacing: -0.2px;
        }

        /* In collapsed mode, hide brand-inner completely to avoid overlap with collapse button */
        .sidebar.collapsed .brand-inner {
            display: none;
        }

        .sidebar-collapse-btn {
            background: #1e293b;
            border: 1px solid #334155;
            color: #94a3b8;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .sidebar-collapse-btn:hover {
            background: #ea580c;
            color: #ffffff;
            border-color: #ea580c;
            transform: scale(1.05);
        }

        .sidebar.collapsed .sidebar-brand {
            justify-content: center;
            padding: 16px 8px;
        }

        .sidebar.collapsed .sidebar-collapse-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            font-size: 13px;
        }

        /* Nav labels */
        .sidebar .nav-label {
            font-size: 11px;
            color: #64748b;
            padding: 16px 20px 6px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .sidebar.collapsed .nav-label {
            opacity: 0;
            height: 0;
            padding: 0;
            margin: 0;
        }

        /* Nav links */
        .sidebar nav {
            padding: 10px 0 30px;
            max-height: calc(100vh - 72px);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar .nav-link {
            color: #cbd5e1;
            padding: 10px 14px;
            margin: 2px 12px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            border-left: none;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.15s ease;
            position: relative;
        }

        .sidebar .nav-link:hover {
            color: #ffffff;
            background: rgba(234, 88, 12, 0.15);
        }

        .sidebar .nav-link:hover i {
            color: #fb923c;
            transform: scale(1.1);
        }

        .sidebar .nav-link.active {
            color: #ffffff;
            background: linear-gradient(135deg, #ea580c, #c2410c);
            font-weight: 700;
            box-shadow: 0 4px 14px rgba(234, 88, 12, 0.4);
        }

        .sidebar .nav-link i {
            font-size: 16px;
            width: 22px;
            text-align: center;
            flex-shrink: 0;
            color: #94a3b8;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link:hover i {
            color: #fb923c;
        }

        .sidebar .nav-link.active i {
            color: #ffffff;
        }

        .sidebar .nav-link span {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: all 0.2s ease;
        }

        /* Nav Live Badges */
        .nav-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: 0.2px;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .nav-badge.badge-amber {
            background: #fef08a;
            color: #854d0e;
        }

        .sidebar .nav-link.active .nav-badge.badge-amber {
            background: #ffffff;
            color: #c2410c;
        }

        .nav-badge.badge-blue {
            background: #bfdbfe;
            color: #1e40af;
        }

        .sidebar .nav-link.active .nav-badge.badge-blue {
            background: #ffffff;
            color: #1e40af;
        }

        .nav-badge.badge-emerald {
            background: #bbf7d0;
            color: #166534;
        }

        .sidebar .nav-link.active .nav-badge.badge-emerald {
            background: #ffffff;
            color: #166534;
        }

        /* Collapsed nav links - icons only */
        .sidebar.collapsed .nav-link {
            padding: 12px;
            margin: 4px 10px;
            justify-content: center;
            border-radius: 10px;
            position: relative;
        }

        .sidebar.collapsed .nav-link span {
            width: 0;
            opacity: 0;
            pointer-events: none;
            display: none;
        }

        .sidebar.collapsed .nav-link i {
            font-size: 18px;
            width: auto;
        }

        /* Collapsed badge dot indicator */
        .sidebar.collapsed .nav-badge {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 8px;
            height: 8px;
            padding: 0;
            border-radius: 50%;
            font-size: 0;
            line-height: 0;
            border: 2px solid #0f172a;
        }
        .sidebar.collapsed .nav-badge.badge-amber {
            background: #eab308;
        }
        .sidebar.collapsed .nav-badge.badge-blue {
            background: #3b82f6;
        }
        .sidebar.collapsed .nav-badge.badge-emerald {
            background: #22c55e;
        }

        /* Tooltip on hover when collapsed */
        .sidebar.collapsed .nav-link::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%) translateX(8px);
            background: #1e293b;
            color: #f8fafc;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease;
            z-index: 1001;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
            border: 1px solid #334155;
        }

        .sidebar.collapsed .nav-link:hover::after {
            opacity: 1;
            transform: translateY(-50%) translateX(12px);
        }

        .sidebar.collapsed .nav-link:hover {
            background: rgba(234, 88, 12, 0.15);
        }

        /* Sidebar close btn (mobile only) */
        .sidebar-close-btn {
            display: none;
            position: absolute;
            top: 16px;
            right: 16px;
            background: #1e293b;
            border: 1px solid #334155;
            color: #94a3b8;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            z-index: 10;
        }

        /* ===== TOPBAR ===== */
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 14px 28px;
            margin-left: 260px;
            position: sticky;
            top: 0;
            z-index: 999;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: margin-left 0.15s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .topbar.sidebar-collapsed {
            margin-left: 80px;
        }

        .topbar .page-title {
            font-size: 20px;
            font-weight: 700;
            color: #1a1d29;
            margin: 0;
        }

        .topbar .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e85d24, #d94a10);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            transform: scale(1.05);
        }

        .user-avatar-img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
        }

        .user-name {
            font-size: 14px;
            font-weight: 500;
            color: #1a1d29;
            margin: 0;
        }

        /* Navbar toggle (mobile) */
        .navbar-toggle {
            display: none;
            background: transparent;
            border: none;
            color: #1a1d29;
            font-size: 20px;
            cursor: pointer;
            padding: 6px;
            margin-right: 12px;
            transition: all 0.3s ease;
        }

        .navbar-toggle:hover {
            color: #e85d24;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: 260px;
            padding: 28px;
            min-height: calc(100vh - 65px);
            background: #f5f7fa;
            position: relative;
            z-index: 1;
            transition: margin-left 0.15s ease;
        }

        .main-content.sidebar-collapsed {
            margin-left: 80px;
        }

        /* ===== OVERLAYS ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            backdrop-filter: blur(2px);
        }

        .sidebar-overlay.active {
            display: block;
        }



        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .navbar-toggle {
                display: flex;
                align-items: center;
            }

            .sidebar-collapse-btn {
                display: none;
            }

            .sidebar {
                width: 260px;
                margin-left: -260px;
                transition: margin-left 0.35s ease;
            }

            .sidebar.show {
                margin-left: 0;
            }

            .sidebar.collapsed {
                width: 260px;
            }

            .topbar {
                margin-left: 0;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
            }

            .topbar.sidebar-collapsed {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
                margin-top: 65px;
                padding: 16px;
            }

            .main-content.sidebar-collapsed {
                margin-left: 0;
            }

            .sidebar-close-btn {
                display: block;
            }

            .notification-panel {
                width: 100%;
                right: -100%;
            }
        }

        @media (max-width: 576px) {
            .user-name {
                display: none;
            }

            .topbar {
                padding: 12px 16px;
            }

            .topbar .page-title {
                font-size: 17px;
            }
        }


        .kh {
            font-family: 'Noto Sans Khmer', 'Hanuman', 'Battambang', 'Khmer OS', sans-serif;
            font-size: 14px;
            line-height: 1.6;
        }

        .req-tag {
            color: #dc2626;
            font-size: 11px;
            font-weight: 600;
            margin-left: 4px;
            letter-spacing: 0.1px;
            display: inline-block;
        }
    </style>
    @stack('styles')
    <style>
        .no-transition,
        .no-transition * {
            transition: none !important
        }
    </style>
</head>

<body class="no-transition">

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <button class="sidebar-close-btn" id="sidebarCloseBtn"><i class="fas fa-times"></i></button>
        <div class="sidebar-brand">
            <a href="/" class="brand-inner">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-pizza-slice"></i>
                </div>
                <span class="sidebar-brand-text">Pizza Happy Family</span>
            </a>
            <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="បង្រួម / ពង្រីក">
                <i class="fas fa-chevron-left" id="collapseIcon"></i>
            </button>
        </div>
        @php
            $user = auth()->user();

            $isAdmin = $user->isAdmin();
            $isManager = $user->isManager();
            $isStaff = $user->isStaff();
            $isInventory = $user->isStaffInventory();
            $isAuditor = $user->isAuditor();

            $isAdminOrManager = $isAdmin || $isManager;
            $isOfficeStaff = $isStaff && !$isInventory;

            // Live workflow counters for Option 4 Pizza Theme Badges
            $todayActiveOrdersCount = \App\Models\Order::whereDate('order_date', \Carbon\Carbon::today())
                ->whereIn('status', ['pending', 'processing'])
                ->count();

            $todayPackingCount = \App\Models\Invoice::whereNotNull('packing_sent_at')
                ->whereNull('packing_completed_at')
                ->whereDate('packing_sent_at', \Carbon\Carbon::today())
                ->count();

            $todayDeliveringCount = \App\Models\Order::whereDate('order_date', \Carbon\Carbon::today())
                ->where('status', 'delivering')
                ->count();
        @endphp

        <nav>

            {{-- 1. Dashboard (ទំព័រដើម) --}}
            @if(!$isInventory)
                <div class="nav-label">ទូទៅ</div>
                <a href="/" class="nav-link {{ request()->is('/') || request()->is('dashboard') ? 'active' : '' }}" data-tooltip="ទំព័រដើម">
                    <i class="fas fa-tachometer-alt"></i><span>ទំព័រដើម</span>
                </a>
            @endif

            {{-- 2. Sales & Orders (ផ្នែកលក់ & កុម្ម៉ង់) --}}
            @if(!$isInventory && !$isAuditor)
                <div class="nav-label">ផ្នែកលក់ & កុម្ម៉ង់</div>
                <a href="/orders" class="nav-link {{ request()->is('orders*') ? 'active' : '' }}" data-tooltip="ចេញវិក្កយបត្រ">
                    <i class="fas fa-shopping-cart"></i><span>ចេញវិក្កយបត្រ</span>
                    @if($todayActiveOrdersCount > 0)
                        <span class="nav-badge badge-amber" title="{{ $todayActiveOrdersCount }} កំពុងដំណើរការ">{{ $todayActiveOrdersCount }}</span>
                    @endif
                </a>
                <a href="/customers" class="nav-link {{ request()->is('customers*') ? 'active' : '' }}" data-tooltip="អតិថិជន">
                    <i class="fas fa-users"></i><span>អតិថិជន</span>
                </a>
                <a href="{{ route('customer-locations.index') }}" class="nav-link {{ request()->is('customer-locations*') ? 'active' : '' }}" data-tooltip="ទីតាំងអតិថិជន">
                    <i class="fas fa-map-marker-alt"></i><span>ទីតាំងអតិថិជននៅលើផែនទី</span>
                </a>
                <a href="/salespersons" class="nav-link {{ request()->is('salespersons*') ? 'active' : '' }}" data-tooltip="ភ្នាក់ងារលក់">
                    <i class="fas fa-user-tie"></i><span>ភ្នាក់ងារលក់</span>
                </a>
            @endif

            {{-- 3. Preparation & Delivery (ផ្នែករៀបចំ & ដឹកជញ្ជូន) --}}
            @if($isAdmin || $isManager || $isStaff || $isInventory || $isOfficeStaff)
                <div class="nav-label">រៀបចំ & ដឹកជញ្ជូន</div>
            @endif
            @if($isAdmin || $isManager || $isStaff || $isInventory)
                <a href="{{ route('packing.index') }}" class="nav-link {{ request()->is('packing*') ? 'active' : '' }}" data-tooltip="រៀបចំទំនិញ">
                    <i class="fas fa-box-open"></i><span>រៀបចំទំនិញ</span>
                    @if($todayPackingCount > 0)
                        <span class="nav-badge badge-blue" title="{{ $todayPackingCount }} កំពុងរៀបចំ">{{ $todayPackingCount }}</span>
                    @endif
                </a>
            @endif

            @if($isAdminOrManager || $isOfficeStaff)
                <a href="/deliveries" class="nav-link {{ (request()->is('deliveries') || request()->is('deliveries/*')) && !request()->is('deliveries/map*') ? 'active' : '' }}" data-tooltip="ការដឹកជញ្ជូន">
                    <i class="fas fa-truck"></i><span>ការដឹកជញ្ជូន</span>
                    @if($todayDeliveringCount > 0)
                        <span class="nav-badge badge-emerald" title="{{ $todayDeliveringCount }} កំពុងដឹកជញ្ជូន">{{ $todayDeliveringCount }}</span>
                    @endif
                </a>
                <a href="{{ route('deliveries.map') }}" class="nav-link {{ request()->is('deliveries/map*') ? 'active' : '' }}" data-tooltip="ផែនទីដឹកជញ្ជូន">
                    <i class="fas fa-map-marked-alt"></i><span>ផែនទីដឹកជញ្ជូន</span>
                </a>
            @endif

            {{-- 4. Invoicing & Finance (ផ្នែកគិតលុយ & ហិរញ្ញវត្ថុ) --}}
            @if($isAdminOrManager || $isOfficeStaff || $isAuditor)
                <div class="nav-label">គណនេយ្យ & ហិរញ្ញវត្ថុ</div>
                <a href="/invoices" class="nav-link {{ request()->is('invoices*') ? 'active' : '' }}" data-tooltip="វិក្កយបត្រ">
                    <i class="fas fa-receipt"></i><span>វិក្កយបត្រ</span>
                </a>
            @endif

            @if($isAdminOrManager || $isAuditor)
                <a href="/payments" class="nav-link {{ request()->is('payments*') ? 'active' : '' }}" data-tooltip="ការទូទាត់">
                    <i class="fas fa-credit-card"></i><span>ការទូទាត់</span>
                </a>
                <a href="/purchasing" class="nav-link {{ request()->is('purchasing*') ? 'active' : '' }}" data-tooltip="ការចំណាយ">
                    <i class="fas fa-file-invoice-dollar"></i><span>ការចំណាយ</span>
                </a>
            @endif

            {{-- 5. Products & Inventory (ផ្នែកទំនិញ & ស្តុក) --}}
            @if($isAdminOrManager || $isOfficeStaff || $isAdmin || $isManager || $isStaff || $isInventory || $isAuditor)
                <div class="nav-label">មុខម្ហូប & ស្តុក</div>
            @endif
            @if($isAdminOrManager || $isOfficeStaff)
                <a href="/products" class="nav-link {{ request()->is('products*') ? 'active' : '' }}" data-tooltip="ទំនិញ">
                    <i class="fas fa-pizza-slice"></i><span>ទំនិញ</span>
                </a>
            @endif

            @if($isAdmin || $isManager || $isStaff || $isInventory || $isAuditor)
                <a href="{{ route('inventory.index', ['period' => 'today']) }}"
                    class="nav-link {{ request()->is('inventory*') ? 'active' : '' }}" data-tooltip="ស្តុកទំនិញ">
                    <i class="fas fa-boxes"></i><span>ស្តុកទំនិញ</span>
                </a>
            @endif

            {{-- 6. Reports & System (ផ្នែករបាយការណ៍ & គ្រប់គ្រង) --}}
            @if($isAdminOrManager || $isAuditor)
                <div class="nav-label">របាយការណ៍ & ប្រព័ន្ធ</div>
                <a href="{{ route('reports.dashboard') }}" class="nav-link {{ request()->is('reports*') ? 'active' : '' }}" data-tooltip="របាយការណ៍">
                    <i class="fas fa-chart-line"></i><span>របាយការណ៍</span>
                </a>
            @endif

            @if($isAdminOrManager)
                <a href="/users" class="nav-link {{ request()->is('users*') ? 'active' : '' }}" data-tooltip="បុគ្គលិក">
                    <i class="fas fa-users-cog"></i><span>បុគ្គលិក</span>
                </a>
            @endif

        </nav>
    </div>

    <!-- Topbar -->
    <div class="topbar" id="topbar">
        <div style="display:flex;align-items:center;gap:12px;flex:1">
            <button class="navbar-toggle" id="navbarToggle"><i class="fas fa-bars"></i></button>
            <h1 class="page-title">@yield('title', 'Dashboard')</h1>
        </div>
        <div class="user-info">
            <div style="position: relative;">
                <button class="user-avatar" id="userDropdownToggle" title="User menu"
                    style="border: none; cursor: pointer; padding:0; overflow: hidden;">
                    @php $u = auth()->user(); @endphp
                    @if(!empty($u->profile_image) && file_exists(public_path($u->profile_image)))
                        <img src="{{ asset($u->profile_image) }}" alt="{{ $u->name }}" class="user-avatar-img">
                    @else
                        {{ strtoupper(substr($u->name ?? 'A', 0, 1)) }}
                    @endif
                </button>
                <div class="user-dropdown-menu" id="userDropdownMenu"
                    style="display: none; position: absolute; top: 100%; right: 0; background: white; border: 1px solid #e9ecef; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 220px; z-index: 1000;">
                    <div style="padding: 12px 16px; border-bottom: 1px solid #e9ecef;">
                        <div style="font-size: 13px; font-weight: 600; color: #1a1d29;">
                            {{ auth()->user()->name ?? 'Admin' }}
                        </div>
                        <div style="font-size: 12px; color: #6c757d; margin-top: 4px;">{{ auth()->user()->email }}</div>
                    </div>
                    <a href="{{ route('profile.edit') }}"
                        style="display: flex; align-items: center; gap: 10px; padding: 10px 16px; color: #1a1d29; text-decoration: none; font-size: 14px; transition: all 0.2s ease;"
                        onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='transparent'">
                        <i class="fas fa-user-edit" style="width: 16px; text-align: center; color: #e85d24;"></i>
                        ព័ត៌មានផ្ទាល់ខ្លួន
                    </a>
                    <a href="{{ route('activity-log') }}"
                        style="display: flex; align-items: center; gap: 10px; padding: 10px 16px; color: #1a1d29; text-decoration: none; font-size: 14px; transition: all 0.2s ease;"
                        onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='transparent'">
                        <i class="fas fa-history" style="width: 16px; text-align: center;"></i> Activity Log
                    </a>
                    <form action="{{ route('logout') }}" method="POST" style="display: contents;">
                        @csrf
                        <button type="submit"
                            style="width: 100%; display: flex; align-items: center; gap: 10px; padding: 10px 16px; color: #dc2626; background: transparent; border: none; text-decoration: none; font-size: 14px; cursor: pointer; font-family: inherit; transition: all 0.2s ease;"
                            onmouseover="this.style.background='#fef2f2'"
                            onmouseout="this.style.background='transparent'">
                            <i class="fas fa-sign-out-alt" style="width: 16px; text-align: center;"></i> ចាកចេញ
                        </button>
                    </form>
                </div>
            </div>
            <p class="user-name">{{ auth()->user()->name ?? 'Admin' }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        @yield('content')
    </div>

    @stack('modals')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/km.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Remove no-transition class after layout is set
            requestAnimationFrame(() => document.body.classList.remove('no-transition'));
            const sidebar = document.getElementById('sidebar');
            const topbar = document.getElementById('topbar');
            const mainContent = document.getElementById('mainContent');
            const collapseBtn = document.getElementById('sidebarCollapseBtn');
            const collapseIcon = document.getElementById('collapseIcon');
            const navbarToggle = document.getElementById('navbarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');

            // ===== SIDEBAR COLLAPSE (desktop) =====
            function applyCollapsed(collapsed) {
                if (collapsed) {
                    sidebar.classList.add('collapsed');
                    topbar.classList.add('sidebar-collapsed');
                    mainContent.classList.add('sidebar-collapsed');
                    collapseIcon.classList.replace('fa-chevron-left', 'fa-chevron-right');
                } else {
                    sidebar.classList.remove('collapsed');
                    topbar.classList.remove('sidebar-collapsed');
                    mainContent.classList.remove('sidebar-collapsed');
                    collapseIcon.classList.replace('fa-chevron-right', 'fa-chevron-left');
                }
                localStorage.setItem('sidebarCollapsed', collapsed);
            }

            // Load saved state
            const savedCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (window.innerWidth > 768) applyCollapsed(savedCollapsed);

            collapseBtn?.addEventListener('click', function (e) {
                e.stopPropagation();
                applyCollapsed(!sidebar.classList.contains('collapsed'));
            });

            // ===== SIDEBAR MOBILE TOGGLE =====
            function openSidebar() {
                sidebar.classList.add('show');
                sidebarOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            navbarToggle?.addEventListener('click', openSidebar);
            sidebarCloseBtn?.addEventListener('click', closeSidebar);
            sidebarOverlay?.addEventListener('click', closeSidebar);

            // Close sidebar on nav link click (mobile)
            document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) closeSidebar();
                });
            });

            // ESC key closes sidebar
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') closeSidebar();
            });

            // Handle resize
            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) {
                    closeSidebar();
                    applyCollapsed(localStorage.getItem('sidebarCollapsed') === 'true');
                } else {
                    topbar.classList.remove('sidebar-collapsed');
                    mainContent.classList.remove('sidebar-collapsed');
                }
            });
            // Initialize Flatpickr for all date inputs with Khmer locale
            try {
                if (typeof flatpickr !== 'undefined') {
                    document.querySelectorAll('input[type="date"]').forEach(inp => {
                        const current = inp.value || null;
                        inp.type = 'text';
                        flatpickr(inp, {
                            dateFormat: 'Y-m-d',
                            defaultDate: current,
                            locale: flatpickr.l10ns && flatpickr.l10ns.km ? flatpickr.l10ns.km : 'default'
                        });
                    });
                }
            } catch (e) {
                console.warn('Flatpickr init failed', e);
            }
            // ===== USER DROPDOWN =====
            const userDropdownToggle = document.getElementById('userDropdownToggle');
            const userDropdownMenu = document.getElementById('userDropdownMenu');

            // Toggle dropdown on avatar click
            userDropdownToggle?.addEventListener('click', e => {
                e.stopPropagation();
                userDropdownMenu.style.display = userDropdownMenu.style.display === 'none' ? 'block' : 'none';
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', e => {
                if (!userDropdownToggle?.contains(e.target) && !userDropdownMenu?.contains(e.target)) {
                    userDropdownMenu.style.display = 'none';
                }
            });

            // ===== DELETE CONFIRMATION =====
            document.querySelectorAll('[data-delete]').forEach(form => {
                form.addEventListener('submit', async e => {
                    e.preventDefault();
                    const result = await Swal.fire({
                        title: 'Delete ' + (form.dataset.delete || 'Item') + '?',
                        text: 'You are about to delete: ' + (form.dataset.itemName || 'this item'),
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Delete',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    });
                    if (result.isConfirmed) form.submit();
                });
            });

          
            document.querySelectorAll('form[data-confirm]').forEach(form => {
                form.addEventListener('submit', async e => {
                    e.preventDefault();
                    const options = {
                        title: form.dataset.confirmTitle || 'តើអ្នកប្រាកដទេ?',
                        icon: form.dataset.confirmIcon || 'warning',
                        showCancelButton: true,
                        confirmButtonColor: form.dataset.confirmColor || '#e85d24',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'យល់ព្រម',
                        cancelButtonText: 'បោះបង់',
                        reverseButtons: true
                    };
                    if (form.dataset.confirmHtml) {
                        options.html = form.dataset.confirm;
                    } else {
                        options.text = form.dataset.confirm;
                    }
                    const result = await Swal.fire(options);
                    if (result.isConfirmed) form.submit();
                });
            });

        });
    </script>
    @stack('scripts')
</body>

</html>

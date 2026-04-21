<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pizza Happy Family</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Noto+Sans+Khmer&family=Hanuman&family=Battambang&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        * { font-family: 'Poppins', 'Noto Sans Khmer', 'Hanuman', 'Battambang', 'Khmer OS', sans-serif; }

        body {
            background: #f5f7fa;
            min-height: 100vh;
            overflow-x: hidden;
        }

        html {
            background: #f5f7fa;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, #1a1d29 0%, #0f1117 100%);
            position: fixed;
            top: 0; left: 0;
            z-index: 1000;
            transition: width 0.15s ease;
            overflow: hidden;
            overflow-y: auto;
        }

        .sidebar.collapsed { width: 80px; }

        .sidebar-brand {
            background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%);
            padding: 20px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 72px;
        }

        .sidebar-brand-text {
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            transition: all 0.3s ease;
            text-decoration: none;
            flex: 1;
        }

        .sidebar.collapsed .sidebar-brand-text {
            width: 0;
            opacity: 0;
            pointer-events: none;
        }

        .sidebar-collapse-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: #fff;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .sidebar-collapse-btn:hover {
            background: rgba(255,255,255,0.35);
            transform: scale(1.1);
        }

        .sidebar.collapsed .sidebar-brand {
            justify-content: center;
        }

        /* Nav labels */
        .sidebar .nav-label {
            font-size: 10px;
            color: rgba(255,255,255,0.35);
            padding: 14px 24px 4px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .nav-label {
            opacity: 0;
            height: 0;
            padding: 0;
        }

        /* Nav links */
        .sidebar .nav-link {
            color: rgba(255,255,255,0.65);
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 500;
            border-left: 4px solid transparent;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.15s ease;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.08);
            border-left-color: #e85d24;
        }

        .sidebar .nav-link:hover i {
            color: #e85d24;
        }

        .sidebar .nav-link.active {
            color: #e85d24;
            background: rgba(232,93,36,0.15);
            border-left-color: #e85d24;
            font-weight: 600;
        }

        .sidebar .nav-link i {
            font-size: 16px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
            transition: color 0.25s ease;
        }

        .sidebar .nav-link span {
            transition: all 0.3s ease;
            overflow: hidden;
        }

        /* Collapsed nav links - icons only */
        .sidebar.collapsed .nav-link {
            padding: 14px;
            justify-content: center;
            border-left: 4px solid transparent;
            position: relative;
        }

        .sidebar.collapsed .nav-link span {
            width: 0;
            opacity: 0;
            pointer-events: none;
        }

        .sidebar.collapsed .nav-link i {
            font-size: 20px;
            width: auto;
        }

        /* Tooltip on hover when collapsed */
        .sidebar.collapsed .nav-link::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%) translateX(8px);
            background: #1a1d29;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease, transform 0.25s ease;
            z-index: 1001;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .sidebar.collapsed .nav-link:hover::after {
            opacity: 1;
            transform: translateY(-50%) translateX(12px);
        }

        .sidebar.collapsed .nav-link:hover {
            background: rgba(232,93,36,0.15);
            border-left-color: #e85d24;
        }

        /* Sidebar close btn (mobile only) */
        .sidebar-close-btn {
            display: none;
            position: absolute;
            top: 16px; right: 16px;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 20px;
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .topbar.sidebar-collapsed { margin-left: 80px; }

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

        .user-avatar:hover { transform: scale(1.05); }

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

        .navbar-toggle:hover { color: #e85d24; }

        /* Notification button */
        .notification-toggle {
            background: linear-gradient(135deg, #e85d24, #d94a10);
            border: none;
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .notification-toggle:hover { transform: scale(1.05); }

        .notification-count {
            position: absolute;
            top: -8px; right: -8px;
            background: #fff;
            color: #e85d24;
            border: 2px solid #e85d24;
            border-radius: 50%;
            width: 22px; height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
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

        .main-content.sidebar-collapsed { margin-left: 80px; }

        /* ===== NOTIFICATION PANEL ===== */
        .notification-panel {
            position: fixed;
            top: 0; right: -380px;
            width: 380px;
            height: 100vh;
            background: #fff;
            box-shadow: -4px 0 20px rgba(0,0,0,0.1);
            z-index: 1002;
            transition: right 0.2s ease;
            overflow-y: auto;
        }

        .notification-panel.show { right: 0; }

        .notification-header {
            background: linear-gradient(135deg, #e85d24, #d94a10);
            padding: 20px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
        }

        .notification-title { font-size: 17px; font-weight: 700; margin: 0; }

        .notification-close-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: #fff;
            width: 34px; height: 34px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .notification-close-btn:hover { background: rgba(255,255,255,0.35); }

        .notification-content { padding: 16px; }

        .notification-item {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .notification-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transform: translateX(-3px);
        }

        .notification-item.unread { border-left: 4px solid #e85d24; background: #fdf9f7; }

        .notification-type {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .notification-type.order { background: #d1ecf1; color: #0c5460; }
        .notification-type.inventory { background: #fff3cd; color: #856404; }
        .notification-type.alert { background: #f8d7da; color: #721c24; }
        .notification-type.message { background: #e2e3e5; color: #383d41; }

        .notification-time { font-size: 11px; color: #999; }
        .notification-title-text { font-size: 13px; font-weight: 600; color: #1a1d29; margin: 6px 0 4px; }
        .notification-message { font-size: 12px; color: #6c757d; }

        .notification-read-btn {
            font-size: 11px;
            padding: 3px 8px;
            background: transparent;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 6px;
        }

        .notification-read-btn:hover { background: #e85d24; color: #fff; border-color: #e85d24; }

        /* ===== OVERLAYS ===== */
        .sidebar-overlay, .notification-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            backdrop-filter: blur(2px);
        }

        .sidebar-overlay.active, .notification-overlay.active { display: block; }

        /* ===== SIDEBAR NAV SCROLL ===== */
        .sidebar nav {
            padding: 16px 0;
            max-height: calc(100vh - 72px);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar nav::-webkit-scrollbar { width: 4px; }
        .sidebar nav::-webkit-scrollbar-thumb { background: rgba(232,93,36,0.4); border-radius: 2px; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .navbar-toggle { display: flex; align-items: center; }
            .sidebar-collapse-btn { display: none; }

            .sidebar {
                width: 260px;
                margin-left: -260px;
                transition: margin-left 0.35s ease;
            }

            .sidebar.show { margin-left: 0; }
            .sidebar.collapsed { width: 260px; }

            .topbar {
                margin-left: 0;
                position: fixed;
                top: 0; left: 0; right: 0;
            }

            .topbar.sidebar-collapsed { margin-left: 0; }

            .main-content {
                margin-left: 0;
                margin-top: 65px;
                padding: 16px;
            }

            .main-content.sidebar-collapsed { margin-left: 0; }

            .sidebar-close-btn { display: block; }

            .notification-panel { width: 100%; right: -100%; }
        }

        @media (max-width: 576px) {
            .user-name { display: none; }
            .topbar { padding: 12px 16px; }
            .topbar .page-title { font-size: 17px; }
        }

        /* Khmer helper class */
        .kh {
            font-family: 'Noto Sans Khmer', 'Hanuman', 'Battambang', 'Khmer OS', sans-serif;
            /* slightly larger for Khmer readability */
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
    @stack('styles')
    <style>.no-transition,.no-transition *{transition:none!important}</style>
</head>
<body class="no-transition">

<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="notification-overlay" id="notificationOverlay"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <button class="sidebar-close-btn" id="sidebarCloseBtn"><i class="fas fa-times"></i></button>
    <div class="sidebar-brand">
        <a href="/" class="sidebar-brand-text">Pizza Happy Family</a>
        <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="Collapse sidebar">
            <i class="fas fa-chevron-left" id="collapseIcon"></i>
        </button>
    </div>
    <nav>
      
        <a href="/" class="nav-link {{ request()->is('/') || request()->is('dashboard') ? 'active' : '' }}" data-tooltip="ទំព័រដើម">
            <i class="fas fa-tachometer-alt"></i><span>ទំព័រដើម</span>
        </a>
        <a href="/orders" class="nav-link {{ request()->is('orders*') ? 'active' : '' }}" data-tooltip="ចេញវិក្ក័យបត្រ">
            <i class="fas fa-shopping-cart"></i><span>ចេញវិក្ក័យបត្រ</span>
        </a>

        {{-- Admin, Manager, Staff (Office) see customers menu --}}
        @if(!auth()->user()->isStaffInventory())
        <a href="/customers" class="nav-link {{ request()->is('customers*') ? 'active' : '' }}" data-tooltip="អតិថិជន">
            <i class="fas fa-users"></i><span>អតិថិជន</span>
        </a>
        @endif
        {{-- Only admins see warehouse section --}}
        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
      
        <a href="/products" class="nav-link {{ request()->is('products*') ? 'active' : '' }}" data-tooltip="ទំនិញ">
            <i class="fas fa-pizza-slice"></i><span>ទំនិញ</span>
        </a>
        <a href="/inventory" class="nav-link {{ request()->is('inventory*') ? 'active' : '' }}" data-tooltip="ស្តុកទំនិញ">
            <i class="fas fa-boxes"></i><span>ស្តុកទំនិញ</span>
        </a>
        <a href="/purchasing" class="nav-link {{ request()->is('purchasing*') ? 'active' : '' }}" data-tooltip="ការចំណាយ">
            <i class="fas fa-file-invoice"></i><span>ការចំណាយ</span>
        </a>
        @endif

        {{-- Managers+ see delivery section --}}
        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
        <a href="/deliveries" class="nav-link {{ request()->is('deliveries*') ? 'active' : '' }}" data-tooltip="ការដឹកជញ្ចូន">
            <i class="fas fa-truck"></i><span>ការដឹកជញ្ចូន</span>
        </a>

        <a href="/invoices" class="nav-link {{ request()->is('invoices*') ? 'active' : '' }}" data-tooltip="វិក័្កយបត្រ">
            <i class="fas fa-receipt"></i><span>វិក័្កយបត្រ</span>
        </a>
        <a href="/payments" class="nav-link {{ request()->is('payments*') ? 'active' : '' }}" data-tooltip="ការទូទាត់">
            <i class="fas fa-credit-card"></i><span>ការទូទាត់</span>
        </a>
        @endif

        {{--  Staff inventory sees invoices & inventory --}}
        @if(auth()->user()->isStaffInventory()) 
        <a href="/inventory" class="nav-link {{ request()->is('inventory*') ? 'active' : '' }}" data-tooltip="ស្តុកទំនិញ">
            <i class="fas fa-boxes"></i><span>ស្តុកទំនិញ</span>
        </a>
        @endif

        {{-- រៀបចំទំនិញ: Admin & Staff Inventory access prep printing page --}}
        @if(auth()->user()->isAdmin() || auth()->user()->isStaffInventory())
        <a href="{{ route('print.index') }}" class="nav-link {{ request()->is('print/index') ? 'active' : '' }}" data-tooltip="រៀបចំទំនិញ">
            <i class="fas fa-box-open"></i><span>រៀបចំទំនិញ</span>
        </a>
        @endif
<!-- 
        {{-- All users see reports --}}
        <a href="/reports" class="nav-link {{ request()->is('reports*') ? 'active' : '' }}" data-tooltip="របាយការណ៍">
            <i class="fas fa-chart-bar"></i><span>របាយការណ៍</span>
        </a> -->

        {{-- Admin & Manager see users --}}
        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
        <a href="/users" class="nav-link {{ request()->is('users*') ? 'active' : '' }}" data-tooltip="បុគ្គលិក">
            <i class="fas fa-users-cog"></i><span>បុគ្គលិក</span>
        </a>
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
        <a href="/settings" class="nav-link {{ request()->is('settings*') ? 'active' : '' }}" data-tooltip="Settings">
            <i class="fas fa-cog"></i><span>Settings</span>
        </a>
        @endif
    </nav>
</div>

<!-- Notification Panel -->
<div class="notification-panel" id="notificationPanel">
    <div class="notification-header">
        <h3 class="notification-title">Notifications</h3>
        <button class="notification-close-btn" id="notificationCloseBtn"><i class="fas fa-times"></i></button>
    </div>
    <div class="notification-content" id="notificationContent"></div>
</div>

<!-- Topbar -->
<div class="topbar" id="topbar">
    <div style="display:flex;align-items:center;gap:12px;flex:1">
        <button class="navbar-toggle" id="navbarToggle"><i class="fas fa-bars"></i></button>
        <h1 class="page-title">@yield('title', 'Dashboard')</h1>
    </div>
    <div class="user-info">
        <button class="notification-toggle" id="notificationToggle" title="Notifications">
            <i class="fas fa-bell"></i>
            <span class="notification-count" id="notificationCount">3</span>
        </button>
        <div style="position: relative;">
            <button class="user-avatar" id="userDropdownToggle" title="User menu" style="border: none; cursor: pointer;">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </button>
            <div class="user-dropdown-menu" id="userDropdownMenu" style="display: none; position: absolute; top: 100%; right: 0; background: white; border: 1px solid #e9ecef; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 220px; z-index: 1000;">
                <div style="padding: 12px 16px; border-bottom: 1px solid #e9ecef;">
                    <div style="font-size: 13px; font-weight: 600; color: #1a1d29;">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div style="font-size: 12px; color: #6c757d; margin-top: 4px;">{{ auth()->user()->email }}</div>
                </div>
                <a href="{{ route('profile.edit') }}" style="display: flex; align-items: center; gap: 10px; padding: 10px 16px; color: #1a1d29; text-decoration: none; font-size: 14px; transition: all 0.2s ease;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='transparent'">
                    <i class="fas fa-user-edit" style="width: 16px; text-align: center; color: #e85d24;"></i> ព័ត៌មានផ្ទាល់ខ្លួន
                </a>
                <a href="{{ route('activity-log') }}" style="display: flex; align-items: center; gap: 10px; padding: 10px 16px; color: #1a1d29; text-decoration: none; font-size: 14px; transition: all 0.2s ease;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='transparent'">
                    <i class="fas fa-history" style="width: 16px; text-align: center;"></i> Activity Log
                </a>
                <form action="{{ route('logout') }}" method="POST" style="display: contents;">
                    @csrf
                    <button type="submit" style="width: 100%; display: flex; align-items: center; gap: 10px; padding: 10px 16px; color: #dc2626; background: transparent; border: none; text-decoration: none; font-size: 14px; cursor: pointer; font-family: inherit; transition: all 0.2s ease;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Remove no-transition class after layout is set
    requestAnimationFrame(() => document.body.classList.remove('no-transition'));
    const sidebar        = document.getElementById('sidebar');
    const topbar         = document.getElementById('topbar');
    const mainContent    = document.getElementById('mainContent');
    const collapseBtn    = document.getElementById('sidebarCollapseBtn');
    const collapseIcon   = document.getElementById('collapseIcon');
    const navbarToggle   = document.getElementById('navbarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarCloseBtn= document.getElementById('sidebarCloseBtn');
    const notifToggle    = document.getElementById('notificationToggle');
    const notifPanel     = document.getElementById('notificationPanel');
    const notifClose     = document.getElementById('notificationCloseBtn');
    const notifOverlay   = document.getElementById('notificationOverlay');
    const notifContent   = document.getElementById('notificationContent');

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
        if (e.key === 'Escape') { closeSidebar(); closeNotif(); }
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

    // ===== NOTIFICATIONS =====
    const notifications = [
        { type:'order',     title:'New Order #ORD-0512',       message:'Order for $45.50 by John Doe',               time:'5 min ago',   unread:true  },
        { type:'inventory', title:'Low Stock Alert',            message:'Mozzarella cheese is below reorder level',   time:'12 min ago',  unread:true  },
        { type:'alert',     title:'Invoice Due Soon',           message:'Invoice INV-0089 is due in 2 days',          time:'1 hour ago',  unread:true  },
        { type:'message',   title:'System Alert',               message:'Database backup completed successfully',      time:'3 hours ago', unread:false },
        { type:'order',     title:'Order Delivered',            message:'Order #ORD-0511 successfully delivered',     time:'5 hours ago', unread:false },
    ];

    function renderNotifications() {
        notifContent.innerHTML = '';
        notifications.forEach((n, i) => {
            const el = document.createElement('div');
            el.className = 'notification-item' + (n.unread ? ' unread' : '');
            el.innerHTML = `
                <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                    <span class="notification-type ${n.type}">${n.type}</span>
                    <span class="notification-time">${n.time}</span>
                </div>
                <div class="notification-title-text">${n.title}</div>
                <div class="notification-message">${n.message}</div>
                ${n.unread ? `<button class="notification-read-btn" onclick="markRead(${i})">Mark as read</button>` : ''}
            `;
            notifContent.appendChild(el);
        });
    }

    window.markRead = function(i) {
        notifications[i].unread = false;
        updateCount();
        renderNotifications();
    };

    function updateCount() {
        const count = notifications.filter(n => n.unread).length;
        const badge = document.getElementById('notificationCount');
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    }

    function openNotif() {
        notifPanel.classList.add('show');
        notifOverlay.classList.add('active');
    }

    function closeNotif() {
        notifPanel.classList.remove('show');
        notifOverlay.classList.remove('active');
    }

    notifToggle?.addEventListener('click', e => { e.stopPropagation(); notifPanel.classList.contains('show') ? closeNotif() : openNotif(); });
    notifClose?.addEventListener('click', closeNotif);
    notifOverlay?.addEventListener('click', closeNotif);

    renderNotifications();
    updateCount();

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
});
</script>
@stack('scripts')
</body>
</html>
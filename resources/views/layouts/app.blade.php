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

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, #1a1d29 0%, #0f1117 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: width 0.15s ease;
            overflow: hidden;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-brand {
            background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%);
            padding: 20px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 72px;
        }

        .sidebar-brand .brand-inner {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .sidebar-logo {
            height: 40px;
            width: auto;
            display: block;
            border-radius: 8px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.15);
            flex-shrink: 0;
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
            background: rgba(255, 255, 255, 0.2);
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
            background: rgba(255, 255, 255, 0.35);
            transform: scale(1.1);
        }

        .sidebar.collapsed .sidebar-brand {
            justify-content: center;
        }

        /* Nav labels */
        .sidebar .nav-label {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.35);
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
            color: rgba(255, 255, 255, 0.65);
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
            background: rgba(255, 255, 255, 0.08);
            border-left-color: #e85d24;
        }

        .sidebar .nav-link:hover i {
            color: #e85d24;
        }

        .sidebar .nav-link.active {
            color: #e85d24;
            background: rgba(232, 93, 36, 0.15);
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .sidebar.collapsed .nav-link:hover::after {
            opacity: 1;
            transform: translateY(-50%) translateX(12px);
        }

        .sidebar.collapsed .nav-link:hover {
            background: rgba(232, 93, 36, 0.15);
            border-left-color: #e85d24;
        }

        /* Sidebar close btn (mobile only) */
        .sidebar-close-btn {
            display: none;
            position: absolute;
            top: 16px;
            right: 16px;
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

        .exchange-chip {
            align-items: center;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 999px;
            color: #9a3412;
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            gap: 7px;
            min-height: 34px;
            padding: 7px 12px;
            white-space: nowrap;
        }

        .exchange-chip i {
            color: #e85d24;
        }

        .exchange-chip small {
            background: #dcfce7;
            border-radius: 999px;
            color: #047857;
            font-size: 10px;
            font-weight: 900;
            line-height: 1;
            padding: 4px 6px;
            text-transform: uppercase;
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

        /* ===== SIDEBAR NAV SCROLL ===== */
        .sidebar nav {
            padding: 16px 0;
            max-height: calc(100vh - 72px);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar nav::-webkit-scrollbar-thumb {
            background: rgba(232, 93, 36, 0.4);
            border-radius: 2px;
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

            .exchange-chip {
                display: none;
            }
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
                <span class="sidebar-brand-text">Pizza Happy Family</span>
            </a>
            <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="Collapse sidebar">
                <i class="fas fa-chevron-left" id="collapseIcon"></i>
            </button>
        </div>
        @php
            $user = auth()->user();

            $isAdmin = $user->isAdmin();
            $isManager = $user->isManager();
            $isStaff = $user->isStaff();
            $isInventory = $user->isStaffInventory();

            $isAdminOrManager = $isAdmin || $isManager;
            $isOfficeStaff = $isStaff && !$isInventory;
        @endphp

        <nav>

            {{-- Dashboard (everyone except inventory staff) --}}
            @if(!$isInventory)
                <a href="/" class="nav-link {{ request()->is('/') || request()->is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i><span>ទំព័រដើម</span>
                </a>
            @endif

            {{-- Customers & Orders (admin, manager, staff office) --}}
            @if(!$isInventory)
              
                <a href="/orders" class="nav-link {{ request()->is('orders*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i><span>ចេញវិក្ក័យបត្រ</span>
                </a>
                  <a href="/customers" class="nav-link {{ request()->is('customers*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i><span>អតិថិជន</span>
                </a>
            @endif
                  {{-- Invoices & Payments (admin, manager, staff office) --}}
            @if($isAdminOrManager || $isOfficeStaff)
                <a href="/invoices" class="nav-link {{ request()->is('invoices*') ? 'active' : '' }}">
                    <i class="fas fa-receipt"></i><span>វិក័្កយបត្រ</span>
                </a>

                <a href="/payments" class="nav-link {{ request()->is('payments*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card"></i><span>ការទូទាត់</span>
                </a>
            @endif
            {{-- Purchasing (admin & manager only) --}}
            @if($isAdminOrManager)
                <a href="/purchasing" class="nav-link {{ request()->is('purchasing*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i><span>ការចំណាយ</span>
                </a>
                <a href="{{ route('reports.daily') }}" class="nav-link {{ request()->is('reports*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i><span>របាយការណ៍</span>
                </a>
            @endif



            {{-- Products (admin, manager, staff office) --}}
            @if($isAdminOrManager || $isOfficeStaff)
                <a href="/products" class="nav-link {{ request()->is('products*') ? 'active' : '' }}">
                    <i class="fas fa-pizza-slice"></i><span>ទំនិញ</span>
                </a>
            @endif

            {{-- Inventory (everyone except none) --}}
            @if($isAdmin || $isManager || $isStaff || $isInventory)
                <a href="{{ route('inventory.index', ['period' => 'today']) }}" class="nav-link {{ request()->is('inventory*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i><span>ស្តុកទំនិញ</span>
                </a>
            @endif

            

      

            {{-- Packing labels (admin + staff inventory + staff office + manager) --}}
            @if($isAdmin || $isManager || $isStaff || $isInventory)
                <a href="{{ route('packing.index') }}" class="nav-link {{ request()->is('packing*') || request()->is('print*') ? 'active' : '' }}">
                    <i class="fas fa-box-open"></i><span>រៀបចំទំនិញ</span>
                </a>
            @endif
            
            {{-- Deliveries (admin, manager, staff office) --}}
            @if($isAdminOrManager || $isOfficeStaff)
                <a href="/deliveries" class="nav-link {{ request()->is('deliveries*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i><span>ការដឹកជញ្ចូន</span>
                </a>
            @endif

            {{-- Users (admin & manager only) --}}
            @if($isAdminOrManager)
                <a href="/users" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
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
            <div class="exchange-chip" title="អត្រាប្តូរប្រាក់">
                <i class="fas fa-money-bill-wave"></i>
                <span>1 USD = ៛{{ number_format($globalExchangeRate['rate'] ?? 4000, 0) }}</span>
                @if(($globalExchangeRate['source'] ?? 'local') === 'live')
                    <small>Live</small>
                @endif
            </div>
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
                            {{ auth()->user()->name ?? 'Admin' }}</div>
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

        });
    </script>
    @stack('scripts')
</body>

</html>

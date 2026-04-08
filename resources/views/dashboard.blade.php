@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.dash-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }

/* Hero */
.dash-hero {
    background: linear-gradient(135deg, #1a1d29 0%, #2d1f0e 55%, #c44a18 100%);
    border-radius: 18px;
    padding: 30px 36px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.dash-hero-pattern {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 80% 50%, rgba(232,93,36,0.18) 0%, transparent 60%),
                      radial-gradient(circle at 20% 80%, rgba(255,255,255,0.04) 0%, transparent 40%);
    pointer-events: none;
}
.dash-hero-dots {
    position: absolute; right: 140px; top: 10px;
    font-size: 60px; opacity: 0.06; line-height: 1;
    pointer-events: none; user-select: none;
}
.dash-hero-title { font-size: 24px; font-weight: 800; color: #fff; margin: 0 0 5px; }
.dash-hero-sub   { font-size: 13px; color: rgba(255,255,255,0.5); margin: 0 0 4px; }
.dash-hero-time  { font-size: 12px; color: rgba(255,255,255,0.35); margin: 0; }
.btn-refresh {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.18);
    color: #fff; padding: 9px 20px;
    border-radius: 9px; font-size: 13px; font-weight: 600;
    cursor: pointer; text-decoration: none;
    display: flex; align-items: center; gap: 7px;
    transition: background 0.2s; position: relative; z-index: 1;
}
.btn-refresh:hover { background: rgba(255,255,255,0.2); color: #fff; }

/* Stats grid - 6 columns */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 14px;
    margin-bottom: 22px;
}
.stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 20px 20px 16px;
    border: 1px solid #efefef;
    position: relative; overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(0,0,0,0.07); }
.stat-accent {
    position: absolute; top: 0; left: 0; right: 0;
    height: 3px; border-radius: 14px 14px 0 0;
}
.stat-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; margin-bottom: 14px;
}
.stat-label {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.6px; color: #b0b8c4; margin-bottom: 5px;
}
.stat-value { font-size: 24px; font-weight: 800; color: #1a1d29; line-height: 1; margin-bottom: 7px; }
.stat-meta  { font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 4px; }

/* Main layout */
.main-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 20px;
    margin-bottom: 20px;
}

/* Cards */
.panel {
    background: #fff; border-radius: 14px;
    border: 1px solid #efefef; overflow: hidden;
}
.panel-header {
    padding: 18px 22px 14px;
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 1px solid #f5f5f5;
}
.panel-title { font-size: 14px; font-weight: 700; color: #1a1d29; margin: 0; }
.panel-link  { font-size: 12px; color: #e85d24; font-weight: 600; text-decoration: none; }
.panel-link:hover { text-decoration: underline; color: #e85d24; }

/* Table */
.dash-table { width: 100%; border-collapse: collapse; }
.dash-table th {
    padding: 9px 22px; font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.5px;
    color: #b0b8c4; background: #fafafa; text-align: left;
}
.dash-table td {
    padding: 13px 22px; font-size: 13px; color: #1a1d29;
    border-bottom: 1px solid #f5f5f5;
}
.dash-table tr:last-child td { border-bottom: none; }
.dash-table tr:hover td { background: #fafafa; }

.badge {
    padding: 3px 9px; border-radius: 20px;
    font-size: 11px; font-weight: 700;
}
.badge-pending    { background: #fff7ed; color: #c2410c; }
.badge-completed  { background: #f0fdf4; color: #15803d; }
.badge-cancelled  { background: #fef2f2; color: #dc2626; }
.badge-processing { background: #eff6ff; color: #1d4ed8; }

/* Empty */
.empty { padding: 44px 22px; text-align: center; color: #c0c8d4; }
.empty-ico { font-size: 36px; margin-bottom: 10px; }
.empty-txt { font-size: 13px; font-weight: 500; }

/* Alerts */
.alert-row {
    padding: 14px 22px;
    display: flex; align-items: center; gap: 12px;
    border-bottom: 1px solid #f5f5f5;
}
.alert-row:last-child { border-bottom: none; }
.alert-row:hover { background: #fafafa; }
.alert-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.alert-name { font-size: 13px; font-weight: 600; color: #1a1d29; margin-bottom: 6px; }
.alert-bar-wrap { height: 4px; background: #f0f0f0; border-radius: 4px; overflow: hidden; }
.alert-bar      { height: 4px; border-radius: 4px; }
.alert-pct { margin-left: auto; font-size: 11px; font-weight: 700; flex-shrink: 0; }

/* Quick actions */
.qa-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding: 18px 22px; }
.qa-btn {
    padding: 11px 14px; border-radius: 10px;
    font-size: 12px; font-weight: 700; text-decoration: none;
    display: flex; align-items: center; gap: 7px;
    border: 1.5px solid transparent; transition: all 0.2s;
}
.qa-btn:hover { transform: translateY(-1px); text-decoration: none; }
.qa-orange { background: #fff4ef; color: #e85d24; border-color: #fcd9c8; }
.qa-blue   { background: #f0f9ff; color: #0284c7; border-color: #bae6fd; }
.qa-green  { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
.qa-purple { background: #faf5ff; color: #9333ea; border-color: #e9d5ff; }
.qa-red    { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
.qa-teal   { background: #f0fdfa; color: #0d9488; border-color: #99f6e4; }

/* Bottom grid */
.bottom-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

/* Stock bar */
.stock-bar-wrap { width: 70px; height: 5px; background: #f0f0f0; border-radius: 4px; display: inline-block; vertical-align: middle; margin-left: 8px; overflow: hidden; }
.stock-bar      { height: 5px; border-radius: 4px; }

@media (max-width: 1200px) {
    .stats-grid  { grid-template-columns: repeat(3, 1fr); }
    .main-grid   { grid-template-columns: 1fr; }
    .bottom-grid { grid-template-columns: 1fr; }
}
</style>

<div class="dash-wrap">

{{-- Hero --}}
<div class="dash-hero">
    <div class="dash-hero-pattern"></div>
    <div class="dash-hero-dots"></div>
    <div style="position:relative;z-index:1">
        <h1 class="dash-hero-title"> Pizza Happy Family</h1>
        <p class="dash-hero-sub">ស្វាគមន៍មកកាន់ប្រព័ន្ធគ្រប់គ្រង — Welcome back, <strong style="color:#fff">{{ auth()->user()->name ?? 'Admin' }}</strong></p>
        <p class="dash-hero-time"><i class="fas fa-clock" style="margin-right:4px"></i>{{ now()->translatedFormat('l, d F Y • H:i') }}</p>
    </div>
    
 
</div>

{{-- Stats --}}
<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-accent" style="background:linear-gradient(90deg,#e85d24,#f97316)"></div>
        <div class="stat-icon" style="background:#fff4ef;color:#e85d24"><i class="fas fa-dollar-sign"></i></div>
        <div class="stat-label">Revenue</div>
        <div class="stat-value">${{ number_format($totalRevenue ?? 0, 2) }}</div>
        <div class="stat-meta" style="color:#e85d24"><i class="fas fa-chart-line" style="font-size:9px"></i> This month</div>
    </div>

    <div class="stat-card">
        <div class="stat-accent" style="background:linear-gradient(90deg,#0ea5e9,#38bdf8)"></div>
        <div class="stat-icon" style="background:#f0f9ff;color:#0284c7"><i class="fas fa-shopping-cart"></i></div>
        <div class="stat-label">Orders</div>
        <div class="stat-value">{{ $totalOrders ?? 0 }}</div>
        <div class="stat-meta" style="color:#0284c7"><i class="fas fa-calendar-day" style="font-size:9px"></i> {{ $todayOrders ?? 0 }} today</div>
    </div>

    {{-- Only admins see customer stats --}}
    @if(auth()->user()->isAdmin())
    <div class="stat-card">
        <div class="stat-accent" style="background:linear-gradient(90deg,#8b5cf6,#a78bfa)"></div>
        <div class="stat-icon" style="background:#faf5ff;color:#7c3aed"><i class="fas fa-users"></i></div>
        <div class="stat-label">Customers</div>
        <div class="stat-value">{{ $totalCustomers ?? 0 }}</div>
        <div class="stat-meta" style="color:#7c3aed"><i class="fas fa-user-check" style="font-size:9px"></i> Active</div>
    </div>

    {{-- Only admins see product stats --}}
    <div class="stat-card">
        <div class="stat-accent" style="background:linear-gradient(90deg,#10b981,#34d399)"></div>
        <div class="stat-icon" style="background:#f0fdf4;color:#059669"><i class="fas fa-boxes"></i></div>
        <div class="stat-label">Products</div>
        <div class="stat-value">{{ $totalProducts ?? 0 }}</div>
        <div class="stat-meta" style="color:#059669"><i class="fas fa-tag" style="font-size:9px"></i> In catalog</div>
    </div>
    @endif

    {{-- Deliveries visible to managers+ --}}
    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
    <div class="stat-card">
        <div class="stat-accent" style="background:linear-gradient(90deg,#06b6d4,#22d3ee)"></div>
        <div class="stat-icon" style="background:#f0f9fa;color:#0891b2"><i class="fas fa-truck"></i></div>
        <div class="stat-label">Deliveries Today</div>
        <div class="stat-value">{{ $stats['today_delivered'] ?? 0 }}</div>
        <div class="stat-meta" style="color:#0891b2"><i class="fas fa-check-circle" style="font-size:9px"></i> Completed</div>
    </div>
    @endif

    {{-- Low stock visible to admins only --}}
    @if(auth()->user()->isAdmin())
    <div class="stat-card">
        <div class="stat-accent" style="background:linear-gradient(90deg,#f59e0b,#fbbf24)"></div>
        <div class="stat-icon" style="background:#fffbeb;color:#d97706"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-label">Low Stock</div>
        <div class="stat-value">{{ $stats['low_inventory_items'] ?? 0 }}</div>
        <div class="stat-meta" style="color:#d97706"><i class="fas fa-bell" style="font-size:9px"></i> Attention</div>
    </div>
    @endif

    {{-- Pending payments visible to managers+ --}}
    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
    <div class="stat-card">
        <div class="stat-accent" style="background:linear-gradient(90deg,#ef4444,#f87171)"></div>
        <div class="stat-icon" style="background:#fef2f2;color:#dc2626"><i class="fas fa-file-invoice-dollar"></i></div>
        <div class="stat-label">Pending Payments</div>
        <div class="stat-value">${{ number_format($stats['pending_payments'] ?? 0, 2) }}</div>
        <div class="stat-meta" style="color:#dc2626"><i class="fas fa-clock" style="font-size:9px"></i> Needs follow-up</div>
    </div>
    @endif

</div>



</div>
@endsection
@extends('layouts.app')

@section('title', $user->name . ' - របាយការណ៍')

@section('content')

<style>
    .report-wrap { max-width: 1200px; }

    .user-hero {
        background: linear-gradient(135deg, #1a1d29 0%, #2d1f0e 55%, #c44a18 100%);
        border-radius: 16px;
        padding: 28px 32px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .user-hero-pattern {
        position: absolute; inset: 0;
        background-image: radial-gradient(circle at 80% 50%, rgba(232,93,36,0.18) 0%, transparent 60%);
        pointer-events: none;
    }
    .user-hero-left { display: flex; align-items: center; gap: 20px; position: relative; z-index: 1; }
    .user-hero-avatar {
        width: 72px; height: 72px; border-radius: 14px; object-fit: cover;
        border: 3px solid rgba(255,255,255,0.2); box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    }
    .user-hero-avatar-ph {
        width: 72px; height: 72px; border-radius: 14px;
        background: rgba(255,255,255,0.15); border: 3px solid rgba(255,255,255,0.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; font-weight: 800; color: #fff;
    }
    .user-hero-name { font-size: 22px; font-weight: 800; color: #fff; margin: 0 0 4px; }
    .user-hero-email { font-size: 13px; color: rgba(255,255,255,0.5); margin: 0; }
    .user-hero-role {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; margin-top: 8px;
    }
    .role-admin { background: rgba(220,38,38,0.2); color: #fca5a5; }
    .role-manager { background: rgba(59,130,246,0.2); color: #93c5fd; }
    .role-staff { background: rgba(168,85,247,0.2); color: #d8b4fe; }
    .role-staff_inventory { background: rgba(234,88,12,0.2); color: #fdba74; }

    .user-hero-right { position: relative; z-index: 1; text-align: right; }
    .hero-stat-label { font-size: 11px; color: rgba(255,255,255,0.4); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .hero-stat-value { font-size: 24px; font-weight: 800; color: #fff; }
    .hero-stat-sub { font-size: 12px; color: rgba(255,255,255,0.35); }

    .filter-bar {
        background: #fff; border: 1px solid #e9ecef; border-radius: 12px;
        padding: 16px 20px; margin-bottom: 24px;
        display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
    }
    .filter-bar label { font-size: 13px; font-weight: 700; color: #1a1d29; margin: 0; }
    .filter-btn {
        padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600;
        border: 1.5px solid #e9ecef; background: #fff; color: #6c757d; cursor: pointer;
        transition: all 0.2s; text-decoration: none;
    }
    .filter-btn:hover { border-color: #e85d24; color: #e85d24; }
    .filter-btn.active { background: #e85d24; color: #fff; border-color: #e85d24; }
    .filter-date {
        padding: 8px 14px; border-radius: 8px; border: 1.5px solid #e9ecef;
        font-size: 13px; font-weight: 600; color: #1a1d29;
    }
    .filter-date:focus { outline: none; border-color: #e85d24; box-shadow: 0 0 0 3px rgba(232,93,36,0.1); }

    .stat-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-box {
        background: #fff; border: 1px solid #e9ecef; border-radius: 14px;
        padding: 20px; position: relative; overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-box:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
    .stat-box-accent { position: absolute; top: 0; left: 0; right: 0; height: 3px; border-radius: 14px 14px 0 0; }
    .stat-box-icon {
        width: 40px; height: 40px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; margin-bottom: 14px;
    }
    .stat-box-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #b0b8c4; margin-bottom: 4px; }
    .stat-box-value { font-size: 22px; font-weight: 800; color: #1a1d29; }
    .stat-box-sub { font-size: 11px; color: #6c757d; margin-top: 4px; }

    .orders-panel {
        background: #fff; border: 1px solid #e9ecef; border-radius: 14px;
        overflow: hidden;
    }
    .orders-header {
        padding: 18px 24px; border-bottom: 1px solid #f0f0f0;
        display: flex; justify-content: space-between; align-items: center;
    }
    .orders-title { font-size: 16px; font-weight: 700; color: #1a1d29; margin: 0; }
    .orders-count { font-size: 12px; color: #6c757d; }

    .orders-table { width: 100%; border-collapse: collapse; }
    .orders-table th {
        padding: 10px 24px; font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.5px;
        color: #b0b8c4; background: #fafafa; text-align: left;
    }
    .orders-table td {
        padding: 14px 24px; font-size: 13px; color: #1a1d29;
        border-bottom: 1px solid #f5f5f5;
    }
    .orders-table tr:last-child td { border-bottom: none; }
    .orders-table tr:hover td { background: #fafafa; }

    .badge-sm {
        padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;
    }
    .badge-completed { background: #f0fdf4; color: #15803d; }
    .badge-pending { background: #fff7ed; color: #c2410c; }
    .badge-processing { background: #eff6ff; color: #1d4ed8; }
    .badge-cancelled { background: #fef2f2; color: #dc2626; }
    .badge-paid { background: #f0fdf4; color: #15803d; }
    .badge-unpaid { background: #fef2f2; color: #dc2626; }
    .badge-partial { background: #fff7ed; color: #c2410c; }

    .empty-orders { text-align: center; padding: 48px 20px; color: #b0b8c4; }
    .empty-orders i { font-size: 40px; margin-bottom: 12px; opacity: 0.5; }
    .empty-orders p { font-size: 14px; font-weight: 500; }

    .btn-view {
        padding: 5px 12px; background: #e85d24; color: #fff; border-radius: 6px;
        font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s;
    }
    .btn-view:hover { background: #d94a10; color: #fff; }

    .btn-back-bar { display: flex; gap: 12px; margin-bottom: 20px; }
    .btn-back {
        background: #f4f5f7; color: #1a1d29; padding: 10px 20px; border-radius: 8px;
        border: 1px solid #e9ecef; font-weight: 600; font-size: 13px; text-decoration: none;
        display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;
    }
    .btn-back:hover { background: #e9ecef; color: #1a1d29; }

    .period-label {
        font-size: 13px; font-weight: 600; color: #e85d24;
        background: #fff4ef; padding: 6px 14px; border-radius: 8px;
        display: inline-flex; align-items: center; gap: 6px;
    }

    @media (max-width: 768px) {
        .stat-row { grid-template-columns: repeat(2, 1fr); }
        .user-hero { flex-direction: column; align-items: flex-start; gap: 16px; }
        .user-hero-right { text-align: left; }
        .filter-bar { flex-direction: column; align-items: stretch; }
    }
</style>

<div class="report-wrap">

    <div class="btn-back-bar">
        <a href="{{ route('users.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> បញ្ជីអ្នកប្រើប្រាស់
        </a>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('users.edit', $user) }}" class="btn-back">
            <i class="fas fa-edit"></i> កែប្រែ
        </a>
        @endif
    </div>

    <!-- User Hero -->
    <div class="user-hero">
        <div class="user-hero-pattern"></div>
        <div class="user-hero-left">
            @if($user->profile_image)
                <img src="{{ asset($user->profile_image) }}" alt="{{ $user->name }}" class="user-hero-avatar">
            @else
                <div class="user-hero-avatar-ph">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            @endif
            <div>
                <h2 class="user-hero-name">{{ $user->name }}</h2>
                <p class="user-hero-email">{{ $user->email }}</p>
                <span class="user-hero-role role-{{ $user->role }}">
                    @if($user->role === 'admin') <i class="fas fa-crown"></i> Administrator
                    @elseif($user->role === 'manager') <i class="fas fa-chart-line"></i> Manager
                    @elseif($user->role === 'staff_inventory') <i class="fas fa-boxes"></i> Staff (Inventory)
                    @else <i class="fas fa-user"></i> Staff (Office)
                    @endif
                </span>
            </div>
        </div>
        <div class="user-hero-right">
            <div class="hero-stat-label">ការបញ្ជាទិញសរុប</div>
            <div class="hero-stat-value">{{ number_format($allTimeStats['total_orders']) }}</div>
            <div class="hero-stat-sub">${{ number_format($allTimeStats['total_revenue'], 2) }} ប្រាក់ចំណូល</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <form class="filter-bar" method="GET" action="{{ route('users.show', $user) }}" id="filterForm">
    
        <input type="hidden" name="period" id="periodInput" value="{{ $period }}">

        <button type="submit" class="filter-btn {{ $period === 'today' ? 'active' : '' }}" onclick="document.getElementById('periodInput').value='today'">
            <i class="fas fa-calendar-day"></i> ថ្ងៃនេះ
        </button>
        <button type="submit" class="filter-btn {{ $period === 'week' ? 'active' : '' }}" onclick="document.getElementById('periodInput').value='week'">
            <i class="fas fa-calendar-week"></i> សប្ដាហ៍នេះ
        </button>
        <button type="submit" class="filter-btn {{ $period === 'month' ? 'active' : '' }}" onclick="document.getElementById('periodInput').value='month'">
            <i class="fas fa-calendar-alt"></i> ខែនេះ
        </button>
        <button type="submit" class="filter-btn {{ $period === 'year' ? 'active' : '' }}" onclick="document.getElementById('periodInput').value='year'">
            <i class="fas fa-calendar"></i> ឆ្នាំនេះ
        </button>

        <div style="margin-left: auto; display: flex; align-items: center; gap: 8px;">
            <input type="date" name="date" class="filter-date" value="{{ $date }}">
            <button type="submit" class="filter-btn active" style="padding: 8px 14px;">
               <label>បញ្ជាក់</label>
            </button>
        </div>
    </form>

    <!-- Period Label -->
    <div style="margin-bottom: 16px;"> 
        <span class="period-label">
            <i class="fas fa-clock"></i>
            {{ $start->translatedFormat('d M Y') }} — {{ $end->translatedFormat('d M Y') }}
        </span>
    </div>

    <!-- Stats -->
    <div class="stat-row">
        <div class="stat-box">
            <div class="stat-box-accent" style="background: linear-gradient(90deg, #e85d24, #f97316);"></div>
            <div class="stat-box-icon" style="background: #fff4ef; color: #e85d24;"><i class="fas fa-shopping-cart"></i></div>
            <div class="stat-box-label">ការបញ្ជាទិញ</div>
            <div class="stat-box-value">{{ $stats['total_orders'] }}</div>
            <div class="stat-box-sub">{{ $stats['completed_orders'] }} បានបញ្ចប់</div>
        </div>
        <div class="stat-box">
            <div class="stat-box-accent" style="background: linear-gradient(90deg, #10b981, #34d399);"></div>
            <div class="stat-box-icon" style="background: #f0fdf4; color: #059669;"><i class="fas fa-dollar-sign"></i></div>
            <div class="stat-box-label">ប្រាក់ចំណូល</div>
            <div class="stat-box-value">${{ number_format($stats['total_revenue'], 2) }}</div>
            <div class="stat-box-sub">៛{{ number_format($stats['total_revenue'] * 4000, 0) }}</div>
        </div>
        @if($user->role !== 'staff' && $user->role !== 'staff_inventory')
        <div class="stat-box">
            <div class="stat-box-accent" style="background: linear-gradient(90deg, #0ea5e9, #38bdf8);"></div>
            <div class="stat-box-icon" style="background: #f0f9ff; color: #0284c7;"><i class="fas fa-check-circle"></i></div>
            <div class="stat-box-label">បានបង់ប្រាក់</div>
            <div class="stat-box-value">${{ number_format($stats['paid_amount'], 2) }}</div>
            <div class="stat-box-sub">៛{{ number_format($stats['paid_amount'] * 4000, 0) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-box-accent" style="background: linear-gradient(90deg, #ef4444, #f87171);"></div>
            <div class="stat-box-icon" style="background: #fef2f2; color: #dc2626;"><i class="fas fa-exclamation-circle"></i></div>
            <div class="stat-box-label">មិនទាន់បង់</div>
            <div class="stat-box-value">${{ number_format($stats['unpaid_amount'], 2) }}</div>
            <div class="stat-box-sub">{{ $stats['pending_orders'] }} រង់ចាំ · {{ $stats['cancelled_orders'] }} បានលុបចោល</div>
        </div>
        @endif
    </div>

    <!-- Orders Table -->
    <div class="orders-panel">
        <div class="orders-header">
            <h3 class="orders-title"><i class="fas fa-list" style="color: #e85d24; margin-right: 8px;"></i> បញ្ជាទិញដោយ {{ $user->name }}</h3>
            <span class="orders-count">{{ $orders->total() }} ការបញ្ជាទិញ</span>
        </div>

        @if($orders->count() > 0)
        <table class="orders-table">
            <thead>
                <tr>
                    <th>លេខ</th>
                    <th>កាលបរិច្ឆេទ</th>
                    <th>អតិថិជន</th>
                    <th>ផលិតផល</th>
                    <th>សរុប</th>
                    <th>ស្ថានភាព</th>
                    <th>ការបង់ប្រាក់</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td style="font-weight: 700; color: #e85d24;">#{{ $order->id }}</td>
                    <td>{{ $order->order_date->translatedFormat('d M Y') }}</td>
                    <td>
                        <div style="font-weight: 600;">{{ $order->customer->name ?? '—' }}</div>
                    </td>
                    <td>
                        @foreach($order->items->take(2) as $item)
                            <div style="font-size: 12px;">{{ $item->product->name ?? '—' }} × {{ $item->quantity }}</div>
                        @endforeach
                        @if($order->items->count() > 2)
                            <div style="font-size: 11px; color: #6c757d;">+{{ $order->items->count() - 2 }} ទៀត</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight: 700;">${{ number_format($order->total_amount, 2) }}</div>
                        <div style="font-size: 11px; color: #6c757d;">៛{{ number_format($order->total_amount * 4000, 0) }}</div>
                    </td>
                    <td>
                        <span class="badge-sm badge-{{ $order->status }}">
                            {{ $order->status === 'completed' ? 'បានបញ្ចប់' : ($order->status === 'pending' ? 'រង់ចាំ' : ($order->status === 'processing' ? 'កំពុងដំណើរការ' : 'បានលុបចោល')) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge-sm badge-{{ $order->payment_status }}">
                            {{ $order->payment_status === 'paid' ? 'បានបង់' : ($order->payment_status === 'unpaid' ? 'មិនទាន់បង់' : 'មួយផ្នែក') }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('orders.show', $order) }}" class="btn-view">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="padding: 16px 24px; border-top: 1px solid #f0f0f0;">
            {{ $orders->appends(request()->query())->links() }}
        </div>
        @else
        <div class="empty-orders">
            <i class="fas fa-inbox"></i>
            <p>មិនមានការបញ្ជាទិញក្នុងរយៈពេលនេះទេ</p>
        </div>
        @endif
    </div>

</div>

@endsection

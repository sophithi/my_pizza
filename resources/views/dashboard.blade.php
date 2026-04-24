@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* (KEEP ALL YOUR EXISTING CSS — unchanged) */
</style>

<div class="dash-wrap">

    {{-- Hero --}}
    <div class="dash-hero">
        <div class="dash-hero-pattern"></div>
        <div class="dash-hero-dots"></div>
        <div style="position:relative;z-index:1">
            <h1 class="dash-hero-title">Pizza Happy Family</h1>
            <p class="dash-hero-sub">
                Welcome back,
                <strong style="color:#fff">{{ auth()->user()->name ?? 'Admin' }}</strong>
            </p>
            <p class="dash-hero-time">
                {{ now()->setTimezone('Asia/Phnom_Penh')->translatedFormat('l, d F Y • h:i A') }}
            </p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">

        <div class="stat-card">
            <div class="stat-accent" style="background:linear-gradient(90deg,#e85d24,#f97316)"></div>
            <div class="stat-icon" style="background:#fff4ef;color:#e85d24">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-label">Revenue</div>
            <div class="stat-value">${{ number_format($totalRevenue ?? 0, 2) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-accent" style="background:linear-gradient(90deg,#0ea5e9,#38bdf8)"></div>
            <div class="stat-icon" style="background:#f0f9ff;color:#0284c7">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-label">Orders</div>
            <div class="stat-value">{{ $totalOrders ?? 0 }}</div>
        </div>

    </div>

    {{-- 🔥 CHART SECTION --}}
    <div class="main-grid">

        <!-- Revenue Chart -->
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Revenue (Last 7 Days)</h3>
            </div>
            <div style="padding:20px;">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>

        <!-- Orders Chart -->
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Order Status</h3>
            </div>
            <div style="padding:20px;">
                <canvas id="ordersChart"></canvas>
            </div>
        </div>

    </div>

</div>

{{-- 🔥 CHART SCRIPT --}}
<script>
    const labels = @json($dates ?? []);
    const revenueData = @json($totals ?? []);

    const pending = {{ $pending ?? 0 }};
    const completed = {{ $completed ?? 0 }};

    // Revenue Line Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue',
                data: revenueData,
                borderColor: '#e85d24',
                backgroundColor: 'rgba(232,93,36,0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Orders Doughnut Chart
    new Chart(document.getElementById('ordersChart'), {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Completed'],
            datasets: [{
                data: [pending, completed],
                backgroundColor: ['#f59e0b', '#10b981']
            }]
        },
        options: {
            responsive: true
        }
    });
</script>

@endsection
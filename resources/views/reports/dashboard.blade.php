
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 style="font-size: 28px; font-weight: 600; color: #333; margin: 0;">Reports Dashboard</h2>
            <p style="color: #666; margin-top: 8px;">Overview of business performance and metrics</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body" style="padding: 24px;">
            <form method="GET" action="{{ route('reports.dashboard') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label" style="font-weight: 600; color: #333;">Period</label>
                    <select name="period" class="form-select" onchange="this.form.submit()">
                        <option value="today" {{ ($period ?? '') === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ ($period ?? '') === 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ ($period ?? 'month') === 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ ($period ?? '') === 'year' ? 'selected' : '' }}>This Year</option>
                        <option value="all" {{ ($period ?? '') === 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="custom" {{ ($period ?? '') === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                @if(($period ?? '') === 'custom')
                <div class="col-md-3">
                    <label class="form-label" style="font-weight: 600; color: #333;">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-weight: 600; color: #333;">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-weight: 600; color: #333;">&nbsp;</label>
                    <button type="submit" class="btn w-100" style="background: #e85d24; color: white; border: none; font-weight: 600;">Filter</button>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #e85d24;">
                <div class="card-body" style="padding: 24px;">
                    <p style="color: #666; font-size: 12px; font-weight: 600; text-transform: uppercase; margin: 0 0 8px 0;">Total Revenue</p>
                    <h3 style="color: #e85d24; font-size: 32px; font-weight: 700; margin: 0;">${{ number_format($totalRevenue, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #17a2b8;">
                <div class="card-body" style="padding: 24px;">
                    <p style="color: #666; font-size: 12px; font-weight: 600; text-transform: uppercase; margin: 0 0 8px 0;">Total Orders</p>
                    <h3 style="color: #17a2b8; font-size: 32px; font-weight: 700; margin: 0;">{{ $totalOrders }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #28a745;">
                <div class="card-body" style="padding: 24px;">
                    <p style="color: #666; font-size: 12px; font-weight: 600; text-transform: uppercase; margin: 0 0 8px 0;">Products</p>
                    <h3 style="color: #28a745; font-size: 32px; font-weight: 700; margin: 0;">{{ $totalProducts }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #6f42c1;">
                <div class="card-body" style="padding: 24px;">
                    <p style="color: #666; font-size: 12px; font-weight: 600; text-transform: uppercase; margin: 0 0 8px 0;">Customers</p>
                    <h3 style="color: #6f42c1; font-size: 32px; font-weight: 700; margin: 0;">{{ $totalCustomers }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    @if($chartData && count($chartData) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header" style="background: none; border-bottom: 2px solid #e9ecef; padding: 20px;">
                    <h5 style="color: #333; font-weight: 600; margin: 0;">Revenue & Orders Trend</h5>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <div style="position: relative; height: 300px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Report Links -->
    <div class="row mb-4">
        <div class="col-12">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <a href="{{ route('reports.sales') }}" class="card border-0 shadow-sm" style="border-radius: 12px; text-decoration: none; transition: all 0.3s ease;">
                    <div class="card-body" style="padding: 24px; text-align: center;">
                        <h5 style="color: #333; font-weight: 600; margin-bottom: 8px;">
                            <i class="fas fa-chart-line" style="color: #e85d24; margin-right: 8px;"></i>Sales Report
                        </h5>
                        <p style="color: #666; font-size: 12px; margin: 0;">View sales performance and revenue</p>
                    </div>
                </a>

                <a href="{{ route('reports.inventory') }}" class="card border-0 shadow-sm" style="border-radius: 12px; text-decoration: none; transition: all 0.3s ease;">
                    <div class="card-body" style="padding: 24px; text-align: center;">
                        <h5 style="color: #333; font-weight: 600; margin-bottom: 8px;">
                            <i class="fas fa-boxes" style="color: #17a2b8; margin-right: 8px;"></i>Inventory Report
                        </h5>
                        <p style="color: #666; font-size: 12px; margin: 0;">Monitor stock levels</p>
                    </div>
                </a>

                <a href="{{ route('reports.customers') }}" class="card border-0 shadow-sm" style="border-radius: 12px; text-decoration: none; transition: all 0.3s ease;">
                    <div class="card-body" style="padding: 24px; text-align: center;">
                        <h5 style="color: #333; font-weight: 600; margin-bottom: 8px;">
                            <i class="fas fa-users" style="color: #28a745; margin-right: 8px;"></i>Customer Report
                        </h5>
                        <p style="color: #666; font-size: 12px; margin: 0;">Analyze customer activity</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header" style="background: none; border-bottom: 2px solid #e9ecef; padding: 20px;">
                    <h5 style="color: #333; font-weight: 600; margin: 0;">Recent Orders</h5>
                </div>
                <div class="card-body" style="padding: 24px;">
                    @if ($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm" style="margin-bottom: 0;">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th style="padding: 8px; color: #666; font-weight: 600;">Order</th>
                                    <th style="padding: 8px; color: #666; font-weight: 600;">Customer</th>
                                    <th style="padding: 8px; color: #666; font-weight: 600; text-align: right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentOrders as $order)
                                <tr style="border-bottom: 1px solid #e9ecef;">
                                    <td style="padding: 8px; color: #333; font-weight: 500;">#{{ $order->id }}</td>
                                    <td style="padding: 8px; color: #666;">{{ $order->customer->name }}</td>
                                    <td style="padding: 8px; color: #333; font-weight: 500; text-align: right;">${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p style="color: #666; text-align: center;">No recent orders</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header" style="background: none; border-bottom: 2px solid #e9ecef; padding: 20px;">
                    <h5 style="color: #333; font-weight: 600; margin: 0;">
                        <i class="fas fa-exclamation-circle" style="color: #ffc107; margin-right: 8px;"></i>Low Stock Alerts
                    </h5>
                </div>
                <div class="card-body" style="padding: 24px;">
                    @if ($lowStockAlerts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm" style="margin-bottom: 0;">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th style="padding: 8px; color: #666; font-weight: 600;">Product</th>
                                    <th style="padding: 8px; color: #666; font-weight: 600; text-align: right;">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lowStockAlerts as $alert)
                                <tr style="border-bottom: 1px solid #e9ecef;">
                                    <td style="padding: 8px; color: #333;">{{ $alert->product->name }}</td>
                                    <td style="padding: 8px; text-align: right;">
                                        <span style="padding: 4px 8px; background: #fff3cd; color: #856404; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                            {{ $alert->quantity }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p style="color: #666; text-align: center;">All products are well stocked</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js for trend visualization -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    @if($chartData && count($chartData) > 0)
    const ctx = document.getElementById('trendChart');
    if (ctx) {
        const dates = [
            @foreach($chartData as $data)
                '{{ \Carbon\Carbon::parse($data->date)->translatedFormat('M d') }}','
            @endforeach
        ];
        
        const revenues = [
            @foreach($chartData as $data)
                {{ $data->total }},
            @endforeach
        ];

        const counts = [
            @foreach($chartData as $data)
                {{ $data->count }},
            @endforeach
        ];

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Revenue ($)',
                        data: revenues,
                        borderColor: '#e85d24',
                        backgroundColor: 'rgba(232, 93, 36, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Orders',
                        data: counts,
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            font: {
                                size: 12,
                                weight: '600'
                            },
                            padding: 16
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Revenue ($)',
                            font: {
                                weight: '600'
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Order Count',
                            font: {
                                weight: '600'
                            }
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                }
            }
        });
    }
    @endif
</script>
@endsection

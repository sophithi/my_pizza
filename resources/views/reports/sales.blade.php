@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 style="font-size: 28px; font-weight: 600; color: #333; margin: 0;">Sales Report</h2>
            <p style="color: #666; margin-top: 8px;">Track sales performance and revenue metrics</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body" style="padding: 24px;">
            <form method="GET" action="{{ route('reports.sales') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label" style="font-weight: 600; color: #333;">Period</label>
                    <select name="period" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ $period === 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ $period === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ $period === 'year' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                @if($period === 'custom')
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
                    <p style="color: #666; font-size: 12px; font-weight: 600; text-transform: uppercase; margin: 0 0 8px 0;">Completed</p>
                    <h3 style="color: #28a745; font-size: 32px; font-weight: 700; margin: 0;">{{ $completedOrders }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #ffc107;">
                <div class="card-body" style="padding: 24px;">
                    <p style="color: #666; font-size: 12px; font-weight: 600; text-transform: uppercase; margin: 0 0 8px 0;">Avg Order Value</p>
                    <h3 style="color: #ffc107; font-size: 32px; font-weight: 700; margin: 0;">${{ number_format($averageOrderValue, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Revenue Chart -->
    @if($dailyRevenue && count($dailyRevenue) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header" style="background: none; border-bottom: 2px solid #e9ecef; padding: 20px;">
                    <h5 style="color: #333; font-weight: 600; margin: 0;">Daily Revenue Trend</h5>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <div style="position: relative; height: 300px;">
                        <canvas id="dailyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header" style="background: none; border-bottom: 2px solid #e9ecef; padding: 20px;">
                    <h5 style="color: #333; font-weight: 600; margin: 0;">Orders by Status</h5>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <div class="table-responsive">
                        <table class="table" style="margin-bottom: 0;">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th style="padding: 8px; color: #666; font-weight: 600;">Status</th>
                                    <th style="padding: 8px; color: #666; font-weight: 600; text-align: right;">Orders</th>
                                    <th style="padding: 8px; color: #666; font-weight: 600; text-align: right;">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ordersByStatus as $stat)
                                <tr style="border-bottom: 1px solid #e9ecef;">
                                    <td style="padding: 8px; color: #333;">
                                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;
                                            background: {{ $stat->status === 'completed' ? '#d4edda' : ($stat->status === 'pending' ? '#fff3cd' : '#f8d7da') }};
                                            color: {{ $stat->status === 'completed' ? '#155724' : ($stat->status === 'pending' ? '#856404' : '#721c24') }};">
                                            {{ ucfirst($stat->status) }}
                                        </span>
                                    </td>
                                    <td style="padding: 8px; color: #666; text-align: right;">{{ $stat->count }}</td>
                                    <td style="padding: 8px; color: #333; font-weight: 500; text-align: right;">${{ number_format($stat->total ?? 0, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header" style="background: none; border-bottom: 2px solid #e9ecef; padding: 20px;">
                    <h5 style="color: #333; font-weight: 600; margin: 0;">Top 10 Products by Orders</h5>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <div class="table-responsive">
                        <table class="table table-sm" style="margin-bottom: 0;">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th style="padding: 8px; color: #666; font-weight: 600;">Product</th>
                                    <th style="padding: 8px; color: #666; font-weight: 600; text-align: right;">Orders</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productRevenue as $product)
                                <tr style="border-bottom: 1px solid #e9ecef;">
                                    <td style="padding: 8px; color: #333;">{{ $product->name }}</td>
                                    <td style="padding: 8px; color: #333; font-weight: 500; text-align: right;">{{ $product->order_items_count }} orders</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header" style="background: none; border-bottom: 2px solid #e9ecef; padding: 20px;">
                    <h5 style="color: #333; font-weight: 600; margin: 0;">Top 10 Customers by Revenue</h5>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <div class="table-responsive">
                        <table class="table table-hover" style="margin-bottom: 0;">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th style="padding: 12px; color: #666; font-weight: 600;">Customer</th>
                                    <th style="padding: 12px; color: #666; font-weight: 600; text-align: right;">Total Spent</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customerRevenue as $customer)
                                <tr style="border-bottom: 1px solid #e9ecef;">
                                    <td style="padding: 12px; color: #333;">{{ $customer->name }}</td>
                                    <td style="padding: 12px; color: #333; font-weight: 500; text-align: right;">${{ number_format($customer->orders_sum_total_amount ?? 0, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top: 20px;">
        <a href="{{ route('reports.dashboard') }}" class="btn" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500;">
            Back to Dashboard
        </a>
    </div>
</div>

<!-- Chart.js for daily revenue visualization -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    @if($dailyRevenue && count($dailyRevenue) > 0)
    const ctx = document.getElementById('dailyChart');
    if (ctx) {
        const dates = [
            @foreach($dailyRevenue as $daily)
                '{{ \Carbon\Carbon::parse($daily->date)->translatedFormat('M d') }}',
            @endforeach
        ];
        
        const revenues = [
            @foreach($dailyRevenue as $daily)
                {{ $daily->total }},
            @endforeach
        ];

        const counts = [
            @foreach($dailyRevenue as $daily)
                {{ $daily->count }},
            @endforeach
        ];

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Revenue ($)',
                        data: revenues,
                        backgroundColor: 'rgba(232, 93, 36, 0.7)',
                        borderColor: '#e85d24',
                        borderWidth: 2,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Orders',
                        data: counts,
                        backgroundColor: 'rgba(23, 162, 184, 0.5)',
                        borderColor: '#17a2b8',
                        borderWidth: 2,
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
                            }
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
                            text: 'Revenue ($)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Order Count'
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

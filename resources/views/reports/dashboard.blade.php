@extends('layouts.app')

@section('title', 'របាយការណ៍')

@push('styles')
    <style>
        .reports-home {
            --accent: #e85d24;
            --accent-dark: #cf4b15;
            --border: #e5e7eb;
            --muted: #64748b;
            --soft: #f8fafc;
            --surface: #fff;
            --text: #0f172a;
        }

        .reports-head {
            align-items: center;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .reports-title {
            color: var(--text);
            font-size: 30px;
            font-weight: 900;
            margin: 0;
        }

        .reports-subtitle {
            color: var(--muted);
            margin: 6px 0 0;
        }

        .metric-card,
        .report-panel,
        .report-link-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, .06);
        }

        .filter-row {
            align-items: center;
            display: grid;
            gap: 10px;
            grid-template-columns: minmax(180px, 240px) auto;
            justify-content: end;
        }

        .filter-row.has-dates {
            grid-template-columns: minmax(180px, 240px) minmax(170px, 220px) minmax(170px, 220px) auto;
        }

        .date-field {
            position: relative;
        }

        .date-field-label {
            color: var(--muted);
            font-size: 11px;
            font-weight: 900;
            left: 12px;
            position: absolute;
            top: 3px;
            text-transform: uppercase;
        }

        .date-field input {
            padding-top: 18px;
        }

        .reports-filter {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .05);
            padding: 10px;
        }

        .report-btn {
            align-items: center;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border: 0;
            border-radius: 8px;
            color: #fff;
            display: inline-flex;
            font-weight: 900;
            justify-content: center;
            min-height: 40px;
            padding: 9px 16px;
            text-decoration: none;
            white-space: nowrap;
        }

        .report-btn:hover {
            color: #fff;
            transform: translateY(-1px);
        }

        .report-link-grid,
        .metric-grid {
            display: grid;
            gap: 14px;
            margin-bottom: 16px;
        }

        .report-link-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .metric-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .report-link-card {
            color: var(--text);
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-height: 130px;
            padding: 18px;
            text-decoration: none;
            transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
        }

        .report-link-card:hover {
            border-color: rgba(232, 93, 36, .45);
            box-shadow: 0 14px 34px rgba(232, 93, 36, .12);
            color: var(--text);
            transform: translateY(-2px);
        }

        .report-link-kicker {
            color: var(--accent);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .report-link-title {
            font-size: 19px;
            font-weight: 900;
            margin: 0;
        }

        .report-link-text {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.45;
            margin: 0;
        }

        .metric-card {
            border-left: 4px solid var(--accent);
            padding: 16px;
        }

        .metric-label {
            color: var(--muted);
            font-size: 15px;
            font-weight: 900;
            margin: 0;
        }

        .metric-value {
            color: var(--text);
            font-size: 28px;
            font-weight: 900;
            margin-top: 6px;
        }

        .content-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: minmax(0, 1.25fr) minmax(320px, .75fr);
        }

        .report-panel {
            margin-bottom: 16px;
            overflow: hidden;
        }

        .panel-head {
            align-items: center;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            padding: 14px 16px;
        }

        .panel-title {
            color: var(--text);
            font-size: 18px;
            font-weight: 900;
            margin: 0;
        }

        .panel-body {
            padding: 16px;
        }

        .chart-wrap {
            height: 300px;
            position: relative;
        }

        .report-table {
            margin: 0;
        }

        .report-table th {
            background: var(--soft);
            color: var(--muted);
            font-size: 13px;
            font-weight: 900;
            padding: 11px 12px;
            text-transform: uppercase;
        }

        .report-table td {
            padding: 11px 12px;
            vertical-align: middle;
        }

        .empty-note {
            color: var(--muted);
            padding: 22px 16px;
            text-align: center;
        }

        @media (max-width: 1200px) {
            .report-link-grid,
            .metric-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .reports-head {
                align-items: stretch;
                flex-direction: column;
            }

            .filter-row,
            .report-link-grid,
            .metric-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $selectedPeriod = $period ?? 'month';
        $rangeText = match ($selectedPeriod) {
            'today' => 'ថ្ងៃនេះ',
            'week' => 'សប្ដាហ៍នេះ',
            'month' => 'ខែនេះ',
            'year' => 'ឆ្នាំនេះ',
            'all' => 'ទាំងអស់',
            'custom' => trim(($startDate ?? '') . ' - ' . ($endDate ?? '')),
            default => 'ខែនេះ',
        };

        $adminReportQuery = $selectedPeriod === 'custom'
            ? ['period' => 'custom', 'start_date' => $startDate, 'end_date' => $endDate]
            : ['period' => $selectedPeriod];
    @endphp

    <div class="container-fluid py-4 reports-home">
        <div class="reports-head">
            <div>
                <h2 class="reports-title">របាយការណ៍</h2>
               
            </div>
            <form method="GET" action="{{ route('reports.dashboard') }}" class="reports-filter">
                <div class="filter-row {{ $selectedPeriod === 'custom' ? 'has-dates' : '' }}">
                    <select name="period" class="form-select" onchange="this.form.submit()">
                        <option value="today" {{ $selectedPeriod === 'today' ? 'selected' : '' }}>ថ្ងៃនេះ</option>
                        <option value="week" {{ $selectedPeriod === 'week' ? 'selected' : '' }}>សប្ដាហ៍នេះ</option>
                        <option value="month" {{ $selectedPeriod === 'month' ? 'selected' : '' }}>ខែនេះ</option>
                        <option value="year" {{ $selectedPeriod === 'year' ? 'selected' : '' }}>ឆ្នាំនេះ</option>
                        <option value="all" {{ $selectedPeriod === 'all' ? 'selected' : '' }}>ទាំងអស់</option>
                        <option value="custom" {{ $selectedPeriod === 'custom' ? 'selected' : '' }}>ជ្រើសថ្ងៃ</option>
                    </select>
                    @if($selectedPeriod === 'custom')
                        <label class="date-field">
                            <span class="date-field-label">From</span>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate ?? '' }}">
                        </label>
                        <label class="date-field">
                            <span class="date-field-label">To</span>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate ?? '' }}">
                        </label>
                    @endif
                    <button type="submit" class="report-btn">Apply</button>
                </div>
            </form>
        </div>

        <div class="report-link-grid">
            @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                <a href="{{ route('reports.daily') }}" class="report-link-card">
                    <span class="report-link-kicker">Daily</span>
                    <h3 class="report-link-title">ប្រចាំថ្ងៃ</h3>
                    <p class="report-link-text">សរុបលក់ ចំណូល ចំណាយ និងស្តុកក្នុងមួយថ្ងៃ។</p>
                </a>
            @endif

            <a href="{{ route('reports.sales', $adminReportQuery) }}" class="report-link-card">
                <span class="report-link-kicker">Sales</span>
                <h3 class="report-link-title">ការលក់</h3>
                <p class="report-link-text">មើលចំណូល ចំនួនវិក្ក័យបត្រ និងទំនិញលក់បានច្រើន។</p>
            </a>

            @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                <a href="{{ route('reports.inventory', $adminReportQuery) }}" class="report-link-card">
                    <span class="report-link-kicker">Stock</span>
                    <h3 class="report-link-title">ស្តុកទំនិញ</h3>
                    <p class="report-link-text">ពិនិត្យចំនួនស្តុក ទំនិញជិតអស់ និងតម្លៃស្តុក។</p>
                </a>

                <a href="{{ route('reports.customers', $adminReportQuery) }}" class="report-link-card">
                    <span class="report-link-kicker">Customers</span>
                    <h3 class="report-link-title">អតិថិជន</h3>
                    <p class="report-link-text">មើលអតិថិជនសកម្ម ចំនួនកម្មង់ និងតម្លៃសរុប។</p>
                </a>
            @endif
        </div>

        <div class="metric-grid">
            <div class="metric-card">
                <p class="metric-label">លក់សរុប</p>
                <div class="metric-value">${{ number_format($totalRevenue, 2) }}</div>
            </div>
            <div class="metric-card">
                <p class="metric-label">ចំនួនវិក្ក័យបត្រ</p>
                <div class="metric-value">{{ number_format($totalOrders) }}</div>
            </div>
            <div class="metric-card">
                <p class="metric-label">ទំនិញ</p>
                <div class="metric-value">{{ number_format($totalProducts) }}</div>
            </div>
            <div class="metric-card">
                <p class="metric-label">អតិថិជន</p>
                <div class="metric-value">{{ number_format($totalCustomers) }}</div>
            </div>
        </div>

        <div class="content-grid">
            <div>
                <div class="report-panel">
                    <div class="panel-head">
                        <h3 class="panel-title">ចំណូល និងចំនួនកម្មង់</h3>
                    </div>
                    <div class="panel-body">
                        @if($chartData && count($chartData) > 0)
                            <div class="chart-wrap">
                                <canvas id="trendChart"></canvas>
                            </div>
                        @else
                            <div class="empty-note">មិនទាន់មានទិន្នន័យសម្រាប់រយៈពេលនេះ។</div>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                <div class="report-panel">
                    <div class="panel-head">
                        <h3 class="panel-title">វិក្ក័យបត្រថ្មីៗ</h3>
                    </div>
                    @if ($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table report-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>អតិថិជន</th>
                                        <th class="text-end">តម្លៃ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentOrders as $order)
                                        <tr>
                                            <td>{{ $order->invoice_number ?? ('#' . $order->id) }}</td>
                                            <td>{{ optional($order->customer)->name ?? 'N/A' }}</td>
                                            <td class="text-end fw-bold">${{ number_format($order->total_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-note">មិនទាន់មានវិក្ក័យបត្រថ្មីៗ។</div>
                    @endif
                </div>

                <div class="report-panel">
                    <div class="panel-head">
                        <h3 class="panel-title">ស្តុកជិតអស់</h3>
                    </div>
                    @if ($lowStockAlerts->count() > 0)
                        <div class="table-responsive">
                            <table class="table report-table">
                                <thead>
                                    <tr>
                                        <th>ទំនិញ</th>
                                        <th class="text-end">នៅសល់</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lowStockAlerts as $alert)
                                        <tr>
                                            <td>{{ optional($alert->product)->name ?? 'N/A' }}</td>
                                            <td class="text-end fw-bold text-danger">{{ number_format($alert->quantity) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-note">ស្តុកទំនិញនៅល្អ។</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($chartData && count($chartData) > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
        <script>
            const trendCanvas = document.getElementById('trendChart');

            if (trendCanvas) {
                new Chart(trendCanvas, {
                    type: 'line',
                    data: {
                        labels: @json($chartData->map(fn($data) => \Carbon\Carbon::parse($data->date)->format('d/m'))->values()),
                        datasets: [
                            {
                                label: 'លក់សរុប ($)',
                                data: @json($chartData->pluck('total')->map(fn($value) => (float) $value)->values()),
                                borderColor: '#e85d24',
                                backgroundColor: 'rgba(232, 93, 36, 0.08)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.35,
                                yAxisID: 'sales',
                            },
                            {
                                label: 'វិក្ក័យបត្រ',
                                data: @json($chartData->pluck('count')->map(fn($value) => (int) $value)->values()),
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37, 99, 235, 0.08)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.35,
                                yAxisID: 'orders',
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
                                labels: {
                                    boxWidth: 10,
                                    font: {
                                        size: 12,
                                        weight: '700'
                                    }
                                }
                            }
                        },
                        scales: {
                            sales: {
                                type: 'linear',
                                position: 'left',
                                ticks: {
                                    callback: value => '$' + value
                                }
                            },
                            orders: {
                                type: 'linear',
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false
                                },
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }
        </script>
    @endif
@endsection

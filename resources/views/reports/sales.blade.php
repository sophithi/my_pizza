@extends('layouts.app')

@section('title', 'របាយការណ៍ការលក់')

@push('styles')
    <style>
        .report-page {
            --accent: #e85d24;
            --accent-dark: #cf4b15;
            --accent-soft: rgba(232, 93, 36, .12);
            --border: #e5e7eb;
            --muted: #64748b;
            --soft: #f8fafc;
            --surface: #fff;
            --text: #0f172a;
            --success: #16a34a;
            --success-soft: #dcfce7;
            --warning: #b45309;
            --warning-soft: #fef3c7;
            --info: #2563eb;
            --info-soft: #dbeafe;
            --danger: #dc2626;
            --danger-soft: #fee2e2;
        }

        .report-head { align-items:flex-end; display:flex; flex-wrap:wrap; gap:16px; justify-content:space-between; margin-bottom:18px; }
        .report-title { color:var(--text); font-size:28px; font-weight:900; margin:0; }
        .report-subtitle { color:var(--muted); margin:6px 0 0; }

        .back-btn {
            align-items: center;
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 8px;
            color: var(--muted);
            display: inline-flex;
            font-weight: 800;
            gap: 7px;
            min-height: 40px;
            padding: 9px 16px;
            text-decoration: none;
            transition: border-color .15s ease, color .15s ease, background .15s ease;
            white-space: nowrap;
        }
        .back-btn:hover { border-color: var(--accent); color: var(--accent-dark); background: var(--accent-soft); }

        .report-filter,.metric,.panel { background:var(--surface); border:1px solid var(--border); border-radius:12px; box-shadow:0 10px 28px rgba(15,23,42,.05); }
        .report-filter { margin-bottom:16px; padding:12px; }
        .filter-row { align-items:center; display:grid; gap:10px; grid-template-columns:minmax(180px,240px) auto; justify-content:end; }
        .filter-row.has-dates { grid-template-columns:minmax(180px,240px) minmax(170px,220px) minmax(170px,220px) auto; }
        .date-field { position:relative; }
        .date-field-label { color:var(--muted); font-size:11px; font-weight:900; left:12px; position:absolute; top:3px; text-transform:uppercase; }
        .date-field input { padding-top:18px; }
        .report-btn { align-items:center; background:linear-gradient(135deg,var(--accent),var(--accent-dark)); border:0; border-radius:8px; box-shadow:0 4px 14px rgba(232,93,36,.28); color:#fff; display:inline-flex; font-weight:900; justify-content:center; min-height:40px; padding:9px 16px; text-decoration:none; transition:transform .15s ease, box-shadow .15s ease; white-space:nowrap; }
        .report-btn:hover { color:#fff; transform:translateY(-1px); box-shadow:0 8px 20px rgba(232,93,36,.32); }

        .metric-grid { display:grid; gap:14px; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); margin-bottom:16px; }
        .metric { align-items:center; display:flex; gap:13px; padding:16px; transition:box-shadow .15s ease, transform .15s ease; }
        .metric:hover { box-shadow:0 14px 32px rgba(15,23,42,.09); transform:translateY(-2px); }
        .metric-icon { align-items:center; background:var(--accent-soft); border-radius:10px; color:var(--accent); display:flex; flex-shrink:0; font-size:17px; height:42px; justify-content:center; width:42px; }
        .metric.is-success .metric-icon { background:var(--success-soft); color:var(--success); }
        .metric.is-info .metric-icon { background:var(--info-soft); color:var(--info); }
        .metric-label { color:var(--muted); font-size:11.5px; font-weight:800; margin:0; text-transform:uppercase; letter-spacing:.02em; }
        .metric-value { color:var(--text); font-size:21px; font-weight:900; line-height:1.2; margin-top:3px; }
        .metric-value-usd { color:var(--muted); font-size:12.5px; font-weight:700; margin-top:1px; }

        .kpi-trend { align-items:center; border-radius:999px; display:inline-flex; font-size:11px; font-weight:800; gap:3px; margin-top:6px; padding:2px 7px; width:fit-content; }
        .kpi-trend.is-up { background:var(--success-soft); color:var(--success); }
        .kpi-trend.is-down { background:var(--danger-soft); color:var(--danger); }
        .kpi-trend svg { height:9px; width:9px; }
        .kpi-caption { color:var(--muted); font-size:10.5px; font-weight:600; margin-top:4px; }

        .rank-row { align-items:center; display:grid; gap:12px; grid-template-columns:1fr 100px; margin-bottom:12px; }
        .rank-row:last-child { margin-bottom:0; }
        .rank-name { color:var(--text); font-size:12.5px; font-weight:700; margin-bottom:4px; }
        .rank-track { background:var(--soft); border-radius:6px; height:20px; overflow:hidden; position:relative; }
        .rank-fill { background:linear-gradient(90deg,var(--accent),var(--accent-dark)); border-radius:6px; bottom:0; left:0; position:absolute; top:0; }
        .rank-value { font-size:12.5px; font-weight:800; text-align:right; }
        .rank-value .u { color:var(--muted); display:block; font-size:10px; font-weight:600; }

        .money-stack { line-height:1.3; }
        .money-stack .khr { color:var(--text); display:block; font-weight:900; }
        .money-stack .usd { color:var(--muted); display:block; font-size:12px; font-weight:700; margin-top:1px; }
        .content-grid { display:grid; gap:16px; grid-template-columns:1fr 1fr; }
        .panel { margin-bottom:16px; overflow:hidden; }
        .panel-head { align-items:center; border-bottom:1px solid var(--border); display:flex; gap:9px; padding:14px 18px; }
        .panel-head i { color:var(--accent); font-size:14px; width:16px; }
        .panel-title { color:var(--text); font-size:15.5px; font-weight:900; margin:0; }
        .panel-body { padding:18px; }
        .chart-wrap { height:300px; position:relative; }
        @media (min-width:1200px) { .chart-wrap { height:340px; } }
        @media (max-width:480px) { .chart-wrap { height:240px; } }
        .report-table { margin:0; }
        .report-table th { background:var(--soft); color:var(--muted); font-size:12px; font-weight:800; letter-spacing:.03em; padding:11px 12px; text-transform:uppercase; }
        .report-table td { padding:11px 12px; vertical-align:middle; }
        .report-table tbody tr:hover { background:var(--soft); }
        .status-pill { border-radius:999px; display:inline-flex; font-size:12px; font-weight:800; padding:4px 10px; }
        .status-completed { background:var(--success-soft); color:#166534; }
        .status-pending { background:var(--warning-soft); color:#92400e; }
        .status-cancelled { background:var(--danger-soft); color:#991b1b; }
        .empty-note { color:var(--muted); padding:26px 16px; text-align:center; font-size:13px; }
        .role-badge { background:var(--soft); border-radius:6px; color:var(--muted); font-size:10px; font-weight:700; margin-left:6px; padding:1px 6px; text-transform:uppercase; }
        @media (max-width:1100px){ .content-grid{grid-template-columns:1fr 1fr;} }
        @media (max-width:760px){ .report-head{align-items:stretch; flex-direction:column;} .filter-row,.content-grid{grid-template-columns:1fr;} }
    </style>
@endpush

@section('content')
    @php
        $selectedPeriod = $period ?? 'all';
        $rangeText = [
            'all' => 'ទាំងអស់',
            'today' => 'ថ្ងៃនេះ',
            'yesterday' => 'ម្សិលមិញ',
            'week' => 'សប្ដាហ៍នេះ',
            'month' => 'ខែនេះ',
            'year' => 'ឆ្នាំនេះ',
            'custom' => trim(($startDate ?? '') . ' - ' . ($endDate ?? '')),
        ][$selectedPeriod] ?? 'ទាំងអស់';
    @endphp

    <div class="container-fluid py-4 report-page">
        <div class="report-head">
            <div>
                <h2 class="report-title">របាយការណ៍ការលក់</h2>
                <p class="report-subtitle">ចំណូល ចំនួនវិក្ក័យបត្រ និងទំនិញលក់បាន។ កំពុងមើល: {{ $rangeText }}</p>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('reports.sales.export.pdf', request()->query()) }}" target="_blank" class="back-btn"><i class="fas fa-file-pdf"></i> Export PDF</a>
                <a href="{{ route('reports.sales.export.excel', request()->query()) }}" class="back-btn"><i class="fas fa-file-excel"></i> Export Excel</a>
                <a href="{{ route('reports.dashboard') }}" class="back-btn"><i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ</a>
            </div>
        </div>

        <form method="GET" action="{{ route('reports.sales') }}" class="report-filter">
            <div class="filter-row {{ $selectedPeriod === 'custom' ? 'has-dates' : '' }}">
                <select name="period" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ $selectedPeriod === 'all' ? 'selected' : '' }}>ទាំងអស់</option>
                    <option value="today" {{ $selectedPeriod === 'today' ? 'selected' : '' }}>ថ្ងៃនេះ</option>
                    <option value="yesterday" {{ $selectedPeriod === 'yesterday' ? 'selected' : '' }}>ម្សិលមិញ</option>
                    <option value="week" {{ $selectedPeriod === 'week' ? 'selected' : '' }}>សប្ដាហ៍នេះ</option>
                    <option value="month" {{ $selectedPeriod === 'month' ? 'selected' : '' }}>ខែនេះ</option>
                    <option value="year" {{ $selectedPeriod === 'year' ? 'selected' : '' }}>ឆ្នាំនេះ</option>
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

        @php
            $trendBadge = function ($pct) {
                if ($pct === null) return '';
                $dir = $pct >= 0 ? 'is-up' : 'is-down';
                $path = $pct >= 0 ? 'M12 19V5M5 12l7-7 7 7' : 'M12 5v14M5 12l7 7 7-7';
                $sign = $pct >= 0 ? '+' : '';
                return '<span class="kpi-trend '.$dir.'"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="'.$path.'"/></svg>'.$sign.$pct.'%</span>';
            };
            $trendCaption = $comparisonLabel ? 'ធៀបនឹង' . $comparisonLabel . ($isPacedComparison ? ' (ត្រឹមម៉ោងនេះ)' : '') : null;
        @endphp
        <div class="metric-grid">
            <div class="metric">
                <div class="metric-icon"><i class="fas fa-sack-dollar"></i></div>
                <div>
                    <p class="metric-label">លក់សរុប</p>
                    <div class="metric-value">៛{{ number_format($totalRevenueKhr, 0) }}</div>
                    <div class="metric-value-usd">${{ number_format($totalRevenue, 2) }}</div>
                    {!! $trendBadge($revenueDeltaPct) !!}
                    @if($revenueDeltaPct !== null)<div class="kpi-caption">{{ $trendCaption }}</div>@endif
                </div>
            </div>
            <div class="metric is-info">
                <div class="metric-icon"><i class="fas fa-receipt"></i></div>
                <div>
                    <p class="metric-label">ចំនួនវិក្ក័យបត្រ</p>
                    <div class="metric-value">{{ number_format($totalOrders) }}</div>
                    {!! $trendBadge($ordersDeltaPct) !!}
                    @if($ordersDeltaPct !== null)<div class="kpi-caption">{{ $trendCaption }}</div>@endif
                </div>
            </div>
            <div class="metric is-success">
                <div class="metric-icon"><i class="fas fa-circle-check"></i></div>
                <div>
                    <p class="metric-label">បានបញ្ចប់</p>
                    <div class="metric-value">{{ number_format($completedOrders) }}</div>
                </div>
            </div>

            <div class="metric">
                <div class="metric-icon"><i class="fas fa-users"></i></div>
                <div>
                    <p class="metric-label">អតិថិជន</p>
                    <div class="metric-value">{{ number_format($uniqueCustomers) }}</div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head"><i class="fas fa-chart-column"></i><h3 class="panel-title">ចំណូលប្រចាំថ្ងៃ</h3></div>
            <div class="panel-body">
                @if($dailyRevenue && count($dailyRevenue) > 0)
                    <div class="chart-wrap"><canvas id="dailyChart"></canvas></div>
                @else
                    <div class="empty-note">មិនទាន់មានទិន្នន័យ។</div>
                @endif
            </div>
        </div>

        <div class="content-grid">
            <div class="panel">
                <div class="panel-head"><i class="fas fa-pizza-slice"></i><h3 class="panel-title">ទំនិញលក់បានច្រើន</h3></div>
                <div class="panel-body">
                    @if($productRevenue->count())
                        @php $maxProductCount = $productRevenue->max('order_items_count') ?: 1; @endphp
                        @foreach($productRevenue as $product)
                            <div class="rank-row">
                                <div>
                                    <div class="rank-name">{{ $product->name }}</div>
                                    <div class="rank-track">
                                        <div class="rank-fill" style="width: {{ max(2, round($product->order_items_count / $maxProductCount * 100, 1)) }}%"></div>
                                    </div>
                                </div>
                                <div class="rank-value">{{ number_format($product->order_items_count) }}<span class="u">កម្មង់</span></div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-note">មិនទាន់មានទិន្នន័យ។</div>
                    @endif
                </div>
            </div>

            <div class="panel">
                <div class="panel-head"><i class="fas fa-money-check-dollar"></i><h3 class="panel-title">វិធីបង់ប្រាក់</h3></div>
                <div class="panel-body">
                    @php $maxMethodKhr = collect($paymentMethodBreakdown)->max('khr') ?: 1; @endphp
                    @foreach($paymentMethodBreakdown as $method)
                        <div class="rank-row">
                            <div>
                                <div class="rank-name">{{ $method['label'] }}</div>
                                <div class="rank-track">
                                    <div class="rank-fill" style="width: {{ max(2, round($method['khr'] / $maxMethodKhr * 100, 1)) }}%"></div>
                                </div>
                            </div>
                            <div class="rank-value">
                                ៛{{ number_format($method['khr'], 0) }}
                                <span class="u">${{ number_format($method['usd'], 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head"><i class="fas fa-clock-rotate-left"></i><h3 class="panel-title">កម្មង់ថ្មីៗ</h3></div>
            @if($recentOrders->count())
                <div class="table-responsive">
                    <table class="table report-table">
                        <thead>
                            <tr>
                                <th>លេខកម្មង់</th>
                                <th>អតិថិជន</th>
                                <th>កាលបរិច្ឆេទ</th>
                                <th>ស្ថានភាព</th>
                                <th class="text-end">តម្លៃ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                                @php $statusClass = 'status-' . $order->status; @endphp
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->customer?->name ?? '—' }}</td>
                                    <td>{{ optional($order->order_date)->format('d/m/Y H:i') }}</td>
                                    <td><span class="status-pill {{ $statusClass }}">{{ ucfirst($order->status) }}</span></td>
                                    <td class="text-end">
                                        <div class="money-stack">
                                            <span class="khr">៛{{ number_format($order->totalKhr(), 0) }}</span>
                                            <span class="usd">${{ number_format($order->total_amount, 2) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-note">មិនទាន់មានទិន្នន័យ។</div>
            @endif
        </div>


        <div class="panel">
            <div class="panel-head"><i class="fas fa-users"></i><h3 class="panel-title">អតិថិជនចំណាយច្រើន</h3></div>
            <div class="panel-body">
                @if($customerRevenue->count())
                    @php $maxCustomerSpend = $customerRevenue->max('orders_sum_total_amount') ?: 1; @endphp
                    @foreach($customerRevenue as $customer)
                        @php $spend = $customer->orders_sum_total_amount ?? 0; @endphp
                        <div class="rank-row">
                            <div>
                                <div class="rank-name">{{ $customer->name }}</div>
                                <div class="rank-track">
                                    <div class="rank-fill" style="width: {{ max(2, round($spend / $maxCustomerSpend * 100, 1)) }}%"></div>
                                </div>
                            </div>
                            <div class="rank-value">
                                ៛{{ number_format($spend * $exchangeRate, 0) }}
                                <span class="u">${{ number_format($spend, 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-note">មិនទាន់មានទិន្នន័យ។</div>
                @endif
            </div>
        </div>
    </div>

    @if($dailyRevenue && count($dailyRevenue) > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
        <script>
            const salesCanvas = document.getElementById('dailyChart');
            if (salesCanvas) {
                const EXCHANGE_RATE = {{ $exchangeRate }};
                const salesUsd = @json($dailyRevenue->pluck('total')->map(fn($value) => (float) $value)->values());
                const salesKhr = salesUsd.map(v => Math.round(v * EXCHANGE_RATE));
                const invoiceCounts = @json($dailyRevenue->pluck('count')->map(fn($value) => (int) $value)->values());

                // Single axis only — a second "invoice count" axis here would be a
                // classic dual-axis chart (two independently-scaled y-axes implying a
                // false relationship at every crossing point). Invoice count is real
                // and useful, so it's surfaced in the tooltip instead of a second scale.
                new Chart(salesCanvas, {
                    type: 'bar',
                    data: {
                        labels: @json($dailyRevenue->map(fn($daily) => \Carbon\Carbon::parse($daily->date)->format('d/m'))->values()),
                        datasets: [
                            {
                                label: 'លក់សរុប (៛)',
                                data: salesKhr,
                                backgroundColor: 'rgba(232, 93, 36, 0.85)',
                                borderColor: '#e85d24',
                                borderWidth: 0,
                                borderRadius: 5,
                                maxBarThickness: 64,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return '៛' + context.parsed.y.toLocaleString();
                                    },
                                    afterLabel: function (context) {
                                        const i = context.dataIndex;
                                        const invoices = invoiceCounts[i];
                                        return [
                                            '$' + salesUsd[i].toFixed(2),
                                            invoices + ' វិក្ក័យបត្រ',
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(15, 23, 42, 0.06)' },
                                ticks: { callback: value => '៛' + value.toLocaleString() }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        </script>
    @endif
@endsection

@extends('layouts.app')

@section('title', 'របាយការណ៍ការលក់')

@push('styles')
    <style>
        .report-page { --accent:#e85d24; --accent-dark:#cf4b15; --border:#e5e7eb; --muted:#64748b; --soft:#f8fafc; --surface:#fff; --text:#0f172a; }
        .report-head { align-items:flex-end; display:flex; gap:16px; justify-content:space-between; margin-bottom:16px; }
        .report-title { color:var(--text); font-size:30px; font-weight:900; margin:0; }
        .report-subtitle { color:var(--muted); margin:6px 0 0; }
        .report-filter,.metric,.panel { background:var(--surface); border:1px solid var(--border); border-radius:8px; box-shadow:0 12px 32px rgba(15,23,42,.06); }
        .report-filter { margin-bottom:16px; padding:14px; }
        .filter-row { align-items:center; display:grid; gap:10px; grid-template-columns:minmax(180px,240px) auto; justify-content:end; }
        .filter-row.has-dates { grid-template-columns:minmax(180px,240px) minmax(170px,220px) minmax(170px,220px) auto; }
        .date-field { position:relative; }
        .date-field-label { color:var(--muted); font-size:11px; font-weight:900; left:12px; position:absolute; top:3px; text-transform:uppercase; }
        .date-field input { padding-top:18px; }
        .report-btn { align-items:center; background:linear-gradient(135deg,var(--accent),var(--accent-dark)); border:0; border-radius:8px; color:#fff; display:inline-flex; font-weight:900; justify-content:center; min-height:40px; padding:9px 16px; text-decoration:none; white-space:nowrap; }
        .report-btn:hover { color:#fff; transform:translateY(-1px); }
        .metric-grid { display:grid; gap:14px; grid-template-columns:repeat(4,minmax(0,1fr)); margin-bottom:16px; }
        .metric { border-left:4px solid var(--accent); padding:16px; }
        .metric-label { color:var(--muted); font-size:15px; font-weight:900; margin:0; }
        .metric-value { color:var(--text); font-size:28px; font-weight:900; margin-top:6px; }
        .content-grid { display:grid; gap:16px; grid-template-columns:1fr 1fr; }
        .panel { margin-bottom:16px; overflow:hidden; }
        .panel-head { border-bottom:1px solid var(--border); padding:14px 16px; }
        .panel-title { color:var(--text); font-size:18px; font-weight:900; margin:0; }
        .panel-body { padding:16px; }
        .chart-wrap { height:300px; position:relative; }
        .report-table { margin:0; }
        .report-table th { background:var(--soft); color:var(--muted); font-size:13px; font-weight:900; padding:11px 12px; text-transform:uppercase; }
        .report-table td { padding:11px 12px; vertical-align:middle; }
        .status-pill { border-radius:999px; display:inline-flex; font-size:12px; font-weight:900; padding:4px 10px; }
        .status-completed { background:#dcfce7; color:#166534; }
        .status-pending { background:#fef3c7; color:#92400e; }
        .status-cancelled { background:#fee2e2; color:#991b1b; }
        .empty-note { color:var(--muted); padding:22px 16px; text-align:center; }
        @media (max-width:1100px){ .metric-grid,.content-grid{grid-template-columns:1fr 1fr;} }
        @media (max-width:760px){ .report-head{align-items:stretch; flex-direction:column;} .filter-row,.metric-grid,.content-grid{grid-template-columns:1fr;} }
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
            <a href="{{ route('reports.dashboard') }}" class="report-btn">Back</a>
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

        <div class="metric-grid">
            <div class="metric"><p class="metric-label">លក់សរុប</p><div class="metric-value">${{ number_format($totalRevenue, 2) }}</div></div>
            <div class="metric"><p class="metric-label">ចំនួនវិក្ក័យបត្រ</p><div class="metric-value">{{ number_format($totalOrders) }}</div></div>
            <div class="metric"><p class="metric-label">បានបញ្ចប់</p><div class="metric-value text-success">{{ number_format($completedOrders) }}</div></div>
            <div class="metric"><p class="metric-label">មធ្យម/វិក្ក័យបត្រ</p><div class="metric-value">${{ number_format($averageOrderValue, 2) }}</div></div>
        </div>

        <div class="panel">
            <div class="panel-head"><h3 class="panel-title">ចំណូលប្រចាំថ្ងៃ</h3></div>
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
                <div class="panel-head"><h3 class="panel-title">ស្ថានភាពវិក្ក័យបត្រ</h3></div>
                <div class="table-responsive">
                    <table class="table report-table">
                        <thead><tr><th>ស្ថានភាព</th><th class="text-end">ចំនួន</th><th class="text-end">តម្លៃ</th></tr></thead>
                        <tbody>
                            @forelse ($ordersByStatus as $stat)
                                @php $statusClass = 'status-' . $stat->status; @endphp
                                <tr>
                                    <td><span class="status-pill {{ $statusClass }}">{{ ucfirst($stat->status) }}</span></td>
                                    <td class="text-end">{{ number_format($stat->count) }}</td>
                                    <td class="text-end fw-bold">${{ number_format($stat->total ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="empty-note">មិនទាន់មានទិន្នន័យ។</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head"><h3 class="panel-title">ទំនិញលក់បានច្រើន</h3></div>
                <div class="table-responsive">
                    <table class="table report-table">
                        <thead><tr><th>ទំនិញ</th><th class="text-end">ចំនួនកម្មង់</th></tr></thead>
                        <tbody>
                            @forelse ($productRevenue as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td class="text-end fw-bold">{{ number_format($product->order_items_count) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="empty-note">មិនទាន់មានទិន្នន័យ។</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head"><h3 class="panel-title">អតិថិជនចំណាយច្រើន</h3></div>
            <div class="table-responsive">
                <table class="table report-table">
                    <thead><tr><th>អតិថិជន</th><th class="text-end">ចំណាយសរុប</th></tr></thead>
                    <tbody>
                        @forelse ($customerRevenue as $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td class="text-end fw-bold">${{ number_format($customer->orders_sum_total_amount ?? 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="empty-note">មិនទាន់មានទិន្នន័យ។</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($dailyRevenue && count($dailyRevenue) > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
        <script>
            const salesCanvas = document.getElementById('dailyChart');
            if (salesCanvas) {
                new Chart(salesCanvas, {
                    type: 'bar',
                    data: {
                        labels: @json($dailyRevenue->map(fn($daily) => \Carbon\Carbon::parse($daily->date)->format('d/m'))->values()),
                        datasets: [
                            { label: 'លក់សរុប ($)', data: @json($dailyRevenue->pluck('total')->map(fn($value) => (float) $value)->values()), backgroundColor: 'rgba(232, 93, 36, 0.72)', borderColor: '#e85d24', borderWidth: 2, yAxisID: 'sales' },
                            { label: 'វិក្ក័យបត្រ', data: @json($dailyRevenue->pluck('count')->map(fn($value) => (int) $value)->values()), backgroundColor: 'rgba(37, 99, 235, 0.46)', borderColor: '#2563eb', borderWidth: 2, yAxisID: 'orders' }
                        ]
                    },
                    options: { responsive:true, maintainAspectRatio:false, interaction:{mode:'index', intersect:false}, scales:{ sales:{type:'linear', position:'left', ticks:{callback:value=>'$'+value}}, orders:{type:'linear', position:'right', grid:{drawOnChartArea:false}, ticks:{precision:0}} } }
                });
            }
        </script>
    @endif
@endsection

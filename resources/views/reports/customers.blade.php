@extends('layouts.app')

@section('title', 'របាយការណ៍អតិថិជន')

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
        .metric-grid { display:grid; gap:14px; grid-template-columns:repeat(3,minmax(0,1fr)); margin-bottom:16px; }
        .metric { border-left:4px solid var(--accent); padding:16px; }
        .metric-label { color:var(--muted); font-size:15px; font-weight:900; margin:0; }
        .metric-value { color:var(--text); font-size:28px; font-weight:900; margin-top:6px; }
        .panel { margin-bottom:16px; overflow:hidden; }
        .panel-head { border-bottom:1px solid var(--border); padding:14px 16px; }
        .panel-title { color:var(--text); font-size:18px; font-weight:900; margin:0; }
        .report-table { margin:0; }
        .report-table th { background:var(--soft); color:var(--muted); font-size:13px; font-weight:900; padding:11px 12px; text-transform:uppercase; }
        .report-table td { padding:11px 12px; vertical-align:middle; }
        .badge-count { background:#eef2ff; border-radius:999px; color:#3730a3; display:inline-flex; font-weight:900; min-width:34px; justify-content:center; padding:4px 10px; }
        .pager-wrap { margin-top:16px; }
        .empty-note { color:var(--muted); padding:22px 16px; text-align:center; }
        @media (max-width:1100px){ .metric-grid{grid-template-columns:1fr 1fr;} }
        @media (max-width:760px){ .report-head{align-items:stretch; flex-direction:column;} .filter-row,.metric-grid{grid-template-columns:1fr;} }
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

        $totalPeriodOrders = $customerActivity->sum('orders_count');
        $totalPeriodSpent = $customerActivity->sum('orders_sum_total_amount');
    @endphp

    <div class="container-fluid py-4 report-page">
        <div class="report-head">
            <div>
                <h2 class="report-title">របាយការណ៍អតិថិជន</h2>
                <p class="report-subtitle">មើលអតិថិជនសកម្ម ចំនួនកម្មង់ និងចំណាយ។ កំពុងមើល: {{ $rangeText }}</p>
            </div>
            <a href="{{ route('reports.dashboard') }}" class="report-btn">Back</a>
        </div>

        <form method="GET" action="{{ route('reports.customers') }}" class="report-filter">
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
            <div class="metric"><p class="metric-label">អតិថិជនសរុប</p><div class="metric-value">{{ number_format($totalCustomers) }}</div></div>
            <div class="metric"><p class="metric-label">អតិថិជនសកម្ម</p><div class="metric-value text-success">{{ number_format($activeCustomers) }}</div></div>
            <div class="metric"><p class="metric-label">ចំណាយក្នុងទំព័រនេះ</p><div class="metric-value">${{ number_format($totalPeriodSpent, 2) }}</div></div>
        </div>

        <div class="panel">
            <div class="panel-head"><h3 class="panel-title">សកម្មភាពអតិថិជន</h3></div>
            <div class="table-responsive">
                <table class="table report-table">
                    <thead><tr><th>ឈ្មោះអតិថិជន</th><th>ទំនាក់ទំនង</th><th class="text-end">កម្មង់</th><th class="text-end">ចំណាយសរុប</th><th class="text-end">មធ្យម/កម្មង់</th></tr></thead>
                    <tbody>
                        @forelse ($customerActivity as $customer)
                            @php
                                $ordersCount = $customer->orders_count ?? 0;
                                $spent = $customer->orders_sum_total_amount ?? 0;
                            @endphp
                            <tr>
                                <td class="fw-bold">{{ $customer->name }}</td>
                                <td>{{ $customer->phone ?? 'N/A' }}</td>
                                <td class="text-end"><span class="badge-count">{{ number_format($ordersCount) }}</span></td>
                                <td class="text-end fw-bold">${{ number_format($spent, 2) }}</td>
                                <td class="text-end">${{ number_format($ordersCount > 0 ? $spent / $ordersCount : 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="empty-note">មិនទាន់មានទិន្នន័យ។</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pager-wrap">{{ $customerActivity->links() }}</div>
    </div>
@endsection

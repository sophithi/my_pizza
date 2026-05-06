@extends('layouts.app')

@section('title', 'របាយការណ៍ស្តុក')

@push('styles')
    <style>
        .report-page { --accent:#e85d24; --accent-dark:#cf4b15; --border:#e5e7eb; --muted:#64748b; --soft:#f8fafc; --surface:#fff; --text:#0f172a; }
        .report-head { align-items:flex-end; display:flex; gap:16px; justify-content:space-between; margin-bottom:16px; }
        .report-title { color:var(--text); font-size:30px; font-weight:900; margin:0; }
        .report-subtitle { color:var(--muted); margin:6px 0 0; }
        .report-filter,.metric,.panel,.alert-soft { background:var(--surface); border:1px solid var(--border); border-radius:8px; box-shadow:0 12px 32px rgba(15,23,42,.06); }
        .report-filter { margin-bottom:16px; padding:14px; }
        .filter-row { align-items:center; display:grid; gap:10px; grid-template-columns:minmax(180px,240px) 1fr; }
        .report-btn { align-items:center; background:linear-gradient(135deg,var(--accent),var(--accent-dark)); border:0; border-radius:8px; color:#fff; display:inline-flex; font-weight:900; justify-content:center; min-height:40px; padding:9px 16px; text-decoration:none; white-space:nowrap; }
        .report-btn:hover { color:#fff; transform:translateY(-1px); }
        .metric-grid { display:grid; gap:14px; grid-template-columns:repeat(4,minmax(0,1fr)); margin-bottom:16px; }
        .metric { border-left:4px solid var(--accent); padding:16px; }
        .metric-label { color:var(--muted); font-size:15px; font-weight:900; margin:0; }
        .metric-value { color:var(--text); font-size:28px; font-weight:900; margin-top:6px; }
        .panel { margin-bottom:16px; overflow:hidden; }
        .panel-head { border-bottom:1px solid var(--border); padding:14px 16px; }
        .panel-title { color:var(--text); font-size:18px; font-weight:900; margin:0; }
        .report-table { margin:0; }
        .report-table th { background:var(--soft); color:var(--muted); font-size:13px; font-weight:900; padding:11px 12px; text-transform:uppercase; }
        .report-table td { padding:11px 12px; vertical-align:middle; }
        .status-pill { border-radius:999px; display:inline-flex; font-size:12px; font-weight:900; padding:4px 10px; }
        .status-ok { background:#dcfce7; color:#166534; }
        .status-low { background:#fef3c7; color:#92400e; }
        .status-out { background:#fee2e2; color:#991b1b; }
        .alert-soft { background:#fff7ed; border-color:#fed7aa; color:#9a3412; margin-bottom:16px; padding:14px 16px; }
        .pager-wrap { margin-top:16px; }
        .empty-note { color:var(--muted); padding:22px 16px; text-align:center; }
        @media (max-width:1100px){ .metric-grid{grid-template-columns:1fr 1fr;} }
        @media (max-width:760px){ .report-head{align-items:stretch; flex-direction:column;} .filter-row,.metric-grid{grid-template-columns:1fr;} }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4 report-page">
        <div class="report-head">
            <div>
                <h2 class="report-title">របាយការណ៍ស្តុក</h2>
                <p class="report-subtitle">ពិនិត្យស្តុកបច្ចុប្បន្ន តម្លៃទំនិញ និងតម្លៃស្តុកសរុប។</p>
            </div>
            <a href="{{ route('reports.dashboard') }}" class="report-btn">Back</a>
        </div>

        <form method="GET" action="{{ route('reports.inventory') }}" class="report-filter">
            <div class="filter-row">
                <select name="period" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ ($period ?? 'all') === 'all' ? 'selected' : '' }}>ស្តុកបច្ចុប្បន្ន</option>
                    <option value="today" {{ ($period ?? '') === 'today' ? 'selected' : '' }}>ថ្ងៃនេះ</option>
                    <option value="week" {{ ($period ?? '') === 'week' ? 'selected' : '' }}>សប្ដាហ៍នេះ</option>
                    <option value="month" {{ ($period ?? '') === 'month' ? 'selected' : '' }}>ខែនេះ</option>
                    <option value="year" {{ ($period ?? '') === 'year' ? 'selected' : '' }}>ឆ្នាំនេះ</option>
                </select>
                <div></div>
            </div>
        </form>

        <div class="metric-grid">
            <div class="metric"><p class="metric-label">ទំនិញសរុប</p><div class="metric-value">{{ number_format($totalProducts) }}</div></div>
            <div class="metric"><p class="metric-label">ជិតអស់</p><div class="metric-value text-warning">{{ number_format($lowStockProducts->count()) }}</div></div>
            <div class="metric"><p class="metric-label">អស់ស្តុក</p><div class="metric-value text-danger">{{ number_format($outOfStockCount) }}</div></div>
            <div class="metric"><p class="metric-label">តម្លៃស្តុក</p><div class="metric-value">${{ number_format($totalInventoryValue, 2) }}</div></div>
        </div>

        @if ($lowStockProducts->count() > 0)
            <div class="alert-soft">
                មានទំនិញ {{ number_format($lowStockProducts->count()) }} មុខត្រូវពិនិត្យស្តុកឡើងវិញ។
            </div>

            <div class="panel">
                <div class="panel-head"><h3 class="panel-title">ទំនិញជិតអស់</h3></div>
                <div class="table-responsive">
                    <table class="table report-table">
                        <thead><tr><th>ទំនិញ</th><th class="text-end">នៅសល់</th><th class="text-end">កម្រិតរំលឹក</th><th class="text-end">ខ្វះ</th></tr></thead>
                        <tbody>
                            @foreach ($lowStockProducts as $low)
                                <tr>
                                    <td class="fw-bold">{{ optional($low->product)->name ?? 'N/A' }}</td>
                                    <td class="text-end">{{ number_format($low->quantity) }}</td>
                                    <td class="text-end">{{ number_format($low->reorder_level) }}</td>
                                    <td class="text-end fw-bold text-danger">{{ number_format(max($low->reorder_level - $low->quantity, 0)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="panel">
            <div class="panel-head"><h3 class="panel-title">ស្ថានភាពស្តុក</h3></div>
            <div class="table-responsive">
                <table class="table report-table">
                    <thead>
                        <tr>
                            <th>ទំនិញ</th>
                            <th class="text-end">នៅសល់</th>
                            <th class="text-end">កម្រិតរំលឹក</th>
                            <th class="text-end">តម្លៃ/មួយ</th>
                            <th class="text-end">តម្លៃសរុប</th>
                            <th class="text-center">ស្ថានភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($inventory as $inv)
                            @php
                                $unitPriceUsd = (float) ($inv->product?->price_usd ?? 0);
                                $unitPriceKhr = (float) ($inv->product?->price_khr ?? 0);
                                $valueUsd = $unitPriceUsd * (float) $inv->quantity;
                                $valueKhr = $unitPriceKhr * (float) $inv->quantity;
                            @endphp
                            <tr>
                                <td class="fw-bold">{{ optional($inv->product)->name ?? 'N/A' }}</td>
                                <td class="text-end">{{ number_format($inv->quantity) }}</td>
                                <td class="text-end">{{ number_format($inv->reorder_level) }}</td>
                                <td class="text-end">
                                    <strong>${{ number_format($unitPriceUsd, 2) }}</strong>
                                    <div class="text-muted small">៛{{ number_format($unitPriceKhr, 0) }}</div>
                                </td>
                                <td class="text-end">
                                    <strong>${{ number_format($valueUsd, 2) }}</strong>
                                    <div class="text-muted small">៛{{ number_format($valueKhr, 0) }}</div>
                                </td>
                                <td class="text-center">
                                    @if ($inv->quantity == 0)
                                        <span class="status-pill status-out">អស់</span>
                                    @elseif ($inv->quantity <= $inv->reorder_level)
                                        <span class="status-pill status-low">ជិតអស់</span>
                                    @else
                                        <span class="status-pill status-ok">ល្អ</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="empty-note">មិនទាន់មានទិន្នន័យ។</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pager-wrap">{{ $inventory->links() }}</div>
    </div>
@endsection

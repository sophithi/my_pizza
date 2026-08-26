@extends('layouts.app')

@section('title', 'របាយការណ៍ស្តុក')

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
            --danger: #dc2626;
            --danger-soft: #fee2e2;
        }

        .report-head {
            align-items: flex-end;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .report-title {
            align-items: center;
            color: var(--text);
            display: flex;
            font-size: 28px;
            font-weight: 900;
            gap: 10px;
            margin: 0;
        }

        .report-title i {
            color: var(--accent);
        }

        .report-subtitle {
            color: var(--muted);
            margin: 6px 0 0;
        }

        .report-filter,
        .metric,
        .panel,
        .alert-soft {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, .06);
        }

        .report-filter {
            margin-bottom: 16px;
            padding: 14px;
        }

        .filter-row {
            align-items: center;
            display: grid;
            gap: 10px;
            grid-template-columns: minmax(180px, 240px) 1fr;
        }

        .report-btn,
        .back-btn {
            align-items: center;
            border: 0;
            border-radius: 8px;
            display: inline-flex;
            font-weight: 900;
            gap: 7px;
            justify-content: center;
            min-height: 40px;
            padding: 9px 16px;
            text-decoration: none;
            white-space: nowrap;
        }

        .report-btn {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
        }

        .report-btn:hover {
            color: #fff;
            transform: translateY(-1px);
        }

        .back-btn {
            background: var(--surface);
            border: 1.5px solid var(--border);
            color: var(--muted);
            transition: background .15s ease, border-color .15s ease, color .15s ease;
        }

        .back-btn:hover {
            background: var(--accent-soft);
            border-color: rgba(232, 93, 36, .4);
            color: var(--accent-dark);
        }

        .metric-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 16px;
        }

        .metric {
            align-items: center;
            display: flex;
            gap: 13px;
            padding: 16px;
            transition: box-shadow .15s ease, transform .15s ease;
        }

        .metric:hover {
            box-shadow: 0 14px 32px rgba(15, 23, 42, .09);
            transform: translateY(-2px);
        }

        .metric-icon {
            align-items: center;
            background: var(--accent-soft);
            border-radius: 10px;
            color: var(--accent);
            display: flex;
            flex-shrink: 0;
            font-size: 17px;
            height: 44px;
            justify-content: center;
            width: 44px;
        }

        .metric.is-warning .metric-icon {
            background: var(--warning-soft);
            color: var(--warning);
        }

        .metric.is-danger .metric-icon {
            background: var(--danger-soft);
            color: var(--danger);
        }

        .metric-label {
            color: var(--muted);
            font-size: 12.5px;
            font-weight: 900;
            margin: 0;
            text-transform: uppercase;
        }

        .metric-value {
            color: var(--text);
            font-size: 22px;
            font-weight: 900;
            line-height: 1.2;
            margin-top: 4px;
        }

        .metric-value-usd {
            color: var(--muted);
            font-size: 12.5px;
            font-weight: 700;
            margin-top: 1px;
        }

        .panel {
            margin-bottom: 16px;
            overflow: hidden;
        }

        .panel-head {
            align-items: center;
            border-bottom: 1px solid var(--border);
            display: flex;
            gap: 9px;
            padding: 14px 16px;
        }

        .panel-head i {
            color: var(--accent);
            font-size: 14px;
            width: 16px;
        }

        .panel-title {
            color: var(--text);
            font-size: 16px;
            font-weight: 900;
            margin: 0;
        }

        .report-table {
            margin: 0;
        }

        .report-table th {
            background: var(--soft);
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .02em;
            padding: 11px 12px;
            text-transform: uppercase;
        }

        .report-table td {
            padding: 11px 12px;
            vertical-align: middle;
        }

        .report-table tbody tr:hover {
            background: var(--soft);
        }

        .status-pill {
            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 900;
            padding: 4px 10px;
        }

        .status-ok {
            background: var(--success-soft);
            color: #166534;
        }

        .status-low {
            background: var(--warning-soft);
            color: #92400e;
        }

        .status-out {
            background: var(--danger-soft);
            color: #991b1b;
        }

        .pill {
            border-radius: 999px;
            display: inline-flex;
            font-size: 12.5px;
            font-weight: 800;
            padding: 3px 10px;
            white-space: nowrap;
        }

        .pill-in {
            background: var(--success-soft);
            color: #166534;
        }

        .pill-out {
            background: var(--danger-soft);
            color: #991b1b;
        }

        .alert-soft {
            align-items: center;
            background: #fff7ed;
            border-color: #fed7aa;
            color: #9a3412;
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
            padding: 14px 16px;
        }

        .alert-soft i {
            font-size: 16px;
        }

        .pager-wrap {
            display: flex;
            justify-content: center;
            padding: 0 16px 16px;
        }

        .pager-wrap .pagination {
            flex-wrap: wrap;
            column-gap: 4px;
            margin-bottom: 0;
            row-gap: 6px;
        }

        .pager-wrap .page-link {
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--accent);
            font-size: 13px;
            padding: 6px 12px;
        }

        .pager-wrap .page-link:hover,
        .pager-wrap .page-link:focus {
            background: var(--accent-soft);
            border-color: var(--accent);
            box-shadow: none;
            color: var(--accent-dark);
        }

        .pager-wrap .page-item.active .page-link {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .pager-wrap .page-item.disabled .page-link {
            background: var(--soft);
            border-color: var(--border);
            color: #cbd5e1;
        }

        .empty-note {
            color: var(--muted);
            padding: 22px 16px;
            text-align: center;
        }

        @media (max-width:1100px) {
            .metric-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width:760px) {
            .report-head {
                align-items: stretch;
                flex-direction: column;
            }

            .filter-row,
            .metric-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4 report-page">
        <div class="report-head">
            <div>
                <h2 class="report-title"><i class="fas fa-warehouse"></i> របាយការណ៍ស្តុក</h2>
                <p class="report-subtitle">ពិនិត្យស្តុកបច្ចុប្បន្ន តម្លៃទំនិញ និងតម្លៃស្តុកសរុប</p>
            </div>
            <a href="{{ route('reports.dashboard') }}" class="back-btn"><i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ</a>
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
            <div class="metric">
                <div class="metric-icon"><i class="fas fa-boxes-stacked"></i></div>
                <div>
                    <p class="metric-label">ទំនិញសរុប</p>
                    <div class="metric-value">{{ number_format($totalProducts) }}</div>
                </div>
            </div>
            <div class="metric is-warning">
                <div class="metric-icon"><i class="fas fa-triangle-exclamation"></i></div>
                <div>
                    <p class="metric-label">ជិតអស់</p>
                    <div class="metric-value">{{ number_format($lowStockProducts->count()) }}</div>
                </div>
            </div>
            <div class="metric is-danger">
                <div class="metric-icon"><i class="fas fa-circle-xmark"></i></div>
                <div>
                    <p class="metric-label">អស់ស្តុក</p>
                    <div class="metric-value">{{ number_format($outOfStockCount) }}</div>
                </div>
            </div>
            <div class="metric">
                <div class="metric-icon"><i class="fas fa-sack-dollar"></i></div>
                <div>
                    <p class="metric-label">តម្លៃស្តុក</p>
                    <div class="metric-value">៛{{ number_format($totalInventoryValueKhr, 0) }}</div>
                    <div class="metric-value-usd">${{ number_format($totalInventoryValue, 2) }}</div>
                </div>
            </div>
        </div>

        @if ($lowStockProducts->count() > 0)
            <div class="alert-soft">
                <i class="fas fa-triangle-exclamation"></i>
                មានទំនិញ {{ number_format($lowStockProducts->count()) }} មុខត្រូវពិនិត្យស្តុកឡើងវិញ។
            </div>

            <div class="panel">
                <div class="panel-head"><i class="fas fa-triangle-exclamation"></i>
                    <h3 class="panel-title">ទំនិញជិតអស់</h3>
                </div>
                <div class="table-responsive">
                    <table class="table report-table">
                        <thead>
                            <tr>
                                <th>ទំនិញ</th>
                                <th class="text-end">នៅសល់</th>
                                <th class="text-end">កម្រិតរំលឹក</th>
                                <th class="text-end">ខ្វះ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lowStockProducts as $low)
                                <tr>
                                    <td class="fw-bold">{{ optional($low->product)->name ?? 'N/A' }}</td>
                                    <td class="text-end">{{ number_format($low->quantity) }}</td>
                                    <td class="text-end">{{ number_format($low->reorder_level) }}</td>
                                    <td class="text-end fw-bold text-danger">
                                        {{ number_format(max($low->reorder_level - $low->quantity, 0)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="panel">
            <div class="panel-head"><i class="fas fa-arrow-right-arrow-left"></i>
                <h3 class="panel-title">ចលនាស្តុក</h3>
            </div>
            @if ($stockMovement->count() > 0)
                <div class="table-responsive">
                    <table class="table report-table">
                        <thead>
                            <tr>
                                <th>ទំនិញ</th>
                                <th class="text-center">ចូលក្នុងស្តុក</th>
                                <th class="text-center">កាត់ចេញពីស្តុក</th>
                                <th class="text-center">នៅសល់ក្នុងស្តុក</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stockMovement as $stock)
                                <tr>
                                    <td class="fw-bold">{{ $stock->name ?? 'មិនមានឈ្មោះ' }}</td>
                                    <td class="text-center">
                                        @if ($stock->stock_in > 0)
                                            <span class="pill pill-in">+{{ number_format($stock->stock_in) }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($stock->stock_out > 0)
                                            <span class="pill pill-out">-{{ number_format($stock->stock_out) }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold">{{ number_format($stock->current_quantity) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-note">មិនមានចលនាស្តុកសម្រាប់រយៈពេលនេះទេ។</div>
            @endif
        </div>

        <div class="panel">
            <div class="panel-head"><i class="fas fa-warehouse"></i>
                <h3 class="panel-title">ស្ថានភាពស្តុក</h3>
            </div>
            <div class="table-responsive">
                <table class="table report-table">
                    <thead>
                        <tr>
                            <th>ទំនិញ</th>
                            <th class="text-end">នៅសល់</th>
                            <th class="text-end">កម្រិតរំលឹក</th>
                            <th class="text-end">តម្លៃ/ខ្នាត</th>
              
                            <th class="text-center">ស្ថានភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($inventory as $inv)
                            @php
                                $unitPriceUsd = (float) ($inv->product?->price_usd ?? 0);
                                $unitPriceKhr = (float) ($inv->product?->price_khr ?? 0);
                                $onHandQty = max((float) $inv->quantity, 0);
                                $valueUsd = $unitPriceUsd * $onHandQty;
                                $valueKhr = $unitPriceKhr * $onHandQty;
                            @endphp
                            <tr>
                                <td class="fw-bold">{{ optional($inv->product)->name ?? 'N/A' }}</td>
                                <td class="text-end {{ $inv->quantity < 0 ? 'text-danger fw-bold' : '' }}">
                                    {{ number_format($inv->quantity) }}</td>
                                <td class="text-end">{{ number_format($inv->reorder_level) }}</td>
                                <td class="text-end">
                                    <strong>៛{{ number_format($unitPriceKhr, 0) }}</strong>
                                    <div class="text-muted small">${{ number_format($unitPriceUsd, 2) }}</div>
                                </td>
                         
                                <td class="text-center">
                                    @if ($inv->quantity <= 0)
                                        <span class="status-pill status-out">អស់</span>
                                    @elseif ($inv->quantity <= $inv->reorder_level)
                                        <span class="status-pill status-low">ជិតអស់</span>
                                    @else
                                        <span class="status-pill status-ok">ល្អ</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-note">មិនទាន់មានទិន្នន័យ។</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div> 
        </div>
          @if ($inventory->hasPages())
                <div class="pager-wrap">
                    {{ $inventory->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
         @endif
    </div>
@endsection
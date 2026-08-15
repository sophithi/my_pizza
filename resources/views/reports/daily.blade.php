@extends('layouts.app')

@section('title', 'របាយការណ៍ប្រចាំថ្ងៃ')

@push('styles')
    <style>
        .daily-report {
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
            align-items: flex-start;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .report-title {
            align-items: center;
            color: var(--text);
            display: flex;
            font-size: 26px;
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

        .report-filter {
            align-items: center;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, .06);
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 8px;
        }

        .day-nav-group {
            align-items: center;
            display: flex;
            gap: 8px;
        }

        .date-input {
            max-width: 190px;
        }

        .day-actions {
            align-items: center;
            display: flex;
            gap: 8px;
        }

        .day-nav-btn {
            align-items: center;
            background: var(--soft);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            display: inline-flex;
            flex-shrink: 0;
            height: 40px;
            justify-content: center;
            text-decoration: none;
            transition: background .15s ease, border-color .15s ease;
            width: 40px;
        }

        .day-nav-btn:hover {
            background: var(--accent-soft);
            border-color: rgba(232, 93, 36, .4);
            color: var(--accent-dark);
        }

        .day-nav-today {
            align-items: center;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--muted);
            display: inline-flex;
            font-size: 13px;
            font-weight: 700;
            height: 40px;
            justify-content: center;
            padding: 0 12px;
            text-decoration: none;
            white-space: nowrap;
        }

        .day-nav-today:hover {
            background: var(--soft);
            color: var(--text);
        }

        .day-nav-today.is-active {
            background: var(--accent-soft);
            border-color: rgba(232, 93, 36, .4);
            color: var(--accent-dark);
        }

        .report-btn {
            align-items: center;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border: 0;
            border-radius: 8px;
            color: #fff;
            display: inline-flex;
            font-weight: 900;
            gap: 8px;
            height: 40px;
            justify-content: center;
            padding: 0 16px;
            white-space: nowrap;
        }

        .report-btn:hover {
            color: #fff;
        }

        .day-nav-btn:focus-visible,
        .day-nav-today:focus-visible,
        .report-btn:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
        }

        .metric-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            margin-bottom: 16px;
        }

        .metric {
            align-items: center;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .04);
            display: flex;
            gap: 12px;
            padding: 16px;
            transition: box-shadow .15s ease, transform .15s ease;
        }

        .metric:hover {
            box-shadow: 0 12px 26px rgba(15, 23, 42, .08);
            transform: translateY(-2px);
        }

        .metric-icon {
            align-items: center;
            background: var(--accent-soft);
            border-radius: 10px;
            color: var(--accent);
            display: flex;
            flex-shrink: 0;
            font-size: 18px;
            height: 44px;
            justify-content: center;
            width: 44px;
        }

        .metric.is-income .metric-icon {
            background: var(--success-soft);
            color: var(--success);
        }

        .metric.is-expense .metric-icon {
            background: var(--danger-soft);
            color: var(--danger);
        }

        .metric.is-unpaid .metric-icon {
            background: var(--warning-soft);
            color: var(--warning);
        }

        .metric-label {
            color: var(--muted);
            font-size: 12.5px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .metric-value {
            color: var(--text);
            font-size: 22px;
            font-weight: 900;
            line-height: 1.2;
            margin-top: 2px;
        }

        .metric-value-usd {
            color: var(--muted);
            font-size: 12.5px;
            font-weight: 700;
            margin-top: 1px;
        }

        .report-grid {
            display: grid;
            gap: 20px;
            grid-template-columns: 1.1fr .9fr;
        }

        .report-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, .06);
            margin-bottom: 16px;
            overflow: hidden;
        }

        .report-card-head {
            align-items: center;
            border-bottom: 1px solid var(--border);
            display: flex;
            flex-wrap: wrap;
            gap: 8px 10px;
            justify-content: space-between;
            padding: 14px 16px;
        }

        .report-card-title {
            align-items: center;
            color: var(--text);
            display: flex;
            font-size: 16px;
            font-weight: 900;
            gap: 9px;
            margin: 0;
        }

        .report-card-title i {
            color: var(--accent);
            font-size: 14px;
            width: 18px;
        }

        .report-card-badge {
            background: var(--soft);
            border-radius: 999px;
            color: var(--text);
            font-size: 13px;
            font-weight: 800;
            padding: 4px 12px;
        }

        .daily-table {
            margin: 0;
        }

        .daily-table th {
            background: var(--soft);
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .3px;
            padding: 10px 16px;
            text-transform: uppercase;
        }

        .daily-table td {
            padding: 11px 16px;
            vertical-align: middle;
        }

        .daily-table tbody tr:hover {
            background: var(--soft);
        }

        .item-name {
            color: var(--text);
            font-weight: 700;
        }

        .method-breakdown {
            border-radius: 12px;
        }

        .method-name {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .method-khr {
            color: var(--text);
            font-size: 22px;
            font-weight: 900;
            line-height: 1.25;
            margin-top: 2px;
        }

        .method-usd {
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
            margin-top: 1px;
        }

        .money-stack {
            line-height: 1.3;
        }

        .money-stack .khr {
            color: var(--text);
            display: block;
            font-weight: 900;
        }

        .money-stack .usd {
            color: var(--muted);
            display: block;
            font-size: 12px;
            font-weight: 700;
            margin-top: 1px;
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

        .pill-low {
            background: var(--danger-soft);
            color: #991b1b;
        }

        .person-name {
            color: var(--text);
            font-weight: 700;
        }

        .empty-note {
            align-items: center;
            color: var(--muted);
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 30px 16px;
            text-align: center;
        }

        .empty-note i {
            color: #cbd5e1;
            font-size: 24px;
        }

        /* ── ranked bar chart (products sold) ── */
        .rank-row {
            align-items: center;
            display: grid;
            gap: 12px;
            grid-template-columns: 130px 1fr 110px;
            margin-bottom: 14px;
            padding: 0 16px;
        }

        .rank-row:last-child {
            margin-bottom: 0;
        }

        .rank-row:first-child {
            margin-top: 16px;
        }

        .rank-name {
            color: var(--text);
            font-size: 12.5px;
            font-weight: 700;
        }

        .rank-qty {
            color: var(--muted);
            font-size: 10.5px;
            font-weight: 600;
        }

        .rank-track {
            background: var(--soft);
            border-radius: 6px;
            height: 22px;
            overflow: hidden;
            position: relative;
        }

        .rank-fill {
            background: linear-gradient(90deg, var(--accent), var(--accent-dark));
            border-radius: 6px;
            bottom: 0;
            left: 0;
            position: absolute;
            top: 0;
        }

        .rank-value {
            font-size: 12.5px;
            font-weight: 800;
            text-align: right;
        }

        .rank-value .u {
            color: var(--muted);
            display: block;
            font-size: 10px;
            font-weight: 600;
        }

        /* ── diverging in/out bars (stock movement) ── */
        .div-legend {
            color: var(--muted);
            display: flex;
            font-size: 10.5px;
            font-weight: 700;
            gap: 14px;
            margin: 16px 0 12px;
            padding: 0 16px;
        }

        .div-legend span {
            align-items: center;
            display: inline-flex;
            gap: 5px;
        }

        .div-legend i {
            border-radius: 2px;
            display: inline-block;
            height: 8px;
            width: 8px;
        }

        .div-row {
            align-items: center;
            display: grid;
            gap: 12px;
            grid-template-columns: 140px 1fr 150px;
            margin-bottom: 16px;
            padding: 0 16px;
        }

        .div-row:last-child {
            margin-bottom: 16px;
        }

        .div-name {
            color: var(--text);
            font-size: 12.5px;
            font-weight: 700;
        }

        .div-track {
            height: 22px;
            position: relative;
        }

        .div-axis {
            background: var(--border);
            bottom: -3px;
            left: 50%;
            position: absolute;
            top: -3px;
            width: 1px;
        }

        .div-bar {
            border-radius: 4px;
            height: 16px;
            position: absolute;
            top: 3px;
        }

        .div-bar.in {
            background: var(--success);
            right: 50%;
        }

        .div-bar.out {
            background: var(--danger);
            left: 50%;
        }

        .div-remain {
            font-size: 12px;
            font-weight: 800;
            text-align: right;
        }

        .div-remain.is-critical {
            color: var(--danger);
        }

        .div-remain .u {
            color: var(--muted);
            display: block;
            font-size: 9.5px;
            font-weight: 700;
        }

        /* ── low-stock severity bars ── */
        .thresh-caption {
            color: var(--muted);
            font-size: 11px;
            margin: 16px 16px 4px;
        }

        .thresh-row {
            border-bottom: 1px solid var(--border);
            padding: 10px 16px;
        }

        .thresh-row:last-child {
            border-bottom: 0;
        }

        .thresh-head {
            align-items: baseline;
            display: flex;
            gap: 8px;
            justify-content: space-between;
            margin-bottom: 7px;
        }

        .thresh-name {
            color: var(--text);
            font-size: 12.5px;
            font-weight: 700;
        }

        .thresh-figs {
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .thresh-figs strong {
            color: var(--danger);
            font-weight: 800;
        }

        .thresh-track {
            background: var(--soft);
            border-radius: 4px;
            height: 8px;
            position: relative;
        }

        .thresh-fill {
            background: var(--danger);
            border-radius: 4px;
            bottom: 0;
            left: 0;
            position: absolute;
            top: 0;
        }

        @media (max-width: 1200px) {
            .metric-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 1100px) {
            .report-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .report-head {
                align-items: stretch;
                flex-direction: column;
            }

            .report-title {
                font-size: 22px;
            }

            .report-filter {
                flex-direction: column;
                width: 100%;
            }

            .day-nav-group,
            .day-actions {
                width: 100%;
            }

            .day-nav-group .date-input {
                flex: 1;
                max-width: none;
            }

            .day-actions {
                justify-content: stretch;
            }

            .day-actions .day-nav-today,
            .day-actions .report-btn {
                flex: 1;
            }

            .metric-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 480px) {
            .metric-grid {
                grid-template-columns: 1fr;
            }

            .metric-value {
                font-size: 20px;
            }

            .daily-table th,
            .daily-table td {
                padding: 9px 10px;
            }

            .report-card-head {
                padding: 12px;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $unitLabels = [
            'kg' => 'គីឡូក្រាម',
            'g' => 'ក្រាម',
            'L' => 'លីត្រ',
            'ml' => 'កំប៉ុង',
            'pcs' => 'បន្ទះ',
            'bag' => 'ដើម',
            'box1' => 'កេស',
            'box2' => 'ប្រអប់',
            'pack' => 'កញ្ចប់',
        ];

        $unitLabel = fn($unit) => $unitLabels[$unit] ?? $unit;

        $isToday = $reportDate->isSameDay(today());
        $prevDate = $reportDate->copy()->subDay()->toDateString();
        $nextDate = $reportDate->copy()->addDay()->toDateString();
        $todayDate = today()->toDateString();
    @endphp

    <div class="container-fluid py-4 daily-report">
        <div class="report-head">
            <div>
                <h2 class="report-title"><i class="fas fa-calendar-day"></i> របាយការណ៍ប្រចាំថ្ងៃ</h2>
                <p class="report-subtitle">សរុបចំនួនលក់ ចំណូល ចំណាយ និងស្តុក {{ $reportDate->format('d/m/Y') }}
                    @if($isToday)
                        <span class="badge rounded-pill text-bg-success ms-1">ថ្ងៃនេះ</span>
                    @endif
                </p>
            </div>

            <form method="GET" action="{{ route('reports.daily') }}" class="report-filter">
                <div class="day-nav-group">
                    <a href="{{ route('reports.daily', ['date' => $prevDate]) }}" class="day-nav-btn" title="ថ្ងៃមុន">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <input type="date" name="date" value="{{ $date }}" class="form-control date-input" max="{{ $todayDate }}">
                    @if($isToday)
                        <span class="day-nav-btn" style="opacity:.4;pointer-events:none;" title="ថ្ងៃបន្ទាប់">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @else
                        <a href="{{ route('reports.daily', ['date' => $nextDate]) }}" class="day-nav-btn" title="ថ្ងៃបន្ទាប់">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @endif
                </div>
                <div class="day-actions">
                    <a href="{{ route('reports.daily') }}" class="day-nav-today {{ $isToday ? 'is-active' : '' }}">ថ្ងៃនេះ</a>
                    <button type="submit" class="report-btn">
                        Apply
                    </button>
                </div>
            </form>
        </div>

        <div class="metric-grid">
            <div class="metric">
                <div class="metric-icon"><i class="fas fa-receipt"></i></div>
                <div>
                    <div class="metric-label">ចំនួនវិក្ក័យបត្រ</div>
                    <div class="metric-value">{{ number_format($totalOrders) }}</div>
                </div>
            </div>
            <div class="metric">
                <div class="metric-icon"><i class="fas fa-chart-line"></i></div>
                <div>
                    <div class="metric-label">លក់សរុប</div>
                    <div class="metric-value">៛{{ number_format($grossSalesKhr, 0) }}</div>
                    <div class="metric-value-usd">${{ number_format($grossSales, 2) }}</div>
                </div>
            </div>
            <div class="metric is-income">
                <div class="metric-icon"><i class="fas fa-hand-holding-dollar"></i></div>
                <div>
                    <div class="metric-label">ចំណូលបានទទួល</div>
                    <div class="metric-value">៛{{ number_format($incomeKhr, 0) }}</div>
                    <div class="metric-value-usd">${{ number_format($income, 2) }}</div>
                </div>
            </div>
            <div class="metric is-expense">
                <div class="metric-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div>
                    <div class="metric-label">ចំណាយ</div>
                    <div class="metric-value">៛{{ number_format($expensesKhr, 0) }}</div>
                    <div class="metric-value-usd">${{ number_format($expenses, 2) }}</div>
                </div>
            </div>
            <div class="metric is-income">
                <div class="metric-icon"><i class="fas fa-hand-holding-dollar"></i></div>
                <div>
                    <div class="metric-label">សងបុងចាស់</div>
                    <div class="metric-value">៛{{ number_format($oldDebtKhr, 0) }}</div>
                    <div class="metric-value-usd">${{ number_format($oldDebt, 2) }}</div>
                </div>
            </div>
            <div class="metric is-unpaid">
                <div class="metric-icon"><i class="fas fa-triangle-exclamation"></i></div>
                <div>
                    <div class="metric-label">នៅមិនទាន់ទូទាត់</div>
                    <div class="metric-value">៛{{ number_format($unpaidKhr, 0) }}</div>
                    <div class="metric-value-usd">${{ number_format($unpaid, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm method-breakdown mb-3">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small fw-bold mb-3">បែងចែកតាមវិធីបង់ប្រាក់</h6>
                <div class="row g-3">
                    @foreach($paymentMethodBreakdown as $method)
                        <div class="col-6 col-md-4 col-lg">
                            <div class="method-chip">
                                <div class="method-name">{{ $method['label'] }}</div>
                                <div class="method-khr">៛{{ number_format($method['khr'], 0) }}</div>
                                <div class="method-usd">${{ number_format($method['usd'], 2) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="report-grid">
            <div>
                <div class="report-card">
                    <div class="report-card-head">
                        <h3 class="report-card-title"><i class="fas fa-pizza-slice"></i> ទំនិញលក់បាន</h3>
                    </div>
                    @if($soldItems->count())
                        @php $maxSoldKhr = $soldItems->max('total_khr') ?: 1; @endphp
                        @foreach($soldItems as $item)
                            <div class="rank-row">
                                <div>
                                    <div class="rank-name">{{ $item->name }}</div>
                                    <div class="rank-qty">{{ number_format($item->quantity) }} {{ $unitLabel($item->unit) }}</div>
                                </div>
                                <div class="rank-track">
                                    <div class="rank-fill" style="width: {{ max(2, round($item->total_khr / $maxSoldKhr * 100, 1)) }}%"></div>
                                </div>
                                <div class="rank-value">
                                    ៛{{ number_format($item->total_khr, 0) }}
                                    <span class="u">${{ number_format($item->total, 2) }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-note"><i class="fas fa-box-open"></i>មិនមានទំនិញលក់សម្រាប់ថ្ងៃនេះទេ។</div>
                    @endif
                </div>

                <div class="report-card">
                    <div class="report-card-head">
                        <h3 class="report-card-title"><i class="fas fa-boxes-stacked"></i> ចលនាស្តុក</h3>
                    </div>
                    @if($stockMovement->count())
                        @php $maxMove = $stockMovement->max(fn($s) => max($s->stock_in, $s->stock_out)) ?: 1; @endphp
                        <div class="div-legend">
                            <span><i style="background:var(--success)"></i>ចូលក្នុងស្តុក</span>
                            <span><i style="background:var(--danger)"></i>កាត់ចេញពីស្តុក</span>
                        </div>
                        @foreach($stockMovement as $stock)
                            <div class="div-row">
                                <div class="div-name">{{ $stock->name ?? 'មិនមានឈ្មោះ' }}</div>
                                <div class="div-track">
                                    <div class="div-axis"></div>
                                    @if($stock->stock_in > 0)
                                        <div class="div-bar in" style="width: {{ max(3, round($stock->stock_in / $maxMove * 50, 1)) }}%"></div>
                                    @endif
                                    @if($stock->stock_out > 0)
                                        <div class="div-bar out" style="width: {{ max(3, round($stock->stock_out / $maxMove * 50, 1)) }}%"></div>
                                    @endif
                                </div>
                                <div class="div-remain {{ $stock->current_quantity < 0 ? 'is-critical' : '' }}">
                                    {{ number_format($stock->current_quantity) }} {{ $unitLabel($stock->unit) }}
                                    <span class="u">នៅសល់ក្នុងស្តុក</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-note"><i class="fas fa-boxes-stacked"></i>មិនមានចលនាស្តុកសម្រាប់ថ្ងៃនេះទេ។</div>
                    @endif
                </div>
            </div>

            <div>

                <div class="report-card">
                    <div class="report-card-head">
                        <h3 class="report-card-title"><i class="fas fa-file-invoice-dollar"></i> ចំណាយ</h3>
                        <span class="report-card-badge text-danger">៛{{ number_format($expensesKhr, 0) }}</span>
                    </div>
                    @if($purchases->count())
                        <div class="table-responsive">
                            <table class="table daily-table">
                                <tbody>
                                    @foreach($purchases as $purchase)
                                        <tr>
                                            <td>
                                                <div class="person-name">{{ $purchase->supplier_name ?? 'Expense' }}</div>
                                                <div class="text-muted small">{{ $purchase->reference_number }}</div>
                                            </td>
                                            <td class="text-end">
                                                <div class="money-stack">
                                                    <span class="khr">៛{{ number_format($purchase->total_amount * $exchangeRate, 0) }}</span>
                                                    <span class="usd">${{ number_format($purchase->total_amount, 2) }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-note"><i class="fas fa-file-invoice-dollar"></i>មិនមានចំណាយទេ។</div>
                    @endif
                </div>

                <div class="report-card">
                    <div class="report-card-head">
                        <h3 class="report-card-title"><i class="fas fa-triangle-exclamation"></i> ស្តុកជិតអស់</h3>
                        @if($lowStockCount)
                            <span class="report-card-badge text-danger">{{ $lowStockCount }}</span>
                        @endif
                    </div>
                    @if($lowStock->count())
                        <p class="thresh-caption">កម្រិត</p>
                        @php $maxLowAbs = $lowStock->max(fn($s) => abs($s->quantity)) ?: 1; @endphp
                        @foreach($lowStock as $stock)
                            <div class="thresh-row">
                                <div class="thresh-head">
                                    <div class="thresh-name">{{ $stock->product?->name ?? 'មិនមានឈ្មោះ' }}</div>
                                    <div class="thresh-figs">ស្តុក <strong>{{ number_format($stock->quantity) }}</strong> · កម្រិតបញ្ជាទិញ {{ number_format($stock->reorder_level) }}</div>
                                </div>
                                <div class="thresh-track">
                                    <div class="thresh-fill" style="width: {{ max(2, round(abs($stock->quantity) / $maxLowAbs * 100, 1)) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                        @if($lowStockCount > $lowStock->count())
                            <p style="text-align:center;margin:14px 0 0;">
                                <a href="{{ route('inventory.index') }}" style="color:var(--accent);font-size:12px;font-weight:700;text-decoration:none;">
                                    +{{ $lowStockCount - $lowStock->count() }} ផលិតផលទៀត — មើលទាំងអស់
                                </a>
                            </p>
                        @endif
                    @else
                        <div class="empty-note"><i class="fas fa-circle-check"></i>ស្តុកគ្រប់គ្រាន់។</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

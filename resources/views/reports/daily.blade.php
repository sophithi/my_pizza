@extends('layouts.app')

@section('title', 'របាយការណ៍ប្រចាំថ្ងៃ')

@push('styles')
    <style>
        .daily-report {
            --accent: #e85d24;
            --border: #e5e7eb;
            --muted: #64748b;
            --surface: #fff;
            --text: #0f172a;
        }

        .report-head {
            align-items: flex-start;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .report-title {
            color: var(--text);
            font-size: 30px;
            font-weight: 900;
            margin: 0;
        }

        .report-subtitle {
            color: var(--muted);
            margin: 6px 0 0;
        }

        .report-filter,
        .report-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, .06);
        }

        .report-filter {
            margin-bottom: 16px;
            padding: 14px;
        }

        .filter-inline {
            align-items: center;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .report-btn {
            align-items: center;
            background: linear-gradient(135deg, #e85d24, #d94a10);
            border: 0;
            border-radius: 8px;
            color: #fff;
            display: inline-flex;
            font-weight: 900;
            gap: 8px;
            min-height: 40px;
            padding: 9px 14px;
        }

        .metric-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 16px;
        }

        .metric {
            background: #fff;
            border: 1px solid var(--border);
            border-left: 4px solid var(--accent);
            border-radius: 8px;
            padding: 16px;
        }

        .metric-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .metric-value {
            color: var(--text);
            font-size: 28px;
            font-weight: 900;
            margin-top: 6px;
        }

        .report-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: 1.1fr .9fr;
        }

        .report-card {
            margin-bottom: 16px;
            overflow: hidden;
        }

        .report-card-head {
            align-items: center;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            padding: 14px 16px;
        }

        .report-card-title {
            color: var(--text);
            font-size: 16px;
            font-weight: 900;
            margin: 0;
        }

        .daily-table {
            margin: 0;
        }

        .daily-table th {
            background: #f8fafc;
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            padding: 12px 16px;
            text-transform: uppercase;
        }

        .daily-table td {
            padding: 12px 16px;
            vertical-align: middle;
        }

        .empty-note {
            color: var(--muted);
            padding: 22px 16px;
            text-align: center;
        }

        @media (max-width: 1100px) {
            .metric-grid,
            .report-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 720px) {
            .report-head,
            .filter-inline {
                align-items: stretch;
                flex-direction: column;
            }

            .metric-grid,
            .report-grid {
                grid-template-columns: 1fr;
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
            'ml' => 'មីលីលីត្រ',
            'pcs' => 'បន្ទះ',
            'box' => 'ប្រអប់',
            'box1' => 'ប្រអប់ 1',
            'box2' => 'ប្រអប់ 2',
            'pack' => 'កញ្ចប់',
            'bag' => 'ដើម',
        ];

        $unitLabel = fn($unit) => $unitLabels[$unit] ?? $unit;
    @endphp

    <div class="container-fluid py-4 daily-report">
        <div class="report-head">
            <div>
                <h2 class="report-title">របាយការណ៍ប្រចាំថ្ងៃ</h2>
                <p class="report-subtitle">សរុបលក់ ចំណូល ចំណាយ និងចលនាស្តុក សម្រាប់ {{ $reportDate->format('d/m/Y') }}</p>
            </div>
        </div>

        <form method="GET" action="{{ route('reports.daily') }}" class="report-filter">
            <div class="filter-inline">
                <input type="date" name="date" value="{{ $date }}" class="form-control" style="max-width: 220px;">
                <button type="submit" class="report-btn">
                    Apply
                </button>
            </div>
        </form>

        <div class="metric-grid">
            <div class="metric">
                <div class="metric-label">ចំនួនវិក្ក័យបត្រ</div>
                <div class="metric-value">{{ number_format($totalOrders) }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">លក់សរុប</div>
                <div class="metric-value">${{ number_format($grossSales, 2) }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">ចំណូលបានទទួល</div>
                <div class="metric-value text-success">${{ number_format($income, 2) }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">ចំណេញសុទ្ធ</div>
                <div class="metric-value {{ $netIncome < 0 ? 'text-danger' : 'text-primary' }}">${{ number_format($netIncome, 2) }}</div>
            </div>
        </div>

        <div class="report-grid">
            <div>
                <div class="report-card">
                    <div class="report-card-head">
                        <h3 class="report-card-title">ទំនិញលក់បាន</h3>
                        <strong>{{ number_format($soldItems->sum('quantity')) }}</strong>
                    </div>
                    @if($soldItems->count())
                        <div class="table-responsive">
                            <table class="table daily-table">
                                <thead>
                                    <tr>
                                        <th>ទំនិញ</th>
                                        <th class="text-center">ចំនួន</th>
                                        <th class="text-end">សរុប</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($soldItems as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td class="text-center">{{ number_format($item->quantity) }} {{ $unitLabel($item->unit) }}</td>
                                            <td class="text-end">${{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-note">មិនមានទំនិញលក់សម្រាប់ថ្ងៃនេះទេ។</div>
                    @endif
                </div>

                <div class="report-card">
                    <div class="report-card-head">
                        <h3 class="report-card-title">ចលនាស្តុក</h3>
                    </div>
                    @if($stockMovement->count())
                        <div class="table-responsive">
                            <table class="table daily-table">
                                <thead>
                                    <tr>
                                        <th>ទំនិញ</th>
                                        <th class="text-center">ចូល</th>
                                        <th class="text-center">ចេញ</th>
                                        <th class="text-center">នៅសល់</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockMovement as $stock)
                                        <tr>
                                            <td>{{ $stock->name ?? 'មិនមានឈ្មោះ' }}</td>
                                            <td class="text-center text-success">+{{ number_format($stock->stock_in) }}</td>
                                            <td class="text-center text-danger">-{{ number_format($stock->stock_out) }}</td>
                                            <td class="text-center">{{ number_format($stock->current_quantity) }} {{ $unitLabel($stock->unit) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-note">មិនមានចលនាស្តុកសម្រាប់ថ្ងៃនេះទេ។</div>
                    @endif
                </div>
            </div>

            <div>
                <div class="report-card">
                    <div class="report-card-head">
                        <h3 class="report-card-title">ចំណូលបានទទួល</h3>
                        <strong>${{ number_format($income, 2) }}</strong>
                    </div>
                    @if($payments->count())
                        <table class="table daily-table">
                            <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->customer_name ?: ($payment->order?->customer?->name ?? 'Customer') }}</td>
                                        <td class="text-end">${{ number_format($payment->paid_amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-note">មិនមានការទូទាត់ទេ។</div>
                    @endif
                </div>

                <div class="report-card">
                    <div class="report-card-head">
                        <h3 class="report-card-title">ចំណាយ</h3>
                        <strong class="text-danger">${{ number_format($expenses, 2) }}</strong>
                    </div>
                    @if($purchases->count())
                        <table class="table daily-table">
                            <tbody>
                                @foreach($purchases as $purchase)
                                    <tr>
                                        <td>
                                            <strong>{{ $purchase->supplier_name ?? 'Expense' }}</strong>
                                            <div class="text-muted small">{{ $purchase->reference_number }}</div>
                                        </td>
                                        <td class="text-end">${{ number_format($purchase->total_amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-note">មិនមានចំណាយទេ។</div>
                    @endif
                </div>

                <div class="report-card">
                    <div class="report-card-head">
                        <h3 class="report-card-title">ស្តុកជិតអស់</h3>
                    </div>
                    @if($lowStock->count())
                        <table class="table daily-table">
                            <tbody>
                                @foreach($lowStock as $stock)
                                    <tr>
                                        <td>{{ $stock->product?->name ?? 'មិនមានឈ្មោះ' }}</td>
                                        <td class="text-end">{{ number_format($stock->quantity) }} / {{ number_format($stock->reorder_level) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-note">ស្តុកគ្រប់គ្រាន់។</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

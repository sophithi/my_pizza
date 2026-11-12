@extends('layouts.app')

@section('title', 'របាយការណ៍ស្តុក')

@push('styles')
    <style>
#inventory-report {

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

#inventory-report .report-head {

            align-items: flex-end;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 16px;

}

#inventory-report .report-title {

            align-items: center;
            color: var(--text);
            display: flex;
            font-size: 28px;
            font-weight: 900;
            gap: 10px;
            margin: 0;

}

#inventory-report .report-title i {

            color: var(--accent);

}

#inventory-report .report-subtitle {

            color: var(--muted);
            margin: 6px 0 0;

}

#inventory-report .report-filter,
#inventory-report .metric,
#inventory-report .panel,
#inventory-report .alert-soft {

            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, .06);

}

#inventory-report .report-filter {

            margin-bottom: 16px;
            padding: 14px;

}

#inventory-report .filter-row {

            align-items: center;
            display: grid;
            gap: 10px;
            grid-template-columns: minmax(180px, 240px) 1fr;

}

#inventory-report .report-btn,
#inventory-report .back-btn {

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

#inventory-report .report-btn {

            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;

}

#inventory-report .report-btn:hover {

            color: #fff;
            transform: translateY(-1px);

}

#inventory-report .back-btn {

            background: var(--surface);
            border: 1.5px solid var(--border);
            color: var(--muted);
            transition: background .15s ease, border-color .15s ease, color .15s ease;

}

#inventory-report .back-btn:hover {

            background: var(--accent-soft);
            border-color: rgba(232, 93, 36, .4);
            color: var(--accent-dark);

}

#inventory-report .metric-grid {

            display: grid;
            gap: 14px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 16px;

}

#inventory-report .metric {

            align-items: center;
            display: flex;
            gap: 13px;
            padding: 16px;
            transition: box-shadow .15s ease, transform .15s ease;

}

#inventory-report .metric:hover {

            box-shadow: 0 14px 32px rgba(15, 23, 42, .09);
            transform: translateY(-2px);

}

#inventory-report .metric-icon {

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

#inventory-report .metric.is-warning .metric-icon {

            background: var(--warning-soft);
            color: var(--warning);

}

#inventory-report .metric.is-danger .metric-icon {

            background: var(--danger-soft);
            color: var(--danger);

}

#inventory-report .metric-label {

            color: var(--muted);
            font-size: 12.5px;
            font-weight: 900;
            margin: 0;
            text-transform: uppercase;

}

#inventory-report .metric-value {

            color: var(--text);
            font-size: 22px;
            font-weight: 900;
            line-height: 1.2;
            margin-top: 4px;

}

#inventory-report .metric-value-usd {

            color: var(--muted);
            font-size: 12.5px;
            font-weight: 700;
            margin-top: 1px;

}

#inventory-report .panel {

            margin-bottom: 16px;
            overflow: hidden;

}

#inventory-report .panel-head {

            align-items: center;
            border-bottom: 1px solid var(--border);
            display: flex;
            gap: 9px;
            padding: 14px 16px;

}

#inventory-report .panel-head i {

            color: var(--accent);
            font-size: 14px;
            width: 16px;

}

#inventory-report .panel-title {

            color: var(--text);
            font-size: 16px;
            font-weight: 900;
            margin: 0;

}

#inventory-report .report-table {

            margin: 0;

}

#inventory-report .report-table th {

            background: var(--soft);
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .02em;
            padding: 11px 12px;
            text-transform: uppercase;

}

#inventory-report .report-table td {

            padding: 11px 12px;
            vertical-align: middle;

}

#inventory-report .report-table tbody tr:hover {

            background: var(--soft);

}

#inventory-report .status-pill {

            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 900;
            padding: 4px 10px;

}

#inventory-report .status-ok {

            background: var(--success-soft);
            color: #166534;

}

#inventory-report .status-low {

            background: var(--warning-soft);
            color: #92400e;

}

#inventory-report .status-out {

            background: var(--danger-soft);
            color: #991b1b;

}

#inventory-report .pill {

            border-radius: 999px;
            display: inline-flex;
            font-size: 12.5px;
            font-weight: 800;
            padding: 3px 10px;
            white-space: nowrap;

}

#inventory-report .pill-in {

            background: var(--success-soft);
            color: #166534;

}

#inventory-report .pill-out {

            background: var(--danger-soft);
            color: #991b1b;

}

#inventory-report .alert-soft {

            align-items: center;
            background: #fff7ed;
            border-color: #fed7aa;
            color: #9a3412;
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
            padding: 14px 16px;

}

#inventory-report .alert-soft i {

            font-size: 16px;

}

#inventory-report .pager-wrap {

            display: flex;
            justify-content: center;
            padding: 0 16px 16px;

}

#inventory-report .pager-wrap .pagination {

            flex-wrap: wrap;
            column-gap: 4px;
            margin-bottom: 0;
            row-gap: 6px;

}

#inventory-report .pager-wrap .page-link {

            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--accent);
            font-size: 13px;
            padding: 6px 12px;

}

#inventory-report .pager-wrap .page-link:hover,
#inventory-report .pager-wrap .page-link:focus {

            background: var(--accent-soft);
            border-color: var(--accent);
            box-shadow: none;
            color: var(--accent-dark);

}

#inventory-report .pager-wrap .page-item.active .page-link {

            background: var(--accent);
            border-color: var(--accent);
            color: #fff;

}

#inventory-report .pager-wrap .page-item.disabled .page-link {

            background: var(--soft);
            border-color: var(--border);
            color: #cbd5e1;

}

#inventory-report .empty-note {

            color: var(--muted);
            padding: 22px 16px;
            text-align: center;

}

@media (max-width:1100px) {
#inventory-report .metric-grid {

                grid-template-columns: 1fr 1fr;

}
}

@media (max-width:760px) {
#inventory-report .report-head {

                align-items: stretch;
                flex-direction: column;

}

#inventory-report .filter-row,
#inventory-report .metric-grid {

                grid-template-columns: 1fr;

}
}

#inventory-report .inventory-filters {

            display: flex;
            flex-wrap: wrap;
            align-items: end;
            gap: 10px;

}

#inventory-report .inventory-filters label {

            display: block;
            font-size: 12px;
            margin-bottom: 5px;
            color: #64748b;

}

#inventory-report .inventory-filters select,
#inventory-report .inventory-filters input {

            min-height: 38px;
            border: 1px solid #e5e7eb;
            border-radius: 7px;
            padding: 6px 10px;
            max-width: 240px;

}

#inventory-report .inventory-report-table th,
#inventory-report .inventory-report-table td {

            white-space: nowrap;
            text-align: right;
            font-variant-numeric: tabular-nums;

}

#inventory-report .inventory-report-table th:first-child,
#inventory-report .inventory-report-table td:first-child {

            text-align: left;

}
@media print {
            body * {
                visibility: hidden;
            }

            #inventory-report,
            #inventory-report * {
                visibility: visible;
            }

            #inventory-report {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 0 !important;
            }

            #inventory-report .no-print {
                display: none !important;
            }

            #inventory-report .table-responsive {
                overflow: visible;
            }

            #inventory-report .panel,
            #inventory-report .metric {
                box-shadow: none;
            }

            #inventory-report th,
            #inventory-report td {
                font-size: 10px;
                padding: 6px;
            }

            @page {
                size: A4 landscape;
                margin: 10mm;
            }
        }

</style>
@endpush

@section('content')

    <div class="container-fluid py-4 report-page" id="inventory-report">
        <div class="report-head">
            <div>
                <h2 class="report-title">របាយការណ៍ស្តុក</h2>
                <p class="report-subtitle">{{ $startDate ?? 'ចាប់ពីដើម' }} → {{ $endDate ?? 'បច្ចុប្បន្ន' }} ·
                    តាមថ្ងៃចលនាស្តុក</p>
            </div>
            <div class="d-flex gap-2 no-print">
                <a class="back-btn"
                    href="{{ route('reports.inventory', array_merge(request()->except(['export', 'page']), ['export' => 'excel'])) }}">Excel</a>
                <button type="button" class="back-btn" onclick="window.print()">បោះពុម្ព / PDF</button>
            </div>
        </div>
        @if($errors->any())
            <div class="alert alert-danger no-print">{{ $errors->first() }}</div>
        @endif
        <form class="report-filter inventory-filters no-print" method="GET" action="{{ route('reports.inventory') }}">
            <div>
                <label for="reportPeriod">រយៈពេល</label>
                <select id="reportPeriod" name="period"
                    onchange="document.querySelectorAll('.custom-date').forEach(el => el.hidden = this.value !== 'custom')">
                    @foreach(['all' => 'គ្រប់ពេល', 'today' => 'ថ្ងៃនេះ', 'yesterday' => 'ម្សិលមិញ', 'week' => 'សប្ដាហ៍នេះ', 'month' => 'ខែនេះ', 'year' => 'ឆ្នាំនេះ', 'custom' => 'ជ្រើសថ្ងៃ'] as $value => $label)
                        <option value="{{ $value }}" @selected($period === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="custom-date" @if($period !== 'custom') hidden @endif>
                <label for="startDate">ពីថ្ងៃ</label><input id="startDate" type="date" name="start_date"
                    value="{{ request('start_date', $startDate) }}">
            </div>
            <div class="custom-date" @if($period !== 'custom') hidden @endif>
                <label for="endDate">ដល់ថ្ងៃ</label><input id="endDate" type="date" name="end_date"
                    value="{{ request('end_date', $endDate) }}">
            </div>
            <div>
                <label for="reportProduct">ទំនិញ</label>
                <select id="reportProduct" name="product_id">
                    <option value="">ទំនិញទាំងអស់</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" @selected((string) request('product_id') === (string) $product->id)>
                            {{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="reportCategory">ប្រភេទ</label>
                <select id="reportCategory" name="category">
                    <option value="">ប្រភេទទាំងអស់</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <button class="report-btn" type="submit">បង្ហាញ</button>
            <a class="back-btn" href="{{ route('reports.inventory') }}">សម្អាត</a>
        </form>

        <div class="panel">
            <div class="panel-head">
                <h3 class="panel-title">សមតុល្យតាមទំនិញ</h3><span class="text-muted small">{{ $rows->count() }} មុខ</span>
            </div>
            <div class="table-responsive">
                <table class="table report-table inventory-report-table">
                    <thead>
                        <tr>
                            <th>ទំនិញ</th>
                            <th>ស្តុកដើម</th>
                            <th>បញ្ចូលថ្មី</th>
                            <th>លក់ក្នុង Order</th>
                            <th>ថែមក្នុង Order</th>
                            <th>បញ្ចូលវិញ</th>
                            <th>ចេញពីស្តុក</th>
                            <th title="កែ Order រាប់ស្តុក ខូច ឬបាត់">កែតម្រូវ </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr>
                                <td><a class="fw-bold"
                                        href="{{ route('inventory.show', ['inventory' => $row->id, 'period' => $period, 'from_date' => $startDate, 'to_date' => $endDate]) }}">{{ $row->name }}</a><small
                                        class="d-block text-muted">{{ $row->unit }}</small></td>
                                <td>{{ number_format($row->opening) }}</td>
                                <td class="text-success">{{ number_format($row->received) }}</td>
                                <td>{{ number_format($row->order_sold) }}</td>
                                <td class="text-primary">{{ number_format($row->order_free) }}</td>
                                <td class="text-success">{{ number_format($row->returned) }}</td>
                                <td>{{ number_format($row->stock_out) }}</td>
                                <td>{{ $row->adjustment > 0 ? '+' : '' }}{{ number_format($row->adjustment) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="empty-note">មិនមានទិន្នន័យ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

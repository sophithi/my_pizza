<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <title>របាយការណ៍ការលក់ — Pizza Happy Family</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 16mm 14mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, "Noto Sans Khmer", sans-serif;
            font-size: 12px;
            color: #1f2937;
            line-height: 1.5;
        }

        .header {
            align-items: flex-end;
            border-bottom: 1.5px solid #1f2937;
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 12px;
        }

        .brand {
            color: #D85A30;
            font-size: 18px;
            font-weight: 800;
        }

        .brand-sub {
            color: #6b7280;
            font-size: 10.5px;
            margin-top: 2px;
        }

        .report-meta {
            color: #6b7280;
            font-size: 10.5px;
            line-height: 1.6;
            text-align: right;
        }

        .report-meta .report-title {
            color: #1f2937;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .summary-bar {
            border-bottom: 1px solid #d1d5db;
            display: flex;
            margin-bottom: 20px;
            padding-bottom: 12px;
        }

        .summary-item {
            border-left: 1px solid #d1d5db;
            flex: 1;
            padding: 0 16px;
        }

        .summary-item:first-child {
            border-left: none;
            padding-left: 0;
        }

        .summary-label {
            color: #6b7280;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
        }

        .summary-value {
            color: #1f2937;
            font-size: 17px;
            font-weight: 800;
            margin-top: 4px;
        }

        .summary-value-usd {
            color: #6b7280;
            font-size: 11px;
            font-weight: 600;
            margin-top: 2px;
        }

        table {
            border: 1px solid #d1d5db;
            border-collapse: collapse;
            width: 100%;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        tr {
            page-break-inside: avoid;
        }

        th {
            background: #f3f4f6;
            border-bottom: 1.5px solid #1f2937;
            border-right: 1px solid #e5e7eb;
            color: #374151;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .4px;
            padding: 9px 10px;
            text-align: center;
            text-transform: uppercase;
        }

        th:last-child {
            border-right: none;
        }

        td {
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #f0f1f3;
            padding: 10px;
            vertical-align: middle;
        }

        td:last-child {
            border-right: none;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            font-variant-numeric: tabular-nums;
            text-align: right;
        }

        .text-price {
            font-variant-numeric: tabular-nums;
            text-align: center;
        }

        .cell-invoice {
            font-weight: 700;
        }

        .cell-customer {
            font-weight: 600;
        }

        tfoot td {
            background: #f3f4f6;
            border-bottom: none;
            border-top: 1.5px solid #1f2937;
            font-weight: 800;
            padding-top: 11px;
        }

        .footer {
            border-top: 1px solid #e5e7eb;
            color: #9ca3af;
            font-size: 9px;
            margin-top: 24px;
            padding-top: 12px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <div>
            <div class="brand">ភីហ្សា គ្រួសាររីករាយ</div>
            <div class="brand-sub">Pizza Happy Family</div>
        </div>
        <div class="report-meta">
            <div class="report-title">របាយការណ៍ការលក់</div>
            <div>រយៈពេល: {{ $periodLabel }}</div>
            <div>{{ now()->format('d M Y H:i') }}</div>
        </div>
    </div>

    @php
        $rows = $orders->map(fn($order) => (object) [
            'order' => $order,
            'khr' => $order->totalKhr(),
            'usd' => (float) $order->total_amount,
        ]);
        $orderCount = $rows->count();
        $totalKhr = $rows->sum('khr');
        $totalUsd = $rows->sum('usd');
        $avgKhr = $orderCount ? $totalKhr / $orderCount : 0;
        $avgUsd = $orderCount ? $totalUsd / $orderCount : 0;
    @endphp

    <table>
        <thead>
            <tr>
                <th width="7%">ល.រ</th>
                <th width="14%">លេខវិក្ក័យបត្រ</th>
                <th width="30%">អតិថិជន</th>
                <th width="17%">កាលបរិច្ឆេទ</th>
                <th width="16%">តម្លៃ (៛)</th>
                <th width="16%">តម្លៃ ($)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center cell-invoice">
                        {{ $row->order->invoice?->invoice_number ?? ('#' . $row->order->id) }}</td>
                    <td class="cell-customer">{{ $row->order->customer?->name ?? '—' }}</td>
                    <td class="text-center">{{ optional($row->order->order_date)->format('d/m/Y') }}</td>
                    <td class="text-price">៛{{ number_format($row->khr, 0) }}</td>
                    <td class="text-price">${{ number_format($row->usd, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #999;">មិនមានទិន្នន័យ</td>
                </tr>
            @endforelse
        </tbody>
        @if($orderCount)
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right">សរុប</td>
                    <td class="text-price">៛{{ number_format($totalKhr, 0) }}</td>
                    <td class="text-price">${{ number_format($totalUsd, 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">
        សរុបចំនួនកម្មង់: {{ $orderCount }} | Pizza Happy Family
    </div>
</body>

</html>
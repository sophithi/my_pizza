<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <title>របាយការណ៍ការលក់ — Pizza Happy Family</title>
    <style>
        @page { size: A4 portrait; margin: 16mm 14mm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, "Noto Sans Khmer", sans-serif; font-size: 11px; color: #111; padding: 28px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .brand { font-size: 18px; font-weight: bold; color: #D85A30; }
        .brand-sub { font-size: 12px; color: #666; margin-top: 2px; }
        .report-meta { text-align: right; font-size: 11px; color: #666; }
        .summary-bar { display: flex; gap: 10px; margin-bottom: 20px; }
        .summary-item { background: #fff8f5; border: 1px solid #fcd5c5; border-radius: 6px; flex: 1; padding: 10px 14px; }
        .summary-label { color: #a35a3a; font-size: 9px; font-weight: 700; letter-spacing: .3px; text-transform: uppercase; }
        .summary-value { color: #111; font-size: 15px; font-weight: bold; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f3f4f6; }
        th { text-align: left; padding: 8px 10px; font-size: 9px; text-transform: uppercase; letter-spacing: .4px; color: #555; font-weight: 600; border-bottom: 2px solid #D85A30; }
        td { padding: 9px 10px; border-bottom: 1px solid #e5e7eb; }
        tbody tr:nth-child(odd) { background: #fafafa; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="brand">ភីហ្សា គ្រួសាររីករាយ</div>
            <div class="brand-sub">Pizza Happy Family</div>
        </div>
        <div class="report-meta">
            <div>របាយការណ៍ការលក់</div>
            <div style="margin-top: 4px;">រយៈពេល: {{ $periodLabel }}</div>
            <div style="margin-top: 4px;">{{ now()->format('d M Y H:i') }}</div>
        </div>
    </div>

    @php
        $totalKhr = 0;
        $totalUsd = 0;
    @endphp

    <div class="summary-bar">
        <div class="summary-item">
            <div class="summary-label">ចំនួនកម្មង់</div>
            <div class="summary-value">{{ number_format($orders->count()) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">លក់សរុប (៛)</div>
            <div class="summary-value">៛{{ number_format($orders->sum(fn($o) => $o->totalKhr()), 0) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">លក់សរុប ($)</div>
            <div class="summary-value">${{ number_format($orders->sum('total_amount'), 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="7%">ល.រ</th>
                <th width="14%">លេខកម្មង់</th>
                <th width="30%">អតិថិជន</th>
                <th width="17%">កាលបរិច្ឆេទ</th>
                <th width="16%">តម្លៃ (៛)</th>
                <th width="16%">តម្លៃ ($)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                @php
                    $khr = $order->totalKhr();
                    $usd = (float) $order->total_amount;
                    $totalKhr += $khr;
                    $totalUsd += $usd;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->customer?->name ?? '—' }}</td>
                    <td class="text-center">{{ optional($order->order_date)->format('d/m/Y') }}</td>
                    <td class="text-right">៛{{ number_format($khr, 0) }}</td>
                    <td class="text-right">${{ number_format($usd, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align: center; color: #999;">មិនមានទិន្នន័យ</td></tr>
            @endforelse
        </tbody>
        @if($orders->count())
            <tfoot>
                <tr style="background: #f3f4f6; font-weight: bold;">
                    <td colspan="4" class="text-right">សរុប</td>
                    <td class="text-right">៛{{ number_format($totalKhr, 0) }}</td>
                    <td class="text-right">${{ number_format($totalUsd, 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">
        សរុបចំនួនកម្មង់: {{ $orders->count() }} |  Pizza Happy Family
    </div>
</body>
</html>

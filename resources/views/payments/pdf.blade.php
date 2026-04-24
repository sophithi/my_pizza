<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <title>របាយការណ៍ការទូទាត់ — Pizza Happy Family</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, "Noto Sans Khmer", sans-serif; font-size: 12px; color: #111; padding: 28px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .brand { font-size: 18px; font-weight: bold; color: #D85A30; }
        .brand-sub { font-size: 12px; color: #666; margin-top: 2px; }
        .report-meta { text-align: right; font-size: 11px; color: #666; }
        .summary { display: flex; gap: 0; margin-bottom: 20px; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden; }
        .sm { flex: 1; padding: 12px 16px; border-right: 1px solid #e5e7eb; }
        .sm:last-child { border-right: none; }
        .sm .sl { font-size: 10px; color: #666; text-transform: uppercase; margin-bottom: 4px; }
        .sm .sv { font-size: 16px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f3f4f6; }
        th { text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; letter-spacing: .4px; color: #555; border-bottom: 1px solid #e5e7eb; }
        td { padding: 9px 10px; border-bottom: 1px solid #f0f0f0; font-size: 12px; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: bold; }
        .badge-paid    { background: #d1fae5; color: #065f46; }
        .badge-partial { background: #fef3c7; color: #92400e; }
        .badge-pending { background: #fee2e2; color: #991b1b; }
        .oid { font-size: 10px; color: #999; }
        .footer { margin-top: 24px; font-size: 10px; color: #aaa; text-align: center; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="header">
    <div>
        <div class="brand">🍕 Pizza Happy Family</div>
        <div class="brand-sub">របាយការណ៍ចំណូលពីការទូទាត់</div>
    </div>
    <div class="report-meta">
        បង្កើតនៅ៖ {{ now()->format('d M Y, H:i') }}<br>
        រយៈពេល៖ {{ $periodLabel }}<br>
        @if($statusLabel) ស្ថានភាព៖ {{ $statusLabel }} @endif
    </div>
</div>

<div class="summary">
    <div class="sm">
        <div class="sl">បានប្រមូលសរុប</div>
        <div class="sv" style="color:#059669">${{ number_format($stats['collected'], 2) }}</div>
    </div>
    <div class="sm">
        <div class="sl">ចំនួននៅសល់</div>
        <div class="sv" style="color:#dc2626">${{ number_format($stats['outstanding'], 2) }}</div>
    </div>
    <div class="sm">
        <div class="sl">ការបញ្ជាទិញ</div>
        <div class="sv">{{ $stats['total'] }}</div>
    </div>
    <div class="sm">
        <div class="sl">បានបង់គ្រប់</div>
        <div class="sv" style="color:#059669">{{ $stats['paid'] }}</div>
    </div>
    <div class="sm">
        <div class="sl">បង់ខ្លះ</div>
        <div class="sv" style="color:#d97706">{{ $stats['partial'] }}</div>
    </div>
    <div class="sm">
        <div class="sl">មិនទាន់បង់</div>
        <div class="sv" style="color:#dc2626">{{ $stats['unpaid'] }}</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>អតិថិជន</th>
            <th>កាលបរិច្ឆេទ</th>
            <th>សរុបការបញ្ជាទិញ</th>
            <th>បានបង់</th>
            <th>នៅសល់</th>
            <th>វិធីបង់</th>
            <th>ស្ថានភាព</th>
            <th>កំណត់ចំណាំ</th>
        </tr>
    </thead>
    <tbody>
        @forelse($payments as $payment)
        <tr>
            <td>
                {{ $payment->customer_name }}
                <div class="oid">{{ $payment->order_id }}</div>
            </td>
            <td>{{ \Carbon\Carbon::parse($payment->order_date)->format('d M Y') }}</td>
            <td>${{ number_format($payment->total_amount, 2) }}</td>
            <td style="color:#059669;font-weight:bold">${{ number_format($payment->paid_amount, 2) }}</td>
            <td style="color:#dc2626">
                @if($payment->balance > 0)
                    ${{ number_format($payment->balance, 2) }}
                @else
                    —
                @endif
            </td>
            <td>{{ $payment->method }}</td>
            <td>
                <span class="badge badge-{{ $payment->status }}">
                    {{ $payment->status === 'pending' ? 'មិនទាន់បង់' : ($payment->status === 'partial' ? 'បង់ខ្លះ' : 'បានបង់') }}
                </span>
            </td>
            <td style="color:#666;font-size:11px">{{ $payment->notes ?: '—' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align:center;padding:20px;color:#999">មិនមានទិន្នន័យការទូទាត់ទេ។</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    Pizza Happy Family &bull; របាយការណ៍ការទូទាត់ &bull; {{ now()->format('Y') }}
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <title>របាយការណ៍ស្តុកទំនិញ — Pizza Happy Family</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, "Noto Sans Khmer", sans-serif; font-size: 11px; color: #111; padding: 28px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .brand { font-size: 18px; font-weight: bold; color: #D85A30; }
        .brand-sub { font-size: 12px; color: #666; margin-top: 2px; }
        .report-meta { text-align: right; font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f3f4f6; }
        th { text-align: left; padding: 8px 10px; font-size: 9px; text-transform: uppercase; letter-spacing: .4px; color: #555; font-weight: 600; border-bottom: 2px solid #D85A30; }
        td { padding: 9px 10px; border-bottom: 1px solid #e5e7eb; }
        tbody tr:nth-child(odd) { background: #fafafa; }
        tbody tr:hover { background: #f0f0f0; }
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
            <div>របាយការណ៍ស្តុកទំនិញ</div>
            <div style="margin-top: 4px;">{{ now()->format('d M Y H:i') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="6%">ល.រ</th>
                <th width="20%">ទំនិញ</th>
                <th width="14%">ប្រភេទ</th>
                <th width="14%">ទីតាំង</th>
                <th width="10%">ចំនួន</th>
                <th width="12%">ម៉ូលឹង</th>
                <th width="12%">ស្ថានភាព</th>
                <th width="12%">កាលបរិច្ឆេទ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventories as $index => $inv)
                @php
                    $status = $inv->quantity <= 0 ? 'អស់' : ($inv->quantity <= $inv->reorder_level ? 'ជិត' : 'មាន');
                    $statusColor = $inv->quantity <= 0 ? '#dc2626' : ($inv->quantity <= $inv->reorder_level ? '#ea8c05' : '#059669');
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $inv->product?->name ?? '—' }}</td>
                    <td>{{ $inv->product?->category ?? '—' }}</td>
                    <td>{{ $inv->warehouse_location ?? '—' }}</td>
                    <td class="text-center"><strong>{{ number_format($inv->quantity) }}</strong></td>
                    <td class="text-center">{{ number_format($inv->reorder_level) }}</td>
                    <td class="text-center" style="color: {{ $statusColor }}; font-weight: 600;">{{ $status }}</td>
                    <td class="text-center">{{ $inv->updated_at?->format('d/m/Y') ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align: center; color: #999;">មិនមានទិន្នន័យ</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        សរុបចំនួនលេខ: {{ $inventories->count() }} | គ្រូបុគ្គលិក Pizza Happy Family
    </div>
</body>
</html>

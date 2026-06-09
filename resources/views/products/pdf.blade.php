<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <title>របាយការណ៍ទំនិញ — Pizza Happy Family</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, "Noto Sans Khmer", sans-serif;
            font-size: 11px;
            color: #111;
            padding: 28px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .brand {
            font-size: 18px;
            font-weight: bold;
            color: #D85A30;
        }

        .brand-sub {
            font-size: 12px;
            color: #666;
            margin-top: 2px;
        }

        .report-meta {
            text-align: right;
            font-size: 11px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: #f3f4f6;
        }

        th {
            text-align: left;
            padding: 8px 10px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #555;
            font-weight: 600;
            border-bottom: 2px solid #D85A30;
        }

        td {
            padding: 9px 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        tbody tr:nth-child(odd) {
            background: #fafafa;
        }

        tbody tr:hover {
            background: #f0f0f0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #999;
        }

        @media print {
            body {
                padding: 0;
            }

            @page {
                margin: 20mm;
            }
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
            <div>របាយការណ៍ទំនិញ</div>
            <div style="margin-top: 4px;">{{ now()->format('d M Y H:i') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="6%">ល.រ</th>
                <th width="10%">កូដ</th>
                <th width="20%">ឈ្មោះទំនិញ</th>
                <th width="12%">ប្រភេទ</th>
                <th width="10%">ខ្នាត</th>
                <th width="12%">តម្លៃ USD</th>
                <th width="12%">តម្លៃ KHR</th>
                <th width="8%">ស្តុក</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $product)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $product->sku }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category ?? '—' }}</td>
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
                    @endphp
                    <td class="text-center">
                        {{ $unitLabels[$product->unit] ?? $product->unit }}
                    </td>
                    <td class="text-right">${{ number_format($product->price_usd ?? 0, 2) }}</td>
                    <td class="text-right">៛{{ number_format($product->price_khr ?? 0, 0) }}</td>
                    <td class="text-center">{{ $product->inventory?->quantity ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #999;">មិនមានទិន្នន័យ</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        សរុបចំនួនទំនិញ: {{ $products->count() }} | Pizza Happy Family
    </div>
</body>

</html>

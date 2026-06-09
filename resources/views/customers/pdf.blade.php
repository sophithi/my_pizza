<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <title>របាយការណ៍អតិថិជន — Pizza Happy Family</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, "Noto Sans Khmer", sans-serif;
            font-size: 12px;
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
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #555;
            font-weight: 600;
            border-bottom: 2px solid #D85A30;
        }

        td {
            padding: 10px;
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

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }

        .badge-facebook {
            background: #e7f3ff;
            color: #0a66c2;
        }

        .badge-telegram {
            background: #e0f7ff;
            color: #0088cc;
        }

        .badge-active {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
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
            <div>របាយការណ៍អតិថិជន</div>
            <div style="margin-top: 4px;">{{ now()->format('d M Y H:i') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="8%">ល.រ</th>
                <th width="18%">ឈ្មោះ</th>
                <th width="12%">ប្រភព</th>
                <th width="14%">ទូរស័ព្ទ</th>
                <th width="16%">ឰង</th>
                <th width="10%">បញ្ជាទិញ</th>
                <th width="14%">ចំណាយ</th>
                <th width="12%">ស្ថានភាព</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $index => $customer)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>
                        @if($customer->type === 'facebook')
                            <span class="badge badge-facebook">Facebook</span>
                        @elseif($customer->type === 'telegram')
                            <span class="badge badge-telegram">Telegram</span>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $customer->phone ?? '—' }}</td>
                    <td>{{ $customer->city ?? $customer->address ?? '—' }}</td>
                    <td class="text-center">{{ $customer->orders_count ?? 0 }}</td>
                    <td class="text-right">${{ number_format($customer->total_spent ?? 0, 2) }}</td>
                    <td>
                        @if($customer->status === 'active')
                            <span class="badge badge-active">សកម្ម</span>
                        @else
                            <span class="badge badge-inactive">អសកម្ម</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #999;">មិនមានទិន្នន័យ</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        សរុបចំនួនអតិថិជន: {{ $customers->count() }} | គ្រូបុគ្គលិក Pizza Happy Family
    </div>
</body>

</html>

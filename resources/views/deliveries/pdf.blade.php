<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <title>ការដឹកជញ្ជូន — {{ $delivery->delivery_name }} — Pizza Happy Family</title>
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

        .summary {
            display: flex;
            gap: 0;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
        }

        .sm {
            flex: 1;
            padding: 12px 16px;
            border-right: 1px solid #e5e7eb;
        }

        .sm:last-child {
            border-right: none;
        }

        .sm .sl {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .sm .sv {
            font-size: 16px;
            font-weight: bold;
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
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 9px 10px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 12px;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .badge-inv {
            background: #eff6ff;
            color: #1d4ed8;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            padding: 2px 6px;
        }

        .footer {
            margin-top: 24px;
            font-size: 10px;
            color: #aaa;
            text-align: center;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    @php
        $orders = $delivery->orders;
        $orderCount = $orders->count();
        $totalFee = $orders->sum('delivery_fee_khr');
        $totalAmount = $orders->sum(fn($o) => $o->invoice?->total_amount ?? $o->total_amount ?? 0);
        $totalBoxes = $orders->sum(fn($o) => (int) ($o->small_pack_qty ?? $o->box_qty ?? 0) + (int) ($o->big_pack_qty ?? 0));
    @endphp

    <div class="header">
        <div>
            <div class="brand"> ភីហ្សា គ្រួសាររីករាយ | Pizza Happy Family </div>
            <div class="brand-sub">ការដឹកជញ្ជូន — {{ $delivery->delivery_name }}</div>
        </div>
        <div class="report-meta">
            បានធ្វើនៅ៖ {{ now()->format('d M Y, H:i') }}<br>
            @if($startDate && $endDate)
                កាលបរិច្ឆេទ៖ {{ \Illuminate\Support\Carbon::parse($startDate)->format('d M Y') }}
                @if($startDate !== $endDate)
                    - {{ \Illuminate\Support\Carbon::parse($endDate)->format('d M Y') }}
                @endif
            @endif
        </div>
    </div>

    <div class="summary">
        <div class="sm">
            <div class="sl">ចំនួនវិក្ក័យបត្រ</div>
            <div class="sv">{{ $orderCount }}</div>
        </div>
        <div class="sm">
            <div class="sl">សរុបកេស</div>
            <div class="sv">{{ number_format($totalBoxes, 0) }}</div>
        </div>
        <div class="sm">
            <div class="sl">សរុបថ្លៃដឹក</div>
            <div class="sv" style="color:#D85A30">៛{{ number_format($totalFee, 0) }}</div>
        </div>
        <div class="sm">
            <div class="sl">សរុបវិក្ក័យបត្រ</div>
            <div class="sv" style="color:#059669">${{ number_format($totalAmount, 0) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>លេខរៀង</th>
                <th>លេខវិក្ក័យបត្រ</th>
                <th>ឈ្មោះអតិថិជន</th>
                <th>លេខទំនាក់ទំនង</th>
                <th style="text-align:center">កេសតូច</th>
                <th style="text-align:center">កេសធំ</th>
                <th style="text-align:right">ថ្លៃដឹក</th>
                <th style="text-align:right">តម្លៃសរុប</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                @php
                    $invoice = $order->invoice;
                    $customer = $order->customer;
                    $smallQty = (int) ($order->small_pack_qty ?? $order->box_qty ?? 1);
                    $bigQty = (int) ($order->big_pack_qty ?? 0);
                    $totalPrice = $invoice?->total_amount ?? $order->total_amount ?? 0;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><span class="badge-inv">{{ $invoice?->invoice_number ?? 'N/A' }}</span></td>
                    <td>{{ $customer?->name ?? 'N/A' }}</td>
                    <td>{{ $customer?->phone ?? '—' }}</td>
                    <td style="text-align:center">{{ number_format($smallQty, 0) }}</td>
                    <td style="text-align:center">{{ number_format($bigQty, 0) }}</td>
                    <td style="text-align:right;color:#D85A30;font-weight:bold">៛{{ number_format((float) $order->delivery_fee_khr, 0) }}</td>
                    <td style="text-align:right;font-weight:bold">${{ number_format($totalPrice, 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:20px;color:#999">មិនមានវិក្ក័យបត្រ</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Pizza Happy Family &bull; ការដឹកជញ្ជូន &bull; {{ now()->format('Y') }}
    </div>

</body>

</html>

<?php
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="Pizza_Happy_Family_Delivery_' . $delivery->id . '_' . now()->format('Y-m-d') . '.xls"');
header('Pragma: no-cache');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Khmer OS', 'Arial', sans-serif; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 10px; text-align: left; }
        th { background-color: #1f2937; color: white; font-weight: bold; text-align: center; }
        tr:nth-child(even) { background-color: #f9fafb; }
        .header { text-align: center; margin: 20px 0; }
        .title { font-size: 18px; font-weight: bold; color: #1f2937; margin: 10px 0; }
        .subtitle { font-size: 12px; color: #6b7280; margin: 5px 0; }
        .currency { text-align: right; font-weight: 600; }
        .center { text-align: center; }
        .total-row { background-color: #fff3cd; font-weight: bold; }
    </style>
</head>
<body>
    @php
        $orders = $delivery->orders;
        $totalFee = 0;
        $totalAmount = 0;
        $totalSmall = 0;
        $totalBig = 0;
    @endphp
    <div class="header">
        <div class="title">ភីហ្សា គ្រួសាររីករាយ</div>
        <div class="subtitle">Pizza Happy Family</div>
        <div class="subtitle">ការដឹកជញ្ជូន — {{ $delivery->delivery_name }}</div>
        @if($startDate && $endDate)
            <div class="subtitle">
                {{ \Illuminate\Support\Carbon::parse($startDate)->format('Y-m-d') }}
                @if($startDate !== $endDate)
                    - {{ \Illuminate\Support\Carbon::parse($endDate)->format('Y-m-d') }}
                @endif
            </div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="60">លេខរៀង</th>
                @if($delivery->show_invoice_info)
                    <th width="110">លេខវិក្ក័យបត្រ</th>
                @endif
                <th width="150">ឈ្មោះអតិថិជន</th>
                <th width="120">លេខទំនាក់ទំនង</th>
                <th width="80">កេសតូច</th>
                <th width="80">កេសធំ</th>
                <th width="110">ថ្លៃដឹក (៛)</th>
                @if($delivery->show_invoice_info)
                    <th width="110">តម្លៃសរុប ($)</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                @php
                    $invoice = $order->invoice;
                    $customer = $order->customer;
                    $smallQty = (int) ($order->small_pack_qty ?? $order->box_qty ?? 1);
                    $bigQty = (int) ($order->big_pack_qty ?? 0);
                    $fee = (float) $order->delivery_fee_khr;
                    $totalPrice = $invoice?->total_amount ?? $order->total_amount ?? 0;
                    $totalSmall += $smallQty;
                    $totalBig += $bigQty;
                    $totalFee += $fee;
                    $totalAmount += $totalPrice;
                @endphp
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    @if($delivery->show_invoice_info)
                        <td>{{ $invoice?->invoice_number ?? 'N/A' }}</td>
                    @endif
                    <td>{{ $customer?->name ?? 'N/A' }}</td>
                    <td>{{ $customer?->phone ?? '—' }}</td>
                    <td class="center">{{ $smallQty }}</td>
                    <td class="center">{{ $bigQty }}</td>
                    <td class="currency">{{ number_format($fee, 0) }}</td>
                    @if($delivery->show_invoice_info)
                        <td class="currency">{{ number_format($totalPrice, 2) }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $delivery->show_invoice_info ? 8 : 6 }}" style="text-align: center; color: #9ca3af;">មិនមានវិក្ក័យបត្រ</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="{{ $delivery->show_invoice_info ? 4 : 3 }}"><strong>សរុប</strong></td>
                <td class="center"><strong>{{ $totalSmall }}</strong></td>
                <td class="center"><strong>{{ $totalBig }}</strong></td>
                <td class="currency"><strong>{{ number_format($totalFee, 0) }}</strong></td>
                @if($delivery->show_invoice_info)
                    <td class="currency"><strong>{{ number_format($totalAmount, 2) }}</strong></td>
                @endif
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: center; color: #6b7280; font-size: 11px;">
        <p>បានធ្វើដោយ: Pizza Happy Family Management System</p>
        <p>ថ្ងៃទី: {{ now()->format('d F Y H:i') }}</p>
    </div>
</body>
</html>

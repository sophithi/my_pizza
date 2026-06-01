<?php
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="Pizza_Happy_Family_Payments.xls"');
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
        tr:hover { background-color: #eff6ff; }
        .header { text-align: center; margin: 20px 0; }
        .title { font-size: 18px; font-weight: bold; color: #1f2937; margin: 10px 0; }
        .subtitle { font-size: 12px; color: #6b7280; margin: 5px 0; }
        .currency { text-align: right; font-weight: 600; }
        .total-row { background-color: #fff3cd; font-weight: bold; }
        .status-paid { color: #065f46; background-color: #d1fae5; padding: 4px 8px; border-radius: 4px; }
        .status-partial { color: #92400e; background-color: #fef3c7; padding: 4px 8px; border-radius: 4px; }
        .status-pending { color: #991b1b; background-color: #fee2e2; padding: 4px 8px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <table style="border: none; width: 100%; margin-bottom: 10px;">
            <tr style="border: none;">
                
                <td style="border: none; vertical-align: middle; padding-left: 20px;">
                    <div class="title">ភីហ្សា គ្រួសាររីករាយ</div>
                    <div class="subtitle">Pizza Happy Family</div>
                </td>
            </tr>
        </table>
        <div class="subtitle" style="margin-top: 10px;">ការទូទាត់ពីអតិថិជន</div>
        <div class="subtitle">{{ $periodLabel }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="80">លេខការបញ្ជាទិញ</th>
                <th width="150">ឈ្មោះអតិថិជន</th>
                <th width="100">កាលបរិច្ឆេទ</th>
                <th width="80" class="currency">សរុប ($)</th>
                <th width="100" class="currency">សរុប (៛)</th>
                <th width="80" class="currency">បានបង់ ($)</th>
                <th width="100" class="currency">បានបង់ (៛)</th>
                <th width="80" class="currency">នៅសល់ ($)</th>
                <th width="100" class="currency">នៅសល់ (៛)</th>
                <th width="100">វិធីបង់</th>
                <th width="100">ស្ថានភាព</th>
                <th width="150">កំណត់ចំណាំ</th>
            </tr>
        </thead>
        <tbody>
            @php $exchangeRate = 4000; $totalUsd = 0; $totalKhr = 0; $paidUsd = 0; $paidKhr = 0; @endphp
            @forelse($payments as $payment)
                @php
                    $totalKhr = ($payment->total_amount ?? 0) * $exchangeRate;
                    $paidKhr = ($payment->paid_amount ?? 0) * $exchangeRate;
                    $balanceKhr = ($payment->balance ?? 0) * $exchangeRate;
                    $totalUsd += $payment->total_amount ?? 0;
                    $totalKhr += $totalKhr;
                    $paidUsd += $payment->paid_amount ?? 0;
                    $paidKhr += $paidKhr;
                @endphp
                <tr>
                    <td>{{ $payment->order_id }}</td>
                    <td>{{ $payment->customer_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->order_date)->format('Y-m-d') }}</td>
                    <td class="currency">${{ number_format($payment->total_amount ?? 0, 2) }}</td>
                    <td class="currency">៛{{ number_format($totalKhr, 0) }}</td>
                    <td class="currency">${{ number_format($payment->paid_amount ?? 0, 2) }}</td>
                    <td class="currency">៛{{ number_format($paidKhr, 0) }}</td>
                    <td class="currency">${{ number_format($payment->balance ?? 0, 2) }}</td>
                    <td class="currency">៛{{ number_format($balanceKhr, 0) }}</td>
                    <td>{{ $payment->method ?? '—' }}</td>
                    <td>
                        @if($payment->status === 'paid')
                            <span class="status-paid">បានបង់</span>
                        @elseif($payment->status === 'partial')
                            <span class="status-partial">បង់ខ្លះ</span>
                        @else
                            <span class="status-pending">មិនទាន់បង់</span>
                        @endif
                    </td>
                    <td>{{ $payment->notes ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align: center; color: #9ca3af;">មិនមានទិន្នន័យ</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="3"><strong>សរុបសម្រុង</strong></td>
                <td class="currency"><strong>${{ number_format($totalUsd, 2) }}</strong></td>
                <td class="currency"><strong>៛{{ number_format($totalKhr, 0) }}</strong></td>
                <td class="currency"><strong>${{ number_format($paidUsd, 2) }}</strong></td>
                <td class="currency"><strong>៛{{ number_format($paidKhr, 0) }}</strong></td>
                <td class="currency"><strong>${{ number_format($totalUsd - $paidUsd, 2) }}</strong></td>
                <td class="currency"><strong>៛{{ number_format($totalKhr - $paidKhr, 0) }}</strong></td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: center; color: #6b7280; font-size: 11px;">
        <p>បានធ្វើដោយ: Pizza Happy Family Management System</p>
        <p>ថ្ងៃទី: {{ now()->format('d F Y H:i') }}</p>
    </div>
</body>
</html>

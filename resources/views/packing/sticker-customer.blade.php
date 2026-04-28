<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Sticker - {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: A5 portrait;
            margin: 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background: white;
            padding: 20px;
            font-size: 14px;
        }

        .sticker {
            max-width: 148mm;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e85d24;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
            color: #e85d24;
        }

        .invoice-details {
            text-align: right;
        }

        .invoice-details p {
            margin: 2px 0;
            color: #666;
            font-size: 14px;
        }

        .invoice-number {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .section {
            margin-bottom: 14px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .customer-info,
        .invoice-info {
            display: inline-block;
            width: 48%;
            vertical-align: top;
        }

        .invoice-info {
            text-align: right;
        }

        .customer-info p,
        .invoice-info p {
            margin: 2px 0;
            font-size: 14px;
        }

        .customer-line {
            display: flex;
            gap: 6px;
            line-height: 1.45;
            margin: 2px 0;
        }

        .customer-line .label {
            color: #555;
            flex: 0 0 58px;
            font-weight: 700;
        }

        .customer-line .value {
            color: #333;
            font-weight: 500;
        }

        .customer-name {
            font-weight: 600;
            font-size: 16px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        thead {
            background: #f8f9fa;
            border-top: 2px solid #ddd;
            border-bottom: 2px solid #ddd;
        }

        th {
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            color: #666;
            font-size: 14px;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }

        .totals-box {
            width: 220px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .grand-total {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background: #f8f9fa;
            font-weight: 600;
            font-size: 16px;
            border-radius: 6px;
            margin-top: 6px;
        }

        .grand-total .amount {
            color: #e85d24;
            font-size: 18px;
            text-align: right;
        }

        .notes {
            margin-top: 14px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
            font-size: 14px;
        }

        .notes-title {
            font-weight: 600;
            margin-bottom: 4px;
            color: #333;
            font-size: 14px;
        }

        .footer {
            margin-top: 16px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #999;
            font-size: 14px;
        }

        .sticker-label {
            display: inline-block;

            color: #000000;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        @media print {
            body {
                padding: 0;
            }

            .sticker {
                border: none;
                padding: 0;
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="sticker">
        <div class="header">
            <div>
                <div class="logo">PizzaHappyFamily</div>
                <span class="sticker-label">វិក្ក័យបត្រអតិថិជន</span>
            </div>
            <div class="invoice-details">
                <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                <p>កាលបរិច្ឆេទ: {{ $invoice->invoice_date->translatedFormat('M d, Y') }}</p>
            </div>
        </div>

        <div class="section">
            <div class="customer-info">
                <div class="section-title">ព័ត៌មានអតិថិជន</div>
                @if($invoice->order && $invoice->order->customer)
                    @php
                        $customer = $invoice->order->customer;
                        $source = $customer->type === 'facebook'
                            ? 'Facebook'
                            : ($customer->type === 'telegram' ? 'Telegram' : null);
                    @endphp
                    <div class="customer-line">
                        <span class="label">ឈ្មោះ:</span>
                        <span class="value">{{ $customer->name }}{{ $source ? ' (' . $source . ')' : '' }}</span>
                    </div>
                    <div class="customer-line">
                        <span class="label">ទីតាំង:</span>
                        <span class="value">{{ $customer->address ?? $customer->city ?? '-' }}</span>
                    </div>
                    <div class="customer-line">
                        <span class="label">លេខ:</span>
                        <span class="value">{{ $customer->phone ?? '-' }}</span>
                    </div>
                    @php
                        $deliveryItems = $invoice->order->items->filter(fn($item) => $item->delivery_id);
                        $deliveryGroups = $deliveryItems->groupBy('delivery_id');
                    @endphp
                    @if($deliveryItems->count())
                        <div style="margin-top: 6px;">
                            <strong>ការដឹកជញ្ជូន:</strong>
                            @if($deliveryGroups->count() === 1)
                                @php
                                    $firstDeliveryItem = $deliveryItems->first();
                                @endphp
                                <p style="margin: 2px 0;">{{ $firstDeliveryItem->delivery->delivery_name ?? 'មិនមាន' }}</p>
                            @else
                                @foreach($deliveryGroups as $group)
                                    @php
                                        $firstDeliveryItem = $group->first();
                                        $productNames = $group->map(fn($item) => $item->product->name ?? 'ទំនិញ')->join(', ');
                                    @endphp
                                    <p style="margin: 2px 0;">
                                        {{ $firstDeliveryItem->delivery->delivery_name ?? 'មិនមាន' }}:
                                        {{ $productNames }}
                                    </p>
                                @endforeach
                            @endif
                        </div>
                    @endif
                @else
                    <p class="customer-name">N/A</p>
                @endif
            </div>
            <div class="invoice-info">
                <div class="section-title">Invoice Info</div>
                <p><strong>Order:</strong> ORD-{{ str_pad($invoice->order->id ?? 0, 4, '0', STR_PAD_LEFT) }}</p>
                <p>
                    <strong>ស្ថានភាព:</strong>
                    @if($invoice->status === 'paid')
                        បានទូទាត់
                    @elseif($invoice->status === 'sent' || $invoice->status === 'draft' || $invoice->status === 'pending')
                        មិនទាន់ទូទាត់
                    @elseif($invoice->status === 'cancelled')
                        មិនទូទាត់
                    @else
                        {{ $invoice->status }}
                    @endif
                </p>
            </div>
        </div>

        @php
            $orderItems = $invoice->order?->items ?? collect();
            $subtotalKhr = $orderItems->sum(function ($item) {
                return (float) ($item->product?->price_khr ?? 0) * (float) $item->quantity;
            });
            $discountKhr = (float) $invoice->discount_amount * 4000;
            $grandTotalKhr = $subtotalKhr - $discountKhr + (float) $invoice->delivery_fee_khr;
        @endphp

        <table>
            <thead>
                <tr>
                    <th>រាយនាមមុខទំនិញ</th>
                    <th class="text-right">ចំនួន</th>
                    <th class="text-right">តម្លៃ</th>
                    <th class="text-right">សរុប (USD)</th>
                    <th class="text-right">សរុប (KHR)</th>
                </tr>
            </thead>
            <tbody>
                @if($orderItems->count() > 0)
                    @foreach ($orderItems as $item)
                        @php
                            $unitPriceKhr = (float) ($item->product?->price_khr ?? 0);
                            $totalPriceKhr = $unitPriceKhr * (float) $item->quantity;
                        @endphp
                        <tr>
                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                            <td class="text-right">{{ $item->quantity }} x</td>
                            <td class="text-right">៛{{ number_format($unitPriceKhr, 0) }}/${{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-right">${{ number_format($item->total_price, 2) }}</td>
                            <td class="text-right">៛{{ number_format($totalPriceKhr, 0) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px; color: #999;">No items found</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-box">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>${{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="total-row" style="border-bottom: none; padding-bottom: 0;">
                    <span></span>
                    <span
                        style="color: #888; font-size: 13px;">៛{{ number_format($subtotalKhr, 0) }}</span>
                </div>
                <div class="total-row">
                    <span>បញ្ចុះតម្លៃ:</span>
                    <span>-${{ number_format($invoice->discount_amount, 2) }}</span>
                </div>
                @if((float) $invoice->delivery_fee_khr > 0)
                    <div class="total-row">
                        <span>ការដឹកជញ្ជូន:</span>
                        <span>
                            ${{ number_format($invoice->delivery_fee_usd, 2) }}
                            <span style="display: block; color: #888; font-size: 13px;">៛{{ number_format($invoice->delivery_fee_khr, 0) }}</span>
                        </span>
                    </div>
                @endif
                <div class="grand-total">
                    <span>តម្លៃសរុបទាំងអស់:</span>
                    <span class="amount">
                        ${{ number_format($invoice->total_amount, 2) }}
                        <span
                            style="display: block; font-size: 14px; color: #888; font-weight: 400;">៛{{ number_format($grandTotalKhr, 0) }}</span>
                    </span>
                </div>
            </div>
        </div>

        @if($invoice->notes)
            <div class="notes">
                <div class="notes-title">Notes</div>
                {{ $invoice->notes }}
            </div>
        @endif

        <div class="footer">
            <p>@PizzaHappyFamily សូមអគុណអតិថិជនសម្រាប់ការកម្មង់</p>
        </div>
    </div>

    <div class="no-print"
        style="text-align: center; margin-top: 20px; display: flex; justify-content: center; gap: 12px;">
        <button onclick="window.print()"
            style="background: #e85d24; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">
            Print Sticker 
        </button>
        <a href="{{ route('packing.index') }}"
            style="background: #f0f2f5; color: #1a1d29; border: 1px solid #e5e7eb; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            ← Back
        </a>
    </div>
</body>

</html>

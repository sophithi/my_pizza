<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->invoice_number }}</title>
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
        .container {
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
        .customer-info, .invoice-info {
            display: inline-block;
            width: 48%;
            vertical-align: top;
        }
        .invoice-info {
            text-align: right;
        }
        .customer-info p, .invoice-info p {
            margin: 2px 0;
            font-size: 14px;
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
        @media print {
            body {
                padding: 0;
            }
            .container {
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
    <div class="container">
        <div class="header">
            <div class="logo">PizzaHappyFamily
                
            </div>
            
            <div class="invoice-details">
                <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                <p>កាលបរិច្ឆេទ: {{ $invoice->invoice_date->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="section">
            <div class="customer-info">
                <div class="section-title">ព័ត៌មានអតិថិជន</div>
                @if($invoice->order && $invoice->order->customer)
                <p class="customer-name">{{ $invoice->order->customer->name }}</p>
                <p>{{ $invoice->order->customer->address ?? '-' }}</p>
                <p>{{ ($invoice->order->customer->city ?? '') . ' ' . ($invoice->order->customer->postal_code ?? '') }}</p>
                <p>{{ $invoice->order->customer->phone ?? '-' }}</p>
                @else
                <p class="customer-name">N/A</p>
                <p>Customer information not available</p>
                @endif
            </div>
            <div class="invoice-info">
                <div class="section-title">Invoice Info</div>
                <p><strong>Order:</strong> {{ $invoice->order->id ?? 'N/A' }}</p>
                <p><strong>Status:</strong> {{ ucfirst($invoice->status) }}</p>
            </div>
        </div>

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
                @if($invoice->order && $invoice->order->items && count($invoice->order->items) > 0)
                    @foreach ($invoice->order->items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">${{ number_format($item->total_price, 2) }}</td>
                        <td class="text-right" style="color: #888;">៛{{ number_format($item->total_price * 4000, 0) }}</td>
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
                    <span style="color: #888; font-size: 13px;">៛{{ number_format($invoice->subtotal * 4000, 0) }}</span>
                </div>
                <div class="total-row">
                    <span>បញ្ចុះតម្លៃ:</span>
                    <span>-${{ number_format($invoice->discount_amount, 2) }}</span>
                </div>
                <div class="grand-total">
                    <span>តម្លៃសរុបទាំងអស់:</span>
                    <span class="amount">
                        ${{ number_format($invoice->total_amount, 2) }}
                        <span style="display: block; font-size: 14px; color: #888; font-weight: 400;">៛{{ number_format($invoice->total_amount * 4000, 0) }}</span>
                    </span>
                </div>
            </div>
        </div>

        @if ($invoice->notes)
        <div >
            <div class="notes-title">Notes</div>
            {{ $invoice->notes }}
        </div>
        @endif

        <div class="footer">
            <p>@PizzaHappyFamily សូមអគុណអតិថិជនសម្រាប់ការកម្មង់</p>
          
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px; display: flex; justify-content: center; gap: 12px;">
        <button onclick="window.print()" style="background: #e85d24; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 500;">
            <i class="fas fa-print"></i> Print 
        </button>
        <a href="{{ route('invoices.show', $invoice->id) }}" style="background: #f0f2f5; color: #1a1d29; border: 1px solid #e5e7eb; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            ← back
        </a>
    </div>
    <script>

    </script>
</body>
</html>

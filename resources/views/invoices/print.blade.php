<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background: white;
            padding: 40px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border: 1px solid #ddd;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e85d24;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #e85d24;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-details p {
            margin: 4px 0;
            color: #666;
        }
        .invoice-number {
            font-size: 20px;
            font-weight: 600;
            color: #333;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 10px;
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
            margin: 4px 0;
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
            margin: 20px 0;
        }
        thead {
            background: #f8f9fa;
            border-top: 2px solid #ddd;
            border-bottom: 2px solid #ddd;
        }
        th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #666;
            font-size: 13px;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }
        .totals-box {
            width: 300px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .grand-total {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            background: #f8f9fa;
            font-weight: 600;
            font-size: 16px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .grand-total .amount {
            color: #e85d24;
            font-size: 18px;
        }
        .notes {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .notes-title {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #999;
            font-size: 12px;
        }
        @media print {
            body {
                padding: 0;
            }
            .container {
                border: none;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">PizzaHappyFamily</div>
            <div class="invoice-details">
                <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                <p>កាលបរិច្ឆេទ: {{ $invoice->invoice_date->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="section">
            <div class="customer-info">
                <div class="section-title">ព័ត៌មានអតិថិជន</div>
                <p class="customer-name">{{ $invoice->order->customer->name }}</p>
                <p>{{ $invoice->order->customer->address }}</p>
                <p>{{ $invoice->order->customer->city }}, {{ $invoice->order->customer->postal_code }}</p>
                <p>{{ $invoice->order->customer->phone }}</p>
            </div>
            <div class="invoice-info">
                <div class="section-title">Invoice Info</div>
                <p><strong>Order #:</strong> {{ $invoice->order->id }}</p>
                <p><strong>Status:</strong> {{ ucfirst($invoice->status) }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>រាយនាមមុខទំនិញ</th>
                    <th class="text-right">ចំនួន</th>
                    <th class="text-right">តម្លៃ</th>
                    <th class="text-right">សរុប</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">${{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-box">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>${{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="total-row">
                    <span>បញ្ចុះតម្លៃ:</span>
                    <span>-${{ number_format($invoice->discount_amount, 2) }}</span>
                </div>
                <div class="grand-total">
                    <span>តម្លៃសរុបទាំងអស់:</span>
                    <span class="amount">${{ number_format($invoice->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        @if ($invoice->notes)
        <div class="notes">
            <div class="notes-title">Notes</div>
            {{ $invoice->notes }}
        </div>
        @endif

        <div class="footer">
            <p>Thank you for order!</p>
            <p>វិក្ក័យប័ត្របានធ្វើនៅ{{ now()->format('M d, Y') }}</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="background: #e85d24; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 500;">
            <i class="fas fa-print"></i> Print វិក្ក័យប័ត្រ
        </button>
    </div>

    <script>
      
    </script>
</body>
</html>

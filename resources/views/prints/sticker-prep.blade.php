<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prep Sticker - {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: A5 portrait;
            margin: 8mm;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1a1d29;
            background: white;
            padding: 16px;
            font-size: 14px;
        }
        .sticker {
            max-width: 148mm;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border: 2px dashed #e85d24;
            border-radius: 8px;
        }
        .sticker-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 12px;
            margin-bottom: 14px;
            border-bottom: 2px solid #e85d24;
        }
        .sticker-title {
            font-size: 20px;
            font-weight: 800;
            color: #e85d24;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .order-id {
            font-size: 22px;
            font-weight: 800;
            color: #1a1d29;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 14px;
            font-size: 13px;
            color: #666;
        }
        .meta-row strong { color: #1a1d29; }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .items-table thead {
            background: #1a1d29;
        }
        .items-table th {
            padding: 10px 12px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #fff;
            text-align: left;
        }
        .items-table th:last-child { text-align: center; }
        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
        }
        .items-table td:last-child {
            text-align: center;
            font-weight: 800;
            font-size: 18px;
            color: #e85d24;
        }
        .items-table tbody tr:last-child td { border-bottom: none; }
        .product-name { font-weight: 600; }
        .product-desc { font-size: 12px; color: #888; margin-top: 2px; }
        .notes-box {
            background: #fff8f5;
            border: 1px solid #fcd5c5;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 14px;
        }
        .notes-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #e85d24;
            margin-bottom: 4px;
        }
        .notes-text { font-size: 13px; color: #333; }
        .footer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 12px;
            border-top: 1px dashed #ccc;
            font-size: 12px;
            color: #999;
        }
        .total-items {
            font-size: 14px;
            font-weight: 700;
            color: #1a1d29;
        }
        .checkbox-area {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px dashed #ccc;
        }
        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
            font-size: 13px;
            color: #666;
        }
        .checkbox-row .box {
            width: 16px;
            height: 16px;
            border: 2px solid #ccc;
            border-radius: 3px;
            flex-shrink: 0;
        }
        @media print {
            body { padding: 0; }
            .sticker { border: 2px dashed #e85d24; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="sticker">
        <div class="sticker-header">
            <span class="sticker-title">Preparation</span>
            <span class="order-id">ORD-{{ str_pad($invoice->order->id, 4, '0', STR_PAD_LEFT) }}</span>
        </div>

        <div class="meta-row">
            <span><strong>Invoice:</strong> {{ $invoice->invoice_number }}</span>
            <span><strong>Date:</strong> {{ $invoice->invoice_date->translatedFormat('M d, Y') }}</span>
        </div>
        <div class="meta-row">
            <span><strong>Customer:</strong> {{ $invoice->order->customer->name ?? 'N/A' }}</span>
            <span><strong>Items:</strong> {{ $invoice->order->items->count() }} products</span>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->order->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <div class="product-name">{{ $item->product->name ?? 'N/A' }}</div>
                        @if($item->product->category ?? false)
                        <div class="product-desc">{{ $item->product->category }}</div>
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($invoice->order->notes || $invoice->notes)
        <div class="notes-box">
            <div class="notes-label">Special Notes</div>
            <div class="notes-text">{{ $invoice->order->notes ?? $invoice->notes }}</div>
        </div>
        @endif

        <div class="footer-row">
            <span class="total-items">Total: {{ $invoice->order->items->sum('quantity') }} items</span>
            <span>Printed: {{ now()->setTimezone('Asia/Phnom_Penh')->format('M d, Y h:i A') }}</span>
        </div>

        <div class="checkbox-area">
            <div class="checkbox-row"><span class="box"></span> Items prepared</div>
            <div class="checkbox-row"><span class="box"></span> Quality checked</div>
            <div class="checkbox-row"><span class="box"></span> Ready for delivery</div>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px; display: flex; justify-content: center; gap: 12px;">
        <button onclick="window.print()" style="background: #e85d24; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">
            Print Sticker
        </button>
        <a href="{{ route('print.index') }}" style="background: #f0f2f5; color: #1a1d29; border: 1px solid #e5e7eb; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            ← Back
        </a>
    </div>
</body>
</html>

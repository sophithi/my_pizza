<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $invoice->invoice_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Moul&family=Noto+Sans+Khmer:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A5 portrait;
            margin: 8mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background: #eef0f3;
            padding: 20px;
            font-size: 14px;
        }

        .sticker {
            max-width: 148mm;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .08);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e85d24;
        }

        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo {
            font-family: 'Moul', 'Noto Sans Khmer', 'Segoe UI', sans-serif;
            font-size: 26px;
            font-weight: normal;
            letter-spacing: 0.3px;
            margin-top: 8px;
            line-height: 1.3;
            color: #d20011;
            text-shadow: 2px 3px 3px rgba(178, 178, 178, 0.35);
        }
/* 
        .logo-sub {
            font-size: 13px;
            font-weight: 700;
            color: #999;
            letter-spacing: .5px;
            text-transform: uppercase;
        } */

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

        .khr-sub {
            display: block;
            color: #888;
            font-size: 12px;
            font-weight: 400;
            margin-top: 1px;
        }
        
        .khr-subbold {
            display: block;
            color: #e85d24;
            font-size: 18px;
            font-weight: 400;
            margin-top: 1px;
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

        .total-row .amount-col {
            text-align: right;
        }

        .grand-total {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background: #eee;
            font-weight: 600;
            font-size: 18px;
            border-radius: 6px;
            margin-top: 6px;
        }

        .grand-total .amount {
            color: #e85d24;
            font-size: 18px;
            text-align: right;
        }

        .free-products-title {
            font-weight: 700;
            color: #333;
            margin-bottom: 4px;
            text-align: left;
        }

        .free-product-item {
            margin: 2px 0;
            color: #e85d24;
            text-align: left;
        }

        .notes-section-title {
            font-weight: 600;
            margin: 0 0 4px;
            color: #333;
            font-size: 14px;
        }

        .notes {
            margin-top: 14px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
             color: #e85d24;
            font-size: 14px;
            text-align: left;
            display: flex;
            gap: 16px;
        }

        .notes-col {
            flex: 1;
            min-width: 0;
        }


        .footer {
            margin-top: 16px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #999;
            font-size: 13px;
            line-height: 1.6;
        }

        .footer-meta {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #e5e5e5;
            font-size: 11px;
            color: #b3b3b3;
        }

        .footer-meta span:not(:last-child)::after {
            content: "•";
            margin-left: 10px;
            color: #ddd;
        }

        .footer-brand {
            color: #e85d24;
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 2px;
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

        .btn-print {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            padding: 12px 26px;
            min-width: 170px;
            height: 48px;

            border: none;
            border-radius: 10px;
            text-decoration: none;

            background: linear-gradient(135deg, #ff7a45, #e85d24);
            color: #fff;

            font-size: 15px;
            font-weight: 600;
            letter-spacing: .3px;

            cursor: pointer;
            transition: all .25s ease;

            box-shadow:
                0 6px 16px rgba(232, 93, 36, .35),
                inset 0 1px 0 rgba(255, 255, 255, .25);
        }

        .btn-print:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #ff8c5c, #f2691f);
            box-shadow:
                0 10px 24px rgba(232, 93, 36, .45),
                inset 0 1px 0 rgba(255, 255, 255, .3);
        }

        .btn-print:active {
            transform: translateY(0);
            box-shadow: 0 4px 10px rgba(232, 93, 36, .35);
        }

        .btn-print .icon {
            font-size: 18px;
            display: inline-block;
        }

        .btn-save {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            padding: 12px 26px;
            min-width: 150px;
            height: 48px;

            border-radius: 10px;

            background: #ffffff;
            color: #e85d24;
            border: 2px solid #e85d24;

            font-size: 15px;
            font-weight: 600;
            letter-spacing: .3px;

            cursor: pointer;
            transition: all .2s ease;
        }

        .btn-save:hover {
            background: #fff4ee;
            transform: translateY(-2px);
        }

        .btn-save:active {
            transform: translateY(0);
        }

        .btn-save .icon {
            font-size: 18px;
            transition: transform 0.3s ease;
            display: inline-block;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .btn-save.copying .icon {
            animation: spin 1s linear infinite;
        }

        .btn-save.copying {
            opacity: 0.8;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            padding: 12px 24px;
            min-width: 150px;
            height: 48px;

            background: #ffffff;
            color: #374151;
            border: 1.5px solid #d1d5db;
            border-radius: 10px;

            font-size: 15px;
            font-weight: 600;
            letter-spacing: .3px;
            text-decoration: none;

            cursor: pointer;
            transition: all .2s ease;
            box-shadow: 0 2px 6px rgba(15, 23, 42, .06);
        }

        .btn-back:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
            color: #111827;
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(15, 23, 42, .12);
        }

        .btn-back:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(15, 23, 42, .08);
        }

        .btn-back .icon {
            font-size: 16px;
            display: inline-block;
        }

        .action-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 10px;
            padding: 14px;
            position: sticky;
            bottom: 0;
            background: rgba(238, 240, 243, .92);
            backdrop-filter: blur(6px);
        }

        @media (max-width: 480px) {
            .action-bar {
                flex-direction: column;
            }

            .action-bar .btn-print,
            .action-bar .btn-save,
            .action-bar .btn-back {
                width: 100%;
            }
        }

        .brand-switch {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 20px;
        }

        .brand-switch-label {
            color: #6b7280;
            font-weight: 600;
            font-size: 13px;
            margin-right: 2px;
        }

        .brand-chip {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 999px;
            border: 1.5px solid #d1d5db;
            color: #4b5563;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            background: #fff;
            transition: all .2s ease;
        }

        .brand-chip:hover {
            border-color: #e85d24;
            color: #e85d24;
            transform: translateY(-1px);
        }

        .brand-chip.active {
            background: #e85d24;
            border-color: #e85d24;
            color: #fff;
            cursor: default;
            pointer-events: none;
        }

        @media print {
            @page {
                size: A5 portrait;
                margin: 5mm;
            }

            html,
            body {
                padding: 0;
                margin: 0;
                font-size: 13px;
                line-height: 1.35;
            }

            .sticker {
                border: none;
                border-radius: 0;
                box-shadow: none;
                padding: 0 1mm;
                max-width: 100%;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .no-print {
                display: none !important;
            }

            .header {
                margin-bottom: 10px;
                padding-bottom: 8px;
            }

            .logo {
                font-size: 21px;
            }

            .logo-sub {
                font-size: 11px;
            }

            .invoice-number {
                font-size: 17px;
            }

            .invoice-details p,
            .customer-info p,
            .invoice-info p,
            .customer-line,
            th,
            td,
            .total-row,
            .notes,
            .footer {
                font-size: 13px;
            }

            .section {
                margin-bottom: 9px;
            }

            .section-title {
                font-size: 13px;
            }

            table {
                margin: 8px 0;
            }

            th,
            td {
                padding: 6px 8px;
            }

            .totals {
                margin-top: 6px;
            }

            .totals-box {
                width: 230px;
            }

            .total-row {
                padding: 4px 0;
            }

            .grand-total {
                padding: 8px;
                margin-top: 6px;
                font-size: 14px;
            }

            .grand-total .amount {
                font-size: 18px;
            }

            .notes {
                margin-top: 10px;
                padding: 8px;
            }

            .notes-section-title {
                margin-top: 0;
            }

            .footer {
                margin-top: 10px;
                padding-top: 8px;
            }

            tr {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .totals,
            .notes,
            .footer {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .header {
                page-break-after: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="sticker" id="invoice-content">

        <div class="header">
            <div class="logo-wrap">
                <div>
                    <div class="logo">ភីហ្សា គ្រួសាររីករាយ</div>
                    <!-- <div class="logo-sub">Pizza Happy Family</div> -->
                </div>
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
                    </div>
                    <div class="invoice-info">
                        <div class="section-title">Invoice Info</div>
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
                                        $deliveryRecord = $firstDeliveryItem->delivery;
                                    @endphp
                                    <p style="margin: 2px 0;">{{ $deliveryRecord->delivery_name ?? 'មិនមាន' }}</p>
                                @else
                                    @foreach($deliveryGroups as $group)
                                        @php
                                            $firstDeliveryItem = $group->first();
                                            $deliveryRecord = $firstDeliveryItem->delivery;
                                            $productNames = $group->map(fn($item) => $item->product->name ?? 'ទំនិញ')->join(', ');
                                        @endphp
                                        <p style="margin: 2px 0;">
                                            {{ $deliveryRecord->delivery_name ?? 'មិនមាន' }}:
                                            {{ $productNames }}
                                        </p>
                                    @endforeach
                                @endif

                                @if(!empty($invoice->order->taxi_phone))
                                    <p style="margin: 2px 0;"><strong>:</strong> {{ $invoice->order->taxi_phone }}</p>
                                @endif
                            </div>
                        @endif
                @else
                    <p class="customer-name">N/A</p>
                @endif
                <p>
                    <strong>ការបង់ប្រាក់:</strong>
                    @if($invoice->order?->payment_status === 'paid')
                        បានទូទាត់
                    @elseif($invoice->order?->payment_status === 'partial')
                        បង់មួយផ្នែក
                    @else
                        មិនទាន់ទូទាត់
                    @endif
                </p>

            </div>
        </div>
        @php
            $orderItems = $invoice->order?->items ?? collect();
            $paidItems = $orderItems->filter(function ($item) {
                return (float) $item->unit_price > 0;
            });

            // Canonical totals — see Order::grossSubtotalKhr()/itemDiscountKhr()/
            // totalKhr(), so this always matches the order/invoice pages instead
            // of re-deriving its own formula. Free items contribute 0 either way,
            // so summing over all items here gives the same result as $paidItems.
            $subtotalKhr = ($invoice->order?->grossSubtotalKhr() ?? 0) - ($invoice->order?->itemDiscountKhr() ?? 0);
            $grandTotalKhr = $invoice->order?->totalKhr() ?? 0;
        @endphp
        <table>
            <thead>
                <tr>
                    <th>រាយនាមមុខទំនិញ</th>
                    <th class="text-right">ចំនួន</th>
                    <th class="text-right">តម្លៃ</th>
                    <th class="text-right">បញ្ចុះតម្លៃ</th>
                    <th class="text-right">សរុប (KHR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($paidItems as $item)
                    @php
                        $unitPriceKhr = $item->displayUnitKhr();
                        $discountPercent = (float) ($item->discount_percent ?? 0);
                        $lineDiscountKhr = $unitPriceKhr * (float) $item->quantity * ($discountPercent / 100);
                        $totalPriceKhr = $item->lineTotalKhr();
                    @endphp
                    <tr>
                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                        <td class="text-right">{{ $item->quantity }} x</td>
                        <td class="text-right">
                            ៛{{ number_format($unitPriceKhr, 0) }}
                        </td>
                        <td class="text-right">
                            @if($discountPercent > 0)
                                {{ number_format($discountPercent, 0) }}%
                                <!-- <span class="khr-sub">-៛{{ number_format($lineDiscountKhr, 0) }}</span> -->
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            <span class="value-khr">៛{{ number_format($totalPriceKhr, 0) }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="totals">
            <div class="totals-box">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span class="amount-col">
                        ៛{{ number_format($subtotalKhr, 0) }}
                        <span class="khr-sub">${{ number_format($invoice->subtotal, 2) }}</span>
                    </span>
                </div>

                @if((float) $invoice->delivery_fee_khr > 0)
                    <div class="total-row">
                        <span>ការដឹកជញ្ជូន:</span>
                        <span class="amount-col">
                            ${{ number_format($invoice->delivery_fee_usd, 2) }}
                            <span class="khr-sub">៛{{ number_format($invoice->delivery_fee_khr, 0) }}</span>
                        </span>
                    </div>
                @endif

                <div class="grand-total">
                    <span>តម្លៃសរុប:</span>
                    <span class="amount">
                        ៛{{ number_format($grandTotalKhr, 0) }}
                        <span class="khr-subbold">${{ number_format($invoice->total_amount, 2) }}</span>
                    </span>
                </div>
            </div>
        </div>
        @if(($invoice->order && $invoice->order->freeItems->count() > 0) || $invoice->notes)
            <div class="notes">
                @if($invoice->order && $invoice->order->freeItems->count() > 0)
                    <div class="notes-col">
                        <div class="free-products-title">free ជូនអតិថិជន</div>
                        @foreach($invoice->order->freeItems as $freeItem)
                            <p class="free-product-item">
                                {{ $freeItem->product->name ?? 'N/A' }} (x{{ $freeItem->quantity ?? 1 }})
                            </p>
                        @endforeach
                    </div>
                @endif
                @if($invoice->notes)
                    <div class="notes-col">
                        <div class="notes-section-title">ផ្សេងៗ</div>
                        <div>{{ $invoice->notes }}</div>
                    </div>
                @endif
            </div>
        @endif

        <div class="footer">
            @if($invoice->order?->user)
                <p class="footer-meta">
                    @if($invoice->order?->user)
                        <span>អ្នកចេញវិក្ក័យបត្រ: {{ $invoice->order->user->name }}</span>
                        <span>អ្នករៀបទំនិញ:..................</span>
                        <span>អ្នកពិនិត្យ:.................. </span>
                    @endif

                </p>
            @endif
            <p>សូមអគុណអតិថិជនសម្រាប់ការកម្មង់</p>
            <p>ទំនាក់ទំនងក្រុមហ៊ុន៖ 095 423 334 | 088 5459 339 | 098 459 339</p>
        </div>
    </div>

    @php
        $currentRoute = request()->route()?->getName();
    @endphp
    <div class="no-print brand-switch">
        <span class="brand-switch-label">ជ្រើសរើស:</span>
        <a href="{{ route('packing.customer', $invoice) }}" target="_blank"
            class="brand-chip @if($currentRoute === 'packing.customer') active @endif">ភីហ្សា គ្រួសាររីករាយ</a>
        <a href="{{ route('packing.customer_mayo', $invoice) }}" target="_blank"
            class="brand-chip @if($currentRoute === 'packing.customer_mayo') active @endif">ម៉ាយូនេស បន្ទាយឆ្មារ</a>
        <a href="{{ route('packing.customer_tamon', $invoice) }}" target="_blank"
            class="brand-chip @if($currentRoute === 'packing.customer_tamon') active @endif">តាម៉ាន់មានជ័យ</a>
    </div>

    <div class="no-print action-bar">
        <button class="btn-print" onclick="window.print()">
            <span class="icon">🖨️</span>
            <span>Print Invoice</span>
        </button>

        <button class="btn-save" id="copyBtn" onclick="handleCopyInvoice()">
            <span class="icon" id="copyIcon">📋</span>
            <span id="copyText">Copy Invoice</span>
        </button>
        @unless(auth()->user()->isStaffInventory())
        <a href="{{ $backUrl ?? route('invoices.index') }}" class="btn-back">
            <span class="icon">←</span> ទំព័រវិក្ក័យបត្រ
        </a>
        @endunless

        <a href="{{ route('packing.index') }}" class="btn-back">
            <span class="icon">←</span> ទំព័ររៀបចំ
        </a>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function handleCopyInvoice() {
            const btn = document.getElementById('copyBtn');
            const icon = document.getElementById('copyIcon');
            const text = document.getElementById('copyText');

            // Prevent multiple clicks
            if (btn.classList.contains('copying')) return;

            // Set loading state
            btn.classList.add('copying');
            btn.style.pointerEvents = 'none';
            icon.textContent = '⏳';
            text.textContent = 'Copying...';

            // Capture the invoice as PNG
            const invoiceElement = document.getElementById('invoice-content');

            html2canvas(invoiceElement, {
                scale: 2,
                backgroundColor: '#ffffff',
                logging: false,
                useCORS: true
            }).then(canvas => {
                // Convert canvas to blob
                canvas.toBlob(blob => {
                    // Copy to clipboard
                    const item = new ClipboardItem({ 'image/png': blob });
                    navigator.clipboard.write([item]).then(() => {
                        // Success
                        icon.textContent = '✅';
                        text.textContent = 'Copied!';

                        // Reset after 2 seconds
                        setTimeout(() => {
                            btn.classList.remove('copying');
                            btn.style.pointerEvents = 'auto';
                            icon.textContent = '📋';
                            text.textContent = 'Copy Invoice';
                        }, 2000);
                    }).catch(error => {
                        // Error
                        console.error('Copy failed:', error);
                        icon.textContent = '❌';
                        text.textContent = 'Error!';

                        // Reset after 2 seconds
                        setTimeout(() => {
                            btn.classList.remove('copying');
                            btn.style.pointerEvents = 'auto';
                            icon.textContent = '📋';
                            text.textContent = 'Copy Invoice';
                        }, 2000);
                    });
                }, 'image/png');
            }).catch(error => {
                console.error('Screenshot error:', error);
                icon.textContent = '❌';
                text.textContent = 'Error!';

                // Reset after 2 seconds
                setTimeout(() => {
                    btn.classList.remove('copying');
                    btn.style.pointerEvents = 'auto';
                    icon.textContent = '📋';
                    text.textContent = 'Copy Invoice';
                }, 2000);
            });
        }
    </script>
</body>

</html>
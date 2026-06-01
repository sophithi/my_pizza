<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Sticker - {{ $invoice->invoice_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Hanuman:wght@400;700;900&family=Noto+Sans+Khmer:wght@400;600;700;900&display=swap"
        rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 32px 16px;
            background: #d9d6d0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
            font-family: 'Noto Sans Khmer', 'Hanuman', sans-serif;
        }

        /* ── A5 Landscape 210mm × 148mm ── */
        .sticker {
            width: 210mm;
            height: 148mm;
            background: #fff;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            outline: 3px dashed #92400e;
            outline-offset: -4px;
            border-radius: 6px;
        }

        /* ── WARNING TITLE BANNER ── */
        .warning-band {
            background: #f6f6f6;
            border-bottom: 2px solid #8d8585;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .warning-band svg {
            width: 24px;
            height: 24px;
            color: #92400e;
            flex-shrink: 0;
        }

        .warning-text {
            font-family: 'Hanuman', sans-serif;
            font-size: 24pt;
            font-weight: 900;
            color: #fc4f28;
            letter-spacing: 0.5px;
            text-align: center;
        }

        /* ── HEADER ── */
        .header {
            background: #ffffff;
            padding: 11px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            flex-shrink: 0;
        }

        .header-logo {
            width: 50px;
            height: 50px;
            flex-shrink: 0;
            border-radius: 4px;
            background: rgba(0, 0, 0, 0);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .header-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brand-container {
            text-align: center;
        }

        .brand-name {
            font-family: 'Hanuman', sans-serif;
            font-size: 20pt;
            font-weight: 900;
            color: #830a0a;
            line-height: 1;
            text-align: center;
        }

        .brand-sub-title {
            font-size: 12pt;
            color:#830a0a;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-top: 3px;
            text-align: center;
        }



        /* ── BODY: 4 equal rows ── */
        .body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .info-row {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 16px;
            border-bottom: 1.5px dashed #e8e0d8;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .row-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 16pt;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            white-space: nowrap;
            min-width: 110px;
            flex-shrink: 0;
        }

        .row-label svg {
            width: 13px;
            height: 13px;
            flex-shrink: 0;
        }

        .row-divider {
            width: 1.5px;
            height: 32px;
            background: #e8e0d8;
            flex-shrink: 0;
            border-radius: 2px;
        }

        .row-value {
            font-family: 'Hanuman', sans-serif;
            font-weight: 900;
            line-height: 1.15;
            word-break: break-word;
        }

        /* Name */
        .row-name .row-label {
            color: #000000;
        }

        .row-name .row-value {
            font-size: 22pt;
            color: #1a1d29;
        }

        /* Address */
        .row-addr .row-label {
            color: #000000;
        }

        .row-addr .row-value {
            font-size: 20pt;
            color: #000000;
        }

        /* Customer phone */
        .row-phone .row-label {
            color: #000000;
        }

        .row-phone .row-value {
            font-size: 22pt;
            color: #000000;
            letter-spacing: 1px;
        }

        /* Sender phone */
        .row-sender .row-label {
            color: #000000;
        }

        .row-sender .row-value {
            font-size: 22pt;
            color: #000000;
            letter-spacing: 1px;
        }

        /* ── FOOTER ── */
        .footer {
            background: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .footer-contact {
            font-size: 12pt;
            color: rgb(0, 0, 0);
        }

        .footer-contact strong {
            color: #000000;
        }

        .footer-order {
            font-size: 12pt;
            font-weight: 700;
            color: #e85d24;
            letter-spacing: 1px;
        }

        /* ── Screen buttons ── */
        .no-print {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 24px;
            position: relative;
            z-index: 100;
        }

        .btn {
            padding: 11px 28px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            font-family: 'Noto Sans Khmer', sans-serif;
            transition: opacity 0.15s, transform 0.1s;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            pointer-events: all !important;
            user-select: none;
        }

        .btn-print {
            background: #e85d24;
            color: #fff;
        }

        .btn-print:hover {
            background: #d94a10;
            box-shadow: 0 4px 12px rgba(232, 93, 36, 0.3);
        }

        .btn-print:active {
            opacity: 0.85;
            transform: scale(0.97);
        }

        .btn-back {
            background: #fff;
            color: #1a1d29;
            border: 1.5px solid #ccc;
        }

        .btn-back:hover {
            background: #f5f5f5;
        }

        .btn-back:active {
            opacity: 0.85;
            transform: scale(0.97);
        }

        /* ── Print ── */
        @media print {
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            @page {
                size: A5 landscape;
                margin: 0;
                padding: 0;
            }

            html,
            body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                background: white;
                display: block;
                filter: grayscale(100%);
            }

            .sticker {
                width: 100%;
                height: 100%;
                outline: none;
                border-radius: 0;
                box-shadow: none;
                page-break-after: avoid;
                page-break-inside: avoid;
                border: 1px solid #000;
            }

            .no-print {
                display: none !important;
            }

            /* Convert all colors to black & white for print */
            .header {
                background: #000 !important;
            }

            .warning-band {
                background: #f5f5f5 !important;
                border-bottom: 2px solid #000 !important;
            }

            .warning-text {
                color: #000 !important;
            }

            .warning-band svg {
                color: #000 !important;
            }
        }
    </style>
</head>

<body>

    <div class="sticker">

        {{-- Header --}}
        <div class="header">
            <div class="header-logo">
                <!-- Add your logo here or use the one from assets/logos/ -->
                <!-- @if(file_exists(public_path('assets/logos/logo_pizza.png')))
                    <img src="{{ asset('assets/logos/logo_pizza.png') }}" alt="Pizza Happy Family  Logo">
                @endif -->
            </div>
            <div class="brand-container">
                <div class="brand-name">ភីហ្សាគ្រួសាររីករាយ-Pizza Happy Family</div>
                <div class="brand-sub-title">ផលិតផលគុណភាពខ្ពស់ ផលិតដោយកូនខ្មែរ រសជាតិឆ្ងាញ់</div>
            </div>
        </div>

        {{-- Warning banner --}}
        <div class="warning-band">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                <line x1="12" y1="9" x2="12" y2="13" />
                <line x1="12" y1="17" x2="12.01" y2="17" />
            </svg>
            <span class="warning-text">សូមដាក់កេសបញ្ឈរ កំុដាក់របស់ធ្ងន់ពីលើប្រយ័ត្នបែក</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                <line x1="12" y1="9" x2="12" y2="13" />
                <line x1="12" y1="17" x2="12.01" y2="17" />
            </svg>
        </div>

        {{-- Body rows --}}
        <div class="body">

            {{-- Customer name --}}
            <div class="info-row row-name">
                <div class="row-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    អតិថិជន
                </div>
                <div class="row-divider"></div>
                <div class="row-value">{{ $invoice->order->customer->name ?? 'N/A' }}</div>
            </div>

            {{-- Address --}}
            <div class="info-row row-addr">
                <div class="row-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M12 21s-7-6.5-7-11a7 7 0 0114 0c0 4.5-7 11-7 11z" />
                        <circle cx="12" cy="10" r="2" />
                    </svg>
                    ទីតាំង
                </div>
                <div class="row-divider"></div>
                <div class="row-value">{{ $invoice->order->customer->address ?? 'N/A' }}</div>
            </div>

            {{-- Customer phone --}}
            <div class="info-row row-phone">
                <div class="row-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path
                            d="M22 16.92v3a2 2 0 01-2.18 2A19.79 19.79 0 013.09 4.18 2 2 0 015.08 2h3a2 2 0 012 1.72c.13 1 .37 1.97.71 2.9a2 2 0 01-.45 2.11L9.09 9.91a16 16 0 006.99 7l1.18-1.18a2 2 0 012.11-.45c.93.34 1.9.58 2.9.71A2 2 0 0122 16.92z" />
                    </svg>
                    លេខអ្នកទទួល
                </div>
                <div class="row-divider"></div>
                <div class="row-value">{{ $invoice->order->customer->phone ?? 'N/A' }}</div>
            </div>
            {{-- Sender phone --}}
            <div class="info-row row-sender">
                <div class="row-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path
                            d="M22 16.92v3a2 2 0 01-2.18 2A19.79 19.79 0 013.09 4.18 2 2 0 015.08 2h3a2 2 0 012 1.72c.13 1 .37 1.97.71 2.9a2 2 0 01-.45 2.11L9.09 9.91a16 16 0 006.99 7l1.18-1.18a2 2 0 012.11-.45c.93.34 1.9.58 2.9.71A2 2 0 0122 16.92z" />
                    </svg>
                    លេខអ្នកផ្ញើរ
                </div>
                <div class="row-divider"></div>
                <div class="row-value">
                    {{ $invoice->order->sender_phone ?? config('app.sender_phone', '097 545 9339 / 096 745 7775') }}
                </div>
            </div>

        </div>{{-- /body --}}

        {{-- Footer --}}
        <div class="footer">
            <div class="footer-contact">
                ទំនាក់ទំនងបោះដុំ: <strong>095 423 334 / 088 5459 339 / 098 459 339</strong> &nbsp;·&nbsp;
                <!-- <strong>pizzahappyfamily@gmail.com</strong> -->
            </div>
            <!-- <div class="footer-order">#{{ $invoice->invoice_number }}</div> -->
        </div>

    </div>{{-- /sticker --}}

    {{-- Screen buttons --}}
    <div class="no-print">
        <button type="button" class="btn btn-print" onclick="window.print(); return false;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9" />
                <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2" />
                <rect x="6" y="14" width="12" height="8" />
            </svg>
            Print Sticker
        </button>
        <a class="btn btn-back" href="{{ $backUrl ?? url()->previous() ?? route('packing.index') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6" />
            </svg>
            Back
        </a>
    </div>

</body>

</html>

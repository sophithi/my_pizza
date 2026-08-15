<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Sticker - {{ $invoice->invoice_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Hanuman:wght@400;700;900&family=Noto+Sans+Khmer:wght@400;600;700;900&family=Pacifico&family=Moul&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --red: #c0272d;
            --red-dark: #8f1620;
            --red-soft: #f0c3c3;
        }

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
            border: 6px solid var(--red);
            border-radius: 22px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.18);
        }

        /* ── HEADER ── */
        .header {
            background: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            flex-shrink: 0;
            border-bottom: 2px dashed var(--red-soft);
        }

        .brand-container {
            text-align: left;
        }

        .brand-name {
            font-family: 'Moul', 'Noto Sans Khmer', 'Segoe UI', sans-serif;
            font-size: 40px;
            font-weight: normal;
            letter-spacing: 1px;
            line-height: 1.4;
            margin-top:8px;
            color: #ffc61a;
            -webkit-text-stroke: 1.1px #8b0000;
            paint-order: stroke fill;
            text-shadow: 0 3px 4px rgba(0, 0, 0, .35);
        }

        .brand-sub-title {
            font-size: 10pt;
            color: #222;
            margin-top: 3px;
        }

        /* ── WARNING TITLE BANNER ── */
        .warning-band {
            background: transparent;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-shrink: 0;
        }

     

        .warning-text {
            font-family: 'Hanuman', 'Noto Sans Khmer', sans-serif;
            font-size: 28pt;
            font-weight: 900;
            color: black;
            letter-spacing: 0.3px;
            text-align: center;
        }

        /* ── BODY ── */
        .body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .info-row {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 0 26px;
            gap: 16px;
            border-bottom: 2px dashed var(--red-soft);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .row-label {
            font-size: 14pt;
            font-weight: 700;
            color: #111;
            letter-spacing: 0.3px;
            white-space: nowrap;
            min-width: 130px;
            flex-shrink: 0;
        }

        .row-divider {
            width: 2px;
            height: 28px;
            background: #ddd;
            flex-shrink: 0;
            border-radius: 2px;
        }

        .row-value {
            font-family: 'Hanuman', sans-serif;
            font-weight: 900;
            font-size: 19pt;
            color: #0a0a0a;
            line-height: 1.15;
            word-break: break-word;
        }

        .row-taxi .row-value,
        .row-phone .row-value,
        .row-addr .row-value {
            background: #fff;
            color: var(--red);
            padding: 2px 12px;
            border-radius: 6px;
            display: inline-block;
        }

        /* ── FOOTER ── */
        .footer {
            background: #fff;
            border-top: 3px solid var(--red);
            padding: 12px 26px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            border-radius: 0 0 16px 16px;
        }

        .footer-contact {
            font-size: 12.5pt;
            font-weight: 700;
            color: black;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-thanks {
            font-family: 'Pacifico', cursive;
            font-size: 18pt;
            color:black;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-thanks svg {
            width: 17px;
            height: 17px;
            flex-shrink: 0;
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
            border-color: #3516e8;
            color: #3516e8;
            transform: translateY(-1px);
        }

        .brand-chip.active {
            background: #3516e8;
            border-color: #3516e8;
            color: #fff;
            cursor: default;
            pointer-events: none;
        }

        /* ── Print ── */
        @media print {
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
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
            }

            .sticker {
                width: 100%;
                height: 100%;
                border: 3px solid var(--red);
                border-radius: 0;
                box-shadow: none;
                page-break-after: avoid;
                page-break-inside: avoid;
            }

            .footer {
                border-radius: 0;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="sticker">
        {{-- Header --}}
        <div class="header">
            <div class="brand-container">
                <div class="brand-name">ម៉ាយូនេស តាមាន់មានជ័យ</div>
            </div>
        </div>

        {{-- Warning banner --}}
        <div class="warning-band">
    
            <span class="warning-text">សូមដាក់កេសបញ្ឈរ កំុដាក់របស់ធ្ងន់ពីលើប្រយ័ត្នបែក</span>

        </div>

        {{-- Body rows --}}
        <div class="body">

            {{-- Customer name --}}
            <div class="info-row row-name">
                <div class="row-label">អតិថិជន</div>
                <div class="row-divider"></div>
                <div class="row-value">{{ $invoice->order->customer->name ?? '#' }}</div>
            </div>

            {{-- Address --}}
            <div class="info-row row-addr">
                <div class="row-label">ទីតាំង</div>
                <div class="row-divider"></div>
                <div class="row-value">{{ $invoice->order->customer->address ?? '#' }}</div>
            </div>

            {{-- Customer phone --}}
            <div class="info-row row-phone">
                <div class="row-label">លេខអ្នកទទួល</div>
                <div class="row-divider"></div>
                <div class="row-value">{{ $invoice->order->customer->phone ?? '#' }}</div>
            </div>

            {{-- Sender taxi phone --}}
            @if(!empty($invoice->order->taxi_phone))
            <div class="info-row row-taxi">
                <div class="row-label">លេខតាក់សុី</div>
                <div class="row-divider"></div>
                <div class="row-value">{{ $invoice->order->taxi_phone }}</div>
            </div>
            @endif

            {{-- Sender phone --}}
            <div class="info-row row-sender">
                <div class="row-label">លេខអ្នកផ្ញើរ</div>
                <div class="row-divider"></div>
                <div class="row-value">
                    {{ $invoice->order->sender_phone ?? config('app.sender_phone', '012 312 477') }}
                </div>
            </div>

        </div>{{-- /body --}}

        {{-- Footer --}}
        <div class="footer">
            <div class="footer-contact">
                ទំនាក់ទំនងផ្សេងៗ៖ <strong>012 312 477</strong>
            </div>
            <div class="footer-thanks">
                Thank You!
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path
                        d="M20.8 4.6a5.5 5.5 0 00-7.8 0L12 5.6l-1-1a5.5 5.5 0 00-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 000-7.8z" />
                </svg>
            </div>
        </div>

    </div>{{-- /sticker --}}
           @php
        $currentRoute = request()->route()?->getName();
        @endphp
        <div class="no-print brand-switch">
        <span class="brand-switch-label">ជ្រើសរើស:</span>
        <a href="{{ route('packing.delivery_pizza', $invoice) }}" target="_blank"
            class="brand-chip @if($currentRoute === 'packing.delivery_pizza') active @endif">ភីហ្សា គ្រួសាររីករាយ</a>
        <a href="{{ route('packing.delivery_mayo', $invoice) }}" target="_blank"
            class="brand-chip @if($currentRoute === 'packing.delivery_mayo') active @endif">ម៉ាយូនេស បន្ទាយឆ្មារ</a>
        <a href="{{ route('packing.delivery_tamon', $invoice) }}" target="_blank"
            class="brand-chip @if($currentRoute === 'packing.delivery_tamon') active @endif">តាមាន់មានជ័យ</a>
        </div>
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

    <script>
        // When opened inside the packing/index sticker popup (an iframe),
        // brand-switch chips and the back link shouldn't navigate the iframe
        // itself — that would either open a stray new tab or load the full
        // app layout squeezed into the small frame. Talk to the parent
        // instead. Standalone/new-tab views are unaffected.
        if (window.self !== window.top) {
            document.querySelectorAll('.brand-chip').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    window.parent.postMessage({ source: 'sticker-page', action: 'navigate', url: link.href, title: document.title }, '*');
                });
            });
            document.querySelectorAll('.btn-back').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    window.parent.postMessage({ source: 'sticker-page', action: 'close' }, '*');
                });
            });
        }
    </script>
</body>

</html>

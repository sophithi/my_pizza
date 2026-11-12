<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Packing INV - {{ $invoice->invoice_number }}</title>
    <script>
        if (window.self !== window.top) document.documentElement.classList.add('embedded');
    </script>
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

        .meta-row strong {
            color: #1a1d29;
        }

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

        .items-table th:last-child {
            text-align: center;
        }

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

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .product-name {
            font-weight: 600;
        }

        .product-desc {
            font-size: 12px;
            color: #888;
            margin-top: 2px;
        }

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

        .notes-text {
            font-size: 13px;
            color: #333;
        }

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

        .packing-check {
            width: 18px;
            height: 18px;
            accent-color: #059669;
            cursor: pointer;
            flex-shrink: 0;
        }

        .packing-check:disabled { cursor: default; }
        .items-table tbody tr:has(.packing-check:checked) { background: #f3fbf7; }
        .items-table .group-row td { background: #f3f6fa; color: #475569; font-size: 13px; font-weight: 800; padding: 9px 12px; }
        .items-table .free-group td { color: #047857; background: #ecfdf5; }
        .items-table .free-product-row td:first-child { color: #047857; }
        .items-table .qty-cell { font-weight: 800; color: #111827; white-space: nowrap; }
        .items-table .check-cell { width: 80px; text-align: center; }
        .check-progress { color: #047857; font-size: 13px; font-weight: 700; }
        .progress-track { height: 6px; overflow: hidden; background: #e2e8f0; border-radius: 999px; margin-bottom: 16px; }
        .progress-fill { display: block; height: 100%; width: 0; background: #059669; border-radius: inherit; transition: width .2s ease; }
        .check-error { color: #b91c1c; font-size: 13px; margin-top: 8px; }
        .btn-done { background: #059669; color: #fff; border: 0; border-radius: 8px; padding: 10px 18px; font-weight: 700; cursor: pointer; }
        .btn-done:hover:not(:disabled) { background: #047857; }
        .btn-done:disabled { background: #a7b4af; cursor: not-allowed; }
        .done-label { color: #047857; font-weight: 700; align-self: center; }

        .embedded, .embedded body { height: 100%; }
        .embedded body { display: flex; flex-direction: column; overflow: hidden; padding: 0; background: #f8fafc; }
        .embedded .sticker {
            flex: 1 1 auto;
            min-height: 0;
            width: 100%;
            max-width: none;
            overflow-y: auto;
            margin: 0;
            padding: 20px 28px 28px;
            border: 0;
            border-radius: 0;
        }
        .embedded .sticker-header { position: sticky; top: -20px; z-index: 2; background: #fff; padding-top: 16px; }
        .embedded .sticker-title { font-size: 17px; color: #172033; letter-spacing: 0; text-transform: none; }
        .embedded .sticker-header { border-color: #e2e8f0; }
        .embedded .meta-row { font-size: 14px; }
        .embedded .items-table th { font-size: 13px; }
        .embedded .items-table td { font-size: 15px; padding-top: 13px; padding-bottom: 13px; }
        .embedded .items-table .group-row td { font-size: 14px; padding-top: 10px; padding-bottom: 10px; }
        .embedded .packing-check { width: 22px; height: 22px; }
        .embedded .items-table { border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
        .embedded .sticker-actions {
            flex: 0 0 auto;
            flex-wrap: wrap;
            justify-content: flex-end !important;
            align-items: center;
            margin-top: 0 !important;
            padding: 12px 20px;
            border-top: 1px solid #e2e8f0;
            background: #fff;
            box-shadow: 0 -4px 16px rgba(15, 23, 42, .06);
        }
        .embedded .sticker-actions button, .embedded .sticker-actions a { min-height: 40px; }

        @media (max-width: 600px) {
            .embedded .sticker { padding: 14px 16px 24px; }
            .embedded .sticker-header { top: -14px; }
            .embedded .meta-row { flex-wrap: wrap; gap: 5px 12px; }
            .embedded .items-table th, .embedded .items-table td { padding-left: 6px; padding-right: 6px; }
        }

        @media print {
            body, .embedded body {
                display: block;
                height: auto;
                overflow: visible;
                padding: 0;
            }

            .sticker, .embedded .sticker {
                display: block;
                width: auto;
                height: auto;
                overflow: visible;
                border: 2px dashed #e85d24;
                max-width: 100%;
                padding: 20px;
            }

            .embedded .sticker-header {
                position: static;
                padding-top: 0;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    @php
        $packingChecklist = $invoice->packing_checklist ?? [];
        $checkedItems = $packingChecklist['items'] ?? [];
        $canCheck = $invoice->packing_sent_at && !$invoice->packing_completed_at && $invoice->status !== 'cancelled'
            && $invoice->order && !$invoice->order->trashed() && $invoice->order->status !== 'cancelled';
    @endphp
    <div class="sticker" id="">
        <div class="sticker-header">
            <span class="sticker-title">ទំនិញដែលត្រូវរៀបចំ</span>
        </div>

        <div class="meta-row">
            <span><strong>Invoice:</strong> {{ $invoice->invoice_number }}</span>
            <span><strong>Date:</strong> {{ $invoice->invoice_date->translatedFormat('M d, Y') }}</span>
        </div>
        <div class="meta-row">
            <span><strong>Customer:</strong> {{ $invoice->order->customer->name ?? 'N/A' }}</span>

        </div>

        @if($invoice->order->delivery_id && (($invoice->order->small_pack_qty ?? 0) > 0 || ($invoice->order->big_pack_qty ?? 0) > 0))
            <div class="notes-box">
                <div class="notes-label">ការដឹកជញ្ជូន — កេសត្រូវរៀបចំ</div>
                <div class="notes-text" style="font-weight: 700;">
                    {{ $invoice->order->delivery->delivery_name ?? 'N/A' }}
                    @if(($invoice->order->small_pack_qty ?? 0) > 0)
                        — កេសតូច × {{ $invoice->order->small_pack_qty }}
                    @endif
                    @if(($invoice->order->big_pack_qty ?? 0) > 0)
                        — កេសធំ × {{ $invoice->order->big_pack_qty }}
                    @endif
                </div>
            </div>
        @endif

        <div class="progress-track no-print" role="progressbar" aria-label="ការពិនិត្យទំនិញ" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="checkProgressTrack"><span class="progress-fill" id="checkProgressFill"></span></div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>មុខទំនិញ</th>
                    <th>ចំនួន</th>
                    <th>ពិនិត្យ</th>
                </tr>
            </thead>
            <tbody>
                <tr class="group-row"><td colspan="4">ទំនិញលក់</td></tr>
                @foreach($invoice->order->items->where('item_type', 'sale')->values() as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <div class="product-name">{{ $item->product->name ?? 'N/A' }}</div>
                            @if($item->product->category ?? false)
                                <div class="product-desc">{{ $item->product->category }}</div>
                            @endif
                        </td>
                        <td class="qty-cell">{{ $item->quantity }}</td>
                        <td class="check-cell">
                            <input type="checkbox" class="packing-check" data-kind="item" data-key="{{ $item->id }}"
                                aria-label="បានពិនិត្យ {{ $item->product->name ?? 'ទំនិញ' }}"
                                @checked(!empty($checkedItems[$item->id])) @disabled(!$canCheck)>
                        </td>
                    </tr>
                @endforeach
                @if($invoice->order->items->where('item_type', 'free')->isNotEmpty())
                    <tr class="group-row free-group"><td colspan="4">ទំនិញថែម</td></tr>
                    @foreach($invoice->order->items->where('item_type', 'free')->values() as $i => $freeItem)
                        <tr class="free-product-row">
                            <td>{{ $i + 1 }}</td>
                            <td><div class="product-name">{{ $freeItem->product->name ?? 'N/A' }}</div></td>
                            <td class="qty-cell">{{ $freeItem->quantity ?? 1 }}</td>
                            <td class="check-cell"><input type="checkbox" class="packing-check" data-kind="item" data-key="{{ $freeItem->id }}"
                                aria-label="បានពិនិត្យទំនិញថែម {{ $freeItem->product->name ?? 'ទំនិញ' }}"
                                @checked(!empty($checkedItems[$freeItem->id])) @disabled(!$canCheck)></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        @if($invoice->order->notes || $invoice->notes)
            <div class="notes-box">
                <div class="notes-label">Special Notes</div>
                <div class="notes-text">{{ $invoice->order->notes ?? $invoice->notes }}</div>
            </div>
        @endif
        <p class="check-error no-print" id="checkError" hidden></p>
    </div>
    <div class="no-print sticker-actions"
        style="text-align: center; margin-top: 20px; display: flex; justify-content: center; gap: 12px;">
        <span class="check-progress" id="checkProgress" role="status" aria-live="polite"></span>
        @if($invoice->packing_completed_at)
            <span class="done-label">✓ រៀបចំរួច</span>
        @else
            <button type="button" class="btn-done" id="packingDoneBtn" disabled>✓ រៀបចំរួច</button>
        @endif
        <button onclick="window.print()"
            style="background: #e85d24; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">
            Print
        </button>
       
        <a href="{{ $backUrl ?? url()->previous() ?? route('packing.index') }}" class="btn-back"
            style="background: #f0f2f5; color: #1a1d29; border: 1px solid #e5e7eb; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            ← Back
        </a>
    </div>
    <script>
        const packingChecks = Array.from(document.querySelectorAll('.packing-check'));
        const checkProgress = document.getElementById('checkProgress');
        const checkProgressTrack = document.getElementById('checkProgressTrack');
        const checkProgressFill = document.getElementById('checkProgressFill');
        const checkError = document.getElementById('checkError');
        const packingDoneBtn = document.getElementById('packingDoneBtn');
        const csrfToken = @json(csrf_token());
        let savingChecks = 0;
        const updateProgress = () => {
            const checked = packingChecks.filter(input => input.checked).length;
            checkProgress.textContent = `បានធីក ${checked}/${packingChecks.length}`;
            const percent = packingChecks.length ? Math.round(checked * 100 / packingChecks.length) : 0;
            checkProgressTrack.setAttribute('aria-valuenow', percent);
            checkProgressFill.style.width = `${percent}%`;
            if (packingDoneBtn) {
                packingDoneBtn.disabled = @json(!$canCheck) || checked !== packingChecks.length || savingChecks > 0;
            }
        };
        updateProgress();

        packingChecks.forEach(input => input.addEventListener('change', async () => {
            const checked = input.checked;
            savingChecks++;
            input.disabled = true;
            checkError.hidden = true;
            updateProgress();
            try {
                const response = await fetch(@json(route('packing.checklist', $invoice)), {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        kind: input.dataset.kind,
                        key: input.dataset.key,
                        checked,
                        revision: @json($invoice->packing_sent_at?->format('Y-m-d H:i:s'))
                    })
                });
                if (!response.ok) {
                    const result = await response.json().catch(() => ({}));
                    throw new Error(result.message || 'មិនអាចរក្សាទុកបាន។ សូមផ្ទុកទំព័រឡើងវិញ។');
                }
            } catch (error) {
                input.checked = !checked;
                checkError.textContent = error.message;
                checkError.hidden = false;
            } finally {
                savingChecks--;
                input.disabled = @json(!$canCheck);
                updateProgress();
            }
        }));

        packingDoneBtn?.addEventListener('click', async () => {
            if (packingDoneBtn.disabled) return;
            packingDoneBtn.disabled = true;
            packingDoneBtn.textContent = 'កំពុងបញ្ចប់...';
            checkError.hidden = true;
            try {
                const response = await fetch(@json(route('packing.complete', $invoice)), {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                if (!response.ok) {
                    const result = await response.json().catch(() => ({}));
                    throw new Error(result.errors?.packing?.[0] || result.message || 'មិនអាចបញ្ចប់បាន។ សូមផ្ទុកទំព័រឡើងវិញ។');
                }
                if (window.self !== window.top) {
                    window.parent.postMessage({ source: 'sticker-page', action: 'completed' }, window.location.origin);
                } else {
                    window.location.href = @json($backUrl ?? route('packing.index'));
                }
            } catch (error) {
                checkError.textContent = error.message;
                checkError.hidden = false;
                packingDoneBtn.textContent = '✓ រៀបចំរួច';
                updateProgress();
            }
        });

        if (window.self !== window.top) {
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

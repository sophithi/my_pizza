@extends('layouts.app')

@section('title', 'Delivery: ' . $delivery->delivery_name)

@push('styles')
    <style>
        .show-page {
            --accent: #e85d24;
            --accent-dark: #d94a10;
            --surface: #ffffff;
            --border: #e5e7eb;
            --text: #111827;
            --muted: #6b7280;
        }

        .show-container {
            max-width: 1180px;
            margin: 24px auto;
            padding: 0 24px 48px;
        }

        /* ── Back link ── */
        .show-back {
            font-size: 13px;
            color: var(--muted);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
        }

        .show-back:hover {
            color: var(--accent);
        }

        /* ── Header ── */
        .show-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .show-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--text);
            margin: 0;
        }

        .show-subtitle {
            font-size: 14px;
            color: var(--muted);
            margin: 4px 0 0;
        }

        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* ── Buttons ── */
        .btn {
            align-items: center;
            border: 0;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            font-size: 14px;
            font-weight: 700;
            gap: 7px;
            min-height: 40px;
            padding: 9px 20px;
            text-decoration: none;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
        }

        .btn-primary:hover {
            color: #fff;
            box-shadow: 0 6px 16px rgba(232, 93, 36, 0.3);
            transform: translateY(-1px);
        }

        .btn-danger {
            background: #dc2626;
            color: #fff;
        }

        .btn-danger:hover {
            background: #b91c1c;
            color: #fff;
        }

        .header-actions .btn-sm {
            min-height: 36px;
            padding: 7px 14px;
            font-size: 13px;
        }

        .header-actions .btn-outline-success {
            background: #fff;
            border: 1px solid #22c55e;
            color: #16a34a;
        }

        .header-actions .btn-outline-success:hover {
            background: #f0fdf4;
            color: #15803d;
        }

        .header-actions .btn-outline-danger {
            background: #fff;
            border: 1px solid #ef4444;
            color: #dc2626;
        }

        .header-actions .btn-outline-danger:hover {
            background: #fef2f2;
            color: #b91c1c;
        }

        /* ── Two-column layout ── */
        .show-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 18px;
            align-items: start;
        }

        .show-left {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .show-right {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        /* ── Card ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .card-header {
            border-bottom: 1px solid var(--border);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f9fafb;
        }

        .card-header-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #fff5f0;
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .card-header-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--text);
            margin: 0;
        }

        .card-body {
            padding: 20px;
        }

        /* ── Info grid ── */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }

        .info-item label {
            display: block;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .info-item span {
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
        }

        .info-item span.accent {
            color: var(--accent);
            font-size: 22px;
            font-weight: 800;
        }

        /* ── Stats ── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .stat-card {
            background: #f9fafb;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 12px;
            text-align: center;
        }

        .stat-val {
            font-size: 20px;
            font-weight: 800;
            color: var(--accent);
            line-height: 1.2;
        }

        .stat-lbl {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--muted);
            margin-top: 4px;
            letter-spacing: 0.4px;
        }

        /* ── Case selector ── */
        .case-selector {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .case-option-card {
            border: 2px solid var(--border);
            border-radius: 10px;
            padding: 14px 16px;
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
        }

        .case-option-card:hover {
            border-color: var(--accent);
        }

        .case-option-card.selected {
            border-color: var(--accent);
            background: #fff8f5;
        }

        .case-option-card input[type="radio"] {
            accent-color: var(--accent);
            width: 16px;
            height: 16px;
        }

        .case-label {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
        }

        .case-price {
            font-size: 13px;
            color: var(--muted);
            margin-top: 2px;
        }

        .selected-price-display {
            margin-top: 12px;
            padding: 14px 16px;
            background: #fff5f0;
            border: 1px solid rgba(232, 93, 36, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .price-label {
            font-size: 13px;
            color: var(--muted);
            font-weight: 600;
        }

        .price-value {
            font-size: 22px;
            font-weight: 800;
            color: var(--accent);
        }

        /* ── Orders table ── */
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .orders-table th {
            background: #f9fafb;
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
            padding: 12px 16px;
            text-transform: uppercase;
            text-align: left;
            letter-spacing: 0.4px;
        }

        .orders-table td {
            border-bottom: 1px solid #f1f3f5;
            color: var(--text);
            padding: 13px 16px;
            vertical-align: middle;
        }

        .orders-table tr:last-child td {
            border-bottom: none;
        }

        .orders-table tr:hover td {
            background: #fafafa;
        }

        .address-cell {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            max-width: 220px;
        }

        .address-cell span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .address-cell i {
            color: var(--muted);
            flex-shrink: 0;
        }

        .text-muted {
            color: var(--muted);
        }

        .address-input {
            border: 1px solid var(--accent);
            border-radius: 6px;
            font-size: 13px;
            padding: 4px 8px;
            width: 180px;
        }

        .badge-inv {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 800;
            padding: 3px 9px;
        }

        .empty-orders {
            padding: 40px 20px;
            text-align: center;
            color: var(--muted);
            font-size: 14px;
        }

        .empty-orders i {
            font-size: 32px;
            color: #d1d5db;
            display: block;
            margin-bottom: 10px;
        }

        /* ── Inline packing qty edit ── */
        .row-no {
            color: var(--muted);
            font-weight: 700;
            text-align: center;
            width: 1%;
        }

        .qty-input {
            border: 1px solid var(--accent);
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            padding: 4px 2px;
            text-align: center;
            width: 52px;
        }

        .fee-cell {
            align-items: center;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .row-icon-btn {
            align-items: center;
            background: none;
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--muted);
            cursor: pointer;
            display: inline-flex;
            font-size: 12px;
            height: 26px;
            justify-content: center;
            padding: 0;
            width: 26px;
        }

        .row-icon-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .row-save-btn {
            border-color: #86efac;
            color: #16a34a;
        }

        .row-save-btn:hover {
            background: #f0fdf4;
            border-color: #22c55e;
            color: #15803d;
        }

        .row-cancel-btn {
            border-color: #fca5a5;
            color: #dc2626;
        }

        .row-cancel-btn:hover {
            background: #fef2f2;
            border-color: #ef4444;
            color: #b91c1c;
        }

        @media (max-width: 800px) {
            .show-grid {
                grid-template-columns: 1fr;
            }

            .stats-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .show-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-3 show-page">
        <div class="show-container">

            @if(session('success'))
                <div class="alert alert-success mb-3">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <a href="{{ route('deliveries.index', request()->only('filter', 'start_date', 'end_date')) }}" class="show-back">
                <i class="fas fa-arrow-left"></i> Back
            </a>

            <div class="show-header">
                <div>
                    <h1 class="show-title">{{ $delivery->delivery_name }}</h1>
                    @if($startDate && $endDate)
                        <p class="show-subtitle">
                            {{ \Illuminate\Support\Carbon::parse($startDate)->format('Y-m-d') }}
                            @if($startDate !== $endDate)
                                - {{ \Illuminate\Support\Carbon::parse($endDate)->format('Y-m-d') }}
                            @endif
                        </p>
                    @endif
                </div>
                <div class="header-actions">
                    <a href="{{ route('deliveries.export.excel', array_filter(['delivery' => $delivery->id, 'start_date' => $startDate, 'end_date' => $endDate])) }}"
                        class="btn btn-outline-success btn-sm">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                    <a href="{{ route('deliveries.export.pdf', array_filter(['delivery' => $delivery->id, 'start_date' => $startDate, 'end_date' => $endDate])) }}"
                        class="btn btn-outline-danger btn-sm" target="_blank">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('deliveries.edit', $delivery) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> កែប្រែ
                    </a>
                    <form action="{{ route('deliveries.destroy', $delivery) }}" method="POST" style="display:inline;"
                        onsubmit="return confirm('លុបការដឹកជញ្ជូននេះ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> លុប
                        </button>
                    </form>
                </div>
            </div>

            @php
                $totalFee = $delivery->orders->sum('delivery_fee_khr');
                $totalAmount = $delivery->orders->sum(fn($o) => $o->invoice?->total_amount ?? $o->total_amount ?? 0);
                $orderCount = $delivery->orders_count ?? $delivery->orders->count();
                $totalBoxes = $delivery->orders->sum(fn($o) => max((int) ($o->box_qty ?? 1), 1));
                $currentUnitPrice = (float) $delivery->delivery_price_khr;
            @endphp

            <div class="show-grid">

                {{-- LEFT --}}
                <div class="show-left">

                

                    {{-- Linked Invoices --}}
                    <div class="card">
                        <div class="card-header">
                            <div class="card-header-icon"><i class="fas fa-file-invoice"></i></div>
                            <p class="card-header-title">វិក្ក័យបត្រ</p>
                        </div>
                        <div class="table-responsive">
                            <table class="orders-table" id="deliveryOrdersTable">
                                <thead>
                                    <tr>
                                        <th style="text-align:center;">លេខរៀង</th>
                                        @if($delivery->show_invoice_info)
                                            <th>លេខវិក្ក័យបត្រ</th>
                                        @endif
                                        <th>ឈ្មោះអតិថិជន</th>
                                        <th>លេខទំនាក់ទំនង</th>
                                        <th>ទីតាំង</th>
                                        <th style="text-align:center;">កេសតូច</th>
                                        <th style="text-align:center;">កេសធំ</th>
                                        <th style="text-align:right;">ថ្លៃដឹក</th>
                                        @if($delivery->show_invoice_info)
                                            <th style="text-align:right;">តម្លៃសរុប</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($delivery->orders as $order)
                                        @php
                                            $invoice = $order->invoice;
                                            $customer = $order->customer;
                                            $deliveryFee = (float) $order->delivery_fee_khr;
                                            $smallQty = (int) ($order->small_pack_qty ?? $order->box_qty ?? 1);
                                            $bigQty = (int) ($order->big_pack_qty ?? 0);
                                            $customerName = $customer?->name ?? 'N/A';
                                            $customerPhone = $customer?->phone ?? '—';
                                            $customerAdress = $customer?->address;
                                            $totalPrice = $order->totalKhr();
                                        @endphp
                                        <tr data-order-row="{{ $order->id }}" data-customer-id="{{ $customer?->id }}">
                                            <td class="row-no">{{ $loop->iteration }}</td>
                                            @if($delivery->show_invoice_info)
                                                <td><span class="badge-inv">{{ $invoice?->invoice_number ?? 'N/A' }}</span></td>
                                            @endif
                                            <td>{{ $customerName }}</td>
                                            <td>{{ $customerPhone }}</td>
                                            <td>
                                                @if($customer)
                                                    <span class="address-view" data-field="address">
                                                        @if($customerAdress)
                                                            <span class="address-cell" title="{{ $customerAdress }}">
                                                                <i class="fas fa-map-marker-alt"></i>
                                                                <span>{{ $customerAdress }}</span>
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </span>
                                                    <input type="text" class="address-input" data-field="address"
                                                        value="{{ $customerAdress }}" placeholder="ទីតាំង" style="display:none;">
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td style="text-align:center; font-weight:700;">
                                                <span class="qty-view" data-field="small">{{ number_format($smallQty, 0) }}</span>
                                                <input type="number" class="qty-input" data-field="small" min="0" step="1"
                                                    value="{{ $smallQty }}" style="display:none;">
                                            </td>
                                            <td style="text-align:center; font-weight:700;">
                                                <span class="qty-view" data-field="big">{{ number_format($bigQty, 0) }}</span>
                                                <input type="number" class="qty-input" data-field="big" min="0" step="1"
                                                    value="{{ $bigQty }}" style="display:none;">
                                            </td>
                                            <td style="text-align:right;">
                                                <div class="fee-cell">
                                                    <span class="fee-view" style="font-weight:700; color:var(--accent);">
                                                        ៛{{ number_format($deliveryFee, 0) }}
                                                    </span>
                                                    <button type="button" class="row-icon-btn row-edit-btn" title="កែប្រែ"
                                                        onclick="startRowEdit({{ $order->id }})">
                                                        <i class="fas fa-pen"></i>
                                                    </button>
                                                    <button type="button" class="row-icon-btn row-save-btn" title="រក្សាទុក"
                                                        onclick="saveRowEdit({{ $order->id }})" style="display:none;">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="row-icon-btn row-cancel-btn" title="បោះបង់"
                                                        onclick="cancelRowEdit({{ $order->id }})" style="display:none;">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            @if($delivery->show_invoice_info)
                                                <td style="text-align:right; font-weight:700;">
                                                    ៛{{ number_format($totalPrice, 0) }}
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $delivery->show_invoice_info ? 9 : 7 }}">
                                                <div class="empty-orders">
                                                    <i class="fas fa-file-invoice"></i>
                                                    មិនមានវិក្ក័យបត្រ.
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                {{-- RIGHT --}}
                <div class="show-right">

                    {{-- Summary --}}
                    <div class="card">
                        <div class="card-header">
                            <div class="card-header-icon"><i class="fas fa-chart-bar"></i></div>
                            <p class="card-header-title">Summary</p>
                        </div>
                        <div class="card-body">
                            <div class="stats-row">
                                <div class="stat-card">
                                    <div class="stat-val">{{ $orderCount }}</div>
                                    <div class="stat-lbl">ចំនួនវិក្ក័យបត្រ</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-val" id="statTotalBoxes">{{ number_format($totalBoxes, 0) }}</div>
                                    <div class="stat-lbl">សរុបកេស</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-val" style="font-size: 15px;">៛{{ number_format($currentUnitPrice, 0) }}</div>
                                    <div class="stat-lbl">តម្លៃបច្ចុប្បន្ន/កេស</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-val" style="font-size: 15px;" id="statTotalFee">៛{{ number_format($totalFee, 0) }}</div>
                                    <div class="stat-lbl">សរុបថ្លៃដឹក</div>
                                </div>
                                @if($delivery->show_invoice_info)
                                    <div class="stat-card">
                                        <div class="stat-val" style="font-size: 15px;">${{ number_format($totalAmount, 0) }}
                                        </div>
                                        <div class="stat-lbl">សរុបវិក្ក័យបត្រ</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        function packingUpdateUrl(orderId) {
            return @json(route('deliveries.orders.update-packing', ['delivery' => $delivery->id, 'order' => '__ORDER__']))
                .replace('__ORDER__', orderId);
        }

        function rowElements(orderId) {
            const row = document.querySelector(`tr[data-order-row="${orderId}"]`);
            return {
                row,
                customerId: row.dataset.customerId || null,
                smallView: row.querySelector('.qty-view[data-field="small"]'),
                smallInput: row.querySelector('.qty-input[data-field="small"]'),
                bigView: row.querySelector('.qty-view[data-field="big"]'),
                bigInput: row.querySelector('.qty-input[data-field="big"]'),
                addressView: row.querySelector('.address-view[data-field="address"]'),
                addressInput: row.querySelector('.address-input[data-field="address"]'),
                feeView: row.querySelector('.fee-view'),
                editBtn: row.querySelector('.row-edit-btn'),
                saveBtn: row.querySelector('.row-save-btn'),
                cancelBtn: row.querySelector('.row-cancel-btn'),
            };
        }

        function renderAddressView(el, address) {
            if (!el.addressView) return;
            if (address) {
                el.addressView.innerHTML = `<span class="address-cell" title="${address}"><i class="fas fa-map-marker-alt"></i><span>${address}</span></span>`;
            } else {
                el.addressView.innerHTML = '<span class="text-muted">—</span>';
            }
        }

        function startRowEdit(orderId) {
            const el = rowElements(orderId);
            el.smallInput.dataset.original = el.smallInput.value;
            el.bigInput.dataset.original = el.bigInput.value;

            el.smallView.style.display = 'none';
            el.bigView.style.display = 'none';
            el.smallInput.style.display = 'inline-block';
            el.bigInput.style.display = 'inline-block';

            if (el.addressInput) {
                el.addressInput.dataset.original = el.addressInput.value;
                el.addressView.style.display = 'none';
                el.addressInput.style.display = 'inline-block';
            }

            el.editBtn.style.display = 'none';
            el.saveBtn.style.display = 'inline-flex';
            el.cancelBtn.style.display = 'inline-flex';
        }

        function exitRowEdit(orderId) {
            const el = rowElements(orderId);
            el.smallInput.style.display = 'none';
            el.bigInput.style.display = 'none';
            el.smallView.style.display = 'inline';
            el.bigView.style.display = 'inline';

            if (el.addressInput) {
                el.addressInput.style.display = 'none';
                el.addressView.style.display = 'inline';
            }

            el.editBtn.style.display = 'inline-flex';
            el.saveBtn.style.display = 'none';
            el.cancelBtn.style.display = 'none';
        }

        function cancelRowEdit(orderId) {
            const el = rowElements(orderId);
            el.smallInput.value = el.smallInput.dataset.original;
            el.bigInput.value = el.bigInput.dataset.original;
            if (el.addressInput) {
                el.addressInput.value = el.addressInput.dataset.original;
            }
            exitRowEdit(orderId);
        }

        function recalculateSummary() {
            let totalBoxes = 0;
            let totalFee = 0;

            document.querySelectorAll('#deliveryOrdersTable tbody tr[data-order-row]').forEach(row => {
                const small = parseInt(row.querySelector('.qty-view[data-field="small"]').textContent.replace(/,/g, ''), 10) || 0;
                const big = parseInt(row.querySelector('.qty-view[data-field="big"]').textContent.replace(/,/g, ''), 10) || 0;
                const fee = parseFloat(row.querySelector('.fee-view').textContent.replace(/[^\d.]/g, '')) || 0;
                totalBoxes += small + big;
                totalFee += fee;
            });

            document.getElementById('statTotalBoxes').textContent = totalBoxes.toLocaleString();
            document.getElementById('statTotalFee').textContent = '៛' + totalFee.toLocaleString();
        }

        async function saveRowEdit(orderId) {
            const el = rowElements(orderId);
            const smallQty = Math.max(0, parseInt(el.smallInput.value || '0', 10));
            const bigQty = Math.max(0, parseInt(el.bigInput.value || '0', 10));
            const address = el.addressInput ? el.addressInput.value.trim() : undefined;

            el.saveBtn.disabled = true;
            el.cancelBtn.disabled = true;

            try {
                const payload = { small_pack_qty: smallQty, big_pack_qty: bigQty };
                if (address !== undefined) {
                    payload.address = address;
                }

                const response = await fetch(packingUpdateUrl(orderId), {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                    },
                    body: JSON.stringify(payload),
                });

                if (!response.ok) {
                    throw new Error('Save failed');
                }

                const data = await response.json();

                el.smallView.textContent = data.small_pack_qty.toLocaleString();
                el.bigView.textContent = data.big_pack_qty.toLocaleString();
                el.feeView.textContent = '៛' + Math.round(data.delivery_fee_khr).toLocaleString();
                el.smallInput.value = data.small_pack_qty;
                el.bigInput.value = data.big_pack_qty;

                if (el.addressInput) {
                    el.addressInput.value = data.address || '';
                    renderAddressView(el, data.address);
                }

                if (data.customer_id) {
                    document.querySelectorAll(`tr[data-customer-id="${data.customer_id}"]`).forEach(row => {
                        if (row === el.row) return;
                        const otherAddressView = row.querySelector('.address-view[data-field="address"]');
                        const otherAddressInput = row.querySelector('.address-input[data-field="address"]');
                        if (otherAddressView) {
                            renderAddressView({ addressView: otherAddressView }, data.address);
                        }
                        if (otherAddressInput) {
                            otherAddressInput.value = data.address || '';
                        }
                    });
                }

                exitRowEdit(orderId);
                recalculateSummary();
            } catch (error) {
                alert('មិនអាចរក្សាទុកបានទេ សូមព្យាយាមម្តងទៀត។');
            } finally {
                el.saveBtn.disabled = false;
                el.cancelBtn.disabled = false;
            }
        }
    </script>
@endpush

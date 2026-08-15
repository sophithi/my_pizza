@extends('layouts.app')

@section('title', $invoice->invoice_number)

@push('styles')
    <style>
        .invoice-show {
            --accent: #e85d24;
            --accent-dark: #d94a10;
            --accent-soft: #fff7ed;
            --border: #e5e7eb;
            --muted: #6b7280;
            --surface: #fff;
            --text: #111827;
            --shadow: 0 12px 32px rgba(15, 23, 42, .07);
        }

        .invoice-header {
            align-items: flex-start;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: var(--shadow);
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 16px;
            padding: 18px;
        }

        .invoice-heading {
            min-width: 0;
        }

        .invoice-title {
            color: var(--text);
            font-size: 30px;
            font-weight: 900;
            margin: 0;
        }

        .invoice-subtitle {
            color: var(--muted);
            font-size: 14px;
            margin: 5px 0 0;
        }

        .invoice-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        .meta-chip {
            align-items: center;
            background: #f9fafb;
            border: 1px solid #eef2f7;
            border-radius: 999px;
            color: #374151;
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            gap: 7px;
            min-height: 30px;
            padding: 6px 10px;
            white-space: nowrap;
        }

        .meta-chip i {
            color: var(--accent);
        }

        .invoice-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
        }

        .invoice-btn {
            align-items: center;
            border: 0;
            border-radius: 8px;
            display: inline-flex;
            font-weight: 800;
            gap: 8px;
            justify-content: center;
            min-height: 40px;
            padding: 9px 14px;
            text-decoration: none;
            transition: background .15s ease, border-color .15s ease, color .15s ease, transform .15s ease;
            white-space: nowrap;
        }

        .invoice-btn:hover {
            transform: translateY(-1px);
        }

        .invoice-btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            box-shadow: 0 8px 18px rgba(232, 93, 36, .22);
            color: #fff;
        }

        .invoice-btn-primary:hover {
            color: #fff;
        }

        .invoice-btn-soft {
            background: #f3f4f6;
            color: #374151;
        }

        .invoice-btn-soft:hover {
            background: #e5e7eb;
            color: #111827;
        }

        .invoice-btn-success {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #047857;
            cursor: default;
        }

        .invoice-btn-success:hover {
            color: #047857;
            transform: none;
        }

        .summary-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: 1fr 1fr;
            margin-bottom: 14px;
        }

        .invoice-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 16px;
        }

        .card-title {
            align-items: center;
            color: var(--text);
            display: flex;
            font-size: 16px;
            font-weight: 900;
            gap: 8px;
            margin-bottom: 10px;
        }

        .card-title i {
            align-items: center;
            background: var(--accent-soft);
            border-radius: 8px;
            color: var(--accent);
            display: inline-flex;
            height: 30px;
            justify-content: center;
            width: 30px;
        }

        .info-row {
            display: flex;
            gap: 12px;
            justify-content: space-between;
            padding: 7px 0;
        }

        .info-row+.info-row {
            border-top: 1px solid #f1f3f5;
        }

        .info-label {
            color: var(--muted);
            font-size: 13px;
            font-weight: 800;
        }

        .info-value {
            color: var(--text);
            font-weight: 800;
            text-align: right;
        }

        .status-pill {
            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            gap: 6px;
            padding: 6px 10px;
        }

        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }

        .status-sent {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-draft {
            background: #fef3c7;
            color: #92400e;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-other {
            background: #e5e7eb;
            color: #374151;
        }

        .packing-status {
            background: #ecfdf5;
            color: #047857;
        }

        .packing-pending {
            background: #f3f4f6;
            color: #4b5563;
        }

        .items-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .items-header {
            align-items: center;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            padding: 13px 16px;
        }

        .items-title {
            color: var(--text);
            font-size: 18px;
            font-weight: 900;
            margin: 0;
        }

        .items-body {
            align-items: start;
            display: grid;
            grid-template-columns: minmax(0, 1fr) 330px;
        }

        .items-body .table-responsive {
            min-width: 0;
        }

        .invoice-table th {
            background: #f9fafb;
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            padding: 12px 16px;
            text-transform: uppercase;
        }

        .invoice-table td {
            border-bottom: 1px solid #f1f3f5;
            color: var(--text);
            padding: 12px 16px;
            vertical-align: middle;
        }

        .free-item-row {
            background: #f0fdf4;
        }

        .free-item-badge {
            background: #dcfce7;
            color: #166534;
            font-size: 11px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .custom-price-badge {
            display: inline-block;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: 10px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 999px;
            margin-left: 6px;
            vertical-align: middle;
        }

        .totals-panel {
            background: #fbfdff;
            border-left: 1px solid var(--border);
            padding: 16px;
            width: 100%;
        }

        .total-row {
            align-items: flex-start;
            border-bottom: 1px solid #eef2f7;
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .total-row .value-block {
            text-align: right;
        }

        .total-row .value-khr {
            display: block;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            margin-top: 2px;
        }

        .grand-total {
            background: linear-gradient(135deg, #fff7ed, #ffedd5);
            border: 1px solid #fed7aa;
            border-radius: 8px;
            margin-top: 8px;
            padding: 14px;
        }

        .grand-total .amount {
            color: var(--accent-dark);
            font-size: 24px;
            font-weight: 900;
        }

        .grand-total .amount-khr {
            color: var(--accent-dark);
            font-size: 13px;
            font-weight: 700;
            opacity: .85;
            display: block;
            text-align: right;
            margin-top: 2px;
        }

        .note-card {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            color: #78350f;
            font-weight: 700;
            margin-top: 16px;
            padding: 16px;
        }

        @media (max-width: 900px) {

            .invoice-header,
            .invoice-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .items-body {
                grid-template-columns: 1fr;
            }

            .totals-panel {
                border-left: 0;
                border-top: 1px solid var(--border);
            }

            .invoice-btn {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $customer = $invoice->order?->customer;
        $items = $invoice->order?->items ?? collect();

        // Canonical totals — see Order::grossSubtotalUsd/Khr(), itemDiscountKhr(),
        // totalKhr(), so this always matches the order/invoice list/packing pages
        // instead of re-deriving its own formula.
        $grossSubtotalUsd = $invoice->order?->grossSubtotalUsd() ?? 0;
        $subtotalKhr = $invoice->order?->grossSubtotalKhr() ?? 0;
        $discountKhr = $invoice->order?->itemDiscountKhr() ?? 0;
        $totalKhr = $invoice->order?->totalKhr() ?? 0;
    @endphp

    <div class="container-fluid py-4 invoice-show">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm" style="border-radius: 10px;">
                {{ session('success') }}
            </div>
        @endif

        <div class="invoice-header">
            <div class="invoice-heading">
                <h2 class="invoice-title">{{ $invoice->invoice_number }}</h2>
                <p class="invoice-subtitle">ព័ត៌មានលម្អិតវិក្ក័យបត្រ និងទំនិញដែលបានបញ្ជាទិញ</p>
            </div>

            <div class="invoice-actions">
                <a href="{{ $backUrl ?? route('invoices.index') }}" class="invoice-btn invoice-btn-soft">
                    <i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ
                </a>
                <a href="{{ route('invoices.print', $invoice) }}?return={{ urlencode($backUrl ?? route('invoices.index')) }}" class="invoice-btn invoice-btn-primary">
                    <i class="fas fa-print"></i> Print
                </a>
                @if(!auth()->user()->isStaffInventory() && !auth()->user()->isAuditor())
                    @if($invoice->packing_sent_at)
                        <span class="invoice-btn invoice-btn-success">
                            <i class="fas fa-check"></i> បានដាក់រៀបចំ
                        </span>
                    @else
                        <form method="POST" action="{{ route('invoices.send-to-packing', $invoice) }}" class="m-0">
                            @csrf
                            <button type="submit" class="invoice-btn invoice-btn-primary">
                                <i class="fas fa-box-open"></i> ដាក់រៀបចំ
                            </button>
                        </form>
                    @endif
                @endif
                @if($invoice->order && !auth()->user()->isAuditor())
                    <a href="{{ route('orders.edit', $invoice->order) }}" class="invoice-btn invoice-btn-soft">
                        <i class="fas fa-shopping-cart"></i> កែបញ្ជាទិញ
                    </a>
                @endif
                @if($invoice->status !== 'paid' && !auth()->user()->isAuditor())
                    <a href="{{ route('invoices.edit', $invoice) }}" class="invoice-btn invoice-btn-soft">
                        <i class="fas fa-file-invoice"></i> កែវិក្ក័យបត្រ
                    </a>
                @endif
            </div>
        </div>

        <div class="summary-grid">
            <div class="invoice-card">
                <div class="card-title">
                    <i class="fas fa-user"></i> ព័ត៌មានអតិថិជន
                </div>

                @if($customer)
                    <div class="info-row">
                        <span class="info-label">ឈ្មោះ</span>
                        <span class="info-value">{{ $customer->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">លេខទូរស័ព្ទ</span>
                        <span class="info-value">{{ $customer->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">ទីតាំង</span>
                        <span class="info-value">{{ $customer->city ?? $customer->address ?? 'N/A' }}</span>
                    </div>
                @else
                    <div class="text-muted">មិនមានព័ត៌មានអតិថិជន</div>
                @endif
            </div>

            <div class="invoice-card">
                <div class="card-title">
                    <i class="fas fa-file-invoice-dollar"></i> ព័ត៌មានវិក្ក័យបត្រ
                </div>

                <div class="info-row">
                    <span class="info-label">កាលបរិច្ឆេទ</span>
                    <span class="info-value">{{ $invoice->invoice_date?->format('d/m/Y') ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">បញ្ជាទិញ</span>
                    <span class="info-value">#{{ $invoice->order?->id ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">ដាក់រៀបចំ</span>
                    <span class="info-value">
                        @if($invoice->packing_sent_at)
                            {{ $invoice->packing_sent_at->setTimezone('Asia/Phnom_Penh')->format('d/m/Y h:i A') }}
                        @else
                            មិនទាន់ផ្ញើ
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">ការបង់ប្រាក់</span>
                    <span class="info-value">
                        @if($invoice->order?->payment_status === 'paid')
                            <span class="status-pill status-paid"><i class="fas fa-check-circle"></i> បានទូទាត់</span>
                        @elseif($invoice->order?->payment_status === 'partial')
                            <span class="status-pill status-draft"><i class="fas fa-adjust"></i> បង់មួយផ្នែក</span>
                        @else
                            <span class="status-pill status-draft"><i class="fas fa-clock"></i> មិនទាន់ទូទាត់</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="items-card">
            <div class="items-header">
                <h3 class="items-title">ទំនិញក្នុងវិក្ក័យបត្រ</h3>
                <!-- <span class="text-muted fw-bold">
                        {{ number_format($items->count()) }} មុខទំនិញ
                    </span> -->
            </div>

            <div class="items-body">
                <div class="table-responsive">
                    <table class="table invoice-table mb-0">
                        <thead>
                            <tr>
                                <th>ទំនិញ</th>
                                <th class="text-center">ចំនួន</th>
                                <th class="text-end">តម្លៃ</th>
                                <th class="text-end">បញ្ចុះតម្លៃ</th>
                                <th class="text-end">សរុប</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $row)
                                @php
                                    $isFreeItem = (float) $row->unit_price <= 0;
                                    $rowDiscount = (float) ($row->discount_percent ?? 0);
                                    $rowUnitKhr = $row->displayUnitKhr();
                                    $rowTotalKhr = $row->lineTotalKhr();
                                @endphp

                                <tr @class(['free-item-row' => $isFreeItem])>
                                    <td class="fw-bold">
                                        {{ $row->product?->name ?? 'N/A' }}
                                        @if(!empty($row->is_custom_price))
                                            <span class="custom-price-badge"><i class="fas fa-tag"></i> តម្លៃពិសេស</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ number_format($row->quantity) }}</td>

                                    @if($isFreeItem)
                                        <td class="text-end" colspan="3">
                                            <span class="free-item-badge">
                                                <i class="fas fa-gift"></i> ឥតគិតថ្លៃ (Free)
                                            </span>
                                        </td>
                                    @else
                                        <td class="text-end">
                                            <strong>${{ number_format($row->unit_price, 2) }}</strong>
                                            <div class="text-muted small">៛{{ number_format($rowUnitKhr, 0) }}</div>
                                        </td>
                                        <td class="text-end">
                                            @if($rowDiscount > 0)
                                                {{ rtrim(rtrim(number_format($rowDiscount, 1), '0'), '.') }}%
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <strong>${{ number_format($row->lineTotalUsd(), 2) }}</strong>
                                            <div class="text-muted small">៛{{ number_format($rowTotalKhr, 0) }}</div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">មិនមានទំនិញក្នុងវិក្ក័យបត្រនេះទេ</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="totals-panel">
                    <div class="total-row">
                        <span class="text-muted fw-bold">Subtotal</span>
                        <div class="value-block">
                            <strong>${{ number_format($grossSubtotalUsd, 2) }}</strong>
                            <span class="value-khr">៛{{ number_format($subtotalKhr, 0) }}</span>
                        </div>
                    </div>

                    @if((float) $invoice->discount_amount > 0)
                        <div class="total-row">
                            <span class="text-muted fw-bold">Discount</span>
                            <div class="value-block">
                                <strong style="color:#dc2626;">-${{ number_format($invoice->discount_amount, 2) }}</strong>
                                <span class="value-khr">-៛{{ number_format($discountKhr, 0) }}</span>
                            </div>
                        </div>
                    @endif

                    @if((float) $invoice->delivery_fee_khr > 0)
                        <div class="total-row">
                            <span class="text-muted fw-bold">
                                Delivery
                                {{ $invoice->order?->delivery ? '(' . $invoice->order->delivery->delivery_name . ')' : '' }}
                            </span>
                            <div class="value-block">
                                <strong>${{ number_format($invoice->delivery_fee_usd, 2) }}</strong>
                                <span class="value-khr">៛{{ number_format($invoice->delivery_fee_khr, 0) }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="grand-total d-flex justify-content-between align-items-start">
                        <span class="fw-bold">សរុបទឹកប្រាក់</span>
                        <div>
                            <span class="amount">${{ number_format($invoice->total_amount, 2) }}</span>
                            <span class="amount-khr">៛{{ number_format($totalKhr, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($invoice->notes)
            <div class="note-card">
                <div class="fw-bold mb-1"><i class="fas fa-sticky-note me-1"></i> កំណត់ចំណាំ</div>
                <div>{{ $invoice->notes }}</div>
            </div>
        @endif
    </div>
@endsection

@if(session('packing_refresh_url'))
    @push('scripts')
        <script>
            localStorage.setItem('packingRefresh', JSON.stringify({
                url: @json(session('packing_refresh_url')),
                time: Date.now()
            }));
        </script>
    @endpush
@endif
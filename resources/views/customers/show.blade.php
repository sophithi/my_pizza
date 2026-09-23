@extends('layouts.app')

@section('title', $customer->name)

@push('styles')
    <style>
        .customer-page {
            background: #f6f7fb;
        }

        .customer-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #eef2f7;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
            overflow: hidden;
            margin-bottom: 18px;
        }

        .customer-header {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 24px;
            border-bottom: 1px solid #eef2f7;
        }

        .customer-left {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .customer-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b35, #e85d24);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .customer-name {
            font-size: 24px;
            font-weight: 800;
            color: #111827;
            margin: 0;
        }

        .customer-sub {
            color: #64748b;
            margin-top: 4px;
            font-size: 14px;
        }

        .badge-soft {
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
            margin-right: 6px;
        }

        .badge-telegram {
            background: #e0f7ff;
            color: #0088cc;
        }

        .badge-facebook {
            background: #e7f3ff;
            color: #0a66c2;
        }

        .badge-active {
            background: #dcfce7;
            color: #166534;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .customer-actions {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .btn-orange {
            background: linear-gradient(135deg, #ff6b35, #e85d24);
            border: none;
            color: #fff;
            font-weight: 700;
            border-radius: 10px;
            padding: 10px 14px;
        }

        .btn-orange:hover {
            color: #fff;
            opacity: .9;
        }

        .btn-light-border {
            background: #fff;
            border: 1px solid #dbe3ef;
            color: #475569;
            font-weight: 700;
            border-radius: 10px;
            padding: 10px 14px;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            padding: 20px 24px;
        }

        .stat-box {
            background: #f8fafc;
            border: 1px solid #eef2f7;
            border-radius: 14px;
            padding: 16px;
        }

        .stat-label {
            color: #64748b;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .stat-value {
            color: #111827;
            font-size: 22px;
            font-weight: 900;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            padding: 0 24px 24px;
        }

        .info-box {
            border: 1px solid #eef2f7;
            border-radius: 14px;
            padding: 16px;
            background: #fff;
        }

        .info-title {
            font-size: 15px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 14px;
        }

        .info-title i {
            color: #e85d24;
            margin-right: 8px;
        }

        .info-label {
            font-size: 12px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .info-value {
            color: #111827;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .note-box {
            background: #fff7ed;
            border-left: 4px solid #e85d24;
            border-radius: 12px;
            padding: 14px;
            color: #111827;
            font-weight: 600;
        }

        .order-card {
            padding: 24px;
        }

        .section-header-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .section-title {
            font-size: 18px;
            font-weight: 900;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Filter Controls */
        .invoice-filter-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 18px;
        }

        .filter-tab-pill {
            font-size: 12px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 6px;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            color: #475569;
            background: #fff;
            transition: all .15s;
        }

        .filter-tab-pill:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .filter-tab-pill.active {
            background: #e85d24;
            border-color: #e85d24;
            color: #fff;
        }

        .modern-table {
            border-collapse: separate;
            border-spacing: 0 8px;
            width: 100%;
        }

        .modern-table thead th {
            border: none;
            color: #64748b;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            padding: 8px 14px;
            background: transparent;
        }

        .modern-table tbody tr {
            background: #fff;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
            border: 1px solid #eef2f7;
            transition: transform .15s;
        }

        .modern-table tbody tr:hover {
            transform: translateY(-1px);
        }

        .modern-table tbody td {
            border: none;
            padding: 14px;
            vertical-align: middle;
            color: #111827;
            font-size: 13.5px;
            font-weight: 600;
        }

        .modern-table tbody td:first-child {
            border-radius: 10px 0 0 10px;
            color: #e85d24;
            font-weight: 800;
        }

        .modern-table tbody td:last-child {
            border-radius: 0 10px 10px 0;
        }

        .status-pill {
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-completed, .status-paid {
            background: #dcfce7;
            color: #166534;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-draft {
            background: #fef3c7;
            color: #92400e;
        }

        .status-sent {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .status-other {
            background: #f1f5f9;
            color: #64748b;
        }

        .item-pill {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11.5px;
            font-weight: 700;
            color: #475569;
            display: inline-block;
            margin: 1px;
        }

        .view-link {
            color: #2563eb;
            font-weight: 800;
            text-decoration: none;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            display: inline-block;
            transition: all .15s;
        }

        .view-link:hover {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .empty-box {
            text-align: center;
            padding: 40px 20px;
            color: #64748b;
        }

        @media(max-width: 768px) {
            .customer-header,
            .customer-left,
            .customer-actions {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats-row,
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')

    <div class="customer-page">

        <div class="customer-card">

            <div class="customer-header">
                <div class="customer-left">
                    <div class="customer-avatar">
                        {{ strtoupper(mb_substr($customer->name, 0, 1)) }}
                    </div>

                    <div>
                        <h2 class="customer-name">{{ $customer->name }}</h2>
                        <div class="customer-sub">
                            Customer Profile Information
                        </div>

                        @if($customer->type == 'facebook')
                            <span class="badge-soft badge-facebook">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </span>
                        @elseif($customer->type == 'telegram')
                            <span class="badge-soft badge-telegram">
                                <i class="fab fa-telegram"></i> Telegram
                            </span>
                        @endif
                        @if($customer->status == 'active')
                            <span class="badge-soft badge-active">
                                <i class="fas fa-check-circle"></i> សកម្ម
                            </span>
                        @else
                            <span class="badge-soft badge-inactive">
                                <i class="fas fa-times-circle"></i> អសកម្ម
                            </span>
                        @endif
                    </div>
                </div>

                <div class="customer-actions">
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-orange">
                        <i class="fas fa-edit"></i> Edit
                    </a>

                    <a href="{{ route('customers.index') }}" class="btn btn-light-border">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value">
                        {{ $summary['orders'] ?? 0 }}
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-label">Total Spent</div>
                    <div class="stat-value">
                        ${{ number_format($summary['spent'] ?? 0, 2) }}
                    </div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-title">
                        ព័ត៌មានទំនាក់ទំនង
                    </div>

                    <div class="info-label">លេខទំនាក់ទំនង</div>
                    <div class="info-value">{{ $customer->phone ?? 'N/A' }}</div>
                </div>

                <div class="info-box">
                    <div class="info-title">
                        អ្នកលក់ / ភ្នាក់ងារលក់
                    </div>

                    <div class="info-label">ឈ្មោះអ្នកលក់</div>
                    <div class="info-value">
                        @if($customer->salesperson)
                            <a href="{{ route('salespersons.show', $customer->salesperson) }}" class="fw-bold text-decoration-none"
                                style="color: #e85d24;">
                                {{ $customer->salesperson->name }}
                            </a>
                        @else
                            <span class="text-muted">មិនទាន់មាន (None)</span>
                        @endif
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-title">
                        ទីតាំង
                    </div>

                    <div class="info-label">ទីតាំងអតិថិជន</div>
                    <div class="info-value">{{ $customer->address ?? 'N/A' }}</div>

                    <div class="info-label">ខេត្ត/ក្រុង</div>
                    <div class="info-value">{{ $customer->city ?? 'N/A' }}</div>
                </div>

                @if($customer->notes)
                    <div class="info-box" style="grid-column: 1 / -1;">
                        <div class="info-title">
                            <i class="fas fa-sticky-note"></i> Notes & Descriptions
                        </div>

                        <div class="note-box">
                            {{ $customer->notes }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Invoice History Card with Filters -->
        <div class="customer-card order-card">
            <div class="section-header-wrap">
                <h4 class="section-title">
                    <i class="fas fa-file-invoice text-warning"></i> ប្រវត្តិវិក្ក័យបត្រ (Invoice History)
                    <span class="badge bg-light text-dark border px-2 py-1 small ms-1" style="font-size: 12px; font-weight: 700;">
                        {{ $orders->total() }} វិក្ក័យបត្រ
                    </span>
                </h4>
            </div>

            <!-- Filter Controls -->
            <form method="GET" action="{{ route('customers.show', $customer) }}" class="invoice-filter-card">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <!-- Period Tabs -->
                    <div class="d-flex align-items-center gap-1 flex-wrap">
                        <span class="text-muted small fw-bold me-1">កាលបរិច្ឆេទ៖</span>
                        @foreach(['all' => 'គ្រប់ពេល', 'today' => 'ថ្ងៃនេះ', 'month' => 'ខែនេះ', 'custom' => 'កំណត់ថ្ងៃ'] as $key => $label)
                            <a href="{{ route('customers.show', array_merge([$customer], request()->except(['page', 'period']), ['period' => $key])) }}"
                               class="filter-tab-pill {{ request('period', 'all') === $key ? 'active' : '' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Status Dropdown & Search -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <select name="status" class="form-select form-select-sm" style="width: 140px; font-size: 12.5px;" onchange="this.form.submit()">
                            <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>ស្ថានភាពទាំងអស់</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>បានទូទាត់</option>
                            <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>មិនទាន់ទូទាត់</option>
                            <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>បង់មួយផ្នែក</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>បានបោះបង់</option>
                        </select>

                        <div class="input-group input-group-sm" style="width: 180px;">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="ស្វែងរកលេខវិក្ក័យបត្រ...">
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                        @if(request()->hasAny(['period', 'status', 'search', 'date_from', 'date_to']) && (request('period', 'all') !== 'all' || request('status', 'all') !== 'all' || request('search') || request('date_from')))
                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-light border text-danger" title="សម្អាត Filter">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>

                @if(request('period') === 'custom')
                    <div class="d-flex align-items-center gap-2 flex-wrap pt-2 border-top">
                        <input type="hidden" name="period" value="custom">
                        <span class="text-muted small fw-bold">ពីថ្ងៃ៖</span>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm" style="width: 140px; font-size: 12.5px;">
                        <span class="text-muted small fw-bold">ដល់ថ្ងៃ៖</span>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm" style="width: 140px; font-size: 12.5px;">
                        <button type="submit" class="btn btn-sm btn-orange" style="padding: 4px 10px; font-size: 12.5px;">អនុវត្ត</button>
                    </div>
                @endif
            </form>

            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table modern-table mb-0">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-end" style="text-align: right;">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($orders as $order)
                                @php
                                    $invoice = $order->invoice;
                                @endphp
                                <tr>
                                    <td>
                                        @if($invoice)
                                            <a href="{{ route('invoices.show', $invoice) }}" class="fw-bold" style="color: #e85d24; text-decoration: none;">
                                                {{ $invoice->invoice_number }}
                                            </a>
                                        @else
                                            <span class="text-muted">#ORD-{{ $order->id }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="text-muted">{{ $order->order_date ? $order->order_date->format('d/m/Y H:i') : ($invoice?->invoice_date ? $invoice->invoice_date->format('d/m/Y') : 'N/A') }}</span>
                                    </td>

                                    <td>
                                        @if($order->items->isNotEmpty())
                                            <div>
                                                @foreach($order->items->take(3) as $item)
                                                    <span class="item-pill">
                                                        {{ $item->product->name ?? 'Item' }} ({{ $item->quantity }})
                                                    </span>
                                                @endforeach
                                                @if($order->items->count() > 3)
                                                    <span class="item-pill text-muted">+{{ $order->items->count() - 3 }} ទៀត</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>

                                    <td class="fw-bold text-dark">
                                        ${{ number_format($invoice?->total_amount ?? $order->total_amount, 2) }}
                                    </td>

                                    <td>
                                        @if($order->status == 'cancelled' || ($invoice && $invoice->status == 'cancelled'))
                                            <span class="status-pill status-cancelled">
                                                <i class="fas fa-times-circle"></i> បានបោះបង់
                                            </span>
                                        @elseif($order->payment_status == 'paid')
                                            <span class="status-pill status-paid">
                                                <i class="fas fa-check-circle"></i> បានទូទាត់
                                            </span>
                                        @elseif($order->payment_status == 'partial')
                                            <span class="status-pill status-draft">
                                                <i class="fas fa-adjust"></i> បង់មួយផ្នែក
                                            </span>
                                        @else
                                            <span class="status-pill status-sent">
                                                <i class="fas fa-clock"></i> មិនទាន់ទូទាត់
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-end" style="text-align: right;">
                                        @if($invoice)
                                            <a href="{{ route('invoices.show', $invoice) }}" class="view-link">
                                                <i class="fas fa-eye"></i> មើល
                                            </a>
                                        @else
                                            <a href="{{ route('orders.show', $order) }}" class="view-link">
                                                <i class="fas fa-eye"></i> មើល
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div class="pt-3 border-top mt-3 d-flex justify-content-center">
                        {{ $orders->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @else
                <div class="empty-box">
                    <i class="fas fa-inbox fa-2x mb-2 text-muted"></i>
                    <p class="mb-0">រកមិនឃើញវិក្ក័យបត្រតាមលក្ខខណ្ឌស្វែងរកនេះទេ។</p>
                </div>
            @endif
        </div>

    </div>

@endsection
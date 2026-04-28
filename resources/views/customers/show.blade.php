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

    .badge-telegram { background: #e0f7ff; color: #0088cc; }
    .badge-facebook { background: #e7f3ff; color: #0a66c2; }
    .badge-active { background: #dcfce7; color: #166534; }
    .badge-inactive { background: #fee2e2; color: #991b1b; }

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
        padding: 22px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 900;
        color: #111827;
        margin-bottom: 18px;
    }

    .section-title i {
        color: #e85d24;
        margin-right: 8px;
    }

    .modern-table {
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .modern-table thead th {
        border: none;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        padding: 8px 14px;
    }

    .modern-table tbody tr {
        background: #fff;
        box-shadow: 0 4px 14px rgba(15, 23, 42, .05);
    }

    .modern-table tbody td {
        border: none;
        padding: 16px 14px;
        vertical-align: middle;
        color: #111827;
        font-size: 14px;
        font-weight: 600;
    }

    .modern-table tbody td:first-child {
        border-radius: 12px 0 0 12px;
        color: #e85d24;
        font-weight: 900;
    }

    .modern-table tbody td:last-child {
        border-radius: 0 12px 12px 0;
    }

    .status-pill {
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .status-pending { background: #fef3c7; color: #92400e; }
    .status-completed { background: #dcfce7; color: #166534; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }
    .status-paid { background: #dcfce7; color: #166534; }
    .status-sent { background: #dbeafe; color: #1d4ed8; }
    .status-draft { background: #fef3c7; color: #92400e; }
    .status-other { background: #e5e7eb; color: #374151; }

    .view-link {
        color: #2563eb;
        font-weight: 800;
        text-decoration: none;
    }

    .empty-box {
        text-align: center;
        padding: 50px 20px;
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
                    {{ $customer->orders_count ?? $customer->orders->count() }}
                </div>
            </div>

            <div class="stat-box">
                <div class="stat-label">Total Spent</div>
                <div class="stat-value">
                    ${{ number_format($customer->total_spent ?? $customer->orders->sum('total_amount'), 2) }}
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

    @if($customer->orders->count())
        <div class="customer-card order-card">
            <h4 class="section-title">
           
                Invoice History ({{ $customer->orders_count ?? $customer->orders->count() }} Invoices)
            </h4>

            <div class="table-responsive">
                <table class="table modern-table mb-0">
                    <thead>
                        <tr>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($customer->orders as $order)
                            @php
                                $invoice = $order->invoice;
                            @endphp
                            <tr>
                                <td>{{ $invoice?->invoice_number ?? 'N/A' }}</td>

                                <td>
                                    {{ $invoice?->invoice_date ? $invoice->invoice_date->format('d/m/Y') : 'N/A' }}
                                </td>

                                <td>
                                    {{ $order->items->count() }} item(s)
                                </td>

                                <td>
                                    ${{ number_format($invoice?->total_amount ?? $order->total_amount, 2) }}
                                </td>

                                <td>
                                    @if(!$invoice)
                                        <span class="status-pill status-other">
                                       No invoice
                                        </span>
                                    @elseif($invoice->status == 'paid')
                                        <span class="status-pill status-paid">
                                            <i class="fas fa-check-circle"></i> បានទូទាត់
                                        </span>
                                    @elseif($invoice->status == 'sent')
                                        <span class="status-pill status-sent">
                                            <i class="fas fa-clock"></i> មិនទាន់ទូទាត់
                                        </span>
                                    @elseif($invoice->status == 'cancelled')
                                        <span class="status-pill status-cancelled">
                                            <i class="fas fa-times-circle"></i> មិនទូទាត់
                                        </span>
                                    @elseif($invoice->status == 'draft')
                                        <span class="status-pill status-draft">
                                            <i class="fas fa-clock"></i> មិនទាន់ទូទាត់
                                        </span>
                                    @else
                                        <span class="status-pill status-other">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if($invoice)
                                        <a href="{{ route('invoices.show', $invoice) }}" class="view-link">
                                            <i class="fas fa-eye"></i> ពិនិត្យ
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="customer-card">
            <div class="empty-box">
                <i class="fas fa-inbox" style="font-size: 46px; margin-bottom: 14px;"></i>
                <p>No orders yet. This customer hasn't placed any orders.</p>
            </div>
        </div>
    @endif

</div>

@endsection

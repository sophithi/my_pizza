@extends('layouts.app')

@section('title', 'Payment Management')

@push('styles')
<style>
    :root {
        --accent: #e85d24;
        --bg: #f4f5f7;
        --surface: #ffffff;
        --border: #e9ecef;
        --text: #1a1d29;
        --text-muted: #6c757d;
        --success: #28a745;
        --warning: #ffc107;
        --danger: #dc3545;
        --info: #0d6efd;
    }

    body { background: var(--bg); }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
    }

    .page-title {
        font-size: 32px;
        font-weight: 800;
        color: var(--text);
        margin: 0;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        transition: background 0.2s;
    }

    .btn-primary:hover {
        background: #d64a1a;
    }

    .btn-danger {
        background: var(--danger);
        color: white;
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
    }

    .payment-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--surface);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .payment-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid var(--border);
    }

    .payment-table thead th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: var(--text-muted);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .payment-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.2s;
    }

    .payment-table tbody tr:hover {
        background: #f8f9fa;
    }

    .payment-table tbody td {
        padding: 16px;
        color: var(--text);
    }

    .payment-amount {
        font-weight: 600;
        font-size: 16px;
        color: var(--success);
    }

    .payment-method {
        display: inline-block;
        padding: 4px 12px;
        background: #f0f0f0;
        border-radius: 4px;
        font-size: 12px;
        color: var(--text-muted);
    }

    .payment-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .payment-status.confirmed {
        background: #d4edda;
        color: #155724;
    }

    .payment-status.pending {
        background: #fff3cd;
        color: #856404;
    }

    .payment-status.failed {
        background: #f8d7da;
        color: #721c24;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
        background: var(--surface);
        border-radius: 8px;
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 16px;
    }

    .empty-state-text {
        font-size: 16px;
        margin-bottom: 16px;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: var(--surface);
        padding: 24px;
        border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .stat-label {
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
    }

    .pagination {
        display: flex;
        gap: 8px;
        justify-content: center;
        margin-top: 32px;
    }

    .pagination a, .pagination span {
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: 6px;
        text-decoration: none;
        color: var(--text);
    }

    .pagination a:hover {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
    }

    .pagination .active {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
    }
</style>
@endpush

@section('content')

<div style="max-width: 1200px; margin: 0 auto; padding: 24px;">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">print invoice</h1>
        <a href="{{ route('payments.create') }}" class="btn-primary">+ New Payment</a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 16px; border-radius: 8px; margin-bottom: 24px; border-left: 4px solid #28a745;">
        ✓ {{ session('success') }}
    </div>
    @endif

    <!-- Statistics -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Total Payments</div>
            <div class="stat-value">{{ $payments->total() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Amount</div>
            <div class="stat-value">${{ number_format($payments->sum('amount'), 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Confirmed</div>
            <div class="stat-value" style="color: #28a745;">{{ $payments->where('status', 'confirmed')->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pending</div>
            <div class="stat-value" style="color: #ffc107;">{{ $payments->where('status', 'pending')->count() }}</div>
        </div>
    </div>

    <!-- Payments Table -->
    @if($payments->count() > 0)
    <table class="payment-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Reference</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                <td>
                    @if($payment->order_id)
                        <a href="{{ route('orders.show', $payment->order) }}" style="color: var(--accent); text-decoration: none;">
                            Order #{{ str_pad($payment->order->id, 4, '0', STR_PAD_LEFT) }}
                        </a>
                    @elseif($payment->invoice_id)
                        <a href="{{ route('invoices.show', $payment->invoice) }}" style="color: var(--accent); text-decoration: none;">
                            Invoice #{{ str_pad($payment->invoice->id, 4, '0', STR_PAD_LEFT) }}
                        </a>
                    @else
                        <span style="color: var(--text-muted);">Unknown</span>
                    @endif
                </td>
                <td>{{ $payment->reference ?? '-' }}</td>
                <td class="payment-amount">${{ number_format($payment->amount, 2) }}</td>
                <td>
                    <span class="payment-method">{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</span>
                </td>
                <td>
                    <span class="payment-status {{ $payment->status }}">{{ ucfirst($payment->status) }}</span>
                </td>
                <td>
                    <form action="{{ route('payments.destroy', $payment) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this payment?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        {{ $payments->links() }}
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">💳</div>
        <div class="empty-state-text">No payments recorded yet</div>
        <a href="{{ route('payments.create') }}" class="btn-primary">Record First Payment</a>
    </div>
    @endif
</div>

@endsection
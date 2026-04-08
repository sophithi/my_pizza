@extends('layouts.app')

@section('title', 'Purchases Management')

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

    .purchase-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--surface);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .purchase-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid var(--border);
    }

    .purchase-table thead th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: var(--text-muted);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .purchase-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.2s;
    }

    .purchase-table tbody tr:hover {
        background: #f8f9fa;
    }

    .purchase-table tbody td {
        padding: 16px;
        color: var(--text);
    }

    .purchase-amount {
        font-weight: 600;
        font-size: 16px;
        color: var(--accent);
    }

    .purchase-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .purchase-status.pending {
        background: #fff3cd;
        color: #856404;
    }

    .purchase-status.received {
        background: #d4edda;
        color: #155724;
    }

    .purchase-status.cancelled {
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
</style>
@endpush

@section('content')

<div style="max-width: 1200px; margin: 0 auto; padding: 24px;">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">📦 Purchases Management</h1>
        <a href="{{ route('purchases.create') }}" class="btn-primary">+ New Purchase</a>
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
            <div class="stat-label">Total Purchases</div>
            <div class="stat-value">{{ $purchases->total() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Amount</div>
            <div class="stat-value">${{ number_format($purchases->sum('total_amount'), 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pending</div>
            <div class="stat-value" style="color: #ffc107;">{{ $purchases->where('status', 'pending')->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Received</div>
            <div class="stat-value" style="color: #28a745;">{{ $purchases->where('status', 'received')->count() }}</div>
        </div>
    </div>

    <!-- Purchases Table -->
    @if($purchases->count() > 0)
    <table class="purchase-table">
        <thead>
            <tr>
                <th>Reference</th>
                <th>Supplier</th>
                <th>Purchase Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $purchase)
            <tr>
                <td>
                    <strong>#{{ $purchase->reference_number ?? str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</strong>
                </td>
                <td>{{ $purchase->supplier_name }}</td>
                <td>{{ $purchase->purchase_date->translatedFormat('M d, Y') }}</td>
                <td class="purchase-amount">${{ number_format($purchase->total_amount, 2) }}</td>
                <td>
                    <span class="purchase-status {{ $purchase->status }}">{{ ucfirst($purchase->status) }}</span>
                </td>
                <td>
                    <a href="{{ route('purchases.show', $purchase) }}" style="color: var(--accent); text-decoration: none; margin-right: 12px;">View</a>
                    <a href="{{ route('purchases.edit', $purchase) }}" style="color: var(--accent); text-decoration: none; margin-right: 12px;">Edit</a>
                    <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this purchase?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger" style="padding: 4px 8px;">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div style="margin-top: 32px;">
        {{ $purchases->links() }}
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">📦</div>
        <div class="empty-state-text">No purchases recorded yet</div>
        <a href="{{ route('purchases.create') }}" class="btn-primary">Record First Purchase</a>
    </div>
    @endif
</div>

@endsection
